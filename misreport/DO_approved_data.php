<?php
ob_start();
session_start();
require("adminUtils.php");
$month=gmdate('m',strtotime('+330 minute'));
$date=gmdate('d',strtotime('+330 minute'));
$curryear=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$curr_date=$curryear.'-'.$month.'-'.$date;

$sqlexpirybargaindetails="SELECT DATE_FORMAT(SUBSTRING(sauda_no,-14,8),'%d-%m-%Y') AS bargain_date_done,dns_sauda_no,customer_code,sauda_no,valid_upto 
					FROM DO_master WHERE is_approved='yes'  AND qty >0 
				ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') ASC";	
$resexpirybargaindetails = mysqli_query($link,$sqlexpirybargaindetails);
$totalexpirybargaindetails = mysqli_num_rows($resexpirybargaindetails);
if($totalexpirybargaindetails >0){
	$expiry_bargain_customer_array=array();
	$expiry_bargain_customer_list='';
	while($rowbargaindetails = mysqli_fetch_assoc($resexpirybargaindetails)){
			$bargain_date_done = $rowbargaindetails['bargain_date_done'];
			$dns_sauda_no = $rowbargaindetails['dns_sauda_no'];
			$customer_code = $rowbargaindetails['customer_code'];
			$sauda_no=$rowbargaindetails['sauda_no'];
			$valid_upto_db=$rowbargaindetails['valid_upto'];
			$sqlcustomername="SELECT sauda_validity_period FROM customer_master WHERE customer_code='".$customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
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
			if(strtotime($curr_date) > strtotime($valid_upto_format_chk))
			{
				if(!in_array($customer_code,$expiry_bargain_customer_array))
				{
					$expiry_bargain_customer_list=$expiry_bargain_customer_list."'".$customer_code."'".',';
					array_push($expiry_bargain_customer_array,$customer_code);
				}
			}
	}
	$expiry_bargain_customer_list=substr($expiry_bargain_customer_list,0,-1);
}
if($expiry_bargain_customer_list =='')
{
	$expiry_bargain_customer_cond='';
}
else
{
	$expiry_bargain_customer_cond=" AND DT.customer_code NOT IN(".$expiry_bargain_customer_list.")";
}
/*$sqlDOdetails="SELECT RM.route_name,EM.emp_name,PM.prod_desc,
				DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done ,DT.DO_no,DT.sku_code
				FROM route_master RM,employee_master EM,product_master PM,DO_transaction DT 
				WHERE DT.destination=RM.route_code AND SUBSTRING(DT.DO_no,-19,5)=EM.emp_code AND
				DT.sku_code=PM.prod_code AND DATE_FORMAT(SUBSTRING(DT.sauda_no,-14,14),'%d-%m-%Y %H:%i:%s')=".$sauda_no." 
			   AND DT.customer_code=".$customer_code." AND DT.DO_status=''
				ORDER BY DATE_FORMAT(SUBSTRING(DT.DO_no,-14,14),'%d-%m-%Y %H:%i:%s') DESC";*/
$sqlDOdetails="SELECT RM.route_name,PM.prod_desc,DT.customer_code,CM.customer_name,
				DT.DO_qty,DT.DO_rate,DT.DO_amount,DATE_FORMAT(SUBSTRING(DT.DO_no,-14,8),'%d-%m-%Y') AS DO_date_done ,DT.DO_no,DT.sku_code,DT.sauda_no,DT.dns_DO_no,SUBSTRING(DT.DO_no,3,9) AS emp_code
				FROM route_master RM,product_master PM,DO_transaction DT,customer_master CM WHERE DT.destination=RM.route_code  AND
				DT.sku_code=PM.prod_code AND DT.DO_status='' AND DT.customer_code=CM.customer_code 
				ORDER BY DATE_FORMAT(SUBSTRING(DT.DO_no,-14,14),'%d-%m-%Y %H:%i:%s') ASC";				
$resDOdetails = mysqli_query($link,$sqlDOdetails);
$totalDOdetails = mysqli_num_rows($resDOdetails); 
if($totalDOdetails >0){
		$count = 1;
		?>
         <form name="frm_bargainapprove" method="post" action="adminDOapproved.php" enctype="multipart/form-data" />
        <input type="hidden" name="mode" value="approvedo"/>
        <!--table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="50%">
          <tr class="TDHEAD_SUB">
          	<td colspan="5" align="center">Information of <?php /*echo $customer_name;?></td>
          </tr>
		  <tr class="TDHEAD" align="center">
            <td width="20%">Credit Limit</td>
			<td width="20%">Outstanding</td>
            <td width="15%">Invoices</td>
            <td width="20%">Transport Mode</td>
            <td width="25%">Incoterms</td>
		  </tr>
          <?php
		  echo "<tr>
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
          	<td colspan="13" align="center">DO Information <!--of <?php //echo $customer_name;?> on <?php //echo $sauda_no;?>--></td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="4%">SI</td>
            <td width="7%">DO Date</td>
            <td width="7%">DO No</td>
            <td width="10%">Customer Name</td>
			<td width="10%">Route Name</td>
            <td width="10%">DO Done By</td>
            <td width="10%">Product</td>
            <td width="7%">Qty(CASE)</td>
            <td width="8%">Rate</td>
            <td width="8%">amount</td>
            <td width="19%">Status</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		$DO_no_array=array();
		$DOnocnt=1;
		$DOarray=array();
		while($rowDOdetails = mysqli_fetch_assoc($resDOdetails)){
			$DO_date = $rowDOdetails['DO_date_done'];
			$route_name = $rowDOdetails['route_name'];
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
			$rsempdetails=mysqli_query($link,$sqlempdetails);
			$rowempdetails=mysqli_fetch_assoc($rsempdetails);
			$emp_name = $rowempdetails['emp_name'];
			$customer_name = $rowDOdetails['customer_name'];
			$customer_code = $rowDOdetails['customer_code'];
			$prod_desc = $rowDOdetails['prod_desc'];
			$DO_qty=$rowDOdetails['DO_qty'];
			$DO_rate=$rowDOdetails['DO_rate'];
			$DO_amount=$rowDOdetails['DO_amount'];
			$DO_no=$rowDOdetails['DO_no'];
			$sauda_no=$rowDOdetails['sauda_no'];
			$sku_code=$rowDOdetails['sku_code'];
			$total_qty=$total_qty+$DO_qty;
			$total_amount=$total_amount+$DO_amount;
			$DO_no=$rowDOdetails['DO_no'];
			$dns_DO_no=$rowDOdetails['dns_DO_no'];
			$dns_DO_no=str_replace('DO//','DO/BHD/',$dns_DO_no);
			if(!in_array($dns_DO_no,$DOarray))
			{
				$DOnocnt++;
				array_push($DOarray,$dns_DO_no);
			}
			if($DOnocnt %2==0)  $color='yellow';
			else				   $color='';
			echo "<tr>";
			if(!in_array($DO_no,$DO_no_array))
			{
				echo "<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$DO_date."</td>
					<td style=\"background:$color;\">".$dns_DO_no."</td>
					<td style=\"background:$color;\">".$customer_name."</td>
					<td style=\"background:$color;\">".$route_name."</td>
					<td style=\"background:$color;\">".$emp_name."</td>";
			}
			else
			{
				echo "<td align=\"right\" style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>
					<td style=\"border: none\"></td>";
			}
				echo "<td style=\"background:$color;\">".$prod_desc."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_qty,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_rate,2)."</td>
					<td align=\"right\" style=\"background:$color;\">".number_format($DO_amount,2)."</td>";
					/*<td align=\"center\" width=\"10%\"><input type=\"radio\" name=\"DO_status\" value=\"approved\" onclick=\"update_status('".$DO_no."','".$sku_code."','approved');\">Approved
				<input type=\"radio\" name=\"DO_status\" value=\"reject\" onclick=\"update_status('".$DO_no."','".$sku_code."','reject');\">Reject</td>
				  </tr>";*/
			if(!in_array($DO_no,$DO_no_array))
			{	  
			echo "<td align=\"center\" width=\"10%\" style=\"background:$color;\"><input type=\"radio\" name='do_status_".$DO_no."' value=\"approved\">Approved
				<input type=\"radio\" name='do_status_".$DO_no."' value=\"reject\" >Reject<input type=\"radio\" name='do_status_".$DO_no."' value=\"unselect\" >Unselect</td>";
				$count++;
			}
			else
			{
				echo "<td align=\"center\" width=\"10%\" style=\"border: none\"></td>";
			}
			echo "</tr>";
			echo "<input type=\"hidden\" name=\"dono[]\" value=".$DO_no."><input type=\"hidden\" name=\"sauda_no[]\" value=".$sauda_no."><input type=\"hidden\" name=\"prod_val[]\" value=".$sku_code."><input type=\"hidden\" name=\"qty_val[]\" value=".$qty."><input type=\"hidden\" name=\"customer_code_val[]\" value=".$customer_code.">";		  
			
			array_push($DO_no_array,$DO_no);
		}
		echo "<tr>
					<td align=\"center\" colspan=\"7\"><b>TOTAL</b></td>
					<td align=\"right\"><b>".number_format($total_qty,2)."</b></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".number_format($total_amount,2)."</b></td>
					<td align=\"center\" width=\"10%\"><input type='submit' name='submit1' value=' SAVE '/></td>
				  </tr>";
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>