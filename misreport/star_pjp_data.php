<?php
ob_start();

session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);

$sql_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code IN(".$employee.") ORDER BY visit_date ASC";
$res_date = mysqli_query($link,$sql_date);
$total_row_check = mysqli_num_rows($res_date);
if($total_row_check>0){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>PJP report for the period 
	<?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
   	  <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <?php if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){?>
        <td width="10%">District</td>
        <?php }?>
        <td>Route Name</td>
      </tr>
    <?php
		foreach($emp_array as $emp_code){
			$sql_visit_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code = '".$emp_code."' ORDER BY visit_date ASC";
			$res_visit_date = mysqli_query($link,$sql_visit_date);
			$visit_date_check = mysqli_num_rows($res_visit_date);
			if($visit_date_check>0){
				
				$sql_emp_name = "SELECT emp_name,dns_emp_code,designation,district FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysqli_query($link,$sql_emp_name);
				$row_emp_name = mysqli_fetch_assoc($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				$dns_emp_code = $row_emp_name['dns_emp_code'];
				$designation = $row_emp_name['designation'];
				$district = $row_emp_name['district'];
				
				if(providing_code=='yes')
				{
	echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name." (".$dns_emp_code.") - ".$designation."</td></tr>";
				}
				else
				{
					if(strtoupper($_SESSION['nick_name'])=='RUPA')
					{
						echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name." ".$emp_code." - ".$designation."</td></tr>";
					}
					else
					{
					echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name." - ".$designation."</td></tr>";
					}
				}
						
				$res_visit_date = mysqli_query($link,$sql_visit_date);
				while($row_visit_date = mysqli_fetch_assoc($res_visit_date)){
					$visit_date = $row_visit_date['visit_date'];
					
					$sql_get_details = "SELECT route_code,create_date,(SELECT emp_name FROM employee_master WHERE emp_code=route_plan.working_with) AS working_with_name,remarks FROM route_plan WHERE visit_date = '".$visit_date."' 
										AND emp_code = '".$emp_code."' ORDER BY create_date ASC";
					$res_get_details = mysqli_query($link,$sql_get_details);
					$total_route_check = mysqli_num_rows($res_get_details);
					if($total_route_check>0){
						
						$res_get_details = mysqli_query($link,$sql_get_details);
						${$prev_create_date.$visit_date.$emp_code}='';
						while($row_get_details = mysqli_fetch_assoc($res_get_details)){
							$route_code = $row_get_details['route_code'];
							$create_date = $row_get_details['create_date'];
							$working_with_name = $row_get_details['working_with_name'];
							$remarks = $row_get_details['remarks'];
							
							$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
							$res_route_name = mysqli_query($link,$sql_route_name);
							$row_route_name = mysqli_fetch_assoc($res_route_name);
							$route_name = $row_route_name['route_name'];
							if($working_with_name !='')
							{
							$route_name_final = $row_route_name['route_name'].' - <b>'.$working_with_name.'</b>';
							}
							else $route_name_final = $row_route_name['route_name'];
							
							
							if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){
								
								if(${$prev_create_date.$visit_date.$emp_code}!='' && (strtotime($create_date) > strtotime(${$prev_create_date.$visit_date.$emp_code})))
								{
									$display_date=date('d/m/y',strtotime($create_date));
									$route_name_string .= '<font color="red"><b>ADDED '.$route_name." ON $display_date</b></font>, ";
								}
								else
								{
								$route_name_string .= $route_name.", ";
								}
							}
							else if(strtoupper($_SESSION['nick_name'])=='ILS'){
								if($remarks!=''){
								$route_name_string .= $route_name_final.' - <b>'.$remarks.'</b>, ';
								}
								else $route_name_string .= $route_name_final.", ";
							}
							else
							{
							$route_name_string .= $route_name_final.", ";
							}
							if(${$prev_create_date.$visit_date.$emp_code}==''){
							${$prev_create_date.$visit_date.$emp_code}=$create_date;
							}
						}
						$route_name_string = rtrim($route_name_string," ");
						$route_name_string = rtrim($route_name_string,",");
					}
				echo "<tr>
						<td>".date('d-m-Y',strtotime($visit_date))."</td>";
						 if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){
							 echo "<td>".$district."</td>";
						 }
						echo "<td>".$route_name_string."</td>
					  </tr>";
				$route_name_string = '';
				}
			}
		}
	?>
    </table><br />
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <?php if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){?>
        <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsvpjp();" >

    <?php }else{?>
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	<?php }?>
</div>
    <?php
}
else{
	echo "No Records";
}
mysqli_close($link);
?>


