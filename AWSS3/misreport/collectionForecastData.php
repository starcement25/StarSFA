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
	$date_condition_forecast = " AND CF.forecast_date BETWEEN '".$start_date."' AND '".$end_date."'";
	//print_r($fsoarray);
	if(count($fsoarray) > 1)
	{
		$emp_hierarchy_val='';
		foreach($fsoarray as $fsoval)
		{
			$sqlselemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$fsoval."',reporting_to)";
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
			$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
							CRR.emp_code=EM.emp_code ".$employee_hierarchy_condition;
			$rsfsodetails=mysql_query($sqlfsodetails);
			while($rowfsodetails = mysql_fetch_array($rsfsodetails)){				
				$customer_list.="'".$rowfsodetails['customer_code']."',";
			}
			$customer_list = rtrim($customer_list,",");
			$customer_code_condition=" AND CF.customer_code IN(".$customer_list.")";
		}
		else
		{
		    /*$sqlfsodetails="SELECT CRR.customer_code FROM customer_route_emp_relation CRR,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CRR.customer_code=".$customer_code." AND CRR.emp_code 
						IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$fso_id."',reporting_to))";*/
			$customer_code_condition=" AND CF.customer_code IN(".$customer_code.")";
		}
		}
	/*}
	else
	{
		$customer_list="";	
		$customer_code_condition="";
	}*/
$sql_get_collection_forecast = "SELECT EM.emp_name,CM.customer_name,DATE_FORMAT(CF.forecast_date,'%d-%m-%Y') AS forecast_date,
							 SUM(CF.invoice_amount) as invoice_amount,SUM(CF.amount_received) AS amount_received
							FROM collection_forecast_details CF,customer_master CM,employee_master EM WHERE CF.customer_code = CM.customer_code  
							".$customer_code_condition.$date_condition_forecast." AND  EM.emp_code=SUBSTRING(CF.forecast_id,3,5)  GROUP BY CF.forecast_date,CM.customer_code 
							ORDER BY EM.emp_name ASC,DATE_FORMAT(CF.forecast_date,'%d-%m-%Y') DESC,CM.customer_name ASC";
$res_get_collection_forecast = mysql_query($sql_get_collection_forecast);
$total_rows = mysql_num_rows($res_get_collection_forecast);
if($total_rows>0){
	echo "<div class='tbl-header'><table  border='0' style='border-collapse:collapse;' class='border datatable' cellpadding='4'><thead>";
	echo "<tr class='TDHEAD'><td colspan='7' align='center'>Coolection Forecast Details</td></tr>";
	echo "<tr class='TDHEAD_SUB' >
			<td width=\"7%\" >SI</td>
			<td width=\"14%\" >FOS name</td>
			<td width=\"14%\">Forecast Date</td>
			<td width=\"20%\" >Party Name</td>
			<td width=\"15%\" >Outstanding amount</td>
			<td width=\"15%\" >Forecast Amount</td>
			<td width=\"15%\" >Actual Received</td>
		  </tr></thead></table></div><div class='tbl-content'><table class='BORDER datatable' cellpadding='0' cellspacing='0' border='1'><tbody>";
	while($row_get_collection_forecast = mysql_fetch_array($res_get_collection_forecast)){
		$customer_name = $row_get_collection_forecast['customer_name'];
		$forecast_date = $row_get_collection_forecast['forecast_date'];
		$invoice_amount=$row_get_collection_forecast['invoice_amount'];
		$amount_received=$row_get_collection_forecast['amount_received'];
		$emp_name=$row_get_collection_forecast['emp_name'];
		$actual_received='';
			echo "<tr>
					<td width=\"7%\">".$count."</td>
					<td width=\"14%\">".$emp_name."</td>
					<td  width=\"14%\">".$forecast_date."</td>
					<td width=\"20%\" >".$customer_name."</td>
					<td  width=\"15%\" style=\"text-align:right\">".number_format($invoice_amount,2)."</td>
					<td  width=\"15%\" style=\"text-align:right\">".number_format($amount_received,2)."</td>
					<td  width=\"15%\" style=\"text-align:right\">".number_format($actual_received,2)."</td>
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