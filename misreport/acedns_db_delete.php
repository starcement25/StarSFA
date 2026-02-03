<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");


$emp_codes=$_GET['emp_code'];
//$deviceId=$_POST['deviceId'];

$emp_codea = explode(",",$emp_codes);

//print_r($emp_codea);
if($_GET['emp_code']!=""){
    
foreach ($emp_codea as $x) {
  //echo "$x <br>";
$emp_code = ltrim($x);


$sqlselect="SELECT is_delete FROM dbbackupcheck  WHERE emp_code='".$emp_code."'";
$rsselect=mysqli_query($link,$sqlselect);
$count=mysqli_num_rows($rsselect);

if($count>0){
	$sqlupdate="UPDATE dbbackupcheck SET is_delete='1' WHERE emp_code='".$emp_code."'";
//echo "u-".	$sqlupdate;
$rsupdate=mysqli_query($link,$sqlupdate);
if($rsupdate){
    echo "UPDATE";
}
}else{
	$sqlupdate="INSERT INTO `dbbackupcheck`(`emp_code`, `device_id`, `is_checked`, `is_delete`) VALUES ('".$emp_code."','test','0','1')";
$rsupdate=mysqli_query($link,$sqlupdate);
//echo "i-".	$sqlupdate;
if($rsupdate){
    echo "UPDATE";
}
}
}

}


?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>DB delete</title>
</head>
<body>

<center>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
		
<label>FOR DB DELETE  ----    EMP CODE</label>

<input type="text" name="emp_code">

<input type="submit" class="btn btn-primary" value="Submit">

	</form>
</center>
</body>
</html>