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
	$emp_hierarchy_condition='CRR.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CRR.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition=" AND CRR.acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if(strtoupper(substr($emp_code,0,1))=='E')
{
$sqlquery="SELECT  DISTINCT CBR.customer_code,CBR.broker_code,CBR.acedns,CBR.mapped_broker FROM customer_route_emp_relation CRR,customer_broker_relation CBR 
			WHERE ".$emp_hierarchy_condition." AND CBR.customer_code=CRR.customer_code 
			 AND CBR.acedns!='N' ".$login_condition.""; 
}
if(strtoupper(substr($emp_code,0,1))=='B')
{
$sqlquery="SELECT  DISTINCT CBR.customer_code,CBR.broker_code,CBR.acedns,CBR.mapped_broker FROM customer_broker_relation CBR 
			WHERE CBR.broker_code='".$emp_code."' AND CBR.acedns!='N' ".$login_condition.""; 
}
if(strtoupper(substr($emp_code,0,1))=='C')
{
$sqlquery="SELECT  DISTINCT CBR.customer_code,CBR.broker_code,CBR.acedns,CBR.mapped_broker FROM customer_broker_relation CBR 
			WHERE CBR.customer_code='".$emp_code."' AND CBR.acedns!='N' ".$login_condition.""; 
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'4';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcustomerrds = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomerrds['customer_code']!='')?$rowcustomerrds['customer_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['broker_code']!='')?$rowcustomerrds['broker_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['acedns']!='')?$rowcustomerrds['acedns']: ' ')."^";
			$contents  .= (($rowcustomerrds['mapped_broker']!='')?$rowcustomerrds['mapped_broker']: ' ');
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
			$datacontents = '0'.'¥'.'4';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-broker-relational-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-branch-relational-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_broker_relation.txt");
	print "$datacontents"; 		
?>
