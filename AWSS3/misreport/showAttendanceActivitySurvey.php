<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("../include/functions.php");
$val=$_REQUEST['val'];
if($val=='T')
{
	$date=date('Y-m-d');
}
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
}
?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 


$tablevalattendance='
				<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 60px;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="5" align="center"><strong>Attendance ON '.date('d-m-Y',strtotime($date)).'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="8%" align="center">Sl</td>
                        <td width="19%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="29%" align="left" style="padding-left:20px;">Name</td>
                        <td width="19%" align="left" style="padding-left:20px;">Time</td>
                        <td width="" align="left" style="padding-left:20px;">Locate</td>
                    </tr>
				</table>	
				<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                   '; 
                       if($date!='' && sale=='no' && instruction=='yes')
						{
							$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
						}
						else
						{
							$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
						}                       
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code 
									AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C'".$emp_hierarchy_condition.$date_condition." ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
						$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
						
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$rowvalattendance.="<tr> 
											<td width=\"5%\" valign=\"top\" align=\"center\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td width=\"20%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
											<td width=\"30%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td width=\"20%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$time."</td>
											<td width=\"\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"adminAttendanceLocate.php?trans_id=$trans_id&emp_code=$emp_code&mode=$val&page=misreport\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
									  </tr>";
						}
					}
  $tablevalattendanceend.='</table><br />';

if(stk_audit=='yes'){
				$stk_audit_TH='<td width="11%" align="left" style="padding-left:20px;">Stock Audit<br /><span style="padding-left:5px;">(Qty)</span></td>';
			}
			else
			{
				$stk_audit_TH='';
			}
if(need_DCR == 'yes'){
	$DCR_TH='<td width="5%" align="left" style="padding-left:20px;">DCR</td>';
}
if(sauda_allocation=='yes')
{
	$order_sauda_text='Sauda';
	$order_sauda_text_one='Booked';	
}
else
{
	$order_sauda_text='Order';
	$order_sauda_text_one='Received';	
}
$tablevalactivity='
<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="4" align="center"><strong>Activity ON '.date('d-m-Y',strtotime($date)).'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td align="center">Sl</td>
		<td align="left" style="padding-left:20px;">Emp code</td>
		<td align="left" style="padding-left:20px;">Name</td>
		<td align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Survey Done</td>
    </tr> ';
        
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS tot_survey FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'SU%' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','T','misreport')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['tot_survey']."</td>
			  </tr>";
        }
$tablevalend='</table>';				
$finalval=$tablevalattendance.$rowvalattendance.$tablevalattendanceend.$tablevalactivity.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>