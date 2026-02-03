<?php
ob_start();
session_start();
require("adminUtils.php");

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
$start_date=date('Y').'0401';
$end_date=(date('Y')+1).'0331';

$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name;
$survey_type='Technical Meets';

$sql_survey_output = "SELECT survey_id, SUBSTRING(survey_id,3,5) AS emp_code, DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') AS survey_date,value FROM survey_output WHERE type = '".$survey_type."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND row_id='RA040' AND (SUBSTRING(survey_id,-14,8) BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') ASC";
$res_survey_output = mysqli_query($link,$sql_survey_output);
$total_rows = mysqli_num_rows($res_survey_output);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="80%">
      <tr>
      	<td colspan="4" class="TDHEAD_SUB">Summary Report</td>
      </tr>
      <tr>
        <td width="20%"><b>Zone</b></td>
        <td width="20%"><?php echo strtoupper($zone);?></td>
        <td width="20%"><b>Employee</b></td>
        <td width="20%"><?php echo strtoupper($new_emp_name);?></td>
      </tr>
       <tr>
        <td width="20%"><b>Branch</b></td>
        <td width="20%"><?php echo strtoupper($branch);?></td>
        <td width="20%"><b>Department</b></td>
        <td width="20%"><?php echo strtoupper($department);?></td>
      </tr>
      </table>
      <table  width="80%">
      <tr>
      	<td colspan="4" >&nbsp;</td>
      </tr>
      </table>
      <table border="1" style="border-collapse:collapse;" class="border" width="80%">
       <tr>
      	<td colspan="4" class="TDHEAD_SUB">YTD</td>
      </tr>
      <tr class="TDHEAD">
        <td width="">Meet Type</td>
        <td width="20%" align="center">Target</td>
        <td width="20%" align="center">Actual</td>
        <td width="20%" align="center">Difference</td>
      </tr>
    <?php
	$res_survey_output = mysqli_query($link,$sql_survey_output);
	$survey_val_array=array();
	$survey_month_array=array();
	while($row_survey_ouput = mysqli_fetch_assoc($res_survey_output)){
		$survey_id = $row_survey_ouput['survey_id'];
		$emp_code = $row_survey_ouput['emp_code'];
		$survey_date = $row_survey_ouput['survey_date'];
		$surveyval=$row_survey_ouput['value'];
		$survey_month=substr($survey_date,3,2);
		//${monthval.$survey_month.$surveyval}=${monthval.$survey_month.$surveyval}.$surveyval.',';
		
		${totalcounts.$surveyval}=${totalcounts.$surveyval}+1;
		${totalcounts.$survey_month.$surveyval}=${totalcounts.$survey_month.$surveyval}+1;
		if(!in_array($surveyval,$survey_val_array))
		{
			array_push($survey_val_array,$surveyval);
		}
		if(!in_array($survey_month,$survey_month_array))
		{
			array_push($survey_month_array,$survey_month);
		}
		/*$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysqli_query($link,$sql_emp_details);
		$row_emp_details = mysqli_fetch_assoc($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$dns_emp_code = $row_emp_details['dns_emp_code'];*/
	}
	foreach($survey_val_array as $surveyvals)
	{
		echo "<tr>
				<td>".$surveyvals."</td>
				<td>&nbsp;</td>
				<td align=\"right\">".${totalcounts.$surveyvals}."</td>
				<td>&nbsp;</td></tr>";
	}
	?>
    </table>
    <br />
    <?php
		foreach($survey_month_array as $monthval)
		{
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="80%">
       <tr>
      	<td colspan="4" class="TDHEAD_SUB"><?php echo date('F', mktime(0, 0, 0, $monthval, 10));?></td>
      </tr>
      <tr class="TDHEAD">
        <td width="">Meet Type</td>
        <td width="20%" align="center">Target</td>
        <td width="20%" align="center">Actual</td>
        <td width="20%" align="center">Difference</td>
      </tr>
    <?php
			foreach($survey_val_array as $surveyvalmonth)
			{
				echo "<tr>
					<td>".$surveyvalmonth."</td>
					<td>&nbsp;</td>
					<td align=\"right\">".${totalcounts.$monthval.$surveyvalmonth}."</td>
					<td>&nbsp;</td></tr>";
			}
			echo "</table><br />";
		}
	?>	
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