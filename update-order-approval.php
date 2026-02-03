<?php
ob_start();
	session_start();
	require("userUtils.php");
	$mode = $_REQUEST['mode'];
	if($mode == 'save')						  add_record();
	else    									 disphtml("main();");
ob_end_flush();

function main()
{
	$order_no=$_REQUEST['order_no'];
	$approval=$_REQUEST['approval'];
	$nick_name=$_REQUEST['nick_name'];
	
	if($approval=='approved')
	{
		$approval_related_text="<b>ORDER APPROVED</b>";
	}
	else
	{
		$approval_related_text="<b>ORDER NOT APPROVED</b>";
	}
	//start order email body
	
			$sqlorderheader="SELECT OH.*,DATE_FORMAT(SUBSTRING(OH.order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS order_date 
							 FROM order_header OH WHERE OH.order_no='".$order_no."'";
			$rsorderheader=mysqli_query($link,$sqlorderheader);
			$roworderheader=mysqli_fetch_assoc($rsorderheader);
			
			$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".substr($order_no,1,5)."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];

			$transaction_type_details=$roworderheader['transaction_type'];
			if($transaction_type_details =='SO')$transaction_type_details='Sale Order';
			$order_date=$roworderheader['order_date'];
			$orderdataheader_customer_code=$roworderheader['customer_code'];
			$orderdataheader_TD=$roworderheader['TD'];
			$orderdataheader_d_instruction=$roworderheader['d_instruction'];
			$orderdataheader_sale_type=$roworderheader['sale_type'];

			$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$orderdataheader_customer_code."'";
			$rscustomername=mysqli_query($link,$sqlcustomername);
			$rowcustomername=mysqli_fetch_assoc($rscustomername);
			$customer_name=$rowcustomername['customer_name'];
			
			$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>";
			$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td>";
			
			$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>";
			
			if(sale_rate=='yes')
			{
				$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>Sale Rate</span></strong></th>";
			}
			else if(mrp=='yes')
			{
				$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>MRP</span></strong></th>";
			}
			else
			{
				$mrp_sale_rate_TR='';
			}
			
			if(TD=='yes' && TD_type=='sku wise')
			{
				$orderemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>TD</span></strong></th>";
				$orderemailbody_TD_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
						<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";		
			}
			else 
			{
				$orderemailbody_TD_TH="";
				$orderemailbody_TD_total="";
			}
			if(VAT=='yes')
			{
				$orderemailbody_VAT_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
						<span style='font-size:10pt;font-family:Arial CE'>VAT</span></strong></th>";
				$orderemailbody_VAT_total="<td style='width:50px;min-height:21px;text-align:right'><strong>
						<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";		
			}
			else 
			{
				$orderemailbody_VAT_TH="";
				$orderemailbody_VAT_total="";
			}
			if(no_of_filter==1){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
									<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
			}
			if(no_of_filter==2){
				$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
									<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
									<th style='width:60px;min-height:21px;text-align:center'><strong>
									<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
			}
			if(no_of_filter==3){
			  $product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
			}
			if(no_of_filter==4){
				$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col3."</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
			}
			$product_details_colspan=no_of_filter;

 			$orderemailbody = "<html><head><title>Order approval</title></head>
							<body>".$approval_related_text."<br /><b>Refference no: ".$order_no."</b><br /><br />
							<b>Order received by ".$emp_name."</b><br /><br />
							<table border=1 style=background-color:AliceBlue>
								<tr>".$orderheader_TR."
									<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
									CE'>Date & Time</span></strong></th>
									<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
									CE'>Transaction Type</span></strong></th>
								</tr><tr>".$orderheader_TD."
									<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$order_date."</span>&nbsp;</td>
									<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$transaction_type_details."</span>&nbsp;</td>
								</tr>
							</table><br />
							<table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
									<th style='width:60px;min-height:21px;text-align:center'><strong>
									<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
									".$mrp_sale_rate_TR.$orderemailbody_TD_TH."
									<th style='width:60px;min-height:21px;text-align:center'><strong>
									<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$orderemailbody_VAT_TH." 	
								</tr>";

			
			$sqlorderdetails="SELECT OD.* FROM order_details OD WHERE OD.order_no='".$order_no."'";
			$rsorderdetails=mysqli_query($link,$sqlorderdetails);
			while($roworderdetails=mysqli_fetch_assoc($rsorderdetails))
			{
				$orderdatadetails_sku_code=$roworderdetails['sku_code'];
				$orderdatadetails_qty=$roworderdetails['qty'];
				$orderdatadetails_TD=$roworderdetails['TD'];
				$orderdatadetails_VAT=$roworderdetails['VAT'];
				$orderdatadetails_mrp_code=$roworderdetails['mrp_code'];
				$orderdatadetails_order_no=$roworderdetails['order_no'];
				$orderdatadetails_amount=$roworderdetails['amount'];
				$orderdatadetails_sale_rate=$roworderdetails['sale_rate'];
			
				if(no_of_filter==1){
					$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$orderdatadetails_sku_code."'";
				}
				if(no_of_filter==2){
					$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM product_master PM,product_group_master PGM 
										WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
				}
				if(no_of_filter==3){
					$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc FROM 
										product_master PM,product_group_master PGM,product_sub_group_master PSGM
										WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
										AND PM.prod_code='".$orderdatadetails_sku_code."'";
				}
				if(no_of_filter==4){
					$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc
										FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
										WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
										AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
				}
				$rsproductdetails=mysqli_query($link,$sqlproductdetails);
				$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
				$prod_desc=$rowproductdetails['prod_desc'];
				$product_group_name=$rowproductdetails['product_group_name'];
				$product_brand_name=$rowproductdetails['product_brand_name'];
				$product_sub_group_name=$rowproductdetails['product_sub_group_name'];			
				if(no_of_filter==1){
					$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
				}
				if(no_of_filter==2){
					$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
				}
				if(no_of_filter==3){
					$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
				}
				if(no_of_filter==4){
					$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$product_brand_name."</span>&nbsp;</td>
									<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
				}
				$sqlmrpdetails="SELECT mrp,sale_rate,UOM FROM mrp WHERE product_code='".$orderdatadetails_sku_code."' 
								AND mrp_code='".$orderdatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				$mrp=$recmrpdetails['mrp'];
				$sale_rate_db=$recmrpdetails['sale_rate'];
				$UOM=$recmrpdetails['UOM'];
				
				 /*if(mrp=='yes')
				 {
					$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp,2)."</span>&nbsp;</td>";
				 }
				 else if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
				 {
					 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($sale_rate_db,2)."</span>&nbsp;</td>";
				 }*/
				 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_sale_rate,2)."</span>&nbsp;</td>";
				 if(TD=='yes' && TD_type=='sku wise')
				 {
					$orderemailbody_TD_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
						<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_TD,2)."</span>&nbsp;</td>";
				 }
				 else
				 {
					 $orderemailbody_TD_VAL="";
				 }
				 if(VAT=='yes')
				 {
					$orderemailbody_VAT_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>".number_format(round($orderdatadetails_VAT,2),2)."</span>&nbsp;</td>";
				 }
				 else
				 {
					 $orderemailbody_VAT_VAL="";
				 }
				 
				 if(mrp=='yes' && TD=='yes' && TD_type=='sku wise'){
					if(TD_calc=='percentage')
					{
						$totalmrp=($orderdatadetails_qty*$mrp)-((($orderdatadetails_qty*$mrp)*$orderdatadetails_TD)/100);
					}
					else
					{
						$totalmrp=($orderdatadetails_qty*$mrp)-$orderdatadetails_TD;
					}
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
					${totalamount.$orderdatadetails_sku_code}=$totalmrp;
				 }
				 if(mrp=='yes' && TD=='no'){
					$totalmrp=($orderdatadetails_qty*$mrp);
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
					${totalamount.$orderdatadetails_sku_code}=$totalmrp;
				 }
				 if(sale_rate=='yes' && TD=='yes' && TD_type=='sku wise'){
					 if(TD_calc=='percentage')
					 {
						$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-((($orderdatadetails_qty*$orderdatadetails_sale_rate)*$orderdatadetails_TD)/100);
					 }
					 else
					 {
						$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-$orderdatadetails_TD;
					 }
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
					${totalamount.$orderdatadetails_sku_code}=$totalrate;
				 }
				 if(sale_rate=='yes' && TD=='no'){
					$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
					${totalamount.$orderdatadetails_sku_code}=$totalrate;
				 }
				 if(mrp=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
					$totalmrp=($orderdatadetails_qty*$mrp);
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp;
					${totalamount.$orderdatadetails_sku_code}=$totalmrp;
				 }
				 if(sale_rate=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
					$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
					${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate;
					${totalamount.$orderdatadetails_sku_code}=$totalrate;
				 }
				 if(VAT=='yes' && VAT_details=='amount')
				 {
					 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$orderdatadetails_VAT;
				 }
				 else if(VAT=='yes' && VAT_details=='percentage')
				 {
					 $vat_total=(${grandtotal.$orderdatadetails_order_no}*$orderdatadetails_VAT/100);
					 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$vat_total;
				 }

				 if(amount=='yes')
				 {
					 if($orderdatadetails_amount >0)
					 {
						$amount_mail=number_format(round($orderdatadetails_amount,2),2);
					 }
					 else
					 {
						 $amount_mail=number_format(round(${totalamount.$orderdatadetails_sku_code},2),2);
					 }
					 if($orderdatadetails_sale_rate >0)
					 {
						 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
					 }
					 else
					 {
						 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
					 }
				 }
				 else
				 {
					 $amount_mail=number_format(${totalamount.$orderdatadetails_sku_code},2);
					 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
				 }
				 ${orderdatadetails_qty_total.$orderdatadetails_order_no}=${orderdatadetails_qty_total.$orderdatadetails_order_no}+$orderdatadetails_qty;
				 if($orderdatadetails_amount >0)
				 {
					${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+$orderdatadetails_amount;
				 }
				 else
				 {
					${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+${totalamount.$orderdatadetails_sku_code};
				 }
					
					$orderemailbody .= "<tr>".$product_details_TD."
									<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_qty,2)."</span>&nbsp;</td>
									<td style='width:60px;text-align:left;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($UOM)."</span>&nbsp;</td>
									".$mrp_sale_rate_TD.$orderemailbody_TD_VAL."
									<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
									<span style='font-family:Arial CE;font-size:10pt'>".$amount_mail."</span>&nbsp;</td>".$orderemailbody_VAT_VAL."
								</tr>";
				}//End of order details while loop
				if(strpos(${orderdatadetails_qty_total.$order_no},'.')!=false){
					 ${orderdatadetails_qty_total.$order_no}=${orderdatadetails_qty_total.$order_no};
				 }
				 else
				 {
					 ${orderdatadetails_qty_total.$order_no}=number_format(${orderdatadetails_qty_total.$order_no},2);
				 }
				 
				if(TD=='yes' && TD_type=='order value wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD,2)."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD,2)."</b></table>";
					}
				}
				if(TD=='yes' && TD_type=='customer wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD."</b></table>";
					}
				}
				if(TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
					
					if(TD_calc=='percentage')
					{
						$total_order_amount=(${grandtotal.$order_no}-(${grandtotal.$order_no}*$orderdataheader_TD)/100);
					}
					else
					{
						$total_order_amount=(${grandtotal.$order_no}- $orderdataheader_TD);
					}
				}
				else
				{
					$total_order_amount=${grandtotal.$order_no};
				}
				if($orderdataheader_sale_type=='CREDIT' || $orderdataheader_sale_type=='COD' || $orderdataheader_sale_type=='SB'){
					if($orderdataheader_sale_type=='COD')
					{
						$COD_val='(CASH ON DELIVERY)';
					}
					else
					{
						$COD_val='';
					}
					if(($orderdataheader_sale_type=='CREDIT' || $orderdataheader_sale_type=='COD') && $orderdataheader_sale_type!='SB'){
					$total_order_val="<br /><table>Total order value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if(($orderdataheader_sale_type=='CREDIT' || $orderdataheader_sale_type=='COD' || $orderdataheader_sale_type=='CASH' ) && 
					$orderdataheader_sale_type=='SB')
					{
						$total_order_val="<br /><table>Total sale value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if($orderdataheader_sale_type=='CASH' && $orderdataheader_sale_type!='SB')
					{
						$total_order_val='';
					}
				}
				$orderinstructionemailbody="Delivery Instruction: <b>".strtoupper($orderdataheader_d_instruction)."</b>";
				$orderemailbody .="<tr><td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${orderdatadetails_qty_total.$order_no}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$orderemailbody_TD_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_amount_total.$order_no},2),2)."</span></strong></th>".$orderemailbody_VAT_total."
							</tr>
						</table>
			".$TD_order_val.$total_order_val."
			<br /><br /><table>".$orderinstructionemailbody."</table><br /><br />
			Powered By aceDNS<br /></body></html>";		

	
	//End order email body
	if($approval=='approved')
	{
		$sqlupdate="UPDATE order_header SET is_approved='YES' WHERE order_no='".$order_no."'";
		if(mysqli_query($link,$sqlupdate))
		{
				$date=gmdate('d',strtotime('+330 minute'));
				$month=gmdate('m',strtotime('+330 minute'));
				$year=gmdate('Y',strtotime('+330 minute'));
			
				$hour=gmdate('H',strtotime('+330 minute'));
				$minute=gmdate('i',strtotime('+330 minute'));
				$second=gmdate('s',strtotime('+330 minute'));
				$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
				//$email   ='export@tttextiles.com'; 
				//$subject ='TT mail checking again on'.$location_date;
			$email   =ORDEREMAILRECIPENTS; 
			$subject =$nick_name.' - '. 'Order Approved On '.date('d-m-Y',strtotime($location_date)).' @'.date('H:i:s',strtotime($location_date)).' hrs.' ;
			
			$message="<html><head><title>Order approval</title></head>
					<body>
					<table>
						<b>Refference No: " .$order_no. "</b>
					</table>
					<br /><br><br>Powered By aceDNS<br></body></html>";   
					

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
		$flgSend=mail($email, $subject, $orderemailbody, $headers,'-facedns@coral.in');
	}
?>
	<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Order Approval</td>
				</tr>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000">Order has been approved</font></strong></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Refference No</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $order_no;?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php		
	}
	else
	{
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	if(form.not_approved_reason.value.search(/\S/)==-1)
	{
		alert("Please provide a reason.");
		form.not_approved_reason.focus();
		return false;
	}
	return true;
}
</script>

<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="update-order-approval.php?nick_name=<?php echo $nick_name;?>" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="save">
            <input type="hidden" name="order_no" value="<?php echo $order_no;?>">
             <input type="hidden" name="orderemailbody" value="<?php echo $orderemailbody;?>">		
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Provide not approval reason</td>
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
					<td width="45%" align="right" valign="top" class="tbllogin">Refference No</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><?php echo $order_no;?></td>
				</tr>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Reason<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="not_approved_reason" class="not_approved_reason" style="width:300px;height:30px;" value="<?php echo $_REQUEST['not_approved_reason'];?>"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Save " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
	}
}//End of main()
function add_record()
{
	$order_no=$_REQUEST['order_no'];
	$not_approved_reason=$_REQUEST['not_approved_reason'];
	$nick_name=$_REQUEST['nick_name'];
	$orderemailbody=$_REQUEST['orderemailbody'];
	
	$sqlupdate="UPDATE order_header SET is_approved='NO',not_approved_reason='".$not_approved_reason."' WHERE order_no='".$order_no."'";
	if(mysqli_query($link,$sqlupdate))
	{
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
		
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
			//$email   ='export@tttextiles.com'; 
			//$subject ='TT mail checking again on'.$location_date;
		$email   =ORDEREMAILRECIPENTS; 
		$subject =$nick_name.' - '. 'Order Not Approved On '.date('d-m-Y',strtotime($location_date)).' @'.date('H:i:s',strtotime($location_date)).' hrs.' ;
		
		/*$message="<html><head><title>Order not approval</title></head>
				<body>
				<table>
					<b>Refference No: " .$order_no. "</b><br />
					<b>Reason of not approval: " .$not_approved_reason. "</b>
				</table>
				<br /><br><br>Powered By aceDNS<br></body></html>";*/ 
		$message="<html><head><title>Order not approval</title></head>
				<body>
				<table>
					<b>Reason of not approval: " .$not_approved_reason. "</b>
				</table>
				<br /></body></html>";		
		$orderemailbodyfinal=$message.$orderemailbody;		  

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		$flgSend=mail($email, $subject, $orderemailbodyfinal, $headers,'-facedns@coral.in');
	}
	?>
     <script language="JavaScript" type="text/javascript">alert('Order not approval reason has saved successfully.');window.close();</script>
    <?php
	//$GLOBALS['err_msg']="Order not approval reason has saved successfully.";
	//disphtml("main();");
}
?>
