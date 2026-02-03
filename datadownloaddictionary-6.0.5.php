<?php
error_reporting(0);
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
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_val_rds=' AND (emp_code IN('.$employee_hierarchy.')';
	}
	else
	{
		$emp_val_rds=" AND emp_code='".$emp_code."'";
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

$sqlselectuserdbversion="SELECT is_update,db_version_code FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
$rsselectuserdbversion=mysqli_query($link,$sqlselectuserdbversion);
$countselectuserdbversion=mysqli_num_rows($rsselectuserdbversion);
if($countselectuserdbversion>0)
{
	$rowselectuserdbversion=mysqli_fetch_assoc($rsselectuserdbversion);
	$is_update=$rowselectuserdbversion['is_update'];
	$user_db_version_code=$rowselectuserdbversion['db_version_code'];
	if((($versionCodecurrent-$user_db_version_code)*10) > '1' && $is_update==1 && $incremental_download=='yes')
		{
			//mysqli_close($link);
			$linksetupdatabase=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
			mysqli_select_db("acedns_acednsproduct",$linksetupdatabase) or die("could not connect the database for invalid setup database");
			$need_download_table_array=array();
			$sqlquery="SELECT table_name FROM app_db_update_execution WHERE db_version > '".$user_db_version_code."'  
						AND db_version <= '".$versionCodecurrent."'  ORDER BY app_db_u_exe_id ASC ";
			$result = mysqli_query($link,$sqlquery) or die(mysqli_error());
			while($rowstructuredetails = mysqli_fetch_assoc($result))
			{
				array_push($need_download_table_array,$rowstructuredetails['table_name']);
			}
			mysqli_close($linksetupdatabase);
			$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
			mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

		if(in_array("menu_details",$need_download_table_array))
		{
			$contents  .= 'menu_details'."\n";
		}
		if(in_array("user_details",$need_download_table_array))
		{
			$contents  .= 'user_details'."\n";
		}
		if(in_array("order_form_details",$need_download_table_array))
		{
			$contents  .= 'order_details'."\n";
		}
		if(in_array("product_details",$need_download_table_array))
		{
			$contents  .= 'product_details'."\n";
		}
		if(route_plan=='yes' && in_array("route_plan_details",$need_download_table_array))
		{
			$contents  .= 'route_plan_details'."\n";
		}
		if(sauda_allocation=='yes' && in_array("sauda_form_details",$need_download_table_array))
		{
			$contents  .= 'sauda_form_details'."\n";
		}
		if(survey=='yes' && in_array("survey_form_details",$need_download_table_array))
		{
			$contents  .= 'survey_form_details'."\n";
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
		if(collection=='yes' && in_array("outstanding_master",$need_download_table_array))
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
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && in_array("mrp",$need_download_table_array) && order=='yes') {
			$contents  .= 'mrp_master'."\n";
		}
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && in_array("sauda_mrp",$need_download_table_array) && sauda_allocation=='yes') {
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
		$contents  .= 'emp_master'."\n";
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
					$contents  .= 'mis_transaction_log'."\n";
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
					$contents  .= 'branch_master'."\n";
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
								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."";
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
				$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."";
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

		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = "http://www.acedns.in/acednsproduct/datadownloaddictionary-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
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
		if(collection=='yes')
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
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && order=='yes'){
		 $contents  .= 'mrp_master'."\n";
		}
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes') {
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
		}
		if(tour_exp=='yes'){
			$contents  .= 'travel_category'."\n";
			$contents  .= 'travel_sub_category'."\n";
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
		$contents  .= 'emp_master'."\n";
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
					$contents  .= 'branch_master'."\n";
					if(sauda_depot_wise=='yes')
					{
						$contents  .= 'customer_branch_relation'."\n";
					}
				}
			}
			if(!in_array('order',$menu_access_array))
			{
				$contents  .= 'branch_master'."\n";
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
		}
		if(survey=='yes')
		{
		  $contents  .= 'survey_category_master'."\n";
		  $contents  .= 'survey_input_details'."\n";
		  if(survey_type=='yes')
			 {
				$contents  .= 'mall_master'."\n";
				$contents  .= 'mall_survey_relation'."\n";
			 }
		}
		if(product_promotion=='yes'  || market_feedback=='yes')
		{
			$contents  .= 'generic_oil_master'."\n";
			//$contents  .= 'street_master'."\n";
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
								WHERE CM.customer_code=POCM.customer_code  ".$emp_val_rds."";
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
				$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
								WHERE CM.customer_code=POCM.customer_code ".$emp_val_rds."";
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
			}
			else
			{
				$emp_val_condition=" AND emp_code='".$emp_code."'";
				$emp_val_condition_route=" AND CM.emp_code='".$emp_code."'";
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
			$sqlroutecnt="select RM.route_code from route_master RM,customer_master CM WHERE RM.route_code=CM.route_code AND 
						  UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$customer_type_condition.$emp_val_condition_route." GROUP BY RM.route_code";				
			$rsroutecnt=mysqli_query($link,$sqlroutecnt);
			$routecnt=mysqli_num_rows($rsroutecnt);
		    if($routecnt >0)
			{
				$contents  .= 'route_master'."\n";
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
			$sqlcustomercnt="SELECT COUNT(CM.customer_code) AS total_customer FROM customer_master CM 
							WHERE UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$customer_type_condition.$emp_val_condition."";
			$rscustomercnt=mysqli_query($link,$sqlcustomercnt);
			$rowcustomercnt=mysqli_fetch_assoc($rscustomercnt);
			$customercnt=$rowcustomercnt['total_customer'];
							
			if($customercnt >0)
			{
				$contents  .= 'customer_master'."\n";
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
		if(collection=='yes')
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
				$sqlprodgroupcnt="SELECT COUNT(product_group_code) AS total_product_group FROM product_group_master 
								WHERE UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$condition_verticle."";
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
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && order=='yes'){
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
		if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && sauda_allocation=='yes'){
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
		$contents  .= 'emp_master'."\n";

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
					$contents  .= 'branch_master'."\n";
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
			   $contents  .= 'branch_master'."\n";
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
									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
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
					$sqlorderstatus="SELECT COUNT(POCM.customer_code) AS total_order_status FROM prev_order_counting_master POCM,customer_master CM 
									WHERE CM.customer_code=POCM.customer_code AND UNIX_TIMESTAMP(POCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds."";
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
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/datadownloaddictionary-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
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
