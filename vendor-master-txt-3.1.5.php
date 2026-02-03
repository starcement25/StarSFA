<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$sqlquery="SELECT vendor_code,vendor_name FROM vendor_master  ORDER BY vendor_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowvendor = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowvendor['vendor_code']!='')?$rowvendor['vendor_code']: ' ')."^";
				$contents  .= (($rowvendor['vendor_name']!='')?$rowvendor['vendor_name']: ' ');
				
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=vendor_master.txt");
	print "$datacontents"; 		
?>
