<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$sqlquery = "SELECT * FROM card_transaction WHERE SUBSTRING(transaction_id,2,5) = '".$emp_code."' ORDER BY transaction_id ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
$contentsrowcolumn=$count.'¥'.'10';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowtransaction = mysqli_fetch_assoc($result))
		{
			$transaction_id=$rowtransaction['transaction_id'];
			$loyalty_card_no=$rowtransaction['loyalty_card_no'];
			$loyalty_card_no=preg_replace('/\s+/', '',$loyalty_card_no);
			$rds_code=$rowtransaction['rds_code'];
			$purchase_value=$rowtransaction['purchase_value'];
			$trans_type=$rowtransaction['trans_type'];
			$vehicle_no=$rowtransaction['vehicle_no'];
			$vehicle_no=preg_replace('/\s+/', '',$vehicle_no);
			$vehicle_type=$rowtransaction['vehicle_type'];
			$points_earned=$purchase_value/100;
			$points_redeemed=0;
			$flag=1;
			
			$contents  = (($transaction_id!='')?$transaction_id: ' ')."^";
			$contents  .= (($loyalty_card_no!='')?$loyalty_card_no: ' ')."^";
			$contents .= (($rds_code!='')?$rds_code: ' ')."^";
			$contents  .= (($purchase_value!='')?$purchase_value: ' ')."^";
			$contents  .= (($trans_type!='')?$trans_type: ' ')."^";
			$contents  .= (($vehicle_no!='')?$vehicle_no: ' ')."^";
			$contents  .= (($vehicle_type!='')?$vehicle_type: ' ')."^";
			$contents  .= (($points_earned!='')?$points_earned: ' ')."^";
			$contents  .= $points_redeemed."^";
			$contents  .= (($flag!='')?$flag: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'10';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/loyalty-card-transaction-details-txt-5.1.3.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/loyalty-card-transaction-details-txt-5.1.3.php?nick_name=$nick_name&emp_code=$emp_code"."\r\n";
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
	header("Content-Disposition: attachment; filename=card_transaction.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
