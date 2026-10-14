<?php
/*
====================================================================
 get_l3_asm_by_dns_codes.php
 --------------------------------------------------------------
 DEPLOY THIS FILE ON THE SECONDARY SERVER (3.7.158.23), not on
 your main app server. It uses the existing local connection
 (sfa_connection.php) on that box, which avoids the charset-
 handshake incompatibility entirely (that only happened when your
 old PHP 5.6 client library connected to this server remotely —
 locally, this server talks to its own MySQL with a matching,
 current client library).

 Purpose : Given one or more SO dns_emp_code(s), find the L3 (ASM)
           employee(s) they report to, using this server's OWN
           employee_master table.

 Logic:
   1. employee_master
        WHERE dns_emp_code IN (<input dns_emp_codes>)
      -> read reporting_to for each (comma-separated, in this
         server's own emp_code terms).

   2. employee_master
        WHERE emp_code IN (<all reporting_to codes from step 1>)
        AND level = 'L3'
      -> these are the ASM(s). Returned with THEIR dns_emp_code
         (not just their local emp_code), so the calling side can
         store a value consistent with its own dns-code terms.

 Input (POST or GET):
   dns_emp_codes - comma-separated list of SO dns_emp_code(s)
                   e.g. dns_emp_codes=D1234,D5678

 Output:
   {
     "process_status": "YES",
     "process_message": "...",
     "data": [
       {"emp_code":"E9001","dns_emp_code":"D9001","emp_name":"...","level":"L3"},
       ...
     ]
   }

 --------------------------------------------------------------
 CONNECTION: uses $localDB->conn from sfa_connection.php. This
 script auto-detects whether $conn is a mysqli object or a PDO
 instance and queries accordingly, since that wasn't confirmed.
 If you know for certain which one it is, feel free to delete the
 branch you don't need.

 SECURITY NOTE: this endpoint returns internal employee data.
 Restrict it — e.g. an IP allowlist for your app server's IP,
 a shared secret header/token check, or both — before this goes
 anywhere near production. As written below it has NO auth at
 all; that's a placeholder, not a recommendation.
====================================================================
*/

require_once("../sfa_connection.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

$employee_master = "employee_master";
$res_data = array();

$is_pdo    = ($conn instanceof PDO);
$is_mysqli = ($conn instanceof mysqli);

if (!$is_pdo && !$is_mysqli) {
	$res_data = array("process_status" => "NO", "process_message" => "Unrecognized connection type from sfa_connection.php - expected mysqli or PDO.", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// small helpers so the rest of the script doesn't need to branch
// on connection type more than once
// --------------------------------------------------------------
function relay_escape($conn, $is_pdo, $val) {
	if ($is_pdo) {
		// PDO doesn't have a standalone escape-string call the same
		// way mysqli does; quote() includes the surrounding quotes.
		return $conn->quote($val);
	}
	return "'" . $conn->real_escape_string($val) . "'";
}

function relay_query($conn, $is_pdo, $sql) {
	if ($is_pdo) {
		$stmt = $conn->query($sql);
		return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
	}
	$result = $conn->query($sql);
	if (!$result) {
		return false;
	}
	$rows = array();
	while ($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}
	return $rows;
}

// --------------------------------------------------------------
// input
// --------------------------------------------------------------
$dns_emp_codes_raw = isset($_REQUEST["dns_emp_codes"]) ? trim($_REQUEST["dns_emp_codes"]) : "";

if ($dns_emp_codes_raw == '') {
	$res_data = array("process_status" => "NO", "process_message" => "dns_emp_codes is required (comma-separated).", "data" => array());
	echo json_encode($res_data);
	die;
}

$dns_codes = array();
foreach (explode(",", $dns_emp_codes_raw) as $dc) {
	$dc = trim($dc);
	if ($dc != '') {
		$dns_codes[] = $dc;
	}
}

if (count($dns_codes) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No valid dns_emp_codes found in input.", "data" => array());
	echo json_encode($res_data);
	die;
}

$dns_list_escaped = array();
foreach ($dns_codes as $dc) {
	$dns_list_escaped[] = relay_escape($conn, $is_pdo, $dc);
}
// mysqli's escape helper above already wraps in quotes for both branches
$dns_in_clause = implode(",", $dns_list_escaped);

// --------------------------------------------------------------
// STEP 1: reporting_to for those dns_emp_codes
// --------------------------------------------------------------
$sql_reporting = "SELECT `emp_code`,`dns_emp_code`,`reporting_to` FROM $employee_master
                   WHERE `dns_emp_code` IN ($dns_in_clause)";
$reporting_rows = relay_query($conn, $is_pdo, $sql_reporting);

$reporting_codes = array();
if ($reporting_rows) {
	foreach ($reporting_rows as $row) {
		$rt_val = trim($row["reporting_to"]);
		if ($rt_val != '') {
			foreach (explode(",", $rt_val) as $rt) {
				$rt = trim($rt);
				if ($rt != '' && !in_array($rt, $reporting_codes)) {
					$reporting_codes[] = $rt;
				}
			}
		}
	}
}

if (count($reporting_codes) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No reporting_to found for the given dns_emp_codes.", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 2: filter reporting_codes to level = 'L3'
// --------------------------------------------------------------
$rep_list_escaped = array();
foreach ($reporting_codes as $rc) {
	$rep_list_escaped[] = relay_escape($conn, $is_pdo, $rc);
}
$rep_in_clause = implode(",", $rep_list_escaped);

$sql_asm = "SELECT `emp_code`,`dns_emp_code`,`emp_name`,`level` FROM $employee_master
            WHERE `emp_code` IN ($rep_in_clause)
            AND `level`='L3'";
$asm_list = relay_query($conn, $is_pdo, $sql_asm);

if ($asm_list === false) {
	$asm_list = array();
}

if (count($asm_list) > 0) {
	$res_data = array("process_status" => "YES", "process_message" => "L3 ASM(s) found.", "data" => $asm_list);
} else {
	$res_data = array("process_status" => "NO", "process_message" => "No L3 employee found among the reporting_to codes.", "data" => array());
}

echo json_encode($res_data);
?>