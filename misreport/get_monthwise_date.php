<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$month = $_REQUEST['month'];
//$emp_hierarchy=return_employee_hierarchy($emp_code);
$list=array();
$year=$_REQUEST['year'];

for($d=1; $d<=31; $d++)
{
    $time=mktime(12, 0, 0, $month, $d, $year);          
    if (date('m', $time)==$month)       
        $list[]=date('Y-m-d', $time);
}
/*echo "<pre>";
print_r($list);
echo "</pre>";*/
echo "<select name=\"op_date\" id=\"op_date\" >";
echo "<option value=\"\">Select Date</option>";
foreach($list as $dateval)
{
	$datevalformatted=date('d-m-Y',strtotime($dateval));
	echo "<option value=\"'".$dateval."'\">".$datevalformatted."</option>";
}
echo "</select>";
mysqli_close($link);
?>