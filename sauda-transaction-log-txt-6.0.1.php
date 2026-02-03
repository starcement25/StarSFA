<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
if(gmdate('m',strtotime('+330 minute')) >='4')
{
	$financial_from_date=gmdate('Y',strtotime('+330 minute'))."-04-01";
	$financial_to_date=(gmdate('Y',strtotime('+330 minute'))+1)."-03-31";
}
else
{
	$financial_from_date=(gmdate('Y',strtotime('+330 minute'))-1)."-04-01";
	$financial_to_date=gmdate('Y',strtotime('+330 minute'))."-03-31";
}

if($emp_code_value!='C0007'){
 	$sqlquery="SELECT * FROM sauda_transaction_log WHERE ".$emp_hierarchy_condition." AND 
				SUBSTRING(sauda_date,1,10)>='".$financial_from_date."' AND SUBSTRING(sauda_date,1,10)<='".$financial_to_date."' ".$login_condition."";
 }
 else
 {
   $sqlquery="SELECT * FROM sauda_transaction_log WHERE SUBSTRING(sauda_date,1,10)>='".$financial_from_date."' 
   					AND SUBSTRING(sauda_date,1,10)<='".$financial_to_date."'  ".$login_condition."";
 }
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$contentsrowcolumn=$count.'¥'.'18';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowsaudatransactionlog = mysqli_fetch_assoc($result))
		{
			$branch_code=$rowsaudatransactionlog['branch_code'];
			$broker_id=$rowsaudatransactionlog['broker_id'];
			$emp_code_transaction=$rowsaudatransactionlog['emp_code'];
			$sauda_date=$rowsaudatransactionlog['sauda_date'];
			$sauda_no=$rowsaudatransactionlog['sauda_no'];
			$customer_code=$rowsaudatransactionlog['customer_code'];
			$prod_code=$rowsaudatransactionlog['prod_code'];
			$qty=$rowsaudatransactionlog['qty'];
			$convert_qty_one=$rowsaudatransactionlog['convert_qty_one'];
			$convert_qty_two=$rowsaudatransactionlog['convert_qty_two'];
			$sale_rate=$rowsaudatransactionlog['sale_rate'];
			$TD=$rowsaudatransactionlog['TD'];
			$premium=$rowsaudatransactionlog['premium'];
			$freight_charge=$rowsaudatransactionlog['freight_charge'];
			$amount=$rowsaudatransactionlog['amount'];
			
			$sqlstatezone="SELECT state,zone FROM employee_master WHERE emp_code='".$emp_code_transaction."'";
			$rsstatezone=mysqli_query($link,$sqlstatezone);
			$rowstatezone=mysqli_fetch_assoc($rsstatezone);
			$state=$rowstatezone['state'];
			$zone=$rowstatezone['zone'];
			
			$sqlplant="SELECT plant_name FROM branch_master WHERE branch_code='".$branch_code."'";
			$rsplant=mysqli_query($link,$sqlplant);
			$rowplant=mysqli_fetch_assoc($rsplant);
			$plant_name=$rowplant['plant_name'];
			
				
				$contents  = (($branch_code!='')?$branch_code: ' ')."^";
				$contents  .= (($broker_id!='')?$broker_id: ' ')."^";
				$contents  .= (($emp_code_transaction!='')?$emp_code_transaction: ' ')."^";
				$contents  .= (($sauda_date!='')?$sauda_date: ' ')."^";
				$contents  .= (($sauda_no!='')?$sauda_no: ' ')."^";
				$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: ' ')."^";
				$contents  .= (($convert_qty_one!='')?$convert_qty_one: ' ')."^";
				$contents  .= (($convert_qty_two!='')?$convert_qty_two: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ')."^";
				$contents  .= (($TD!='')?$TD: ' ')."^";
				$contents  .= (($premium!='')?$premium: ' ')."^";
				$contents  .= (($freight_charge!='')?$freight_charge: ' ')."^";
				$contents  .= (($amount!='')?$amount: ' ')."^";
				$contents  .= (($plant_name!='')?$plant_name: ' ')."^";
				$contents  .= (($state!='')?$state: ' ')."^";
				$contents  .= (($zone!='')?$zone: ' ');
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
			$datacontents = '0'.'¥'.'18';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sauda-transaction-log-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code_value&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code_value,$url,$nick_name);
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
	header("Content-Disposition: attachment; filename=sauda_transaction_log.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
