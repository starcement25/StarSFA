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
	?>
    <div id="display_data">
    <table border="1" style="border-collapse:collapse;" class="border" width="195%">
    	<tr class="TDHEAD_SUB">
        	<td align="center" colspan="30">Summary of Technical Meet</td>
        </tr>
        <tr  style="border-collapse:collapse; color:#FFF; background-color: #903">
        <td >Date</td>
        <?php
			$sql_get_display="SELECT display_name,row_id,action FROM survey_input WHERE  survey_sub_menu='Technical Meets' ORDER BY display_order ASC";
			$res_get_display = mysql_query($sql_get_display);
			$count_display=mysql_num_rows($res_get_display);
			while($row_get_display = mysql_fetch_array($res_get_display))
			{
				echo '<td align="left">'.$row_get_display['display_name'].'</td>';
				$display_id=$row_get_display['row_id'];
				$row_id_string.="'".$display_id."'".',';
				$row_id_string_SET.=$display_id.',';
			}
			$row_id_string=substr($row_id_string,0,-1);
			$row_id_string_SET=substr($row_id_string_SET,0,-1);
		?>
        </tr>
    <?php
	$res_emp_code = mysql_query($sql_emp_code);
	while($row_emp_code = mysql_fetch_array($res_emp_code)){
		$emp_code = $row_emp_code['emp_code'];
		
		$sql_emp_details = "SELECT emp_name, designation, sale_access FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_details = mysql_query($sql_emp_details);
		$row_emp_details = mysql_fetch_array($res_emp_details);
		$emp_name = $row_emp_details['emp_name'];
		$designation = $row_emp_details['designation'];
		$sale_access = $row_emp_details['sale_access'];
		?>
        <tr>
        	<td colspan="30" align="center" style="padding:8px;">
            	<table border="1" style="border-collapse:collapse;" width="40%">
                	<tr>
                    	<td style="font-weight:bold;">Name</td>
                        <td><?php echo $emp_name; ?></td>
                        <td style="font-weight:bold;">Designation</td>
                        <td><?php echo $designation; ?></td>
                    </tr>
                    <tr>
                    	<td style="font-weight:bold;">Department</td>
                        <td><?php echo $sale_access; ?></td>
                    </tr>
                    <tr>
                    	<td colspan="4" align="center" style="font-weight:bold;">Details Of Technical Meets - (<?php echo date('d-m-Y', strtotime($start_date)). " to ".date('d-m-Y', strtotime($end_date)); ?>)</td>
                    </tr>
                </table>            	
            </td>
        </tr>
        <?php
				
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
					 echo "</tr>";
				 }
				 $previous_survey_id=$row_survey_output['survey_id'];
				 if(!in_array($survey_id, $survey_id_array))
				 {
					echo "<tr>
						<td>".$survey_date."</td>";
						array_push($survey_id_array,$survey_id);
				 }
				 if($row_id == 'RA054')
				 {
					 $sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$value."'";
					 $rscustomer=mysql_query($sqlcustomer);
					 $rowcustomer=mysql_fetch_array($rscustomer);
					 $customer_name=$rowcustomer['customer_name'];
					 echo"<td>".$customer_name."</td>";
				 }
				 else if($row_id == 'RA107' || $row_id == 'RA108'){
					$site_image = $value;
					$site_image = ltrim($site_image," ");
					$site_image = rtrim($site_image," ");
					$site_image = rtrim($site_image,";");
					$site_image_array = explode(";",$site_image);
					
					$image_string = '';
					foreach($site_image_array as $image){
						$image = ltrim($image," ");
						if($image != '')
						$image_string .= "<a href=\"http://salesmpower.acedns.in/upload/".$_SESSION['nick_name']."/".$image."\" target=\"_blank\" style=\"color:brown;\">View</a><br>";
					}
					echo"<td>".$image_string."</td>";
				 }
				 else
				 {
					echo"<td>".$value."</td>";
				 }
			 }
		}
	?>
    </table>
    </div>
    <?php
}
else{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
?>
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display_data');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv('#display_data');">
    
<?php    
    mysql_close($link);
?>


