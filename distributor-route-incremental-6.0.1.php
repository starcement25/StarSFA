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
	$emp_hierarchy_condition=' AND emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND emp_code='".$emp_code."'";
}
if(employeewise_upperhierarchy=='yes')
{
	//For selection of Route
	$route_code_array=array();
	$emp_hierarchy_route_condition=' AND RM.emp_code IN('.$employee_hierarchy.')';
	if(modified_customer_emp_route=='yes')
	{
	$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
							route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
							".$emp_hierarchy_route_condition;
	$resultquerycustomerroute=mysqli_query($link,$sqlquerycustomerroute);						
	while($rowsquerycustomerroute = mysqli_fetch_assoc($resultquerycustomerroute))
		{
			$route_code=$rowsquerycustomerroute['route_code'];
			if(!in_array($route_code,$route_code_array))
			{
				$route_code_string=$route_code_string."'".$route_code."'".',';
				array_push($route_code_array,$route_code);
			}
		}						
	}
	$route_code_string=substr($route_code_string,0,-1);
	if($nick_name=='OSHEA' || $nick_name=='HALDIRAM')
	{
		$sqlquerydistributorroute="SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1  
							".$emp_hierarchy_route_condition;
		$resultquerydistributorroute=mysqli_query($link,$sqlquerydistributorroute);
		$distributor_route_count=0;						
		while($rowsquerydistributorroute = mysqli_fetch_assoc($resultquerydistributorroute))
		 {
			$route_code_distributor=$rowsquerydistributorroute['route_code'];
			if(!in_array($route_code_distributor,$route_code_array))
			{
				$route_code_string_distributor=$route_code_string_distributor."'".$route_code_distributor."'".',';
				$distributor_route_count++;
				array_push($route_code_array,$route_code_distributor);
			}
		}
		if($distributor_route_count >0){
			$route_code_string_distributor=substr($route_code_string_distributor,0,-1);
			$route_code_string=$route_code_string.','.$route_code_string_distributor;
		}
	}
	$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
	$employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
	//$emp_upper_hierarchy_condition=' AND c1.emp_code IN('.$employee_upper_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
	$emp_hierarchy_condition=' AND emp_code IN('.$employee_final_hierarchy.') AND route_code IN('.$route_code_string.')';
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if($emp_code!='C0007'){
	$sqlquery="select distributor_code,route_code,emp_code FROM distributor_route_relation WHERE 1 ".$emp_hierarchy_condition." ".$login_condition."";
}
else
{
	$sqlquery="select distributor_code,route_code,emp_code FROM distributor_route_relation WHERE 1 ".$login_condition."";
}

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowdistributorroute = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowdistributorroute['distributor_code']!='')?$rowdistributorroute['distributor_code']: ' ')."^";
			$contents  .= (($rowdistributorroute['route_code']!='')?$rowdistributorroute['route_code']: ' ')."^";
			$contents  .= (($rowdistributorroute['emp_code']!='')?$rowdistributorroute['emp_code']: ' ');
			
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
	$url = APICALLLOGURL."/distributor-route-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/route-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=route_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
