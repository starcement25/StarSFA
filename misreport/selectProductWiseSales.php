<?php
require("include/config.php");
require("include/dbcon.php");

$from_date=$_REQUEST['from_date'];
$to_date=$_REQUEST['to_date'];
$emp_code=$_REQUEST['emp_code'];
$rds_code=$_REQUEST['rds_code'];
$radio_type=$_REQUEST['radio_type'];
$prod_code=$_REQUEST['prod_code'];
$from_date=date('Y-m-d',strtotime($from_date));
$to_date=date('Y-m-d',strtotime($to_date));

$sqlrds="SELECT rds_name FROM rds_master WHERE rds_code='".$rds_code."'";
$rsrds=mysqli_query($link,$sqlrds);
$rowrds=mysqli_fetch_assoc($rsrds);
$rds_name=$rowrds['rds_name'];

$sqlproduct="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
$rsproduct=mysqli_query($link,$sqlproduct);
$rowproduct=mysqli_fetch_assoc($rsproduct);
$prod_desc=$rowproduct['prod_desc'];
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
	$datetoday=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	//For  Today
	if($radio_type=='today')
	{
		$date=date('Y-m-d');
		if($date!='')
		{
			$date_condition =" AND DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') ='".$date."'";
		}
		$heading_val='Of '.date('d-m-Y');
	}
	//For MTD OR Month Today
	if($radio_type=='monthly')
	{
		$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) 
						AND MONTH(DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
		$heading_val='Of '.date('M,Y');				
	}
	//For Custom
	if($radio_type=='yourchoice')
	{
	  $date_condition=" AND DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') >='".$from_date."' AND DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') <='".$to_date."'";
		$heading_val='From '.date('d-m-Y',strtotime($from_date)).' To '.date('d-m-Y',strtotime($to_date));							
	}
	
	if($month>='04'){
		$fiinancial_year=$year.'-04-01';
	}
	else
	{
		$fiinancial_year=($year-1).'-04-01';
	}
$tableval='
<table width="90%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 320px;width: 90%;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="9" align="center"><strong>Transaction details of '.$prod_desc.' of '.$rds_name.' '.$heading_val.' </strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
		<td width="" align="left" style="padding-left:20px;">Transaction Date</td>
		<td width="13%" align="left" style="padding-left:20px;">Transaction Type</td>
		<td width="13%" align="left" style="padding-left:20px;">Qty</td>
		<td width="13%" align="left" style="padding-left:20px;">Sale rate</td>
		<td width="13%" align="left" style="padding-left:20px;">Amount</td>
		<td width="13%" align="left" style="padding-left:20px;">Bill no</td>
		<td width="13%" align="left" style="padding-left:20px;">Bill date</td>
    </tr> ';
       $sqlproducttransaction="SELECT DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%d-%m-%Y') AS transaction_date,OH.transaction_type,
	   							OD.qty,OD.sale_rate,OD.amount AS input_amount,(OD.qty*OD.sale_rate) AS amount,OH.d_instruction 
								FROM order_details OD,order_header OH WHERE OH.order_no=OD.order_no 
								AND OD.sku_code='".$prod_code."' AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$date_condition." 
								ORDER BY DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')"; 	
	   $resproducttransaction=mysqli_query($link,$sqlproducttransaction) or die(mysqli_error()." Error in select product group product: ".$sqlproducttransaction);
	   $cnt=$GLOBALS[start]+1;
		while($rowproducttransaction=mysqli_fetch_assoc($resproducttransaction))
		{
			$transaction_date=$rowproducttransaction['transaction_date'];
			$transaction_type=$rowproducttransaction['transaction_type'];
			$d_instruction=$rowproducttransaction['d_instruction'];
			$qty=$rowproducttransaction['qty'];
			$sale_rate=$rowproducttransaction['sale_rate'];
			$input_amount=$rowproducttransaction['input_amount'];
			if($input_amount==0)
			{
				$amount=$rowproducttransaction['amount'];
			}
			else
			{
				$amount=$input_amount;
			}
			if($d_instruction!='')
			{
				$d_instruction_Array=explode(';',$d_instruction);
				$bill_no=$d_instruction_Array[0];
				$bill_date=$d_instruction_Array[1];
			}
			else
			{
				$bill_no='';
				$bill_date='';
			}
			
			$rowval.="<tr> 
                    <td valign=\"top\" align=\"center\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$cnt++."</td>
                    <td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$transaction_date."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$transaction_type."</td>
                    <td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($qty,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($sale_rate,2)."</td>
					<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($amount,2)."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$bill_no."</td>
					<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$bill_date."</td>
              </tr>";

		}
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysqli_close($link);
?>