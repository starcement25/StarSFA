<?php
ob_start();
session_start();
if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		require("adminUtils_branding.php");
	}
	else
	{
		require("adminUtils.php");
	}

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
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc";
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

$sql_distinct_date = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') AS survey_date 
						FROM survey_output WHERE 
					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = 'Branding Verification'  
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
$res_distinct_date = mysql_query($sql_distinct_date);
$total_rows = mysql_num_rows($res_distinct_date);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="15" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
      <tr class="TDHEAD">
        <td width="5%">Date</td>
        <td width="6%">Vendor Code</td>
        <td width="9%">Vendor Name</td>
        <td width="6%">Employee Code</td>
        <td width="9%">Employee Name</td>
        <td width="6%">Branch</td>
        <td width="6%">Type of Branding</td>
        <td width="9%">Site Name</td>
        <!--td width="6%">Width (in inch)</td>
        <td width="6%">Height (in inch)</td>
        <td width="7%">Total Sq ft completed</td-->
         <td width="">Remarks</td>
        <td width="6%">Photo link</td>
        <td width="6%">Lattitude</td>
        <td width="6%">Longitude</td>
        <td width="6%">Address</td>
        <td width="6%">Locate</td>
      </tr>
    <?php
	$res_survey_output = mysql_query($sql_distinct_date);
	while($row_survey_ouput = mysql_fetch_array($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$reporting_to = $row_emp_details['reporting_to'];
		
		$sql_reporting = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$reporting_to."'";
		$res_reporting = mysql_query($sql_reporting);
		$row_reporting = mysql_fetch_array($res_reporting);
		$vendor_name = $row_reporting['emp_name'];
		$vendor_code = $row_reporting['dns_emp_code'];
		
		$sql_location = "SELECT latt,longi FROM location WHERE trans_id = '".$survey_id."'";
		$res_location = mysql_query($sql_location);
		$row_location = mysql_fetch_array($res_location);
		$latt = $row_location['latt'];
		$longi = $row_location['longi'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$vendor_code."</td>
				<td>".$vendor_name."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysql_query($sql_survey_details);
		while($row_survey_details = mysql_fetch_array($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA150'){
				$branch = $survey_value;
				$sqlbranchname="SELECT branch_name FROM branch_master WHERE branch_code='".$branch."'";
				$rsbranchname=mysql_query($sqlbranchname);

				$rowbranchname=mysql_fetch_array($rsbranchname);
				$branch_name=$rowbranchname['branch_name'];
			}
			else if($row_id == 'RA151'){
				$type_of_brand = $survey_value;
					}
			else if($row_id == 'RA147'){
				$Site_name =$survey_value;
			}
			else if($row_id == 'RA148'){
				$remarks = $survey_value;
			}
			/*else if($row_id == 'RA152'){
				$width = $survey_value;
			}
			else if($row_id == 'RA153'){
				$height = $survey_value;
			}
			else if($row_id == 'RA154'){
				$sq_feet = $survey_value;
			}*/
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
					$image_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";

				}
			}
		}
		if($latt > 0 && $longi > 0)
		{
			 $address=getReverseGeoAdd($latt,$longi);
		}
		echo "<td>".$branch_name."</td>
			<td >".$type_of_brand."</td>
			<td >".$Site_name."</td>
			<td >".$remarks."</td>
			<td>".$image_string."</td>
			<td>".$latt."</td>
			<td>".$longi."</td>
			<td>".$address."</td>
			<td><a href=\"adminAttendanceLocate.php?trans_id=$survey_id&emp_code=$emp_code&mode=daterange&page=branding\" target=\"_blank\" style=\"color:brown;\">Locate</a></td>
		  </tr>";
	}
}
else{
	echo "<center>No Records Found</center>";
}
mysql_close($link);
?>
