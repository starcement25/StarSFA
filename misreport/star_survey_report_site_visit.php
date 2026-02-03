<?php
//ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace(",","#",$employee);
$employee_arg = str_replace("'","^",$employee_arg);
$survey_type = $_REQUEST['survey_type'];

$sql_visit_status_value = "SELECT value FROM table_view WHERE row_id = 'RA033'";
$res_visit_status_value = mysqli_query($link,$sql_visit_status_value);
$row_visit_status_value = mysqli_fetch_assoc($res_visit_status_value);
$visit_status_value = $row_visit_status_value['value'];
$visit_status_array = explode("/",$visit_status_value);
sort($visit_status_array);

foreach($visit_status_array as $visit_status_val){
	$visit_status_string .= "'".$visit_status_val."',";
}
$visit_status_string = rtrim($visit_status_string,",");

$sql_distinct_date = "SELECT DISTINCT DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') AS survey_date FROM survey_output WHERE (SUBSTRING(survey_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND value IN(".$visit_status_string.") AND type = '".$survey_type."'  ORDER BY DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') DESC";
//echo $sql_distinct_date;
$res_distinct_date = mysqli_query($link,$sql_distinct_date);
$total_rows = mysqli_num_rows($res_distinct_date);

if($total_rows>0){
	?>
    <table border="1" style="border-collapse:collapse;" class="border" width="100%">
      <tr class="TDHEAD" align="center">
        <td>Date1</td>
        <td>Total Visited</td>
    <?php
	foreach($visit_status_array as $visit_status_val){
		echo "<td>".$visit_status_val."</td>";
		$visit_status_string .= "'".$visit_status_val."',";
		$visit_status_arg .= "^".$visit_status_val."^-";
	}
	echo "</tr>";
	$visit_status_arg = rtrim($visit_status_arg,"-");
	$res_distinct_date = mysqli_query($link,$sql_distinct_date);
	while($row_distinct_date = mysqli_fetch_assoc($res_distinct_date)){
		$survey_date = $row_distinct_date['survey_date'];
		$display_survey_date = date('d-m-Y',strtotime($survey_date));
		
		$total_visit_count = '';
		$visit_count_string = '';
		echo "<tr>
				<td>".date('d-m-Y',strtotime($survey_date))."</td>";
		foreach($visit_status_array as $visit_status_val){
			$sql_visit_count = "SELECT COUNT(survey_id) FROM survey_output WHERE row_id = 'RA033' AND value = '".$visit_status_val."' AND SUBSTRING(survey_id,3,5) IN(".$employee.") AND SUBSTRING(survey_id,-14,8) = '".str_replace("-","",$survey_date)."' AND type = '".$survey_type."'";
			//echo $sql_visit_count;
			$res_visit_count = mysqli_query($link,$sql_visit_count);
			$row_visit_count = mysqli_fetch_assoc($res_visit_count);
			$visit_count = $row_visit_count['COUNT(survey_id)'];
			if($visit_count == 0)
				$visit_count_data = "--";
			else
				$visit_count_data = "<a href=\"#\" style=\"color:blue;\" onclick=\"site_visit_details('$visit_status_val','$survey_date','$employee_arg','$survey_type');\">".$visit_count."</a>";
				
			$total_visit_count = (int)$total_visit_count+(int)$visit_count;
			$visit_count_string .= "<td align=\"right\">".$visit_count_data."</td>";
			
			$visit_array[$visit_status_val] += $visit_count;
		}
		$grand_total_visit_count = (int)$grand_total_visit_count+(int)$total_visit_count;
		echo "<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"site_visit_details('$visit_status_arg','$survey_date','$employee_arg','$survey_type');\">".$total_visit_count."</a></td>";
		echo $visit_count_string;
		
	}
	echo "<tr>";
	echo "<td><b>Total</b></td>";
	echo "<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"site_visit_details_all('$visit_status_arg','$start_date','$end_date','$employee_arg','$survey_type');\">".$grand_total_visit_count."</a></td>";
	foreach($visit_status_array as $visit_status_val){
		echo "<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"site_visit_details_all('$visit_status_val','$start_date','$end_date','$employee_arg','$survey_type');\">".$visit_array[$visit_status_val]."</a></td>";
	}
	echo "</tr>";
	echo "</table>";
}
else{
	echo "<center>No Records Found</center>";
}
mysqli_close($link);
?>
