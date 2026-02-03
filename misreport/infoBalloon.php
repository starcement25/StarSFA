<?php
//require_once("../../includes/config.php"); 
//require_once("../../includes/dbcon.php");
define("ENT_MASTER", "ent_master");
$link = mysqli_connect("localhost","karinfo_lend4pea","lend4peace");
$db = mysqli_select_db("karinfo_lend4peace",$link);

$sql = "SELECT ent_name,ent_group_name,ent_photo FROM ".ENT_MASTER." WHERE ent_id=".$_REQUEST[ent_id];
$rs = mysqli_query($link,$sql) or die(mysqli_error());
$row=mysqli_num_rows($rs);
$rec=mysqli_fetch_assoc($rs);

$ent_photo = stripslashes($rec[ent_photo]);
$ent_name= ($rec['ent_group_name']!="") ? stripslashes($rec['ent_group_name']) : stripslashes($rec['ent_name']);
echo $ent_photo."|".$ent_name;
?>
