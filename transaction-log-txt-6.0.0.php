<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$sqlquery = "select EM.emp_name,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,
				OD.order_no,CM.customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
				OD.amount AS input_amount,OH.VAT,
				OH.transaction_type,OH.d_instruction from order_details OD,customer_master CM,product_master PM, 
				order_header OH,employee_master EM,location LO where OH.customer_code = CM.customer_code 
				and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND LO.trans_id=OH.order_no AND 
				SUBSTRING(OH.order_no,2,5) = '".$emp_code."'
												UNION
				select EM.emp_name,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,OD.order_no, 
				VM.vendor_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
				OD.amount AS input_amount,OH.VAT,
				OH.transaction_type, OH.d_instruction from order_details OD,vendor_master VM,product_master PM,order_header OH,
				employee_master EM,location LO where OH.customer_code = VM.vendor_code 
				and OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND LO.trans_id=OH.order_no AND 
				SUBSTRING(OH.order_no,2,5) = '".$emp_code."'
												UNION
				select EM.emp_name,EM.emp_code,DATE_FORMAT(LO.date,'%d-%m-%Y') AS transaction_date,
				OD.order_no,RM.rds_name AS customer_name,PM.prod_desc, OD.qty, OD.sale_rate,(OD.qty*OD.sale_rate) AS Amount,
				OD.amount AS input_amount,OH.VAT,
				OH.transaction_type, OH.d_instruction from order_details OD,rds_master RM,product_master PM,order_header OH,
				employee_master EM,location LO where SUBSTRING(OH.order_no,2,5) = RM.emp_code 
				AND OH.transaction_type='ST' AND OD.sku_code = PM.prod_code and OD.order_no = OH.order_no AND SUBSTRING(OH.order_no,2,5)=EM.emp_code 
				AND LO.trans_id=OH.order_no  AND SUBSTRING(OH.order_no,2,5) = '".$emp_code."' ORDER BY transaction_type";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$contentsrowcolumn=$count.'¥'.'14';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowtransactionlog = mysqli_fetch_assoc($result))
		{
			$branch_name='';
			$rds_name='';
			$emp_name=$rowtransactionlog['emp_name'];
			$transaction_date=$rowtransactionlog['transaction_date'];
			$order_no=$rowtransactionlog['order_no'];
			$customer_name=$rowtransactionlog['customer_name'];
			$prod_desc=$rowtransactionlog['prod_desc'];
			$qty=$rowtransactionlog['qty'];
			$sale_rate=$rowtransactionlog['sale_rate'];
			$Amount=$rowtransactionlog['Amount'];
			$input_amount=$rowtransactionlog['input_amount'];
			if($input_amount >0)
			{
				$Amount=$input_amount;
				$sale_rate=0;
			}
			$VAT=$rowtransactionlog['VAT'];
			$TD='0';
			$trans_type=$rowtransactionlog['transaction_type'];
			$d_instruction=preg_replace('/\s+/', '',$rowtransactionlog['d_instruction']);
				
				$contents  = (($branch_name!='')?$branch_name: ' ')."^";
				$contents  .= (($rds_name!='')?$rds_name: ' ')."^";
				$contents .= (($emp_name!='')?$emp_name: ' ')."^";
				$contents  .= (($transaction_date!='')?$transaction_date: ' ')."^";
				$contents  .= (($order_no!='')?$order_no: ' ')."^";
				$contents  .= (($customer_name!='')?$customer_name: ' ')."^";
				$contents  .= (($prod_desc!='')?$prod_desc: ' ')."^";
				$contents  .= (($qty!='')?$qty: ' ')."^";
				$contents  .= (($sale_rate!='')?$sale_rate: ' ')."^";
				$contents  .= (($Amount!='')?$Amount: ' ')."^";
				$contents  .= (($VAT!='')?$VAT: ' ')."^";
				$contents  .= (($TD!='')?$TD: ' ')."^";
				$contents  .= (($trans_type!='')?$trans_type: ' ')."^";
				$contents  .= (($d_instruction!='')?$d_instruction: ' ');
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
			$datacontents = '0'.'¥'.'14';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/transaction-log-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/transaction-log-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=transaction_log.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
