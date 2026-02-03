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
$rsgroupname=mysqli_query($link,$sqlgroupname);
$rowgroupname=mysqli_fetch_assoc($rsgroupname);
$product_group_name=$rowgroupname['product_group_name'];

$sqlstate="SELECT state FROM state_master WHERE state=".$state."";
$rsstate=mysqli_query($link,$sqlstate);
$rowstate=mysqli_fetch_assoc($rsstate);
$state_name=$rowstate['state'];
$routearray=explode(",",$routecode);
$capacityarray=explode(",",$capacityval);

if(strpos($routecode,',')!=false){
	$route_name='All';
}
else
{
	$sqlroutename="SELECT route_name FROM route_master WHERE route_code=".$routecode."";
	$rsroutename=mysqli_query($link,$sqlroutename);
	$rowroutename=mysqli_fetch_assoc($rsroutename);
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
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
if($curdateserver==$from_date_check)
{
	$mrp_condition=" AND MR.acedns='Y'";
	$industrial_condition=" AND acedns='Y'";
}
else
{
	$mrp_condition="";
	$industrial_condition="";
}

$sql_product_details = "SELECT * FROM (SELECT DISTINCT PM.dns_prod_code,PM.prod_desc,PM.conversion_factor,PM.packing_realization,
						PM.conversion_factor_two,PM.product_group_code,MR.process_cost,MR.basic_rate,PM.pack_size,MR.sale_rate,PM.prod_code,MR.parent_child 
						FROM product_master PM, sauda_mrp MR
						WHERE PM.product_group_code IN(".$product_group_code.") AND PM.prod_code = MR.product_code 
						AND PM.acedns='Y' AND PM.black_list='N' AND SUBSTRING(MR.create_date,1,10)='".$from_date_check."'  ".$mrp_condition." ".$prod_condition." 
						ORDER BY MR.create_date DESC) AS SAT GROUP BY 1 ORDER BY 2 ASC";
//exit();						   
$res_product_details = mysqli_query($link,$sql_product_details);
$total_rows = mysqli_num_rows($res_product_details);

if($total_rows >0){
	    $header="Sale Rate Report - ".$from_date." - ".$product_group_name." - ".$state_name." - ".$route_name." - ".$transport_mode." - ".$capacityval."\n";
	  	$header.= "SI"."\t"."Route"."\t"."Capacity"."\t"."Load Distribution"."\t"."Actual Freight"."\t"."Oil group"."\t"."Material Code"."\t"."Material Description"."\t"."Material Cost"."\t"."Process Cost"."\t"."Packing Cost"."\t"."Labour Cost"."\t"."Extra Cost"."\t"."Packing Realization"."\t"."Margin cost"."\t"."Ex Plant Rate"."\t"."Plant to Route Freight"."\t"."Sale Rate - FOR Plant";
	  	$count = 1;
foreach($routearray as $route)
{
    $sqlroutenamedetails="SELECT route_name FROM route_master WHERE route_code=".$route."";
	$rsroutenamedetails=mysqli_query($link,$sqlroutenamedetails);
	$rowroutenamedetails=mysqli_fetch_assoc($rsroutenamedetails);
	$route_name_details=$rowroutenamedetails['route_name'];

	foreach($capacityarray as $capacity)
	{
	$res_product_details = mysqli_query($link,$sql_product_details);
	$depot_array=array();
	while($row_product_details = mysqli_fetch_assoc($res_product_details))
	{
		$conversion_one=$row_product_details['conversion_factor'];
		$conversion_two=$row_product_details['conversion_factor_two'];
		$dns_prod_code=$row_product_details['dns_prod_code'];
		$prod_desc=$row_product_details['prod_desc'];
		$product_group_code=$row_product_details['product_group_code'];
		$basic_rate=$row_product_details['basic_rate'];
		$process_cost=$row_product_details['process_cost'];
		$packing_realization=$row_product_details['packing_realization'];
		/*$sqlstatecode="SELECT dns_state_code FROM state_master WHERE state='".$branch_state."'";
		$rsstatecode=mysqli_query($link,$sqlstatecode);
		$rowstatecode=mysqli_fetch_assoc($rsstatecode);*/
		$sale_rate=$rowpublishedproddetails['sale_rate'];
		$prod_code=$row_product_details['prod_code'];
		$parent_child=$row_product_details['parent_child'];
		$pack_size=$row_product_details['pack_size'];
		
		$sqlproductgroupname="SELECT product_group_name,formulation,vertical_value FROM product_group_master WHERE 
								product_group_code='".$product_group_code."'";
		$rsproductgroupname=mysqli_query($link,$sqlproductgroupname);
		$rowproductgroupname=mysqli_fetch_assoc($rsproductgroupname);
		$product_group_name=$rowproductgroupname['product_group_name'];
		$is_formulation=$rowproductgroupname['formulation'];
		$vertical_value=$rowproductgroupname['vertical_value'];

		$sqlpackingprodwise="SELECT packing_cost,labour_cost,extra_cost FROM packing_master WHERE 
		dns_prod_code='".$row_product_details['dns_prod_code']."'  AND datetime <='".$from_date_final."'ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
		$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
		$packing_cost=$rowpackingprodwise['packing_cost'];
		$labour_cost=$rowpackingprodwise['labour_cost'];
		$extra_cost=$rowpackingprodwise['extra_cost'];
		$extra_cost=((($packing_cost+$labour_cost)*10)/100)+$extra_cost;
		
		if($packing_cost=='')  $packing_cost=0;
		$sqlqtytruckload="SELECT qty_truck_load FROM load_distribution WHERE transport_mode='".$transport_mode."' 
						 AND truck_load='".$capacity."' AND prod_code='".$row_product_details['dns_prod_code']."' AND datetime <='".$from_date_final."' 
						 ORDER BY datetime DESC LIMIT 0,1";
		$rsqtytruckload=mysqli_query($link,$sqlqtytruckload);
		$rowqtytruckload=mysqli_fetch_assoc($rsqtytruckload);
		$qty_truck_load=floor($rowqtytruckload['qty_truck_load']);
		
		$sqlfreihgt="SELECT freight FROM branch_route_freight WHERE branch_code='B0001' 
						AND route_code=".$route." AND acedns='Y' AND transport_mode='".$transport_mode."' 
					AND capacity='".$capacity."'";			
		$rsfreight=mysqli_query($link,$sqlfreihgt);
		$rowfreight=mysqli_fetch_assoc($rsfreight);
		$freight=$rowfreight['freight'];			
		$freight_charge_plant=ceil(($freight/$qty_truck_load));
		$freight_charge_depot=0;
		
		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							 AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
		$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
		$margin_cost=$rowmargincostprodwise['margin_cost'];
		if($margin_cost=='')  $margin_cost=0;
		if($parent_child=='child') $margin_cost=0;
		//echo $material_cost;
		//$ex_plant_rate=$basic_rate+$process_cost+$packing_cost+$labour_cost+$extra_cost;
		$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$prod_code."' AND 
								SUBSTRING(create_date,1,10)='".$from_date_check."'  ".$industrial_condition." ORDER BY create_date DESC LIMIT 0,1";
		$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);
		$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);
		$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);
		if($cntselindustrialrate >0)
		{
			$ex_plant_rate=$rowselindustrialrate['sale_rate'];
			$basic_rate=0;
			$process_cost=0;
			$packing_realization=0;
			$packing_cost=0;
			$labour_cost=0;
			$extra_cost=0;
			$FOR_plant_rate=$ex_plant_rate+$freight_charge_plant;
		}
		else 
		{
			$ex_plant_rate=$basic_rate+$process_cost+$packing_realization+$margin_cost;
			if($freight_charge_plant==0)
			{
				$FOR_plant_rate=0;
			}
			else
			{
				//$FOR_plant_rate=$basic_rate+$process_cost+$packing_cost+$labour_cost+$extra_cost+$freight_charge_plant;
				$FOR_plant_rate=$basic_rate+$process_cost+$packing_realization+$margin_cost+$freight_charge_plant;
			}
		}
		if($pack_size=='BP')
		{
			$ex_plant_rate=round($ex_plant_rate,0);
		}
		if($pack_size=='CP')
		{
			$ex_plant_rate=round($ex_plant_rate,1);
		}
		//$ex_plant_rate=round($ex_plant_rate,2);
		//$FOR_plant_rate=round($FOR_plant_rate,2);
		if($pack_size=='BP')
		{
			$FOR_plant_rate=round($FOR_plant_rate,0);
		}
		if($pack_size=='CP')
		{
			$FOR_plant_rate=round($FOR_plant_rate,1);
		}

		$table_data .= $count."\t".$route_name_details."\t".$capacity."\t".number_format($qty_truck_load,2)."\t".number_format($freight,2)."\t".$product_group_name."\t".$dns_prod_code."\t".$prod_desc."\t".number_format($material_cost,2)."\t".number_format($process_cost,2)."\t".number_format($packing_cost,2)."\t".number_format($labour_cost,2)."\t".number_format($extra_cost,2)."\t".number_format($packing_realization,2)."\t".number_format($margin_cost,2)."\t".number_format($ex_plant_rate,2)."\t".number_format($freight_charge_plant,2)."\t".number_format($FOR_plant_rate,2)."\n";

		$count++;
	}
 }
}
if($count ==1){
		$table_data="No rates found.";
	}
}
else
{
	$table_data="No records";
}
if($table_data !=''){	
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=Rate destination wise.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
	echo ucwords($header)."\n".$table_data;
}
else
{
	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
}
//}
mysqli_close($link);
?>
