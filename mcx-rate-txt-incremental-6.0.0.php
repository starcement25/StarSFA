<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	/*$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND MRP.vertical_value IN ('.$emp_vertical_value.')';*/
    $sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',MRP.vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
}
else
{
	$condition_one="";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

	$sqlquery="SELECT MRP.product_code,MRP.mrp_code,MRP.sale_rate_open,MRP.branch_code,MRP.basic_rate_open,MRP.primary_freight,MRP.depot_cost,
				MRP.sale_rate_close,MRP.basic_rate_close 
	FROM mcx_rate MRP WHERE acedns='Y'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'9';
	if($count>0){
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowprice = mysqli_fetch_assoc($result))
		{
			/*$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$rowprice['product_code']."' AND acedns='Y'";
			$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);
			$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);
			$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);
			if($cntselindustrialrate >0)
			{
				$sale_rate_open=$rowselindustrialrate['sale_rate'];
			}
			else*/ 
			$sale_rate_open=$rowprice['sale_rate_open'];
			
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($sale_rate_open!='')?$sale_rate_open: ' ')."^";
			$contents  .= (($rowprice['branch_code']!='')?$rowprice['branch_code']: ' ')."^";
			$contents  .= (($rowprice['basic_rate_open']!='')?$rowprice['basic_rate_open']: ' ')."^";
			$contents  .= (($rowprice['primary_freight']!='')?$rowprice['primary_freight']: ' ')."^";
			$contents  .= (($rowprice['depot_cost']!='')?$rowprice['depot_cost']: ' ')."^";
			$contents  .= (($rowprice['sale_rate_close']!='')?$rowprice['sale_rate_close']: ' ')."^";
			$contents  .= (($rowprice['basic_rate_close']!='')?$rowprice['basic_rate_close']: ' ');
			$linecontents  .= $contents."\n";
		}
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
			$datacontents = '0'.'¥'.'9';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/mcx-rate-txt-incremental-6.0.0.php.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/mrp-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving //Users position
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=mrp.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
