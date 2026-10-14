<?php
require_once("../sfa_connection.php");
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

// Shared theme colors — matches the Star Cement Griha Pravesh scheme card design
define('THEME_BG', '#4a0e1c');       // deep maroon page background
define('THEME_ACCENT', '#f04438');   // coral/red button + accents
define('THEME_TEXT_MUTED', '#7a7a7a');

function theme_shell_open($heading)
{
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>$heading</title></head>
    <body style='margin:0;background:" . THEME_BG . ";font-family:\"Segoe UI\",Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;box-sizing:border-box;'>
    <div style='width:100%;max-width:420px;'>
        <h1 style='color:#fff;text-align:center;font-size:22px;font-weight:700;margin:0 0 24px;line-height:1.35;'>$heading</h1>
        <div style='background:#fff;border-radius:10px;padding:28px 26px;box-shadow:0 10px 30px rgba(0,0,0,0.25);box-sizing:border-box;'>";
}

function theme_shell_close()
{
    echo "</div></div></body></html>";
    exit;
}

function render_page($title, $message, $color = '#2e7d32')
{
    theme_shell_open("Star Cement &ndash; Issue Update");
    echo "
        <div style='text-align:center;'>
            <div style='width:56px;height:56px;border-radius:50%;background:$color;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;'>
                <span style='color:#fff;font-size:28px;line-height:1;'>&#10003;</span>
            </div>
            <h2 style='color:$color;margin:0 0 10px;font-size:19px;'>$title</h2>
            <p style='font-size:15px;color:#444;line-height:1.5;margin:0;'>$message</p>
        </div>";
    theme_shell_close();
}

function render_reason_form($id, $level, $customer_name, $esc_dept, $error = null)
{
    $id_esc = (int) $id;
    $level_esc = htmlspecialchars($level, ENT_QUOTES, 'UTF-8');
    $customer_esc = htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8');
    $dept_esc = htmlspecialchars($esc_dept, ENT_QUOTES, 'UTF-8');

    $error_html = '';
    if ($error) {
        $error_esc = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
        $error_html = "<p style='color:#f04438;font-size:13px;margin:0 0 14px;'>$error_esc</p>";
    }

    theme_shell_open("Resolve Issue");
    echo "
        <div style='background:" . THEME_BG . ";border-radius:8px;padding:16px 18px;margin-bottom:22px;'>
            <p style='color:#fff;font-size:13px;margin:0 0 4px;opacity:0.75;letter-spacing:.03em;text-transform:uppercase;'>Customer</p>
            <p style='color:#fff;font-size:16px;margin:0 0 12px;font-weight:600;'>$customer_esc</p>
            <p style='color:#fff;font-size:13px;margin:0 0 4px;opacity:0.75;letter-spacing:.03em;text-transform:uppercase;'>Department</p>
            <p style='color:#fff;font-size:16px;margin:0;font-weight:600;'>$dept_esc</p>
        </div>
        $error_html
        <form method='POST' action=''>
            <input type='hidden' name='id' value='$id_esc'>
            <input type='hidden' name='level' value='$level_esc'>
            <label for='reason' style='display:flex;align-items:center;gap:8px;color:#333;font-size:14px;font-weight:600;margin-bottom:10px;'>
                <span style='font-size:16px;'>&#9998;</span> Reason for Resolve
            </label>
            <textarea id='reason' name='reason' rows='4' required
                style='width:100%;padding:12px;box-sizing:border-box;border:none;border-bottom:1px solid #ddd;border-radius:0;font-family:inherit;font-size:14px;color:#333;resize:vertical;outline:none;background:#fafafa;'
                placeholder='Enter the reason for resolving this issue'
                onfocus=\"this.style.borderBottomColor='" . THEME_ACCENT . "'\"
                onblur=\"this.style.borderBottomColor='#ddd'\"></textarea>
            <button type='submit'
                style='width:100%;margin-top:22px;background:" . THEME_ACCENT . ";color:#fff;padding:14px;border:none;border-radius:6px;font-size:15px;font-weight:600;letter-spacing:.03em;cursor:pointer;text-transform:uppercase;'>
                Submit &amp; Resolve
            </button>
        </form>";
    theme_shell_close();
}

$id    = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
$level = isset($_REQUEST['level']) ? trim($_REQUEST['level']) : '';

if ($id <= 0) {
    render_page("Invalid Request", "No valid issue id was provided.", '#c62828');
}

// Fetch current status
$stmt = $conn->prepare("SELECT id, status, customer_name, esc_dept FROM raise_issue WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    render_page("Not Found", "No issue found with id $id.", '#c62828');
}

if ($row['status'] === 'resolved') {
    render_page(
        "Already Resolved",
        "This issue (Customer: {$row['customer_name']}, Dept: {$row['esc_dept']}) has already been marked as resolved. No further action is needed.",
        '#757575'
    );
}
if ($row['status'] === 'forwarded' && $level !== 'boss') {
    render_page(
        "Issue Forwarded",
        "This issue has already been forwarded to the next level. You can no longer resolve it.",
        '#f57c00'
    );
}

// ======================================
// GET (or first visit): show the reason form instead of resolving immediately
// ======================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render_reason_form($id, $level, $row['customer_name'], $row['esc_dept']);
}

// ======================================
// POST: validate reason, then resolve
// ======================================
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if ($reason === '') {
    render_reason_form($id, $level, $row['customer_name'], $row['esc_dept'], "Reason for Resolve is required.");
}

$now = date('Y-m-d H:i:s');

$stmt = $conn->prepare("
    UPDATE raise_issue
    SET status = 'resolved',
        resolve_reason = ?,
        status_cnange_date = ?,
        updated_at = ?
    WHERE id = ?
");
$stmt->bind_param("sssi", $reason, $now, $now, $id);

if (!$stmt->execute()) {
    $stmt->close();
    render_page("Error", "Could not update the issue status. Please try again or contact support.", '#c62828');
}

$stmt->close();

render_page(
    "Issue Resolved",
    "The issue for {$row['customer_name']} ({$row['esc_dept']}) has been successfully marked as resolved.",
    '#2e7d32'
);