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
	$sqlduplicatecustomer="SELECT dns_customer_code,COUNT(dns_customer_code) FROM `customer_master` WHERE cust_type='D' AND acedns='Y' 
							GROUP BY dns_customer_code HAVING COUNT(dns_customer_code) > 1";
	$rsduplicatecustomer=mysqli_query($link,$sqlduplicatecustomer);
	while($rowduplicatecustomer=mysqli_fetch_assoc($rsduplicatecustomer))
	{
		$dns_customer_code=$rowduplicatecustomer['dns_customer_code'];
		
		$sqlsorting="SELECT customer_code,emp_code FROM `customer_master` 
					WHERE dns_customer_code='".$dns_customer_code."' AND acedns='Y' 
					order by emp_code,customer_code ASC";
		/*$sqlsorting="SELECT customer_code,emp_code FROM `customer_master` 
					WHERE dns_customer_code='106630' AND acedns='Y' 
					order by emp_code,customer_code ASC";*/			
		$rssorting=mysqli_query($link,$sqlsorting);
		while($rowsorting=mysqli_fetch_assoc($rssorting))
		{
			$emp_code=$rowsorting['emp_code'];
			$customer_code=$rowsorting['customer_code'];
			if(($emp_code==$previous_emp_code) && $emp_code!='' && $previous_emp_code!='')
			{
				echo $sqlupdate="UPDATE customer_master SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				mysqli_query($link,$sqlupdate);
			}
			$previous_emp_code=$rowsorting['emp_code'];
		}
	}
	
	
	
	/*if(mysqli_query($link,$sqlupdateproductCP))
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
	}*/
	
	mysqli_close($link);
?>