<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
$survey_type = $_REQUEST['survey_type'];
$month_data = $_REQUEST['month_data'];
$month_data_new = str_replace("-","",$month_data);
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$date_condition = " SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."' ";

?>
<table border="1" style="border-collapse:collapse;" class="border" width="30%">
  <tr class="TDHEAD">
  	<td align="center">Branding Activity</td>
    <td align="center">Actual</td>
  </tr>
  
<?php
$visit_cat_array = array();
$sql_visit_cat = "SELECT DISTINCT value FROM `survey_output` WHERE row_id = 'RA045' AND ".$date_condition." AND type = '".$survey_type."' ORDER BY value ASC";
$res_visit_cat = mysql_query($sql_visit_cat);
while($row_visit_cat = mysql_fetch_array($res_visit_cat)){
	$visit_cat_name = $row_visit_cat['value'];
	$visit_cat_name_parts=substr($visit_cat_name,0,7);
	if(strtolower($visit_cat_name_parts)=='counter') $visit_cat_name='Counter';
	if(!in_array($visit_cat_name,$visit_cat_array))
	{
		array_push($visit_cat_array,$visit_cat_name);
	}
}
//print_r($visit_cat_array);
foreach($visit_cat_array as $visit_cat_val){
	if(stristr($visit_cat_val, 'COUNTER') === FALSE) 
	{
	$sql_survey_visittype = "SELECT COUNT(survey_id) FROM `survey_output` WHERE row_id = 'RA045' AND value = '".$visit_cat_val."' AND ".$date_condition." AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND type = '".$survey_type."'";
	}
	else
	{
		$sql_survey_visittype = "SELECT COUNT(survey_id) FROM `survey_output` WHERE row_id = 'RA045' AND SUBSTRING(value,1,7) = 'Counter' 
						AND ".$date_condition." AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND type = '".$survey_type."'";
		$visit_cat_val='Counter Visit';				
	}
	$res_survey_visittype = mysql_query($sql_survey_visittype);
	$row_survey_visittype = mysql_fetch_array($res_survey_visittype);
	$visit_type = $row_survey_visittype['COUNT(survey_id)'];
	$visit_type_total += $visit_type;
	
	echo "<tr>
			<td>".$visit_cat_val."</td>
			<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"get_visit_details('$visit_cat_val','$start_date','$end_date','$employee_arg','$survey_type');\">".$visit_type."</a></td>
		  </tr>";
	$visit_cat_val_string .= "^".$visit_cat_val."^-";
}
$visit_cat_val_string = rtrim($visit_cat_val_string,"-");
echo "<tr>
		<td>Total</td>
		<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"get_visit_details('$visit_cat_val_string','$start_date','$end_date','$employee_arg','$survey_type');\">".$visit_type_total."</a></td>
	</tr>";
?>
</table>