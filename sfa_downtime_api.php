<?php
// Set response type to JSON
header('Content-Type: application/json');

// Allow only GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Only GET requests are allowed']);
    exit;
}

// Get parameters from the query string
$status = 'start'; //['start', 'stop']
$message = "SFA App wise temporarily unavailable due to server downtime.

Please click this link to 'Onboard dealers' through Star Pravesh.";
$is_link_available='Y';
$link='https://manchtech.com/login/';
// Validate input
if (!$status || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

if (!in_array($status, ['start', 'stop'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status value (use start or stop)']);
    exit;
}
if($status=='start'){
   $message=''; 
   $is_link_available='N';
   $link=''; 
}
// Response
$response = [
    'status' => 'success',
    'app_status' => $status,
    'body_message' => $message,
    'is_link_available'=>$is_link_available,
    'body_link' => $link,
    'is_database_deleted' => (int) 1
    
];

echo json_encode($response);
?>
