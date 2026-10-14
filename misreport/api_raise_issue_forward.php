<?php
require_once("../sfa_connection.php");
require_once("include/PHPMailer/src/PHPMailer.php");
require_once("include/PHPMailer/src/SMTP.php");
require_once("include/PHPMailer/src/Exception.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

// Base URL used to build the Resolve link inside the forwarded email.
$base_url = "https://sfa.starcement.co.in/misreport/";
function send_the_mail($to_email, $subject, $bodyml, $attachment_path = null)
{
    $sts = "FALSE";
    date_default_timezone_set("Asia/Kolkata");

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;
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
        $mail->Password = "Nh26sjqgWk";
        $mail->SMTPSecure = false; // Explicit TLS encryption is not used
        $mail->SMTPAutoTLS = false;
        //$mail->setFrom('sfa@starcement.co.in', 'SFA');
        $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');
        // $to_email_arr = explode(",", $to_email);
        // foreach ($to_email_arr as $email) {
        //     if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        //         $mail->addAddress(trim($email));
        //     }
        // }

        if (is_array($to_email)) {

    foreach ($to_email as $item) {

        if (isset($item['emails']) && is_array($item['emails'])) {

            foreach ($item['emails'] as $email) {

                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress(trim($email));
                }

            }

        }

    }

} else {

    $to_email_arr = explode(",", $to_email);

    foreach ($to_email_arr as $email) {

        if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            $mail->addAddress(trim($email));
        }

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

function render_page($title, $message, $color = '#2e7d32')
{
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>$title</title></head>
    <body style='font-family:Arial,sans-serif;text-align:center;padding:60px;'>
        <div style='display:inline-block;padding:30px 50px;border-radius:8px;border:2px solid $color;'>
            <h2 style='color:$color;margin-top:0;'>$title</h2>
            <p style='font-size:16px;color:#333;'>$message</p>
        </div>
    </body></html>";
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    render_page("Invalid Request", "No valid issue id was provided.", '#c62828');
}

// ======================================
// FETCH THE ISSUE (original raise_issue columns only)
// ======================================

$stmt = $conn->prepare("
    SELECT id, status, emp_name, emp_code, branch_name, customer_name, customer_code,
           category, state, district, esc_dept, issue
    FROM raise_issue
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    render_page("Not Found", "No issue found with id $id.", '#c62828');
}

// Once resolved, no further action (including forward) is allowed.
if ($row['status'] === 'resolved') {
    render_page(
        "Already Resolved",
        "This issue (Customer: {$row['customer_name']}, Dept: {$row['esc_dept']}) has already been marked as resolved. It cannot be forwarded.",
        '#757575'
    );
}


// ======================================
// RE-DERIVE ZONE from customer_master (using the stored customer_code)
// ======================================

$customer_zone = null;

$stmt = $conn->prepare("SELECT zone FROM customer_master WHERE customer_code = ? LIMIT 1");
$stmt->bind_param("s", $row['customer_code']);
$stmt->execute();
$res = $stmt->get_result();
if ($zoneRow = $res->fetch_assoc()) {
    $customer_zone = trim($zoneRow['zone']);
}
$stmt->close();

if ($customer_zone === null || $customer_zone === '') {
    render_page(
        "Cannot Forward",
        "Could not determine the customer's zone, so the boss could not be identified. Please contact support.",
        '#c62828'
    );
}


// ======================================
// RE-DERIVE BOSS from raise_issue_hier (using esc_dept from the issue + zone just fetched)
// ======================================

$boss       = null;
$boss_email = null;

$stmt = $conn->prepare("
    SELECT boss, boss_email
    FROM raise_issue_hier
    WHERE department = ? AND zone = ?
    LIMIT 1
");
$stmt->bind_param("ss", $row['esc_dept'], $customer_zone);
$stmt->execute();
$res = $stmt->get_result();
if ($hierRow = $res->fetch_assoc()) {
    $boss       = trim($hierRow['boss']);
    // $boss_email = trim($hierRow['boss_email']);
    $boss_email = 'pratipbhunia@starcement.co.in';
}
$stmt->close();

if ($boss === null || $boss_email === '') {
    render_page(
        "Cannot Forward",
        "No boss mapping found for department '{$row['esc_dept']}' and zone '$customer_zone'. Please contact support.",
        '#c62828'
    );
}


// ======================================
// UPDATE STATUS
// ======================================

$now = date('Y-m-d H:i:s');

$stmt = $conn->prepare("
    UPDATE raise_issue
    SET status = 'forwarded',
        status_cnange_date = ?,
        updated_at = ?
    WHERE id = ?
");
$stmt->bind_param("ssi", $now, $now, $id);

if (!$stmt->execute()) {
    $stmt->close();
    render_page("Error", "Could not update the issue status. Please try again or contact support.", '#c62828');
}

$stmt->close();


// ======================================
// SEND MAIL TO BOSS
// ======================================

// $resolve_link = $base_url . "resolve.php?id=" . $id;
$resolve_link = $base_url . "api_raise_issue_resolve.php?id=" . $id . "&level=boss";

$subject = "Escalated Issue Forwarded: {$row['customer_name']} ({$row['customer_code']}) - {$row['esc_dept']}";

$htmlBody = "
    <p>Dear $boss,</p>
    <p>The following issue was not resolved by the assigned escalation contact and has been forwarded to you:</p>
    <table border='1' cellpadding='6' cellspacing='0'>
        <tr><td><b>Raised By</b></td><td>{$row['emp_name']} ({$row['emp_code']})</td></tr>
        <tr><td><b>Branch</b></td><td>{$row['branch_name']}</td></tr>
        <tr><td><b>Customer</b></td><td>{$row['customer_name']} ({$row['customer_code']})</td></tr>
        <tr><td><b>Category</b></td><td>{$row['category']}</td></tr>
        <tr><td><b>State</b></td><td>{$row['state']}</td></tr>
        <tr><td><b>District</b></td><td>{$row['district']}</td></tr>
        <tr><td><b>Department</b></td><td>{$row['esc_dept']}</td></tr>
        <tr><td><b>Issue</b></td><td>{$row['issue']}</td></tr>
    </table>
    <br>
    <a href='$resolve_link' style='background:#2e7d32;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Mark as Resolved</a>
";

$to_emails = [
    ['emails' => [$boss_email]]
];

send_the_mail($to_emails, $subject, $htmlBody);


render_page(
    "Forwarded to Boss",
    "The issue for {$row['customer_name']} ({$row['esc_dept']}) has been forwarded to $boss.",
    '#c62828'
);