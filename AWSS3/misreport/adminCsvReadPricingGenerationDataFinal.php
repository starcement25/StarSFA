<?php	
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//if($_REQUEST['mode']=="csv_upload")				csv_upload();
	disphtml("main();");
ob_end_flush();
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
function main()
{
	//print_r($_POST);
	//exit();
	if($_REQUEST['mode']=="submit_depot")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		$distinct_branch_code=$_POST['branch_code_array'];
		$depot_cost_case_prodwise=$_POST['depot_cost_case_array'];
		$vertical_value=$_POST['vertical_value_array'];
		$depot_cost=$_POST['depot_cost_ton_array'];
		$distinct_plant_name=$_POST['plant_name_array'];
		//$product_group_code=$_POST['oil_group_array'];
		//For log insertion
		$dns_branch_code=$_POST['dns_branch_code'];
		//$product_group_name=$_POST['product_group_name'];
		$depot_cost_ton=$_POST['depot_cost_array'];
		$vertical_value_csv=$_POST['vertical_value_csv_array'];
		for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{								 
			$sqlinsertdeptcost="INSERT INTO depot_cost 
								SET dns_prod_code='".$distinct_dnsprod_code[$i]."',
								branch_code='".$distinct_branch_code[$i]."',
								depot_cost='".$depot_cost_case_prodwise[$i]."',
								depot_cost_ton='".$depot_cost[$i]."',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertdeptcost);
			
			$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code[$i]."'";
			$rsproductgroupcode=mysql_query($sqlproductgroupcode);
			$countproductgroupcode=mysql_num_rows($rsproductgroupcode);
			if($countproductgroupcode==0)
			{
				$rowproductgroupcode=mysql_fetch_array($rsproductgroupcode);
				$product_group_code=$rowproductgroupcode['product_group_code'];
				$is_formulation=$rowproductgroupcode['formulation'];
			}
			else
			{
				$product_group_code='';
				$is_formulation='';
			}
			
			if($is_formulation=='yes')
			{
				$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
			}
			else
			{
				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
			}
			if($cntchkpricegeneration > 0)
			{
				generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);
			}
		}
		for($k=0;$k<count($dns_branch_code);$k++)
		{
			$sqlinsertdeptcostlog="INSERT INTO depot_cost_log 
								SET branch_code='".$dns_branch_code[$k]."',
								depot_cost='".$depot_cost_ton[$k]."',
								vertical_value='".$vertical_value_csv[$k]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertdeptcostlog);
		}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=="submit_margin")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		//$distinct_branch_code=$_POST['branch_code_array'];
		$state_code=$_POST['state_code_array'];
		$margin_cost_case_prodwise=$_POST['margin_cost_case_array'];
		$vertical_value=$_POST['vertical_value_array'];
		$margin_cost=$_POST['margin_cost_array'];
		//$plant_name_array=$_POST['plant_name_array'];
		$product_group_code=$_POST['oil_group_array'];
		
		//For log insertion
		$pack_size=$_POST['oil_type_array'];
		//$dns_branch_code=$_POST['dns_branch_code'];
		$dns_state_code=$_POST['dns_state_code'];
		$product_group_name=$_POST['product_group_name'];
		$margin_cost_ton=$_POST['margin_cost_ton_array'];
		//$plant_name_csv=$_POST['plant_name_csv_array'];
		$vertical_value_csv=$_POST['vertical_value_csv_array'];
		//print_r($distinct_dnsprod_code);
		//print_r($plant_name_array);
							
		for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{								 
			$sqlinsertmargincost="INSERT INTO margin_cost 
								SET dns_prod_code='".$distinct_dnsprod_code[$i]."',
								state_code='".$state_code[$i]."',
								margin_cost='".$margin_cost_case_prodwise[$i]."',
								margin_cost_ton='".$margin_cost[$i]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincost);
			
			/*$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".$product_group_code[$i]."'";
			$rschkformulation=mysql_query($sqlchkformulation);
			$rowchkformulation=mysql_fetch_array($rschkformulation);
			$is_formulation=$rowchkformulation['formulation'];
			if($is_formulation=='yes')
			{
				$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".$product_group_code[$i]."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
			}
			else
			{
				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".$product_group_code[$i]."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
			}
			if($cntchkpricegeneration > 0)
			{
				generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);
			}*/
	  }
	  for($k=0;$k<count($dns_state_code);$k++)
		{
	  		$sqlinsertmargincostlog="INSERT INTO margin_cost_log
								SET state_code='".$dns_state_code[$k]."',
								dns_prod_code='".$distinct_dnsprod_code[$i]."',
								margin_cost='".$margin_cost_ton[$k]."',
								pack_size='".$pack_size[$k]."',
								oil_group='".$product_group_name[$k]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$k]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
				mysql_query($sqlinsertmargincostlog);
		}
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=="submit_margin_rasoi")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		//$distinct_branch_code=$_POST['branch_code_array'];
		$state_code=$_POST['state_code_array'];
		$margin_cost_case_prodwise=$_POST['margin_cost_case_array'];
		$margin_cost_ton_prodwise=$_POST['margin_cost_ton_array'];
		$vertical_value=$_POST['vertical_value_array'];
		//$margin_cost=$_POST['margin_cost_array'];
		//$distinct_plant_name=$_POST['plant_name_array'];
		
		//For log insertion
		//$dns_branch_code=$_POST['dns_branch_code'];
		/*$dns_state_code=$_POST['dns_state_code'];
		$margin_cost_ton=$_POST['margin_cost_ton_array'];
		$plant_name_csv=$_POST['plant_name_csv_array'];
		$vertical_value_csv=$_POST['vertical_value_csv_array'];*/
							
		for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{								 
			$sqlinsertmargincost="INSERT INTO margin_cost 
								SET dns_prod_code='".$distinct_dnsprod_code[$i]."',
								state_code='".$state_code[$i]."',
								margin_cost='".$margin_cost_case_prodwise[$i]."',
								margin_cost_ton='".$margin_cost_ton_prodwise[$i]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincost);
			
			$sqlinsertmargincostlog="INSERT INTO margin_cost_log
								SET state_code='".$state_code[$i]."',
								dns_prod_code='".$distinct_dnsprod_code[$i]."',
								margin_cost='".$margin_cost_ton[$i]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincostlog);
			
			/*$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
							WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$distinct_dnsprod_code[$i]."'";
			$rschkformulation=mysql_query($sqlchkformulation);
			$rowchkformulation=mysql_fetch_array($rschkformulation);
			$is_formulation=$rowchkformulation['formulation'];
			$product_group_code=$rowchkformulation['product_group_code'];

			if($is_formulation=='yes')
			{
				$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$plant_name_csv[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
			}
			else
			{
				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name_csv[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
			}
			if($cntchkpricegeneration > 0)
			{
				generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);
			}*/
	  }
	  for($k=0;$k<count($dns_state_code);$k++)
		{
	  		$sqlinsertmargincostlog="INSERT INTO margin_cost_log
								SET state_code='".$dns_state_code[$k]."',
								dns_prod_code='".$distinct_dnsprod_code[$k]."',
								margin_cost='".$margin_cost_ton[$k]."',
								plant_name='".$plant_name_csv[$k]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$k]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincostlog);
		}
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=="submit_margin_sf")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		//$distinct_branch_code=$_POST['branch_code_array'];
		$state_code=$_POST['state_code_array'];
		$margin_cost_case_prodwise=$_POST['margin_cost_case_array'];
		$margin_cost_ton_prodwise=$_POST['margin_cost_ton_array'];
		$vertical_value=$_POST['vertical_value_array'];
		//$margin_cost=$_POST['margin_cost_array'];
		//$distinct_plant_name=$_POST['plant_name_array'];
		
		//For log insertion
		//$dns_branch_code=$_POST['dns_branch_code'];
		/*$dns_state_code=$_POST['dns_state_code'];
		$margin_cost_ton=$_POST['margin_cost_ton_array'];
		$plant_name_csv=$_POST['plant_name_csv_array'];
		$vertical_value_csv=$_POST['vertical_value_csv_array'];*/
							
		for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{								 
			$sqlinsertmargincost="INSERT INTO margin_cost 
								SET dns_prod_code='".$distinct_dnsprod_code[$i]."',
								state_code='".$state_code[$i]."',
								margin_cost='".$margin_cost_case_prodwise[$i]."',
								margin_cost_ton='".$margin_cost_ton_prodwise[$i]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincost);
			
			$sqlinsertmargincostlog="INSERT INTO margin_cost_log
								SET state_code='".$state_code[$i]."',
								dns_prod_code='".$distinct_dnsprod_code[$i]."',
								margin_cost='".$margin_cost_ton_prodwise[$i]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$i]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincostlog);
			
			/*$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
							WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$distinct_dnsprod_code[$i]."'";
			$rschkformulation=mysql_query($sqlchkformulation);
			$rowchkformulation=mysql_fetch_array($rschkformulation);
			$is_formulation=$rowchkformulation['formulation'];
			$product_group_code=$rowchkformulation['product_group_code'];

			if($is_formulation=='yes')
			{
				$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$plant_name_csv[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
			}
			else
			{
				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$plant_name_csv[$i]."' AND 	
										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
			}
			if($cntchkpricegeneration > 0)
			{
				generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);
			}*/
	  }
	  /*for($k=0;$k<count($dns_state_code);$k++)
		{
	  		$sqlinsertmargincostlog="INSERT INTO margin_cost_log
								SET state_code='".$dns_state_code[$k]."',
								dns_prod_code='".$distinct_dnsprod_code[$k]."',
								margin_cost='".$margin_cost_ton[$k]."',
								plant_name='".$plant_name_csv[$k]."',
								auth_one_limit='0',
								vertical_value='".$vertical_value[$k]."',
								ip_address='".$_SERVER['REMOTE_ADDR']."',
								operation_type='UPLOAD',
								datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertmargincostlog);
		}*/
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	//echo $_REQUEST['mode'];
	//exit();
	if($_REQUEST['mode']=="submit_honeycomb")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code_array=$_POST['dns_prod_code_array'];
		//$distinct_branch_code=$_POST['branch_code_array'];
		//$dns_branch_code=$_POST['dns_branch_code'];
		$dns_state_code=$_POST['dns_state_code'];
		$vertical_value=$_POST['vertical_value_csv_array'];
		$honeycomb_cost=$_POST['honeycomb_cost_array'];
		$transport_mode=$_POST['transport_mode_array'];
		$distinct_plant_name=$_POST['plant_name_csv_array'];
		//$pack_type=$_POST['pack_type_array'];
		//$product_group_code=$_POST['product_group_code_array'];
		//$is_formulation=$_POST['is_formulation_array'];

		for($i=0;$i<count($distinct_dnsprod_code_array);$i++)
		{
			/*$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master 
									  WHERE  prod_desc NOT LIKE '%LUP%' 
									  AND acedns='Y' AND black_list='N' AND dns_prod_code='".$distinct_dnsprod_code_array[$i]."' AND 
									branch_code='".$distinct_branch_code[$i]."'";
			$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two,product_group_code FROM product_master 
									  WHERE  prod_desc NOT LIKE '%LUP%' 
									  AND acedns='Y' AND black_list='N' AND pack_type='".$pack_type[$i]."' AND 
									branch_code='".$distinct_branch_code[$i]."'";*/	
			$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two,product_group_code FROM product_master 
									  WHERE  prod_desc NOT LIKE '%LUP%' 
									  AND acedns='Y' AND black_list='N' AND dns_prod_code='".$distinct_dnsprod_code_array[$i]."'";											
			$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
			$countselectdistinctdnsprod=mysql_num_rows($rsselectdistinctdnsprod);
			//if($countselectdistinctdnsprod > 0){
			$rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod);
			$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
			${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];
			${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];
			${product_group_code.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['product_group_code'];
			
			/*$sqlformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
			$rsformulation=mysql_query($sqlformulation);
			$rowformulation=mysql_fetch_array($rsformulation);
			$formulation=$rowformulation['formulation'];*/
			
			$honeycomb_cost_case_prodwise=$honeycomb_cost[$i]/${conversion_factor_two.$distinct_dnsprod_code};
			$honeycomb_cost_case_prodwise=round(($honeycomb_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
			
			$sqlinserthoneycombcost="INSERT INTO honeycomb_cost 
							  SET plant_name='".$distinct_plant_name[$i]."',
							  state_code='".$dns_state_code[$i]."',
							  prod_code='".$distinct_dnsprod_code."',
							  transport_mode='".$transport_mode[$i]."',
							  honeycomb_cost='".$honeycomb_cost_case_prodwise."',
							  honeycomb_cost_ton='".$honeycomb_cost[$i]."',
							  ip_address='".$_SERVER['REMOTE_ADDR']."',
							  user_id='".$_SESSION['admin_login']."',
							   vertical_value='".$vertical_value[$i]."',
							  datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinserthoneycombcost);
							  
			/*if($formulation=='yes')
			{
				$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".${product_group_code.$distinct_dnsprod_code}."' 
										AND SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
			}
			else
			{
				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
										product_group_code='".${product_group_code.$distinct_dnsprod_code}."' 
										AND SUBSTRING(datetime,1,10)='".$current_date."'";
				$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
				$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
			}
			if($cntchkpricegeneration > 0)
			{
				generate_price_details($distinct_dnsprod_code,$distinct_branch_code[$i]);
			}*/
		// }
	   //}
	   $sqlinserthoneycombcostlog="INSERT INTO honeycomb_cost_log 
								  SET plant_name='".$distinct_plant_name[$i]."',
								  state_code='".$dns_state_code[$i]."',
								  honeycomb_cost='".$honeycomb_cost[$i]."',
								  transport_mode='".$transport_mode[$i]."',
								  prod_code='".$distinct_dnsprod_code_array[$i]."',
								  ip_address='".$_SERVER['REMOTE_ADDR']."',
								  user_id='".$_SESSION['admin_login']."',
								  vertical_value='".$vertical_value[$i]."',
								  operation_type='UPLOAD',
								  datetime=CURRENT_TIMESTAMP";
		mysql_query($sqlinserthoneycombcostlog);	
	}
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
}
	if($_REQUEST['mode']=="submit_detention")
	{
		$current_date=date('Y-m-d');
		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		$distinct_branch_code=$_POST['branch_code_array'];
		$detention_cost_case_prodwise=$_POST['detention_cost_case_array'];
		$vertical_value=$_POST['vertical_value_array'];
		$detention_cost=$_POST['detention_cost_array'];
		$distinct_plant_name=$_POST['plant_name_array'];
		$product_group_code=$_POST['oil_group_array'];
		
		//For log table
		$dns_branch_code=$_POST['dns_branch_code'];
		$detention_cost_ton=$_POST['detention_cost_ton_array'];
		$vertical_value_csv=$_POST['vertical_value_csv_array'];
		
		for($i=0;$i<count($distinct_dnsprod_code);$i++)
		{								 
			$sqlinsertdetentioncost="INSERT INTO detention_cost SET
									prod_code='".$distinct_dnsprod_code[$i]."',
									branch_code='".$distinct_branch_code[$i]."',
								   detention_cost	='".$detention_cost_case_prodwise[$i]."',
								   detention_cost_ton	='".$detention_cost[$i]."',
								   vertical_value='".$vertical_value[$i]."',
								   ip_address='".$_SERVER['REMOTE_ADDR']."',
								   user_id='".$_SESSION['admin_login']."',
								   datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertdetentioncost);
			$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".$product_group_code[$i]."'";
			$rschkformulation=mysql_query($sqlchkformulation);
			$rowchkformulation=mysql_fetch_array($rschkformulation);
			$is_formulation=$rowchkformulation['formulation'];

		if($is_formulation=='yes')
		{
			$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
									product_group_code='".$product_group_code[$i]."' 
									AND SUBSTRING(datetime,1,10)='".$current_date."'";
			$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
			$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
		}
		else
		{
			$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
									product_group_code='".$product_group_code[$i]."' 
									AND SUBSTRING(datetime,1,10)='".$current_date."'";
			$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
			$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
		}
		if($cntchkpricegeneration > 0)
		{
			generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);
		}
	  }
	  	for($k=0;$k<count($dns_branch_code);$k++)
		{
		   $sqlinsertdetentioncostlog="INSERT INTO detention_cost_log SET
											branch_code='".$dns_branch_code[$k]."',
										   detention_cost	='".$detention_cost_ton[$k]."',
										   vertical_value='".$vertical_value_csv[$k]."',
										   ip_address='".$_SERVER['REMOTE_ADDR']."',
										   user_id='".$_SESSION['admin_login']."',
										   operation_type='UPLOAD',
										   datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertdetentioncostlog);
		}
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=="submit_hirecost"){
		$current_date=date('Y-m-d');
		/*$distinct_dnsprod_code=$_POST['dns_prod_code_array'];
		$distinct_branch_code=$_POST['branch_code_array'];
		$primary_freight=$_POST['primary_freight_cost_array'];
		$vertical_value=$_POST['vertical_value_array'];
		$hire_cost=$_POST['hire_cost_array'];
		$distinct_plant_name=$_POST['plant_name_array'];
		$product_group_code=$_POST['oil_group_array'];
		$transport_mode=$_POST['transport_mode_array'];
		$truck_load=$_POST['truck_load_array'];*/
		
		$distinct_branch_code=$_POST['branch_code_array'];
		$distinct_plant_name=$_POST['plant_name_array'];
		$vertical_value=$_POST['vertical_value_array'];
		$hire_cost=$_POST['hire_cost_array'];
		$transport_mode=$_POST['transport_mode_array'];
		$truck_load=$_POST['truck_load_array'];
		$dns_branch_code=$_POST['depot_code_array'];
		
		for($i=0;$i<count($distinct_branch_code);$i++)
		{
			//$distinct_pack_type='';
			/*$sqldistinctpacktype="SELECT product_group_code,pack_type FROM load_distribution WHERE plant_name='".$distinct_plant_name[$i]."' 
								AND pack_type <>'' AND product_group_code <>'' GROUP BY product_group_code,pack_type";
			$rsdistinctpacktype=mysql_query($sqldistinctpacktype);
			while($rowdistinctpacktype=mysql_fetch_array($rsdistinctpacktype))
			{
				//$distinct_pack_type=$distinct_pack_type."'".$rowdistinctpacktype['pack_type']."'".",";
			   //$distinct_pack_type=substr($distinct_pack_type,0,-1);
			$product_group_code=$rowdistinctpacktype['product_group_code'];
			$distinct_pack_type=$rowdistinctpacktype['pack_type'];*/
			
			/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE branch_code='".$distinct_branch_code[$i]."' AND prod_desc 
										NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N' AND vertical_value='".$vertical_value[$i]."' 
										AND product_group_code='".$product_group_code."' AND pack_type='".$distinct_pack_type."'";*/
			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE branch_code='".$distinct_branch_code[$i]."' AND prod_desc 
										NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N' AND vertical_value='".$vertical_value[$i]."'";																
			$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
			while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
			{
				$dns_prod_code=$rowselectdistinctdnsprod['dns_prod_code'];
				$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code,PM.pack_type FROM product_group_master PGM,product_master PM 
								WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."'";
				$rschkformulation=mysql_query($sqlchkformulation);
				$rowchkformulation=mysql_fetch_array($rschkformulation);
				$is_formulation=$rowchkformulation['formulation'];
				${product_group_code.$dns_prod_code}=$rowchkformulation['product_group_code'];
				${pack_type.$dns_prod_code}=$rowchkformulation['pack_type'];

				/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE plant_name='".$distinct_plant_name[$i]."' 
								AND transport_mode='".$transport_mode[$i]."' AND truck_load='".$truck_load[$i]."' 
								AND pack_type='".${pack_type.$dns_prod_code}."' AND product_group_code='".${product_group_code.$dns_prod_code}."'  
								ORDER BY datetime DESC LIMIT 0,1";*/
				$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode[$i]."' 
								AND truck_load='".$truck_load[$i]."' AND prod_code='".$dns_prod_code."'  ORDER BY datetime DESC LIMIT 0,1";				
				$rsqtytruckload=mysql_query($sqlqtytruckload);
				$countqtytruckload=mysql_num_rows($rsqtytruckload);
				if($countqtytruckload >0)
				{
					$rowqtytruckload=mysql_fetch_array($rsqtytruckload);
					${qty_truck_load.$dns_prod_code}=$rowqtytruckload['qty_truck_load'];
				}
				else{
					${qty_truck_load.$dns_prod_code}=0;
				}
				if(${qty_truck_load.$dns_prod_code}>0)
				{
					${freight_cost.$dns_prod_code}=$hire_cost[$i]/${qty_truck_load.$dns_prod_code};
				}
				else
				{
					${freight_cost.$dns_prod_code}=0;
				}
				$sqlinsertfreightcost="INSERT INTO freight_cost SET 
										freight_cost='".${freight_cost.$dns_prod_code}."',
										hire_cost='".$hire_cost[$i]."',
										transport_mode='".$transport_mode[$i]."',
										truck_load='".$truck_load[$i]."',
										dns_prod_code='".$dns_prod_code."',
										branch_code='".$distinct_branch_code[$i]."',
										vertical_value='".$vertical_value[$i]."',
										ip_address='".$_SERVER['REMOTE_ADDR']."',
										user_id='".$_SESSION['admin_login']."',
										datetime=CURRENT_TIMESTAMP()";
			   mysql_query($sqlinsertfreightcost);
			   	if($is_formulation=='yes')
				{
					$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
											product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
					$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
				}
				else
				{
					$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	
											product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
											SUBSTRING(datetime,1,10)='".$current_date."'";
					$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
					$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
				}
				if($cntchkpricegeneration > 0)
				{
					generate_price_details($dns_prod_code,$distinct_branch_code[$i]);
				}
		  }//End of while loop
		  	$sqlinsertbasicfreight="INSERT INTO basic_freight 
									  SET branch_code='".$dns_branch_code[$i]."',
									  truck_load='".$truck_load[$i]."',
									  plant_name='".$distinct_plant_name[$i]."',
									  hire_cost='".$hire_cost[$i]."',
									  transport_mode='".$transport_mode[$i]."',
									  vertical_value='".$vertical_value[$i]."',
									  operation_type='UPLOAD',
									  ip_address='".$_SERVER['REMOTE_ADDR']."',
									  user_id='".$_SESSION['admin_login']."',
									  datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertbasicfreight);							  
	  }//End of for loop
	  $GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}

	if($_REQUEST['mode']=='submit_load_distribution'){
		
		//$plant_name=$_POST['plant_name'];
		$transport_mode=$_POST['transport_mode'];
		$load_capacity=$_POST['load_capacity'];
		$oil_group=$_POST['oil_group'];
		//$pack_type=$_POST['pack_type'];
		$dns_prod_code=$_POST['dns_prod_code'];
		$qty_truck_load=$_POST['qty_truck_load'];
		$vertical_value=$_POST['vertical_value'];
		
		for($i=0;$i<count($dns_prod_code);$i++)
		{				
			$sqlinsertloaddistribution="INSERT INTO load_distribution 
								  		SET transport_mode='".$transport_mode[$i]."',
								  		truck_load='".$load_capacity[$i]."',
										 qty_truck_load='".$qty_truck_load[$i]."',
										 prod_code='".$dns_prod_code[$i]."',
										 ip_address='".$_SERVER['REMOTE_ADDR']."',
										  user_id='".$_SESSION['admin_login']."',
										 vertical_value='".$vertical_value[$i]."',
										  operation_type='UPLOAD',
										  datetime=CURRENT_TIMESTAMP";
			mysql_query($sqlinsertloaddistribution);		
		}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
	if($_REQUEST['mode']=='submit_depot_route_freight')
	{
		$distinct_branch_code=$_POST['distinct_branch_code'];
		$route_code=$_POST['route_code'];	
		$freight=$_POST['freight'];	
		$acedns=$_POST['acedns'];	
		$transport_mode=$_POST['transport_mode'];	
		$capacity=$_POST['capacity'];	
		$state_code=$_POST['state_code'];
		$vertical_value=$_POST['vertical_value_array'];
		for($i=0;$i<count($distinct_branch_code);$i++){									
		$sqlbranchdestinationfreight="SELECT branch_code FROM branch_route_freight WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 
						AND route_code='".$route_code[$i]."' AND vertical_value='".$vertical_value[$i]."'";
		$rsbranchdestinationfreight=mysql_query($sqlbranchdestinationfreight);
		$countbranchdestinationfreight=mysql_num_rows($rsbranchdestinationfreight);
		if($countbranchdestinationfreight>=1 )
			{
				$sqlisplant="SELECT is_plant FROM branch_master WHERE branch_code='".$distinct_branch_code[$i]."'";
				$rsisplant=mysql_query($sqlisplant);
				$rowisplant=mysql_fetch_array($rsisplant);
				$is_plant=$rowisplant['is_plant'];
				
				if($is_plant=='yes')
				{
					$sqlbranchdestinationfreightupd  = "update branch_route_freight ";
					$sqlbranchdestinationfreightupd .= " SET acedns='N'";
					$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 
														AND route_code='".$route_code[$i]."' AND transport_mode='".$transport_mode[$i]."' 
														AND capacity='".$capacity[$i]."' AND vertical_value='".$vertical_value[$i]."'";
					mysql_query($sqlbranchdestinationfreightupd);
				}
				else
				{
					$sqlbranchdestinationfreightupd  = "update branch_route_freight ";
					$sqlbranchdestinationfreightupd .= " SET acedns='N'";
					$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 
														AND route_code='".$route_code[$i]."' AND vertical_value='".$vertical_value[$i]."'";
					mysql_query($sqlbranchdestinationfreightupd);
				}
			}
			// off it for plantwise branch route freight
			/*$sqlbranchdestinationfreight  = "insert into branch_route_freight ";
			$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";
			$sqlbranchdestinationfreight .= " ,route_code='".$route_code."'";
			$sqlbranchdestinationfreight .= " ,acedns='".$acedns."'";
			$sqlbranchdestinationfreight .= " ,freight='".$freight."'";
			$sqlbranchdestinationfreight .= " , `date`=CURDATE()";
			$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";*/
			
			// on it for plantwise branch route freight
			$sqlbranchdestinationfreight  = "insert into branch_route_freight ";
			$sqlbranchdestinationfreight .= " SET branch_code='".$distinct_branch_code[$i]."'";
			$sqlbranchdestinationfreight .= " ,route_code='".$route_code[$i]."'";
			$sqlbranchdestinationfreight .= " ,acedns='".$acedns[$i]."'";
			$sqlbranchdestinationfreight .= " ,freight='".$freight[$i]."'";
			$sqlbranchdestinationfreight .= " , `date`=CURDATE()";
			$sqlbranchdestinationfreight .= " , transport_mode='".$transport_mode[$i]."'";
			$sqlbranchdestinationfreight .= " , capacity='".$capacity[$i]."'";
			$sqlbranchdestinationfreight .= " , state_code='".$state_code[$i]."'";
			$sqlbranchdestinationfreight .= " , vertical_value='".$vertical_value[$i]."'";
			$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";
			mysql_query($sqlbranchdestinationfreight) or  array_push($error_array,"mysql_error().
							Internal DATA execution problem on depot freight table.PLease contact aceDNS admin.");				
			/*else
			{
				$sqlbranchdestinationfreightupd  = "update branch_route_freight ";
				$sqlbranchdestinationfreightupd .= " SET acedns='N'";
				$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' AND route_code='".$route_code."'";
				
				mysql_query($sqlbranchdestinationfreightupd);
			}*/
		}
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
?>
<script language="JavaScript">
function checkFields()
{
	if(document.form_add_CSV.zip_file.value=="")
	{
		alert("Please browse the ZIP file first...");
		document.form_add_CSV.zip_file.focus();
		return false;
	}
	
	var fname = document.form_add_CSV.zip_file.value.toUpperCase();
	var pos1 = fname.indexOf(".ZIP");
	
	if(pos1==-1)
	{
		alert("Invalid File Type\nPlease use ZIP only...");
		document.form_add_CSV.zip_file.focus();
		return false;	
	}
	return true;	
}
</script>
<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" >
			<table width="70%" align="center" cellpadding="5" cellspacing="2">
            	 <tr> 
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>Upload Pricing Data</u></font></td>
            	</tr>
            </table>
          </td>
    </tr>       
    <tr> 
        <td height="30"  align="left">
        <table width="100%">
            <tr> 
                <td width="90%" align="center" class="ERR"><?=$GLOBALS['msg']?></td>
                <td width="" align="right"></td>
            </tr>
            <tr> 
                <td width="90%" align="center" class="ERR" nowrap="nowrap">
                <?php 
                $errr_msg=$GLOBALS['error_msg'];
                $error_msgArr=explode('#',$errr_msg);
                if(count($error_msgArr)>0){
                    for($i=0;$i<count($error_msgArr);$i++){
                        echo "<b>$error_msgArr[$i]</b><br /><br />";
                    }
                }
                ?>
                </td>
                <td width="" align="right"></td>
            </tr>
        </table></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
<table width="70%" align="center" cellpadding="5" cellspacing="2" class="border">
	<form name="form_add_CSV" action="<?=$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']?>" method="post"  onsubmit="javascript:return checkFields();" enctype="multipart/form-data" >
	<input type="hidden" name="mode" value="csv_upload">
		
		<tr class="TDHEAD" > 
			<td colspan="10">Upload Zip File</td>
		</tr>
			
		<tr> 
		  <td align="right">Zip File*</td>
			<td width="2%">:</td>
			<td><input type="file" name="zip_file" class="" ><br/ ><strong><font color="#FF0000">[Extension will be .zip]</font></strong></td>
		</tr>
		<tr>
            <td>&nbsp;</td>
            <td >&nbsp;</td>
            <td>		
                <input type="submit" name="Add" value="Add" onClick="return check();"> 
                <!--input type="button" name="back" value=" Back " onClick="javascript:document.location='adminMain.php'"-->
            </td>
		</tr>
		<tr class="TDHEAD_SUB"> 
			<td colspan="10">&nbsp;</td>
		</tr>
	</form>
</table>
</td>
</tr>
</table><br /><br /><br />
<?php
if($_REQUEST['mode']=="csv_upload"){
	//For Unzip a zip file
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	$error_array=array();
	if (!file_exists("../csv/$folderName")){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
	}
		// Get array of all source files
		$files = scandir("../csv/$folderName");
		// Identify directories
		$source = "../csv/$folderName/";
		$destination = "../csv/$folderName/filebkup/";
		// Cycle through all source files
		foreach ($files as $file) {
		  if (in_array($file, array(".",".."))) continue;
		  // If we copied this successfully, mark it for deletion
		  if (@copy($source.$file, $destination.$file)) {
			$delete[] = $source.$file;
		  }
		}
		// Delete all successfully-copied files
		foreach ($delete as $file) {
		  unlink($file);
		}
	$upload_dir="../csv/$folderName/";
	if(file_exists($_FILES['zip_file']['tmp_name']))
	{
		$file_name = $_FILES['zip_file']['name'];
		$tmp_name=$_FILES['zip_file']['tmp_name'];
		$upload_file = $upload_dir.$file_name;
		
	    move_uploaded_file($tmp_name,$upload_file);
		$zip = new ZipArchive;
		if ($zip->open($upload_file)) {
			$zip->extractTo("../csv/$folderName/");
			$zip->close();
		} 
	 }
	//For primary freight CSV
	if(similar_file_exists("../csv/$folderName/primary freight.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/primary freight.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledataval='';
		$tabledata='<form name="primary_freight" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Primary freight</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Plant name</td>
						<td>Depot code</td>
						<td>Transport mode</td>
						<td>Truck load</td>
						<td>Hire cost</td>
						<td>Vertical value</td>
					  </tr>';

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
					$csv_row_count=$rec_count+1;
					
					$plant_name=trim($data[0]);
					$dns_branch_code=trim($data[1]);
					$transport_mode=trim($data[2]);
					$truck_load=trim($data[3]);
					$hire_cost=trim($data[4]);
					$vertical_value=trim($data[5]);
					
					$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
					$rsplant=mysql_query($sqlplant);
					$rowplant=mysql_fetch_array($rsplant);
					$countplant=mysql_num_rows($rsplant);
					if($countplant==0)
					{
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
					}
					
					$sqlbranchcode="SELECT branch_code,plant_name FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsbranchcode=mysql_query($sqlbranchcode);
					$rowbranchcode=mysql_fetch_array($rsbranchcode);
					$countdistinctbranchcode=mysql_num_rows($rsbranchcode);
					if($countdistinctbranchcode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$distinct_branch_code=$rowbranchcode['branch_code'];
					$distinct_plant_name=$rowbranchcode['plant_name'];
					if($distinct_plant_name!=$plant_name)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");
					}
					
					$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
					$rstransportmode=mysql_query($sqltransportmode);
					$counttransportmode=mysql_num_rows($rstransportmode);
					if($counttransportmode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");
					}
					$sqltruckload="SELECT load_capacity FROM plantwise_load_capacity WHERE load_capacity='".$truck_load."'";
					$rsttruckload=mysql_query($sqltruckload);
					$counttruckload=mysql_num_rows($rsttruckload);
					if($counttruckload==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for 
					Truck load column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Truck load)");
					}
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
												AND truck_load='".$truck_load."' ";
					$rscheckloaddistribution=mysql_query($sqlcheckloaddistribution);
					$countcheckloaddistribution=mysql_num_rows($rscheckloaddistribution);
					if($countcheckloaddistribution==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for 
					Truck load column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Load Distribution Error @Row (".$csv_row_count.") Column : (Transport mode,Truck load)");
					}
					$tabledatacsv.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">
									    <input type=\"hidden\" name=\"depot_code_array[]\" value=\"$dns_branch_code\">
									   <input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
										<input type=\"hidden\" name=\"transport_mode_array[]\" value=\"$transport_mode\">
										<input type=\"hidden\" name=\"truck_load_array[]\" value=\"$truck_load\">
										<input type=\"hidden\" name=\"hire_cost_array[]\" value=\"$hire_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$distinct_plant_name."</td>
											<td>".$dns_branch_code."</td>
											<td>".$transport_mode."</td>
											<td>".$truck_load."</td>
											<td align=\"right\">".number_format($hire_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
						/*$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">
									<input type=\"hidden\" name=\"depot_code_array[]\" value=\"$dns_branch_code\">
									<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
									<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$dns_prod_code\">
									<input type=\"hidden\" name=\"oil_group_array[]\" value=\"${product_group_code.$dns_prod_code}\">
									<input type=\"hidden\" name=\"transport_mode_array[]\" value=\"$transport_mode\">
									<input type=\"hidden\" name=\"truck_load_array[]\" value=\"$truck_load\">
									<input type=\"hidden\" name=\"primary_freight_cost_array[]\" value=\"${freight_cost.$dns_prod_code}\">
									<input type=\"hidden\" name=\"hire_cost_array[]\" value=\"$hire_cost\">
									<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";*/
			 }
			 $count++;
		  $rec_count++;
		}
		if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Primary freight</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_hirecost\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit5' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;		
	       $successval=1;
		 }
    }
	/*else
	{
		echo $successval="Naming convention for primary freight.csv is wrong.";
		exit();
	}*/
	//For Load distribution csv
	   if(similar_file_exists("../csv/$folderName/load distribution.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/load distribution.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$oil_group_array=array();
		$depot_cost_case_array=array();
		$depot_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Load Distribution</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Transport mode</td>
						<td>Load capacity</td>
						<td>SKU code</td>
						<td>Truck load quantity</td>
						<td>Vertical value</td>
					  </tr>';		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				//$plant_name=trim($data[0]);
				$transport_mode=trim($data[0]);
				$load_capacity=trim($data[1]);
				$dns_prod_code=trim($data[2]);
				//$pack_type=trim($data[4]);
				$qty_truck_load=trim($data[3]);
				$vertical_value=trim($data[4]);
				
				/*$sqldistinctplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
				$rsdistinctplant=mysql_query($sqldistinctplant);
				$countdistinctplant=mysql_num_rows($rsdistinctplant);
				if($countdistinctplant==0)
				{
					/*echo "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Plant name column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
				}*/
				$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
				$rstransportmode=mysql_query($sqltransportmode);
				$counttransportmode=mysql_num_rows($rstransportmode);
				if($counttransportmode==0)
				{
					/*echo  "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");
				}
				$sqlloadcapacity="SELECT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' 
								AND load_capacity='".$load_capacity."'";
				$rsloadcapacity=mysql_query($sqlloadcapacity);
				$countloadcapacity=mysql_num_rows($rsloadcapacity);
				if($countloadcapacity==0)
				{
					/*echo  "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Load capacity column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Load capacity)");
				}
				/*$sqloilgroup="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$oil_group."'";
				$rsoilgroup=mysql_query($sqloilgroup);
				$countoilgroup=mysql_num_rows($rsoilgroup);
				if($countoilgroup==0)
				{
					/*echo  "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Load capacity column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");
				}
				else
				{
					$rowoilgroup=mysql_fetch_array($rsoilgroup);
					$product_group_code=$rowoilgroup['product_group_code'];
				}
				$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type='".$pack_type."'";
				$rspacktype=mysql_query($sqlpacktype);
				$countpacktype=mysql_num_rows($rspacktype);
				if($countpacktype==0)
				{
					/*echo  "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Pack Type column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Pack Type)");

				}*/
				$sqlprodvalue="SELECT prod_code,product_group_code FROM product_master WHERE acedns='Y' AND 	dns_prod_code='".$dns_prod_code."'";
				$rsprodvalue=mysql_query($sqlprodvalue);
				$countprodvalue=mysql_num_rows($rsprodvalue);
				if($countprodvalue==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU code)");
				}
				else
				{
					$rowprodvalue=mysql_fetch_array($rsprodvalue);
					$product_group_code=$rowprodvalue['product_group_code'];
				}

				$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
				$rsverticalvalue=mysql_query($sqlverticalvalue);
				$countverticalvalue=mysql_num_rows($rsverticalvalue);
				if($countverticalvalue==0)
				{
					/*echo  "<tr> 
				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
					die;*/
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
				}
				$tabledatacsv.="<input type=\"hidden\" name=\"transport_mode[]\" value=\"$transport_mode\">
								<input type=\"hidden\" name=\"load_capacity[]\" value=\"$load_capacity\">
								<input type=\"hidden\" name=\"oil_group[]\" value=\"$product_group_code\">
								<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$dns_prod_code\">
								<input type=\"hidden\" name=\"qty_truck_load[]\" value=\"$qty_truck_load\">
								<input type=\"hidden\" name=\"vertical_value[]\" value=\"$vertical_value\">
								<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$transport_mode."</td>
										<td>".$load_capacity."</td>
										<td>".$dns_prod_code."</td>
										<td align=\"right\">".number_format($qty_truck_load)."</td>
										<td>".$vertical_value."</td>
									</tr>";
			  }
			  $count++;	
			  $rec_count++;
			}
			if(count($error_array) >0){
			 echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Load Distribution</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
			echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_load_distribution' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		 }
		/*else
		{
			echo $successval="Naming convention for Load distribution.csv is wrong.";
			exit();
		}*/
		//For Packing master csv
		if(similar_file_exists("../csv/$folderName/Packing master.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Packing master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
				  //print_r($data);
				  
					$dns_prod_code=trim($data[0]);
					$prod_desc=trim($data[1]);
					$packing_cost=trim($data[2]);
					$no_pc_one_case=trim($data[3]);
					$plant_name=trim($data[4]);
					$packing_realization=trim($data[5]);
					$csv_row_count=$rec_count+1;
					$current_date=date('Y-m-d');

					/*$sqlpackchk="SELECT dns_prod_code,prod_desc FROM packing_master WHERE dns_prod_code='".addslashes($dns_prod_code)."' AND prod_desc='".addslashes($prod_desc)."'";
					$rspackchk=mysql_query($sqlpackchk);
					$countpackchk=mysql_num_rows($rspackchk);
					$rowpackchk=mysql_fetch_array($rspackchk);
					
					if($countpackchk<1)
					{*/
						$sqlpacking  = "insert into packing_master SET ";
						$sqlpacking .= "  dns_prod_code='".mysql_real_escape_string($dns_prod_code)."'";
						$sqlpacking .= "  ,prod_desc='".mysql_real_escape_string($prod_desc)."'";
						$sqlpacking .= " , packing_cost='".mysql_real_escape_string($packing_cost)."'";
						$sqlpacking .= " , no_pc_one_case='".mysql_real_escape_string($no_pc_one_case)."'";
						$sqlpacking .= " , plant_name='".mysql_real_escape_string($plant_name)."'";
						$sqlpacking .= " , packing_realization='".mysql_real_escape_string($packing_realization)."'";
						$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";
						mysql_query($sqlpacking) or die(mysql_error().".Internal error occurrs @row $csv_row_count in Packing master.csv.Please check.");
					/*}
					else
					{
						$dns_prod_code_db=$rowpackchk['dns_prod_code'];
						$prod_desc_db=$rowpackchk['prod_desc'];
						$sqlupdatepacking  = "UPDATE packing_master SET ";
						$sqlupdatepacking .= " packing_cost='".mysql_real_escape_string($packing_cost)."'";
						$sqlupdatepacking .= " , datetime=CURRENT_TIMESTAMP";
						$sqlupdatepacking .= " WHERE dns_prod_code='".addslashes($dns_prod_code_db)."' AND prod_desc='".addslashes($prod_desc_db)."'";
						mysql_query($sqlupdatepacking) or die(mysql_error().".Internal error occurrs @row $csv_row_count in Packing master.csv.Please check.");
					}*/
				    $sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'  AND branch_code IN(SELECT branch_code FROM branch_master WHERE plant_name='".$plant_name."' AND acedns='Y')";
					$rsselectdistinctbranchcode=mysql_query($sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysql_fetch_array($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						//generate_price_details($prod_code,$branch_code);
						
						$sqldistinctplant="SELECT plant_name FROM branch_master WHERE branch_code='".$branch_code."'";
						$rsdistinctplant=mysql_query($sqldistinctplant);
						$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
						$distinct_plant_name=$rowdistinctplant['plant_name'];
						
						$sqlchkformulation="SELECT PGM.formulation,PGM.product_group_code FROM product_group_master PGM,product_master PM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$dns_prod_code."'";
						$rschkformulation=mysql_query($sqlchkformulation);
						$rowchkformulation=mysql_fetch_array($rschkformulation);
						$is_formulation=$rowchkformulation['formulation'];
						${product_group_code.$dns_prod_code}=$rowchkformulation['product_group_code'];
						
						if($is_formulation=='yes')
						{
							$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
													SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
							$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);
						}
						else
						{
							$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name."' AND 	
													product_group_code='".${product_group_code.$dns_prod_code}."' AND 	
													SUBSTRING(datetime,1,10)='".$current_date."'";
							$rschkpricegeneration=mysql_query($sqlchkpricegeneration);	
							$cntchkpricegeneration=mysql_num_rows($rschkpricegeneration);					
						}
						if($cntchkpricegeneration > 0)
						{
							generate_price_details($dns_prod_code,$branch_code);
						}
					}
				}
				 $rec_count++;
			}		
			//$successval=1;
			echo '<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
					<tr> 
					  <td width="90%" align="center" class="ERR" nowrap="nowrap" >
					  <font size="+1">Zip file extracted and data has been uploaded successfully</font></td>
					</tr>
				  </table>';	
		}
		/*else
		{
			echo $successval="Naming convention for Packing master.csv is wrong.";
			exit();	
		}*/
		

		//For Process cost csv
		if(similar_file_exists("../csv/$folderName/Process cost.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Process cost.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{

						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
				  //print_r($data);
				  
					$dns_prod_code=trim($data[0]);
					$process_cost=trim($data[1]);	
					$plant_name=trim($data[2]);				
					$csv_row_count=$rec_count+1;

						$sqlprocess  = "insert into process_cost SET ";
						$sqlprocess .= "  dns_prod_code='".mysql_real_escape_string($dns_prod_code)."'";
						$sqlprocess .= " , process_cost='".mysql_real_escape_string($process_cost)."'";
						$sqlprocess .= " , plant_name='".mysql_real_escape_string($plant_name)."'";
						$sqlprocess .= " , datetime=CURRENT_TIMESTAMP";
						mysql_query($sqlprocess) or die(mysql_error().".Internal error occurrs @row $csv_row_count in Process cost.csv.Please check.");
				    /*$sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
					$rsselectdistinctbranchcode=mysql_query($sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysql_fetch_array($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						generate_price_details($prod_code,$branch_code);
					}*/
				}
				 $rec_count++;
			}		
			//$successval=1;
			echo '<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
					<tr> 
					  <td width="90%" align="center" class="ERR" nowrap="nowrap" >
					  <font size="+1">Zip file extracted and data has been uploaded successfully</font></td>
					</tr>
				  </table>';
		}
		/*else
		{
			echo $successval="Naming convention for Process cost.csv is wrong.";
			exit();	
		}*/
		
	 	//For Oilrate formulation csv
		if(similar_file_exists("../csv/$folderName/Oilrate formulation.csv")!=false)
		{
			$filename=similar_file_exists("../csv/$folderName/Oilrate formulation.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$plantarray=array();
			$productgrouparray=array();
			$prodcodearray=array();
			$prodcodearraydistinct=array();
			$updationprodcodearray=array();
			$oilsarray=array();
			$formulationarray=array();
			$error_array=array();
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
				  //print_r($data);
				  
					$plant=trim($data[0]);
					$product_group=trim($data[1]);
					$prod_code=trim($data[2]);
					$oils=trim($data[3]);
					$formulation=trim($data[4]).'%';
					
					$csv_row_count=$rec_count+1;
					
					$sqlprodgroup="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$product_group."'";
					$rsprodgroup=mysql_query($sqlprodgroup);
					$rowprodgroup=mysql_fetch_array($rsprodgroup);
					$product_group_code=$rowprodgroup['product_group_code'];
					${totalformulation.$prod_code}=${totalformulation.$prod_code}+trim($data[4]);
					
					array_push($plantarray,$plant);
					array_push($productgrouparray,$product_group_code);
					array_push($prodcodearray,$prod_code);
					array_push($oilsarray,$oils);
					array_push($formulationarray,$formulation);
					if(!in_array($prod_code,$prodcodearraydistinct))
					{
						array_push($prodcodearraydistinct,$prod_code);
					}
				}
				 $rec_count++;
			}
			foreach($prodcodearraydistinct as $prodcodeval){
				if(${totalformulation.$prodcodeval}!=100)
					{
						array_push($error_array,"Formulation percent should be 100 for the product $prodcodeval");
					}
			}
			if(count($error_array) >0){
			   echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Oilrate Formulation</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\">No Data is Uploaded.</font></td></tr><br />";
			  }
		     else
		     {
				 for($i=0;$i<count($prodcodearray);$i++)
				 {
					if(!in_array($prodcodearray[$i],$updationprodcodearray))
					{
						$sqlupdateformulation="update loose_oilrate_formulation SET acedns='N' WHERE prod_code='".$prodcodearray[$i]."'";
						mysql_query($sqlupdateformulation);
						array_push($updationprodcodearray,$prodcodearray[$i]);
					}
					$sqloilformulation  = "insert into loose_oilrate_formulation SET ";
					$sqloilformulation .= "  plant_name='".mysql_real_escape_string($plantarray[$i])."'";
					$sqloilformulation .= " , product_group_code='".mysql_real_escape_string($productgrouparray[$i])."'";
					$sqloilformulation .= " , prod_code='".mysql_real_escape_string($prodcodearray[$i])."'";
					$sqloilformulation .= " , oils='".mysql_real_escape_string($oilsarray[$i])."'";
					$sqloilformulation .= " , formulation='".mysql_real_escape_string($formulationarray[$i])."'";
					$sqloilformulation .= " , datetime=CURRENT_TIMESTAMP";
					mysql_query($sqloilformulation) or die(mysql_error().".Internal error occurrs @row $csv_row_count in oilrate formulation.csv.Please check.");
				    /*$sqlselectdistinctbranchcode="SELECT branch_code,prod_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND prod_desc 
												NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N'";
					$rsselectdistinctbranchcode=mysql_query($sqlselectdistinctbranchcode);
					while($rowselectdistinctbranchcode=mysql_fetch_array($rsselectdistinctbranchcode))
					{
						$branch_code=$rowselectdistinctbranchcode['branch_code'];
						$prod_code=$rowselectdistinctbranchcode['prod_code'];
						generate_price_details($prod_code,$branch_code);
					}*/
				 }
				 echo '<table width="70%" align="center" cellpadding="2" cellspacing="2" border="0">
					<tr> 
					  <td width="90%" align="center" class="ERR" nowrap="nowrap" >
					  <font size="+1">Zip file extracted and data has been uploaded successfully</font></td>
					</tr>
				  </table>';
			 }
			//$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Process cost.csv is wrong.";
			exit();	
		}*/
		//For Depot Cost csv
	   if(similar_file_exists("../csv/$folderName/Depot cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/Depot cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$oil_group_array=array();
		$depot_cost_case_array=array();
		$depot_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$tabledata='<form name="depot_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Depot cost</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Depot code</td>
						<td>Depot cost</td>
						<td>Vertical value</td>
					  </tr>';

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$dns_branch_code=trim($data[0]);
				$depot_cost=trim($data[1]);
				$vertical_value=trim($data[2]);
								
					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						/*echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					/*$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 
											product_group_name='".$product_group_name."'";
					$rsproductgroupcode=mysql_query($sqlproductgroupcode);
					$countproductgroupcode=mysql_num_rows($rsproductgroupcode);
					if($countproductgroupcode==0)
					{
						/*echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil group column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");
					}
					$rowproductgroupcode=mysql_fetch_array($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];*/
					
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$tabledatacsv.="<input type=\"hidden\" name=\"dns_branch_code[]\" value=\"$dns_branch_code\">
									<input type=\"hidden\" name=\"product_group_name[]\" value=\"$product_group_name\">
									<input type=\"hidden\" name=\"depot_cost_array[]\" value=\"$depot_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$dns_branch_code."</td>
											<td align=\"right\">".number_format($depot_cost,2)."</td>
											<td>".$vertical_value."</td>
									</tr>";
									
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."' 
												AND vertical_value='".$vertical_value."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysql_query($sqlconversionfactor);
						$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						
						$depot_cost_case_prodwise=$depot_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$depot_cost_case_prodwise=round(($depot_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">
										<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"depot_cost_case_array[]\" value=\"$depot_cost_case_prodwise\">
										<input type=\"hidden\" name=\"depot_cost_ton_array[]\" value=\"$depot_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				}
				$count++;			  
			$rec_count++;
		   }
		   if(count($error_array) >0){
			   echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Depot Cost</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_depot\"><input type='submit' name='submit2' value='Final Upload' /></td></td><td colspan='3' align='left'><input type='button' name='button2' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}
		/*else
		{
			echo $successval="Naming convention for Depot cost.csv is wrong.";
			exit();
		}*/
	   //For Margin Cost csv
	   if(similar_file_exists("../csv/$folderName/margin cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/margin cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$oil_group_array=array();
		$oil_type_array=array();
		$margin_cost_case_array=array();
		$margin_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="margin_cost" method="post" action=""><input type="hidden" name="mode" value="submit_margin"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Margin cost</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>State code</td>
						<td>Oil group</td>
						<td>Oil type</td>
						<td>Margin cost</td>
						<td>Vertical value</td>
					  </tr>';
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);

				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				//$dns_branch_code=trim($data[1]);
				$dns_state_code=trim($data[0]);
				$product_group_name=trim($data[1]);
				$pack_size=trim($data[2]);
				$margin_cost=trim($data[3]);
				$vertical_value=trim($data[4]);
								
					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						/*echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					  /*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];*/
					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";
					$rsstatechk=mysql_query($sqlstatechk);
					$countstatechk=mysql_num_rows($rsstatechk);
					if($countstatechk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");
					}
					$rowstate=mysql_fetch_array($rsstatechk);
					$state_code=$rowstate['state_code'];
					
					$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 
										product_group_name='".$product_group_name."'";
					$rsproductgroupcode=mysql_query($sqlproductgroupcode);
					$countproductgroupcode=mysql_num_rows($rsproductgroupcode);
					if($countproductgroupcode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil group column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");
					}
					$rsproductgroupcode=mysql_query($sqlproductgroupcode);
					$rowproductgroupcode=mysql_fetch_array($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					if($is_formulation=='yes'){
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group) - RASOI can't be uploaded");
					}
					$sqlpacksizechk="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	pack_size='".$pack_size."'";
					$rspacksizechk=mysql_query($sqlpacksizechk);
					$countpacksizechk=mysql_num_rows($rspacksizechk);
					if($countpacksizechk==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil type column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil type)");
					}
					$sqloilgrouppacksizechk="SELECT prod_code FROM product_master WHERE acedns='Y' AND product_group_code='".$product_group_code."' AND	
										pack_size='".$pack_size."'";
					$rsoilgrouppacksizechk=mysql_query($sqloilgrouppacksizechk);
					$countoilgrouppacksizechk=mysql_num_rows($rsoilgrouppacksizechk);
					if($countoilgrouppacksizechk==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil type column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group,Oil type)");
					}
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$tabledatacsv.="<input type=\"hidden\" name=\"dns_state_code[]\" value=\"$dns_state_code\">
									<input type=\"hidden\" name=\"product_group_name[]\" value=\"$product_group_name\">
									<input type=\"hidden\" name=\"oil_type_array[]\" value=\"$pack_size\">
									<input type=\"hidden\" name=\"margin_cost_ton_array[]\" value=\"$margin_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$dns_state_code."</td>
											<td>".$product_group_name."</td>
											<td>".$pack_size."</td>
											<td align=\"right\">".number_format($margin_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
												product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."' AND pack_size='".$pack_size."'";*/
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 
												product_group_code='".$product_group_code."' AND prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND pack_size='".$pack_size."'";	
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";
						$rsconversionfactor=mysql_query($sqlconversionfactor);
						$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						
						$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"state_code_array[]\" value=\"$dns_state_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"oil_group_array[]\" value=\"$product_group_code\">
										<input type=\"hidden\" name=\"oil_type_array[]\" value=\"$pack_size\">
										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost_case_prodwise\">
										<input type=\"hidden\" name=\"margin_cost_array[]\" value=\"$margin_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				 }
			$count++;				  
			$rec_count++;
		   }
		    if(count($error_array) >0){
				  echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Margin Cost</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='4' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}
		//For Margin Cost RASOI csv
	  /* if(similar_file_exists("../csv/$folderName/margin cost-rasoi.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/margin cost-rasoi.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="margin_cost" method="post" action=""><input type="hidden" name="mode" value="submit_margin_rasoi"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Margin cost RASOI</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Plant name</td>
						<td>Depot code</td>
						<td>SKU code</td>
						<td>Margin cost</td>
						<td>Vertical value</td>
					  </tr>';
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$plant_name=trim($data[0]);
				//$dns_branch_code=trim($data[1]);
				$dns_state_code=trim($data[1]);
				$dns_prod_code=trim($data[2]);
				$margin_cost=trim($data[3]);
				$vertical_value=trim($data[4]);
								
					$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
					$rsplant=mysql_query($sqlplant);
					$rowplant=mysql_fetch_array($rsplant);
					$countplant=mysql_num_rows($rsplant);
					if($countplant==0)
					{
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
					}
					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
					  array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					if($distinct_plant_name!=$plant_name)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");
					}
					
					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";
					$rsproductcode=mysql_query($sqlproductcode);
					$countproductcode=mysql_num_rows($rsproductcode);
					if($countproductcode==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");
					}
					$rowproductcode=mysql_fetch_array($rsproductcode);
					$is_formulation=$rowproductcode['formulation'];
					if($is_formulation=='no'){
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group) - only RASOI can be uploaded)");
					}
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$sqldepotproductchk="SELECT prod_code FROM product_master WHERE acedns='Y' AND branch_code='".$distinct_branch_code."' AND	
										dns_prod_code='".$dns_prod_code."'";
					$rsdepotproductchk=mysql_query($sqldepotproductchk);
					$countdepotproductchk=mysql_num_rows($rsdepotproductchk);
					if($countdepotproductchk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code,SKU code)");
					}
					$tabledatacsv.="<input type=\"hidden\" name=\"plant_name_csv_array[]\" value=\"$distinct_plant_name\">
									<input type=\"hidden\" name=\"dns_branch_code[]\" value=\"$dns_branch_code\">
									<input type=\"hidden\" name=\"margin_cost_ton_array[]\" value=\"$margin_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$distinct_plant_name."</td>
											<td>".$dns_branch_code."</td>
											<td>".$dns_prod_code."</td>
											<td align=\"right\">".number_format($margin_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two 
												FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."' AND 
												dns_prod_code='".$dns_prod_code."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						/*$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysql_query($sqlconversionfactor);
						$rowconversionfactor=mysql_fetch_array($rsconversionfactor);*/
		
						/*${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];
						
						$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">
										<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost_case_prodwise\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				}
			$count++;				  
			$rec_count++;
		   }
		    if(count($error_array) >0){
				  echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Margin Cost RASOI</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='4' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}*/
	   //For Margin Cost RASOI csv
	  if(similar_file_exists("../csv/$folderName/margin cost-rasoi.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/margin cost-rasoi.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="margin_cost" method="post" action=""><input type="hidden" name="mode" value="submit_margin_rasoi"><table border="1" style="border-collapse:collapse;" class="border" width="50%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Margin cost RASOI</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>State code</td>
						<td>SKU code</td>
						<td>Margin cost</td>
						<td>Vertical value</td>
					  </tr>';
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$dns_state_code=trim($data[0]);
				$dns_prod_code=trim($data[1]);
				$margin_cost=trim($data[2]);
				$vertical_value=trim($data[3]);
								
					/*$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
					$rsplant=mysql_query($sqlplant);
					$rowplant=mysql_fetch_array($rsplant);
					$countplant=mysql_num_rows($rsplant);
					if($countplant==0)
					{
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
					}
					$rowplant=mysql_fetch_array($rsplant);
					$distinct_plant_name=$rowplant['plant_name'];*/
					
					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
					  array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					if($distinct_plant_name!=$plant_name)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");
					}*/
					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";
					$rsstatechk=mysql_query($sqlstatechk);
					$countstatechk=mysql_num_rows($rsstatechk);
					if($countstatechk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");
					}
					$rowstate=mysql_fetch_array($rsstatechk);
					$state_code=$rowstate['state_code'];
					
					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";
					$rsproductcode=mysql_query($sqlproductcode);
					$countproductcode=mysql_num_rows($rsproductcode);
					if($countproductcode==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");
					}
					$rowproductcode=mysql_fetch_array($rsproductcode);
					$is_formulation=$rowproductcode['formulation'];
					if($is_formulation=='no'){
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group) - only RASOI can be uploaded)");
					}
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					/*$sqldepotproductchk="SELECT prod_code FROM product_master WHERE acedns='Y' AND branch_code='".$distinct_branch_code."' AND	
										dns_prod_code='".$dns_prod_code."'";
					$rsdepotproductchk=mysql_query($sqldepotproductchk);
					$countdepotproductchk=mysql_num_rows($rsdepotproductchk);
					if($countdepotproductchk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code,SKU code)");
					}*/
					$tabledatacsv.="<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$dns_state_code."</td>
											<td>".$dns_prod_code."</td>
											<td align=\"right\">".number_format($margin_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two 
												FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N'  AND dns_prod_code='".$dns_prod_code."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
		
						${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];
						
						$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"state_code_array[]\" value=\"$dns_state_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost_case_prodwise\">
										<input type=\"hidden\" name=\"margin_cost_ton_array[]\" value=\"$margin_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				}
			$count++;				  
			$rec_count++;
		   }
		    if(count($error_array) >0){
				  echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Margin Cost RASOI</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}

	   //For Margin Cost Specialty Fats csv
	   //For Margin Cost Specialty Fats csv
	   if(similar_file_exists("../csv/$folderName/margin cost-SF.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/margin cost-SF.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="margin_cost" method="post" action=""><input type="hidden" name="mode" value="submit_margin_sf"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Margin cost Specialty Fats</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>State code</td>
						<td>SKU code</td>
						<td>Margin cost</td>
						<td>Vertical value</td>
					  </tr>';
		
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$dns_state_code=trim($data[0]);
				$dns_prod_code=trim($data[1]);
				$margin_cost=trim($data[2]);
				$vertical_value=trim($data[3]);
								
					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";
					$rsstatechk=mysql_query($sqlstatechk);
					$countstatechk=mysql_num_rows($rsstatechk);
					if($countstatechk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");
					}
					$rowstate=mysql_fetch_array($rsstatechk);
					$state_code=$rowstate['state_code'];
					
					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";
					$rsproductcode=mysql_query($sqlproductcode);
					$countproductcode=mysql_num_rows($rsproductcode);
					if($countproductcode==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");
					}
					$rowproductcode=mysql_fetch_array($rsproductcode);
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					/*$sqldepotproductchk="SELECT prod_code FROM product_master WHERE acedns='Y' AND branch_code='".$distinct_branch_code."' AND	
										dns_prod_code='".$dns_prod_code."'";
					$rsdepotproductchk=mysql_query($sqldepotproductchk);
					$countdepotproductchk=mysql_num_rows($rsdepotproductchk);
					if($countdepotproductchk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code,SKU code)");
					}*/
					$tabledatacsv.="<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$dns_state_code."</td>
											<td>".$dns_prod_code."</td>
											<td align=\"right\">".number_format($margin_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two 
												FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N'  AND dns_prod_code='".$dns_prod_code."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
		
						${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];
						
						$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"state_code_array[]\" value=\"$dns_state_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost_case_prodwise\">
										<input type=\"hidden\" name=\"margin_cost_ton_array[]\" value=\"$margin_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				}
			$count++;				  
			$rec_count++;
		   }
		    if(count($error_array) >0){
				  echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Margin Cost Specialty Fats</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}

	   //For Honey comb Cost csv
	  /* if(similar_file_exists("../csv/$folderName/honeycomb cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/honeycomb cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$oil_group_array=array();
		$transport_mode_array=array();
		$honeycomb_cost_case_array=array();
		$honeycomb_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$tabledata='<form name="honeycomb_cost" method="post" action=""><input type="hidden" name="mode" value="submit_honeycomb"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Honeycomb cost</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Plant name</td>
						<td>Depot code</td>
						<td>Transport mode</td>
						<td>SKU code</td>
						<td>Honeycomb cost</td>
						<td>Vertical value</td>
					  </tr>';
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$plant_name=trim($data[0]);
				$dns_branch_code=trim($data[1]);
				$transport_mode=trim($data[2]);
				$dns_prod_code=trim($data[3]);
				$honeycomb_cost=trim($data[4]);
				$vertical_value=trim($data[5]);
								
					$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
					$rsplant=mysql_query($sqlplant);
					$rowplant=mysql_fetch_array($rsplant);
					$countplant=mysql_num_rows($rsplant);
					if($countplant==0)
					{
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
					}

					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					 /* array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					
					if($distinct_plant_name!=$plant_name)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");
					}
					$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
					$rstransportmode=mysql_query($sqltransportmode);
					$counttransportmode=mysql_num_rows($rstransportmode);
					if($counttransportmode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");
					}
					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";
					$rsproductcode=mysql_query($sqlproductcode);
					$countproductcode=mysql_num_rows($rsproductcode);
					if($countproductcode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for sku code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");
					}
					/*$rowproductgroupcode=mysql_fetch_array($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type='".$pack_type."'";
					$rspacktype=mysql_query($sqlpacktype);
					$countpacktype=mysql_num_rows($rspacktype);
					if($countpacktype==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Pack Type column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Pack Type)");
					}*/
					/*$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					 /*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master 
												WHERE  prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND dns_prod_code='".$dns_prod_code."' AND 
												branch_code='".$distinct_branch_code."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					$countselectdistinctdnsprod=mysql_num_rows($rsselectdistinctdnsprod);
					if($countselectdistinctdnsprod==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Depot code and sku code combination is mismatching in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Depot code and sku code combination is mismatching @Row (".$csv_row_count.")");
					}
					else
					{
					$tabledatacsv.="<input type=\"hidden\" name=\"plant_name_csv_array[]\" value=\"$plant_name\">
									<input type=\"hidden\" name=\"dns_branch_code[]\" value=\"$dns_branch_code\">
									<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
									<input type=\"hidden\" name=\"transport_mode_array[]\" value=\"$transport_mode\">
									<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$dns_prod_code\">
									<input type=\"hidden\" name=\"honeycomb_cost_array[]\" value=\"$honeycomb_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$plant_name."</td>
											<td>".$dns_branch_code."</td>
											<td>".$transport_mode."</td>
											<td>".$dns_prod_code."</td>
											<td align=\"right\">".number_format($honeycomb_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					}
				}
			$count++;				  
			$rec_count++;
		   }
		   if(count($error_array) >0){
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Honeycomb Cost</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'>&nbsp;&nbsp;&nbsp;<input type='submit' name='submit3' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}
		/*else
		{
			echo $successval="Naming convention for honeycomb cost.csv is wrong.";
			exit();
		}*/
	  //For Honey comb Cost csv
	  if(similar_file_exists("../csv/$folderName/honeycomb cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/honeycomb cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$oil_group_array=array();
		$transport_mode_array=array();
		$honeycomb_cost_case_array=array();
		$honeycomb_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$tabledata='<form name="honeycomb_cost" method="post" action=""><input type="hidden" name="mode" value="submit_honeycomb"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Honeycomb cost</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Plant name</td>
						<td>State code</td>
						<td>Transport mode</td>
						<td>SKU code</td>
						<td>Honeycomb cost</td>
						<td>Vertical value</td>
					  </tr>';
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$plant_name=trim($data[0]);
				//$dns_branch_code=trim($data[1]);
				$dns_state_code=trim($data[1]);
				$transport_mode=trim($data[2]);
				$dns_prod_code=trim($data[3]);
				$honeycomb_cost=trim($data[4]);
				$vertical_value=trim($data[5]);
								
					$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";
					$rsplant=mysql_query($sqlplant);
					$rowplant=mysql_fetch_array($rsplant);
					$countplant=mysql_num_rows($rsplant);
					if($countplant==0)
					{
						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");
					}
					$rowplant=mysql_fetch_array($rsplant);
					$distinct_plant_name=$rowplant['plant_name'];
					
					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					 /*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					
					if($distinct_plant_name!=$plant_name)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");
					}*/
					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";
					$rsstatechk=mysql_query($sqlstatechk);
					$countstatechk=mysql_num_rows($rsstatechk);
					if($countstatechk==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");
					}
					$rowstate=mysql_fetch_array($rsstatechk);
					$state_code=$rowstate['state_code'];
					
					$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
					$rstransportmode=mysql_query($sqltransportmode);
					$counttransportmode=mysql_num_rows($rstransportmode);
					if($counttransportmode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");
					}
					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 
									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";
					$rsproductcode=mysql_query($sqlproductcode);
					$countproductcode=mysql_num_rows($rsproductcode);
					if($countproductcode==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for sku code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");
					}
					/*$rowproductgroupcode=mysql_fetch_array($rsproductgroupcode);
					$product_group_code=$rowproductgroupcode['product_group_code'];
					$is_formulation=$rowproductgroupcode['formulation'];
					$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type='".$pack_type."'";
					$rspacktype=mysql_query($sqlpacktype);
					$countpacktype=mysql_num_rows($rspacktype);
					if($countpacktype==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Pack Type column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Pack Type)");
					}*/
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
					 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					/*$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master 
												WHERE  prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND dns_prod_code='".$dns_prod_code."' AND 
												branch_code='".$distinct_branch_code."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					$countselectdistinctdnsprod=mysql_num_rows($rsselectdistinctdnsprod);
					if($countselectdistinctdnsprod==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Depot code and sku code combination is mismatching in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						/*array_push($error_array,"Depot code and sku code combination is mismatching @Row (".$csv_row_count.")");
					}
					else
					{*/
					$tabledatacsv.="<input type=\"hidden\" name=\"plant_name_csv_array[]\" value=\"$distinct_plant_name\">
									<input type=\"hidden\" name=\"dns_state_code[]\" value=\"$dns_state_code\">
									<input type=\"hidden\" name=\"transport_mode_array[]\" value=\"$transport_mode\">
									<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$dns_prod_code\">
									<input type=\"hidden\" name=\"honeycomb_cost_array[]\" value=\"$honeycomb_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$plant_name."</td>
											<td>".$dns_state_code."</td>
											<td>".$transport_mode."</td>
											<td>".$dns_prod_code."</td>
											<td align=\"right\">".number_format($honeycomb_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					//}
				}
			$count++;				  
			$rec_count++;
		   }
		   if(count($error_array) >0){
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Honeycomb Cost</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		   echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'>&nbsp;&nbsp;&nbsp;<input type='submit' name='submit3' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}
		/*else
		{
			echo $successval="Naming convention for honeycomb cost.csv is wrong.";
			exit();
		}*/

		//For Detention Cost csv
	   if(similar_file_exists("../csv/$folderName/detention cost.csv")!=false)
	   {
		$filename=similar_file_exists("../csv/$folderName/detention cost.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$plant_name_array=array();
		$depot_code_array=array();
		$branch_code_array=array();
		$dns_prod_code_array=array();
		$detention_cost_case_array=array();
		$detention_cost_array=array();
		$vertical_value_array=array();
		$count=0;
		$tabledataval='';
		$tabledatacsv='';
		$tabledata='<form name="margin_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="50%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="8" class="TDHEAD" align="center">Detention cost</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td width="">SI</td>
						<td width="">Depot code</td>
						<td width="">Detention cost</td>
						<td width="">Vertical value</td>
					  </tr>';

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$lines = file($filename);
			$customerarray=array();
			$customernamearray=array();
			
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
				$csv_row_count=$rec_count+1;
				
				$dns_branch_code=trim($data[0]);
				$detention_cost=trim($data[1]);
				$vertical_value=trim($data[2]);

					$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
					$rsdistinctplant=mysql_query($sqldistinctplant);
					$countdistinctplant=mysql_num_rows($rsdistinctplant);
					if($countdistinctplant==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Detention cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
					}
					$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
					$distinct_plant_name=$rowdistinctplant['plant_name'];
					$distinct_branch_code=$rowdistinctplant['branch_code'];
					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
					$rsverticalvalue=mysql_query($sqlverticalvalue);
					$countverticalvalue=mysql_num_rows($rsverticalvalue);
					if($countverticalvalue==0)
					{
						/*echo  "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Detention cost.csv at row ".$csv_row_count."</font></td></tr>";
						die;*/
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
					}
					$tabledatacsv.="<input type=\"hidden\" name=\"dns_branch_code[]\" value=\"$dns_branch_code\">
									<input type=\"hidden\" name=\"detention_cost_ton_array[]\" value=\"$detention_cost\">
									<input type=\"hidden\" name=\"vertical_value_csv_array[]\" value=\"$vertical_value\">
									<tr id=\"tab\">
											<td>".$count."</td>
											<td>".$dns_branch_code."</td>
											<td align=\"right\">".number_format($detention_cost,2)."</td>
											<td>".$vertical_value."</td>
										</tr>";
					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 
												AND acedns='Y' AND black_list='N' AND branch_code='".$distinct_branch_code."' 
												AND vertical_value='".$vertical_value."'";
					$rsselectdistinctdnsprod=mysql_query($sqlselectdistinctdnsprod);
					while($rowselectdistinctdnsprod=mysql_fetch_array($rsselectdistinctdnsprod))
					{
						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];
						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 
											dns_prod_code='".$distinct_dnsprod_code."' AND branch_code='".$distinct_branch_code."'";
						$rsconversionfactor=mysql_query($sqlconversionfactor);
						$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
		
						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];
						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];
						${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];
						
						$sqlchkformulation="SELECT formulation FROM product_group_master WHERE 
										product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";
						$rschkformulation=mysql_query($sqlchkformulation);
						$rowchkformulation=mysql_fetch_array($rschkformulation);
						$is_formulation=$rowchkformulation['formulation'];
		
						$detention_cost_case_prodwise=$detention_cost/${conversion_factor_two.$distinct_dnsprod_code};
						$detention_cost_case_prodwise=round(($detention_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);
						
						$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">
										<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">
										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">
										<input type=\"hidden\" name=\"oil_group_array[]\" value=\"${product_group_code.$distinct_dnsprod_code}\">
										<input type=\"hidden\" name=\"detention_cost_case_array[]\" value=\"$detention_cost_case_prodwise\">
										<input type=\"hidden\" name=\"detention_cost_array[]\" value=\"$detention_cost\">
										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";
					}
				}
				$count++;			  
			$rec_count++;
		   }
		   if(count($error_array) >0){
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Detention Cost</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		    echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_detention\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit4' value='Final Upload' /></td><td colspan='3' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
		}
		/*else
		{
			echo $successval="Naming convention for detention cost.csv is wrong.";
			exit();
		}*/
	//For Depot route Freight
	if(similar_file_exists("../csv/$folderName/Depot Route Freight.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/Depot Route Freight.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="depot_route_freight" method="post" action=""><input type="hidden" name="mode" value="submit_depot_route_freight"><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="9" class="TDHEAD" align="center">Depot Route Freight</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Depot code</td>
						<td>Route</td>
						<td>Freight</td>
						<td>acedns</td>
						<td>Transport mode</td>
						<td>Capacity</td>
						<td>State code</td>
						<td>Vertical value</td>
					  </tr>';
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
			  //print_r($data);
			  	$csv_row_count=$rec_count+1;
				$branch_code_name=trim($data[0]);
				$route_code_name=trim($data[1]);
				$freight=trim($data[2]);
				$acedns=trim($data[3]);
				//$date=trim($data[4]);
				$transport_mode=trim($data[4]);
				$capacity=trim($data[5]);
				$state_code=trim($data[6]);
				$vertical_value=trim($data[7]);
			
				/*$dateArr=explode('-',$date);
				if(strlen($dateArr[2])==2)
				{
					$year='20'.$dateArr[2];
				}
				else
				{
					$year=$dateArr[2];
				}
				$finaldate=$year.'-'.$dateArr[0].'-'.$dateArr[1];*/
				$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
				$rsdistinctplant=mysql_query($sqldistinctplant);
				$countdistinctplant=mysql_num_rows($rsdistinctplant);
				if($countdistinctplant==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
				}
				$rowdistinctplant=mysql_fetch_array($rsdistinctplant);
				$distinct_plant_name=$rowdistinctplant['plant_name'];
				$distinct_branch_code=$rowdistinctplant['branch_code'];
				$sqlroutenamechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_code_name)."'";
				$rsroutenamechk=mysql_query($sqlroutenamechk);
				$countroutenamechk=mysql_num_rows($rsroutenamechk);
				if($countroutenamechk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Route)");
				}
				$rowroutenamechk=mysql_fetch_array($rsroutenamechk);
				$route_code=$rowroutenamechk['route_code'];

				$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";
				$rstransportmode=mysql_query($sqltransportmode);
				$counttransportmode=mysql_num_rows($rstransportmode);
				if($counttransportmode==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");
				}
				$sqlloadcapacity="SELECT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' 
								AND load_capacity='".$capacity."'";
				$rsloadcapacity=mysql_query($sqlloadcapacity);
				$countloadcapacity=mysql_num_rows($rsloadcapacity);
				if($countloadcapacity==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Load capacity)");
				}
				$sqlstatechk="SELECT dns_state_code FROM state_master WHERE dns_state_code='".addslashes($state_code)."'";
				$rsstatechk=mysql_query($sqlstatechk);
				$countstatechk=mysql_num_rows($rsstatechk);
				if($countstatechk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");
				}
				$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
												AND truck_load='".$capacity."' ";
				$rscheckloaddistribution=mysql_query($sqlcheckloaddistribution);
				$countcheckloaddistribution=mysql_num_rows($rscheckloaddistribution);
				if($countcheckloaddistribution==0)
				{
					array_push($error_array,"Load Distribution Error @Row (".$csv_row_count.") Columns : (Transport mode,Capacity)");
				}
				$sqlchkstateroutecombination="SELECT customer_code FROM customer_master WHERE route_code='".$route_code."' AND state_code='".$state_code."' 
											AND acedns='Y'";
				$rschkstateroutecombination=mysql_query($sqlchkstateroutecombination);
				$countchkstateroutecombination=mysql_num_rows($rschkstateroutecombination);
				if($countchkstateroutecombination==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Route,State code)");
				}
				$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";
				$rsverticalvalue=mysql_query($sqlverticalvalue);
				$countverticalvalue=mysql_num_rows($rsverticalvalue);
				if($countverticalvalue==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");
				}
				$tabledatacsv.="<input type=\"hidden\" name=\"distinct_branch_code[]\" value=\"$distinct_branch_code\">
								<input type=\"hidden\" name=\"route_code[]\" value=\"$route_code\">
								<input type=\"hidden\" name=\"freight[]\" value=\"$freight\">
								<input type=\"hidden\" name=\"acedns[]\" value=\"$acedns\">
								<input type=\"hidden\" name=\"transport_mode[]\" value=\"$transport_mode\">
								<input type=\"hidden\" name=\"capacity[]\" value=\"$capacity\">
								<input type=\"hidden\" name=\"state_code[]\" value=\"$state_code\">
								<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">
								<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$branch_code_name."</td>
										<td>".$route_code_name."</td>
										<td align=\"right\">".number_format($freight,2)."</td>
										<td>".$acedns."</td>
										<td>".$transport_mode."</td>
										<td>".$capacity."</td>
										<td>".$state_code."</td>
										<td>".$vertical_value."</td>
								</tr>";
			   }
			   $count++;
			 $rec_count++;
			}
			if(count($error_array) >0){
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"9\"><font size=\"+2\"><u>Depot Route Freight</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"9\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		    echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='submit' name='submit8' value='Final Upload' /></td><td colspan='5' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadPricingGenerationDatareconstruct.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
	/*else
	{
		echo $successval="Naming convention for Depot route freight.csv is wrong.";
		exit();
	}	*/
	if($successval==1)
	{
		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
		if(mysql_query($sqlInsert))
		{
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			//$mailto='kuntald@coral.in';
			$mailto='';
		
			if(count($error_array)>0)
			{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' with error(s) on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
				
				for($i=0;$i<count($error_array);$i++){
					$mailbody.= "<b>$error_array[$i]</b><br /><br />";
				}	
			}
			else{
				$mailsub='Data has been successfully uploaded to '.$nick_name.' on '.date('d-m-Y H:i:s');
				$mailbody='Data has been successfully uploaded to '.$nick_name.' database.';	
			}
			if($dupliacateproductval!=''){
				$mailbody.=$dupliacateproductval;
			}
			//$mailto='';			
			if(mail($mailto, $mailsub, $mailbody, $headers,'-facedns@coral.in'))
			{
				if(count($error_array)>0)
				{
					$error_string=implode('#',$error_array);
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully with the following error(s).';
				}
				else{
					$GLOBALS['msg'] = '<b>Zip file extracted and data has been uploaded successfully</b>';
				}
				$GLOBALS['error_msg']=$error_string;
				/*$error_msgArr=explode('#',$GLOBALS['error_msg']);
					if(count($error_msgArr)>0){
						for($i=0;$i<count($error_msgArr);$i++){
							echo "<b>$error_msgArr[$i]</b><br /><br />";
						}
					}*/
				disphtml("main();");
			}
			else
			{
				echo $GLOBALS['msg'] = "Error in mail sending.";
				disphtml("main();");
			}
			//echo $err = 'Zip file extracted and data has been uploaded successfully';
		}
		else 
		{
			echo $GLOBALS['msg'] = "Problem with uploading Zip file";
			disphtml("main();");
		}
	}
  }
}
?>