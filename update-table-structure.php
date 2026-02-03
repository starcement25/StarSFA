<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="UPDATE table_structure_master SET need_update='N',is_transaction='N'";
if(mysqli_query($link,$sqlquery))
{
	echo 'SUCCESS';
}
else
{
	echo 'FAILURE';
}
?>