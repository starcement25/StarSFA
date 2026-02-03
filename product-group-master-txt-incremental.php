<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100002157';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND PGM.vertical_value IN ('.$emp_vertical_value.')';
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
	$condition_one=" AND PGM.vertical_value IN (".$emp_vertical_value.") AND PM.branch_code='".$emp_branch_code."'";
}*/
if($incremental_download=='no')
{
	if($nick_name=='SMOTO')
	{
		$login_condition='';
	}
	else
	{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
	}
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PGM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}


$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
	$sqlquery="SELECT DISTINCT PGM.* FROM product_group_master PGM,employee_master EM,product_master PM WHERE 
				EM.branch_code=PM.branch_code AND PM.product_group_code=PGM.product_group_code 
				AND EM.emp_code='".$emp_code."' ".$condition_one." ".$login_condition." 
				ORDER BY PGM.product_group_name ASC";
}
else
{
	if($nick_name=='SMOTO')
	{
		$sqlquery="SELECT DISTINCT PGM.* FROM product_group_master PGM WHERE PGM.is_download='yes' ".$condition_one." ".$login_condition." 
				ORDER BY PGM.product_group_name ASC";
	}
	else
	{
		$sqlquery="SELECT DISTINCT PGM.* FROM product_group_master PGM,product_master PM WHERE PM.product_group_code=PGM.product_group_code 
		".$condition_one." ".$login_condition." ORDER BY PGM.product_group_name ASC";
	}
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowproductgroup = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowproductgroup['product_group_code']!='')?$rowproductgroup['product_group_code']: ' ')."^";
			$contents  .= (($rowproductgroup['product_group_name']!='')?$rowproductgroup['product_group_name']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/product-group-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-group-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving //Users position
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
	header("Content-Disposition: attachment; filename=product_group_master.txt");
	print "$datacontents"; 		
?>
