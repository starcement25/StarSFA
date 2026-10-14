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

$report_type = isset($_REQUEST['report_type']) ? $_REQUEST['report_type'] : 'ocr';
$zone        = isset($_REQUEST['zone'])       ? trim($_REQUEST['zone'])       : '';
$branch      = isset($_REQUEST['branch'])     ? trim($_REQUEST['branch'])     : '';
$start_date  = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$end_date    = isset($_REQUEST['end_date'])   ? trim($_REQUEST['end_date'])   : '';

/* ---------- sanitise ---------- */
$zone       = mysqli_real_escape_string($link, $zone);
$branch     = mysqli_real_escape_string($link, $branch);
$start_date = mysqli_real_escape_string($link, $start_date);
$end_date   = mysqli_real_escape_string($link, $end_date);

/* ---------- shared table styling ---------- */
$ocr_table_style = "
<style>
    #display_table { border-collapse:collapse; font-size:12px; }
    #display_table td { padding:6px 9px; vertical-align:middle; }
    #display_table tr.TDHEAD td { white-space:nowrap; vertical-align:middle; }
    #display_table a.ocr-csv-btn {
        display:inline-block; padding:4px 12px; background:#a4315f; color:#ffffff;
        text-decoration:none; border-radius:4px; font-size:11px; font-weight:bold; white-space:nowrap;
    }
    #display_table a.ocr-csv-btn:hover { background:#7f2549; }
</style>";

/* ---------- header string ---------- */
$header_string = "Zone:".($zone != '' ? $zone : 'All')
    ."&nbsp;&nbsp;Branch:".($branch != '' ? $branch : 'All');
if($start_date != '' && $end_date != ''){
    $header_string .= "&nbsp;&nbsp;From:".date('d-m-Y', strtotime($start_date))
        ."&nbsp;&nbsp;To:".date('d-m-Y', strtotime($end_date));
}

/* =====================================================================
 *  REPORT 2 : PARTICIPANTS DATA
 *  (a participant must not appear more than once -> GROUP BY name+contact)
 * ===================================================================== */
if($report_type == 'participants'){

    // Filters applied inside the de-dup subquery (aliases suffixed with 2).
    $where = " WHERE 1 ";
    if($start_date != '' && $end_date != '') $where .= " AND p2.date BETWEEN '$start_date' AND '$end_date' ";
    if($zone   != '') $where .= " AND EM2.zone = '$zone' ";
    if($branch != '') $where .= " AND m2.branch_code = '$branch' ";

    // A participant must not appear more than once: keep one row per
    // (participant_name, contact_no) via MAX(id) -> latest inserted record.
    // Written to be compatible with sql_mode=only_full_group_by.
    $sql = "SELECT m.employee_code,
                   EM.emp_name,
                   CM.customer_name AS dealer_name,
                   m.dealer_code,
                   p.participant_name,
                   p.address,
                   p.contact_no,
                   p.ilp_registered,
                   p.date
            FROM ocr_participants_data p
            JOIN ocr_main_data m         ON m.id       = p.ocr_main_data_id
            LEFT JOIN employee_master EM ON EM.emp_code = m.employee_code
            LEFT JOIN customer_master CM ON CM.customer_code = m.dealer_code
            JOIN (
                SELECT MAX(p2.id) AS max_id
                FROM ocr_participants_data p2
                JOIN ocr_main_data m2         ON m2.id       = p2.ocr_main_data_id
                LEFT JOIN employee_master EM2 ON EM2.emp_code = m2.employee_code
                $where
                GROUP BY p2.participant_name, p2.contact_no
            ) latest ON latest.max_id = p.id
            ORDER BY p.participant_name ASC";

    $res   = mysqli_query($link, $sql);
    $total = $res ? mysqli_num_rows($res) : 0;

    if($total > 0){
        $count = 1;
        echo $ocr_table_style;
        ?>
        <table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB"><td colspan="10"><?php echo $header_string; ?></td></tr>
          <tr class="TDHEAD" align="center">
            <td>SI</td>
            <td>Employee Code</td>
            <td>Employee Name</td>
            <td>Dealer/Sub-Dealer Name</td>
            <td>Dealer/Sub-Dealer Code</td>
            <td>Participant Name</td>
            <td>Address</td>
            <td>Contact No.</td>
            <td>ILP Registered</td>
            <td>Date</td>
          </tr>
        <?php
        while($row = mysqli_fetch_assoc($res)){
            $date_disp = ($row['date'] != '' && $row['date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['date'])) : '';
            echo "<tr>
                    <td>".$count."</td>
                    <td>".htmlspecialchars($row['employee_code'])."</td>
                    <td>".htmlspecialchars($row['emp_name'])."</td>
                    <td>".htmlspecialchars($row['dealer_name'])."</td>
                    <td>".htmlspecialchars($row['dealer_code'])."</td>
                    <td>".htmlspecialchars($row['participant_name'])."</td>
                    <td>".htmlspecialchars($row['address'])."</td>
                    <td>".htmlspecialchars($row['contact_no'])."</td>
                    <td>".htmlspecialchars($row['ilp_registered'])."</td>
                    <td>".$date_disp."</td>
                  </tr>";
            $count++;
        }
        ?>
        </table>
        <?php
    } else {
        echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
    }

/* =====================================================================
 *  REPORT 1 : OCR DATA  (default)
 * ===================================================================== */
} else {

    $where = " WHERE 1 ";
    if($start_date != '' && $end_date != '') $where .= " AND m.date BETWEEN '$start_date' AND '$end_date' ";
    if($zone   != '') $where .= " AND EM.zone = '$zone' ";
    if($branch != '') $where .= " AND m.branch_code = '$branch' ";

    $sql = "SELECT m.id,
                   m.employee_code,
                   EM.emp_name,
                   CM.customer_name AS dealer_name,
                   m.dealer_code,
                   m.branch_code,
                   BM.branch_name,
                   EM.zone,
                   m.address,
                   m.tech_meet_type,
                   m.date
            FROM ocr_main_data m
            LEFT JOIN employee_master EM ON EM.emp_code = m.employee_code
            LEFT JOIN customer_master CM ON CM.customer_code = m.dealer_code
            LEFT JOIN branch_master   BM ON BM.branch_code    = m.branch_code
            $where
            ORDER BY m.date DESC, m.id DESC";

    $res   = mysqli_query($link, $sql);
    $total = $res ? mysqli_num_rows($res) : 0;

    if($total > 0){
        $count = 1;
        echo $ocr_table_style;
        ?>
        <table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB"><td colspan="12"><?php echo $header_string; ?></td></tr>
          <tr class="TDHEAD" align="center">
            <td>SI</td>
            <td>Employee Code</td>
            <td>Employee Name</td>
            <td>Dealer/Sub-Dealer Name</td>
            <td>Dealer/Sub-Dealer Code</td>
            <td>Branch Code</td>
            <td>Branch Name</td>
            <td>Zone</td>
            <td>Area/Address</td>
            <td>Tech Meet Type</td>
            <td>Date</td>
            <td>Participants List</td>
          </tr>
        <?php
        while($row = mysqli_fetch_assoc($res)){
            $date_disp = ($row['date'] != '' && $row['date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['date'])) : '';
            $csv_link  = "admin_ocr_report_participants_export.php?main_id=".intval($row['id']);
            echo "<tr>
                    <td>".$count."</td>
                    <td>".htmlspecialchars($row['employee_code'])."</td>
                    <td>".htmlspecialchars($row['emp_name'])."</td>
                    <td>".htmlspecialchars($row['dealer_name'])."</td>
                    <td>".htmlspecialchars($row['dealer_code'])."</td>
                    <td>".htmlspecialchars($row['branch_code'])."</td>
                    <td>".htmlspecialchars($row['branch_name'])."</td>
                    <td>".htmlspecialchars($row['zone'])."</td>
                    <td>".htmlspecialchars($row['address'])."</td>
                    <td>".htmlspecialchars($row['tech_meet_type'])."</td>
                    <td>".$date_disp."</td>
                    <td align='center'><a href='".$csv_link."' target='_blank' class='ocr-csv-btn'>CSV Download</a></td>
                  </tr>";
            $count++;
        }
        ?>
        </table>
        <?php
    } else {
        echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
    }
}
?>
