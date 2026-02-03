<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code_value=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code_value);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code_value."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if($emp_code_value!='C0007'){
 	$sqlquery="SELECT * FROM mis_transaction_log WHERE ".$emp_hierarchy_condition." ".$login_condition."";
 }
 else
 {
	$sqlquery="SELECT * FROM mis_transaction_log WHERE 1  ".$login_condition."";
 }
 

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$contentsrowcolumn=$count.'¥'.'16';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowmislog = mysqli_fetch_assoc($result))
		{
			$branch_code=$rowmislog['branch_code'];
			$rds_code=$rowmislog['rds_code'];
			$emp_code=$rowmislog['emp_code'];
			$transaction_date=$rowmislog['trans_date'];
			$order_no=$rowmislog['trans_id'];
			$customer_code=$rowmislog['customer_code'];
			$customer_name=$rowmislog['customer_name'];
			$prod_code=$rowmislog['sku_code'];
			$qty=$rowmislog['qty'];
			$sale_rate=$rowmislog['sale_rate'];
			$amount=$rowmislog['amount'];
			$VAT=$rowmislog['VAT'];
			$TD='';
			$trans_type=$rowmislog['trans_type'];
			$d_instruction=preg_replace('/\s+/', '',$rowmislog['d_instruction']);
			//$d_instruction='';
			$group_code=$rowmislog['group_code'];
				
				$contents  = (($branch_code!='')?$branch_code: ' ')."^";
				$contents  .= (($rds_code!='')?$rds_code: ' ')."^";
				$contents  .= (($emp_code!='')?$emp_code: ' ')."^";
				$contents  .= (($transaction_date!='')?$transaction_date: ' ')."^";
				$contents  .= (($order_no!='')?$order_no: ' ')."^";
				$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($customer_name!='')?$customer_name: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ')."^";
				$contents  .= (($amount!='')?$amount: ' ')."^";
				$contents  .= (($VAT!='')?$VAT: ' ')."^";
				$contents  .= (($TD!='')?$TD: ' ')."^";
				$contents  .= (($trans_type!='')?$trans_type: ' ')."^";
				$contents  .= (($d_instruction!='')?$d_instruction: ' ')."^";
				$contents  .= (($group_code!='')?$group_code: ' ');
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
			$datacontents = '0'.'¥'.'16';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/mis-transaction-log-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code_value&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/mis-transaction-log-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code_value&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving 
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
	header("Content-Disposition: attachment; filename=mis_transaction_log.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
