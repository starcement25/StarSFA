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
$status = $_REQUEST['status'];

/*$sqlbargaindetails="SELECT EM.emp_name,EM.state,PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,

				DM.qty,DM.sale_rate,DM.amount,DATE_FORMAT(SUBSTRING(DM.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,

				DM.sku_code,DM.dns_sauda_no,DM.customer_code,DM.sauda_no,DM.is_approved,SD.freight_charge,SD.TD,SD.VAT

				FROM employee_master EM,product_master PM,DO_master DM,sauda_details SD 

				WHERE SUBSTRING(DM.sauda_no,-19,5)=EM.emp_code AND DM.sauda_no=SD.sauda_no AND DM.sku_code=SD.sku_code AND 

				DM.sku_code=PM.prod_code AND DM.customer_code IN(".$customer_code.") AND DM.sku_code IN(".$prod_code.") AND 

				SUBSTRING(SD.sauda_no,3,5) IN(".$employee.")

				AND DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'

				ORDER BY DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,EM.state ASC,EM.emp_name ASC";*/		
/*$sqlDOdetails="SELECT EM.emp_name,EM.state,PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,PM.dns_prod_code,
				DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DT.sauda_no,DT.sku_code,DT.customer_code,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done,
				DT.dns_DO_no,DT.DO_status,DATE_FORMAT(DT.delivery_date,'%d-%m-%Y') AS delivery_date
				FROM employee_master EM,product_master PM,DO_transaction DT
				WHERE SUBSTRING(DT.DO_no,-19,5)=EM.emp_code  AND 
				DT.sku_code=PM.prod_code AND DT.customer_code IN(".$customer_code.") AND DT.sku_code IN(".$prod_code.") AND SUBSTRING(DT.DO_no,3,5) IN(".$employee.")
				AND DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."'
				ORDER BY DATE_FORMAT(SUBSTRING(DT.DO_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC,EM.state ASC,EM.emp_name ASC";	*/
				if($mod=='initial' || $status=='all')
				{
					$sql_customer = "SELECT DISTINCT CM.customer_code,CM.customer_name FROM customer_master CM,DO_transaction DM 
					WHERE DM.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
					$res_customer = mysql_query($sql_customer);
					$customer_string='';
					while($row_customer = mysql_fetch_array($res_customer)){
							$customer_string .= "'".$row_customer['customer_code']."',";
						}
					$customer_string = rtrim($customer_string,",");
					$customer_code=$customer_string;
					
					$sqlqueryprod="SELECT dns_prod_code,prod_desc,prod_code FROM product_master WHERE acedns='Y' 
								  AND prod_code IN(SELECT DISTINCT sku_code FROM DO_transaction) ORDER BY prod_desc ASC";
					$resultqueryprod = mysql_query($sqlqueryprod);
					$countqueryprod=mysql_num_rows($resultqueryprod);
					$prod_code_string_val='';
					if($countqueryprod>0){
					while($rowqueryprod = mysql_fetch_array($resultqueryprod))
					{
						$prod_code = $rowqueryprod['prod_code'];
						$prod_code_string_val .= "'".$prod_code."',";
					}
					$prod_code_string_val = rtrim($prod_code_string_val,",");
					}
					$prod_code=$prod_code_string_val;
					if($mod=='initial'){
					$start_date='2020-04-01';
					$end_date=date('Y-m-d');
					}
					else
					{
						$start_date=$start_date;
						$end_date=$end_date;
					}
					
				}
				else
				{
					$status_condition=" AND DT.DO_status='$status'";
				}
	$sqlDOdetails="SELECT PM.prod_desc,PM.UOM1,PM.UOM2,PM.product_group_code,PM.dns_prod_code,
				DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,DT.sauda_no,DT.sku_code,DT.customer_code,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done,
				DT.dns_DO_no,DT.DO_status,DATE_FORMAT(DT.delivery_date,'%d-%m-%Y') AS delivery_date,SUBSTRING(DT.DO_no,3,9) AS emp_code
				FROM product_master PM,DO_transaction DT
				WHERE  DT.sku_code=PM.prod_code AND DT.customer_code IN(".$customer_code.") AND DT.sku_code IN(".$prod_code.") 
				AND DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' ".$status_condition."
				ORDER BY DATE_FORMAT(SUBSTRING(DT.DO_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";				

$resDOdetails = mysql_query($sqlDOdetails);
$totalDOdetails = mysql_num_rows($resDOdetails);
if($totalDOdetails >0){
		$count = 1;
		?>

       <form name="frm_bargainapprove" method="post" action="adminDORegister.php" enctype="multipart/form-data" />
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">

          <tr class="TDHEAD_SUB">
          	<td colspan="20" align="center">DO Register <!--of <?php //echo $customer_name;?> on <?php //echo $dns_sauda_no;?>--></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="3%">SI</td>
            <td width="4%">Bargain Date</td>
           	<td width="4%">Bargain No.</td>
            <td width="4%">DO Date</td>
            <td width="4%">DO No.</td>
            <td width="8%">Customer</td>
            <td width="6%">Location</td>
            <td width="6%">State</td>
            <td width="7%">Employee</td>
             <td width="6%">Product Category</td>
            <td width="8%">Product Description</td>
            <td width="4%">DO Qty</td>
           <td width="4%">UOM1</td>
			<td width="4%">UOM2</td>
            <td width="4%">EX/FOR</td>
            <td width="5%">Rate</td>
            <td width="5%">Amount</td>
            <td width="5%">Delivery Date</td>
            <td width="5%">DO Status</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		$DOcnt=1;
		$DOarray=array();
		while($rowDOdetails = mysql_fetch_array($resDOdetails)){
			$bargain_date_done = $rowDOdetails['bargain_date_done'];
			
			$emp_code=$rowDOdetails['emp_code'];
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
			$state=$rowempdetails['state'];
			$prod_desc = $rowDOdetails['prod_desc'];
			$customer_code = $rowDOdetails['customer_code'];
			$dns_DO_no=$rowDOdetails['dns_DO_no'];
			$DO_date_done=$rowDOdetails['DO_date_done'];
			$DO_qty=$rowDOdetails['DO_qty'];
			$DO_rate=$rowDOdetails['DO_rate'];
			$DO_amount=$rowDOdetails['DO_amount'];
			$DO_status=$rowDOdetails['DO_status'];
			$sku_code=$rowDOdetails['sku_code'];
			$UOM1=$rowDOdetails['UOM1'];
			$UOM2=$rowDOdetails['UOM2'];
			$DO_status=$rowDOdetails['DO_status'];
			$delivery_date=$rowDOdetails['delivery_date'];
			$sauda_no=$rowDOdetails['sauda_no'];
			$total_qty=$total_qty+$DO_qty;
			$dns_prod_code=$rowDOdetails['dns_prod_code'];
			$sqlsaudadetails="SELECT dns_sauda_no FROM DO_master WHERE sauda_no='".$sauda_no."'";
			$rssaudadetails=mysql_query($sqlsaudadetails);
			$cntsaudadetails=mysql_num_rows($rssaudadetails);
			if($cntsaudadetails > 0)
			{
			$rowsaudadetails=mysql_fetch_array($rssaudadetails);					
			$dns_sauda_no=$rowsaudadetails['dns_sauda_no'];
			}
			else
			{
				$dns_sauda_no='';			
			}
			$sqlmappedsku="SELECT mapped_prod_code FROM product_unit_coversion_matrix WHERE prod_code='".$dns_prod_code."'";
			$rsmappedsku=mysql_query($sqlmappedsku);
			$rowmappedsku=mysql_fetch_array($rsmappedsku);
			$mapped_sku_code=$rowmappedsku['mapped_prod_code'];
			$sqlsaudadetailsqty="SELECT qty FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code='".$mapped_sku_code."' 
								AND qty >0";
			$rssaudadetailsqty=mysql_query($sqlsaudadetailsqty);
			$cntsaudadetailsqty=mysql_num_rows($rssaudadetailsqty);
			if($cntsaudadetailsqty > 0)
			{
			$rowsaudadetailsqty=mysql_fetch_array($rssaudadetailsqty);					
			$bargain_qty=$rowsaudadetailsqty['qty'];
			}
			else
			{
				$bargain_qty=0;			
			}
			$sqlbargainqty="SELECT qty,sku_code  FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code	='".$mapped_sku_code."'";
			$rsbargainqty=mysql_query($sqlbargainqty);
			$bargain_qty_total=0;
			$total_DO_qty=0;
			while($rowbargainqty=mysql_fetch_array($rsbargainqty))
			{
				$child_sku_code=$rowbargainqty['sku_code'];
				$sqltotalDOqty="SELECT SUM(DO_qty) AS  DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$child_sku_code."'";
				$rstotalDOqty=mysql_query($sqltotalDOqty);
				$rowtotalDOqty=mysql_fetch_array($rstotalDOqty);
				//echo '<br />';
				$total_DO_qty=$total_DO_qty+$rowtotalDOqty['DO_qty'];
			}

			$balance_qty=$bargain_qty-$total_DO_qty;
			//echo '<br />';
			if($balance_qty >=0)
			{
			$total_amount=$total_amount+$DO_amount;
			$sqlprodgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$rowDOdetails['product_group_code']."'";
			$rsprodgroupname=mysql_query($sqlprodgroupname);
			$rowprodgroupname=mysql_fetch_array($rsprodgroupname);
			$product_group_name=$rowprodgroupname['product_group_name'];

			$sqlcustomername="SELECT customer_name,credit_limit,transport_mode,incoterms,dns_customer_code,cust_type,sauda_validity_period,route_code 
								FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysql_query($sqlcustomername);

			$rowcustomername=mysql_fetch_array($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			$credit_limit=$rowcustomername['credit_limit'];
			$transport_mode=$rowcustomername['transport_mode'];
			$incoterms=$rowcustomername['incoterms'];
			$dns_customer_code=$rowcustomername['dns_customer_code'];
			$dnscustomercode=$rowcustomername['dns_customer_code'];
			$cust_type=$rowcustomername['cust_type'];
			$sauda_validity_period=$rowcustomername['sauda_validity_period'];
			$route_code=$rowcustomername['route_code'];

			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroutename=mysql_query($sqlroutename);
			$rowroutename=mysql_fetch_array($rsroutename);
			$route_name=$rowroutename['route_name'];
			if(strtoupper($incoterms)=='EX PLANT')
			{
				$ex_for='EX';
			}
			if(strtoupper($incoterms)=='FOR PLANT')
			{
				$ex_for='FOR';
			}
			$dns_DO_no=str_replace('DO//','DO/BHD/',$dns_DO_no);
			/*$sauda_valid_from = date('d-m-Y',strtotime($bargain_date_done));
			if($sauda_validity_period=='')
			{
				$valid_upto='<b>Wrong Data</b>';
			}
			else
			{
			$valid_upto = date('d-m-Y',strtotime($sauda_valid_from. '+'.$sauda_validity_period.' days'));
			}
			$sqltotalDOqty="SELECT DO_qty FROM DO_transaction WHERE sauda_no='".$sauda_no."' AND sku_code	='".$sku_code."' AND 

							customer_code	='".$customer_code."'";

			$rstotalDOqty=mysql_query($sqltotalDOqty);

			$rowtotalDOqty=mysql_fetch_array($rstotalDOqty);

			$total_DO_qty=$rowtotalDOqty['DO_qty'];

			$pending_qty=$qty-$total_DO_qty;

			$total_pending_qty=$total_pending_qty+$pending_qty;*/
			if(!in_array($dns_DO_no,$DOarray))
			{
				$DOcnt++;
				array_push($DOarray,$dns_DO_no);
			}
			if($DOcnt %2==0)  $color='yellow';
			else				   $color='';
			if($DO_status!='') $DO_status=$DO_status;
			else 				$DO_status='APPROVAL PENDING';
			echo "<tr>
					<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$bargain_date_done."</td>
					<td style=\"background:$color;\">".$dns_sauda_no."</td>
					<td style=\"background:$color;\">".$DO_date_done."</td>
					<td style=\"background:$color;\">".$dns_DO_no."</td>
					<td style=\"background:$color;\">".$customer_name."</td>
					<td style=\"background:$color;\">".$route_name."</td>
					<td style=\"background:$color;\">".$state."</td>
					<td style=\"background:$color;\">".$emp_name."</td>
					<td style=\"background:$color;\">".$product_group_name."</td>
					<td style=\"background:$color;\">".$prod_desc."</td>
					<td align=\"right\" style=\"background:$color;\">".(($UOM2=='MT')?number_format($DO_qty,3):number_format($DO_qty,2))."</td>
					<td align=\"center\" style=\"background:$color;\">".$UOM1."</td>
					<td align=\"center\" style=\"background:$color;\">".$UOM2."</td>
					<td align=\"center\" style=\"background:$color;\">".$ex_for."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_rate,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_amount,2)."</td>
					<td style=\"background:$color;\">".$delivery_date."</td>
					<td style=\"background:$color;\">".strtoupper($DO_status)."</td>
					";
			$count++;
			}
		}
		if($DOcnt >1)
		{
		echo "<tr style=\"height:200 px;\">

					<td align=\"center\" colspan=\"11\"><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_amount,2)."</b></td>
					<td align=\"right\"></td>
					<td ></td>
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