<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
	//$from_date=date('2017-09-11');
	$from_date='2018-09-07';
	$to_date=date('Y-m-d');
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(SUBSTRING(sauda_no,-14,18),'%Y-%m-%d') >='".$from_date."' AND
					  	DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d') <='".$to_date."'";
	}
	$sql_sauda_header_download = "SELECT branch_code,PR00,prod_code,sl_no,customer_code,route_name,incoterms,
								sauda_no,DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') AS sauda_date_time,primary_freight,packing_cost,	honeycomb_cost,detention_charges,depot_cost,margin_cost_RA,GST_value,amount,qty,premium,TD,liquid_TD
									FROM sauda_download_log WHERE `sauda_type` = 'RAA' ".$date_condition." AND incoterms IN('FOR DEPOT','FOR PLANT')
									ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	$rs_sauda_header_download = mysqli_query($link,$sql_sauda_header_download) or die(mysqli_error()." Error in sauda data download: ".$sql_sauda_header_download);
	while($rec_sauda_header_download = mysqli_fetch_assoc($rs_sauda_header_download))
	{
		$branch_code = $rec_sauda_header_download['branch_code'];
		$dns_prod_code = $rec_sauda_header_download['prod_code'];
		$sl_code = $rec_sauda_header_download['sl_no'];
		$customer_code=$rec_sauda_header_download['customer_code'];
		$route_name=$rec_sauda_header_download['route_name'];
		$incoterms=$rec_sauda_header_download['incoterms'];
		$primary_freight=$rec_sauda_header_download['primary_freight'];
		$packing_cost=$rec_sauda_header_download['packing_cost'];
		$honeycomb_cost=$rec_sauda_header_download['honeycomb_cost'];
		$detention_charges=$rec_sauda_header_download['detention_charges'];
		$depot_cost=$rec_sauda_header_download['depot_cost'];
		$margin_cost_RA=$rec_sauda_header_download['margin_cost_RA'];
		$GST_value=$rec_sauda_header_download['GST_value'];
		$amount=$rec_sauda_header_download['amount'];
		$qty=$rec_sauda_header_download['qty'];
		$bid_rate=($amount/$qty);
		$premium=$rec_sauda_header_download['premium'];
		$TD=$rec_sauda_header_download['TD'];
		$liquid_TD=$rec_sauda_header_download['liquid_TD'];
		$sauda_no=$rec_sauda_header_download['sauda_no'];
		
		$sqlplant="SELECT plant_name,branch_code FROM branch_master WHERE dns_branch_code='".$branch_code."'";
		$rsplant=mysqli_query($link,$sqlplant);
		$rowplant=mysqli_fetch_assoc($rsplant);
		$plant_name=$rowplant['plant_name'];
		$branch_code=$rec_sauda_header_download['branch_code'];
		
		$sqlcustomerdetails="SELECT dns_customer_code,route_code,sauda_validity_period,transport_mode,loadability_ton,state_code FROM customer_master WHERE 
							dns_customer_code='".$customer_code."' AND acedns='Y'";
		$rscustomerdetails=mysqli_query($link,$sqlcustomerdetails);
		$rowcustomerdetails=mysqli_fetch_assoc($rscustomerdetails);
		$sauda_validity_period=$rowcustomerdetails['sauda_validity_period'];
		$route_code=$rowcustomerdetails['route_code'];
		$transport_mode=$rowcustomerdetails['transport_mode'];
		$loadability_ton=$rowcustomerdetails['loadability_ton'];
		$state_code=$rowcustomerdetails['state_code'];
		$dns_customer_code=$rowcustomerdetails['dns_customer_code'];					
		
		$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_name."'";
		$rsroutecode=mysqli_query($link,$sqlroutecode);
		$rowroutecode=mysqli_fetch_assoc($rsroutecode);
		echo $route_code=$rowroutecode['route_code'];
		
		$sqlconversion="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE dns_prod_code='".$dns_prod_code."' AND acedns='Y'";
		$rsconversion=mysqli_query($link,$sqlconversion); 
		$rowconversion=mysqli_fetch_assoc($rsconversion);
		$conversion_factor = $rowconversion['conversion_factor'];
		$conversion_factor_two = $rowconversion['conversion_factor_two'];

		
		$sauda_date_time=$rec_sauda_header_download['sauda_date_time'];
		
		if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='FOR PLANT')
		{
			if(strtoupper($incoterms)=='FOR DEPOT'){
				$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' 
						AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."'  AND state_code='".$state_code."' 
						AND vertical_value='HBC:Rasoi:BIB'";
			}
			if(strtoupper($incoterms)=='FOR PLANT'){
				$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' 
						AND route_code='".$route_code."' AND acedns='Y' AND transport_mode='".$transport_mode."' 
						AND capacity='".$loadability_ton."' AND state_code='".$state_code."' AND vertical_value='HBC:Rasoi:BIB'";
			}
			
			$rsfreight=mysqli_query($link,$sqlfreihgt);
			$rowfreight=mysqli_fetch_assoc($rsfreight);
			$freight=$rowfreight['freight'];
			
			if($freight=='') $freight=0;
			
			$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$loadability_ton."' AND prod_code='".$dns_prod_code."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";
			/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
								ORDER BY datetime DESC LIMIT 0,1";	*/										
			$rsqtytruckload=mysqli_query($link,$sqlqtytruckload);
			$countqtytruckload=mysqli_num_rows($rsqtytruckload);
			if($countqtytruckload >0)
			{
			  $rowqtytruckload=mysqli_fetch_assoc($rsqtytruckload);
			  $qty_truck_load=$rowqtytruckload['qty_truck_load'];
			}
			else $qty_truck_load=0;
			/*else{
				$sqlqtytruckloadnext="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
							 AND truck_load='".$loadability_ton."' AND prod_code='".$material_code[$i]."' 
							 AND datetime >='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
				$rsqtytruckloadnext=mysqli_query($link,$sqlqtytruckloadnext);
				$countqtytruckloadnext=mysqli_num_rows($rsqtytruckloadnext);
				if($countqtytruckloadnext >0)
				{
					$rowqtytruckloadnext=mysqli_fetch_assoc($rsqtytruckloadnext);
			  		$qty_truck_load=$rowqtytruckloadnext['qty_truck_load'];
				}
				else
				{
					$qty_truck_load=0;
				}
			}*/
			if($freight > 0 && $qty_truck_load >0)
			{
				$freight_charge=round(($freight/$qty_truck_load),2);
			}
			else $freight_charge=0;
		}
		if(strtoupper($incoterms)=='FOR DEPOT' || strtoupper($incoterms)=='FOR PLANT')
		{
			if(strtoupper($incoterms)=='FOR DEPOT')  $FRC1=$primary_freight+$freight_charge+$depot_cost+$detention_charges;
			if(strtoupper($incoterms)=='FOR PLANT')  $FRC1=$freight_charge+$detention_charges;
			
			$PR00=$bid_rate-$FRC1-$GST_value;
		}
		$material_cost=$bid_rate-$packing_cost-$margin_cost_RA-$FRC1-$honeycomb_cost-$GST_value;

		$realization_per_case=$material_cost+$packing_cost+$margin_cost_RA+$premium-$TD-$liquid_TD-$packing_cost;
		$realization_per_case=round($realization_per_case,2);
		$realization_per_MT=round((($realization_per_case*$conversion_factor_two)/$conversion_factor),2);
		
		echo $sqlupdatesaudadownloadlog="Update sauda_download_log SET realization_per_case='".$realization_per_case."',
									realization_per_MT='".$realization_per_MT."',
									freight_charge='".$freight_charge."',PR00='".$PR00."',
									FRC1='".$FRC1."',material_cost='".$material_cost."',download_time=CURRENT_TIMESTAMP() 
									WHERE 	sauda_no='".$sauda_no."' AND prod_code='".$dns_prod_code."' AND sauda_type='RAA'";
		
		//exit();
		mysqli_query($link,$sqlupdatesaudadownloadlog);
		
		//exit();
	}
?>
