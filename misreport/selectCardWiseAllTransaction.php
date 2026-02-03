<?php
require("include/config.php");
require("include/dbcon.php");

$loyalty_card_no=$_REQUEST['card_no'];
$month_val=$_REQUEST['month'];
$year_val=$_REQUEST['year'];
$rds_name=$_REQUEST['rds_name'];
$vertical=$_REQUEST['vertical'];

$sqlcardholder="SELECT loyalty_card_holder_name FROM loyalty_card_holder_master WHERE loyalty_card_no LIKE '%".$loyalty_card_no."%'";
$rscardholder=mysqli_query($link,$sqlcardholder) or die(mysqli_error()." Error in select card holder name and code : ".$sqlcardholder);
$rowcardholder=mysqli_fetch_assoc($rscardholder);
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
$tablevalloyalty='<table width="45%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 200px;overflow-y: scroll;display:block;" id="todayAttDisplay">
                    <tr class="TDHEAD" > 
                        <td colspan="5" align="center"><strong>Loyalty Transaction Of '.$loyalty_card_holder_name.'</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="5%" align="center">Sl</td>
                        <td width="" align="left" style="padding-left:20px;">Month</td>
						<td width="15%" align="left" style="padding-left:20px;">Count of <br />transaction</td>
                        <td width="20%" align="left" style="padding-left:20px;">Amount</td>
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
					$sqlinformation="SELECT SUM(CT.purchase_value) AS amount,COUNT(LLT.trans_id) AS no_transaction,DATE_FORMAT(LLT.date,'%M') AS month,
									DATE_FORMAT(LLT.date,'%m') AS month_val,DATE_FORMAT(LLT.date,'%Y') AS year FROM 
									location LLT,card_transaction CT
									 WHERE LLT.trans_id=CT.transaction_id AND CT.loyalty_card_no LIKE '%".$loyalty_card_no."%'
									  AND LLT.emp_code!='C0007' ".$vertical_condition.$rds_condition."  
									GROUP BY DATE_FORMAT(LLT.date,'%Y'),DATE_FORMAT(LLT.date,'%m') 
									ORDER BY DATE_FORMAT(LLT.date,'%Y'),DATE_FORMAT(LLT.date,'%m') ASC ";
					$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
					$count=mysqli_num_rows($resinformation);
					if($count==0)
					{
						$tablevalloyalty.='<tr><td align="center" colspan="5">No records found.</td></tr>';
					 }
				else{
						$cnt=$GLOBALS[start]+1;
						while($rowinformation=mysqli_fetch_assoc($resinformation))
						{
							$no_transaction=$rowinformation['no_transaction'];
							$amount=$rowinformation['amount'];
							$month=$rowinformation['month'];
							$month_val=$rowinformation['month_val'];
							$year=$rowinformation['year'];
							$point=floor($amount/100);
							
							$rowvalloyalty.="<tr> 
											<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\"><a href=\"javascript:void(0)\" onClick=\"javascript:showCardWiseSelTransactiondisplay('".$loyalty_card_no."','".$month_val."','".$year."','".$rds_name."','".$vertical."')\"  
											style=\"color:#930;font-weight:bold;\">".$month.",".$year."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$no_transaction."</td>
											<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">
											".number_format($amount,2)."</td>
											<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$point."</td>
									  </tr>";
						}
					}
  $tablevalloyaltyend.='</table><br />';

$finalval=$tablevalloyalty.$rowvalloyalty.$tablevalloyaltyend;

echo $finalval;
mysqli_close($link);
?>