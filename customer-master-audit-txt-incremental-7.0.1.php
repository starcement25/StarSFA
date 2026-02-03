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
	$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="c1.emp_code='".$emp_code."'";
}
//echo $employee_hierarchy;
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
   //echo $route_code_string;
	$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
	$employee_final_hierarchy=$employee_upper_hierarchy.','.$employee_hierarchy;
	//$emp_upper_hierarchy_condition=' AND c1.emp_code IN('.$employee_upper_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
	$emp_hierarchy_condition=' c1.emp_code IN('.$employee_final_hierarchy.') AND c1.route_code IN('.$route_code_string.')';
}

if($incremental_download=='no')
{
	$login_condition=" AND c1.acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(c1.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
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
		$customer_type_condition=" AND c1.cust_type='D'";
	}
	else
	{
		//$customer_type_condition=" AND c1.cust_type IN('R','D')";
		$customer_type_condition="";
	}
}
else
{
	$customer_type_condition='';
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

	if(modified_customer_emp_route=='yes')
	{
		$sqlquerycustomerroute="SELECT DISTINCT c1.customer_code,c1.route_code,c1.acedns,c1.emp_code FROM customer_route_emp_relation c1 WHERE 
							".$emp_hierarchy_condition." ".$login_condition." AND customer_code IN(SELECT customer_code FROM customer_master) 
							ORDER BY customer_code ASC,acedns DESC";
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
	
			$dns_customer_code='';
			$customebr_code_array=array();
			$customer_count=0;
			while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
			{
				$customer_code=$rowscustomerroute['customer_code'];
				$route_code=$rowscustomerroute['route_code'];
				$acedns=$rowscustomerroute['acedns'];
				
				if(!in_array($customer_code,$customebr_code_array))
				{
					$sqlcustomer="SELECT  customer_name,emp_code,current_balance,credit_limit,black_list,acedns,
							TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,
							 cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email FROM customer_master 
							WHERE customer_code='".$customer_code."'";
					$rscustomer=mysqli_query($link,$sqlcustomer);
					$rowcustomer=mysqli_fetch_assoc($rscustomer);		
					if($rowcustomer['customer_name']!=''){
					$contents  = (($customer_code!='')?$customer_code: ' ')."^";
					$contents  .= (($rowcustomer['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer['customer_name'])): ' ')."^";
					$contents  .= (($route_code!='')?$route_code: ' ')."^";
					$contents  .= (($rowcustomer['emp_code']!='')?$rowcustomer['emp_code']: ' ')."^";
					$contents  .= (($rowcustomer['current_balance']!='')?$rowcustomer['current_balance']: ' ')."^";
					$contents  .= (($rowcustomer['credit_limit']!='')?$rowcustomer['credit_limit']: ' ')."^";
					$contents  .= (($acedns!='')?$acedns: ' ')."^";
					$contents  .= (($rowcustomer['black_list']!='')?$rowcustomer['black_list']: ' ')."^";
					$contents  .= (($rowcustomer['TD']!='')?$rowcustomer['TD']: '0')."^";
					$contents  .= (($rowcustomer['cust_type']!='')?$rowcustomer['cust_type']: ' ')."^";
					$contents  .= (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";
					$contents  .= (($rowcustomer['sauda_validity_period']!='')?$rowcustomer['sauda_validity_period']: ' ')."^";
					$contents  .= (($rowcustomer['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer['address'])): ' ')."^";
					$contents  .= (($rowcustomer['pin']!='')?$rowcustomer['pin']: ' ')."^";
					$contents  .= (($rowcustomer['phone_no']!='')?$rowcustomer['phone_no']: ' ')."^";
					$contents  .= (($customer_code!='')?$customer_code: ' ')."^";// For dns customer code forcefully given the original customer code
					$contents  .= (($rowcustomer['landline_no']!='')?$rowcustomer['landline_no']: ' ')."^";
					$contents  .= (($rowcustomer['owner_name']!='')?$rowcustomer['owner_name']: ' ')."^";
					$contents  .= (($rowcustomer['owner_phone']!='')?$rowcustomer['owner_phone']: ' ')."^";
					$contents  .= (($rowcustomer['cust_class']!='')?$rowcustomer['cust_class']: ' ')."^";
					$contents  .= (($rowcustomer['weekly_closing_day']!='')?$rowcustomer['weekly_closing_day']: ' ')."^";
					$contents  .= (($rowcustomer['coverage_type']!='')?$rowcustomer['coverage_type']: ' ')."^";
					$contents  .= (($rowcustomer['TIN']!='')?$rowcustomer['TIN']: ' ')."^";
					$contents  .= (($rowcustomer['PAN']!='')?$rowcustomer['PAN']: ' ')."^";
					$contents  .= (($rowcustomer['minimum_stock']!='')?$rowcustomer['minimum_stock']: ' ')."^";
					$contents  .= (($rowcustomer['branch_code']!='')?$rowcustomer['branch_code']: ' ')."^";
					$contents  .= (($rowcustomer['visit_day']!='')?$rowcustomer['visit_day']: ' ')."^";
					$contents  .= (($rowcustomer['email']!='')?$rowcustomer['email']: ' ');
					$linecontents  .= $contents."\n";
					//For secondary sales all distributor downloading
					if(!in_array($rowcustomer['rds_tag'],$customebr_code_array))
					{
						$sqlrdswisecustomer="SELECT  customer_name,route_code,current_balance,credit_limit,black_list,
											acedns,TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,
											owner_phone,cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email 
											FROM customer_master WHERE customer_code='".$rowcustomer['rds_tag']."'";
						$rsrdswisecustomer=mysqli_query($link,$sqlrdswisecustomer);
						$rowrdswisecustomer=mysqli_fetch_assoc($rsrdswisecustomer);
						if($rowrdswisecustomer['customer_name']!=''){
						$contents  = (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowrdswisecustomer['customer_name'])): ' ')."^";
						$contents  .= (($rowrdswisecustomer['route_code']!='')?$rowrdswisecustomer['route_code']: ' ')."^";
						$contents  .= (($emp_code!='')?$emp_code: ' ')."^";
						$contents  .= (($rowrdswisecustomer['current_balance']!='')?$rowrdswisecustomer['current_balance']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['credit_limit']!='')?$rowrdswisecustomer['credit_limit']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['acedns']!='')?$rowrdswisecustomer['acedns']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['black_list']!='')?$rowrdswisecustomer['black_list']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['TD']!='')?$rowrdswisecustomer['TD']: '0')."^";
						$contents  .= (($rowrdswisecustomer['cust_type']!='')?$rowrdswisecustomer['cust_type']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['rds_tag']!='')?$rowrdswisecustomer['rds_tag']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['sauda_validity_period']!='')?$rowrdswisecustomer['sauda_validity_period']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowrdswisecustomer['address'])): ' ')."^";
						$contents  .= (($rowrdswisecustomer['pin']!='')?$rowrdswisecustomer['pin']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['phone_no']!='')?$rowrdswisecustomer['phone_no']: ' ')."^";
						$contents  .= (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";// For dns customer code forcefully given the original customer code
						$contents  .= (($rowrdswisecustomer['landline_no']!='')?$rowrdswisecustomer['landline_no']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['owner_name']!='')?$rowrdswisecustomer['owner_name']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['owner_phone']!='')?$rowrdswisecustomer['owner_phone']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['cust_class']!='')?$rowrdswisecustomer['cust_class']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['weekly_closing_day']!='')?$rowrdswisecustomer['weekly_closing_day']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['coverage_type']!='')?$rowrdswisecustomer['coverage_type']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['TIN']!='')?$rowrdswisecustomer['TIN']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['PAN']!='')?$rowrdswisecustomer['PAN']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['minimum_stock']!='')?$rowrdswisecustomer['minimum_stock']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['branch_code']!='')?$rowrdswisecustomer['branch_code']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['visit_day']!='')?$rowrdswisecustomer['visit_day']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['email']!='')?$rowrdswisecustomer['email']: ' ');

						array_push($customebr_code_array,$rowcustomer['rds_tag']);
						$linecontents  .= $contents."\n";
						$customer_count++;
						}
					}
					
					 array_push($customebr_code_array,$customer_code);
					 $customer_count++;
					}
				}
			}
			$contentsrowcolumn=$customer_count.'¥'.'28';
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
				$datacontents = '0'.'¥'.'28';
			}
		}
	}
	else
	{
		 if($nick_name=='SELVEL'){
			 $sqlquery="SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC";
		 }
		 else
		 {
			 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
			 /*$sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
							c1.TD,c1.cust_type,c1.rds_tag
							FROM customer_master c1,route_master r, employee_master em WHERE ".$emp_hierarchy_condition." ".$login_condition." 
							OR ( r.emp_code = em.emp_code AND c1.route_code = r.route_code AND em.emp_code ='".$emp_code."') 
							ORDER BY c1.customer_name ASC";*/
			  $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
						c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin,c1.landline_no,c1.owner_name,c1.owner_phone,
						 c1.cust_class,c1.weekly_closing_day,c1.coverage_type,c1.TIN,c1.PAN,c1.minimum_stock,c1.branch_code,c1.visit_day,c1.email 
						 FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." 
						 ORDER BY c1.customer_name ASC";			
			 }
			 else if($emp_code=='C0007')
			 {
				$sqlquery="SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC";
			 }
			 else
			 {
				$sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
						   c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin,c1.landline_no,c1.owner_name,c1.owner_phone,
						 c1.cust_class,c1.weekly_closing_day,c1.coverage_type,c1.TIN,c1.PAN,c1.minimum_stock,c1.branch_code,c1.visit_day,c1.email FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." ORDER BY c1.customer_name ASC"; 
			 }
		 }
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'28';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		$dns_customer_code='';
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp['customer_name'])): ' ')."^";
			$contents  .= (($rowsemp['route_code']!='')?$rowsemp['route_code']: ' ')."^";
			$contents  .= (($rowsemp['emp_code']!='')?$rowsemp['emp_code']: ' ')."^";
			$contents  .= (($rowsemp['current_balance']!='')?$rowsemp['current_balance']: ' ')."^";
			$contents  .= (($rowsemp['credit_limit']!='')?$rowsemp['credit_limit']: ' ')."^";
			$contents  .= (($rowsemp['acedns']!='')?$rowsemp['acedns']: ' ')."^";
			$contents  .= (($rowsemp['black_list']!='')?$rowsemp['black_list']: ' ')."^";
			$contents  .= (($rowsemp['TD']!='')?$rowsemp['TD']: '0')."^";
			$contents  .= (($rowsemp['cust_type']!='')?$rowsemp['cust_type']: ' ')."^";
			$contents  .= (($rowsemp['rds_tag']!='')?$rowsemp['rds_tag']: ' ')."^";
			$contents  .= (($rowsemp['sauda_validity_period']!='')?$rowsemp['sauda_validity_period']: ' ')."^";
			$contents  .= (($rowsemp['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsemp['address'])): ' ')."^";
			$contents  .= (($rowsemp['pin']!='')?$rowsemp['pin']: ' ')."^";
			$contents  .= (($rowsemp['phone_no']!='')?$rowsemp['phone_no']: ' ')."^";
			$contents  .= (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";// For dns customer code forcefully given the original customer code
			$contents  .= (($rowsemp['landline_no']!='')?$rowsemp['landline_no']: ' ')."^";
			$contents  .= (($rowsemp['owner_name']!='')?$rowsemp['owner_name']: ' ')."^";
			$contents  .= (($rowsemp['owner_phone']!='')?$rowsemp['owner_phone']: ' ')."^";
			$contents  .= (($rowsemp['cust_class']!='')?$rowsemp['cust_class']: ' ')."^";
			$contents  .= (($rowsemp['weekly_closing_day']!='')?$rowsemp['weekly_closing_day']: ' ')."^";
			$contents  .= (($rowsemp['coverage_type']!='')?$rowsemp['coverage_type']: ' ')."^";
			$contents  .= (($rowsemp['TIN']!='')?$rowsemp['TIN']: ' ')."^";
			$contents  .= (($rowsemp['PAN']!='')?$rowsemp['PAN']: ' ')."^";
			$contents  .= (($rowsemp['minimum_stock']!='')?$rowsemp['minimum_stock']: ' ')."^";
			$contents  .= (($rowsemp['branch_code']!='')?$rowsemp['branch_code']: ' ')."^";
			$contents  .= (($rowsemp['visit_day']!='')?$rowsemp['visit_day']: ' ')."^";
			$contents  .= (($rowsemp['email']!='')?$rowsemp['email']: ' ');

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
			$datacontents = '0'.'¥'.'28';
		}
	}
  }
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-master-audit-txt-incremental-7.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
