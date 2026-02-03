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
		$emp_val_order=' AND (SUBSTRING(order_no,2,5) IN('.$employee_hierarchy.'))';
		$emp_val_collection=' AND (SUBSTRING(receipt_id,2,5) IN('.$employee_hierarchy.'))';
		$emp_val_cash_deposit=' AND (SUBSTRING(cash_deposit_recv_id,3,5) IN('.$employee_hierarchy.'))';
	}
	else
	{
		$emp_val_rds=" AND emp_code='".$emp_code."'";
		$emp_val_order=" AND SUBSTRING(order_no,2,5)='".$emp_code."'";
		$emp_val_collection=" AND SUBSTRING(receipt_id,2,5)='".$emp_code."'";
		$emp_val_cash_deposit=" AND SUBSTRING(cash_deposit_recv_id,3,5)='".$emp_code."'";
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
$sqlempsaleaccess="SELECT sale_access FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempsaleaccess=mysqli_query($link,$sqlempsaleaccess);
$rowempsaleaccess=mysqli_fetch_assoc($rsempsaleaccess);
$sale_access_emp=$rowempsaleaccess['sale_access'];
	
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
$trans_download_params_array=explode(",",trans_download_params);
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
			mysqli_select_db("acedns_acednsproduct",$linksetupdatabase) or die("could not connect the database for invalid setup database");
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
			foreach($trans_download_params_array as $trans_download_params_val){
				if($trans_download_params_val=='location'){
				  if(in_array("location",$need_download_table_array))
					{
						$contents  .= 'location'."\n";
					}
			      else{	
					$sqltransdownloadlocationcnt="SELECT trans_id FROM location 
										WHERE UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds;
					$rstransdownloadlocationcnt=mysqli_query($link,$sqltransdownloadlocationcnt);
					$locationcnt=mysqli_num_rows($rstransdownloadlocationcnt);
					if($locationcnt >0)
					{
						$contents  .= 'location'."\n";
					}
				  }
				}
				if($trans_download_params_val=='order_header' && !in_array('order',$menu_access_array))
				{
				  if(in_array("order_header",$need_download_table_array))
					{
						$contents  .= 'order_header'."\n";
					}
			      else{	
					$sqltransdownloadorderhcnt="SELECT order_no FROM order_header 
										WHERE UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s')) > 
										UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_order;
					$rstransdownloadorderhcnt=mysqli_query($link,$sqltransdownloadorderhcnt);
					$orderhcnt=mysqli_num_rows($rstransdownloadorderhcnt);
					if($orderhcnt >0)
					{
						$contents  .= 'order_header'."\n";
					}
				  }
				}
				if($trans_download_params_val=='order_details' && !in_array('order',$menu_access_array)){
				  if(in_array("order_details",$need_download_table_array))
					{
						$contents  .= 'order_details'."\n";
					}
			      else{	
					$sqltransdownloadorderdcnt="SELECT order_no FROM order_details 
										WHERE UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s')) > 
										UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_order;
					$rstransdownloadorderdcnt=mysqli_query($link,$sqltransdownloadorderdcnt);
					$orderdcnt=mysqli_num_rows($rstransdownloadorderdcnt);
					if($orderdcnt >0)
					{
						$contents  .= 'order_details'."\n";
					}
				  }
				}
		   }
		$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
		$url = APICALLLOGURL."/transdownloaddictionary-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
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
	header("Content-Disposition: attachment; filename=transdownloaddictionary.txt");
	print "$contents";
	exit();
	}
}
//End For those user who's db version is older than the current version 
if($successval=="1"){
	if($incremental_download=='no'){
		foreach($trans_download_params_array as $trans_download_params_val){
		if($trans_download_params_val=='location'){
			$sqltransdownloadlocationcnt="SELECT trans_id FROM location WHERE 1 ".$emp_val_rds;
			$rstransdownloadlocationcnt=mysqli_query($link,$sqltransdownloadlocationcnt);
			$locationcnt=mysqli_num_rows($rstransdownloadlocationcnt);
			if($locationcnt >0)
			{
				$contents  .= 'location'."\n";
			}
		}
		if($trans_download_params_val=='order_header' && !in_array('order',$menu_access_array))
		{
			$sqltransdownloadorderhcnt="SELECT order_no FROM order_header WHERE 1 ".$emp_val_order;
			$rstransdownloadorderhcnt=mysqli_query($link,$sqltransdownloadorderhcnt);
			$orderhcnt=mysqli_num_rows($rstransdownloadorderhcnt);
			if($orderhcnt >0)
			{
				$contents  .= 'order_header'."\n";
			}
		}
		if($trans_download_params_val=='order_details' && !in_array('order',$menu_access_array)){
			$sqltransdownloadorderdcnt="SELECT order_no FROM order_details WHERE 1 ".$emp_val_order;
			$rstransdownloadorderdcnt=mysqli_query($link,$sqltransdownloadorderdcnt);
			$orderdcnt=mysqli_num_rows($rstransdownloadorderdcnt);
			if($orderdcnt >0)
			{
				$contents  .= 'order_details'."\n";
			}
		}
		if($trans_download_params_val=='payment_header' && !in_array('collection',$menu_access_array)){
			$sqltransdownloadpaymenthcnt="SELECT receipt_id FROM payment_header WHERE 1 ".$emp_val_collection;
			$rstransdownloadpaymenthcnt=mysqli_query($link,$sqltransdownloadpaymenthcnt);
			$paymenthcnt=mysqli_num_rows($rstransdownloadpaymenthcnt);
			if($paymenthcnt >0)
			{
				$contents  .= 'payment_header'."\n";
			}
		}
		if($trans_download_params_val=='payment_details' && !in_array('collection',$menu_access_array)){
			$sqltransdownloadpaymentdcnt="SELECT receipt_id FROM payment_details WHERE 1 ".$emp_val_collection;
			$rstransdownloadpaymentdcnt=mysqli_query($link,$sqltransdownloadpaymentdcnt);
			$paymentdcnt=mysqli_num_rows($rstransdownloadpaymentdcnt);
			if($paymentdcnt >0)
			{
				$contents  .= 'payment_details'."\n";
			}
		}
		if($trans_download_params_val=='cash_deposit_receive_details' && !in_array('collection',$menu_access_array)){
			$sqltransdownloadcash="SELECT cash_deposit_recv_id FROM cash_deposit_receive_details WHERE 1 ".$emp_val_cash_deposit;
			$rstransdownloadcash=mysqli_query($link,$sqltransdownloadcash);
			$cashdepositcnt=mysqli_num_rows($rstransdownloadcash);
			if($cashdepositcnt >0)
			{
				$contents  .= 'cash_deposit_receive_details'."\n";
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
		foreach($trans_download_params_array as $trans_download_params_val){
			if($trans_download_params_val=='location'){
			  if(in_array("location",$need_update_table_array))
				{
					$contents  .= 'location'."\n";
				}
			  else{	
				$sqltransdownloadlocationcnt="SELECT trans_id FROM location 
									WHERE UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_rds;
				$rstransdownloadlocationcnt=mysqli_query($link,$sqltransdownloadlocationcnt);
				$locationcnt=mysqli_num_rows($rstransdownloadlocationcnt);
				if($locationcnt >0)
				{
					$contents  .= 'location'."\n";
				}
			  }
			}
			if($trans_download_params_val=='order_header' && !in_array('order',$menu_access_array))
			{
			  if(in_array("order_header",$need_update_table_array))
				{
					$contents  .= 'order_header'."\n";
				}
			  else{	
				$sqltransdownloadorderhcnt="SELECT order_no FROM order_header 
									WHERE UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s')) > 
									UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_order;
				$rstransdownloadorderhcnt=mysqli_query($link,$sqltransdownloadorderhcnt);
				$orderhcnt=mysqli_num_rows($rstransdownloadorderhcnt);
				if($orderhcnt >0)
				{
					$contents  .= 'order_header'."\n";
				}
			  }
			}
			if($trans_download_params_val=='order_details' && !in_array('order',$menu_access_array)){
			  if(in_array("order_details",$need_update_table_array))
				{
					$contents  .= 'order_details'."\n";
				}
			  else{	
				$sqltransdownloadorderdcnt="SELECT order_no FROM order_details 
									WHERE UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s')) > 
									UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_order;
				$rstransdownloadorderdcnt=mysqli_query($link,$sqltransdownloadorderdcnt);
				$orderdcnt=mysqli_num_rows($rstransdownloadorderdcnt);
				if($orderdcnt >0)
				{
					$contents  .= 'order_details'."\n";
				}
			  }
			}
		   if($trans_download_params_val=='payment_header' && !in_array('collection',$menu_access_array)){
			 if(in_array("payment_header",$need_update_table_array))
				{
					$contents  .= 'payment_header'."\n";
				}
			  else{	
				$sqltransdownloadpaymenthcnt="SELECT receipt_id FROM payment_header WHERE 
										UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s')) > 
										UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_collection;
				$rstransdownloadpaymenthcnt=mysqli_query($link,$sqltransdownloadpaymenthcnt);
				$paymenthcnt=mysqli_num_rows($rstransdownloadpaymenthcnt);
				if($paymenthcnt >0)
				{
					$contents  .= 'payment_header'."\n";
				}
			  }
		  }
		 if($trans_download_params_val=='payment_details' && !in_array('collection',$menu_access_array)){
			 if(in_array("payment_details",$need_update_table_array))
				{
					$contents  .= 'payment_details'."\n";
				}
			  else{
				$sqltransdownloadpaymentdcnt="SELECT receipt_id FROM payment_details WHERE 
										UNIX_TIMESTAMP(DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s')) > 
										UNIX_TIMESTAMP('".$last_update_time."') ".$emp_val_collection;
				$rstransdownloadpaymentdcnt=mysqli_query($link,$sqltransdownloadpaymentdcnt);
				$paymentdcnt=mysqli_num_rows($rstransdownloadpaymentdcnt);
				if($paymentdcnt >0)
				{
					$contents  .= 'payment_details'."\n";
				}
			  }
		  }
	    }
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/transdownloaddictionary-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=transsdownloaddictionary.txt");
	print "$contents";
	}
	else
	{
		echo '0';
	}
mysqli_close($link);
?>
