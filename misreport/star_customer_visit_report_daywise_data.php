<?php
ob_start();
session_start();

ini_set('memory_limit', '512M');
set_time_limit(0);

// Uncomment below two lines for debugging if needed
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$employee     = $_REQUEST['employee'];
$employee_arg = str_replace("#", ",", $employee);
$employee_arg = str_replace("^", "'", $employee_arg);

$zone       = $_REQUEST['zone'];
$state      = $_REQUEST['state'];
$branch     = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date   = $_REQUEST['end_date'];

$purpose_of_visit = "";

if(strpos($zone,",") == FALSE)       $zone       = str_replace("'","",$zone);
else                                 $zone       = "All";

if(strpos($state,",") == FALSE)      $state      = str_replace("'","",$state);
else                                 $state      = "All";

if(strpos($branch,",") == FALSE)     $branch     = str_replace("'","",$branch);
else                                 $branch     = "All";

if(strpos($department,",") == FALSE) $department = str_replace("'","",$department);
else                                 $department = "All";

if(strpos($employee,",") == FALSE){
    $new_emp_code = str_replace("'","",$employee);
    $sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
    $res_emp_name = mysqli_query($link, $sql_emp_name);
    $row_emp_name = mysqli_fetch_assoc($res_emp_name);
    $new_emp_name = $row_emp_name['emp_name'];
} else {
    $new_emp_name = "All";
}

// ============================================================
// Office plant exclusion list
// ============================================================
$sql_office_plant    = "SELECT customer_code FROM office_plant_master";
$res_office_plant    = mysqli_query($link, $sql_office_plant);
$office_plant_string = '';
while($row_office_plant = mysqli_fetch_assoc($res_office_plant)){
    $office_plant_string .= "'".$row_office_plant['customer_code']."',";
}
$office_plant_string = rtrim($office_plant_string, ',');
if($office_plant_string == "") $office_plant_string = "''";

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state
    ."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department
    ."&nbsp;&nbsp;Employee:".$new_emp_name
    ."&nbsp;&nbsp;From:".date('d-m-Y', strtotime($start_date))
    ."&nbsp;&nbsp;To:".date('d-m-Y', strtotime($end_date));

// ============================================================
// Main report query
// Check-in / Check-out fully resolved in SQL via LEFT JOINs:
//   CIO  = check_in_out_details  (priority 1: per customer per day)
//   LOCI = location AE%          (priority 2 fallback: daily check-in)
//   LOCO = location CHE%         (priority 2 fallback: daily check-out)
// COALESCE picks CIO first, falls back to location table.
// No PHP pre-fetch arrays needed — zero memory overhead.
// ============================================================
$sql_customer = "SELECT * FROM (
                    SELECT DISTINCT CVD.emp_code,
                    DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d') AS visit_date,
                    SUBSTRING(CVD.trans_id,-14,14) AS visit_date_parts,
                    CVD.customer_code, CVD.customer_name, CVD.route_name, CVD.route_code,
                    CVD.cust_type, EM.emp_name, EM.dns_emp_code, CM.dns_customer_code,
                    BM.branch_name, CVD.trans_id, CVD.hint_remarks,
                    LO.purpose_of_visit, LO.date AS ch_date, LO.updatetime AS ch_o_date,
                    COALESCE(
                        TIME_FORMAT(CIO.check_in_time,  '%H:%i:%s'),
                        TIME_FORMAT(LOCI.loc_time,      '%H:%i:%s')
                    ) AS check_in_time,
                    COALESCE(
                        TIME_FORMAT(CIO.check_out_time, '%H:%i:%s'),
                        TIME_FORMAT(LOCO.loc_time,      '%H:%i:%s')
                    ) AS check_out_time
                    FROM customer_visit_details CVD
                    JOIN employee_master EM ON CVD.emp_code      = EM.emp_code
                    JOIN customer_master CM ON CVD.customer_code = CM.customer_code
                    JOIN branch_master   BM ON CM.branch_code    = BM.branch_code
                    JOIN location        LO ON LO.trans_id       = CVD.trans_id
                    LEFT JOIN check_in_out_details CIO
                        ON  CIO.customer_code = CVD.customer_code
                        AND DATE_FORMAT(SUBSTRING(CIO.trans_id,-14,8),'%Y-%m-%d')
                            = DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d')
                        AND SUBSTRING(CIO.trans_id,3,5) = CVD.emp_code
                    LEFT JOIN (
                        SELECT emp_code, DATE(date) AS loc_date, MIN(date) AS loc_time
                        FROM location
                        WHERE DATE(date) BETWEEN '".$start_date."' AND '".$end_date."'
                          AND trans_id LIKE 'AE%'
                        GROUP BY emp_code, DATE(date)
                    ) AS LOCI ON LOCI.emp_code = CVD.emp_code
                             AND LOCI.loc_date  = DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d')
                    LEFT JOIN (
                        SELECT emp_code, DATE(date) AS loc_date, MIN(date) AS loc_time
                        FROM location
                        WHERE DATE(date) BETWEEN '".$start_date."' AND '".$end_date."'
                          AND trans_id LIKE 'CHE%'
                        GROUP BY emp_code, DATE(date)
                    ) AS LOCO ON LOCO.emp_code = CVD.emp_code
                             AND LOCO.loc_date  = DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d')
                    WHERE CVD.emp_code IN(".$employee.")
                    AND (SUBSTRING(CVD.trans_id,-14,8) BETWEEN
                        '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."')
                    AND CM.dns_customer_code NOT IN(".$office_plant_string.")
                    ORDER BY SUBSTRING(CVD.trans_id,-14,14) DESC
                 ) AS SAT
                 ORDER BY SAT.emp_name ASC";

$res_customer   = mysqli_query($link, $sql_customer);
$total_customer = mysqli_num_rows($res_customer);

if($total_customer > 0){
    $count = 1;
    ?>
    <table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
      <tr class="TDHEAD_SUB">
        <td colspan="15"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD" align="center">
        <td>SI</td>
        <td>Date of Visit</td>
        <td>Customer Code</td>
        <td>Customer Name</td>
        <td>Route</td>
        <td>Type</td>
        <td>Branch</td>
        <td>Employee Code</td>
        <td>Employee Name</td>
        <td>Check In Time</td>
        <td>Check Out Time</td>
        <td>Duration</td>
        <td>Visit Status(Productive / Non productive)</td>
        <td>Remarks</td>
        <td>Purpose Of Visit</td>
      </tr>
    <?php
    $customer_code_array = array();

    while($row_customer = mysqli_fetch_assoc($res_customer)){
        $emp_code            = $row_customer['emp_code'];
        $dns_emp_code        = $row_customer['dns_emp_code'];
        $emp_name            = $row_customer['emp_name'];
        $customer_code       = $row_customer['customer_code'];
        $customer_name       = $row_customer['customer_name'];
        $dns_customer_code   = $row_customer['dns_customer_code'];
        $route_name          = $row_customer['route_name'];
        $route_code          = $row_customer['route_code'];
        $cust_type           = $row_customer['cust_type'];
        $branch_name         = $row_customer['branch_name'];
        $trans_id            = $row_customer['trans_id'];
        $visit_date_parts    = $row_customer['visit_date_parts'];
        $visit_date          = date('d-m-Y', strtotime(substr($trans_id,-14,8)));
        $visit_date_formated = $row_customer['visit_date'];    // YYYY-MM-DD
        $remark              = $row_customer['hint_remarks'];
        $purpose_of_visit    = $row_customer['purpose_of_visit'];

        // --------------------------------------------------------
        // Check-in / Check-out — resolved directly from SQL result
        // --------------------------------------------------------
        $check_in_time  = $row_customer['check_in_time']  ?? '';
        $check_out_time = $row_customer['check_out_time'] ?? '';

        // --------------------------------------------------------
        // Visit status
        // --------------------------------------------------------
        $transid_substr              = substr($trans_id, 0, 2);
        $transid_substr_first_string = substr($trans_id, 0, 1);
        $visit_status = '';
        if($transid_substr == "OE" || $transid_substr == "SE" || $transid_substr == "PE"){
            $visit_status = 'Productive';
        } elseif($transid_substr == "NS" || $transid_substr == "NO" || $transid_substr == "NC"){
            $visit_status = 'Non Productive';
        }
        if($transid_substr_first_string == 'M') $visit_status = 'Productive';

        // --------------------------------------------------------
        // Calculate duration
        // --------------------------------------------------------
        $time_duration         = '';
        $time_difference_final = 0;
        if($check_in_time !== '' && $check_out_time !== ''){
            $time_difference_final = strtotime($check_out_time) - strtotime($check_in_time);
            if($time_difference_final >= 3600){
                $hours         = floor($time_difference_final / 3600);
                $minutes       = floor(($time_difference_final / 60) % 60);
                $seconds       = $time_difference_final % 60;
                $time_duration = $hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
            } elseif($time_difference_final >= 60){
                $minutes       = floor(($time_difference_final / 60) % 60);
                $seconds       = $time_difference_final % 60;
                $time_duration = $minutes.' Minute(s) '.$seconds.' Second(s)';
            } else {
                $time_duration = $time_difference_final.' Second(s)';
            }
        }

        // --------------------------------------------------------
        // Dedup and output row
        // --------------------------------------------------------
        $customer_chk_str = $visit_date . $dns_customer_code . $emp_code;

        if(!in_array($customer_chk_str, $customer_code_array)){
            echo "<tr>
                    <td>".$count."</td>
                    <td>".$visit_date."</td>
                    <td>".$dns_customer_code."</td>
                    <td>".$customer_name."</td>
                    <td>".$route_name."</td>
                    <td>".$cust_type."</td>
                    <td>".$branch_name."</td>
                    <td>".$dns_emp_code."</td>
                    <td>".$emp_name."</td>
                    <td>".$check_in_time."</td>
                    <td>".$check_out_time."</td>
                    <td>".$time_duration."</td>
                    <td>".$visit_status."</td>
                    <td>".$remark."</td>
                    <td>".$purpose_of_visit."</td>
                  </tr>";
            $count++;
            array_push($customer_code_array, $customer_chk_str);
        }
    }
    ?>
    </table>
    <?php
} else {
    echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
}
?>