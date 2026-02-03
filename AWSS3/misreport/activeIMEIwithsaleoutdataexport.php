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
					CRR.emp_code=EM.emp_code AND CRR.customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag=".$customer_code." AND acedns='Y' AND cust_type='R') ".$employee_hierarchy_condition;
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
$sql_get_active_IMEI_details = "SELECT EM.emp_name, CM.customer_name, PM.prod_desc, CPB.IMEI, CPB.activation_date ,CPB.stock_out_date ,CPB.invoice_date FROM customer_product_billing CPB,customer_master CM, product_master PM, employee_master EM WHERE CM.phone_no = EM.phone_no AND CPB.customer_code = CM.customer_code AND CPB.prod_code = PM.prod_code AND CPB.activation_date != '0000-00-00' AND CPB.stock_out_date != '0000-00-00' ".$customer_code_condition.$prod_code_condition."  ORDER BY CM.customer_name ASC,PM.prod_desc ASC";
$res_get_active_IMEI_details = mysql_query($sql_get_active_IMEI_details);
$total_rows = mysql_num_rows($res_get_active_IMEI_details);
//if(!empty($date_array)){
	$header = "Employee"."\t"."Party Name"."\t"."Model"."\t"."IMEI"."\t"."Bill Date"."\t"."Activation Date"."\t"."Sale Date";
	if($total_rows>0){
		while($row_get_active_IMEI_details = mysql_fetch_array($res_get_active_IMEI_details)){
		$emp_name = $row_get_active_IMEI_details['emp_name'];
		$customer_name = $row_get_active_IMEI_details['customer_name'];
		$prod_desc = $row_get_active_IMEI_details['prod_desc'];
		$IMEI=$row_get_active_IMEI_details['IMEI'];
		$activation_date=$row_get_active_IMEI_details['activation_date'];
		$stock_out_date = $row_get_active_IMEI_details['stock_out_date'];
		$invoice_date = $row_get_active_IMEI_details['invoice_date'];

		$table_data .= $emp_name."\t".$customer_name."\t".$prod_desc."\t".$IMEI."\t".$invoice_date."\t".$activation_date."\t".$stock_out_date."\t"."\n";

		}
	if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Active_IMEI_with_Sale.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
mysql_close($link);
?>


