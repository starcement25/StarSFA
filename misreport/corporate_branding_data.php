<?php
//ob_start();
session_start();
if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		require("adminUtils_branding.php");
	}
	else
	{
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

$sql_table_view = "SELECT value FROM table_view WHERE row_id = 'RA205' and type='checkbox'";
$res_table_view = mysqli_query($link,$sql_table_view);

$rowtableview=mysqli_fetch_assoc($res_table_view);
$value = $rowtableview['value'];
$value_parts=explode("/",$value);

foreach($value_parts as $rowheaderval)
{
	$product_row .= "<td width=\"2%\">$rowheaderval</td>";
}

$sql_distinct_date = "SELECT DISTINCT survey_id,value, SUBSTRING(survey_id,3,5) AS emp_code,DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date,
					DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%H:%i:%s') AS survey_time 
						FROM survey_output WHERE 
					(SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					AND SUBSTRING(survey_id,3,5) IN(".$employee.")  AND type = 'Corporate Branding' AND row_id='RA353'  
					ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
        <form action="branding_verification_account.php" method="post">
    <input type="hidden" name="mode" value="PO_no_update" />

    <table border="1" style="border-collapse:collapse;" class="border" width="90%">
 		<tr>
      	  <td colspan="43" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
        </tr>
        
      <tr class="TDHEAD">
		 <td width="8%">Transaction ID No.</td> 
        <td width="8%">Visit Date</td>
        <td width="8%">Employee Code</td>
        <td width="12%">Employee Name</td>
		 <td width="8%">Area</td>
		 <td width="8%">Route</td> 
        <td width="8%">Type Of Branding</td>
        <td width="8%">Shop/Dhaba Name/Wall Number</td>
        <td width="8%">L-R (ft.)</td>
        <td width="8%">T-B (ft.)</td>
        <td width="8%">Sq. Ft.</td>
		<td width="8%">Picture</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_distinct_date);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$survey_time = $row_survey_ouput['survey_time'];
		$value=$row_survey_ouput['value'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name ,reporting_to,district,HQ FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$reporting_to = $row_emp_details['reporting_to'];
		$district = $row_emp_details['district'];
		$HQ = $row_emp_details['HQ'];
		
		
		/*$sql_location = "SELECT latt,longi FROM location WHERE trans_id = '".$survey_id."'";
		$res_location = mysqli_query($link,$sql_location);
		$row_location = mysqli_fetch_assoc($res_location);
		$latt = $row_location['latt'];
		$longi = $row_location['longi'];*/
		
		echo "<tr>
				<td>".$survey_id."</td>
				<td>".$survey_date." ".$survey_time."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				";

		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		$technical_checked_row='';
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA353'){
				$branch_value=$survey_value;
				$sql_branch = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_value."'";
				$res_branch = mysqli_query($link,$sql_branch);
				$row_branch = mysqli_fetch_assoc($res_branch);
				$branch_name = $row_branch['branch_name'];	
			}
			if($row_id == 'RA338'){
				$route  = $survey_value;
			}
			else if($row_id == 'RA337'){
				$type_of_branding  = $survey_value;
			}
			else if($row_id == 'RA354'){
				$shop_dhaba  = $survey_value;
			}
			else if($row_id == 'RA340'){
				$L_R =$survey_value;
			}
			else if($row_id == 'RA341'){
				$T_B = $survey_value;
			}
			else if($row_id == 'RA342'){
				$sq_ft = $survey_value;
			}
			else if($row_id == 'RA336'){
				$photo = $survey_value;
				
			$photo = ltrim($photo," ");
			$photo = rtrim($photo," ");
			$photo = rtrim($photo,";");
			$photo=str_replace('.JPEG','.jpeg',$photo);
			$photo_array = explode(";",$photo);
			
			$image_string = '';
			foreach($photo_array as $image){
				$image = ltrim($image," ");
				if($image != '')
				//$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				$image_string .= "<a href=\"https://starcement1-sbinfo-upload.s3.ap-south-1.amazonaws.com/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
			}
			}
		}
		echo "
			<td>".$branch_name."</td>
			<td>".$route."</td>
			<td>".$type_of_branding."</td>
			<td >".$shop_dhaba."</td>
			<td >".$L_R."</td>
			<td >".$T_B."</td>
			<td >".$sq_ft."</td>
			<td >".$image_string."</td>
		  </tr>";
	  }
	}
	else{
		echo "<tr><td colspan='39' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br>
    <br>
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>			
			
			
    <?php
mysqli_close($link);
?>
