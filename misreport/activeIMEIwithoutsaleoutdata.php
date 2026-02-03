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
			$sqlselemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$fsoval."',reporting_to) AND acedns='Y'";
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
							CRR.emp_code=EM.emp_code ".$employee_hierarchy_condition;*/
			$customer_code_condition="";				
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
			$rsfsodetails=mysqli_query($link,$sqlfsodetails);
			while($rowfsodetails = mysqli_fetch_assoc($rsfsodetails)){				
				$customer_list.="'".$rowfsodetails['customer_code']."',";
			}
			$customer_list = rtrim($customer_list,",");
			$customer_code_condition=" AND CPB.customer_code IN(".$customer_list.")";
			}
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
$sql_get_active_IMEI_details = "SELECT EM.emp_name, CM.customer_name, PM.prod_desc, CPB.IMEI, CPB.activation_date ,CPB.stock_out_date ,CPB.invoice_date FROM customer_product_billing CPB,customer_master CM, product_master PM, employee_master EM WHERE CM.phone_no = EM.phone_no AND CPB.customer_code = CM.customer_code AND CPB.prod_code = PM.prod_code AND CPB.activation_date != '0000-00-00' AND CPB.stock_out_date = '0000-00-00' ".$customer_code_condition.$prod_code_condition."  ORDER BY CM.customer_name ASC,PM.prod_desc ASC";
$res_get_active_IMEI_details = mysqli_query($link,$sql_get_active_IMEI_details);
$total_rows = mysqli_num_rows($res_get_active_IMEI_details);
if($total_rows>0){
	echo "<div class='tbl-header'><table width='100%'  border='0' style='border-collapse:collapse;' class='border datatable' cellpadding='4'><thead>";
	echo "<tr class='TDHEAD'><td colspan='8' align='center'>Active IMEI with sale date</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td width=\"6%\" >SI</td>
			<td width=\"15%\" align=\"center\">Employee</td>
			<td width=\"15%\" align=\"center\">Party Name</td>
			<td width=\"15%\" align=\"center\">Model</td>
			<td width=\"14%\" align=\"center\">IMEI</td>
			<td width=\"10%\" align=\"center\">Bill Date</td>
			<td width=\"15%\" align=\"center\">Activation Date</td>
			<td width=\"10%\"   style=\"padding-right:20px;\">Sale Date</td>
		  </tr></thead></table></div><div class='tbl-content'><table class='BORDER datatable' cellpadding='0' cellspacing='0' border='1'><tbody>";
	while($row_get_active_IMEI_details = mysqli_fetch_assoc($res_get_active_IMEI_details)){
		$emp_name = $row_get_active_IMEI_details['emp_name'];
		$customer_name = $row_get_active_IMEI_details['customer_name'];
		$prod_desc = $row_get_active_IMEI_details['prod_desc'];
		$IMEI=$row_get_active_IMEI_details['IMEI'];
		$activation_date=$row_get_active_IMEI_details['activation_date'];
		$stock_out_date = $row_get_active_IMEI_details['stock_out_date'];
		$invoice_date = $row_get_active_IMEI_details['invoice_date'];
			echo "<tr>
					<td width='6%'>".$count."</td>
					<td width='15%'>".$emp_name."</td>
					<td width='15%'>".$customer_name."</td>
					<td  width='15%'>".$prod_desc."</td>
					<td width='14%'>".$IMEI."</td>
					<td  width='10%'>".$invoice_date."</td>
					<td  width='15%'>".$activation_date."</td>
					<td  width='10%'>".$stock_out_date."</td>
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
mysqli_close($link);
?>