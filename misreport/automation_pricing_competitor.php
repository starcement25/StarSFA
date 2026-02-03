
<?php
ob_start();


ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);


ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

ini_set('memory_limit', '1024M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

define("SERVER", "localhost");
define("USER", "root");
define("PASSWORD", "Passw0rd123#$");
define("DB", "acedns_STAR");


ob_clean();


    $link = mysqli_connect(SERVER, USER, PASSWORD, DB);

    if (!$link) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }

  
    mysqli_set_charset($link, "utf8mb4");

    $start_date = '2025-07-01';
    $end_date = '2025-12-31';

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }

    $start_date = mysqli_real_escape_string($link, $start_date);
    $end_date = mysqli_real_escape_string($link, $end_date);

  echo $start_date;die;
?>

