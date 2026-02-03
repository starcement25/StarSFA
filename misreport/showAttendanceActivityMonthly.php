<?php

/*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 session_start();
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("../include/functions.php");

$val=$_REQUEST['val'];
$employee = $_REQUEST['employee'];
$atd_report = $_REQUEST['atd_report'];

$emphierarchyval=$_REQUEST['emphierarchyval'];
$state=$_REQUEST['state'];
$state=str_replace("'","",$state);
$emp_type=$_REQUEST['emp_type'];
$emp_type=str_replace("'","",$emp_type);
$page=$_REQUEST['page'];
$modehierarchy=$_REQUEST['modehierarchy'];
$employee_lev_one=$_REQUEST['employee_lev_one'];
$employee_lev_one=str_replace("'","",$employee_lev_one);

if($page=='misreporthierarchy')
{
	$page='misreporthierarchy';
}
else
{
	$page='misreport';
}

if($val=='MTD')
{
	$headerval=date('F').' ,'.date('Y');
}
if($val=='YTD')
{
	$headerval=date('Y');
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

if($atd_report == 'true'){
	$employee = str_replace("^","'",$employee);
	$employee = str_replace("#",",",$employee);
	$emp_hierarchy_condition=' AND LO.emp_code IN('.$employee.') ';
}
$emphierarchyval=$_REQUEST['emphierarchyval'];
if($emphierarchyval !='')
{
	$emp_hierarchy_filterwise = " AND LO.emp_code IN(".$emphierarchyval.") ";
	$emp_hierarchy_condition="";
}
else
{
	$emp_hierarchy_filterwise="";
	$emp_hierarchy_condition=$emp_hierarchy_condition;
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
if(DCR_map=='yes')
{
	$locatestring='<td width="25%" align="left" style="padding-left:20px;" colspan="2">Locate</td>';
}
else
{
	$locatestring='';
}
if(strtoupper($nick_name)=='PALSONS')
{
	$HQstring='<td width="10%" align="left" style="padding-left:20px;">HQ</td>';
}
else $HQstring='';
$tablevalattendance='<table width="60%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
				style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>Attendance On '.$headerval.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>'.$HQstring.'
                        <td width="24%" align="left" style="padding-left:20px;">Days on the Field</td>'.$locatestring.'
                    </tr>'; 
					if($val=='MTD')
					{
						if(sale=='no' && instruction=='yes')
						{
							$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
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
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,LO.latt,LO.longi 
									FROM location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
									 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise."
									 GROUP BY LO.emp_code  ORDER BY EM.emp_code ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information monthly: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						$tablevalattendance.='<tr><td align="center" colspan="5">No records found.</td></tr>';
						
					 }
				else{
						$cnt=$GLOBALS['start']+1;
						while($rowinformation=mysqli_fetch_assoc($resinformation))
						{
							$trans_id=$rowinformation['trans_id'];
							$latt=$rowinformation['latt'];
							$longi=$rowinformation['longi'];
							$time=$rowinformation['time'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$sqlcountatt="SELECT count(emp_code) AS totalattendence FROM location LO WHERE LO.trans_id LIKE 'A%' 
										  AND LO.emp_code='".$emp_code."' ".$date_condition."";
							$rescountatt=mysqli_query($link,$sqlcountatt) or die(mysqli_error()." Error in select count attendence: ".$sqlcountatt);
							$rowcountatt=mysqli_fetch_assoc($rescountatt);
							$totalattendance=$rowcountatt['totalattendence'];
							if(DCR_map=='yes')
							{
								if($latt==0 && $longi==0)
								{
									$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
								}
								else
								{
								$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
																		<a href=\"adminMultiAttendanceLocate.php?emp_code=$emp_code&mode=$val&state=".urlencode($state)."&emp_type=".urlencode($emp_type)."&employee_lev_one=$employee_lev_one&page=$page&modehierarchy=$modehierarchy\" style=\"color:#930;font-weight:bold;\">Locate</a></td>";
								}
							}
							else $locateval='';
							if(strtoupper($nick_name)=='PALSONS')
							{
								$HQval="<td width=\"10%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['HQ']."</td>";
							}
							else $HQval='';

							$rowvalattendance.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"javascript:void(0)\" 
				onClick=\"javascript:showEmployeeWiseAttendacedisplay('".$emp_code."','".$val."','".$page."')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>".$HQval."
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$totalattendance."</td>".$locateval."
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
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="9" align="center"><strong>Activity ON '.$headerval.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="12%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="20%" align="left" style="padding-left:20px;">Name</td>'.$HQstring.'
		<td width="15%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>
		<td width="21%" align="left" style="padding-left:20px;">'.$order_sauda_text.' '.$order_sauda_text_one.' Quantity<br /><span style="padding-left:24px;"></span></td>
		<td width="20%" align="left" style="padding-left:20px;">Order Amount</td>';
		
		if(collection == 'yes'){
			$tablevalactivity .= '<td width="18%" align="left" style="padding-left:20px;">Total Collection<br /><span style="padding-left:20px;">(Rs/-)</span></td>';
		}
		if(strtoupper($nick_name)=='PALSONS')
		{
			$tablevalactivity .= '<td width="17%" align="left" style="padding-left:20px;">Productive <br /><span style="padding-left:20px;">Calls</span></td>';
		}
		$tablevalactivity .= '<td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>'.$stk_audit_TH.'
    </tr> ';
        
		if(check_in_out=='yes' && check_in_out_menu_access=='')
		 {
			$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
				location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(trans_id,1,1) IN
				('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','SU')  
				AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		 }
		 else
		 {
			$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','CI','SU')  
						AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		 }
		$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS['start']+1;
        while($rowinformation=mysqli_fetch_assoc($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			$sqltotalorder="SELECT SUM(OD.qty) AS total_order_received, SUM(OD.amount) AS total_amount_received
							FROM order_details OD,location LO
							WHERE LO.emp_code='".$emp_code."' 
							AND LO.trans_id LIKE 'O%' AND LO.trans_id=OD.order_no ".$date_condition."";
			$rstotalorder=mysqli_query($link,$sqltotalorder) or die(mysqli_error()." Error in total order received: ".$sqltotalorder);
			$rowtotalorder=mysqli_fetch_assoc($rstotalorder);
			
			$sqltotalcollection="SELECT SUM(PD.amount) AS total_collection_received
								FROM payment_details PD,location LO
								WHERE  LO.emp_code='".$emp_code."' 
								AND LO.trans_id LIKE 'P%' AND LO.trans_id=PD.receipt_id ".$date_condition."";
			$rstotalcollection=mysqli_query($link,$sqltotalcollection) or die(mysqli_error()." Error in total collection received: ".$sqltotalcollection);
			$rowtotalcollection=mysqli_fetch_assoc($rstotalcollection);
			
			$sqlnotransaction="SELECT COUNT(LO.trans_id)AS total_no_transaction
								FROM location LO WHERE  LO.emp_code='".$emp_code."' 
								AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$date_condition."";
			$rsnotransaction=mysqli_query($link,$sqlnotransaction) or die(mysqli_error()." Error in total no transaction: ".$sqlnotransaction);
			$rownotransaction=mysqli_fetch_assoc($rsnotransaction);
			if(stk_audit=='yes'){
				$sqlstkaudit="SELECT SUM(SA.quantity)AS total_stk_audit FROM location LO,stock_audit SA 
						WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') AND LO.emp_code='".$emp_code."'".$date_condition."";
				$rsstkaudit=mysqli_query($link,$sqlstkaudit) or die(mysqli_error()." Error in total stk audit: ".$sqlstkaudit);
				$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
				$stk_audit=$rowstkaudit['total_stk_audit'];
				if($stk_audit=='')  $stk_audit=0;
				$stk_audit_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($stk_audit,3)."</td>
";
			}
			if(strtoupper($nick_name)=='PALSONS')
			{
				$HQval="<td width=\"10%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['HQ']."</td>";
			}
			else $HQval='';
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"javascript:void(0)\" onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','".$val."','".$page."')\"  					style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>".$HQval."
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['no_of_visit']."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($rowtotalorder['total_order_received'],3)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowtotalorder['total_amount_received'],2)."</td>";
			
			if(collection == 'yes'){		
				$rowval.= "<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($rowtotalcollection['total_collection_received'],2)."</td>";
			}
				if(strtoupper($nick_name)=='PALSONS')
			{
				$rowval .= "<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".($rowinformation['no_of_visit']-$rownotransaction['total_no_transaction'])."</td>";
			}
			$rowval.= "<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rownotransaction['total_no_transaction']."</td>
              		".$stk_audit_TD."
			  </tr>";
        }
$tablevalend='</table>';	

if($atd_report == 'true'){
	$tablevalactivity = '';
	$rowval = '';
	$tablevalend = '';
}
			
$finalval=$tablevalattendance.$rowvalattendance.$tablevalattendanceend.$tablevalactivity.$rowval.$tablevalend;

echo $finalval;
mysqli_close($link);
?>