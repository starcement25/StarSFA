<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	/*$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");*/
	
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	$customer_code="";
	
	function get_record($column,$table,$trans_id){
	    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
		$sql_transaction_data = "SELECT customer_code FROM $table WHERE $column = '$trans_id'";
		$res_transaction_data = mysqli_query($link,$sql_transaction_data);
		$row_transaction_data = mysqli_fetch_assoc($res_transaction_data);
		$customer_code = $row_transaction_data['customer_code'];
		return $customer_code;
	}
	function get_hint_remarks($column,$table,$trans_id){
	    $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
		$sql_hint_remarks = "SELECT hint_remarks FROM $table WHERE $column = '".$trans_id."'";
		$res_hint_remarks = mysqli_query($link,$sql_hint_remarks);
		$row_hint_remarks = mysqli_fetch_assoc($res_hint_remarks);
		$hint_remarks = $row_hint_remarks['hint_remarks'];
		return $hint_remarks;
	}
	$sqlcustomer="SELECT trans_id,emp_code FROM location WHERE SUBSTRING(date,1,10) BETWEEN '2024-04-01' AND '2024-04-07' 
					AND SUBSTRING(trans_id,1,1) NOT IN('A','C') AND SUBSTRING(trans_id,1,2) NOT IN('SU') ORDER BY date ASC ";
	//$sqlcustomer="SELECT trans_id,emp_code FROM location WHERE trans_id='SE094220230103095950' ";
	
	/*$sqlcustomer="SELECT SUBSTRING(trans_id,1,1),SUBSTRING(trans_id,1,2),SUBSTRING(date,1,10),trans_id,emp_code FROM location WHERE SUBSTRING(date,1,10) BETWEEN '2024-03-21' AND '2024-03-21' AND SUBSTRING(trans_id,1,2) IN('NS') ORDER BY date ASC";*/
	$rscustomer=mysqli_query($link,$sqlcustomer); 
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$trans_id=$rowcustomer['trans_id'];
		$emp_code=$rowcustomer['emp_code'];
		$customer_code="";
		if(substr($trans_id,0,1) == "O" || substr($trans_id,0,2)=='MS' || substr($trans_id,0,1)=='S' || substr($trans_id,0,2)=='NO' || substr($trans_id,0,2)=='NS'){
			$hint_remarks = '';
			if(substr($trans_id,0,1) == "O" || substr($trans_id,0,2)=='NO'){
				$customer_code = get_record('order_no','order_header',$trans_id);
				$hint_remarks = get_hint_remarks('order_no','order_header',$trans_id);
			}
			else if(substr($trans_id,0,1)=='S' || substr($trans_id,0,2)=='NS'){
				$customer_code = get_record('transaction_id','stock_audit',$trans_id);
				$hint_remarks = get_hint_remarks('transaction_id','stock_audit',$trans_id);
			}
			else if(substr($trans_id,0,2)=='MS'){
				$customer_code = get_record('mf_stk_audit_id','mf_stk_audit_header',$trans_id);
				$hint_remarks = '';
			}
			$sql_cust_details = "SELECT customer_name, cust_type, rds_tag, route_code FROM customer_master WHERE customer_code = '".$customer_code."'";
			echo $sql_cust_details;
			$res_cust_details = mysqli_query($link,$sql_cust_details);
			$row_cust_details = mysqli_fetch_assoc($res_cust_details);
			$customer_name = $row_cust_details['customer_name'];
			$cust_type = $row_cust_details['cust_type'];
			$route_code = $row_cust_details['route_code'];
			$rds_tag = $row_cust_details['rds_tag'];
			
			$sql_route = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
			$res_route = mysqli_query($link,$sql_route);
			$row_route = mysqli_fetch_assoc($res_route);
			$route_name = $row_route['route_name'];
			$sqlchkexistance="SELECT `trans_id` FROM customer_visit_details WHERE trans_id='".$trans_id."'";
			$rschkexistance=mysqli_query($link,$sqlchkexistance);
			$cntchkexistance=mysqli_num_rows($rschkexistance);
			if($cntchkexistance ==0)
			{
			 $sql_customer_visit_details = "INSERT INTO customer_visit_details SET 
													 `emp_code` = '".$emp_code."', 
													 `trans_id` = '".$trans_id."', 
												`customer_code` = '".$customer_code."', 
												`customer_name` = '".addslashes($customer_name)."', 
													`cust_type` = '".$cust_type."', 
												   `route_code` = '".$route_code."', 
												   `route_name` = '".addslashes($route_name)."', 
													  `rds_tag` = '".$rds_tag."', 
											     `hint_remarks` = '".addslashes($hint_remarks)."'";
											     echo $sql_customer_visit_details;
			mysqli_query($link,$sql_customer_visit_details);
			}else{
			     $sql_customer_visit_details = "UPDATE customer_visit_details SET 
													 `emp_code` = '".$emp_code."', 
													  
												`customer_code` = '".$customer_code."', 
												`customer_name` = '".addslashes($customer_name)."', 
													`cust_type` = '".$cust_type."', 
												   `route_code` = '".$route_code."', 
												   `route_name` = '".addslashes($route_name)."', 
													  `rds_tag` = '".$rds_tag."', 
											     `hint_remarks` = '".addslashes($hint_remarks)."'
											     
											     WHERE `trans_id` = '".$trans_id."'";
											     echo //$sql_customer_visit_details;
			mysqli_query($link,$sql_customer_visit_details);
			}
		}

	}
    echo "SUCCESS";
	mysqli_close($link);
?>
