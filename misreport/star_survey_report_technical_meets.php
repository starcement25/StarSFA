<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);

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

$sql_survey_output = "SELECT DISTINCT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date 
FROM survey_output WHERE type = '".$survey_type."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") 
AND (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND row_id IN('RA039','RA040','RA041','RA042','RA043','RA054') 
ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
$res_survey_output = mysqli_query($link,$sql_survey_output);
$total_rows = mysqli_num_rows($res_survey_output);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="145%">
      <tr>
      	<td colspan="9" class="TDHEAD_SUB"><?php echo $header_string; ?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="6%">Date</td>
        <td width="7%">Employee Code</td>
        <td width="12%">Employee Name</td>
        <td width="12%">Branch</td>
        <td width="12%">Route/Place</td>
        <td width="6%">Meet Type</td>
        <td width="5%">No of Particpants</td>
        <td width="18%">Name of Particpants</td>
        <td width="16%">Meet Details</td>
        <td width="6%">Photo link</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		
		$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];
		
		echo "<tr>
				<td>".$survey_date."</td>
				<td>".$dns_emp_code."</td>
				<td>".$emp_name."</td>";
		
		$sql_survey_details = "SELECT * FROM survey_output WHERE survey_id = '".$survey_id."'";
		$res_survey_details = mysqli_query($link,$sql_survey_details);
		while($row_survey_details = mysqli_fetch_assoc($res_survey_details)){
			$row_id = $row_survey_details['row_id'];
			$survey_value = str_replace('#',':',$row_survey_details['value']);
			
			if($row_id == 'RA039')
				$branch = $survey_value;
			else if($row_id == 'RA054')
				$place = $survey_value;
			else if($row_id == 'RA040'){
				$meet_type = $survey_value;
				if(strtoupper(substr($meet_type,0,7))=='COUNTER') 
				{
					$couter_parts_array=explode(':',$meet_type);
					$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$couter_parts_array[1]."'";
					$rscustomername=mysqli_query($link,$sqlcustomername);
					$rowcustomername=mysqli_fetch_assoc($rscustomername);
					$customer_name=$rowcustomername['customer_name'];
					$meet_type='Counter Meet'.':'.$customer_name;
				}
			}
			else if($row_id == 'RA041'){
				$no_of_participants = count(explode(':',$survey_value));
				$name_of_participants='';
				$name_of_participants='<table>';
				$name_participants_first=explode(':',$survey_value);
				
				foreach ($name_participants_first as $name_participants_first_val)
				{
					$name_participants_second=explode(';',$name_participants_first_val);
					if($name_participants_second[0]!='' || $name_participants_second[1]!='')
					{
						$name_of_participants.='<tr><td>'.$name_participants_second[0].';'.$name_participants_second[1].'</td></tr>';
					}
				}
				$name_of_participants.='</table>';
			}
			else if($row_id == 'RA042')
				$meet_details = $survey_value;
			else if($row_id == 'RA043'){
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
					$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/STAR/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
				}
			}
		}
		echo "<td>".$branch."</td>
			<td>".$place."</td>
			<td>".$meet_type."</td>
			<td align=\"right\">".$no_of_participants."</td>
			<td >".$name_of_participants."</td>
			<td>".$meet_details."</td>
			<td>".$image_string."</td>
		  </tr>";
	}
	?>
    </table>
    <br />
    
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "<center>No Records Found</center>";
}
mysqli_close($link);
?>