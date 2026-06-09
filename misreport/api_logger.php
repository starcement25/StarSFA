<?php
date_default_timezone_set('Asia/Kolkata');

/**
 * START API LOG
 * Inserts initial record
 */
function api_log_start($conn, $api_name, $user_id = null) {

    $start_time = microtime(true);
    $start_datetime = date('Y-m-d H:i:s');
//echo"<pre>";print_r($user_id);die;
//echo $user_id;
    // Escape values
    $api_name = mysqli_real_escape_string($conn, $api_name);
    $user_id  = mysqli_real_escape_string($conn, $user_id);

     $sql = "
        INSERT INTO api_logs (api_name, user_id, start_time, status)
        VALUES ('$api_name', '$user_id', '$start_datetime', 'STARTED')
    ";

    mysqli_query($conn, $sql);

    $log_id = mysqli_insert_id($conn);

    return [
        'start_time' => $start_time,
        'log_id' => $log_id
    ];
}


/**
 * END API LOG
 * Updates record with end time + response time
 */
function api_log_end($conn, $log_data, $status = 'COMPLETED') {

    if (empty($log_data['log_id'])) {
        return; // safety check
    }

    $end_time = microtime(true);
    $response_time = round(($end_time - $log_data['start_time']) * 1000);

    $end_datetime = date('Y-m-d H:i:s');

    // Escape values
    $status = mysqli_real_escape_string($conn, $status);
    $log_id = (int)$log_data['log_id'];

    $sql = "
        UPDATE api_logs 
        SET end_time = '$end_datetime',
            response_time_ms = '$response_time',
            status = '$status'
        WHERE id = $log_id
    ";

    mysqli_query($conn, $sql);
}


/**
 * SHUTDOWN HANDLER
 * Automatically logs FAILED if API crashes
 */
function api_log_shutdown($conn, $log_data) {

    $error = error_get_last();

    // If any fatal error occurred
    if ($error !== NULL) {
        api_log_end($conn, $log_data, 'FAILED');
    }
}


/**
 * OPTIONAL: MANUAL FAIL LOG (for validation errors etc.)
 */
function api_log_fail($conn, $log_data) {
    api_log_end($conn, $log_data, 'FAILED');
}


/**
 * OPTIONAL: MANUAL SUCCESS LOG
 */
function api_log_success($conn, $log_data) {
    api_log_end($conn, $log_data, 'COMPLETED');
}

?>