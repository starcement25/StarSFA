<?php
require("include/config.php");
require("include/config-setup.php");
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
<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="4" align="center"><strong>From: '.$from_date_pre.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: '. $to_date_pre.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td align="center">Sl</td>
		<td align="left" style="padding-left:20px;">Emp code</td>
		<td align="left" style="padding-left:20px;">Name</td>
		<td align="right" style="padding-left:20px;">No. of Survey Done</td>
    </tr> ';
        
   		$date_condition=" AND DATE_FORMAT(LO.date,'%Y-%m-%d') BETWEEN '".$from_date."' AND '".$to_date."' ";
		
                        
		     
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
                    <td align=\"center\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','YC','activity')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['tot_survey']."</td>
             </tr>";
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>