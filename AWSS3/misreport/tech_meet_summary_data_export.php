<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$start_date_search=str_replace('-','',$start_date);
$end_date = $_REQUEST['end_date'];
$end_date_search=str_replace('-','',$end_date);
if(strpos($employee,',')!=false){
	$emp_condition = " AND SUBSTRING(survey_id,3,5) IN (".$employee.")";
}
else{
	$emp_condition = " AND SUBSTRING(survey_id,3,5) = ".$employee."";
}

$sql_emp_code = "SELECT DISTINCT SUBSTRING(survey_id,3,5) AS emp_code FROM survey_header WHERE survey_type='Technical Meets' 
				".$emp_condition." AND (DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')  
					AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%d-%m-%Y') >'08-08-2017'";
$res_emp_code = mysql_query($sql_emp_code);
$total_row_check = mysql_num_rows($res_emp_code);
if($total_row_check>0){
$survey_id_array=array();
	$header = "Date"."\t";
	$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE  survey_sub_menu='Technical Meets' AND row_id NOT IN('RA107','RA108')
						ORDER BY display_order ASC";
	$res_get_display = mysql_query($sql_get_display);
	$count_display=mysql_num_rows($res_get_display);
	while($row_get_display = mysql_fetch_array($res_get_display))
	{
		$header.=$row_get_display['display_name']."\t";
		$display_id=$row_get_display['row_id'];
		$row_id_string.="'".$display_id."'".',';
		$row_id_string_SET.=$display_id.',';
	}
	$header.="\n";
	$row_id_string=substr($row_id_string,0,-1);
	$row_id_string_SET=substr($row_id_string_SET,0,-1);
	$res_emp_code = mysql_query($sql_emp_code);
	while($row_emp_code = mysql_fetch_array($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		
		$sql_emp_details = "SELECT emp_name, designation, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$designation = $row_emp_details['designation'];
		$sale_access = $row_emp_details['sale_access'];
		if(count($survey_id_array)>0)
		{
			$table_data.="\n\n";
		}
		$table_data.="Name"."\t".$emp_name."\t"."Designation"."\t".$designation."\t"."\n";
		$table_data.="Department"."\t".$sale_access."\n\n";
				
			  $sql_survey_output="SELECT *,DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') AS survey_date FROM survey_output SO WHERE SO.row_id IN(".$row_id_string.") AND (DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') 
			  AND SUBSTRING(SO.survey_id,3,5)='".$emp_code."' AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%d-%m-%Y') >'08-08-2017'
			  	ORDER BY SO.survey_id DESC,FIND_IN_SET(SO.row_id,'".$row_id_string_SET."')";
			  $rs_survey_output=mysql_query($sql_survey_output);
			  $output_no=1;
			  while($row_survey_output=mysql_fetch_array($rs_survey_output))
			  {		
				 $survey_id= $row_survey_output['survey_id'];
				 $survey_date= $row_survey_output['survey_date'];
				 $value=$row_survey_output['value'];
				 $row_id =$row_survey_output['row_id'];
				 if(isset($previous_survey_id) && $previous_survey_id!=$survey_id)
				 {
					$table_data.="\n";
				 }
				 $previous_survey_id=$row_survey_output['survey_id'];
				 if(!in_array($survey_id, $survey_id_array))
				 {
						$table_data.=$survey_date."\t";
						array_push($survey_id_array,$survey_id);
				 }
				 if($row_id == 'RA054')
				 {
					 $sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$value."'";
					 $rscustomer=mysql_query($sqlcustomer);
					 $rowcustomer=mysql_fetch_array($rscustomer);
					 $customer_name=$rowcustomer['customer_name'];
					 $table_data.=$customer_name."\t";
				 }
				 else
				 {
					$table_data.=$value."\t";
				 }
			 }
		}
}
if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Tech_Summary_SKIPPER.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
    mysql_close($link);
?>


