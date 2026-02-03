<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_PALSONS");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlcustomernamechk="SELECT * FROM customer_need_deletion ORDER BY al_no ASC";
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
		
		$sqlempchk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_del)."'";
		$rsempchk=mysqli_query($link,$sqlempchk);
		$rowempchk=mysqli_fetch_assoc($rsempchk);
		$emp_code_del=$rowempchk['emp_code'];
		
		$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name_del)."' AND 
						route_code='".addslashes($route_code_del)."'";
		$rscustomercode=mysqli_query($link,$sqlcustomercode);
		$rowcustomercode=mysqli_fetch_assoc($rscustomercode);
		$customer_code_del=$rowcustomercode['customer_code'];
		
		
		echo $sqldelcustomercode="DELETE FROM customer_route_emp_relation WHERE customer_code='".addslashes($customer_code_del)."' AND 
						route_code='".addslashes($route_code_del)."' AND emp_code='".addslashes($emp_code_del)."'";
		$rsdelcustomercode=mysqli_query($link,$sqldelcustomercode);
		
		$sqlcountchk="SELECT customer_code FROM customer_route_emp_relation 
						WHERE customer_code='".addslashes($customer_code_del)."' AND 
						route_code='".addslashes($route_code_del)."' AND acedns='Y'";
		$rscountchk=mysqli_query($link,$sqlcountchk);	
		$countchk=mysqli_num_rows($rscountchk);
		
		if($countchk==0)
		{
			$sqldelcustomerrelation="DELETE FROM customer_route_emp_relation WHERE customer_code='".addslashes($customer_code_del)."' AND 
						route_code='".addslashes($route_code_del)."'";
			$rsdelcustomerrelation=mysqli_query($link,$sqldelcustomerrelation);
			
			echo $sqldelcustomermaster="DELETE FROM customer_master WHERE customer_code='".addslashes($customer_code_del)."' AND 
						route_code='".addslashes($route_code_del)."'";
			$rsdelcustomermaster=mysqli_query($link,$sqldelcustomermaster);
		}
		
	//exit();
	}
	echo '1';
	mysqli_close($link);
?>