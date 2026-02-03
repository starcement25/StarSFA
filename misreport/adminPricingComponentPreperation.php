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

		//exit();

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

			mysqli_query($link,$sqlinsertdeptcost);

			/*$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 

											dns_prod_code='".$distinct_dnsprod_code[$i]."'";

			$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

			$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);

			if($countproductgroupcode==0)

			{

				$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

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

				$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	

				$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);

			}

			else

			{

				$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	

										product_group_code='".$product_group_code."' AND 	SUBSTRING(datetime,1,10)='".$current_date."'";

				$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	

				$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					

			}

			if($cntchkpricegeneration > 0)

			{

				generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);

			}*/

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

			mysqli_query($link,$sqlinsertdeptcostlog);

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	//print_r($_POST);

	if($_REQUEST['mode']=='submit_opening_bargain')
	{
	  $customer_code=$_POST['customer_code'];
	  $dns_customer_code=$_POST['dns_customer_code'];
	  $branch_code=$_POST['branch_code'];
	  $prod_code=$_POST['prod_code'];
	  $dns_prod_code=$_POST['dns_prod_code'];
	  $bargain_no=$_POST['bargain_no'];
	  $bargain_date=$_POST['bargain_date'];
	  $bargain_qty=$_POST['bargain_qty'];
	  $bargain_rate=$_POST['bargain_rate'];
	  $pending_bargain_qty=$_POST['pending_bargain_qty'];
	  $unit=$_POST['unit'];
	  $mapped_prod_array=array();
	  $mapped_prod_bargain_array=array();
	  $dns_sauda_no_array=array();
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  
	  /*$sqldellocation="DELETE FROM location WHERE SUBSTRING(trans_id,1,2) IN ('FT','DO')";
	  mysqli_query($link,$sqldellocation);
	  $sqldelsaudaheader="DELETE FROM sauda_header";
	  mysqli_query($link,$sqldelsaudaheader);
	  $sqldelsaudadetails="DELETE FROM sauda_details";
	  mysqli_query($link,$sqldelsaudadetails);
	  $sqldelDOmaster="DELETE FROM DO_master";
	  mysqli_query($link,$sqldelDOmaster);
	  $sqldelDOtrans="DELETE FROM DO_transaction";
	  mysqli_query($link,$sqldelDOtrans);
	  $sqlupdatependingqty="UPDATE customer_sauda_limit SET pending_qty='0'";
	  mysqli_query($link,$sqlupdatependingqty);*/
	  
	  for($k=0;$k<count($customer_code);$k++)
		{

	  $sqlparentsku="SELECT PUCM.mapped_prod_code FROM product_master PM,product_unit_coversion_matrix PUCM 
						WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' AND PM.prod_code='".$prod_code[$k]."'";

	  $rsparentsku=mysqli_query($link,$sqlparentsku);

	  $rowparentsku=mysqli_fetch_assoc($rsparentsku);

	  $mapped_prod_code=$rowparentsku['mapped_prod_code'];

	 

	 if($dns_prod_code[$k]== $mapped_prod_code)
	 {

		$sqlselcapacity="SELECT transport_mode,loadability_ton,route_code,incoterms FROM customer_master WHERE customer_code='".$customer_code[$k]."'";

		$rsselcapacity=mysqli_query($link,$sqlselcapacity);

		$rowselcapacity=mysqli_fetch_assoc($rsselcapacity);

		$transport_mode=$rowselcapacity['transport_mode'];

		$loadability_ton=$rowselcapacity['loadability_ton'];

		$incoterms=$rowselcapacity['incoterms'];

		$route_code=$rowselcapacity['route_code'];

		$sqlprodconversion="SELECT UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two,UOM4 FROM product_master WHERE prod_code='".$prod_code[$k]."'";

		 $rsprodconversion=mysqli_query($link,$sqlprodconversion);

		 $rowprodconversion=mysqli_fetch_assoc($rsprodconversion);

		 $conversion_factor=$rowprodconversion['conversion_factor'];

		 $conversion_factor_two=$rowprodconversion['conversion_factor_two'];

		 $UOM4parent=$rowprodconversion['UOM4'];

		 if(strtoupper($unit[$k])=='MT')
			{

				$convert_qty_MT=$pending_bargain_qty[$k];

			}

			else

			{

				$convert_qty_MT=round(($pending_bargain_qty[$k]*$conversion_factor_two),3);

			}	

		 /*if($incoterms=='FOR PLANT')

			{

			$sqlbranchroutefreight="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code[$k]."' 

									AND route_code='".$route_code."' AND transport_mode='".$transport_mode."' 

									AND capacity='".$loadability_ton."' AND acedns='Y' ORDER BY download_time DESC LIMIT 0,1";

			$rsbranchroutefreight=mysqli_query($link,$sqlbranchroutefreight);

			$rowbranchroutefreight=mysqli_fetch_assoc($rsbranchroutefreight);

			$freight=$rowbranchroutefreight['freight'];

			}

			else

			{

				$freight=0;

			}*/

			$sqlselchildprods="SELECT PM.prod_code,PM.dns_prod_code,PM.pack_size,PM.UOM5,PM.UOM4,PUCM.add_subtract_val 

						FROM product_master PM,product_unit_coversion_matrix PUCM 

						WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' AND PM.acedns='Y' AND PUCM.mapped_prod_code='".$mapped_prod_code."'";

			$rschildprods=mysqli_query($link,$sqlselchildprods);
			$second=$second+1;
			if($second >59)
			{
				$second='10';
				$minute=$minute+1;
			}
			if($minute > 59)
			{
				$second='10';
				$minute='10';
				$hour=$hour+1;
			}
			$bargaindateformat=substr($bargain_date[$k],6,4).substr($bargain_date[$k],3,2).substr($bargain_date[$k],0,2);
			$dns_sauda_no=$bargain_no[$k].'/'.str_replace('-','',$bargain_date[$k]);	 
			
			if(!in_array($dns_sauda_no,$dns_sauda_no_array))
			{
				if(strlen($second)==1)
				{
					$second='0'.$second;
				}
				$sauda_no='FTE0010'.$bargaindateformat.$hour.$minute.$second;
				array_push($dns_sauda_no_array,$dns_sauda_no);
				${saudano.$dns_sauda_no}=$sauda_no;
			}
			else
			{
				$sauda_no=${saudano.$dns_sauda_no};
			}
			while($rowchildprods = mysqli_fetch_assoc($rschildprods))
			{

				//echo $incoterms;

				/*if($incoterms=='FOR PLANT')

				{

				$sqlloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 

									AND truck_load='".$loadability_ton."' AND prod_code='".$rowchildprods['dns_prod_code']."' ORDER BY datetime DESC LIMIT 0,1";

				$rsloadistribution=mysqli_query($link,$sqlloaddistribution);

				$rowloaddistribution=mysqli_fetch_assoc($rsloadistribution);

				$qty_truck_load=$rowloaddistribution['qty_truck_load'];

				$freight_charge=round(($freight/$qty_truck_load),2);

				}

				else

				{

					$freight_charge=0;

				}*/
			$freight_charge=0;	
			
			 if($rowchildprods['dns_prod_code']==$mapped_prod_code)
			 {

				 $qty=$pending_bargain_qty[$k];

				 $sale_rate=$bargain_rate[$k];

			 }

			 else

			 {

				 $qty=0;

				 if($rowchildprods['pack_size']=='BP')

					{

						$sale_rate=$bargain_rate[$k]+$rowchildprods['add_subtract_val'];

						$sale_rate=round($sale_rate,0);

					}

					if($rowchildprods['pack_size']=='CP')

					{

						$sale_rate=((($bargain_rate[$k]/$UOM4parent)*$rowchildprods['UOM5'])+$rowchildprods['add_subtract_val'])*$rowchildprods['UOM4'];

						$sale_rate=round($sale_rate,1);

					}

			 }
			 $sqlinsertDomaster="INSERT INTO DO_master SET sauda_no='".$sauda_no."',

									customer_code='".$customer_code[$k]."',

									dns_sauda_no='".$dns_sauda_no."',

									branch_code='".$branch_code[$k]."',

									sku_code='".$rowchildprods['prod_code']."',

									mapped_sku_code='".$mapped_prod_code."',

									qty='".$qty."',

									sale_rate='".$sale_rate."',

									freight_charge='".$freight_charge."',

									amount='0',

									incoterms='".$incoterms."',

									status='no',

									is_approved='yes',

									is_approved_date_time=CURRENT_TIMESTAMP(),

									is_approved_by='OpeningBargain',

									download_time=CURRENT_TIMESTAMP()";

			mysqli_query($link,$sqlinsertDomaster);
			$prev_bargain_no=$dns_sauda_no;
	   }

	 }

	/* else

	 {

		 ${qty.$bargain_no[$k].$mapped_prod_code}=${qty.$bargain_no[$k].$mapped_prod_code}+$pending_bargain_qty[$k];

		 

		 $sqlprodconversion="SELECT UOM1,UOM2,UOM3,conversion_factor,conversion_factor_two FROM product_master WHERE prod_code='".$prod_code[$k]."'";

		 $rsprodconversion=mysqli_query($link,$sqlprodconversion);

		 $rowprodconversion=mysqli_fetch_assoc($rsprodconversion);

		 $conversion_factor=$rowprodconversion['conversion_factor'];

		 $conversion_factor_two=$rowprodconversion['conversion_factor_two'];

		 if(strtoupper($unit[$k])=='MT')

			{

				$convert_qty_MT=$pending_bargain_qty[$k];

			}

			else

			{

				$convert_qty_MT=round(($pending_bargain_qty[$k]*$conversion_factor_two),3);

			}

			$mapped_prod_bargain_string=$bargain_no[$k].'#'.$mapped_prod_code;	

		 

		 $sauda_no='FT'.$bargain_no[$k];

		 $sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$mapped_prod_code."'";

		 $rsprodcode=mysqli_query($link,$sqlprodcode);

		 $rowprodcode=mysqli_fetch_assoc($rsprodcode);

		 $mapped_prod=$rowprodcode['prod_code'];

		 

		 $sqlinsertDomasterchild="INSERT INTO DO_master SET sauda_no='".$sauda_no."',

								customer_code='".$customer_code[$k]."',

								dns_sauda_no='".$bargain_no[$k]."',

								branch_code='".$branch_code[$k]."',

								sku_code='".$prod_code[$k]."',

								mapped_sku_code='".$mapped_prod_code."',

								qty='0',

								sale_rate='".$bargain_rate[$k]."',

								freight_charge='',

								amount='0',

								incoterms='EX PLANT',

								status='no',

								is_approved='yes',

								is_approved_date_time=CURRENT_TIMESTAMP(),

								is_approved_by='OpeningBargain',

								download_time=CURRENT_TIMESTAMP()";

		mysqli_query($link,$sqlinsertDomasterchild);

		${customercode.$bargain_no[$k].$mapped_prod_code}=$customer_code[$k];

		${branchcode.$bargain_no[$k].$mapped_prod_code}=$branch_code[$k];

		${skucode.$bargain_no[$k].$mapped_prod_code}=$mapped_prod;

		if(!in_array($mapped_prod_bargain_string,$mapped_prod_bargain_array))

		{

			array_push($mapped_prod_bargain_array,$mapped_prod_bargain_string);

		}
	 }*/

	   $sqlupdatelimit="UPDATE customer_sauda_limit SET 

						pending_qty=(pending_qty+$convert_qty_MT),download_time=CURRENT_TIMESTAMP()  WHERE customer_code='".$dns_customer_code[$k]."'";

		mysqli_query($link,$sqlupdatelimit);	

		$sqlupdatecustomerdatetime="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() 

									WHERE customer_code='".$customer_code[$k]."'";				

		mysqli_query($link,$sqlupdatecustomerdatetime);

		//exit();

	}
	/*$sqlupdateUOM="UPDATE `DO_master` set UOM=(SELECT UOM1 FROM product_master WHERE prod_code=DO_master.sku_code)";
	mysqli_query($link,$sqlupdateUOM);
	$sqlupdateqtyMTcase="UPDATE `DO_master` set `qty_MT`=ROUND((`qty`*(SELECT conversion_factor_two FROM product_master WHERE prod_code=DO_master.sku_code)),3) WHERE UOM='Case' AND qty > 0";
	mysqli_query($link,$sqlupdateqtyMTcase);
	$sqlupdateqtyMT="UPDATE `DO_master` set `qty_MT`=ROUND(`qty`,3) WHERE UOM='Loose' AND qty > 0";
	mysqli_query($link,$sqlupdateqtyMT);
	$sqlupdatetablestructure="UPDATE table_structure_updation SET is_update='1'";
	mysqli_query($link,$sqlupdatetablestructure);
	*/
	
		/*foreach($mapped_prod_bargain_array as $mapped_prod_bargain_val)

		{

			$mapped_prod_bargain_val_parts=explode('#',$mapped_prod_bargain_val);

			$sauda_no='FT'.$mapped_prod_bargain_val_parts[0];

			$sqlinsertDomasterparent="INSERT INTO DO_master SET sauda_no='".$sauda_no."',

									customer_code='".${customercode.$mapped_prod_bargain_val_parts[0].$mapped_prod_bargain_val_parts[1]}."',

									dns_sauda_no='".$mapped_prod_bargain_val_parts[0]."',

									branch_code='".${branchcode.$mapped_prod_bargain_val_parts[0].$mapped_prod_bargain_val_parts[1]}."',

									sku_code='".${skucode.$mapped_prod_bargain_val_parts[0].$mapped_prod_bargain_val_parts[1]}."',

									mapped_sku_code='".$mapped_prod_bargain_val_parts[1]."',

									qty='".${qty.$mapped_prod_bargain_val_parts[0].$mapped_prod_bargain_val_parts[1]}."',

									sale_rate='0',

									freight_charge='',

									amount='0',

									incoterms='EX PLANT',

									status='no',

									is_approved='yes',

									is_approved_date_time=CURRENT_TIMESTAMP(),

									is_approved_by='OpeningBargain',

									download_time=CURRENT_TIMESTAMP()";

			mysqli_query($link,$sqlinsertDomasterparent);

		}*/

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=="submit_margin_rasoi")

	{

		$current_date=date('Y-m-d');

		$distinct_dnsprod_code=$_POST['dns_prod_code_array'];

		$margin_cost_case_prodwise=$_POST['margin_cost_case_array'];

		$plant_name=$_POST['plant_name_array'];

		

							

		for($i=0;$i<count($distinct_dnsprod_code);$i++)

		{								 

			$sqlinsertmargincost="INSERT INTO margin_cost 

								SET dns_prod_code='".$distinct_dnsprod_code[$i]."',

								margin_cost='".$margin_cost_case_prodwise[$i]."',

								plant_name='".$plant_name_array[$i]."',

								ip_address='".$_SERVER['REMOTE_ADDR']."',

								datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlinsertmargincost);

	    }

	  /*for($k=0;$k<count($product_group_name);$k++)

		{

	  		$sqlinsertmargincostlog="INSERT INTO margin_cost_log

								SET margin_cost='".$margin_cost_case_prodwise[$k]."',

								pack_size='".$pack_size[$k]."',

								oil_group='".$product_group_name[$k]."',

								user_id='".$_SESSION['admin_login']."',

								ip_address='".$_SERVER['REMOTE_ADDR']."',

								operation_type='UPLOAD',

								datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlinsertmargincostlog);

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

			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

			$countselectdistinctdnsprod=mysqli_num_rows($rsselectdistinctdnsprod);

			//if($countselectdistinctdnsprod > 0){

			$rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod);

			$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];

			${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];

			${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];

			${product_group_code.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['product_group_code'];

			

			/*$sqlformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";

			$rsformulation=mysqli_query($link,$sqlformulation);

			$rowformulation=mysqli_fetch_assoc($rsformulation);

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

			mysqli_query($link,$sqlinserthoneycombcost);

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

		mysqli_query($link,$sqlinserthoneycombcostlog);	

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

			mysqli_query($link,$sqlinsertdetentioncost);

			$sqlchkformulation="SELECT formulation FROM product_group_master WHERE product_group_code='".$product_group_code[$i]."'";

			$rschkformulation=mysqli_query($link,$sqlchkformulation);

			$rowchkformulation=mysqli_fetch_assoc($rschkformulation);

			$is_formulation=$rowchkformulation['formulation'];



		/*if($is_formulation=='yes')

		{

			$sqlchkpricegeneration="SELECT oils_rate FROM pricing_detials_formulation WHERE plant_name='".$distinct_plant_name[$i]."' AND 	

									product_group_code='".$product_group_code[$i]."' 

									AND SUBSTRING(datetime,1,10)='".$current_date."'";

			$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	

			$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);

		}

		else

		{

			$sqlchkpricegeneration="SELECT loose_rate_ton FROM pricing_detials WHERE plant_name='".$distinct_plant_name[$i]."' AND 	

									product_group_code='".$product_group_code[$i]."' 

									AND SUBSTRING(datetime,1,10)='".$current_date."'";

			$rschkpricegeneration=mysqli_query($link,$sqlchkpricegeneration);	

			$cntchkpricegeneration=mysqli_num_rows($rschkpricegeneration);					

		}

		if($cntchkpricegeneration > 0)

		{

			generate_price_details($distinct_dnsprod_code[$i],$distinct_branch_code[$i]);

		}*/

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

			mysqli_query($link,$sqlinsertdetentioncostlog);

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

			$rsdistinctpacktype=mysqli_query($link,$sqldistinctpacktype);

			while($rowdistinctpacktype=mysqli_fetch_assoc($rsdistinctpacktype))

			{

				//$distinct_pack_type=$distinct_pack_type."'".$rowdistinctpacktype['pack_type']."'".",";

			   //$distinct_pack_type=substr($distinct_pack_type,0,-1);

			$product_group_code=$rowdistinctpacktype['product_group_code'];

			$distinct_pack_type=$rowdistinctpacktype['pack_type'];*/

			

			/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE branch_code='".$distinct_branch_code[$i]."' AND prod_desc 

										NOT LIKE '%LUP%' AND acedns='Y' AND black_list='N' AND vertical_value='".$vertical_value[$i]."' 

										AND product_group_code='".$product_group_code."' AND pack_type='".$distinct_pack_type."'";*/

			if(vertical_fields=='yes')

			{							

			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE  acedns='Y' AND black_list='N' 

										AND vertical_value='".$vertical_value[$i]."'";

			}

			else

			{

			$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE  acedns='Y' AND black_list='N'";

			}

			$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

			while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

			{

				$dns_prod_code=$rowselectdistinctdnsprod['dns_prod_code'];

		/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE plant_name='".$distinct_plant_name[$i]."' 

								AND transport_mode='".$transport_mode[$i]."' AND truck_load='".$truck_load[$i]."' 

								AND pack_type='".${pack_type.$dns_prod_code}."' AND product_group_code='".${product_group_code.$dns_prod_code}."'  

								ORDER BY datetime DESC LIMIT 0,1";*/

				$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode[$i]."' 

								AND truck_load='".$truck_load[$i]."' AND prod_code='".$dns_prod_code."'  ORDER BY datetime DESC LIMIT 0,1";				

				$rsqtytruckload=mysqli_query($link,$sqlqtytruckload);

				$countqtytruckload=mysqli_num_rows($rsqtytruckload);

				if($countqtytruckload >0)

				{

					$rowqtytruckload=mysqli_fetch_assoc($rsqtytruckload);

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

			   mysqli_query($link,$sqlinsertfreightcost);

			   

					//	generate_price_details($dns_prod_code,$distinct_branch_code[$i]);

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

			mysqli_query($link,$sqlinsertbasicfreight);							  

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

			mysqli_query($link,$sqlinsertloaddistribution);	

			//For primary freight auto update

			$sqlhirecost="SELECT * FROM (SELECT FC.dns_prod_code,FC.branch_code,FC.freight_cost, FC.hire_cost,FC.transport_mode,FC.truck_load FROM freight_cost FC WHERE FC.dns_prod_code='".$dns_prod_code[$i]."' AND FC.vertical_value='".$vertical_value[$i]."' AND FC.transport_mode='".$transport_mode[$i]."' AND FC.truck_load='".$load_capacity[$i]."' ORDER BY FC.datetime DESC) AS SAT GROUP BY 1,2";

			$rshirecost=mysqli_query($link,$sqlhirecost);

			$cnthirecost=mysqli_num_rows($rshirecost);

			if($cnthirecost > 0)

			{

			 while($rowshirecost=mysqli_fetch_assoc($rshirecost))

			 {

				$branch_code_load_dist=$rowshirecost['branch_code'];

				$hire_cost_load_dist=$rowshirecost['hire_cost'];

				if($qty_truck_load[$i] >0)

				{

					$freight_cost_load_dist=$hire_cost_load_dist/$qty_truck_load[$i];

				}

				else

				{

					$freight_cost_load_dist=0;

				}

				$sqlinsertfreightcostloaddist="INSERT INTO freight_cost SET 

										freight_cost='".$freight_cost_load_dist."',

										hire_cost='".$hire_cost_load_dist."',

										transport_mode='".$transport_mode[$i]."',

										truck_load='".$load_capacity[$i]."',

										dns_prod_code='".$dns_prod_code[$i]."',

										branch_code='".$branch_code_load_dist."',

										vertical_value='".$vertical_value[$i]."',

										ip_address='".$_SERVER['REMOTE_ADDR']."',

										user_id='".$_SESSION['admin_login']."',

										datetime=CURRENT_TIMESTAMP()";

			   mysqli_query($link,$sqlinsertfreightcostloaddist);

			 }

		 }

	  }

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_packing'){

		$plant_name=$_POST['plant_name'];

		$dns_prod_code=$_POST['dns_prod_code'];

		$packing_cost=$_POST['packing_cost'];

		$labour_cost=$_POST['labour_cost'];

		$extra_cost=$_POST['extra_cost'];

		

		for($i=0;$i<count($dns_prod_code);$i++)

		{				

			$sqlpacking  = "insert into packing_master SET ";

			$sqlpacking .= "  dns_prod_code='".mysqli_real_escape_string($dns_prod_code[$i])."'";

			$sqlpacking .= " , packing_cost='".mysqli_real_escape_string($packing_cost[$i])."'";

			$sqlpacking .= " , labour_cost='".mysqli_real_escape_string($labour_cost[$i])."'";

			$sqlpacking .= " , plant_name='".mysqli_real_escape_string($plant_name[$i])."'";

			$sqlpacking .= " , extra_cost='".mysqli_real_escape_string($extra_cost[$i])."'";

			$sqlpacking .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpacking .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpacking);

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_formulation'){

		$plant_name=$_POST['plant_name'];

		$dns_prod_code=$_POST['dns_prod_code'];

		$oil_type=$_POST['oil_type'];

		$formulation=$_POST['formulation'];

		$base_oil=$_POST['base_oil'];

		$percentile_calc=$_POST['percentile_calc'];

		$sqlupdateformulation="UPDATE loose_oilrate_formulation SET acedns='N'";

		$rsupdateformulation=mysqli_query($link,$sqlupdateformulation);

		

		for($i=0;$i<count($dns_prod_code);$i++)

		{				

		$sqloilformulation  = "insert into loose_oilrate_formulation SET ";

		$sqloilformulation .= "  plant_name='".mysqli_real_escape_string($plant_name[$i])."'";

		$sqloilformulation .= " , prod_code='".mysqli_real_escape_string($dns_prod_code[$i])."'";

		$sqloilformulation .= " , oils='".mysqli_real_escape_string($oil_type[$i])."'";

		$sqloilformulation .= " , formulation='".mysqli_real_escape_string($formulation[$i])."'";

		$sqloilformulation .= " , base_oil='".mysqli_real_escape_string($base_oil[$i])."'";

		$sqloilformulation .= " , percentile_calc='".mysqli_real_escape_string($percentile_calc[$i])."'";

		$sqloilformulation .= " , user_id='".$_SESSION['admin_login']."'";

		$sqloilformulation .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

		$sqloilformulation .= " , datetime=CURRENT_TIMESTAMP";

		mysqli_query($link,$sqloilformulation) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in oilrate formulation.csv.Please check.");

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_process'){

		$oil_type=$_POST['oil_type'];

		$oil_category=$_POST['oil_category'];

		$process_cost=$_POST['process_cost'];

	    $plant_name=$_POST['plant_name'];

		

		for($i=0;$i<count($oil_type);$i++)

		{				

			$sqlprocess  = "insert into process_cost SET ";

			$sqlprocess .= "  oil_type='".mysqli_real_escape_string($oil_type[$i])."'";

			$sqlprocess .= " , oil_category='".mysqli_real_escape_string($oil_category[$i])."'";

			$sqlprocess .= " , process_cost='".mysqli_real_escape_string($process_cost[$i])."'";

			$sqlprocess .= " , plant_name='".mysqli_real_escape_string($plant_name[$i])."'";

			$sqlprocess .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlprocess .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlprocess .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlprocess) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Process cost.csv.Please check.");

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_packing_material'){

		$material_name=$_POST['material_name'];

		$UOM=$_POST['UOM'];

	    $price=$_POST['price'];

		$weight=$_POST['weight'];

		$final_pack=$_POST['final_pack'];

		$height=$_POST['height'];

		$length=$_POST['length'];

		$breadth=$_POST['breadth'];

		$BOM_prod_array=array();

		$truck_load_array=array();

		

		//For load distribution truck load volume

		$sqltruckloadval="SELECT * FROM(SELECT load_qty,height,length,breadth FROM truck_load_master ORDER BY download_time DESC) AS SAT GROUP BY 1";

		$rstruckloadval=mysqli_query($link,$sqltruckloadval);

		while($rowtruckloadval=mysqli_fetch_assoc($rstruckloadval))

		{

			$truckloadvolume=$rowtruckloadval['height']*$rowtruckloadval['length']*$rowtruckloadval['breadth'];

			if(!in_array($rowtruckloadval['load_qty'],$truck_load_array))

			{

				array_push($truck_load_array,$rowtruckloadval['load_qty']);

			}

			${truckloadvolume.$rowtruckloadval['load_qty']}=$truckloadvolume;

		}

		//End of load distribution truck load volume

		for($i=0;$i<count($material_name);$i++)

		{				

			$sqlpackingmaterial  = "insert into packing_material_master SET ";

			$sqlpackingmaterial .= "  material_name='".mysqli_real_escape_string($material_name[$i])."'";

			$sqlpackingmaterial .= " , UOM='".mysqli_real_escape_string($UOM[$i])."'";

			$sqlpackingmaterial .= " , price='".mysqli_real_escape_string($price[$i])."'";

			$sqlpackingmaterial .= " , weight='".mysqli_real_escape_string($weight[$i])."'";

			$sqlpackingmaterial .= " , final_pack='".mysqli_real_escape_string($final_pack[$i])."'";

			$sqlpackingmaterial .= " , height='".mysqli_real_escape_string($height[$i])."'";

			$sqlpackingmaterial .= " , length='".mysqli_real_escape_string($length[$i])."'";

			$sqlpackingmaterial .= " , breadth='".mysqli_real_escape_string($breadth[$i])."'";

			$sqlpackingmaterial .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpackingmaterial .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpackingmaterial .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpackingmaterial) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Packing Material Master.csv.Please check.");

			$sqlprodlist="SELECT prod_code FROM packing_BOM WHERE material_name='".addslashes($material_name[$i])."'";

			$rsprodlist=mysqli_query($link,$sqlprodlist);

			while($rowprodlist=mysqli_fetch_assoc($rsprodlist))

			{

				$prod_code=$rowprodlist['prod_code'];

				if(!in_array($prod_code,$BOM_prod_array))

				{

					array_push($BOM_prod_array,$prod_code);

				}

			}

			//For load distribution

			if(strtoupper($final_pack[$i])=='Y')

			{

				$material_volume=$height[$i]*$length[$i]*$breadth[$i];

				$sqlprodlistloaddist="SELECT * FROM(SELECT prod_code FROM packing_BOM WHERE material_name='".addslashes($material_name[$i])."' 

									ORDER BY datetime DESC) AS SAT GROUP BY 1";

				$rsprodlistloaddist=mysqli_query($link,$sqlprodlistloaddist);

				while($rowprodlistloaddist=mysqli_fetch_assoc($rsprodlistloaddist))

				{

					$prod_code_BOM=$rowprodlistloaddist['prod_code'];

					foreach($truck_load_array as $truck_load_val)

					{

						${loaddistribution.$prod_code_BOM.$truck_load_val}=round((${truckloadvolume.$truck_load_val}/$material_volume),2);

						$sqlinsertloaddist="INSERT INTO load_distribution SET 

											plant_name='BHIWADI',

											transport_mode='truck',

											truck_load='".$truck_load_val."',

											prod_code='".$prod_code_BOM."',

											qty_truck_load='".${loaddistribution.$prod_code_BOM.$truck_load_val}."',

											datetime=CURRENT_TIMESTAMP(),

											user_id='".$_SESSION['admin_login']."',

											ip_address='".$_SERVER['REMOTE_ADDR']."'";

					   mysqli_query($link,$sqlinsertloaddist);						

					}

				}

			}

			//End for load distribution

		}

		foreach($BOM_prod_array as $prod_code_val)

		{

			$sql_BOM= "SELECT* FROM(SELECT prod_code,material_name,usage_qty,multiple_single FROM packing_BOM WHERE 

						prod_code='".$prod_code_val."'  

			   		ORDER BY datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 1";

			$res_BOM = mysqli_query($link,$sql_BOM);

			while($row_BOM = mysqli_fetch_assoc($res_BOM)){

				$prod_code = $row_BOM['prod_code'];

				$material_name = $row_BOM['material_name'];

				$usage_qty = $row_BOM['usage_qty'];

				$multiple_single = $row_BOM['multiple_single'];

				

				$sqlprodUOM="SELECT UOM4 FROM product_master WHERE dns_prod_code='".$prod_code_val."'";

				$rsprodUOM=mysqli_query($link,$sqlprodUOM);

				$rowprodUOM=mysqli_fetch_assoc($rsprodUOM);

				$UOM4=$rowprodUOM['UOM4'];

				$sqlselpackmatprice="SELECT price,weight FROM packing_material_master WHERE material_name='".addslashes($material_name)."' 

							ORDER BY datetime DESC LIMIT 0,1";

				$resselpackmatprice = mysqli_query($link,$sqlselpackmatprice);

				$rowselpackmatprice = mysqli_fetch_assoc($resselpackmatprice);

				$price = $rowselpackmatprice['price'];

				

				if(strtoupper($multiple_single)=='M')

				{

					$weight = ($rowselpackmatprice['weight']*$UOM4);

					$packing_cost=($usage_qty*($price*$UOM4));

				}

				else

				{

					$weight = $rowselpackmatprice['weight'];

					$packing_cost=$usage_qty*$price;

				}

				${total_packing.$prod_code_val}=${total_packing.$prod_code_val}+$packing_cost;

				${total_weight.$prod_code_val}=${total_weight.$prod_code_val}+$weight;

			}

			$sqllatestlabourextra="SELECT labour_cost,extra_cost FROM packing_master WHERE 

								dns_prod_code='".mysqli_real_escape_string($prod_code_val)."' AND plant_name='Bhiwadi' ORDER BY datetime DESC LIMIT 0,1";

			$rslatestlabourextra=mysqli_query($link,$sqllatestlabourextra);

			$rowlatestlabourextra=mysqli_fetch_assoc($rslatestlabourextra);

			$latest_labour_cost=$rowlatestlabourextra['labour_cost'];

			$latest_extra_cost=$rowlatestlabourextra['extra_cost'];

			$packing_realization_percent=((((${total_packing.$prod_code_val}+$latest_labour_cost)*10)/100)+$latest_extra_cost);

			$packing_realization=round((${total_packing.$prod_code_val}+$latest_labour_cost+$packing_realization_percent),1);

								

			$sqlproddetails="SELECT prod_code,conversion_factor,conversion_factor_two FROM product_master 

							WHERE dns_prod_code='".$prod_code_val."'";

			$rsproddetails=mysqli_query($link,$sqlproddetails);

			$rowproddetails=mysqli_fetch_assoc($rsproddetails);

			$conversion_two=$rowproddetails['conversion_factor_two'];

			${gross_weight.$prod_code_val}=${total_weight.$prod_code_val}+($conversion_two*1000);

					

			$sqlpacking  = "INSERT into packing_master SET ";

			$sqlpacking .= "  dns_prod_code='".mysqli_real_escape_string($prod_code_val)."'";

			$sqlpacking .= " , packing_cost='".mysqli_real_escape_string(${total_packing.$prod_code_val})."'";

			$sqlpacking .= " , labour_cost='".mysqli_real_escape_string($latest_labour_cost)."'";

			$sqlpacking .= " , plant_name='Bhiwadi'";

			$sqlpacking .= " , extra_cost='".mysqli_real_escape_string($latest_extra_cost)."'";

			$sqlpacking .=" , packing_realization='".mysqli_real_escape_string($packing_realization)."'";

			$sqlpacking .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpacking .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpacking);

			

			$sqlupdateprodweight="UPDATE product_master SET gross_weight='".${gross_weight.$prod_code_val}."' WHERE dns_prod_code='".mysqli_real_escape_string($prod_code_val)."'";

			mysqli_query($link,$sqlupdateprodweight);

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_packing_bom'){

		$prod_code=$_POST['prod_code'];

		$material_name=$_POST['material_name'];

	    $usage_qty=$_POST['usage_qty'];

		$UOM=$_POST['UOM'];

		$multiple_single=$_POST['multiple_single'];

		$prod_code_distinct=array();

		for($i=0;$i<count($prod_code);$i++)

		{				

			$sqlpackingBOM  = "insert into packing_BOM SET ";

			$sqlpackingBOM .= "  material_name='".mysqli_real_escape_string($material_name[$i])."'";

			$sqlpackingBOM .= " , prod_code='".mysqli_real_escape_string($prod_code[$i])."'";

			$sqlpackingBOM .= " , usage_qty='".mysqli_real_escape_string($usage_qty[$i])."'";

			$sqlpackingBOM .= " , UOM='".mysqli_real_escape_string($UOM[$i])."'";

			$sqlpackingBOM .= " , multiple_single='".mysqli_real_escape_string($multiple_single[$i])."'";

			$sqlpackingBOM .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpackingBOM .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpackingBOM .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpackingBOM) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Packing BOM.csv.Please check.");

			

			$sqlprodUOM="SELECT UOM4 FROM product_master WHERE dns_prod_code='".$prod_code[$i]."'";

			$rsprodUOM=mysqli_query($link,$sqlprodUOM);

			$rowprodUOM=mysqli_fetch_assoc($rsprodUOM);

			$UOM4=$rowprodUOM['UOM4'];

			$sqlpackingmaterial="SELECT price,weight FROM packing_material_master WHERE material_name='".mysqli_real_escape_string($material_name[$i])."' 

								ORDER BY datetime DESC LIMIT 0,1";

			$rspackingmaterial=mysqli_query($link,$sqlpackingmaterial);

			$rowpackingmaterial=mysqli_fetch_assoc($rspackingmaterial);

			$price=$rowpackingmaterial['price'];

			$weight=$rowpackingmaterial['weight'];

			if(strtoupper($multiple_single[$i])=='M')

			{

				${packing_cost.$prod_code[$i]}=${packing_cost.$prod_code[$i]}+($usage_qty[$i]*($price*$UOM4));

				${total_weight.$prod_code[$i]}=${total_weight.$prod_code[$i]}+($weight*$UOM4);

			}

			else

			{

				${packing_cost.$prod_code[$i]}=${packing_cost.$prod_code[$i]}+($usage_qty[$i]*$price);

				${total_weight.$prod_code[$i]}=${total_weight.$prod_code[$i]}+$weight;

			}

			if(!in_array($prod_code[$i],$prod_code_distinct))

			{

				array_push($prod_code_distinct,$prod_code[$i]);

			}

		}

		foreach($prod_code_distinct as $prod_code_val)

		{

			$sqllatestlabourextra="SELECT labour_cost,extra_cost FROM packing_master WHERE 

								dns_prod_code='".mysqli_real_escape_string($prod_code_val)."' AND plant_name='Bhiwadi' ORDER BY datetime DESC LIMIT 0,1";

			$rslatestlabourextra=mysqli_query($link,$sqllatestlabourextra);

			$rowlatestlabourextra=mysqli_fetch_assoc($rslatestlabourextra);

			$latest_labour_cost=$rowlatestlabourextra['labour_cost'];

			$latest_extra_cost=$rowlatestlabourextra['extra_cost'];

			

			$sqlproddetails="SELECT prod_code,conversion_factor,conversion_factor_two,packing_realization FROM product_master 

							WHERE dns_prod_code='".$prod_code_val."'";

			$rsproddetails=mysqli_query($link,$sqlproddetails);

			$rowproddetails=mysqli_fetch_assoc($rsproddetails);

			$conversion_two=$rowproddetails['conversion_factor_two'];

			$packing_realization=$rowproddetails['packing_realization'];

			${gross_weight.$prod_code_val}=${total_weight.$prod_code_val}+($conversion_two*1000);



			//$packing_realization_percent=((((${packing_cost.$prod_code_val}+$latest_labour_cost)*10)/100)+$latest_extra_cost);

			$packing_realization_percent=((((${packing_cost.$prod_code_val}+$latest_labour_cost)*$packing_realization)/100)+$latest_extra_cost);

			$packing_realization=round((${packing_cost.$prod_code_val}+$latest_labour_cost+$packing_realization_percent),1);

			

			$sqlpacking  = "INSERT into packing_master SET ";

			$sqlpacking .= "  dns_prod_code='".mysqli_real_escape_string($prod_code_val)."'";

			$sqlpacking .= " , packing_cost='".mysqli_real_escape_string(${packing_cost.$prod_code_val})."'";

			$sqlpacking .= " , labour_cost='".mysqli_real_escape_string($latest_labour_cost)."'";

			$sqlpacking .= " , plant_name='Bhiwadi'";

			$sqlpacking .= " ,extra_cost='".mysqli_real_escape_string($latest_extra_cost)."'";

			$sqlpacking .=" , packing_realization='".mysqli_real_escape_string($packing_realization)."'";

			$sqlpacking .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpacking .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpacking);

			$sqlupdateprodweight="UPDATE product_master SET gross_weight='".${gross_weight.$prod_code_val}."' WHERE dns_prod_code='".mysqli_real_escape_string($prod_code_val)."'";

			mysqli_query($link,$sqlupdateprodweight);

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_labour_bom'){

		$prod_code=$_POST['prod_code'];

		$manufacturing=$_POST['manufacturing'];

	    $unloading_top=$_POST['unloading_top'];

		$unloading=$_POST['unloading'];

		$empty_shifting=$_POST['empty_shifting'];

		$filling=$_POST['filling'];

		$labelling=$_POST['labelling'];

		$filed_shifting=$_POST['filed_shifting'];

	    $loading=$_POST['loading'];

	    $marking=$_POST['marking'];

		$prod_code_distinct=array();

		for($i=0;$i<count($prod_code);$i++)

		{				

			$sqllabourBOM  = "insert into labour_BOM SET ";

			$sqllabourBOM .= "  prod_code='".mysqli_real_escape_string($prod_code[$i])."'";

			$sqllabourBOM .= " , manufacturing='".mysqli_real_escape_string($manufacturing[$i])."'";

			$sqllabourBOM .= " , unloading_top='".mysqli_real_escape_string($unloading_top[$i])."'";

			$sqllabourBOM .= " , unloading='".mysqli_real_escape_string($unloading[$i])."'";

			$sqllabourBOM .= " , empty_shifting='".mysqli_real_escape_string($empty_shifting[$i])."'";

			$sqllabourBOM .= " , filling='".mysqli_real_escape_string($filling[$i])."'";

			$sqllabourBOM .= " , labelling='".mysqli_real_escape_string($labelling[$i])."'";

			$sqllabourBOM .= " , filed_shifting='".mysqli_real_escape_string($filed_shifting[$i])."'";

			$sqllabourBOM .= " , loading='".mysqli_real_escape_string($loading[$i])."'";

			$sqllabourBOM .= " , marking='".mysqli_real_escape_string($marking[$i])."'";

			$sqllabourBOM .= " , user_id='".$_SESSION['admin_login']."'";

			$sqllabourBOM .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqllabourBOM .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqllabourBOM) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Labour BOM.csv.Please check.");

			

			${labour_cost.$prod_code[$i]}=$manufacturing[$i]+$unloading_top[$i]+$unloading[$i]+$empty_shifting[$i]+$filling[$i]+$labelling[$i]+$filed_shifting[$i]+$loading[$i]+$marking[$i];

			if(!in_array($prod_code[$i],$prod_code_distinct))

			{

				array_push($prod_code_distinct,$prod_code[$i]);

			}

		}

		foreach($prod_code_distinct as $prod_code_val)

		{

			$sqlproddetails="SELECT prod_code,conversion_factor,conversion_factor_two,packing_realization FROM product_master 

							WHERE dns_prod_code='".$prod_code_val."'";

			$rsproddetails=mysqli_query($link,$sqlproddetails);

			$rowproddetails=mysqli_fetch_assoc($rsproddetails);

			$packing_realization=$rowproddetails['packing_realization'];



			$sqllatestpackextra="SELECT packing_cost,extra_cost FROM packing_master WHERE 

								dns_prod_code='".mysqli_real_escape_string($prod_code_val)."' AND plant_name='Bhiwadi' ORDER BY datetime DESC LIMIT 0,1";

			$rslatestpackextra=mysqli_query($link,$sqllatestpackextra);

			$rowlatestpackextra=mysqli_fetch_assoc($rslatestpackextra);

			$latest_packing_cost=$rowlatestpackextra['packing_cost'];

			$latest_extra_cost=$rowlatestpackextra['extra_cost'];

			

			//$packing_realization_percent=(((($latest_packing_cost+${labour_cost.$prod_code_val})*10)/100)+$latest_extra_cost);

			$packing_realization_percent=(((($latest_packing_cost+${labour_cost.$prod_code_val})*$packing_realization)/100)+$latest_extra_cost);

			$packing_realization=round((${labour_cost.$prod_code_val}+$latest_packing_cost+$packing_realization_percent),1);

								

			$sqlpacking  = "INSERT into packing_master SET ";

			$sqlpacking .= "  dns_prod_code='".mysqli_real_escape_string($prod_code_val)."'";

			$sqlpacking .= " , packing_cost='".mysqli_real_escape_string($latest_packing_cost)."'";

			$sqlpacking .= " , labour_cost='".mysqli_real_escape_string(${labour_cost.$prod_code_val})."'";

			$sqlpacking .=" , packing_realization='".mysqli_real_escape_string($packing_realization)."'";

			$sqlpacking .= " , plant_name='Bhiwadi'";

			$sqlpacking .= " , extra_cost='".mysqli_real_escape_string($latest_extra_cost)."'";

			$sqlpacking .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlpacking .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlpacking .= " , datetime=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlpacking);

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_conversion'){

		$prod_code=$_POST['prod_code'];

		$mapped_prod_code=$_POST['mapped_prod_code'];

	    $is_flash=$_POST['is_flash'];

		$flash_name=$_POST['flash_name'];

		$add_subtract_val=$_POST['add_subtract_val'];

		

		$sqlupdateconversion="UPDATE product_unit_coversion_matrix SET acedns='N'";

		$rsupdateconversion=mysqli_query($link,$sqlupdateconversion);

		for($i=0;$i<count($prod_code);$i++)

		{

			$sqlconversion  = "insert into product_unit_coversion_matrix SET ";

			$sqlconversion .= "  	prod_code='".mysqli_real_escape_string($prod_code[$i])."'";

			$sqlconversion .= " , mapped_prod_code='".mysqli_real_escape_string($mapped_prod_code[$i])."'";

			$sqlconversion .= " , is_flash='".mysqli_real_escape_string($is_flash[$i])."'";

			$sqlconversion .= " , flash_name='".mysqli_real_escape_string($flash_name[$i])."'";

			$sqlconversion .= " , add_subtract_val='".mysqli_real_escape_string($add_subtract_val[$i])."'";

			$sqlconversion .= " , user_id='".$_SESSION['admin_login']."'";

			$sqlconversion .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqlconversion .= " , download_time=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqlconversion) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in conversion.csv.Please check.");



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

			if($route_code !='')

			{

			 //Logic for EMAMI										

		/*$sqlbranchdestinationfreight="SELECT branch_code FROM branch_route_freight WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 

						AND route_code='".$route_code[$i]."' AND vertical_value='".$vertical_value[$i]."'";

		$rsbranchdestinationfreight=mysqli_query($link,$sqlbranchdestinationfreight);

		$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);

		if($countbranchdestinationfreight>=1 )

			{

				$sqlisplant="SELECT is_plant FROM branch_master WHERE branch_code='".$distinct_branch_code[$i]."'";

				$rsisplant=mysqli_query($link,$sqlisplant);

				$rowisplant=mysqli_fetch_assoc($rsisplant);

				$is_plant=$rowisplant['is_plant'];

				

				if($is_plant=='yes')

				{

				    $sqlbranchdestinationfreightupd  = "update branch_route_freight ";

					$sqlbranchdestinationfreightupd .= " SET acedns='N'";

					$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 

														AND route_code='".$route_code[$i]."' AND transport_mode='".$transport_mode[$i]."' 

														AND capacity='".$capacity[$i]."' AND vertical_value='".$vertical_value[$i]."'";

					mysqli_query($link,$sqlbranchdestinationfreightupd);



				}

				else

				{

					$sqlbranchdestinationfreightupd  = "update branch_route_freight ";

					$sqlbranchdestinationfreightupd .= " SET acedns='N'";

					$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 

														AND route_code='".$route_code[$i]."' AND vertical_value='".$vertical_value[$i]."'";

					mysqli_query($link,$sqlbranchdestinationfreightupd);

				}

			}*/

			//End of logic for EMAMI

			 $sqlbranchdestinationfreightupd  = "update branch_route_freight ";

			$sqlbranchdestinationfreightupd .= " SET acedns='N'";

			$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($distinct_branch_code[$i])."' 

												AND route_code='".$route_code[$i]."' AND transport_mode='".$transport_mode[$i]."' 

												AND capacity='".$capacity[$i]."'";

			mysqli_query($link,$sqlbranchdestinationfreightupd);

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

			mysqli_query($link,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

							Internal DATA execution problem on depot freight table.PLease contact aceDNS admin.");				

			/*else

			{

				$sqlbranchdestinationfreightupd  = "update branch_route_freight ";

				$sqlbranchdestinationfreightupd .= " SET acedns='N'";

				$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' AND route_code='".$route_code."'";

				

				mysqli_query($link,$sqlbranchdestinationfreightupd);

			}*/

			}

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	if($_REQUEST['mode']=='submit_truck_load'){

		$load_qty=$_POST['load_qty_array'];

		$height=$_POST['height_array'];

		$length=$_POST['length_array'];

		$breadth=$_POST['breadth_array'];

		for($i=0;$i<count($load_qty);$i++)

		{

			$sqltruckload  = "insert into truck_load_master SET ";

			$sqltruckload .= "  load_qty='".mysqli_real_escape_string($load_qty[$i])."'";

			$sqltruckload .= " , height='".mysqli_real_escape_string($height[$i])."'";

			$sqltruckload .= " , length='".mysqli_real_escape_string($length[$i])."'";

			$sqltruckload .= " , breadth='".mysqli_real_escape_string($breadth[$i])."'";

			$sqltruckload .= " , user_id='".$_SESSION['admin_login']."'";

			$sqltruckload .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

			$sqltruckload .= " , download_time=CURRENT_TIMESTAMP";

			mysqli_query($link,$sqltruckload) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Truck load master.csv.Please check.");



		}

	 }

	 if($_REQUEST['mode']=='submit_base_oil'){

		$base_oil=$_POST['base_oil_array'];

		$mandatory=$_POST['mandatory_array'];

		$upper_limit=$_POST['upper_limit_array'];

		$lower_limit=$_POST['lower_limit_array'];

		$sqlupdatebaseoil="UPDATE base_oil_master SET acedns='N'";

		if(mysqli_query($link,$sqlupdatebaseoil)){

			for($i=0;$i<count($base_oil);$i++)

			{

				$sqlbaseoil  = "insert into base_oil_master SET ";

				$sqlbaseoil .= "  base_oil='".mysqli_real_escape_string($base_oil[$i])."'";

				$sqlbaseoil .= " , mandatory='".mysqli_real_escape_string($mandatory[$i])."'";

				$sqlbaseoil .= " , upper_limit='".mysqli_real_escape_string($upper_limit[$i])."'";

				$sqlbaseoil .= " , lower_limit='".mysqli_real_escape_string($lower_limit[$i])."'";

				$sqlbaseoil .= " , user_id='".$_SESSION['admin_login']."'";

				$sqlbaseoil .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

				$sqlbaseoil .= " , download_time=CURRENT_TIMESTAMP";

				mysqli_query($link,$sqlbaseoil) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Base Oil Master.csv.Please check.");

			}

		}

		$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	 }

	 if($_REQUEST['mode']=='submit_brokerage_cost'){

		$broker_code=$_POST['broker_code'];

		$broker_name=$_POST['broker_name'];

		$oil_category=$_POST['oil_category'];

		

		$pack_size=$_POST['pack_size'];

		$UOM=$_POST['UOM'];

		$brokerage_cost=$_POST['brokerage_cost'];

		print_r($_POST);

			for($i=0;$i<count($broker_code);$i++)

			{

				$oil_category_array=explode(',',$oil_category[$i]);

				foreach($oil_category_array as $oil_category_val)

				{

					if($oil_category_val!=''){

					$sqloilcategorycode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$oil_category_val."'";

					$rsoilcategorycode=mysqli_query($link,$sqloilcategorycode);

					$rowoilcategorycode=mysqli_fetch_assoc($rsoilcategorycode);

					$oil_category_code=$rowoilcategorycode['product_group_code'];

					$sqlbrokerage  = "insert into brokerage_cost SET ";

					$sqlbrokerage .= "  broker_id='".mysqli_real_escape_string($broker_code[$i])."'";

					$sqlbrokerage .= " , broker_name='".mysqli_real_escape_string($broker_name[$i])."'";

					$sqlbrokerage .= " , oil_category='".mysqli_real_escape_string($oil_category_code)."'";

					$sqlbrokerage .= " , pack_size='".mysqli_real_escape_string($pack_size[$i])."'";

					$sqlbrokerage .= " , UOM='".mysqli_real_escape_string($UOM[$i])."'";

					$sqlbrokerage .= " , brokerage_cost='".mysqli_real_escape_string($brokerage_cost[$i])."'";

					$sqlbrokerage .= " , user_id='".$_SESSION['admin_login']."'";

					$sqlbrokerage .= " , ip_address='".$_SERVER['REMOTE_ADDR']."'";

					$sqlbrokerage .= " , download_time=CURRENT_TIMESTAMP";

					mysqli_query($link,$sqlbrokerage) or die(mysqli_error().".Internal error occurrs @row $csv_row_count in Brokerage Cost.csv.Please check.");

					}

				}

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

					$rsplant=mysqli_query($link,$sqlplant);

					$rowplant=mysqli_fetch_assoc($rsplant);

					$countplant=mysqli_num_rows($rsplant);

					if($countplant==0)

					{

						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");

					}

					

					$sqlbranchcode="SELECT branch_code,plant_name FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

					$rsbranchcode=mysqli_query($link,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);

					$countdistinctbranchcode=mysqli_num_rows($rsbranchcode);

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

					

					$sqltransportmode="SELECT transport_mode FROM customer_master WHERE transport_mode='".$transport_mode."'";

					$rstransportmode=mysqli_query($link,$sqltransportmode);

					$counttransportmode=mysqli_num_rows($rstransportmode);

					if($counttransportmode==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");

					}

					$sqltruckload="SELECT loadability_ton FROM customer_master WHERE loadability_ton='".$truck_load."'";

					$rsttruckload=mysqli_query($link,$sqltruckload);

					$counttruckload=mysqli_num_rows($rsttruckload);

					if($counttruckload==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for 

					Truck load column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Truck load)");

					}

					/*$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

					if($countverticalvalue==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in primary freight.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

					}*/

					$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 

												AND truck_load='".$truck_load."' ";

					$rscheckloaddistribution=mysqli_query($link,$sqlcheckloaddistribution);

					$countcheckloaddistribution=mysqli_num_rows($rscheckloaddistribution);

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

echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_hirecost\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit5' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button5' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php.php'\"/></td></tr></table></form>";

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

				$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

				$countdistinctplant=mysqli_num_rows($rsdistinctplant);

				if($countdistinctplant==0)

				{

					/*echo "<tr> 

				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Plant name column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

					die;*/

					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");

				}*/

				$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";

				$rstransportmode=mysqli_query($link,$sqltransportmode);

				$counttransportmode=mysqli_num_rows($rstransportmode);

				if($counttransportmode==0)

				{

					/*echo  "<tr> 

				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

					die;*/

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");

				}

				$sqlloadcapacity="SELECT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' 

								AND load_capacity='".$load_capacity."'";

				$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);

				$countloadcapacity=mysqli_num_rows($rsloadcapacity);

				if($countloadcapacity==0)

				{

					/*echo  "<tr> 

				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Load capacity column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

					die;*/

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Load capacity)");

				}

				/*$sqloilgroup="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$oil_group."'";

				$rsoilgroup=mysqli_query($link,$sqloilgroup);

				$countoilgroup=mysqli_num_rows($rsoilgroup);

				if($countoilgroup==0)

				{

					/*echo  "<tr> 

				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Load capacity column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

					die;*/

					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");

				}

				else

				{

					$rowoilgroup=mysqli_fetch_assoc($rsoilgroup);

					$product_group_code=$rowoilgroup['product_group_code'];

				}

				$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type='".$pack_type."'";

				$rspacktype=mysqli_query($link,$sqlpacktype);

				$countpacktype=mysqli_num_rows($rspacktype);

				if($countpacktype==0)

				{

					/*echo  "<tr> 

				<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Pack Type column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

					die;*/

					/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Pack Type)");

				}*/

				$sqlprodvalue="SELECT prod_code,product_group_code FROM product_master WHERE acedns='Y' AND 	dns_prod_code='".$dns_prod_code."'";

				$rsprodvalue=mysqli_query($link,$sqlprodvalue);

				$countprodvalue=mysqli_num_rows($rsprodvalue);

				if($countprodvalue==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU code)");

				}

				else

				{

					$rowprodvalue=mysqli_fetch_assoc($rsprodvalue);

					$product_group_code=$rowprodvalue['product_group_code'];

				}



				$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

				$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

				$countverticalvalue=mysqli_num_rows($rsverticalvalue);

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

			echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_load_distribution' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php.php'\"/></td></tr></table></form>";

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

		$tabledata='<form name="packing_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="8" class="TDHEAD" align="center">Packing Master</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>SKU code</td>

						<td>Packing Cost</td>

						<td>Labour Cost</td>

						<td>Extra Cost</td>

						<td>Plant name</td>

					  </tr>';		



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

					$packing_cost=trim($data[1]);

					$labour_cost=trim($data[2]);

					$extra_cost=trim($data[3]);

					$plant_name=trim($data[4]);

					$csv_row_count=$rec_count+1;

					$current_date=date('Y-m-d');

					

					$sqlprodvalue="SELECT prod_code,product_group_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";

					$rsprodvalue=mysqli_query($link,$sqlprodvalue);

					$countprodvalue=mysqli_num_rows($rsprodvalue);

					if($countprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Prod code - $dns_prod_code)");

					}



				$tabledatacsv.="<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$dns_prod_code\">

								<input type=\"hidden\" name=\"packing_cost[]\" value=\"$packing_cost\">

								<input type=\"hidden\" name=\"labour_cost[]\" value=\"$labour_cost\">

								<input type=\"hidden\" name=\"extra_cost[]\" value=\"$extra_cost\">

								<input type=\"hidden\" name=\"plant_name[]\" value=\"$plant_name\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$dns_prod_code."</td>

										<td align=\"right\">".$packing_cost."</td>

										<td align=\"right\">".$labour_cost."</td>

										<td align=\"right\">".$extra_cost."</td>

										<td>".$plant_name."</td>

							   </tr>";					

				 }

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Packing Master</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_packing' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

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

			$error_array=array();

			$current_date=date('Y-m-d');

			$oil_type_array=array();

			$process_cost_array=array();

			$plant_name_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="process_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="50%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="5" class="TDHEAD" align="center">Process Cost</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>Oil Category</td>

						<td>Oil type</td>

						<td>Process cost</td>

						<td>Plant name</td>

					  </tr>';		

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

				  	$oil_category=trim($data[0]);

					$oil_type=trim($data[1]);

					$process_cost=trim($data[2]);	

					$plant_name=trim($data[3]);				

					$csv_row_count=$rec_count+1;

					

				     $tabledatacsv.="<input type=\"hidden\" name=\"oil_category[]\" value=\"$oil_category\">

					 		<input type=\"hidden\" name=\"oil_type[]\" value=\"$oil_type\">

					 		<input type=\"hidden\" name=\"process_cost[]\" value=\"$process_cost\">

					 		<input type=\"hidden\" name=\"plant_name[]\" value=\"$plant_name\">

						<tr id=\"tab\">

								<td>".$count."</td>

								<td>".$oil_category."</td>

								<td>".$oil_type."</td>

								<td align=\"right\">".$process_cost."</td>

								<td >".$plant_name."</td>

					   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"5\"><font size=\"+2\"><u>Process Cost</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"5\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			 echo $tabledata.=$tabledatacsv."<tr><td colspan='3' align='right'><input type='hidden' name='mode' value='submit_process' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			 die;

			 $successval=1;

		   }

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

			

			$error_array=array();

			$current_date=date('Y-m-d');

			$plant_name_array=array();

			$dns_prod_code_array=array();

			$oil_type_array=array();

			$formulation_array=array();

			$base_oil_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="oil_formulation" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="50%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="8" class="TDHEAD" align="center">Oilrate formulation</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="7%">SI</td>

						<td width="18%">Plant</td>

						<td width="27%">Prod code</td>

						<td width="18%">Oil type</td>

						<td width="10%">Formulation</td>

						<td width="10%">Base oil</td>

						<td width="10%">Percentile calc</td>

					  </tr>';		



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

				  

					$plant_name=trim($data[0]);

					$dns_prod_code=trim($data[1]);

					$oil_type=trim($data[2]);

					$formulation=trim($data[3]);

					$base_oil=trim($data[4]);

					$percentile_calc=trim($data[5]);

					$csv_row_count=$rec_count+1;

					$current_date=date('Y-m-d');

					

					$sqlplantvalue="SELECT branch_name FROM branch_master WHERE is_plant='yes' AND branch_name='".$plant_name."'";

					$rsplantvalue=mysqli_query($link,$sqlplantvalue);

					$countplantvalue=mysqli_num_rows($rsplantvalue);

					if($countplantvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");

					}



					if($oil_type=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil Type)");

					}

					$sqlprodvalue="SELECT prod_code,product_group_code FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";

					$rsprodvalue=mysqli_query($link,$sqlprodvalue);

					$countprodvalue=mysqli_num_rows($rsprodvalue);

					if($countprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Prod code - $dns_prod_code)");

					}

					

					$tabledatacsv.="<input type=\"hidden\" name=\"plant_name[]\" value=\"$plant_name\">

								<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$dns_prod_code\">

								<input type=\"hidden\" name=\"oil_type[]\" value=\"$oil_type\">

								<input type=\"hidden\" name=\"formulation[]\" value=\"$formulation\">

								<input type=\"hidden\" name=\"base_oil[]\" value=\"$base_oil\">

								<input type=\"hidden\" name=\"percentile_calc[]\" value=\"$percentile_calc\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$plant_name."</td>

										<td>".$dns_prod_code."</td>

										<td >".$oil_type."</td>

										<td align=\"right\">".$formulation."</td>

										<td align=\"right\">".$base_oil."</td>

										<td >".$percentile_calc."</td>

							   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Oilrate Formulation</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_formulation' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}

		/*else

		{

			echo $successval="Naming convention for Oilrate formulation.csv is wrong.";

			exit();	

		}*/

		

		//For Conversion csv

		if(similar_file_exists("../csv/$folderName/conversion.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/conversion.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			

			$error_array=array();

			$current_date=date('Y-m-d');

			$plant_name_array=array();

			$dns_prod_code_array=array();

			$oil_type_array=array();

			$formulation_array=array();

			$base_oil_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="oil_formulation" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="45%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="8" class="TDHEAD" align="center">Conversion</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="8%">SI</td>

						<td width="31%">Prod code</td>

						<td width="31%">Mapped prod code</td>

						<td width="10%">Is Flash</td>

						<td width="20%">Flash name</td>

					  </tr>';		



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

				  

					$prod_code=trim($data[0]);

					$mapped_prod_code=trim($data[1]);

					$is_flash=trim($data[2]);

					$flash_name=trim($data[3]);

					$add_subtract_val=trim($data[4]);

					$csv_row_count=$rec_count+1;



					/*if($prod_code==$mapped_prod_code)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Prod code and Mapped Prod code are same)");

					}*/

					$sqlprodvalue="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code."'";

					$rsprodvalue=mysqli_query($link,$sqlprodvalue);

					$countprodvalue=mysqli_num_rows($rsprodvalue);

					if($countprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Prod code - $prod_code)");

					}

					$sqlmappedprodvalue="SELECT prod_code FROM product_master WHERE dns_prod_code='".$mapped_prod_code."'";

					$rsmappedprodvalue=mysqli_query($link,$sqlmappedprodvalue);

					$countmappedprodvalue=mysqli_num_rows($rsmappedprodvalue);

					if($countmappedprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Mapped prod code - $mapped_prod_code)");

					}

					

					$tabledatacsv.="<input type=\"hidden\" name=\"prod_code[]\" value=\"$prod_code\">

								<input type=\"hidden\" name=\"mapped_prod_code[]\" value=\"$mapped_prod_code\">

								<input type=\"hidden\" name=\"is_flash[]\" value=\"$is_flash\">

								<input type=\"hidden\" name=\"flash_name[]\" value=\"$flash_name\">

								<input type=\"hidden\" name=\"add_subtract_val[]\" value=\"$add_subtract_val\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$prod_code."</td>

										<td>".$mapped_prod_code."</td>

										<td>".$is_flash."</td>

										<td>".$flash_name."</td>

							   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"5\"><font size=\"+2\"><u>Conversion</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"5\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			echo $tabledata.=$tabledatacsv."<tr><td colspan='3' align='right'><input type='hidden' name='mode' value='submit_conversion' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}

		/*else

		{

			echo $successval="Naming convention for conversion.csv is wrong.";

			exit();	

		}*/

		//For Packing Material Master csv

		if(similar_file_exists("../csv/$folderName/Packing Material Master.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Packing Material Master.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$error_array=array();

			$current_date=date('Y-m-d');

			$oil_type_array=array();

			$process_cost_array=array();

			$plant_name_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="packing_material" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="45%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="9" class="TDHEAD" align="center">Packing Material Master</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>Material Name</td>

						<td>UOM</td>

						<td>Price</td>

						<td>Weight</td>

						<td>Final Pack</td>

						<td>Height</td>

						<td>Length</td>

						<td>Beadth</td>

					  </tr>';		

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

				  

					$material_name=trim($data[0]);

					$UOM=trim($data[1]);	

					$price=trim($data[2]);

					$weight=trim($data[3]);

					$final_pack=trim($data[4]);

					$height=trim($data[5]);

					$length=trim($data[6]);

					$breadth=trim($data[7]);				

					$csv_row_count=$rec_count+1;

					

					if($material_name=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Material name)");

					}

					/*if($UOM=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (UOM)");

					}*/

					/*if($price=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Price)");

					}*/

				     $tabledatacsv.="<input type=\"hidden\" name=\"material_name[]\" value=\"$material_name\">

					 	<input type=\"hidden\" name=\"UOM[]\" value=\"$UOM\">

					 	<input type=\"hidden\" name=\"price[]\" value=\"$price\">

						<input type=\"hidden\" name=\"weight[]\" value=\"$weight\">

						<input type=\"hidden\" name=\"final_pack[]\" value=\"$final_pack\">

						<input type=\"hidden\" name=\"height[]\" value=\"$height\">

						<input type=\"hidden\" name=\"length[]\" value=\"$length\">

						<input type=\"hidden\" name=\"breadth[]\" value=\"$breadth\">

						<tr id=\"tab\">

								<td>".$count."</td>

								<td>".$material_name."</td>

								<td >".$UOM."</td>

								<td align=\"right\">".$price."</td>

								<td align=\"right\">".$weight."</td>

								<td align=\"right\">".$final_pack."</td>

								<td align=\"right\">".$height."</td>

								<td align=\"right\">".$length."</td>

								<td align=\"right\">".$breadth."</td>

					   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"9\"><font size=\"+2\"><u>Packing Material Master</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"9\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			 echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_packing_material' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='5' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			 die;

			 $successval=1;

		   }

		}

		/*else

		{

			echo $successval="Naming convention for Packing Material Master.csv is wrong.";

			exit();	

		}*/

		//For Packing BOM csv

		if(similar_file_exists("../csv/$folderName/Packing BOM.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Packing BOM.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$error_array=array();

			$current_date=date('Y-m-d');

			$oil_type_array=array();

			$process_cost_array=array();

			$plant_name_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="packing_bom" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="55%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="7" class="TDHEAD" align="center">Packing BOM</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>SKU Code</td>

						<td>SKU Name</td>

						<td>Material Name</td>

						<td>Usage Qty</td>

						<td>UOM</td>

						<td>Multiple/Single</td>

					  </tr>';		

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

				  

					$prod_code=trim($data[0]);

					$prod_name=trim($data[1]);	

					$material_name=trim($data[2]);

					$usage_qty=trim($data[3]);

					$UOM=trim($data[4]);

					$multiple_single=trim($data[5]);				

					$csv_row_count=$rec_count+1;

					

					if($prod_code=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU Code)");

					}

					if($material_name=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Material name)");

					}

					$sqlprodvalue="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code."'";

					$rsprodvalue=mysqli_query($link,$sqlprodvalue);

					$countprodvalue=mysqli_num_rows($rsprodvalue);

					if($countprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU Code - $prod_code)");

					}

					/*if($usage_qty=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Usage Qty)");

					}*/

					if($usage_qty=='') $usage_qty=0;

				     $tabledatacsv.="<input type=\"hidden\" name=\"prod_code[]\" value=\"$prod_code\"><input type=\"hidden\" name=\"material_name[]\" value=\"$material_name\">

					 	<input type=\"hidden\" name=\"usage_qty[]\" value=\"$usage_qty\"><input type=\"hidden\" name=\"UOM[]\" value=\"$UOM\"><input type=\"hidden\" name=\"multiple_single[]\" value=\"$multiple_single\">

						<tr id=\"tab\">

								<td>".$count."</td>

								<td >".$prod_code."</td>

								<td >".$prod_name."</td>

								<td>".$material_name."</td>

								<td align=\"right\">".$usage_qty."</td>

								<td>".$UOM."</td>

								<td>".$multiple_single."</td>

					   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"7\"><font size=\"+2\"><u>Packing BOM</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"7\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			 echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'><input type='hidden' name='mode' value='submit_packing_bom' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='3' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			 die;

			 $successval=1;

		   }

		}

		/*else

		{

			echo $successval="Naming convention for Packing BOM.csv is wrong.";

			exit();	

		}*/

		//For Labour BOM csv

		if(similar_file_exists("../csv/$folderName/Labour BOM.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Labour BOM.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$error_array=array();

			$current_date=date('Y-m-d');

			$oil_type_array=array();

			$process_cost_array=array();

			$plant_name_array=array();

			$count=0;

			$tabledataval='';

			$tabledatacsv='';

			$tabledata='<form name="labour_bom" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="60%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="11" class="TDHEAD" align="center">Labour BOM</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>SKU Code</td>

						<td>Manufacturing</td>

						<td>Unloading top</td>

						<td>Unloading</td>

						<td>Empty Shifting</td>

						<td>Filling</td>

						<td>Labelling</td>

						<td>Field Shifting</td>

						<td>Loading</td>

						<td>Marking</td>

					  </tr>';		

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

				  

					$prod_code=trim($data[0]);

					$manufacturing=trim($data[1]);

					$unloading_top=trim($data[2]);	

					$unloading=trim($data[3]);	

					$empty_shifting=trim($data[4]);	

					$filling=trim($data[5]);	

					$labelling=trim($data[6]);	

					$filed_shifting=trim($data[7]);	

					$loading=trim($data[8]);

					$marking=trim($data[9]);					

					$csv_row_count=$rec_count+1;

					

					if($prod_code=='')

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU Code)");

					}

					$sqlprodvalue="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code."'";

					$rsprodvalue=mysqli_query($link,$sqlprodvalue);

					$countprodvalue=mysqli_num_rows($rsprodvalue);

					if($countprodvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (SKU Code - $prod_code)");

					}



					if($usage_qty=='') $usage_qty=0;

				     $tabledatacsv.="<input type=\"hidden\" name=\"prod_code[]\" value=\"$prod_code\">

					 			   <input type=\"hidden\" name=\"manufacturing[]\" value=\"$manufacturing\">

					 				<input type=\"hidden\" name=\"unloading_top[]\" value=\"$unloading_top\">

									<input type=\"hidden\" name=\"unloading[]\" value=\"$unloading\">

									<input type=\"hidden\" name=\"empty_shifting[]\" value=\"$empty_shifting\">

									<input type=\"hidden\" name=\"filling[]\" value=\"$filling\">

									<input type=\"hidden\" name=\"labelling[]\" value=\"$labelling\">

									<input type=\"hidden\" name=\"filed_shifting[]\" value=\"$filed_shifting\">

									<input type=\"hidden\" name=\"loading[]\" value=\"$loading\">

									<input type=\"hidden\" name=\"marking[]\" value=\"$marking\">

						<tr id=\"tab\">

								<td>".$count."</td>

								<td >".$prod_code."</td>

								<td align=\"right\">".$manufacturing."</td>

								<td align=\"right\">".$unloading_top."</td>

								<td align=\"right\">".$unloading."</td>

								<td align=\"right\">".$empty_shifting."</td>

								<td align=\"right\">".$filling."</td>

								<td align=\"right\">".$labelling."</td>

								<td align=\"right\">".$filed_shifting."</td>

								<td align=\"right\">".$loading."</td>

								<td align=\"right\">".$marking."</td>

					   </tr>";					

				}

				 $count++;	

			  	$rec_count++;

			}		

			if(count($error_array) >0){

			 echo "<tr> 

					<td width=\"60%\" align=\"center\"  colspan=\"11\"><font size=\"+2\"><u>Labour BOM</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"60%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"11\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

			 echo $tabledata.=$tabledatacsv."<tr><td colspan='6' align='right'><input type='hidden' name='mode' value='submit_labour_bom' /><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='5' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			 die;

			 $successval=1;

		   }

		}

		/*else

		{

			echo $successval="Naming convention for Packing BOM.csv is wrong.";

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

					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

					$countdistinctplant=mysqli_num_rows($rsdistinctplant);

					if($countdistinctplant==0)

					{

						/*echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

					}

					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

					$distinct_plant_name=$rowdistinctplant['plant_name'];

					$distinct_branch_code=$rowdistinctplant['branch_code'];

					/*$sqlproductgroupcode="SELECT product_group_code,formulation FROM product_group_master WHERE 

											product_group_name='".$product_group_name."'";

					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

					$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);

					if($countproductgroupcode==0)

					{

						/*echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil group column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");

					}

					$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

					$product_group_code=$rowproductgroupcode['product_group_code'];

					$is_formulation=$rowproductgroupcode['formulation'];*/

					

					/*$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

					if($countverticalvalue==0)

					{

						/*echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Depot cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

					}*/

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

					if(vertical_fields=='yes')

					{				

						$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 

													acedns='Y' AND black_list='N' AND vertical_value='".$vertical_value."'";

					}

					else

					{

						$sqlselectdistinctdnsprod="SELECT dns_prod_code,conversion_factor,conversion_factor_two FROM product_master WHERE 

												acedns='Y' AND black_list='N'";

					}

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

					{

						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];

						${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];

						${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];

						$depot_cost_case_prodwise=$depot_cost*${conversion_factor_two.$distinct_dnsprod_code};

						//$depot_cost_case_prodwise=round(($depot_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$distinct_plant_name\">

										<input type=\"hidden\" name=\"branch_code_array[]\" value=\"$distinct_branch_code\">

										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">

										<input type=\"hidden\" name=\"depot_cost_case_array[]\" value=\"$depot_cost_case_prodwise\">

										<input type=\"hidden\" name=\"depot_cost_ton_array[]\" value=\"$depot_cost\">

										<input type=\"hidden\" name=\"vertical_value_array[]\" value=\"$vertical_value\">";

						$ins_count++;				

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

		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_depot\"><input type='submit' name='submit2' value='Final Upload' /></td></td><td colspan='3' align='left'><input type='button' name='button2' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php.php'\"/></td></tr></table></form>";

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

	   if(similar_file_exists("../csv/$folderName/margin cost-old.csv")!=false)

	   {

		$filename=similar_file_exists("../csv/$folderName/margin cost-old.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$plant_name_array=array();

		$dns_prod_code_array=array();

		$oil_group_array=array();

		$oil_type_array=array();

		$margin_cost_case_array=array();

		$margin_cost_array=array();

		$vertical_value_array=array();

		$count=0;

		$tabledatacsv='';

		$tabledataval='';

		$tabledata='<form name="margin_cost" method="post" action=""><input type="hidden" name="mode" value="submit_margin"><table border="1" style="border-collapse:collapse;" class="border" width="40%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="4" class="TDHEAD" align="center">Margin cost</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>Oil group</td>

						<td>Oil type</td>

						<td>Margin cost</td>

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

				

				$product_group_name=trim($data[0]);

				$pack_size=trim($data[1]);

				$margin_cost=trim($data[2]);

				//$vertical_value=trim($data[4]);

								

					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

					$countdistinctplant=mysqli_num_rows($rsdistinctplant);

					if($countdistinctplant==0)

					{

						/*echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

					  /*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

					}

					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

					$distinct_plant_name=$rowdistinctplant['plant_name'];

					$distinct_branch_code=$rowdistinctplant['branch_code'];

					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";

					$rsstatechk=mysqli_query($link,$sqlstatechk);

					$countstatechk=mysqli_num_rows($rsstatechk);

					if($countstatechk==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");

					}

					$rowstate=mysqli_fetch_assoc($rsstatechk);

					$state_code=$rowstate['state_code'];*/

					

					$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$product_group_name."'";

					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

					$countproductgroupcode=mysqli_num_rows($rsproductgroupcode);

					if($countproductgroupcode==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil group column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

					 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group)");

					}

					$rsproductgroupcode=mysqli_query($link,$sqlproductgroupcode);

					$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

					$product_group_code=$rowproductgroupcode['product_group_code'];

					//$is_formulation=$rowproductgroupcode['formulation'];

					

					$sqlpacksizechk="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	pack_size='".$pack_size."'";

					$rspacksizechk=mysqli_query($link,$sqlpacksizechk);

					$countpacksizechk=mysqli_num_rows($rspacksizechk);

					if($countpacksizechk==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil type column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil type)");

					}

					$sqloilgrouppacksizechk="SELECT prod_code FROM product_master WHERE acedns='Y' AND product_group_code='".$product_group_code."' AND	

										pack_size='".$pack_size."'";

					$rsoilgrouppacksizechk=mysqli_query($link,$sqloilgrouppacksizechk);

					$countoilgrouppacksizechk=mysqli_num_rows($rsoilgrouppacksizechk);

					if($countoilgrouppacksizechk==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Oil type column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Oil group,Oil type)");

					}

					/*$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

					if($countverticalvalue==0)

					{*/

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in margin cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

					}*/

					$tabledatacsv.="<input type=\"hidden\" name=\"product_group_name[]\" value=\"$product_group_name\">

									<input type=\"hidden\" name=\"oil_type_array[]\" value=\"$pack_size\">

									<input type=\"hidden\" name=\"margin_cost_ton_array[]\" value=\"$margin_cost\">

									<tr id=\"tab\">

											<td>".$count."</td>

											<td>".$product_group_name."</td>

											<td>".$pack_size."</td>

											<td align=\"right\">".number_format($margin_cost,2)."</td>

										</tr>";

					$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code FROM product_master WHERE 

												product_group_code='".$product_group_code."'  AND acedns='Y' AND black_list='N' 

												AND pack_size='".$pack_size."'";	

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

					{

						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];

						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 

											dns_prod_code='".$distinct_dnsprod_code."' AND acedns='Y' AND black_list='N'";

						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);

						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

		

						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];

						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];

						

						//$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};

						$margin_cost_case_prodwise=$margin_cost/1000;

						$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						$tabledataval.="<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$distinct_dnsprod_code\">

										<input type=\"hidden\" name=\"oil_group_array[]\" value=\"$product_group_code\">

										<input type=\"hidden\" name=\"oil_type_array[]\" value=\"$pack_size\">

										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost_case_prodwise\">

										<input type=\"hidden\" name=\"margin_cost_array[]\" value=\"$margin_cost\">";

					}

				 }

			$count++;				  

			$rec_count++;

		   }

		    if(count($error_array) >0){

				  echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"4\"><font size=\"+2\"><u>Margin Cost</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"4\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='2' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}

	   //For Margin Cost RASOI csv

	  if(similar_file_exists("../csv/$folderName/margin cost.csv")!=false)

	   {

		$filename=similar_file_exists("../csv/$folderName/margin cost.csv");

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

					  	<td colspan="4" class="TDHEAD" align="center">Margin cost RASOI</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td>SI</td>

						<td>Plant name</td>

						<td>SKU code</td>

						<td>Margin cost</td>

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

				$dns_prod_code=trim($data[1]);

				$margin_cost=trim($data[2]);

								

					/*$sqlplant="SELECT plant_name FROM branch_master WHERE plant_name='".$plant_name."'";

					$rsplant=mysqli_query($link,$sqlplant);

					$rowplant=mysqli_fetch_assoc($rsplant);

					$countplant=mysqli_num_rows($rsplant);

					if($countplant==0)

					{

						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");

					}

					$rowplant=mysqli_fetch_assoc($rsplant);

					$distinct_plant_name=$rowplant['plant_name'];*/

					

					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

					$countdistinctplant=mysqli_num_rows($rsdistinctplant);

					if($countdistinctplant==0)

					{

					  array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

					}

					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

					$distinct_plant_name=$rowdistinctplant['plant_name'];

					$distinct_branch_code=$rowdistinctplant['branch_code'];

					if($distinct_plant_name!=$plant_name)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");

					}*/

					

					$sqlproductcode="SELECT PGM.product_group_code FROM product_group_master PGM,product_master PM 

									WHERE PM.product_group_code=PGM.product_group_code  AND PM.dns_prod_code='".$dns_prod_code."'";

					$rsproductcode=mysqli_query($link,$sqlproductcode);

					$countproductcode=mysqli_num_rows($rsproductcode);

					if($countproductcode==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");

					}

					/*$sqldepotproductchk="SELECT prod_code FROM product_master WHERE acedns='Y' AND branch_code='".$distinct_branch_code."' AND	

										dns_prod_code='".$dns_prod_code."'";

					$rsdepotproductchk=mysqli_query($link,$sqldepotproductchk);

					$countdepotproductchk=mysqli_num_rows($rsdepotproductchk);

					if($countdepotproductchk==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code,SKU code)");

					}*/

					$tabledatacsv.="<tr id=\"tab\">

											<td>".$count."</td>

											<td>".$plant_name."</td>

											<td>".$dns_prod_code."</td>

											<td align=\"right\">".number_format($margin_cost,2)."</td>

										</tr>";

					$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$plant_name\">

										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$dns_prod_code\">

										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost\">";					

					/*$sqlselectdistinctdnsprod="SELECT DISTINCT dns_prod_code,conversion_factor,conversion_factor_two 

												FROM product_master WHERE prod_desc NOT LIKE '%LUP%' 

												AND acedns='Y' AND black_list='N'  AND dns_prod_code='".$dns_prod_code."'";

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

					{

						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];

		

						${conversion_factor.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor'];

						${conversion_factor_two.$distinct_dnsprod_code}=$rowselectdistinctdnsprod['conversion_factor_two'];

						

						//$margin_cost_case_prodwise=$margin_cost/${conversion_factor_two.$distinct_dnsprod_code};

						//$margin_cost_case_prodwise=round(($margin_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						

						$tabledataval.="<input type=\"hidden\" name=\"plant_name_array[]\" value=\"$plant_name\">

										<input type=\"hidden\" name=\"dns_prod_code_array[]\" value=\"$dns_prod_code\">

										<input type=\"hidden\" name=\"margin_cost_case_array[]\" value=\"$margin_cost\">";

					}*/

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

		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}



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

					$rsstatechk=mysqli_query($link,$sqlstatechk);

					$countstatechk=mysqli_num_rows($rsstatechk);

					if($countstatechk==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");

					}

					$rowstate=mysqli_fetch_assoc($rsstatechk);

					$state_code=$rowstate['state_code'];

					

					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 

									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";

					$rsproductcode=mysqli_query($link,$sqlproductcode);

					$countproductcode=mysqli_num_rows($rsproductcode);

					if($countproductcode==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");

					}

					$rowproductcode=mysqli_fetch_assoc($rsproductcode);

					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

					if($countverticalvalue==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

					}

					/*$sqldepotproductchk="SELECT prod_code FROM product_master WHERE acedns='Y' AND branch_code='".$distinct_branch_code."' AND	

										dns_prod_code='".$dns_prod_code."'";

					$rsdepotproductchk=mysqli_query($link,$sqldepotproductchk);

					$countdepotproductchk=mysqli_num_rows($rsdepotproductchk);

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

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

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

		   	echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type='submit' name='submit1' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button3' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}



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

					$rsplant=mysqli_query($link,$sqlplant);

					$rowplant=mysqli_fetch_assoc($rsplant);

					$countplant=mysqli_num_rows($rsplant);

					if($countplant==0)

					{

						 array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Plant name)");

					}

					$rowplant=mysqli_fetch_assoc($rsplant);

					$distinct_plant_name=$rowplant['plant_name'];

					

					/*$sqldistinctplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

					$countdistinctplant=mysqli_num_rows($rsdistinctplant);

					if($countdistinctplant==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

					 /*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

					}

					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

					$distinct_plant_name=$rowdistinctplant['plant_name'];

					$distinct_branch_code=$rowdistinctplant['branch_code'];

					

					if($distinct_plant_name!=$plant_name)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Plant name,Depot code)");

					}*/

					$sqlstatechk="SELECT dns_state_code,state_code FROM state_master WHERE dns_state_code='".addslashes($dns_state_code)."'";

					$rsstatechk=mysqli_query($link,$sqlstatechk);

					$countstatechk=mysqli_num_rows($rsstatechk);

					if($countstatechk==0)

					{

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");

					}

					$rowstate=mysqli_fetch_assoc($rsstatechk);

					$state_code=$rowstate['state_code'];

					

					$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";

					$rstransportmode=mysqli_query($link,$sqltransportmode);

					$counttransportmode=mysqli_num_rows($rstransportmode);

					if($counttransportmode==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Transport mode column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");

					}

					$sqlproductcode="SELECT PGM.product_group_code,PGM.formulation FROM product_group_master PGM,product_master PM 

									WHERE PM.product_group_code=PGM.product_group_code AND PM.acedns='Y' AND PM.dns_prod_code='".$dns_prod_code."'";

					$rsproductcode=mysqli_query($link,$sqlproductcode);

					$countproductcode=mysqli_num_rows($rsproductcode);

					if($countproductcode==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for sku code column in Honeycomb cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (sku code)");

					}

					/*$rowproductgroupcode=mysqli_fetch_assoc($rsproductgroupcode);

					$product_group_code=$rowproductgroupcode['product_group_code'];

					$is_formulation=$rowproductgroupcode['formulation'];

					$sqlpacktype="SELECT DISTINCT pack_type FROM product_master WHERE pack_type='".$pack_type."'";

					$rspacktype=mysqli_query($link,$sqlpacktype);

					$countpacktype=mysqli_num_rows($rspacktype);

					if($countpacktype==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Pack Type column in load distribution.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Pack Type)");

					}*/

					$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

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

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					$countselectdistinctdnsprod=mysqli_num_rows($rsselectdistinctdnsprod);

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

		   echo $tabledata.=$tabledatacsv."<tr><td colspan='4' align='right'>&nbsp;&nbsp;&nbsp;<input type='submit' name='submit3' value='Final Upload' /></td><td colspan='4' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php.php'\"/></td></tr></table></form>";

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

					$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

					$countdistinctplant=mysqli_num_rows($rsdistinctplant);

					if($countdistinctplant==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Depot code column in Detention cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

					}

					$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

					$distinct_plant_name=$rowdistinctplant['plant_name'];

					$distinct_branch_code=$rowdistinctplant['branch_code'];

					/*$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

					$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

					$countverticalvalue=mysqli_num_rows($rsverticalvalue);

					if($countverticalvalue==0)

					{

						/*echo  "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+2\">Please provide proper value for Vertical value column in Detention cost.csv at row ".$csv_row_count."</font></td></tr>";

						die;*/

						/*array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

					}*/

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

												AND acedns='Y' AND black_list='N'";

					$rsselectdistinctdnsprod=mysqli_query($link,$sqlselectdistinctdnsprod);

					while($rowselectdistinctdnsprod=mysqli_fetch_assoc($rsselectdistinctdnsprod))

					{

						$distinct_dnsprod_code=$rowselectdistinctdnsprod['dns_prod_code'];

						$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,product_group_code FROM product_master WHERE 

											dns_prod_code='".$distinct_dnsprod_code."'";

						$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);

						$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

		

						${conversion_factor.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor'];

						${conversion_factor_two.$distinct_dnsprod_code}=$rowconversionfactor['conversion_factor_two'];

						${product_group_code.$distinct_dnsprod_code}=$rowconversionfactor['product_group_code'];

						

						/*$sqlchkformulation="SELECT formulation FROM product_group_master WHERE 

										product_group_code='".${product_group_code.$distinct_dnsprod_code}."'";

						$rschkformulation=mysqli_query($link,$sqlchkformulation);

						$rowchkformulation=mysqli_fetch_assoc($rschkformulation);

						$is_formulation=$rowchkformulation['formulation'];*/

		

						$detention_cost_case_prodwise=$detention_cost*${conversion_factor_two.$distinct_dnsprod_code};

						//$detention_cost_case_prodwise=round(($detention_cost_case_prodwise*${conversion_factor.$distinct_dnsprod_code}),2);

						

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

		    echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_detention\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit4' value='Final Upload' /></td><td colspan='3' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

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

					  	<td colspan="8" class="TDHEAD" align="center">Depot Route Freight</td>

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

				$rsdistinctplant=mysqli_query($link,$sqldistinctplant);

				$countdistinctplant=mysqli_num_rows($rsdistinctplant);

				if($countdistinctplant==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Depot code)");

				}

				$rowdistinctplant=mysqli_fetch_assoc($rsdistinctplant);

				$distinct_plant_name=$rowdistinctplant['plant_name'];

				$distinct_branch_code=$rowdistinctplant['branch_code'];

				$sqlroutenamechk="SELECT route_code FROM route_master WHERE dns_route_code='".addslashes($route_code_name)."'";

				$rsroutenamechk=mysqli_query($link,$sqlroutenamechk);

				$countroutenamechk=mysqli_num_rows($rsroutenamechk);

				/*if($countroutenamechk==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Route - $route_code_name)");

				}*/

				$rowroutenamechk=mysqli_fetch_assoc($rsroutenamechk);

				$route_code=$rowroutenamechk['route_code'];



				/*$sqltransportmode="SELECT transport_mode FROM transport_mode WHERE transport_mode='".$transport_mode."'";

				$rstransportmode=mysqli_query($link,$sqltransportmode);

				$counttransportmode=mysqli_num_rows($rstransportmode);

				if($counttransportmode==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Transport mode)");

				}

				$sqlloadcapacity="SELECT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' 

								AND load_capacity='".$capacity."'";

				$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);

				$countloadcapacity=mysqli_num_rows($rsloadcapacity);

				if($countloadcapacity==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Load capacity)");

				}

				$sqlstatechk="SELECT dns_state_code FROM state_master WHERE state='".addslashes($state_code)."'";

				$rsstatechk=mysqli_query($link,$sqlstatechk);

				$countstatechk=mysqli_num_rows($rsstatechk);

				if($countstatechk==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (State code)");

				}

				$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 

												AND truck_load='".$capacity."' ";

				$rscheckloaddistribution=mysqli_query($link,$sqlcheckloaddistribution);

				$countcheckloaddistribution=mysqli_num_rows($rscheckloaddistribution);

				if($countcheckloaddistribution==0)

				{

					array_push($error_array,"Load Distribution Error @Row (".$csv_row_count.") Columns : (Transport mode,Capacity)");

				}*/

				/*$sqlchkstateroutecombination="SELECT customer_code FROM customer_master WHERE route_code='".$route_code."' AND state_code='".$state_code."' 

											AND acedns='Y'";

				$rschkstateroutecombination=mysqli_query($link,$sqlchkstateroutecombination);

				$countchkstateroutecombination=mysqli_num_rows($rschkstateroutecombination);

				if($countchkstateroutecombination==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Route,State code)");

				}

				$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

				$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

				$countverticalvalue=mysqli_num_rows($rsverticalvalue);

				if($countverticalvalue==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

				}*/

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

								</tr>";

			   }

			   $count++;

			 $rec_count++;

			}

			if(count($error_array) >0){

			    echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Depot Route Freight</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		    echo $tabledata.=$tabledatacsv."<tr><td colspan='3' align='right'><input type='submit' name='submit8' value='Final Upload' /></td><td colspan='5' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

	}

	/*else

	{

		echo $successval="Naming convention for Depot route freight.csv is wrong.";

		exit();

	}*/

	//For Depot route Freight error chk

	if(similar_file_exists("../csv/$folderName/depot route freight error chk.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/depot route freight error chk.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$count=0;

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

			

				$sqlroutenamechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_code_name)."'";

				$rsroutenamechk=mysqli_query($link,$sqlroutenamechk);

				$countroutenamechk=mysqli_num_rows($rsroutenamechk);

				$rowroutenamechk=mysqli_fetch_assoc($rsroutenamechk);

				$route_code=$rowroutenamechk['route_code'];

				if($countroutenamechk==0)

				{

					$sqlbranchdestinationfreight  = "insert into branch_route_freight_test ";

					$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code_name."'";

					$sqlbranchdestinationfreight .= " ,route_code='".$route_code_name."'";

					$sqlbranchdestinationfreight .= " ,acedns='".$acedns."'";

					$sqlbranchdestinationfreight .= " ,freight='".$freight."'";

					$sqlbranchdestinationfreight .= " , `date`=CURDATE()";

					$sqlbranchdestinationfreight .= " , transport_mode='".$transport_mode."'";

					$sqlbranchdestinationfreight .= " , capacity='".$capacity."'";

					$sqlbranchdestinationfreight .= " , state_code='".$state_code."'";

					$sqlbranchdestinationfreight .= " , vertical_value='".$vertical_value."'";

					$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($link,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

									Internal DATA execution problem on depot freight test table.PLease contact aceDNS admin.");				



				}

			  }

			 $rec_count++;

			}

			$GLOBALS['msg'] = 'Zip file extracted and data has been uploaded successfully';

	}

	/*else

	{

		echo $successval="Naming convention for Depot route freight.csv is wrong.";

		exit();

	}*/



	//For Truck load master csv

	   if(similar_file_exists("../csv/$folderName/Truck load master.csv")!=false)

	   {

		$filename=similar_file_exists("../csv/$folderName/Truck load master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$load_qty_array=array();

		$height_array=array();

		$length_array=array();

		$breadth_array=array();

		$count=1;

		$tabledataval='';

		$tabledatacsv='';

		$tabledata='<form name="margin_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="40%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="5" class="TDHEAD" align="center">Truck load master</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="">SI</td>

						<td width="">Load Qty</td>

						<td width="">Height</td>

						<td width="">Length</td>

						<td width="">Breadth</td>

					  </tr>';



			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$lines = file($filename);

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

				

				$load_qty=trim($data[0]);

				$height=trim($data[1]);

				$length=trim($data[2]);

				$breadth=trim($data[2]);



				$tabledatacsv.="<input type=\"hidden\" name=\"load_qty_array[]\" value=\"$load_qty\">

								<input type=\"hidden\" name=\"height_array[]\" value=\"$height\">

								<input type=\"hidden\" name=\"length_array[]\" value=\"$length\">

								<input type=\"hidden\" name=\"breadth_array[]\" value=\"$breadth\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td align=\"right\">".$load_qty."</td>

										<td align=\"right\">".number_format($height,2)."</td>

										<td align=\"right\">".number_format($length,2)."</td>

										<td align=\"right\">".number_format($breadth,2)."</td>

									</tr>";

				$count++;					

				}

			$rec_count++;

		   }

		   if(count($error_array) >0){

			    echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"5\"><font size=\"+2\"><u>Truck load master</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"5\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		    echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_truck_load\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit4' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}

	 //For Base oil master csv

	   if(similar_file_exists("../csv/$folderName/Base oil master.csv")!=false)

	   {

		$filename=similar_file_exists("../csv/$folderName/Base oil master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$load_qty_array=array();

		$height_array=array();

		$length_array=array();

		$breadth_array=array();

		$count=1;

		$tabledataval='';

		$tabledatacsv='';

		$tabledata='<form name="margin_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="40%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="5" class="TDHEAD" align="center">Base Oil Master</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="">SI</td>

						<td width="">Base Oil</td>

						<td width="">Mandatory</td>

						<td width="">Lower Limit</td>

						<td width="">Upper Limit</td>

					  </tr>';



			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

			$lines = file($filename);

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

				$base_oil=trim($data[0]);

				$mandatory=trim($data[1]);

				$lower_limit=trim($data[2]);

				$upper_limit=trim($data[3]);



				$tabledatacsv.="<input type=\"hidden\" name=\"base_oil_array[]\" value=\"$base_oil\">

								<input type=\"hidden\" name=\"mandatory_array[]\" value=\"$mandatory\">

								<input type=\"hidden\" name=\"upper_limit_array[]\" value=\"$upper_limit\">

								<input type=\"hidden\" name=\"lower_limit_array[]\" value=\"$lower_limit\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$base_oil."</td>

										<td>".$mandatory."</td>

										<td align=\"right\">".number_format($lower_limit,2)."</td>

										<td align=\"right\">".number_format($upper_limit,2)."</td>

									</tr>";

				$count++;					

				}

			$rec_count++;

		   }

		   if(count($error_array) >0){

			    echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"5\"><font size=\"+2\"><u>Base Oil Master</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"5\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		    echo $tabledata.=$tabledatacsv.$tabledataval."<tr><td colspan='3' align='right'><input type=\"hidden\" name=\"mode\" value=\"submit_base_oil\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit4' value='Final Upload' /></td><td colspan='2' align='left'><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

		}

	//For Brokerage Cost csv

	if(similar_file_exists("../csv/$folderName/Brokerage Cost.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Brokerage Cost.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$load_qty_array=array();

		$height_array=array();

		$length_array=array();

		$breadth_array=array();

		$count=1;

		$tabledataval='';

		$tabledatacsv='';

		$tabledata='<form name="margin_cost" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="60%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="7" class="TDHEAD" align="center">Brokerage Cost</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="">SI</td>

						<td width="">Broker Code</td>

						<td width="">Broker Name</td>

						<td width="">Oil Category</td>

						<td width="">Pack Size</td>

						<td width="">UOM</td>

						<td width="">Brokerage Cost</td>

					  </tr>';

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

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

				$broker_code=trim($data[0]);

				$broker_name=trim($data[1]);

				$oil_category=trim($data[2]);

				//$oil_category_array=explode(',',$oil_category);

				$pack_size=trim($data[3]);

				$UOM=trim($data[4]);

				$brokerage_cost=trim($data[5]);

				

				$sqlchkbroker="SELECT broker_id FROM broker_master WHERE dns_broker_id='".$broker_code."'";

				$rschkbroker=mysqli_query($link,$sqlchkbroker);

				$countchkbroker=mysqli_num_rows($rschkbroker);

				if($countchkbroker==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Broker code)");

				}

				$rowchkbroker=mysqli_fetch_assoc($rschkbroker);

				$broker_code_db=$rowchkbroker['broker_id'];

				

				/*$sqlcheckloaddistribution="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 

												AND truck_load='".$capacity."' ";

				$rscheckloaddistribution=mysqli_query($link,$sqlcheckloaddistribution);

				$countcheckloaddistribution=mysqli_num_rows($rscheckloaddistribution);

				if($countcheckloaddistribution==0)

				{

					array_push($error_array,"Load Distribution Error @Row (".$csv_row_count.") Columns : (Transport mode,Capacity)");

				}*/

				/*$sqlchkstateroutecombination="SELECT customer_code FROM customer_master WHERE route_code='".$route_code."' AND state_code='".$state_code."' 

											AND acedns='Y'";

				$rschkstateroutecombination=mysqli_query($link,$sqlchkstateroutecombination);

				$countchkstateroutecombination=mysqli_num_rows($rschkstateroutecombination);

				if($countchkstateroutecombination==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Route,State code)");

				}

				$sqlverticalvalue="SELECT prod_code FROM product_master WHERE acedns='Y' AND 	vertical_value='".$vertical_value."'";

				$rsverticalvalue=mysqli_query($link,$sqlverticalvalue);

				$countverticalvalue=mysqli_num_rows($rsverticalvalue);

				if($countverticalvalue==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Vertical value)");

				}*/

				$tabledatacsv.="<input type=\"hidden\" name=\"broker_code[]\" value=\"$broker_code_db\">

								<input type=\"hidden\" name=\"broker_name[]\" value=\"$broker_name\">

								<input type=\"hidden\" name=\"oil_category[]\" value=\"$oil_category\">

								<input type=\"hidden\" name=\"pack_size[]\" value=\"$pack_size\">

								<input type=\"hidden\" name=\"UOM[]\" value=\"$UOM\">

								<input type=\"hidden\" name=\"brokerage_cost[]\" value=\"$brokerage_cost\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$broker_code."</td>

										<td>".$broker_name."</td>

										<td>".$oil_category."</td>

										<td>".$pack_size."</td>

										<td>".$UOM."</td>

										<td align=\"right\">".number_format($brokerage_cost,2)."</td>

								</tr>";

				$count++;

			   }

			  $rec_count++;

			}

			if(count($error_array) >0){

			    echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"8\"><font size=\"+2\"><u>Brokerage Cost</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"8\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		    echo $tabledata.=$tabledatacsv."<tr><td colspan='7' align='center'><input type=\"hidden\" name=\"mode\" value=\"submit_brokerage_cost\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit8' value='Final Upload' /><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

	}

	/*else

	{

		echo $successval="Naming convention for customer product relation.csv is wrong.";

		exit();

	}*/

    //For Opening Bargain csv

	if(similar_file_exists("../csv/$folderName/Opening Bargain.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Opening Bargain.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$error_array=array();

		$current_date=date('Y-m-d');

		$load_qty_array=array();

		$height_array=array();

		$length_array=array();

		$breadth_array=array();

		$count=1;

		$tabledataval='';

		$tabledatacsv='';

		$tabledata='<form name="opening_bargain" method="post" action=""><table border="1" style="border-collapse:collapse;" class="border" width="60%" cellpadding="4" align="center" >

					  <tr class="TDHEAD" align="center" id="head_main">

					  	<td colspan="9" class="TDHEAD" align="center">Opening Bargain</td>

					  </tr>

					  <tr class="TDHEAD_SUB" align="center" id="head_main">

						<td width="">SI</td>

						<td width="">Customer Code</td>

						<td width="">Item Code</td>

						<td width="">Bargain no</td>

						<td width="">Bargain date</td>

						<td width="">Bargain qty</td>

						<td width="">Bargain rate</td>

						<td width="">Pending Bargain qty</td>

						<td width="">Unit</td>

					  </tr>';

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

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

				$customer_code=trim($data[0]);

				$item_code=trim($data[2]);

				$bargain_no=trim($data[4]);

				$bargain_date=trim($data[5]);

				$bargain_qty=trim($data[6]);

				$bargain_rate=trim($data[7]);

				$pending_bargain_qty=trim($data[8]);

				//$oil_category_array=explode(',',$oil_category);

				$unit=trim($data[9]);

				

				$sqlchkcustomercode="SELECT customer_code,branch_code FROM customer_master WHERE dns_customer_code='".$customer_code."'";

				$rschkcustomercode=mysqli_query($link,$sqlchkcustomercode);

				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);

				if($countchkcustomercode==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Columns : (Customer Code)");

				}

				else

				{

				$rowchkcustomercode=mysqli_fetch_assoc($rschkcustomercode);

				$customer_code_db=$rowchkcustomercode['customer_code'];

				$branch_code_db=$rowchkcustomercode['branch_code'];

				}

				

				$sqlchkprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$item_code."' and acedns='Y'";

				$rschkprodcode=mysqli_query($link,$sqlchkprodcode);

				$countchkprodcode=mysqli_num_rows($rschkprodcode);

				if($countchkprodcode==0)

				{

					array_push($error_array,"Error @Row (".$csv_row_count.") Column : (Item code)");

				}

				$rowchkprodcode=mysqli_fetch_assoc($rschkprodcode);

				

				$prod_code_db=$rowchkprodcode['prod_code'];

				$sqlchkmappedprodcode="SELECT prod_code FROM product_unit_coversion_matrix WHERE mapped_prod_code='".$item_code."' ";

				$rschkmappedprodcode=mysqli_query($link,$sqlchkmappedprodcode);

				$countchkmappedprodcode=mysqli_num_rows($rschkmappedprodcode);

				if($countchkmappedprodcode==0)

				{

					array_push($error_array,"Error Item code is not parent @Row (".$csv_row_count.") Column : (Item code)");

				}

				if($customer_code_db !='' && $prod_code_db!='')

				{

				$tabledatacsv.="<input type=\"hidden\" name=\"customer_code[]\" value=\"$customer_code_db\">

								<input type=\"hidden\" name=\"dns_customer_code[]\" value=\"$customer_code\">

								<input type=\"hidden\" name=\"branch_code[]\" value=\"$branch_code_db\">

								<input type=\"hidden\" name=\"prod_code[]\" value=\"$prod_code_db\">

								<input type=\"hidden\" name=\"dns_prod_code[]\" value=\"$item_code\">

								<input type=\"hidden\" name=\"bargain_no[]\" value=\"$bargain_no\">

								<input type=\"hidden\" name=\"bargain_date[]\" value=\"$bargain_date\">

								<input type=\"hidden\" name=\"bargain_qty[]\" value=\"$bargain_qty\">

								<input type=\"hidden\" name=\"bargain_rate[]\" value=\"$bargain_rate\">

								<input type=\"hidden\" name=\"pending_bargain_qty[]\" value=\"$pending_bargain_qty\">

								<input type=\"hidden\" name=\"unit[]\" value=\"$unit\">

								<tr id=\"tab\">

										<td>".$count."</td>

										<td>".$customer_code."</td>

										<td>".$item_code."</td>

										<td>".$bargain_no."</td>

										<td>".$bargain_date."</td>

										<td>".$bargain_qty."</td>

										<td align=\"right\">".number_format($bargain_rate,2)."</td>

										<td>".$pending_bargain_qty."</td>

										<td>".$unit."</td>

								</tr>";

				$count++;

				}

			   }

			  $rec_count++;

			}

			if(count($error_array) >0){

			    echo "<tr> 

					<td width=\"90%\" align=\"center\"  colspan=\"9\"><font size=\"+2\"><u>Opening Bargain</u></font></td></tr><br />";

			   foreach($error_array as $error_val)

			   {

				   echo "<tr> 

					<td width=\"90%\" align=\"center\" class=\"ERR\" nowrap=\"nowrap\" colspan=\"9\"><font size=\"+1\">".$error_val."</font></td></tr>";

			   }

		   }

		   else

		   {

		    echo $tabledata.=$tabledatacsv."<tr><td colspan='9' align='center'><input type=\"hidden\" name=\"mode\" value=\"submit_opening_bargain\">&nbsp;&nbsp;&nbsp;<input type='submit' name='submit11' value='Final Upload' /><input type='button' name='button4' value='Cancel' onclick=\"javascript:window.location='http://salesmpower.acedns.in/misreport/adminPricingComponentPreperation.php'\"/></td></tr></table></form>";

			die;

			$successval=1;

		   }

	}

	/*else

	{

		echo $successval="Naming convention for customer product relation.csv is wrong.";

		exit();

	}*/



	if($successval==1)

	{

		$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";

		if(mysqli_query($link,$sqlInsert))

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