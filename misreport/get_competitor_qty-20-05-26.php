<?php
header("Content-Type: text/plain");
date_default_timezone_set('Asia/Kolkata');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

set_time_limit(1000);
ini_set('memory_limit','512M');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/* =========================================================
   VALIDATE INPUT
========================================================= */

if(!isset($_GET['emp_code']) || $_GET['emp_code']==''){
    echo "emp_code required";
    exit;
}

$emp_code = mysqli_real_escape_string($conn, $_GET['emp_code']);

/* =========================================================
   FAST EMPLOYEE HIERARCHY
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
   EMPLOYEE CONDITION
========================================================= */

$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);

$emp_condition = "c1.emp_code IN ($employee_hierarchy)";

/* =========================================================
   MAIN QUERY
========================================================= */

$sql = "

SELECT 
    final_data.competitor_quantity_id,
    final_data.customer_name,
    final_data.customer_code,
    final_data.dns_customer_code,
    final_data.competitor_name,
    final_data.competitor_type,
    final_data.mandatory,
    final_data.qty

FROM
(

    /* =========================================================
       CASE 1 : market_feedback_details DATA EXISTS
    ========================================================= */

    SELECT 
        mfd.competitor_quantity_id,
        c.customer_name,
        c.customer_code,
        c.dns_customer_code,
        cgm.competitor_name,
        cgm.competitor_type,
        cgm.mandatory,
        mfd.qty

    FROM
    (
        SELECT
            customer_code,
            competitor_quantity_id,
            (marketfeedbackdetails_qty) AS qty
        FROM market_feedback_details
        GROUP BY customer_code, competitor_quantity_id
    ) mfd

    INNER JOIN customer_master c
        ON c.customer_code = mfd.customer_code

    INNER JOIN customer_route_emp_relation c1
        ON c1.customer_code = c.customer_code

    LEFT JOIN competitor_group_master_potential cgm
        ON cgm.id = mfd.competitor_quantity_id

    WHERE $emp_condition



    UNION ALL



    /* =========================================================
       CASE 2 : competitor_quantity DATA EXISTS
    ========================================================= */

    SELECT
        cq.competitor_quantity_id,
        c.customer_name,
        c.customer_code,
        c.dns_customer_code,
        cgm.competitor_name,
        cgm.competitor_type,
        cgm.mandatory,
        cq.qty

    FROM competitor_quantity cq

    INNER JOIN customer_master c
        ON c.customer_code = cq.customer_code

    INNER JOIN customer_route_emp_relation c1
        ON c1.customer_code = c.customer_code

    LEFT JOIN competitor_group_master_potential cgm
        ON cgm.id = cq.competitor_quantity_id

    LEFT JOIN
    (
        SELECT DISTINCT customer_code
        FROM market_feedback_details
    ) mfcheck
        ON mfcheck.customer_code = cq.customer_code

    WHERE
        cq.acedns = 'yes'
        AND mfcheck.customer_code IS NULL
        AND $emp_condition



    UNION ALL



    /* =========================================================
       CASE 3 : NO DATA ANYWHERE
    ========================================================= */

    SELECT
        cgm.id AS competitor_quantity_id,
        c.customer_name,
        c.customer_code,
        c.dns_customer_code,
        cgm.competitor_name,
        cgm.competitor_type,
        cgm.mandatory,
        0 AS qty

    FROM customer_master c

    INNER JOIN customer_route_emp_relation c1
        ON c1.customer_code = c.customer_code

    INNER JOIN competitor_group_master_potential cgm
        ON cgm.competitor_type =
            CASE
                WHEN c.zone IN ('NE1','NE2') THEN 'NE'
                ELSE 'ROE'
            END

    LEFT JOIN
    (
        SELECT DISTINCT customer_code
        FROM market_feedback_details
    ) mfd3
        ON mfd3.customer_code = c.customer_code

    LEFT JOIN competitor_quantity cq2
        ON cq2.customer_code = c.customer_code
        AND cq2.acedns = 'yes'

    WHERE
        mfd3.customer_code IS NULL
        AND cq2.customer_code IS NULL
        AND c.customer_name != ''
        AND $emp_condition

) final_data

ORDER BY
    final_data.customer_name ASC,
    final_data.competitor_name ASC

";

/* =========================================================
   EXECUTE QUERY
========================================================= */

$result = mysqli_query($conn, $sql);

if(!$result){

    echo "SQL ERROR : ".mysqli_error($conn);
    exit;
}

$total = mysqli_num_rows($result);

/* =========================================================
   OUTPUT
========================================================= */

echo $total . "¥8\n";

if($total > 0){

    while($row = mysqli_fetch_assoc($result)){

        if(str_contains($row['dns_customer_code'], ',')){
            $dns_customer_code = '';
        }else{
            $dns_customer_code = $row['dns_customer_code'];
        }

        echo
            $row['competitor_quantity_id'] . "^" .
            $row['customer_code'] . "^" .
            $row['customer_name'] . "^" .
            $row['mandatory'] . "^" .
            $row['competitor_name'] . "^" .
            $row['competitor_type'] . "^" .
            $row['qty'] . "^" .
            $dns_customer_code . "\n";
    }

}else{

    echo "No record found";
}

mysqli_close($conn);
exit;

?>