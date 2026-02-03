<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
$visit_type = $_REQUEST['visit_type'];
$month_data = $_REQUEST['month_data'];
$survey_type = $_REQUEST['survey_type'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_condition = " SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."' ";

$visit_type_data = '';
$visit_type_data = str_replace("^","'",$visit_type);
$visit_type_data = str_replace("-",",",$visit_type_data);

if(stristr($visit_type_data,"'") == true){
	$visit_condition = " (value IN(".$visit_type_data.") OR SUBSTRING(value,1,7) = 'Counter')";
}
else{
	if($visit_type=='Counter Visit')
	{
		$visit_condition = " SUBSTRING(value,1,7) = 'Counter'";
	}
	else
	{
		$visit_condition = " value = '".$visit_type."'";
	}
}

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


if(strpos($employee,"#") == FALSE){
	$new_emp_code = str_replace("^","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysql_query($sql_emp_name);
	$row_emp_name = mysql_fetch_array($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name;
?>

<table border="1" style="border-collapse:collapse;" class="border" width="100%">
  <tr class="TDHEAD_SUB">
    <td colspan="10"><?php echo $header_string; ?></td>
  </tr>
  <tr class="TDHEAD">
  	<td align="center" width="10%">Date</td>
    <td align="center">Emp Code</td>
    <td align="center">Emp Name</td>
    <td align="center">Department</td>
    <td align="center">Zone</td>
    <td align="center">Branch</td>
    <td align="center">Place</td>
    <td align="center">Branding Activity</td>
    <td align="center">Details</td>
    <td align="center">Photo Link</td>
  </tr>

<?php
$sql_distinct_survey_id = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output WHERE ".$visit_condition." AND ".$date_condition." AND SUBSTRING(survey_id,3,5) IN(".$employee_arg.") AND type = '".$survey_type."' ORDER BY SUBSTRING(survey_id,-14,8) DESC";
$res_distinct_survey_id = mysql_query($sql_distinct_survey_id);
while($row_distinct_survey_id = mysql_fetch_array($res_distinct_survey_id)){
	$survey_id = $row_distinct_survey_id['survey_id'];
	$emp_code = $row_distinct_survey_id['emp_code'];
	$survey_date = $row_distinct_survey_id['survey_date'];
	
	$sql_vtype = "SELECT value FROM survey_output WHERE row_id = 'RA045' AND survey_id = '".$survey_id."'";
	$res_vtype = mysql_query($sql_vtype);
	$row_vtype = mysql_fetch_array($res_vtype);
	//$vtype = $row_vtype['value'];
	$vtype = str_replace('#',':',$row_vtype['value']);
	
	if(substr($vtype,0,7)=='Counter') 
	{
		$couter_parts_array=explode(':',$vtype);
		$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$couter_parts_array[1]."'";
		$rscustomername=mysql_query($sqlcustomername);
		$rowcustomername=mysql_fetch_array($rscustomername);
		$customer_name=$rowcustomername['customer_name'];
		$vtype='Counter Visit'.':'.$customer_name;
	}
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code, sale_access, zone FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysql_query($sql_emp_details);
	$row_emp_details = mysql_fetch_array($res_emp_details);
	
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	$emp_name = $row_emp_details['emp_name'];
	$sale_access = $row_emp_details['sale_access'];
	$zone = $row_emp_details['zone'];
	
	echo "<tr>
			<td>".$survey_date."</td>
			<td>".$dns_emp_code."</td>
			<td>".$emp_name."</td>
			<td>".$sale_access."</td>
			<td>".$zone."</td>
			";
	
	$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
	$res_survey_details = mysql_query($sql_survey_details);
	while($row_survey_details = mysql_fetch_array($res_survey_details)){
		$row_id = $row_survey_details['row_id'];
		$value = $row_survey_details['value'];
		
		if($row_id == 'RA046')
			$visit_details = $value;
		else if($row_id == 'RA056')
			$branch = $value;
		else if($row_id == 'RA057')
			$place = $value;
		else if($row_id == 'RA047'){
			$pic = $value;
			$pic = ltrim($pic," ");
			$pic = rtrim($pic," ");
			$pic = rtrim($pic,";");
			$pic=str_replace('.JPEG','.jpeg',$pic);

			$pic_array = explode(";",$pic);
			foreach($pic_array as $pic_val){
				$pic_val = ltrim($pic_val," ");
				if($pic_val != '')
				//$pic_string .= "<a href=\"http://salesmpower.acedns.in/upload/STAR/".$pic_val."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				$pic_string .= "<a href=\"https://starcement.s3.ap-south-1.amazonaws.com/".$pic_val."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
			}
			
		}
		
	}
	echo "<td>".$branch."</td>
		  <td>".$place."</td>
		  <td>".$vtype."</td>
		  <td>".$visit_details."</td>
		  <td>".$pic_string."</td>
		  </tr>";
		  $pic_string = '';
}
mysql_close($link);
?>
</table>
<br />
<div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>