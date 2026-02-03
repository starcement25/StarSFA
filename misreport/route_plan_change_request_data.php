<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if($employee == 'all'){
	$emp_condition = '';
}
else{
	$emp_condition = " AND RP.emp_code IN('".$employee."') ";
}

$sqlqueryempname="SELECT emp_name FROM employee_master WHERE emp_code IN (".$employee.")";
$resultempname = mysqli_query($link,$sqlqueryempname);
$rowempname=mysqli_fetch_assoc($resultempname);
$emp_name= $rowempname['emp_name'];
$emp_code = str_replace("'","",$employee);


				?>
                <form name ="frmApprove" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
                <input type="hidden" name="mode" value="approve" />
                <input type="hidden" name="emp_code" value="<?php echo $emp_code;?>" />
                <table class="border" width="100%" style="border-collapse:collapse;" border="1">
                    <tr class="TDHEAD" > 
                        <td colspan="10" align="center"><strong>PJP CHANGE REQUEST DETAILS</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="7%" align="center">Sl no.</td>
                        <td width="10%" align="center">Month</td>
                        <td width="10%" align="center">Boy Name</td>
                        <td width="10%" align="left">Date</td>
                        <td width="10%">Planned PJP Area</td>
                        <td width="10%">Planned PJP Route</td>
                        <td width="10%">Changed PJP Area</td>
                        <td width="10%">Changed PJP Route</td>
                         <td width="10%">Approved By</td>
                        <td width="13%">Remarks</td>
                    </tr> 
                    <?php
						$sqlrouteplan="SELECT RM.route_code,RM.route_name,RM.area,DATE_FORMAT(RP.visit_date,'%d-%m-%Y') 
										AS visit_date,DATE_FORMAT(RP.visit_date,'%d%m%Y') AS visit_date_codehints,RP.route_plan_trans_id,
										RP.remarks,EM.emp_name,RP.change_request,RP.emp_code,RP.approved_by 
										FROM 
									  route_master RM,route_plan RP,employee_master EM WHERE RM.route_code=RP.route_code 
									  AND RP.emp_code=EM.emp_code
									  $emp_condition
									  AND (SUBSTRING(RP.visit_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') 
									  ORDER BY EM.emp_name ASC,RP.visit_date ASC,RP.create_date ASC";
						$rsrouteplan=mysqli_query($link,$sqlrouteplan) or die(mysqli_error()." Error in route plan: ".$sqlrouteplan);
						$countrouteplan=mysqli_num_rows($rsrouteplan);
						if($countrouteplan >0){
						$visit_date_array=array();	
						$visit_date_codehints_array=array();	
						$sl_no_route_plan=0;
						$route_visit_status_prev_array=array();
						$code_hints_array=array();
						while($rowrouteplan=mysqli_fetch_assoc($rsrouteplan))
						{
							$route_code=$rowrouteplan['route_code'];
							$route_name=$rowrouteplan['route_name'];
							$area=$rowrouteplan['area'];
							$visit_date=$rowrouteplan['visit_date'];
							$visit_date_codehints=$rowrouteplan['visit_date_codehints'];
							$change_request=$rowrouteplan['change_request'];
							$emp_code=$rowrouteplan['emp_code'];
							$emp_name=$rowrouteplan['emp_name'];
							$approved_by=$rowrouteplan['approved_by'];
							$remarks=$rowrouteplan['remarks'];
							$route_plan_trans_id=$rowrouteplan['route_plan_trans_id'];
							
							
							
							${visit_date.$visit_date_codehints.$emp_code}=$visit_date;
							${emp_name.$visit_date_codehints.$emp_code}=$emp_name;
							${remarks.$visit_date_codehints.$emp_code}=$remarks;
							
							if($change_request=='no' && ${route_name_planned.$visit_date_codehints.$emp_code}=='')
							{
								${route_name_planned.$visit_date_codehints.$emp_code}=$rowrouteplan['route_name'];
								${area_name_planned.$visit_date_codehints.$emp_code}=$area;
							}
							else if($change_request=='no' && ${route_name_planned.$visit_date_codehints.$emp_code}!='')
							{
								${route_name_planned.$visit_date_codehints.$emp_code}=${route_name_planned.$visit_date_codehints.$emp_code}.','.$rowrouteplan['route_name'];
								if(${area_name_planned.$visit_date_codehints.$emp_code}!=''){
									${area_name_planned.$visit_date_codehints.$emp_code}=${area_name_planned.$visit_date_codehints.$emp_code}.','.$area;
								}
							}
							
							if($change_request=='yes' && ${change_planned.$visit_date_codehints.$emp_code}=='')
							{
								
								${change_planned.$visit_date_codehints.$emp_code}=$rowrouteplan['route_name'];
								${change_area_name.$visit_date_codehints.$emp_code}=$area;
							}
							else if($change_request=='yes' && ${change_planned.$visit_date_codehints.$emp_code}!='')
							{
								${change_planned.$visit_date_codehints.$emp_code}=${change_planned.$visit_date_codehints.$emp_code}.','.$rowrouteplan['route_name'];
								if(${change_area_name.$visit_date_codehints.$emp_code}!=''){
								${change_area_name.$visit_date_codehints.$emp_code}=${change_area_name.$visit_date_codehints.$emp_code}.','.$area;
								}
							}
							$code_hints_string=$visit_date_codehints.'#'.$emp_code;
							if($change_request=='yes' && $approved_by!='')
							{
							$sqlapprovedbyname="SELECT emp_name FROM employee_master WHERE emp_code='".$approved_by."'";
							$rsapprovedbyname=mysqli_query($link,$sqlapprovedbyname);
							$rowapprovedbyname=mysqli_fetch_assoc($rsapprovedbyname);
							${approved_by_name.$visit_date_codehints.$emp_code}=$rowapprovedbyname['emp_name'];
							}
							else ${approved_by_name.$visit_date_codehints.$emp_code}='';
							if(!in_array($code_hints_string,$code_hints_array))
							{
								array_push($code_hints_array,$code_hints_string);
							}
						}
						//print_r($code_hints_array);
						$sl_no_route_plan=1;
						for($i=0;$i <count($code_hints_array);$i++ )
						{
							$code_hints_part_val=explode("#",$code_hints_array[$i]);
							$visit_date_val=$code_hints_part_val[0];
							$emp_code_val=$code_hints_part_val[1];
							$monthval=date('F',strtotime(${visit_date.$visit_date_val.$emp_code_val}));
							?>
                            	<tr> 
                                <td width="7%" align="center"><?php echo $sl_no_route_plan;?></td>
                                <td width="10%" align="center"><?php echo $monthval;?></td>
                                <td width="10%" align="center"><?php echo ${emp_name.$visit_date_val.$emp_code_val};?></td>
                                <td width="10%" align="left"><?php echo ${visit_date.$visit_date_val.$emp_code_val};?></td>
                                <td width="10%"><?php echo ${area_name_planned.$visit_date_val.$emp_code_val};?></td>
                                <td width="10%"><?php echo ${route_name_planned.$visit_date_val.$emp_code_val};?></td>
                                <td width="10%"><?php echo ${change_area_name.$visit_date_val.$emp_code_val};?></td>
                                <td width="10%"><?php echo ${change_planned.$visit_date_val.$emp_code_val};?></td>
                                 <td width="10%"><?php echo ${approved_by_name.$visit_date_val.$emp_code_val};?></td>
                                <td width="13%"><?php echo ${remarks.$visit_date_val.$emp_code_val};?></td>
                                
                                </tr>
						<?php
							$sl_no_route_plan++;
						}
				}
			else
			{?>
				<tr> 
					<td valign="top" align="center"  colspan="4">No route plan founds for this search</td>
				 </tr>
			<?php 
			}
        ?>
            </table></form><br />
            </td>
	</tr>
</table>
<br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
<?php
mysqli_close($link);
?>