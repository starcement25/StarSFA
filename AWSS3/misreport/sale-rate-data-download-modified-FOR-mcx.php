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
if(strtoupper($transport_mode)=='TRUCK')
{
	$prod_condition=" AND PM.UOM1 <>'Loose'";
}
if(strtoupper($transport_mode)=='TANKER')
{
	$prod_condition="AND PM.UOM1='Loose'";
}

$sql_product_details = "SELECT DISTINCT PM.dns_prod_code,PM.prod_desc,PM.conversion_factor,PM.packing_realization,
						PM.conversion_factor_two,PM.product_group_code,MR.process_cost,MR.basic_rate_open,MR.basic_rate_close,PM.pack_size
						FROM product_master PM, mcx_rate MR
						WHERE PM.product_group_code IN(".$product_group_code.") AND PM.prod_code = MR.product_code 
						AND PM.acedns='Y' AND PM.black_list='N' AND SUBSTRING(MR.create_date,1,10)='".$from_date_check."' ".$prod_condition." 
						ORDER BY PM.prod_desc ASC";
//exit();						   
$res_product_details = mysql_query($sql_product_details);
$total_rows = mysql_num_rows($res_product_details);

if($total_rows >0){
    echo '<table class="border" width="98%" border="1"   align="center">
		<thead>
      <tr>
        <td colspan="20" class="TDHEAD" align="left" ><b>Sale Rate Report MCX - '.$from_date.' - '.$product_group_name.' - '.$state_name.' - '.$route_name.' - '.$transport_mode.' - '.$capacityval.'</b></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td width="4%">SI</td>
		<td width="5%">Route</td>
		<td width="4%">Capacity</td>
		<td width="5%">Load Distribution</td>
		<td width="5%">Actual Freight</td>
		<td width="5%">Oil group</td>
        <td width="5%">Material Code</td>
        <td width="7%">Material Description</td>
        <td width="5%">Material Cost Open (MCX)</td>
		<td width="5%">Material Cost Close (MCX)</td>
		<td width="5%">Process Cost</td>
        <td width="5%">Packing Cost</td>
		<td width="5%">Labour Cost</td>
		<td width="5%">Extra Cost</td>
		<td width="5%">Packing Realization</td>
		<td width="5%">Ex Plant Rate Open</td>
		<td width="5%">Ex Plant Rate Close</td>
		<td width="5%">Plant to Route Freight</td>
		<td width="5%">Sale Rate - FOR Plant Open</td>
		<td width="5%">Sale Rate - FOR Plant Close</td>
      </tr></thead>
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
		$dns_prod_code=$row_product_details['dns_prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$product_group_code=$row_product_details['product_group_code'];
		$basic_rate_open=$row_product_details['basic_rate_open'];
		$basic_rate_close=$row_product_details['basic_rate_close'];
		$process_cost=$row_product_details['process_cost'];
		$packing_realization=$row_product_details['packing_realization'];
		$pack_size=$row_product_details['pack_size'];
		/*$sqlstatecode="SELECT dns_state_code FROM state_master WHERE state='".$branch_state."'";
		$rsstatecode=mysql_query($sqlstatecode);
		$rowstatecode=mysql_fetch_array($rsstatecode);*/
		//$dns_state_code=$row_product_details['dns_state_code'];
		
		$sqlproductgroupname="SELECT product_group_name,formulation,vertical_value FROM product_group_master WHERE 
								product_group_code='".$product_group_code."'";
		$rsproductgroupname=mysql_query($sqlproductgroupname);
		$rowproductgroupname=mysql_fetch_array($rsproductgroupname);
		$product_group_name=$rowproductgroupname['product_group_name'];
		$is_formulation=$rowproductgroupname['formulation'];
		$vertical_value=$rowproductgroupname['vertical_value'];

		$sqlpackingprodwise="SELECT packing_cost,labour_cost,extra_cost FROM packing_master WHERE 
		dns_prod_code='".$row_product_details['dns_prod_code']."'  AND datetime <='".$from_date_final."'ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysql_query($sqlpackingprodwise);
		$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
		$packing_cost=$rowpackingprodwise['packing_cost'];
		$labour_cost=$rowpackingprodwise['labour_cost'];
		$extra_cost=$rowpackingprodwise['extra_cost'];
		//$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		//$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		
		if($packing_cost=='')  $packing_cost=0;
		
		/*$sqldepotcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		$freight=$rowdepotcostprodwise['freight'];

		if($depot_cost=='')  $depot_cost=0;
		if($freight=='')      $freight=0;*/
		/*$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."'  AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		if($depot_cost=='')  $depot_cost=0;*/
		
		/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."'  AND datetime <='".$from_date_final."' AND 
							vertical_value='".$_SESSION['vertical_value']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
		$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
		$margin_cost=$rowmargincostprodwise['margin_cost'];
		if($margin_cost=='')  $margin_cost=0;*/
		/*$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
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
		}*/

		$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
						 AND truck_load='".$capacity."' AND prod_code='".$row_product_details['dns_prod_code']."' AND datetime <='".$from_date_final."' 
						 ORDER BY datetime DESC LIMIT 0,1";
		$rsqtytruckload=mysql_query($sqlqtytruckload);
		$rowqtytruckload=mysql_fetch_array($rsqtytruckload);
		//$qty_truck_load=$rowqtytruckload['qty_truck_load'];
		$qty_truck_load= floor($rowqtytruckload['qty_truck_load']);
		/*if($is_plant=='yes')
		{*/
			/*$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='".$row_product_details['branch_code']."' 
						AND route_code=".$route." AND acedns='Y' AND transport_mode='".$transport_mode."' 
						AND capacity='".$capacity."' AND state_code=".$state."";*/
			$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='B0001' 
						AND route_code=".$route." AND acedns='Y' AND transport_mode='".$transport_mode."' 
						AND capacity='".$capacity."'";			
			$rsfreight=mysql_query($sqlfreihgt);
			$rowfreight=mysql_fetch_array($rsfreight);
			$freight=$rowfreight['freight'];			
			$freight_charge_plant=ceil(($freight/$qty_truck_load));
			$freight_charge_depot=0;
		/*}
		else
		{
			$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='B0001' 
						AND route_code=".$route." AND acedns='Y' AND state_code=".$state."";
			$rsfreight=mysql_query($sqlfreihgt);
			$rowfreight=mysql_fetch_array($rsfreight);
			$freight=$rowfreight['freight'];
			$freight_charge_depot=round(($freight/$qty_truck_load),2);
			$freight_charge_plant=0;
		}*/
				 
		/*$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$material_code[$i]."' AND datetime <='".$sauda_date_time."' 
							ORDER BY datetime DESC LIMIT 0,1";	*/										
		//$rsqtytruckload=mysql_query($sqlqtytruckload);
				
		//$ex_plant_rate=$basic_rate+$process_cost+$packing_cost+$labour_cost+$extra_cost;
		if($basic_rate_open==0)
		{
			$ex_plant_rate_open=0;
		}
		else
		{
			$ex_plant_rate_open=$basic_rate_open+$process_cost+$packing_realization;
		}
		if($basic_rate_close==0)
		{
			$ex_plant_rate_close=0;
		}
		else
		{
			$ex_plant_rate_close=$basic_rate_close+$process_cost+$packing_realization;
		}
		if($pack_size=='BP')
		{
			$ex_plant_rate_open=round($ex_plant_rate_open,0);
			$ex_plant_rate_close=round($ex_plant_rate_close,0);
		}
		if($pack_size=='CP')
		{
			$ex_plant_rate_open=round($ex_plant_rate_open,1);
			$ex_plant_rate_close=round($ex_plant_rate_close,1);
		}
		if($freight_charge_plant==0)
		{
			$FOR_plant_rate=0;
		}
		else
		{
			//$FOR_plant_rate=$basic_rate+$process_cost+$packing_cost+$labour_cost+$extra_cost+$freight_charge_plant;
			if($basic_rate_open==0)
			{
				$FOR_plant_rate_open=0;
			}
			else
			{
				$FOR_plant_rate_open=$ex_plant_rate_open+$freight_charge_plant;
			}
			if($basic_rate_close==0)
			{
				$FOR_plant_rate_close=0;
			}
			else
			{
				$FOR_plant_rate_close=$ex_plant_rate_close+$freight_charge_plant;
			}
		}
		if($pack_size=='BP')
		{
			$FOR_plant_rate_open=round($FOR_plant_rate_open,0);
			$FOR_plant_rate_close=round($FOR_plant_rate_close,0);
		}
		if($pack_size=='CP')
		{
			$FOR_plant_rate_open=round($FOR_plant_rate_open,1);
			$FOR_plant_rate_close=round($FOR_plant_rate_close,1);
		}
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
		
		if($capacity==0) $capacity='-';
		if($qty_truck_load==0) {
		  $qty_truck_load='-';
		}
		else $qty_truck_load=number_format($qty_truck_load,2);
		if($freight==0) { $freight='-';}
		else 			$freight=number_format($freight,2);
		if($basic_rate_open==0) {$basic_rate_open='-';}
		else 			$basic_rate_open=number_format($basic_rate_open,2);
		if($basic_rate_close==0) {$basic_rate_close='-';}
		else 			$basic_rate_close=number_format($basic_rate_close,2);
		if($process_cost==0) {$process_cost='-';}
		else 			$process_cost=number_format($process_cost,2);
		if($packing_cost==0) {$packing_cost='-';}
		else 			$packing_cost=number_format($packing_cost,2);
		if($labour_cost==0) {$labour_cost='-';}
		else				$labour_cost=number_format($labour_cost,2);
		if($extra_cost==0) { $extra_cost='-';}
		else				$extra_cost=number_format($extra_cost,2);
		if($packing_realization==0) { $packing_realization='-';}
		else				$packing_realization=number_format($packing_realization,2);
		if($ex_plant_rate_open==0) {$ex_plant_rate_open='-';}
		else				$ex_plant_rate_open=number_format($ex_plant_rate_open,2);
		if($ex_plant_rate_close==0) {$ex_plant_rate_close='-';}
		else				$ex_plant_rate_close=number_format($ex_plant_rate_close,2);
		if($freight_charge_plant==0) {$freight_charge_plant='-';}
		else				$freight_charge_plant=number_format($freight_charge_plant,2);
		if($FOR_plant_rate_open==0) {$FOR_plant_rate_open='-';}
		else				$FOR_plant_rate_open=number_format($FOR_plant_rate_open,2);
	    if($FOR_plant_rate_close==0) {$FOR_plant_rate_close='-';}
		else				$FOR_plant_rate_close=number_format($FOR_plant_rate_close,2);			
		if($freight_charge_plant!='-')
		{
		echo "<tr>
				<td>".$count."</td>
				<td >".strtoupper($route_name_details)."</td>
				<td align=\"right\">".$capacity."</td>
				<td align=\"right\">".$qty_truck_load."</td>
				<td align=\"right\">".$freight."</td>
				<td >".$product_group_name."</td>
				<td>".$dns_prod_code."</td>
				<td >".$prod_desc."</td>
				<td align=\"right\">".$basic_rate_open."</td>
				<td align=\"right\">".$basic_rate_close."</td>
				<td align=\"right\">".$process_cost."</td>
				<td align=\"right\">".$packing_cost."</td>
				<td align=\"right\">".$labour_cost."</td>
				<td align=\"right\">".$extra_cost."</td>
				<td align=\"right\">".$packing_realization."</td>
				<td align=\"right\" style=\"background:#FFFF33;\">".$ex_plant_rate_open."</td>
				<td align=\"right\" style=\"background:#FFFF33;\">".$ex_plant_rate_close."</td>
				<td align=\"right\">".$freight_charge_plant."</td>
				<td align=\"right\" style=\"background:#00CC66;\">".$FOR_plant_rate_open."</td>
				<td align=\"right\" style=\"background:#00CC66;\">".$FOR_plant_rate_close."</td>
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