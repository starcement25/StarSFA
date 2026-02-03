<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

$sqlrds="SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'";
$rsrds=mysqli_query($link,$sqlrds);
$rowrds=mysqli_fetch_assoc($rsrds);
$rds_code=$rowrds['rds_code'];

$sqlquery="SELECT * FROM activity_log WHERE updated_flag_app='0' AND (rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' 
			OR SUBSTRING(transaction_id,3,5)='".$emp_code."' OR receiver_code='".$emp_code."')";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><root>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowsdelete = mysqli_fetch_assoc($result))
		{
				if($rowsdelete['transaction_id']!='') $deletion_mode='transactionwise';
				else							$deletion_mode='datewise';
				
				$contents.="<deletion_data>";
				$contents .='<order_no><![CDATA['.mb_convert_encoding($rowsdelete['transaction_id'], 'UTF-8', 'UTF-8').']]></order_no>
							<start_date><![CDATA['.mb_convert_encoding($rowsdelete['start_date'], 'UTF-8', 'UTF-8').']]></start_date>
							<end_date><![CDATA['.mb_convert_encoding($rowsdelete['end_date'], 'UTF-8', 'UTF-8').']]></end_date>
							<deletion_mode><![CDATA['.mb_convert_encoding($deletion_mode, 'UTF-8', 'UTF-8').']]></deletion_mode>';
				$contents.="</deletion_data>";
				//echo $cnt++;
		}
	}
	$contents .= "</root>";	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/transaction-to-delete.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/transaction-to-delete.php?nick_name=$nick_name&emp_code=$emp_code"."\r\n";
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
			
	echo $contents;		
?>
