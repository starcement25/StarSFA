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
	$date_condition_allocation = " AND SUBSTRING(SBD.allocation_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."'";
	$employee_hierarchy=return_employee_hierarchy($fso_id);
	$count = 1;
	if(count($fsoarray) > 1)
	{
		$emp_hierarchy_val='';
		foreach($fsoarray as $fsoval)
		{
			$sqlselemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$fsoval."',reporting_to) AND acedns='Y'";
			$rsselemp=mysql_query($sqlselemp);
			while($rowselemp=mysql_fetch_array($rsselemp))
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
		if($customer_code!=''){
		if($customer_code=='all'){
		/*$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.emp_code 
						IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$fso_id."',reporting_to))";*/
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
			$rsempcust=mysql_query($sqlempcust); 
			$rowempcust=mysql_fetch_array($rsempcust);
			$retailer_app=$rowempcust['retailer_app'];
			$cust_type=$rowempcust['cust_type'];
			$rds_tag=$rowempcust['rds_tag'];
			$sqlcustomerleafcount="SELECT COUNT(customer_code) AS total_leaf FROM customer_master WHERE cust_type='R' AND rds_tag=".$customer_code."";
			$rscustomerleafcount=mysql_query($sqlcustomerleafcount);
			$total_leaf=mysql_num_rows($rscustomerleafcount);
				if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='D' && $total_leaf > 0)
				{			
			      $sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag=".$customer_code." AND acedns='Y' AND cust_type='R')".$employee_hierarchy_condition;
				}
				else
				{
					$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.customer_code=".$customer_code.$employee_hierarchy_condition;
				}
		}
		$rsfsodetails=mysql_query($sqlfsodetails);
		while($rowfsodetails = mysql_fetch_array($rsfsodetails)){				
			$customer_list.="'".$rowfsodetails['customer_code']."',";
		}
		$customer_list = rtrim($customer_list,",");
		$customer_code_condition="AND SBD.customer_code IN(".$customer_list.")";
		}
	/*else
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
		   $prod_code_condition=" AND SBD.prod_code=".$prod_code."";
	   }
	}
	else
	{
	   $prod_code_condition="";
	}
/*$sql_get_allocation_details = "SELECT DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%d-%m-%Y') AS allocation_date,CPA.prod_code,CPA.customer_code,
							SUM(CPA.qty) AS allocate_qty,SUM(RD.requisition_qty) AS requisition_qty,COUNT(IMEI) AS billing_qty,DATE_FORMAT(SUBSTRING(RD.requisition_id,-14,8),'%d-%m-%Y') AS requisition_date,DATE_FORMAT(CPB.invoice_date,'%d-%m-%Y') AS billing_date
						FROM customer_product_allocation CPA LEFT JOIN requisition_details RD ON CPA.customer_code=REPLACE(SUBSTRING(RD.allocation_id,3,9),'#','/') AND CPA.prod_code=RD.prod_code AND 
						CPA.allocation_id=RD.allocation_id
						LEFT JOIN customer_product_billing CPB ON REPLACE(SUBSTRING(RD.allocation_id,3,9),'#','/') =CPB.customer_code AND RD.prod_code=CPB.prod_code AND RD.requisition_id=CPB.requisition_id  WHERE
						 ".$date_condition_allocation.$customer_code_condition." AND CPA.qty > 0 GROUP BY DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%Y-%m-%d'),CPA.customer_code,CPA.prod_code
						ORDER BY DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%Y-%m-%d') DESC";*/
$sql_get_allocation_details = "SELECT SUM(CASE WHEN SUBSTRING(SBD.allocation_id,1,2)='CA' AND SBD.active_flag='Y' AND SBD.requisition_id='' AND  SBD.billed_qty=0 THEN SBD.allocation_qty ELSE 0 END) AS 'allocation_w_requisition',SUM(CASE WHEN SUBSTRING(SBD.allocation_id,1,2)='CA'  AND SBD.requisition_id!='' AND  SBD.billed_qty=0 THEN SBD.allocation_qty ELSE 0 END) AS 'allocation_requisition',SUM(CASE WHEN SUBSTRING(SBD.allocation_id,1,2)='CA'  AND SBD.requisition_id!='' AND  SBD.billed_qty=0 THEN SBD.requisition_qty ELSE 0 END) AS 'requisition_qty_val',SUM(CASE WHEN SUBSTRING(SBD.allocation_id,1,2)='RA'  THEN SBD.allocation_qty ELSE 0 END) AS 'reallocation_qty',SUM(CASE WHEN SUBSTRING(SBD.allocation_id,1,2)='RA'  AND SBD.requisition_id!='' THEN SBD.requisition_qty ELSE 0 END) AS 'reallocation_reqisition_qty',DATE_FORMAT(SUBSTRING(SBD.allocation_id,-14,8),'%d-%m-%Y') AS allocation_date_val,SBD.prod_code,SBD.customer_code,CM.customer_name,PM.prod_desc,SUM(SBD.billed_qty) AS billed_qty
						FROM stock_balance_details SBD,customer_master CM,product_master PM WHERE SBD.customer_code=CM.customer_code AND 
						SBD.prod_code=PM.prod_code
						 ".$date_condition_allocation.$customer_code_condition.$prod_code_condition." AND SBD.allocation_qty > 0 GROUP BY DATE_FORMAT(SUBSTRING(SBD.allocation_id,-14,8),'%d-%m-%Y'),SBD.customer_code,SBD.prod_code
						ORDER BY DATE_FORMAT(SUBSTRING(SBD.allocation_id,-14,8),'%Y-%m-%d') ASC,SBD.customer_code ASC";						

$res_get_allocation_details = mysql_query($sql_get_allocation_details);
$total_rows = mysql_num_rows($res_get_allocation_details);
if($total_rows>0){
	echo "<div class='tbl-header'><table width='100%'  border='0' style='border-collapse:collapse;' class='border datatable' cellpadding='4'><thead>";
	echo "<tr class='TDHEAD'><td colspan='12' align='center'>Stock Balance Report</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Allocation Date</td>
			<td>Party Name</td>
			<td>Model</td>
			<td>Allocated Qty</td>
			<td>Requisition Qty</td>
			<td>Reallocation Qty</td>
			<td>Reallocated Requisition Qty</td>
			<td>Totoal Allocated qty</td>
			<td>Total Requisition Qty</td>
			<td>Total Billed Qty</td>
			<td>Variance (+/-)</td>
		  </tr></thead></table></div><div class='tbl-content'><table class='BORDER datatable' cellpadding='0' cellspacing='0' border='1'><tbody>";
	
	while($row_get_allocation_details = mysql_fetch_array($res_get_allocation_details)){
		$allocation_date_val = $row_get_allocation_details['allocation_date_val'];
		$customer_code = $row_get_allocation_details['customer_code'];
		$prod_code = $row_get_allocation_details['prod_code'];
		$prod_desc=$row_get_allocation_details['prod_desc'];
		$customer_name=$row_get_allocation_details['customer_name'];
		$allocation_w_requisition = $row_get_allocation_details['allocation_w_requisition'];
		$allocation_requisition = $row_get_allocation_details['allocation_requisition'];
		$allocate_qty = $allocation_w_requisition+$allocation_requisition;
		$requisition_qty = $row_get_allocation_details['requisition_qty_val'];
		$reallocation_qty = $row_get_allocation_details['reallocation_qty'];
		$reallocation_reqisition_qty = $row_get_allocation_details['reallocation_reqisition_qty'];
		$billing_qty = $row_get_allocation_details['billed_qty'];
		
		/*$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
		$rsproddesc=mysql_query($sqlproddesc);	
		$rowproddesc=mysql_fetch_array($rsproddesc);
		$prod_desc=$rowproddesc['prod_desc'];
		
		$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomer=mysql_query($sqlcustomer);	
		$rowcustomer=mysql_fetch_array($rscustomer);
		$customer_name=$rowcustomer['customer_name'];*/
		$total_allocated_qty=$allocate_qty+$reallocation_qty;
		$total_requisition_qty=$requisition_qty+$reallocation_reqisition_qty;
		$variance=$total_requisition_qty-$total_allocated_qty;
		
		echo "<tr>
					<td>".$count."</td>
					<td >".$allocation_date_val."</td>
					<td >".$customer_name."</td>
					<td >".$prod_desc."</td>
					<td align='right'>".$allocate_qty."</td>
					<td align='right'>".$requisition_qty."</td>
					<td align='right'>".$reallocation_qty."</td>
					<td align='right'>".$reallocation_reqisition_qty."</td>
					<td align='right'>".$total_allocated_qty."</td>
					<td align='right'>".$total_requisition_qty."</td>
					<td align='right'>".$billing_qty."</td>
					<td align='right'>".$variance."</td>
				  </tr>";
				  
		$count++;
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
mysql_close($link);
?>
