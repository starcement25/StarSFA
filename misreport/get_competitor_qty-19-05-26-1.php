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

/* ================= EMPLOYEE HIERARCHY FUNCTIONS ================= */

function return_employee_hierarchy($emp_code, $conn) {
    $emphierarchy = array();

    employee_hierarchy_details($emp_code, $emphierarchy, $conn);

    $emphierarchystring = '';

    foreach($emphierarchy as $hierarchyval){
        $emphierarchystring .= $hierarchyval . ',';
    }

    $emphierarchystring = rtrim($emphierarchystring, ',');

    // Ensure current emp_code included
    if(strpos($emphierarchystring, "'".$emp_code."'") === false){
        if($emphierarchystring != ''){
            $emphierarchystring .= ",";
        }
        $emphierarchystring .= "'".$emp_code."'";
    }

    return $emphierarchystring;
}

function employee_hierarchy_details($emp_code, &$emphierarchy, $link){

    $sql = "SELECT emp_code 
            FROM employee_master 
            WHERE FIND_IN_SET('$emp_code', reporting_to)";

    $res = mysqli_query($link, $sql);

    if($res && mysqli_num_rows($res) > 0){
        while($row = mysqli_fetch_assoc($res)){
            $child = "'".$row['emp_code']."'";

            if(!in_array($child, $emphierarchy)){
                $emphierarchy[] = $child;
                employee_hierarchy_details($row['emp_code'], $emphierarchy, $link);
            }
        }
    }
}

/* ================= VALIDATE INPUT ================= */

if(!isset($_GET['emp_code'])){
    echo "emp_code required";
    exit;
}

$emp_code = mysqli_real_escape_string($conn, $_GET['emp_code']);

/* ================= EMP CONDITION ================= */

$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);
$emp_condition = "c1.emp_code IN ($employee_hierarchy)";

/* ================= QUERY ================= */

//  $sql = "
// SELECT 
//     cq.competitor_quantity_id,
//     c.customer_name,
//     cq.customer_code,
//     c.dns_customer_code,
//     cgm.competitor_name,
//     cgm.id,
//     cgm.competitor_type,
//     cgm.mandatory,
//     cq.qty
// FROM competitor_quantity cq

// LEFT JOIN customer_master c 
//     ON c.customer_code = cq.customer_code

// INNER JOIN customer_route_emp_relation c1
//     ON c1.customer_code = cq.customer_code

// LEFT JOIN competitor_group_master_potential cgm
//     ON cgm.id = cq.competitor_quantity_id

// WHERE 
//     cq.acedns = 'yes'
//     AND $emp_condition

// GROUP BY 
//     c.customer_name,
//     cq.customer_code, 
//     cgm.competitor_name,
//     cgm.competitor_type

// ORDER BY c.customer_name, cgm.competitor_name ASC
// ";
/*
$sql = "


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

WHERE 
    cq.acedns = 'yes'
    AND $emp_condition


UNION ALL



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

WHERE 
    $emp_condition

    
    AND NOT EXISTS (
        SELECT 1 
        FROM competitor_quantity cq2
        WHERE cq2.customer_code = c.customer_code
        AND cq2.acedns = 'yes' 
    )
AND customer_name !=''


ORDER BY customer_name, competitor_name ASC
";
*/
/*
 $sql = "

SELECT 
    competitor_quantity_id,
    customer_name,
    customer_code,
    dns_customer_code,
    competitor_name,
    competitor_type,
    mandatory,
    (qty) AS qty

FROM (

     ================= CASE 1: DATA EXISTS ================= *
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

    WHERE 
        cq.acedns = 'yes'
        AND $emp_condition


    UNION ALL


     ================= CASE 2: NO DATA ================= 
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

    WHERE 
        $emp_condition
        AND NOT EXISTS (
            SELECT 1 
            FROM competitor_quantity cq2
            WHERE cq2.customer_code = c.customer_code
            AND cq2.acedns = 'yes'
        )
            AND customer_name !=''

) AS final_data

GROUP BY 
    customer_name,
    customer_code,
    competitor_name,
    competitor_type

ORDER BY 
    customer_name,
    competitor_name ASC
";*/
//change 19-05-26
$sql1 = "

SELECT 
    competitor_quantity_id,
    customer_name,
    customer_code,
    dns_customer_code,
    competitor_name,
    competitor_type,
    mandatory,
    (qty) AS qty

FROM (

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
        SUM(mfd.marketfeedbackdetails_qty) AS qty

    FROM market_feedback_details mfd

    INNER JOIN customer_master c
        ON c.customer_code = mfd.customer_code

    INNER JOIN customer_route_emp_relation c1
        ON c1.customer_code = c.customer_code

    LEFT JOIN competitor_group_master_potential cgm
        ON cgm.id = mfd.competitor_quantity_id

    WHERE
        $emp_condition

    GROUP BY
        mfd.customer_code,
        mfd.competitor_quantity_id



    UNION ALL



    /* =========================================================
       CASE 2 : competitor_quantity DATA EXISTS
       ONLY IF market_feedback_details NOT EXISTS
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

    WHERE
        cq.acedns = 'yes'
        AND $emp_condition

        AND NOT EXISTS (
            SELECT 1
            FROM market_feedback_details mfd2
            WHERE mfd2.customer_code = cq.customer_code
        )



    UNION ALL



    /* =========================================================
       CASE 3 : NO DATA FOUND ANYWHERE
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

    WHERE
        $emp_condition

        /* no market feedback */
        AND NOT EXISTS (
            SELECT 1
            FROM market_feedback_details mfd3
            WHERE mfd3.customer_code = c.customer_code
        )

        /* no competitor quantity */
        AND NOT EXISTS (
            SELECT 1
            FROM competitor_quantity cq2
            WHERE cq2.customer_code = c.customer_code
            AND cq2.acedns = 'yes'
        )

        AND c.customer_name != ''

) AS final_data

GROUP BY
    customer_name,
    customer_code,
    competitor_name,
    competitor_type

ORDER BY
    customer_name ASC,
    competitor_name ASC
";
//change 19-05-26
$sql = "

SELECT 
    competitor_quantity_id,
    customer_name,
    customer_code,
    dns_customer_code,
    competitor_name,
    competitor_type,
    mandatory,
    qty

FROM (

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
        SUM(mfd.marketfeedbackdetails_qty) AS qty

    FROM market_feedback_details mfd

    INNER JOIN customer_master c
        ON c.customer_code = mfd.customer_code

    INNER JOIN customer_route_emp_relation c1
        ON c1.customer_code = c.customer_code

    LEFT JOIN competitor_group_master_potential cgm
        ON cgm.id = mfd.competitor_quantity_id

    WHERE
        $emp_condition

    GROUP BY
        mfd.customer_code,
        mfd.competitor_quantity_id



    UNION ALL



    /* =========================================================
       CASE 2 : NO DATA FOUND
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

    WHERE
        $emp_condition

        AND NOT EXISTS (
            SELECT 1
            FROM market_feedback_details mfd2
            WHERE mfd2.customer_code = c.customer_code
        )

        AND c.customer_name != ''

) AS final_data

GROUP BY
    customer_name,
    customer_code,
    competitor_name,
    competitor_type

ORDER BY
    customer_name ASC,
    competitor_name ASC
";
//die;
$result = mysqli_query($conn, $sql);
$total = ($result) ? mysqli_num_rows($result) : 0;

/* ================= OUTPUT ================= */
echo $total."¥8 \n"; 

if (str_contains($row['dns_customer_code'], ',')) {
$dns_customer_code='';
}else{
    $dns_customer_code=$row['dns_customer_code'];
}
if($result && $total > 0){

    while($row = mysqli_fetch_assoc($result)){

        echo 
            $row['competitor_quantity_id'] . "^" .
            $row['customer_code'] . "^" .
            $row['customer_name'] . "^" .
            $row['mandatory'] . "^" .
            $row['competitor_name'] . "^" .
            $row['competitor_type'] . "^" .
            $row['qty'] . "^" .
            $row['dns_customer_code'] . "\n";
    }

}else{
    echo "No record found";
}

exit;
?>