<?php
header("Content-Type: text/plain");
date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

$sql = "
SELECT 
    MAX(cq.competitor_quantity_id) as competitor_quantity_id,
    c.customer_name,
    cq.customer_code,
    cgm.competitor_name,
    cgm.id,
    cgm.competitor_type,
    SUM(cq.qty) as qty
FROM competitor_quantity cq

LEFT JOIN customer_master c 
    ON c.customer_code = cq.customer_code

INNER JOIN customer_route_emp_relation c1
    ON c1.customer_code = cq.customer_code

LEFT JOIN competitor_group_master_potential cgm
    ON cgm.id = cq.competitor_quantity_id

WHERE 
    cq.acedns = 'yes'
    AND $emp_condition

GROUP BY 
    c.customer_name,
    cq.customer_code, 
    cgm.competitor_name,
    cgm.competitor_type

ORDER BY c.customer_name, cgm.competitor_name ASC
";

$result = mysqli_query($conn, $sql);
$total = ($result) ? mysqli_num_rows($result) : 0;

/* ================= OUTPUT ================= */
echo $total."¥7 \n"; 

if($result && $total > 0){

    while($row = mysqli_fetch_assoc($result)){

        echo 
            $row['competitor_quantity_id'] . "^" .
            $row['customer_code'] . "^" .
            $row['customer_name'] . "^" .
            $row['id'] . "^" .
            $row['competitor_name'] . "^" .
            $row['competitor_type'] . "^" .
            $row['qty'] . "\n";
    }

}else{
    echo "No record found";
}

exit;
?>