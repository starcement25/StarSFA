<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_SUPERSHAKTIDMS");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlcustomernamechk="SELECT * FROM customer_need_deletion ORDER BY sl_no ASC";
	$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
	$customer_exists_array=array();
	$customer_not_exists_array=array();
	while($rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk))
	{
		$dns_customer_code_del=$rowcustomernamechk['dns_customer_code'];
		$phone_no_del=$rowcustomernamechk['phone_no'];
		$customer_name_del=$rowcustomernamechk['customer_name'];
		$dns_route_code_del=$rowcustomernamechk['route_code'];
		$route_name_del=$rowcustomernamechk['route_name'];
		$emp_code_name_del=$rowcustomernamechk['emp_code_name'];
		$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_name_del)."'";
		$rsroutechk=mysqli_query($link,$sqlroutechk);
		$rowroutechk=mysqli_fetch_assoc($rsroutechk);
		$route_code_del=$rowroutechk['route_code'];
		
		echo $sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name_del)."' AND 
						route_code='".addslashes($route_code_del)."'";
		$rscustomercode=mysqli_query($link,$sqlcustomercode);
		$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
		echo $customer_code_del=$rowcustomercode['customer_code'];
		
	//check T_APPERPDO	
	echo $sqlcustomerchk="SELECT customer_code FROM 	T_APPERPDO WHERE customer_code='".$customer_code_del."'";
	$rscustomerchk=mysqli_query($link,$sqlcustomerchk);
	$countcustomerchk=mysqli_num_rows($rscustomerchk);
	if($countcustomerchk > 0 && !in_array($customer_code_del,$customer_exists_array))
	{
		echo 'a'.$sqlinsertexists="INSERT INTO customer_having_trans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertexists);	
		array_push($customer_exists_array,$customer_code_del);			
	}
	else if($countcustomerchk==0 && !in_array($customer_code_del,$customer_not_exists_array))
	{
		echo 'b'.$sqlinsertnotexists="INSERT INTO customer_having_notrans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertnotexists);	
		array_push($customer_not_exists_array,$customer_code_del);
	}
	
	//check T_order
	echo $sqlcustomernamechkone="SELECT customer_code FROM 	T_ORDER WHERE customer_code='".$customer_code_del."'";
	$rscustomernamechkone=mysqli_query($link,$sqlcustomernamechkone);
	$countcustomernamechkone=mysqli_num_rows($rscustomernamechkone);
	if($countcustomernamechkone > 0 && !in_array($customer_code_del,$customer_exists_array))
	{
		echo 'c'.$sqlinsertexistsone="INSERT INTO customer_having_trans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertexistsone);
		array_push($customer_exists_array,$customer_code_del);				
	}
	else if($countcustomernamechkone==0 && !in_array($customer_code_del,$customer_not_exists_array))
	{
		echo 'd'.$sqlinsertnotexistsone="INSERT INTO customer_having_notrans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertnotexistsone);	
		array_push($customer_not_exists_array,$customer_code_del);
	}
	//check T_DOCHALLAN
	echo $sqlcustomernamechktwo="SELECT customer_code FROM 	T_DOCHALLAN WHERE customer_code='".$customer_code_del."'";
	$rscustomernamechktwo=mysqli_query($link,$sqlcustomernamechktwo);
	$countcustomernamechktwo=mysqli_num_rows($rscustomernamechktwo);
	if($countcustomernamechktwo > 0 && !in_array($customer_code_del,$customer_exists_array))
	{
		echo 'e'.$sqlinsertexiststwo="INSERT INTO customer_having_trans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertexiststwo);
		array_push($customer_exists_array,$customer_code_del);				
	}
	else if($countcustomernamechktwo==0 && !in_array($customer_code_del,$customer_not_exists_array))
	{
		echo 'f'.$sqlinsertnotexiststwo="INSERT INTO customer_having_notrans SET dns_customer_code='".$dns_customer_code_del."',
						customer_name='".$customer_name_del."',
						phone_no='".$phone_no_del."',
						route_code='".$dns_route_code_del."',
						route_name='".$route_name_del."',
						emp_code_name='".$emp_code_name_del."'";
		mysqli_query($link,$sqlinsertnotexiststwo);	
		array_push($customer_not_exists_array,$customer_code_del);
	}
	//exit();
	}
	echo '1';
	mysqli_close($link);
?>