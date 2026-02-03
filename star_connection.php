<?php
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "acedns_dnsprod";
$password = "dnsprod1234#";
$db_name = "acedns_START";

$conn = mysqli_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysqli_error());
}

$db_selected = mysqli_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysqli_error());
}

?>