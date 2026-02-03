
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

try{
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


    $mainQuery = "
   SELECT mf.competitor_name,mf.PTR,mf.PTD,mf.PTC,mf.PV,mf.billing_ex_for,mf.wsp_ex_for,mf.rsp_ex_for,rt.route_name,DATE_FORMAT(SUBSTRING(mf.market_feedback_id, -14, 8), '%Y-%m-%d') AS feedback_date FROM `market_feedback` mf INNER JOIN `route_master` rt ON rt.route_code=mf.route_code WHERE DATE_FORMAT(SUBSTRING(mf.market_feedback_id, -14, 8), '%Y-%m-%d') BETWEEN '{$start_date}' AND '{$end_date}' ORDER BY feedback_date DESC;
    ";

   $result = mysqli_query($link, $mainQuery);

    if (!$result) {
        throw new Exception('Query failed: ' . mysqli_error($link));
    }

    $results = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }

    mysqli_close($link);


    ob_clean();
    
    
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: no-cache, must-revalidate');
    

    echo json_encode(['data' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    
    ob_end_flush();

} catch (Exception $e) {
    
    ob_clean();
    

    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 500 Internal Server Error');
    
   
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    ob_end_flush();
    exit;
}
?>

