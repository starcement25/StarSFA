<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
ob_start();
session_start();
require("adminUtils.php");
//if($_SESSION['admin_login']=="")  		header("location:index.php");

$attribute = $_REQUEST['attribute'];
//Customer block
$foldername=strtoupper($_SESSION['nick_name']);
if($attribute=='customer'){
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='KARMA'){
	$sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, (SELECT dns_route_code
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_code, (SELECT route_name
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_name, (SELECT dns_emp_code
					FROM employee_master
					WHERE emp_code = CRER.emp_code
					) AS emp_code_name, CRER.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (SELECT dns_branch_code
					FROM branch_master
					WHERE branch_code = CM.branch_code
					) AS branch_code, CM.cust_type, (SELECT dns_customer_code
				FROM customer_master
				WHERE customer_code = CM.rds_tag
				) AS rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, 	CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number
				FROM customer_master CM, customer_route_emp_relation CRER
				WHERE CM.route_code = CRER.route_code
				AND CM.customer_code = CRER.customer_code";
	}
	if(strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'){
	 $sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, '' AS route_code, (SELECT route_name FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_name, (SELECT emp_name FROM employee_master
					WHERE emp_code = CRER.emp_code
					) AS emp_code_name, CRER.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (SELECT branch_name FROM branch_master
					WHERE branch_code = CM.branch_code
					) AS branch_code, CM.cust_type, (SELECT CMB.customer_name FROM customer_master CMB
					WHERE CMB.customer_code = CM.rds_tag
					) AS rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email,CM.state_code
					FROM customer_master CM LEFT JOIN customer_route_emp_relation CRER ON CM.customer_code = CRER.customer_code WHERE CRER.acedns='Y'";
	}
	if(strtoupper($_SESSION['nick_name'])=='EMAMI'){
	$sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no,(SELECT dns_route_code FROM route_master WHERE route_code = CM.route_code) as route_code, (SELECT route_name FROM route_master WHERE route_code = CM.route_code) as route_name, (SELECT dns_emp_code FROM employee_master WHERE emp_code = CM.emp_code) as emp_code_name, CM.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM, customer_branch_relation CBR WHERE BM.branch_code=CBR.branch_code AND CBR.customer_code=CM.customer_code) as branch_code, CM.cust_type,(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag
) AS rds_tag, CM.sauda_validity_period,CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email,CM.visit_day,CM.state_code FROM customer_master CM WHERE CM.acedns='Y'
";
	}
	if(strtoupper($_SESSION['nick_name'])=='ABDOS'){
	$sql_customer="SELECT CM.customer_code, CM.customer_name, CM.phone_no, ( SELECT dns_route_code FROM route_master WHERE route_code = CRER.route_code ) AS route_code, ( SELECT route_name FROM route_master WHERE route_code = CRER.route_code ) AS route_name, ( SELECT emp_name FROM employee_master WHERE emp_code = CRER.emp_code ) AS emp_code_name, CRER.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, ( SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code ) AS branch, CM.cust_type, ( SELECT CMB.customer_name FROM customer_master CMB WHERE customer_code = CM.rds_tag ) AS rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email,CM.visit_day,CM.state_code FROM customer_master CM, customer_route_emp_relation CRER WHERE CM.customer_code = CRER.customer_code AND CRER.acedns='Y'
";
	}
	if(strtoupper($_SESSION['nick_name'])=='DNV'){
	$sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, (SELECT dns_route_code
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_code, (

					SELECT route_name
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_name, (

					SELECT dns_emp_code
					FROM employee_master
					WHERE emp_code = CRER.emp_code
					) AS emp_code_name, CRER.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (

					SELECT dns_branch_code
					FROM branch_master
					WHERE branch_code = CM.branch_code
					) AS branch_code, CM.cust_type, (

					SELECT dns_customer_code
					FROM customer_master CMB
					WHERE CMB.customer_code = CM.rds_tag
					) AS rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email,CM.visit_day,CM.state_code
					FROM customer_master CM, customer_route_emp_relation CRER
					WHERE CM.customer_code = CRER.customer_code";
		}
		if(strtoupper($_SESSION['nick_name'])=='SHYAM'){
			$sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, (SELECT dns_route_code
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_code, (SELECT route_name
					FROM route_master
					WHERE route_code = CRER.route_code
					) AS route_name, (SELECT emp_name
					FROM employee_master
					WHERE emp_code = CRER.emp_code
					) AS emp_code_name, CRER.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (SELECT branch_name
					FROM branch_master
					WHERE branch_code = CM.branch_code
					) AS branch_code, CM.cust_type, (SELECT CMB.customer_name
					FROM customer_master CMB
					WHERE CMB.customer_code = CM.rds_tag
					) AS rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class,
					CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email
					FROM customer_master CM, customer_route_emp_relation CRER
					WHERE CM.customer_code = CRER.customer_code";
		}
		if(strtoupper($_SESSION['nick_name'])=='RUPA'){
				$sql_customer="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, RM.dns_route_code, RM.route_name, 
							( SELECT emp_name FROM employee_master WHERE emp_code = CM.emp_code ) AS emp_code_name, 
							CM.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, 
							(SELECT branch_name FROM branch_master WHERE branch_code=CM.branch_code) As branch_code, 
							CM.cust_type, (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag, 
							CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number,CM.email,CM.visit_day FROM customer_master CM,route_master RM WHERE CM.route_code=RM.route_code
";
		}
	//exit();
	$res_customer = mysql_query($sql_customer);
	$total_customer = mysql_num_rows($res_customer);
	if($total_customer > 0){
		$header_customer = "DNS Customer Code".","."Customer Name".","."Phone no".","."Route code".","."Route Name".","."Emp Code name".","."acedns".","."Credit Limit".","."Credit Days".","."Current Balance".","."Black list".","."TD".","."Branch code".","."Cust type".","."rds tag".","."Sauda validity period".","."Address".","."Landline no".","."Owner name".","."Owner phone".","."Cust class".","."Weekly closing day".","."Coverage type".","."TIN".","."PAN".","."District".","."Minimum stock".","."Bank name".","."Bank account number".","."Email".","."Visit Day".","."State";
		//$res_prev_order_counting_master = mysql_query($sql_prev_order_counting_master);
		while($row_customer = mysql_fetch_array($res_customer)){
			$dns_customer_code = $row_customer['dns_customer_code'];
			$customer_name = '"'.preg_replace('/[\r\n]+/', '',$row_customer['customer_name']).'"';
			$phone_no = $row_customer['phone_no'];
			$route_code = str_replace(',','',$row_customer['route_code']);
			//$route_name =  '"'.str_replace(',','',$row_customer['route_name']).'"';
			$route_name = '"'.preg_replace('/[\r\n]+/', '',$row_customer['route_name']).'"';
			$emp_code_name = $row_customer['emp_code_name'];
			$acedns = $row_customer['acedns'];
			$credit_limit = $row_customer['credit_limit'];
			$credit_days = $row_customer['credit_days'];
			$current_balance = $row_customer['current_balance'];
			$black_list = $row_customer['black_list'];
			$TD = $row_customer['TD'];
			$branch_code = $row_customer['branch_code'];
			$cust_type = $row_customer['cust_type'];
			$rds_tag = '"'.$row_customer['rds_tag'].'"';
			$sauda_validity_period = $row_customer['sauda_validity_period'];
			$address =  '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['address'])).'"';
			$landline_no = $row_customer['landline_no'];
			$owner_name = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['owner_name'])).'"';
			$owner_phone = $row_customer['owner_phone'];
			$cust_class = $row_customer['cust_class'];
			$weekly_closing_day = $row_customer['weekly_closing_day'];
			$coverage_type = $row_customer['coverage_type'];
			$TIN = $row_customer['TIN'];
			$PAN = $row_customer['PAN'];
			$district = $row_customer['district'];
			$minimum_stock = $row_customer['minimum_stock'];
			$bank_name = $row_customer['bank_name'];
			$bank_account_number= $row_customer['bank_account_number'];
			$email= $row_customer['email'];
			$visit_day= $row_customer['visit_day'];
			$state_code= $row_customer['state_code'];

			$contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code."\n";
			$count++;
		}
		if ( !file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$datacontents=$header_customer."\n".$contents;
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/customer master.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/customer master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
		}
}

//Employee block
if($attribute=='employee'){
	if(strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'){
	$sql_employee="SELECT EM.dns_emp_code, EM.emp_name, EM.branch_code, EM.vertical_value, (SELECT emp_name FROM employee_master
				WHERE emp_code = EM.reporting_to) AS reporting_to, EM.email, EM.phone_no, EM.sale_access, EM.designation, EM.HQ, EM.state, EM.zone,
				EM.acedns,EM.District,EM.DOJ FROM employee_master EM WHERE EM.acedns='Y' ORDER BY EM.emp_code";
	}
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER'){
	$sql_employee="SELECT EM.dns_emp_code, EM.emp_name, (SELECT dns_branch_code FROM branch_master
				WHERE branch_code = EM.branch_code) AS branch_code, EM.vertical_value, (SELECT dns_emp_code FROM employee_master
				WHERE emp_code = EM.reporting_to) AS reporting_to, EM.email, EM.phone_no, EM.sale_access, EM.designation, EM.HQ, EM.state, EM.zone,
				EM.acedns,EM.District,EM.DOJ FROM employee_master EM ORDER BY EM.emp_code";
	}
	if(strtoupper($_SESSION['nick_name'])=='EMAMI'){
	 $sql_employee="SELECT EM.dns_emp_code, EM.emp_name, (SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code)) as branch_code, EM.vertical_value, ( SELECT dns_emp_code FROM employee_master WHERE emp_code = EM.reporting_to ) AS reporting_to, EM.email, EM.phone_no, EM.sale_access, EM.designation, EM.HQ, EM.state, EM.zone, EM.acedns,EM.District FROM employee_master EM WHERE EM.acedns='Y'";
	}
	if(strtoupper($_SESSION['nick_name'])=='SHYAM'){
	$sql_employee="SELECT EM.dns_emp_code, EM.emp_name, EM.branch_code, EM.vertical_value, (SELECT emp_name FROM employee_master
				WHERE emp_code = EM.reporting_to) AS reporting_to, EM.email, EM.phone_no, EM.sale_access, EM.designation, EM.HQ, EM.state, EM.zone,
				EM.acedns,EM.District,EM.DOJ FROM employee_master EM ORDER BY EM.emp_code";
	}

	//exit();
	$res_employee = mysql_query($sql_employee);
	$total_employee = mysql_num_rows($res_employee);

	if($total_employee > 0){
		$header_employee = "DNS Emp Code".","."Employee Name".","."Branch".","."Vertical value".","."Reporting to".","."Email".","."Phone no".","."Sale access".","."Designation".","."HQ".","."State".","."Zone".","."acedns".","."District".","."Functionality".","."Functionality val".","."DOJ";
		//$res_prev_order_counting_master = mysql_query($sql_prev_order_counting_master);
		while($row_employee = mysql_fetch_array($res_employee)){
			$dns_emp_code = $row_employee['dns_emp_code'];
			$emp_name = str_replace(',','',$row_employee['emp_name']);
			$branch_code = $row_employee['branch_code'];
			$vertical_value = str_replace(',',';',$row_employee['vertical_value']);
			$reporting_to = str_replace(',','',$row_employee['reporting_to']);
			$email = $row_employee['email'];
			$phone_no = $row_employee['phone_no'];
			$sale_access = $row_employee['sale_access'];
			$designation = $row_employee['designation'];
			$HQ = str_replace(',','',$row_employee['HQ']);
			$state = str_replace(',','',$row_employee['state']);
			$zone = str_replace(',','',$row_employee['zone']);
			$acedns = $row_employee['acedns'];
			$District = $row_employee['District'];
			$District = str_replace(',','',$District);
			if($row_employee[DOJ]=='0000-00-00') $DOJ='';
			else
			{
				$DOJ = date('d-m-Y',strtotime($row_employee['DOJ']));
			}
			$functionality='';
			$functionality_val='';

			$contents_emp.=$dns_emp_code.",".$emp_name.",".$branch_code.",".$vertical_value.",".$reporting_to.",".$email.",".$phone_no.",".$sale_access.",".$designation.",".$HQ.",".$state.",".$zone.",".$acedns.",".$District.",".$functionality.",".$functionality_val.",".$DOJ."\n";
			$count++;
		}
		$datacontents=$header_employee."\n".$contents_emp;
		if (!file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Employee master.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/Employee master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
		}
}
if($attribute=='distributor_route_emp'){
	if(strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'){
		$sql_distributor="SELECT CM.customer_name, (SELECT emp_name FROM employee_master WHERE emp_code = DRR.emp_code ) AS emp_code_name,DRR.state AS state_name,(SELECT route_name FROM route_master WHERE route_code = DRR.route_code ) AS route_name FROM `distributor_route_relation` DRR, customer_master CM WHERE DRR.distributor_code = CM.customer_code";
	}
	$res_distributor = mysql_query($sql_distributor);
	$total_distributor = mysql_num_rows($res_distributor);

	if($total_distributor > 0){
		$header_distributor = "Customer Name".","."Employee Name".","."Route Name".","."State";
		//$res_prev_order_counting_master = mysql_query($sql_prev_order_counting_master);
		while($row_distributor = mysql_fetch_array($res_distributor)){
			$customer_name = str_replace(',','',$row_distributor['customer_name']);
			$emp_code_name = str_replace(',','',$row_distributor['emp_code_name']);
			$state_name = str_replace(',','',$row_distributor['state_name']);
			$route_name = str_replace(',','',$row_distributor['route_name']);

			$contents_distributor.=$customer_name.",".$emp_code_name.",".$route_name.",".$state_name."\n";
			$count++;
		}
		$datacontents=$header_distributor."\n".$contents_distributor;
		if (!file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Distributor_emp_route_relation.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/Distributor_emp_route_relation.csv";
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	 }
}
if($attribute=='product')
{
	if(strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'){
	$sql_product="SELECT product_master.branch_code, product_master.dns_prod_code as Prod_Code, product_master.prod_desc, product_group_master.product_group_name, product_sub_group_master.product_sub_group_name, product_master.product_brand_code, product_master.cl_stk, product_master.acedns, product_master.black_list, product_master.vertical_value, product_master.UOM1, product_master.UOM2, product_master.conversion_factor as Conversion, product_master.pack_size as Pack_Size, product_master.UOM3, product_master.conversion_factor_two as Conversion2, product_master.TD FROM `product_master`, `product_group_master`, `product_sub_group_master` WHERE product_master.product_group_code=product_group_master.product_group_code AND product_master.product_sub_group_code=product_sub_group_master.product_sub_group_code
";
	}
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER'){
	$sql_product="SELECT product_master.branch_code, product_master.dns_prod_code as Prod_Code, product_master.prod_desc, product_group_master.product_group_name, product_sub_group_master.product_sub_group_name, product_brand_master.product_brand_name, product_master.cl_stk, product_master.acedns, product_master.black_list, product_master.vertical_value, product_master.UOM1, product_master.UOM2, product_master.conversion_factor as Conversion, product_master.pack_size as Pack_Size, product_master.UOM3, product_master.conversion_factor_two as Conversion2, product_master.TD FROM `product_master`, `product_group_master`, `product_sub_group_master`,product_brand_master WHERE product_master.product_group_code=product_group_master.product_group_code AND
product_master.product_sub_group_code=product_sub_group_master.product_sub_group_code AND product_master.product_brand_code=product_brand_master.product_brand_code
";
	}
	if(strtoupper($_SESSION['nick_name'])=='KARMA'){
	$sql_product="SELECT product_master.branch_code, product_master.dns_prod_code as Prod_Code, product_master.prod_desc, product_group_master.product_group_name, product_master.product_sub_group_code, product_master.product_brand_code, product_master.cl_stk, product_master.acedns, product_master.black_list, product_master.vertical_value, product_master.UOM1, product_master.UOM2, product_master.conversion_factor as Conversion, product_master.pack_size as Pack_Size, product_master.UOM3, product_master.conversion_factor_two as Conversion2, product_master.TD FROM `product_master`, `product_group_master` WHERE product_master.product_group_code=product_group_master.product_group_code 
";
	}
	if(strtoupper($_SESSION['nick_name'])=='SHYAM'){
	 $sql_product="SELECT '', product_master.dns_prod_code as Prod_Code, product_master.prod_desc, product_master.product_group_code, product_master.product_sub_group_code,
	 product_master.product_brand_code, product_master.cl_stk, product_master.acedns, product_master.black_list, product_master.vertical_value, product_master.UOM1, product_master.UOM2, product_master.conversion_factor as Conversion, product_master.pack_size as Pack_Size, product_master.UOM3, product_master.conversion_factor_two as Conversion2, product_master.TD FROM `product_master`";
	}

	$res_product = mysql_query($sql_product);
	$total_product = mysql_num_rows($res_product);

	if($total_product > 0){
		$header_product = "Branch code/name".","."Prod_Code".","."prod_desc".","."brand_code (fk)/brand_name".","."brand_form_code (fk)/brand_form_name".","."brand_sub_form_code(fk)/brand_sub_form_name".","."cl_stk".","."acends".","."black_list".","."vertical_value".","."UOM1".","."UOM2".","."Conversion".","."Pack size".","."UOM3".","."Conversion2".","."TD";
		//$res_prev_order_counting_master = mysql_query($sql_prev_order_counting_master);
		while($row_product = mysql_fetch_array($res_product)){
			$branch_code = str_replace(',','',$row_product['branch_code']);
			$Prod_Code = str_replace(',','',$row_product['Prod_Code']);
			$prod_desc = str_replace(',','',$row_product['prod_desc']);
			$product_group_name = str_replace(',','',$row_product['product_group_name']);
			$product_sub_group_name = str_replace(',','',$row_product['product_sub_group_name']);
			$product_brand_name = str_replace(',','',$row_product['product_brand_name']);
			$cl_stk = $row_product['cl_stk'];
			$acedns = $row_product['acedns'];
			$black_list = $row_product['black_list'];
			$vertical_value = $row_product['vertical_value'];
			$UOM1 = $row_product['UOM1'];
			$UOM2 = $row_product['UOM2'];
			$Conversion = $row_product['Conversion'];
			$Pack_Size = $row_product['Pack_Size'];
			$UOM3 = $row_product['UOM3'];
			$Conversion2 = $row_product['Conversion2'];
			$TD = $row_product['TD'];

			$prod_desc = $row_product['prod_desc'];

			$contents_product.=$branch_code.",".$Prod_Code.",".$prod_desc.",".$product_group_name.",".$product_sub_group_name.",".$product_brand_name.",".$cl_stk.",".$acedns.",".$black_list.",".$vertical_value.",".$UOM1.",".$UOM2.",".$Conversion.",".$Pack_Size.",".$UOM3.",".$Conversion2.",".$TD."\n";
			$count++;
		}
		$datacontents=$header_product."\n".$contents_product;
		if (!file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/sku master.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/sku master.csv";
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	 }
}
if($attribute=='packing'){
	$sql_packing= "SELECT * FROM (SELECT dns_prod_code, prod_desc, packing_cost, no_pc_one_case,plant_name,packing_realization FROM
					packing_master ORDER BY datetime DESC) AS SAT GROUP BY 1";
	$res_packing = mysql_query($sql_packing);
	$total_packing = mysql_num_rows($res_packing);
	if($total_packing > 0){
		$header_packing = "dns prod code".","."prod desc".","."packing cost".","."no pc one case".","."plant".","."packing realization";
		while($row_packing = mysql_fetch_array($res_packing)){
			$dns_prod_code = $row_packing['dns_prod_code'];
			$prod_desc = str_replace(',','',$row_packing['prod_desc']);
			$packing_cost = $row_packing['packing_cost'];
			$no_pc_one_case = $row_packing['no_pc_one_case'];
			$plant = $row_packing['plant_name'];
			$packing_realization = $row_packing['packing_realization'];

			$contents_packing.=$dns_prod_code.",".$prod_desc.",".$packing_cost.",".$no_pc_one_case.",".$plant.",".$packing_realization."\n";
			$count++;
		}
		$datacontents=$header_packing."\n".$contents_packing;
		if (!file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/packing master.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/packing master.csv";
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	 }
}
if($attribute=='mrp')
{
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='KARMA'){
	$sqlmrp="SELECT(SELECT dns_branch_code FROM branch_master WHERE branch_code=MRP.branch_code) as branch, PM.dns_prod_code, '' AS mrp_code, MRP.mrp, MRP.sale_rate, MRP.vertical_value, 0 AS destination_code, 0 AS sale_type, MRP.acedns, MRP.ws_rate, MRP.distributor_rate, MRP.ss_rate, MRP.depot_rate FROM product_master PM, mrp MRP WHERE MRP.product_code = PM.prod_code";
	}
	if(strtoupper($_SESSION['nick_name'])=='SHYAM'){
	$sqlmrp="SELECT '' AS branch, product_master.prod_desc AS dns_prod_code, '' AS mrp_code, mrp.mrp, mrp.sale_rate, mrp.vertical_value, 0 AS destination_code, 0 AS sale_type, mrp.acedns, mrp.ws_rate, mrp.distributor_rate, mrp.ss_rate, mrp.depot_rate, (SELECT statename FROM state_master WHERE state_code=mrp.state_code) AS state FROM `product_master`, `mrp` WHERE mrp.product_code = product_master.prod_code";
	}
	if(strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'){
		$sqlmrp="SELECT MRP.branch_code as branch, PM.dns_prod_code, MRP.mrp_code, MRP.mrp, MRP.sale_rate, MRP.vertical_value, 0 AS destination_code, 0 AS sale_type, MRP.acedns, MRP.ws_rate, MRP.distributor_rate, MRP.ss_rate, MRP.depot_rate, (SELECT statename FROM state_master WHERE state_code=MRP.state_code) as state FROM product_master PM, mrp MRP WHERE MRP.product_code = PM.prod_code";
	}
	$res_mrp = mysql_query($sqlmrp);
	$total_mrp = mysql_num_rows($res_mrp);

	if($total_mrp > 0){
		$header_mrp = "Branch code/name".","."Prod_Code".","."mrp_code".","."mrp".","."sale_rate".","."vertical_value".","."destination_code".",".
		"sale_type".","."acedns".","."ws_rate".","."distributor_rate".","."ss_rate".","."depot_rate".","."statename";
		//$res_prev_order_counting_master = mysql_query($sql_prev_order_counting_master);
		while($row_mrp = mysql_fetch_array($res_mrp)){
			$branch_code = $row_mrp['branch'];
			$Prod_Code = $row_mrp['dns_prod_code'];
			$mrp_code = $row_mrp['mrp_code'];
			$mrp_code='';
			$mrp = $row_mrp['mrp'];
			$sale_rate = $row_mrp['sale_rate'];
			$vertical_value = $row_mrp['vertical_value'];
			$destination_code = $row_mrp['destination_code'];
			$sale_type = $row_mrp['sale_type'];
			$acedns = $row_mrp['acedns'];
			$ws_rate = $row_mrp['ws_rate'];
			$distributor_rate = $row_mrp['distributor_rate'];
			$ss_rate = $row_mrp['ss_rate'];
			$depot_rate = $row_mrp['depot_rate'];
			$state = $row_mrp['state'];


			$contents_mrp.=$branch_code.",".$Prod_Code.",".$mrp_code.",".$mrp.",".$sale_rate.",".$vertical_value.",".$destination_code.",".$sale_type.",".$acedns.",".$ws_rate.",".$distributor_rate.",".$ss_rate.",".$depot_rate.",".$state."\n";
			$count++;
		}
		$datacontents=$header_mrp."\n".$contents_mrp;
		if (!file_exists("../dump/$foldername")){
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/MRP.csv","wb");
		fwrite($fp,$datacontents);
		fclose($fp);
		echo "$foldername/MRP.csv";
	 }
	 else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	 }
}

mysql_close($link);