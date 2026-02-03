<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database");

$the_oils = $_REQUEST['the_oils'];
$process_val=$_REQUEST['process_val'];

$sqlproductlist="SELECT DISTINCT PM.prod_desc,PM.prod_code FROM product_master PM,process_cost PC,loose_oilrate_formulation LF WHERE PM.dns_prod_code=LF.prod_code AND LF.oils=PC.oil_type AND LF.percentile_calc='N' AND PC.oil_type='".$the_oils."' ORDER BY PM.prod_desc ASC";
$rsproductlist=mysqli_query($link,$sqlproductlist);
$price_list_string='If Procss Cost changed done . Rate of the related Items will be as mentioned below,,';
while($rowproductlist=mysqli_fetch_assoc($rsproductlist))
{
	$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$the_oils."' ORDER BY datetime DESC LIMIT 0,1";
	$rsprocesscost=mysqli_query($link,$sqlprocesscost);
	$rowprocesscost=mysqli_fetch_assoc($rsprocesscost);
	$process_cost=$rowprocesscost['process_cost'];
	
	$sqlmrp="SELECT sale_rate,basic_rate,process_cost_ton,process_cost,packing_realization,margin_cost FROM sauda_mrp WHERE 
	product_code='".$rowproductlist['prod_code']."' ORDER BY download_time DESC LIMIT 0,1";
	$rsmrp=mysqli_query($link,$sqlmrp);
	$rowmrp=mysqli_fetch_assoc($rsmrp);
	$process_cost_ton=$rowmrp['process_cost_ton'];
	$process_cost_ton=($process_cost_ton-$process_cost)+$process_val;
	
	$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,UOM1,pack_size,packing_realization,UOM4 
									FROM product_master WHERE prod_code='".$rowproductlist['prod_code']."'";
	$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
	$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
	$conversion_factor=$rowconversionfactor['conversion_factor'];
	$conversion_factor_two=$rowconversionfactor['conversion_factor_two'];
	
	$final_process_cost=$process_cost_ton*$conversion_factor_two;
	$final_rate=$rowmrp['basic_rate']+$final_process_cost+$rowmrp['packing_realization']+$rowmrp['margin_cost'];
	if($rowconversionfactor['pack_size']=='BP')
	{
		$final_rate=round($final_rate,0);
	}
	if($rowconversionfactor['pack_size']=='CP')
	{
		$final_rate=round($final_rate,1);
	}
	
	
	$price_list_string.=$rowproductlist['prod_desc']. '-'. $final_rate.',';
}
echo $price_list_string;
mysqli_close($link);
?>