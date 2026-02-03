<?php
ob_start();
session_start();
require("adminUtils.php");
$yellowcardno = $_REQUEST['yellowcardno'];
$qty=$_REQUEST['qty'];
$sql=mysql_fetch_array(mysql_query("SELECT `challan_no`,`qty` FROM `yellow_card_details` WHERE `yellow_card_no`='".$yellowcardno."'"));
$refid=$_SESSION['admin_login'].date('Ymdhms');
	$priviou_qty=$sql['qty'];
	$ip=$_SERVER['REMOTE_ADDR'];
	
	$sqlup="UPDATE `yellow_card_details` SET qty='".$_REQUEST['qty']."' WHERE `yellow_card_no`='".$yellowcardno."' limit 1";
	mysql_query($sqlup);
	$sqllog="INSERT INTO yellow_card_qtyupdate_log SET yellow_card_no='".$yellowcardno."',previous_qty='".$priviou_qty."',revise_id='".$refid."',ip='".$ip."'";
	if(mysql_query($sqllog)){
		echo "Qty updated sucessfully";
	}
	else{
	   echo "Qty not updated sucessfully";
	}
?>