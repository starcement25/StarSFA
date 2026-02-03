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

if(sale=='yes')
{
	$sqlemp="SELECT EM.emp_code FROM employee_master EM,branch_master BM WHERE 
				EM.branch_code=BM.branch_code AND EM.emp_code='".$emp_code."'";
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
}

/*$sqlrds="SELECT rds_code FROM rds_master RM WHERE emp_code='".$emp_code."'";
$result = mysqli_query($link,$sqlrds);
$rowrds = mysqli_fetch_assoc($result);
$rds_code=$rowrds['rds_code'];*/
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND GIT.receiver_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND GIT.receiver_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND GIT.transaction_type='ST' AND UNIX_TIMESTAMP(GIT.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

 if($emp_code!='C0007'){
 	$sqlquery="SELECT GIT.grn_no,GIT.despatcher_code,GIT.prod_code,GIT.despatch_qty,
 			GIT.rec_qty,GIT.status,GIT.transaction_type,GIT.order_no,GIT.sale_rate 
			FROM goods_in_transit GIT WHERE 1 ".$emp_hierarchy_condition.$login_condition."";
 }
 else
 {
	$sqlquery="SELECT GIT.grn_no,GIT.despatcher_code,GIT.prod_code,GIT.despatch_qty,
 			GIT.rec_qty,GIT.status,GIT.transaction_type,GIT.order_no,GIT.sale_rate FROM goods_in_transit GIT WHERE 1 ".$login_condition."";
 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'9';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowgit = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowgit['grn_no']!='')?$rowgit['grn_no']: ' ')."^";
			$contents  .= (($rowgit['despatcher_code']!='')?$rowgit['despatcher_code']: ' ')."^";
			$contents  .= (($rowgit['prod_code']!='')?$rowgit['prod_code']: ' ')."^";
			$contents  .= (($rowgit['despatch_qty']!='')?$rowgit['despatch_qty']: ' ')."^";
			$contents  .= (($rowgit['rec_qty']!='')?$rowgit['rec_qty']: ' ')."^";
			$contents  .= (($rowgit['status']!='')?$rowgit['status']: ' ')."^";
			$contents  .= (($rowgit['order_no']!='')?$rowgit['order_no']: ' ')."^";
			$contents  .= (($rowgit['transaction_type']!='')?$rowgit['transaction_type']: ' ')."^";
			$contents  .= (($rowgit['sale_rate']!='')?$rowgit['sale_rate']: ' ');
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
			$datacontents = '0'.'¥'.'8';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/git-master-txt-incremental-5.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/git-master-txt-incremental-5.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=git_master.txt");
	print "$datacontents"; 		
?>
