<?php
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
	foreach($emp_vertical_value_array as $emp_vertical_array_val)
	{
		$final_emp_vertical_value_array[]=ltrim($emp_vertical_array_val);
	}
	$condition_one=" AND (";
	$condition_two='';
	foreach($final_emp_vertical_value_array as $emp_vertical_values)
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
/*if(strtoupper($nick_name)=='UCLINDIA')
{
	$acedns_conditions=" AND acedns='Y'";
}
else
{
	$acedns_conditions="";
}*/
$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if(branch_wise_mrp=='yes')
{
   $sqlquery="SELECT DISTINCT MRP.* FROM mrp MRP,employee_master EM WHERE FIND_IN_SET(MRP.branch_code,EM.branch_code)
			AND EM.emp_code='".$emp_code."' ".$acedns_conditions." ".$condition_one." ".$login_condition."";
  //$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";			
}
else
{
	$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";
}
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
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($rowprice['mrp']!='')?round($rowprice['mrp'],2): ' ')."^";
			$contents  .= (($rowprice['sale_rate']!='')?round($rowprice['sale_rate'],2): ' ')."^";
			$contents  .= (($rowprice['UOM']!='')?$rowprice['UOM']: ' ')."^";
			$contents  .= (($rowprice['branch_code']!='')?$rowprice['branch_code']: ' ')."^";
			$contents  .= (($rowprice['destination_code']!='')?$rowprice['destination_code']: ' ')."^";
			$contents  .= (($rowprice['order_type']!='')?$rowprice['order_type']: ' ')."^";
			$contents  .= (($rowprice['acedns']!='')?$rowprice['acedns']: ' ');
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
	$url = "http://www.acedns.in/acednsproduct/mrp-txt-incremental-6.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
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
