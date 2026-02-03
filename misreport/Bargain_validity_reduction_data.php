<?php
ob_start();
session_start();
require("adminUtils.php");

$customer_code = $_REQUEST['customer_code'];
//$sauda_no = $_REQUEST['sauda_no'];


/*$sqlbargaindetails="SELECT EM.emp_name,PM.prod_desc,PM.UOM1,
				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DM.sku_code
				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 
				DM.sku_code=PM.prod_code AND DM.sauda_no=".$sauda_no." 
			   AND DM.customer_code=".$customer_code." AND DM.is_approved='no'
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%d-%m-%Y %H:%i:%s') DESC";*/
$sqlexpirybargaindetails="SELECT EM.emp_name,PM.prod_desc,PM.UOM1,
				DM.qty,DM.qty_MT,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,
				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,DM.freight_charge,DM.valid_upto,DM.mapped_sku_code
				FROM employee_master EM,product_master PM,DO_master DM
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND 
				DM.sku_code=PM.prod_code AND DM.is_approved='yes'  AND DM.qty >0 AND DM.customer_code IN (".$customer_code.") 
				ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC ";	
//exit();					
$resexpirybargaindetails = mysqli_query($link,$sqlexpirybargaindetails);
$totalexpirybargaindetails = mysqli_num_rows($resexpirybargaindetails);
if($totalexpirybargaindetails >0){
		$count = 1;
		?>
       <form name="frm_bargainapprove" method="post" action="adminBargainvlalidityreduction.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="expiryreductionbargain"/>

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
			<td width="4%">SI</td>
            <td width="7%">Bargain Date</td>
            <td width="9%">Customer</td>
            <td width="8%">Bargain Done By</td>
             <td width="7%">Bargain No</td>
            <td width="9%">Product</td>
            <td width="5%">UOM</td>
            <td width="5%">Qty</td>
            <td width="5%">Pending Qty</td>
            <td width="5%">Rate</td>
            <td width="6%">amount</td>
            <td width="7%">Expiry Date</td>
            <td width="7%">Decrease Validity <br />(Days)</td>
            <td width="16%">Status</td>
		  </tr>
		<?php
		$month=gmdate('m',strtotime('+330 minute'));
		$date=gmdate('d',strtotime('+330 minute'));
		$curryear=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$curr_date=$curryear.'-'.$month.'-'.$date;
		$total_qty=0;
		$total_amount=0;
		$bargaincnt=1;
		$bargainarray=array();
		$bargainnoarray=array();
		while($rowbargaindetails = mysqli_fetch_assoc($resexpirybargaindetails)){
			$bargain_date_done = $rowbargaindetails['bargain_date_done'];
			$emp_name = $rowbargaindetails['emp_name'];
			$prod_desc = $rowbargaindetails['prod_desc'];
			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];
			$customer_code = $rowbargaindetails['customer_code'];
			$qty=$rowbargaindetails['qty'];
			$sale_rate=$rowbargaindetails['sale_rate'];
			$base_sale_rate=$rowbargaindetails['sale_rate'];
			$amount=$rowbargaindetails['amount'];
			$sku_code=$rowbargaindetails['sku_code'];
			$UOM1=$rowbargaindetails['UOM1'];
			$sauda_no=$rowbargaindetails['sauda_no'];
			$valid_upto_db=$rowbargaindetails['valid_upto'];
			$mapped_sku_code=$rowbargaindetails['mapped_sku_code'];
			$qty_MT=$rowbargaindetails['qty_MT'];
			$sqlsaudadetails="SELECT freight_charge,VAT,TD FROM sauda_details 
								WHERE sauda_no='".$sauda_no."' AND sku_code='".$sku_code."'";
			$rssaudadetails=mysqli_query($link,$sqlsaudadetails);
			$cntsaudadetails=mysqli_num_rows($rssaudadetails);
			if($cntsaudadetails > 0)
			{
			$rowsaudadetails=mysqli_fetch_assoc($rssaudadetails);					
			$freight_charge=$rowsaudadetails['freight_charge'];
			$VAT=$rowsaudadetails['VAT'];
			$TD=$rowsaudadetails['TD'];
			}
			else
			{
				$freight_charge=$rowbargaindetails['freight_charge'];
				$VAT=0;
				$TD=0;
			}
			$total_qty=$total_qty+$qty;
			$sale_rate=$base_sale_rate+$freight_charge+$TD;// For -Td value TD is in addition
			$amount=$sale_rate*$qty;
			$total_amount=$total_amount+$amount;			
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
			$sauda_validity_period=$rowcustomername['sauda_validity_period'];
			
			$sauda_valid_from = date('d-m-Y',strtotime($bargain_date_done));
			
			if($valid_upto_db=='0000-00-00')
			{
				$valid_upto = date('d-m-Y',strtotime($sauda_valid_from. '+'.$sauda_validity_period.' days'));
				$valid_upto_format=date('Y-m-d',strtotime($valid_upto));
				$valid_upto_format_chk=$valid_upto_format;
			}
			else
			{
				$valid_upto = date('d-m-Y',strtotime($valid_upto_db));
				$valid_upto_format_chk=$valid_upto_db;
			}
			//echo $curr_date;
			if(strtotime($valid_upto_format_chk) > strtotime($curr_date))
			{
				$sqlbargainqty="SELECT qty_MT,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
				$rsbargainqty=mysqli_query($link,$sqlbargainqty);
				$bargain_qty_total=0;
				$total_DO_qty=0;
				$total_DO_qty_case=0;
				while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
				{
					$child_sku_code=$rowbargainqty['sku_code'];
					$sqltotalDOqty="SELECT SUM(DO_qty_MT) AS  DO_qty_MT,SUM(DO_qty) AS DO_qty_case FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
					$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
					$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
					//echo '<br />';
					$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty_MT'];
					$total_DO_qty_case=$total_DO_qty_case+$rowtotalDOqty['DO_qty_case'];
				}
				if(($qty_MT-$total_DO_qty) > 0)
				{
				if($sauda_validity_period=='' && $valid_upto_db=='0000-00-00')
				{
					$valid_upto='<b>Wrong Data</b>';
				}
				else
				{
					$valid_upto=$valid_upto;
				}
			if(!in_array($dns_sauda_no,$bargainarray))
			{
				$bargaincnt++;
				array_push($bargainarray,$dns_sauda_no);
			}
			if($bargaincnt %2==0)  $color='yellow';
			else				   $color='';
			$validity_diff = strtotime($valid_upto_format_chk) - strtotime($curr_date); 
   			$validity_diff= abs(round($validity_diff / 86400)); 
			//exit();
			$option_string='';
			for($i=0;$i<$validity_diff;$i++)
			{
				$option_string.="<option>$i</option>";
			}

			echo "<tr >";
			if(!in_array($sauda_no,$bargainnoarray))
			{	
			   echo"<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$bargain_date_done."</td>
					<td style=\"background:$color;\">".$customer_name."</td>
					<td style=\"background:$color;\">".$emp_name."</td>
					<td style=\"background:$color;\">".$dns_sauda_no."</td>";
			}
			else
			 {
				 echo "<td align=\"right\" style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>";
			 }
				echo"<td style=\"background:$color;\">".$prod_desc."</td>
					<td style=\"background:$color;\">".$UOM1."</td>
					<td align=\"right\" style=\"background:$color;\"><input type=\"text\" name='saudaqty_".$sauda_no."_".$sku_code."' value=\"$qty\" style=\"width:70px;text-align:right;\"></td>
					<td style=\"background:$color;text-align:right;\">".($qty-$total_DO_qty_case)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($sale_rate,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($amount,2)."</td>";
			if(!in_array($sauda_no,$bargainnoarray))
			{			
				echo "<td style=\"background:$color;\">".$valid_upto."</td>
				<td style=\"background:$color;\">
					<select name='vailidity_period_".$sauda_no."'>".$option_string."</select>
				</td>
				<td align=\"center\" width=\"10%\" style=\"background:$color;\"><input type=\"radio\" name='bargain_status_".$sauda_no."' value=\"approved\">Approved
				<input type=\"radio\" name='bargain_status_".$sauda_no."' value=\"reject\" >Reject
				</td>";
				array_push($bargainnoarray,$sauda_no);
			}
			echo "</tr>";
			echo "<input type=\"hidden\" name=\"saudano[]\" value=".$sauda_no."><input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"corporate_val[]\" value=".$cust_type."><input type=\"hidden\" name=\"valid_upto[]\" value=".$valid_upto."><input type=\"hidden\" name=\"prodname_val[]\" value=".str_replace(' ','_',$prod_desc)."><input type=\"hidden\" name=\"qty_val[]\" value=".$qty."><input type=\"hidden\" name=\"customer_code_val[]\" value=".$dnscustomercode.">";	  
			$count++;
			}
			}
		}
		if($bargaincnt >1)
		{
		echo "<tr style=\"height:200 px;\">
					<td align=\"center\" colspan=\"7\"><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_amount,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"center\" width=\"10%\" ><input type='submit' name='submit1' value=' SAVE '/></td>
				  </tr>";
		}
		?>
        </table>
        </form>
        <?php
	}
	if($bargaincnt==1)
		{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
		}
?>