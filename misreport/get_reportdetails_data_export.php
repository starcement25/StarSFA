<?php
ob_start();
session_start();
require("adminUtils.php");

$empcode = $_REQUEST['empcode'];
$sdate = $_REQUEST['sdate'];

	$header = "Customer Name"."\t"."State"."\t"."CP Name"."\t"."Beat Name"."\t"."CRE Emp Id"."\t"."Call Duration"."\t"."Remarks"."\t"."Phone no".
	"\t"."Employee Name";
	$sqlqueryemp="SELECT customer_code,call_duration,recorded_file,call_id,response_type FROM `CRM_transaction` WHERE SUBSTRING(call_id,4,5)='".$empcode."' AND SUBSTRING(call_id,-14,8)='".$sdate."'";
	$resultemp = mysqli_query($link,$sqlqueryemp);
	$countemp=mysqli_num_rows($resultemp);
	if($countemp>0){
		while($row_emp = mysqli_fetch_assoc($resultemp)){
			$cust_code = $row_emp['customer_code'];
			$custname=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name,state_code,rds_tag,phone_no FROM customer_master WHERE customer_code='".$cust_code."'"));
			$cpname=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name FROM customer_master WHERE customer_code='".$custname['rds_tag']."'"));
			$routname=mysqli_fetch_assoc(mysqli_query($link,"SELECT RM.route_name,RM.route_code FROM `customer_route_emp_relation` CRE,route_master RM WHERE CRE.route_code=RM.route_code AND CRE.customer_code='".$cust_code."'"));
			
			$sqlcpemp="SELECT emp_name FROM employee_master WHERE emp_code=(SELECT DISTINCT emp_code FROM distributor_route_relation 
								WHERE distributor_code='".$custname['rds_tag']."' AND route_code='".$routname['route_code']."')";
			$rscpemp=mysqli_query($link,$sqlcpemp);
			$rowcpemp=mysqli_fetch_assoc($rscpemp);
			$cpempname=$rowcpemp['emp_name'];					
			$sql_remarks="SELECT GROUP_CONCAT(remarks SEPARATOR '#') as remarkes FROM `CRM_customer_feedback` WHERE call_id='".$row_emp['call_id']."'";
			$row_remarkes=mysqli_query($link,$sql_remarks);
			$res_remarkes=mysqli_fetch_assoc($row_remarkes);
			$remarks=explode('#',$res_remarkes['remarkes']);
			
			$finalremarks=(($remarks[0])?'1.'.preg_replace('/[\r\n]+/', '',$remarks[0]):'').
					(($remarks[1])?'2.'.preg_replace('/[\r\n]+/', '',$remarks[1]):'').
					(($remarks[2])?'3.'.preg_replace('/[\r\n]+/', '',$remarks[2]):'').
					(($remarks[3])?'4.'.preg_replace('/[\r\n]+/', '',$remarks[3]):'').
					(($remarks[4])?'5.'.preg_replace('/[\r\n]+/', '',$remarks[4]):'').
					(($remarks[5])?'6.'.preg_replace('/[\r\n]+/', '',$remarks[5]):'').
					(($remarks[6])?'7.'.preg_replace('/[\r\n]+/', '',$remarks[6]):'').
					(($remarks[7])?'8.'.preg_replace('/[\r\n]+/', '',$remarks[7]):'');
			if($finalremarks=='' && $row_emp['response_type']=='Wrong No'){
				$finalremarks=$row_emp['response_type'];
			}
			
			$call_duration = $row_emp['call_duration'];
			$recorded_file=$row_emp['recorded_file'];
			$file = $row_emp['recorded_file'];
			$table_data .=$custname['customer_name']."\t".$custname['state_code']."\t".$cpname['customer_name']."\t".$routname['route_name']."\t".$empcode."\t".$call_duration."\t".
					$finalremarks."\t".$custname['phone_no']."\t".$cpempname."\n";
			}
		}
		preg_replace('/[\r\n]+/', '',$rowroute['route_name']);
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$contentsdatetime =$year.$month.$date.$hour.$minute.$second;
	
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=CRMReport_$empcode$contentsdatetime.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
	echo ucwords($header)."\n".$table_data;
	?>
    