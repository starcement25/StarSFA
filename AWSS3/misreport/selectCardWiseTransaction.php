<?php
require("include/config.php");
require("include/dbcon.php");

$loyalty_card_no=$_REQUEST['card_no'];
$month=$_REQUEST['month'];
$year=$_REQUEST['year'];
$rds_name=$_REQUEST['rds_name'];
$vertical=$_REQUEST['vertical'];

$sqlcardholder="SELECT loyalty_card_holder_name FROM loyalty_card_holder_master WHERE loyalty_card_no LIKE '%".$loyalty_card_no."%'";
$rscardholder=mysql_query($sqlcardholder) or die(mysql_error()." Error in select card holder name and code : ".$sqlcardholder);
$rowcardholder=mysql_fetch_array($rscardholder);
$loyalty_card_holder_name=$rowcardholder['loyalty_card_holder_name'];
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
$tablevalloyalty='<table width="50%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="6" align="center"><strong>Loyalty Transaction Of '.$loyalty_card_holder_name.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="" align="left" style="padding-left:20px;">Date</td>
						<td width="15%" align="left" style="padding-left:20px;">Time</td>
                        <td width="20%" align="left" style="padding-left:20px;">Amount</td>
						<td width="25%" align="left" style="padding-left:20px;">Depot</td>
						<td width="9%" align="left" style="padding-left:20px;">Point</td>
                    </tr>';
					if($rds_name!='all'){
						$rds_condition=" AND CT.rds_code='".$rds_name."'";
					}
					else
					{
						$rds_condition=" ";
					} 
					if($vertical!='all')
					{
						$vertical_condition=" AND CT.trans_type='".$vertical."'";
					}
					else
					{
						$vertical_condition="";
					}
					$sqlinformation="SELECT CT.purchase_value AS amount,LLT.trans_id,DATE_FORMAT(LLT.date,'%d/%m/%Y') AS date
									,DATE_FORMAT(LLT.date,'%T') AS time,RM.rds_name FROM 
									location LLT,card_transaction CT,rds_master RM
									 WHERE LLT.trans_id=CT.transaction_id AND CT.loyalty_card_no LIKE '%".$loyalty_card_no."%' 
									 AND CT.rds_code=RM.rds_code  AND DATE_FORMAT(LLT.date,'%m')='".$month."' AND DATE_FORMAT(LLT.date,'%Y')='".$year."'
									 AND LLT.emp_code!='C0007'".$vertical_condition.$rds_condition." ORDER BY DATE_FORMAT(LLT.date,'%Y-%m-%d') DESC ";
					//exit();				 
					$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysql_num_rows($resinformation);
					if($count==0)
					{
						$tablevalloyalty.='<tr><td align="center" colspan="6">No records found.</td></tr>';
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysql_fetch_array($resinformation))
						{
							$no_transaction=$rowinformation['no_transaction'];
							$amount=$rowinformation['amount'];
							$date=$rowinformation['date'];
							$time=$rowinformation['time'];
							$rds_name=$rowinformation['rds_name'];
							$point=floor($amount/100);
							
							$rowvalloyalty.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$date."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$time."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											".number_format($amount,2)."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$rds_name."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$point."</td>
									  </tr>";
						}
					}
  $tablevalloyaltyend.='</table><br />';

$finalval=$tablevalloyalty.$rowvalloyalty.$tablevalloyaltyend;

echo $finalval;
mysql_close($link);
?>