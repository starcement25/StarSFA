<?php
//require_once("../../includes/config.php"); 
//require_once("../../includes/dbcon.php");
define("ENT_MASTER", "ent_master");
$link = mysql_connect("localhost","karinfo_lend4pea","lend4peace");
$db = mysql_select_db("karinfo_lend4peace",$link);

$sql = "SELECT ent_name,ent_group_name,ent_photo FROM ".ENT_MASTER." WHERE ent_id=".$_REQUEST[ent_id];
$rs = mysql_query($sql) or die(mysql_error());
$row=mysql_num_rows($rs);
$rec=mysql_fetch_array($rs);

$ent_photo = stripslashes($rec[ent_photo]);
$ent_name= ($rec['ent_group_name']!="") ? stripslashes($rec['ent_group_name']) : stripslashes($rec['ent_name']);
echo $ent_photo."|".$ent_name;
?>
