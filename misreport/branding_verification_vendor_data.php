<?php
//ob_start();
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
session_start();
if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		//require("adminUtils_branding.php");
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils.php");
	}

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

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

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
//$survey_type = $_REQUEST['survey_type'];

function getReverseGeoAdd($latitude,$longitude)
	{
		// format this string with the appropriate latitude longitude
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyD1FStdfP2A_yPGPaT4Ga1CIAr_dV4BFxk";
		// make the HTTP request
		$data = @file_get_contents($url);
		// parse the json response
		$jsondata = json_decode($data,true);
		
		//print_r($jsondata);
		// if we get a formatted_address array and the status was OK, get the addres
		if(is_array($jsondata )&& $jsondata['status']=='OK')
		{
			  $addr = $jsondata['results']['0']['formatted_address'];
		}		
		return  $addr;	
	}


$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));

$sql_distinct_date = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') AS survey_date,
					DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%H:%i:%s') AS survey_time 
						FROM survey_output WHERE 
					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = 'Branding Verification'  
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
        <form action="branding_verification_account.php" method="post">
    <input type="hidden" name="mode" value="PO_no_update" />

    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="22" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="5%">Time</td>
        <td width="6%">Vendor Code</td>
        <td width="9%">Vendor Name</td>
        <td width="5%">State</td>
        <td width="5%">Area</td>
        <td width="5%">City</td>
        <td width="5%">Location Facing</td>
        <td width="3%">Media</td>
        <td width="3%">Type</td>
        <td width="3%">L-R</td>
        <td width="3%">T-B</td>
        <td width="3%">Qty</td>
        <td width="4%">Fascia</td>
        <td width="3%">Sq.Ft.</td>
        <td width="3%">Photo link</td>
        <td width="3%">Lattitude</td>
        <td width="3%">Longitude</td>
        <td width="">Address</td>
        <td width="4%">Locate</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_distinct_date);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$survey_time = $row_survey_ouput['survey_time'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		//$vendor_name = $row_emp_details['emp_name'];
		//$vendor_code = $row_emp_details['dns_emp_code'];
		$reporting_to = $row_emp_details['reporting_to'];
		
		$sql_reporting = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$reporting_to."'";
		$res_reporting = mysqli_query($link,$sql_reporting);
		$row_reporting = mysqli_fetch_assoc($res_reporting);
		$vendor_name = $row_reporting['emp_name'];
		$vendor_code = $row_reporting['dns_emp_code'];
		$sql_po_no = "SELECT PO_no, PO_submitted_by,PO_submitted_date FROM survey_header WHERE survey_id = '".$survey_id."'";
		$res_po_no = mysqli_query($link,$sql_po_no);
		$row_po_no = mysqli_fetch_assoc($res_po_no);
		$PO_no = $row_po_no['PO_no'];
		$PO_submitted_date = $row_po_no['PO_submitted_date'];
		if($PO_no=='') $PO_submitted_date='';
		
		$sql_location = "SELECT latt,longi FROM location WHERE trans_id = '".$survey_id."'";
		$res_location = mysqli_query($link,$sql_location);
		$row_location = mysqli_fetch_assoc($res_location);
		$latt = $row_location['latt'];
		$longi = $row_location['longi'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$survey_time."</td>
				<td>".$vendor_code."</td>
				<td>".$vendor_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA147'){
				$state=$survey_value;	
			}
			else if($row_id == 'RA148'){
				$area = $survey_value;
					}
			else if($row_id == 'RA150'){
				$city =$survey_value;
			}
			else if($row_id == 'RA151'){
				$location_facing = $survey_value;
			}
			else if($row_id == 'RA152'){
				$media = $survey_value;
			}
			else if($row_id == 'RA153'){
				$type = $survey_value;
			}
			else if($row_id == 'RA154'){
				$L_R = $survey_value;
			}
			else if($row_id == 'RA160'){
				$T_B = $survey_value;
			}
			else if($row_id == 'RA161'){
				$Qty = $survey_value;
			}
			else if($row_id == 'RA162'){
				$fascia = $survey_value;
			}
			else if($row_id == 'RA163'){
				$Sq_ft = $survey_value;
			}
			else if($row_id == 'RA149'){
				$site_image = $survey_value;
				$site_image = ltrim($site_image," ");
				$site_image = rtrim($site_image," ");
				$site_image = rtrim($site_image,";");
				$site_image=str_replace('.JPEG','.jpeg',$site_image);
				$site_image_array = explode(";",$site_image);
				
				$image_string = '';
				foreach($site_image_array as $image){
					$image = ltrim($image," ");
					if($image != '')
					//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".strtoupper($_SESSION['nick_name'])."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		if($latt > 0 && $longi > 0)
		{
			 $address=getReverseGeoAdd($latt,$longi);
		}
		echo "<td>".$state."</td>
			<td >".$area."</td>
			<td >".$city."</td>
			<td >".$location_facing."</td>
			<td >".$media."</td>
			<td >".$type."</td>
			<td >".$L_R."</td>
			<td >".$T_B."</td>
			<td >".$Qty."</td>
			<td >".$fascia."</td>
			<td >".$Sq_ft."</td>
			<td>".$image_string."</td>
			<td>".$latt."</td>
			<td>".$longi."</td>
			<td>".$address."</td>
			<td><a href=\"adminAttendanceLocate.php?trans_id=$survey_id&emp_code=$emp_code&mode=daterange&page=branding\" target=\"_blank\" style=\"color:brown;\">Locate</a></td>
		  </tr>";
	}
	}
	else{
		echo "<tr><td colspan='22' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
    <?php
mysqli_close($link);
?>
