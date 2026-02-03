<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_EMAMI");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	
	//$sqlupdateproductCP="UPDATE product_master SET TD='5',download_time=current_timestamp() WHERE pack_size='CP' AND vertical_value!='Specialty Fats'";
	$sqlupdateproductCP="UPDATE product_master SET TD='5',download_time=current_timestamp() WHERE pack_size='CP' 
						AND product_group_code IN('BR2','BR4','BR3','BR9')";
	if(mysqli_query($link,$sqlupdateproductCP))
	{
		$sqlupdateproductBP="UPDATE product_master SET TD='10',download_time=current_timestamp() WHERE pack_size='BP' 
							AND product_group_code IN('BR2','BR4','BR3','BR9','BR7')";
		if(mysqli_query($link,$sqlupdateproductBP))
		{
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILURE';
		}
	}
	else
	{
		echo 'FAILURE';
	}
	$sqlupdateproductvertical="UPDATE product_master SET TD='0',download_time=current_timestamp() WHERE vertical_value='Specialty Fats'";
	if(mysqli_query($link,$sqlupdateproductvertical))
	{
		echo '<br />SUCCESS';
	}
	else
	{
		echo '<br />FAILURE';
	}
	$sqlupdateproductgroup="UPDATE product_master SET TD='0',download_time=current_timestamp() WHERE product_group_code IN('BR18','BR17')";
	if(mysqli_query($link,$sqlupdateproductgroup))
	{
		echo '<br />SUCCESS';
	}
	else
	{
		echo '<br />FAILURE';
	}
	
	mysqli_close($link);
?>