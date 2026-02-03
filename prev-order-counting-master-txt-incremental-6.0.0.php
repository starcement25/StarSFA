<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
	$emp_hierarchy_condition=' (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
}
else
{
	$emp_hierarchy_condition=" SUBSTRING(order_no,2,5)='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

/*$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);*/


$sqlquery="SELECT DISTINCT POCM.customer_code,POCM.product_code FROM prev_order_counting_master POCM,customer_master CM 
			WHERE ".$emp_hierarchy_condition." AND CM.customer_code=POCM.customer_code"; 
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
		$countprevorder=0;
		while($rowsprevorder = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowsprevorder['customer_code'];
			$product_code=$rowsprevorder['product_code'];
			$contents  = (($customer_code!='')?$customer_code: ' ')."^";
			$contents  .= (($product_code!='')?$product_code: ' ')."^";

		    $sqlqueryprevorderdetails="SELECT POCM.visit_qty
	 							FROM prev_order_counting_master POCM WHERE  POCM.customer_code='".$customer_code."' 
								AND POCM.product_code='".$product_code."' ORDER BY POCM.visit_date DESC LIMIT 0,3";
			$resultprevorderdetails = mysqli_query($link,$sqlqueryprevorderdetails);
			$countprevorderdetails=mysqli_num_rows($resultprevorderdetails);
			$visit_qty='';
			while($rowsprevorderdetails = mysqli_fetch_assoc($resultprevorderdetails))
			{
			 $visit_qty=$visit_qty.$rowsprevorderdetails['visit_qty'].',';
			}
			$visit_qty=substr($visit_qty,0,-1);
			$contents  .= (($visit_qty!='')?$visit_qty: ' ');
			$linecontents  .= $contents."\n";
			if($countprevorderdetails >0)
			{
				$countprevorder++;
			}
		}
		$contentsrowcolumn=$countprevorder.'¥'.'3';
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
			$datacontents = '0'.'¥'.'3';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/prev-order-counting-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
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
	header("Content-Disposition: attachment; filename=prev_order_counting_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
