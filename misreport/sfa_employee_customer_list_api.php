<?php
header("Content-Type: text/plain");

date_default_timezone_set('Asia/Kolkata');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

set_time_limit(1000);
ini_set('memory_limit','512M');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/* =========================================================
   VALIDATE INPUT
========================================================= */

if(!isset($_GET['emp_code']) || $_GET['emp_code'] == ''){

    echo "emp_code required";
    exit;
}

$emp_code = mysqli_real_escape_string($conn, $_GET['emp_code']);

/* =========================================================
   EMPLOYEE HIERARCHY
========================================================= */

function return_employee_hierarchy($emp_code, $conn){

    $employees = array();

    $sql = "SELECT emp_code, reporting_to FROM employee_master";

    $res = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($res)){

        $reporting_to = trim($row['reporting_to']);

        if($reporting_to == ''){
            continue;
        }

        $reportingArr = explode(',', $reporting_to);

        foreach($reportingArr as $manager){

            $manager = trim($manager);

            if($manager != ''){

                $employees[$manager][] = $row['emp_code'];
            }
        }
    }

    $hierarchy = array();
    $queue = array($emp_code);

    while(!empty($queue)){

        $current = array_shift($queue);

        if(isset($employees[$current])){

            foreach($employees[$current] as $child){

                if(!in_array($child, $hierarchy)){

                    $hierarchy[] = $child;
                    $queue[] = $child;
                }
            }
        }
    }

    $hierarchy[] = $emp_code;

    $hierarchy = array_unique($hierarchy);

    return "'" . implode("','", $hierarchy) . "'";
}

/* =========================================================
   GET EMPLOYEE HIERARCHY
========================================================= */

$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);

/* =========================================================
   GET CUSTOMER LIST
========================================================= */

$sql = "

SELECT DISTINCT
    c.customer_code,
    c.customer_name,
    c.SAP_customer_code as customer_id,
    c.zone

FROM customer_master c

INNER JOIN customer_route_emp_relation cre
    ON cre.customer_code = c.customer_code

WHERE cre.emp_code IN ($employee_hierarchy)
AND cre.acedns='Y'
AND c.SAP_customer_code!=''

ORDER BY c.customer_name ASC

";

$result = mysqli_query($conn, $sql);

if(!$result){

    echo "SQL ERROR : " . mysqli_error($conn);
    exit;
}

/* =========================================================
   OUTPUT
========================================================= */

$count = 0;
$data  = '';

while($row = mysqli_fetch_assoc($result)){

    $count++;

    $customer_code = trim($row['customer_code']);
    $customer_name = trim($row['customer_name']);
    $customer_id   = trim($row['customer_id']);
    $zone          = trim($row['zone']);

    $data .=
        $customer_code . "^" .
        $customer_name . "^" .
        $customer_id . "^" .
        $zone . "\n";
}

/* =========================================================
   FINAL OUTPUT
========================================================= */

echo $count . "¥8\n";
echo $data;

mysqli_close($conn);
exit;

?>