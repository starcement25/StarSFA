<?php
ob_start();
session_start();
require("adminUtils.php");

$serdate = date('Ymd',strtotime($_REQUEST['allocation_date']));

?>
<table class="border" width="30%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
        <td>Employee Name</td>
        <td >Number Of Call</td>
         <td >Total Call Duration</td>
      </tr>
      <?php
	$sqlqueryemp="SELECT EM.emp_code,EM.emp_name,COUNT(CT.customer_code) as tot_cnt,SEC_TO_TIME(SUM(TIME_TO_SEC(CT.call_duration))) as tot_duration FROM `CRM_transaction` CT,employee_master EM WHERE SUBSTRING(call_id,4,5)=EM.emp_code AND SUBSTRING(call_id,-14,8)='".$serdate."'  GROUP BY SUBSTRING(call_id,4,5)";
	$resultemp = mysql_query($sqlqueryemp);
	$countemp=mysql_num_rows($resultemp);
	if($countemp>0){
		while($row_emp = mysql_fetch_array($resultemp)){
			$emp_name = $row_emp['emp_name'];
			$emp_code = $row_emp['emp_code'];
			$callcnt = $row_emp['tot_cnt'];
			$tot_duration= $row_emp['tot_duration'];
			echo "<tr>
					<td><a href=\"#\" style=\"color:blue;\" onclick=\"return show_report_details('".$emp_code."')\">".$emp_name."</td>
					<td align=\"right\">".$callcnt."</td>
					<td align=\"right\">".$tot_duration."</td>
				  </tr>";
			}
		}
	else{
		echo "<tr><td colspan=\"3\" align=\"center\" >No Record Found</td></tr>";
	}
	
	?>
    <input type="hidden" name="emp_code" id="emp_code" value="<?php echo $emp_code?>" />
    <input type="hidden" name="serdate" id="serdate" value="<?php echo $serdate?>" />
    </table>