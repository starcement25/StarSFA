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
	$emp_hierarchy_condition='CRER.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CRER.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition=" AND c1.acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

	 if($nick_name=='SELVEL'){
		 $sqlquery="SELECT DISTINCT c1.*,CRER.rds_tag FROM customer_master c1,customer_route_emp_relation CRER WHERE 
		 			CRER.customer_code=c1.customer_code  ".$login_condition." ORDER BY c1.customer_name ASC";
	 }
	 else
	 {
		 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
		 /*$sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
						c1.TD,c1.cust_type,c1.rds_tag
						FROM customer_master c1,route_master r, employee_master em WHERE ".$emp_hierarchy_condition." ".$login_condition." 
						OR ( r.emp_code = em.emp_code AND c1.route_code = r.route_code AND em.emp_code ='".$emp_code."') 
						ORDER BY c1.customer_name ASC";*/
			$sqlquery="SELECT DISTINCT c1.customer_code,c1.dns_customer_code, c1.customer_name,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
					c1.TD,c1.cust_type,c1.route_code,CRER.rds_tag FROM customer_master c1,employee_master em,customer_route_emp_relation CRER WHERE ".$emp_hierarchy_condition." ".$login_condition." AND CRER.customer_code=c1.customer_code ORDER BY c1.customer_name ASC";			
		 }
		 else if($emp_code=='C0007')
		 {
			$sqlquery="SELECT DISTINCT c1.*,CRER.rds_tag FROM customer_master c1,customer_route_emp_relation CRER WHERE 
		 					CRER.customer_code=c1.customer_code  ".$login_condition." ORDER BY c1.customer_name ASC";
		 }
		 else
		 {
		  $sqlquery="SELECT DISTINCT c1.customer_code,c1.dns_customer_code, c1.customer_name,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
					c1.TD,c1.cust_type,c1.route_code,CRER.rds_tag FROM customer_master c1,employee_master em,customer_route_emp_relation CRER 
						 WHERE ".$emp_hierarchy_condition." ".$login_condition." 
						AND CRER.customer_code=c1.customer_code ORDER BY c1.customer_name ASC"; 
		 }
	 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'12';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		$emp_code_val='';
		while($rowscustomer = mysqli_fetch_assoc($result))
		{
			
			if(substr($rowscustomer['dns_customer_code'],0,1)=='N')
			{
				$dns_customer_code=$rowscustomer['dns_customer_code'];
			}
			else
			{
				$dns_customer_code='';
			}
			$contents  = (($rowscustomer['customer_code']!='')?$rowscustomer['customer_code']: ' ')."^";
			$contents  .= (($rowscustomer['customer_name']!='')?$rowscustomer['customer_name']: ' ')."^";
			$contents  .= (($rowscustomer['route_code']!='')?$rowscustomer['route_code']: ' ')."^";
			$contents  .= (($emp_code_val!='')?$emp_code_val: ' ')."^";
			$contents  .= (($rowscustomer['current_balance']!='')?$rowscustomer['current_balance']: ' ')."^";
			$contents  .= (($rowscustomer['credit_limit']!='')?$rowscustomer['credit_limit']: ' ')."^";
			$contents  .= (($rowscustomer['acedns']!='')?$rowscustomer['acedns']: ' ')."^";
			$contents  .= (($rowscustomer['black_list']!='')?$rowscustomer['black_list']: ' ')."^";
			$contents  .= (($rowscustomer['TD']!='')?$rowscustomer['TD']: '0')."^";
			$contents  .= (($rowscustomer['cust_type']!='')?$rowscustomer['cust_type']: ' ')."^";
			$contents  .= (($rowscustomer['rds_tag']!='')?$rowscustomer['rds_tag']: ' ')."^";
			$contents  .= (($dns_customer_code!='')?$dns_customer_code: ' ');
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
			$datacontents = '0'.'¥'.'12';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-5.0.6.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-5.0.6.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 		
?>
