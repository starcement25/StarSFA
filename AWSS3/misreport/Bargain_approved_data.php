<?php
ob_start();
session_start();
require("adminUtils.php");

//$customer_code = $_REQUEST['customer_code'];
//$sauda_no = $_REQUEST['sauda_no'];
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

$sqlcustomerbargainlimit="SELECT sauda_limit,pending_qty,(sauda_limit-pending_qty) as balance FROM customer_sauda_limit WHERE customer_code='".$dns_customer_code."'";
$rscustomerbargainlimit=mysql_query($sqlcustomerbargainlimit);
$rowcustomerbargainlimit=mysql_fetch_array($rscustomerbargainlimit);
$sauda_limit=$rowcustomerbargainlimit['sauda_limit'];
$pending_qty=$rowcustomerbargainlimit['pending_qty'];
$balance=$rowcustomerbargainlimit['balance'];
$sqloutstandingdet="SELECT COUNT(invoice_no) AS tot_invoice_no,SUM(invoice_amount) AS tot_invoice_amount FROM outstanding 
					WHERE customer_code=".$customer_code."";
$rsoutstandingdet=mysql_query($sqloutstandingdet);
$rowoutstandingdet=mysql_fetch_array($rsoutstandingdet);
$tot_invoice_no=$rowoutstandingdet['tot_invoice_no'];
$tot_invoice_amount=$rowoutstandingdet['tot_invoice_amount'];

/*$sqlbargaindetails="SELECT EM.emp_name,PM.prod_desc,PM.UOM1,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DM.sku_code
				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.sauda_no=".$sauda_no." 
			   AND DM.customer_code=".$customer_code." AND DM.is_approved='no'
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') DESC";*/
/*$sqlbargaindetails="SELECT EM.emp_name,PM.prod_desc,PM.UOM1,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,
				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,SD.freight_charge,SD.TD
				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.is_approved='no'
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";*/
$sqlbargaindetails="SELECT PM.prod_desc,PM.UOM1,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,
				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,SD.freight_charge,SD.TD,SUBSTRING(DM.sauda_no,3,9) AS emp_code
				FROM product_master PM,DO_master DM,sauda_details SD 
				WHERE DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.is_approved='no'
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";						
$resbargaidetails = mysql_query($sqlbargaindetails);
$totalbargaidetails = mysql_num_rows($resbargaidetails);
if($totalbargaidetails >0){
		$count = 1;
		?>
       <form name="frm_bargainapprove" method="post" action="adminBargainApproved.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="approvebargain"/>

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
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="14" align="center">Bargain Information <!--of <?php //echo $customer_name;?> on <?php //echo $dns_sauda_no;?>--></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="3%">SI</td>
            <td width="7%">Bargain Date</td>
            <td width="9%">Customer</td>
            <td width="9%">Bargain Done By</td>
             <td width="6%">Bargain No</td>
            <td width="8%">Product</td>
            <td width="5%">UOM</td>
            <td width="5%">Qty</td>
            <td width="5%">Rate</td>
            <td width="6%">amount</td>
            <td width="6%">PO</td>
            <td width="7%">Remarks</td>
            <td width="16%">Operation</td>
            <td width="8%">Status</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		$bargaincnt=1;
		$bargainarray=array();
		while($rowbargaindetails = mysql_fetch_array($resbargaidetails)){
			$bargain_date_done = $rowbargaindetails['bargain_date_done'];
			$emp_code=$rowbargaindetails['emp_code'];
			if(substr($emp_code,0,1)=='E')
			{
				$emp_code=substr($emp_code,0,5);
				$sqlempdetails="SELECT emp_name,state FROM employee_master WHERE emp_code='".$emp_code."'";
			}
			if(substr($emp_code,0,1)=='C')
			{
				$emp_code=$emp_code;
				$sqlempdetails="SELECT customer_name as emp_name,state_code as state FROM customer_master WHERE customer_code='".$emp_code."'";
			}
			if(substr($emp_code,0,1)=='B')
			{
				$emp_code=substr($emp_code,0,6);
				$sqlempdetails="SELECT broker_name as emp_name,state_code as state FROM broker_master WHERE broker_id='".$emp_code."'";
			}
			$rsempdetails=mysql_query($sqlempdetails);
			$rowempdetails=mysql_fetch_array($rsempdetails);
			
			$emp_name = $rowempdetails['emp_name'];
			$prod_desc = $rowbargaindetails['prod_desc'];
			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];
			$customer_code = $rowbargaindetails['customer_code'];
			$qty=$rowbargaindetails['qty'];
			$sale_rate=$rowbargaindetails['sale_rate'];
			$freight_charge=$rowbargaindetails['freight_charge'];
			$TD=$rowbargaindetails['TD'];
			$sale_rate=$sale_rate+$freight_charge+$TD;// For -Td value TD is in addition
			$amount=$rowbargaindetails['amount'];
			$sku_code=$rowbargaindetails['sku_code'];
			$UOM1=$rowbargaindetails['UOM1'];
			$sauda_no=$rowbargaindetails['sauda_no'];
			$total_qty=$total_qty+$qty;
			$total_amount=$total_amount+$amount;
			
			$sqlremarks="SELECT d_instruction FROM sauda_header WHERE sauda_no='".$sauda_no."'";
			$rsremarks=mysql_query($sqlremarks);
			$rowremarks=mysql_fetch_array($rsremarks);
			$remarks=$rowremarks['d_instruction'];
			
			$sqlcustomername="SELECT customer_name,credit_limit,transport_mode,incoterms,dns_customer_code,cust_type FROM customer_master 
								WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);
			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			$credit_limit=$rowcustomername['credit_limit'];
			$transport_mode=$rowcustomername['transport_mode'];
			$incoterms=$rowcustomername['incoterms'];
			$dns_customer_code=$rowcustomername['dns_customer_code'];
			$dnscustomercode=$rowcustomername['dns_customer_code'];
			$cust_type=$rowcustomername['cust_type'];
			if(!in_array($dns_sauda_no,$bargainarray))
			{
				$bargaincnt++;
				array_push($bargainarray,$dns_sauda_no);
			}
			if($bargaincnt %2==0)  $color='yellow';
			else				   $color='';
			echo "<tr >	
					<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$bargain_date_done."</td>
					<td style=\"background:$color;\">".$customer_name."</td>
					<td style=\"background:$color;\">".$emp_name."</td>
					<td style=\"background:$color;\">".$dns_sauda_no."</td>
					<td style=\"background:$color;\">".$prod_desc."</td>
					<td style=\"background:$color;\">".$UOM1."</td>
					<td align=\"right\" style=\"background:$color;\"><input type=\"text\" name='saudaqty_".$sauda_no."_".$sku_code."' value=\"$qty\" style=\"width:70px;\"></td>
					<td align=\"right\" style=\"background:$color;\">".number_format($sale_rate,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($amount,2)."</td>";
				if(strtoupper($cust_type)=='CORPORATE')
				{
					echo "<td style=\"background:$color;\"><input type=\"file\" name='po_file_".$sauda_no."_".$sku_code."'></td>";
				}
				else
				{
					echo "<td style=\"background:$color;\">&nbsp;</td>";
				}
				echo "<td style=\"background:$color;\">".$remarks."</td>";	
				echo "<td align=\"center\" width=\"10%\" style=\"background:$color;\"><input type=\"radio\" name='bargain_status_".$sauda_no."_".$sku_code."' value=\"approved\">Approved
				<input type=\"radio\" name='bargain_status_".$sauda_no."_".$sku_code."' value=\"reject\" >Reject<input type=\"radio\" name='bargain_status_".$sauda_no."_".$sku_code."' value=\"unselect\" >Unselect</td>";
				
			$sqlbargainstatus="SELECT approval_category_id FROM approval_trans_log WHERE approval_category_id='".$sauda_no."' AND approval_sub_category_id='".$sku_code."'";
			$rsbargainstatus=mysql_query($sqlbargainstatus);
			$cntbargainstatus=mysql_num_rows($rsbargainstatus);
			if($cntbargainstatus==0)
			{
				$bargainstatus='Approval Pending';
			}
			else
			{
				$sqlbargainstatusone="SELECT approval_category_id FROM approval_trans_log WHERE approval_category_id='".$sauda_no."' AND 	approval_sub_category_id='".$sku_code."' 
									AND approval_level_1!='' AND approval_level_2 IS NULL";
				$rsbargainstatusone=mysql_query($sqlbargainstatusone);
				$cntbargainstatusone=mysql_num_rows($rsbargainstatusone);
				if($cntbargainstatusone >0)
				{
					$bargainstatus='Tier 1';
					$sqlbargainstatustwo="SELECT approval_category_id FROM approval_trans_log WHERE approval_category_id='".$sauda_no."' AND approval_sub_category_id='".$sku_code."' 
									 AND approval_level_2 IS NOT NULL";
					$rsbargainstatustwo=mysql_query($sqlbargainstatustwo);
					$cntbargainstatustwo=mysql_num_rows($rsbargainstatustwo);
					if($cntbargainstatustwo >0)
					{
						$bargainstatus='Tier 2';
					}
				}
				else
				{
					$sqlbargainstatustwo="SELECT approval_category_id FROM approval_trans_log WHERE approval_category_id='".$sauda_no."' AND approval_sub_category_id='".$sku_code."'
									 AND approval_level_2 IS NOT NULL";
					$rsbargainstatustwo=mysql_query($sqlbargainstatustwo);
					$cntbargainstatustwo=mysql_num_rows($rsbargainstatustwo);
					if($cntbargainstatustwo >0)
					{
						$bargainstatus='Tier 2';
					}
				}
			}
			echo"<td align=\"center\" width=\"8%\" ><b>$bargainstatus</b></td>";
  
				  echo "</tr>";
			echo "<input type=\"hidden\" name=\"saudano[]\" value=".$sauda_no."><input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"corporate_val[]\" value=".$cust_type."><input type=\"hidden\" name=\"customername_val[]\" value=".str_replace(' ','_',$customer_name)."><input type=\"hidden\" name=\"prodname_val[]\" value=".str_replace(' ','_',$prod_desc)."><input type=\"hidden\" name=\"qty_val[]\" value=".$qty."><input type=\"hidden\" name=\"customer_code_val[]\" value=".$dnscustomercode.">";
			

			$count++;
		}
		echo "<tr style=\"height:200 px;\">
					<td align=\"center\" colspan=\"7\"><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_amount,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"center\" width=\"10%\" ><input type='submit' name='submit1' value=' SAVE '/></td>
					<td align=\"right\"></td>
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