<?php
ob_start();
session_start();
require("adminUtils.php");
$prod_code = $_REQUEST['prod_code'];
$order_no = $_REQUEST['order_no'];
 if(modified_customer_emp_route=='yes'){
	 $sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no,PM.prod_desc,
		OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code
		FROM customer_master CM,customer_route_emp_relation CRR,route_master RM,employee_master EM,product_master PM,order_details OD,
		order_header OH WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CRR.emp_code=EM.emp_code AND
		OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  AND OD.order_no='".$order_no."' AND OD.sku_code='".$prod_code."'";
 }
 else
	 {
$sqlorderdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,RM.route_name,EM.emp_name,CM.phone_no,PM.prod_desc,
	OD.qty,OD.sale_rate,OD.amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.order_no,PM.prod_code
	FROM customer_master CM,route_master RM,employee_master EM,product_master PM,order_details OD,
	order_header OH WHERE CM.route_code=RM.route_code AND CM.emp_code=EM.emp_code AND
	OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND PM.prod_code=OD.sku_code  
	AND OD.order_no='".$order_no."' AND OD.sku_code='".$prod_code."'";
	 }
$resorderdetails = mysql_query($sqlorderdetails);
$roworderdetails = mysql_fetch_array($resorderdetails);
?>	
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmedit" method="post" action="adminOrderEdit.php" >
			<input type="hidden" name="mode" value="edit">	
	
			<table width="60%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Edit Order of "<?php echo $roworderdetails['customer_name'];?>" for product 
                  "<?php echo $roworderdetails['prod_desc'];?>"</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<?php if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Qty<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="qty" id="qty" class="inplogin" style="width:100px;height:20px;" 
                    value="<?php echo $roworderdetails['qty'];?>"/></td>
				</tr>
                <tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Rate<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="sale_rate" id="sale_rate" class="inplogin" style="width:100px;height:20px;" 
                    value="<?php echo $roworderdetails['sale_rate'];?>"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="button" value=" Edit " class="inplogin" onClick="update_result('<?=$prod_code?>','<?=$order_no?>');"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>