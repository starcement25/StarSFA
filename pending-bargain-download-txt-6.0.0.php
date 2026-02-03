<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$customer_code=$_REQUEST['customer_code'];
$product_details_parts=explode("$",$product_details);
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$countdata=0;
    $sqlquery="SELECT DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date,PM.prod_desc,DM.qty,DM.sale_rate,DM.freight_charge,DM.additional_TD,DM.additional_premium,PM.prod_code,PM.UOM1,DM.sauda_no FROM 
				DO_master DM,product_master PM WHERE DM.sku_code=PM.prod_code AND DM.status='no' 
				 AND customer_code='".$customer_code."' AND DM.is_approved!='reject' ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,PM.prod_desc ASC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count > 0)
	{
		while($rowpendingbargain = mysqli_fetch_assoc($result))
		{
			$bargain_date=$rowpendingbargain['bargain_date'];
			$prod_desc=$rowpendingbargain['prod_desc'];
			$prod_code=$rowpendingbargain['prod_code'];
			$UOM1=$rowpendingbargain['UOM1'];
			$qty=$rowpendingbargain['qty'];
			$sale_rate=$rowpendingbargain['sale_rate'];
			$freight_charge=$rowpendingbargain['freight_charge'];
			$addiional_TD=$rowpendingbargain['additional_TD'];
			$addiional_premium=$rowpendingbargain['additional_premium'];
			$sauda_no=$rowpendingbargain['sauda_no'];
			if($freight_charge=='')  $freight_charge=0;
			
			$sqlTD="SELECT TD from sauda_details WHERE sauda_no='".$sauda_no."' AND sku_code='".$prod_code."'";
			$rsTD=mysqli_query($link,$sqlTD);
			$rowTD=mysqli_fetch_assoc($rsTD);
			$TD=$rowTD['TD'];
			if($TD=='') $TD=0;
			$sale_rate=(($sale_rate+$freight_charge+$addiional_premium)+$TD)-$addiional_TD;// As TD is -value so addition
			
			if(strtoupper($UOM1)=='CASE')
			{
			    $sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE prod_code='".$prod_code."'";
				$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
				$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);
				$conversion_factor_two=$rowconversionfactor['conversion_factor_two'];
				$pending_qty=$qty*$conversion_factor_two;
				$pending_qty=round($pending_qty,3);
				$pending_qty_show=$qty.'/'.$pending_qty;
			}
			else
			{
				$pending_qty=$qty;
				$pending_qty_show=$pending_qty;
			}
			
			if($qty >0)
			{
				$contents  = (($bargain_date!='')?$bargain_date: ' ')."^";
				$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
				$contents  .= (($pending_qty_show!='')?$pending_qty_show: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ');
				$linecontents  .= $contents."\n";
				$countdata++;
			}
		}
	$contentsrowcolumn=$countdata.'¥'.'4';
	$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
		
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/pending-bargain-download-txt-6.0.0.php?nick_name=$nick_name&customer_code=$customer_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=pending_bargain.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
