<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_id = $_REQUEST['emp_id'];
$emp_name = $_REQUEST['emp_name'];
$designation = $_REQUEST['designation'];
$other_designation = $_REQUEST['other_designation'];
if($designation=='Other')
{
	$designation=$other_designation;
}
$mobile = $_REQUEST['mobile'];
$zone = $_REQUEST['zone'];
$other_zone = $_REQUEST['other_zone'];
if($zone=='Other')
{
	$zone=$other_zone;
}
$region = $_REQUEST['region'];
$other_region = $_REQUEST['other_region'];
if($region=='Other')
{
	$region=$other_region;
}
$division = $_REQUEST['division'];
$other_division = $_REQUEST['other_division'];
if($division=='Other')
{
	$division=$other_division;
}
$ccc = $_REQUEST['ccc'];
$other_ccc = $_REQUEST['other_ccc'];
if($ccc=='Other')
{
	$ccc=$other_ccc;
}
$kiosk_id = $_REQUEST['kiosk_id'];
$other_kiosk = $_REQUEST['other_kiosk'];
if($kiosk_id=='Other')
{
	$kiosk_id=$other_kiosk;
}
$reporting_to = $_REQUEST['reporting_to'];

$sql_max_empid = "SELECT MAX(emp_code) as maxempcode FROM employee_master";
$res_max_empid = mysql_query($sql_max_empid);
$row_max_empid = mysql_fetch_array($res_max_empid);
$max_row_id = $row_max_empid['maxempcode'];
if($max_row_id=='')
{
	$max_row_id='E0001';
}
else
{
	$max_row_id++;
}
$new_emp_code = $max_row_id;

/*---------------------------> INSERT INTO employee_master table <-------------------------------*/
$sql_insert_empmaster = "INSERT INTO `employee_master` SET 
								  `emp_code` = '".$new_emp_code."', 
							  `dns_emp_code` = '".$emp_id ."', 
								  `emp_name` = '".$emp_name."', 
									`acedns` = 'Y', 
							   `branch_code` = '', 
							`vertical_value` = '', 
							  `reporting_to` = '".$reporting_to."', 
									 `email` = '', 
								  `phone_no` = '".$mobile."', 
										`HQ` = '', 
							   `sale_access` = 'primary', 
							   `designation` = '".$designation."', 
									 `state` = '', 
									  `app_access` = 'Y', 
									  `zone` = '".$zone."',
									 download_time=CURRENT_TIMESTAMP()";
if(mysql_query($sql_insert_empmaster))
{
		/*---------------------------> INSERT INTO employee into changepassword table <-------------------------------*/
		$sqlchpass="SELECT emp_code FROM changepassword WHERE emp_code='".$new_emp_code."'";
		$rschpass=mysql_query($sqlchpass);
		$countchpass=mysql_num_rows($rschpass);
		if($countchpass==0)
		{
			$sql_change_password = "INSERT INTO changepassword SET emp_code='".$new_emp_code."', newpassword='1234', oldpassword='1234', is_licensed='1'";
			$res_change_password = mysql_query($sql_change_password);
		}
		/*---------------------------> INSERT INTO route_master table <-------------------------------*/
	 if(isset($division) & $division!='' && isset($ccc) & $ccc!='')
	 {
		$sqlroutechk="SELECT route_code FROM route_master WHERE route_name='".addslashes($division)."' AND emp_code='".$new_emp_code."'";
		$rsroutechk=mysql_query($sqlroutechk);
		$countroutechk=mysql_num_rows($rsroutechk);
		if($countroutechk<1 )
		{
			$sqlroutechkblank="SELECT emp_code,route_code FROM route_master WHERE route_name='".addslashes($division)."'";
			$rsroutechkblank=mysql_query($sqlroutechkblank);
			$rowroutechkblank=mysql_fetch_array($rsroutechkblank);
			$emp_code_blank=$rowroutechkblank['emp_code'];
			if($emp_code_blank=='')
			{
				$sqlupdateroue="UPDATE route_master SET emp_code='".$new_emp_code."', 
								download_time=CURRENT_TIMESTAMP() WHERE route_name='".addslashes($division)."'";
				mysql_query($sqlupdateroue);
				$route_code=$rowroutechkblank['route_code'];
			}
			else
			{
				$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code 
								FROM route_master WHERE route_code NOT LIKE '%N%'";
				$rsmaxroutecode=mysql_query($sqlmaxroutecode);
				$rowmaxroutecode=mysql_fetch_array($rsmaxroutecode);
				$new_route_code=$rowmaxroutecode['new_route_code'];
				
				$max_route_code='RT/'.($new_route_code+1);
										
				$sqlroute  = "insert into route_master ";
				$sqlroute .= " SET route_code='".$max_route_code."'";
				$sqlroute .= " ,dns_route_code=''";
				$sqlroute .= " ,route_name='".addslashes($division)."'";
				$sqlroute .= " , emp_code='".$new_emp_code."'";
				$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqlroute);
				$route_code=$max_route_code;
			}
		}
		else
		{
			$rowroutechk=mysql_fetch_array($rsroutechk);
			$route_code=$rowroutechk['route_code'];
		}
								
		/*---------------------------> INSERT INTO customer_master table <-------------------------------*/
		$sqlcustomernamechk="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($ccc)."' 
												AND emp_code='".$new_emp_code."' AND route_code='".$route_code."'";	
		if($countcustomernamechk<1)
		{
			$sqlcustomerchkblank="SELECT emp_code,route_code FROM customer_master WHERE customer_name='".addslashes($ccc)."' 
								AND route_code='".$route_code."'";
			$rscustomerchkblank=mysql_query($sqlcustomerchkblank);
			$rowcustomerchkblank=mysql_fetch_array($rscustomerchkblank);
			$emp_code_blank_customer=$rowcustomerchkblank['emp_code'];
			if($emp_code_blank_customer=='')
			{
				$sqlupdatecustomer="UPDATE customer_master SET emp_code='".$new_emp_code."', 
									download_time=CURRENT_TIMESTAMP() WHERE customer_name='".addslashes($ccc)."' 
								AND route_code='".$route_code."'";
				mysql_query($sqlupdatecustomer);
			}
			else
			{
			$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
			$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
			$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
			$max_customer_code=$rowmaxcustomercode['max_customer_code'];
					
			$max_customer_code++;
					
					$sql  = "insert into customer_master ";
					$sql .= " SET customer_code='".$max_customer_code."'";
					$sql .= " , dns_customer_code='".$kiosk_id."'";
					$sql .= " , customer_name='".addslashes($ccc)."'";
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
					$sql .= " , district='".addslashes($region)."'";
					$sql .= " , zone='".addslashes($zone)."'";
					$sql .= " , download_time=CURRENT_TIMESTAMP()";
					//exit();
					mysql_query($sql);
			}
		}
		else
		{
			$sqlupdatecustomer="UPDATE customer_master 
								SET dns_customer_code='".$kiosk_id."',
								district='".addslashes($region)."',
								zone='".addslashes($zone)."',
								download_time=CURRENT_TIMESTAMP() 
								WHERE customer_name='".addslashes($ccc)."' AND emp_code='".$new_emp_code."' AND route_code='".$route_code."'";
			mysql_query($sqlupdatecustomer);		
		}
	}
	echo "Employee informations entered successfully";
}
else
{
	echo "Error occurs";
}
mysql_close($link);
?>