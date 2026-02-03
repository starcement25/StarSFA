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
	$emp_hierarchy_condition='ERM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="ERM.emp_code='".$emp_code."'";
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
		 $sqlquery="SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC";
	 }
	 else
	 {
		 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
		  $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,ERM.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
					c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period FROM customer_master c1,emp_route_relation ERM WHERE ".$emp_hierarchy_condition." 
					 AND c1.route_code=ERM.route_code AND ERM.acends!='N' ".$login_condition." ORDER BY c1.customer_name ASC";			
		 }
		 else if($emp_code=='C0007')
		 {
			$sqlquery="SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC";
		 }
		 else
		 {
		    $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,ERM.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
					   c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period FROM customer_master c1,emp_route_relation ERM WHERE ".$emp_hierarchy_condition." 
					   AND c1.route_code=ERM.route_code AND ERM.acends!='N' ".$login_condition." ORDER BY c1.customer_name ASC"; 
		 }
	 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'13';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		$dns_customer_code='';
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp['customer_name'])): ' ')."^";
			$contents  .= (($rowsemp['route_code']!='')?$rowsemp['route_code']: ' ')."^";
			$contents  .= (($rowsemp['emp_code']!='')?$rowsemp['emp_code']: ' ')."^";
			$contents  .= (($rowsemp['current_balance']!='')?$rowsemp['current_balance']: ' ')."^";
			$contents  .= (($rowsemp['credit_limit']!='')?$rowsemp['credit_limit']: ' ')."^";
			$contents  .= (($rowsemp['acedns']!='')?$rowsemp['acedns']: ' ')."^";
			$contents  .= (($rowsemp['black_list']!='')?$rowsemp['black_list']: ' ')."^";
			$contents  .= (($rowsemp['TD']!='')?$rowsemp['TD']: '0')."^";
			$contents  .= (($rowsemp['cust_type']!='')?$rowsemp['cust_type']: ' ')."^";
			$contents  .= (($rowsemp['rds_tag']!='')?$rowsemp['rds_tag']: ' ')."^";
			$contents  .= (($rowsemp['sauda_validity_period']!='')?$rowsemp['sauda_validity_period']: ' ')."^";
			$contents  .= (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ');// For dns customer code forcefully given the original customer code
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
			$datacontents = '0'.'¥'.'13';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 		
?>
