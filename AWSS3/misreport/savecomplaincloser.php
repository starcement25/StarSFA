<?php
ob_start();
session_start();
require("adminUtils.php");

$call_id = $_REQUEST['call_id'];
$complainval = $_REQUEST['complainval'];

$sqlupdatecomplaincloser="UPDATE CRM_transaction set complain_closer='".$complainval."' WHERE call_id='".$call_id."'";
if(mysql_query($sqlupdatecomplaincloser))
{
	echo '1'.'#'.$call_id.'#'.$complainval;
}
else
{
	echo '2';
}

?>
