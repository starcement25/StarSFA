<?php
require("include/config.php");
require("include/dbcon.php");

$trans_id=$_REQUEST['trans_id'];
$receipt_id=$trans_id;

$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$_REQUEST['customer_code']."'";
$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer name : ".$sqlcustomer);	
$rowcustomer=mysql_fetch_array($rscustomer);
$customer=$rowcustomer['customer_name'];
$sale_type=$_REQUEST['sale_type'];
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
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;">
    <tr class="TDHEAD" > 
        <td colspan="7" align="center"><strong>Collection Details of  '.$customer.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="23%" align="left" style="padding-left:20px;">Invoice</td>
		<td width="23%" align="left" style="padding-left:20px;">Invoice Amount</td>
		<td width="23%" align="left" style="padding-left:20px;">Due Amount</td>
		<td width="23%" align="left" style="padding-left:20px;">Collected Amount</td>
    </tr> ';
		if($sale_type!='CASH'){
       		$sqlpaymentdetails="SELECT DISTINCT PD.amount,OU.invoice_amount,OU.due_amount,PD.invoice_id FROM payment_details PD LEFT JOIN outstanding OU 
	   						ON PD.invoice_id=OU.invoice_id WHERE PD.receipt_id='".$receipt_id."'";
		}
		else
		{
			$sqlpaymentdetails="SELECT amount,invoice_id FROM payment_details WHERE receipt_id='".$receipt_id."'";
		}
	   $rspaymentdetails=mysql_query($sqlpaymentdetails) or die(mysql_error()." Error in select payment details: ".$sqlpaymentdetails);

		 $cnt=$GLOBALS[start]+1;
        while($rowpaymentdetails=mysql_fetch_array($rspaymentdetails))
        {
				$invoice=$rowpaymentdetails['invoice_id'];
				if($invoice=='')
				{
					$invoice='ON ACCOUNT';
				}
				$invoice_amount=number_format($rowpaymentdetails['invoice_amount'],2);
				$due_amount=number_format($rowpaymentdetails['due_amount'],2);
				$collected_amount=number_format($rowpaymentdetails['amount'],2);
				
				$rowval.="<tr> 
							<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
							<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$invoice."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$invoice_amount."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$due_amount."</td>
							<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$collected_amount."</td>
				  		</tr>";
      }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>