<?php

define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_HALDIRAM");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$setExcelName="distrutor_customer_mismatch";

$header = "Distributor Name"."\t"."Route Name"."\t"."Retailer Name"."\t"."Tag Distributor Name";

$sql="SELECT DISTINCT distributor_code,route_code FROM `distributor_route_relation` ORDER BY distributor_code DESC";
$res=mysqli_query($link,$sql);
while($row=mysqli_fetch_assoc($res)){
	
	//echo $row['distributor_code'].'==========';  echo $row['route_code'].'<br>';
	$sql_list="SELECT DISTINCT CM.customer_code,CM.rds_tag,CM.cust_type,RM.route_name,CM.customer_name AS retaliername FROM `customer_master` CM,customer_route_emp_relation CRM,route_master RM WHERE CM.customer_code=CRM.customer_code AND CRM.route_code='".$row['route_code']."' AND CM.cust_type='R' AND RM.route_code=CRM.route_code AND CM.rds_tag!='".$row['distributor_code']."'";
	$res_list=mysqli_query($link,$sql_list);
	while($row_list=mysqli_fetch_assoc($res_list)){
		
		$distributor_name=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name FROM `customer_master` WHERE customer_code='".$row['distributor_code']."'"));
		$tagdistributorname=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name FROM `customer_master` WHERE customer_code='".$row_list['rds_tag']."'"));
		
		$disname=$distributor_name['customer_name'];
		$routname=$row_list['route_name'];
		$retailname=$row_list['retaliername'];
		$tagdisname=$tagdistributorname['customer_name'];
		
		$content .= $disname."\t".$routname."\t".$retailname."\t".$tagdisname."\n";
	}




}
			if ( !file_exists("/dump/HALDIRAM")){
			mkdir("/dump/HALDIRAM");
			chmod("/dump/HALDIRAM", 0777);
			}
			$datacontents=$header."\n".$content;
			$fp = fopen("/home/acedns/public_html/dump/HALDIRAM/distributor_customer_mismatch.xls","wb");
			fwrite($fp,$datacontents);
			fclose($fp);
			echo "HALDIRAM/distributor_customer_mismatch.xls";