<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
include("include/dbcon.php");
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
$survey_type = $_REQUEST['survey_type'];

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

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

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;From:".date('d-m-Y',strtotime($start_date))."&nbsp;&nbsp;To:".date('d-m-Y',strtotime($end_date));


$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output WHERE type = '".$survey_type."' AND SUBSTRING(survey_id,3,5) IN(".$employee_arg.") AND (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
$res_survey_output = mysqli_query($link,$sql_survey_output);
$total_rows = mysqli_num_rows($res_survey_output);
if($total_rows>0){
	?>
    
    <table border="1" style="border-collapse:collapse;" class="border" width="145%">
      <tr class="TDHEAD_SUB">
      	<td colspan="15"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td>Firm</td>
        <td>Contact Person</td>
        <td>Mobile</td>
        <td>Area</td>
        <td>Address</td>
        <td>Pincode</td>
        <td>Category</td>
        <td>Sub-Category</td>
        <td>Brand Used</td>
        <td>Potential</td>
        <td>Emp Code</td>
        <td>Created By</td>
        <td>Department</td>
        <td>Linked dealer</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_survey_details = "SELECT SO.row_id, SO.value FROM survey_output SO, survey_input SI WHERE SO.survey_id = '".$survey_id."' AND SO.row_id = SI.row_id AND SI.row_id IN ('RA004','RA005','RA006','RA013','RA007','RA008','RA011','RA012','RA014','RA015','RA060')";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$survey_row_id = $row_survey_details['row_id'];
			$survey_value = $row_survey_details['value'];
			
			if($survey_row_id == 'RA004')
				$firm_name = $survey_value;
			else if($survey_row_id == 'RA005')
				$contact = $survey_value;
			else if($survey_row_id == 'RA006')
				$mobile = $survey_value;
			else if($survey_row_id == 'RA007')
				$address = $survey_value;
			else if($survey_row_id == 'RA008')
				$pincode = $survey_value;
			else if($survey_row_id == 'RA011')
				$category = $survey_value;
			else if($survey_row_id == 'RA012')
				$sub_category = $survey_value;
			else if($survey_row_id == 'RA013')
				$area = $survey_value;
			else if($survey_row_id == 'RA014')
				$brand_most_sold = $survey_value;
			else if($survey_row_id == 'RA015')
				$potential = $survey_value;
			else if($survey_row_id == 'RA060')
				$linked_dealer = $survey_value;	
		}
		$sql_emp_details = "SELECT dns_emp_code, emp_name, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		$emp_name = $row_emp_details['emp_name'];
		$sale_access = $row_emp_details['sale_access'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$firm_name."</td>
				<td>".$contact."</td>
				<td>".$mobile."</td>
				<td>".$area."</td>
				<td>".$address."</td>
				<td>".$pincode."</td>
				<td>".$category."</td>
				<td>".$sub_category."</td>
				<td>".$brand_most_sold."</td>
				<td align=\"right\">".$potential."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>
				<td>".$sale_access."</td>
				<td>".$linked_dealer."</td>
			  </tr>";
	}
	?>
    </table>
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
	
}
else{
	echo "<center>No records found</center>";
}
mysqli_close($link);
?>