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
	/*$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND PSGM.vertical_value IN ('.$emp_vertical_value.')';*/
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PSGM.vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
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
	$condition_one=" AND PSGM.vertical_value IN (".$emp_vertical_value.") AND PM.branch_code='".$emp_branch_code."'";
}*/

if($incremental_download=='no')
{
		$login_condition='';
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PSGM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
	/*$sqlquery="SELECT DISTINCT PSGM.* FROM product_sub_group_master PSGM,employee_master EM,product_master PM WHERE 
			PM.product_sub_group_code=PSGM.product_sub_group_code AND 
			EM.branch_code=PM.branch_code  AND EM.emp_code='".$emp_code."' ".$condition_one." ".$login_condition."";*/
	$sqlquery="SELECT DISTINCT PSGM.* FROM product_sub_group_master PSGM WHERE 1 ".$condition_one." ".$login_condition." ORDER 
				BY PSGM.product_sub_group_name ASC";		
}
else
{
	$sqlquery="SELECT DISTINCT PSGM.* FROM product_sub_group_master PSGM WHERE 1 ".$condition_one." ".$login_condition." 
				ORDER BY PSGM.product_sub_group_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowproductsubgroup = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowproductsubgroup['product_sub_group_code']!='')?$rowproductsubgroup['product_sub_group_code']: ' ')."^";
			$contents  .= (($rowproductsubgroup['product_group_code']!='')?$rowproductsubgroup['product_group_code']: ' ')."^";
			$contents  .= (($rowproductsubgroup['product_sub_group_name']!='')?$rowproductsubgroup['product_sub_group_name']: ' ');
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
	$url = APICALLLOGURL."/product-sub-group-master-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-sub-group-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=product_sub_group_master.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
