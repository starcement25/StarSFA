<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlselect="SELECT emp_code FROM emp_data_update_log  WHERE emp_code='".$emp_code."'";
$rsselect=mysqli_query($link,$sqlselect);
$count=mysqli_num_rows($rsselect);
$rowselect=mysqli_fetch_assoc($rsselect);
	
	if($count>0)
	{
		$sqlUpdate="UPDATE emp_data_update_log SET
					update_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
		if(mysqli_query($link,$sqlUpdate))
		{
			$successval="1";
		}
		else
		{
			$successval="0";
		}
	}
	else
	{
		$sqlInsert="INSERT INTO emp_data_update_log SET
					emp_code='".$emp_code."',
					update_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlInsert))
		{
			$successval="1";
		}
		else
		{
			$successval="0";
		}
	}
if($successval=="1"){
	$date=date('Y-m-d');
	$time=date('H:i:s');
	$contents='';
	$contents .= $date.'€'.$time."\n";
	if($incremental_download=='no'){
		$contents  .= 'route_master'."\n";
		$contents  .= 'bank_master'."\n";
		$contents  .= 'customer_master'."\n";
		if(credit_limit=='yes'){
			$contents  .= 'credit_limit'."\n";
		}
		$contents  .= 'outstanding_master'."\n";
		if(no_of_filter > 1){
			$contents  .= 'product_group_master'."\n";
		}
		if(no_of_filter > 2){
			$contents  .= 'product_sub_group_master'."\n";
		}
		if(no_of_filter > 3){
			$contents  .= 'product_brand_master'."\n";
		}
		$contents  .= 'product_master'."\n";
		if(cl_stk=='yes'){
			$contents  .= 'closing_stock'."\n";
		}
		$contents  .= 'mrp_master'."\n";
		if(route_plan=='yes'){
			$contents  .= 'route_plan'."\n";
		}
		if(tour_exp=='yes'){
			$contents  .= 'travel_category'."\n";
			$contents  .= 'travel_sub_category'."\n";
		}
		if(loyalty=='yes'){
			$contents  .= 'outlet_master'."\n";
			$contents  .= 'loyalty_customer'."\n";
		}
	}
	else
	{
		if($emp_code=='C0007'){
			$emp_val_condition="";
		}
		else
		{
			$emp_val_condition=" AND emp_code='".$emp_code."'";
		}
		$sqlroutecnt="SELECT COUNT(route_code) AS total_route FROM route_master 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
		$rsroutecnt=mysqli_query($link,$sqlroutecnt);
		$rowroutecnt=mysqli_fetch_assoc($rsroutecnt);
		$routecnt=$rowroutecnt['total_route'];
		
		if($routecnt >0)
		{
			$contents  .= 'route_master'."\n";
		}
		$sqlbankcnt="SELECT COUNT(bank_id) AS total_bank FROM bank_master 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsbankcnt=mysqli_query($link,$sqlbankcnt);
		$rowbankcnt=mysqli_fetch_assoc($rsbankcnt);
		$bankcnt=$rowbankcnt['total_bank'];
		if($bankcnt >0)
		{
			$contents  .= 'bank_master'."\n";
		}
		
		$sqlcustomercnt="SELECT COUNT(customer_code) AS total_customer FROM customer_master 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
		$rscustomercnt=mysqli_query($link,$sqlcustomercnt);
		$rowcustomercnt=mysqli_fetch_assoc($rscustomercnt);
		$customercnt=$rowcustomercnt['total_customer'];
						
		if($customercnt >0)
		{
			$contents  .= 'customer_master'."\n";
		}
		if(credit_limit=='yes'){
			
			$sqlcustomercreditcnt="SELECT COUNT(customer_code) AS total_customer_credit FROM customer_master 
										WHERE UNIX_TIMESTAMP(download_time_credit_limit) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
			$rscustomercreditcnt=mysqli_query($link,$sqlcustomercreditcnt);
			$rowcustomercreditcnt=mysqli_fetch_assoc($rscustomercreditcnt);
			$customercreditcnt=$rowcustomercreditcnt['total_customer_credit'];
			
			if($customercreditcnt >0)
			{
				$contents  .= 'credit_limit'."\n";
			}	
		}
		$contents  .= 'outstanding_master'."\n";
		if(no_of_filter > 1){
			$sqlprodgroupcnt="SELECT COUNT(product_group_code) AS total_product_group FROM product_group_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsprodgroupcnt=mysqli_query($link,$sqlprodgroupcnt);
			$rowprodgroupcnt=mysqli_fetch_assoc($rsprodgroupcnt);
			$prodgroupcnt=$rowprodgroupcnt['total_product_group'];
							
			if($prodgroupcnt >0)
			{
				$contents  .= 'product_group_master'."\n";
			}
		}
		if(no_of_filter > 2){
			$sqlprodsubgroupcnt="SELECT COUNT(product_sub_group_code) AS total_product_sub_group FROM product_sub_group_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsprodsubgroupcnt=mysqli_query($link,$sqlprodsubgroupcnt);
			$rowprodsubgroupcnt=mysqli_fetch_assoc($rsprodsubgroupcnt);
			$prodsubgroupcnt=$rowprodsubgroupcnt['total_product_sub_group'];
							
			if($prodsubgroupcnt >0)
			{
				$contents  .= 'product_sub_group_master'."\n";
			}
		}
		if(no_of_filter > 3){
			$sqlprodbrandcnt="SELECT COUNT(product_brand_code) AS total_product_brand FROM product_brand_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsprodbrandcnt=mysqli_query($link,$sqlprodbrandcnt);
			$rowprodbrandcnt=mysqli_fetch_assoc($rsprodbrandcnt);
			$prodbrandcnt=$rowprodbrandcnt['total_product_brand'];
							
			if($prodbrandcnt >0)
			{
				$contents  .= 'product_brand_master'."\n";
			}
		}
		$sqlprodcnt="SELECT COUNT(prod_code) AS total_product FROM product_master 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsprodcnt=mysqli_query($link,$sqlprodcnt);
		$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
		$prodcnt=$rowprodcnt['total_product'];
		
		if($prodcnt >0)
		{
			$contents  .= 'product_master'."\n";
		}
		if(cl_stk=='yes'){
			
			$sqlprodstkcnt="SELECT COUNT(prod_code) AS total_product_stk FROM product_master 
						WHERE UNIX_TIMESTAMP(download_time_cl_stk) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsprodstkcnt=mysqli_query($link,$sqlprodstkcnt);
			$rowprodstkcnt=mysqli_fetch_assoc($rsprodstkcnt);
			$prodstkcnt=$rowprodstkcnt['total_product_stk'];
			if($prodstkcnt >0)
			{
				$contents  .= 'closing_stock'."\n";
			}	
		}
		$sqlmrpcnt="SELECT COUNT(mrp_code) AS total_mrp FROM mrp 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
		$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
		$mrpcnt=$rowmrpcnt['total_mrp'];
						
		if($mrpcnt >0)
		{
			$contents  .= 'mrp_master'."\n";
		}
		if(route_plan=='yes'){
			$contents  .= 'route_plan'."\n";
		}
		if(tour_exp=='yes'){
			$contents  .= 'travel_category'."\n";
			$contents  .= 'travel_sub_category'."\n";
		}
		if(loyalty=='yes'){
			$contents  .= 'outlet_master'."\n";
			$contents  .= 'loyalty_customer'."\n";
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/datadownloaddictionary.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.coralindia.com/dev/acednsproduct/datadownloaddictionary.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");
print "$contents";
}
else
{
	echo '0';
}
?>
