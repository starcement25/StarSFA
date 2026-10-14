<?php
ini_set('memory_limit', '1024M');
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= EXCEL DOWNLOAD ================= */
$filename = "SBG_Report_".date('Ymd_His').".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

/* ================= INPUT ================= */
$start_date = $_REQUEST['start_date'];
$end_date   = $_REQUEST['end_date'];
$branch     = $_REQUEST['branch'];
$employee   = $_REQUEST['employee'];
$type   = $_REQUEST['type'];


$employee_arg = str_replace("#", ",", $employee);
$employee_arg = str_replace("^", "'", $employee);

/* ================= BRANCH CONDITION ================= */
$branchcondition = ($branch == "all") ? "" : " AND CM.branch_code='$branch'";

/* ================= GET COMPETITOR LIST ================= */
 $sql_comp = "SELECT id, competitor_name, competitor_type 
             FROM competitor_group_master_potential
             WHERE status='yes' AND competitor_type='$type'
             ORDER BY competitor_type ASC, competitor_name ASC";

$res_comp = mysqli_query($link, $sql_comp);

$competitor_array = [];
while($row = mysqli_fetch_assoc($res_comp)){
    $competitor_array[$row['id']] = [
        'name' => $row['competitor_name'],
        'type' => $row['competitor_type']
    ];
}

$show_location_columns = (strtoupper($type) == 'NE');
$colspanheader = ($show_location_columns ? 16 : 12) + count($competitor_array);

/* ================= TABLE START ================= */
echo "<table border='1' style='border-collapse:collapse;'>";

/* ================= TITLE ================= */
echo "<tr style='background:#ccc;font-weight:bold;'>
<td colspan='{$colspanheader}' align='center'>
SBG ({$type})
</td>
</tr>";

/* ================= HEADER ================= */
echo "<tr style='background:#eee;font-weight:bold;'>
<td>SI</td>
<td>Date</td>
<td>Emp Code</td>
<td>Emp Name</td>
<td>Branch</td>
<td>Cust Type</td>
<td>Cust Code</td>
<td>Cust Name</td>
<td>Route</td>
<td>Phone</td>
<td>Universe Type</td>";

if($show_location_columns){
    echo "
<td>State</td>
<td>District</td>
<td>Block</td>
<td>Grampanchayat</td>";
}

foreach($competitor_array as $comp){
    echo "<td>{$comp['name']} <!--({$comp['type']})--></td>";
}

echo "<td>Total</td>";
echo "</tr>";

/* ================= MAIN QUERY ================= */
// $sql = "
// SELECT 
//     mfd.market_feedback_id,
//     mfd.customer_code,
//     mfd.competitor_quantity_id,
//     (mfd.marketfeedbackdetails_qty) AS qty,
//     mfut.universe_type,
//     mfut.customer_state,
//     mfut.customer_district,
//     mfut.customer_block_name,
//     mfut.customer_gram_panchayat,

//     CM.customer_name,
//     CM.phone_no,
//     CM.cust_type,
//     CM.dns_customer_code,
//     RM.route_name,
//     EM.emp_name,
//     EM.dns_emp_code,
//     BM.branch_name,

//     DATE_FORMAT(SUBSTRING(mfd.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date

// FROM market_feedback_details mfd

// LEFT JOIN market_feedback_universe_type mfut
//     ON mfd.market_feedback_id = mfut.market_feedback_id

// INNER JOIN competitor_group_master_potential cgm 
//     ON mfd.competitor_quantity_id = cgm.id

// INNER JOIN customer_master CM ON mfd.customer_code = CM.customer_code
// INNER JOIN route_master RM ON CM.route_code = RM.route_code
// INNER JOIN branch_master BM ON CM.branch_code = BM.branch_code
// INNER JOIN employee_master EM ON mfd.emp_code = EM.emp_code

// WHERE 
//     mfd.emp_code IN ($employee_arg)
//     AND cgm.competitor_type = '$type' 
//     AND (DATE_FORMAT(SUBSTRING(mfd.market_feedback_id,-14,8),'%Y-%m-%d') 
//         BETWEEN '$start_date' AND '$end_date')
//     $branchcondition

// GROUP BY 
//     mfd.market_feedback_id,
//     mfd.customer_code,
//     mfd.competitor_quantity_id

// ORDER BY visit_date DESC
// ";

$sql = "
SELECT
    mfd.market_feedback_id,
    mfd.customer_code,
    mfd.competitor_quantity_id,
    SUM(mfd.marketfeedbackdetails_qty) AS qty,

    ANY_VALUE(mfut.universe_type) AS universe_type,
    ANY_VALUE(mfut.customer_state) AS customer_state,
    ANY_VALUE(mfut.customer_district) AS customer_district,
    ANY_VALUE(mfut.customer_block_name) AS customer_block_name,
    ANY_VALUE(mfut.customer_gram_panchayat) AS customer_gram_panchayat,

    ANY_VALUE(CM.customer_name) AS customer_name,
    ANY_VALUE(CM.phone_no) AS phone_no,
    ANY_VALUE(CM.cust_type) AS cust_type,
    ANY_VALUE(CM.dns_customer_code) AS dns_customer_code,

    ANY_VALUE(RM.route_name) AS route_name,

    ANY_VALUE(EM.emp_name) AS emp_name,
    ANY_VALUE(EM.dns_emp_code) AS dns_emp_code,

    ANY_VALUE(BM.branch_name) AS branch_name,

    DATE_FORMAT(
        STR_TO_DATE(SUBSTRING(mfd.market_feedback_id, -14, 8), '%Y%m%d'),
        '%d-%m-%Y'
    ) AS visit_date

FROM market_feedback_details mfd

LEFT JOIN market_feedback_universe_type mfut
    ON mfd.market_feedback_id = mfut.market_feedback_id

INNER JOIN competitor_group_master_potential cgm
    ON mfd.competitor_quantity_id = cgm.id

INNER JOIN customer_master CM
    ON mfd.customer_code = CM.customer_code

INNER JOIN route_master RM
    ON CM.route_code = RM.route_code

INNER JOIN branch_master BM
    ON CM.branch_code = BM.branch_code

INNER JOIN employee_master EM
    ON mfd.emp_code = EM.emp_code

WHERE
    mfd.emp_code IN ($employee_arg)
    AND cgm.competitor_type = '$type'
    AND STR_TO_DATE(
            SUBSTRING(mfd.market_feedback_id,-14,8),
            '%Y%m%d'
        ) BETWEEN '$start_date' AND '$end_date'
    $branchcondition

GROUP BY
    mfd.market_feedback_id,
    mfd.customer_code,
    mfd.competitor_quantity_id

ORDER BY
    STR_TO_DATE(
        SUBSTRING(mfd.market_feedback_id,-14,8),
        '%Y%m%d'
    ) DESC
";
// echo"<pre>";print_r($sql);die;

$res = mysqli_query($link, $sql);
 
/* ================= DATA GROUP ================= */
$data = [];

while($row = mysqli_fetch_assoc($res)){

    $key = $row['dns_emp_code'].'#'.$row['customer_code'].'#'.$row['visit_date'];
    //$key = $row['dns_emp_code'].'#'.$row['customer_code'].'#'.$row['visit_date'].'#'.$row['market_feedback_id'];

    /*$data[$key] = [
        'emp_code' => $row['dns_emp_code'],
        'emp_name' => $row['emp_name'],
        'date' => $row['visit_date'],
        'customer_code' => $row['dns_customer_code'],
        'customer_name' => $row['customer_name'],
        'phone' => $row['phone_no'],
        'universe_type' => $row['universe_type'],
        'cust_type' => $row['cust_type'],
        'route' => $row['route_name'],
        'branch' => $row['branch_name'],
        'competitors' => []
    ];

    $data[$key]['competitors'][$row['competitor_quantity_id']] = $row['qty'];*/
    if(!isset($data[$key])){
    $data[$key] = [
        'emp_code' => $row['dns_emp_code'],
        'emp_name' => $row['emp_name'],
        'date' => $row['visit_date'],
        'customer_code' => $row['dns_customer_code'],
        'customer_name' => $row['customer_name'],
        'phone' => $row['phone_no'],
        'universe_type' => $row['universe_type'],
        'cust_type' => $row['cust_type'],
        'route' => $row['route_name'],
        'branch' => $row['branch_name'],
        'customer_state' => $row['customer_state'],
        'customer_district' => $row['customer_district'],
        'customer_block_name' => $row['customer_block_name'],
        'customer_gram_panchayat' => $row['customer_gram_panchayat'],
        'competitors' => []
    ];
}

$data[$key]['competitors'][$row['competitor_quantity_id']] = $row['qty'];
}

/* ================= OUTPUT ================= */
$count = 1;

foreach($data as $row){

    $total = 0;

    echo "<tr>
    <td>{$count}</td>
    <td>{$row['date']}</td>
    <td>{$row['emp_code']}</td>
    <td>{$row['emp_name']}</td>
    <td>{$row['branch']}</td>
    <td>{$row['cust_type']}</td>
    <td>{$row['customer_code']}</td>
    <td>{$row['customer_name']}</td>
    <td>{$row['route']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['universe_type']}</td>";

    if($show_location_columns){
        echo "
    <td>{$row['customer_state']}</td>
    <td>{$row['customer_district']}</td>
    <td>{$row['customer_block_name']}</td>
    <td>{$row['customer_gram_panchayat']}</td>";
    }

    foreach($competitor_array as $comp_id => $comp){

        if(isset($row['competitors'][$comp_id])){
            $qty = $row['competitors'][$comp_id];
            $total += $qty;
            $show = ($qty == 0) ? '' : $qty;
        } else {
            $show = '';
        }

        echo "<td align='right'>{$show}</td>";
    }

    echo "<td align='right'>{$total}</td>";
    echo "</tr>";

    $count++;
}

if(empty($data)){
    echo "<tr><td colspan='{$colspanheader}'>No Records Found</td></tr>";
}

echo "</table>";

mysqli_close($link);
exit;
?>
