<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
$nick_name=$_REQUEST['nick_name'];
if(strtoupper($nick_name)=='EMAMI'){
	define("DB","acedns_EMAMI");
}
if(strtoupper($nick_name)=='EMAMIT'){
	define("DB","acedns_EMAMIT");
}
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
	$sauda_no=$_REQUEST['sauda_no'];
	$sql_sauda_header_download = "SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.credit_days,CM.state_code,SH.broker_id,SH.sauda_no,DATE_FORMAT(LO.date,'%d-%m-%Y') AS sauda_date,DATE_FORMAT(LO.date,'%H:%i:%s') AS sauda_time,
								 LO.date AS sauda_date_time,DATE_FORMAT(SH.sauda_valid_from,'%d-%m-%Y') AS sauda_valid_from,DATE_FORMAT(SH.sauda_valid_from,'%Y-%m-%d') AS sauda_valid_from_calc,
								 DATE_FORMAT(LO.date,'%Y-%m-%d') AS sauda_date_calc,CM.sauda_validity_period,PM.dns_prod_code,PM.vertical_value,SH.customer_code,LO.emp_code,
								 SD.sku_code,SD.qty,SD.convert_qty_two,PM.UOM1,PM.prod_desc,PM.product_group_code,PM.vertical_value,PM.conversion_factor,PM.conversion_factor_two,BM.branch_code,BM.dns_branch_code,BM.branch_name,BM.plant_name,SD.sale_rate,SD.freight_charge,SD.TD,SD.liquid_TD,SD.premium,SD.amount,CM.TD AS CMTD,EM.emp_name,EM.state,
								 PGM.product_group_name,PGM.is_upload,PGM.formulation,SH.d_instruction,RM.route_name,SD.primary_freight,SD.depot_cost,SD.honeycomb_cost,SD.brokerage_cost
								 ,SD.margin_cost FROM sauda_header SH,location LO,sauda_details SD,product_master PM,customer_master CM,branch_master BM,employee_master EM, product_group_master PGM,route_master RM
								 WHERE LO.trans_id=SH.sauda_no AND SH.sauda_no=SD.sauda_no AND LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'FT%') AND LO.emp_code!='C0007' AND SD.sku_code=PM.prod_code AND
								 SH.customer_code=CM.customer_code AND SH.branch_code=BM.branch_code AND PM.product_group_code=PGM.product_group_code AND CM.route_code=RM.route_code AND SH.sauda_no='".$sauda_no."'";
	$rs_sauda_header_download = mysqli_query($link,$sql_sauda_header_download) or die(mysqli_error()." Error in sauda data download: ".$sql_sauda_header_download);
	$linesaudaheader = '';
	while($rec_sauda_header_download = mysqli_fetch_assoc($rs_sauda_header_download))
	{
		$dns_customer_code=$rec_sauda_header_download['dns_customer_code'];
		$customer_code=$rec_sauda_header_download['customer_code'];
		$customer_name=$rec_sauda_header_download['customer_name'];
		$broker_id=$rec_sauda_header_download['broker_id'];
		$product_group_code = $rec_sauda_header_download['product_group_code'];
		$emp_code = $rec_sauda_header_download['emp_code'];
		$product_group_name = $rec_sauda_header_download['product_group_name'];
		$state = $rec_sauda_header_download['state'];
		$customer_code = $rec_sauda_header_download['customer_code'];
		$branch_code = $rec_sauda_header_download['branch_code'];
		$plant_name = $rec_sauda_header_download['plant_name'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$conversion_one = $rec_sauda_header_download['conversion_factor'];
		$conversion_two = $rec_sauda_header_download['conversion_factor_two'];
		$sauda_date_time = $rec_sauda_header_download['sauda_date_time'];
		$sauda_time = $rec_sauda_header_download['sauda_time'];
		$liquid_TD = $rec_sauda_header_download['liquid_TD'];
		$state_code = $rec_sauda_header_download['state_code'];
		$is_upload = $rec_sauda_header_download['is_upload'];
		$formulation = $rec_sauda_header_download['formulation'];
		$route_name = $rec_sauda_header_download['route_name'];

		$sqlbroker="SELECT dns_broker_id,broker_name,brokerage_cost FROM broker_master WHERE broker_id='".$broker_id."'";
		$rsbroker=mysqli_query($link,$sqlbroker);
		$recbroker=mysqli_fetch_assoc($rsbroker);
		$dns_broker_id=$recbroker['dns_broker_id'];
		$broker_name=$recbroker['broker_name'];
		//$brokerage_cost=$recbroker['brokerage_cost'];
		$brokerage_cost=$rec_sauda_header_download['brokerage_cost'];
		$sauda_no=$rec_sauda_header_download['sauda_no'];
		$sauda_date=date('Y-m-d',strtotime($rec_sauda_header_download['sauda_date']));
		$credit_days=$rec_sauda_header_download['credit_days'];
		$sauda_date_calc=$rec_sauda_header_download['sauda_date_calc'];
		$payment_due_on=date('Y-m-d',strtotime("+$credit_days days,$sauda_date_calc"));
		$sauda_valid_from=date('Y-m-d',strtotime($rec_sauda_header_download['sauda_valid_from']));
		$sauda_valid_from_calc=$rec_sauda_header_download['sauda_valid_from_calc'];
		$sauda_valid_days = $rec_sauda_header_download['sauda_validity_period'];
		$valid_upto = date('Y-m-d',strtotime("+$sauda_valid_days days,$sauda_valid_from_calc"));
		$dns_prod_code=$rec_sauda_header_download['dns_prod_code'];
		$emp_code=$rec_sauda_header_download['emp_code'];
		$sku_code=$rec_sauda_header_download['sku_code'];
		$qty=$rec_sauda_header_download['qty'];
		$qty=round($qty,2);
		$convert_qty_two=$rec_sauda_header_download['convert_qty_two'];
		$UOM1=$rec_sauda_header_download['UOM1'];
		$prod_desc=$rec_sauda_header_download['prod_desc'];
		$vertical_value = $rec_sauda_header_download['vertical_value'];
		$vertical_value =str_replace(',','&',$vertical_value);
		$dns_branch_code=$rec_sauda_header_download['dns_branch_code'];
		$branch_name=$rec_sauda_header_download['branch_name'];
		$sale_rate=$rec_sauda_header_download['sale_rate'];
		$freight_charge=$rec_sauda_header_download['freight_charge'];
		$TD=$rec_sauda_header_download['TD'];
		$premium=$rec_sauda_header_download['premium'];
		if($premium=='')  $premium=0;
		$employee_name=$rec_sauda_header_download['emp_name'];
		$CMTD=$rec_sauda_header_download['CMTD'];
		if($freight_charge=='')  $freight_charge=0;
		if($freight_charge!=0) $incoterms='FOR';
		else  					$incoterms='Exw';
		$d_instruction = preg_replace('/[\r\n]+/', '',$rec_sauda_header_download['d_instruction']);
		$d_instruction =str_replace(',','',$d_instruction);
		/*$sauda_date=str_replace('-','.',$sauda_date);
		$sauda_date=str_replace('/','.',$sauda_date);
		$sauda_valid_from=str_replace('-','.',$sauda_valid_from);
		$sauda_valid_from=str_replace('/','.',$sauda_valid_from);
		$valid_upto=str_replace('-','.',$valid_upto);
		$valid_upto=str_replace('/','.',$valid_upto);

		$payment_due_on=str_replace('-','.',$payment_due_on);
		$payment_due_on=str_replace('/','.',$payment_due_on);*/

		//$amount=round(($qty*(($sale_rate+$freight_charge+$premium)-$TD)),2);
		$amount=$rec_sauda_header_download['amount'];
		if($CMTD=='')
		{
			$CMTD=0;
		}
		$sqlstate="SELECT `state` FROM state_master WHERE dns_state_code='".$state_code."'";
		$rsstate=mysqli_query($link,$sqlstate);
		$rowstate=mysqli_fetch_assoc($rsstate);
		$state_name=$rowstate['state'];
		$sqlcustomercreditlimit="SELECT credit_limit FROM customer_vertical_credit_limit WHERE
								customer_code='".$customer_code."' AND branch_code='".$branch_code."' AND vertical_value='".$vertical_value."'";
		$rscustomercreditlimit=mysqli_query($link,$sqlcustomercreditlimit);
		$rowcustomercreditlimit=mysqli_fetch_assoc($rscustomercreditlimit);
		$credit_limit=$rowcustomercreditlimit['credit_limit'];

		/*For Price Generation Parameter*/
		$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND
						plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rslooserate=mysqli_query($link,$sqllooserate);
		$rowlooserate=mysqli_fetch_assoc($rslooserate);
		$loose_rate_ton=$rowlooserate['loose_rate_ton'];

		//For live

		$sqlpackingprodwise="SELECT packing_cost,packing_realization FROM packing_master WHERE dns_prod_code='".$dns_prod_code."'
							AND plant_name='".$plant_name."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
		$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
		$packing_cost=round($rowpackingprodwise['packing_cost'],2);
		if($packing_cost=='')   $packing_cost=0;

		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $packing_cost=0;
	   	 }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $packing_cost=$packing_cost;
		 }
		/* $sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$dns_prod_code."' 
	                              AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rshoneycombcostprodwise=mysqli_query($link,$sqlhoneycombcostprodwise);
		$rowhoneycombcostprodwise=mysqli_fetch_assoc($rshoneycombcostprodwise);
		$honeycomb_cost=$rowhoneycombcostprodwise['honeycomb_cost'];
		if($honeycomb_cost=='')
		{
			$honeycomb_cost=0;
		}
		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $honeycomb_cost=0;
	   	 }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $honeycomb_cost=$honeycomb_cost;
		 }*/
		$honeycomb_cost=$rec_sauda_header_download['honeycomb_cost'];
		$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$dns_prod_code."' 
									AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdetentioncostprodwise=mysqli_query($link,$sqldetentioncostprodwise);
		$rowdetentioncostprodwise=mysqli_fetch_assoc($rsdetentioncostprodwise);
		$detention_cost=$rowdetentioncostprodwise['detention_cost'];
		if($detention_cost=='')
		{
			$detention_cost=0;
		}
		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $detention_cost=0;
	   	 }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $detention_cost=$detention_cost;
		 }

		/*$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
		$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
		$depot_cost=round($rowdepotcostprodwise['depot_cost'],2);
		if($depot_cost=='')  		$depot_cost=0;

		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $depot_cost=0;
	   	 }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $depot_cost=$depot_cost;
		 }*/
		$depot_cost=$rec_sauda_header_download['depot_cost'];
		/*$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
		$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
		$freight_cost=round($rowfreightcostprodwise['freight_cost'],2);
		if($freight_cost=='')      $freight_cost=0;
		
		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $freight_cost=0;
	     }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $freight_cost=$freight_cost;
		 }*/
		$freight_cost=$rec_sauda_header_download['primary_freight'];
		/*if(strtoupper($nick_name)=='EMAMI'){
		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dns_prod_code."'
							AND branch_code='".$branch_code."' AND datetime <='".$sauda_date_time."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
		$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
		$margin_cost=round($rowmargincostprodwise['margin_cost'],2);
		if($margin_cost=='')       $margin_cost=0;

		if($loose_rate_ton==0 && $formulation=='no')
		 {
		   $margin_cost=0;
	     }
		if($loose_rate_ton==0 && $formulation=='yes'){
			 $margin_cost=$margin_cost;
		 }
		}*/
		if(strtoupper($nick_name)=='EMAMIT' || strtoupper($nick_name)=='EMAMI'){
		$margin_cost=$rec_sauda_header_download['margin_cost'];
		 }
		if($formulation=='yes'){
			$material_cost=$sale_rate-$packing_cost;
		}
		else{
		$material_cost=round(($loose_rate_ton/$conversion_two),2);
		$material_cost=round(($material_cost*$conversion_one),2);
	    }

		if(strpos($prod_desc,'LUP')==true){
			$material_cost=0;
		}
		if($material_cost==0)
		{
			/*$sqlsalerate="SELECT sale_rate FROM sauda_mrp WHERE branch_code='".$branch_code."' AND product_code='".$sku_code."'";
			$rssalerate=mysqli_query($link,$sqlsalerate);
			$rowsalerate=mysqli_fetch_assoc($rssalerate);
			$material_cost=$rowsalerate['sale_rate'];*/
			$material_cost=$sale_rate;
		}
		
		$PR00=$material_cost+$packing_cost+$honeycomb_cost+$margin_cost+$premium-$TD-$liquid_TD;
		$packing_realization=$rowpackingprodwise['packing_realization'];
		if($packing_realization=='')   $packing_realization=0;
		if($material_cost==0){
			$realization_per_case=0;
			$realization_per_MT=0;
		}
		else
		{
		
		//$realization_per_case=$material_cost-$TD+$premium;
			//For live
		//$realization_per_case=$PR00-$packing_realization;
		$realization_per_case=$material_cost+$packing_cost+$margin_cost+$premium-$TD-$liquid_TD-$packing_realization-$brokerage_cost;
		$realization_per_case=round($realization_per_case,2);
		$realization_per_MT=round((($realization_per_case*$rec_sauda_header_download['conversion_factor_two'])/$rec_sauda_header_download['conversion_factor']),2);
		}
		//$final_amount=$amount-(($amount*$CMTD)/100);
		$FRC1=$freight_cost+$freight_charge+$depot_cost+$detention_cost;
		$csv_sale_rate=$PR00+$FRC1;
		$final_amount=$amount;
		/*$final_amount=$amount;
		$valuesaudaheader  = $dns_customer_code.",";
		$valuesaudaheader .= $customer_name.",";
		$valuesaudaheader .= $dns_broker_id.",";
		$valuesaudaheader .= $broker_name.",";
		$valuesaudaheader .= $sauda_no.",";
		$valuesaudaheader .= $sauda_date.",";
		$valuesaudaheader .= $sauda_time.",";
		$valuesaudaheader .= $sauda_valid_from.",";
		$valuesaudaheader .= $valid_upto.",";
		$valuesaudaheader .= $dns_prod_code.",";
		$valuesaudaheader .= $qty.",";
		$valuesaudaheader .= $convert_qty_two.",";
		$valuesaudaheader .= $UOM1.",";
		$valuesaudaheader .= $product_group_name.",";
		$valuesaudaheader .= $prod_desc.",";
		$valuesaudaheader .= $dns_branch_code.",";
		$valuesaudaheader .= $branch_name.",";
		$valuesaudaheader .= $state_name.",";
		$valuesaudaheader .= $material_cost.",";
		$valuesaudaheader .= $freight_cost.",";
		$valuesaudaheader .= $packing_cost.",";
		$valuesaudaheader .= $depot_cost.",";
		$valuesaudaheader .= $margin_cost.",";
		//$valuesaudaheader .= $sale_rate.",";
		$valuesaudaheader .= $freight_charge.",";
		$valuesaudaheader .= $TD.",";
		$valuesaudaheader .= $liquid_TD.",";
		$valuesaudaheader .= $premium.",";
		$valuesaudaheader .= $PR00.",";
		$valuesaudaheader .= $FRC1.",";
		$valuesaudaheader .= $final_amount.",";
		$valuesaudaheader .= $incoterms.",";
		$valuesaudaheader .= $employee_name.",";
		$valuesaudaheader .= $payment_due_on.",";
		$valuesaudaheader .= $credit_limit.",";
		$valuesaudaheader .= $d_instruction.",";
		$valuesaudaheader .= $vertical_value.",";
		$valuesaudaheader .= $realization_per_case.",";
		$valuesaudaheader .= $realization_per_MT.",";
		$valuesaudaheader .= $csv_sale_rate.",";*/
		$sqlselsuada="SELECT sauda_no,prod_code FROM sauda_download_log WHERE sauda_no='".$sauda_no."' AND prod_code='".$dns_prod_code."'";
		$rsselsauda=mysqli_query($link,$sqlselsuada);
		$cntselsauda=mysqli_num_rows($rsselsauda);
		if($cntselsauda ==0)
		{
		$sqlinsertsaudadownloadlog="INSERT INTO sauda_download_log SET customer_code='".$dns_customer_code."',
									customer_name='".addslashes($customer_name)."',
									route_name='".addslashes($route_name)."',
									broker_id='".$dns_broker_id."',
									broker_name='".addslashes($broker_name)."',sauda_no='".$sauda_no."',
									sauda_date='".$sauda_date."',
									sauda_time='".$sauda_time."',
									contract_valid_from='".$sauda_valid_from."',
									contract_valid_to='".$valid_upto."',
									prod_code='".$dns_prod_code."',
									qty='".$qty."',
									convert_qty_two='".$convert_qty_two."',
									UOM='".$UOM1."',
									product_group_code='".$product_group_code."',
									product_group_name='".addslashes($product_group_name)."',
									prod_desc='".$prod_desc."',branch_code='".$dns_branch_code."',
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
									TD='".$TD."',
									liquid_TD='".$liquid_TD."',
									premium='".$premium."',
									PR00='".$PR00."',
									FRC1='".$FRC1."',
										amount='".$final_amount."',
									incoterms='".$incoterms."',
									emp_name='".addslashes($employee_name)."',
									payment_due_on='".$payment_due_on."',
									cm_credit_limit='".$credit_limit."',
										remarks='".addslashes($d_instruction)."',
										vertical='".$vertical_value."',
									realization_per_case='".$realization_per_case."',
									realization_per_MT='".$realization_per_MT."',
										sale_rate='".$csv_sale_rate."',packing_realization='".$packing_realization."',download_time=CURRENT_TIMESTAMP()";
		//exit();								
		mysqli_query($link,$sqlinsertsaudadownloadlog);
		}
	}
?>
