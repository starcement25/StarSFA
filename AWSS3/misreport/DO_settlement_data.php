<?php
ob_start();
session_start();
require("adminUtils.php");
$sqlDOdetails="SELECT RM.route_name,EM.emp_name,PM.prod_desc,PM.dns_prod_code,DT.customer_code,CM.customer_name,
				DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done ,DT.DO_no,DT.sku_code,DT.sauda_no,DT.dns_DO_no,DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done
				FROM route_master RM,employee_master EM,product_master PM,DO_transaction DT,customer_master CM WHERE DT.destination=RM.route_code AND SUBSTRING(DT.DO_no,-19,5)=EM.emp_code AND
				DT.sku_code=PM.prod_code  AND DT.customer_code=CM.customer_code 
				ORDER BY DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";				
$resDOdetails = mysql_query($sqlDOdetails);
$totalDOdetails = mysql_num_rows($resDOdetails);
if($totalDOdetails >0){
		$count = 1;
		?>
         <form name="frm_dosettlement" method="post" action="adminDOSettlement.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="dosettlement"/>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="15" align="center">DO Settlement Information <!--of <?php //echo $customer_name;?> on <?php //echo $sauda_no;?>--></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="3%">SI</td>
          	<td width="4%">Bargain Date</td>
           	<td width="5%">Bargain No.</td>
            <td width="4%">DO Date</td>
            <td width="5%">DO No.</td>
            <td width="10%">Customer</td>
			<td width="10%">Route Name</td>
            <td width="8%">DO Done By</td>
            <td width="10%">Product</td>
            <td width="4%">Bargain Qty</td>
            <td width="4%">DO Qty</td>
            <td width="4%">Balance Qty</td>
            <td width="5%">DO Rate</td>
            <td width="5%">DO amount</td>
            <td width="19%">Status</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		$DO_no_array=array();
		$DOnocnt=1;
		$DOarray=array();
		$bargainskuarray=array();
		$bargainskunoarray=array();
		while($rowDOdetails = mysql_fetch_array($resDOdetails)){
			$DO_date = $rowDOdetails['DO_date_done'];
			$route_name = $rowDOdetails['route_name'];
			$emp_name = $rowDOdetails['emp_name'];
			$customer_name = $rowDOdetails['customer_name'];
			$customer_code = $rowDOdetails['customer_code'];
			$prod_desc = $rowDOdetails['prod_desc'];
			$DO_qty=$rowDOdetails['DO_qty'];
			$DO_rate=$rowDOdetails['DO_rate'];
			$DO_amount=$rowDOdetails['DO_amount'];
			$DO_no=$rowDOdetails['DO_no'];
			$sauda_no=$rowDOdetails['sauda_no'];
			$sku_code=$rowDOdetails['sku_code'];
			$dns_prod_code=$rowDOdetails['dns_prod_code'];
			$total_qty=$total_qty+$DO_qty;
			$total_amount=$total_amount+$DO_amount;
			$DO_no=$rowDOdetails['DO_no'];
			$dns_DO_no=$rowDOdetails['dns_DO_no'];
			$dns_DO_no=str_replace('DO//','DO/BHD/',$dns_DO_no);
			$bargain_date_done=$rowDOdetails['bargain_date_done'];
			
			$sqlmappedsku="SELECT mapped_prod_code FROM product_unit_coversion_matrix WHERE prod_code='".$dns_prod_code."'";
			$rsmappedsku=mysql_query($sqlmappedsku);
			$rowmappedsku=mysql_fetch_array($rsmappedsku);
			$mapped_sku_code=$rowmappedsku['mapped_prod_code'];
			$bargain_mapped_string=$sauda_no.'-'.$mapped_sku_code;
			
			$sqlsaudadetails="SELECT dns_sauda_no,qty FROM DO_master WHERE sauda_no='".$sauda_no."' AND mapped_sku_code='".$mapped_sku_code."' 
								AND qty >0";
			$rssaudadetails=mysql_query($sqlsaudadetails);
			$cntsaudadetails=mysql_num_rows($rssaudadetails);
			if($cntsaudadetails > 0)
			{
			$rowsaudadetails=mysql_fetch_array($rssaudadetails);					
			$dns_sauda_no=$rowsaudadetails['dns_sauda_no'];
			$bargain_qty=$rowsaudadetails['qty'];
			}
			else
			{
				$dns_sauda_no='';
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
			if($balance_qty > 0)
			{
			if(!in_array($bargain_mapped_string,$bargainskuarray))
			{
				$DOnocnt++;
				array_push($bargainskuarray,$bargain_mapped_string);
			}
			if($DOnocnt %2==0)  $color='yellow';
			else				   $color='';
			echo "<tr>";
				echo "<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$bargain_date_done."</td>
					<td style=\"background:$color;\">".$dns_sauda_no."</td>
					<td style=\"background:$color;\">".$DO_date."</td>
					<td style=\"background:$color;\">".$dns_DO_no."</td>
					<td style=\"background:$color;\">".$customer_name."</td>
					<td style=\"background:$color;\">".$route_name."</td>
					<td style=\"background:$color;\">".$emp_name."</td>
					<td style=\"background:$color;\">".$prod_desc."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($bargain_qty,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_qty,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($balance_qty,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_rate,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_amount,2)."</td>";
			if(!in_array($bargain_mapped_string,$bargainskunoarray))
			{	
			echo "<td align=\"center\" width=\"10%\" style=\"background:$color;\"><input type=\"radio\" name='do_status_".$DO_no."_".$sku_code."' value=\"pending\">Pending
				<input type=\"radio\" name=''do_status_".$DO_no."_".$sku_code."' value=\"settlement\" >Settlement<input type=\"radio\" name=''do_status_".$DO_no."_".$sku_code."' value=\"unselect\" >Unselect</td></tr>";
				array_push($bargainskunoarray,$bargain_mapped_string);
			}
			echo "<input type=\"hidden\" name=\"dono[]\" value=".$DO_no."><input type=\"hidden\" name=\"sauda_no[]\" value=".$sauda_no."><input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"qty_val[]\" value=".$qty."><input type=\"hidden\" name=\"customer_code_val[]\" value=".$customer_code.">";		  
			$count++;
			}
		}
		if($DOnocnt > 1)
			{
		echo "<tr>
					<td align=\"center\" colspan=\"9\"><b>TOTAL</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_amount,2)."</b></td>
					<td align=\"center\" width=\"10%\"><input type='submit' name='submit1' value=' SAVE '/></td>
				  </tr>";
			}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>