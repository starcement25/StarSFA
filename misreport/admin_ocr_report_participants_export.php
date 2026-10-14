<?php
ob_start();
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

if($_SESSION['admin_login']==""){
    header("Location: index.php");
    exit;
}

$main_id = isset($_REQUEST['main_id']) ? intval($_REQUEST['main_id']) : 0;

if($main_id <= 0){
    echo "Invalid request";
    exit;
}

/* ---------- parent (OCR main) record for context ---------- */
$sql_main = "SELECT m.employee_code,
                    EM.emp_name,
                    CM.customer_name AS dealer_name,
                    COALESCE(NULLIF(CM.SAP_customer_code,''), m.dealer_code) AS dealer_code,
                    m.branch_code,
                    BM.branch_name,
                    m.tech_meet_type,
                    m.date
             FROM ocr_main_data m
             LEFT JOIN employee_master EM ON EM.emp_code = m.employee_code
             LEFT JOIN customer_master CM ON (CM.customer_code = m.dealer_code OR CM.SAP_customer_code = m.dealer_code)
             LEFT JOIN branch_master   BM ON BM.branch_code    = m.branch_code
             WHERE m.id = ".$main_id." LIMIT 1";
$res_main = mysqli_query($link, $sql_main);
$main     = $res_main ? mysqli_fetch_assoc($res_main) : null;

/* ---------- participants ---------- */
$sql_part = "SELECT participant_name, address, contact_no, ilp_registered, date
             FROM ocr_participants_data
             WHERE ocr_main_data_id = ".$main_id."
             ORDER BY id ASC";
$res_part = mysqli_query($link, $sql_part);

/* ---------- CSV headers ---------- */
$filename = "OCR_Participants_List_".$main_id.".csv";
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

$out = fopen("php://output", "w");

/* ---------- context block ---------- */
if($main){
    $meet_date = ($main['date'] != '' && $main['date'] != '0000-00-00') ? date('d-m-Y', strtotime($main['date'])) : '';
    fputcsv($out, array("Employee Code", $main['employee_code']));
    fputcsv($out, array("Employee Name", $main['emp_name']));
    fputcsv($out, array("Dealer/Sub-Dealer Name", $main['dealer_name']));
    fputcsv($out, array("Dealer/Sub-Dealer Code", $main['dealer_code']));
    fputcsv($out, array("Branch Code", $main['branch_code']));
    fputcsv($out, array("Branch Name", $main['branch_name']));
    fputcsv($out, array("Tech Meet Type", $main['tech_meet_type']));
    fputcsv($out, array("Date", $meet_date));
    fputcsv($out, array()); // blank line
}

/* ---------- participants table ---------- */
fputcsv($out, array("SI", "Participant Name", "Address", "Contact No.", "ILP Registered", "Date"));

$count = 1;
if($res_part && mysqli_num_rows($res_part) > 0){
    while($row = mysqli_fetch_assoc($res_part)){
        $p_date = ($row['date'] != '' && $row['date'] != '0000-00-00') ? date('d-m-Y', strtotime($row['date'])) : '';
        fputcsv($out, array(
            $count,
            $row['participant_name'],
            $row['address'],
            $row['contact_no'],
            $row['ilp_registered'],
            $p_date
        ));
        $count++;
    }
} else {
    fputcsv($out, array("No participants found"));
}

fclose($out);
exit;
?>
