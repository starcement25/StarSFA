<?php
session_start();
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee name and code : ".$sqlemp);
$rowemp=mysqli_fetch_assoc($rsemp);
$emp_name=$rowemp['emp_name'];
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
$tablevalrouteplan='<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="5" align="center"><strong>Route Plan Of '.$emp_name.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="40%" align="left" style="padding-left:20px;">Route Name</td>
                        <td width="20%" align="left" style="padding-left:20px;">Visit date</td>
						<td width="" align="left" style="padding-left:20px;">CHANGE ROUTE PLAN</td>
                    </tr>'; 
					$sqlinformation="SELECT RM.route_name, RM.route_code,DATE_FORMAT(RP.visit_date,'%d-%m-%Y') AS visit_date,RP.route_plan_trans_id FROM 
									route_plan RP,route_master RM WHERE RM.route_code=RP.route_code AND RP.emp_code='".$emp_code."'
									ORDER BY DATE_FORMAT(RP.visit_date,'%Y-%m-%d') ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
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
							$route_name=$rowinformation['route_name'];
							$route_code=$rowinformation['route_code'];
							$visit_date=$rowinformation['visit_date'];
							
							$rowvalrouteplan.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$route_name."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$visit_date."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"routePlanEdit.php?route_code=$route_code&visit_date=$visit_date&trans_id=$trans_id&emp_code=$emp_code\" style=\"color:#930;font-weight:bold;\">CHANGE</a></td>
									  </tr>";
						}
					}
  $tablevalrouteplanend.='</table><br />';

$finalval=$tablevalrouteplan.$rowvalrouteplan.$tablevalrouteplanend;

echo $finalval;
mysqli_close($link);
?>