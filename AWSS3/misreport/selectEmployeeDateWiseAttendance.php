<?php
require("include/config.php");
require("include/dbcon.php");
require("../include/functions.php");

$from_date_pre=$_REQUEST['fromDate'];
$from_date=date('Y-m-d',strtotime($from_date_pre));
$to_date_pre=$_REQUEST['toDate'];	
$to_date=date('Y-m-d',strtotime($to_date_pre));
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
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
$tableval='
<table width="85%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 220;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="11" align="center"><strong>From: '.$from_date_pre.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: '. $to_date_pre.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
		<td width="20%" align="left" style="padding-left:20px;">'.strtoupper($_SESSION['nick_name']).' Emp Code</td>
        <td width="20%" align="left" style="padding-left:20px;">Emp Code</td>
        <td width="25%" align="left" style="padding-left:20px;">Emp Name</td>
		<td width="25%" align="left" style="padding-left:20px;">HQ</td>';
		
		if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){
		$tableval .='<td width="10%" align="left" style="padding-left:20px;">District</td>
		<td width="10%" align="left" style="padding-left:20px;">Designation</td><td width="10%" align="left" style="padding-left:20px;">Vertical</td>';
		}
        $tableval .='<td width="" align="left" style="padding-left:20px;">Days on the Field</td>
		<td width="20%" align="left" style="padding-left:20px;">Active/Inactive</td>
		<td width="" align="left" style="padding-left:20px;">Details</td>
    </tr> ';
        
   		$date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
                        
		$sqlinformation="SELECT EM.emp_name,EM.dns_emp_code,EM.emp_code,EM.HQ,LO.trans_id,EM.acedns,EM.district,EM.designation,EM.vertical_value
						FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
						AND SUBSTRING(EM.emp_code,1,1)!='C'".$emp_hierarchy_condition.$date_condition." GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
		$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$dns_emp_code = $rowinformation['dns_emp_code'];
			$emp_code=$rowinformation['emp_code'];
			$emp_name=$rowinformation['emp_name'];
			$HQ=$rowinformation['HQ'];
			$district=$rowinformation['district'];
			$designation=$rowinformation['designation'];
			$vertical_value=$rowinformation['vertical_value'];
			$acedns=$rowinformation['acedns'];
			if(strtoupper($acedns)=='Y'){
				$active_inactive='ACTIVE';
				$style='style="padding-left:20px; color:#0F0"';
			}
			else {
				$active_inactive='INACTIVE';
				$style='style="padding-left:20px; color:#F00"';
			}
			$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location WHERE trans_id LIKE 'A%' 
			 			  AND emp_code='".$emp_code."' AND DATE_FORMAT(date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."'";
			$rescountatt=mysql_query($sqlcountatt) or die(mysql_error()." Error in select count attendence: ".$sqlcountatt);
			$rowcountatt=mysql_fetch_array($rescountatt);
			$totalattendance=$rowcountatt['totalattendence'];

            
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\">".$cnt++."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;\">".$dns_emp_code."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;\">".$emp_code."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;\">".$emp_name."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;\">".$HQ."</td>";
					if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){
						$rowval .="<td  align=\"left\" style=\"padding-left:20px;\">".$district."</td>
						<td  align=\"left\" style=\"padding-left:20px;\">".$designation."</td><td  align=\"left\" style=\"padding-left:20px;\">".$vertical_value."</td>";
					}
					$rowval.="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;\">".$totalattendance."</td>
					<td align=\"left\" valign=\"top\" $style>".$active_inactive."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','yourchoice','".$from_date."','".$to_date."')\"  style=\"color:#930;font-weight:bold;\">Details</a></td>
              </tr>";
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>