<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("../include/functions.php");

$val=$_REQUEST['val'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if($val=='MTD')
{
	$headerval=date('F').' ,'.date('Y');
}
if($val=='YTD')
{
	$headerval=date('Y');
}
if($val == 'custom'){
	$headerval = "FROM ".date('d-m-Y',strtotime($start_date))." TO ".date('d-m-Y',strtotime($end_date));
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

$tablevalattendance='<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
				style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>Attendance On '.$headerval.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="24%" align="left" style="padding-left:20px;">Days on the Field</td>
                        <td width="25%" align="left" style="padding-left:20px;" colspan="2">Locate</td>
                    </tr>'; 
					if($val=='MTD')
					{
						if(sale=='no' && instruction=='yes')
						{
							$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
						}
						else
						{
							$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
											AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
						}
					}
					if($val=='YTD')
					{
						$date=gmdate('d',strtotime('+330 minute'));
						$month=gmdate('m',strtotime('+330 minute'));
						$year=gmdate('Y',strtotime('+330 minute'));
						
						$hour=gmdate('H',strtotime('+330 minute'));
						$minute=gmdate('i',strtotime('+330 minute'));
						$second=gmdate('s',strtotime('+330 minute'));
								
						if($month>='04'){
							$fiinancial_year=$year.'-04-01';
						}
						else
						{
							$fiinancial_year=($year-1).'-04-01';
						}

						if(sale=='no' && instruction=='yes')
						{
							//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
							$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
										AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
						}
						else
						{
							//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
							$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
										AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
						}
					}
					if($val == 'custom'){
						if(sale=='no' && instruction=='yes')
						{
							$date_condition = " AND (DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
						}
						else
						{
							$date_condition = " AND (DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
						}
						
					}
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time 
									 FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
									 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition."
									 GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select attendance information monthly: ".$sqlinformation);
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
							$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location LO WHERE LO.trans_id LIKE 'A%' 
										  AND LO.emp_code='".$emp_code."' ".$date_condition."";
							$rescountatt=mysql_query($sqlcountatt) or die(mysql_error()." Error in select count attendence: ".$sqlcountatt);
							$rowcountatt=mysql_fetch_array($rescountatt);
							$totalattendance=$rowcountatt['totalattendence'];

							$rowvalattendance.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"javascript:void(0)\" 
				onClick=\"javascript:showEmployeeWiseAttendacedisplay('".$emp_code."','".$val."','".$start_date."','".$end_date."','misreport')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$totalattendance."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"adminMultiAttendanceLocate.php?emp_code=$emp_code&mode=$val&page=misreport&start_date=$start_date&end_date=$end_date\" style=\"color:#930;font-weight:bold;\">Locate</a></td>
									  </tr>";
						}
					}
  $tablevalattendanceend.='</table><br /><div id="employeetabledisplay" style="display:none;">
                </div><br />';

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
$tablevalactivity='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="8" align="center"><strong>Customer Activity ON '.$headerval.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="12%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="20%" align="left" style="padding-left:20px;">Name</td>
		<td width="15%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>'.$DCR_TH.'
    </tr> ';
        
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND SUBSTRING(LO.trans_id,1,2) IN
						('CI') AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			$DCR_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"adminDCRCheckinout.php?emp_code=$emp_code&mode=$val&start_date=$start_date&end_date=$end_date&page=checkinoutreport\"style=\"color:#930;font-weight:bold;\">DCR</a></td>";
			
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['no_of_visit']."</td>
              		".$DCR_TD."
			  </tr>";
        }
$tablevalend='</table>';				
$finalval=$tablevalattendance.$rowvalattendance.$tablevalattendanceend.$tablevalactivity.$rowval.$tablevalend;

echo $finalval;

mysql_close($link);
?>