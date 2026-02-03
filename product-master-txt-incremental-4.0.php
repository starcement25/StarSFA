<?php
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND PM.vertical_value IN ('.$emp_vertical_value.')';
}
else
{
	$condition_one="";
}
/*else if(vertical_fields=='yes' && vertical_branch_relation=='yes'){
	$sqlempvertical="SELECT vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_branch_code=$rowempvertical['branch_code'];
	$condition_one=" AND PM.vertical_value IN (".$emp_vertical_value.") AND PM.branch_code='".$emp_branch_code."'";
}*/

if($incremental_download=='no')
{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
	/*$sqlquery="SELECT DISTINCT PM.*
				FROM product_master PM,employee_master EM
				WHERE EM.branch_code = PM.branch_code AND PM.prod_desc <>''  
				AND EM.emp_code ='".$emp_code."' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";*/
	$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";			
}
else
{
	$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
}
			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'12';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_code']!='')?$rowproduct['product_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_name']!='')?$rowproduct['product_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_code']!='')?$rowproduct['product_sub_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_name']!='')?$rowproduct['product_sub_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_code']!='')?$rowproduct['product_brand_code']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_name']!='')?$rowproduct['product_brand_name']: ' ')."^";
			$contents  .= (($rowproduct['prod_desc']!='')?$rowproduct['prod_desc']: ' ')."^";
			$contents  .= (($rowproduct['black_list']!='')?$rowproduct['black_list']: ' ')."^";
			$contents  .= (($rowproduct['acedns']!='')?$rowproduct['acedns']: ' ')."^";
			$contents  .= (($rowproduct['UOM1']!='')?$rowproduct['UOM1']: ' ')."^";
			$contents  .= (($rowproduct['UOM2']!='')?$rowproduct['UOM2']: ' ');
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
	$url = "http://www.acedns.in/acednsproduct/product-master-txt-incremental-4.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-master-txt-incremental-4.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=product_master.txt");
	print "$datacontents";	
?>
