<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = strtotime($_REQUEST['start_date']);
$end_date = strtotime($_REQUEST['end_date']);
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);
?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
   	  <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <td>Route Name</td>
      </tr>
    <?php
		$no_records=0;
		foreach($emp_array as $emp_code){
			$sql_visit_details = "SELECT CM.visit_day,RM.route_name FROM customer_master CM,route_master RM,customer_route_emp_relation CRER 
								WHERE CM.route_code=RM.route_code AND CM.customer_code=CRER.customer_code AND CRER.emp_code = '".$emp_code."' AND CRER.acedns='Y' GROUP BY CM.visit_day,RM.route_name ORDER BY CM.visit_day ASC,RM.route_name ASC";
			$res_visit_details = mysqli_query($link,$sql_visit_details);
			$visit_details_check = mysqli_num_rows($res_visit_details);
			if($visit_details_check >0){
					while($row_visit_details = mysqli_fetch_assoc($res_visit_details)){
						$visit_day=strtoupper($row_visit_details['visit_day']);
						$route_name=$row_visit_details['route_name'];
							
						${visit_details.$visit_day.$emp_code}=${visit_details.$visit_day.$emp_code}.$route_name.",";
					}
					
					$sql_emp_name = "SELECT emp_name,dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
					$res_emp_name = mysqli_query($link,$sql_emp_name);
					$row_emp_name = mysqli_fetch_assoc($res_emp_name);
					$emp_name = $row_emp_name['emp_name'];
					$dns_emp_code = $row_emp_name['dns_emp_code'];
					
					if(providing_code=='yes')
					{
						echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name." (".$dns_emp_code.") </td></tr>";
					}
					else
					{
						echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name."</td></tr>";
					}
					for ($i=$start_date;$i<=$end_date;$i+=86400)
					{
					    $date = date("Y-m-d", $i);
						$dayofweek = date('w', strtotime($date));
						if($dayofweek==0) $dayname='Sunday';
						if($dayofweek==1) $dayname='Monday';
						if($dayofweek==2) $dayname='Tuesday';
						if($dayofweek==3) $dayname='Wednesday';
						if($dayofweek==4) $dayname='Thursday';
						if($dayofweek==5) $dayname='Friday';
						if($dayofweek==6) $dayname='Saturday';
						${visit_details.strtoupper($dayname).$emp_code}=substr(${visit_details.strtoupper($dayname).$emp_code},0,-1);
						if(${visit_details.strtoupper($dayname).$emp_code} !=''){
						 echo "<tr>
								<td>".date('d-m-Y',strtotime($date))."</td>
								<td>".${visit_details.strtoupper($dayname).$emp_code}."</td>
						   </tr>";
						}
					}
				}
				else
				{
					$no_record=1;
				}
			}
	?>
    </table><br />
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
	if($no_record==1){
		echo "No Records";
	}
mysqli_close($link);
?>


