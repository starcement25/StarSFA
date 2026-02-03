<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$incremental_download=$_REQUEST['incremental_download'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='CRER.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CRER.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition=" AND c1.acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(c1.download_time_credit_limit) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);


 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
 $sqlquery="SELECT DISTINCT c1.customer_code,c1.credit_limit
			FROM customer_master c1,route_master r, employee_master em,customer_route_emp_relation CRER 
			WHERE ".$emp_hierarchy_condition." ".$login_condition." AND CRER.customer_code=c1.customer_code
			OR ( r.emp_code = em.emp_code AND CRER.route_code = r.route_code AND em.emp_code ='".$emp_code."') 
			ORDER BY c1.customer_name ASC";
 }
 else if($countbranches>1 && vertical_fields=='no' && $emp_code!='C0007')
 {
	 $sqlquery="SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,employee_master em,customer_route_emp_relation CRER 
  				WHERE ".$emp_hierarchy_condition." ".$login_condition." AND CRER.customer_code=c1.customer_code OR c1.branch_code=em.branch_code 
				ORDER BY c1.customer_name ASC"; 
 }
 else if($emp_code=='C0007')
 {
	$sqlquery="SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,customer_route_emp_relation CRER 
			  WHERE CRER.customer_code=c1.customer_code  ".$login_condition." ORDER BY c1.customer_name ASC";
 }
 else
 {
 	$sqlquery="SELECT DISTINCT c1.customer_code,c1.credit_limit FROM customer_master c1,customer_route_emp_relation CRER 
			  WHERE CRER.customer_code=c1.customer_code  ".$login_condition." ORDER BY c1.customer_name ASC"; 
 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'2';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['credit_limit']!='')?$rowsemp['credit_limit']: ' ');
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
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-master-credit-limit-incremental-5.0.6.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-master-credit-limit-incremental-5.0.6.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_master_credit_limit.txt");
	print "$datacontents"; 		
?>
