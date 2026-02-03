<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_values_parts=substr($emp_vertical_value,0,1);
	if($emp_vertical_values_parts == 'M') $emp_vertical_value='MACROMAN';
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
}
else
{
	$emp_vertical_value_array=array();
}
//print_r($emp_vertical_value_array);	
if(vertical_fields=='yes'){
	if(count($emp_vertical_value_array) >0)
	{
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	
		$linecontents='';
		foreach($emp_vertical_value_array as $emp_vertical_values)
		{
			if($nick_name=='RUPA')
			{
							$sqlquery="SELECT vertical,file_name,	file_version FROM catalogue_info WHERE vertical='".$emp_vertical_values."' 
			ORDER BY download_time DESC ";

			}
			else
			{
			$sqlquery="SELECT vertical,file_name,	file_version FROM catalogue_info WHERE vertical='".$emp_vertical_values."' 
			ORDER BY download_time DESC LIMIT 0,1";
			}
			$result = mysqli_query($link,$sqlquery);
			$count=mysqli_num_rows($result);
				$cnt=1;
				if($count>0){
					while($rowbranch = mysqli_fetch_assoc($result))
					{
							if(strpos($rowbranch['file_name'],'.pdf')!=false){
								$vertical=$rowbranch['vertical'].' - PDF';
							}
							if(strpos($rowbranch['file_name'],'.mp4')!=false){
								$vertical=$rowbranch['vertical'].' - VIDEO';
							}
							$contents  = (($vertical!='')?$vertical: ' ')."^";
							$contents  .= (($rowbranch['file_name']!='')?$rowbranch['file_name']: ' ')."^";
							$contents  .= (($rowbranch['file_version']!='')?$rowbranch['file_version']: ' ');
							
							$linecontents  .= $contents."\n";
					}
					$cnt++;
				}
		}
		$contentsrowcolumn=$cnt.'¥'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$sqlquery="SELECT vertical,file_name,file_version FROM catalogue_info WHERE 1 ORDER BY download_time DESC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=0;
	if($count>0){
		while($rowbranch = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowbranch['vertical']!='')?$rowbranch['vertical']: ' ')."^";
			$contents  .= (($rowbranch['file_name']!='')?$rowbranch['file_name']: ' ')."^";
			$contents  .= (($rowbranch['file_version']!='')?$rowbranch['file_version']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$cnt.'¥'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}

header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=catalogue_info.txt");
print "$datacontents"; 		
?>
