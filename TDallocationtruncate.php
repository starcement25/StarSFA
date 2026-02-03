<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$sql_truncate_TD = "TRUNCATE table TD_allocation";
$res_truncate_TD = mysqli_query($link,$sql_truncate_TD);
if(mysqli_query($link,$res_truncate_TD))
	$flag = 1;
else
	$flag = 0;
echo "TD allocation truncated successfully";
?>