<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 */
 	$datacontents = '0'.'¥'.'22';
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=kyc_master.txt");
	print "$datacontents"; 
 exit();
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
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(created_datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT * FROM kyc_master WHERE 
			".$emp_hierarchy_condition.$login_condition." ORDER BY firm_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'22';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowkyc = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowkyc['kyc_id']!='')?$rowkyc['kyc_id']: ' ')."^";
			$contents  .= (($rowkyc['emp_code']!='')?$rowkyc['emp_code']: ' ')."^";
			$contents  .= (($rowkyc['wholesale_retail']!='')?$rowkyc['wholesale_retail']: ' ')."^";
			$contents  .= (($rowkyc['firm_name']!='')?$rowkyc['firm_name']: ' ')."^";
			$contents  .= (($rowkyc['address']!='')?$rowkyc['address']: ' ')."^";
			$contents  .= (($rowkyc['pin']!='')?$rowkyc['pin']: ' ')."^";
			$contents  .= (($rowkyc['area']!='')?$rowkyc['area']: ' ')."^";
			$contents  .= (($rowkyc['city']!='')?$rowkyc['city']: ' ')."^";
			$contents  .= (($rowkyc['state']!='')?$rowkyc['state']: ' ')."^";
			$contents  .= (($rowkyc['contact_person_name']!='')?$rowkyc['contact_person_name']: ' ')."^";
			$contents  .= (($rowkyc['mobile']!='')?$rowkyc['mobile']: ' ')."^";
			$contents  .= (($rowkyc['GST']!='')?$rowkyc['GST']: ' ')."^";
			$contents  .= (($rowkyc['PAN']!='')?$rowkyc['PAN']: ' ')."^";
			$contents  .= (($rowkyc['birth_date']!='')?$rowkyc['birth_date']: ' ')."^";
			$contents  .= (($rowkyc['anniversary_date']!='')?$rowkyc['anniversary_date']: ' ')."^";
			$contents  .= (($rowkyc['years_business']!='')?$rowkyc['years_business']: ' ')."^";
			$contents  .= (($rowkyc['selling_brand']!='')?$rowkyc['selling_brand']: ' ')."^";
			$contents  .= (($rowkyc['customer_other_qty']!='')?$rowkyc['customer_other_qty']: ' ')."^";
			$contents  .= (($rowkyc['customer_agree']!='')?$rowkyc['customer_agree']: ' ')."^";
			$contents  .= (($rowkyc['customer_p_category']!='')?$rowkyc['customer_p_category']: ' ')."^";
			$contents  .= (($rowkyc['customer_deal_business']!='')?$rowkyc['customer_deal_business']: ' ')."^";
			$contents  .= (($rowkyc['card_image']!='')?$rowkyc['card_image']: ' ');

			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'22';
		}
	}
	$datacontents = '0'.'¥'.'22';
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/kyc-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=kyc_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
