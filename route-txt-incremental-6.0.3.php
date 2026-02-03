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
	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
	$emp_hierarchy_condition_one=' AND RM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
	$emp_hierarchy_condition_one=" AND RM.emp_code='".$emp_code."'";
}
if($incremental_download=='no')
{
	$login_condition="";
	$login_condition_one="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$login_condition_one=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
	//For checking employee menu access
	$sqlmenuaccess="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code."'";
	$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
	$countmenuaccess=mysqli_num_rows($rsmenuaccess);
	$not_accessible_menu_array=array();
	if($countmenuaccess >0)
	{
		while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
		{
			array_push($not_accessible_menu_array,$rowmenuaccess['not_accessible_menu']);
		}
	}
	if(in_array('order',$not_accessible_menu_array))
	{
		$customer_type_condition=" AND CM.cust_type='D'";
	}
	else
	{
		//$customer_type_condition=" AND CM.cust_type IN('R','D')";
		$customer_type_condition="";
	}
}
else
{
	$customer_type_condition='';
}

	if(modified_customer_emp_route=='yes' && $nick_name!='OSHEA' && $nick_name!='HALDIRAM' && $nick_name!='PRAKASH' && $nick_name!='ARCHITA')
	{ 
		$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
							route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
							".$emp_hierarchy_condition_one." ".$login_condition_one." ";
		$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
		$countcustomerroute=mysqli_num_rows($resultcustomerroute);
		if($countcustomerroute>0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
			{
				$route_code=$rowscustomerroute['route_code'];
				$sqlroute="SELECT  route_name FROM route_master WHERE route_code='".$route_code."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$rowroute=mysqli_fetch_assoc($rsroute);		
	
				$dns_route_code='';
				$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
				$contents  = (($route_code!='')?$route_code: ' ')."^";
				$contents  .= (($route_name!='')?$route_name: ' ')."^";
				$contents  .= (($route_code!='')?$route_code: ' '); // For dns route code forcefully given the original route code
				$linecontents  .= $contents."\n";
			}
			if($nick_name=='SKIPPER' || $nick_name=='DNV' || $nick_name=='GANESH')
			{
				$sqlrouteextra="SELECT  route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')";
				$rsrouteextra=mysqli_query($link,$sqlrouteextra);
				while($rowrouteextra=mysqli_fetch_assoc($rsrouteextra))
				{
					$contents  = (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' ')."^";
					$contents  .= (($rowrouteextra['route_name']!='')?$rowrouteextra['route_name']: ' ')."^";
					$contents  .= (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' '); // For dns route code forcefully given the original route code
					$linecontents  .= $contents."\n";
				}
				$countcustomerroute=$countcustomerroute+2;
			}
			$contentsrowcolumn=$countcustomerroute.'¥'.'3';

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
	}
	else if($nick_name=='OSHEA' || $nick_name=='HALDIRAM' || $nick_name=='PRAKASH' || $nick_name=='ARCHITA')
	{
		$route_code_array=array();
		$sqlquerycustomerroute="SELECT DISTINCT RM.route_code FROM customer_route_emp_relation RM WHERE 
							route_code IN(SELECT route_code FROM route_master) AND acedns='Y' 
							".$emp_hierarchy_condition_one." ".$login_condition_one." ";
		$resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
		$countcustomerroute=mysqli_num_rows($resultcustomerroute);
		
		$sqlquerydistributorroute="SELECT DISTINCT RM.route_code FROM distributor_route_relation RM WHERE 1  
							".$emp_hierarchy_condition_one." ".$login_condition_one." AND acedns='Y'";
		$resultquerydistributorroute = mysqli_query($link,$sqlquerydistributorroute);
		$countdistributorroute=mysqli_num_rows($resultquerydistributorroute);
		//$contentsrowcolumn=$countcustomerroute.'¥'.'3';
		if($countcustomerroute>0 || $countdistributorroute >0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			if($countcustomerroute>0){
				while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
				{
					$route_code=$rowscustomerroute['route_code'];
					if(!in_array($route_code,$route_code_array))
					{
						array_push($route_code_array,$route_code);
					}
				}
		     }
			 if($countdistributorroute>0){
				 while($rowsdistributorroute = mysqli_fetch_assoc($resultquerydistributorroute))
					{
						$route_code=$rowsdistributorroute['route_code'];
						if(!in_array($route_code,$route_code_array))
						{
							array_push($route_code_array,$route_code);
						}
				  }
			  }
			  $routecount=0;
			  foreach($route_code_array as $routeval){
				$sqlroute="SELECT  route_name FROM route_master WHERE route_code='".$routeval."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$rowroute=mysqli_fetch_assoc($rsroute);		
	
				$dns_route_code='';
				$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
				$contents  = (($routeval!='')?$routeval: ' ')."^";
				$contents  .= (($route_name!='')?$route_name: ' ')."^";
				$contents  .= (($routeval!='')?$routeval: ' '); // For dns route code forcefully given the original route code
				$linecontents  .= $contents."\n";
				$routecount++;
			  }
			if($nick_name=='HALDIRAM')
			{
				$sqlrouteextra="SELECT  route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')";
				$rsrouteextra=mysqli_query($link,$sqlrouteextra);
				while($rowrouteextra=mysqli_fetch_assoc($rsrouteextra))
				{
					$contents  = (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' ')."^";
					$contents  .= (($rowrouteextra['route_name']!='')?$rowrouteextra['route_name']: ' ')."^";
					$contents  .= (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' '); // For dns route code forcefully given the original route code
					$linecontents  .= $contents."\n";
				}
				$routecount=$routecount+2;
			}
			$contentsrowcolumn=$routecount.'¥'.'3';
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
	}
	else
	{
		if(distributor_route_planning=='yes')
		{
			if($emp_code!='C0007'){
				/*$sqlquery="select RM.* from route_master RM,distributor_route_relation DRR WHERE RM.route_code=DRR.route_code 
							".$emp_hierarchy_condition_one." ".$login_condition_one." GROUP BY RM.route_code ORDER BY RM.route_name ASC";*/
				$sqlquery="select RM.* from route_master RM WHERE 1 ".$emp_hierarchy_condition_one." ".$login_condition_one." ORDER BY RM.route_name ASC";
			}
			else
			{
				$sqlquery="select * from route_master  ORDER BY route_name ASC";
			}
		}
		else
		{
			if($emp_code!='C0007'){
				$sqlquery="select RM.* from route_master RM,customer_master CM WHERE RM.route_code=CM.route_code AND CM.acedns='Y' 
							".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." GROUP BY RM.route_code ORDER BY RM.route_name ASC";
			}
			else
			{
				$sqlquery="select * from route_master  ORDER BY route_name ASC";
			}
		}

	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$dns_route_code='';
		while($rowroute = mysqli_fetch_assoc($result))
		{
			$route_name=preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
			$contents  = (($rowroute['route_code']!='')?$rowroute['route_code']: ' ')."^";
			$contents  .= (($route_name!='')?$route_name: ' ')."^";
			$contents  .= (($rowroute['route_code']!='')?$rowroute['route_code']: ' '); // For dns route code forcefully given the original route code
			$linecontents  .= $contents."\n";
		}
		if($nick_name=='NHPL')
		{
			$sqlrouteextra="SELECT  route_code,route_name FROM route_master WHERE route_name IN('Office Visit','Leave Request')";
			$rsrouteextra=mysqli_query($link,$sqlrouteextra);
			while($rowrouteextra=mysqli_fetch_assoc($rsrouteextra))
			{
				$contents  = (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' ')."^";
				$contents  .= (($rowrouteextra['route_name']!='')?$rowrouteextra['route_name']: ' ')."^";
				$contents  .= (($rowrouteextra['route_code']!='')?$rowrouteextra['route_code']: ' '); 
				// For dns route code forcefully given the original route code
				$linecontents  .= $contents."\n";
			}
			$count=$count+2;
		}
		$contentsrowcolumn=$count.'¥'.'3';
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
  }
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/route-txt-incremental-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
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
