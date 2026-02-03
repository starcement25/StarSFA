<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
$rsbranch=mysqli_query($link,$sqlbranch);
$rowbranch=mysqli_fetch_assoc($rsbranch);
$branch_code=$rowbranch['branch_code'];

if($branch_code !='')
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	/*$sqlquery="SELECT * FROM (SELECT PDF_file_name,branch_code,acedns,start_date,end_date FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
			ORDER BY download_time DESC) AS SAT GROUP BY 2 ORDER BY 2 ASC ";*/
	if($incremental_download=='no')
	{
		$login_condition=" AND acedns='Y'";
	}
	else
	{
		$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	}
	/*$sqlquery="SELECT PDF_file_name,branch_code,acedns,start_date,end_date FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch_code."') ".$login_condition." ORDER BY download_time DESC ";	*/
	// $sqlquery="SELECT * FROM (SELECT PDF_file_name,branch_code,acedns,start_date,end_date FROM branch_schemes_PDF WHERE 
	// 		FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
	// 		ORDER BY download_time DESC) AS SAT GROUP BY 2,1 ORDER BY 2 ASC ";	
			
	$sqlquery = "SELECT PDF_file_name,branch_code,ANY_VALUE(acedns) AS acedns,ANY_VALUE(start_date) AS start_date,ANY_VALUE(end_date) AS end_date FROM (
    SELECT PDF_file_name, branch_code, acedns, start_date, end_date FROM branch_schemes_PDF WHERE FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."') ORDER BY download_time DESC) AS SAT GROUP BY branch_code, PDF_file_name ORDER BY branch_code ASC";//21-11-2025 new added
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowschemePDF = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowschemePDF['branch_code']!='')?$rowschemePDF['branch_code']: ' ')."^";
			$contents  .= (($rowschemePDF['PDF_file_name']!='')?$rowschemePDF['PDF_file_name']: ' ')."^";
			$contents  .= (($rowschemePDF['acedns']!='')?$rowschemePDF['acedns']: ' ')."^";
			$contents  .= (($rowschemePDF['start_date']!='')?$rowschemePDF['start_date']: ' ')."^";
			$contents  .= (($rowschemePDF['end_date']!='')?$rowschemePDF['end_date']: ' ');
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'5';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$datacontents = '0'.'¥'.'0';
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/branchwise-scheme-download-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=branch_scheme_PDF.txt");
print "$datacontents"; 		
?>
