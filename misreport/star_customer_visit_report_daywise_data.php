<?php
ob_start();
session_start();
// check error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$purpose_of_visit = "";

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
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
	
	$sqlcheckinout="SELECT customer_code,check_in_time,check_out_time,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') AS visit_date,SUBSTRING(`trans_id`,3,5) as emp_code 
					FROM check_in_out_details WHERE (SUBSTRING(`trans_id`,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(`trans_id`,3,5) IN(".$employee.")";
					// echo $sqlcheckinout;die;
	$rscheckinout=mysqli_query($link,$sqlcheckinout);	
	while($rowcheckinout=mysqli_fetch_assoc($rscheckinout))
	{
		$visit_date_check_in_out=$rowcheckinout['visit_date'];
		$emp_code_check_in_out=$rowcheckinout['emp_code'];
		$customer_code_check_in_out=$rowcheckinout['customer_code'];
		${'check_in_time'.$customer_code_check_in_out.$visit_date_check_in_out.$emp_code_check_in_out}=date('H:i:s',strtotime($rowcheckinout['check_in_time']));
		${'check_out_time'.$customer_code_check_in_out.$visit_date_check_in_out.$emp_code_check_in_out}=date('H:i:s',strtotime($rowcheckinout['check_out_time']));
	}
	
	$sql_office_plant = "SELECT customer_code FROM office_plant_master";
	
	$res_office_plant = mysqli_query($link,$sql_office_plant);
	$office_plant_string='';
				

	while($row_office_plant = mysqli_fetch_assoc($res_office_plant))
	{
		$office_plant_code = $row_office_plant['customer_code'];
		$office_plant_string=$office_plant_string."'".$office_plant_code."'".',';	
	}
	
	// echo "test".$sql_office_plant;die;
	$office_plant_string=substr($office_plant_string,0,-1);
if($office_plant_string==""){
    $office_plant_string="''";
}

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

	$count = 1;
	/*$emp_array = array();
	$sql_customer = "SELECT DISTINCT emp_code FROM customer_visit_details WHERE emp_code IN(".$employee.") AND SUBSTRING(trans_id,-14,6) = '".str_replace("-","",$month_data)."' ORDER BY emp_code, SUBSTRING(trans_id,-14,8) ASC";
	$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);*/
	// $sql_customer = "SELECT * FROM(SELECT DISTINCT CVD.emp_code,DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d') AS visit_date,
	// 				SUBSTRING(CVD.trans_id,-14,14) AS visit_date_parts,CVD.customer_code,CVD.customer_name,CVD.route_name,CVD.route_code,
	// 				CVD.cust_type,EM.emp_name,EM.dns_emp_code,CM.dns_customer_code,BM.branch_name,CVD.trans_id,CVD.hint_remarks, LO.purpose_of_visit
	// 				FROM customer_visit_details CVD,employee_master EM,customer_master CM,branch_master BM, location LO
	// 				WHERE CVD.emp_code IN(".$employee.") AND LO.trans_id =CVD.trans_id AND (SUBSTRING(CVD.trans_id,-14,8) BETWEEN 
	// 			'".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
	// 			AND CVD.customer_code=CM.customer_code AND CM.branch_code=BM.branch_code AND CVD.emp_code=EM.emp_code AND CM.dns_customer_code NOT IN(".$office_plant_string.") 
	// 			ORDER BY SUBSTRING(trans_id,-14,14) DESC) AS SAT GROUP BY 1,2,3 ORDER BY SAT.emp_name ASC";

	$sql_customer = "SELECT * FROM(SELECT DISTINCT CVD.emp_code,DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d') AS visit_date,
                SUBSTRING(CVD.trans_id,-14,14) AS visit_date_parts,CVD.customer_code,CVD.customer_name,CVD.route_name,CVD.route_code,
                CVD.cust_type,EM.emp_name,EM.dns_emp_code,CM.dns_customer_code,BM.branch_name,CVD.trans_id,CVD.hint_remarks, LO.purpose_of_visit
                FROM customer_visit_details CVD
                JOIN employee_master EM ON CVD.emp_code=EM.emp_code
                JOIN customer_master CM ON CVD.customer_code=CM.customer_code
                JOIN branch_master BM ON CM.branch_code=BM.branch_code
                JOIN location LO ON LO.trans_id=CVD.trans_id
                WHERE CVD.emp_code IN(".$employee.") 
                AND (SUBSTRING(CVD.trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
                AND CM.dns_customer_code NOT IN(".$office_plant_string.") 
                ORDER BY SUBSTRING(CVD.trans_id,-14,14) DESC) AS SAT 
                ORDER BY SAT.emp_name ASC";
 	//echo $sql_customer;
	//echo"<pre>";print_r($sql_customer);die;

	$res_customer = mysqli_query($link,$sql_customer);
	$total_customer = mysqli_num_rows($res_customer);
	if($total_customer>0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="14"><?php echo $header_string;  ?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
            <td>Date of Visit</td>
            <td>Customer Code</td>
			<td>Customer Name</td>
			<td>Route</td>
            <td>Type</td>
            <td>Branch</td>
            <td>Employee Code</td>
			<td>Employee Name</td>
			<!--td>Visit Time</td-->
            <td>Check In Time</td>
            <td>Check Out Time</td>
            <td>Duration</td>
			<td>Visit Status(Productive / Non productive)</td>
			<td>Remarks</td>
			<td>Purpose Of Visit</td>
		  </tr>
		<?php
		// $res_customer = mysqli_query($link,$sql_customer);
		$customer_code_array=array();
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$dns_emp_code = $row_customer['dns_emp_code'];
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
			$visit_date_parts = $row_customer['visit_date_parts'];
			$visit_date = date('d-m-Y',strtotime(substr($trans_id,-14,8)));
			$visit_time = date('H:i:s',strtotime(substr($trans_id,-6,6)));
			$visit_date_formated=$row_customer['visit_date'];
			$remark = $row_customer['hint_remarks'];
			$purpose_of_visit = $row_customer['purpose_of_visit'];
			$transid_substr = substr($trans_id,0,2);
			$transid_substr_first_string=substr($trans_id,0,1);
			if($transid_substr == "OE" || $transid_substr == "SE" || $transid_substr == "PE" ){
				$visit_status='Productive';
			}
			else if( $transid_substr == "NS" || $transid_substr == "NO" || $transid_substr == "NC"){
				$visit_status='Non Productive';
			}
			if($transid_substr_first_string=='M'){
				$visit_status='Productive';
			}
			
			
			/*$sqlLoc = "SELECT * FROM `location` WHERE `trans_id`='$trans_id'";
			$resLoc = mysqli_query($link,$sqlLoc);
            $totresLoc = mysqli_num_rows($resLoc);
            if($totresLoc>0){
	            while($rowLoc=mysqli_fetch_assoc($resLoc)){
	    	        $purpose_of_visit = $rowLoc['purpose_of_visit'];
	    	
	    	
	            }
            }*/
			/*$sqlchkinouttime="SELECT trans_id  FROM `location` WHERE `trans_id` like 'CI%' AND SUBSTRING(`trans_id`,-14,14) > '".$visit_date_parts."' 
							AND emp_code='".$emp_code."' ORDER BY date ASC LIMIT 0,1";
			$rschkinouttime=mysqli_query($link,$sqlchkinouttime);
			$rowchkinouttime=mysqli_fetch_assoc($rschkinouttime);
			$trans_id_checkinout=$rowchkinouttime['trans_id'];
			
			//$sqlcheckinout="SELECT check_in_time,check_out_time FROM check_in_out_details WHERE trans_id='".$trans_id_checkinout."'";
			$sqlcheckinout="SELECT check_in_time,check_out_time FROM check_in_out_details WHERE SUBSTRING(`trans_id`,-14,14) > '".$visit_date_parts."'
							AND SUBSTRING(`trans_id`,3,5)='".$emp_code."'"; 
			$rscheckinout=mysqli_query($link,$sqlcheckinout) or die(mysqli_error()." Error in select check in out details ".$sqlcheckinout);
			$countcheckinout=mysqli_num_rows($rscheckinout);
			if($countcheckinout >0)
			{*/
			if(${'check_in_time'.$customer_code.$visit_date_formated.$emp_code}!=''){
			
				$time_difference_final=0;
				$cntcheckinout=1;
				//$rowcheckinout=mysqli_fetch_assoc($rscheckinout);
					$check_in_time=${'check_in_time'.$customer_code.$visit_date_formated.$emp_code};
					$check_out_time=${'check_out_time'.$customer_code.$visit_date_formated.$emp_code};
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
			$customer_chk_str=$visit_date.$dns_customer_code.$emp_code;
			
			if(!in_array($customer_chk_str,$customer_code_array)){
			echo "<tr>
					<td>".$count."</td>
					<td>".$visit_date."</td>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$route_name."</td>
					<td>".$cust_type."</td>
					<td>".$branch_name."</td>
					<td>".$dns_emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$check_in_time."</td>
					<td>".$check_out_time."</td>
					<td>".$time_duration."</td>
					<td>".$visit_status."</td>
					<td>".$remark."</td>
					<td>".$purpose_of_visit."</td>
				  </tr>";
			
			$count++;
			array_push($customer_code_array,$customer_chk_str);
			}
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
