<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
    $sqlquery="SELECT DISTINCT PBR.prod_code,PBR.batch_no,PBR.batch_date,PBR.in_qty,PBR.out_qty,PBR.acedns  
				FROM DO_transaction DT,batch_wise_stock PBR WHERE PBR.prod_code=DT.sku_code AND DT.DO_status 
					IN('weighbridge_in') AND (PBR.in_qty - PBR.out_qty) > 0";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$dns_route_code='';
		while($rowproductbatch = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowproductbatch['prod_code']!='')?$rowproductbatch['prod_code']: ' ')."^";
			$contents  .= (($rowproductbatch['batch_no']!='')?$rowproductbatch['batch_no']: ' ')."^";
			$contents  .= (($rowproductbatch['batch_date']!='')?$rowproductbatch['batch_date']: ' ')."^";
			$contents  .= (($rowproductbatch['in_qty']!='')?$rowproductbatch['in_qty']: ' ')."^";
			$contents  .= (($rowproductbatch['out_qty']!='')?$rowproductbatch['out_qty']: ' ')."^";
			$contents  .= (($rowproductbatch['acedns']!='')?$rowproductbatch['acedns']: ' ');
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'6';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'6';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-product-batch-download.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=Product_batch.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
