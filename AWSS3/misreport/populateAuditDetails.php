<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$trans_id=$_REQUEST['trans_id'];

$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$_REQUEST['customer_code']."'";
$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select rds name : ".$sqlcustomer);	
$rowcustomer=mysql_fetch_array($rscustomer);
$customer=$rowcustomer['customer_name'];

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
<table width="96%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 150px;overflow-y: scroll;">
    <tr class="TDHEAD" > 
        <td colspan="8" align="center"><strong>Stock Audit Details of  '.$customer.'</strong></td>
    </tr>
   <tr class="TDHEAD_SUB"> 
         <td width="5%" align="center">Sl</td>
		<td width="18%" align="left" style="padding-left:20px;">Brand</td>
		<td width="18%" align="left" style="padding-left:20px;">Brand Form</td>
		<td width="18%" align="left" style="padding-left:20px;">Brand Sub Form</td>
		<td width="22%" align="left" style="padding-left:20px;">Sku</td>
		<td width="10%" align="left" style="padding-left:20px;">Quantity</td>
		<td width="10%" align="left" style="padding-left:20px;">Mrp</td>
		<td width="" align="left" style="padding-left:20px;">Amount</td>
    </tr> '; 
	
			if(no_of_filter==1){
				$sqlauditdetails="SELECT PM.prod_desc,SA.quantity,SA.product_mrp FROM product_master PM,stock_audit SA 
									WHERE SA.product_code=PM.prod_code AND SA.transaction_id='".$trans_id."'";
			}
			if(no_of_filter==2){
				$sqlauditdetails="SELECT PGM.product_group_name,PM.prod_desc,SA.quantity,SA.product_mrp FROM 
									product_master PM,product_group_master PGM,stock_audit SA
									WHERE PM.product_group_code=PGM.product_group_code AND SA.product_code=PM.prod_code AND 
									SA.transaction_id='".$trans_id."'";
			}
			if(no_of_filter==3){
				$sqlauditdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc,SA.quantity,SA.product_mrp FROM 
									product_master PM,product_group_master PGM,product_sub_group_master PSGM,stock_audit SA 
									WHERE PM.product_group_code=PGM.product_group_code 
									AND PM.product_sub_group_code=PSGM.product_sub_group_code 
									AND SA.product_code=PM.prod_code AND 
									SA.transaction_id='".$trans_id."'";
			}
			if(no_of_filter==4){
				$sqlauditdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc,SA.quantity,SA.product_mrp
									FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,
									product_brand_master PBM,stock_audit SA 
									WHERE PM.product_group_code=PGM.product_group_code 
									AND PM.product_sub_group_code=PSGM.product_sub_group_code
									AND PM.product_brand_code=PBM.product_brand_code AND SA.product_code=PM.prod_code AND 
									SA.transaction_id='".$trans_id."'";
			}
		$rsauditdetails=mysql_query($sqlauditdetails) or die(mysql_error()." Error in select audit product details: ".$sqlauditdetails);
		
		 $cnt=$GLOBALS[start]+1;
        while($rowauditdetails=mysql_fetch_array($rsauditdetails))
        {
			$product_group_name=$rowauditdetails['product_group_name'];
			$product_brand_name=$rowauditdetails['product_brand_name'];
			$product_sub_group_name=$rowauditdetails['product_sub_group_name'];
			$prod_desc=$rowauditdetails['prod_desc'];
			$qty=$rowauditdetails['quantity'];
			$mrp=$rowauditdetails['prod_mrp'];
			
			$amount=$mrp*$qtys;
			//$amount=getAmount($basic_rate,$no_pack,$qty,$tax1_rate,$tax2_rate);
				
			$rowval.="<tr> 
						<td valign=\"top\" align=\"center\" style=\"BORDER: #A92A61 1px solid;\">".$cnt++."</td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$product_group_name."</td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$product_sub_group_name."</td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:5px;BORDER: #A92A61 1px solid;\">".$product_brand_name."</td>
						<td align=\"left\" valign=\"top\" style=\"padding-left:5px;BORDER: #A92A61 1px solid;\">".$prod_desc."</td>
						<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$qty."</td>
						<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".$prod_mrp."</td>
						<td align=\"right\" valign=\"top\" style=\"padding-left:20px;BORDER: #A92A61 1px solid;\">".number_format($amount,2)."</td>
					</tr>";
		        
      }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;
mysql_close($link);
?>