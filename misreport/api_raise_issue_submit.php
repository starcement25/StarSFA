<?php
require_once("../sfa_connection.php");
//require_once("mailer.php"); // must expose send_the_mail() and PHPMailer autoload — adjust path
// require_once("../vendor/autoload.php");
require_once("include/PHPMailer/src/PHPMailer.php");
require_once("include/PHPMailer/src/SMTP.php");
require_once("include/PHPMailer/src/Exception.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

$created_at = date('Y-m-d H:i:s');

// Base URL used to build the Resolve/Forward links inside the email.
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Only POST method is allowed"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE || empty($input)) {
    $input = $_POST;
}

$emp_code      = isset($input['emp_code']) ? trim($input['emp_code']) : '';
$branch_code   = isset($input['branch_code']) ? trim($input['branch_code']) : '';
$customer_code = isset($input['customer_code']) ? trim($input['customer_code']) : '';
$category      = isset($input['category']) ? trim($input['category']) : '';
$state         = isset($input['state']) ? trim($input['state']) : '';
$district      = isset($input['district']) ? trim($input['district']) : '';
$esc_dept      = isset($input['esc_dept']) ? trim($input['esc_dept']) : '';
$issue         = isset($input['issue']) ? trim($input['issue']) : '';
$status        = isset($input['status']) ? trim($input['status']) : 'pending';

if ($emp_code === '' || $branch_code === '' || $customer_code === '') {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "emp_code, branch_code and customer_code are required"
    ]);
    exit;
}

if ($esc_dept === '') {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "esc_dept is required to determine escalation routing"
    ]);
    exit;
}

$allowed_status = ['pending', 'resolved', 'forwarded'];
if (!in_array($status, $allowed_status)) {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid status value"
    ]);
    exit;
}


// ======================================
// LOOKUP: employee
// ======================================

$emp_name = null;
$stmt = $conn->prepare("SELECT emp_name FROM employee_master WHERE emp_code = ? LIMIT 1");
$stmt->bind_param("s", $emp_code);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $emp_name = $row['emp_name'];
}
$stmt->close();

if ($emp_name === null) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid emp_code, employee not found"
    ]);
    exit;
}


// ======================================
// LOOKUP: branch
// ======================================

$branch_name = null;
$stmt = $conn->prepare("SELECT branch_name FROM branch_master WHERE branch_code = ? LIMIT 1");
$stmt->bind_param("s", $branch_code);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $branch_name = $row['branch_name'];
}
$stmt->close();

if ($branch_name === null) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid branch_code, branch not found"
    ]);
    exit;
}


// ======================================
// LOOKUP: customer + zone (zone used only for finding the escalation contact — not stored)
// ======================================

$customer_name = null;
$customer_zone = null;

$stmt = $conn->prepare("SELECT customer_name, zone FROM customer_master WHERE customer_code = ? LIMIT 1");
$stmt->bind_param("s", $customer_code);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $customer_name = $row['customer_name'];
    $customer_zone = trim($row['zone']);
}
$stmt->close();

if ($customer_name === null) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid customer_code, customer not found"
    ]);
    exit;
}

if ($customer_zone === null || $customer_zone === '') {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Customer has no zone assigned, cannot determine escalation routing"
    ]);
    exit;
}


// ======================================
// LOOKUP: escalation hierarchy (department + zone) — used only to send the mail, not stored
// ======================================

$esc_person = null;
$esc_email  = null;

$stmt = $conn->prepare("
    SELECT esc_person, esc_email
    FROM raise_issue_hier
    WHERE department = ? AND zone = ?
    LIMIT 1
");
$stmt->bind_param("ss", $esc_dept, $customer_zone);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $esc_person = trim($row['esc_person']);
    //$esc_email  = trim($row['esc_email']);
    $esc_email='subhrajit.das@sbinfowaves.com';
}
$stmt->close();

if ($esc_person === null || $esc_email === '') {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "No escalation mapping found for department '$esc_dept' and zone '$customer_zone'"
    ]);
    exit;
}


// ======================================
// INSERT — original raise_issue columns only
// ======================================

$sql = "INSERT INTO raise_issue 
        (emp_code, emp_name, branch_code, branch_name, customer_code, customer_name, 
         category, state, district, esc_dept, issue, status, status_cnange_date, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "sssssssssssssss",
    $emp_code,
    $emp_name,
    $branch_code,
    $branch_name,
    $customer_code,
    $customer_name,
    $category,
    $state,
    $district,
    $esc_dept,
    $issue,
    $status,
    $created_at,
    $created_at,
    $created_at
);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Insert failed: " . $stmt->error
    ]);
    $stmt->close();
    exit;
}

$insert_id = $stmt->insert_id;
$stmt->close();


// ======================================
// SEND MAIL TO ESCALATED PERSON
// ======================================

// $resolve_link = $base_url . "api_raise_issue_resolve.php?id=" . $insert_id;
$resolve_link= $base_url . "api_raise_issue_resolve.php?id=" . $insert_id . "&level=esc";
$forward_link = $base_url . "api_raise_issue_forward.php?id=" . $insert_id;

$subject = "New Issue Raised: $customer_name ($customer_code) - $esc_dept";

$htmlBody = "
    <p>Dear $esc_person,</p>
    <p>A new issue has been raised and escalated to you:</p>
    <table border='1' cellpadding='6' cellspacing='0'>
        <tr><td><b>Raised By</b></td><td>$emp_name ($emp_code)</td></tr>
        <tr><td><b>Branch</b></td><td>$branch_name</td></tr>
        <tr><td><b>Customer</b></td><td>$customer_name ($customer_code)</td></tr>
        <tr><td><b>Category</b></td><td>$category</td></tr>
        <tr><td><b>State</b></td><td>$state</td></tr>
        <tr><td><b>District</b></td><td>$district</td></tr>
        <tr><td><b>Department</b></td><td>$esc_dept</td></tr>
        <tr><td><b>Issue</b></td><td>$issue</td></tr>
    </table>
    <br>
    <a href='$resolve_link' style='background:#2e7d32;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Resolve</a>
    &nbsp;&nbsp;
    <a href='$forward_link' style='background:#c62828;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Forward to Boss</a>
";

$to_emails = [
    ['emails' => [$esc_email]]
];

send_the_mail($to_emails, $subject, $htmlBody);


// ======================================
// RESPONSE
// ======================================

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "insert_id" => $insert_id,
    "data" => [
        "emp_code" => $emp_code,
        "emp_name" => $emp_name,
        "branch_code" => $branch_code,
        "branch_name" => $branch_name,
        "customer_code" => $customer_code,
        "customer_name" => $customer_name,
        "category" => $category,
        "state" => $state,
        "district" => $district,
        "esc_dept" => $esc_dept,
        "esc_person" => $esc_person,
        "esc_email" => $esc_email,
        "issue" => $issue,
        "status" => $status,
        "created_at" => $created_at
    ]
]);

exit;