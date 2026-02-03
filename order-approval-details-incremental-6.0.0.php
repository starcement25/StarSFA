<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.') AND CM.acedns="Y"' ;
}
else
{
	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."' AND CM.acedns='Y'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(TAP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

/*$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);*/

$sqlquery="SELECT DISTINCT TAP.id,TAP.APPORDERNO,TAP.order_date,TAP.order_for,TAP.customer_code,TAP.dns_customer_code,TAP.sub_dealer_code,TAP.prod_code ,TAP.dns_prod_code,TAP.prod_display_name
,TAP.QTY,TAP.STATUS,TAP.freight,TAP.destination_code,TAP.destination_name,TAP.destination_address,TAP.phone_no,TAP.dump_status,TAP.dump_code,TAP.dump_name,TAP.dealer_truck,TAP.approval_status FROM T_APPERPDO_APPROVAL TAP,customer_route_emp_relation CM WHERE CM.customer_code=TAP.customer_code ".$login_condition.$emp_hierarchy_condition." AND TAP.approval_status='PENDING'";	
			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$contentsrowcolumn=$count.'¥'.'22';

		while($rowsorderapproval = mysqli_fetch_assoc($result))
		{
			$id=$rowsorderapproval['id'];
			$APPORDERNO=$rowsorderapproval['APPORDERNO'];
			$order_date=$rowsorderapproval['order_date'];
			$order_for=$rowsorderapproval['order_for'];
			$customer_code=$rowsorderapproval['customer_code'];
			$dns_customer_code=$rowsorderapproval['dns_customer_code'];
			$sub_dealer_code=$rowsorderapproval['sub_dealer_code'];
			$prod_code=$rowsorderapproval['prod_code'];
			$dns_prod_code=$rowsorderapproval['dns_prod_code'];
			$prod_display_name=$rowsorderapproval['prod_display_name'];
			$QTY=$rowsorderapproval['QTY'];
			$STATUS=$rowsorderapproval['STATUS'];
			$freight=$rowsorderapproval['freight'];
			$destination_code=$rowsorderapproval['destination_code'];
			$destination_name=$rowsorderapproval['destination_name'];
			$destination_address=$rowsorderapproval['destination_address'];
			$phone_no=$rowsorderapproval['phone_no'];
			$dump_status=$rowsorderapproval['dump_status'];
			$dump_code=$rowsorderapproval['dump_code'];
			$dump_name=$rowsorderapproval['dump_name'];
			$dealer_truck=$rowsorderapproval['dealer_truck'];
			$approval_status=$rowsorderapproval['approval_status'];
			$remarks='';
			$flag='1';
			
			$contents  = (($id!='')?$id: ' ')."^";
			$contents  .= (($APPORDERNO!='')?$APPORDERNO: ' ')."^";
			$contents  .= (($order_date!='')?$order_date: ' ')."^";
			$contents  .= (($order_for!='')?trim(preg_replace('/[\r\n]+/', '',$order_for)): ' ')."^";
			$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($dns_customer_code!='')?$dns_customer_code: ' ')."^";
			$contents  .= (($sub_dealer_code!='')?$sub_dealer_code: ' ')."^";
			$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
			$contents  .= (($dns_prod_code!='')?$dns_prod_code: ' ')."^";
			$contents  .= (($prod_display_name!='')?$prod_display_name: ' ')."^";
			$contents  .= (($QTY!='')?$QTY: ' ')."^";
			$contents  .= (($STATUS!='')?$STATUS: ' ')."^";
			$contents  .= (($freight!='')?$freight: ' ')."^";
			$contents  .= (($destination_code!='')?$destination_code: ' ')."^";
			$contents  .= (($destination_name!='')?$destination_name: ' ')."^";
			$contents  .= (($destination_address!='')?trim(preg_replace('/[\r\n]+/', '',$destination_address)): ' ')."^";
			$contents  .= (($phone_no!='')?$phone_no: ' ')."^";
			$contents  .= (($dump_status!='')?$dump_status: ' ')."^";
			$contents  .= (($dump_code!='')?$dump_code: ' ')."^";
			$contents  .= (($dump_name!='')?trim(preg_replace('/[\r\n]+/', '',$dump_name)): ' ')."^";
			$contents  .= (($dealer_truck!='')?$dealer_truck: ' ')."^";
			$contents  .= (($approval_status!='')?$approval_status: ' ');
			
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
			$datacontents = '0'.'¥'.'22';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/order-approval-details-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/prev-stock-counting-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=order_approval.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
