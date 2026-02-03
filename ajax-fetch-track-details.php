<?php
// (A) INIT
//require "2a-core.php";
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
//require("include/config-setup.php");
define("DB","acedns_MAGIK");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database");
$live_tracking_latt_long = "live_tracking_latt_long";

if (isset($_POST["req"])) { switch ($_POST["req"]) {
  // (B) INVALID REQUEST
  default: echo "Invalid request."; break;

  
  // (E) GET ALL RIDER LOCATIONS
  case "getAll":
    $sqllattlong="SELECT latt,`long` FROM $live_tracking_latt_long ";
	$rslattlong=mysqli_query($link,$sqllattlong);
			$rowroutename=mysqli_fetch_assoc($rsroutename);
    echo json_encode([
      "status" => is_array($location) ? 1 : 0,
      "message" => $location
    ]);
    break;
}}
