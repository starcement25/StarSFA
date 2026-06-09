<?php
header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;
 date_default_timezone_set('Asia/Kolkata');
if(!isset($_FILES['csv_file'])){
    echo json_encode(["status"=>false,"message"=>"No file uploaded"]);
    exit;
}

$file = $_FILES['csv_file']['tmp_name'];

$handle = fopen($file, "r");

if(!$handle){
    echo json_encode(["status"=>false,"message"=>"File open failed"]);
    exit;
}

/* ================= SKIP HEADER ================= */
fgetcsv($handle);

$inserted = 0;
$updated  = 0;

while(($data = fgetcsv($handle)) !== FALSE){

    $product_name  = mysqli_real_escape_string($conn, $data[0]); // competitor_name
    $customer_code = mysqli_real_escape_string($conn, $data[1]);
    $qty           = mysqli_real_escape_string($conn, $data[2]);
    $active        = strtolower(trim($data[3]));

    $acedns = ($active == 'yes') ? 'yes' : 'no';
    $created_at = date('Y-m-d H:i:s');

    /* ================= LOG TABLE INSERT ================= */
    $log_sql = "INSERT INTO competitor_quantity_log 
                (customer_code, competitor_name, qty, acedns, created_at)
                VALUES 
                ('$customer_code','$product_name','$qty','$acedns','$created_at')";
        //echo"<pre>";print_r($log_sql);die;

    mysqli_query($conn, $log_sql);
    $inserted++;

    /* ================= CHECK EXIST ================= */
    $check_sql = "SELECT competitor_quantity_id 
                  FROM competitor_quantity
                  WHERE customer_code='$customer_code'
                  AND competitor_name='$product_name'";

    $res = mysqli_query($conn, $check_sql);

    if(mysqli_num_rows($res) > 0){

        /* ================= UPDATE ================= */
        $update_sql = "UPDATE competitor_quantity SET
                        qty='$qty',
                        acedns='$acedns',
                        update_type='excel',
                        updated_at='$created_at'
                       WHERE customer_code='$customer_code'
                       AND competitor_name='$product_name'";

        mysqli_query($conn, $update_sql);
        $updated++;

    } else {

        /* ================= INSERT ================= */
        $insert_sql = "INSERT INTO competitor_quantity
                        (customer_code, competitor_name, qty, acedns,update_type, created_at, updated_at)
                       VALUES
                        ('$customer_code','$product_name','$qty','$acedns','excel','$created_at','$created_at')";

        mysqli_query($conn, $insert_sql);
    }
}

fclose($handle);

echo json_encode([
    "status" => true,
    "message" => "Upload Done | Log Inserted: $inserted | Updated: $updated"
]);
exit;
?>