<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	//mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlcustomernamechk="SELECT `dns_customer_code` FROM `customer_master` group by dns_customer_code HAVING COUNT(dns_customer_code) > 1";
	$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
	$customer_exists_array=array();
	$customer_not_exists_array=array();
	while($rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk))
	{
		$dns_customer_code_del=$rowcustomernamechk['dns_customer_code'];
		
		echo $sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code_del)."' ORDER BY customer_code DESC limit 0,1";
		$rscustomercode=mysqli_query($link,$sqlcustomercode);
		$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
		echo $customer_code_not_del=$rowcustomercode['customer_code'];
		
		echo '------------------------------------del'.'<br />';
		echo $sqldelcustomermaster="DELETE FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code_del)."' AND 
						customer_code!='".addslashes($customer_code_not_del)."'";
		$rsdelcustomermaster=mysqli_query($link,$sqldelcustomermaster);
		
	//exit();
	}
	echo '1';
	mysqli_close($link);
?>