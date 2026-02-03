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
	$emp_hierarchy_condition='CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CM.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition=" AND CM.acedns='Y'";
	$login_condition_customer=" AND acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$login_condition_customer=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
	//For checking employee menu access
	$sqlmenuaccess="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code."'";
	$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
	$countmenuaccess=mysqli_num_rows($rsmenuaccess);
	$not_accessible_menu_array=array();
	if($countmenuaccess >0)
	{
		while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
		{
			array_push($not_accessible_menu_array,$rowmenuaccess['not_accessible_menu']);
		}
	}
	if(in_array('order',$not_accessible_menu_array))
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

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
	 if(strtoupper($nick_name)=='ARCHITA')
	 {
		$sqlquery="SELECT  CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_route_emp_relation CM,employee_master EM,customer_branch_relation CBR 
					WHERE ".$emp_hierarchy_condition." ".$customer_type_condition." AND CBR.customer_code=CM.customer_code AND CM.emp_code=EM.emp_code ".$login_condition."";
	 }
	 else
		 {
		$sqlquery="SELECT  CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_master CM,employee_master EM,customer_branch_relation CBR 
					WHERE ".$emp_hierarchy_condition." ".$customer_type_condition." AND CBR.customer_code=CM.customer_code AND CM.emp_code=EM.emp_code ".$login_condition."";	
		 }
 }
 else if($emp_code=='C0007')
 {
	$sqlquery="SELECT CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_master CM,customer_branch_relation CBR 
				WHERE  CBR.customer_code=CM.customer_code ".$login_condition."";
 }
 else
 {
	 if(strtoupper($nick_name)=='ASL')
	 {
		if(strtoupper(substr($emp_code,0,1))=='E')
		{
		$sqlquery="SELECT  CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_route_emp_relation CM,employee_master EM,customer_branch_relation CBR 
					WHERE ".$emp_hierarchy_condition." ".$customer_type_condition." AND CBR.customer_code=CM.customer_code AND CM.emp_code=EM.emp_code ".$login_condition."";
		}
		if(strtoupper(substr($emp_code,0,1))=='C')
		{
		$sqlquery="SELECT  customer_code,branch_code,acedns FROM customer_branch_relation  WHERE customer_code='".$emp_code."'".$login_condition_customer."";
		}
		if(strtoupper(substr($emp_code,0,1))=='B')
		{
		$sqlquery="SELECT  CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_broker_relation CM,customer_branch_relation CBR 
					WHERE CBR.customer_code=CM.customer_code AND CM.broker_code='".$emp_code."' ".$login_condition."";
		}
		
	 }
	 else
	 {
	$sqlquery="SELECT  CBR.customer_code,CBR.branch_code,CBR.acedns FROM customer_master CM,employee_master EM,customer_branch_relation CBR 
				WHERE ".$emp_hierarchy_condition." ".$customer_type_condition." AND CBR.customer_code=CM.customer_code AND 
				CM.emp_code=EM.emp_code ".$login_condition.""; 
	 }
}

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'3';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcustomerrds = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomerrds['customer_code']!='')?$rowcustomerrds['customer_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['branch_code']!='')?$rowcustomerrds['branch_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['acedns']!='')?$rowcustomerrds['acedns']: ' ');
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
			$datacontents = '0'.'¥'.'3';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-branch-relational-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-branch-relational-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_branch_relation.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
