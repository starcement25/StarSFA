<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
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
	$nick_name = strtoupper($_SESSION['nick_name']);
	$folderName = strtoupper($_SESSION['nick_name']);
	if($_REQUEST['mode']=='submit_ra_sauda')
	{
		$customer_code_name=$_POST['customer_code_name'];
		$route_code_name=$_POST['route_code_name'];
		$broker_code_name=$_POST['broker_code_name'];
		$app_contract_date=$_POST['app_contract_date'];
		$material_code=$_POST['material_code'];;
		$material_qty_case=$_POST['material_qty_case'];;
		$depot_code=$_POST['depot_code'];;
		$incoterms=$_POST['incoterms'];;
		$emp_code_array=$_POST['emp_code'];;
		$vertical_value=$_POST['vertical_value'];;
		$sale_rate=$_POST['sale_rate'];
		$plant_name=$_POST['plant_name'];
		$Remarks=$_POST['Remarks'];
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		for($i=0;$i<count($customer_code_name);$i++){
		$sqlemp="SELECT emp_code,emp_name FROM employee_master WHERE dns_emp_code='".$emp_code_array[$i]."'";
		$rsemp=mysql_query($sqlemp);
		$rowemp=mysql_fetch_array($rsemp);
		$emp_code=$rowemp['emp_code'];
		$emp_name=$rowemp['emp_name'];
		
		$sqlcustomercode="SELECT customer_code,route_code,customer_name,sauda_validity_period,incoterms,transport_mode,loadability_ton,state_code 
						FROM customer_master WHERE dns_customer_code='".$customer_code_name[$i]."' AND acedns='Y' AND emp_code='".$emp_code."'";
		$rscustomercode=mysql_query($sqlcustomercode);
		$rowcustomercode=mysql_fetch_array($rscustomercode); 
		$customer_code=$rowcustomercode['customer_code'];
		$customer_name=$rowcustomercode['customer_name'];
		$sauda_valid_days = $rowcustomercode['sauda_validity_period'];
		$incoterms_val = $rowcustomercode['incoterms'];
		$transport_mode = $rowcustomercode['transport_mode'];
		$loadability_ton = $rowcustomercode['loadability_ton'];
		$state_code = $rowcustomercode['state_code'];
		
		$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_code_name[$i]."'";
		$rsroutecode=mysql_query($sqlroutecode);
		$rowroutecode=mysql_fetch_array($rsroutecode);
		$route_code=$rowroutecode['route_code'];
		$sqlbrachname="SELECT branch_name,branch_code,branch_state,is_plant,plant_name FROM branch_master WHERE dns_branch_code='".$depot_code[$i]."'";
		$rsbrachname=mysql_query($sqlbrachname);
		$rowbrachname=mysql_fetch_array($rsbrachname);
		$branch_name=$rowbrachname['branch_name'];
		$branch_code=$rowbrachname['branch_code'];
		//$state_name=$rowbrachname['branch_state'];
		$is_plant=$rowbrachname['is_plant'];
		$plant_name=$rowbrachname['plant_name'];
		
		$sqlstate="SELECT state FROM state_master WHERE dns_state_code='".$state_code."'";
		$rsstate=mysql_query($sqlstate);
		$rowstate=mysql_fetch_array($rsstate);
		$state_name=$rowstate['state'];
		
		$sqlbroker="SELECT broker_name,broker_id,brokerage_cost FROM broker_master WHERE dns_broker_id='".$broker_code_name[$i]."'";
		$rsbroker=mysql_query($sqlbroker);
		$rowbroker=mysql_fetch_array($rsbroker);
		$broker_name=$rowbroker['broker_name'];
		$broker_id=$rowbroker['broker_id'];
		$brokerage_cost=$rowbroker['brokerage_cost'];
		
		$sqlproductdetails="SELECT PGM.product_group_name,PGM.product_group_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.UOM3,PM.conversion_factor,PM.conversion_factor_two,
		PM.prod_code,PM.branch_code,PM.dns_prod_code,PGM.formulation FROM product_master PM,product_group_master PGM 
		WHERE PM.product_group_code=PGM.product_group_code AND PM.dns_prod_code='".$material_code[$i]."' AND PM.acedns='Y' 
		AND PM.branch_code='".$branch_code."'";
		$rsproductdetails=mysql_query($sqlproductdetails);
		$rowproductdetails=mysql_fetch_array($rsproductdetails);
		$prod_code=$rowproductdetails['prod_code'];
		$prod_desc=$rowproductdetails['prod_desc'];
		$product_group_code=$rowproductdetails['product_group_code'];
		$product_group_name=$rowproductdetails['product_group_name'];
		$product_sub_group_code=$rowproductdetails['product_sub_group_code'];
		$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
		$product_brand_code=$rowproductdetails['product_brand_code'];
		$product_brand_name=$rowproductdetails['product_brand_name'];
		$UOM1=$rowproductdetails['UOM1'];
		$UOM2=$rowproductdetails['UOM2'];
		$UOM3=$rowproductdetails['UOM3'];
		$conversion_factor=$rowproductdetails['conversion_factor'];
		$conversion_factor_two=$rowproductdetails['conversion_factor_two'];
		$dns_prod_code=$rowproductdetails['dns_prod_code'];
		$formulation=$rowproductdetails['formulation'];
		//$branch_code=$rowproductdetails['branch_code'];
		$convert_qty_one=round(($material_qty_case[$i]*$conversion_factor),3);
		$convert_qty_two=round((($material_qty_case[$i]*$conversion_factor)/$conversion_factor_two),3);
		
		$curdate=date('d-m-Y');
		$previousday=date('d-m-Y', strtotime("-1 days,$curdate "));
		$beforepreviousday=date('d-m-Y', strtotime("-2 days,$curdate "));
		$daybeforepreviousday=date('d-m-Y', strtotime("-3 days,$curdate "));
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$second=$second+$i;
		$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
		$trans_id='FT'.$emp_code.$contentsdatetime;
		$saudadate=date('Y-m-d',strtotime(str_replace('.','-',$app_contract_date[$i])));
		$saudadate_diff_format=date('d-m-Y',strtotime(str_replace('.','-',$app_contract_date[$i])));
		$contract_valid_from=date('Y-m-d',strtotime($saudadate));
		//$saudatime=$hour.':'.$minute.':'.$second;
		//$saudatime='23:52:59';
		if($previousday==$saudadate_diff_format || $beforepreviousday==$saudadate_diff_format || $daybeforepreviousday==$saudadate_diff_format){
				$saudatime='23:52:59';
			}
			else
			{
				$saudatime=$hour.':'.$minute.':'.$second;
			}
		$valid_upto = date('Y-m-d',strtotime("+$sauda_valid_days days,$contract_valid_from"));
		$sauda_date_time=$contract_valid_from.' '.$saudatime;
		if($is_plant=='yes'){ 
			if(strtoupper($incoterms[$i])=='FOR'){
				$incoterms_val=strtoupper($incoterms[$i]).' '.'PLANT';
			}
			if(strtoupper($incoterms[$i])=='EXW'){
				$incoterms_val='EX'.' '.'PLANT';
			}
		}
		if($is_plant=='no'){ 
			if(strtoupper($incoterms[$i])=='FOR'){
				$incoterms_val=strtoupper($incoterms[$i]).' '.'DEPOT';
			}
			if(strtoupper($incoterms[$i])=='EXW'){
				$incoterms_val='EX'.' '.'DEPOT';
			}
		}
		
		if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='FOR PLANT')
		{
			if(strtoupper($incoterms_val)=='FOR DEPOT'){
				$sqlfreihgt="SELECT freight,capacity FROM branch_route_freight WHERE branch_code='".$branch_code."' 
						AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."'  AND state_code='".$state_code."'";
			}
			if(strtoupper($incoterms_val)=='FOR PLANT'){
				$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' 
						AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."' 
						AND capacity='".$loadability_ton."' AND state_code='".$state_code."'";
			}
			
			$rsfreight=mysql_query($sqlfreihgt);
			$rowfreight=mysql_fetch_array($rsfreight);
			$freight=$rowfreight['freight'];
			
			if(strtoupper($incoterms_val)=='FOR DEPOT'){
			$capacity=$rowfreight['capacity'];
			 $sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$capacity."' AND prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";
			}
			if(strtoupper($incoterms_val)=='FOR PLANT'){
				$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$loadability_ton."' AND prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";
			}
			/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
								ORDER BY datetime DESC LIMIT 0,1";	*/										
			$rsqtytruckload=mysql_query($sqlqtytruckload);
			$countqtytruckload=mysql_num_rows($rsqtytruckload);
			if($countqtytruckload >0)
			{
			  $rowqtytruckload=mysql_fetch_array($rsqtytruckload);
			  $qty_truck_load=$rowqtytruckload['qty_truck_load'];
			}
			else{
				if(strtoupper($incoterms_val)=='FOR DEPOT'){
				$capacity=$rowfreight['capacity'];
				$sqlqtytruckloadnext="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$capacity."' AND prod_code='".$material_code[$i]."' 
							 AND datetime >='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
				}
				else
				{
				$sqlqtytruckloadnext="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$loadability_ton."' AND prod_code='".$material_code[$i]."' 
							 AND datetime >='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
				}
				$rsqtytruckloadnext=mysql_query($sqlqtytruckloadnext);
				$countqtytruckloadnext=mysql_num_rows($rsqtytruckloadnext);
				if($countqtytruckloadnext >0)
				{
					$rowqtytruckloadnext=mysql_fetch_array($rsqtytruckloadnext);
			  		$qty_truck_load=$rowqtytruckloadnext['qty_truck_load'];
				}
				else
				{
					$qty_truck_load=0;
				}
			}
			$freight_charge=round(($freight/$qty_truck_load),2);
		}
		else $freight_charge=0;

		//echo strtoupper($incoterms_val);
		if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='EX DEPOT')
		{
			/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$material_code[$i]."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";*/
			$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$material_code[$i]."'
							AND branch_code='".$branch_code."' AND transport_mode='".$transport_mode."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";				
			$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
			$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
			$freight_cost=round($rowfreightcostprodwise['freight_cost'],2);
		}
		else
		{
			$freight_cost=0;
		}
		if($freight_cost=='')      $freight_cost=0;
		if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='EX DEPOT')
		{
		$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$material_code[$i]."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=round($rowdepotcostprodwise['depot_cost'],2);
		}
		else   $depot_cost=0;
		if($depot_cost=='')  		$depot_cost=0;
		
		$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$material_code[$i]."' 
									AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
		$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
		$detention_cost=$rowdetentioncostprodwise['detention_cost'];
		if($detention_cost=='')
		{
			$detention_cost=0;
		}
		
		$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$material_code[$i]."' 
	                              AND plant_name='".$plant_name."' AND transport_mode='".$transport_mode."' AND state_code='".$state_code."' 
								  AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
		$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
		$honeycomb_cost=$rowhoneycombcostprodwise['honeycomb_cost'];
		if($honeycomb_cost=='')
		{
			$honeycomb_cost=0;
		}

		$total_amount=($material_qty_case[$i]*($sale_rate[$i]+$freight_charge));
		if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='EX DEPOT' || strtoupper($incoterms_val)=='FOR PLANT')
		{
			if(strtoupper($incoterms_val)=='FOR DEPOT')  $FRC1=$freight_cost+$freight_charge+$depot_cost+$detention_cost;
			if(strtoupper($incoterms_val)=='EX DEPOT')   $FRC1=$freight_cost+$depot_cost+$detention_cost;
			if(strtoupper($incoterms_val)=='FOR PLANT')   $FRC1=$freight_charge+$detention_cost;
			
			$PR00=$sale_rate[$i]-$FRC1;
		}
		else if(strtoupper($incoterms_val)=='EX PLANT'){
			$PR00=$sale_rate[$i];
			$FRC1=0;
		}
	    $sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND
						plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rslooserate=mysql_query($sqllooserate);
		$rowlooserate=mysql_fetch_array($rslooserate);
		$loose_rate_ton=$rowlooserate['loose_rate_ton'];
		
		$sqlpackingprodwise="SELECT packing_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$material_code[$i]."'
							AND plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysql_query($sqlpackingprodwise);
		$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
		$packing_cost=round($rowpackingprodwise['packing_cost'],2);
		if($packing_cost=='')   $packing_cost=0;
		
		/*if(strtoupper($folderName)=='EMAMI')
		 {
			$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$material_code[$i]."'
						AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
			$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
			$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
			$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
			if($margin_cost=='')       $margin_cost=0;
		 }*/
			 $sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$material_code[$i]."' AND state_code='".$state_code."' 
									AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
			$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
			$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
			$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
			if($margin_cost=='')       $margin_cost=0;
		 //if($formulation=='yes'){
		   $material_cost=$sale_rate[$i]-$packing_cost-$margin_cost-$FRC1-$honeycomb_cost;
		/*}
		else{
		$material_cost=round(($loose_rate_ton/$conversion_factor_two),2);
		$material_cost=round(($material_cost*$conversion_factor),2);
	    }*/
		if($material_cost==0){
			$realization_per_case=0;
			$realization_per_MT=0;
		}
		else
		{
			$premium=0;
			$TD=0;
			$liquid_TD=0;
			
			$realization_per_case=$material_cost+$packing_cost+$margin_cost+$premium-$TD-$liquid_TD-$packing_cost-$brokerage_cost;
			$realization_per_case=round($realization_per_case,2);
			$realization_per_MT=round((($realization_per_case*$conversion_factor_two)/$conversion_factor),2);
		}
		//For Insert into the location table for new trans id regarding sauda
		$sqlinsertorlocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='0',
									longi='0',
									date='".$location_date."',
									updatetime='".$location_date."'"; 
		//For Insert into the Sauda Header table for new trans id
		$sqlinsertsaudaheader="INSERT INTO sauda_header SET sauda_no='".$trans_id."',
								  customer_code 	='".$customer_code."',
								  branch_code		='".$branch_code."',
								  TD				='',
								  broker_id			='".$broker_id."',
								  VAT          		='',
								  transaction_type  ='OB',
								  sauda_valid_from  ='".$contract_valid_from."',
								  sauda_type  ='RA',
								  d_instruction		='".addslashes($Remarks[$i])."'";   
		if(mysql_query($sqlinsertorlocation) && mysql_query($sqlinsertsaudaheader))
		{
			$flag=5;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $flag=0;
			$GLOBALS['msg'] = 'Data upload unsuccessful.';
			return;
		}
		$sqlinsertsaudadetails="INSERT INTO sauda_details SET sauda_no='".$trans_id."',
								sku_code 	='".$prod_code."',
								qty			='".$material_qty_case[$i]."',
								convert_qty_one	='".$convert_qty_one."',
								convert_qty_two	='".$convert_qty_two."',
								TD			='',
								premium		='',
								sale_rate	='".$sale_rate[$i]."',
								VAT			='',
								freight_charge ='".$freight_charge."',
								primary_freight ='".$freight_cost."',
								depot_cost 	='".$depot_cost."',
								honeycomb_cost 	=0,
								brokerage_cost 	=0,
								amount		='".round($total_amount,2)."',
								liquid_TD	='',
								mrp_code	=''";
		if(mysql_query($sqlinsertsaudadetails))
		{
			$flag=5;
			//For sauda limit updation in customer master
			if(strtoupper($vertical_value[$i])=='HBC:RASOI:BIB'){
				$sqlupdatecustomersaudalimit="UPDATE customer_sauda_limit SET pending_qty=(pending_qty+$convert_qty_two),
											download_time=CURRENT_TIMESTAMP() WHERE 
											customer_code='".$customer_code_name[$i]."'";
				mysql_query($sqlupdatecustomersaudalimit);
				$sqlupdatecustomer="UPDATE customer_master SET download_time=CURRENT_TIMESTAMP() WHERE 
									dns_customer_code='".$customer_code_name[$i]."'";
				mysql_query($sqlupdatecustomer);
			}
		}					
		else
		{
			mysql_query("ROLLBACK");
			$flag=0;
			$GLOBALS['msg'] = 'Data upload unsuccessful.';
			return;
		}
		$sqlselsuada="SELECT sauda_no,prod_code FROM sauda_download_log WHERE sauda_no='".$trans_id."' AND prod_code='".$material_code[$i]."'";
		$rsselsauda=mysql_query($sqlselsuada);
		$cntselsauda=mysql_num_rows($rsselsauda);
		if($cntselsauda ==0)
		{
	     $sqlinsertsaudadownloadlog="INSERT INTO sauda_download_log SET customer_code='".$customer_code_name[$i]."',
									customer_name='".addslashes($customer_name)."',
									route_name='".addslashes($route_code_name[$i])."',
									broker_id='".$broker_code_name[$i]."',
									broker_name='".addslashes($broker_name)."',sauda_no='".$trans_id."',
									sauda_date='".$saudadate."',
									sauda_time='".$saudatime."',
									contract_valid_from='".$contract_valid_from."',
									contract_valid_to='".$valid_upto."',
									prod_code='".$material_code[$i]."',
									qty='".$material_qty_case[$i]."',
									convert_qty_two='".$convert_qty_two."',
									UOM='".$UOM1."',
									product_group_code='".$product_group_code."',
									product_group_name='".addslashes($product_group_name)."',
									prod_desc='".$prod_desc."',branch_code='".$depot_code[$i]."',
									branch_name='".addslashes($branch_name)."',
									state='".addslashes($state_name)."',
									material_cost='".$material_cost."',
									primary_freight='".$freight_cost."',
									packing_cost='".$packing_cost."',
									honeycomb_cost='".$honeycomb_cost."',
									detention_charges='".$detention_cost."',
									brokerage_cost='".$brokerage_cost."',
									depot_cost='".$depot_cost."',
									margin_cost='".$margin_cost."',
									freight_charge='".$freight_charge."',
									TD='',
									liquid_TD='',
									premium='',
									PR00='".$PR00."',
									FRC1='".$FRC1."',
									amount='".round($total_amount,2)."',
									incoterms='".$incoterms_val."',
									emp_name='".addslashes($emp_name)."',
									payment_due_on='',
									cm_credit_limit='',
									remarks='".addslashes($Remarks[$i])."',
									vertical='".$vertical_value[$i]."',
									realization_per_case='".$realization_per_case."',
									realization_per_MT='".$realization_per_MT."',
									sale_rate='".$sale_rate[$i]."',packing_realization='".$packing_cost."',sauda_type  ='RA',download_time=CURRENT_TIMESTAMP()";
			if(mysql_query($sqlinsertsaudadownloadlog))
			{
				$flag=5;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo $flag=0;
				$GLOBALS['msg'] = 'Data upload unsuccessful.';
				return;
			}
		}
	}
	//exit();
	if($flag==5){
		mysql_query("COMMIT");
		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
	}
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
                    <td width="90%" align="center" class="ERR"><font size="+2"><u>Upload Sauda Data</u></font></td>
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
	//For RA Sauda
	if(similar_file_exists("../csv/$folderName/RA Sauda.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/RA Sauda.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		$error_array=array();
		$customer_code_array=array();
		$current_date=date('Y-m-d');
		$count=0;
		$tabledatacsv='';
		$tabledataval='';
		$tabledata='<form name="ra_sauda" method="post" action=""><input type="hidden" name="mode" value="submit_ra_sauda"><table border="1" style="border-collapse:collapse;" class="border" width="90%" cellpadding="4" align="center" >
					  <tr class="TDHEAD" align="center" id="head_main">
					  	<td colspan="14" class="TDHEAD" align="center">RA Sauda</td>
					  </tr>
					  <tr class="TDHEAD_SUB" align="center" id="head_main">
						<td>SI</td>
						<td>Customer code</td>
						<td>Route name</td>
						<td>Broker code</td>
						<td>App Contract Date</td>
						<td>Material Code</td>
						<td>Material Qty(case)</td>
						<td>Depot code</td>
						<td>Incoterms</td>
						<td>Employee Code</td>
						<td>Vertical</td>
						<td>Sale Rate</td>
						<td>Plant Name</td>
						<td>Remarks</td>
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
				$customer_code_name=trim($data[0]);
				$route_code_name=trim($data[1]);
				$broker_code_name=trim($data[2]);
				$app_contract_date=trim($data[3]);
				$material_code=trim($data[4]);
				$material_qty_case=trim($data[5]);
				$depot_code=trim($data[6]);
				$incoterms=trim($data[7]);
				$emp_code=trim($data[8]);
				$vertical_value=trim($data[9]);
				$sale_rate=trim($data[10]);
				$plant_name=trim($data[11]);
				$Remarks=trim($data[12]);
			
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
				$curdate=date('Y-m-d');
				$curdate_diff_format=date('d-m-Y');
				$previousday=date('d-m-Y', strtotime("-1 days,$curdate "));
				$beforepreviousday=date('d-m-Y', strtotime("-2 days,$curdate "));
				$daybeforepreviousday=date('d-m-Y', strtotime("-3 days,$curdate "));
				$saudadate=date('d-m-Y',strtotime(str_replace('.','-',$app_contract_date)));
				$saudadate_diff_format=date('Y-m-d',strtotime(str_replace('.','-',$app_contract_date)));
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
				//$saudatime=$hour.':'.$minute.':'.$second;
				if($previousday==$saudadate || $beforepreviousday==$saudadate || $daybeforepreviousday==$saudadate){
					$saudatime='23:52:59';
				}
				else
				{
					$saudatime=$hour.':'.$minute.':'.$second;
				}
				$sauda_date_time=$saudadate_diff_format.' '.$saudatime;
				
				if($previousday!=$saudadate && $curdate_diff_format!=$saudadate && $beforepreviousday!=$saudadate && $daybeforepreviousday!=$saudadate)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (App Contract Date)");
				}
				$sqlempcodechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$emp_code."'";
				$rsempcodechk=mysql_query($sqlempcodechk);
				$countempcodechk=mysql_num_rows($rsempcodechk);
				if($countempcodechk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Employee code)");
					$emp_code_db='';
				}
				else
				{
					$rowempcodechk=mysql_fetch_array($rsempcodechk);
					$emp_code_db=$rowempcodechk['emp_code'];
				}
				$sqlchkcustomer="SELECT customer_code,incoterms,state_code,loadability_ton,transport_mode FROM customer_master WHERE 
								dns_customer_code='".$customer_code_name."' AND acedns='Y' AND emp_code='".$emp_code_db."'";
				$rschkcustomer=mysql_query($sqlchkcustomer);
				$countchkcustomer=mysql_num_rows($rschkcustomer);
				
				$sqlsaudalimit="SELECT sauda_limit,pending_qty FROM customer_sauda_limit WHERE customer_code='".$customer_code_name."'";
				$rssaudalimit=mysql_query($sqlsaudalimit);
				$rowsaudalimit=mysql_fetch_array($rssaudalimit);
				$sauda_limit=$rowsaudalimit['sauda_limit'];
				$pending_qty=$rowsaudalimit['pending_qty'];
				${finalsaudalimit.$customer_code_name}=$sauda_limit-$pending_qty;
				
				if($countchkcustomer==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Inactive Customer code)");
				}
				$rowchkcustomer=mysql_fetch_array($rschkcustomer);
				$incoterms_val = $rowchkcustomer['incoterms'];
				$transport_mode = $rowchkcustomer['transport_mode'];
				$loadability_ton = $rowchkcustomer['loadability_ton'];
				$state_code=$rowchkcustomer['state_code'];
				
				$sqlroutenamechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_code_name)."'";
				$rsroutenamechk=mysql_query($sqlroutenamechk);
				$countroutenamechk=mysql_num_rows($rsroutenamechk);
				if($countroutenamechk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Route)");
				}
				$rowroutenamechk=mysql_fetch_array($rsroutenamechk);
				$route_code=$rowroutenamechk['route_code'];

				if($broker_code_name !='')
				{
					$sqlbroker="SELECT broker_code FROM broker_master WHERE dns_broker_code='".$broker_code_name."'";
					$rsbroker=mysql_query($sqlbroker);
					$countbroker=mysql_num_rows($rsbroker);
					if($countbroker==0)
					{
						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Broker code)");
					}
				}
				$sqlmaterialcodechk="SELECT prod_code,conversion_factor,conversion_factor_two 
									FROM product_master WHERE dns_prod_code='".addslashes($material_code)."'";
				$rsmaterialcodechk=mysql_query($sqlmaterialcodechk);
				$countmaterialcodechk=mysql_num_rows($rsmaterialcodechk);
				if($countmaterialcodechk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Material code)");
				}
				else{
					$rowproductdetails=mysql_fetch_array($rsmaterialcodechk);
					$conversion_factor=$rowproductdetails['conversion_factor'];
					$conversion_factor_two=$rowproductdetails['conversion_factor_two'];
					$convert_qty_two=round((($material_qty_case*$conversion_factor)/$conversion_factor_two),3);
				}
				${totalqty.$customer_code_name}=${totalqty.$customer_code_name}+$convert_qty_two;

				$sqldepotchk="SELECT branch_code,is_plant,plant_name FROM branch_master WHERE dns_branch_code='".$depot_code."'";
				$rsdepotchk=mysql_query($sqldepotchk);
				$countdepotchk=mysql_num_rows($rsdepotchk);
				if($countdepotchk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");
				}
				$rowdepotchk=mysql_fetch_array($rsdepotchk);
				$branch_code=$rowdepotchk['branch_code'];
				$is_plant=$rowdepotchk['is_plant'];
				$plant_name=$rowdepotchk['plant_name'];
				
				if($is_plant=='yes'){ 
					if(strtoupper($incoterms)=='FOR'){
						$incoterms_val=strtoupper($incoterms).' '.'PLANT';
					}
					if(strtoupper($incoterms)=='EXW'){
						$incoterms_val='EX'.' '.'PLANT';
					}
				}
				if($is_plant=='no'){ 
					if(strtoupper($incoterms)=='FOR'){
						$incoterms_val=strtoupper($incoterms).' '.'DEPOT';
					}
					if(strtoupper($incoterms)=='EXW'){
						$incoterms_val='EX'.' '.'DEPOT';
					}
				}
				$sqlverticalchk="SELECT DISTINCT prod_code FROM product_master WHERE vertical_value='".$vertical_value."'";
				$rsverticalchk=mysql_query($sqlverticalchk);
				$countverticalchk=mysql_num_rows($rsverticalchk);
				if($countverticalchk==0)
				{
					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical)");
				}
				if(!in_array($customer_code_name,$customer_code_array))
				{
					array_push($customer_code_array,$customer_code_name);
				}
				
				$sqlpackingprodwise="SELECT packing_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$material_code."'
							AND plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
				$rspackingprodwise=mysql_query($sqlpackingprodwise);
				$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
				$packing_cost=round($rowpackingprodwise['packing_cost'],2);
				if($packing_cost=='')   $packing_cost=0;
				
				 if($packing_cost==0){
					 array_push($error_array,"Packing cost Error @Row (".$csv_row_count.")");
				 }
				 /*if(strtoupper($folderName)=='EMAMI')
				 {
					$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$material_code."'
										AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
					$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
					$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
					$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
					if($margin_cost=='')       $margin_cost=0;
				 }*/
				 if(strtoupper($folderName)=='EMAMIT' || strtoupper($folderName)=='EMAMI')
				 {
					$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$material_code."' AND state_code='".$state_code."' 
											AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
					$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
					$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
					$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
					if($margin_cost=='')       $margin_cost=0;
				 }
				 /*if($margin_cost==0){
					 array_push($error_array,"Margin cost Error @Row (".$csv_row_count.")");
				 }*/
				if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='EX DEPOT')
				{
					$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$material_code."'
							AND branch_code='".$branch_code."' AND transport_mode='".$transport_mode."'  AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";					
					$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
					$freight_cost=round($rowfreightcostprodwise['freight_cost'],2);
					if($freight_cost=='')       $freight_cost=0;
					if($freight_cost==0){
					 array_push($error_array,"Primary freight Error @Row (".$csv_row_count.")");
					 }
				}
				if(strtoupper($incoterms_val)=='FOR DEPOT' || strtoupper($incoterms_val)=='FOR PLANT')
				{
					if(strtoupper($incoterms_val)=='FOR DEPOT'){
					$sqlfreihgt="SELECT freight,capacity FROM branch_route_freight WHERE branch_code='".$branch_code."' 
								AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."' 
								AND state_code='".$state_code."'";
					}
					if(strtoupper($incoterms_val)=='FOR PLANT')
					{
						$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' 
								AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."' 
								AND capacity='".$loadability_ton."' AND state_code='".$state_code."'";
					}
					$rsfreight=mysql_query($sqlfreihgt);
					$rowfreight=mysql_fetch_array($rsfreight);
					$freight=$rowfreight['freight'];
					if(strtoupper($incoterms_val)=='FOR DEPOT'){
					$capacity=$rowfreight['capacity'];
					$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
									 AND truck_load='".$capacity."' AND prod_code='".$material_code."' AND datetime <='".$sauda_date_time."' 
									ORDER BY datetime DESC LIMIT 0,1";
					}
					if(strtoupper($incoterms_val)=='FOR PLANT')
					{
						$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
									 AND truck_load='".$loadability_ton."' AND prod_code='".$material_code."' AND datetime <='".$sauda_date_time."' 
									ORDER BY datetime DESC LIMIT 0,1";
					}
					$rsqtytruckload=mysql_query($sqlqtytruckload);
					$countqtytruckload=mysql_num_rows($rsqtytruckload);
					if($countqtytruckload >0)
					{
					  $rowqtytruckload=mysql_fetch_array($rsqtytruckload);
					  $qty_truck_load=$rowqtytruckload['qty_truck_load'];
					}
					else{
						if(strtoupper($incoterms_val)=='FOR DEPOT'){
							$capacity=$rowfreight['capacity'];
							$sqlqtytruckloadnext="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 					 AND truck_load='".$capacity."' AND prod_code='".$material_code."' 
							 					AND datetime >='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
						}
						if(strtoupper($incoterms_val)=='FOR PLANT')
						{
							$sqlqtytruckloadnext="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 						AND truck_load='".$loadability_ton."' AND prod_code='".$material_code."' 
							 						AND datetime >='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
						}
						$rsqtytruckloadnext=mysql_query($sqlqtytruckloadnext);
						$countqtytruckloadnext=mysql_num_rows($rsqtytruckloadnext);
						if($countqtytruckloadnext >0)
						{
							$rowqtytruckloadnext=mysql_fetch_array($rsqtytruckloadnext);
							$qty_truck_load=$rowqtytruckloadnext['qty_truck_load'];
						}
						else
						{
							$qty_truck_load=0;
						}
					}
					//echo $qty_truck_load;
					 $freight_charge=round(($freight/$qty_truck_load),2);
					 if($freight_charge=='') $freight_charge=0;
					 if($freight_charge==0){
						 array_push($error_array,"Secodary Freight Error @Row (".$csv_row_count.")");
					 }
				}
				$tabledatacsv.="<input type=\"hidden\" name=\"customer_code_name[]\" value=\"$customer_code_name\">
								<input type=\"hidden\" name=\"route_code_name[]\" value=\"$route_code_name\">
								<input type=\"hidden\" name=\"broker_code_name[]\" value=\"$broker_code_name\">
								<input type=\"hidden\" name=\"app_contract_date[]\" value=\"$app_contract_date\">
								<input type=\"hidden\" name=\"material_code[]\" value=\"$material_code\">
								<input type=\"hidden\" name=\"material_qty_case[]\" value=\"$material_qty_case\">
								<input type=\"hidden\" name=\"depot_code[]\" value=\"$depot_code\">
								<input type=\"hidden\" name=\"incoterms[]\" value=\"$incoterms\">
								<input type=\"hidden\" name=\"emp_code[]\" value=\"$emp_code\">
								<input type=\"hidden\" name=\"vertical_value[]\" value=\"$vertical_value\">
								<input type=\"hidden\" name=\"sale_rate[]\" value=\"$sale_rate\">
								<input type=\"hidden\" name=\"plant_name[]\" value=\"$plant_name\">
								<input type=\"hidden\" name=\"Remarks[]\" value=\"$Remarks\">
								<tr id=\"tab\">
										<td>".$count."</td>
										<td>".$customer_code_name."</td>
										<td>".$route_code_name."</td>
										<td>".$broker_code_name."</td>
										<td>".$app_contract_date."</td>
										<td>".$material_code."</td>
										<td>".$material_qty_case."</td>
										<td >".$depot_code."</td>
										<td>".$incoterms."</td>
										<td>".$emp_code."</td>
										<td>".$vertical_value."</td>
										<td align=\"right\">".number_format($sale_rate,2)."</td>
										<td>".$plant_name."</td>
										<td>".$Remarks."</td>
								</tr>";
			   }
			   $count++;
			 $rec_count++;
			}
			foreach($customer_code_array as $customer_code_val)
			{
				if(${finalsaudalimit.$customer_code_val} < ${totalqty.$customer_code_val})
				{
					array_push($error_array,"Error , Exceede customer sauda limit  for customer code $customer_code_val");
				}
			}
			if(count($error_array) >0){
			    echo "<tr> 
					<td width=\"90%\" align=\"center\"  colspan=\"14\"><font size=\"+2\"><u>RA Sauda</u></font></td></tr><br />";
			   foreach($error_array as $error_val)
			   {
				   echo "<tr> 
					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"14\"><font size=\"+1\">".$error_val."</font></td></tr>";
			   }
		   }
		   else
		   {
		    echo $tabledata.=$tabledatacsv."<tr><td colspan='8' align='right'><input type='submit' name='submit10' value='Final Upload' /></td><td colspan='6' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminCsvReadSaudaUpload.php'\"/></td></tr></table></form>";
			die;
			$successval=1;
		   }
	}
	/*else
	{
		echo $successval="Naming convention for Depot route freight.csv is wrong.";
		exit();
	}*/
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
					$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';
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