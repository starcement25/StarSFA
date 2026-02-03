<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$fso_id=$_GET['fso_id'];
	$fsoarray=explode(",",$fso_id);
	$customer_code=$_GET['customer_code'];
	$prod_code=$_GET['prod_code'];
	$date_condition_billing = " AND CPB.invoice_date BETWEEN '".$start_date."' AND '".$end_date."'";
	//print_r($fsoarray);
	if(count($fsoarray) > 1)
	{
		$emp_hierarchy_val='';
		foreach($fsoarray as $fsoval)
		{
			$sqlselemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$fsoval."',reporting_to)";
			$rsselemp=mysqli_query($link,$sqlselemp);
			while($rowselemp=mysqli_fetch_assoc($rsselemp))
			{
				//$emp_code_string .= "'".$emp_code."',";
				$emp_hierarchy_val.="'".$rowselemp['emp_code']."',";
			}
		}
		$emp_hierarchy_val = rtrim($emp_hierarchy_val,",");
		$employee_hierarchy_condition=" AND CRR.emp_code IN(".$emp_hierarchy_val.")";
	}
	else
	{
	$employee_hierarchy=return_employee_hierarchy($fso_id);
	$employee_hierarchy_condition=" AND CRR.emp_code IN(".$employee_hierarchy.")";
	}
	$count = 1;
	//if(strpos($fso_id,",") == FALSE)
	//{
		if($customer_code!=''){
		if($customer_code=='all'){
			/*$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.emp_code 
						IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$fso_id."',reporting_to))";*/
			
			/*$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
							CRR.emp_code=EM.emp_code AND CRR.emp_code IN(".$employee_hierarchy.")";*/
			$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
							CRR.emp_code=EM.emp_code ".$employee_hierarchy_condition;				
		}
		else
		{
		    /*$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.customer_code=".$customer_code." AND CRR.emp_code 
						IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$fso_id."',reporting_to))";*/
			$sqlempcust="SELECT customer_code,retailer_app,cust_type,rds_tag FROM customer_master 
						WHERE customer_code=".$customer_code." AND acedns='Y'";
			$rsempcust=mysqli_query($link,$sqlempcust); 
			$rowempcust=mysqli_fetch_assoc($rsempcust);
			$retailer_app=$rowempcust['retailer_app'];
			$cust_type=$rowempcust['cust_type'];
			$rds_tag=$rowempcust['rds_tag'];
			$sqlcustomerleafcount="SELECT COUNT(customer_code) AS total_leaf FROM customer_master WHERE cust_type='R' AND rds_tag=".$customer_code."";
			$rscustomerleafcount=mysqli_query($link,$sqlcustomerleafcount);
			$total_leaf=mysqli_num_rows($rscustomerleafcount);
			if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='D' && $total_leaf > 0)
			{			
			  $sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
					CRR.emp_code=EM.emp_code AND CRR.customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag=".$customer_code." AND acedns='Y' AND cust_type='R') ".$employee_hierarchy_condition;
			}
			else
			{
				$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
					CRR.emp_code=EM.emp_code AND CRR.customer_code=".$customer_code.$employee_hierarchy_condition;
			}
		}
		$rsfsodetails=mysqli_query($link,$sqlfsodetails);
		while($rowfsodetails = mysqli_fetch_assoc($rsfsodetails)){				
			$customer_list.="'".$rowfsodetails['customer_code']."',";
		}
		$customer_list = rtrim($customer_list,",");
		$customer_code_condition=" AND CPB.customer_code IN(".$customer_list.")";
		}
	/*}
	else
	{
		$customer_list="";
		$customer_code_condition="";
	}*/
	if($prod_code!='')
	{
	   if($prod_code=='all'){
	     $prod_code_condition="";
	   }
	   else
	   {
		   $prod_code_condition=" AND CPB.prod_code=".$prod_code."";
	   }
	}
	else
	{
	   $prod_code_condition="";
	}
/*$sql_get_billed_details = "SELECT COUNT(CPB.IMEI) AS billed_qty,SUM(CASE WHEN CPB.stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS 'stock_out_qty',CPB.prod_code,CPB.customer_code,CM.customer_name,PM.prod_desc,DATE_FORMAT(CPB.invoice_date,'%d-%m-%Y') AS invoice_date
FROM customer_product_billing CPB,customer_master CM,product_master PM 
WHERE CPB.customer_code=CM.customer_code AND CPB.prod_code=PM.prod_code ".$date_condition_billing.$customer_code_condition.$prod_code_condition." 
GROUP BY CPB.customer_code,CPB.prod_code ORDER BY CPB.invoice_date ASC,CPB.customer_code ASC";*/

/*$sql_get_billed_details = "SELECT SUM(CASE WHEN CPB.invoice_date BETWEEN '".$start_date."' AND '".$end_date."' THEN 1 ELSE 0 END) AS billed_qty,CPB.prod_code,CPB.customer_code,CM.customer_name,PM.prod_desc,DATE_FORMAT(CPB.invoice_date,'%d-%m-%Y') AS invoice_date,
CM.rds_tag FROM customer_master CM LEFT JOIN customer_product_billing CPB ON CM.customer_code=CPB.customer_code LEFT JOIN product_master PM
ON CPB.prod_code=PM.prod_code WHERE 1 ".$customer_code_condition.$prod_code_condition." GROUP BY CPB.customer_code,CPB.prod_code ORDER BY CM.customer_name ASC,PM.prod_desc ASC";*/
//echo $customer_code_condition;
$sql_get_billed_details = "SELECT SUM(CASE WHEN CPB.invoice_date BETWEEN '".$start_date."' AND '".$end_date."' THEN 1 ELSE 0 END) AS billed_qty,CPB.prod_code,CPB.customer_code,CM.customer_name,PM.prod_desc,DATE_FORMAT(CPB.invoice_date,'%d-%m-%Y') AS invoice_date,
CM.rds_tag FROM customer_product_billing CPB,customer_master CM,product_master PM
WHERE CPB.customer_code=CM.customer_code AND CPB.prod_code=PM.prod_code ".$customer_code_condition.$prod_code_condition." GROUP BY CPB.customer_code,CPB.prod_code ORDER BY CM.customer_name ASC,PM.prod_desc ASC";
$res_get_billed_details = mysqli_query($link,$sql_get_billed_details);
$total_rows = mysqli_num_rows($res_get_billed_details);
if($total_rows>0){
	$branch_oulet_array=array();
	echo "<div class='tbl-header'><table width='100%'  border='0' style='border-collapse:collapse;' class='border datatable' cellpadding='4'><thead>";
	echo "<tr class='TDHEAD'><td colspan='7' align='center'>Stock Out Report</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td width=\"8%\" >SI</td>
			<td width=\"17%\" align=\"center\">Party Name</td>
			<td width=\"15%\" align=\"center\">Model</td>
			<td width=\"15%\" align=\"center\">Opening Stock</td>
			<td width=\"15%\" align=\"center\">Billed Qty</td>
			<td width=\"15%\" align=\"center\">Stock Out Qty</td>
			<td width=\"15%\" align=\"center\">Closing Stock</td>
		  </tr></thead></table></div><div class='tbl-content'><table class='BORDER datatable' cellpadding='0' cellspacing='0' border='1'><tbody>";
	while($row_get_billed_details = mysqli_fetch_assoc($res_get_billed_details)){
		$invoice_date = $row_get_billed_details['invoice_date'];
		$customer_code = $row_get_billed_details['customer_code'];
		$prod_code = $row_get_billed_details['prod_code'];
		$prod_desc=$row_get_billed_details['prod_desc'];
		$customer_name=$row_get_billed_details['customer_name'];
		$rds_tag=$row_get_billed_details['rds_tag'];
		/*$sqlstockoutqty="SELECT CRR.emp_code,SUM(CASE WHEN SOD.stock_out_date BETWEEN '".$start_date."' AND '".$end_date."' THEN 1 ELSE 0 END) 
					AS stock_out_qty FROM customer_route_emp_relation CRR,stock_out_details SOD WHERE 
					CRR.emp_code=SUBSTRING(SOD.stock_out_id,3,5) AND CRR.customer_code='".$customer_code."' AND SOD.prod_code='".$prod_code."'";*/
		if($rds_tag=='')
		{			
			$billed_qty = $row_get_billed_details['billed_qty'];
			$sqlstockoutqty="SELECT SUM(CASE WHEN SOD.stock_out_date BETWEEN '".$start_date."' AND '".$end_date."' THEN 1 ELSE 0 END) 
						AS stock_out_qty FROM stock_out_details SOD WHERE SOD.customer_code='".$customer_code."' AND SOD.prod_code='".$prod_code."'";			
			$rsstockoutqty=mysqli_query($link,$sqlstockoutqty);
			$rowstockoutqty=mysqli_fetch_assoc($rsstockoutqty);
			$emp_code_stockout=$rowstockoutqty['emp_code'];
			$stock_out_qty=$rowstockoutqty['stock_out_qty'];
			
			//$stock_out_qty = $row_get_billed_details['stock_out_qty'];
			if($stock_out_qty =='')   $stock_out_qty=0;
			
			$sqlselopeningstock="SELECT (COUNT(IMEI)-SUM(CASE WHEN stock_out_date!='0000-00-00' AND stock_out_date < '".$start_date."' THEN 1 ELSE 0 END)) AS opening_stock 
								FROM customer_product_billing  WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."' AND 
									invoice_date < '".$start_date."'";
			$rsselopeningstock=mysqli_query($link,$sqlselopeningstock);
			$rowselopeningstock=mysqli_fetch_assoc($rsselopeningstock);
			$opening_stock=$rowselopeningstock['opening_stock'];
			if($opening_stock==''){						
			   $opening_stock=0;
			}
		}
		else
		{
		   $sqlbilling="SELECT COUNT(IMEI) as consolidated_billed_qty FROM customer_product_billing  
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."' AND invoice_date BETWEEN '".$start_date."' AND '".$end_date."'";
			$rsbilling=mysqli_query($link,$sqlbilling);
			$rowbilling=mysqli_fetch_assoc($rsbilling);
			$billed_qty=$rowbilling['consolidated_billed_qty'];
			$sqlstockoutqty="SELECT SUM(CASE WHEN IMEI!='' AND stock_out_date BETWEEN '".$start_date."' AND '".$end_date."' THEN 1 ELSE 0 END) 
							AS consolidated_stock_out_qty,SUM(CASE WHEN IMEI!='' AND stock_out_date < '".$start_date."' THEN 1 ELSE 0 END) 
							AS consolidated_stock_out_qty_opening  FROM stock_out_details  
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
								AND prod_code='".$prod_code."'";			
			$rsstockoutqty=mysqli_query($link,$sqlstockoutqty);
			$rowstockoutqty=mysqli_fetch_assoc($rsstockoutqty);
			$stock_out_qty=$rowstockoutqty['consolidated_stock_out_qty'];
			$stock_out_qty_opening=$rowstockoutqty['consolidated_stock_out_qty_opening'];
			if($stock_out_qty =='')   $stock_out_qty=0;
			if($stock_out_qty_opening =='')   $stock_out_qty_opening=0;
			
			$sqlselopeningstockbilling="SELECT COUNT(IMEI) AS opening_stock_billing FROM customer_product_billing  WHERE 
										customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
										AND prod_code='".$prod_code."' AND invoice_date < '".$start_date."'";
			$rsselopeningstockbilling=mysqli_query($link,$sqlselopeningstockbilling);
			$rowselopeningstockbilling=mysqli_fetch_assoc($rsselopeningstockbilling);
			$opening_stock_billing=$rowselopeningstockbilling['opening_stock_billing'];
			if($opening_stock_billing==''){						
			   $opening_stock_billing=0;
			}
			
			$opening_stock=$opening_stock_billing-$stock_out_qty_opening;
		}
		/*$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
		$rsproddesc=mysqli_query($link,$sqlproddesc);	
		$rowproddesc=mysqli_fetch_assoc($rsproddesc);
		$prod_desc=$rowproddesc['prod_desc'];
		
		$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomer=mysqli_query($link,$sqlcustomer);	
		$rowcustomer=mysqli_fetch_assoc($rscustomer);
		$customer_name=$rowcustomer['customer_name'];*/
		$closing_stock=$opening_stock+$billed_qty-$stock_out_qty;
		if($closing_stock < 0)
		{
			$closing_stock=0;
		}
		 if($opening_stock >0 || $billed_qty > 0 || $stock_out_qty > 0 || $closing_stock >0){
			if($rds_tag=='')
			{
				echo "<tr>
					<td width='10%'>".$count."</td>
					<td width='15%'>".$customer_name."</td>
					<td width='15%'>".$prod_desc."</td>
					<td align=\"right\" width='15%'>".$opening_stock."</td>
					<td align=\"right\" width='15%'>".$billed_qty."</td>
					<td align=\"right\" width='15%'>".$stock_out_qty."</td>
					<td align=\"right\" width='15%'>".$closing_stock."</td>
				  </tr>";
			 	$count++;
			}
			else
			{
				$sqlselbranchoutlet="SELECT customer_code,customer_name FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y'";
				$rsselbranchoutlet=mysqli_query($link,$sqlselbranchoutlet);
				while($rowselbranchoutlet=mysqli_fetch_assoc($rsselbranchoutlet))
				{
				   $concatenatestring= $rowselbranchoutlet['customer_code'].'-'.$prod_code;
				   if(!in_array($concatenatestring,$branch_oulet_array))
					 {
						 echo "<tr>
							<td width='10%'>".$count."</td>
							<td width='15%'>".$rowselbranchoutlet['customer_name']."</td>
							<td width='15%'>".$prod_desc."</td>
							<td align=\"right\" width='15%'>".$opening_stock."</td>
							<td align=\"right\" width='15%'>".$billed_qty."</td>
							<td align=\"right\" width='15%'>".$stock_out_qty."</td>
							<td align=\"right\" width='15%'>".$closing_stock."</td>
						  </tr>";
						  array_push($branch_oulet_array,$concatenatestring);
						$count++;
					 }
				}
			}
		 }
	}
	echo "</tbody></table> 
</div>";
?>
 <div style="width:100%;" align="center" id="print_export"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
<?php
}
else{
   echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>
