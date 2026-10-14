<?php
ini_set('memory_limit', '1024M');
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
             ORDER BY mandatory ASC, competitor_name ASC";

$res_comp = mysqli_query($link, $sql_comp);

$competitor_array = [];
while($row = mysqli_fetch_assoc($res_comp)){
    $competitor_array[$row['id']] = [
        'name' => $row['competitor_name'],
        'type' => $row['competitor_type']
    ];
}

$colspanheader = 12 + count($competitor_array);
?>

<table border="1" style="border-collapse:collapse;" width="150%">
<tr class="TDHEAD">
    <td colspan="<?php echo $colspanheader; ?>" align="center">
        SBG (<?php echo $type; ?>)
    </td>
</tr>

<tr class="TDHEAD_SUB">
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
    <td>Universe Type</td>

    <?php
    foreach($competitor_array as $comp){
        echo "<td>{$comp['name']}<!--<br>({$comp['type']})--></td>";
    }
    ?>
    <td>Total</td>
</tr>

<?php
/* ================= MAIN QUERY ================= */
 
  $sql = "
SELECT 
    mfd.market_feedback_id,
    mfd.customer_code,
    mfd.competitor_quantity_id,
    (mfd.marketfeedbackdetails_qty) AS qty,

    cgm.competitor_type,

    CM.customer_name,
    CM.phone_no,
    CM.cust_type,
    CM.dns_customer_code,
    RM.route_name,
    EM.emp_name,
    EM.dns_emp_code,
    BM.branch_name,
    mfut.universe_type,
    DATE_FORMAT(SUBSTRING(mfd.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date

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
    AND (DATE_FORMAT(SUBSTRING(mfd.market_feedback_id,-14,8),'%Y-%m-%d') 
        BETWEEN '$start_date' AND '$end_date')
    $branchcondition

GROUP BY 
    mfd.market_feedback_id,
    mfd.customer_code,
    mfd.competitor_quantity_id

ORDER BY visit_date DESC
";

$res = mysqli_query($link, $sql);

/* ================= DATA GROUP ================= */
$data = [];

while($row = mysqli_fetch_assoc($res)){

    $key = $row['dns_emp_code'].'#'.$row['customer_code'].'#'.$row['visit_date'];
    //$key = $row['dns_emp_code'].'#'.$row['customer_code'].'#'.$row['visit_date'].'#'.$row['market_feedback_id'];

    if(!isset($data[$key])){
        $data[$key] = [
            'emp_code' => $row['dns_emp_code'],
            'emp_name' => $row['emp_name'],
            'date' => $row['visit_date'],
            'customer_code' => $row['dns_customer_code'],
            'customer_name' => $row['customer_name'],
            'phone' => $row['phone_no'],
            'cust_type' => $row['cust_type'],
            'route' => $row['route_name'],
            'branch' => $row['branch_name'],
            'universe_type' => $row['universe_type'],
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

    foreach($competitor_array as $comp_id => $comp){

        if(isset($row['competitors'][$comp_id])){
            $qty = $row['competitors'][$comp_id];
            $total += $qty;
            $show = ($qty == 0) ? '-' : $qty;
        } else {
            $show = '-';
        }

        echo "<td align='right'>{$show}</td>";
    }

    echo "<td align='right'>".($total==0?'-':$total)."</td>";
    echo "</tr>";

    $count++;
}

if(empty($data)){
    echo "<tr><td colspan='{$colspanheader}'>No Records Found</td></tr>";
}

mysqli_close($link);
?>

</table>