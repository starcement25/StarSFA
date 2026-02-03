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
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT farmer_id,emp_code,farmer_name,address,district,taluka,locality,pin,farming_area,type_of_crops,mi_applied_for,spacing,
		chitta_andangal,land_suervey_map,small_farmer_certificate,photo,aadhar,BOQ_generated,quotation_generated,
		document_uploaded_to_TANHODA,total_area,mi_area,linked_dealer,mi_id,farmer_type,mi_reference_no,fittings_accessories,additional_material 
			FROM farmer_master WHERE 
			".$emp_hierarchy_condition.$login_condition." ORDER BY farmer_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'28';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowfarmer = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowfarmer['farmer_id']!='')?$rowfarmer['farmer_id']: ' ')."^";
			$contents  .= (($rowfarmer['emp_code']!='')?$rowfarmer['emp_code']: ' ')."^";
			$contents  .= (($rowfarmer['farmer_name']!='')?$rowfarmer['farmer_name']: ' ')."^";
			$contents  .= (($rowfarmer['address']!='')?$rowfarmer['address']: ' ')."^";
			$contents  .= (($rowfarmer['district']!='')?$rowfarmer['district']: ' ')."^";
			$contents  .= (($rowfarmer['taluka']!='')?$rowfarmer['taluka']: ' ')."^";
			$contents  .= (($rowfarmer['locality']!='')?$rowfarmer['locality']: ' ')."^";
			$contents  .= (($rowfarmer['pin']!='')?$rowfarmer['pin']: ' ')."^";
			$contents  .= (($rowfarmer['farming_area']!='')?$rowfarmer['farming_area']: ' ')."^";
			$contents  .= (($rowfarmer['type_of_corps']!='')?$rowfarmer['type_of_corps']: ' ')."^";
			$contents  .= (($rowfarmer['mi_applied_for']!='')?$rowfarmer['mi_applied_for']: ' ')."^";
			$contents  .= (($rowfarmer['spacing']!='')?$rowfarmer['spacing']: ' ')."^";
			$contents  .= (($rowfarmer['chitta_andangal']!='')?$rowfarmer['chitta_andangal']: ' ')."^";
			$contents  .= (($rowfarmer['land_suervey_map']!='')?$rowfarmer['land_suervey_map']: ' ')."^";
			$contents  .= (($rowfarmer['small_farmer_certificate']!='')?$rowfarmer['small_farmer_certificate']: ' ')."^";
			$contents  .= (($rowfarmer['photo']!='')?$rowfarmer['photo']: ' ')."^";
			$contents  .= (($rowfarmer['aadhar']!='')?$rowfarmer['aadhar']: ' ')."^";
			$contents  .= (($rowfarmer['BOQ_generated']!='')?$rowfarmer['BOQ_generated']: ' ')."^";
			$contents  .= (($rowfarmer['quotation_generated']!='')?$rowfarmer['quotation_generated']: ' ')."^";
			$contents  .= (($rowfarmer['document_uploaded_to_TANHODA']!='')?$rowfarmer['document_uploaded_to_TANHODA']: ' ')."^";
			$contents  .= (($rowfarmer['total_area']!='')?$rowfarmer['total_area']: ' ')."^";
			$contents  .= (($rowfarmer['mi_area']!='')?$rowfarmer['mi_area']: ' ')."^";
			$contents  .= (($rowfarmer['linked_dealer']!='')?$rowfarmer['linked_dealer']: ' ')."^";
			$contents  .= (($rowfarmer['mi_id']!='')?$rowfarmer['mi_id']: ' ')."^";
			$contents  .= (($rowfarmer['farmer_type']!='')?$rowfarmer['farmer_type']: ' ')."^";
			$contents  .= (($rowfarmer['mi_reference_no']!='')?$rowfarmer['mi_reference_no']: ' ')."^";
			$contents  .= (($rowfarmer['fittings_accessories']!='')?$rowfarmer['fittings_accessories']: ' ')."^";
			$contents  .= (($rowfarmer['additional_material']!='')?$rowfarmer['additional_material']: ' ');		

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
			$datacontents = '0'.'¥'.'28';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/farmer-master-txt-incremental-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=farmer_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
