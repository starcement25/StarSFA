<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
}*/
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$currentdate=$year.'-'.$month.'-'.$date;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_val_rds=' AND (LO.emp_code IN('.$employee_hierarchy.'))';
}
else
{
	$emp_val_rds=" AND emp_code='".$emp_code."'";
}
$sqllocation = "SELECT EM.emp_code,EM.emp_name,DATE_FORMAT(LO.date,'%T') as att_time FROM employee_master EM INNER  JOIN location LO 
			    ON LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')='".$currentdate."' 
				".$emp_val_rds." ORDER BY EM.emp_name ASC";
				
$resultlocation = mysqli_query($link,$sqllocation);
$count=mysqli_num_rows($resultlocation);
	if($count>0){
		$contentsrowcolumn=$count.'¥'.'4';
		while($rowlocation = mysqli_fetch_assoc($resultlocation))
		{
		   $emp_code_lower=$rowlocation['emp_code'];
		   $emp_name=$rowlocation['emp_name'];
		   ${att_time.$emp_code_lower}=$rowlocation['att_time'];

		   if(strtoupper($nick_name)=='STAR' || strtoupper($nick_name)=='START')
		   {
		   	$sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM customer_visit_details WHERE emp_code='".$emp_code_lower."' 
							AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')='".$currentdate."' ";
		   }
		   else
		   {
			  $sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM prev_order_counting_master WHERE 
			  					SUBSTRING(order_no,-19,5)='".$emp_code_lower."' 
							AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')='".$currentdate."' ";
		   }
		   $rscustomervisit=mysqli_query($link,$sqlcustomervisit);
		   $rowcustomervisit=mysqli_fetch_assoc($rscustomervisit);
		   $total_customer_visit=$rowcustomervisit['tot_customer_visit'];
			
			$contents = (($emp_code_lower!='')?$emp_code_lower: ' ')."^";
			$contents .= (($emp_name!='')?$emp_name: ' ')."^";
			$contents .= ((${att_time.$emp_code_lower}!='')?${att_time.$emp_code_lower}: 'ABSENT')."^";
			$contents .= (($total_customer_visit!='')?$total_customer_visit: 0);
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/manager-activity-download.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=manager-activity.txt");
	print "$datacontents"; 		
?>
