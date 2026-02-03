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
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition=" AND acedns!='N'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT site_id,emp_code,site_name,address,city,location,sub_area,state,pin,contact_person,phone_no,site_reffered_by,
		sq_feet_area,facilitator_mapped,project_type,current_status,acedns,product_info,product_category,check_in_date,dealer_involved,
		sub_dealer_involved,stage_of_construction,material_sold_value,desc_thickness_qty,veneer_species,veneer_qty  FROM site_master WHERE 
			".$emp_hierarchy_condition.$login_condition." ORDER BY site_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'27';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowsite = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsite['site_id']!='')?$rowsite['site_id']: ' ')."^";
			$contents  .= (($rowsite['emp_code']!='')?$rowsite['emp_code']: ' ')."^";
			$contents  .= (($rowsite['site_name']!='')?$rowsite['site_name']: ' ')."^";
			$contents  .= (($rowsite['address']!='')?$rowsite['address']: ' ')."^";
			$contents  .= (($rowsite['city']!='')?$rowsite['city']: ' ')."^";
			$contents  .= (($rowsite['location']!='')?$rowsite['location']: ' ')."^";
			$contents  .= (($rowsite['sub_area']!='')?$rowsite['sub_area']: ' ')."^";
			$contents  .= (($rowsite['state']!='')?$rowsite['state']: ' ')."^";
			$contents  .= (($rowsite['pin']!='')?$rowsite['pin']: ' ')."^";
			$contents  .= (($rowsite['contact_person']!='')?$rowsite['contact_person']: ' ')."^";
			$contents  .= (($rowsite['phone_no']!='')?$rowsite['phone_no']: ' ')."^";
			$contents  .= (($rowsite['site_reffered_by']!='')?$rowsite['site_reffered_by']: ' ')."^";
			$contents  .= (($rowsite['sq_feet_area']!='')?$rowsite['sq_feet_area']: ' ')."^";
			$contents  .= (($rowsite['facilitator_mapped']!='')?$rowsite['facilitator_mapped']: ' ')."^";
			$contents  .= (($rowsite['project_type']!='')?$rowsite['project_type']: ' ')."^";
			$contents  .= (($rowsite['current_status']!='')?$rowsite['current_status']: ' ')."^";
			$contents  .= (($rowsite['acedns']!='')?$rowsite['acedns']: ' ')."^";
			$contents  .= (($rowsite['product_info']!='')?$rowsite['product_info']: ' ')."^";
			$contents  .= (($rowsite['product_category']!='')?$rowsite['product_category']: ' ')."^";
			$contents  .= (($rowsite['check_in_date']!='')?$rowsite['check_in_date']: ' ')."^";
			$contents  .= (($rowsite['dealer_involved']!='')?$rowsite['dealer_involved']: ' ')."^";
			$contents  .= (($rowsite['sub_dealer_involved']!='')?$rowsite['sub_dealer_involved']: ' ')."^";
			$contents  .= (($rowsite['stage_of_construction']!='')?$rowsite['stage_of_construction']: ' ')."^";
			$contents  .= (($rowsite['material_sold_value']!='')?$rowsite['material_sold_value']: ' ')."^";
			$contents  .= (($rowsite['desc_thickness_qty']!='')?$rowsite['desc_thickness_qty']: ' ')."^";
			$contents  .= (($rowsite['veneer_species']!='')?$rowsite['veneer_species']: ' ')."^";
			$contents  .= (($rowsite['veneer_qty']!='')?$rowsite['veneer_qty']: ' ');
			
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
			$datacontents = '0'.'¥'.'27';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/site-master-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=site_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
