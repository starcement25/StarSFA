<?php
//error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//$nickname=$_REQUEST['nick_name'];
ini_set('memory_limit', '2048M');
// phpinfo();
require("include/config.php");
require("include/config-setup.php");
// echo"<pre>";print_r(menu_details_download);die;
require("include/dbcon.php");
require("include/functions.php");


$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

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

//For those user who's db version is older than the current version 
$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysqli_query($link,$sqlselectversion);
$rowselectversion=mysqli_fetch_assoc($rsselectversion);
$versionCodecurrent=$rowselectversion['version_code'];

if(employeewise_hierarchy=='yes')
{
	$sqlreportinglevel="SELECT COUNT(emp_code) AS total_emp_code FROM employee_master WHERE FIND_IN_SET('".$emp_code."', reporting_to)";
	$rsreportinglevel=mysqli_query($link,$sqlreportinglevel);
	$rowreportinglevel=mysqli_fetch_assoc($rsreportinglevel);
	$reporting_level=$rowreportinglevel['total_emp_code'];
}
$server_current_date=$year.'-'.$month.'-'.$date;
	
if($emp_code=='C0007'){
		$emp_val_condition_audit="";
		$emp_val_rds="";
}
else
{
	$emp_val_condition_audit=" AND CM.emp_code='".$emp_code."'";
	if(employeewise_hierarchy=='yes'){
		//echo 6563546356353;die;
		$employee_hierarchy=return_employee_hierarchy($emp_code);

		$emp_val_rds=' AND (emp_code IN('.$employee_hierarchy.')';
		$emp_val_prev_order=' AND (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
	}
	else
	{
		$emp_val_rds=" AND emp_code='".$emp_code."'";
		$emp_val_prev_order=" AND SUBSTRING(order_no,2,5)='".$emp_code."'";
	}
	if(sale=='yes')
	{
		$sqlemp="SELECT EM.emp_code FROM employee_master EM,branch_master BM WHERE 
					EM.branch_code=BM.branch_code AND EM.emp_code='".$emp_code."'";
		$rsemp=mysqli_query($link,$sqlemp);
		while($rowemp=mysqli_fetch_assoc($rsemp))
		{
			$emp_code_list=$emp_code_list."'".$rowemp['emp_code']."'".',';
		}
		$emp_code_list=substr($emp_code_list,0,-1);
		if($emp_code_list!='')
		{
			$emp_val_rds.=' OR emp_code IN('.$emp_code_list.'))';
		}
		else
		{
			$emp_val_rds.=')';
		}
	}
	else
	{
		$emp_val_rds.=')';
	}
}
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
	
$sqlempsaleaccess="SELECT sale_access,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempsaleaccess=mysqli_query($link,$sqlempsaleaccess);
$rowempsaleaccess=mysqli_fetch_assoc($rsempsaleaccess);
$sale_access_emp=$rowempsaleaccess['sale_access'];
$branch_value_fetched=$rowsaleaccess['branch_code'];
	
//For checking employee menu access
$sqlmenuaccess="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['not_accessible_menu']);
	}
}
if(CRM_app=='yes' && !in_array('CRM_app',$menu_access_array))
{
	if($incremental_download=='no'){
		$contents   .= 'menu_details'."\n";
		$contents  .= 'user_details'."\n";
		$contents  .= 'order_details'."\n";
		$contents  .= 'product_details'."\n";
		if($nick_name=='HALDIRAM')
		{
			$contents  .= 'route_master_CRM'."\n";
		}
		$contents  .= 'customer_master_CRM'."\n";
		$contents  .= 'emp_datewise_route_allocation'."\n";
		$contents  .= 'menu_access'."\n";
	}
	else
	{
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
		if($nick_name=='HALDIRAM')
		{
			$contents  .= 'route_master_CRM'."\n";
		}
		$contents  .= 'customer_master_CRM'."\n";
		$contents  .= 'emp_datewise_route_allocation'."\n";
		$contents  .= 'menu_access'."\n";
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/datadownloaddictionary-7.0.5.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=datadownloaddictionary.txt");
	print "$contents";

}
else
{
$sqlselectuserdbversion="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
$rsselectuserdbversion=mysqli_query($link,$sqlselectuserdbversion);
$countselectuserdbversion=mysqli_num_rows($rsselectuserdbversion);
if($countselectuserdbversion>0)
{
	$rowselectuserdbversion=mysqli_fetch_assoc($rsselectuserdbversion);
	$is_update=$rowselectuserdbversion['is_update'];
	$user_db_version_code=$rowselectuserdbversion['db_version_code'];
	//if(intval(($versionCodecurrent-$user_db_version_code)*10) > '1' && $is_update==1 && $incremental_download=='yes')
	if(intval(($versionCodecurrent-$user_db_version_code)*10) > '1' && $is_update==1 && $incremental_download=='yes')
		{
			//mysqli_close($link);
			$linksetupdatabase=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
			mysqli_select_db($linksetupdatabase,"acedns_acednsproduct") or die("could not connect the database for invalid setup database");
			$need_download_table_array=array();
			$sqlquery="SELECT table_name FROM app_db_update_execution WHERE db_version > ".$user_db_version_code."  
						AND db_version <= ".$versionCodecurrent."  ORDER BY app_db_u_exe_id ASC ";
			$result = mysqli_query($link,$sqlquery) or die(mysqli_error());
			while($rowstructuredetails = mysqli_fetch_assoc($result))
			{
				array_push($need_download_table_array,$rowstructuredetails['table_name']);
			}
			$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
			$rsdbaccessdetails=mysqli_query($link,$sqldbaccessdetails,$linksetupdatabase);
			$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
			$remote_db_access=$rowdbaccessdetails['remote_db_access'];
			mysqli_close($linksetupdatabase);
			/*echo SERVER;
			echo USER;
			echo PASSWORD;
			echo DB;*/
			if($remote_db_access=='yes')
			{
				define("SERVERREMOTE","52.66.101.239");
				define("USERREMOTE","root");
				define("PASSWORDREMOTE","cmcl@123");
				define("DBREMOTE","acedns_$nick_name");
				$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error.");
				mysqli_select_db(DBREMOTE,$link) or die("could not connect the remote database for invalid nick names");

			}
			else
			{
				$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
				mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
			}

		
		if(in_array("menu_details",$need_download_table_array))
		{
			$contents  .= 'menu_details'."\n";
		}
		else{
			if(menu_details_download=='yes'){
				$contents  .= 'menu_details'."\n";
			}
		}
		if(in_array("user_details",$need_download_table_array))
		{
			$contents  .= 'user_details'."\n";
		}
		else{
			if(user_details_download=='yes'){
				$contents  .= 'user_details'."\n";
			}
		}
		if(in_array("order_form_details",$need_download_table_array))
		{
			$contents  .= 'order_details'."\n";
		}
		else{
			if(order_form_details_download=='yes'){
				$contents  .= 'order_details'."\n";
			}
		}
		if(in_array("product_details",$need_download_table_array))
		{
			$contents  .= 'product_details'."\n";
		}
		else{
			if(product_details_download=='yes'){

				$contents  .= 'product_details'."\n";
			}
		}
		if(route_plan=='yes' && in_array("route_plan_details",$need_download_table_array))
		{
			$contents  .= 'route_plan_details'."\n";
		}
		else{
				if(route_plan_details_download=='yes'){
					$contents  .= 'route_plan_details'."\n";
				}
			}
		if(sauda_allocation=='yes' && in_array("sauda_form_details",$need_download_table_array))
		{
			$contents  .= 'sauda_form_details'."\n";
		}
		if(survey=='yes' && in_array("survey_form_details",$need_download_table_array))
		{
			$contents  .= 'survey_form_details'."\n";
		}
		else if(survey=='yes' && survey_form_details_download=='yes'){
				$contents  .= 'survey_form_details'."\n";
		 }
		if(market_feedback=='yes' && in_array("market_feedback_details",$need_download_table_array))
		{
			$contents  .= 'market_feedback_details'."\n";
		}
		if(sauda_allocation=='yes' && in_array("broker_master",$need_download_table_array))
		{
			if(!in_array('sauda',$menu_access_array))
			{
				if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					$contents  .= 'broker_master'."\n";
				}
			}
		}
		if(in_array("route_master",$need_download_table_array))
		{
			$contents  .= 'route_master'."\n";
		}
		if(collection=='yes' && in_array("bank_master",$need_download_table_array))
		{
			$contents  .= 'bank_master'."\n";
		}
		if(in_array("customer_master",$need_download_table_array))
		{
			$contents  .= 'customer_master'."\n";
			if(credit_limit=='yes'){
				$contents  .= 'credit_limit'."\n";
			}
		}
		if(collection=='yes' || outstanding=='yes' || outstanding_ageing=='yes' || collection_forecast=='yes')
		{
			$contents  .= 'outstanding_master'."\n";
		}
		if(no_of_filter > 1 && in_array("product_group_master",$need_download_table_array)){
			$contents  .= 'product_group_master'."\n";
		}
		if(no_of_filter > 2 && in_array("product_sub_group_master",$need_download_table_array)){
			$contents  .= 'product_sub_group_master'."\n";
		}
		if(no_of_filter > 3 && in_array("product_brand_master",$need_download_table_array)){
			$contents  .= 'product_brand_master'."\n";
		}
		if(in_array("product_master",$need_download_table_array))
		{
			$contents  .= 'product_master'."\n";
		}
		if((cl_stk=='yes' || sale=='yes') && in_array("closing_stock",$need_download_table_array)) {
			$contents  .= 'closing_stock'."\n";
		}
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && in_array("mrp",$need_download_table_array) && (order=='yes' || van_sales=='yes')) {
			$contents  .= 'mrp_master'."\n";
		}
		if(strtoupper($nick_name)=='ARCHITA' && in_array("mrp",$need_download_table_array))
		  {
			  $contents  .= 'mrp_master'."\n";
		  }
		/*if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && in_array("sauda_mrp",$need_download_table_array) && sauda_allocation=='yes') {
			$contents  .= 'sauda_mrp'."\n";
		}*/
		if((mrp=='yes' || (sale_rate=='yes' && sauda_sale_rate_input_dropdown=='dropdown')) && in_array("sauda_mrp",$need_download_table_array) && sauda_allocation=='yes') {
			$contents  .= 'sauda_mrp'."\n";
		}
		/*if(mrp=='yes' || sale_rate=='yes'){
			$contents  .= 'mrp_master'."\n";
		}*/
		if(stk_audit=='yes' && previous_stock=='yes'){
			$sqlprevstkcnt="SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM 
							WHERE CM.customer_code=PSCM.customer_code  ".$emp_val_rds."";
			$rsprevstkcnt=mysqli_query($link,$sqlprevstkcnt);
			$rowprevstkcnt=mysqli_fetch_assoc($rsprevstkcnt);
			$prevstkcnt=$rowprevstkcnt['total_prev_stk'];
							
			if($prevstkcnt >0 && in_array("prev_stock_counting_master",$need_download_table_array))
			{
				$contents  .= 'prev_stock_counting_master'."\n";
			}
		}
		if(route_plan=='yes'){
			$contents  .= 'route_plan'."\n";
			if(route_customer_planning=='yes')
			{
				$sqlroutecustomerplancnt="SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan 
									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."' 
									AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsroutecustomerplancnt=mysqli_query($link,$sqlroutecustomerplancnt);
				$rowroutecustomerplancnt=mysqli_fetch_assoc($rsroutecustomerplancnt);
				$routecustomerplancnt=$rowroutecustomerplancnt['total_route_customer_plan'];
				if($routecustomerplancnt >0 && in_array("route_customer_plan_transaction",$need_download_table_array))
				{
					$contents  .= 'route_customer_plan'."\n";
				}
			}
		}
		if(tour_exp=='yes'){
			if(in_array("transport_mode_category",$need_download_table_array))
			{
				$contents  .= 'travel_category'."\n";
			}
			if(in_array("transport_mode_sub_category",$need_download_table_array))
			{
				$contents  .= 'travel_sub_category'."\n";
			}
		}
		if(loyalty=='yes'){
			//$contents  .= 'outlet_master'."\n";
			if(in_array("loyalty_card_holder_master",$need_download_table_array))
			{
				$contents  .= 'loyalty_customer'."\n";
			}
			$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
					$contents  .= 'scheme_details'."\n";
			}
			$sqlqueryloyaltypurchase="SELECT loyalty_card_no AS total_purchase_value FROM `card_transaction`";
			$resultloyaltypurchase = mysqli_query($link,$sqlqueryloyaltypurchase);
			$countloyaltypurchase=mysqli_num_rows($resultloyaltypurchase);
			if($countloyaltypurchase >0)
			{
				if(in_array("loyalty_purchase_details",$need_download_table_array))
				{
					$contents  .= 'loyalty_purchase_details'."\n";
				}
			}
			$sqlqueryredeeme="SELECT COUNT(sn) AS total_redeeme FROM redeem_details";
			$resqueryredeeme = mysqli_query($link,$sqlqueryredeeme);
			$countredeeme=mysqli_num_rows($resqueryredeeme);
			if($countredeeme >0)
			{
					$contents  .= 'redeeme_details'."\n";
			}
		}
		$sqlrds="SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1  ".$emp_val_rds."";
		$rsrds=mysqli_query($link,$sqlrds);
		$rowrds=mysqli_fetch_assoc($rsrds);
		$rdscnt=$rowrds['total_rds'];
						
		if($rdscnt >0)
		{
			if(in_array("rds_master",$need_download_table_array))
				{
					$contents  .= 'rds_master'."\n";
				}
		}
		if(in_array('emp_master',$need_download_table_array))
			{
				$contents  .= 'emp_master'."\n";
			}
			else
			{
				$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
				//$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";						
				$rsqueryemp=mysqli_query($link,$sqlqueryemp);
				$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
				$cntemp=$rowqueryemp['total_emp'];
								
				if($cntemp >0)
				{
					$contents  .= 'emp_master'."\n";
				}
			}
		if(survey=='yes')
		{
			if(in_array('branch_master',$need_download_table_array))
			{
				$contents  .= 'branch_master'."\n";
			}
			else
			{
				$sqlquerybranch="SELECT COUNT(BM.branch_code) AS total_branch FROM 
							branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND 
							EM.emp_code='".$emp_code."'";
				/*$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/			
				$rsquerybranch=mysqli_query($link,$sqlquerybranch);
				$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
				$branchcnt=$rowquerybranch['total_branch'];
								
				if($branchcnt >0)
				{
					$contents  .= 'branch_master'."\n";
				}
			}
		}

		if(sale=='yes')
		{
			$contents  .= 'branch_master'."\n";
			$contents  .= 'vendor_master'."\n";
			
			$sqlquerygit="SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code='".$emp_code."'";
			$rsquerygit=@mysqli_query($link,$sqlquerygit);
			$rowquerygit=@mysqli_fetch_assoc($rsquerygit);
			$gitcnt=$rowquerygit['total_git'];
							
			if($gitcnt >0 && in_array("goods_in_transit",$need_download_table_array))
			{
				$contents  .= 'git_master'."\n";
			}
			if($reporting_level >0)
			{
				if(in_array("mis_transaction_log",$need_download_table_array))
				{
					$contents  .= ' '."\n";
				}
				$sqlrdslist="SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."";
				$rsrdslist=mysqli_query($link,$sqlrdslist);
				while($rowrdslist=mysqli_fetch_assoc($rsrdslist))
				{
					$rds_list=$rds_list."'".$rowrdslist['rds_code']."'".',';
				}
				$rds_list=substr($rds_list,0,-1);
				$sqlchkdelete="SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN(".$rds_list.") OR SUBSTRING(transaction_id,2,5) IN (".$employee_hierarchy."))";
				$rschkdelete=mysqli_query($link,$sqlchkdelete);
				$rowchkdelete=mysqli_fetch_assoc($rschkdelete);
				$no_of_trans_id=$rowchkdelete['no_of_trans_id'];
				if($no_of_trans_id >0)
				{
					$contents  .= 'mis_transaction_delete'."\n";
				}
			}
			$contents  .= 'user_access'."\n";
		}
		if(sauda_allocation=='yes')
		{
			if(!in_array('sauda',$menu_access_array))
			{
				$contents  .= 'sauda_allocation'."\n";
				if(in_array('order',$menu_access_array))
				{
					if(in_array('branch_master',$need_download_table_array))
					{
						$contents  .= 'branch_master'."\n";
					}
					else
					{
						$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
										UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
						$rsquerybranch=mysqli_query($link,$sqlquerybranch);
						$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
						$branchcnt=$rowquerybranch['total_branch'];
										
						if($branchcnt >0)
						{
							$contents  .= 'branch_master'."\n";
						}
					}

					if(sauda_depot_wise=='yes')
					{
						if(in_array('customer_branch_relation',$need_download_table_array))
						{
							$contents  .= 'customer_branch_relation'."\n";
						}
						else
						{
							$sqlquerycustomerrds="SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds." 
										AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
							$rsquerycustomerrds=mysqli_query($link,$sqlquerycustomerrds);
							$rowquerycustomerrds=mysqli_fetch_assoc($rsquerycustomerrds);
							$customer_depot_cnt=$rowquerycustomerrds['total_customer_depot'];
							if($customer_depot_cnt >0)
							{
								$contents  .= 'customer_branch_relation'."\n";
							}
						}
					}
				}
				if(in_array('branch_route_freight',$need_download_table_array))
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				else
				{
					$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 
											UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
					$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
					$countbranchfreight=mysqli_num_rows($resultbranchfreight);
					if($countbranchfreight >0)
					{
						$contents  .= 'branch_route_freight'."\n";
					}
				}
				if(in_array('load_distribution',$need_download_table_array))
				{
					$contents  .= 'load_distribution'."\n";
				}
				else
				{
					//$sqlqueryload="SELECT prod_code FROM load_distribution WHERE UNIX_TIMESTAMP(datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
					$sqlqueryload="SELECT * FROM (SELECT LD.transport_mode,LD.truck_load,LD.qty_truck_load,LD.datetime,LD.prod_code
								FROM load_distribution LD WHERE UNIX_TIMESTAMP(LD.datetime) > UNIX_TIMESTAMP('".$last_update_time."') 
								ORDER BY LD.datetime DESC) AS SAT GROUP BY 1,2,5 ORDER BY 4";
					$resultqueryload = mysqli_query($link,$sqlqueryload);
					$countqueryload =mysqli_num_rows($resultqueryload);
					if($countqueryload >0)
					{
						$contents  .= 'load_distribution'."\n";
					}
				}
			}
			if(!in_array('sauda_allocation_app',$menu_access_array))
			{
				$contents  .= 'sauda_allocation_access'."\n";
				if(sauda_allocation_app=='yes')
				{
					if(in_array('sauda_allocation_log',$need_download_table_array))
					{
						$contents  .= 'sauda_allocation_log'."\n";
					}
					else
					{
						$sqlsaudaallocation="SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log 
										WHERE allocation_id<>'' AND UNIX_TIMESTAMP(allocation_date) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
						$rssaudaallocation=mysqli_query($link,$sqlsaudaallocation);
						$rowsaudaallocation=mysqli_fetch_assoc($rssaudaallocation);
						$total_allocation_cnt=$rowsaudaallocation['total_allocation'];
	
						if($total_allocation_cnt >0)
						{
							$contents  .='sauda_allocation_log'."\n";
						}
					}
				}
			}
			if(!in_array('sauda_mis',$menu_access_array))
			{
				if(sauda_mis=='yes')
				{
					if(in_array('sauda_transaction_log',$need_download_table_array))
					{
						$contents  .= 'sauda_transaction_log'."\n";
					}
					else
					{
						$sqlsaudatransaction="SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log 
										WHERE  UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
						$rssaudatransaction=mysqli_query($link,$sqlsaudatransaction);
						$rowsaudatransaction=mysqli_fetch_assoc($rssaudatransaction);
						$total_sauda_transaction=$rowsaudatransaction['total_sauda_transaction'];
	
						if($total_sauda_transaction >0)
						{
							$contents  .='sauda_transaction_log'."\n";
						}
					}
				}
			}
			$sqlvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsvertical=mysqli_query($link,$sqlvertical);
			$rowvertical=mysqli_fetch_assoc($rsvertical);
			$vertical_value=$rowvertical['vertical_value'];
			$split_incoterms=explode(',',incoterms_vertical);
			if(in_array($vertical_value,$split_incoterms)){
				if(in_array('honeycomb_cost',$need_download_table_array))
				{
					$contents  .= 'honeycomb_cost'."\n";
				}
				else
				 {
					$sqlhoneycomb= "SELECT * FROM (SELECT HC.prod_code,HC.state_code,DATE_FORMAT(SUBSTRING(HC.datetime,1,10),'%d-%m-%Y') As 
									last_updated_date,HC.plant_name FROM honeycomb_cost HC WHERE HC.vertical_value='".$vertical_value."' AND 
									UNIX_TIMESTAMP(HC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') ORDER BY HC.datetime DESC) AS SAT GROUP BY 1,2,4";
					$rshoneycomb = mysqli_query($link,$sqlhoneycomb);
					$total_honeycomb = mysqli_num_rows($rshoneycomb);
					if($total_honeycomb >0){
						$contents  .='honeycomb_cost'."\n";
					}
				 }
				 if(in_array('margin_cost',$need_download_table_array))
				 {
					$contents  .= 'margin_cost'."\n";
				 }
				else
				 {
					$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,MC.plant_name,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
									last_updated_date FROM margin_cost MC WHERE MC.vertical_value='".$vertical_value."' AND 
									UNIX_TIMESTAMP(MC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2,3";
					$rsmargin = mysqli_query($link,$sqlmargin);
					$total_margin = mysqli_num_rows($rsmargin);
					if($total_margin >0){
						$contents  .='margin_cost'."\n";
					}
				}
			}
		}
		if(survey=='yes')
		{
		  if(in_array('survey_category_master',$need_download_table_array))
			{
				$contents  .= 'survey_category_master'."\n";
			}
			else
			 {
				$sqlquerysurveycat="SELECT COUNT(SCM.sub_cat_id) AS total_sub_cat_id FROM survey_category_master SCM WHERE 
									UNIX_TIMESTAMP(SCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveycat=mysqli_query($link,$sqlquerysurveycat);
				$rowquerysurveycat=mysqli_fetch_assoc($rsquerysurveycat);
				$sub_cat_id_cnt=$rowquerysurveycat['total_sub_cat_id'];
				if($sub_cat_id_cnt >0)
				{
					$contents  .= 'survey_category_master'."\n";
				}
			 }
			 
			 if(in_array('table_view',$need_download_table_array))
			 {
				$contents  .= 'survey_table_view'."\n";
			 }
			else
			 {
				$sqlquerysurveytableview="SELECT COUNT(row_id) AS total_survey_table_view FROM table_view WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveytableview=mysqli_query($link,$sqlquerysurveytableview);
				$rowquerysurveytableview=mysqli_fetch_assoc($rsquerysurveytableview);
				$survey_table_view_cnt=$rowquerysurveytableview['total_survey_table_view'];
				if($survey_table_view_cnt >0)
				{
					$contents  .= 'survey_table_view'."\n";
				}
			 }
			 if(in_array('survey_input',$need_download_table_array))
			 {
				$contents  .= 'survey_input_details'."\n";
			 }
			else
			 {
				$sqlquerysurveyinput="SELECT COUNT(SI.row_id) AS total_survey_input_id FROM survey_input SI WHERE 
									UNIX_TIMESTAMP(SI.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveyinput=mysqli_query($link,$sqlquerysurveyinput);
				$rowquerysurveyinput=mysqli_fetch_assoc($rsquerysurveyinput);
				$survey_input_cnt=$rowquerysurveyinput['total_survey_input_id'];
				if($survey_input_cnt >0)
				{
					$contents  .= 'survey_input_details'."\n";
				}
			 }
			 if(in_array('mall_master',$need_download_table_array) || survey_type=='yes')
			 {
				$contents  .= 'mall_master'."\n";
			 }
			 if(in_array('mall_survey_relation',$need_download_table_array) || survey_type=='yes')
			 {
				$contents  .= 'mall_survey_relation'."\n";
			 }
			 if(in_array('survey_publish',$need_download_table_array))
			 {
				 $contents  .= 'survey_publish'."\n";
			 }
			 else
			 {
			     try {
			         $sqlsurveyidfetch="SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE 
							SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND 
							UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND MEAR.emp_code='".$emp_code."'";
    				 $rssurveyidfetch = mysqli_query($link,$sqlsurveyidfetch)  or die(mysqli_error());
    				 $countsurveyidfetch=mysqli_num_rows($rssurveyidfetch);
    				 if($countsurveyidfetch >0)
    				 {
    					 $contents  .= 'survey_publish'."\n";
    				 }
  
                } catch(Exception $e) {
                  
                }
				 
			 }
			 if(in_array('fs_survey_publish',$need_download_table_array))
			 {
				 $contents  .= 'fs_survey_publish'."\n";
			 }
			 else
			 {
				 $sqlmallidfetch="SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM 
								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ";
				 $rsmallidfetch = mysqli_query($link,$sqlmallidfetch);
				 $countmallidfetch=mysqli_num_rows($rsmallidfetch);
				 if($countmallidfetch >0){
					 $countdata=0;
					 while($rowmallidfetch = mysqli_fetch_assoc($rsmallidfetch))
					  {					
						 $mall_id_fetch=$rowmallidfetch['mall_id'];
						 $mall_name_fetch=$rowmallidfetch['mall_name'];
						 $sqlquery="SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						 $result = mysqli_query($link,$sqlquery);
						 $countfs=mysqli_num_rows($result);
						 if($countfs >0)
						 {
							 $contents  .= 'fs_survey_publish'."\n";
							 break;
						 }
					  }
				 }
			 }
			if(strtoupper($nick_name)=='EMAMI' ||  strtoupper($nick_name)=='EMAMIT' || strtoupper($nick_name)=='DURO_old')
			{
				if(in_array('state_district_town',$need_download_table_array))
				{
					$contents  .= 'state_district_town'."\n";
				}
				else
				{
					$sqlquerystatedistrict="SELECT SDT.state FROM state_district_town SDT,employee_master EM 
											WHERE EM.state=SDT.state AND EM.emp_code='".$emp_code."'";			
					$resultstatedistrict = mysqli_query($link,$sqlquerystatedistrict);
					$counttstatedistrict=mysqli_num_rows($resultstatedistrict);
					if($counttstatedistrict > 0)
					{
						$contents  .= 'state_district_town'."\n";
					}
				}
				$contents  .= 'state_master'."\n";
			}
		}
		if(product_promotion=='yes' || market_feedback=='yes')
		{
			$contents  .= 'generic_oil_master'."\n";
			/*if(in_array('street_master',$need_download_table_array))
			 {
				$contents  .= 'street_master'."\n";
			 }
			else
			 {
				$sqlquerystreet="SELECT COUNT(street_name) AS total_street FROM street_master WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerystreet=mysqli_query($link,$sqlquerystreet);
				$rowquerystreet=mysqli_fetch_assoc($rsquerystreet);
				$street_cnt=$rowquerystreet['total_street'];
				if($street_cnt >0)
				{
					$contents  .= 'street_master'."\n";
				}
			 }*/
		}
		if(market_feedback=='yes')
		{
			$contents  .= 'competitor_group_master'."\n";
		}
		$contents  .= 'menu_access'."\n";
		if(!in_array('pending_contract',$menu_access_array))
		{
			if(pending_contract=='yes' && in_array("pending_contract",$need_download_table_array))
			{
				$sqlpendingcontractcnt="SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."";
				$rspendingcontractcnt=mysqli_query($link,$sqlpendingcontractcnt);
				$rowpendingcontractcnt=mysqli_fetch_assoc($rspendingcontractcnt);
				$pendingcontractcnt=$rowpendingcontractcnt['total_pending_contract'];
				
				if($pendingcontractcnt >0)
				{
				  $contents  .= 'pending_contract'."\n";
				}
			}
		}
		if(!in_array('order',$menu_access_array))
		{
			if(order=='yes' && previous_order=='yes'){
				$sqlprevordercnt="SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM 
								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_prev_order."";
				$rsprevordercnt=mysqli_query($link,$sqlprevordercnt);
				$rowprevordercnt=mysqli_fetch_assoc($rsprevordercnt);
				$prevordercnt=$rowprevordercnt['total_prev_order'];
								
				if($prevordercnt >0 && in_array("prev_order_counting_master",$need_download_table_array))
				{
					$contents  .= 'prev_order_counting_master'."\n";
				}
			}
		}
		if(!in_array('order',$menu_access_array))
		{
			if(order=='yes' && order_status=='yes'){
					if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='ABDOST')
					{				
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM
									WHERE 1  ".$emp_val_prev_order."";	
					}
					else
					{
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
									WHERE CM.customer_code=POCM.customer_code  ".$emp_val_prev_order."";	
					}

				$rsorderstatus=mysqli_query($link,$sqlorderstatus);
				$roworderstatus=mysqli_fetch_assoc($rsorderstatus);
				$total_order_status=$roworderstatus['total_order_status'];
								
				if($total_order_status >0 && in_array("order_status",$need_download_table_array))
				{
					$contents  .= 'order_status'."\n";
				}
			}
		}
		if(!in_array('outstanding_ageing',$menu_access_array))
		{
			if(sauda_outstanding=='yes')
			{
				$sqloutstandingcnt="SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
									 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."";
				$rsoutstandingcnt=mysqli_query($link,$sqloutstandingcnt);
				$rowoutstandingcnt=mysqli_fetch_assoc($rsoutstandingcnt);
				$outstandingcnt=$rowoutstandingcnt['total_outstanding'];
 
				if(in_array('outstanding_ageing',$need_download_table_array))
				{
					if($outstandingcnt >0)
					{
						$contents  .= 'outstanding_ageing'."\n";
					}
				}
			}
		}
		if(sale_performance=='yes' && in_array("sale_performance_details",$need_download_table_array))
		{
			$contents  .= 'sale_performance'."\n";
		}
		if(destination_price_list=='yes' || destination_ordertype_price_list=='yes' || destination=='yes')
		{
			if(in_array('destination_master',$need_download_table_array))
			{
				$contents  .= 'destination_master'."\n";
			}
			else
			{
				if(destination=='yes' && branch_wise_destination=='yes')
				{
					$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
					$rsempbranch=mysqli_query($link,$sqlempbranch);
					while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
					{
						$branch_value=$rowempbranch['branch_code'];
						$branch_code=$branch_code.$branch_value.',';
					}
					$branch_value_array=explode(',',$branch_code);
					$branch_value_final = "'".implode("','", $branch_value_array)."'";
					$condition_branch=' AND BDF.branch_code IN ('.$branch_value_final.')';

					$sqlquerydestination="SELECT COUNT(DM.destination_code) AS total_destination FROM destination_master DM,branch_destination_freight BDF
								WHERE DM.destination_code=BDF.destination_code ".$condition_branch." AND 
								UNIX_TIMESTAMP(BDF.download_time) > UNIX_TIMESTAMP('".$last_update_time."')ORDER BY destination_name ASC";
				}
				else
				{

					$sqlquerydestination="SELECT COUNT(destination_code) AS total_destination FROM destination_master WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				}
				$rsquerydestination=mysqli_query($link,$sqlquerydestination);
				$rowquerydestination=mysqli_fetch_assoc($rsquerydestination);
				$destinationcnt=$rowquerydestination['total_destination'];
								
				if($destinationcnt >0)
				{
					$contents  .= 'destination_master'."\n";
				}
			}
		}
		if(target_achievement=='yes')
		{
			$contents  .= 'target_achievement'."\n";
		}
		if(distributor_route_planning=='yes' || distributor_route_emp_relation=='yes')
		{
			if(in_array('distributor_route_relation',$need_download_table_array))
			{
				$contents  .= 'distributor_route_relation'."\n";
			}
			else
			{
				$sqlquerydistributorroute="SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds." 
				AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
				$rsquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
				$rowquerydistributorroute=mysqli_fetch_assoc($rsquerydistributorroute);
				$distributorroutecnt=$rowquerydistributorroute['total_distributor'];
				
				if($distributorroutecnt >0)
				{
					$contents  .= 'distributor_route_relation'."\n";
				}
			}
		}
		/*if(self_appraisal=='yes')
		{
			if(in_array('self_appraisal',$need_download_table_array))
			{
				$contents  .= 'freight_cost'."\n";
			}
		}*/
		if(self_appraisal=='yes')
		{
			if(strtoupper($sale_access_emp)=='PRIMARY' || strtoupper($sale_access_emp)=='SECONDARY')
			{
				if(in_array('self_appraisal_details',$need_download_table_array))
				{
					$contents  .= 'self_appraisal_details'."\n";
				}
				else{
					if(self_appraisal_details_download=='yes'){
						$contents  .= 'self_appraisal_details'."\n";
					}
				}
				if(multiple_target_achievement=='yes')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
					$contents  .= 'self_appraisal_branch_wise'."\n";
					if(strtoupper($nick_name)=='ARCHITA' || strtoupper($nick_name)=='DNVFOODS' || strtoupper($nick_name)=='DNV')
		  			{
						$contents  .= 'self_appraisal_emp_wise'."\n";
					}
				}
				if(product_group_wise=='yes')
				{
					$contents  .= 'self_appraisal_productgroup_wise'."\n";
				}
				if(customer_wise_self_appraisal=='yes' && multiple_target_achievement=='no')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
				}
				if(product_wise=='yes')
				{
					$contents  .= 'self_appraisal_product_wise'."\n";
				}
				if(week_wise=='yes')
				{
					$contents  .= 'self_appraisal_emp_week_wise'."\n";
				}
			}
		}
		if(branchwise_scheme_PDF=='yes')
		{
			if(in_array('branch_schemes_PDF',$need_download_table_array))
			{
				$contents  .= 'branchwise_scheme_PDF'."\n";
			}
			else
			{
				$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
				$rsbranch=mysqli_query($link,$sqlbranch);
				$rowbranch=mysqli_fetch_assoc($rsbranch);
				$branch_code=$rowbranch['branch_code'];
				
				$sqlquery="SELECT PDF_file_name,branch_code,acedns FROM branch_schemes_PDF WHERE 
				FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
				ORDER BY download_time DESC ";
				$result = mysqli_query($link,$sqlquery);
				$count=mysqli_num_rows($result);
				if($count > 0)
				{
					$contents  .= 'branchwise_scheme_PDF'."\n";
				}
			}
		}
		if(input_screen_planwise=='yes')
		{
			$sqlqueryorderplan="SELECT COUNT(CPOD.customer_code) as total_order_plan FROM 
								customer_product_wise_orderplan_details CPOD,customer_route_emp_relation CRER WHERE 
								CPOD.customer_code = CRER.customer_code ".$emp_val_rds."";
			$rsqueryorderplan=mysqli_query($link,$sqlqueryorderplan);
			$orderplancnt=$rowqueryorderplan['total_order_plan'];
			
			if($orderplancnt >0){			
			$contents  .= 'customer_product_wise_orderplan'."\n";
			}
		}
		if(product_qty_wise_TD=='yes')
		{
			if(in_array('prodqty_custclass_wise_TD',$need_download_table_array))
			{
				$contents  .= 'prodqty_custclass_wise_TD'."\n";
			}
			else
			{
				$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
				$rsempbranch=mysqli_query($link,$sqlempbranch);
				while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
				{
					$branch_value=$rowempbranch['branch_code'];
					$branch_code=$branch_code.$branch_value.',';
				}
				$branch_value_array=explode(',',$branch_code);
				$branch_value_final = "'".implode("','", $branch_value_array)."'";
				$condition_branch=' AND branch_code IN ('.$branch_value_final.')';
				
				$sqlquerydiscount="SELECT prod_code FROM prodqty_custclass_wise_TD WHERE 1 ".$condition_branch." AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$resultquerydiscount = mysqli_query($link,$sqlquerydiscount);
				$countdiscount=mysqli_num_rows($resultquerydiscount);
				if($countdiscount >0)
				{
					$contents  .= 'prodqty_custclass_wise_TD'."\n";
				}
			}
		}
		if(TD_allocation_app=='yes')
		  {
		  	if(!in_array('TD_allocation_app',$menu_access_array))
			  {
				  $contents  .= 'TD_allocation_access'."\n";
			  }
			  $contents  .= 'TD_allocation'."\n";
		  }
		  if(yellow_card=='yes')
		  {
			 $contents  .= 'yellow-card-date-validation'."\n";
			 $contents  .= 'yellow-card-date-validation_customerwise'."\n";
		  }
		  if(schemes=='yes'){
		  	$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_master";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
				$contents  .= 'scheme_master'."\n";
				$sqlqueryfreebies="SELECT COUNT(scheme_id) AS total_scheme_id FROM freebies_master";
				$rsqueryfreebies=mysqli_query($link,$sqlqueryfreebies);
				$rowqueryfreebies=mysqli_fetch_assoc($rsqueryfreebies);
				$total_freebies_cnt=$rowqueryfreebies['total_scheme_id'];
				if($total_freebies_cnt >0)
				{
					$contents  .= 'freebies_master'."\n";	
				}
			}
			$sqlquery="SELECT order_no FROM prev_order_counting_master WHERE 1 ".$emp_val_rds." "; 
			$result = mysqli_query($link,$sqlquery);
			$countordersummary=mysqli_num_rows($result);
			if($countordersummary >0)
			{
				$contents  .= 'order_summary'."\n";	
			}
		  }
		  if(stk_audit_msl=='yes')
		  {
			  $contents  .= 'customer_product_wise_msl'."\n";
		  }
		  if(retailer_app=='yes')
		  {
			//$contents  .= 'stock_allocation'."\n";
			//Billing start
			/*if(in_array('customer_product_billing',$need_download_table_array))
			{
				$contents  .= 'billing_information'."\n";
			}
			else
			{
				$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,
							customer_master CM
							WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";
				$rsempcust=mysqli_query($link,$sqlempcust); 
				$rowempcust=mysqli_fetch_assoc($rsempcust);
				$customer_code_emp=$rowempcust['customer_code'];
				$retailer_app=$rowempcust['retailer_app'];
				$cust_type=$rowempcust['cust_type'];
				$rds_tag=$rowempcust['rds_tag'];
			if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' AND 
							customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')";
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				else
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' ".$emp_val_rds;
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				$sqlquery="SELECT * FROM customer_product_billing WHERE customer_code IN (".$customer_code.") AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$result = mysqli_query($link,$sqlquery);
				$countbilling=mysqli_num_rows($result);
				if($countbilling >0)
				{
					$contents  .= 'billing_information'."\n";
				}
			}*/
			//Billing End
			$contents  .= 'billing_information'."\n";
			/*if(!in_array('stock_balance_details',$need_download_table_array))
			{
				$contents  .= 'stock_balance_details'."\n";
			}
			else
			{
			$sqlstockbalancedetails = "SELECT SBD.customer_code  FROM customer_route_emp_relation CRR,
							employee_master EM,stock_balance_details SBD WHERE  
							CRR.emp_code=EM.emp_code AND SBD.customer_code=CRR.customer_code AND CRR.emp_code 
							IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$emp_code."',reporting_to) UNION SELECT emp_code FROM employee_master WHERE acedns='Y' AND emp_code='".$emp_code."') 
							AND UNIX_TIMESTAMP(SBD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resultstockbalancedetails = mysqli_query($link,$sqlstockbalancedetails);
			$countstockbalance=mysqli_num_rows($resultstockbalancedetails);	
			
			if($countstockbalance >0){
				$contents  .= 'stock_balance_details'."\n";
			}
		   }
		   if(!in_array('stock_mis_details',$need_download_table_array))
			{
				$contents  .= 'sale_stock_mis'."\n";
			}
			else
			{
			$sqlstockmisdetails = "SELECT SMD.sl_no FROM customer_route_emp_relation CRR,
								employee_master EM,stock_mis_details SMD WHERE 
								CRR.emp_code=EM.emp_code AND SMD.customer_code=CRR.customer_code AND CRR.emp_code 
								IN(".$employee_hierarchy.") AND UNIX_TIMESTAMP(SMD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resultstockmisdetails = mysqli_query($link,$sqlstockmisdetails);
			$countstockmisdetails=mysqli_num_rows($resultstockmisdetails);	
			
			if($countstockmisdetails >0){
				$contents  .= 'sale_stock_mis'."\n";
			}
		   }*/
		  }
		  if(strtoupper($nick_name)=='ARCHITA')
		  {
			  $contents  .= 'customer_branch_relation'."\n";
		  }
		  if(strtoupper($nick_name)=='START' || strtoupper($nick_name)=='DNV' || strtoupper($nick_name)=='DNVFOODS')
		  {
		 	 $contents  .= 'branch_master'."\n";
		  }
		  if($reporting_level >0)
		  {
			if(in_array('attendance_checkout_details',$need_download_table_array))
			{
				$contents  .= 'attendance_checkout_details'."\n";
			}
			else
			{
			$sqlattendancecheckoutdetails = "SELECT trans_id FROM location WHERE (trans_id LIKE 'A%' OR trans_id LIKE 'CH%' OR trans_id LIKE 'WO%' 
											OR trans_id LIKE 'LR%') ".$emp_val_rds."
										AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resattendancecheckoutdetails = mysqli_query($link,$sqlattendancecheckoutdetails);
			$countattendancecheckout=mysqli_num_rows($resattendancecheckoutdetails);	
			
			if($countattendancecheckout >0){
				$contents  .= 'attendance_checkout_details'."\n";
			} 
		  }
		 }
		 if(strtoupper($nick_name)=='AJANTA')
		  {
			  if(!in_array('survey',$menu_access_array))
			  {
			  	$contents  .= 'sample_master'."\n";
			  }
		  }
		  if(van_sales=='yes')
		  {
			  $contents  .= 'van_stock_allocation'."\n";
		  }
		  if(strtoupper($nick_name)=='DURO' || strtoupper($nick_name)=='SYLVAN')
		  {
			  $contents  .= 'site_master'."\n";
			   $sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
			  $rsbranchcode=mysqli_query($link,$sqlbranchcode);
			  $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			  $branch_code=$rowbranchcode['branch_code'];
			  /*$sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE 1 ".$emp_val_rds." AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/
			  $sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE branch_code='".$branch_code."'";			
			  $resultfacilitator = mysqli_query($link,$sqlqueryfacilitator);
			  $countfacilitator=mysqli_num_rows($resultfacilitator);
			  if($countfacilitator >0)
			  {
			  $contents  .= 'facilitator_master'."\n";
			  }
			  $sqlquerydealer="SELECT customer_code FROM dealer_transaction WHERE acedns='Y' ".$emp_val_rds;
			  $resultdealer = mysqli_query($link,$sqlquerydealer);
			  $countdealer=mysqli_num_rows($resultdealer);
			  if($countdealer >0)
			  {
				$contents  .= 'dealer_transaction'."\n";
			  }
		  }
		  
		  if(strtoupper($nick_name)=='NIMBUS')
			  {
				 // $contents  .= 'farmer_master'."\n";
				  //$contents  .= 'BOQ_master'."\n";
				  $contents  .= 'additional_material'."\n";
			  }
		  if(customer_product_relation=='yes'){
			if(in_array('customer_product_relation',$need_download_table_array))
			{
				$contents  .= 'customer_product_relation'."\n";
			}
			else
			{
				$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_route_emp_relation CM 
								WHERE  CPR.customer_code=CM.customer_code ".$emp_val_rds." AND 
								UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rscustomerprod=mysqli_query($link,$sqlcustomerprod);
				$total_cus_prod=mysqli_num_rows($rscustomerprod);
				if($total_cus_prod >0)
				{
					$contents  .= 'customer_product_relation'."\n";
				}
		  }
		  }
		  if(bargain=='yes')
			{
				if(!in_array('bargain',$menu_access_array))
				{
					if(in_array("sauda_form_details",$need_download_table_array))
					{
						$contents  .= 'sauda_form_details'."\n";
					}
					$contents  .= 'conversion_data'."\n";
					$contents  .= 'bargain_mrp'."\n";
					$contents  .= 'mcx_rate'."\n";
				   if(in_array('customer_branch_relation',$need_download_table_array))
					{
						$contents  .= 'customer_branch_relation'."\n";
					}
					else
					{
						$sqlquerycustomerrds="SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_route_emp_relation  CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds." 
									AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						$rsquerycustomerrds=mysqli_query($link,$sqlquerycustomerrds);
						$rowquerycustomerrds=mysqli_fetch_assoc($rsquerycustomerrds);
						$customer_depot_cnt=$rowquerycustomerrds['total_customer_depot'];
						if($customer_depot_cnt >0)
						{
							$contents  .= 'customer_branch_relation'."\n";
						}
					}
					 if(in_array('margin_cost',$need_download_table_array))
					 {
						$contents  .= 'margin_cost'."\n";
					 }
					else
					 {
						$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,MC.plant_name,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
										last_updated_date FROM margin_cost MC WHERE UNIX_TIMESTAMP(MC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') 
										ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2,3";
						$rsmargin = mysqli_query($link,$sqlmargin);
						$total_margin = mysqli_num_rows($rsmargin);
						if($total_margin >0){
							$contents  .='margin_cost'."\n";
						}
					}
					if(in_array('depot_cost',$need_download_table_array))
					{
						$contents  .= 'depot_cost'."\n";
					}
					$sqldepot= "SELECT * FROM (SELECT DC.dns_prod_code,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') As last_updated_date,
								DC.branch_code,DC.depot_cost FROM depot_cost DC WHERE  UNIX_TIMESTAMP(DC.datetime) > UNIX_TIMESTAMP('".$last_update_time."')  ORDER BY DC.datetime DESC) AS SAT GROUP BY 1,3 ORDER BY 2 DESC";
					$rsdepot = mysqli_query($link,$sqldepot);
					$total_depot = mysqli_num_rows($rsdepot);
					if($total_depot >0){
						$contents  .='depot_cost'."\n";
					}
					if(in_array('freight_cost',$need_download_table_array))
					{
						$contents  .= 'primary_freight'."\n";
					}
					$sqlfreight= "SELECT * FROM (SELECT PF.dns_prod_code,DATE_FORMAT(SUBSTRING(PF.datetime,1,10),'%d-%m-%Y') As last_updated_date,
									PF.branch_code,PF.freight_cost,PF.transport_mode,PF.truck_load  FROM freight_cost PF  
									WHERE UNIX_TIMESTAMP(PF.datetime) > UNIX_TIMESTAMP('".$last_update_time."') 
									ORDER BY PF.datetime DESC) AS SAT GROUP BY 1,3,5,6 ORDER BY 2 DESC";
					$rsfreight = mysqli_query($link,$sqlfreight);
					$total_freight = mysqli_num_rows($rsfreight);
					if($total_freight >0){
						$contents  .='primary_freight'."\n";
					}
				if(in_array('branch_master',$need_download_table_array))
				{
					$contents  .= 'branch_master'."\n";
				}
				else
				{
					$sqlquerybranch="SELECT COUNT(BM.branch_code) AS total_branch FROM 
								branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND 
								EM.emp_code='".$emp_code."'";
					$rsquerybranch=mysqli_query($link,$sqlquerybranch);
					$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
					$branchcnt=$rowquerybranch['total_branch'];
									
					if($branchcnt >0)
					{
						$contents  .= 'branch_master'."\n";
					}
				}
				if(in_array('branch_route_freight',$need_download_table_array))
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				else
				{
					$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 
											UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
					$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
					$countbranchfreight=mysqli_num_rows($resultbranchfreight);
					if($countbranchfreight >0)
					{
						$contents  .= 'branch_route_freight'."\n";
					}
				}
			  }
			  if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					$contents  .= 'broker_master'."\n";
					$contents  .= 'customer_broker_relation'."\n";
					$contents  .= 'brokerage_cost'."\n";
				}
			}
			if(delivery_order=='yes')
			{
				if(in_array('DO_master',$need_download_table_array))
				{
					$contents  .= 'bargain_transaction'."\n";
				}
				else
				{
					$sqlqueryDO="SELECT DISTINCT SH.sauda_no  FROM sauda_header SH,sauda_details SD,customer_route_emp_relation CRR WHERE SH.DO_done='no' 
								AND SH.customer_code=CRR.customer_code AND SH.sauda_no=SD.sauda_no AND CRR.emp_code IN(".$employee_hierarchy.") 
								AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(SH.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."')";
					$resultDO = mysqli_query($link,$sqlqueryDO);
					$countDO=mysqli_num_rows($resultDO);
					if($countDO > 0)
					{
						$contents  .= 'bargain_transaction'."\n";
					}
				}
			}
			if(app_order_approval=='yes'){
				$sqlorderapproval="SELECT COUNT(TAP.customer_code) AS total_order_approval FROM T_APPERPDO_APPROVAL TAP,customer_route_emp_relation CM 
								WHERE CM.customer_code=TAP.customer_code  AND CM.emp_code IN(".$employee_hierarchy.") AND CM.acedns='Y' 
								AND UNIX_TIMESTAMP(TAP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsorderapproval=mysqli_query($link,$sqlorderapproval);
				$roworderapproval=mysqli_fetch_assoc($rsorderapproval);
				$total_order_approval=$roworderapproval['total_order_approval'];
								
				if($total_order_approval >0 || in_array("T_APPERPDO_APPROVAL",$need_download_table_array))
				{
					$contents  .= 'order_approval'."\n";
				}
				if(in_array('branch_dump',$need_download_table_array))
				{
					$contents  .= 'branch_dump'."\n";
				}
				else
				{
					$sqlbranch="SELECT GROUP_CONCAT(branch_code SEPARATOR ',') AS branch_code FROM employee_master 
								WHERE emp_code IN(".$employee_hierarchy.")";
					$rsbranch=mysqli_query($link,$sqlbranch);
					$rowbranch=mysqli_fetch_assoc($rsbranch);
					$branch_code=$rowbranch['branch_code'];
					
					$sqlquery="SELECT branch_code FROM branch_dump WHERE acedns='Y' AND FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					$result = mysqli_query($link,$sqlquery);
					$count=mysqli_num_rows($result);
					if($count > 0)
					{
						$contents  .= 'branch_dump'."\n";
					}
				}
			}
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = APICALLLOGURL."/datadownloaddictionary-7.0.3.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
		insertapilog($datetime,$emp_code,$url,$nick_name);
		/*$config = 'api_calllog.txt';
		$file=fopen($config,"r+");
		$date = date("F j, Y");
		$time = date("H:i:s");
		$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/datadownloaddictionary-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
	exit();
	}
}
//End For those user who's db version is older than the current version 

if($successval=="1"){
	if($incremental_download=='no'){
		$contents  .= 'menu_details'."\n";
		$contents  .= 'user_details'."\n";
		$contents  .= 'order_details'."\n";
		$contents  .= 'product_details'."\n";
		
		if(route_plan=='yes')
		{
			$contents  .= 'route_plan_details'."\n";
		}
		if(sauda_allocation=='yes')
		{
			$contents  .= 'sauda_form_details'."\n";
		}
		if(survey=='yes')
		{
			$contents  .= 'survey_form_details'."\n";
			$contents  .= 'survey_table_view'."\n";
		}
		if(market_feedback=='yes')
		{
			$contents  .= 'market_feedback_details'."\n";
		}
		if(sauda_allocation=='yes')
		{
			if(!in_array('sauda',$menu_access_array))
			{
				if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					$contents  .= 'broker_master'."\n";
				}
			}
		}
		$contents  .= 'route_master'."\n";
		if(collection=='yes')
		{
			$contents  .= 'bank_master'."\n";
		}
		$contents  .= 'customer_master'."\n";
		if(credit_limit=='yes'){
			$contents  .= 'credit_limit'."\n";
		}
		if(collection=='yes' || outstanding=='yes' || outstanding_ageing=='yes' || collection_forecast=='yes')
		{
			$contents  .= 'outstanding_master'."\n";
		} 
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
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && (order=='yes' || van_sales=='yes')){
		 $contents  .= 'mrp_master'."\n";
		}
		if(strtoupper($nick_name)=='ARCHITA')
		  {
			  $contents  .= 'mrp_master'."\n";
		  }
		/*if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes') {
			$contents  .= 'sauda_mrp'."\n";
		}*/
		if((mrp=='yes' || (sale_rate=='yes' && sauda_sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes') {
			$contents  .= 'sauda_mrp'."\n";
		}
		/*if(mrp=='yes' || sale_rate=='yes'){
			$contents  .= 'mrp_master'."\n";
		}*/
		if(stk_audit=='yes' && previous_stock=='yes'){
			$sqlprevstkcnt="SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM 
							WHERE CM.customer_code=PSCM.customer_code  ".$emp_val_rds."";
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
			if(route_customer_planning=='yes')
			{
				$sqlroutecustomerplancnt="SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan 
									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."'";
				$rsroutecustomerplancnt=mysqli_query($link,$sqlroutecustomerplancnt);
				$rowroutecustomerplancnt=mysqli_fetch_assoc($rsroutecustomerplancnt);
				$routecustomerplancnt=$rowroutecustomerplancnt['total_route_customer_plan'];
				if($routecustomerplancnt >0)
				{
					$contents  .= 'route_customer_plan'."\n";
				}
			}
		}
		if(tour_exp=='yes'){
			$contents  .= 'travel_category'."\n";
			$contents  .= 'travel_sub_category'."\n";
			if(tour_exp_DA=='VARIABLE')
			{
				$contents  .= 'TA_DA_limit'."\n";
			}
		}
		if(loyalty=='yes'){
			//$contents  .= 'outlet_master'."\n";
			$contents  .= 'loyalty_customer'."\n";
			$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
				$contents  .= 'scheme_details'."\n";
			}
			$sqlqueryloyaltypurchase="SELECT loyalty_card_no AS total_purchase_value FROM `card_transaction`";
			$resultloyaltypurchase = mysqli_query($link,$sqlqueryloyaltypurchase);
			$countloyaltypurchase=mysqli_num_rows($resultloyaltypurchase);
			if($countloyaltypurchase >0)
			{
				$contents  .= 'loyalty_purchase_details'."\n";
			}
			$sqlqueryredeeme="SELECT COUNT(sn) AS total_redeeme FROM redeem_details";
			$resqueryredeeme = mysqli_query($link,$sqlqueryredeeme);
			$countredeeme=mysqli_num_rows($resqueryredeeme);
			if($countredeeme >0)
			{
				$contents  .= 'redeeme_details'."\n";
			}
		}
		$sqlrds="SELECT COUNT(rds_code) AS total_rds FROM rds_master WHERE 1  ".$emp_val_rds."";
		$rsrds=mysqli_query($link,$sqlrds);
		$rowrds=mysqli_fetch_assoc($rsrds);
		$rdscnt=$rowrds['total_rds'];
						
		if($rdscnt >0)
		{
			$contents  .= 'rds_master'."\n";
		}
		//$contents  .= 'emp_master'."\n";
		if(strtoupper(substr($emp_code,0,1))=='E'){
		$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";			
		$rsqueryemp=mysqli_query($link,$sqlqueryemp);
		$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
		$cntemp=$rowqueryemp['total_emp'];
		}
		if(strtoupper(substr($emp_code,0,1))=='C'){
		$sqlqueryemp="SELECT COUNT(customer_code) AS total_emp FROM customer_master WHERE customer_code='".$emp_code."'";			
		$rsqueryemp=mysqli_query($link,$sqlqueryemp);
		$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
		$cntemp=$rowqueryemp['total_emp'];
		}
		if(strtoupper(substr($emp_code,0,1))=='B'){
		$sqlqueryemp="SELECT COUNT(broker_id) AS total_emp FROM employee_master WHERE broker_id='".$emp_code."'";			
		$rsqueryemp=mysqli_query($link,$sqlqueryemp);
		$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
		$cntemp=$rowqueryemp['total_emp'];
		}
						
		if($cntemp >0)
		{
			$contents  .= 'emp_master'."\n";
		}
		if(survey=='yes')
		{
			$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master ";
			$rsquerybranch=mysqli_query($link,$sqlquerybranch);
			$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
			$branchcnt=$rowquerybranch['total_branch'];
							
			if($branchcnt >0)
			{
				$contents  .= 'branch_master'."\n";
			}	
		}
		if(sale=='yes')
		{
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
			
			/*$sqlrds="SELECT rds_code FROM rds_master RM WHERE emp_code='".$emp_code."'";
			$result = mysqli_query($link,$sqlrds);
			$rowrds = mysqli_fetch_assoc($result);
			$rds_code=$rowrds['rds_code'];*/
			
			$sqlquerygit="SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code='".$emp_code."'";
			$rsquerygit=@mysqli_query($link,$sqlquerygit);
			$rowquerygit=@mysqli_fetch_assoc($rsquerygit);
			$gitcnt=$rowquerygit['total_git'];
							
			if($gitcnt >0)
			{
				$contents  .= 'git_master'."\n";
			}
			if($reporting_level >0)
			{
				$contents  .= 'mis_transaction_log'."\n";
				
				$sqlrdslist="SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."";
				$rsrdslist=mysqli_query($link,$sqlrdslist);
				while($rowrdslist=mysqli_fetch_assoc($rsrdslist))
				{
					$rds_list=$rds_list."'".$rowrdslist['rds_code']."'".',';
				}
				$rds_list=substr($rds_list,0,-1);
				$sqlchkdelete="SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN(".$rds_list.") OR SUBSTRING(transaction_id,2,5) IN (".$employee_hierarchy."))";
				$rschkdelete=mysqli_query($link,$sqlchkdelete);
				$rowchkdelete=mysqli_fetch_assoc($rschkdelete);
				$no_of_trans_id=$rowchkdelete['no_of_trans_id'];
				if($no_of_trans_id >0)
				{
					$contents  .= 'mis_transaction_delete'."\n";
				}
			}
			$contents  .= 'user_access'."\n";
		}
		if(sauda_allocation=='yes')
		{
			if(!in_array('sauda',$menu_access_array))
			{
				$contents  .= 'sauda_allocation'."\n";
				if(in_array('order',$menu_access_array))
				{
					$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master ";
					$rsquerybranch=mysqli_query($link,$sqlquerybranch);
					$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
					$branchcnt=$rowquerybranch['total_branch'];
									
					if($branchcnt >0)
					{
						$contents  .= 'branch_master'."\n";
					}	
					if(sauda_depot_wise=='yes')
					{
						$contents  .= 'customer_branch_relation'."\n";
					}
				}
				$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 1 ";
				$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
				$countbranchfreight=mysqli_num_rows($resultbranchfreight);
				if($countbranchfreight >0)
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				$sqlqueryload="SELECT prod_code FROM load_distribution WHERE 1";
				$resultqueryload = mysqli_query($link,$sqlqueryload);
				$countqueryload =mysqli_num_rows($resultqueryload);
				if($countqueryload >0)
				{
					$contents  .= 'load_distribution'."\n";
				}
			}
			if(!in_array('order',$menu_access_array))
			{
				$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master ";
				$rsquerybranch=mysqli_query($link,$sqlquerybranch);
				$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
				$branchcnt=$rowquerybranch['total_branch'];
								
				if($branchcnt >0)
				{
					$contents  .= 'branch_master'."\n";
				}	
				if(sauda_depot_wise=='yes')
				{
					$contents  .= 'customer_branch_relation'."\n";
				}
			}
			if(!in_array('sauda_allocation_app',$menu_access_array))
			{
				$contents  .= 'sauda_allocation_access'."\n";
				if(sauda_allocation_app=='yes')
				{
					$sqlsaudaallocation="SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log WHERE allocation_id<>'' ".$emp_val_rds."";
					$rssaudaallocation=mysqli_query($link,$sqlsaudaallocation);
					$rowsaudaallocation=mysqli_fetch_assoc($rssaudaallocation);
					$total_allocation_cnt=$rowsaudaallocation['total_allocation'];
	
					if($total_allocation_cnt >0)
					{
						$contents  .='sauda_allocation_log'."\n";
					}
				}
			}
			if(!in_array('sauda_mis',$menu_access_array))
			{
				if(sauda_mis=='yes')
				{
					$sqlsaudatransaction="SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log 
									WHERE 1 ".$emp_val_rds."";
					$rssaudatransaction=mysqli_query($link,$sqlsaudatransaction);
					$rowsaudatransaction=mysqli_fetch_assoc($rssaudatransaction);
					$total_sauda_transaction=$rowsaudatransaction['total_sauda_transaction'];

					if($total_sauda_transaction >0)
					{
						$contents  .='sauda_transaction_log'."\n";
					}
				}
			}
			$sqlvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsvertical=mysqli_query($link,$sqlvertical);
			$rowvertical=mysqli_fetch_assoc($rsvertical);
			$vertical_value=$rowvertical['vertical_value'];
			//echo incoterms_vertical;
			$split_incoterms=explode(',',incoterms_vertical);
			if(in_array($vertical_value,$split_incoterms)){
				$sqlhoneycomb= "SELECT * FROM (SELECT HC.prod_code,HC.state_code,DATE_FORMAT(SUBSTRING(HC.datetime,1,10),'%d-%m-%Y') As 
								last_updated_date,HC.plant_name FROM honeycomb_cost HC 
								WHERE HC.vertical_value='".$vertical_value."' ORDER BY HC.datetime DESC) 
								AS SAT GROUP BY 1,2,4";
				$rshoneycomb = mysqli_query($link,$sqlhoneycomb);
				$total_honeycomb = mysqli_num_rows($rshoneycomb);
				if($total_honeycomb >0){
					$contents  .='honeycomb_cost'."\n";
				}
				
				$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,MC.plant_name,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
								last_updated_date FROM margin_cost MC WHERE MC.vertical_value='".$vertical_value."' ORDER BY MC.datetime DESC) 
								AS SAT GROUP BY 1,2,3";
				$rsmargin = mysqli_query($link,$sqlmargin);
				$total_margin = mysqli_num_rows($rsmargin);
				if($total_margin >0){
					$contents  .='margin_cost'."\n";
				}
			}
		}
		if(survey=='yes')
		{
		  	$sqlquerysurveycat="SELECT COUNT(sub_cat_id) AS total_sub_cat_id FROM survey_category_master";
			$rsquerysurveycat=mysqli_query($link,$sqlquerysurveycat);
			$rowquerysurveycat=mysqli_fetch_assoc($rsquerysurveycat);
			$sub_cat_id_cnt=$rowquerysurveycat['total_sub_cat_id'];
			if($sub_cat_id_cnt >0)
			{
		 		 $contents  .= 'survey_category_master'."\n";
			}
			$contents  .= 'survey_input_details'."\n";
			if(survey_type=='yes')
			 {
				$contents  .= 'mall_master'."\n";
				$contents  .= 'mall_survey_relation'."\n";
			 }
			 
			 /////////
			 
			 try{
        			 $sqlsurveyidfetch="SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE 
        						SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND 
        						UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND MEAR.emp_code='".$emp_code."'";
        			 $rssurveyidfetch = mysqli_query($link,$sqlsurveyidfetch);
        			 $countsurveyidfetch=mysqli_num_rows($rssurveyidfetch);
        			 if($countsurveyidfetch >0)
        			 {
        				 $contents  .= 'survey_publish'."\n";
        			 }
        			 
        			 
			    } catch(Exception $e) {
                  
                }
        		
        		try{	 
        			 $sqlmallidfetch="SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM 
        								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ";
        			 $rsmallidfetch = mysqli_query($link,$sqlmallidfetch);
        			 $countmallidfetch=mysqli_num_rows($rsmallidfetch);
        			 if($countmallidfetch >0){
        				 while($rowmallidfetch = mysqli_fetch_assoc($rsmallidfetch))
        				  {					
        					 $mall_name_fetch=$rowmallidfetch['mall_name'];
        					 $mall_id_fetch=$rowmallidfetch['mall_id'];
        					 $sqlquery="SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND 
        							UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
        					 $result = mysqli_query($link,$sqlquery);
        					 $countfs=mysqli_num_rows($result);
        					 if($countfs >0)
        					 {
        						 $contents  .= 'fs_survey_publish'."\n";
        						 break;
        					 }
        				  }
        			 }
        			 
			 
		        } catch(Exception $e) {
                  
                }
			 
			 
			 ////
			 
			 
			 if(strtoupper($nick_name)=='EMAMI' ||  strtoupper($nick_name)=='EMAMIT' ||  strtoupper($nick_name)=='DURO' || strtoupper($nick_name)=='SYLVAN')
			 {
				/*$sqlquerystatedistrict="SELECT SDT.state FROM state_district_town SDT,employee_master EM 
										WHERE EM.state=SDT.state AND EM.emp_code='".$emp_code."'";			
				$resultstatedistrict = mysqli_query($link,$sqlquerystatedistrict);
				$counttstatedistrict=mysqli_num_rows($resultstatedistrict);
				if($counttstatedistrict > 0)
				{
					$contents  .= 'state_district_town'."\n";
				}*/
				$contents  .= 'state_master'."\n";
				
			}
		}
		if(product_promotion=='yes'  || market_feedback=='yes')
		{
			$contents  .= 'generic_oil_master'."\n";
			//$contents  .= 'street_master'."\n";
		}
		if(market_feedback=='yes')
		{
			$contents  .= 'competitor_group_master'."\n";
		}
		$contents  .= 'menu_access'."\n";
		if(!in_array('pending_contract',$menu_access_array))
		{
			if(pending_contract=='yes')
			{
				$sqlpendingcontractcnt="SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."";
				$rspendingcontractcnt=mysqli_query($link,$sqlpendingcontractcnt);
				$rowpendingcontractcnt=mysqli_fetch_assoc($rspendingcontractcnt);
				$pendingcontractcnt=$rowpendingcontractcnt['total_pending_contract'];
				
				if($pendingcontractcnt >0)
				{
					$contents  .= 'pending_contract'."\n";
				}
			}
		}
		if(!in_array('order',$menu_access_array))
		{
			if(order=='yes' && previous_order=='yes'){
				$sqlprevordercnt="SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM 
								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_prev_order."";
				$rsprevordercnt=mysqli_query($link,$sqlprevordercnt);
				$rowprevordercnt=mysqli_fetch_assoc($rsprevordercnt);
				$prevordercnt=$rowprevordercnt['total_prev_order'];
								
				if($prevordercnt >0)
				{
					$contents  .= 'prev_order_counting_master'."\n";
				}
			}
		}
		if(!in_array('order',$menu_access_array))
		{
			if(order=='yes' && order_status=='yes'){
					if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='ABDOST')
					{				
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM
									WHERE 1  ".$emp_val_prev_order."";	
					}
					else
					{
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
									WHERE CM.customer_code=POCM.customer_code  ".$emp_val_prev_order."";	
					}

				$rsorderstatus=mysqli_query($link,$sqlorderstatus);
				$roworderstatus=mysqli_fetch_assoc($rsorderstatus);
				$total_order_status=$roworderstatus['total_order_status'];
								
				if($total_order_status >0)
				{
					$contents  .= 'order_status'."\n";
				}
			}
		}
		if(!in_array('outstanding_ageing',$menu_access_array))
		{
			if(sauda_outstanding=='yes')
			{
			    $sqloutstandingcnt="SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
									 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."";
				$rsoutstandingcnt=mysqli_query($link,$sqloutstandingcnt);
				$rowoutstandingcnt=mysqli_fetch_assoc($rsoutstandingcnt);
				$outstandingcnt=$rowoutstandingcnt['total_outstanding'];
				
				if($outstandingcnt >0)
				{
					$contents  .= 'outstanding_ageing'."\n";
				}
			}
		}
		if(sale_performance=='yes')
		{
			$contents  .= 'sale_performance'."\n";
		}
		if(destination_price_list=='yes' || destination_ordertype_price_list=='yes' || destination=='yes')
		{
			//$contents  .= 'destination_master'."\n";
			if(branch_wise_destination=='yes')
			{
				$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
				$rsempbranch=mysqli_query($link,$sqlempbranch);
				while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
				{
					$branch_value=$rowempbranch['branch_code'];
					$branch_code=$branch_code.$branch_value.',';
				}
				$branch_value_array=explode(',',$branch_code);
				$branch_value_final = "'".implode("','", $branch_value_array)."'";
				$condition_branch=' AND BDF.branch_code IN ('.$branch_value_final.')';
			
				$sqlquerydestination="SELECT COUNT(DM.destination_code) AS total_destination FROM destination_master DM,branch_destination_freight BDF
							WHERE DM.destination_code=BDF.destination_code ".$condition_branch."";
			}
			else
			{
				$sqlquerydestination="SELECT COUNT(destination_code) AS total_destination FROM destination_master";
			}
			$rsquerydestination=mysqli_query($link,$sqlquerydestination);
			$rowquerydestination=mysqli_fetch_assoc($rsquerydestination);
			$destinationcnt=$rowquerydestination['total_destination'];
							
			if($destinationcnt >0)
			{
				$contents  .= 'destination_master'."\n";
			}
		}
		if(target_achievement=='yes')
		{
			$contents  .= 'target_achievement'."\n";
		}
		if(distributor_route_planning=='yes' || distributor_route_emp_relation=='yes')
		{
			if(modified_customer_emp_route=='yes')
			{
				if(employeewise_upperhierarchy=='yes')
				{
					//For selection of Route
					$route_code_array=array();
					$emp_hierarchy_route_condition=' AND RM.emp_code IN('.$employee_hierarchy.')';
					$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
											route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
											".$emp_hierarchy_route_condition;
					$resultquerycustomerroute=mysqli_query($link,$sqlquerycustomerroute);						
					while($rowsquerycustomerroute = mysqli_fetch_assoc($resultquerycustomerroute))
						{
							$route_code=$rowsquerycustomerroute['route_code'];
							if(!in_array($route_code,$route_code_array))
							{
								$route_code_string=$route_code_string."'".$route_code."'".',';
								array_push($route_code_array,$route_code);
							}
						}						
					$route_code_string=substr($route_code_string,0,-1);
					if($nick_name=='OSHEA' || $nick_name=='HALDIRAM' || $nick_name=='PRABHUJI')
					{
						$sqlquerydistributorroute="SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1  
											".$emp_hierarchy_route_condition;
						$resultquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
						$distributor_route_count=0;						
						while($rowsquerydistributorroute = mysqli_fetch_assoc($resultquerydistributorroute))
						 {
							$route_code_distributor=$rowsquerydistributorroute['route_code'];
							if(!in_array($route_code_distributor,$route_code_array))
							{
								$route_code_string=$route_code_string."'".$route_code_distributor."'".',';
								$distributor_route_count++;
								array_push($route_code_array,$route_code_distributor);
							}
						}
						if($distributor_route_count >0){
							$route_code_string=substr($route_code_string,0,-1);
						}
					}
					$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
					$employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
					//$emp_upper_hierarchy_condition=' AND c1.emp_code IN('.$employee_upper_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
					$emp_hierarchy_condition=' AND emp_code IN('.$employee_final_hierarchy.') AND route_code IN('.$route_code_string.')';
					$sqlquerydistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE 1 ".$emp_hierarchy_condition."";
					$resultquerydistributorroute = mysqli_query($link,$sqlquerydistributorroute);
					$countdistributorroute=mysqli_num_rows($resultquerydistributorroute);
					if($countdistributorroute>0){
						$contents  .= 'distributor_route_relation'."\n";
					}
				}
				else{
					$sqlquerydistributorroute="SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds."";			
					$rsquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
					$rowquerydistributorroute=mysqli_fetch_assoc($rsquerydistributorroute);
					$distributorroutecnt=$rowquerydistributorroute['total_distributor'];
					
					if($distributorroutecnt >0)
					{
						$contents  .= 'distributor_route_relation'."\n";
					}
				}
			}
			else
			{
				$sqlquerydistributorroute="SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds."";			
				$rsquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
				$rowquerydistributorroute=mysqli_fetch_assoc($rsquerydistributorroute);
				$distributorroutecnt=$rowquerydistributorroute['total_distributor'];
				
				if($distributorroutecnt >0)
				{
					$contents  .= 'distributor_route_relation'."\n";
				}
			}
		}
		/*if(freight_cost=='yes')
		{
			$contents  .= 'freight_cost'."\n";
		}*/
		if(self_appraisal=='yes')
		{
			if(strtoupper($sale_access_emp)=='PRIMARY' || strtoupper($sale_access_emp)=='SECONDARY')
			{
				$contents  .= 'self_appraisal_details'."\n";
				if(multiple_target_achievement=='yes')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
					$contents  .= 'self_appraisal_branch_wise'."\n";
					if(strtoupper($nick_name)=='ARCHITA' || strtoupper($nick_name)=='DNV' || strtoupper($nick_name)=='DNVFOODS')
		  			{
						$contents  .= 'self_appraisal_emp_wise'."\n";
					}
				}
				if(product_group_wise=='yes')
				{
					$contents  .= 'self_appraisal_productgroup_wise'."\n";
				}
				if(customer_wise_self_appraisal=='yes' && multiple_target_achievement=='no')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
				}
				if(product_wise=='yes')
				{
					$contents  .= 'self_appraisal_product_wise'."\n";
				}
				if(week_wise=='yes')
				{
					$contents  .= 'self_appraisal_emp_week_wise'."\n";
				}
			}
		}
		if(catalogue=='yes')
		{
			$contents  .= 'catalogue_info'."\n";
		}
		if(branchwise_scheme_PDF=='yes')
		{
			$contents  .= 'branchwise_scheme_PDF'."\n";
		}
		if(golden_rules=='yes')
		{
			$contents  .= 'golden_rules'."\n";
		}
		if(input_screen_planwise=='yes')
		{
			$sqlqueryorderplan="SELECT COUNT(CPOD.customer_code) as total_order_plan FROM 
								customer_product_wise_orderplan_details CPOD,customer_route_emp_relation CRER WHERE 
								CPOD.customer_code = CRER.customer_code ".$emp_val_rds."";
			$rsqueryorderplan=mysqli_query($link,$sqlqueryorderplan);
			$orderplancnt=$rowqueryorderplan['total_order_plan'];
			
			if($orderplancnt >0){			
				$contents  .= 'customer_product_wise_orderplan'."\n";
			}
		}
		if(product_qty_wise_TD=='yes')
		{
			$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
			$rsempbranch=mysqli_query($link,$sqlempbranch);
			while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
			{
				$branch_value=$rowempbranch['branch_code'];
				$branch_code=$branch_code.$branch_value.',';
			}
			$branch_value_array=explode(',',$branch_code);
			$branch_value_final = "'".implode("','", $branch_value_array)."'";
			$condition_branch=' AND branch_code IN ('.$branch_value_final.')';
			
			$sqlquerydiscount="SELECT prod_code FROM prodqty_custclass_wise_TD WHERE 1 ".$condition_branch;
			$resultquerydiscount = mysqli_query($link,$sqlquerydiscount);
			$countdiscount=mysqli_num_rows($resultquerydiscount);
			if($countdiscount >0)
			{
				$contents  .= 'prodqty_custclass_wise_TD'."\n";
			}
		}
		if(TD_allocation_app=='yes')
		{
		  	if(!in_array('TD_allocation_app',$menu_access_array))
			  {
				  $contents  .= 'TD_allocation_access'."\n";
			  }
			  $contents  .= 'TD_allocation'."\n";
		}
		if(business_prospect=='yes'){
			$sql_business_prospect="SELECT * FROM `prospective_customer_master`";
			$res_business_prospect=mysqli_query($link,$sql_business_prospect);
			if(mysqli_num_rows($res_business_prospect)>0){
			 $contents  .= 'prospective_customer_master'."\n";
			}
		}
		if(yellow_card=='yes')
		  {
			 $contents  .= 'yellow-card-date-validation'."\n";
			 $contents  .= 'yellow-card-date-validation_customerwise'."\n";
		  }
		  if(schemes=='yes'){
		  	$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_master";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
				$contents  .= 'scheme_master'."\n";
				$sqlqueryfreebies="SELECT COUNT(scheme_id) AS total_scheme_id FROM freebies_master";
				$rsqueryfreebies=mysqli_query($link,$sqlqueryfreebies);
				$rowqueryfreebies=mysqli_fetch_assoc($rsqueryfreebies);
				$total_freebies_cnt=$rowqueryfreebies['total_scheme_id'];
				if($total_freebies_cnt >0)
				{
					$contents  .= 'freebies_master'."\n";	
				}
			}
			$sqlquery="SELECT order_no FROM prev_order_counting_master WHERE 1 ".$emp_val_prev_order." "; 
			$result = mysqli_query($link,$sqlquery);
			$countordersummary=mysqli_num_rows($result);
			if($countordersummary >0)
			{
				$contents  .= 'order_summary'."\n";	
			}
		  }
		  if(stk_audit_msl=='yes')
		  {
			  $contents  .= 'customer_product_wise_msl'."\n";
		  }
		  if(retailer_app=='yes')
		  {
			 //$contents  .= 'stock_allocation'."\n";
			 //Billing start
			 /*$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,
							customer_master CM
							WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";
			$rsempcust=mysqli_query($link,$sqlempcust); 
			$rowempcust=mysqli_fetch_assoc($rsempcust);
			$customer_code_emp=$rowempcust['customer_code'];
			$retailer_app=$rowempcust['retailer_app'];
			$cust_type=$rowempcust['cust_type'];
			$rds_tag=$rowempcust['rds_tag'];
			if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' AND 
							customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')";
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				else
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' ".$emp_val_rds;
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				$sqlquery="SELECT * FROM customer_product_billing WHERE customer_code IN (".$customer_code.")";
				$result = mysqli_query($link,$sqlquery);
				$countbilling=mysqli_num_rows($result);
				if($countbilling >0)
				{
					$contents  .= 'billing_information'."\n";
				}*/
			//Billing End	
			 $contents  .= 'billing_information'."\n";
			 /*$sqlstockbalancedetails = "SELECT SBD.customer_code  FROM customer_route_emp_relation CRR,
							employee_master EM,stock_balance_details SBD WHERE  
							CRR.emp_code=EM.emp_code AND SBD.customer_code=CRR.customer_code AND CRR.emp_code 
							IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$emp_code."',reporting_to) 
							UNION SELECT emp_code FROM employee_master WHERE acedns='Y' AND emp_code='".$emp_code."')";
			$resultstockbalancedetails = mysqli_query($link,$sqlstockbalancedetails);
			$countstockbalance=mysqli_num_rows($resultstockbalancedetails);	
			
			if($countstockbalance >0){
				$contents  .= 'stock_balance_details'."\n";
			}
			$sqlstockmisdetails = "SELECT SMD.sl_no FROM customer_route_emp_relation CRR,
								employee_master EM,stock_mis_details SMD WHERE 
								CRR.emp_code=EM.emp_code AND SMD.customer_code=CRR.customer_code AND CRR.emp_code 
								IN(".$employee_hierarchy.")";
			$resultstockmisdetails = mysqli_query($link,$sqlstockmisdetails);
			$countstockmisdetails=mysqli_num_rows($resultstockmisdetails);	
			if($countstockmisdetails >0){
				$contents  .= 'sale_stock_mis'."\n";
			}*/
		  }
		  if(strtoupper($nick_name)=='ARCHITA')
		  {
			  $contents  .= 'customer_branch_relation'."\n";
		  }
		  if(strtoupper($nick_name)=='START' || strtoupper($nick_name)=='DNV' || strtoupper($nick_name)=='DNVFOODS')
		  {
		 	 $contents  .= 'branch_master'."\n";
		  }
		  if($reporting_level >0)
			{
				$sqlattendancecheckoutdetails = "SELECT trans_id FROM location WHERE (trans_id LIKE 'A%' OR trans_id LIKE 'CH%' OR trans_id LIKE 'WO%' 
												OR trans_id LIKE 'LR%') ".$emp_val_rds."";
				$resattendancecheckoutdetails = mysqli_query($link,$sqlattendancecheckoutdetails);
				$countattendancecheckout=mysqli_num_rows($resattendancecheckoutdetails);	
				
				if($countattendancecheckout >0){
					$contents  .= 'attendance_checkout_details'."\n";
				} 
			}
		 if(strtoupper($nick_name)=='AJANTA')
		  {
			  if(!in_array('survey',$menu_access_array))
			  {
			  	$contents  .= 'sample_master'."\n";
			  }
		  }
		  if(strtoupper($nick_name)=='DURO' || strtoupper($nick_name)=='SYLVAN')
		  {
			  $contents  .= 'site_master'."\n";
			  $sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
			  $rsbranchcode=mysqli_query($link,$sqlbranchcode);
			  $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			  $branch_code=$rowbranchcode['branch_code'];
			  /*$sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE 1 ".$emp_val_rds." AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/
			  $sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE branch_code='".$branch_code."'";			
			  $resultfacilitator = mysqli_query($link,$sqlqueryfacilitator);
			  $countfacilitator=mysqli_num_rows($resultfacilitator);
			  if($countfacilitator >0)
			  {
			  $contents  .= 'facilitator_master'."\n";
			  }
			  $sqlquerydealer="SELECT customer_code FROM dealer_transaction WHERE acedns='Y' ".$emp_val_rds;
			  $resultdealer = mysqli_query($link,$sqlquerydealer);
			  $countdealer=mysqli_num_rows($resultdealer);
			  if($countdealer >0)
			  {
			  	$contents  .= 'dealer_transaction'."\n";
			  }
		  }
		  if(van_sales=='yes')
		  {
			  $contents  .= 'van_stock_allocation'."\n";
		  }
		if(customer_product_relation=='yes'){
			if(strtoupper(substr($emp_code,0,1))=='E')
			{
			$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_route_emp_relation CM 
			WHERE  CPR.customer_code=CM.customer_code ".$emp_val_rds."";
			}
			if(strtoupper(substr($emp_code,0,1))=='C')
			{
			$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR WHERE  CPR.customer_code='".$emp_code."'";
			}
			if(strtoupper(substr($emp_code,0,1))=='B')
			{
			$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_broker_relation CM 
			WHERE  CPR.customer_code=CM.customer_code AND CM.broker_code='".$emp_code."'";
			}
			$rscustomerprod=mysqli_query($link,$sqlcustomerprod);
			$total_cus_prod=mysqli_num_rows($rscustomerprod);
			if($total_cus_prod >0)
			{
				$contents  .= 'customer_product_relation'."\n";
			}
		}
		if(bargain=='yes')
		{
			if(!in_array('bargain',$menu_access_array))
			{
				$contents  .= 'sauda_form_details'."\n";
				$contents  .= 'conversion_data'."\n";
				$contents  .= 'bargain_mrp'."\n";
				$contents  .= 'mcx_rate'."\n";
				$contents  .= 'customer_branch_relation'."\n";
				$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master ";
				$rsquerybranch=mysqli_query($link,$sqlquerybranch);
				$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
				$branchcnt=$rowquerybranch['total_branch'];
								
				if($branchcnt >0)
				{
					$contents  .= 'branch_master'."\n";
				}
				$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,MC.plant_name,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
							last_updated_date FROM margin_cost MC ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2,3";
				$rsmargin = mysqli_query($link,$sqlmargin);
				$total_margin = mysqli_num_rows($rsmargin);
				if($total_margin >0){
					$contents  .='margin_cost'."\n";
				}
				$sqldepot= "SELECT * FROM (SELECT DC.dns_prod_code,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') As last_updated_date,
							DC.branch_code,DC.depot_cost FROM depot_cost DC ORDER BY DC.datetime DESC) AS SAT GROUP BY 1,3 ORDER BY 2 DESC";
				$rsdepot = mysqli_query($link,$sqldepot);
				$total_depot = mysqli_num_rows($rsdepot);
				if($total_depot >0){
					$contents  .='depot_cost'."\n";
				}
				$sqlfreight= "SELECT * FROM (SELECT PF.dns_prod_code,DATE_FORMAT(SUBSTRING(PF.datetime,1,10),'%d-%m-%Y') As last_updated_date,
								PF.branch_code,PF.freight_cost,PF.transport_mode,PF.truck_load  FROM freight_cost PF 
								ORDER BY PF.datetime DESC) AS SAT GROUP BY 1,3,5,6 ORDER BY 2 DESC";
				$rsfreight = mysqli_query($link,$sqlfreight);
				$total_freight = mysqli_num_rows($rsfreight);
				if($total_freight >0){
					$contents  .='primary_freight'."\n";
				}
				$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 1 ";
				$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
				$countbranchfreight=mysqli_num_rows($resultbranchfreight);
				if($countbranchfreight >0)
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					$contents  .= 'broker_master'."\n";
					$contents  .= 'customer_broker_relation'."\n";
					$contents  .= 'brokerage_cost'."\n";
				}
			}
		}
		if(delivery_order=='yes')
		{
		    $sqlqueryDO="SELECT DISTINCT SH.sauda_no  FROM sauda_header SH,sauda_details SD,customer_route_emp_relation CRR WHERE SH.DO_done='no' 
						AND SH.customer_code=CRR.customer_code AND SH.sauda_no=SD.sauda_no AND CRR.emp_code IN(".$employee_hierarchy.")";
			$resultDO = mysqli_query($link,$sqlqueryDO);
			$countDO=mysqli_num_rows($resultDO);
			if($countDO > 0)
			{
				$contents  .= 'bargain_transaction'."\n";
			}
		}
		if(strtoupper($nick_name)=='START' || strtoupper($nick_name)=='STAR' || strtoupper($nick_name)=='GOLDSTONET' || strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='PALSONS')
		  {
		 	 $contents  .= 'branch_geo_fencing'."\n";
		  }
		  
		  if(strtoupper($nick_name)=='NIMBUS')
		  {
			 // $contents  .= 'farmer_master'."\n";
			 // $contents  .= 'BOQ_master'."\n";
			  $contents  .= 'additional_material'."\n";
		  }
		  //echo app_order_approval;
		  if(app_order_approval=='yes'){
				$sqlorderapproval="SELECT COUNT(TAP.customer_code) AS total_order_approval FROM T_APPERPDO_APPROVAL TAP,customer_route_emp_relation CM 
								WHERE CM.customer_code=TAP.customer_code  AND CM.emp_code IN(".$employee_hierarchy.") AND CM.acedns='Y'";
				$rsorderapproval=mysqli_query($link,$sqlorderapproval);
				$roworderapproval=mysqli_fetch_assoc($rsorderapproval);
				$total_order_approval=$roworderapproval['total_order_approval'];
				if($total_order_approval >0)
				{
					$contents  .= 'order_approval'."\n";
				}
				$sqlbranch="SELECT GROUP_CONCAT(branch_code SEPARATOR ',') AS branch_code FROM employee_master 
								WHERE emp_code IN(".$employee_hierarchy.")";
				$rsbranch=mysqli_query($link,$sqlbranch);
				$rowbranch=mysqli_fetch_assoc($rsbranch);
				$branch_code=$rowbranch['branch_code'];
				
				$sqlquery="SELECT branch_code FROM branch_dump WHERE acedns='Y' AND FIND_IN_SET(branch_code,'".$branch_code."') ";
				$result = mysqli_query($link,$sqlquery);
				$count=mysqli_num_rows($result);
				if($count > 0)
				{
					$contents  .= 'branch_dump'."\n";
				}
				$sqlquerybranchdest="SELECT branch_code FROM branch_destination_freight WHERE acedns='Y' AND FIND_IN_SET(branch_code,'".$branch_code."') ";
				$resultbranchdest = mysqli_query($link,$sqlquerybranchdest);
				$countbranchdest=mysqli_num_rows($resultbranchdest);
				if($countbranchdest > 0)
				{
					$contents  .= 'branch_destination'."\n";
				}
			}
			if(strtoupper($nick_name)=='MAGIK')
			{
				$contents  .= 'customer_product_info'."\n";
				$contents  .= 'customer_proposed_product'."\n";
				$contents  .= 'retailer-wise-target-ach'."\n";
			}
			if(strtoupper($nick_name)=='PALSONS')
			{
				$contents  .= 'beatwise_TA_DA'."\n";
				$contents  .= 'customer_product_info'."\n";
			}
			if(strtoupper($nick_name)=='SUPERSHAKTI')
			{
				$contents  .= 'gift_master'."\n";
			}
			if(strtoupper($nick_name)=='MAGIK' || strtoupper($nick_name)=='ELEGANT')
			{
				$contents  .= 'scheme_pdf'."\n";
			}
	}
	else
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
				$sqlquery="SELECT table_name FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
				$resultquery = mysqli_query($link,$sqlquery);
				while($rowquery=mysqli_fetch_assoc($resultquery))
				{
					array_push($need_update_table_array,$rowquery['table_name']);
				}
			}
		}
		if(vertical_fields=='yes'){
			$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempvertical=mysqli_query($link,$sqlempvertical);
			$rowempvertical=mysqli_fetch_assoc($rsempvertical);
			$emp_vertical_value=$rowempvertical['vertical_value'];
			$emp_vertical_value_array=explode(',',$emp_vertical_value);
			//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
			$condition_verticle=" AND (";
			$condition_verticle_one=" AND (";
			$condition_verticle_two=" AND (";
			$condition_two='';
			$condition_three='';
			$condition_four='';
			foreach($emp_vertical_value_array as $emp_vertical_values)
			{
				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',vertical_value) OR";
				$condition_three.=" FIND_IN_SET( '".$emp_vertical_values."',PM.vertical_value) OR";
				$condition_four.=" FIND_IN_SET( '".$emp_vertical_values."',MRP.vertical_value) OR";
			}
			$condition_two=substr($condition_two,0,-2);
			$condition_verticle.=$condition_two.")";
			$condition_three=substr($condition_three,0,-2);
			$condition_verticle_one.=$condition_three.")";
			$condition_four=substr($condition_four,0,-2);
			$condition_verticle_two.=$condition_four.")";
		}
		else
		{
			$condition_verticle="";
			$emp_vertical_value="";
		}
		if(strtoupper($nick_name)=='STAR')
			{
				$sqlsaleaccess="SELECT sale_access FROM employee_master WHERE emp_code='".$emp_code."'";
				$rssaleaccess=mysqli_query($link,$sqlsaleaccess);
				$rowsaleaccess=mysqli_fetch_assoc($rssaleaccess);
				$sale_access_emp=strtoupper($rowsaleaccess['sale_access']);
			}
		//Set up tables download checking
		if(in_array('menu_details',$need_update_table_array))
		{
			$contents  .= 'menu_details'."\n";
		}
		else{
			if(menu_details_download=='yes'){
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
		if(in_array('order_form_details',$need_update_table_array))
		{
			$contents  .= 'order_details'."\n";
		}
		else{
			if(order_form_details_download=='yes'){
				$contents  .= 'order_details'."\n";
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
		if(route_plan=='yes')
		{
			if(in_array('route_plan_details',$need_update_table_array))
			{
				$contents  .= 'route_plan_details'."\n";
			}
			else{
				if(route_plan_details_download=='yes'){
					$contents  .= 'route_plan_details'."\n";
				}
			}
		}
		if(sauda_allocation=='yes')
		{
			if(in_array('sauda_form_details',$need_update_table_array))
			{
				$contents  .= 'sauda_form_details'."\n";
			}
			else{
				if(sauda_form_details_download=='yes'){
					$contents  .= 'sauda_form_details'."\n";
				}
			}
		}
		if(survey=='yes')
		{
			if(in_array('survey_form_details',$need_update_table_array))
			{
				$contents  .= 'survey_form_details'."\n";
			}
			else{
				if(survey_form_details_download=='yes'){
					$contents  .= 'survey_form_details'."\n";
				}
			}
		}
		if(market_feedback=='yes')
		{
			if(in_array('market_feedback_details',$need_update_table_array))
			{
				$contents  .= 'market_feedback_details'."\n";
			}
			else{
				if(market_feedback_details_download=='yes'){
					$contents  .= 'market_feedback_details'."\n";
				}
			}
		}
		if(sauda_allocation=='yes')
		{
			if(!in_array('sauda',$menu_access_array))
			{
				if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					if(in_array('broker_master',$need_update_table_array))
					{
						$contents  .= 'broker_master'."\n";
					}
					else
					{
						$sqlbrokercnt="SELECT COUNT(broker_id) AS total_broker FROM broker_master 
									WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						$rsbrokercnt=mysqli_query($link,$sqlbrokercnt);
						$rowbrokercnt=mysqli_fetch_assoc($rsbrokercnt);
						$brokercnt=$rowbrokercnt['total_broker'];
						
						if($brokercnt >0)
						{
							$contents  .= 'broker_master'."\n";
						}
					}
				}
				if(in_array('branch_route_freight',$need_update_table_array))
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				else
				{
					$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 
											UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
					$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
					$countbranchfreight=mysqli_num_rows($resultbranchfreight);
					if($countbranchfreight >0)
					{
						$contents  .= 'branch_route_freight'."\n";
					}
				}
				if(in_array('load_distribution',$need_update_table_array))
				{
					$contents  .= 'load_distribution'."\n";
				}
				else
				{
					$sqlqueryload="SELECT prod_code FROM load_distribution WHERE UNIX_TIMESTAMP(datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
					$resultqueryload = mysqli_query($link,$sqlqueryload);
					$countqueryload =mysqli_num_rows($resultqueryload);
					if($countqueryload >0)
					{
						$contents  .= 'load_distribution'."\n";
					}
				}
			}
		}
		//Employee condition construction
		if($emp_code=='C0007'){
			$emp_val_condition="";
		}
		else
		{
			if(employeewise_hierarchy=='yes'){
				$employee_hierarchy=return_employee_hierarchy($emp_code);
				$emp_val_condition=' AND emp_code IN('.$employee_hierarchy.')';
				$emp_val_condition_route=' AND CM.emp_code IN('.$employee_hierarchy.')';
				$emp_val_condition_distributor_route=' AND DRR.emp_code IN('.$employee_hierarchy.')';
			}
			else
			{
				$emp_val_condition=" AND emp_code='".$emp_code."'";
				$emp_val_condition_route=" AND CM.emp_code='".$emp_code."'";
				$emp_val_condition_distributor_route=" AND DRR.emp_code='".$emp_code."'";
			}
		}
		if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
			if(in_array('order',$menu_access_array))
			{
				$customer_type_condition=" AND CM.cust_type='D'";
			}
			else
			{
				$customer_type_condition=" AND CM.cust_type IN('R','D')";
				//$customer_type_condition="";
			}
		}
		else
		{
			$customer_type_condition='';
		}
		//route download checking
		if(in_array('route_master',$need_update_table_array))
		{
			$contents  .= 'route_master'."\n";
		}
		else{
			/*$sqlroutecnt="SELECT COUNT(route_code) AS total_route FROM route_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";*/
			
			if(distributor_route_planning=='no')
			{				
				if(modified_customer_emp_route=='yes')
				{
					
					if($nick_name=='HALDIRAM' || $nick_name=='OSHEA' || $nick_name=='PRABHUJI' || $nick_name=='ASL' || strtoupper($nick_name)=='DNVFOODS' || strtoupper($nick_name)=='GOLDSTONE')
					{
						$sqlquerycustomerroute="SELECT DISTINCT route_code FROM distributor_route_relation WHERE 
										UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
						$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
						$countcustomerroute=mysqli_num_rows($resultcustomerroute);
						
						$sqlroutecnt="SELECT RM.route_code from route_master RM,customer_route_emp_relation CM WHERE RM.route_code=CM.route_code AND 
									  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
									  ".$emp_val_condition_route." GROUP BY RM.route_code";
						$rsroutecnt = mysqli_query($link,$sqlroutecnt);
						$routecnt=mysqli_num_rows($rsroutecnt);
						
						if($countcustomerroute >0 || $routecnt >0)
						{
							$contents  .= 'route_master'."\n";
						}
					}
					else if($nick_name=='MAGIK' || $nick_name=='ABDOS')
					{
						$sqlroutecnt="SELECT DISTINCT route_code from customer_route_emp_relation CM  WHERE  
									  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
									  ".$emp_val_condition_route." 	OR (route_code IN(SELECT route_code FROM customer_master WHERE cust_type <> 'D' AND rds_tag 
											 IN(SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE CM.cust_type='D' AND 
											 CRR.customer_code=CM.customer_code AND CRR.emp_code IN(".$employee_hierarchy.") AND CRR.acedns='Y')))";
						$rsroutecnt = mysqli_query($link,$sqlroutecnt);
						$routecnt=mysqli_num_rows($rsroutecnt);
						if($routecnt>0){
							$contents  .= 'route_master'."\n";
						}
					}
					else if(strtoupper($nick_name)=='STAR' &&  $sale_access_emp=='SURVEY')
					{
						$sqlroutecnt="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
										route_code IN(SELECT route_code FROM route_master) AND acedns='Y' AND 
										RM.customer_code IN(SELECT customer_code FROM market_survey_tagging WHERE emp_code='".$emp_code."' )";
						$rsroutecnt = mysqli_query($link,$sqlroutecnt);
						$routecnt=mysqli_num_rows($rsroutecnt);
						if($routecnt>0){
							$contents  .= 'route_master'."\n";
						}
					}
					else if(strtoupper($nick_name)=='STAR' &&  ($sale_access_emp=='BRANDING VERIFICATION' || $sale_access_emp=='BRANDING VERIFICATION VENDOR'))
					{
						$branch_value_array=explode(',',$branch_value_fetched);
						$branch_value_fetched = "'".implode("','", $branch_value_array)."'";
						$condition_branch=' AND CM.branch_code IN ('.$branch_value_fetched.')';
						
						$sqlroutecnt="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM,customer_master CM WHERE 
							RM.customer_code=CM.customer_code AND RM.route_code IN(SELECT route_code FROM route_master) $condition_branch AND RM.acedns='Y'";
						$rsroutecnt = mysqli_query($link,$sqlroutecnt);
						$routecnt=mysqli_num_rows($rsroutecnt);
						if($routecnt>0){
							$contents  .= 'route_master'."\n";
						}
					}
					else
					{
						$sqlroutecnt="SELECT RM.route_code from route_master RM,customer_route_emp_relation CM WHERE RM.route_code=CM.route_code AND 
									  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
									  ".$emp_val_condition_route." GROUP BY RM.route_code";
						$rsroutecnt = mysqli_query($link,$sqlroutecnt);
						$routecnt=mysqli_num_rows($rsroutecnt);
						if($routecnt>0){
							$contents  .= 'route_master'."\n";
						}
					}
				}
				else
				{
				
					$sqlroutecnt="select RM.route_code from route_master RM,customer_master CM WHERE RM.route_code=CM.route_code AND 
								  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
								  ".$customer_type_condition.$emp_val_condition_route." GROUP BY RM.route_code";				
					$rsroutecnt=mysqli_query($link,$sqlroutecnt);
					$routecnt=mysqli_num_rows($rsroutecnt);
					if($routecnt >0)
					{
						$contents  .= 'route_master'."\n";
					}
				}
			}
			else
			{
				/*$sqlroutecnt="select RM.route_code from route_master RM,distributor_route_relation DRR WHERE RM.route_code=DRR.route_code AND 
							  UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition_distributor_route." 
							  GROUP BY RM.route_code";*/
				$sqlroutecnt="SELECT COUNT(route_code) AS total_route FROM route_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";				  
				$rsroutecnt=mysqli_query($link,$sqlroutecnt);
				//$routecnt=mysqli_num_rows($rsroutecnt);
				$rowroutecnt=mysqli_fetch_assoc($rsroutecnt);
				$routecnt=$rowroutecnt['total_route'];
				if($routecnt >0)
				{
					$contents  .= 'route_master'."\n";
				}	
			}
		}
		//bank download checking
		if(collection=='yes')
		{
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
		}
		//customer download checking
		if(in_array('customer_master',$need_update_table_array))
		{
			$contents  .= 'customer_master'."\n";
		}
		else{
			if(modified_customer_emp_route=='yes')
			{
				if(employeewise_upperhierarchy=='yes')
				{
					//For selection of Route
					$route_code_array=array();
					$emp_hierarchy_route_condition=' AND RM.emp_code IN('.$employee_hierarchy.')';
					$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
											route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
											".$emp_hierarchy_route_condition;
					$resultquerycustomerroute=mysqli_query($link,$sqlquerycustomerroute);						
					while($rowsquerycustomerroute = mysqli_fetch_assoc($resultquerycustomerroute))
						{
							$route_code=$rowsquerycustomerroute['route_code'];
							if(!in_array($route_code,$route_code_array))
							{
								$route_code_string=$route_code_string."'".$route_code."'".',';
								array_push($route_code_array,$route_code);
							}
						}						
					$route_code_string=substr($route_code_string,0,-1);
					if($nick_name=='OSHEA' || $nick_name=='HALDIRAM' || $nick_name=='PRABHUJI')
					{
						$sqlquerydistributorroute="SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1  
											".$emp_hierarchy_route_condition;
						$resultquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
						$distributor_route_count=0;						
						while($rowsquerydistributorroute = mysqli_fetch_assoc($resultquerydistributorroute))
						 {
							$route_code_distributor=$rowsquerydistributorroute['route_code'];
							if(!in_array($route_code_distributor,$route_code_array))
							{
								$route_code_string_distributor=$route_code_string_distributor."'".$route_code_distributor."'".',';
								$distributor_route_count++;
								array_push($route_code_array,$route_code_distributor);
							}
						}
						if($distributor_route_count >0){
							$route_code_string_distributor=substr($route_code_string_distributor,0,-1);
						}
					}
					// if(strtoupper($nick_name)=='STAR' &&  $sale_access_emp=='SURVEY')
					// {
					// 	$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code,c1.emp_code,CM.customer_name FROM customer_route_emp_relation c1 INNER JOIN customer_master CM WHERE 
					// 			 c1.customer_code=CM.customer_code AND CM.customer_code IN(SELECT customer_code FROM market_survey_tagging WHERE emp_code='".$emp_code."' ) GROUP BY c1.customer_code  ORDER BY CM.customer_code ASC";
					// }
					// else if(strtoupper($nick_name)=='STAR' &&  ($sale_access_emp=='BRANDING VERIFICATION' || $sale_access_emp=='BRANDING VERIFICATION VENDOR'))
					// {
					// 	$branch_value_array=explode(',',$branch_value_fetched);
					// 	$branch_value_fetched = "'".implode("','", $branch_value_array)."'";
					// 	$condition_branch=' AND CM.branch_code IN ('.$branch_value_fetched.')';
						
					// 	$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code,c1.emp_code,CM.customer_name FROM customer_route_emp_relation c1 INNER JOIN customer_master CM WHERE 
					// 			 c1.customer_code=CM.customer_code $condition_branch GROUP BY c1.customer_code  ORDER BY CM.customer_code ASC";
					// }
					// else{
					// if($route_code_string_distributor !=''){
					// $route_code_string=$route_code_string.','.$route_code_string_distributor;
					// }
					// else
					// {
					// 	$route_code_string=$route_code_string;
					// }
					// $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
					// $employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
					// //$emp_upper_hierarchy_condition=' AND c1.emp_code IN('.$employee_upper_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
					// $emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_final_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
					// if($route_code_string==""){
					//     $emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_final_hierarchy.')';
					// }
					// $sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code FROM customer_route_emp_relation c1 
					// 					WHERE UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_hierarchy_condition."";
					// }

					if(strtoupper($nick_name)=='STAR' &&  $sale_access_emp=='SURVEY')
{
    $sqlquerycustomerroute="SELECT c1.customer_code, 
                                   MAX(c1.route_code) as route_code, 
                                   MAX(c1.emp_code) as emp_code, 
                                   CM.customer_name 
                            FROM customer_route_emp_relation c1 
                            INNER JOIN customer_master CM ON c1.customer_code=CM.customer_code 
                            WHERE CM.customer_code IN(SELECT customer_code FROM market_survey_tagging WHERE emp_code='".$emp_code."') 
                            GROUP BY c1.customer_code, CM.customer_name  
                            ORDER BY CM.customer_code ASC";
}
else if(strtoupper($nick_name)=='STAR' &&  ($sale_access_emp=='BRANDING VERIFICATION' || $sale_access_emp=='BRANDING VERIFICATION VENDOR'))
{
    $branch_value_array=explode(',',$branch_value_fetched);
    $branch_value_fetched = "'".implode("','", $branch_value_array)."'";
    $condition_branch=' AND CM.branch_code IN ('.$branch_value_fetched.')';
    
    $sqlquerycustomerroute="SELECT c1.customer_code, 
                                   MAX(c1.route_code) as route_code, 
                                   MAX(c1.emp_code) as emp_code, 
                                   CM.customer_name 
                            FROM customer_route_emp_relation c1 
                            INNER JOIN customer_master CM ON c1.customer_code=CM.customer_code 
                            WHERE 1=1 $condition_branch 
                            GROUP BY c1.customer_code, CM.customer_name  
                            ORDER BY CM.customer_code ASC";
}
else{
    if($route_code_string_distributor !=''){
        $route_code_string=$route_code_string.','.$route_code_string_distributor;
    }
    else
    {
        $route_code_string=$route_code_string;
    }
    $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
    $employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
    $emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_final_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
    
    if($route_code_string==""){
        $emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_final_hierarchy.')';
    }
    
    
    $sqlquerycustomerroute="SELECT DISTINCT c1.customer_code, c1.route_code 
                            FROM customer_route_emp_relation c1 
                            WHERE UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') 
                            ".$emp_hierarchy_condition."";
}
					//echo $sqlquerycustomerroute;die;
					$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
					$countcustomerroute=mysqli_num_rows($resultcustomerroute);
					if($countcustomerroute>0){
						$contents  .= 'customer_master'."\n";
					}
				}
				else{
					if(strtoupper($nick_name)=='ARCHITA')
					{
						$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code FROM customer_route_emp_relation c1 
											WHERE UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition." OR 
											c1.customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag IN(SELECT DISTINCT distributor_code FROM distributor_route_relation WHERE acedns='Y' AND emp_code='".$emp_code."'))
											";
					}
					else if(strtoupper($nick_name)=='MAGIK' || strtoupper($nick_name)=='ABDOS')
					{
						$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code FROM customer_route_emp_relation c1 
											WHERE UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition." 
											 OR (c1.customer_code IN(SELECT customer_code FROM customer_master WHERE cust_type <> 'D' AND rds_tag 
											 IN(SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE CM.cust_type='D' AND 
											 CRR.customer_code=CM.customer_code AND CRR.emp_code IN(".$employee_hierarchy.") AND CRR.acedns='Y')))";
					}
					else
					{
						if(distributor_route_emp_relation=='yes' && (strtoupper($nick_name)=='ASL' || strtoupper($nick_name)=='DNVFOODS' || strtoupper($nick_name)=='GOLDSTONE'))
						 {
							$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code FROM customer_route_emp_relation c1 WHERE 
												   1 AND UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND 
												(c1.customer_code IN
												(SELECT customer_code FROM customer_master WHERE rds_tag IN
												(SELECT DISTINCT distributor_code FROM distributor_route_relation 
												WHERE acedns='Y' AND emp_code IN(".$employee_hierarchy.") AND distributor_code <> '') AND 
												route_code IN(SELECT DISTINCT route_code FROM distributor_route_relation 
												WHERE acedns='Y' AND emp_code IN(".$employee_hierarchy.") AND distributor_code <> '')
												) OR c1.customer_code IN(SELECT DISTINCT 
												distributor_code FROM distributor_route_relation WHERE acedns='Y' 
												AND emp_code IN(".$employee_hierarchy.") AND distributor_code <> '')
												) 
												ORDER BY customer_code ASC,acedns DESC";
						 }
						 else
						 {
							$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code FROM customer_route_emp_relation c1 
											WHERE UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_condition."";
						 }
					}
					 $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
					 $countcustomerroute=mysqli_num_rows($resultcustomerroute);
					  if($countcustomerroute>0){
							$contents  .= 'customer_master'."\n";
					 }
				}
			}
			else
			{
				$sqlcustomercnt="SELECT COUNT(CM.customer_code) AS total_customer FROM customer_master CM 
								WHERE UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$customer_type_condition.
								$emp_val_condition."";
				$rscustomercnt=mysqli_query($link,$sqlcustomercnt);
				$rowcustomercnt=mysqli_fetch_assoc($rscustomercnt);
				$customercnt=$rowcustomercnt['total_customer'];
								
				if($customercnt >0)
				{
					$contents  .= 'customer_master'."\n";
				}
			}
		}
		// customer credit limit download checking
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
		if(collection=='yes' || outstanding=='yes' || outstanding_ageing=='yes' || collection_forecast=='yes')
		{
			$contents  .= 'outstanding_master'."\n";
		}
		//product download checking
		if(no_of_filter > 1){
			if(in_array('product_group_master',$need_update_table_array))
			{
				$contents  .= 'product_group_master'."\n";
			}
			else{
				if($nick_name=='RUPA' && substr($emp_vertical_value,0,1)=='M'){  //For RUPA M'SERIES
					$sqlprodgroupcnt="SELECT COUNT(product_group_code) AS total_product_group  FROM product_group_master WHERE vertical_value 
								LIKE 'M%' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				}
				else
				{
					$sqlprodgroupcnt="SELECT COUNT(product_group_code) AS total_product_group FROM product_group_master 
									WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
				}
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
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
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
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
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
			if(branch_wise_product=='yes')
			{
			    $sqlprodcnt="SELECT COUNT(PM.prod_code) AS total_product FROM product_master PM,employee_master EM
							WHERE FIND_IN_SET(PM.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND 
							UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_one."";
				$rsprodcnt=mysqli_query($link,$sqlprodcnt);
				$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
				$prodcnt=$rowprodcnt['total_product'];
			}
			if(state_wise_product=='yes')
			{
				$sqlstate="SELECT state FROM employee_master WHERE emp_code='".$emp_code."'";
				$rsstate=mysqli_query($link,$sqlstate);
				$rowstate=mysqli_fetch_assoc($rsstate);
				$state_name=$rowstate['state'];
				
				$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_name."%'";
				$rsstatecode=mysqli_query($link,$sqlstatecode);
				$rowstatecode=mysqli_fetch_assoc($rsstatecode);
				$state_code=$rowstatecode['state_code'];
				$sqlprodcnt="SELECT COUNT(PM.prod_code) AS total_product
								FROM product_master PM WHERE PM.prod_desc <>'' AND FIND_IN_SET( PM.state_code,'".$state_code."') AND 
							UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_one."";
				$rsprodcnt=mysqli_query($link,$sqlprodcnt);
				$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
				$prodcnt=$rowprodcnt['total_product'];				
			}
			else
			{
				$sqlprodcnt="SELECT COUNT(prod_code) AS total_product FROM product_master 
							WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
				$rsprodcnt=mysqli_query($link,$sqlprodcnt);
				$rowprodcnt=mysqli_fetch_assoc($rsprodcnt);
				$prodcnt=$rowprodcnt['total_product'];
			}
			if($prodcnt >0)
			{
				$contents  .= 'product_master'."\n";
			}
		}
		//product closing stock download checking
		if(cl_stk=='yes' || sale=='yes'){
			if(in_array('closing_stock',$need_update_table_array))
			{
				$contents  .= 'closing_stock'."\n";
			}
		else{
			if(cl_stk=='yes')
			{
				if(branch_wise_cl_stk=='yes')
				{
					$sqlprodstkcnt="SELECT COUNT(product_code) AS total_product_stk FROM branch_product_wise_stock 
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND product_code!=''";
					$rsprodstkcnt=mysqli_query($link,$sqlprodstkcnt);
					$rowprodstkcnt=mysqli_fetch_assoc($rsprodstkcnt);
					$prodstkcnt=$rowprodstkcnt['total_product_stk'];
					if($prodstkcnt >0)
					{
						$contents  .= 'closing_stock'."\n";
					}
				}
				else
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
			}
			if(sale=='yes')
			{
				/*$sqlselect="SELECT loggedin_date_time FROM changepassword  WHERE emp_code='".$emp_code."'";
				$rsselect=mysqli_query($link,$sqlselect);
				$rowselect=mysqli_fetch_assoc($rsselect);
				$loggedin_date_time_database=substr($rowselect['loggedin_date_time'],0,10);
				if(strtotime($loggedin_date_time_database)< strtotime($server_current_date))
				{*/
					$contents  .= 'closing_stock'."\n";
				//}
			}
		  }
		}
		//mrp download checking
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && (order=='yes' || van_sales=='yes')){
			if(in_array('mrp',$need_update_table_array))
			{
				$contents  .= 'mrp_master'."\n";
			}
			else{
				if(branch_wise_mrp=='yes')
				{
					$sqlmrpcnt="SELECT COUNT(MRP.mrp_code) AS total_mrp FROM mrp MRP,employee_master EM WHERE 
								FIND_IN_SET(MRP.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND 
								UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_two."";
					$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
					$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
					$mrpcnt=$rowmrpcnt['total_mrp'];
				}
				else
				{
					$sqlmrpcnt="SELECT COUNT(mrp_code) AS total_mrp FROM mrp 
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
					$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
					$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
					$mrpcnt=$rowmrpcnt['total_mrp'];
				}
								
				if($mrpcnt >0)
				{
					$contents  .= 'mrp_master'."\n";
				}
			}
		}
		if(strtoupper($nick_name)=='ARCHITA')
		  {
			if(in_array('mrp',$need_update_table_array))
			{
				$contents  .= 'mrp_master'."\n";
			}
			else
			{ 
				$sqlstatecode="SELECT SM.state_code,EM.vertical_value FROM state_master SM,employee_master EM WHERE EM.state=SM.dns_state_code 
						AND EM.emp_code='".$emp_code."'";
				$rsstatecode=mysqli_query($link,$sqlstatecode);
				$rowstatecode=mysqli_fetch_assoc($rsstatecode);
				$state_code=$rowstatecode['state_code'];	
				$vertical_value=$rowstatecode['vertical_value'];				
				$sqlmrpcnt="SELECT COUNT(mrp_code) AS total_mrp FROM mrp  WHERE 
						FIND_IN_SET(state_code,'".$state_code."') AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND FIND_IN_SET(vertical_value,'".$vertical_value."')";
				$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
				$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
				$mrpcnt=$rowmrpcnt['total_mrp'];
				if($mrpcnt >0)
				{
					$contents  .= 'mrp_master'."\n";
				}
			}
		  }
		//if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes'){
		if((mrp=='yes' || (sale_rate=='yes' && sauda_sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes'){	
			if(in_array('sauda_mrp',$need_update_table_array))
			{
				$contents  .= 'sauda_mrp'."\n";
			}
			else{
				if(branch_wise_mrp=='yes')
				{
					$sqlmrpcnt="SELECT COUNT(MRP.mrp_code) AS total_mrp FROM sauda_mrp MRP,employee_master EM WHERE 
								FIND_IN_SET(MRP.branch_code,EM.branch_code) AND EM.emp_code='".$emp_code."' AND 
								UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle_two."";
					$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
					$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
					$mrpcnt=$rowmrpcnt['total_mrp'];
				}
				else
				{
					$sqlmrpcnt="SELECT COUNT(mrp_code) AS total_mrp FROM sauda_mrp 
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
					$rsmrpcnt=mysqli_query($link,$sqlmrpcnt);
					$rowmrpcnt=mysqli_fetch_assoc($rsmrpcnt);
					$mrpcnt=$rowmrpcnt['total_mrp'];
				}
								
				if($mrpcnt >0)
				{
					$contents  .= 'sauda_mrp'."\n";
				}
			}
		}
		if(stk_audit=='yes' && previous_stock=='yes'){
			if(in_array('prev_stock_counting_master',$need_update_table_array))
			{
				$contents  .= 'prev_stock_counting_master'."\n";
			}
		else{
			$sqlprevstkcnt="SELECT COUNT(PSCM.customer_code) AS total_prev_stk FROM prev_stock_counting_master PSCM,customer_master CM 
							WHERE CM.customer_code=PSCM.customer_code 
							AND UNIX_TIMESTAMP(PSCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
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
			if(route_customer_planning=='yes')
			{
				if(in_array('route_customer_plan_transaction',$need_update_table_array))
				{
					$contents  .= 'route_customer_plan'."\n";
				}
				$sqlroutecustomerplancnt="SELECT COUNT(route_plan_trans_id) AS total_route_customer_plan FROM route_customer_plan 
									WHERE SUBSTRING(route_plan_trans_id,3,5)='".$emp_code."' 
									AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsroutecustomerplancnt=mysqli_query($link,$sqlroutecustomerplancnt);
				$rowroutecustomerplancnt=mysqli_fetch_assoc($rsroutecustomerplancnt);
				$routecustomerplancnt=$rowroutecustomerplancnt['total_route_customer_plan'];
				
				if($routecustomerplancnt >0)
				{
					$contents  .= 'route_customer_plan'."\n";
				}
			}
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
			if(tour_exp_DA=='VARIABLE')
			{
				$contents  .= 'TA_DA_limit'."\n";
			}
		}
		if(loyalty=='yes'){
			$contents  .= 'loyalty_customer'."\n";
			$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_details";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
				$contents  .= 'scheme_details'."\n";
			}
			if(in_array('loyalty_purchase_details',$need_update_table_array))
			{
				$contents  .= 'loyalty_purchase_details'."\n";
			}
			else
			{
				$sqlqueryloyaltypurchase="SELECT loyalty_card_no AS total_purchase_value FROM `card_transaction` WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$resultloyaltypurchase = mysqli_query($link,$sqlqueryloyaltypurchase);
				$countloyaltypurchase=mysqli_num_rows($resultloyaltypurchase);
				if($countloyaltypurchase >0)
				{
					$contents  .= 'loyalty_purchase_details'."\n";
				}
			}
			$sqlqueryredeeme="SELECT COUNT(sn) AS total_redeeme FROM redeem_details";
			$resqueryredeeme = mysqli_query($link,$sqlqueryredeeme);
			$countredeeme=mysqli_num_rows($resqueryredeeme);
			if($countredeeme >0)
			{
				$contents  .= 'redeeme_details'."\n";
			}
			if($db_version_code <'7.0')
			{
				$contents  .= 'card_transaction'."\n";
			}
		}
		//rds download checking
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
		
		//$contents  .= 'emp_master'."\n";
		if(in_array('emp_master',$need_update_table_array))
			{
				$contents  .= 'emp_master'."\n";
			}
			else
			{
				if(strtoupper(substr($emp_code,0,1))=='E'){
				$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
				//$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";					
				$rsqueryemp=mysqli_query($link,$sqlqueryemp);
				$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
				$cntemp=$rowqueryemp['total_emp'];
				}
				if(strtoupper(substr($emp_code,0,1))=='C'){
				$sqlqueryemp="SELECT COUNT(customer_code) AS total_emp FROM customer_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND customer_code='".$emp_code."'";
				//$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";					
				$rsqueryemp=mysqli_query($link,$sqlqueryemp);
				$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
				$cntemp=$rowqueryemp['total_emp'];
				}
				if(strtoupper(substr($emp_code,0,1))=='B'){
				$sqlqueryemp="SELECT COUNT(broker_id) AS total_emp FROM broker_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND broker_id='".$emp_code."'";
				//$sqlqueryemp="SELECT COUNT(emp_code) AS total_emp FROM employee_master WHERE 1 ".$emp_val_rds."";					
				$rsqueryemp=mysqli_query($link,$sqlqueryemp);
				$rowqueryemp=mysqli_fetch_assoc($rsqueryemp);
				$cntemp=$rowqueryemp['total_emp'];
				}
								
				if($cntemp >0)
				{
					$contents  .= 'emp_master'."\n";
				}
			}
		
		if(survey=='yes')
		{
		  if(in_array('branch_master',$need_update_table_array))
			{
				$contents  .= 'branch_master'."\n";
			}
			else
			{
			   /*$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/
				$sqlquerybranch="SELECT COUNT(BM.branch_code) AS total_branch FROM 
							branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND 
							EM.emp_code='".$emp_code."'";							
				$rsquerybranch=mysqli_query($link,$sqlquerybranch);
				$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
				$branchcnt=$rowquerybranch['total_branch'];
								
				if($branchcnt >0)
				{
					$contents  .= 'branch_master'."\n";
				}
			}
		}
		//Checking of sale related download
		if(sale=='yes')
		{
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
			if(in_array('goods_in_transit',$need_update_table_array))
			{
				$sqlquerygit="SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE GIT.receiver_code='".$emp_code."'";
				$rsquerygit=@mysqli_query($link,$sqlquerygit);
				$rowquerygit=@mysqli_fetch_assoc($rsquerygit);
				$gitcnt=$rowquerygit['total_git'];
								
				if($gitcnt >0)
				{
					$contents  .= 'git_master'."\n";
				}
			}
			else
			{
				$sqlquerygit="SELECT COUNT(GIT.grn_no) AS total_git FROM goods_in_transit GIT  WHERE 
							GIT.transaction_type='ST' AND GIT.receiver_code='".$emp_code."' AND UNIX_TIMESTAMP(GIT.download_time) > 
							UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerygit=mysqli_query($link,$sqlquerygit);
				$rowquerygit=mysqli_fetch_assoc($rsquerygit);
				$gitcnt=$rowquerygit['total_git'];
				if($gitcnt >0)
				{
					$contents  .= 'git_master'."\n";
				}
			}
			
			if($reporting_level >0)
			{
				if(in_array('mis_transaction_log',$need_update_table_array))
				{
					$sqlquerymis="SELECT COUNT(trans_id) AS total_trans_id FROM mis_transaction_log WHERE 1 ".$emp_val_rds."";
					$rsquerymis=mysqli_query($link,$sqlquerymis);
					$rowquerymis=mysqli_fetch_assoc($rsquerymis);
					$mis_trans_id_cnt=$rowquerymis['total_trans_id'];
					if($mis_trans_id_cnt >0)
					{
						$contents  .= 'mis_transaction_log'."\n";
					}
				}
				else
				{
					$sqlquerymis="SELECT COUNT(trans_id) AS total_trans_id FROM mis_transaction_log WHERE 1 ".$emp_val_rds." 
								AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					$rsquerymis=mysqli_query($link,$sqlquerymis);
					$rowquerymis=mysqli_fetch_assoc($rsquerymis);
					$mis_trans_id_cnt=$rowquerymis['total_trans_id'];
					if($mis_trans_id_cnt >0)
					{
						$contents  .= 'mis_transaction_log'."\n";
					}
				}
			}
			if($db_version_code <='5.5')
			{
				$contents  .= 'transaction_log'."\n";
			}
			if($reporting_level >0)
			{
				$sqlrdslist="SELECT rds_code FROM rds_master WHERE 1  ".$emp_val_rds."";
				$rsrdslist=mysqli_query($link,$sqlrdslist);
				while($rowrdslist=mysqli_fetch_assoc($rsrdslist))
				{
					$rds_list=$rds_list."'".$rowrdslist['rds_code']."'".',';
				}
				$rds_list=substr($rds_list,0,-1);
				$sqlchkdelete="SELECT COUNT(transaction_id) AS no_of_trans_id FROM activity_log WHERE mis_updated_flag_app='0' AND (rds_code IN(".$rds_list.") OR SUBSTRING(transaction_id,2,5) IN (".$employee_hierarchy."))";
				$rschkdelete=mysqli_query($link,$sqlchkdelete);
				$rowchkdelete=mysqli_fetch_assoc($rschkdelete);
				$no_of_trans_id=$rowchkdelete['no_of_trans_id'];
				if($no_of_trans_id >0)
				{
					$contents  .= 'mis_transaction_delete'."\n";
				}
			}
			$contents  .= 'user_access'."\n";
		}
		if(sauda_allocation=='yes')
		{
			if(!in_array('sauda',$menu_access_array))
			{
				$contents  .= 'sauda_allocation'."\n";
				if(in_array('order',$menu_access_array))
				{
					if(in_array('branch_master',$need_update_table_array))
					{
						$contents  .= 'branch_master'."\n";
					}
					else
					{
						$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
						$rsquerybranch=mysqli_query($link,$sqlquerybranch);
						$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
						$branchcnt=$rowquerybranch['total_branch'];
										
						if($branchcnt >0)
						{
							$contents  .= 'branch_master'."\n";
						}
					}
					if(sauda_depot_wise=='yes')
					{
						if(in_array('customer_branch_relation',$need_update_table_array))
						{
							$contents  .= 'customer_branch_relation'."\n";
						}
						else
						{
							$sqlquerycustomerrds="SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds." 
										AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
							$rsquerycustomerrds=mysqli_query($link,$sqlquerycustomerrds);
							$rowquerycustomerrds=mysqli_fetch_assoc($rsquerycustomerrds);
							$customer_depot_cnt=$rowquerycustomerrds['total_customer_depot'];
							if($customer_depot_cnt >0)
							{
								$contents  .= 'customer_branch_relation'."\n";
							}
						}
					}
				}
			}
			if(!in_array('sauda_allocation_app',$menu_access_array))
			{
				$contents  .= 'sauda_allocation_access'."\n";
				if(sauda_allocation_app=='yes')
				{
					if(in_array('sauda_allocation_log',$need_update_table_array))
					{
						$contents  .= 'sauda_allocation_log'."\n";
					}
					else
					{
						$sqlsaudaallocation="SELECT count(allocation_id) AS total_allocation FROM sauda_allocation_log 
										WHERE allocation_id<>'' AND UNIX_TIMESTAMP(allocation_date) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
						$rssaudaallocation=mysqli_query($link,$sqlsaudaallocation);
						$rowsaudaallocation=mysqli_fetch_assoc($rssaudaallocation);
						$total_allocation_cnt=$rowsaudaallocation['total_allocation'];
	
						if($total_allocation_cnt >0)
						{
							$contents  .='sauda_allocation_log'."\n";
						}
					}
				}
			}
			if(!in_array('sauda_mis',$menu_access_array))
			{
				if(sauda_mis=='yes')
				{
					if(in_array('sauda_transaction_log',$need_update_table_array))
					{
						$contents  .= 'sauda_transaction_log'."\n";
					}
					else
					{
						$sqlsaudatransaction="SELECT count(sauda_no) AS total_sauda_transaction FROM sauda_transaction_log 
										WHERE  UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
						$rssaudatransaction=mysqli_query($link,$sqlsaudatransaction);
						$rowsaudatransaction=mysqli_fetch_assoc($rssaudatransaction);
						$total_sauda_transaction=$rowsaudatransaction['total_sauda_transaction'];
	
						if($total_sauda_transaction >0)
						{
							$contents  .='sauda_transaction_log'."\n";
						}
					}
				}
			}
			$sqlvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsvertical=mysqli_query($link,$sqlvertical);
			$rowvertical=mysqli_fetch_assoc($rsvertical);
			$vertical_value=$rowvertical['vertical_value'];
			$split_incoterms=explode(',',incoterms_vertical);
			if(in_array($vertical_value,$split_incoterms)){
				
				if(in_array('honeycomb_cost',$need_update_table_array))
					{
						$contents  .= 'honeycomb_cost'."\n";
					}
					else
					{
						$sqlhoneycomb= "SELECT * FROM (SELECT HC.prod_code,HC.state_code,DATE_FORMAT(SUBSTRING(HC.datetime,1,10),'%d-%m-%Y') As 
										last_updated_date,HC.plant_name,HC.transport_mode FROM honeycomb_cost HC WHERE HC.vertical_value='".$vertical_value."' AND 
										UNIX_TIMESTAMP(HC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') ORDER BY HC.datetime DESC) AS SAT GROUP BY 1,2,4,5";
						$rshoneycomb = mysqli_query($link,$sqlhoneycomb);
						$total_honeycomb = mysqli_num_rows($rshoneycomb);
						if($total_honeycomb >0){
							$contents  .='honeycomb_cost'."\n";
						}
					}
					if(in_array('margin_cost',$need_update_table_array))
					{
						$contents  .= 'margin_cost'."\n";
					}
					$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
									last_updated_date FROM margin_cost MC WHERE MC.vertical_value='".$vertical_value."' AND 
									UNIX_TIMESTAMP(MC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2";
					$rsmargin = mysqli_query($link,$sqlmargin);
					$total_margin = mysqli_num_rows($rsmargin);
					if($total_margin >0){
						$contents  .='margin_cost'."\n";
					}
			}
		}
		if(survey=='yes')
		{
		  if(in_array('survey_category_master',$need_update_table_array))
			{
				$contents  .= 'survey_category_master'."\n";
			}
			else
			 {
				$sqlquerysurveycat="SELECT COUNT(SCM.sub_cat_id) AS total_sub_cat_id FROM survey_category_master SCM WHERE 
									UNIX_TIMESTAMP(SCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveycat=mysqli_query($link,$sqlquerysurveycat);
				$rowquerysurveycat=mysqli_fetch_assoc($rsquerysurveycat);
				$sub_cat_id_cnt=$rowquerysurveycat['total_sub_cat_id'];
				if($sub_cat_id_cnt >0)
				{
					$contents  .= 'survey_category_master'."\n";
				}
			 }
			 
			 if(in_array('table_view',$need_update_table_array))
			 {
				$contents  .= 'survey_table_view'."\n";
			 }
			else
			 {
				$sqlquerysurveytableview="SELECT COUNT(row_id) AS total_survey_table_view FROM table_view WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveytableview=mysqli_query($link,$sqlquerysurveytableview);
				$rowquerysurveytableview=mysqli_fetch_assoc($rsquerysurveytableview);
				$survey_table_view_cnt=$rowquerysurveytableview['total_survey_table_view'];
				if($survey_table_view_cnt >0)
				{
					$contents  .= 'survey_table_view'."\n";
				}
			 }

			 if(in_array('survey_input',$need_update_table_array))
			 {
				$contents  .= 'survey_input_details'."\n";
			 }
			else
			 {
				$sqlquerysurveyinput="SELECT COUNT(SI.row_id) AS total_survey_input_id FROM survey_input SI WHERE 
									UNIX_TIMESTAMP(SI.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerysurveyinput=mysqli_query($link,$sqlquerysurveyinput);
				$rowquerysurveyinput=mysqli_fetch_assoc($rsquerysurveyinput);
				$survey_input_cnt=$rowquerysurveyinput['total_survey_input_id'];
				if($survey_input_cnt >0)
				{
					$contents  .= 'survey_input_details'."\n";
				}
			 }
			 if(in_array('mall_master',$need_update_table_array) || survey_type=='yes')
			 {
				$contents  .= 'mall_master'."\n";
			 }
			 if(in_array('mall_survey_relation',$need_update_table_array) || survey_type=='yes')
			 {
				$contents  .= 'mall_survey_relation'."\n";
			 }
			 if(in_array('survey_publish',$need_update_table_array))
			 {
				 $contents  .= 'survey_publish'."\n";
			 }
			 else
			 {
			     try{
				 $sqlsurveyidfetch="SELECT SH.survey_id FROM survey_header SH,mall_emp_audit_relation MEAR WHERE 
							SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND 
							UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND MEAR.emp_code='".$emp_code."'";
							
				 $rssurveyidfetch = mysqli_query($link,$sqlsurveyidfetch);
				 $countsurveyidfetch=mysqli_num_rows($rssurveyidfetch);
				 if($countsurveyidfetch >0)
				 {
					 $contents  .= 'survey_publish'."\n";
				 }
				 
			 } catch(Exception $e) {
                  
                }
				 
			 }
			 if(in_array('fs_survey_publish',$need_update_table_array))
			 {
				 $contents  .= 'fs_survey_publish'."\n";
			 }
			 else
			 {
			     try{
				 $sqlmallidfetch="SELECT MER.mall_id,MM.mall_name FROM mall_emp_relation MER,mall_master MM 
								 WHERE MM.mall_id=MER.mall_id AND MER.status='assigned' AND MER.emp_code='".$emp_code."' ";
				 $rsmallidfetch = mysqli_query($link,$sqlmallidfetch);
				 $countmallidfetch=mysqli_num_rows($rsmallidfetch);
				 if($countmallidfetch >0){
					 $countdata=0;
					 while($rowmallidfetch = mysqli_fetch_assoc($rsmallidfetch))
					  {					
						 $mall_id_fetch=$rowmallidfetch['mall_id'];
						 $mall_name_fetch=$rowmallidfetch['mall_name'];
						 $sqlquery="SELECT * from foot_soldier  WHERE mall_id='".$mall_id_fetch."' AND DCE_status='NOT DONE' AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						 $result = mysqli_query($link,$sqlquery);
						 $countfs=mysqli_num_rows($result);
						 if($countfs >0)
						 {
							 $contents  .= 'fs_survey_publish'."\n";
							 break;
						 }
					  }
				 }
			    } catch(Exception $e) {
                  
                }
			 }
			if(strtoupper($nick_name)=='EMAMI' ||  strtoupper($nick_name)=='EMAMIT' ||  strtoupper($nick_name)=='DURO_OLD')
			{
				if(in_array('state_district_town',$need_update_table_array))
				{
					$contents  .= 'state_district_town'."\n";
				}
				else
				{
					$sqlquerystatedistrict="SELECT SDT.state FROM state_district_town SDT,employee_master EM 
											WHERE EM.state=SDT.state AND EM.emp_code='".$emp_code."'";			
					$resultstatedistrict = mysqli_query($link,$sqlquerystatedistrict);
					$counttstatedistrict=mysqli_num_rows($resultstatedistrict);
					if($counttstatedistrict > 0)
					{
						$contents  .= 'state_district_town'."\n";
					}
				}
				$contents  .= 'state_master'."\n";
			}
		}
	    if(product_promotion=='yes' || market_feedback=='yes')
		{
			$contents  .= 'generic_oil_master'."\n";
			/*if(in_array('street_master',$need_update_table_array))
			 {
				$contents  .= 'street_master'."\n";
			 }
			else
			 {
				$sqlquerystreet="SELECT COUNT(street_name) AS total_street FROM street_master WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsquerystreet=mysqli_query($link,$sqlquerystreet);
				$rowquerystreet=mysqli_fetch_assoc($rsquerystreet);
				$street_cnt=$rowquerystreet['total_street'];
				if($street_cnt >0)
				{
					$contents  .= 'street_master'."\n";
				}
			 }*/
		}
		if(market_feedback=='yes')
		{
			$contents  .= 'competitor_group_master'."\n";
		}
		$contents  .= 'menu_access'."\n";
		if(!in_array('pending_contract',$menu_access_array))
		{
			if(pending_contract=='yes')
			{
				if(in_array('pending_contract_ageing',$need_update_table_array))
				{
					$contents  .= 'pending_contract'."\n";
				}
				else
				 {
					$sqlpendingcontractcnt="SELECT COUNT(PC.customer_code) AS total_pending_contract FROM pending_contract_ageing PC,customer_master CM
									 WHERE CM.customer_code=PC.customer_code ".$emp_val_rds."";
					$rspendingcontractcnt=mysqli_query($link,$sqlpendingcontractcnt);
					$rowpendingcontractcnt=mysqli_fetch_assoc($rspendingcontractcnt);
					$pendingcontractcnt=$rowpendingcontractcnt['total_pending_contract'];
					
					if($pendingcontractcnt >0)
					{
						$contents  .= 'pending_contract'."\n";
					}
				 }
			}
		}
		if(!in_array('order',$menu_access_array))
		{
		    if(order=='yes' && previous_order=='yes'){
				if(in_array('branch_master',$need_update_table_array))
				{
					$contents  .= 'branch_master'."\n";
				}
				else
				{
					$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
					$rsquerybranch=mysqli_query($link,$sqlquerybranch);
					$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
					$branchcnt=$rowquerybranch['total_branch'];
									
					if($branchcnt >0)
					{
						$contents  .= 'branch_master'."\n";
					}
				}
			}
				if(sauda_depot_wise=='yes')
				{
					if(in_array('customer_branch_relation',$need_update_table_array))
					{
						$contents  .= 'customer_branch_relation'."\n";
					}
					else
					{
						$sqlquerycustomerrds="SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,customer_master CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds." 
									AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
						$rsquerycustomerrds=mysqli_query($link,$sqlquerycustomerrds);
						$rowquerycustomerrds=mysqli_fetch_assoc($rsquerycustomerrds);
						$customer_depot_cnt=$rowquerycustomerrds['total_customer_depot'];
						if($customer_depot_cnt >0)
						{
							$contents  .= 'customer_branch_relation'."\n";
						}
					}
				}

			if(order=='yes' && previous_order=='yes'){
				if(in_array('prev_order_counting_master',$need_update_table_array))
				{
					$contents  .= 'prev_order_counting_master'."\n";
				}
				else
				 {
					$sqlprevordercnt="SELECT COUNT(POCM.customer_code) AS total_prev_order FROM prev_order_counting_master POCM,customer_master CM 
									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_prev_order."";
					$rsprevordercnt=mysqli_query($link,$sqlprevordercnt);
					$rowprevordercnt=mysqli_fetch_assoc($rsprevordercnt);
					$prevordercnt=$rowprevordercnt['total_prev_order'];
									
					if($prevordercnt >0)
					{
						$contents  .= 'prev_order_counting_master'."\n";
					}
				 }
			}
		}
		
		if(!in_array('order',$menu_access_array))
		{
			if(order=='yes' && order_status=='yes'){
				if(in_array('order_status',$need_update_table_array))
				{
					$contents  .= 'order_status'."\n";
				}
				else
				 {
					if(strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='ABDOST')
					{				
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM
									WHERE UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')  ".$emp_val_prev_order."";					
						$rsorderstatus=mysqli_query($link,$sqlorderstatus);
					}
					else
					{
						$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_prev_order."";
					}
					$rsorderstatus=mysqli_query($link,$sqlorderstatus);
					$roworderstatus=mysqli_fetch_assoc($rsorderstatus);
					$total_order_status=$roworderstatus['total_order_status'];
									
					if($total_order_status >0)
					{
						$contents  .= 'order_status'."\n";
					}
				 }
			}
		}
		
		if(!in_array('outstanding_ageing',$menu_access_array))
		{
			if(sauda_outstanding=='yes')
			{
			    if(in_array('outstanding_ageing',$need_update_table_array))
				{
					$contents  .= 'outstanding_ageing'."\n";
				}
				else
				 {
					$sqloutstandingcnt="SELECT COUNT(OA.customer_code) AS total_outstanding FROM outstanding_ageing OA,customer_master CM
										 WHERE CM.customer_code=OA.customer_code ".$emp_val_rds."";
					$rsoutstandingcnt=mysqli_query($link,$sqloutstandingcnt);
					$rowoutstandingcnt=mysqli_fetch_assoc($rsoutstandingcnt);
					$outstandingcnt=$rowoutstandingcnt['total_outstanding'];
					
					if($outstandingcnt >0)
					{
						$contents  .= 'outstanding_ageing'."\n";
					}
				 }
			}
		}
		if(sale_performance=='yes')
		{
			$contents  .= 'sale_performance'."\n";
		}
		if(destination_price_list=='yes' || destination_ordertype_price_list=='yes' || destination=='yes')
		{
			if(in_array('destination_master',$need_update_table_array))
			{
				$contents  .= 'destination_master'."\n";
			}
			else
			{
				if(destination=='yes' && branch_wise_destination=='yes')
				{
					$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
					$rsempbranch=mysqli_query($link,$sqlempbranch);
					while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
					{
						$branch_value=$rowempbranch['branch_code'];
						$branch_code=$branch_code.$branch_value.',';
					}
					$branch_value_array=explode(',',$branch_code);
					$branch_value_final = "'".implode("','", $branch_value_array)."'";
					$condition_branch=' AND BDF.branch_code IN ('.$branch_value_final.')';
				
					$sqlquerydestination="SELECT COUNT(DM.destination_code) AS total_destination FROM destination_master DM,branch_destination_freight BDF
								WHERE DM.destination_code=BDF.destination_code ".$condition_branch." AND 
								UNIX_TIMESTAMP(BDF.download_time) > UNIX_TIMESTAMP('".$last_update_time."')ORDER BY destination_name ASC";
				}
				else
				{
					$sqlquerydestination="SELECT COUNT(destination_code) AS total_destination FROM destination_master WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				}		
				$rsquerydestination=mysqli_query($link,$sqlquerydestination);
				$rowquerydestination=mysqli_fetch_assoc($rsquerydestination);
				$destinationcnt=$rowquerydestination['total_destination'];
								
				if($destinationcnt >0)
				{
					$contents  .= 'destination_master'."\n";
				}
			}
		}
		if(target_achievement=='yes')
		{
			$contents  .= 'target_achievement'."\n";
		}
		if(distributor_route_planning=='yes' || distributor_route_emp_relation=='yes')
		{
			if(in_array('distributor_route_relation',$need_update_table_array))
			{
				$contents  .= 'distributor_route_relation'."\n";
			}
			else
			{
			if(modified_customer_emp_route=='yes')
			{
				if(employeewise_upperhierarchy=='yes')
				{
					//For selection of Route
					$route_code_array=array();
					$emp_hierarchy_route_condition=' AND RM.emp_code IN('.$employee_hierarchy.')';
					$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
											route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
											".$emp_hierarchy_route_condition;
					$resultquerycustomerroute=mysqli_query($link,$sqlquerycustomerroute);						
					while($rowsquerycustomerroute = mysqli_fetch_assoc($resultquerycustomerroute))
						{
							$route_code=$rowsquerycustomerroute['route_code'];
							if(!in_array($route_code,$route_code_array))
							{
								$route_code_string=$route_code_string."'".$route_code."'".',';
								array_push($route_code_array,$route_code);
							}
						}						
					$route_code_string=substr($route_code_string,0,-1);
					if($nick_name=='OSHEA' || $nick_name=='HALDIRAM' || $nick_name=='PRABHUJI')
					{
						$sqlquerydistributorroute="SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1  
											".$emp_hierarchy_route_condition;
						$resultquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
						$distributor_route_count=0;						
						while($rowsquerydistributorroute = mysqli_fetch_assoc($resultquerydistributorroute))
						 {
							$route_code_distributor=$rowsquerydistributorroute['route_code'];
							if(!in_array($route_code_distributor,$route_code_array))
							{
								$route_code_string=$route_code_string."'".$route_code_distributor."'".',';
								$distributor_route_count++;
								array_push($route_code_array,$route_code_distributor);
							}
						}
						if($distributor_route_count >0){
							$route_code_string=substr($route_code_string,0,-1);
						}
					}
					$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
					$employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
					//$emp_upper_hierarchy_condition=' AND c1.emp_code IN('.$employee_upper_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
					$emp_hierarchy_condition=' AND emp_code IN('.$employee_final_hierarchy.') AND route_code IN('.$route_code_string.')';
					$sqlquerydistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE 1 ".$emp_hierarchy_condition." 
											AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					$resultquerydistributorroute = mysqli_query($link,$sqlquerydistributorroute);
					$countdistributorroute=mysqli_num_rows($resultquerydistributorroute);
					if($countdistributorroute>0){
						$contents  .= 'distributor_route_relation'."\n";
					}
				}
				else{
					$sqlquerydistributorroute="SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds." 
											AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
					$rsquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
					$rowquerydistributorroute=mysqli_fetch_assoc($rsquerydistributorroute);
					$distributorroutecnt=$rowquerydistributorroute['total_distributor'];
					
					if($distributorroutecnt >0)
					{
						$contents  .= 'distributor_route_relation'."\n";
					}
				}
			}
			else{
					$sqlquerydistributorroute="SELECT COUNT(distributor_code) AS total_distributor FROM distributor_route_relation WHERE 1 ".$emp_val_rds." 
					AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
					$rsquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
					$rowquerydistributorroute=mysqli_fetch_assoc($rsquerydistributorroute);
					$distributorroutecnt=$rowquerydistributorroute['total_distributor'];
					
					if($distributorroutecnt >0)
					{
						$contents  .= 'distributor_route_relation'."\n";
					}
				}
			}
		}
		/*if(freight_cost=='yes')
		{
			$sqlqueryfreight="SELECT * from freight_cost  WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			$resqueryfreight = mysqli_query($link,$sqlqueryfreight);
			$countqueryfreight=mysqli_num_rows($resqueryfreight);
			if($countqueryfreight >0)
			{
				$contents  .= 'freight_cost'."\n";
			}
		}*/
		
		if(self_appraisal=='yes')
		{
			if(strtoupper($sale_access_emp)=='PRIMARY' || strtoupper($sale_access_emp)=='SECONDARY')
			{
				if(in_array('self_appraisal_details',$need_update_table_array))
				{
					$contents  .= 'self_appraisal_details'."\n";
				}
				else{
					if(self_appraisal_details_download=='yes'){
						$contents  .= 'self_appraisal_details'."\n";
					}
				}
				if(multiple_target_achievement=='yes')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
					$contents  .= 'self_appraisal_branch_wise'."\n";
					if(strtoupper($nick_name)=='ARCHITA' || strtoupper($nick_name)=='DNV' || strtoupper($nick_name)=='DNVFOODS')
		  			{
						$contents  .= 'self_appraisal_emp_wise'."\n";
					}
				}
				if(product_group_wise=='yes')
				{
					$contents  .= 'self_appraisal_productgroup_wise'."\n";
				}
				if(customer_wise_self_appraisal=='yes' && multiple_target_achievement=='no')
				{
					$contents  .= 'self_appraisal_customer_wise'."\n";
				}
				if(product_wise=='yes')
				{
					$contents  .= 'self_appraisal_product_wise'."\n";
				}
				if(week_wise=='yes')
				{
					$contents  .= 'self_appraisal_emp_week_wise'."\n";
				}
			}
		}
		if(catalogue=='yes')
		{
			$sqlcatalogue_info="SELECT COUNT(file_name) AS total_file FROM catalogue_info WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
			$rscatalogue_info=mysqli_query($link,$sqlcatalogue_info);
			$rowcatalogue_info=mysqli_fetch_assoc($rscatalogue_info);
			$cataloguecnt=$rowcatalogue_info['total_file'];
			
			if($cataloguecnt >0)
			{
				$contents  .= 'catalogue_info'."\n";
			}
		}
		if(branchwise_scheme_PDF=='yes')
		{
			$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsbranch=mysqli_query($link,$sqlbranch);
			$rowbranch=mysqli_fetch_assoc($rsbranch);
			$branch_code=$rowbranch['branch_code'];
			
			$sqlquery="SELECT PDF_file_name,branch_code,acedns FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
			ORDER BY download_time DESC ";
			$result = mysqli_query($link,$sqlquery);
			$count=mysqli_num_rows($result);
			if($count > 0)
			{
				$contents  .= 'branchwise_scheme_PDF'."\n";
			}
		}
		if(golden_rules=='yes')
		{
			if(strtoupper($nick_name)=='GOLDSTONE')
		  	{
				$sqlquery="SELECT gr_file_name FROM branchwise_goldenrules WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
				ORDER BY download_time DESC";
				$result = mysqli_query($link,$sqlquery);
				$count=mysqli_num_rows($result);
				if($count > 0)
				{
					$contents  .= 'golden_rules'."\n";
				}
			}
			else
			{
				$sqlbranch="SELECT BM.branch_state FROM branch_master BM,employee_master EM  WHERE FIND_IN_SET(BM.branch_code,EM.branch_code)
							AND EM.emp_code='".$emp_code."'";
				$rsbranch=mysqli_query($link,$sqlbranch);
				while($rowbranch=mysqli_fetch_assoc($rsbranch))
				{
					$branch_state=$branch_state.$rowbranch['branch_state'].',';
				}
				$branch_state=substr($branch_state,0,-1);
				
				$sqlquery="SELECT gr_file_name FROM branchwise_goldenrules WHERE 
				FIND_IN_SET(state,'".$branch_state."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
				ORDER BY download_time DESC";
				$result = mysqli_query($link,$sqlquery);
				$count=mysqli_num_rows($result);
				if($count > 0)
				{
					$contents  .= 'golden_rules'."\n";
				}
			}
		}
		if(input_screen_planwise=='yes')
		{
			$sqlqueryorderplan="SELECT COUNT(CPOD.customer_code) as total_order_plan FROM 
								customer_product_wise_orderplan_details CPOD,customer_route_emp_relation CRER WHERE 
								CPOD.customer_code = CRER.customer_code ".$emp_val_rds."";
			$rsqueryorderplan=mysqli_query($link,$sqlqueryorderplan);
			$orderplancnt=$rowqueryorderplan['total_order_plan'];
			
			if($orderplancnt >0){			
			$contents  .= 'customer_product_wise_orderplan'."\n";
			}
		}
		if(product_qty_wise_TD=='yes')
		{
			if(in_array('prodqty_custclass_wise_TD',$need_update_table_array))
			{
				$contents  .= 'prodqty_custclass_wise_TD'."\n";
			}
			else
			{
				$sqlempbranch="SELECT branch_code FROM employee_master WHERE 1 ".$emp_val_rds."";
				$rsempbranch=mysqli_query($link,$sqlempbranch);
				while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
				{
					$branch_value=$rowempbranch['branch_code'];
					$branch_code=$branch_code.$branch_value.',';
				}
				$branch_value_array=explode(',',$branch_code);
				$branch_value_final = "'".implode("','", $branch_value_array)."'";
				$condition_branch=' AND branch_code IN ('.$branch_value_final.')';
				
				$sqlquerydiscount="SELECT prod_code FROM prodqty_custclass_wise_TD WHERE 1 ".$condition_branch." AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$resultquerydiscount = mysqli_query($link,$sqlquerydiscount);
				$countdiscount=mysqli_num_rows($resultquerydiscount);
				if($countdiscount >0)
				{
					$contents  .= 'prodqty_custclass_wise_TD'."\n";
				}
			}
		}
		if(TD_allocation_app=='yes')
		  {
		  	if(!in_array('TD_allocation_app',$menu_access_array))
			  {
				  $contents  .= 'TD_allocation_access'."\n";
			  }
			  $contents  .= 'TD_allocation'."\n";
		  }
		  if(business_prospect=='yes'){
			$sql_business_prospect="SELECT * FROM `prospective_customer_master`";
			$res_business_prospect=mysqli_query($link,$sql_business_prospect);
			if(mysqli_num_rows($res_business_prospect)>0){
			 $contents  .= 'prospective_customer_master'."\n";
			}
			}
		  if(yellow_card=='yes')
		  {
			 $contents  .= 'yellow-card-date-validation'."\n";
			 $contents  .= 'yellow-card-date-validation_customerwise'."\n";
		  }
		  if(schemes=='yes'){
		  	$sqlqueryscheme="SELECT COUNT(scheme_id) AS total_scheme_id FROM scheme_master";
			$rsqueryscheme=mysqli_query($link,$sqlqueryscheme);
			$rowqueryscheme=mysqli_fetch_assoc($rsqueryscheme);
			$total_scheme_cnt=$rowqueryscheme['total_scheme_id'];
			if($total_scheme_cnt >0)
			{
				$contents  .= 'scheme_master'."\n";
				$sqlqueryfreebies="SELECT COUNT(scheme_id) AS total_scheme_id FROM freebies_master";
				$rsqueryfreebies=mysqli_query($link,$sqlqueryfreebies);
				$rowqueryfreebies=mysqli_fetch_assoc($rsqueryfreebies);
				$total_freebies_cnt=$rowqueryfreebies['total_scheme_id'];
				if($total_freebies_cnt >0)
				{
					$contents  .= 'freebies_master'."\n";	
				}
			}
		  }
		  if(stk_audit_msl=='yes')
		  {
			  $contents  .= 'customer_product_wise_msl'."\n";
		  }
		  if(retailer_app=='yes')
		  {
			//Billing start
			/*if(in_array('customer_product_billing',$need_update_table_array))
			{
				$contents  .= 'billing_information'."\n";
			}
			else
			{
				$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,
							customer_master CM
							WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";
				$rsempcust=mysqli_query($link,$sqlempcust); 
				$rowempcust=mysqli_fetch_assoc($rsempcust);
				$customer_code_emp=$rowempcust['customer_code'];
				$retailer_app=$rowempcust['retailer_app'];
				$cust_type=$rowempcust['cust_type'];
				$rds_tag=$rowempcust['rds_tag'];
			if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' AND 
							customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')";
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				else
				{
					$sqlempcust="SELECT customer_code FROM customer_route_emp_relation WHERE acedns='Y' ".$emp_val_rds;
					$rsempcust=mysqli_query($link,$sqlempcust); 
					while($rowempcust=mysqli_fetch_assoc($rsempcust))
					{
						$customer_code=$customer_code."'".$rowempcust['customer_code']."'".',';
					}
					$customer_code=substr($customer_code,0,-1);
				}
				$sqlquery="SELECT * FROM customer_product_billing WHERE customer_code IN (".$customer_code.") AND 
								UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$result = mysqli_query($link,$sqlquery);
				$countbilling=mysqli_num_rows($result);
				if($countbilling >0)
				{
					$contents  .= 'billing_information'."\n";
				}
			}*/
			//Billing End 
			//$contents  .= 'stock_allocation'."\n";
			$contents  .= 'billing_information'."\n";
			/*if(!in_array('stock_balance_details',$need_update_table_array))
			{
				$contents  .= 'stock_balance_details'."\n";
			}
			else
			{
			$sqlstockbalancedetails = "SELECT SBD.customer_code  FROM customer_route_emp_relation CRR,
							employee_master EM,stock_balance_details SBD WHERE  
							CRR.emp_code=EM.emp_code AND SBD.customer_code=CRR.customer_code AND CRR.emp_code 
							IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$emp_code."',reporting_to) UNION SELECT emp_code FROM employee_master WHERE acedns='Y' AND emp_code='".$emp_code."') 
							AND UNIX_TIMESTAMP(SBD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resultstockbalancedetails = mysqli_query($link,$sqlstockbalancedetails);
			$countstockbalance=mysqli_num_rows($resultstockbalancedetails);	
			
			if($countstockbalance >0){
				$contents  .= 'stock_balance_details'."\n";
			}
		   }
		   if(!in_array('stock_mis_details',$need_update_table_array))
			{
				$contents  .= 'sale_stock_mis'."\n";
			}
			else
			{
			$sqlstockmisdetails = "SELECT SMD.sl_no FROM customer_route_emp_relation CRR,
								employee_master EM,stock_mis_details SMD WHERE 
								CRR.emp_code=EM.emp_code AND SMD.customer_code=CRR.customer_code AND CRR.emp_code 
								IN(".$employee_hierarchy.") AND UNIX_TIMESTAMP(SMD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resultstockmisdetails = mysqli_query($link,$sqlstockmisdetails);
			$countstockmisdetails=mysqli_num_rows($resultstockmisdetails);	
			
			if($countstockmisdetails >0){
				$contents  .= 'sale_stock_mis'."\n";
			}
		   }*/
		  }
		  if(strtoupper($nick_name)=='START' || strtoupper($nick_name)=='DNV' || strtoupper($nick_name)=='DNVFOODS')
		  {
		 	 $contents  .= 'branch_master'."\n";
		  }
		  if($reporting_level >0)
		  {
			if(in_array('attendance_checkout_details',$need_update_table_array))
			{
				$contents  .= 'attendance_checkout_details'."\n";
			}
			else
			{
			$sqlattendancecheckoutdetails = "SELECT trans_id FROM location WHERE (trans_id LIKE 'A%' OR trans_id LIKE 'CH%' OR trans_id LIKE 'WO%' 
										OR trans_id LIKE 'LR%') ".$emp_val_rds."
							AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
			$resattendancecheckoutdetails = mysqli_query($link,$sqlattendancecheckoutdetails);
			$countattendancecheckout=mysqli_num_rows($resattendancecheckoutdetails);	
			
			if($countattendancecheckout >0){
				$contents  .= 'attendance_checkout_details'."\n";
			} 
		  }
		 }
		 if(strtoupper($nick_name)=='AJANTA')
		  {
			  if(!in_array('survey',$menu_access_array))
			  {
			  	$contents  .= 'sample_master'."\n";
			  }
		  }
		  if(strtoupper($nick_name)=='DURO' || strtoupper($nick_name)=='SYLVAN')
		  {
			  $sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
			  $rsbranchcode=mysqli_query($link,$sqlbranchcode);
			  $rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
			  $branch_code=$rowbranchcode['branch_code'];
			  
			  if(in_array('site_master',$need_update_table_array))
				{
					$contents  .= 'site_master'."\n";
				}
			 else
			{
			  $sqlquery="SELECT site_id FROM site_master WHERE 1 ".$emp_val_rds." AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
			  $result = mysqli_query($link,$sqlquery);
			  $count=mysqli_num_rows($result);
			  if($count >0)
			  {
			 	 $contents  .= 'site_master'."\n";
			  }
			 }
				if(in_array('facilitator_master',$need_update_table_array))
				{
				  $sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE branch_code='".$branch_code."'";			
				  $resultfacilitator = mysqli_query($link,$sqlqueryfacilitator);
				  $countfacilitator=mysqli_num_rows($resultfacilitator);
				  if($countfacilitator >0)
				  {
					$contents  .= 'facilitator_master'."\n";
				  }
				}
				else
				{
			  /*$sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE 1 ".$emp_val_rds." AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/
			  $sqlqueryfacilitator="SELECT f_code FROM facilitator_master WHERE branch_code='".$branch_code."' AND 
						UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";			
			  $resultfacilitator = mysqli_query($link,$sqlqueryfacilitator);
			  $countfacilitator=mysqli_num_rows($resultfacilitator);
			  if($countfacilitator >0)
			  {
			  	$contents  .= 'facilitator_master'."\n";
			  }
				}
			  $sqlquerydealer="SELECT customer_code FROM dealer_transaction WHERE acedns='Y' ".$emp_val_rds;
			  $resultdealer = mysqli_query($link,$sqlquerydealer);
			  $countdealer=mysqli_num_rows($resultdealer);
			  if($countdealer >0)
			  {
			  	$contents  .= 'dealer_transaction'."\n";
			  }
		  }
		  if(van_sales=='yes')
		  {
			  $contents  .= 'van_stock_allocation'."\n";
		  }
		 if(customer_product_relation=='yes'){
			 if(in_array('customer_product_relation',$need_update_table_array))
				{
					$contents  .= 'customer_product_relation'."\n";
				}
				else
				{
					if(strtoupper(substr($emp_code,0,1))=='E')
					{
					$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_route_emp_relation CM 
								WHERE  CPR.customer_code=CM.customer_code ".$emp_val_rds." AND 
								UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					}
					if(strtoupper(substr($emp_code,0,1))=='C')
					{
					$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR 
								WHERE  CPR.customer_code='".$emp_code."' AND UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					}
					if(strtoupper(substr($emp_code,0,1))=='B')
					{
					$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_broker_relation CM 
								WHERE  CPR.customer_code=CM.customer_code AND CPR.customer_code='".$emp_code."' AND
								  UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					}
				/*$sqlcustomerprod="SELECT CPR.customer_code FROM customer_product_relation CPR,customer_route_emp_relation CM 
								WHERE  CPR.customer_code=CM.customer_code ".$emp_val_rds." AND 
								UNIX_TIMESTAMP(CPR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rscustomerprod=mysqli_query($link,$sqlcustomerprod);*/
				$rscustomerprod=mysqli_query($link,$sqlcustomerprod);
				$total_cus_prod=mysqli_num_rows($rscustomerprod);
					if($total_cus_prod >0)
					{
						$contents  .= 'customer_product_relation'."\n";
					}
				}
		}
		if(bargain=='yes')
		{
			if(!in_array('bargain',$menu_access_array))
			{
				if(in_array('sauda_form_details',$need_update_table_array))
				{
					$contents  .= 'sauda_form_details'."\n";
				}
				else{
					if(sauda_form_details_download=='yes'){
						$contents  .= 'sauda_form_details'."\n";
					}
				}
				$contents  .= 'conversion_data'."\n";
				$contents  .= 'bargain_mrp'."\n";
				$contents  .= 'mcx_rate'."\n";
				if(in_array('customer_branch_relation',$need_update_table_array))
				{
					$contents  .= 'customer_branch_relation'."\n";
				}
				else
				{
					$sqlquerycustomerrds="SELECT COUNT(CBR.customer_code) AS total_customer_depot FROM customer_branch_relation CBR,
									customer_route_emp_relation CM WHERE CM.customer_code=CBR.customer_code ".$emp_val_rds." 
								AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
					$rsquerycustomerrds=mysqli_query($link,$sqlquerycustomerrds);
					$rowquerycustomerrds=mysqli_fetch_assoc($rsquerycustomerrds);
					$customer_depot_cnt=$rowquerycustomerrds['total_customer_depot'];
					if($customer_depot_cnt >0)
					{
						$contents  .= 'customer_branch_relation'."\n";
					}
				}
			   if(in_array('branch_master',$need_update_table_array))
				{
					$contents  .= 'branch_master'."\n";
				}
				else
				{
				   /*$sqlquerybranch="SELECT COUNT(branch_code) AS total_branch FROM branch_master WHERE 
									UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";*/
					$sqlquerybranch="SELECT COUNT(BM.branch_code) AS total_branch FROM 
								branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND 
								EM.emp_code='".$emp_code."'";							
					$rsquerybranch=mysqli_query($link,$sqlquerybranch);
					$rowquerybranch=mysqli_fetch_assoc($rsquerybranch);
					$branchcnt=$rowquerybranch['total_branch'];
									
					if($branchcnt >0)
					{
						$contents  .= 'branch_master'."\n";
					}
				}
				if(in_array('margin_cost',$need_update_table_array))
				{
					$contents  .= 'margin_cost'."\n";
				}
				$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.state_code,DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As 
								last_updated_date FROM margin_cost MC WHERE  UNIX_TIMESTAMP(MC.datetime) > UNIX_TIMESTAMP('".$last_update_time."') 
								ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2";
				$rsmargin = mysqli_query($link,$sqlmargin);
				$total_margin = mysqli_num_rows($rsmargin);
				if($total_margin >0){
					$contents  .='margin_cost'."\n";
				}
				if(in_array('depot_cost',$need_update_table_array))
				{
					$contents  .= 'depot_cost'."\n";
				}
				$sqldepot= "SELECT * FROM (SELECT DC.dns_prod_code,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') As last_updated_date,
							DC.branch_code,DC.depot_cost FROM depot_cost DC WHERE UNIX_TIMESTAMP(DC.datetime) > UNIX_TIMESTAMP('".$last_update_time."')  ORDER BY DC.datetime DESC) AS SAT GROUP BY 1,3 ORDER BY 2 DESC";
				$rsdepot = mysqli_query($link,$sqldepot);
				$total_depot = mysqli_num_rows($rsdepot);
				if($total_depot >0){
					$contents  .='depot_cost'."\n";
				}
				if(in_array('freight_cost',$need_update_table_array))
				{
					$contents  .= 'primary_freight'."\n";
				}
				$sqlfreight= "SELECT * FROM (SELECT PF.dns_prod_code,DATE_FORMAT(SUBSTRING(PF.datetime,1,10),'%d-%m-%Y') As last_updated_date,
								PF.branch_code,PF.freight_cost,PF.transport_mode,PF.truck_load  FROM freight_cost PF  
								WHERE UNIX_TIMESTAMP(PF.datetime) > UNIX_TIMESTAMP('".$last_update_time."') 
								ORDER BY PF.datetime DESC) AS SAT GROUP BY 1,3,5,6 ORDER BY 2 DESC";
				$rsfreight = mysqli_query($link,$sqlfreight);
				$total_freight = mysqli_num_rows($rsfreight);
				if($total_freight >0){
					$contents  .='primary_freight'."\n";
				}
				if(in_array('branch_route_freight',$need_update_table_array))
				{
					$contents  .= 'branch_route_freight'."\n";
				}
				else
				{
					$sqlquerybranchfreight="SELECT branch_code FROM branch_route_freight WHERE 
											UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
					$resultbranchfreight = mysqli_query($link,$sqlquerybranchfreight);
					$countbranchfreight=mysqli_num_rows($resultbranchfreight);
					if($countbranchfreight >0)
					{
						$contents  .= 'branch_route_freight'."\n";
					}
				}
				if(sauda_booked_through =='BROKER' || sauda_booked_through =='BOTH')
				{
					$contents  .= 'broker_master'."\n";
					$contents  .= 'customer_broker_relation'."\n";
					$contents  .= 'brokerage_cost'."\n";
				}

			}
		}
		if(delivery_order=='yes')
		{
			if(in_array('DO_master',$need_update_table_array))
			{
				$contents  .= 'bargain_transaction'."\n";
			}
			else
			{
				$sqlqueryDO="SELECT DISTINCT SH.sauda_no  FROM sauda_header SH,sauda_details SD,customer_route_emp_relation CRR WHERE SH.DO_done='no' 
							AND SH.customer_code=CRR.customer_code AND SH.sauda_no=SD.sauda_no AND CRR.emp_code IN(".$employee_hierarchy.") 
							AND UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(SH.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s')) > UNIX_TIMESTAMP('".$last_update_time."')";
				$resultDO = mysqli_query($link,$sqlqueryDO);
				$countDO=mysqli_num_rows($resultDO);
				if($countDO > 0)
				{
					$contents  .= 'bargain_transaction'."\n";
				}
			}
		}
		if(strtoupper($nick_name)=='START' || strtoupper($nick_name)=='STAR' || strtoupper($nick_name)=='GOLDSTONET' || strtoupper($nick_name)=='ABDOS' || strtoupper($nick_name)=='PALSONS')
		  {
		 	 $contents  .= 'branch_geo_fencing'."\n";
		  }
		  if(strtoupper($nick_name)=='NIMBUS')
		  {
			 // $contents  .= 'farmer_master'."\n";
			 // $contents  .= 'BOQ_master'."\n";
			  $contents  .= 'additional_material'."\n";
		  }
		  if(app_order_approval=='yes'){
			if(in_array('T_APPERPDO_APPROVAL',$need_update_table_array))
			{
				$contents  .= 'order_approval'."\n";
			}
			else
			 {
				$sqlorderapproval="SELECT COUNT(TAP.customer_code) AS total_order_approval FROM T_APPERPDO_APPROVAL TAP,customer_route_emp_relation CM 
								WHERE CM.customer_code=TAP.customer_code  AND CM.emp_code IN(".$employee_hierarchy.") AND CM.acedns='Y' AND 
								UNIX_TIMESTAMP(TAP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$rsorderapproval=mysqli_query($link,$sqlorderapproval);
				$roworderapproval=mysqli_fetch_assoc($rsorderapproval);
				$total_order_approval=$roworderapproval['total_order_approval'];
				if($total_order_approval >0)
				{
					$contents  .= 'order_approval'."\n";
				}
			}
			if(in_array('branch_dump',$need_update_table_array))
			{
				$contents  .= 'branch_dump'."\n";
			}
			else
			 {
				$sqlbranch="SELECT GROUP_CONCAT(branch_code SEPARATOR ',') AS branch_code FROM employee_master 
							WHERE emp_code IN(".$employee_hierarchy.")";
				$rsbranch=mysqli_query($link,$sqlbranch);
				$rowbranch=mysqli_fetch_assoc($rsbranch);
				$branch_code=$rowbranch['branch_code'];
				
				$sqlquery="SELECT branch_code FROM branch_dump WHERE acedns='Y' AND FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
				$result = mysqli_query($link,$sqlquery);
				$count=mysqli_num_rows($result);
				if($count > 0)
				{
					$contents  .= 'branch_dump'."\n";
				}
			 }
		 }
		 if(strtoupper($nick_name)=='MAGIK')
			{
				$contents  .= 'customer_product_info'."\n";
				$contents  .= 'customer_proposed_product'."\n";
				$contents  .= 'retailer-wise-target-ach'."\n";
			}
			if(strtoupper($nick_name)=='PALSONS')
			{
				$contents  .= 'beatwise_TA_DA'."\n";
				$contents  .= 'customer_product_info'."\n";
			}
			if(strtoupper($nick_name)=='SUPERSHAKTI')
			{
				$contents  .= 'gift_master'."\n";
			}
			if(strtoupper($nick_name)=='MAGIK' || strtoupper($nick_name)=='ELEGANT')
			{
				$contents  .= 'scheme_pdf'."\n";
			}

	}
	
	        if(strtoupper($nick_name)=='DURO')
			{
    			if (str_contains($contents, 'state_master')) {
        
                }else{
    				$contents  .= 'state_master'."\n";
                }
			}
	
	if(strtoupper($nick_name)=='STAR')
			{
	            $contents  .= 'emp_mtl_mapping'."\n";
	            $contents  .= 'mtl_testing_format'."\n";
	            $contents  .= 'quality_complaint_master'."\n";
	            
	            $contents  .= 'survey_input_details'."\n";
	            $contents  .= 'survey_table_view'."\n";
	            $contents  .= 'emp_master'."\n";
	            $contents  .= 'customer_master'."\n";
	            $contents  .= 'user_details'."\n";
	            
			}
			
			if(strtoupper($nick_name)=='DURO')
			{
	            
	            $contents  .= 'survey_input_details'."\n";
	            $contents  .= 'survey_table_view'."\n";
			}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/datadownloaddictionary-7.0.3.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/datadownloaddictionary-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
}
		else
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
}
mysqli_close($link);
?>
