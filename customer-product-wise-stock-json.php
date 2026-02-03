<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
}

	$sqlstock="SELECT DISTINCT CPS.customer_code,CPS.prod_code,CPS.stock FROM customer_product_stock CPS 
			INNER JOIN customer_route_emp_relation CM ON CPS.customer_code=CM.customer_code 
			WHERE CM.acedns='Y' AND CM.customer_code IN(SELECT DISTINCT customer_code FROM customer_master WHERE cust_type='D') $emp_hierarchy_condition";
	$resstock = mysqli_query($link,$sqlstock);
	$count=mysqli_num_rows($resstock);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($rowstock = mysqli_fetch_assoc($resstock)){
			$customer_code = $rowstock['customer_code'];
			$prod_code=$rowstock['prod_code'];
			$stock=$rowstock['stock'];
			$res_data[] = array("customer_code"=>$customer_code,"prod_code"=>$prod_code,"stock"=>$stock);
			
		}
			$countcolumns='3';
$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something Went Wrong." );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-product-wise-stock-json.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
