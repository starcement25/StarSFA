<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];
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
	$date=gmdate('d',strtotime('+329 minute'));
	$month=gmdate('m',strtotime('+329 minute'));
	$year=gmdate('Y',strtotime('+329 minute'));
	
	$hour=gmdate('H',strtotime('+329 minute'));
	$minute=gmdate('i',strtotime('+329 minute'));
	$second=gmdate('s',strtotime('+329 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contents='';
	$contents =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	if($emp_code=='C0007'){
			$emp_val_condition_audit="";
			$emp_val_rds="";
	}
	else
	{
		$emp_val_condition_audit=" AND CM.emp_code='".$emp_code."'";
		if(employeewise_hierarchy=='yes'){
			$employee_hierarchy=return_employee_hierarchy($emp_code);
			$emp_val_rds=' AND emp_code IN('.$employee_hierarchy.')';
		}
		else
		{
			$emp_val_rds=" AND emp_code='".$emp_code."'";
		}
	}
	if($incremental_download=='no'){
		$contents  .= 'menu_details'."\n";
		$contents  .= 'user_details'."\n";
		$contents  .= 'order_details'."\n";
		$contents  .= 'product_details'."\n";
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
		if(cl_stk=='yes' || sale=='yes'){
			$contents  .= 'closing_stock'."\n";
		}
		if(mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')){
		$contents  .= 'mrp_master'."\n";
		}
	/*if(mrp=='yes' || sale_rate=='yes'){
		$contents  .= 'mrp_master'."\n";
	}*/
		if(stk_audit=='yes'){
			$sqlprevstkcnt="SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM 
							WHERE CM.customer_code=PSCM.customer_code  ".$emp_val_condition_audit."";
			$rsprevstkcnt=mysqli_query($link,$sqlprevstkcnt);
			$rowprevstkcnt=mysqli_fetch_assoc($rsprevstkcnt);
			$prevstkcnt=$rowprevstkcnt['total_prev_stk'];
							
			if($prevstkcnt >0)
			{
				$contents  .= 'prev_stock_counting_master'."\n";
			}
		}
		if(route_plan=='yes'){
			$contents  .= 'route_plan'."\n";
		}
		if(tour_exp=='yes'){
			$contents  .= 'travel_category'."\n";
			$contents  .= 'travel_sub_category'."\n";
		}
		if(loyalty=='yes'){
			if($nick_name=='RKBKL'){
				$contents  .= 'outlet_master'."\n";
			}
			//$contents  .= 'outlet_master'."\n";
			$contents  .= 'loyalty_customer'."\n";
		}
		$sqlrds="SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1  ".$emp_val_rds."";
		$rsrds=mysqli_query($link,$sqlrds);
		$rowrds=mysqli_fetch_assoc($rsrds);
		$rdscnt=$rowrds['total_rds'];
						
		if($rdscnt >0)
		{
			$contents  .= 'rds_master'."\n";
		}
		if(sale=='yes')
		{
			$contents  .= 'emp_master'."\n";
			$contents  .= 'branch_master'."\n";
			$contents  .= 'vendor_master'."\n";
			$sqlclstksales="SELECT COUNT(OH.order_no) AS total_stk FROM order_header OH,location LO
							WHERE LO.trans_id=OH.order_no AND 
							OH.transaction_type='CN' AND OH.customer_code='".$emp_code."'";
			$rsclstksales=mysqli_query($link,$sqlclstksales);
			$rowclstksales=mysqli_fetch_assoc($rsclstksales);
			$clstksalescnt=$rowclstksales['total_stk'];
							
			if($clstksalescnt >0)
			{
				$contents  .= 'cl_stk_sales'."\n";
			}
		}
	}
	else
	{
		$sqlselect="SELECT is_update FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
		$rsselect=mysqli_query($link,$sqlselect);
		$count=mysqli_num_rows($rsselect);
		$need_update_table_array=array();
		if($count>0)
		{
			$rowselect=mysqli_fetch_assoc($rsselect);
			$is_update=$rowselect['is_update'];
			if($is_update==1){
				$sqlquery="SELECT table_name FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
				$resultquery = mysqli_query($link,$sqlquery);
				while($rowquery=mysqli_fetch_assoc($resultquery))
				{
					array_push($need_update_table_array,$rowquery['table_name']);
				}
			}
		}
		
		if(menu_details_download=='yes'){
			$contents  .= 'menu_details'."\n";
		}
		if(user_details_download=='yes'){
			$contents  .= 'user_details'."\n";
		}
		if(order_form_details_download=='yes'){
			$contents  .= 'order_details'."\n";
		}
		if(product_details_download=='yes'){
			$contents  .= 'product_details'."\n";
		}
		if($emp_code=='C0007'){
			$emp_val_condition="";
		}
		else
		{
			$emp_val_condition=" AND emp_code='".$emp_code."'";
		}
		
		if(in_array('route_master',$need_update_table_array))
		{
			$contents  .= 'route_master'."\n";
		}
		else{
			$sqlroutecnt="SELECT COUNT(route_code) AS total_route FROM route_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
			$rsroutecnt=mysqli_query($link,$sqlroutecnt);
			$rowroutecnt=mysqli_fetch_assoc($rsroutecnt);
			$routecnt=$rowroutecnt['total_route'];
			
			if($routecnt >0)
			{
				$contents  .= 'route_master'."\n";
			}
		}
		if(in_array('bank_master',$need_update_table_array))
		{
			$contents  .= 'bank_master'."\n";
		}
		else{
			$sqlbankcnt="SELECT COUNT(bank_id) AS total_bank FROM bank_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsbankcnt=mysqli_query($link,$sqlbankcnt);
			$rowbankcnt=mysqli_fetch_assoc($rsbankcnt);
			$bankcnt=$rowbankcnt['total_bank'];
			if($bankcnt >0)
			{
				$contents  .= 'bank_master'."\n";
			}
		}
		if(in_array('customer_master',$need_update_table_array))
		{
			$contents  .= 'customer_master'."\n";
		}
		else{
			$sqlcustomercnt="SELECT COUNT(customer_code) AS total_customer FROM customer_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
			$rscustomercnt=mysqli_query($link,$sqlcustomercnt);
			$rowcustomercnt=mysqli_fetch_assoc($rscustomercnt);
			$customercnt=$rowcustomercnt['total_customer'];
							
			if($customercnt >0)
			{
				$contents  .= 'customer_master'."\n";
			}
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
			if(in_array('product_group_master',$need_update_table_array))
			{
				$contents  .= 'product_group_master'."\n";
			}
			else{
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
		}
		if(no_of_filter > 2){
			if(in_array('product_sub_group_master',$need_update_table_array))
			{
				$contents  .= 'product_sub_group_master'."\n";
			}
			else{
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
		}
		if(no_of_filter > 3){
			if(in_array('product_brand_master',$need_update_table_array))
			{
				$contents  .= 'product_brand_master'."\n";
			}
			else{
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
		}
		if(in_array('product_master',$need_update_table_array))
			{
				$contents  .= 'product_master'."\n";
			}
		else{
			$sqlprodcnt="SELECT COUNT(prod_code) AS total_product FROM product_master 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND prod_code IN(SELECT DISTINCT product_code
						FROM mrp)";
			$rsprodcnt=mysqli_query($link,$sqlprodcnt);
			$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
			$prodcnt=$rowprodcnt['total_product'];
			
			if($prodcnt >0)
			{
				$contents  .= 'product_master'."\n";
			}
		}
		if(cl_stk=='yes' || sale=='yes'){
			if(in_array('closing_stock',$need_update_table_array))
			{
				$contents  .= 'closing_stock'."\n";
			}
		else{
			if(cl_stk=='yes')
			{
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
			if(sale=='yes')
			{
				$contents  .= 'closing_stock'."\n";
			}
		  }
		}
		if(mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')){
			if(in_array('mrp',$need_update_table_array))
			{
				$contents  .= 'mrp_master'."\n";
			}
			else{
				$sqlmrpcnt="SELECT COUNT(mrp_code) AS total_mrp FROM mrp 
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
				$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
				$mrpcnt=$rowmrpcnt['total_mrp'];
								
				if($mrpcnt >0)
				{
					$contents  .= 'mrp_master'."\n";
				}
			}
		}
		if(stk_audit=='yes'){
			if(in_array('prev_stock_counting_master',$need_update_table_array))
			{
				$contents  .= 'prev_stock_counting_master'."\n";
			}
		else{
			$sqlprevstkcnt="SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM 
							WHERE CM.customer_code=PSCM.customer_code 
							AND UNIX_TIMESTAMP(PSCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition_audit."";
			$rsprevstkcnt=mysqli_query($link,$sqlprevstkcnt);
			$rowprevstkcnt=mysqli_fetch_assoc($rsprevstkcnt);
			$prevstkcnt=$rowprevstkcnt['total_prev_stk'];
							
			if($prevstkcnt >0)
			{
				$contents  .= 'prev_stock_counting_master'."\n";
			}
		  }
		}
		if(route_plan=='yes'){
			$contents  .= 'route_plan'."\n";
		}
		if(tour_exp=='yes'){
			$sqltravelcatcnt="SELECT COUNT(transport_mode_cat_id) AS total_travel_cat FROM transport_mode_category 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rstravelcatcnt=mysqli_query($link,$sqltravelcatcnt);
			$rowtravelcatcnt=mysqli_fetch_assoc($rstravelcatcnt);
			$travelcatcnt=$rowtravelcatcnt['total_travel_cat'];
			if($travelcatcnt >0)
			{
				$contents  .= 'travel_category'."\n";
			}	
			$sqltravelsubcatcnt="SELECT COUNT(transport_mode_sub_cat_id) AS total_travel_sub_cat FROM transport_mode_sub_category 
						WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rstravelsubcatcnt=mysqli_query($link,$sqltravelsubcatcnt);
			$rowtravelsubcatcnt=mysqli_fetch_assoc($rstravelsubcatcnt);
			$travelsubcatcnt=$rowtravelsubcatcnt['total_travel_sub_cat'];
			if($travelsubcatcnt >0)
			{
				$contents  .= 'travel_sub_category'."\n";
			}	
		}
		if(loyalty=='yes'){
			if($nick_name=='RKBKL'){
				$contents  .= 'outlet_master'."\n";
			}
			$contents  .= 'loyalty_customer'."\n";
		}
		
		if(in_array('rds_master',$need_update_table_array))
			{
				$sqlrds="SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1";
				$rsrds=mysqli_query($link,$sqlrds);
				$rowrds=mysqli_fetch_assoc($rsrds);
				$rdscnt=$rowrds['total_rds'];
								
				if($rdscnt >0)
				{
					$contents  .= 'rds_master'."\n";
				}
			}
		else{
			$sqlrds="SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE  
					UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
			$rsrds=mysqli_query($link,$sqlrds);
			$rowrds=mysqli_fetch_assoc($rsrds);
			$rdscnt=$rowrds['total_rds'];
							
			if($rdscnt >0)
			{
				$contents  .= 'rds_master'."\n";
			}
		}
		if(sale=='yes')
		{
			$contents  .= 'emp_master'."\n";
			$contents  .= 'branch_master'."\n";
			$contents  .= 'vendor_master'."\n";
			$sqlclstksales="SELECT COUNT(OH.order_no) AS total_stk FROM order_header OH,location LO
							WHERE LO.trans_id=OH.order_no AND 
							OH.transaction_type='CN' AND OH.customer_code='".$emp_code."' AND 
							UNIX_TIMESTAMP(LO.date) > UNIX_TIMESTAMP('".$last_update_time."')";
			$rsclstksales=mysqli_query($link,$sqlclstksales);
			$rowclstksales=mysqli_fetch_assoc($rsclstksales);
			$clstksalescnt=$rowclstksales['total_stk'];
							
			if($clstksalescnt >0)
			{
				$contents  .= 'cl_stk_sales'."\n";
			}
			
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/datadownloaddictionary.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/datadownloaddictionary.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
