<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ASL");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database");

$the_oils = $_REQUEST['the_oils'];
$process_val=$_REQUEST['process_val'];

$sqlproductlist="SELECT DISTINCT PM.prod_desc,PM.prod_code FROM product_master PM,process_cost PC,loose_oilrate_formulation LF WHERE PM.dns_prod_code=LF.prod_code AND LF.oils=PC.oil_type AND LF.percentile_calc='N' AND PC.oil_type='".$the_oils."' ORDER BY PM.prod_desc ASC";
$rsproductlist=mysql_query($sqlproductlist);
$price_list_string="<html><body><table><tr><td colspan='3'>Procss Cost changed done . Rate of the related Items will be as mentioned below.</td></tr>
				<tr><td colspan='3'></td></tr>";
while($rowproductlist=mysql_fetch_array($rsproductlist))
{
	$sqlprocesscost="SELECT process_cost FROM process_cost WHERE oil_type='".$the_oils."' ORDER BY datetime DESC LIMIT 0,1";
	$rsprocesscost=mysql_query($sqlprocesscost);
	$rowprocesscost=mysql_fetch_array($rsprocesscost);
	$process_cost=$rowprocesscost['process_cost'];
	
	$sqlmrp="SELECT sale_rate,basic_rate,process_cost_ton,process_cost,packing_realization,margin_cost FROM sauda_mrp WHERE 
	product_code='".$rowproductlist['prod_code']."' ORDER BY download_time DESC LIMIT 0,1";
	$rsmrp=mysql_query($sqlmrp);
	$rowmrp=mysql_fetch_array($rsmrp);
	$process_cost_ton=$rowmrp['process_cost_ton'];
	$process_cost_ton=($process_cost_ton-$process_cost)+$process_val;
	
	$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two,prod_desc,UOM1,pack_size,packing_realization,UOM4 
									FROM product_master WHERE prod_code='".$rowproductlist['prod_code']."'";
	$rsconversionfactor=mysql_query($sqlconversionfactor);
	$rowconversionfactor=mysql_fetch_array($rsconversionfactor);
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
	
	
		$price_list_string.="<tr><td>".$rowproductlist['prod_desc'].  "</td><td>-</td><td>".$final_rate."</td></tr>";

}
$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$date.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second."\n";
$mailto='abhishekd@coral.in,dipankarc@coral.in';
$spam_filter='-finfo@salesmpower.acedns.in';
$subject='ASL Process Cost changed for - '.$the_oils ." on ".$contentsdatetime;
$body=$price_list_string."</table></body></html>";
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= "From: salesmpower<info@salesmpower.acedns.in> \r\n" .
						"Reply-To: info@salesmpower.acedns.in \r\n" .
						'X-Mailer: PHP/' . phpversion();
			
mail($mailto, $subject, $body, $headers,$spam_filter);
mysql_close($link);
?>