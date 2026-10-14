<?php
/* ==========================================================================
   MARKET FEEDBACK - PRICING : streaming CSV export
   --------------------------------------------------------------------------
   Mirrors admin_mis_market_pricing_data_customize_modified.php column for
   column, but streams the rows straight to the browser instead of collecting
   them in an array first, so a wide date range (Apr'26 - Sep'26) does not
   exhaust memory or hit max_execution_time.
   ========================================================================== */

ob_start();
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

if($_SESSION['admin_login']=="") { header("location:index.php"); exit; }

/* ================= NO LIMITS ================= */

set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '1024M');

/* ================= INPUT ================= */

$start_date = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$end_date   = isset($_REQUEST['end_date'])   ? trim($_REQUEST['end_date'])   : '';
$branch     = isset($_REQUEST['branch'])     ? trim($_REQUEST['branch'])     : '';
$employee   = isset($_REQUEST['employee'])   ? trim($_REQUEST['employee'])   : '';

/* market_feedback_id carries YYYYMMDDHHMMSS in its last 14 characters,
   so the range is compared against the raw YYYYMMDD substring.
   Accepts either YYYY-MM-DD or DD-MM-YYYY, with - or / as separator. */
$start_key = normalize_date_key($start_date);
$end_key   = normalize_date_key($end_date);

if($start_key === false || $end_key === false){

    while(ob_get_level()) ob_end_clean();

    echo "Invalid start date / end date.<br>";
    echo "Received start_date = '" . htmlspecialchars($start_date) . "', ";
    echo "end_date = '" . htmlspecialchars($end_date) . "'.<br><br>";
    echo "Open this report from the Market Feedback - Pricing screen and use the "
       . "Export button - this page needs the employee, branch and date range "
       . "passed in the URL and cannot be opened on its own.";
    exit;
}

if($start_key > $end_key){

    while(ob_get_level()) ob_end_clean();
    echo "Start date cannot be greater than end date.";
    exit;
}

/* ================= EMPLOYEE LIST ================= */
/* UI sends the list with # for comma and ^ for quote */

$employee_arg = str_replace("#", ",", $employee);
$employee_arg = str_replace("^", "'", $employee_arg);

$employee_list = array();

foreach(explode(",", $employee_arg) as $emp){

    $emp = trim($emp);
    $emp = trim($emp, "'");

    if($emp === '') continue;

    $employee_list[] = "'" . mysqli_real_escape_string($link, $emp) . "'";
}

if(empty($employee_list)){
    while(ob_get_level()) ob_end_clean();
    echo "No employee selected.";
    exit;
}

$employee_in = implode(",", $employee_list);

/* ================= BRANCH CONDITION ================= */

$branchcondition = ($branch == "all" || $branch == "")
    ? ''
    : " AND branch_code='" . mysqli_real_escape_string($link, $branch) . "'";

/* ================= COMPETITOR LIST ================= */
/* Fetched before the main query is opened, because the main query runs
   unbuffered and holds the connection until it is fully consumed. */

$sql_competitor_name = "SELECT DISTINCT competitor_name, acedns
                        FROM competitor_group_master
                        WHERE acedns='yes'
                        $branchcondition
                        AND branch_code IS NOT NULL
                        AND branch_code!=''
                        ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,
                                 competitor_name ASC";

$res_competitor_name = mysqli_query($link, $sql_competitor_name);

$competitor_name_array = array();
$competitor_string     = '';

while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){

    $competitor_name_array[] = $row_competitor_name['competitor_name'];

    $competitor_string .= "'" . mysqli_real_escape_string($link, $row_competitor_name['competitor_name']) . "',";
}

mysqli_free_result($res_competitor_name);

$competitor_string = rtrim($competitor_string, ",");

if($competitor_string === ''){
    while(ob_get_level()) ob_end_clean();
    echo "No competitor configured for this branch.";
    exit;
}

/* ================= CSV HEADERS ================= */

$filename = "Market_Feedback_Pricing_" . $start_key . "_" . $end_key . "_" . date('His') . ".csv";

while(ob_get_level()) ob_end_clean();

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
header("X-Accel-Buffering: no");

$out = fopen("php://output", "w");

/* BOM so Excel reads UTF-8 correctly */
fwrite($out, "\xEF\xBB\xBF");

/* ================= HEADER ROW 1 : competitor names ================= */

$fixed_headers = array(
    "SI. No",
    "Date of Visit",
    "Emp Code",
    "Emp Name",
    "Branch",
    "Cust Category",
    "Cust Code",
    "Cust Name",
    "Route Name",
    "Contact No"
);

$head1 = array();

foreach($fixed_headers as $h){
    $head1[] = "";
}

foreach($competitor_name_array as $competitor_name){

    /* name sits over the first of its five columns */
    $head1[] = $competitor_name;
    $head1[] = "";
    $head1[] = "";
    $head1[] = "";
    $head1[] = "";
}

fputcsv($out, $head1);

/* ================= HEADER ROW 2 : measures ================= */

$head2 = $fixed_headers;

foreach($competitor_name_array as $competitor_name){
    $head2[] = "Billing Ex";
    $head2[] = "Billing For";
    $head2[] = "WSP Ex";
    $head2[] = "WSP For";
    $head2[] = "RSP";
}

fputcsv($out, $head2);

flush();

/* ================= MAIN QUERY =================
   Ordered so that every row belonging to one (date, employee, customer)
   arrives together. That lets the loop below emit a line as soon as the
   group changes, instead of holding the whole result set in an array.
   visit_date_sort is a real YYYY-MM-DD so DESC actually sorts by date. */

$sql_competitor_stock = "
    SELECT
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        SUM(MF.PTD) AS PTD,
        SUM(MF.PTR) AS PTR,
        SUM(MF.PTC) AS PTC,
        SUM(MF.PV) AS PV,
        SUM(MF.billing_ex_for) AS billing_ex_for,
        SUM(MF.wsp_ex_for) AS wsp_ex_for,
        DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
        SUBSTRING(MF.market_feedback_id, -14, 8) AS visit_date_sort,
        BM.branch_name,
        MF.competitor_name,
        SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
        MF.customer_code
    FROM
        market_feedback MF,
        customer_master CM,
        employee_master EM,
        route_master RM,
        branch_master BM
    WHERE
        MF.customer_code = CM.customer_code
        AND CM.route_code = RM.route_code
        AND SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
        AND SUBSTRING(MF.market_feedback_id, 3, 5) IN ($employee_in)
        AND SUBSTRING(MF.market_feedback_id, -14, 8) BETWEEN '$start_key' AND '$end_key'
        AND MF.competitor_name IN ($competitor_string)
        AND CM.branch_code = BM.branch_code
    GROUP BY
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        visit_date_sort,
        BM.branch_name,
        MF.competitor_name,
        emp_code,
        MF.customer_code
    ORDER BY
        visit_date_sort DESC,
        EM.dns_emp_code ASC,
        CM.dns_customer_code ASC,
        FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
        MF.competitor_name ASC
";

/* Unbuffered : rows are pulled one at a time rather than the whole
   result set being copied into PHP memory up front. */

if(!mysqli_real_query($link, $sql_competitor_stock)){

    fputcsv($out, array("Query failed : " . mysqli_error($link)));
    fclose($out);
    exit;
}

$res_competitor_stock = mysqli_use_result($link);

/* ================= STREAM ROWS ================= */

$count       = 1;
$current_key = null;
$current     = null;
$competitors = array();

while($row = mysqli_fetch_assoc($res_competitor_stock)){

    $key = $row['dns_emp_code'] . '#' . $row['dns_customer_code'] . '#' . $row['visit_date'];

    if($key !== $current_key){

        /* previous group is complete - write it out */
        if($current_key !== null){
            writeRow($out, $count, $current, $competitors, $competitor_name_array);
            $count++;

            if($count % 500 == 0) flush();
        }

        $current_key = $key;
        $competitors = array();

        $current = array(
            'visit_date'        => $row['visit_date'],
            'dns_emp_code'      => $row['dns_emp_code'],
            'emp_name'          => $row['emp_name'],
            'branch_name'       => $row['branch_name'],
            'cust_type'         => $row['cust_type'],
            'dns_customer_code' => $row['dns_customer_code'],
            'customer_name'     => $row['customer_name'],
            'route_name'        => $row['route_name'],
            'phone_no'          => $row['phone_no']
        );
    }

    $competitors[$row['competitor_name']] = array(
        'PTD'            => $row['PTD'],
        'PTR'            => $row['PTR'],
        'PTC'            => $row['PTC'],
        'billing_ex_for' => $row['billing_ex_for'],
        'wsp_ex_for'     => $row['wsp_ex_for']
    );
}

/* last group */
if($current_key !== null){
    writeRow($out, $count, $current, $competitors, $competitor_name_array);
    $count++;
}

if($count == 1){
    fputcsv($out, array("No Records found"));
}

mysqli_free_result($res_competitor_stock);
mysqli_close($link);

fclose($out);
exit;


/* ================= FUNCTIONS ================= */

/* Returns the date as YYYYMMDD, or false when it cannot be read.
   Handles YYYY-MM-DD and DD-MM-YYYY, separated by - or / */
function normalize_date_key($date)
{
    $date = trim($date);

    if($date === '') return false;

    $date = str_replace('/', '-', $date);

    /* YYYY-MM-DD */
    if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date, $m)){
        $year  = $m[1];
        $month = $m[2];
        $day   = $m[3];
    }
    /* DD-MM-YYYY */
    else if(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $date, $m)){
        $day   = $m[1];
        $month = $m[2];
        $year  = $m[3];
    }
    else{
        return false;
    }

    if(!checkdate((int)$month, (int)$day, (int)$year)) return false;

    return $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);
}


function writeRow($out, $count, $current, $competitors, $competitor_name_array)
{
    $line = array(
        $count,
        $current['visit_date'],
        $current['dns_emp_code'],
        $current['emp_name'],
        $current['branch_name'],
        $current['cust_type'],
        $current['dns_customer_code'],
        $current['customer_name'],
        $current['route_name'],
        $current['phone_no']
    );

    foreach($competitor_name_array as $competitorval){

        if(isset($competitors[$competitorval])){

            $c = $competitors[$competitorval];

            $line[] = ($c['PTD'] == 0)            ? '-' : $c['PTD'];
            $line[] = ($c['billing_ex_for'] == 0) ? '-' : $c['billing_ex_for'];
            $line[] = ($c['PTR'] == 0)            ? '-' : $c['PTR'];
            $line[] = ($c['wsp_ex_for'] == 0)     ? '-' : $c['wsp_ex_for'];
            $line[] = ($c['PTC'] == 0)            ? '-' : $c['PTC'];
        }
        else{

            $line[] = '-';
            $line[] = '-';
            $line[] = '-';
            $line[] = '-';
            $line[] = '-';
        }
    }

    fputcsv($out, $line);
}
