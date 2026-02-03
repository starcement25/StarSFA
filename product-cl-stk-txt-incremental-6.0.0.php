<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
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
	foreach($emp_vertical_value_array as $emp_vertical_array_val)
	{
		$final_emp_vertical_value_array[]=ltrim($emp_vertical_array_val);
	}
	$emp_vertical_value = "'".implode("','", $final_emp_vertical_value_array)."'";
	$condition_one=' AND PM.vertical_value IN ('.$emp_vertical_value.')';
}
else
{
	$condition_one="";
}


if(sale=='no'){
	if($incremental_download=='no')
	{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
		$login_condition_one="AND PM.acedns='Y' AND PM.black_list='N'";
	}
	else
	{
		$login_condition="AND UNIX_TIMESTAMP(PM.download_time_cl_stk) > UNIX_TIMESTAMP('".$last_update_time."')";
		$login_condition_one="AND UNIX_TIMESTAMP(BPWS.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	}
	$sqlbranches="SELECT * FROM branch_master WHERE 1";
	$rsbranches=mysqli_query($link,$sqlbranches);
	$countbranches=mysqli_num_rows($rsbranches);
	
	if($countbranches>1)
	{
	   /*$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk
					FROM product_master PM,employee_master EM
					WHERE EM.branch_code = PM.branch_code AND PM.prod_desc <>''  
					AND EM.emp_code ='".$emp_code."' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";*/
		if(branch_wise_cl_stk=='yes')
		{
		$sqlquery="SELECT DISTINCT PM.prod_code,BPWS.closing_stk AS cl_stk FROM product_master PM,branch_product_wise_stock BPWS,employee_master EM
						WHERE PM.prod_desc <>'' AND EM.branch_code=BPWS.branch_code AND PM.prod_code=BPWS.product_code 
						".$condition_one.$login_condition_one." AND EM.emp_code ='".$emp_code."'  ORDER BY PM.prod_desc ASC";
		}
		else
		{
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM WHERE PM.prod_desc <>'' 
					".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";	
		}		
	}
	else
	{
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk FROM product_master PM WHERE PM.prod_desc <>'' 
					 ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
	}
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<prod_code><![CDATA['.mb_convert_encoding($rowproduct['prod_code'], 'UTF-8', 'UTF-8').']]></prod_code>
						<prod_desc><![CDATA['.mb_convert_encoding($rowproduct['prod_desc'], 'UTF-8', 'UTF-8').']]></prod_desc>
						<cl_stk><![CDATA['.mb_convert_encoding($rowproduct['cl_stk'], 'UTF-8', 'UTF-8').']]></cl_stk>';
			$contents.="</data>";*/
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['cl_stk']!='')?$rowproduct['cl_stk']: ' ');
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
			$datacontents = '0'.'¥'.'2';
		}
	}
}
else
{
	$sqlempbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempbranch=mysqli_query($link,$sqlempbranch);
	$rowempbranch=mysqli_fetch_assoc($rsempbranch);
	$branch_code=$rowempbranch['branch_code'];
	if($branch_code!=='')
	{
		$sqlemprds="SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'";
		$rsemprds=mysqli_query($link,$sqlemprds);
		$rowemprds=mysqli_fetch_assoc($rsemprds);
		$rds_code=$rowemprds['rds_code'];
			
		$sqlquery="SELECT BRPWS.product_code,BRPWS.closing_stk FROM product_master PM,branch_rds_product_wise_stock BRPWS 
				WHERE PM.prod_code=BRPWS.product_code AND BRPWS.branch_code='".$branch_code."' AND BRPWS.rds_code='".$rds_code."' 
				".$condition_one." ORDER BY PM.prod_code ASC";			
	}
	else
	{
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$sql_rds_details='SELECT rds_code FROM rds_master WHERE emp_code IN('.$employee_hierarchy.')';
		$rs_rds_details=mysqli_query($link,$sql_rds_details);
		while($row_rds_details=mysqli_fetch_assoc($rs_rds_details))
		{
			$rds_list=$rds_list."'".$row_rds_details['rds_code']."'".',';
		}
		$rds_list=substr($rds_list,0,-1);
		$sqlquery="SELECT BRPWS.product_code,SUM(BRPWS.closing_stk) AS closing_stk FROM product_master PM,branch_rds_product_wise_stock BRPWS 
				WHERE PM.prod_code=BRPWS.product_code ".$condition_one." AND BRPWS.rds_code IN(".$rds_list.") GROUP BY 
				BRPWS.product_code ORDER BY PM.prod_code ASC";			
	}
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowproduct['product_code']!='')?$rowproduct['product_code']: ' ')."^";
			$contents  .= (($rowproduct['closing_stk']!='')?$rowproduct['closing_stk']: ' ');
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
			$datacontents = '0'.'¥'.'2';
		}
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/product-cl-stk-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/product-cl-stk-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=product_cl_stk.txt");
	print "$datacontents";	
	mysqli_close($link);	
	
?>
