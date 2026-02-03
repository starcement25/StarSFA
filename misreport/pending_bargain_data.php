<?php

ob_start();

session_start();

require("adminUtils.php");



$customer_code = $_REQUEST['customer_code'];

$start_date = $_REQUEST['start_date'];

$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];

$prod_code = $_REQUEST['prod_code'];
$mod=$_REQUEST['mod'];

/*$sqldnssaudano="SELECT dns_sauda_no FROM DO_master WHERE sauda_no=".$sauda_no."";

$rsdnssaudano=mysqli_query($link,$sqldnssaudano);

$rowdnssaudano=mysqli_fetch_assoc($rsdnssaudano);

$dns_sauda_no=$rowdnssaudano['dns_sauda_no'];
$sqlbargaindetails="SELECT EM.emp_name,EM.state,PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,

				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,

				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,SD.freight_charge,SD.TD

				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 

				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 

				DM.sku_code=PM.prod_code AND DM.customer_code IN(".$customer_code.") AND DM.sku_code IN(".$prod_code.") AND 

				SUBSTRING(SD.sauda_no,3,5) IN(".$employee.")

				AND DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' 

				AND DM.status='no' ORDER BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,EM.state ASC,EM.emp_name ASC";*/
/*$sqlbargaindetails="SELECT EM.emp_name,EM.state,PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,

				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,DM.is_approved,DM.freight_charge,DM.mapped_sku_code,DM.additional_TD,DM.additional_premium FROM employee_master EM,product_master PM,DO_master DM
				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code  AND 
				DM.sku_code=PM.prod_code AND DM.customer_code IN(".$customer_code.") AND DM.sku_code IN(".$prod_code.") AND SUBSTRING(DM.sauda_no,3,5) IN(".$employee.")
				AND DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' 
				AND DM.status='no' AND DM.qty >0 AND DM.is_approved!='reject' ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,EM.state ASC,EM.emp_name ASC";*/
				if($mod=='initial')
				{
					$sql_customer = "SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,DO_master DM 
					WHERE DM.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
					$res_customer = mysqli_query($link,$sql_customer);
					$customer_string='';
					while($row_customer = mysqli_fetch_assoc($res_customer)){
							$customer_string .= "'".$row_customer['customer_code']."',";
						}
					$customer_string = rtrim($customer_string,",");
					$customer_code=$customer_string;
					
					$sqlqueryprod="SELECT dns_prod_code,prod_desc,prod_code FROM product_master WHERE acedns='Y' 
								  AND prod_code IN(SELECT DISTINCT sku_code FROM DO_master) ORDER BY prod_desc ASC";
					$resultqueryprod = mysqli_query($link,$sqlqueryprod);
					$countqueryprod=mysqli_num_rows($resultqueryprod);
					$prod_code_string_val='';
					if($countqueryprod>0){
					while($rowqueryprod = mysqli_fetch_assoc($resultqueryprod))
					{
						$prod_code = $rowqueryprod['prod_code'];
						$prod_code_string_val .= "'".$prod_code."',";
					}
					$prod_code_string_val = rtrim($prod_code_string_val,",");
					}
					$prod_code=$prod_code_string_val;
					$start_date='2020-04-01';
					$end_date=date('Y-m-d');
				}
		$sqlbargaindetails="SELECT PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,DM.is_approved,DM.freight_charge,DM.mapped_sku_code,DM.additional_TD,DM.additional_premium,SUBSTRING(DM.sauda_no,3,9) AS emp_code FROM product_master PM,DO_master DM
				WHERE 
				DM.sku_code=PM.prod_code AND DM.customer_code IN(".$customer_code.") AND DM.sku_code IN(".$prod_code.") 
				AND DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' 
				AND DM.status='no' AND DM.qty >0 AND DM.is_approved!='reject' ORDER BY DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";	
 
$resbargaidetails = mysqli_query($link,$sqlbargaindetails);

$totalbargaidetails = mysqli_num_rows($resbargaidetails);

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

          	<td colspan="16" align="center">Pending Bargain <!--of <?php //echo $customer_name;?> on <?php //echo $dns_sauda_no;?>--></td>

          </tr>

		  <tr class="TDHEAD" align="center">

			<td width="4%">SI</td>

            <td width="8%">Bargain Date</td>

            <td width="4%">Customer Code</td>
			 <td width="10%">Customer Code</td>
            <td width="7%">State</td>

            <td width="9%">Employee</td>

            <td width="7%">Bargain No.</td>

             <td width="9%">Product Category</td>

            <td width="10%">Product Description</td>

            <td width="6%">Bargain Qty</td>

            <td width="6%">Pending Qty</td>

           <td width="6%">UOM1</td>

            <td width="6%">Rate</td>

            <td width="6%">Base Rate</td>

            <td width="6%">Today's Base Rate</td>

            <td width="6%">Difference of Rate</td>

		  </tr>

		<?php

		$total_qty=0;

		$total_amount=0;
		$bargaincnt=1;
		$bargainarray=array();
		while($rowbargaindetails = mysqli_fetch_assoc($resbargaidetails)){

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
			$rsempdetails=mysqli_query($link,$sqlempdetails);
			$rowempdetails=mysqli_fetch_assoc($rsempdetails);

			$emp_name = $rowempdetails['emp_name'];

			$state=$rowempdetails['state'];

			$prod_desc = $rowbargaindetails['prod_desc'];

			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];

			$customer_code = $rowbargaindetails['customer_code'];

			$qty=$rowbargaindetails['qty'];

			$base_sale_rate=$rowbargaindetails['sale_rate'];
			$amount=$rowbargaindetails['amount'];

			$sku_code=$rowbargaindetails['sku_code'];

			$UOM1=$rowbargaindetails['UOM1'];

			$UOM2=$rowbargaindetails['UOM2'];

			$sauda_no=$rowbargaindetails['sauda_no'];
			$mapped_sku_code=$rowbargaindetails['mapped_sku_code'];
			$addiional_TD=$rowbargaindetails['additional_TD'];
			$addiional_premium=$rowbargaindetails['additional_premium'];

			$total_qty=$total_qty+$qty;
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
			$sale_rate=($base_sale_rate+$freight_charge+$TD+$addiional_premium)-$addiional_TD;// For -Td value TD is in addition
			$amount=$sale_rate*$qty;
			$total_amount=$total_amount+$amount;
			$sqlprodgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$rowbargaindetails['product_group_code']."'";

			$rsprodgroupname=mysqli_query($link,$sqlprodgroupname);

			$rowprodgroupname=mysqli_fetch_assoc($rsprodgroupname);

			$product_group_name=$rowprodgroupname['product_group_name'];

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

			$valid_upto = date('d-m-Y',strtotime($sauda_valid_from. '+'.$sauda_validity_period.' days'));

			

			$sqlbargainqty="SELECT qty_MT,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
			$rsbargainqty=mysqli_query($link,$sqlbargainqty);
			$bargain_qty_total=0;
			$total_DO_qty=0;
			$total_DO_qty_case=0;
			while($rowbargainqty=mysqli_fetch_assoc($rsbargainqty))
			{
				$child_sku_code=$rowbargainqty['sku_code'];
				$sqltotalDOqty="SELECT SUM(DO_qty) AS DO_qty_case FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
				$rstotalDOqty=mysqli_query($link,$sqltotalDOqty);
				$rowtotalDOqty=mysqli_fetch_assoc($rstotalDOqty);
				//echo '<br />';
				
				$total_DO_qty_case=$total_DO_qty_case+$rowtotalDOqty['DO_qty_case'];
			}
			$pending_qty=$qty-$total_DO_qty_case;
			$total_pending_qty=$total_pending_qty+$pending_qty;

			if($pending_qty > 0)
			{
			$sqlpublishedrate="SELECT sale_rate,parent_child FROM sauda_mrp WHERE acedns='Y' AND product_code='".$sku_code."' ";						

			$rspublishedrate=mysqli_query($link,$sqlpublishedrate);

			$rowpublishedrate=mysqli_fetch_assoc($rspublishedrate);

			$today_base_rate=$rowpublishedrate['sale_rate'];

			$parent_child=$rowpublishedrate['parent_child'];

			if($parent_child=='child')

			{

				$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$sku_code."' AND acedns='Y'";

				$rsselindustrialrate=mysqli_query($link,$sqlselindustrialrate);

				$rowselindustrialrate=mysqli_fetch_assoc($rsselindustrialrate);

				$cntselindustrialrate=mysqli_num_rows($rsselindustrialrate);

				if($cntselindustrialrate >0)

				{

					$today_base_rate=$rowselindustrialrate['sale_rate'];

				}

			}

			else

			{

				$today_base_rate=$today_base_rate;

			}

			if($today_base_rate =='') $today_base_rate=0;

			//echo $base_sale_rate;

			//echo '<br />';

			//echo $today_base_rate;

			//echo '<br />';
			if(!in_array($dns_sauda_no,$bargainarray))
			{
				$bargaincnt++;
				array_push($bargainarray,$dns_sauda_no);
			}
			$rate_difference=$base_sale_rate-$today_base_rate;
			if($bargaincnt %2==0)  $color='yellow';
			else				   $color='';
			echo "<tr>

					<td align=\"right\" style=\"background:$color;\">".$count."</td>

					<td style=\"background:$color;\">".$bargain_date_done."</td>
					<td style=\"background:$color;\">".$dnscustomercode."</td>
					<td style=\"background:$color;\">".$customer_name."</td>

					<td style=\"background:$color;\">".$state."</td>

					<td style=\"background:$color;\">".$emp_name."</td>

					<td style=\"background:$color;\">".$dns_sauda_no."</td>

					<td style=\"background:$color;\">".$product_group_name."</td>

					<td style=\"background:$color;\">".$prod_desc."</td>

					<td align=\"right\" style=\"background:$color;\">".(($UOM2=='MT')?number_format($qty,3):number_format($qty,2))."</td>

					<td align=\"right\" style=\"background:$color;\">".(($UOM2=='MT')?number_format($pending_qty,3):number_format($pending_qty,2))."</td>

					<td align=\"center\" style=\"background:$color;\">".$UOM1."</td>

					<td align=\"right\" style=\"background:$color;\">".number_format($sale_rate,2)."</td>

					<td align=\"right\" style=\"background:$color;\">".number_format($base_sale_rate,2)."</td>

					<td align=\"right\" style=\"background:$color;\">".number_format($today_base_rate,2)."</td>

					<td align=\"right\" style=\"background:$color;\">".number_format($rate_difference,2)."</td>

					

					";

			$count++;
			}

		}
		if($bargaincnt > 1)
		{
		echo "<tr style=\"height:200 px;\">

					<td align=\"center\" colspan=\"9\"><b>TOTAL</b></td>

					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>

					<td align=\"right\"><b>".number_format($total_pending_qty,2)."</b></td>

					<td align=\"right\"></td>

					<td align=\"right\"></td>

					<td align=\"right\"></td>

					<td align=\"right\"></td>

					<td align=\"center\" ></td>

				  </tr>";
		}

		?>

        </table>

        <br /><br />

    <p>

    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;

    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >

</div>

    </p>

        </form>

        <?php

	}

	else{

		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";

	}

?>