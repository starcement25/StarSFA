<?php
ob_start();
session_start();
require("adminUtils.php");

//$customer_code = $_REQUEST['customer_code'];
//$sauda_no = $_REQUEST['sauda_no'];
$sqldnssaudano="SELECT dns_sauda_no FROM DO_master WHERE sauda_no=".$sauda_no."";
$rsdnssaudano=mysqli_query($link,$sqldnssaudano);
$rowdnssaudano=mysqli_fetch_assoc($rsdnssaudano);
$dns_sauda_no=$rowdnssaudano['dns_sauda_no'];

$sqlcustomername="SELECT customer_name,credit_limit,transport_mode,incoterms,dns_customer_code FROM customer_master WHERE customer_code=".$customer_code."";
$rscustomername=mysqli_query($link,$sqlcustomername);
$rowcustomername=mysqli_fetch_assoc($rscustomername);
$customer_name=$rowcustomername['customer_name'];
$credit_limit=$rowcustomername['credit_limit'];
$transport_mode=$rowcustomername['transport_mode'];
$incoterms=$rowcustomername['incoterms'];
$dns_customer_code=$rowcustomername['dns_customer_code'];

$sqlcustomerbargainlimit="SELECT sauda_limit,pending_qty,(sauda_limit-pending_qty) as balance FROM customer_sauda_limit WHERE customer_code='".$dns_customer_code."'";
$rscustomerbargainlimit=mysqli_query($link,$sqlcustomerbargainlimit);
$rowcustomerbargainlimit=mysqli_fetch_assoc($rscustomerbargainlimit);
$sauda_limit=$rowcustomerbargainlimit['sauda_limit'];
$pending_qty=$rowcustomerbargainlimit['pending_qty'];
$balance=$rowcustomerbargainlimit['balance'];
$sqloutstandingdet="SELECT COUNT(invoice_no) AS tot_invoice_no,SUM(invoice_amount) AS tot_invoice_amount FROM outstanding 
					WHERE customer_code=".$customer_code."";
$rsoutstandingdet=mysqli_query($link,$sqloutstandingdet);
$rowoutstandingdet=mysqli_fetch_assoc($rsoutstandingdet);
$tot_invoice_no=$rowoutstandingdet['tot_invoice_no'];
$tot_invoice_amount=$rowoutstandingdet['tot_invoice_amount'];

/*$sqlbargaindetails="SELECT EM.emp_name,PM.prod_desc,PM.UOM1,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DM.sku_code
				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.sauda_no=".$sauda_no." 
			   AND DM.customer_code=".$customer_code." AND DM.is_approved='no'
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') DESC";*/
$approval_access_array=array();
$edit_access_array=array();				
$sqlapprovalaccess="SELECT operation_type FROM approval_matrix WHERE approval_authority_id='".$_SESSION['admin_login']."' 
					AND LOWER(approval_category)='bargain' and is_active='yes'";
$rsapprovalaccess=mysqli_query($link,$sqlapprovalaccess);
$subcatstring='';
while($rowapprovalaccess=mysqli_fetch_assoc($rsapprovalaccess))
{
	array_push($approval_access_array,$rowapprovalaccess['operation_type']);
	
}
if(strtoupper($_SESSION['admin_login'])!='ADMIN')
{
$sqlapprovalaccessedit="SELECT operation_type,approval_sub_category FROM approval_matrix WHERE approval_authority_id='".$_SESSION['admin_login']."' 
					AND LOWER(approval_category)='bargain' AND UPPER(operation_type)='EDIT'";
$rsapprovalaccessedit=mysqli_query($link,$sqlapprovalaccessedit);
while($rowapprovalaccessedit=mysqli_fetch_assoc($rsapprovalaccessedit))
{
	array_push($edit_access_array,strtoupper($rowapprovalaccessedit['approval_sub_category']));
	$subcatstring=$subcatstring.str_replace(' ','_',$rowapprovalaccessedit['approval_sub_category']).',';
}
}
else
{
	$sqlapprovalaccessedit="SELECT approval_sub_category FROM approval_sub_category WHERE  LOWER(approval_category)='bargain' AND is_active='yes'";
	$rsapprovalaccessedit=mysqli_query($link,$sqlapprovalaccessedit);
	while($rowapprovalaccessedit=mysqli_fetch_assoc($rsapprovalaccessedit))
	{
		$subcatstring=$subcatstring.str_replace(' ','_',$rowapprovalaccessedit['approval_sub_category']).',';
	}
}
$subcatstring=substr($subcatstring,0,-1);

//print_r($edit_access_array);

$sqlbargaindetails="SELECT EM.emp_name,PM.prod_desc,
				DM.qty,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,
				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,SD.freight_charge,SD.TD,SD.premium
				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.is_approved='no' AND 
				DM.sauda_no IN(SELECT DISTINCT approval_category_id FROM approval_trans_log) AND
				DM.sku_code IN(SELECT DISTINCT approval_sub_category_id FROM approval_trans_log) ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";		
$resbargaidetails = mysqli_query($link,$sqlbargaindetails);
$totalbargaidetails = mysqli_num_rows($resbargaidetails);
if($totalbargaidetails >0){
		$count = 1;
		?>
       <form name="frm_bargainapprove" method="post" action="adminBargainApprovedTierwise.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="approvebargaintierwise"/>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="13" align="center">2nd Tier Bargain Approval</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="4%">SI</td>
            <td width="10%">Bargain Date</td>
            <td width="14%">Customer</td>
             <td width="9%">Bargain No</td>
            <td width="13%">Product</td>
            <td width="6%">Qty</td>
            <td width="6%">TD</td>
            <td width="6%">Pemium</td>
            <td width="7%">PO No</td>
            <td width="10%">Validity Period(Days)</td>
            <td width="15%">Operation</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		while($rowbargaindetails = mysqli_fetch_assoc($resbargaidetails)){
			$bargain_date_done = $rowbargaindetails['bargain_date_done'];
			$emp_name = $rowbargaindetails['emp_name'];
			$prod_desc = $rowbargaindetails['prod_desc'];
			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];
			$customer_code = $rowbargaindetails['customer_code'];
			$qty=$rowbargaindetails['qty'];
			$sale_rate=$rowbargaindetails['sale_rate'];
			$freight_charge=$rowbargaindetails['freight_charge'];
			$TD=$rowbargaindetails['TD'];
			$premium=$rowbargaindetails['premium'];
			$sku_code=$rowbargaindetails['sku_code'];
			$UOM1=$rowbargaindetails['UOM1'];
			$sauda_no=$rowbargaindetails['sauda_no'];
			$total_qty=$total_qty+$qty;
			//$total_amount=$total_amount+$amount;
			$PO_no='';
			
			$sqlcustomername="SELECT customer_name,credit_limit,transport_mode,incoterms,dns_customer_code,cust_type,sauda_validity_period FROM customer_master 
								WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			$credit_limit=$rowcustomername['credit_limit'];
			$transport_mode=$rowcustomername['transport_mode'];
			$incoterms=$rowcustomername['incoterms'];
			$dns_customer_code=$rowcustomername['dns_customer_code'];
			$dnscustomercode=$rowcustomername['dns_customer_code'];
			$cust_type=$rowcustomername['cust_type'];
			$validity_period=$rowcustomername['sauda_validity_period'];
			echo "<tr>
					<td align=\"right\">".$count."</td>
					<td>".$bargain_date_done."</td>
					<td>".$customer_name."</td>
					<td>".$dns_sauda_no."</td>
					<td>".$prod_desc."</td>
					<td align=\"right\">";
					if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('QTY',$edit_access_array))
					{
					 echo "<input type=\"text\" name='qty_".$sauda_no."_".$sku_code."' value=\"$qty\" style=\"width:70px;text-align:right;\">";
					}
					else
					{
						 echo $qty;
					}
					echo "</td><td align=\"right\">";
					if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('TD',$edit_access_array))
					{
					echo "<input type=\"text\" name='TD_".$sauda_no."_".$sku_code."' value=\"$TD\" style=\"width:70px;text-align:right;\">";
					}
					else
					{
						 echo $TD;
					}
					echo "</td><td align=\"right\">";
					if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('PREMIUM',$edit_access_array))
					{
					echo "<input type=\"text\" name='premium_".$sauda_no."_".$sku_code."' value=\"$premium\" style=\"width:70px;text-align:right;\">";
					}
					else
					{
						 echo $premium;
					}
					echo "</td><td align=\"right\">";
					if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('PO NO',$edit_access_array))
					{
					echo "<input type=\"text\" name='po_no_".$sauda_no."_".$sku_code."' value=\"\" style=\"width:70px;\">";
					}
					else
					{
						echo $PO_no;
					}
					echo "</td><td align=\"right\">";
					if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('VALIDITY PERIOD',$edit_access_array))
					{
					echo "<input type=\"text\" name='validity_period_".$sauda_no."_".$sku_code."' value=\"$validity_period\" style=\"width:70px;text-align:right;\">";
					}
					else
					{
						echo $validity_period;
					}
					
					echo "</td><td align=\"center\" width=\"10%\">";
				if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('APPROVE',$approval_access_array))
				{
				echo "<input type=\"radio\" name='bargain_status_".$sauda_no."_".$sku_code."' value=\"approved\">Approved";
				}
				if(strtoupper($_SESSION['admin_login'])=='ADMIN' || in_array('REJECT',$approval_access_array))
				{
				echo"<input type=\"radio\" name='bargain_status_".$sauda_no."_".$sku_code."' value=\"reject\" >Reject";
				}
				
				echo "</td></tr>";
			echo "<input type=\"hidden\" name=\"saudano[]\" value=".$sauda_no."><input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"qty_val[]\" value=".$qty."><input type=\"hidden\" name=\"subcat_val\" value=".$subcatstring."><input type=\"hidden\" name=\"customer_code_val[]\" value=".$dnscustomercode.">";	  
			$count++;
		}
		echo "<tr style=\"height:200 px;\">
					<td align=\"center\" colspan=\"5\"><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"center\" width=\"10%\" ><input type='submit' name='submit1' value=' SAVE '/></td>
				  </tr>";
		?>
        </table>
        </form>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>