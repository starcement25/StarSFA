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

/*if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='RM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="RM.emp_code='".$emp_code."'";
}
if(sale=='yes')
{
	$sqlemp="SELECT emp_code FROM employee_master WHERE 
			branch_code=(SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."')";
	$rsemp=mysqli_query($link,$sqlemp);
	while($rowemp=mysqli_fetch_assoc($rsemp))
	{
		$emp_code_list=$emp_code_list."'".$rowemp['emp_code']."'".',';
	}
	$emp_code_list=substr($emp_code_list,0,-1);
	$emp_val_rds=' OR emp_code IN('.$emp_code_list.')';
}
else
{
	$emp_val_rds='';
}*/

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

 /*if($emp_code!='C0007'){
 	echo $sqlquery="SELECT DISTINCT 
 			RM.rds_code, RM.rds_name, RM.emp_code,RM.rds_type
			FROM rds_master RM  WHERE (".$emp_hierarchy_condition." ".$login_condition.")".$emp_val_rds." ORDER BY RM.rds_name ASC";
 }
 else
 {*/
	$sqlquery="SELECT RM.rds_code, RM.rds_name, RM.emp_code,RM.rds_type FROM rds_master RM WHERE 1  ".$login_condition." ORDER BY RM.rds_name ASC";
 //}

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	if(substr($emp_code,0,1)=='C' && sale=='yes')
	{
		$contentsrowcolumn=$count.'^^'.'9';
	}
	else if(substr($emp_code,0,1)!='C' && sale=='yes')
	{
		$contentsrowcolumn=($count-1).'^^'.'9';
	}
	else
	{
		$contentsrowcolumn=$count.'^^'.'9';
	}
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowsrds = mysqli_fetch_assoc($result))
		{
			if(sale=='yes'){
				if($rowsrds['emp_code']!=$emp_code)
				{
					$contents  = (($rowsrds['rds_code']!='')?$rowsrds['rds_code']: ' ')."^";
					$contents  .= (($rowsrds['rds_name']!='')?$rowsrds['rds_name']: ' ')."^";
					$contents  .= (($rowsrds['emp_code']!='')?$rowsrds['emp_code']: ' ')."^";
					$contents  .= (($rowsrds['rds_type']!='')?$rowsrds['rds_type']: ' ')."^";
					$contents  .= (($rowsrds['rds_nick_name']!='')?$rowsrds['rds_nick_name']: ' ')."^";
					$contents  .= (($rowsrds['rds_address']!='')?$rowsrds['rds_address']: ' ')."^";
					$contents  .= (($rowsrds['rds_pin_code']!='')?$rowsrds['rds_pin_code']: ' ')."^";
					$contents  .= (($rowsrds['rds_tin_no']!='')?$rowsrds['rds_tin_no']: ' ')."^";
					$contents  .= (($rowsrds['rds_cst_no']!='')?$rowsrds['rds_cst_no']: ' ');
					$linecontents  .= $contents."\n";
				}
			}
			else
			{
				$contents  = (($rowsrds['rds_code']!='')?$rowsrds['rds_code']: ' ')."^";
				$contents  .= (($rowsrds['rds_name']!='')?$rowsrds['rds_name']: ' ')."^";
				$contents  .= (($rowsrds['emp_code']!='')?$rowsrds['emp_code']: ' ')."^";
				$contents  .= (($rowsrds['rds_type']!='')?$rowsrds['rds_type']: ' ')."^";
				$contents  .= (($rowsrds['rds_nick_name']!='')?$rowsrds['rds_nick_name']: ' ')."^";
				$contents  .= (($rowsrds['rds_address']!='')?$rowsrds['rds_address']: ' ')."^";
				$contents  .= (($rowsrds['rds_pin_code']!='')?$rowsrds['rds_pin_code']: ' ')."^";
				$contents  .= (($rowsrds['rds_tin_no']!='')?$rowsrds['rds_tin_no']: ' ')."^";
				$contents  .= (($rowsrds['rds_cst_no']!='')?$rowsrds['rds_cst_no']: ' ');
				$linecontents  .= $contents."\n";
			}
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'^^'.'0';
		}
		else
		{
			$datacontents = '0'.'^^'.'9';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/rds-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/rds-master-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=rds_master.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
