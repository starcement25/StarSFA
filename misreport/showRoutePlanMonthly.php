<?php
session_start();
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$val=$_REQUEST['val'];
if($val=='MTD')
{
	$headerval=date('F').' ,'.date('Y');
}
if($val=='YTD')
{
	$headerval=(date('Y')-1).' - '.date('Y');
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

$tablevalrouteplan='<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" 
				style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>Route Plan ON '.$headerval.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="" align="left" style="padding-left:20px;">No. of Route Plan</td>
                    </tr>'; 
					if($val=='MTD')
					{
						$date_condition =" AND YEAR(RP.visit_date) = YEAR(CURDATE()) AND MONTH(RP.visit_date) = MONTH(CURDATE())";
					}
					if($val=='YTD')
					{
						//$date_condition =" AND YEAR(RP.visit_date) = YEAR(CURDATE())";
						$date_condition=return_YTD('routeplan');
					}
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,RP.route_plan_trans_id,RM.route_name,COUNT(route_plan_trans_id) AS no_route_plans FROM 
									route_plan RP,employee_master EM,route_master RM WHERE RP.emp_code=EM.emp_code 
									AND RM.route_code=RP.route_code AND RM.rds_code=RP.rds_code ".$emp_hierarchy_condition.$date_condition." GROUP BY EM.emp_code ORDER BY EM.emp_code ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select attendance information monthly: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						$tablevalrouteplan.='<tr><td align="center" colspan="5">No records found.</td></tr>';
						
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysqli_fetch_assoc($resinformation))
						{
							$trans_id=$rowinformation['route_plan_trans_id'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$route_name=$rowinformation['route_name'];
							$totalrouteplan=$rowinformation['no_route_plans'];
							
							$rowvalrouteplan.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											<a href=\"javascript:void(0)\" 
				onClick=\"javascript:showEmployeeWiseRouteplandisplay('".$emp_code."','".$val."','misreport')\"  style=\"color:#930;font-weight:bold;\">".$emp_code."</a></td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$totalrouteplan."</td>
								  </tr>";
						}
					}
  $tablevalrouteplanend.='</table><br /><div id="employeetabledisplay" style="display:none;">
                </div><br />';


$finalval=$tablevalrouteplan.$rowvalrouteplan.$tablevalrouteplanend;

echo $finalval;
mysqli_close($link);
?>