<?php
ob_start();
session_start();
require("adminUtils.php");

$empcode = $_REQUEST['empcode'];
$sdate = $_REQUEST['sdate'];

?>
<input type="hidden" name="emp_code" id="emp_code" value="<?php echo $empcode?>" />
<table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
        <td width="12%">Customer Name</td>
        <td width="7%">State</td>
        <td width="10%">CP Name</td>
        <td width="8%">Beat Name</td>
        <td width="8%">CRE Emp Id</td>
        <td width="8%">Call Duration</td>
        <td width="">Remarks</td>
        <td width="15%">Complain Closer</td>
         <td width="8%">File</td>
      </tr>
      <?php
	$sqlqueryemp="SELECT customer_code,call_duration,recorded_file,call_id,response_type,complain_closer FROM 
				`CRM_transaction` WHERE SUBSTRING(call_id,4,5)='".$empcode."' AND SUBSTRING(call_id,-14,8)='".$sdate."'";
	$resultemp = mysqli_query($link,$sqlqueryemp);
	$countemp=mysqli_num_rows($resultemp);
	if($countemp>0){
		while($row_emp = mysqli_fetch_assoc($resultemp)){
			$cust_code = $row_emp['customer_code'];
			$custname=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name,state_code,rds_tag FROM customer_master WHERE customer_code='".$cust_code."'"));
			$cpname=mysqli_fetch_assoc(mysqli_query($link,"SELECT customer_name FROM customer_master WHERE customer_code='".$custname['rds_tag']."'"));
			$routname=mysqli_fetch_assoc(mysqli_query($link,"SELECT RM.route_name FROM `customer_route_emp_relation` CRE,route_master RM WHERE CRE.route_code=RM.route_code AND CRE.customer_code='".$cust_code."'"));
			
			$sql_remarks="SELECT GROUP_CONCAT(remarks SEPARATOR '#') as remarkes FROM `CRM_customer_feedback` WHERE call_id='".$row_emp['call_id']."'";
			$row_remarkes=mysqli_query($link,$sql_remarks);
			$res_remarkes=mysqli_fetch_assoc($row_remarkes);
			$remarks=explode('#',$res_remarkes['remarkes']);
			
			$call_duration = $row_emp['call_duration'];
			$file = $row_emp['recorded_file'];
			$complain_closer=$row_emp['complain_closer'];
			$call_id = $row_emp['call_id'];
			
				$finalremarks=(($remarks[0])?'1.'.$remarks[0]:'').
					(($remarks[1])?'2.'.$remarks[1]:'').
					(($remarks[2])?'3.'.$remarks[2]:'').
					(($remarks[3])?'4.'.$remarks[3]:'').
					(($remarks[4])?'5.'.$remarks[4]:'').
					(($remarks[5])?'6.'.$remarks[5]:'').
					(($remarks[6])?'7.'.$remarks[6]:'').
					(($remarks[7])?'8.'.$remarks[7]:'');
			if($finalremarks=='' && $row_emp['response_type']=='Wrong No'){
				$finalremarks=$row_emp['response_type'];
			}
			if($complain_closer !=''){
				$complain_closer_TD="<td style=\"display:''\" id=\"complainshow_$call_id\">".$complain_closer."</td>";
			}
			else
			{
				$complain_closer_TD="<td id=\"complain_$call_id\" style=\"display:''\"><input type=\"text\" name=\"complainval_$call_id\"  id=\"complainval_$call_id\" value=\"\" style=\"height:80px;width:135px;\"/>&nbsp;<input type=\"button\" name=\"Addcloser\" value=\"save\" onclick=\"javascript:save_complain('".$call_id."');\"/></td>";
			}
			echo "<tr>
					<td>".$custname['customer_name']."</td>
					<td>".$custname['state_code']."</td>
					<td>".$cpname['customer_name']."</td>
					<td>".$routname['route_name']."</td>
					<td>".$empcode."</td>
					<td>".$call_duration."</td>
					<td>".$finalremarks."</td>".$complain_closer_TD."
					<td><a href=\"http://salesmpower.acedns.in/upload/HALDIRAM/".$file."\" target=\"_blank\" style=\"color:blue\">".$file."</a></td>
				  </tr>";
			}
		}
	else{
		echo "<tr><td colspan=\"2\" align=\"center\" >No Record Found</td></tr>";
	}
	
	?>
    </table>
    <p>
    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display_details');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv('<?php echo $empcode;?>');" >
</div>
    </p>