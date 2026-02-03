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
		$customer_type_condition=" AND c1.cust_type IN('R','D')";
	}
}
else
{
	$customer_type_condition='';
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

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
					c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." ORDER BY c1.customer_name ASC";			
		 }
		 else if($emp_code=='C0007')
		 {
			$sqlquery="SELECT DISTINCT c1.* FROM customer_master c1 WHERE 1  ".$login_condition." ORDER BY c1.customer_name ASC";
		 }
		 else
		 {
		    $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
					   c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin FROM customer_master c1 WHERE ".$emp_hierarchy_condition." ".$login_condition." ".$customer_type_condition." ORDER BY c1.customer_name ASC"; 
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

		$dns_customer_code='';
		$customer_code_array=array();
		$countdata=0;
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			if($nick_name=='EMAMI' || $nick_name=='EMAMIT'){
				$rds_tag=$rowsemp['rds_tag'];
				$cust_type=$rowsemp['cust_type'];
				$customer_code=$rowsemp['customer_code'];
				
				if(!in_array($customer_code,$customer_code_array))
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
					$contents  .= (($rowsemp['address']!='')?$rowsemp['address']: ' ')."^";
					$contents  .= (($rowsemp['pin']!='')?$rowsemp['pin']: ' ')."^";
					$contents  .= (($rowsemp['phone_no']!='')?$rowsemp['phone_no']: ' ')."^";
					$contents  .= (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ');// For dns customer code forcefully given the original customer code		
					array_push($customer_code_array,$customer_code);
					$countdata++;
				}
				if($cust_type=='R' && $rds_tag!='')
				{
				  $selrdsdata="SELECT customer_code, customer_name,route_code,emp_code,current_balance,credit_limit,black_list,acedns,
					   			TD,cust_type,sauda_validity_period,address,phone_no,pin FROM customer_master WHERE customer_code='".$rds_tag."'";
				  $rsrdsdata=mysqli_query($link,$selrdsdata);
				  $rowrdsdata=mysqli_fetch_assoc($rsrdsdata);
				  $customer_code_rds=$rowrdsdata['customer_code'];
				  
				  	if(!in_array($customer_code_rds,$customer_code_array))
					{
						$contents  .="\n"; 
						$contents  .= (($rowrdsdata['customer_code']!='')?$rowrdsdata['customer_code']: ' ')."^";
						$contents  .= (($rowrdsdata['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowrdsdata['customer_name'])): ' ')."^";
						$contents  .= (($rowrdsdata['route_code']!='')?$rowrdsdata['route_code']: ' ')."^";
						$contents  .= (($rowrdsdata['emp_code']!='')?$rowrdsdata['emp_code']: ' ')."^";
						$contents  .= (($rowrdsdata['current_balance']!='')?$rowrdsdata['current_balance']: ' ')."^";
						$contents  .= (($rowrdsdata['credit_limit']!='')?$rowrdsdata['credit_limit']: ' ')."^";
						$contents  .= (($rowrdsdata['acedns']!='')?$rowrdsdata['acedns']: ' ')."^";
						$contents  .= (($rowrdsdata['black_list']!='')?$rowrdsdata['black_list']: ' ')."^";
						$contents  .= (($rowrdsdata['TD']!='')?$rowrdsdata['TD']: '0')."^";
						$contents  .= (($rowrdsdata['cust_type']!='')?$rowrdsdata['cust_type']: ' ')."^";
						$contents  .= (($rowrdsdata['rds_tag']!='')?$rowrdsdata['rds_tag']: ' ')."^";
						$contents  .= (($rowrdsdata['sauda_validity_period']!='')?$rowrdsdata['sauda_validity_period']: ' ')."^";
						$contents  .= (($rowrdsdata['address']!='')?$rowrdsdata['address']: ' ')."^";
						$contents  .= (($rowrdsdata['pin']!='')?$rowrdsdata['pin']: ' ')."^";
						$contents  .= (($rowrdsdata['phone_no']!='')?$rowrdsdata['phone_no']: ' ')."^";
						$contents  .= (($rowrdsdata['customer_code']!='')?$rowrdsdata['customer_code']: ' ');// For dns customer code forcefully given the original customer 
						array_push($customer_code_array,$customer_code_rds);
						$countdata++;
					}
				}
				
				$linecontents  .= $contents."\n";
			}
			else
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
				$contents  .= (($rowsemp['address']!='')?$rowsemp['address']: ' ')."^";
				$contents  .= (($rowsemp['pin']!='')?$rowsemp['pin']: ' ')."^";
				$contents  .= (($rowsemp['phone_no']!='')?$rowsemp['phone_no']: ' ')."^";
				$contents  .= (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ');// For dns customer code forcefully given the original customer code
				$linecontents  .= $contents."\n";
				$countdata++;
			}
		}
		$contentsrowcolumn=$countdata.'¥'.'16';
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
			$datacontents = '0'.'¥'.'16';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
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
?>
