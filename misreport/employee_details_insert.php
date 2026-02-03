<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_name = $_REQUEST['emp_name'];
$designation = $_REQUEST['designation'];
$hq = $_REQUEST['hq'];
$state = $_REQUEST['state'];
$zone = $_REQUEST['zone'];
$phn = $_REQUEST['phn'];
$email = $_REQUEST['email'];
$branch = $_REQUEST['branch'];
$reporting_to = $_REQUEST['reporting_to'];
$vertical_fields = $_REQUEST['vertical_fields'];
$sale_access = $_REQUEST['sale_access'];
$acedns = $_REQUEST['acedns'];

$sql_max_empid = "SELECT MAX(emp_code) as maxempcode FROM employee_master";
$res_max_empid = mysqli_query($link,$sql_max_empid);
$row_max_empid = mysqli_fetch_assoc($res_max_empid);
$max_row_id = $row_max_empid['maxempcode'];
$max_row_id++;
$new_emp_code = $max_row_id;

/*---------------------------> INSERT INTO employee_master table <-------------------------------*/
$sql_insert_empmaster = "INSERT INTO `employee_master` SET 
								  `emp_code` = '".$new_emp_code."', 
							  `dns_emp_code` = '', 
								  `emp_name` = '".$emp_name."', 
									`acedns` = '".$acedns."', 
							   `branch_code` = '".$branch."', 
							`vertical_value` = '".$vertical_fields."', 
							  `reporting_to` = '".$reporting_to."', 
									 `email` = '".$email."', 
								  `phone_no` = '".$phn."', 
										`HQ` = '".$hq."', 
							   `sale_access` = '".$sale_access."', 
							   `designation` = '".$designation."', 
									 `state` = '".$state."', 
									  `zone` = '".$zone."'";
$res_insert_empmaster = mysqli_query($link,$sql_insert_empmaster);

/*---------------------------> INSERT INTO employee into changepassword table <-------------------------------*/
$sql_change_password = "INSERT INTO changepassword SET emp_code='".$new_emp_code."', newpassword='1234', oldpassword='1234', is_licensed='1'";
$res_change_password = mysqli_query($link,$sql_change_password);
$row_change_password = mysqli_fetch_assoc($res_change_password);


/*---------------------------> INSERT INTO route_master table <-------------------------------*/
$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
						$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
						$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
						$new_route_code=$rowmaxroutecode['new_route_code'];
						
						$max_route_code='RT/'.($new_route_code+1);
												
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,dns_route_code=''";
						$sqlroute .= " ,route_name='KOLKATA'";
						$sqlroute .= " , emp_code='".$new_emp_code."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlroute);
						$route_code=$max_route_code;
						
/*---------------------------> INSERT INTO customer_master table <-------------------------------*/						
$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
						$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
						$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
						$max_customer_code=$rowmaxcustomercode['max_customer_code'];
						
						$max_customer_code++;
						
						$sql  = "insert into customer_master ";
						$sql .= " SET customer_code='".$max_customer_code."'";
						$sql .= " , dns_customer_code=''";
						$sql .= " , customer_name='STOREHANDLE'";
						$sql .= " , branch_code=''";
						$sql .= " , phone_no=''";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$new_emp_code."'";
						$sql .= " , current_balance	=''";
						$sql .= " , credit_limit=''";
						$sql .= " , credit_days=''";
						$sql .= " , acedns='Y'";
						$sql .= " , black_list='N'";
						$sql .= " , TD=''";
						$sql .= " , rds_tag=''";
						$sql .= " , cust_type=''";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						//exit();
						mysqli_query($link,$sql);
echo "Data inserted successfully";
mysqli_close($link);
?>