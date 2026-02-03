<?php
session_start();
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$val=$_REQUEST['val'];
if($val=='T')
{
	$date=date('Y-m-d');
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
$tablevalrouteplan='<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="5" align="center"><strong>Route Plan ON '.date('d-m-Y',strtotime($date)).'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="20%" align="left" style="padding-left:20px;">Emp code</td>
                        <td width="30%" align="left" style="padding-left:20px;">Name</td>
                        <td width="" align="left" style="padding-left:20px;">Route</td>
                    </tr>'; 
                        if($date!='')
                        {
                            $date_condition ="  AND DATE_FORMAT(RP.visit_date,'%Y-%m-%d') LIKE '%".$date."%'";
                        }                        
					$sqlinformation="SELECT EM.emp_name,EM.emp_code,RP.route_plan_trans_id,RM.route_name FROM 
									route_plan RP,employee_master EM,route_master RM WHERE RP.emp_code=EM.emp_code 
									AND RM.route_code=RP.route_code ".$date_condition." ORDER BY EM.emp_code ASC ";
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
						$tablevalrouteplan.='<tr><td align="center" colspan="5">No records found.</td></tr>';
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$trans_id=$rowinformation['route_plan_trans_id'];
							$emp_name=$rowinformation['emp_name'];
							$emp_code=$rowinformation['emp_code'];
							$route_name=$rowinformation['route_name'];
							
							$rowvalrouteplan.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_code."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$emp_name."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$route_name."</td>
									  </tr>";
						}
					}
  $tablevalrouteplanend.='</table><br />';

$finalval=$tablevalrouteplan.$rowvalrouteplan.$tablevalrouteplanend;

echo $finalval;
mysql_close($link);
?>