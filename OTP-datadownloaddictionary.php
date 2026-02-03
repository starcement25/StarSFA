<?php
error_reporting(0);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$query_validate_datetime=$year.$month.$date;
$contents='';
$contents =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if(employeewise_hierarchy=='yes')
	{
		$sqlreportinglevel="SELECT COUNT(emp_code) AS total_emp_code FROM employee_master WHERE FIND_IN_SET('".$emp_code."', reporting_to)";
		$rsreportinglevel=mysqli_query($link,$sqlreportinglevel);
		$rowreportinglevel=mysqli_fetch_assoc($rsreportinglevel);
		$reporting_level=$rowreportinglevel['total_emp_code'];
	}
	$server_current_date=$year.'-'.$month.'-'.$date;
		
		if(employeewise_hierarchy=='yes'){
			$employee_hierarchy=return_employee_hierarchy($emp_code);
			$emp_val_rds=' AND (emp_code IN('.$employee_hierarchy.')';
		}
		else
		{
			$emp_val_rds=" AND emp_code='".$emp_code."'";
		}
		$emp_val_rds.=')';
	$sqlempsaleaccess="SELECT sale_access FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempsaleaccess=mysqli_query($link,$sqlempsaleaccess);
	$rowempsaleaccess=mysqli_fetch_assoc($rsempsaleaccess);
	$sale_access_emp=$rowempsaleaccess['sale_access'];
	
//For checking employee menu access
	$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
	$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
	$countmenuaccess=mysqli_num_rows($rsmenuaccess);
	$menu_access_array=array();
	if($countmenuaccess >0)
	{
		while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
		{
			array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
		}
	}	   
		$contents  .= 'OTP_menu_details'."\n";
		$contents  .= 'user_details'."\n";
		$contents  .= 'product_details'."\n";
		$contents  .= 'menu_access'."\n";
		$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";			
		$rsqueryemp=mysqli_query($link,$sqlqueryemp);
		$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
		$cntemp=$rowqueryemp['total_emp'];
		if($cntemp >0)
		{
			$contents  .= 'emp_master'."\n";
		}
		if(in_array('transporter',$menu_access_array) || $emp_code=='E0001')
		{
			$sqlroutecnt="SELECT RM.route_code from route_master RM,customer_route_emp_relation CM,DO_transaction DT WHERE 
						RM.route_code=CM.route_code AND DT.customer_code= CM.customer_code GROUP BY RM.route_code";
			$rsroutecnt = mysqli_query($link,$sqlroutecnt);
			$routecnt=mysqli_num_rows($rsroutecnt);
			if($routecnt >0)
			{
				$contents  .= 'route_master'."\n";
			}
			$sqlcustomercnt="SELECT CM.customer_code FROM customer_route_emp_relation CM,DO_transaction DT WHERE 
								DT.customer_code= CM.customer_code GROUP BY CM.customer_code";
			$rscustomercnt = mysqli_query($link,$sqlcustomercnt);
			$customercnt=mysqli_num_rows($rscustomercnt);
			
			if($customercnt >0)
			{
				$contents  .= 'customer_master'."\n";
			}
			$sqlquery="SELECT DISTINCT sauda_no,customer_code,destination,DO_no,sku_code,DO_qty,DO_rate,DO_amount,DO_status,DO_date  
					FROM DO_transaction WHERE DO_status 
					IN('approved','vehicle_allotted','invoice_generated','despatch')";
			$result = mysqli_query($link,$sqlquery);
			$count=mysqli_num_rows($result);
			if($count > 0)
			{
				$contents  .= 'DO_transaction'."\n";
			}
		}
		if(in_array('gate_keeper1',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE arrival_date_gate='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list'."\n";
			}
		}
		if(in_array('despatch_in',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE despatch_approval_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_despatch_in'."\n";
			}
		}
		if(in_array('gate_keeper2',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE gate_vehicle_in_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_gate_keeper2'."\n";
			}
		}
		if(in_array('weighbridge_in',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE wb_in_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_weighbridge_in'."\n";
			}
		}
		if(in_array('loading',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE loading_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_loading'."\n";
			}
			$sqlbatchcnt="SELECT COUNT(batch_no) AS total_batch FROM product_batch_relation PBR,DO_transaction DT 
						WHERE DT.sku_code= PBR.prod_code ";
			$rsbatchcnt=mysqli_query($link,$sqlbatchcnt);
			$rowbatchcnt=mysqli_fetch_assoc($rsbatchcnt);
			$batchcnt=$rowbatchcnt['total_batch'];
			if($batchcnt >0)
			{
				$contents  .= 'product_batch_relation'."\n";
			}
		}
		if(in_array('weighbridge_out',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE wb_out_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_weighbridge_out'."\n";
			}
		}
		if(in_array('despatch_out',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE invoice_date='0000-00-00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_despatch_out'."\n";
			}
		}
		if(in_array('gate_keeper2_out',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE exit_approval_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_gate_keeper2_out'."\n";
			}
			$sqlchklists="SELECT checklist FROM OTP_checklists WHERE operation_type='gate_keeper2_out'";
			$rschklists==mysqli_query($link,$sqlchklists);
			$cntchklists=mysqli_num_rows($rschklists);
			if($cntchklists > 0)
			{
				$contents  .= 'check_list_gate_keeper2_out'."\n";
			}
		}
		if(in_array('security_out',$menu_access_array))
		{
			$sqlvehiclelistcnt="SELECT vehicle_no FROM DO_tracking WHERE security_chk_date='0000-00-00 00:00:00'";
			$rsvehiclelistcnt=mysqli_query($link,$sqlvehiclelistcnt);
			$cntvehiclelistcnt=mysqli_num_rows($rsvehiclelistcnt);
			if($cntvehiclelistcnt > 0)
			{
				$contents  .= 'vehicle_list_security_out'."\n";
			}
			$sqlchklists="SELECT checklist FROM OTP_checklists WHERE operation_type='security_out'";
			$rschklists==mysqli_query($link,$sqlchklists);
			$cntchklists=mysqli_num_rows($rschklists);
			if($cntchklists > 0)
			{
				$contents  .= 'check_list_security_out'."\n";
			}
		}
		if(no_of_filter > 1){
			$sqlproductgroupcnt="SELECT PGM.product_group_code FROM product_group_master PGM,DO_transaction DT,product_master PM WHERE 
						DT.sku_code= PM.prod_code AND PM.product_group_code=PGM.product_group_code GROUP BY PGM.product_group_code";
			$rsprodgroupcnt=mysqli_query($link,$sqlproductgroupcnt);
			$prodgroupcnt=mysqli_num_rows($rsprodgroupcnt);
			if($prodgroupcnt >0)
			{
				$contents  .= 'product_group_master'."\n";
			}
		}
		/*if(no_of_filter > 2){
			$sqlprodsubgroupcnt="SELECT PSGM.product_sub_group_code FROM product_sub_group_master PSGM,DO_transaction DT,product_master PM WHERE 
							DT.sku_code= PM.prod_code AND PM.product_sub_group_code=PSGM.product_sub_group_code GROUP BY PSGM.product_sub_group_code";
			$rsprodsubgroupcnt=mysqli_query($link,$sqlprodsubgroupcnt);
			$rowprodsubgroupcnt=mysqli_fetch_assoc($rsprodsubgroupcnt);
			$prodsubgroupcnt=mysqli_num_rows($rsprodsubgroupcnt);
			if($prodsubgroupcnt >0)
			{
				$contents  .= 'product_sub_group_master'."\n";
			}
		}*/
		if(no_of_filter > 3){
			$sqlprodbrandcnt="SELECT PBM.product_brand_code FROM product_brand_master PBM,DO_transaction DT,product_master PM WHERE 
							DT.sku_code= PM.prod_code AND PM.product_brand_code=PBM.product_brand_code GROUP BY PBM.product_brand_code";
			$rsprodbrandcnt=mysqli_query($link,$sqlprodbrandcnt);
			$rowprodbrandcnt=mysqli_fetch_assoc($rsprodbrandcnt);
			$prodbrandcnt=mysqli_num_rows($rsprodbrandcnt);
			if($prodbrandcnt >0)
			{
				$contents  .= 'product_brand_master'."\n";
			}
		}
		$sqlprodcnt="SELECT COUNT(prod_code) AS total_product FROM product_master PM,DO_transaction DT 
						WHERE DT.sku_code= PM.prod_code GROUP BY PM.prod_code";
		$rsprodcnt=mysqli_query($link,$sqlprodcnt);
		$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
		$prodcnt=$rowprodcnt['total_product'];
		if($prodcnt >0)
		{
			$contents  .= 'product_master'."\n";
		}
	/*else
	{
		//Construction of need to update table array
		$sqlselect="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
		$rsselect=mysqli_query($link,$sqlselect);
		$count=mysqli_num_rows($rsselect);
		$need_update_table_array=array();
		if($count>0)
		{
			$rowselect=mysqli_fetch_assoc($rsselect);
			$is_update=$rowselect['is_update'];
			$db_version_code=$rowselect['db_version_code'];
			if($is_update==1){
				$sqlquery="SELECT table_name FROM OTP_table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
				$resultquery = mysqli_query($link,$sqlquery);
				while($rowquery=mysqli_fetch_assoc($resultquery))
				{
					array_push($need_update_table_array,$rowquery['table_name']);
				}
			}
		}
		//Set up tables download checking
		if(in_array('menu_details',$need_update_table_array))
		{
			$contents  .= 'OTP_menu_details'."\n";
		}
		else{
			if(OTP_menu_details_download=='yes'){
				$contents  .= 'menu_details'."\n";
			}
		}
		if(in_array('user_details',$need_update_table_array))
		{
			$contents  .= 'user_details'."\n";
		}
		else{
			if(user_details_download=='yes'){
				$contents  .= 'user_details'."\n";
			}
		}
		if(in_array('product_details',$need_update_table_array))
		{
			$contents  .= 'product_details'."\n";
		}
		else{
			if(product_details_download=='yes'){

				$contents  .= 'product_details'."\n";
			}
		}
		//route download checking
		if(in_array('route_master',$need_update_table_array))
		{
			$contents  .= 'route_master'."\n";
		}
		else{
			$sqlroutecnt="SELECT RM.route_code from route_master RM,customer_route_emp_relation CM,DO_transaction DT WHERE 
							RM.route_code=CM.route_code AND DT.customer_code= CM.customer_code GROUP BY RM.route_code";
			$rsroutecnt = mysqli_query($link,$sqlroutecnt);
			$routecnt=mysqli_num_rows($rsroutecnt);
			
			if($routecnt >0)
			{
				$contents  .= 'route_master'."\n";
			}
		  }
		//customer download checking
		if(in_array('customer_master',$need_update_table_array))
		{
			$contents  .= 'customer_master'."\n";
		}
		else{
			$sqlcustomercnt="SELECT CM.customer_code FROM customer_route_emp_relation CM,DO_transaction DT WHERE 
							DT.customer_code= CM.customer_code GROUP BY CM.customer_code";
			$rscustomercnt = mysqli_query($link,$sqlcustomercnt);
			$customercnt=mysqli_num_rows($rscustomercnt);
			
			if($customercnt >0)
			{
				$contents  .= 'customer_master'."\n";
			}
		}
		//product download checking
		if(no_of_filter > 1){
			if(in_array('product_group_master',$need_update_table_array))
			{
				$contents  .= 'product_group_master'."\n";
			}
			else{
			    $sqlproductgroupcnt="SELECT PGM.product_group_code FROM product_group_master PGM,DO_transaction DT,product_master PM WHERE 
							DT.sku_code= PM.prod_code AND PM.product_group_code=PGM.product_group_code GROUP BY PGM.product_group_code";
				$rsprodgroupcnt=mysqli_query($link,$sqlproductgroupcnt);
				$prodgroupcnt=mysqli_num_rows($rsprodgroupcnt);
								
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
				$sqlprodsubgroupcnt="SELECT PSGM.product_sub_group_code FROM product_sub_group_master PSGM,DO_transaction DT,product_master PM WHERE 
							DT.sku_code= PM.prod_code AND PM.product_sub_group_code=PSGM.product_sub_group_code GROUP BY PSGM.product_sub_group_code";
				$rsprodsubgroupcnt=mysqli_query($link,$sqlprodsubgroupcnt);
				$rowprodsubgroupcnt=mysqli_fetch_assoc($rsprodsubgroupcnt);
				$prodsubgroupcnt=mysqli_num_rows($rsprodsubgroupcnt);
								
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
				$sqlprodbrandcnt="SELECT PBM.product_brand_code FROM product_brand_master PBM,DO_transaction DT,product_master PM WHERE 
							DT.sku_code= PM.prod_code AND PM.product_brand_code=PBM.product_brand_code GROUP BY PBM.product_brand_code";
				$rsprodbrandcnt=mysqli_query($link,$sqlprodbrandcnt);
				$rowprodbrandcnt=mysqli_fetch_assoc($rsprodbrandcnt);
				$prodbrandcnt=mysqli_num_rows($rsprodbrandcnt);
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
			$sqlprodcnt="SELECT COUNT(prod_code) AS total_product FROM product_master PM,DO_transaction DT
						WHERE DT.sku_code= PM.prod_code GROUP BY PM.prod_code";
			$rsprodcnt=mysqli_query($link,$sqlprodcnt);
			$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
			$prodcnt=$rowprodcnt['total_product'];
			if($prodcnt >0)
			{
				$contents  .= 'product_master'."\n";
			}
		}
		if(in_array('emp_master',$need_update_table_array))
			{
				$contents  .= 'emp_master'."\n";
			}
			else
			{
				$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";
				//$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";					
				$rsqueryemp=mysqli_query($link,$sqlqueryemp);
				$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
				$cntemp=$rowqueryemp['total_emp'];
								
				if($cntemp >0)
				{
					$contents  .= 'emp_master'."\n";
				}
			}
	}*/
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-datadownloaddictionary.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");
	print "$contents";
	mysqli_close($link);
?>
