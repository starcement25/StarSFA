<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');
require_once("../sfa_connection.php");
require_once("include/PHPMailer/src/PHPMailer.php");
require_once("include/PHPMailer/src/SMTP.php");
require_once("include/PHPMailer/src/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$localDB = new sfa_connection();
$conn = $localDB->conn;

function send_the_mail($to_email, $subject, $bodyml, $attachment_path = null)
{
    $sts = "FALSE";
    date_default_timezone_set("Asia/Kolkata");

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug  = 2;
        $mail->Debugoutput = 'html';
        $mail->SMTPAuth   = true;
        // $mail->Host       = "smtp.gmail.com";
        $mail->Host = "cloudmail2.up99plus.com";
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //$mail->Port       = 587;
        $mail->Port = 25;
        // $mail->Username   = 'test.sbinfowaves@gmail.com';
        // $mail->Password   = 'dzltchhdafyfnqhh'; 
        $mail->Username = "starcement@cloudmail.up99plus.com";
        $mail->Password = "K2TTvLxATyULV2um";
        $mail->SMTPSecure = false; // Explicit TLS encryption is not used
        $mail->SMTPAutoTLS = false;
        //$mail->setFrom('sfa@starcement.co.in', 'SFA');
        $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');
        $to_email_arr = explode(",", $to_email);
        foreach ($to_email_arr as $email) {
            if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress(trim($email));
            }
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyml;
        $mail->AltBody = strip_tags($bodyml);

        if ($attachment_path && file_exists($attachment_path)) {
            $mail->addAttachment($attachment_path);
        }

        $mail->send();
        $sts = "TRUE";
    } catch (Exception $e) {
        echo " PHPMailer Error: " . $mail->ErrorInfo . "<br>";
    }

    return $sts;
}
$exclude_sm = ['id', 'updated_at', 'branch_code', 'route_code'];
$exclude_svm = ['site_id', 'updated_at', 'is_sent', 'approved_by', 'visited_by', 'id'];

$sm_columns = getFilteredColumns($conn, 'site_master', 'sm', $exclude_sm);
$svm_columns = getFilteredColumns($conn, 'site_visit_master', 'svm', $exclude_svm);



$extra_columns = [
    'svm.id AS visit_id',
    'bm.branch_name',
    'rm.route_name',
    'ev.emp_name AS visited_by_name',
    'ea.emp_name AS approved_by_name'
];

$all_columns = array_merge($sm_columns, $svm_columns, $extra_columns);
$sql = "
    SELECT " . implode(",\n    ", $all_columns) . "
    FROM site_visit_master svm
    JOIN site_master sm ON sm.id = svm.site_id
    LEFT JOIN branch_master bm ON bm.branch_code = sm.branch_code
    LEFT JOIN route_master rm ON rm.route_code = sm.route_code
    LEFT JOIN employee_master ev ON ev.emp_code = svm.visited_by
    LEFT JOIN employee_master ea ON ea.emp_code = svm.approved_by
    WHERE svm.is_sent = 0
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $filename = 'site_visits_' . date('Ymd_His') . '.csv';
    $filepath = __DIR__ . '/' . $filename;
    $fp = fopen($filepath, 'w');

    $firstRow = $result->fetch_assoc();
    $headers = array_map(function ($key) {
        return strtoupper(str_replace('_', ' ', $key));
    }, array_keys($firstRow));
    fputcsv($fp, $headers);
    fputcsv($fp, array_values($firstRow));

    $idsToUpdate = [$firstRow['visit_id']];

    while ($row = $result->fetch_assoc()) {

        fputcsv($fp, array_values($row));
        $idsToUpdate[] = $row['visit_id'];
    }

    fclose($fp);

 $currentDateTime = date('Y-m-d H:i:s'); 
 $currentTime = date('Y-m-d h:i A'); 
    // $to = "souvik@yopmail.com,saurabhkumar@starcement.co.in,arvindpatil@starcement.co.in,bijaysaha@starcement.co.in,gourabchatterjee@starcement.co.in,udaibhanu@starcement.co.in,ashishbaroi@starcement.co.in";
    $to = "souvik.pal@sbinfowaves.com";
    $subject = "Khoj - Site Visit Report";
    //$body = "<p>Please find attached the new khoj site visit entries as a CSV file.</p>";
     $body = "
    <p>Dear All,</p>
    <p>Please find the <strong>Khoj - Site Visit Report</strong> attached along with this mail.</p>
    <p>Thanks & Regards<br>SFA Team</p>
";

    $status = send_the_mail($to, $subject, $body, $filepath);

    if ($status === "TRUE") {
        echo " Email sent successfully.<br>";

        $ids = implode(',', array_map('intval', $idsToUpdate));
        $update = $conn->query("UPDATE site_visit_master SET is_sent = 1 WHERE id IN ($ids)");
        echo $update ? "Records marked as sent." : " Failed to update records.";


        $sentEmails = implode(',', array_filter(array_map('trim', explode(',', $to))));
        // $stmt = $conn->prepare("INSERT INTO site_visit_cron_log (emails) VALUES (?)");
        // $stmt->bind_param("s", $sentEmails);
        // $stmt->execute();
       // Format: 2025-07-18 14:35:00

        $stmt = $conn->prepare("INSERT INTO site_visit_cron_log (emails, created_at) VALUES (?, ?)");
        $stmt->bind_param("ss", $sentEmails, $currentDateTime);
        $stmt->execute();
    } else {
        echo " Failed to send email.";
    }

    unlink($filepath);
} else {
    echo " No unsent site visit records found.";
}

$conn->close();


function getFilteredColumns($conn, $table, $alias, $exclude = [])
{
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM `$table`");

    while ($row = $result->fetch_assoc()) {
        $column = $row['Field'];
        if (!in_array($column, $exclude)) {
            $columns[] = "$alias.`$column`";
        }
    }

    return $columns;
}
