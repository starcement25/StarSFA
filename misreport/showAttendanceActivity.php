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

if($atd_report == 'true'){
	$employee = str_replace("^","'",$employee);
	$employee = str_replace("#",",",$employee);
	$emp_hierarchy_condition=' AND LO.emp_code IN('.$employee.')';
}
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
	$locatestring='<td width="" align="left" style="padding-left:20px;">Locate</td>';
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
$tablevalattendance='
				<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 60px;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="6" align="center"><strong>Attendance ON '.date('d-m-Y',strtotime($date)).'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>'.$HQstring.'
                        <td width="20%" align="left" style="padding-left:20px;">Time</td>'.$locatestring.'
                    </tr>
				</table>	
				<table width="70%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                   '; 
                       if($date!='' && sale=='no' && instruction=='yes')
						{
							$date_condition ="  AND DATE_FORMAT(LO.date,'%Y-%m-%d') LIKE '%".$date."%'";
						}
						else
						{
							$date_condition ="  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') LIKE '%".$date."%'";
						}                       
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,LO.latt,LO.longi FROM 
									location LO,employee_master EM WHERE LO.emp_code=EM.emp_code 
									AND LO.trans_id LIKE 'A%' AND SUBSTRING(EM.emp_code,1,1)!='C'".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise." ORDER BY EM.emp_code ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
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
							if(DCR_map=='yes')
							{
								if($latt==0 && $longi==0)
								{
									$locateval="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;font-weight:bold;\">GPS TURNED OFF</td>";
								}
								else
								{
								$locateval="<td width=\"\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
																		<a href=\"adminAttendanceLocate.php?trans_id=$trans_id&emp_code=$emp_code&mode=$val&atd_report=$atd_report&state=".urlencode($state)."&emp_type=".urlencode($emp_type)."&employee_lev_one=$employee_lev_one&page=$page&modehierarchy=$modehierarchy\" style=\"color:#930;font-weight:bold;\">Locate</a></td>";
								}
							}
							else $locateval='';
							if(strtoupper($nick_name)=='PALSONS')
							{
								$HQval="<td width=\"10%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['HQ']."</td>";
							}
							else $HQval='';

							$rowvalattendance.="<tr> 
											<td width=\"5%\" valign=\"top\" align=\"center\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td width=\"20%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
											<td width=\"30%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>".$HQval."
											<td width=\"20%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$time."</td>".$locateval."
											
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
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="11" align="center"><strong>Activity ON '.date('d-m-Y',strtotime($date)).'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
		<td width="12%" align="left" style="padding-left:20px;">Emp code</td>
		<td width="16%" align="left" style="padding-left:20px;">Name</td>'.$HQstring.'
		<td width="15%" align="left" style="padding-left:20px;"><span style="padding-left:14px;">No. of </span><br />Customer Visit</td>
		<td width="20%" align="left" style="padding-left:20px;">No of '.$order_sauda_text.' '.$order_sauda_text_one.' <br /><span style="padding-left:24px;"></span></td>
		<td width="20%" align="left" style="padding-left:20px;">Order Amount</td>';
		
		if(collection == 'yes'){
			$tablevalactivity .= '<td width="17%" align="left" style="padding-left:20px;">Total Collection<br /><span style="padding-left:20px;">(Rs/-)</span></td>';
		}
		if(strtoupper($nick_name)=='PALSONS')
		{
			$tablevalactivity .= '<td width="17%" align="left" style="padding-left:20px;">Productive <br /><span style="padding-left:20px;">Calls</span></td>';
		}
		$tablevalactivity .= '<td width="" align="left" style="padding-left:20px;"><span style="padding-left:30px;">No</span><br /> Transaction</td>'.$stk_audit_TH.$DCR_TH.'
    </tr> ';
		if(check_in_out=='yes' && check_in_out_menu_access=='')
		 {
			$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
				location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(trans_id,1,1) IN
				('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','SU')  
				 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise." 
				 GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		 }
		 else
		 {
		$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.HQ,LO.trans_id,count(LO.trans_id) AS no_of_visit FROM 
						location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) AND SUBSTRING(trans_id,1,2) NOT IN('PA','CI','SU')  
						 AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition.$emp_hierarchy_filterwise." 
						 GROUP BY EM.emp_code ORDER BY EM.emp_name ASC ";
		 }
		$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
        $cnt=$GLOBALS['start']+1;
        while($rowinformation=mysqli_fetch_assoc($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
			$operation_type=substr($trans_id,0,1);
			$emp_name=$rowinformation['emp_name'];
			$emp_code=$rowinformation['emp_code'];
			
			  /*if(check_in_out=='yes' && check_in_out_menu_access=='')
				{
					$sql_checkin_out_customer = "SELECT SUBSTRING(trans_id,-19,5) AS emp_code, customer_code, SUBSTRING(trans_id,-14,8) 
													AS ci_date FROM check_in_out_details WHERE 
													".$check_in_out_date_cond." ".$emp_hierarchy_condition_four."
												GROUP BY SUBSTRING(trans_id,-19,5), customer_code, SUBSTRING(trans_id,-14,8)";
					$res_checkin_out_customer = mysqli_query($link,$sql_checkin_out_customer);
				}*/

			if(strtoupper($nick_name)=='VIPL' || strtoupper($nick_name)=='AMW' || strtoupper($nick_name)=='RUPA' || strtoupper($nick_name)=='CDNS' || strtoupper($_SESSION['nick_name'])=='AMPL' || strtoupper($nick_name)=='TT' || strtoupper($nick_name)=='LALANI' || 
			strtoupper($nick_name)=='MDPL' || strtoupper($nick_name)=='RMED' || strtoupper($nick_name)=='LTS' || strtoupper($nick_name)=='EMARK' || strtoupper($nick_name)=='OJBH' || strtoupper($nick_name)=='JPHARMA' || strtoupper($nick_name)=='STAR' 
			|| strtoupper($nick_name)=='MINU' || strtoupper($nick_name)=='PARLE' || strtoupper($nick_name)=='HALDIRAM' || strtoupper($nick_name)=='SKIPPER' || strtoupper($nick_name)=='SHYAM' || strtoupper($nick_name)=='KHMER' || strtoupper($nick_name)=='ARCHITA' || strtoupper($nick_name)=='PRABHUJI' || strtoupper($nick_name)=='DIXCY' || strtoupper($nick_name)=='AJANTA' || strtoupper($nick_name)=='MAITHAN' 
			|| strtoupper($nick_name)=='VCONNECT' || strtoupper($nick_name)=='ASL' || strtoupper($nick_name)=='MUKHAROCHAK' || strtoupper($nick_name)=='NIMBUS' || strtoupper($nick_name)=='PERFUME' || strtoupper($nick_name)=='SUPRA' || strtoupper($nick_name)=='GOLDSTONE' || strtoupper($nick_name)=='DNVFOODS'){
			$DCR_TD="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
					<a href=\"adminDCR.php?emp_code=$emp_code&mode=$val&state=".urlencode($state)."&emp_type=".urlencode($emp_type)."&employee_lev_one=$employee_lev_one&page=$page&modehierarchy=$modehierarchy\"style=\"color:#930;font-weight:bold;\">DCR</a></td>";
			}
			
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
						WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') AND LO.emp_code='".$emp_code."' 
						 ".$date_condition."";
				$rsstkaudit=mysqli_query($link,$sqlstkaudit) or die(mysqli_error()." Error in total stk audit: ".$sqlstkaudit);
				$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
				$stk_audit=$rowstkaudit['total_stk_audit'];
				if($stk_audit=='')  $stk_audit=0;
				$stk_audit_TD="<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".round($stk_audit,3)."</td>
";
			}
            
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>";
			if(stk_audit=='yes' || order=='yes' || collection=='yes' || (check_in_out=='yes' && check_in_out_menu_access=='')){	
            $rowval.="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" 
					onClick=\"javascript:showEmployeeWisedisplay('".$emp_code."','T','".$page."')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>";
			}
			else
			{
			  $rowval.="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>";
			}
			if(strtoupper($nick_name)=='PALSONS')
				{
					$HQval="<td width=\"10%\" align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rowinformation['HQ']."</td>";
				}
				else $HQval='';
            $rowval.="<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>".$HQval."
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
              		".$stk_audit_TD.$DCR_TD."
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