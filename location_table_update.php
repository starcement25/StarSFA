<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define("SERVERREMOTE","103.233.25.204");
define("USERREMOTE","acedns_dnsprod");
define("PASSWORDREMOTE",'dnsprod1234#');
define("DBREMOTE","acedns_STAR");

$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");


$links = mysqli_connect("localhost", "root", "Passw0rd123#$", "acedns_STAR")or die("Setup Database Connection Error.");



$sql = "SELECT trans_id, date FROM location WHERE DATE(date) >= '2025-08-01'";
$result = mysqli_query($link, $sql);

if (!$result) {
    die("Error fetching remote data: " . mysqli_error($link));
}

$updateCount = 0;
// $allRows = mysqli_fetch_all($result, MYSQLI_ASSOC);
// print_r($allRows);die;
while ($row = mysqli_fetch_assoc($result)) {
    $trans_id = mysqli_real_escape_string($links, $row['trans_id']);
    $date     = mysqli_real_escape_string($links, $row['date']);

  
    $updateSql = "
        UPDATE location 
        SET date = '$date' 
        WHERE trans_id = '$trans_id'
    ";

    if (mysqli_query($links, $updateSql)) {
        if (mysqli_affected_rows($links) > 0) {
            $updateCount++;
        }
    } else {
        echo "Error updating $trans_id: " . mysqli_error($links) . "<br>";
    }
}

echo "$updateCount rows updated successfully.";

mysqli_close($link);
mysqli_close($links);




