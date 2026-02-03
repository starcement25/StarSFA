<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
if($nick_name=='RKBKT')
{
	$last_update_time='2015-09-20€13:42:34';
}
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'] ?? '';
$data_download_time=str_replace('€',' ',$data_download_time);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$query_validate_datetime=$year.$month.$date;

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT loyalty_card_no,trans_type FROM `card_transaction` WHERE 1
			".$login_condition." GROUP BY loyalty_card_no,trans_type ORDER BY loyalty_card_no ASC";
/*$sqlquery="SELECT loyalty_card_no,trans_type FROM `card_transaction` WHERE 1
			 GROUP BY loyalty_card_no,trans_type ORDER BY loyalty_card_no ASC";*/			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'5';
	if($count>0){
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowpurchasedetails = mysqli_fetch_assoc($result))
		{
			$sqlcardholderdetails="SELECT total_reward_point,total_redeemed_point FROM loyalty_card_holder_master 
									WHERE loyalty_card_no='".$rowpurchasedetails['loyalty_card_no']."'";
			$rscardholderdetails=mysqli_query($link,$sqlcardholderdetails);
			$rowcardholderdetails=mysqli_fetch_assoc($rscardholderdetails);
			//$total_purchase_value=$rowcardholderdetails['total_purchase_value'];
			$total_reward_point=$rowcardholderdetails['total_reward_point'];
			$total_redeemed_point=$rowcardholderdetails['total_redeemed_point'];
			$sqlcardholderpurchase="SELECT SUM(purchase_value) AS total_purchase_value FROM `card_transaction` 
									WHERE loyalty_card_no='".$rowpurchasedetails['loyalty_card_no']."' AND SUBSTRING(transaction_id,7,8)='".$query_validate_datetime."'";
			$rscardholderpurchase=mysqli_query($link,$sqlcardholderpurchase);
			$rowcardholderpurchase=mysqli_fetch_assoc($rscardholderpurchase);
			$total_purchase_value=$rowcardholderdetails['total_purchase_value'];

			$contents  = (($rowpurchasedetails['loyalty_card_no']!='')?$rowpurchasedetails['loyalty_card_no']: ' ')."^";
			$contents  .= (($rowpurchasedetails['trans_type']!='')?$rowpurchasedetails['trans_type']: ' ')."^";
			$contents  .= (($total_purchase_value!='')?$total_purchase_value: 0)."^";
			$contents  .= (($total_reward_point!='')?$total_reward_point: ' ')."^";
			$contents  .= (($total_redeemed_point!='')?$total_redeemed_point: ' ');
			
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
			$datacontents = '0'.'¥'.'5';
		}
	}
	/*$sqlquerytrans_type="SELECT DISTINCT trans_type FROM `card_transaction`";
	$resultquerytrans_type = mysqli_query($link,$sqlquerytrans_type);
	$countloyaltydata=0;
	while($rowquerytrans_type = mysqli_fetch_assoc($resultquerytrans_type))
		{
			$sqlqueryloyaltycardno="SELECT loyalty_card_no FROM `card_transaction` GROUP BY loyalty_card_no  ORDER BY loyalty_card_no ASC";
			$resultqueryloyaltycardno = mysqli_query($link,$sqlqueryloyaltycardno);
			$countqueryloyaltycardno=mysqli_num_rows($resultqueryloyaltycardno);
	       //$contentsrowcolumn  =$countqueryloyaltycardno.'¥'.'3';	
	
			if($countqueryloyaltycardno>0){
			while($rowqueryloyaltycardno = mysqli_fetch_assoc($resultqueryloyaltycardno))
				{
					$loyalty_card_no=$rowqueryloyaltycardno['loyalty_card_no'];
					$trans_type=$rowquerytrans_type['trans_type'];
					
					$sqlquery="SELECT SUM(purchase_value) AS total_purchase_value FROM `card_transaction` 
							WHERE  loyalty_card_no='".$loyalty_card_no."' AND SUBSTRING(transaction_id,7,8)='".$query_validate_datetime."' 
							AND trans_type='".$trans_type."'";
					$result = mysqli_query($link,$sqlquery);
					$rowpurchasedetails = mysqli_fetch_assoc($result);
					$total_purchase_value=$rowpurchasedetails['total_purchase_value'];
					
					$contents  = (($loyalty_card_no!='')?$loyalty_card_no: ' ')."^";
					$contents  .= (($trans_type!='')?$trans_type: ' ')."^";
					$contents  .= (($total_purchase_value!='' || !is_null($total_purchase_value) )?$total_purchase_value:0);
							
					 $linecontents  .= $contents."\n";
					 $countloyaltydata++;
				}
				$datacontents = str_replace("\r","",$linecontents);
			}
		}
	if($countloyaltydata >0)
	{
		$contentsrowcolumn  =$countloyaltydata.'¥'.'3';
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.$datacontents;
	}*/
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/loyalty-purchase-details-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/loyalty-purchase-details-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";


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
	header("Content-Disposition: attachment; filename=loyalty_purchase_details.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
