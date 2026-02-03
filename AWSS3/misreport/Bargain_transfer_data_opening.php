<?php
ob_start();
session_start();
require("adminUtils.php");

$customer_code = $_REQUEST['customer_code'];
$sauda_no = $_REQUEST['sauda_no'];
$sqldnssaudano="SELECT dns_sauda_no FROM DO_master WHERE sauda_no=".$sauda_no."";
$rsdnssaudano=mysql_query($sqldnssaudano);
$rowdnssaudano=mysql_fetch_array($rsdnssaudano);
$dns_sauda_no=$rowdnssaudano['dns_sauda_no'];

$sqlcustomername="SELECT customer_name,credit_limit,transport_mode,incoterms,dns_customer_code FROM customer_master WHERE customer_code=".$customer_code."";
$rscustomername=mysql_query($sqlcustomername);
$rowcustomername=mysql_fetch_array($rscustomername);
$customer_name=$rowcustomername['customer_name'];
$credit_limit=$rowcustomername['credit_limit'];
$transport_mode=$rowcustomername['transport_mode'];
$incoterms=$rowcustomername['incoterms'];
$dns_customer_code=$rowcustomername['dns_customer_code'];

$sqlbargaindetails="SELECT  PM.product_sub_group_code,PM.prod_desc,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') AS bargain_date_db,DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,DM.mapped_sku_code 
				FROM product_master PM,DO_master DM
				WHERE  DM.mapped_sku_code=PM.dns_prod_code AND DM.sauda_no=".$sauda_no." 
			    AND DM.customer_code=".$customer_code." AND DM.is_approved='yes' AND DM.status='no' 
				AND DM.qty >0 AND DM.dns_sauda_no LIKE 'B/%'";
$resbargaidetails = mysql_query($sqlbargaindetails);
$totalbargaidetails = mysql_num_rows($resbargaidetails);
if($totalbargaidetails >0){
		$count = 1;
		?>
       <form name="frm_bargaintransfer" method="post" action="adminBargainTransferOpening.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="transferbargain"/>

        <!--table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="50%">
          <tr class="TDHEAD_SUB">
          	<td colspan="5" align="center">Information of <?php //echo $customer_name;?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
            <td width="20%">Credit Limit</td>
			<td width="20%">Outstanding</td>
            <td width="15%">Invoices</td>
            <td width="20%">Transport Mode</td>
            <td width="25%">Incoterms</td>
		  </tr>
          <?php
		  /*echo "<tr>
					<td align=\"right\">".number_format($credit_limit,2)."</td>
					<td align=\"right\">".number_format($tot_invoice_amount,2)."</td>
					<td align=\"right\">".$tot_invoice_no."</td>
					<td >".$transport_mode."</td>
					<td >".$incoterms."</td>
			  </tr>";
		  ?>
        </table>
        <br />
        <br />
        <table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="40%">
          <tr class="TDHEAD_SUB">
          	<td colspan="4" align="center">Bargain Limit of <?php echo $customer_name;?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
            <td width="33%">Bargain Limit(MT)</td>
			<td width="33%">Pending Qty(MT)</td>
            <td width="33%">Balance Qty(MT)</td>
		  </tr>
          <?php
		  echo "<tr>
					<td align=\"right\">".number_format($sauda_limit,2)."</td>
					<td align=\"right\">".round($pending_qty,3)."</td>
					<td align=\"right\">".round($balance,3)."</td>
			  </tr>";*/
		  ?>
        </table>
        <br />
        <br /-->
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="50%">
          <tr class="TDHEAD_SUB">
          	<td colspan="13" align="center">Bargain Transfer Entity <!--of <?php //echo $customer_name;?> on <?php //echo $dns_sauda_no;?>--></td>
          </tr>
		  <tr class="TDHEAD" align="center">
            <td width="70%">Product</td>
            <td width="15%">Qty</td>
            <td width="15%">Rate</td>
		  </tr>
		<?php
		$parent_array=array();
		$total_qty=0;
		$total_amount=0;
		while($rowbargaindetails = mysql_fetch_array($resbargaidetails)){
			$bargain_date_done = $rowbargaindetails['bargain_date_done'];
			$prod_desc = $rowbargaindetails['prod_desc'];
			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];
			$customer_code = $rowbargaindetails['customer_code'];
			$qty=$rowbargaindetails['qty'];
			$sale_rate=$rowbargaindetails['sale_rate'];
			$amount=$rowbargaindetails['amount'];
			$sku_code=$rowbargaindetails['sku_code'];
			$sauda_no=$rowbargaindetails['sauda_no'];
			$product_sub_group_code=$rowbargaindetails['product_sub_group_code'];
			$total_qty=$total_qty+$qty;
			$sauda_no = $rowbargaindetails['sauda_no'];
			$mapped_sku_code=$rowbargaindetails['mapped_sku_code'];
			$bargain_date_db = $rowbargaindetails['bargain_date_db'];
			
			$sqlselchildprods="SELECT PM.prod_code,PM.dns_prod_code FROM product_master PM,product_unit_coversion_matrix PUCM WHERE PUCM.prod_code=PM.dns_prod_code AND PUCM.acedns='Y' AND PM.acedns='Y' AND PUCM.mapped_prod_code='".$mapped_sku_code."'";
			$rschildprods=mysql_query($sqlselchildprods);
			while($rowchildprods = mysql_fetch_array($rschildprods))
			{
				$child_prod_code=$rowchildprods['prod_code'];
				$sqltotalDOqty="SELECT DO_qty FROM DO_transaction 
				               WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_prod_code."'";
				$rstotalDOqty=mysql_query($sqltotalDOqty);
				$rowtotalDOqty=mysql_fetch_array($rstotalDOqty);
				$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];
			}
			if($total_DO_qty >0)
			{
				$qty=$qty-$total_DO_qty;	
			}
			else
			{
				$qty=$qty;
			}

			$sqlsubgroupname="SELECT product_sub_group_name FROM product_sub_group_master 
							WHERE product_sub_group_code='".$product_sub_group_code."'";
			$rssubgroupname=mysql_query($sqlsubgroupname);
			$rowsubgroupname=mysql_fetch_array($rssubgroupname);
			$product_sub_group_name=$rowsubgroupname['product_sub_group_name'];				
			if(!in_array($rowbargaindetails['sku_code'],$parent_array))
				{
					if($sale_rate=='' || $sale_rate==0){$text_val='readonly="readonly"';}
						else $text_val='';			
			echo "<tr>
					<td>".$prod_desc."</td>
					<td align=\"right\"><input type=\"text\" name=\"saudaqty_$sku_code\" value=\"$qty\" style=\"width:70px;text-align:right;\" $text_val></td>
					<td align=\"right\">".number_format($sale_rate,2)."</td>
				  </tr>";
					array_push($parent_array,$rowbargaindetails['sku_code']);
				echo"<input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"prodname_val[]\" value=".$prod_desc."><input type=\"hidden\" name=\"parent_qty_val[]\" value=".$qty.">";
				}
			$sqlrelatedparent="SELECT DISTINCT PM.prod_desc,PM.prod_code,PM.dns_prod_code FROM product_master PM,product_unit_coversion_matrix PCM,product_sub_group_master PSGM WHERE PM.product_sub_group_code=PSGM.product_sub_group_code AND PM.dns_prod_code=PCM.mapped_prod_code AND PCM.acedns='Y' AND PM.prod_code <>'".$sku_code."' AND 
			PSGM.product_sub_group_name='".$product_sub_group_name."' AND PM.acedns='Y' ORDER BY PM.prod_desc ASC";
			$resrelatedparent = mysql_query($sqlrelatedparent);
	  		while($rowrelatedparent = mysql_fetch_array($resrelatedparent)){
				$sqlparentrate="SELECT sale_rate FROM DO_master WHERE  product_code='".$rowrelatedparent['prod_code']."' AND sauda_no='".$sauda_no."'";
				$rsparentrate=mysql_query($sqlparentrate);
				$rowparentrate=mysql_fetch_array($rsparentrate);
				$sale_rate_parent=$rowparentrate['sale_rate'];
				if(!in_array($rowrelatedparent['prod_code'],$parent_array))
				{
					$sqlparentexstchk="SELECT qty,sale_rate,sku_code FROM DO_master WHERE 
					sauda_no='".$sauda_no."' AND mapped_sku_code='".$rowrelatedparent['dns_prod_code']."' 
					AND is_approved='yes' AND qty >0";
					$rsparentexstchk=mysql_query($sqlparentexstchk);
					$cntparentexstchk=mysql_num_rows($rsparentexstchk);
					if($cntparentexstchk >0)
					{
					  $rowparentexstchk=mysql_fetch_array($rsparentexstchk);	
					  	if($rowparentexstchk['sale_rate']=='' || $rowparentexstchk['sale_rate']==0){$text_val='readonly="readonly"';}
						else $text_val='';
					
				echo "<tr>
					<td>".$rowrelatedparent['prod_desc']."</td>
					<td align=\"right\"><input type=\"text\" name=\"saudaqty_$rowparentexstchk[sku_code]\" value=\"$rowparentexstchk[qty]\" style=\"width:70px;text-align:right;\" $text_val></td>
					<td align=\"right\">".number_format($rowparentexstchk['sale_rate'],2)."</td>
				  </tr>";
				  echo"<input type=\"hidden\" name=\"prod_val[]\" value=".$rowparentexstchk['sku_code']."><input type=\"hidden\" name=\"prodname_val[]\" value=".$rowrelatedparent['prod_desc']."><input type=\"hidden\" name=\"parent_qty_val[]\" value=".$rowparentexstchk['qty'].">";
					}
					else
					{
						if($sale_rate_parent=='' || $sale_rate_parent==0){$text_val='readonly="readonly"';}
						else $text_val='';
					echo "<tr>
					<td>".$rowrelatedparent['prod_desc']."</td>
					<td align=\"right\"><input type=\"text\" name=\"saudaqty_$rowrelatedparent[prod_code]\" value=\"0\" 
					style=\"width:70px;text-align:right;\" $text_val></td>
					<td align=\"right\">".number_format($sale_rate_parent,2)."</td>
				  </tr>";
				  echo"<input type=\"hidden\" name=\"prod_val[]\" value=".$rowrelatedparent['prod_code']."><input type=\"hidden\" name=\"prodname_val[]\" value=".$rowrelatedparent['prod_desc']."><input type=\"hidden\" name=\"parent_qty_val[]\" value=\"0\">";
					}
				  array_push($parent_array,$rowrelatedparent['prod_code']);
				}
			}
			$count++;
		}
		echo "<tr style=\"height:200 px;\">
					<td align=\"center\" ><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
				  </tr>";
		echo "<tr style=\"height:200 px;\">
					<td align=\"center\" colspan=\"3\" ><input type='submit' name='submit1' value=' TRANSFER '/></td>
				  </tr>";
		echo "<input type=\"hidden\" name=\"saudano\" value=".$sauda_no."><input type=\"hidden\" name=\"dns_sauda_no\" value=".$dns_sauda_no."><input type=\"hidden\" name=\"total_qty\" value=".$total_qty."><input type=\"hidden\" name=\"customer_code\" value=".$customer_code.">";
		  		  
		?>
        </table>
        </form>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>