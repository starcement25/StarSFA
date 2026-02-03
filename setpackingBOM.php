<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	$packing_mat_array=array();
	$prod_code_array=array();
	
	$sqlseldisroute="SELECT prod_code,packm1,packm2,packm3,packm4,packm5,packm6,packm7,packm8,packm9,packm10,packm11,packm12 FROM packing_BOM_prepare ";
	$rsseldisroute=mysqli_query($link,$sqlseldisroute);
	while($rowseldisroute=mysqli_fetch_assoc($rsseldisroute))
	{
		$prod_code=$rowseldisroute['prod_code'];
		${prod_code_array.$prod_code}=array();
		$packm1=$rowseldisroute['packm1'];
		$packm2=$rowseldisroute['packm2'];
		$packm3=$rowseldisroute['packm3'];
		$packm4=$rowseldisroute['packm4'];
		$packm5=$rowseldisroute['packm5'];
		$packm6=$rowseldisroute['packm6'];
		$packm7=$rowseldisroute['packm7'];
		$packm8=$rowseldisroute['packm8'];
		$packm9=$rowseldisroute['packm9'];
		$packm10=$rowseldisroute['packm10'];
		$packm11=$rowseldisroute['packm11'];
		$packm12=$rowseldisroute['packm12'];
		if($packm1!='' && !in_array($packm1,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm1);
		}
		if($packm2!='' && !in_array($packm2,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm2);
		}
		if($packm3!='' && !in_array($packm3,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm3);
		}
		if($packm4!='' && !in_array($packm4,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm4);
		}
		if($packm5!='' && !in_array($packm5,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm5);
		}
		if($packm6!='' && !in_array($packm6,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm6);
		}
		if($packm7!='' && !in_array($packm7,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm7);
		}
		if($packm8!='' && !in_array($packm8,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm8);
		}
		if($packm9!='' && !in_array($packm9,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm9);
		}
		if($packm10!='' && !in_array($packm10,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm10);
		}
		if($packm11!='' && !in_array($packm11,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm11);
		}
		if($packm12!='' && !in_array($packm12,${prod_code_array.$prod_code}))
		{
			array_push(${prod_code_array.$prod_code},$packm12);
		}
		if(!in_array($prod_code,$prod_code_array))
		{
			array_push($prod_code_array,$prod_code);
		}
		/*$sqlchkdistributorroute="SELECT route_code FROM distributor_route_relation WHERE distributor_code='".$rds_tag."' AND 
					route_code='".$route_code."' AND emp_code='".$emp_code."'";
		$rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);
		$cntchkdistributorroute=mysqli_num_rows($rschkdistributorroute);
		if($cntchkdistributorroute ==0)
		{				
			$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$rds_tag."',
							route_code='".$route_code."',emp_code='".$emp_code."', download_time=CURRENT_TIMESTAMP()";
			//exit();				
			mysqli_query($link,$sqlinsertdistributorroute);
		}*/
	}
	print_r($prod_code_array);
	foreach($prod_code_array as $prod_code_val)
	{
		for($i=0;$i<count(${prod_code_array.$prod_code_val});$i++)
		{
		$sqlinsertdistributorroute="INSERT INTO packing_BOM_test SET 
									prod_code='".$prod_code_val."',	
									material_name='".${prod_code_array.$prod_code_val}[$i]."',
									datetime=CURRENT_TIMESTAMP()";
	    mysqli_query($link,$sqlinsertdistributorroute);
		}
	}
	
	
	mysqli_close($link);
?>