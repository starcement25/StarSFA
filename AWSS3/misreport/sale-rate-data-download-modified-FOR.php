<?php
error_reporting(0);
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	/*if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{*/
$from_date = $_REQUEST['from_date'];
$from_date_check=date('Y-m-d',strtotime($from_date));
$from_date_final=$from_date_check.' 23:59:59';
$product_group_code=$_REQUEST['product_group_code'];
$state=$_REQUEST['state'];
$routecode=$_REQUEST['route'];
$transport_mode=$_REQUEST['transport_mode'];
$capacityval=$_REQUEST['capacity'];

$sqlgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code=".$product_group_code."";
$rsgroupname=mysql_query($sqlgroupname);
$rowgroupname=mysql_fetch_array($rsgroupname);
$product_group_name=$rowgroupname['product_group_name'];

$sqlstate="SELECT state FROM state_master WHERE dns_state_code=".$state."";
$rsstate=mysql_query($sqlstate);
$rowstate=mysql_fetch_array($rsstate);
$state_name=$rowstate['state'];
$routearray=explode(",",$routecode);
$capacityarray=explode(",",$capacityval);

if(strpos($routecode,',')!=false){
	$route_name='All';
}
else
{
	$sqlroutename="SELECT route_name FROM route_master WHERE route_code=".$routecode."";
	$rsroutename=mysql_query($sqlroutename);
	$rowroutename=mysql_fetch_array($rsroutename);
	$route_name=$rowroutename['route_name'];
}
if(strpos($capacityval,',')!='false'){
	$capacityval='All';
}
else
{
	$capacityval=$capacityval;
}

$sql_product_details = "SELECT PM.dns_prod_code,PM.prod_desc,BM.branch_code,BM.dns_branch_code,BM.branch_name,PM.conversion_factor,
						PM.conversion_factor_two,BM.plant_name,BM.plant_code,PM.product_group_code,BM.branch_state,BM.dns_state_code,BM.is_plant 
						FROM product_master PM, sauda_mrp MR,branch_master BM
						WHERE PM.product_group_code IN(".$product_group_code.") AND PM.prod_code = MR.product_code 
						AND MR.branch_code=BM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc NOT LIKE '%LUP%' AND BM.acedns='Y' 
						AND BM.dns_state_code=".$state." ORDER BY BM.plant_name,BM.branch_name,PM.prod_desc ASC";
//exit();						   
$res_product_details = mysql_query($sql_product_details);
$total_rows = mysql_num_rows($res_product_details);

if($total_rows >0){
    echo '<div class="tbl-header"><table class="border datatable" width="100%" border="0" style="border-collapse:collapse;removed: fixed;"  align="center">
		<thead>
      <tr>
        <td colspan="23" class="TDHEAD" align="left" ><b>Sale Rate Report - '.$from_date.' - '.$product_group_name.' - '.$state_name.' - '.$route_name.' - '.$transport_mode.' - '.$capacityval.'</b></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>SI</td>
		<td>Plant Code</td>
		<td>Plant Name</td>
        <td>Depot Code</td>
        <td>Depot Name</td>
		<td>Route</td>
		<td>Capacity</td>
		<td>Oil group</td>
        <td>Material Code</td>
        <td>Material Description</td>
        <td>Material Cost</td>
        <td>Packing Cost</td>
		<td>Margin</td>
		<td>Detention Cost</td>
		<td>Honeycomb Cost</td>
		<td>Ex Plant Rate</td>
		<td>Plant to Route Freight</td>
		<td>Sale Rate - FOR Plant</td>
		<td>Depot Cost</td>
		<td>Primary Freight</td>
		<td>Ex - Depot Rate</td>
		<td>Scondary Freight</td>
		<td>Sale Rate - FOR Depot</td>
      </tr></thead></table></div><div class="tbl-content"><table class="BORDER datatable" cellpadding="0" cellspacing="0" border="1" style="removed: fixed;">
      <tbody>';
	  	$count = 1;

foreach($routearray as $route)
{
    $sqlroutenamedetails="SELECT route_name FROM route_master WHERE route_code=".$route."";
	$rsroutenamedetails=mysql_query($sqlroutenamedetails);
	$rowroutenamedetails=mysql_fetch_array($rsroutenamedetails);
	$route_name_details=$rowroutenamedetails['route_name'];

	foreach($capacityarray as $capacity)
	{
	$res_product_details = mysql_query($sql_product_details);
	$depot_array=array();
	while($row_product_details = mysql_fetch_array($res_product_details))
	{
		$conversion_one=$row_product_details['conversion_factor'];
		$conversion_two=$row_product_details['conversion_factor_two'];
		$plant_name=$row_product_details['plant_name'];
		$plant_code=$row_product_details['plant_code'];
		$dns_branch_code=$row_product_details['dns_branch_code'];
		$branch_name=$row_product_details['branch_name'];
		$dns_prod_code=$row_product_details['dns_prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$product_group_code=$row_product_details['product_group_code'];
		$branch_state=$row_product_details['branch_state'];
		$is_plant=$row_product_details['is_plant'];
		/*$sqlstatecode="SELECT dns_state_code FROM state_master WHERE state='".$branch_state."'";
		$rsstatecode=mysql_query($sqlstatecode);
		$rowstatecode=mysql_fetch_array($rsstatecode);*/
		$dns_state_code=$row_product_details['dns_state_code'];
		
		$sqlproductgroupname="SELECT product_group_name,formulation,vertical_value FROM product_group_master WHERE 
								product_group_code='".$product_group_code."'";
		$rsproductgroupname=mysql_query($sqlproductgroupname);
		$rowproductgroupname=mysql_fetch_array($rsproductgroupname);
		$product_group_name=$rowproductgroupname['product_group_name'];
		$is_formulation=$rowproductgroupname['formulation'];
		$vertical_value=$rowproductgroupname['vertical_value'];

		$sqlpackingprodwise="SELECT packing_cost FROM packing_master WHERE 
		dns_prod_code='".$row_product_details['dns_prod_code']."' AND  plant_name='".$plant_name."' AND datetime <='".$from_date_final."'ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysql_query($sqlpackingprodwise);
		$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
		$packing_cost=$rowpackingprodwise['packing_cost'];
		if($packing_cost=='')  $packing_cost=0;
		
		/*$sqldepotcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		$freight=$rowdepotcostprodwise['freight'];

		if($depot_cost=='')  $depot_cost=0;
		if($freight=='')      $freight=0;*/
		$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."'  AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		if($depot_cost=='')  $depot_cost=0;
		
		/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."'  AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
		$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
		$margin_cost=$rowmargincostprodwise['margin_cost'];
		if($margin_cost=='')  $margin_cost=0;*/
		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND state_code=".$state."  AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
		$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
		$margin_cost=$rowmargincostprodwise['margin_cost'];
		if($margin_cost=='')  $margin_cost=0;
		
		$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."'   AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
		$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
		$freight_cost=$rowfreightcostprodwise['freight_cost'];
		if($freight_cost=='')
		{
		$freight_cost=0;
		}
		$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$row_product_details['dns_prod_code']."' 
	                              AND plant_name='".$plant_name."' AND transport_mode='".$transport_mode."' AND state_code='".$state."' 
								  AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
		$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
		$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
		$honeycomb_cost=$rowhoneycombcostprodwise['honeycomb_cost'];
		if($honeycomb_cost=='')
		{
			$honeycomb_cost=0;
		}

			
		$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
						 AND truck_load='".$capacity."' AND prod_code='".$row_product_details['dns_prod_code']."' AND datetime <='".$from_date_final."' 
						 ORDER BY datetime DESC LIMIT 0,1";
		$rsqtytruckload=mysql_query($sqlqtytruckload);
		$rowqtytruckload=mysql_fetch_array($rsqtytruckload);
		$qty_truck_load=$rowqtytruckload['qty_truck_load'];
		if($is_plant=='yes')
		{
			$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$row_product_details['branch_code']."' 
						AND route_code=".$route." AND acedns='Y' AND transport_mode='".$transport_mode."' 
						AND capacity='".$capacity."' AND state_code=".$state."";
			$rsfreight=mysql_query($sqlfreihgt);
			$rowfreight=mysql_fetch_array($rsfreight);
			$freight=$rowfreight['freight'];			
			$freight_charge_plant=round(($freight/$qty_truck_load),2);
			$freight_charge_depot=0;
		}
		else
		{
			$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$row_product_details['branch_code']."' 
						AND route_code=".$route." AND acedns='Y' AND state_code=".$state."";
			$rsfreight=mysql_query($sqlfreihgt);
			$rowfreight=mysql_fetch_array($rsfreight);
			$freight=$rowfreight['freight'];
			$freight_charge_depot=round(($freight/$qty_truck_load),2);
			$freight_charge_plant=0;
		}
				 
		/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";	*/										
		//$rsqtytruckload=mysql_query($sqlqtytruckload);
				
		$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$row_product_details['dns_prod_code']."' 
									AND branch_code='".$row_product_details['branch_code']."' AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."   'ORDER BY datetime DESC LIMIT 0,1";
		$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
		$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
		$detention_cost=$rowdetentioncostprodwise['detention_cost'];
		if($detention_cost=='')
		{
		 $detention_cost=0;
		}
		if($is_formulation=='yes')
		{
		    $loosrate_final_val="";
			$sqlprocesscost="SELECT process_cost FROM process_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
								AND plant_name='".$plant_name."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
			$rsprocesscost=mysql_query($sqlprocesscost);
			$rowprocesscost=mysql_fetch_array($rsprocesscost);
			$process_cost=$rowprocesscost['process_cost'];
			
			//loose oilrate fetching
			$sqlprodwiseformulation="SELECT * FROM(SELECT prod_code,formulation,oils FROM loose_oilrate_formulation 
							WHERE product_group_code='".$product_group_code."' AND acedns='Y' AND plant_name='".$plant_name."' 
							AND prod_code='".$row_product_details['dns_prod_code']."' 
							AND datetime <='".$from_date_final."' ORDER BY datetime DESC) AS SAT GROUP BY 3 ";
			$rsprodwiseformulation=mysql_query($sqlprodwiseformulation);
			while($rowprodwiseformulation=mysql_fetch_array($rsprodwiseformulation))
			{					
				/*$sqllooserate="SELECT oils_rate FROM pricing_detials_formulation WHERE product_group_code='".$product_group_code."' AND 
						plant_name='".$plant_name."' AND oils='".$rowprodwiseformulation['oils']."' 
						AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";*/
				if(strtoupper($vertical_value)=='SPECIALTY FATS'){
					$sqllooserate="SELECT oils_rate,price_generated FROM pricing_detials_formulation WHERE  plant_name='".$plant_name."' 
									AND oils='".$rowprodwiseformulation['oils']."' 
									AND SUBSTRING(datetime,1,10) ='".$from_date_check."' ORDER BY datetime DESC LIMIT 0,1";	
				}
				else
				{
				$sqllooserate="SELECT oils_rate,price_generated FROM pricing_detials_formulation WHERE product_group_code='".$product_group_code."' AND 
						plant_name='".$plant_name."' AND oils='".$rowprodwiseformulation['oils']."' 
						AND SUBSTRING(datetime,1,10) ='".$from_date_check."' ORDER BY datetime DESC LIMIT 0,1";	
				}
				$rslooserate=mysql_query($sqllooserate);
				$rowlooserate=mysql_fetch_array($rslooserate);
				$oils_rate_ton=$rowlooserate['oils_rate'];
				if($oils_rate_ton==''){
					$material_cost=0;
				}
				else
				{
				
				 $loosrate_calc_val=(substr($rowprodwiseformulation['formulation'],0,-1)*$oils_rate_ton)/100;
				 $loosrate_final_val=$loosrate_final_val+$loosrate_calc_val;
				}
				$price_generated=$rowlooserate['price_generated'];
			 }
			 $loosrate_final_val=$loosrate_final_val+$process_cost;
			$material_cost=round(($loosrate_final_val/$conversion_two),2);	
			$material_cost=round(($material_cost*$conversion_one),2);

		}
		else
		{
			/*$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND 
					plant_name='".$plant_name."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";*/
			$sqllooserate="SELECT loose_rate_ton,price_generated FROM pricing_detials WHERE product_group_code='".$product_group_code."' AND 
					plant_name='".$plant_name."' AND SUBSTRING(datetime,1,10) ='".$from_date_check."' ORDER BY datetime DESC LIMIT 0,1";		
			$rslooserate=mysql_query($sqllooserate);
			$rowlooserate=mysql_fetch_array($rslooserate);
			$loose_rate_ton=$rowlooserate['loose_rate_ton'];
			if($loose_rate_ton=='')
			{
				$material_cost=0;
			}
			else
			{
				$material_cost=round(($loose_rate_ton/$conversion_two),2);
				$material_cost=round(($material_cost*$conversion_one),2);
			}
		}
		$price_generated=$rowlooserate['price_generated'];
		/*if($qty_truck_load>0)
		{
			$freight=$hire_cost/$qty_truck_load;
		}
		else
		{
			$freight=0;
		}*/
		//echo $material_cost;
		$ex_plant_rate=$material_cost+$packing_cost+$margin_cost+$honeycomb_cost+$detention_cost;
		$ex_plant_rate=round($ex_plant_rate,2);
		if($freight_charge_plant==0)
		{
			$FOR_plant_rate=0;
		}
		else
		{
			$FOR_plant_rate=$material_cost+$packing_cost+$margin_cost+$honeycomb_cost+$detention_cost+$freight_charge_plant;
		}
		$FOR_plant_rate=round($FOR_plant_rate,2);
		if($freight_cost=='0')
		{
			$Ex_depot_rate=0;
		}
		else
		{
			$Ex_depot_rate=$material_cost+$packing_cost+$margin_cost+$honeycomb_cost+$detention_cost+$depot_cost+$freight_cost;
		}
		$Ex_depot_rate=round($Ex_depot_rate,2);
		if($freight_cost==0 || $freight_charge_depot==0){
			$FOR_depot_rate=0;
		}
		else
		{
			$FOR_depot_rate=$material_cost+$packing_cost+$margin_cost+$honeycomb_cost+$detention_cost+$depot_cost+$freight_cost+$freight_charge_depot;
		}
		$FOR_depot_rate=round($FOR_depot_rate,2);
		
		if($material_cost>0 && $price_generated=='yes'){
		echo "<tr>
				<td>".$count."</td>
				<td >".$plant_code."</td>
				<td >".$plant_name."</td>
				<td>".$dns_branch_code."</td>
				<td >".$branch_name."</td>
				<td >".$route_name_details."</td>
				<td >".$capacity."</td>
				<td >".$product_group_name."</td>
				<td>".$dns_prod_code."</td>
				<td >".$prod_desc."</td>
				<td align=\"right\">".number_format($material_cost,2)."</td>
				<td align=\"right\">".number_format($packing_cost,2)."</td>
			    <td align=\"right\">".number_format($margin_cost,2)."</td>
				<td align=\"right\">".number_format($detention_cost,2)."</td>
				<td align=\"right\">".number_format($honeycomb_cost,2)."</td>
				<td align=\"right\" style=\"background:#FFFF33;\">".number_format($ex_plant_rate,2)."</td>
				<td align=\"right\">".number_format($freight_charge_plant,2)."</td>
				<td align=\"right\" style=\"background:#00CC66;\">".number_format($FOR_plant_rate,2)."</td>
				<td align=\"right\">".number_format($depot_cost,2)."</td>
				<td align=\"right\">".number_format($freight_cost,2)."</td>
				<td align=\"right\" style=\"background:#6699FF;\">".number_format($Ex_depot_rate,2)."</td>
				<td align=\"right\">".number_format($freight_charge_depot,2)."</td>
				<td align=\"right\" style=\"background:#FF9900;\">".number_format($FOR_depot_rate,2)."</td>
		</tr>";
		$count++;
		}
	}
 }
}
	if($count ==1){
		echo "<tr><td colspan='15' align='center'><strong><font color=\"red\">No rates found</font></strong></td></tr>";
	}
	echo "</tbody></table></div>";
	//echo "</div>";
	/*echo "<div style=\"width:60%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
    <input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"ExportToExcel('');\" ></div>";*/
}
else
{
	echo "<strong><font color=\"red\">No records</font></strong>";
}
//}
?>
