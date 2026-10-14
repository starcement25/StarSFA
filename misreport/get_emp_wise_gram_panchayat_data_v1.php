<?php

header("Content-Type: text/plain");

date_default_timezone_set('Asia/Kolkata');

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

    $employees = [];

    $sql = "SELECT emp_code, reporting_to 
            FROM employee_master";

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

    $hierarchy = [];
    $queue = [$emp_code];

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
   EMPLOYEE FILTER
========================================================= */

$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);

/* =========================================================
   MAIN QUERY
========================================================= */

$sql = "

SELECT DISTINCT

    c.customer_code,
    c.customer_name,
    c.dns_customer_code,
    c.state_code,

    CASE
        WHEN c.zone IN ('NE1','NE2') THEN 'NE'
        ELSE 'ROE'
    END AS zone_type,

    csat.sfa_ac_type,
    csat.customer_state,
    csat.customer_district,
    csat.customer_block_name,
    csat.customer_gram_panchayat,
    csat.approved_by,
    csat.approval_status

FROM customer_master c

INNER JOIN customer_route_emp_relation crer
    ON crer.customer_code = c.customer_code
    AND crer.acedns = 'Y'

LEFT JOIN customer_sfa_ac_type csat
    ON csat.customer_code = c.customer_code

WHERE crer.emp_code IN ($employee_hierarchy)

ORDER BY c.customer_name ASC

";
// echo $sql;die;
/* =========================================================
   EXECUTE QUERY
========================================================= */

$result = mysqli_query($conn, $sql, MYSQLI_USE_RESULT);

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

    if(str_contains($row['dns_customer_code'], ',')){
        $dns_customer_code = '';
    }else{
        $dns_customer_code = $row['dns_customer_code'];
    }
    if($row['customer_state']!=''){
        $customer_state = $row['customer_state'];
    }else{
        $customer_state = $row['state_code'];
    }

    $data .=
        $row['customer_code'] . "^" .
        $row['customer_name'] . "^" .
        $dns_customer_code . "^" .
        $row['zone_type'] . "^" .
        $row['sfa_ac_type'] . "^" .
         $customer_state . "^" .
        $row['customer_district'] . "^" .
        $row['customer_block_name'] . "^" .
        $row['customer_gram_panchayat'] . "^" .
        $row['approval_status'] . "^" .
        $row['approved_by'] . "\n";
}

/* =========================================================
   FINAL OUTPUT
========================================================= */

echo $count . "¥11\n";
echo $data;

mysqli_close($conn);
exit;

?>