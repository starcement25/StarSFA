<?php
date_default_timezone_set('Asia/Kolkata');
//ob_start();
session_start();
// check error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (strpos(strtolower($_SESSION['sale_access']), 'vendor') != false && (strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START')) {
	require("adminUtils_branding.php");
} else {
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$site_status = $_REQUEST['site_status'] ?? '';
// echo $start_date;die;
if (strpos($zone, ",") == FALSE)	$zone = str_replace("'", "", $zone);
else								$zone = "All";

if (strpos($state, ",") == FALSE)	$state = str_replace("'", "", $state);
else								$state = "All";

if (strpos($branch, ",") == FALSE)	$branch = str_replace("'", "", $branch);
else								$branch = "All";

if (strpos($department, ",") == FALSE)	$department = str_replace("'", "", $department);
else									$department = "All";

// $employee = $_REQUEST['employee'];

// $employee_arg = str_replace(",", "#", $employee);
// $employee_arg = str_replace("'", "^", $employee_arg);
//$survey_type = $_REQUEST['survey_type'];

if (isset($_REQUEST['use_session_emp']) && $_REQUEST['use_session_emp'] == '1') {
	if (isset($_SESSION['temp_employee_list'])) {
		$employee = $_SESSION['temp_employee_list'];

		unset($_SESSION['temp_employee_list']);
	} else {
		echo "<table border='1'><tr><td colspan='50' style='color:red;'>Error: Employee data not found in session. Please try again.</td></tr></table>";
		exit;
	}
} else {

	$employee = $_REQUEST['employee'] ?? '';
	if (empty($employee)) {
		echo "<table border='1'><tr><td colspan='50' style='color:red;'>Error: No employee data provided.</td></tr></table>";
		exit;
	}
}
$emp_array = $employee;
if (strpos($emp_array, ",") == FALSE)	$emp_array = str_replace("'", "", $emp_array);
else	$emp_array = "All";

function getReverseGeoAdd($latitude, $longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data, true);

	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if (is_array($jsondata) && $jsondata['status'] == 'OK') {
		$addr = $jsondata['results']['0']['formatted_address'];
	}
	return  $addr;
}


$header_string = "Zone:" . $zone . "&nbsp;&nbsp;State:" . $state . "&nbsp;&nbsp;Branch:" . $branch . "&nbsp;&nbsp;Department:" . $department . "&nbsp;&nbsp;Employee:" . $emp_array . "&nbsp;&nbsp;Site Status:" . $site_status . "&nbsp;&nbsp;From:" . date('d-m-Y', strtotime($start_date)) . "&nbsp;&nbsp;To:" . date('d-m-Y', strtotime($end_date));

// $sql_table_view = "SELECT value FROM table_view WHERE row_id = 'RA205' and type='checkbox'";
// $res_table_view = mysqli_query($link,$sql_table_view);

// $rowtableview=mysqli_fetch_assoc($res_table_view);
// $value = $rowtableview['value'];
// $value_parts=explode("/",$value);

// foreach($value_parts as $rowheaderval)
// {
// 	$product_row .= "<td width=\"2%\">$rowheaderval</td>";
// }
// $sql_distinct_date = "SELECT DISTINCT survey_id,value, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date,
// 					DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%H:%i:%s') AS survey_time 
// 						FROM survey_output WHERE 
// 					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
// 					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = 'Site Lead and Conversion Tracking' AND row_id='RA164'  
// 					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";


// $sql_distinct_date = "SELECT 
//     m.*, 
//     v.*
// FROM new_site_lead_visit_master AS v
// LEFT JOIN new_site_lead_master AS m 
//     ON v.new_site_lead_id = m.id
// WHERE m.emp_code IN ($employee)
//   AND DATE(v.created_at) BETWEEN '$start_date' AND '$end_date'
// ORDER BY v.created_at DESC";
// echo $_SESSION['admin_id'];die;
$emp_condition = ($_SESSION['emp_code']  == 'E0658') ? "1=1" : "m.emp_code IN ($employee)";

$sql_distinct_date = "SELECT 
    m.*, 
    v.*,
	m.created_at as creation_date
FROM new_site_lead_visit_master AS v
LEFT JOIN new_site_lead_master AS m 
    ON v.new_site_lead_id = m.id
WHERE $emp_condition
  AND DATE(v.created_at) BETWEEN '$start_date' AND '$end_date'
ORDER BY v.created_at ASC";
// echo $sql_distinct_date;die;
$res_distinct_date = mysqli_query($link, $sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if ($total_rows > 0) {
?>
	<form action="branding_verification_account.php" method="post">
		<table border="1" style="border-collapse:collapse;" class="border" width="90%">
			<tr>
				<td colspan="52" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
			</tr>
		</table>
		<input type="hidden" name="mode" value="PO_no_update" />

		<table border="1" style="border-collapse:collapse;" class="border" width="90%">


			<tr class="TDHEAD">
				<td width="3%">Transaction ID No.</td>
				<td width="3%">Site Unique ID No.</td>
				<td width="3%">Site Status</td>
				<td width="3%">Visit-Date</td>
				<td width="3%">Site Creation Date</td>
				<td width="3%">Employee Code</td>
				<td width="3%">Employee Name</td>
				<td width="3%">Lattitude</td>
				<td width="3%">Longitude</td>
				<td width="3%">Zone</td>
				<td width="3%">Branch</td>
				<td width="3%">District</td>
				<td width="3%">State</td>
				<td width="3%">Customer Name</td>
				<td width="3%">Customer Contact No.</td>
				<td width="3%">Full Address</td>
				<td width="3%">Petty Contractor Regd. In Star Link (Yes/ No)</td>
				<td width="3%">Petty Contractor - Head Mason Name</td>
				<td width="3%">Petty Conttractor- Head Mason Contact No.</td>
				<td width="3%">Engineer Regd. In Star Stellar (Yes/ No)</td>
				<td width="3%">Engineer Name</td>
				<td width="3%">Engineer Contact No.</td>
				<td width="3%">Meeting Person</td>
				<td width="3%">Decision Maker</td>
				<td width="3%">Site Segment</td>
				<td width="3%">VISIT TYPE</td>
				<td width="3%">Project Segment</td>
				<td width="3%">Type of Construction</td>
				<td width="3%">Current Stage of Construction</td>
				<td width="3%">Built Up Area</td>

				<td width="3%">Site Potential (No. of Bags)</td>
				<td width="3%">Consumed Till Date (No. of Bags)</td>
				<td width="3%">Balance Potential (No. of Bags)</td>
				<td width="3%">Site Category</td>
				<td width="3%">Cement Brand Used</td>
				<td width="3%">Price Per Bag (RSP)</td>
				<td width="3%">Conversion (Yes/No/Retention site/Upgrade to Premium)</td>
				<td width="3%">If Yes or Retention site/Upgrade to Premium Select Product</td>
				<td width="3%">Requested Date of Delivery (DD-MM-YYYY)</td>
				<td width="3%">No. of Bags Ordered</td>
				<td width="3%">Counter Type</td>
				<td width="3%">Counter Code</td>
				<td width="3%">Counter Name (Dealer/ RSAR/ SD)</td>
				<td width="3%">If 'No' Reasons for non-conversion</td>
				<td width="3%">Weather Shield Demo</td>
				<td width="3%"> Approval Status</td>
				<td width="3%">Approval/Reject Date and time</td>
				<td width="3%">ASM Name</td>
				<td width="3%">Asm Employee ID</td>
				<td width="3%">Actual Date of Delivery</td>
				<td width="3%">Delivery Remarks</td>
				<td width="3%">Reason For Not Delivery</td>
				<td width="3%">Site Status</td>
			</tr>
		<?php
		$seen_unique_ids = [];
		$site_type = $site_status;
		$res_survey_output = mysqli_query($link, $sql_distinct_date);
		while ($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)) {

			$unique_id = $row_survey_ouput['unique_id'];

			$check_old_visit = "
						SELECT 1
						FROM new_site_lead_visit_master 
						WHERE new_site_lead_id = '".$row_survey_ouput['new_site_lead_id']."'
						AND created_at < '".$row_survey_ouput['created_at']."'
						LIMIT 1
					";

					$res_old_visit = mysqli_query($link, $check_old_visit);

					if(mysqli_num_rows($res_old_visit) > 0){
						$site_status_flag = "existing";
					}else{
						$site_status_flag = "new";
					}
			// if (in_array($unique_id, $seen_unique_ids)) {
			// 	$site_status_flag = "existing";
			// } else {
			// 	$site_status_flag = "new";
			// 	$seen_unique_ids[] = $unique_id;
			// }


			if ($site_type === "new" && $site_status_flag !== "new") {
				continue;
			}

			if ($site_type === "existing" && $site_status_flag !== "existing") {
				continue;
			}


			$transaction_id = $row_survey_ouput['transaction_id'];
			$unique_id = $row_survey_ouput['unique_id'];
			$emp_code = $row_survey_ouput['emp_code'];
$emp_details = "SELECT * FROM employee_master WHERE emp_code = '" . $emp_code . "'";

			$res_emp_details = mysqli_query($link, $emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$emp_dns_code = $row_emp_details['dns_emp_code'];

$asm_emp_code = $row_survey_ouput['asm_id'];
$asm_emp_details = "SELECT * FROM employee_master WHERE emp_code = '" . $asm_emp_code . "'";

			$res_asm_emp_details = mysqli_query($link, $asm_emp_details);
			$row_asm_emp_details = mysqli_fetch_assoc($res_asm_emp_details);
			$asm_emp_dns_code = $row_asm_emp_details['dns_emp_code'];


			$emp_name = $row_survey_ouput['emp_name'];
			$site_created_date = $row_survey_ouput['visit_date'];
			$survey_time = $row_survey_ouput['survey_time'];
			$value = $row_survey_ouput['value'];

			$branch_details = "SELECT * FROM branch_master WHERE branch_code = '" . $row_survey_ouput['branch'] . "'";

			$res_branch_details = mysqli_query($link, $branch_details);
			$row_branch_details = mysqli_fetch_assoc($res_branch_details);
			$branch = $row_branch_details['branch_name'];




			$latt = $row_survey_ouput['latitude'];
			$longi = $row_survey_ouput['longitude'];

			// $sql_survey_status = "SELECT status,status_updated_datetime,actual_date_delivery,delivery_remarks,reason_not_delivery FROM survey_header WHERE survey_id = '" . $survey_id . "'";
			// $res_survey_status = mysqli_query($link, $sql_survey_status);
			// $row_survey_status = mysqli_fetch_assoc($res_survey_status);
			$survey_status = $row_survey_status['status'];
			$actual_date_delivery = $row_survey_status['actual_date_delivery'];
			$delivery_remarks = $row_survey_status['delivery_remarks'];
			$reason_not_delivery = $row_survey_status['reason_not_delivery'];
			if (strtolower($survey_status) != 'pending') {
				$status_updated_datetime = $row_survey_status['status_updated_datetime'];
			} else {
				$status_updated_datetime = '';
			}

			if (!empty($row_survey_ouput['approval_date_time'])) {
				$formattedDate = date("d-m-Y h:i A", strtotime($row_survey_ouput['approval_date_time']));
			} else {
				$formattedDate = "";
			}
/*
$dt = new DateTime($row_survey_ouput['created_at']);
$dtt = new DateTime($site_created_date);
$tzz=$dtt->format('Y-m-d');
$tz = new DateTimeZone('Asia/Kolkata'); // or whatever zone you're after
$dt->setTimezone($tz);
$ist_time = $dt->format('Y-m-d H:i:s');*/
			// $utc = new DateTime($site_created_date, new DateTimeZone('UTC'));
			// $utc->setTimezone(new DateTimeZone('Asia/Kolkata'));
			// $ist_time = $utc->format('d-m-Y h:i A');
//sk add line 05-03-26
// Server timezone (example: UTC)
$server_tz = new DateTimeZone('UTC');  

// Target timezone
$india_tz = new DateTimeZone('Asia/Kolkata');  

// Create datetime with server timezone
$dt = new DateTime($row_survey_ouput['created_at'], $server_tz);
$dt_creation = new DateTime($row_survey_ouput['creation_date'], $server_tz);

// Convert to IST
$dt->setTimezone($india_tz);

// Format output
$ist_time = $dt->format('Y-m-d H:i:s');
$tzz = $dt_creation->format('Y-m-d');

			echo " <tr>
				<td>" . $transaction_id . "</td>
				<td>" . $unique_id . "</td>
				<td>" . ucfirst($site_status_flag) . "</td>
				<td>" . $ist_time . "</td>
				<td>"  . $tzz.  "</td>
				
				<td>" . $emp_dns_code . "</td>
				<td>" . $emp_name . "</td>
				<td>" . $latt . "</td>
				<td>" . $longi . "</td>
				<td>" . $row_survey_ouput['zone'] . "</td>
				<td>" . $branch . "</td>
				<td>" . $row_survey_ouput['district'] . "</td>
				<td>" . $row_survey_ouput['state'] . "</td>
				<td>" . $row_survey_ouput['cust_name'] . "</td>
				<td>" . $row_survey_ouput['cust_phn_no'] . "</td>
				<td>" . $row_survey_ouput['address'] . "</td>
				<td>" . $row_survey_ouput['petty_contractor_registered'] . "</td>
				<td>" . $row_survey_ouput['head_mason_name'] . "</td>
				<td>" . $row_survey_ouput['head_mason_contact'] . "</td>
				<td>" . $row_survey_ouput['engg_registered'] . "</td>
				<td>" . $row_survey_ouput['engg_name'] . "</td>
				<td>" . $row_survey_ouput['engg_contact'] . "</td>
				<td>" . $row_survey_ouput['meeting_person'] . "</td>
				<td>" . $row_survey_ouput['decision_maker'] . "</td>
				<td>" . $row_survey_ouput['site_segment'] . "</td>
				<td>" . $row_survey_ouput['visit_type'] . "</td>
				<td>" . $row_survey_ouput['project_segment'] . "</td>
				<td>" . $row_survey_ouput['type_of_const'] . "</td>
				<td>" . $row_survey_ouput['current_stage_of_construction'] . "</td>
				<td>" . $row_survey_ouput['built_up_area'] . "</td>			
				<td>" . $row_survey_ouput['site_potential'] . "</td>
				<td>" . $row_survey_ouput['consumed_till_date'] . "</td>
				<td>" . $row_survey_ouput['balance_potential'] . "</td>
				<td>" . $row_survey_ouput['site_category'] . "</td>
				<td>" . $row_survey_ouput['brand_used'] . "</td>
				<td>" . $row_survey_ouput['price_per_bag'] . "</td>
				<td>" . $row_survey_ouput['conversion'] . "</td>
				<td>" . $row_survey_ouput['select_product'] . "</td>
				<td>" . $row_survey_ouput['requested_date'] . "</td>
				<td>" . $row_survey_ouput['no_of_bags_ordered'] . "</td>
				<td>" . $row_survey_ouput['counter_type'] . "</td>
				<td>" . $row_survey_ouput['counter_code'] . "</td>
				<td>" . $row_survey_ouput['counter_name'] . "</td>
				<td>" . $row_survey_ouput['reason_for_non_conversion'] . "</td>
				<td>" . $row_survey_ouput['weather_shield_demo'] . "</td>
				<td>" . $row_survey_ouput['approval_status'] . "</td>
				<td>" . $formattedDate . "</td>
				<td>" . $row_survey_ouput['asm_name'] . "</td>
				<td>" . $asm_emp_dns_code . "</td>
				<td>" . $row_survey_ouput['actual_date_of_delivery'] . "</td>
				<td>" . $row_survey_ouput['delivery_remarks'] . "</td>
				<td>" . $row_survey_ouput['reason_for_not_delivery'] . "</td>
				<td>" . $row_survey_ouput['site_status'] . "</td></tr>
				";

			// 	$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '" . $survey_id . "'";
			// 	$res_survey_details = mysqli_query($link, $sql_survey_details);
			// 	$technical_checked_row = '';
			// 	$product_demo = '';
			// 	while ($row_survey_details = mysqli_fetch_assoc($res_survey_details)) {
			// 		$row_id = $row_survey_details['row_id'];
			// 		$survey_value = str_replace('#', ':', $row_survey_details['value']);
			// 		$product_demo = '';
			// 		if ($row_id == 'RA164') {
			// 			$customer_name = $survey_value;
			// 		}
			// 		if ($row_id == 'RA165') {
			// 			$branch_value = $survey_value;
			// 			$sql_branch = "SELECT branch_name FROM branch_master WHERE branch_code = '" . $branch_value . "'";
			// 			$res_branch = mysqli_query($link, $sql_branch);
			// 			$row_branch = mysqli_fetch_assoc($res_branch);
			// 			$branch_name = $row_branch['branch_name'];
			// 		} else if ($row_id == 'RA166') {
			// 			$district  = $survey_value;
			// 		} else if ($row_id == 'RA168') {
			// 			$customer_contact_no  = $survey_value;
			// 		} else if ($row_id == 'RA169') {
			// 			$full_address = $survey_value;
			// 		} else if ($row_id == 'RA170') {
			// 			$head_mason_name = $survey_value;
			// 		} else if ($row_id == 'RA171') {
			// 			$head_mason_no = $survey_value;
			// 		} else if ($row_id == 'RA172') {
			// 			$engineer_name = $survey_value;
			// 		} else if ($row_id == 'RA173') {
			// 			$engineer_no = $survey_value;
			// 		} else if ($row_id == 'RA174') {
			// 			$head_reg_sellar = $survey_value;
			// 		} else if ($row_id == 'RA175') {
			// 			$site_segment = $survey_value;
			// 		} else if ($row_id == 'RA503') {
			// 			$product_demo = $survey_value;
			// 		} else if ($row_id == 'RA176') {
			// 			$visit_type = $survey_value;
			// 			$visit_type_value_parts = explode(':', $visit_type);
			// 			$visit_type_value = $visit_type_value_parts[0];
			// 			$conversion = $visit_type_value_parts[1];
			// 			//$technical_service_value=str_replace(';','',$technical_service_value);
			// 		} else if ($row_id == 'RA411') {
			// 			$select_product = $survey_value;
			// 		} else if ($row_id == 'RA412') {
			// 			$req_date_delivery = $survey_value;
			// 		} else if ($row_id == 'RA413') {
			// 			$no_of_bags = $survey_value;
			// 		} else if ($row_id == 'RA414') {
			// 			$source_purchase_dealer = $survey_value;

			// 			$sql_source_purchase_dealer = "SELECT customer_name, customer_code  FROM customer_master WHERE dns_customer_code = '" . $survey_value . "'";
			// 			$res_source_purchase_dealer = mysqli_query($link, $sql_source_purchase_dealer);
			// 			$row_source_purchase_dealer = mysqli_fetch_assoc($res_source_purchase_dealer);
			// 			$source_purchase_dealer_code = $row_source_purchase_dealer['customer_code'];
			// 			$source_purchase_dealer = $row_source_purchase_dealer['customer_name'];

			// 			//$source_purchase_dealer = $survey_value;

			// 			$source_purchase_dealer_code = $survey_value;
			// 		} else if ($row_id == 'RA415') {
			// 			$reason_non_conversion = $survey_value;
			// 		} else if ($row_id == 'RA416') {
			// 			$other_remarks = $survey_value;
			// 		} else if ($row_id == 'RA177') {
			// 			$project_segment = $survey_value;
			// 		} else if ($row_id == 'RA178') {
			// 			$type_of_construction = $survey_value;
			// 		} else if ($row_id == 'RA179') {
			// 			$site_potential_no_bags = $survey_value;
			// 		} else if ($row_id == 'RA180') {
			// 			$current_stage_construction = $survey_value;
			// 		} else if ($row_id == 'RA181') {
			// 			$cement_brand_used = $survey_value;
			// 		} else if ($row_id == 'RA182') {
			// 			$other_brand = $survey_value;
			// 		} else if ($row_id == 'RA183') {
			// 			$consumed_till_no_bags = $survey_value;
			// 		} else if ($row_id == 'RA184') {
			// 			$estimated_req_no_bags = $survey_value;
			// 		} else if ($row_id == 'RA185') {
			// 			$meeting_person = $survey_value;
			// 		} else if ($row_id == 'RA186') {
			// 			$decision_maker = $survey_value;
			// 		} else if ($row_id == 'RA188') {
			// 			$overall_remarks = $survey_value;
			// 		} else if ($row_id == 'RA417') {
			// 			$approved_by = $survey_value;
			// 			$sql_approved_by = "SELECT dns_emp_code, emp_name  FROM employee_master WHERE emp_code = '" . $approved_by . "'";
			// 			$res_approved_by = mysqli_query($link, $sql_approved_by);
			// 			$row_approved_by = mysqli_fetch_assoc($res_approved_by);
			// 			$approval_emp_code = $row_approved_by['dns_emp_code'];
			// 			$approval_emp_name = $row_approved_by['emp_name'];
			// 		} else if ($row_id == 'RA418') {
			// 			$price_per_bag_rsp = $survey_value;
			// 		}

			// 		/*else if($row_id == 'RA252'){
			// 		$technical_others = $survey_value;
			// 	}
			// 	else if($row_id == 'RA334'){
			// 		$district_survey = $survey_value;
			// 	}
			// 	else if($row_id == 'RA196'){

			// 		if(strpos($survey_value,':')===false){
			// 			$service_category = $survey_value;
			// 			$dhalai_date='';
			// 		}
			// 		else
			// 		{
			// 			$survey_value_parts=explode(':',$survey_value);
			// 			$service_category = $survey_value_parts[0];
			// 			$dhalai_date=$survey_value_parts[1];
			// 		}
			// 	}
			// 	else if($row_id == 'RA205'){
			// 		//$technical_service = $survey_value;
			// 		$technical_service_value=strtoupper(substr($technical_service_value,0,-1));
			// 		$technical_service_value=str_replace('; ',';',$survey_value);
			// 		$technical_service_value=explode(';',$technical_service_value);
			// 		//$technical_service_value=str_replace(';','',$technical_service_value);
			// 		//print_r($technical_service_value);

			// 		foreach($value_parts as $rowvalue)
			// 		{
			// 			//echo strtoupper($rowvalue);
			// 			//echo '<br />';

			// 				//echo strtoupper($technical_checked_value);
			// 				if( in_array(strtoupper($rowvalue),$technical_service_value))
			// 				{
			// 					$checked_val="Y";
			// 				}
			// 				else
			// 				{
			// 					$checked_val ="";
			// 				}
			// 			$technical_checked_row .="<td >$checked_val</td>";
			// 		}

			// 	}

			// 	else if($row_id == 'RA208'){
			// 		$site_image = $survey_value;
			// 		$site_image = ltrim($site_image," ");
			// 		$site_image = rtrim($site_image," ");
			// 		$site_image = rtrim($site_image,";");
			// 		$site_image=str_replace('.JPEG','.jpeg',$site_image);
			// 		$site_image_array = explode(";",$site_image);

			// 		$image_string = '';
			// 		foreach($site_image_array as $image){
			// 			$image = ltrim($image," ");
			// 			if($image != '')
			// 			//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
			// 			$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

			// 		}
			// 	}*/
			// 	}
			// 	echo "
			// 	<td>" . $branch_name . "</td>
			// 	<td>" . $district . "</td>
			// 	<td>" . $customer_name . "</td>
			// 	<td >" . $customer_contact_no . "</td>
			// 	<td >" . $full_address . "</td>
			// 	<td >" . $head_mason_name . "</td>
			// 	<td >" . $head_mason_no . "</td>
			// 	<td >" . $engineer_name . "</td>
			// 	<td >" . $engineer_no . "</td>
			// 	<td >" . $head_reg_sellar . "</td>
			// 	<td >" . $site_segment . "</td>
			// 	<td >" . $visit_type_value . "</td>
			// 	<td >" . $conversion . "</td>
			// 	<td >" . $select_product . "</td>
			// 	<td >" . $req_date_delivery . "</td>
			// 	<td >" . $no_of_bags . "</td>

			// 	<td >" . $source_purchase_dealer . "</td>
			// 	<td >" . $source_purchase_dealer_code . "</td>
			// 	<td >" . $reason_non_conversion . "</td>
			// 	<td>" . $other_remarks . "</td>
			// 	<td>" . $project_segment . "</td>
			// 	<td>" . $type_of_construction . "</td>
			// 	<td>" . $site_potential_no_bags . "</td>
			// 	<td>" . $current_stage_construction . "</td>
			// 	<td>" . $cement_brand_used . "</td>
			// 	<td>" . $other_brand . "</td>

			// 	<td>" . $price_per_bag_rsp . "</td>

			// 	<td>" . $consumed_till_no_bags . "</td>
			// 	<td>" . $estimated_req_no_bags . "</td>
			// 	<td>" . $meeting_person . "</td>
			// 	<td>" . $decision_maker . "</td>
			// 	<td>" . $product_demo . "</td>
			// 	<td>" . $overall_remarks . "</td>
			// 	<td>" . $survey_status . "</td>
			// 	<td>" . $status_updated_datetime . "</td>
			// 	<td>" . $approval_emp_name . "</td>
			// 	<td>" . $approval_emp_code . "</td>
			// 	<td>" . $actual_date_delivery . "</td>
			// 	<td>" . $delivery_remarks . "</td>
			// 	<td>" . $reason_not_delivery . "</td>
			//   </tr>";
		}
	} else {
		echo "<tr><td colspan='39' align='center'>No Records</td><tr>";
	}
		?>
		</table>
		<br>
		<br>
		<div style="width:100%;" align="right" id="print_export"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
			<!-- <input name="export" type="button" value="Export" id="btnExport" onClick="exprttocsv();"> -->
			<input name="export" type="button" value="Export CSV" id="btnExport" onClick="exporttocsv();">
			<!-- <input name="export_old" type="button" value="Export XLS (Old)" id="btnExportOld" onClick="exporttocsv();" >-->
		</div>


		<?php
		mysqli_close($link);
		?>
		<script>
			// function exportToCSVNew() {
			//     var table = document.querySelector('table[border="1"]') || document.querySelector('table');
			//     if (!table) {
			//         alert('No table found to export');
			//         return;
			//     }

			//     var csv = [];
			//     var rows = table.querySelectorAll('tr');

			//     for (var i = 0; i < rows.length; i++) {
			//         var row = [];
			//         var cols = rows[i].querySelectorAll('td, th');

			//         for (var j = 0; j < cols.length; j++) {
			//             var cell = cols[j];

			//             // Skip hidden columns
			//             if (cell.style.display === 'none' || 
			//                 window.getComputedStyle(cell).display === 'none' ||
			//                 cell.offsetParent === null) {
			//                 continue;
			//             }

			//             var cellText = (cell.innerText || cell.textContent || '')
			//                 .replace(/(\r\n|\n|\r)/gm, ' ')
			//                 .replace(/\s+/g, ' ')
			//                 .trim();

			//             if (cellText.indexOf(',') !== -1 || cellText.indexOf('"') !== -1) {
			//                 cellText = '"' + cellText.replace(/"/g, '""') + '"';
			//             }

			//             row.push(cellText);
			//         }

			//         if (row.length > 0) csv.push(row.join(','));
			//     }

			//     var csvString = csv.join('\n');
			//     var dt = new Date();
			//     var filename = 'Site_Lead_Report_' + dt.getDate() + '.' + (dt.getMonth()+1) + '.' + dt.getFullYear() + '.csv';

			//     var blob = new Blob(["\uFEFF" + csvString], { type: 'text/csv;charset=utf-8;' });

			//     if (window.navigator.msSaveOrOpenBlob) {
			//         window.navigator.msSaveOrOpenBlob(blob, filename);
			//     } else {
			//         var link = document.createElement('a');
			//         var url = URL.createObjectURL(blob);
			//         link.href = url;
			//         link.download = filename;
			//         document.body.appendChild(link);
			//         link.click();
			//         document.body.removeChild(link);
			//     }
			// }
		</script>