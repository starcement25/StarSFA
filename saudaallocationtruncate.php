<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_acednsproduct");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$nick_names = array();

$sql_sauda_carry_forward = "SELECT * FROM sauda_form_details WHERE sauda_allocation_carry_forward = 'no'";
$res_sauda_carry_forward = mysqli_query($link,$sql_sauda_carry_forward);
while($row_sauda_carry_forward = mysqli_fetch_assoc($res_sauda_carry_forward))
{
	$nick_names[] = "acedns_".$row_sauda_carry_forward['nick_name'];
}

foreach($nick_names as $key)
{
	if (mysqli_select_db($key))
	{
		$sql_truncate_sauda = "TRUNCATE table sauda_allocation";
		$res_truncate_sauda = mysqli_query($link,$sql_truncate_sauda);
		if(mysqli_query($link,$res_truncate_sauda))
			$flag = 1;
		else
			$flag = 0;
	}
}
echo "sauda_allocation table truncated successfully";
?>