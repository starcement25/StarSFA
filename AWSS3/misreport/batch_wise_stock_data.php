<?php
ob_start();

session_start();

require("adminUtils.php");
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$sqlstockdetails="SELECT PM.prod_desc,PM.dns_prod_code,BW.*
				FROM batch_wise_stock BW,product_master PM
				WHERE PM.dns_prod_code=BW.dns_prod_code
				AND BW.batch_date BETWEEN '".$start_date."' AND '".$end_date."'
				ORDER BY PM.prod_desc ASC,BW.batch_date ASC";		

$resstockdetails = mysql_query($sqlstockdetails);
$totalstockdetails = mysql_num_rows($resstockdetails);
if($totalstockdetails >0){
		$count = 1;
		?>

       <form name="frm_stock" method="post" action="" enctype="multipart/form-data" />
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">

          <tr class="TDHEAD_SUB">
          	<td colspan="20" align="center">Batch wise Stock</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td width="4%">SI</td>
            <td width="8%">Product Code</td>
            <td width="20%">Product Description</td>
            <td width="10%">Batch no</td>
            <td width="10%">Batch date</td>
           <td width="5%">In qty</td>
			<td width="5%">Out qty</td>
            <td width="5%">Closing Stock</td>
		  </tr>
		<?php
		$total_qty=0;
		$total_amount=0;
		$DOcnt=1;
		$DOarray=array();
		while($rowstockdetails = mysql_fetch_array($resstockdetails)){
			$prod_desc = $rowstockdetails['prod_desc'];
			$dns_prod_code = $rowstockdetails['dns_prod_code'];
			$batch_no=$rowstockdetails['batch_no'];
			$batch_date = date('d-m-Y',strtotime($rowstockdetails['batch_date']));
			$in_qty = $rowstockdetails['in_qty'];
			$out_qty=$rowstockdetails['out_qty'];
			$cl_stock=$rowstockdetails['cl_stock'];
			${total_in_qty.$dns_prod_code}=${total_in_qty.$dns_prod_code}+$in_qty;
			${total_out_qty.$dns_prod_code}=${total_out_qty.$dns_prod_code}+$out_qty;
			 if($count > 1 && $previous_dns_prod_code!=$dns_prod_code)
				  {
					  echo "<tr>
					<td align=\"center\" style=\"background:$color;\" colspan=\"5\"><b>Total</b></td>
					<td style=\"background:$color;text-align: right;\" ><b>".${total_in_qty.$previous_dns_prod_code}."</b></td>
					<td style=\"background:$color;text-align: right;\"><b>".${total_out_qty.$previous_dns_prod_code}."</b></td>
					<td style=\"background:$color;text-align: right;\"><b>".(${total_in_qty.$previous_dns_prod_code}-${total_out_qty.$previous_dns_prod_code})
					."</b></td>
					";
				  }
			echo "<tr>
					<td align=\"right\" style=\"background:$color;\">".$count."</td>
					<td style=\"background:$color;\">".$dns_prod_code."</td>
					<td style=\"background:$color;\">".$prod_desc."</td>
					<td style=\"background:$color;\">".$batch_no."</td>
					<td style=\"background:$color;\">".$batch_date."</td>
					<td style=\"background:$color;text-align: right;\" >".$in_qty."</td>
					<td style=\"background:$color;text-align: right;\">".$out_qty."</td>
					<td style=\"background:$color;text-align: right;\">".$cl_stock."</td>
					";
			 if($count==$totalstockdetails)
				  {
					  echo "<tr>
					<td align=\"center\" style=\"background:$color;\" colspan=\"5\"><b>Total</b></td>
					<td style=\"background:$color;text-align: right;\" ><b>".${total_in_qty.$previous_dns_prod_code}."</b></td>
					<td style=\"background:$color;text-align: right;\"><b>".${total_out_qty.$previous_dns_prod_code}."</b></td>
					<td style=\"background:$color;text-align: right;\"><b>".(${total_in_qty.$previous_dns_prod_code}-${total_out_qty.$previous_dns_prod_code})
					."</b></td>
					";
				  }		
			$count++;
			$previous_dns_prod_code=$dns_prod_code;
		}
		/*if($DOcnt >1)
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
		}*/
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