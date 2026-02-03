<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CM.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PSCM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

	 if($countbranches>1 && vertical_fields=='yes' && $emp_code!='C0007'){
	 $sqlquery="SELECT PSCM.customer_code,PSCM.product_code,PSCM.visit_1,PSCM.visit_2,PSCM.visit_3 
	 			FROM prev_stock_counting_master PSCM,customer_master CM,route_master RM WHERE ".$emp_hierarchy_condition." ".$login_condition." 
				AND CM.customer_code=PSCM.customer_code OR (CM.route_code = RM.route_code AND RM.emp_code ='".$emp_code."')";
	 }
	 else if($emp_code=='C0007')
	 {
			$sqlquery="SELECT DISTINCT PSCM.* FROM prev_stock_counting_master PSCM WHERE 1 ".$login_condition."";
	 }
	 else
	 {
	  $sqlquery="SELECT PSCM.customer_code,PSCM.product_code,PSCM.visit_1,PSCM.visit_2,PSCM.visit_3 
	 			FROM prev_stock_counting_master PSCM,customer_master CM WHERE ".$emp_hierarchy_condition." ".$login_condition." 
				AND CM.customer_code=PSCM.customer_code"; 
	 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'3';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$visit_1=$rowsemp['visit_1'];
			$visit_2=$rowsemp['visit_2'];
			$visit_3=$rowsemp['visit_3'];
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['product_code']!='')?$rowsemp['product_code']: ' ')."^";
			
			if($visit_1 >0 && $visit_2 >0 && $visit_3 >0)
			{
				$contents  .=$visit_1.','.$visit_2.','.$visit_3;
			}
			else
			{
				if($visit_3 >0 && $visit_1==0 && $visit_2==0)
				{
					$contents  .=$visit_3.','.$visit_1.','.$visit_2;
				}
				else if($visit_3 >0 && $visit_1==0 && $visit_2 >0)
				{
					$contents  .=$visit_2.','.$visit_3.','.$visit_1;
				}
			}
			
			
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/prev-stock-counting-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/prev-stock-counting-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=prev_stock_counting_master.txt");
	print "$datacontents"; 		
?>
