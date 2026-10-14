<?php
/*
====================================================================
 get_pending_allocation_list_by_emp_code.php
 --------------------------------------------------------------
 DEPLOY THIS ON THE SFA SERVER (sfa.starcement.co.in), alongside
 api_get_asm_from_so.php.

 Purpose : Let a caller pass this server's OWN emp_code (instead
           of the primary server's dns_emp_code), and get back the
           same pending allocation-update list that
           get_new_allocation_update_list_asm.php (on the PRIMARY
           server, dev.starsaathi.com) returns for that ASM.

 Logic:
   1. employee_master (LOCAL, this server)
        WHERE emp_code = <input emp_code>
      -> read dns_emp_code.

   2. HTTP GET to the PRIMARY server:
        https://dev.starsaathi.com/SAP/admin/get_new_allocation_update_list_asm.php?dns_emp_code=<dns_emp_code from step 1>
      -> pass that response straight through, unchanged.

 Input (POST or GET):
   emp_code - this server's own emp_code for the ASM

 Output: identical shape to get_new_allocation_update_list_asm.php
   (passed through as-is), e.g.:
   {
     "process_status": "YES",
     "process_message": "...",
     "dns_emp_code": "...",
     "data": [ ... ]
   }

 --------------------------------------------------------------
 CONNECTION: uses $localDB->conn from sfa_connection.php, same as
 api_get_asm_from_so.php — auto-detects mysqli vs PDO.

 SECURITY NOTE: as with api_get_asm_from_so.php, this has no auth
 as written. Add one (shared token / IP allowlist) before this
 goes live, matching whatever's decided for that endpoint.
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

// same small helpers as api_get_asm_from_so.php
function relay_escape($conn, $is_pdo, $val) {
	if ($is_pdo) {
		return $conn->quote($val);
	}
	return "'" . $conn->real_escape_string($val) . "'";
}

function relay_query_one($conn, $is_pdo, $sql) {
	if ($is_pdo) {
		$stmt = $conn->query($sql);
		return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
	}
	$result = $conn->query($sql);
	if (!$result) {
		return false;
	}
	return $result->fetch_assoc();
}

// --------------------------------------------------------------
// input
// --------------------------------------------------------------
$emp_code = isset($_REQUEST["emp_code"]) ? trim($_REQUEST["emp_code"]) : "";

if ($emp_code == '') {
	$res_data = array("process_status" => "NO", "process_message" => "emp_code is required.", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 1: resolve dns_emp_code locally
// --------------------------------------------------------------
$emp_code_escaped = relay_escape($conn, $is_pdo, $emp_code);

$sql_emp = "SELECT `emp_code`,`dns_emp_code` FROM $employee_master
            WHERE `emp_code`=$emp_code_escaped
            LIMIT 1";
$emp_row = relay_query_one($conn, $is_pdo, $sql_emp);

if (!$emp_row || empty($emp_row["dns_emp_code"])) {
	$res_data = array("process_status" => "NO", "process_message" => "No dns_emp_code found for this emp_code.", "data" => array());
	echo json_encode($res_data);
	die;
}

$dns_emp_code = trim($emp_row["dns_emp_code"]);

// --------------------------------------------------------------
// STEP 2: call the PRIMARY server's pending-list endpoint and
// pass its response straight through
// --------------------------------------------------------------
$primary_url = "https://dev.starsaathi.com/SAP/admin/get_new_allocation_update_list_asm.php";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $primary_url . "?" . http_build_query(array("dns_emp_code" => $dns_emp_code)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);

// -----------------------------------------------------------------
// SSL CA BUNDLE HANDLING
// This server's cURL can't locate/trust a CA bundle by default
// ("unable to get local issuer certificate"). Try a couple of
// common system paths first; only fall back to disabling
// verification (insecure - MITM risk) if none are found, and flag
// that clearly in the response so it's never silently insecure.
// -----------------------------------------------------------------
$ca_bundle_candidates = array(
	"/etc/ssl/certs/ca-certificates.crt",   // Debian/Ubuntu
	"/etc/pki/tls/certs/ca-bundle.crt",     // RHEL/CentOS
	"/etc/ssl/cert.pem",                    // some other distros
	__DIR__ . "/cacert.pem"                 // manually placed bundle, if you download one - see note below
);

$ca_bundle_used = null;
foreach ($ca_bundle_candidates as $candidate) {
	if (file_exists($candidate)) {
		$ca_bundle_used = $candidate;
		break;
	}
}

$ssl_verification_disabled = false;
if ($ca_bundle_used) {
	curl_setopt($ch, CURLOPT_CAINFO, $ca_bundle_used);
} else {
	// No usable CA bundle found on this server. Falling back to
	// disabling verification so the endpoint keeps working, but
	// this is insecure (no protection against a MITM on this
	// connection) - fix properly per the note below when you can.
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$ssl_verification_disabled = true;
}

$primary_response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($primary_response === false || $curl_error != '') {
	$res_data = array("process_status" => "NO", "process_message" => "Failed to reach primary server: " . $curl_error, "ssl_verification_disabled" => $ssl_verification_disabled, "data" => array());
	echo json_encode($res_data);
	die;
}

$primary_json = json_decode($primary_response, true);

if (!is_array($primary_json)) {
	// primary server didn't return valid JSON - pass the raw text back for visibility rather than silently failing
	$res_data = array("process_status" => "NO", "process_message" => "Primary server returned an unexpected (non-JSON) response.", "raw_response" => $primary_response, "ssl_verification_disabled" => $ssl_verification_disabled, "data" => array());
	echo json_encode($res_data);
	die;
}

// pass the primary server's response straight through, adding a
// warning flag only if SSL verification had to be disabled
if ($ssl_verification_disabled) {
	$primary_json["ssl_verification_disabled"] = true;
	$primary_json["ssl_warning"] = "No CA bundle found on this server - this request was NOT verified against a trusted certificate. Fix the CA bundle (see script comments) before relying on this in production.";
}
echo json_encode($primary_json);
?>