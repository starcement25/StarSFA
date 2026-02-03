<?php

ob_start();

session_start();

require("adminUtils.php");



$employee = $_REQUEST['employee'];

$employee_arg = str_replace("#",",",$employee);

$employee_arg = str_replace("^","'",$employee_arg);



$zone = $_REQUEST['zone'];

$state = $_REQUEST['state'];

$branch = $_REQUEST['branch'];

$department = $_REQUEST['department'];

$start_date = $_REQUEST['start_date'];

$end_date = $_REQUEST['end_date'];



if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);

else								$zone = "All";



if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);

else								$state = "All";



if(strpos($branch,",") == FALSE)	$branch = str_replace("'","",$branch);

else								$branch = "All";



if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);

else									$department = "All";





if(strpos($employee,",") == FALSE){

	$new_emp_code = str_replace("'","",$employee);

	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";

	$res_emp_name = mysql_query($sql_emp_name);

	$row_emp_name = mysql_fetch_array($res_emp_name);

	$new_emp_name = $row_emp_name['emp_name'];

}

else{

	$new_emp_name = "All";

}

$sql_office_plant = "SELECT customer_code FROM office_plant_master";
	$res_office_plant = mysql_query($sql_office_plant);
	$office_plant_string='';
	while($row_office_plant = mysql_fetch_array($res_office_plant))
	{
		$office_plant_code = $row_office_plant['customer_code'];
		$office_plant_string=$office_plant_string."'".$office_plant_code."'".',';	
	}
	$office_plant_string=substr($office_plant_string,0,-1);

	$header = "Date of Visit"."\t"."Customer Code"."\t"."Customer Name"."\t"."Route"."\t"."Type"."\t"."Branch"."\t"."Employee Name"."\t"."Check In Time"."\t"."Check Out Time"."\t"."Duration"."\t"."Visit Status(Productive/Non productive)"."\t"."Remarks";



	$count = 1;

	/*$emp_array = array();

	$sql_customer = "SELECT DISTINCT emp_code FROM customer_visit_details WHERE emp_code IN(".$employee.") AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";

	$res_customer = mysql_query($sql_customer);

	$total_customer = mysql_num_rows($res_customer);*/

	
	$customer_code_array=array();
	$sql_customer = "SELECT * FROM(SELECT DISTINCT CVD.emp_code,DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d') AS visit_date,SUBSTRING(CVD.trans_id,-14,14) AS visit_date_parts,CVD.customer_code,CVD.customer_name,CVD.route_name,CVD.route_code,

					CVD.cust_type,EM.emp_name,CM.dns_customer_code,BM.branch_name,CVD.trans_id,CVD.hint_remarks 

					FROM customer_visit_details CVD,employee_master EM,customer_master CM,branch_master BM

					WHERE CVD.emp_code IN(".$employee.") AND (SUBSTRING(CVD.trans_id,-14,8) BETWEEN 

				'".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 

				AND CVD.customer_code=CM.customer_code AND CM.branch_code=BM.branch_code AND CVD.emp_code=EM.emp_code AND CM.dns_customer_code NOT IN(".$office_plant_string.") 

				ORDER BY SUBSTRING(trans_id,-14,14) DESC) AS SAT GROUP BY 1,2,3 ORDER BY SAT.emp_name ASC";

	$res_customer = mysql_query($sql_customer);

	$total_customer = mysql_num_rows($res_customer);

	if($total_customer>0){

		$count = 1;

		while($row_customer = mysql_fetch_array($res_customer)){

			$emp_code = $row_customer['emp_code'];

			$emp_name = $row_customer['emp_name'];

			$customer_code = $row_customer['customer_code'];

			$customer_name = $row_customer['customer_name'];

			$dns_customer_code = $row_customer['dns_customer_code'];

			$route_name = $row_customer['route_name'];

			$route_code = $row_customer['route_code'];

			$cust_type = $row_customer['cust_type'];

			$branch_code = $row_customer['branch_code'];

			$branch_name = $row_customer['branch_name'];

			$trans_id = $row_customer['trans_id'];

			$visit_date = date('d-m-Y',strtotime(substr($trans_id,-14,8)));

			$visit_time = date('H:i:s',strtotime(substr($trans_id,-6,6)));

			$remark = $row_customer['hint_remarks'];

			$transid_substr = substr($trans_id,0,2);

			$transid_substr_first_string=substr($trans_id,0,1);
			$visit_date_parts = $row_customer['visit_date_parts'];
						$sqlchkinouttime="SELECT trans_id  FROM `location` WHERE `trans_id` like 'CI%' AND SUBSTRING(`trans_id`,-14,14) > '".$visit_date_parts."' 
							AND emp_code='".$emp_code."' ORDER BY date ASC LIMIT 0,1";
			$rschkinouttime=mysql_query($sqlchkinouttime);
			$rowchkinouttime=mysql_fetch_array($rschkinouttime);
			$trans_id_checkinout=$rowchkinouttime['trans_id'];
			
			$sqlcheckinout="SELECT check_in_time,check_out_time FROM check_in_out_details WHERE trans_id='".$trans_id_checkinout."'";
			$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select check in out details ".$sqlcheckinout);
			$countcheckinout=mysql_num_rows($rscheckinout);
			if($countcheckinout >0)
			{
				$time_difference_final=0;
				$cntcheckinout=1;
				$rowcheckinout=mysql_fetch_array($rscheckinout);
					$check_in_time=date('H:i:s',strtotime($rowcheckinout['check_in_time']));
					$check_out_time=date('H:i:s',strtotime($rowcheckinout['check_out_time']));
					$time_difference=strtotime($check_out_time)-strtotime($check_in_time);
					$time_difference_final=$time_difference;
				if($time_difference_final >=3600)
				{
					$hours = floor($time_difference_final / 3600);
					$minutes = floor(($time_difference_final / 60) % 60);
					$seconds = $time_difference_final % 60;
					$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
				}
				else if($time_difference_final >=60 && $time_difference_final<3600)
				{
					$minutes = floor(($time_difference_final / 60) % 60);
					$seconds = $time_difference_final % 60;
					$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
				}
				else
				{
					$seconds = $time_difference_final % 60;
					$time_duration=$seconds.' Second(s)';
				}
			}
			else
			{
				$time_duration='';
				$time_difference_final='';
			}

			if($transid_substr == "OE" || $transid_substr == "SE" || $transid_substr == "PE" ){

				$visit_status='Productive';

			}

			else if( $transid_substr == "NS" || $transid_substr == "NO" || $transid_substr == "NC"){

				$visit_status='Non Productive';

			}

			if($transid_substr_first_string=='M'){

				$visit_status='Productive';

			}
			$customer_chk_str=$visit_date.$dns_customer_code.$emp_code;
			if(!in_array($customer_chk_str,$customer_code_array)){
			$table_data .= $visit_date."\t".$dns_customer_code."\t".$customer_name."\t".$route_name."\t".$cust_type."\t".$branch_name."\t".$emp_name."\t".$check_in_time."\t".$check_out_time."\t".$time_duration."\t".$visit_status."\t".$remark."\n";

			$count++;
			array_push($customer_code_array,$customer_chk_str);
			}

		}

	}

	if($table_data !=''){	

		header("Content-Type: application/force-download");

		header("Content-Type: application/octet-stream");

		header("Content-Type: application/download");

		header("Content-Disposition: attachment; filename=Customer_Visit_data_daywise.xls"); 

		header("Content-Transfer-Encoding: binary");

		header("Pragma: no-cache"); 

		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 

		echo ucwords($header)."\n".$table_data;

	}

	else

	{

		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";

	}

?>

