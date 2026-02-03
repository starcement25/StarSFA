<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit(0);
ini_set('memory_limit', '-1');
ob_start();

// check error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
//require("include/functions.php");

//if($_SESSION['admin_login']=="")  		header("location:index.php");
$contents = '';
$count = 0;
$attribute = $_REQUEST['attribute'];
//Customer block
$foldername = strtoupper($_SESSION['nick_name']);
$basepath = '/var/www/html/misreport/dump/';
//$attribute="product";
//$foldername='DURO';
if ($attribute == 'branch') {
	/*$sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$login_emp_code."'";
	$rsbranchcode=mysqli_query($link,$sqlbranchcode);
	$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
	$branchcodelist=$rowbranchcode['branch_code'];*/

	$sql_branch = "SELECT BM.dns_branch_code, BM.branch_name,BM.branch_location,BM.comp_code,BM.branch_state,BM.branch_email_id,
	 			BM.branch_accounts_email_id,BM.alternative_email_id,BM.plant_name,BM.HQ,BM.is_plant FROM branch_master BM
				WHERE BM.acedns='Y'";

	$res_branch = mysqli_query($link, $sql_branch);
	$total_branch = mysqli_num_rows($res_branch);
	//exit();
	//echo $total_branch;
	$contents_branch = '';
	if ($total_branch > 0) {
		$header_branch = "DNS Branch Code" . "," . "Branch Name" . "," . "Branch Location" . "," . "Comp Code" . "," . "Branch State" . "," . "Branch Email" . "," .
			"Branch Account Email" . "," . "Alternative Email" . "," . "Plant name" . "," . "HQ" . "," . "Is plant";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_branch = mysqli_fetch_assoc($res_branch)) {
			$dns_branch_code = $row_branch['dns_branch_code'];
			$branch_name = str_replace(',', '', $row_branch['branch_name']);
			$branch_location = $row_branch['branch_location'];
			$comp_code = $row_branch['comp_code'];
			$branch_state = $row_branch['branch_state'];
			$branch_email_id = $row_branch['branch_email_id'];
			$branch_accounts_email_id = $row_branch['branch_accounts_email_id'];
			$alternative_email_id = $row_branch['alternative_email_id'];
			$plant_name = $row_branch['plant_name'];
			$HQ = str_replace(',', '', $row_branch['HQ'] ?? '');
			$is_plant = $row_branch['is_plant'];

			$contents_branch .= $dns_branch_code . "," . $branch_name . "," . $branch_location . "," . $comp_code . "," . $branch_state . "," . $branch_email_id . "," . $branch_accounts_email_id . "," . $alternative_email_id . "," . $plant_name . "," . $HQ . "," . $is_plant . "\n";
			$count++;
		}
		$datacontents = $header_branch . "\n" . $contents_branch;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		//$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Branch master.csv","wb");
		$fp = fopen($basepath . $foldername . "/Branch master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Branch master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		//if($foldername="CORAL"){
		$datacontents = "No Records Found";
		$folderPath = __DIR__ . "/dump/$foldername";
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		//$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Branch master.csv","wb");
		$fp = fopen($basepath . $foldername . "/Branch master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Branch master.csv";
		/*}else{
	         echo "<strong><font color=\"red\">No Records Found</font></strong>";
	     }*/
	}
}
if ($attribute == 'branch_competitor') {
	/*$sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$login_emp_code."'";
	$rsbranchcode=mysqli_query($link,$sqlbranchcode);
	$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
	$branchcodelist=$rowbranchcode['branch_code'];*/

	$sql_branch_competitor = "SELECT BM.dns_branch_code,CGM.competitor_name,CGM.acedns FROM branch_master BM,competitor_group_master CGM
				WHERE BM.branch_code=CGM.branch_code ORDER BY BM.dns_branch_code ASC";
	$res_branch_competitor = mysqli_query($link, $sql_branch_competitor);
	$total_branch_competitor = mysqli_num_rows($res_branch_competitor);
	//exit();
	$contents_branch_competitor = '';
	if ($total_branch_competitor > 0) {
		$header_branch_competitor = "Group name" . "," . "Branch Code" . "," . "Brand" . "," . "Acedns";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_branch_competitor = mysqli_fetch_assoc($res_branch_competitor)) {
			$dns_branch_code = $row_branch_competitor['dns_branch_code'];
			$competitor_name = $row_branch_competitor['competitor_name'];
			$acedns = $row_branch_competitor['acedns'];
			$contents_branch_competitor .= '' . "," . $dns_branch_code . "," . $competitor_name . "," . $acedns . "\n";
		}
		$datacontents = $header_branch_competitor . "\n" . $contents_branch_competitor;
		$folderPath = __DIR__ . "/dump/$foldername";
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen($basepath . $foldername . "/BRANCH WISE COMPETITOR.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/BRANCH WISE COMPETITOR.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}

if ($attribute == 'branch_wise_competitor') {
	/*$sqlbranchcode="SELECT branch_code FROM employee_master WHERE emp_code='".$login_emp_code."'";
	$rsbranchcode=mysqli_query($link,$sqlbranchcode);
	$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
	$branchcodelist=$rowbranchcode['branch_code'];*/

	$sql_branch_competitor = "SELECT BM.dns_branch_code,BM.branch_name,CGM.competitor_name,CGM.acedns,CGM.product_type FROM branch_master BM,competitor_group_master CGM
				WHERE BM.branch_code=CGM.branch_code ORDER BY BM.dns_branch_code ASC";
	$res_branch_competitor = mysqli_query($link, $sql_branch_competitor);
	$total_branch_competitor = mysqli_num_rows($res_branch_competitor);
	//exit();
	$contents_branch_competitor = '';
	if ($total_branch_competitor > 0) {
		$header_branch_competitor = "COMPANY NAME" . "," . "Product Name in SFA" . "," . "Branch Code" . "," . "Branch Name" . "," . "Active" . "," . "PRODUCT TYPE";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_branch_competitor = mysqli_fetch_assoc($res_branch_competitor)) {
			$dns_branch_code = $row_branch_competitor['dns_branch_code'];
			$branch_name = $row_branch_competitor['branch_name'];
			$competitor_name = $row_branch_competitor['competitor_name'];
			$product_type = $row_branch_competitor['product_type'];
			$acedns = $row_branch_competitor['acedns'];
			$contents_branch_competitor .= '' . "," . $competitor_name . "," . $dns_branch_code . "," . $branch_name . "," . $acedns . "," . $product_type . "\n";
		}
		$datacontents = $header_branch_competitor . "\n" . $contents_branch_competitor;
		$folderPath = __DIR__ . "/dump/$foldername";
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen($basepath . $foldername . "/BRANCH WISE COMPETITOR.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/BRANCH WISE COMPETITOR.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}

// if ($attribute == 'customer') {
// 	if (modified_customer_emp_route == 'yes') {
// 		if (providing_code == 'yes') {
// 			$emp_query = "(SELECT GROUP_CONCAT(EM.dns_emp_code SEPARATOR ';') FROM employee_master EM,customer_route_emp_relation CRER 
// 		  WHERE EM.emp_code = CRER.emp_code AND CRER.customer_code=CM.customer_code) AS emp_code_name";
// 			$customer = "(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
// 			$sorting_cond = "ORDER BY CM.dns_customer_code ASC";
// 			//$group_cond="GROUP BY CM.dns_customer_code";

// 		} else {
// 			if (strtoupper($foldername) == 'ELEGANT') {
// 				$emp_query = "(SELECT dns_emp_code FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name";
// 			} else {
// 				$emp_query = "(SELECT emp_name FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name";
// 			}
// 			if ($foldername == 'SUPERSHAKTI') {
// 				$customer = "(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
// 			} else {
// 				$customer = " (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
// 			}

// 			$sorting_cond = "ORDER BY CM.customer_name ASC";
// 			//$group_cond="GROUP BY CM.customer_name ";
// 		}
// 		if (sauda_depot_wise == 'yes' || $foldername == 'ASL') {
// 			$branch = "(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM,customer_branch_relation CBR WHERE BM.branch_code=CBR.branch_code AND CBR.customer_code=CM.customer_code) as branch_code";
// 		} else {
// 			if (providing_code == 'yes') {
// 				$branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
// 				$branch .= ",(SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_name";
// 			} else {
// 				$branch = "(SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
// 			}
// 		}
// 		if (strtoupper($foldername) == 'ABDOS') {
// 			$emp_hq_query = "(SELECT HQ FROM employee_master WHERE emp_code = CRER.emp_code) AS HQ,";
// 		} else {
// 			$emp_hq_query = "";
// 		}
// 		if (strtoupper($foldername) == 'STAR') {
// 			/* $sql="SELECT DISTINCT CM.dns_customer_code,
// 				  CM.customer_name,
// 				  CM.phone_no,
// 				  (SELECT RM.dns_route_code FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CR.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_code, 
// 				  (SELECT RM.route_name FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CR.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_name,
// 				  (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
// 				  (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
// 				   ".$emp_query.",
// 				   GROUP_CONCAT(CRER.acedns SEPARATOR ';') AS acedns,
// 				   CM.credit_limit,
// 				   CM.credit_days,
// 				   CM.current_balance,
// 				   CM.black_list,
// 				   CM.TD,
// 				   ".$branch.",
// 				   CM.cust_type,
// 				   ".$customer.",
// 				   CM.sauda_validity_period,
// 				   CM.address,
// 				   CM.landline_no,
// 				   CM.owner_name,
// 				   CM.owner_phone,
// 				   CM.cust_class,
// 				   CM.weekly_closing_day,
// 				   CM.coverage_type,
// 				   CM.TIN,
// 				   CM.PAN,
// 				   CM.district,
// 				   CM.minimum_stock,
// 				   CM.bank_name,
// 				   CM.bank_account_number,
// 				   CM.email,
// 				   CM.visit_day,
// 				   CM.state_code,
// 				   CM.monthly_potential,
// 				   CM.sauda_limit,
// 				   CM.incoterms,
// 				   CM.loadability_ton,
// 				   CM.transport_mode,
// 				   CM.sauda_type,
// 				   CM.zone,
// 				   CM.visit_sequence,
// 				   CM.appointment_date,
// 				   CM.date_of_birth,
// 				   CM.date_of_anniversary,
// 				   CM.whatsapp_no,
// 				   CM.beneficiary_name,
// 				   CM.IFS_code,
// 				   CM.pin,
// 				   CM.base_latt,
// 				   CM.base_longi,CM.retailer_app,CM.is_new_customer,CM.category_of_store,CM.is_nlp,CM.cluster,CM.SAP_customer_code FROM customer_master CM INNER JOIN customer_route_emp_relation CRER
// 				ON CM.customer_code = CRER.customer_code   GROUP BY CM.dns_customer_code $sorting_cond";*/
// 			$sql = "SELECT DISTINCT CM.dns_customer_code,
// 				  CM.customer_name,
// 				  CM.phone_no,
// 				  (SELECT RM.dns_route_code FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CM.route_code AND CM.customer_code=CR.customer_code ORDER BY CM.acedns DESC LIMIT 0,1) AS route_code, 
// 				  (SELECT RM.route_name FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CM.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_name,
// 				  (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
// 				  (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
// 				   (SELECT GROUP_CONCAT(EM.dns_emp_code SEPARATOR ';') FROM employee_master EM,customer_route_emp_relation CRER 
// 		  WHERE EM.emp_code = CRER.emp_code AND CRER.customer_code=CM.customer_code) AS emp_code_name,
// 				   GROUP_CONCAT(CRER.acedns SEPARATOR ';') AS acedns,
// 				   CM.credit_limit,
// 				   CM.credit_days,
// 				   CM.current_balance,
// 				   CM.black_list,
// 				   CM.TD,
// 				   (SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code,
//                    (SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_name,
// 				   CM.cust_type,
// 				   (SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag,
// 				   CM.sauda_validity_period,
// 				   CM.address,
// 				   CM.landline_no,
// 				   CM.owner_name,
// 				   CM.owner_phone,
// 				   CM.cust_class,
// 				   CM.weekly_closing_day,
// 				   CM.coverage_type,
// 				   CM.TIN,
// 				   CM.PAN,
// 				   CM.district,
// 				   CM.minimum_stock,
// 				   CM.bank_name,
// 				   CM.bank_account_number,
// 				   CM.email,
// 				   CM.visit_day,
// 				   CM.state_code,
// 				   CM.monthly_potential,
// 				   CM.sauda_limit,
// 				   CM.incoterms,
// 				   CM.loadability_ton,
// 				   CM.transport_mode,
// 				   CM.sauda_type,
// 				   CM.zone,
// 				   CM.visit_sequence,
// 				   CM.appointment_date,
// 				   CM.date_of_birth,
// 				   CM.date_of_anniversary,
// 				   CM.whatsapp_no,
// 				   CM.beneficiary_name,
// 				   CM.IFS_code,
// 				   CM.pin,
// 				   CM.base_latt,
// 				   CM.base_longi,CM.retailer_app,CM.is_new_customer,CM.category_of_store,CM.is_nlp,CM.cluster,CM.SAP_customer_code FROM customer_master CM INNER JOIN customer_route_emp_relation CRER
// 				ON CM.customer_code = CRER.customer_code  GROUP BY CM.dns_customer_code ORDER BY CM.dns_customer_code ASC;";
// 		} else {
// 			$sql = "SELECT DISTINCT CM.dns_customer_code,
// 			  CM.customer_name,
// 			  CM.phone_no,
// 			  (SELECT dns_route_code FROM route_master WHERE route_code = CRER.route_code) AS route_code,
// 			  (SELECT route_name FROM route_master WHERE route_code = CRER.route_code) AS route_name,
// 			   (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
// 				  (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
// 			   " . $emp_query . ",
// 			   " . $emp_hq_query . "
// 			   CRER.acedns,
// 			   CM.credit_limit,
// 			   CM.credit_days,
// 			   CM.current_balance,
// 			   CM.black_list,
// 			   CM.TD,
// 			   " . $branch . ",
// 			   CM.cust_type,
// 			   " . $customer . ",
// 			   CM.sauda_validity_period,
// 			   CM.address,
// 			   CM.landline_no,
// 			   CM.owner_name,
// 			   CM.owner_phone,
// 			   CM.cust_class,
// 			   CM.weekly_closing_day,
// 			   CM.coverage_type,
// 			   CM.TIN,
// 			   CM.PAN,
// 			   CM.district,
// 			   CM.minimum_stock,
// 			   CM.bank_name,
// 			   CM.bank_account_number,
// 			   CM.email,
// 			   CM.visit_day,
// 			   CM.state_code,
// 			   CM.monthly_potential,
// 			   CM.sauda_limit,
// 			   CM.incoterms,
// 			   CM.loadability_ton,
// 			   CM.transport_mode,
// 			   CM.sauda_type,
// 			   CM.zone,
// 			   CM.visit_sequence,
// 			   CM.appointment_date,
// 			   CM.date_of_birth,
// 			   CM.date_of_anniversary,
// 			   CM.whatsapp_no,
// 			   CM.beneficiary_name,
// 			   CM.IFS_code,
// 			   CM.pin,
// 			   CM.retailer_app,
// 			   CM.base_latt,
// 			   CM.base_longi,
// 			   CM.retailer_app,CM.is_new_customer,CM.category_of_store,CM.is_nlp,CM.cluster FROM customer_master CM INNER JOIN customer_route_emp_relation CRER ON CM.customer_code = CRER.customer_code  $sorting_cond";
// 		}
// 	} else {
// 		if (providing_code == 'yes') {

// 			$emp_query = "(SELECT dns_emp_code FROM employee_master WHERE emp_code = CM.emp_code) AS emp_code_name";
// 			$customer = "(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
// 		} else {

// 			$emp_query = "(SELECT emp_name FROM employee_master WHERE emp_code = CM.emp_code) AS emp_code_name";
// 			$customer = " (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
// 		}
// 		if (sauda_depot_wise == 'yes' || $foldername == 'ASL') {
// 			$branch = "(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM,customer_branch_relation CBR WHERE BM.branch_code=CBR.branch_code AND CBR.customer_code=CM.customer_code) as branch_code";
// 		} else {
// 			if (providing_code == 'yes') {
// 				$branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
// 			} else {
// 				$branch = "(SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
// 			}
// 		}
// 		if ($foldername == 'RUPA') {
// 			$sql = "SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, RM.dns_route_code, RM.route_name, 
// 							( SELECT emp_name FROM employee_master WHERE emp_code = CM.emp_code ) AS emp_code_name,
// 							( SELECT vertical_value FROM employee_master WHERE emp_code = CM.emp_code ) AS vertical_value, 
// 							CM.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, 
// 							(SELECT branch_name FROM branch_master WHERE branch_code=CM.branch_code) As branch_code, 
// 							CM.cust_type, (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag, 
// 							CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, 
// 							CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, 
// 							CM.bank_account_number,CM.email,CM.visit_day,CM.state_code,CM.monthly_potential,CM.sauda_limit,
// 				   			CM.incoterms,CM.loadability_ton,CM.transport_mode,CM.sauda_type,CM.zone,CM.visit_sequence,CM.appointment_date,
// 				   			CM.date_of_birth,CM.date_of_anniversary,CM.whatsapp_no,CM.beneficiary_name,CM.IFS_code,CM.pin,
// 				   			CM.base_latt,CM.base_longi 
// 							FROM customer_master CM,route_master RM 
// 							WHERE CM.route_code=RM.route_code ORDER BY vertical_value ASC";
// 		} else {
// 			$sql = "SELECT CM.dns_customer_code,
// 					  CM.customer_name,
// 					  CM.phone_no,
// 					  (SELECT dns_route_code FROM route_master WHERE route_code = CM.route_code) as route_code,
// 					  (SELECT route_name FROM route_master WHERE route_code = CM.route_code) as route_name,
// 					   " . $emp_query . ",
// 					  CM.acedns,
// 					  CM.credit_limit,
// 					  CM.credit_days,
// 					  CM.current_balance,
// 					  CM.black_list,
// 					  CM.TD,
// 					  " . $branch . ",
// 					  CM.cust_type,
// 					  " . $customer . ",
// 					  CM.sauda_validity_period,
// 					  CM.address,
// 					  CM.landline_no,
// 					  CM.owner_name,
// 					  CM.owner_phone,
// 					  CM.cust_class,
// 					  CM.weekly_closing_day,
// 					  CM.coverage_type,
// 					  CM.TIN,
// 					  CM.PAN,
// 					  CM.district,
// 					  CM.minimum_stock,
// 					  CM.bank_name,
// 					  CM.bank_account_number,
// 					  CM.email,
// 					  CM.visit_day,
// 					  CM.state_code,
// 					  CM.monthly_potential,
// 					   CM.sauda_limit,
// 					   CM.incoterms,
// 					   CM.loadability_ton,
// 					   CM.transport_mode,
// 					   CM.sauda_type,
// 					   CM.zone,
// 					   CM.visit_sequence,
// 					   CM.appointment_date,
// 					   CM.date_of_birth,
// 					   CM.date_of_anniversary,
// 					   CM.whatsapp_no,
// 					   CM.beneficiary_name,
// 					   CM.IFS_code,
// 					   CM.pin,
// 					   CM.base_latt,CM.base_longi,CM.is_new_customer
// 					   FROM customer_master CM WHERE CM.acedns='Y'";
// 		}
// 	}
// 	echo "<pre>";
// 	print_r($sql);
// 	$res = mysqli_query($link, $sql);
// 	$total_customer = mysqli_num_rows($res);

// 	if ($total_customer > 0) {
// 		$dns_customer_code_array = array();

// 		if (strtoupper($foldername) == 'RUPA') {
// 			$header_customer = "DNS Customer Code" . "," . "Customer Name" . "," . "Phone no" . "," . "Route code" . "," . "Route Name" . "," . "Emp Code name" . "," . "Vertical" . "," . "acedns" . "," . "Credit Limit" . "," . "Credit Days" . "," . "Current Balance" . "," . "Black list" . "," . "TD" . "," . "Branch code" . "Branch Name" . "," . "," . "Cust type" . "," . "rds tag" . "," . "Sauda validity period" . "," . "Address" . "," . "Landline no" . "," . "Owner name" . "," . "Owner phone" . "," . "Cust class" . "," . "Weekly closing day" . "," . "Coverage type" . "," . "TIN" . "," . "PAN" . "," . "District" . "," . "Minimum stock" . "," . "Bank name" . "," . "Bank account number" . "," .
// 				"Email" . "," . "Visit Day" . "," . "State" . "," . "Monthly Potential" . "," . "Sauda limit" . "," . "Incoterms" . "," . "Loadability ton" . "," . "Transport mode" . "," . "Sauda type" . "," . "Zone" . "," . "Visit sequence" . "," . "Appointment date" . "," . "date_of_birth" . "," . "date_of_anniversary" . "," . "whatsapp_no" . "," . "beneficiary_name" . "," . "IFS_code" . "," . "pin" . "," . "Base lattitude" . "," . "Base longitude" . "," . "Retailer app" . "," . "New Customer" . "," . "Category of store" . "," . "NLP" . "," . "Area" . "," . "Route No";
// 		} else {
// 			$header_customer = "DNS Customer Code" . "," . "Customer Name" . "," . "Phone no" . "," . "Route code" . "," . "Route Name" . "," . "Emp Code name" . "," . "acedns" . "," . "Credit Limit" . "," . "Credit Days" . "," . "Current Balance" . "," . "Black list" . "," . "TD" . "," . "Branch code" . "," . "Branch Name" . "," . "Cust type" . "," . "rds tag" . "," . "Sauda validity period" . "," . "Address" . "," . "Landline no" . "," . "Owner name" . "," . "Owner phone" . "," . "Cust class" . "," . "Weekly closing day" . "," . "Coverage type" . "," . "GST" . "," . "PAN" . "," . "District" . "," . "Minimum stock" . "," . "Bank name" . "," . "Bank account number" . "," .
// 				"Email" . "," . "Visit Day" . "," . "State" . "," . "Monthly Potential" . "," . "Sauda limit" . "," . "Incoterms" . "," . "Loadability ton" . "," . "Transport mode" . "," . "Sauda type" . "," . "Zone" . "," . "Visit sequence" . "," . "Appointment date" . "," . "date_of_birth" . "," . "date_of_anniversary" . "," . "whatsapp_no" . "," . "beneficiary_name" . "," . "IFS_code" . "," . "pin" . "," . "Base lattitude" . "," . "Base longitude" . "," . "Retailer app" . "," . "New Customer" . "," . "Category of store" . "," . "NLP" . "," . "Area" . "," . "Route No" . "," . "Cluster" . "," . "SAP customer code";
// 		}
// 		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
// 		if (strtoupper($foldername) == 'STAR') {
// 			$header_customer = $header_customer . "," . "Status";
// 		}
// 		if (strtoupper($foldername) == 'ABDOS') {
// 			$header_customer = $header_customer . "," . "HQ";
// 		}
// 		//echo"<pre>";echo($header_customer);die;
// 		//echo"<pre>";print_r(mysqli_fetch_array($res));die;
// 		while ($row_customer = mysqli_fetch_assoc($res)) {
// 			//sk add condition if customer name not exist
// 			if (trim($row_customer['dns_customer_code']) == '' || $row_customer['dns_customer_code'] == NULL || trim($row_customer['customer_name']) == '' || $row_customer['customer_name'] == NULL) {
// 				continue;
// 			}
// 			$dns_customer_code = $row_customer['dns_customer_code'];
// 			$customer_name = '"' . preg_replace('/[\r\n]+/', '', $row_customer['customer_name']) . '"';
// 			$phone_no = ($row_customer['phone_no'] != '') ? str_replace(',', '-', $row_customer['phone_no']) : '';
// 			$route_code = ($row_customer['route_code'] != '') ? str_replace(',', '', $row_customer['route_code']) : '';
// 			//$route_name =  '"'.str_replace(',','',$row_customer['route_name']).'"';
// 			$route_name =  '"' . $row_customer['route_name'] . '"';
// 			$emp_code_name = $row_customer['emp_code_name'] != '' && $row_customer['emp_code_name'] != null ? str_replace(';;', ';', $row_customer['emp_code_name']) : '';
// 			$acedns = $row_customer['acedns'];
// 			$credit_limit = $row_customer['credit_limit'];
// 			$credit_days = $row_customer['credit_days'];
// 			$current_balance = $row_customer['current_balance'];
// 			$black_list = $row_customer['black_list'];
// 			$TD = $row_customer['TD'];
// 			$branch_code = $row_customer['branch_code'];
// 			$branch_name = $row_customer['branch_name'];

// 			$cust_type = $row_customer['cust_type'];
// 			$rds_tag = '"' . $row_customer['rds_tag'] . '"';
// 			$sauda_validity_period = $row_customer['sauda_validity_period'];
// 			//$address =  '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['address'])).'"';
// 			$address = '"' . preg_replace('/[\r\n]+/', '', $row_customer['address']) . '"';
// 			$landline_no =  '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['landline_no'])) . '"';


// 			$owner_name = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['owner_name'])) . '"';
// 			$owner_phone = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['owner_phone'])) . '"';

// 			$cust_class = $row_customer['cust_class'];
// 			$weekly_closing_day = $row_customer['weekly_closing_day'];
// 			$coverage_type = $row_customer['coverage_type'];
// 			$TIN = $row_customer['TIN'];
// 			$PAN = $row_customer['PAN'];
// 			$district = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['district'])) . '"';

// 			$minimum_stock = $row_customer['minimum_stock'];

// 			$bank_name = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['bank_name'])) . '"';
// 			$bank_account_number = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['bank_account_number'])) . '"';


// 			$email = '"' . str_replace(',', '', preg_replace('/[\r\n]+/', '', $row_customer['email'])) . '"';

// 			$visit_day = $row_customer['visit_day'];
// 			$state_code = $row_customer['state_code'];
// 			$monthly_potential = $row_customer['monthly_potential'];
// 			$sauda_limit = $row_customer['sauda_limit'];
// 			$incoterms = $row_customer['incoterms'];
// 			$loadability_ton = $row_customer['loadability_ton'];
// 			$transport_mode = $row_customer['transport_mode'];
// 			$sauda_type = $row_customer['sauda_type'];
// 			$zone = $row_customer['zone'];
// 			$visit_sequence = $row_customer['visit_sequence'];
// 			$appointment_date = $row_customer['appointment_date'];
// 			$date_of_birth = $row_customer['date_of_birth'];
// 			$date_of_anniversary = $row_customer['date_of_anniversary'];
// 			$whatsapp_no = $row_customer['whatsapp_no'];
// 			//$beneficiary_name= $row_customer['beneficiary_name'];
// 			$beneficiary_name = '"' . preg_replace('/[\r\n]+/', '', $row_customer['beneficiary_name']) . '"';

// 			//$IFS_code= $row_customer['IFS_code'];
// 			$IFS_code = '"' . preg_replace('/[\r\n]+/', '', $row_customer['IFS_code']) . '"';

// 			$pin = $row_customer['pin'];
// 			$base_latt = $row_customer['base_latt'];
// 			$base_longi = $row_customer['base_longi'];
// 			$retailer_app = $row_customer['retailer_app'];
// 			$vertical_value = isset($row_customer['vertical_value']) ? $row_customer['vertical_value'] : '';
// 			$is_new_customer = isset($row_customer['is_new_customer']) ? $row_customer['is_new_customer'] : '';
// 			$category_of_store = $row_customer['category_of_store'];
// 			$is_nlp = $row_customer['is_nlp'];
// 			$area = $row_customer['area'];
// 			$route_no = $row_customer['route_no'];
// 			$cluster = $row_customer['cluster'];;
// 			$HQ = isset($row_customer['HQ']) ? $row_customer['HQ'] : '';
// 			$SAP_customer_code = '';

// 			if (strtoupper($foldername) == 'ASL') {
// 				$sqllimit = "SELECT sauda_limit FROM customer_sauda_limit WHERE customer_code='" . $dns_customer_code . "'";
// 				$rslimit = mysqli_query($link, $sqllimit);
// 				$rowlimit = mysqli_fetch_assoc($rslimit);
// 				$sauda_limit = $rowlimit['sauda_limit'];
// 			}
// 			if (strtoupper($foldername) == 'STAR') {
// 				$acednsparts = explode(";", $acedns);
// 				$SAP_customer_code = $row_customer['SAP_customer_code'];

// 				if (in_array('Y', $acednsparts)) {
// 					$contents .= $dns_customer_code . "," . $customer_name . "," . $phone_no . "," . $route_code . "," . $route_name . "," . $emp_code_name . "," . $acedns . "," . $credit_limit . "," . $credit_days . "," . $current_balance . "," . $black_list . "," . $TD . "," . $branch_code . "," . $branch_name . "," . $cust_type . "," . $rds_tag . "," . $sauda_validity_period . "," . $address . "," . $landline_no . "," . $owner_name . "," . $owner_phone . "," . $cust_class . "," . $weekly_closing_day . "," . $coverage_type . "," . $TIN . "," . $PAN . "," . $district . "," . $minimum_stock . "," . $bank_name . "," . $bank_account_number . "," . $email . "," . $visit_day . "," . $state_code . "," . $monthly_potential . "," . $sauda_limit . "," . $incoterms . "," . $loadability_ton . "," . $transport_mode . "," . $sauda_type . "," . $zone . "," . $visit_sequence . "," . $appointment_date . "," . $date_of_birth . "," . $date_of_anniversary . "," . $whatsapp_no . "," . $beneficiary_name . "," . $IFS_code . "," . $pin . "," .
// 						$base_latt . "," . $base_longi . "," . $retailer_app . "," . $is_new_customer . "," . $category_of_store . "," . $is_nlp . "," . $area . "," . $route_no . "," . $cluster . "," . $SAP_customer_code . "," . 'Y' . "\n";
// 				} else {
// 					$contents .= $dns_customer_code . "," . $customer_name . "," . $phone_no . "," . $route_code . "," . $route_name . "," . $emp_code_name . "," . $acedns . "," . $credit_limit . "," . $credit_days . "," . $current_balance . "," . $black_list . "," . $TD . "," . $branch_code . "," . $branch_name . "," . $cust_type . "," . $rds_tag . "," . $sauda_validity_period . "," . $address . "," . $landline_no . "," . $owner_name . "," . $owner_phone . "," . $cust_class . "," . $weekly_closing_day . "," . $coverage_type . "," . $TIN . "," . $PAN . "," . $district . "," . $minimum_stock . "," . $bank_name . "," . $bank_account_number . "," . $email . "," . $visit_day . "," . $state_code . "," . $monthly_potential . "," . $sauda_limit . "," . $incoterms . "," . $loadability_ton . "," . $transport_mode . "," . $sauda_type . "," . $zone . "," . $visit_sequence . "," . $appointment_date . "," . $date_of_birth . "," . $date_of_anniversary . "," . $whatsapp_no . "," . $beneficiary_name . "," . $IFS_code . "," . $pin . ","
// 						. $base_latt . "," . $base_longi . "," . $retailer_app . "," . $is_new_customer . "," . $category_of_store . "," . $is_nlp . "," . $area . "," . $route_no . "," . $cluster . "," . $SAP_customer_code . "," . 'N' . "\n";
// 				}
// 			} else if (strtoupper($foldername) == 'RUPA') {
// 				$contents .= $dns_customer_code . "," . $customer_name . "," . $phone_no . "," . $route_code . "," . $route_name . "," . $emp_code_name . "," . $vertical_value . "," . $acedns . "," . $credit_limit . "," . $credit_days . "," . $current_balance . "," . $black_list . "," . $TD . "," . $branch_code . "," . $cust_type . "," . $rds_tag . "," . $sauda_validity_period . "," . $address . "," . $landline_no . "," . $owner_name . "," . $owner_phone . "," . $cust_class . "," . $weekly_closing_day . "," . $coverage_type . "," . $TIN . "," . $PAN . "," . $district . "," . $minimum_stock . "," . $bank_name . "," . $bank_account_number . "," . $email . "," . $visit_day . "," . $state_code . "," . $monthly_potential . "," . $sauda_limit . "," . $incoterms . "," . $loadability_ton . "," . $transport_mode . "," . $sauda_type . "," . $zone . "," . $visit_sequence . "," . $appointment_date . "," . $date_of_birth . "," . $date_of_anniversary . "," . $whatsapp_no . "," . $beneficiary_name . "," . $IFS_code . "," . $pin . "," . $base_latt . "," . $base_longi . "\n";
// 			} else if (strtoupper($foldername) == 'ABDOS') {
// 				$contents .= $dns_customer_code . "," . $customer_name . "," . $phone_no . "," . $route_code . "," . $route_name . "," . $emp_code_name . "," . $acedns . "," . $credit_limit . "," . $credit_days . "," . $current_balance . "," . $black_list . "," . $TD . "," . $branch_code . "," . $cust_type . "," . $rds_tag . "," . $sauda_validity_period . "," . $address . "," . $landline_no . "," . $owner_name . "," . $owner_phone . "," . $cust_class . "," . $weekly_closing_day . "," . $coverage_type . "," . $TIN . "," . $PAN . "," . $district . "," . $minimum_stock . "," . $bank_name . "," . $bank_account_number . "," . $email . "," . $visit_day . "," . $state_code . "," . $monthly_potential . "," . $sauda_limit . "," . $incoterms . "," . $loadability_ton . "," . $transport_mode . "," . $sauda_type . "," . $zone . "," . $visit_sequence . "," . $appointment_date . "," . $date_of_birth . "," . $date_of_anniversary . "," . $whatsapp_no . "," . $beneficiary_name . "," . $IFS_code . "," . $pin . "," . $base_latt . "," . $base_longi . "," . $retailer_app . "," . $is_new_customer . "," . $category_of_store . "," . $is_nlp . "," . $area . "," . $route_no . "," . $cluster . "," . $HQ . "\n";
// 			} else {
// 				$contents .= $dns_customer_code . "," . $customer_name . "," . $phone_no . "," . $route_code . "," . $route_name . "," . $emp_code_name . "," . $acedns . "," . $credit_limit . "," . $credit_days . "," . $current_balance . "," . $black_list . "," . $TD . "," . $branch_code . "," . $cust_type . "," . $rds_tag . "," . $sauda_validity_period . "," . $address . "," . $landline_no . "," . $owner_name . "," . $owner_phone . "," . $cust_class . "," . $weekly_closing_day . "," . $coverage_type . "," . $TIN . "," . $PAN . "," . $district . "," . $minimum_stock . "," . $bank_name . "," . $bank_account_number . "," . $email . "," . $visit_day . "," . $state_code . "," . $monthly_potential . "," . $sauda_limit . "," . $incoterms . "," . $loadability_ton . "," . $transport_mode . "," . $sauda_type . "," . $zone . "," . $visit_sequence . "," . $appointment_date . "," . $date_of_birth . "," . $date_of_anniversary . "," . $whatsapp_no . "," . $beneficiary_name . "," . $IFS_code . "," . $pin . "," . $base_latt . "," . $base_longi . "," . $retailer_app . "," . $is_new_customer . "," . $category_of_store . "," . $is_nlp . "," . $area . "," . $route_no . "," . $cluster . "," . $SAP_customer_code . "\n";
// 			}

// 			$count++;
// 		}
// 		//echo"<pre>";echo $contents ;die;
// 		//commend- no need to create folder required
// 		// if ( !file_exists("../dump/$foldername")){
// 		// 	mkdir("../dump/$foldername");
// 		// 	chmod("../dump/$foldername", 0777);
// 		// }
// 		$datacontents = $header_customer . "\n" . $contents;
// 		//$datacontents=$header_customer;

// 		//echo"<pre>";echo($datacontents);die;

// 		//add line sk 290425
// 		$base_path = $_SERVER['DOCUMENT_ROOT'];
// 		$fp = fopen("$base_path/misreport/dump/$foldername/customer master.csv", "wb");
// 		// if ($stream === false) {
// 		// 	die("Failed to open file for writing: $fp");
// 		// }
// 		//commend by sk 290425
// 		//$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/customer master.csv","wb");
// 		fwrite($fp, $datacontents);
// 		fclose($fp);

// 		echo "$foldername/customer master.csv";
// 		//echo $datacontents;
// 		//header("Content-type: application/octet-stream");
// 		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
// 		//print "$datacontents";
// 		//$nick_name=strtoupper($_SESSION['nick_name']);
// 		//echo $datacontents;
// 	} else {
// 		echo "<strong><font color=\"red\">No Records Found</font></strong>";
// 	}
// }
//AND CRER.acedns='Y'
if($attribute=='customer'){
  if(modified_customer_emp_route=='yes'){
    	if(providing_code=='yes'){
    	  $emp_query="(SELECT GROUP_CONCAT(EM.dns_emp_code SEPARATOR ';') FROM employee_master EM,customer_route_emp_relation CRER 
		  WHERE EM.emp_code = CRER.emp_code AND CRER.customer_code=CM.customer_code ) AS emp_code_name";
    	  $customer="(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
		  $sorting_cond="ORDER BY CM.dns_customer_code ASC";
		  //$group_cond="GROUP BY CM.dns_customer_code";
    	}
    	else{
			if(strtoupper($foldername)=='ELEGANT'){
				$emp_query="(SELECT dns_emp_code FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name";
			}
			else
			{
    	  	$emp_query="(SELECT emp_name FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name";
			}
		  	if($foldername=='SUPERSHAKTI'){
				  $customer="(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
				}
			else
			{
    	  $customer=" (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
			}

		  $sorting_cond="ORDER BY CM.customer_name ASC";
		  //$group_cond="GROUP BY CM.customer_name ";
    	}
    	if(sauda_depot_wise=='yes' || $foldername=='ASL'){
    		$branch="(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM,customer_branch_relation CBR WHERE BM.branch_code=CBR.branch_code AND CBR.customer_code=CM.customer_code) as branch_code";
    	}
    	else{
			if(providing_code=='yes'){
			 $branch="(SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
			}
			else{
    		 $branch="(SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
			}
    	}
		if(strtoupper($foldername)=='ABDOS'){
				$emp_hq_query="(SELECT HQ FROM employee_master WHERE emp_code = CRER.emp_code) AS HQ,";
			}
			else
			{
				$emp_hq_query="";
			}
		if(strtoupper($foldername)=='STAR')
		{
        $sql="SELECT DISTINCT CM.dns_customer_code,
				  CM.customer_name,
				  CM.phone_no,
				  (SELECT RM.dns_route_code FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CR.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_code, (SELECT RM.route_name FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CR.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_name,
				  (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
				  (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
				   ".$emp_query.",
				   GROUP_CONCAT(CRER.acedns SEPARATOR ';') AS acedns,
				   CM.credit_limit,
				   CM.credit_days,
				   CM.current_balance,
				   CM.black_list,
				   CM.TD,
				   ".$branch.",
				   CM.cust_type,
				   ".$customer.",
				   CM.sauda_validity_period,
				   CM.address,
				   CM.landline_no,
				   CM.owner_name,
				   CM.owner_phone,
				   CM.cust_class,
				   CM.weekly_closing_day,
				   CM.coverage_type,
				   CM.TIN,
				   CM.PAN,
				   CM.district,
				   CM.minimum_stock,
				   CM.bank_name,
				   CM.bank_account_number,
				   CM.email,
				   CM.visit_day,
				   CM.state_code,
				   CM.monthly_potential,
				   CM.sauda_limit,
				   CM.incoterms,
				   CM.loadability_ton,
				   CM.transport_mode,
				   CM.sauda_type,
				   CM.zone,
				   CM.visit_sequence,
				   CM.appointment_date,
				   CM.date_of_birth,
				   CM.date_of_anniversary,
				   CM.whatsapp_no,
				   CM.beneficiary_name,
				   CM.IFS_code,
				   CM.pin,
				   CM.base_latt,
				   CM.base_longi,CM.retailer_app,CM.is_new_customer,CM.category_of_store,CM.is_nlp,CM.cluster,CM.SAP_customer_code FROM customer_master CM INNER JOIN customer_route_emp_relation CRER
				ON CM.customer_code = CRER.customer_code   GROUP BY CM.dns_customer_code $sorting_cond";
				 //echo $sql;
		}
		else
		{
			$sql="SELECT DISTINCT CM.dns_customer_code,
			  CM.customer_name,
			  CM.phone_no,
			  (SELECT dns_route_code FROM route_master WHERE route_code = CRER.route_code) AS route_code,
			  (SELECT route_name FROM route_master WHERE route_code = CRER.route_code) AS route_name,
			   (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
				  (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
			   ".$emp_query.",
			   ".$emp_hq_query."
			   CRER.acedns,
			   CM.credit_limit,
			   CM.credit_days,
			   CM.current_balance,
			   CM.black_list,
			   CM.TD,
			   ".$branch.",
			   CM.cust_type,
			   ".$customer.",
			   CM.sauda_validity_period,
			   CM.address,
			   CM.landline_no,
			   CM.owner_name,
			   CM.owner_phone,
			   CM.cust_class,
			   CM.weekly_closing_day,
			   CM.coverage_type,
			   CM.TIN,
			   CM.PAN,
			   CM.district,
			   CM.minimum_stock,
			   CM.bank_name,
			   CM.bank_account_number,
			   CM.email,
			   CM.visit_day,
			   CM.state_code,
			   CM.monthly_potential,
			   CM.sauda_limit,
			   CM.incoterms,
			   CM.loadability_ton,
			   CM.transport_mode,
			   CM.sauda_type,
			   CM.zone,
			   CM.visit_sequence,
			   CM.appointment_date,
			   CM.date_of_birth,
			   CM.date_of_anniversary,
			   CM.whatsapp_no,
			   CM.beneficiary_name,
			   CM.IFS_code,
			   CM.pin,
			   CM.retailer_app,
			   CM.base_latt,
			   CM.base_longi,
			   CM.retailer_app,CM.is_new_customer,CM.category_of_store,CM.is_nlp,CM.cluster FROM customer_master CM INNER JOIN customer_route_emp_relation CRER ON CM.customer_code = CRER.customer_code  $sorting_cond";
			
		}
   }
   else{
  	if(providing_code=='yes'){

  	  $emp_query="(SELECT dns_emp_code FROM employee_master WHERE emp_code = CM.emp_code) AS emp_code_name";
  	  $customer="(SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
  	}
  	else{

  	  $emp_query="(SELECT emp_name FROM employee_master WHERE emp_code = CM.emp_code) AS emp_code_name";
  	  $customer=" (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag";
  	}
  	if(sauda_depot_wise=='yes' || $foldername=='ASL'){
  		$branch="(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master BM,customer_branch_relation CBR WHERE BM.branch_code=CBR.branch_code AND CBR.customer_code=CM.customer_code) as branch_code";
  	}
  	else{
  		if(providing_code=='yes'){
			 $branch="(SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
		}
		else{
    		 $branch="(SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code";
		}
  	}
	if($foldername=='RUPA')
	{
		$sql="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, RM.dns_route_code, RM.route_name, 
							( SELECT emp_name FROM employee_master WHERE emp_code = CM.emp_code ) AS emp_code_name,
							( SELECT vertical_value FROM employee_master WHERE emp_code = CM.emp_code ) AS vertical_value, 
							CM.acedns, CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, 
							(SELECT branch_name FROM branch_master WHERE branch_code=CM.branch_code) As branch_code, 
							CM.cust_type, (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code=CM.rds_tag) AS rds_tag, 
							CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, 
							CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, 
							CM.bank_account_number,CM.email,CM.visit_day,CM.state_code,CM.monthly_potential,CM.sauda_limit,
				   			CM.incoterms,CM.loadability_ton,CM.transport_mode,CM.sauda_type,CM.zone,CM.visit_sequence,CM.appointment_date,
				   			CM.date_of_birth,CM.date_of_anniversary,CM.whatsapp_no,CM.beneficiary_name,CM.IFS_code,CM.pin,
				   			CM.base_latt,CM.base_longi 
							FROM customer_master CM,route_master RM 
							WHERE CM.route_code=RM.route_code ORDER BY vertical_value ASC";
							 
	}
	else
	{
    $sql="SELECT CM.dns_customer_code,
					  CM.customer_name,
					  CM.phone_no,
					  (SELECT dns_route_code FROM route_master WHERE route_code = CM.route_code) as route_code,
					  (SELECT route_name FROM route_master WHERE route_code = CM.route_code) as route_name,
					   ".$emp_query.",
					  CM.acedns,
					  CM.credit_limit,
					  CM.credit_days,
					  CM.current_balance,
					  CM.black_list,
					  CM.TD,
					  ".$branch.",
					  CM.cust_type,
					  ".$customer.",
					  CM.sauda_validity_period,
					  CM.address,
					  CM.landline_no,
					  CM.owner_name,
					  CM.owner_phone,
					  CM.cust_class,
					  CM.weekly_closing_day,
					  CM.coverage_type,
					  CM.TIN,
					  CM.PAN,
					  CM.district,
					  CM.minimum_stock,
					  CM.bank_name,
					  CM.bank_account_number,
					  CM.email,
					  CM.visit_day,
					  CM.state_code,
					  CM.monthly_potential,
					   CM.sauda_limit,
					   CM.incoterms,
					   CM.loadability_ton,
					   CM.transport_mode,
					   CM.sauda_type,
					   CM.zone,
					   CM.visit_sequence,
					   CM.appointment_date,
					   CM.date_of_birth,
					   CM.date_of_anniversary,
					   CM.whatsapp_no,
					   CM.beneficiary_name,
					   CM.IFS_code,
					   CM.pin,
					   CM.base_latt,CM.base_longi,CM.is_new_customer
					   FROM customer_master CM WHERE CM.acedns='Y'";
					  
	}
  }
  
 
 $res=mysqli_query($link,$sql);
  $total_customer = mysqli_num_rows($res);

  if($total_customer > 0){
	  $dns_customer_code_array=array();
	  
		if(strtoupper($foldername)=='RUPA')
		{
		  $header_customer = "DNS Customer Code".","."Customer Name".","."Phone no".","."Route code".","."Route Name".","."Emp Code name".","."Vertical".","."acedns".","."Credit Limit".","."Credit Days".","."Current Balance".","."Black list".","."TD".","."Branch code".","."Cust type".","."rds tag".","."Sauda validity period".","."Address".","."Landline no".","."Owner name".","."Owner phone".","."Cust class".","."Weekly closing day".","."Coverage type".","."TIN".","."PAN".","."District".","."Minimum stock".","."Bank name".","."Bank account number".",".
			"Email".","."Visit Day".","."State".","."Monthly Potential".","."Sauda limit".","."Incoterms".","."Loadability ton".","."Transport mode".","."Sauda type".","."Zone".","."Visit sequence".","."Appointment date".","."date_of_birth".","."date_of_anniversary".","."whatsapp_no".","."beneficiary_name".","."IFS_code".","."pin".","."Base lattitude".","."Base longitude".","."Retailer app".","."New Customer".","."Category of store".","."NLP".","."Area".","."Route No";
		}
		else
		{
		$header_customer = "DNS Customer Code".","."Customer Name".","."Phone no".","."Route code".","."Route Name".","."Emp Code name".","."acedns".","."Credit Limit".","."Credit Days".","."Current Balance".","."Black list".","."TD".","."Branch code".","."Cust type".","."rds tag".","."Sauda validity period".","."Address".","."Landline no".","."Owner name".","."Owner phone".","."Cust class".","."Weekly closing day".","."Coverage type".","."GST".","."PAN".","."District".","."Minimum stock".","."Bank name".","."Bank account number".",".
		"Email".","."Visit Day".","."State".","."Monthly Potential".","."Sauda limit".","."Incoterms".","."Loadability ton".","."Transport mode".","."Sauda type".","."Zone".","."Visit sequence".","."Appointment date".","."date_of_birth".","."date_of_anniversary".","."whatsapp_no".","."beneficiary_name".","."IFS_code".","."pin".","."Base lattitude".","."Base longitude".","."Retailer app".","."New Customer".","."Category of store".","."NLP".","."Area".","."Route No".","."Cluster".","."SAP customer code";
		}
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		if(strtoupper($foldername)=='STAR')
		{
			$header_customer=$header_customer.","."Status";
		}
		if(strtoupper($foldername)=='ABDOS')
		{
			$header_customer=$header_customer.","."HQ";
		}
		while($row_customer = mysqli_fetch_assoc($res)){
		    //sk add condition if customer name not exist
			if(trim($row_customer['dns_customer_code'])=='' || $row_customer['dns_customer_code']==NULL || trim($row_customer['customer_name'])=='' || $row_customer['customer_name']==NULL)
			{
				continue;
			}
			$dns_customer_code = $row_customer['dns_customer_code'];
			$customer_name = '"'.preg_replace('/[\r\n]+/', '',$row_customer['customer_name']).'"';
			$phone_no = str_replace(',','-',$row_customer['phone_no']);
			$route_code = str_replace(',','',$row_customer['route_code']);
			//$route_name =  '"'.str_replace(',','',$row_customer['route_name']).'"';
			$route_name =  '"'.$row_customer['route_name'].'"';
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
			//$address =  '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['address'])).'"';
			$address = '"'.preg_replace('/[\r\n]+/', '',$row_customer['address']).'"';
			$landline_no =  '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['landline_no'])).'"';
			
			
			$owner_name = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['owner_name'])).'"';
			$owner_phone = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['owner_phone'])).'"';
			
			$cust_class = $row_customer['cust_class'];
			$weekly_closing_day = $row_customer['weekly_closing_day'];
			$coverage_type = $row_customer['coverage_type'];
			$TIN = $row_customer['TIN'];
			$PAN = $row_customer['PAN'];
			$district = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['district'])).'"';
			
			$minimum_stock = $row_customer['minimum_stock'];
			
			$bank_name = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['bank_name'])).'"';
			$bank_account_number = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['bank_account_number'])).'"';
			
			
			$email = '"'.str_replace(',','',preg_replace('/[\r\n]+/', '',$row_customer['email'])).'"';

			$visit_day= $row_customer['visit_day'];
			$state_code= $row_customer['state_code'];
			$monthly_potential= $row_customer['monthly_potential'];
			$sauda_limit= $row_customer['sauda_limit'];
			$incoterms= $row_customer['incoterms'];
			$loadability_ton= $row_customer['loadability_ton'];
			$transport_mode= $row_customer['transport_mode'];
			$sauda_type= $row_customer['sauda_type'];
			$zone= $row_customer['zone'];
			$visit_sequence= $row_customer['visit_sequence'];
			$appointment_date= $row_customer['appointment_date'];
			$date_of_birth= $row_customer['date_of_birth'];
			$date_of_anniversary= $row_customer['date_of_anniversary'];
			$whatsapp_no= $row_customer['whatsapp_no'];
			//$beneficiary_name= $row_customer['beneficiary_name'];
			$beneficiary_name = '"'.preg_replace('/[\r\n]+/', '',$row_customer['beneficiary_name']).'"';
			//$IFS_code= $row_customer['IFS_code'];
			$IFS_code = '"'.preg_replace('/[\r\n]+/', '',$row_customer['IFS_code']).'"';
			$pin= $row_customer['pin'];
			$base_latt= $row_customer['base_latt'];
			$base_longi= $row_customer['base_longi'];
			$retailer_app= $row_customer['retailer_app'];
			$vertical_value= $row_customer['vertical_value'];
			$is_new_customer= $row_customer['is_new_customer'];
			$category_of_store= $row_customer['category_of_store'];	
			$is_nlp= $row_customer['is_nlp'];
			$area= $row_customer['area'];
			$route_no= $row_customer['route_no'];
			$cluster=$row_customer['cluster'];;	
			$HQ=$row_customer['HQ'];
			$SAP_customer_code='';
			
			if(strtoupper($foldername)=='ASL')
			{
				$sqllimit="SELECT sauda_limit FROM customer_sauda_limit WHERE customer_code='".$dns_customer_code."'";
				$rslimit=mysqli_query($link,$sqllimit);
				$rowlimit=mysqli_fetch_assoc($rslimit);
				$sauda_limit= $rowlimit['sauda_limit'];
			}
		    if(strtoupper($foldername)=='STAR')
			{
				$acednsparts=explode(";",$acedns);
				$SAP_customer_code=$row_customer['SAP_customer_code'];
				//$acedns='Y';
				if(in_array('Y',$acednsparts))
				{
				 	$contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code.",".$monthly_potential.",".$sauda_limit.",".$incoterms.",".$loadability_ton.",".$transport_mode.",".$sauda_type.",".$zone.",".$visit_sequence.",".$appointment_date.",".$date_of_birth.",".$date_of_anniversary.",".$whatsapp_no.",".$beneficiary_name.",".$IFS_code.",".$pin.",".
					$base_latt.",".$base_longi.",".$retailer_app.",".$is_new_customer.",".$category_of_store.",".$is_nlp.",".$area.",".$route_no.",".$cluster.",".$SAP_customer_code.",".'Y'."\n";
				}
				else
				{
					$contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code.",".$monthly_potential.",".$sauda_limit.",".$incoterms.",".$loadability_ton.",".$transport_mode.",".$sauda_type.",".$zone.",".$visit_sequence.",".$appointment_date.",".$date_of_birth.",".$date_of_anniversary.",".$whatsapp_no.",".$beneficiary_name.",".$IFS_code.",".$pin.","
					.$base_latt.",".$base_longi.",".$retailer_app.",".$is_new_customer.",".$category_of_store.",".$is_nlp.",".$area.",".$route_no.",".$cluster.",".$SAP_customer_code.",".'N'."\n";
				}
			}
			else if(strtoupper($foldername)=='RUPA')
			{
			   $contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$vertical_value.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code.",".$monthly_potential.",".$sauda_limit.",".$incoterms.",".$loadability_ton.",".$transport_mode.",".$sauda_type.",".$zone.",".$visit_sequence.",".$appointment_date.",".$date_of_birth.",".$date_of_anniversary.",".$whatsapp_no.",".$beneficiary_name.",".$IFS_code.",".$pin.",".$base_latt.",".$base_longi."\n";
			}
			else if(strtoupper($foldername)=='ABDOS')
			{
				$contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code.",".$monthly_potential.",".$sauda_limit.",".$incoterms.",".$loadability_ton.",".$transport_mode.",".$sauda_type.",".$zone.",".$visit_sequence.",".$appointment_date.",".$date_of_birth.",".$date_of_anniversary.",".$whatsapp_no.",".$beneficiary_name.",".$IFS_code.",".$pin.",".$base_latt.",".$base_longi.",".$retailer_app.",".$is_new_customer.",".$category_of_store.",".$is_nlp.",".$area.",".$route_no.",".$cluster.",".$HQ."\n";
			}
			else
			{
				$contents.=$dns_customer_code.",".$customer_name.",".$phone_no.",".$route_code.",".$route_name.",".$emp_code_name.",".$acedns.",".$credit_limit.",".$credit_days.",".$current_balance.",".$black_list.",".$TD.",".$branch_code.",".$cust_type.",".$rds_tag.",".$sauda_validity_period.",".$address.",".$landline_no.",".$owner_name.",".$owner_phone.",".$cust_class.",".$weekly_closing_day.",".$coverage_type.",".$TIN.",".$PAN.",".$district.",".$minimum_stock.",".$bank_name.",".$bank_account_number.",".$email.",".$visit_day.",".$state_code.",".$monthly_potential.",".$sauda_limit.",".$incoterms.",".$loadability_ton.",".$transport_mode.",".$sauda_type.",".$zone.",".$visit_sequence.",".$appointment_date.",".$date_of_birth.",".$date_of_anniversary.",".$whatsapp_no.",".$beneficiary_name.",".$IFS_code.",".$pin.",".$base_latt.",".$base_longi.",".$retailer_app.",".$is_new_customer.",".$category_of_store.",".$is_nlp.",".$area.",".$route_no.",".$cluster.",".$SAP_customer_code."\n";
			}

			$count++;
		}
		 $folderPath = __DIR__ . "/dump/$foldername";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
            chmod($folderPath, 0777);
        }
		$datacontents=$header_customer."\n".$contents;
		$fp = fopen("$basepath$foldername/customer master.csv","wb");
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

if ($attribute == 'employee') {
	if (providing_code == 'yes') {
		if (strtoupper($foldername) == 'STAR' || strtoupper($foldername) == 'SYLVAN') {
			$reporting_to = " (SELECT GROUP_CONCAT(dns_emp_code SEPARATOR ';') FROM employee_master WHERE FIND_IN_SET(emp_code,EM.reporting_to)) AS reporting_to";
		} else {
			$reporting_to = "(SELECT dns_emp_code FROM employee_master WHERE emp_code = EM.reporting_to) AS reporting_to";
		}
	} else {
		if (strtoupper($foldername) == 'AJANTA' || strtoupper($foldername) == 'SUPERSHAKTI' ||  strtoupper($foldername) == 'PALSONS') {
			$reporting_to = " (SELECT GROUP_CONCAT(emp_name SEPARATOR ';') FROM employee_master WHERE FIND_IN_SET(emp_code,EM.reporting_to)) AS reporting_to";
		} else {
			$reporting_to = " (SELECT emp_name FROM employee_master WHERE emp_code = EM.reporting_to) AS reporting_to";
		}
	}

	if (sauda_depot_wise == 'yes') {

		$branch = "(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master
        BM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code)) as branch_code";
	} else {

		if (providing_code == 'yes') {
			if (strtoupper($foldername) == 'STAR') {
				$branch = "(SELECT GROUP_CONCAT(DISTINCT BM.dns_branch_code SEPARATOR ';') FROM branch_master
       					 BM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code)) as branch_code";
			} else {
				$branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code = EM.branch_code) AS branch_code";
			}
		} else {
			$branch = "(SELECT branch_name FROM branch_master WHERE branch_code = EM.branch_code) AS branch_code";
		}
	}
	$sql_employee = "SELECT EM.dns_emp_code,
                    EM.emp_name,
                    EM.vertical_value,
                    $reporting_to,
                    $branch,
                    EM.email,
                    EM.phone_no,
                    EM.sale_access,
                    EM.designation,
                    EM.HQ,
                    EM.state,
                    EM.zone,
  				    EM.acedns,
                    EM.District, 
					EM.functionality,
					EM.functionality_rel_val,
					EM.DOJ,
					EM.level,
					EM.region
					FROM employee_master EM ORDER BY EM.emp_code";
	//	echo $sql_employee;
	//exit();				
	$res_employee = mysqli_query($link, $sql_employee);
	$total_employee = mysqli_num_rows($res_employee);
	$contents_emp = "";
	if ($total_employee > 0) {
		$header_employee = "DNS Emp Code" . "," . "Employee Name" . "," . "Branch" . "," . "Vertical value" . "," . "Reporting to" . "," . "Email" . "," . "Phone no" . "," . "Sale access" . "," . "Designation" . "," . "HQ" . "," . "State" . "," . "Zone" . "," . "acedns" . "," . "District" . "," . "Functionality" . "," . "Functionality Val" . "," . "DOJ" . "," . "Level" . "," . "Region";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		$count = 0;
		while ($row_employee = mysqli_fetch_assoc($res_employee)) {
			$dns_emp_code = str_replace(',', '', $row_employee['dns_emp_code'] ??'');
			$emp_name = str_replace(',', '', $row_employee['emp_name'] ??'');
			$branch_code = $row_employee['branch_code'] ??'';
			$vertical_value = str_replace(',', ';', $row_employee['vertical_value'] ??'');
			$reporting_to = str_replace(',', '', $row_employee['reporting_to'] ??'');
			$email = $row_employee['email'] ??'';
			$phone_no = $row_employee['phone_no'] ??'';
			$sale_access = $row_employee['sale_access'] ?? '';
			$designation = $row_employee['designation'] ?? '';
			$HQ = str_replace(',', '', $row_employee['HQ'] ?? '');
			$state = str_replace(',', '', $row_employee['state'] ?? '');
			$zone = str_replace(',', '', $row_employee['zone'] ?? '');
			$acedns = $row_employee['acedns'] ?? '';
			$District = $row_employee['District'] ?? '';
			$District = str_replace(',', '', $District);
			$functionality = $row_employee['functionality'] ?? '';
			$DOJ = $row_employee['DOJ'] ?? '';
			$functionality_rel_val = $row_employee['functionality_rel_val'] ?? '';
			$level = $row_employee['level'] ?? '';
			$region = $row_employee['region'] ?? '';

			$contents_emp .= $dns_emp_code . "," . $emp_name . "," . $branch_code . "," . $vertical_value . "," . $reporting_to . "," . $email . "," . $phone_no . "," . $sale_access . "," . $designation . "," . $HQ . "," . $state . "," . $zone . "," . $acedns . "," . $District . "," . $functionality . "," . $functionality_rel_val . "," . $DOJ . "," . $level . "," . $region . "\n";
			$count++;
		}
		$datacontents = $header_employee . "\n" . $contents_emp;
		 $folderPath = __DIR__ . "/dump/$foldername";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
            chmod($folderPath, 0777);
        }
		$fp = fopen("$basepath$foldername/Employee master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Employee master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}

// if ($attribute == 'product') {
// 	$product_group_name = "";
// 	$product_subgroup_name = "";
// 	$product_brand_code = "";
// 	if (no_of_filter == 1) {

// 		$clause = " `product_master` WHERE 1 ";
// 	}
// 	if (no_of_filter == 2) {
// 		$product_group_name = "product_group_master.product_group_name,";
// 		$clause = " `product_master`, `product_group_master` WHERE
//     product_master.product_group_code=product_group_master.product_group_code";
// 	}
// 	if (no_of_filter == 3 || strtoupper($_SESSION['nick_name']) == 'ASL') {
// 		$product_group_name = "product_group_master.product_group_name,";
// 		$product_subgroup_name = "product_sub_group_master.product_sub_group_name,";
// 		$clause = " `product_master`, `product_group_master`,product_sub_group_master WHERE
//     product_master.product_group_code=product_group_master.product_group_code AND product_master.product_sub_group_code=product_sub_group_master.product_sub_group_code";
// 	}
// 	if (no_of_filter == 4 && strtoupper($_SESSION['nick_name']) != 'ASL') {
// 		$product_group_name = "product_group_master.product_group_name,";
// 		$product_subgroup_name = "product_sub_group_master.product_sub_group_name,";
// 		$product_brand_code = "product_brand_master.product_brand_name,";
// 		$clause = " `product_master`,`product_group_master`,`product_sub_group_master`,`product_brand_master` WHERE
//     product_master.product_group_code=product_group_master.product_group_code AND product_master.product_sub_group_code=product_sub_group_master.product_sub_group_code AND product_master.product_brand_code=product_brand_master.product_brand_code";
// 	}
// 	if (providing_code == 'yes') {
// 		$branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code = product_master.branch_code) AS branch_code";
// 	} else {
// 		$branch = "(SELECT branch_name FROM branch_master WHERE branch_code = product_master.branch_code) AS branch_code";
// 	}

// 	$sql_product = "SELECT $branch,
//   product_master.dns_prod_code as Prod_Code,
//   product_master.prod_desc,
//   $product_group_name
//   $product_subgroup_name
//   $product_brand_code
//   product_master.cl_stk,
//   product_master.acedns,
//   product_master.black_list,
//   product_master.vertical_value,
//   product_master.UOM1,
//   product_master.UOM2,
//    product_master.focus,
//   product_master.conversion_factor as Conversion,
//   product_master.pack_size as Pack_Size,
//   product_master.UOM3,
//   product_master.conversion_factor_two as Conversion2,
//   product_master.TD,product_master.pack_unit,
//   product_master.prod_size,product_master.lead_time,
//   product_master.buffer_level,product_master.max_level_marketing,
//   product_master.UOM4,product_master.UOM5,product_master.gross_weight,
//   product_master.fg_rm,product_master.oil_category,product_master.alias,product_master.hsn_sac,product_master.packing_realization,
//   product_master.state_code,product_master.dealer_login,product_master.sp_login FROM " . $clause . ";
//   ";

// 	$res_product = mysqli_query($link, $sql_product);
// 	$total_product = mysqli_num_rows($res_product);

// 	if ($total_product > 0) {
// 		$header_product = "Branch code/name" . "," . "Prod_Code" . "," . "prod_desc" . "," . "brand_code (fk)/brand_name" . "," . "brand_form_code (fk)/brand_form_name" . "," . "brand_sub_form_code(fk)/brand_sub_form_name" . "," . "cl_stk" . "," . "acends" . "," . "black_list" . "," . "vertical_value" . "," . "UOM1" . "," . "UOM2" . "," . "Conversion" . "," . "Pack size" . "," . "UOM3" . "," . "Conversion2" . "," . "TD" . "," . "Focus" . "," . "VAT" . "," . "Pack Unit" . "," . "Prod size" . "," . "Lead time" . "," . "Buffer level" . "," . "Max level marketing" . "," . "UOM4" . "," . "UOM5" . "," . "Gross weight" . "," . "FG_RM" . "," . "Oil category" . "," . "Alias" . "," . "HSN/SAC" . "," . "Packing Realization" . "," . "Statename" . "," . "Prod Full Form" . "," . "Category" . "," . "SP Login" . "," . "Dealer Login";
// 		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
// 		while ($row_product = mysqli_fetch_assoc($res_product)) {
// 			$branch_code = str_replace(',', '', $row_product['branch_code']);
// 			$Prod_Code = str_replace(',', '', $row_product['Prod_Code']);
// 			$prod_desc = str_replace(',', '', $row_product['prod_desc']);
// 			$product_group_name = str_replace(',', '', $row_product['product_group_name']);
// 			$product_sub_group_name = str_replace(',', '', $row_product['product_sub_group_name']);
// 			$product_brand_name = str_replace(',', '', $row_product['product_brand_name']);
// 			$cl_stk = $row_product['cl_stk'];
// 			$acedns = $row_product['acedns'];
// 			$black_list = $row_product['black_list'];
// 			$vertical_value = $row_product['vertical_value'];
// 			$UOM1 = $row_product['UOM1'];
// 			$UOM2 = $row_product['UOM2'];
// 			$Conversion = $row_product['Conversion'];
// 			$Pack_Size = $row_product['Pack_Size'];
// 			$UOM3 = $row_product['UOM3'];
// 			$Conversion2 = $row_product['Conversion2'];
// 			$TD = $row_product['TD'];
// 			$focus = $row_product['focus'];
// 			$vat = $row_product['vat'];
// 			$pack_unit = $row_product['pack_unit'];

// 			$prod_desc = $row_product['prod_desc'];
// 			$prod_size = $row_product['prod_size'];
// 			$lead_time = $row_product['lead_time'];
// 			$buffer_level = $row_product['buffer_level'];
// 			$max_level_marketing = $row_product['max_level_marketing'];
// 			$UOM4 = $row_product['UOM4'];
// 			$UOM5 = $row_product['UOM5'];
// 			$gross_weight = $row_product['gross_weight'];
// 			$fg_rm = $row_product['fg_rm'];
// 			$oil_category = $row_product['oil_category'];
// 			$alias = $row_product['alias'];
// 			$hsn_sac = $row_product['hsn_sac'];
// 			$packing_realization = $row_product['packing_realization'];
// 			$state_code = $row_product['state_code'];
// 			$dealer_login = $row_product['dealer_login'];
// 			$sp_login = $row_product['sp_login'];

// 			$sqlstatecode = "SELECT statename FROM state_master WHERE state_code='" . $state_code . "'";
// 			if ($foldername === "DURO") {
// 				$sqlstatecode = "SELECT state_name FROM state_master WHERE state_code='" . $state_code . "'";
// 			}
// 			//echo $sqlstatecode;
// 			$rsstatecode = mysqli_query($link, $sqlstatecode);
// 			$rowstatecode = mysqli_fetch_assoc($rsstatecode);
// 			if ($foldername === "DURO") {
// 				$statename = $rowstatecode['state_name'];
// 			} else {
// 				$statename = $rowstatecode['statename'];
// 			}
// 			$contents_product .= $branch_code . "," . $Prod_Code . "," . $prod_desc . "," . $product_group_name . "," . $product_sub_group_name . "," . $product_brand_name . "," . $cl_stk . "," . $acedns . "," . $black_list . "," . $vertical_value . "," . $UOM1 . "," . $UOM2 . "," . $Conversion . "," . $Pack_Size . "," . $UOM3 . "," . $Conversion2 . "," . $TD . "," . $focus . "," . $vat . "," . $pack_unit . "," . $prod_size . "," . $lead_time . "," . $buffer_level . "," . $max_level_marketing . "," . $UOM4 . "," . $UOM5 . "," . $gross_weight . "," . $fg_rm . "," . $oil_category . ","
// 				. $alias . "," . $hsn_sac . "," . $packing_realization . "," . $statename . "," . '' . "," . '' . "," . $sp_login . "," . $dealer_login . "\n";
// 			$count++;
// 		}
// 		$datacontents = $header_product . "\n" . $contents_product;
// 		$folderPath = __DIR__ . "/dump/$foldername";

// 		if (!file_exists($folderPath)) {
// 			mkdir($folderPath, 0777, true);
// 			chmod($folderPath, 0777);
// 		}
// 		$fp = fopen("$basepath$foldername/sku master.csv", "wb");
// 		fwrite($fp, $datacontents);
// 		fclose($fp);
// 		echo "$foldername/sku master.csv";
// 	} else {
// 		echo "<strong><font color=\"red\">No Records Found</font></strong>";
// 	}
// }

if ($attribute == 'product') {
    $product_group_name = "";
    $product_subgroup_name = "";
    $product_brand_code = "";

    if ($no_of_filter == 1) {
        $clause = " `product_master` WHERE 1 ";
    }
    if ($no_of_filter == 2) {
        $product_group_name = "product_group_master.product_group_name,";
        $clause = " `product_master`, `product_group_master` WHERE
            product_master.product_group_code = product_group_master.product_group_code";
    }
    if ($no_of_filter == 3 || strtoupper($_SESSION['nick_name']) == 'ASL') {
        $product_group_name = "product_group_master.product_group_name,";
        $product_subgroup_name = "product_sub_group_master.product_sub_group_name,";
        $clause = "`product_master`, `product_group_master`, product_sub_group_master WHERE
            product_master.product_group_code = product_group_master.product_group_code AND
            product_master.product_sub_group_code = product_sub_group_master.product_sub_group_code";
    }
    if ($no_of_filter == 4 && strtoupper($_SESSION['nick_name']) != 'ASL') {
        $product_group_name = "product_group_master.product_group_name,";
        $product_subgroup_name = "product_sub_group_master.product_sub_group_name,";
        $product_brand_code = "product_brand_master.product_brand_name,";
        $clause = "`product_master`, `product_group_master`, `product_sub_group_master`, `product_brand_master` WHERE
            product_master.product_group_code = product_group_master.product_group_code AND
            product_master.product_sub_group_code = product_sub_group_master.product_sub_group_code AND
            product_master.product_brand_code = product_brand_master.product_brand_code";
    }

    if ($providing_code == 'yes') {
        $branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code = product_master.branch_code) AS branch_code";
    } else {
        $branch = "(SELECT branch_name FROM branch_master WHERE branch_code = product_master.branch_code) AS branch_code";
    }

    $sql_product = "SELECT $branch,
        product_master.dns_prod_code AS Prod_Code,
        product_master.prod_desc,
        $product_group_name
        $product_subgroup_name
        $product_brand_code
        product_master.cl_stk,
        product_master.acedns,
        product_master.black_list,
        product_master.vertical_value,
        product_master.UOM1,
        product_master.UOM2,
        product_master.conversion_factor AS Conversion,
        product_master.pack_size AS Pack_Size,
        product_master.UOM3,
        product_master.conversion_factor_two AS Conversion2,
        product_master.TD,
        product_master.focus,
        product_master.vat,
        product_master.pack_unit,
        product_master.prod_size,
        product_master.lead_time,
        product_master.buffer_level,
        product_master.max_level_marketing,
        product_master.UOM4,
        product_master.UOM5,
        product_master.gross_weight,
        product_master.fg_rm,
        product_master.oil_category,
        product_master.alias,
        product_master.hsn_sac,
        product_master.packing_realization,
        product_master.state_code,
        product_master.dealer_login,
        product_master.sp_login
        FROM " . $clause;

    $res_product = mysqli_query($link, $sql_product);
    $total_product = mysqli_num_rows($res_product);

    if ($total_product > 0) {
        $contents_product = '';
        $header_product = "Branch code/name,Prod_Code,prod_desc,brand_code (fk)/brand_name,brand_form_code (fk)/brand_form_name,brand_sub_form_code(fk)/brand_sub_form_name,cl_stk,acends,black_list,vertical_value,UOM1,UOM2,Conversion,Pack size,UOM3,Conversion2,TD,Focus,VAT,Pack Unit,Prod size,Lead time,Buffer level,Max level marketing,UOM4,UOM5,Gross weight,FG_RM,Oil category,Alias,HSN/SAC,Packing Realization,Statename,Prod Full Form,Category,SP Login,Dealer Login";

        while ($row_product = mysqli_fetch_assoc($res_product)) {
            $branch_code           = str_replace(',', '', $row_product['branch_code'] ?? '');
            $Prod_Code             = str_replace(',', '', $row_product['Prod_Code'] ?? '');
            $prod_desc             = str_replace(',', '', $row_product['prod_desc'] ?? '');
            $product_group_name    = str_replace(',', '', $row_product['product_group_name'] ?? '');
            $product_sub_group_name= str_replace(',', '', $row_product['product_sub_group_name'] ?? '');
            $product_brand_name    = str_replace(',', '', $row_product['product_brand_name'] ?? '');
            $cl_stk                = $row_product['cl_stk'] ?? '';
            $acedns                = $row_product['acedns'] ?? '';
            $black_list            = $row_product['black_list'] ?? '';
            $vertical_value        = $row_product['vertical_value'] ?? '';
            $UOM1                  = $row_product['UOM1'] ?? '';
            $UOM2                  = $row_product['UOM2'] ?? '';
            $Conversion            = $row_product['Conversion'] ?? '';
            $Pack_Size             = $row_product['Pack_Size'] ?? '';
            $UOM3                  = $row_product['UOM3'] ?? '';
            $Conversion2           = $row_product['Conversion2'] ?? '';
            $TD                    = $row_product['TD'] ?? '';
            $focus                 = $row_product['focus'] ?? '';
            $vat                   = $row_product['vat'] ?? '';
            $pack_unit             = $row_product['pack_unit'] ?? '';
            $prod_size             = $row_product['prod_size'] ?? '';
            $lead_time             = $row_product['lead_time'] ?? '';
            $buffer_level          = $row_product['buffer_level'] ?? '';
            $max_level_marketing   = $row_product['max_level_marketing'] ?? '';
            $UOM4                  = $row_product['UOM4'] ?? '';
            $UOM5                  = $row_product['UOM5'] ?? '';
            $gross_weight          = $row_product['gross_weight'] ?? '';
            $fg_rm                 = $row_product['fg_rm'] ?? '';
            $oil_category          = $row_product['oil_category'] ?? '';
            $alias                 = $row_product['alias'] ?? '';
            $hsn_sac               = $row_product['hsn_sac'] ?? '';
            $packing_realization   = $row_product['packing_realization'] ?? '';
            $state_code            = $row_product['state_code'] ?? '';
            $dealer_login          = $row_product['dealer_login'] ?? '';
            $sp_login              = $row_product['sp_login'] ?? '';

            $sqlstatecode = "SELECT statename FROM state_master WHERE state_code='" . $state_code . "'";
            if ($foldername === "DURO") {
                $sqlstatecode = "SELECT state_name FROM state_master WHERE state_code='" . $state_code . "'";
            }

            $rsstatecode = mysqli_query($link, $sqlstatecode);
            $rowstatecode = mysqli_fetch_assoc($rsstatecode);
            $statename = $foldername === "DURO"
                ? ($rowstatecode['state_name'] ?? '')
                : ($rowstatecode['statename'] ?? '');

            $contents_product .= implode(",", [
                $branch_code, $Prod_Code, $prod_desc, $product_group_name, $product_sub_group_name,
                $product_brand_name, $cl_stk, $acedns, $black_list, $vertical_value, $UOM1, $UOM2,
                $Conversion, $Pack_Size, $UOM3, $Conversion2, $TD, $focus, $vat, $pack_unit,
                $prod_size, $lead_time, $buffer_level, $max_level_marketing, $UOM4, $UOM5,
                $gross_weight, $fg_rm, $oil_category, $alias, $hsn_sac, $packing_realization,
                $statename, '', '', $sp_login, $dealer_login
            ]) . "\n";
        }

        $datacontents = $header_product . "\n" . $contents_product;
        $folderPath = __DIR__ . "/dump/$foldername";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
            chmod($folderPath, 0777);
        }

        $filePath = "$basepath$foldername/sku master.csv";
        $fp = fopen($filePath, "wb");
        fwrite($fp, $datacontents);
        fclose($fp);

        echo "$foldername/sku master.csv";
    } else {
        echo "<strong><font color='red'>No Records Found</font></strong>";
    }
}

if ($attribute == 'mrp') {
	if (branch_wise_mrp == 'yes') {
		if (providing_code == 'yes') {
			$branch = "(SELECT dns_branch_code FROM branch_master WHERE branch_code=MRP.branch_code) AS branch_code,";
			$product = "PM.dns_prod_code,";
		} else {
			$branch = "(SELECT branch_name FROM branch_master WHERE branch_code=MRP.branch_code) AS branch_code,";
			$product = "PM.prod_desc AS dns_prod_code,";
		}
	} else {
		$branch = '';
		if (providing_code == 'yes') {
			$product = "PM.dns_prod_code,";
		} else {
			$product = "PM.prod_desc AS dns_prod_code,";
		}
	}
	if (mrp == 'yes') {
		$mrp = "MRP.mrp";
	} else {
		$mrp = "0 AS mrp";
	}
	if (sale_rate == 'yes') {
		$sale_rate = "MRP.sale_rate";
	} else {
		$sale_rate = "0 AS sale_rate";
	}
	if (state_wise_mrp == 'yes') {
		if (providing_code == 'yes') {
			$state_wise_mrp = ",(SELECT dns_state_code FROM state_master WHERE state_code=MRP.state_code) AS state";
		} else {
			$state_wise_mrp = ",(SELECT statename FROM state_master WHERE state_code=MRP.state_code) AS state";
		}
	} else {
		$state_wise_mrp = '';
	}


	$sqlmrp = "SELECT $branch $product
					'' AS mrp_code, 
					$mrp, 
					$sale_rate, 
					MRP.vertical_value, 
					0 AS destination_code, 
					0 AS sale_type, 
					MRP.acedns, 
					MRP.ws_rate, 
					MRP.distributor_rate, 
					MRP.ss_rate,
					MRP.UOM, 
					MRP.depot_rate
					$state_wise_mrp 
					FROM product_master PM, mrp MRP WHERE MRP.product_code = PM.prod_code";

	$res_mrp = mysqli_query($link, $sqlmrp);
	$total_mrp = mysqli_num_rows($res_mrp);

	if ($total_mrp > 0) {
		$header_mrp = "Branch code/name" . "," . "Prod_Code" . "," . "mrp_code" . "," . "mrp" . "," . "sale_rate" . "," . "vertical_value" . "," . "destination_code" . "," .
			"sale_type" . "," . "acedns" . "," . "ws_rate" . "," . "distributor_rate" . "," . "ss_rate" . "," . "depot_rate" . "," . "statename" . "," . "UOM";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_mrp = mysqli_fetch_assoc($res_mrp)) {
			$branch_code = $row_mrp['branch_code'];
			$Prod_Code = $row_mrp['dns_prod_code'];
			$mrp_code = $row_mrp['mrp_code'];
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
			$UOM = $row_mrp['UOM'];

			$contents_mrp .= $branch_code . "," . $Prod_Code . "," . $mrp_code . "," . $mrp . "," . $sale_rate . "," . $vertical_value . "," . $destination_code . "," . $sale_type . "," . $acedns . "," . $ws_rate . "," . $distributor_rate . "," . $ss_rate . "," . $depot_rate . "," . $state . "," . $UOM . "\n";
			$count++;
		}
		$datacontents = $header_mrp . "\n" . $contents_mrp;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/MRP.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/MRP.csv";
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'distributor_route_emp') {
	if (strtoupper($_SESSION['nick_name']) == 'ARCHITA' || strtoupper($_SESSION['nick_name']) == 'ASL' || strtoupper($_SESSION['nick_name']) == 'DNVFOODS') {
		$sql_distributor = "SELECT CM.dns_customer_code, (SELECT dns_emp_code FROM employee_master WHERE emp_code = DRR.emp_code ) AS emp_code_name, (SELECT dns_route_code FROM route_master WHERE route_code = DRR.route_code ) AS route_name,DRR.visit_day,DRR.state,DRR.acedns FROM `distributor_route_relation` DRR, customer_master CM WHERE DRR.distributor_code = CM.customer_code";
	}
	if (strtoupper($_SESSION['nick_name']) == 'EDIBLE' || strtoupper($_SESSION['nick_name']) == 'EDIBLEH' || strtoupper($_SESSION['nick_name']) == 'AJANTA' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONE') {
		$sql_distributor = "SELECT CM.customer_name as dns_customer_code, (SELECT emp_name FROM employee_master WHERE emp_code = DRR.emp_code ) AS emp_code_name, 
		(SELECT route_name FROM route_master WHERE route_code = DRR.route_code ) AS route_name,DRR.visit_day,DRR.state,DRR.acedns FROM `distributor_route_relation` DRR, customer_master CM WHERE DRR.distributor_code = CM.customer_code";
	}
	$res_distributor = mysqli_query($link, $sql_distributor);
	$total_distributor = mysqli_num_rows($res_distributor);

	if ($total_distributor > 0) {
		$header_distributor = "Customer Name" . "," . "Employee Name" . "," . "Route Name" . "," . "State" . "," . "Visit Day" . "," . "acedns";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_distributor = mysqli_fetch_assoc($res_distributor)) {
			$customer_name = str_replace(',', '', $row_distributor['dns_customer_code']);
			$emp_code_name = str_replace(',', '', $row_distributor['emp_code_name']);
			$state_name = $row_distributor['state'];
			$visit_day = $row_distributor['visit_day'];
			$acedns = $row_distributor['acedns'];
			$route_name = str_replace(',', '', $row_distributor['route_name']);

			$contents_distributor .= $customer_name . "," . $emp_code_name . "," . $route_name . "," . $state_name . "," . $visit_day . "," . $acedns . "\n";
			$count++;
		}
		$datacontents = $header_distributor . "\n" . $contents_distributor;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Distributor_emp_route_relation.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Distributor_emp_route_relation.csv";
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'route') {
	$sql_route = "SELECT dns_route_code,route_name FROM route_master ORDER BY route_name ASC";
	$res_route = mysqli_query($link, $sql_route);
	$total_route = mysqli_num_rows($res_route);
	//exit();
	$contents_route = '';
	if ($total_route > 0) {
		$header_route = "Route Code" . "," . "Route Name";
		while ($row_route = mysqli_fetch_assoc($res_route)) {
			$dns_route_code = $row_route['dns_route_code'];
			$route_name =  '"' . $row_route['route_name'] . '"';
			$contents_route .= $dns_route_code . "," . $route_name . "\n";
		}
		$datacontents = $header_route . "\n" . $contents_route;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/Route master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Route master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'destination') {
	$sql_destination = "SELECT dns_destination_code,destination_name FROM destination_master ORDER BY destination_name ASC";
	$res_destination = mysqli_query($link, $sql_destination);
	$total_destination = mysqli_num_rows($res_destination);
	//exit();
	$contents_destination='';
	if ($total_destination > 0) {
		$header_destination = "Destination Code" . "," . "Destination Name";
		while ($row_destination = mysqli_fetch_assoc($res_destination)) {
			$dns_destination_code = $row_destination['dns_destination_code'];
			$destination_name =  '"' . $row_destination['destination_name'] . '"';
			$contents_destination .= $dns_destination_code . "," . $destination_name . "\n";
		}
		$datacontents = $header_destination . "\n" . $contents_destination;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/Destination master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Destination master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'state') {
	$sql_state = "SELECT dns_state_code,statename FROM state_master ORDER BY dns_state_code ASC";
	$res_state = mysqli_query($link, $sql_state);
	$total_state = mysqli_num_rows($res_state);
	if ($total_state > 0) {
		$header_state = "dns state code" . "," . "state";
		while ($row_state = mysqli_fetch_assoc($res_state)) {
			$dns_state_code = $row_state['dns_state_code'];
			$state = str_replace(',', '', $row_state['statename']);

			$contents_state .= $dns_state_code . "," . $state . "\n";
			$count++;
		}
		$datacontents = $header_state . "\n" . $contents_state;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/state master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/state master.csv";
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'conversion') {
	$sql_conversion = "SELECT prod_code,`mapped_prod_code`,is_flash,`flash_name`,`add_subtract_val` FROM `product_unit_coversion_matrix` WHERE acedns='Y'";
	$res_conversion = mysqli_query($link, $sql_conversion);
	$total_conversion = mysqli_num_rows($res_conversion);
	if ($total_conversion > 0) {
		$header_conversion = "Prod code" . "," . "Mapped prod code" . "," . "Is Flash" . "," . "Flash name" . "," . "Add subtract val";
		while ($row_conversion = mysqli_fetch_assoc($res_conversion)) {
			$prod_code = $row_conversion['prod_code'];
			$mapped_prod_code = $row_conversion['mapped_prod_code'];
			$is_flash = $row_conversion['is_flash'];
			$flash_name = $row_conversion['flash_name'];
			$add_subtract_val = $row_conversion['add_subtract_val'];

			$contents_conversion .= $prod_code . "," . $mapped_prod_code . "," . $is_flash . "," . $flash_name . "," . $add_subtract_val . "\n";
			$count++;
		}
		$datacontents = $header_conversion . "\n" . $contents_conversion;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/conversion.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/conversion.csv";
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}


if ($attribute == 'site') {
	$sql_site = "SELECT SM.site_id,SM.emp_code,(SELECT dns_emp_code FROM employee_master WHERE emp_code = SM.emp_code ) AS emp_code_name,SM.site_name,SM.address,SM.city,SM.location,SM.area,SM.sub_area,SM.state,SM.pin,SM.contact_person,SM.phone_no,SM.sq_feet_area,SM.project_type,SM.current_status,SM.product_info,SM.check_in_date,SM.stage_of_construction,SM.remarks,SM.follow_up_date,SM.expected_month_maturity,SM.email,SM.contact_person_type,SM.nature_of_work,SM.site_owner_name,SM.site_owner_contact FROM site_master SM";
	$res_site = mysqli_query($link, $sql_site);
	$total_site = mysqli_num_rows($res_site);
	//exit();
	if ($total_site > 0) {
		$header_site = "site name" . "," . "Employee" . "," . "address" . "," . "city" . "," . "location" . "," . "area" . "," . "sub_area" . "," . "state" . "," . "pin" . "," . "contact_person" . "," . "phone_no" . "," . "sq_feet_area" . "," . "project_type" . "," . "current_status" . "," . "product_info" . "," . "check_in_date" . "," . "stage_of_construction" . "," . "remarks" . "," . "follow_up_date" . "," . "expected_month_maturity" . "," . "email" . "," . "contact_person_type" . "," . "nature_of_work" . "," . "site_owner_name" . "," . "site_owner_contact";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_site = mysqli_fetch_assoc($res_site)) {
			$site_id = $row_site['site_id'];
			$dns_emp_code = $row_site['emp_code_name'];
			$site_name = str_replace(',', '', $row_site['site_name']);
			$address = str_replace(',', '', $row_site['address']);
			$city = str_replace(',', '', $row_site['city']);
			$location = str_replace(',', '', $row_site['location']);
			$area = str_replace(',', '', $row_site['area']);
			$sub_area = str_replace(',', '', $row_site['sub_area']);
			$state =  str_replace(',', '', $row_site['state']);
			$pin = str_replace(',', '', $row_site['pin']);
			$pin = str_replace(',', '', preg_replace('/[\r\n]+/', '', $pin));
			$contact_person = str_replace(',', '', $row_site['contact_person']);
			$contact_person = str_replace(',', '', preg_replace('/[\r\n]+/', '', $contact_person));
			$phone_no = str_replace(',', '', $row_site['phone_no']);
			$phone_no = str_replace(',', '', preg_replace('/[\r\n]+/', '', $phone_no));
			$sq_feet_area = str_replace(',', '', $row_site['sq_feet_area']);
			$sq_feet_area = str_replace(',', '', preg_replace('/[\r\n]+/', '', $sq_feet_area));
			$project_type = $row_site['project_type'];
			$current_status = $row_site['current_status'];
			$product_info = $row_site['product_info'];
			$check_in_date = $row_site['check_in_date'];
			$stage_of_construction = $row_site['stage_of_construction'];
			$remarks = $row_site['remarks'];
			$follow_up_date = $row_site['follow_up_date'];
			$expected_month_maturity = $row_site['expected_month_maturity'];
			$email = $row_site['email'];
			$contact_person_type = $row_site['contact_person_type'];
			$nature_of_work = $row_site['nature_of_work'];
			$site_owner_name = $row_site['site_owner_name'];
			$site_owner_contact = $row_site['site_owner_contact'];

			/*if(substr($f_code,0,2)=='FE') $creation_type='ADD';
			else						  $creation_type='UPLOAD';*/

			$contents_site .= $site_name . "," . $dns_emp_code . "," . $address . "," . $city . "," . $location . "," . $area . "," . $sub_area . "," . $state . "," . $pin . "," . $contact_person . "," . $phone_no . "," . $sq_feet_area . "," . $project_type . "," . $current_status . "," . $product_info . "," . $check_in_date . "," . $stage_of_construction . "," . $remarks . "," . $follow_up_date . "," . $expected_month_maturity . "," . $email . "," . $contact_person_type . "," . $nature_of_work . "," . $site_owner_name . "," . $site_owner_contact . "\n";
			$count++;
		}
		$datacontents = $header_site . "\n" . $contents_site;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/site master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/site master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'facilitator') {
	$sql_facilitator = "SELECT FM.f_code,FM.emp_code,(SELECT dns_emp_code FROM employee_master WHERE emp_code = FM.emp_code ) AS emp_code_name,(SELECT dns_branch_code FROM branch_master WHERE branch_code = FM.branch_code ) AS branch_code_name,FM.facilitator_name,FM.f_type,FM.firm_name,FM.f_address,FM.f_pin,FM.f_area,FM.f_sub_area,FM.mobile_no,FM.email_id,FM.dob,FM.annniversary,FM.acedns,FM.check_in_date,FM.f_category FROM facilitator_master FM";
	$res_facilitator = mysqli_query($link, $sql_facilitator);
	$total_facilitator = mysqli_num_rows($res_facilitator);
	//exit();
	if ($total_facilitator > 0) {
		$header_facilitator = "Faclitator name" . "," . "Employee" . "," . "Facilitator Type" . "," . "firm_name" . "," . "address" . "," . "pin" . "," . "Sub area" . "," . "area" . "," . "mobile no" . "," . "email" . "," . "DOB" . "," . "Anniversary Date" . "," . "Acedns" . "," . "Branch";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_facilitator = mysqli_fetch_assoc($res_facilitator)) {
			$f_code = $row_facilitator['f_code'];
			$dns_branch_code = $row_facilitator['branch_code_name'];
			$dns_emp_code = $row_facilitator['emp_code_name'];
			$facilitator_name = str_replace(',', '', $row_facilitator['facilitator_name']);
			$f_type = str_replace(',', '', $row_facilitator['f_type']);
			$firm_name = str_replace(',', '', $row_facilitator['firm_name']);
			$f_address = str_replace(',', '', $row_facilitator['f_address']);
			$f_pin = str_replace(',', '', $row_facilitator['f_pin']);
			$f_sub_area = str_replace(',', '', $row_facilitator['f_sub_area']);
			$f_area =  str_replace(',', '', $row_facilitator['f_area']);
			$mobile_no = str_replace(',', '', $row_facilitator['mobile_no']);
			$email_id = str_replace(',', '', $row_facilitator['email_id']);
			$dob = str_replace(',', '', $row_facilitator['dob']);
			$annniversary = str_replace(',', '', $row_facilitator['annniversary']);
			$acedns = $row_facilitator['acedns'];

			/*if(substr($f_code,0,2)=='FE') $creation_type='ADD';
			else						  $creation_type='UPLOAD';*/

			$contents_facilitator .= $facilitator_name . "," . $dns_emp_code . "," . $f_type . "," . $firm_name . "," . $f_address . "," . $f_pin . "," . $f_sub_area . "," . $f_area . "," . $mobile_no . "," . $email_id . "," . $dob . "," . $annniversary . "," . $acedns . "," . $dns_branch_code . "\n";
			$count++;
		}
		$datacontents = $header_facilitator . "\n" . $contents_facilitator;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Facilitator master.csv", "wb");
		fwrite($fp, $datacontents);
		fclose($fp);
		echo "$foldername/Facilitator master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'fecilitator') {
	$sql_fecilitator = "SELECT FM.f_code,FM.dns_f_code,FM.facilitator_name,FM.emp_code,FM.emp_name,FM.f_type,FM.designation,FM.firm_name,FM.f_address,FM.f_pin,FM.f_district,
	 FM.f_area,FM.mobile_no,FM.whatsapp_no,FM.email_id,FM.dob,FM.annniversary,FM.acedns,FM.branch FROM facilitator_master FM";
	$res_fecilitator = mysqli_query($link, $sql_fecilitator);
	$total_fecilitator = mysqli_num_rows($res_fecilitator);
	//exit();
	$contents_fecilitator = '';
	if ($total_fecilitator > 0) {
		$header_fecilitator = "Faclitator code" . "," . "Faclitator name" . "," . "Employee code" . "," . "Employee Name" . "," . "Facilitator Type" . "," . "Designation" . "," . "Firm_name" . "," . "Address" . "," . "pin" . "," . "Dist" . "," . "area" . "," . "mobile no" . "," . "Whatsapp No" . "," . "email" . "," . "DOB" . "," . "Anniversary Date" . "," . "Acedns" . "," . "Branch";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_fecilitator = mysqli_fetch_assoc($res_fecilitator)) {
			$dns_f_code = $row_fecilitator['dns_f_code'];
			$facilitator_name = str_replace(',', '', $row_fecilitator['facilitator_name']);
			$emp_code = $row_fecilitator['emp_code'];
			$emp_name = str_replace(',', '', $row_fecilitator['emp_name']);
			$f_type = str_replace(',', '', $row_fecilitator['f_type']);
			$designation = str_replace(',', '', $row_fecilitator['designation']);
			$firm_name = str_replace(',', '', $row_fecilitator['firm_name']);
			$f_address = str_replace(',', '', $row_fecilitator['f_address']);
			$f_pin = str_replace(',', '', $row_fecilitator['f_pin']);
			$f_district = str_replace(',', '', $row_fecilitator['f_district']);
			$f_area =  str_replace(',', '', $row_fecilitator['f_area']);
			$mobile_no = str_replace(',', '', $row_fecilitator['mobile_no']);
			$whatsapp_no = str_replace(',', '', $row_fecilitator['whatsapp_no']);
			$email_id = str_replace(',', '', $row_fecilitator['email_id']);
			$dob = str_replace(',', '', $row_fecilitator['dob']);
			$annniversary = str_replace(',', '', $row_fecilitator['annniversary']);
			$acedns = $row_fecilitator['acedns'];
			$branch = $row_fecilitator['branch'];

			$contents_fecilitator .= $dns_f_code . "," . $facilitator_name . "," . $emp_code . "," . $emp_name . "," . $f_type . "," . $designation . "," . $firm_name . "," . $f_address . "," . $f_pin . "," . $f_district . "," . $f_area . "," . $mobile_no . "," . $whatsapp_no . "," . $email_id . "," . $dob . "," . $annniversary . "," . $acedns . "," . $branch . "\n";
			$count++;
		}
		$datacontentsfec = $header_fecilitator . "\n" . $contents_fecilitator;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/fecilitator master.csv", "wb");
		fwrite($fp, $datacontentsfec);
		fclose($fp);
		echo "$foldername/fecilitator master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'beatewise_TA_DA') {
	$sql_beatewise_TA_DA = "SELECT emp_name,reporting_to_name,route_name,HQ_EX_OS,DA,distance,TA FROM beatwise_TA_DA";
	$res_beatewise_TA_DA = mysqli_query($link, $sql_beatewise_TA_DA);
	$total_beatewise_TA_DA = mysqli_num_rows($res_beatewise_TA_DA);
	//exit();
	if ($total_beatewise_TA_DA > 0) {
		$header_beatewise_TA_DA = "SR" . "," . "SO" . "," . "BEAT" . "," . "HQ/EX/OS" . "," . "DA" . "," . "ONE WAY DISTANCE" . "," . "TA";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_beatewise_TA_DA = mysqli_fetch_assoc($res_beatewise_TA_DA)) {
			$emp_name = $row_beatewise_TA_DA['emp_name'];
			$reporting_to_name = str_replace(',', '', $row_beatewise_TA_DA['reporting_to_name']);
			$route_name = str_replace(',', '', $row_beatewise_TA_DA['route_name']);
			$HQ_EX_OS = str_replace(',', '', $row_beatewise_TA_DA['HQ_EX_OS']);
			$DA = str_replace(',', '', $row_beatewise_TA_DA['DA']);
			$distance = str_replace(',', '', $row_beatewise_TA_DA['distance']);
			$TA = str_replace(',', '', $row_beatewise_TA_DA['TA']);

			$contents_beatewise_TA_DA .= $emp_name . "," . $reporting_to_name . "," . $route_name . "," . $HQ_EX_OS . "," . $DA . "," . $distance . "," . $TA . "\n";
			$count++;
		}
		$datacontentsTADA = $header_beatewise_TA_DA . "\n" . $contents_beatewise_TA_DA;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/beatewise_TA_DA.csv", "wb");
		fwrite($fp, $datacontentsTADA);
		fclose($fp);
		echo "$foldername/beatewise_TA_DA.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'weightage') {
	$sql_weightage = "SELECT SPW.*,PM.prod_desc FROM state_product_wise_weightage SPW,product_master PM WHERE SPW.prod_code=PM.prod_code AND SPW.acedns='yes'";
	$res_weightage = mysqli_query($link, $sql_weightage);
	$total_weightage = mysqli_num_rows($res_weightage);
	//exit();
	if ($total_weightage > 0) {
		$header_weightage = "Sate name" . "," . "Sku name" . "," . "Uom1" . "," . "Uom2" . "," . "Weightage coversion 1" . "," . "Weightage coversion 2";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_weightage = mysqli_fetch_assoc($res_weightage)) {
			$state_name = $row_weightage['state_name'];
			$prod_desc = $row_weightage['prod_desc'];
			$UOM1 = $row_weightage['UOM1'];
			$UOM2 = $row_weightage['UOM2'];
			$weightage_conversio1 = $row_weightage['weightage_conversio1'];
			$weightage_conversion2 = $row_weightage['weightage_conversion2'];


			$contents_weightage .= $state_name . "," . $prod_desc . "," . $UOM1 . "," . $UOM2 . "," . $weightage_conversio1 . "," . $weightage_conversion2 . "\n";
			$count++;
		}
		$datacontentsweightage = $header_weightage . "\n" . $contents_weightage;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/weightage conversion.csv", "wb");
		fwrite($fp, $datacontentsweightage);
		fclose($fp);
		echo "$foldername/weightage conversion.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'manager_list') {
	$sql_manager_list = "SELECT emp_code,emp_name,designation,phone_no FROM whats_app_phone_list";
	$res_manager_list = mysqli_query($link, $sql_manager_list);
	$total_manager_list = mysqli_num_rows($res_manager_list);
	//exit();
	$contents_manager_list='';
	if ($total_manager_list > 0) {
		$header_manager_list = "SFA ID" . "," . "EMP NAME" . "," . "DESIGNATION" . "," . "MOBILE NO";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_manager_list = mysqli_fetch_assoc($res_manager_list)) {
			$emp_name = $row_manager_list['emp_name'];
			$emp_code = $row_manager_list['emp_code'];
			$designation	= $row_manager_list['designation'];
			$phone_no = $row_manager_list['phone_no'];

			$contents_manager_list .= $emp_code . "," . $emp_name . "," . $designation . "," . $phone_no . "\n";
			$count++;
		}
		$datacontentsmanager = $header_manager_list . "\n" . $contents_manager_list;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/manager list.csv", "wb");
		fwrite($fp, $datacontentsmanager);
		fclose($fp);
		echo "$foldername/manager list.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'customer_broker') {
	$sqlcustbroker = "SELECT  DISTINCT CM.dns_customer_code,CM.customer_name,BM.dns_broker_id,BM.broker_name,CBR.acedns 
					FROM customer_master CM,customer_broker_relation CBR,broker_master BM
					WHERE CBR.customer_code=CM.customer_code AND CBR.broker_code=BM.broker_id";
	$rescustbroker = mysqli_query($link, $sqlcustbroker);
	$totalcustbroker = mysqli_num_rows($rescustbroker);
	//exit();
	$contents_custbroker = '';
	if ($totalcustbroker > 0) {
		$headercustbroker = "dns_customer_code" . "," . "customer_name" . "," . "dns_broker_id" . "," . "broker_name" . "," . "acedns";
		while ($rowcustbroker = mysqli_fetch_assoc($rescustbroker)) {
			$dns_customer_code = $rowcustbroker['dns_customer_code'];
			$customer_name = $rowcustbroker['customer_name'];
			$dns_broker_id = $rowcustbroker['dns_broker_id'];
			$broker_name = $rowcustbroker['broker_name'];
			$acedns = $rowcustbroker['acedns'];

			$contents_custbroker .= $dns_customer_code . "," . $customer_name . "," . $dns_broker_id . "," . $broker_name . "," . $acedns . "\n";
			$count++;
		}
		$datacontentscustbroker = $headercustbroker . "\n" . $contents_custbroker;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/Customer broker mapping.csv", "wb");
		fwrite($fp, $datacontentscustbroker);
		fclose($fp);
		echo "$foldername/Customer broker mapping.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'TA_DA_Master') {
	$sql_TA_DA = "SELECT * FROM TA_DA_master";
	$res_TA_DA = mysqli_query($link, $sql_TA_DA);
	$total_TA_DA = mysqli_num_rows($res_TA_DA);
	//exit();
	if ($total_TA_DA > 0) {
		$header_TA_DA = "Employee Code" . "," . "Employee Name" . "," . "Per KM TA Amt" . "," . "DA HQ" . "," . "DA EX HQ" . "," . "DA Outstation";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_TA_DA = mysqli_fetch_assoc($res_TA_DA)) {
			$emp_code = $row_TA_DA['emp_code'];
			$emp_name = $row_TA_DA['emp_name'];
			$per_km_TA = $row_TA_DA['per_km_TA'];
			$DA_HQ = $row_TA_DA['DA_HQ'];
			$DA_EX_HQ = $row_TA_DA['DA_EX_HQ'];
			$DA_outstation = $row_TA_DA['DA_outstation'];


			$contents_weightage .= $emp_code . "," . $emp_name . "," . $per_km_TA . "," . $DA_HQ . "," . $DA_EX_HQ . "," . $DA_outstation . "\n";
			$count++;
		}
		$datacontentsweightage = $header_TA_DA . "\n" . $contents_weightage;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/TA_DA.csv", "wb");
		fwrite($fp, $datacontentsweightage);
		fclose($fp);
		echo "$foldername/TA_DA.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'PJP') {
	$sql_pjp = "SELECT RP.*,EM.emp_name,EM.state,(SELECT route_name FROM route_master WHERE route_code = RP.route_code) AS route_name,
				  (SELECT area FROM route_master WHERE route_code = RP.route_code) AS area_fetched,
				  (SELECT route_no FROM route_master WHERE route_code = RP.route_code) AS route_no_fetched  FROM route_plan RP,employee_master EM WHERE 
				RP.emp_code=EM.emp_code  ORDER BY RP.create_date DESC";
	$res_pjp = mysqli_query($link, $sql_pjp);
	$total_pjp = mysqli_num_rows($res_pjp);
	//exit();
	if ($total_pjp > 0) {
		$header_pjp = "State" . "," . "Date" . "," . "Day" . "," . "Employee Name" . "," . "Area" . "," . "Route Name" . "," . "Route No." . "," . "Objective";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_pjp = mysqli_fetch_assoc($res_pjp)) {
			$state_name = $row_pjp['state'];
			$visit_date = $row_pjp['visit_date'];
			$visit_date = date('d-m-Y', strtotime($visit_date));
			$Day = date('l', strtotime($visit_date));
			$emp_name = $row_pjp['emp_name'];
			$area = $row_pjp['route_name'];
			$area = $row_pjp['area_fetched'];;
			$route_name = $row_pjp['route_name'];
			$route_no = $row_pjp['route_no_fetched'];
			$objective = $row_pjp['objective'];

			$contents_pjp .= $state_name . "," . $visit_date . "," . $Day . "," . $emp_name . "," . $area . "," . $route_name . "," . $route_no . "," . $objective . "\n";
			$count++;
		}
		$datacontentspjp = $header_pjp . "\n" . $contents_pjp;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/PJP UPLOAD.csv", "wb");
		fwrite($fp, $datacontentspjp);
		fclose($fp);
		echo "$foldername/PJP UPLOAD.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'Target') {
	$sql_target = "SELECT DISTINCT SCW.emp_code, SCW.emp_name,SCW.route_code, SCW.route_name,SCW.product_category,SCW.area,SCW.route_no, SCW.jan_31_target,SCW.feb_28_target,SCW.mar_31_target,
			SCW.apr_30_target,SCW.may_31_target,SCW.jun_30_target,SCW.jul_31_target,SCW.aug_31_target,SCW.sep_30_target
			,SCW.oct_31_target,SCW.nov_30_target,SCW.dec_31_target FROM self_appraisal_route_product_group_wise	SCW";
	$res_target = mysqli_query($link, $sql_target);
	$total_target = mysqli_num_rows($res_target);
	//exit();
	if ($total_target > 0) {
		$date = gmdate('d', strtotime('+330 minute'));
		$month = gmdate('m', strtotime('+330 minute'));
		$year = gmdate('Y', strtotime('+330 minute'));
		$header_target = "Employee Name" . "," . "Area" . "," . "Route Name" . "," . "Route No." . "," . "DT'S Target(Kg)" . "," . "DW Target(Kg)" . "," . "Other Target(Kg)";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		$emp_route_array = array();
		$contents_target = '';
		while ($row_target = mysqli_fetch_assoc($res_target)) {
			$emp_code = $row_target['emp_code'];
			$route_code = $row_target['route_code'];
			${'area' . $emp_code . $route_code} = $row_target['area'];;
			${'route_no' . $emp_code . $route_code} = $row_target['route_no'];;
			${'emp_name' . $emp_code . $route_code} = $row_target['emp_name'];
			${'route_name' . $emp_code . $route_code} = '"' . $row_target['route_name'] . '"';

			${'product_category' . $emp_code . $route_code} = $row_target['product_category'];

			$first_date = '01' . '-' . $month . '-' . $year;
			$month_abrev = date('M', strtotime($first_date));
			$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			if (${'product_category' . $emp_code . $route_code} == 'DTS') {
				$DTS_target = strtolower($month_abrev) . '_' . $days . '_target';
				${'DTS_target' . $emp_code . $route_code} = $row_target[$DTS_target];
			}
			if (${'product_category' . $emp_code . $route_code} == 'DW') {
				$DW_target = strtolower($month_abrev) . '_' . $days . '_target';
				${'DW_target' . $emp_code . $route_code} = $row_target[$DW_target];
			}
			if (${'product_category' . $emp_code . $route_code} == 'Other') {
				$Other_target = strtolower($month_abrev) . '_' . $days . '_target';
				${'Other_target' . $emp_code . $route_code} = $row_target[$Other_target];
			}
			$emp_route_string = $emp_code . '#' . $route_code;

			if (!in_array($emp_route_string, $emp_route_array)) {
				array_push($emp_route_array, $emp_route_string);
			}
		}
		foreach ($emp_route_array as $emp_route_val) {
			$emp_route_val_str = explode("#", $emp_route_val);
			$emp_val = $emp_route_val_str[0];
			$route_val = $emp_route_val_str[1];
			$contents_target .= ${'emp_name' . $emp_val . $route_val} . "," . ${'area' . $emp_val . $route_val} . "," . ${'route_name' . $emp_val . $route_val} . "," . ${'route_no' . $emp_val . $route_val} . "," . ${'DTS_target' . $emp_val . $route_val} . "," . ${'DW_target' . $emp_val . $route_val} . "," . ${'Other_target' . $emp_val . $route_val} . "\n";
			$count++;
		}
		$datacontentstarget = $header_target . "\n" . $contents_target;
		if (!file_exists("../dump/$foldername")) {
			mkdir("../dump/$foldername");
			chmod("../dump/$foldername", 0777);
		}
		$fp = fopen("/home/acedns/public_html/misreport/dump/$foldername/Route Category Target Ach.csv", "wb");
		fwrite($fp, $datacontentstarget);
		fclose($fp);
		echo "$foldername/Route Category Target Ach.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'dealer_target') {
	$sql_target = "SELECT DISTINCT SCW.customer_code,CMA.customer_name,'',SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
			SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
			SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
			SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
			SCW.dec_31_achievement,	SCW.jan_prev_y_target,SCW.jan_prev_y_achievement,SCW.feb_prev_y_target,SCW.feb_prev_y_achievement,SCW.mar_prev_y_target,SCW.mar_prev_y_achievement,
			SCW.apr_prev_y_target,SCW.apr_prev_y_achievement,SCW.may_prev_y_target,SCW.may_prev_y_achievement,SCW.jun_prev_y_target,SCW.jun_prev_y_achievement,SCW.jul_prev_y_target,
			SCW.jul_prev_y_achievement,SCW.aug_prev_y_target,SCW.aug_prev_y_achievement,SCW.sep_prev_y_target,SCW.sep_prev_y_achievement,SCW.oct_prev_y_target,SCW.oct_prev_y_achievement,
			SCW.nov_prev_y_target,SCW.nov_prev_y_achievement,SCW.dec_prev_y_target,SCW.dec_prev_y_achievement	
			 FROM self_appraisal_customer_wise SCW,customer_master CMA WHERE 
			SCW.customer_code = CMA.dns_customer_code";
	$res_target = mysqli_query($link, $sql_target);
	$total_target = mysqli_num_rows($res_target);
	//exit();
	if ($total_target > 0) {
		$date = gmdate('d', strtotime('+330 minute'));
		$month = gmdate('m', strtotime('+330 minute'));
		$year = gmdate('Y', strtotime('+330 minute'));

		$header_target = "Customer Code" . "," . "Customer Name" . "," . "April" . "," . "April Achv." . "," . "April prev year" . "," . "April prev year Achv." . "," . "May" . "," . "May Achv." . "," . "May prev year" . "," . "May prev year Achv." . "," . "June" . "," . "June Achv." . "," . "June prev year." . "," . "June prev year Achv." . "," . "July" . "," . "July Achv." . "," . "July prev year" . "," . "July prev year Achv." . "," . "August" . "," . "August Achv." . "," . "August prev year" . "," . "August prev year Achv." . "," . "September" . "," . "September Achv." . "," . "September prev year" . "," . "September prev year Achv." . "," . "October" . "," . "October Achv." . "," . "October prev year" . "," . "October prev year Achv." . "," . "November" . "," . "November Achv." . "," . "November prev year" . "," . "November prev year Achv." . "," . "December" . "," . "December Achv." . "," . "December prev year" . "," . "December prev year Achv." . "," . "January" . "," . "January Achv." . "," . "January  prev year" . "," . "January prev year Achv." . "," . "February" . "," . "February Achv." . "," . "February  prev year" . "," . "February  prev year Achv." . "," . "March" . "," . "March Achv." . "," . "March prev year" . "," . "March prev year Achv.";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		$emp_route_array = array();
		$contents_target = '';
		while ($row_target = mysqli_fetch_assoc($res_target)) {
			$customer_code = $row_target['customer_code'];
			$customer_name = $row_target['customer_name'];
			$april_target = $row_target['apr_30_target'];
			$april_achievement = $row_target['apr_30_achievement'];
			$april_prev_target = $row_target['apr_prev_y_target'];
			$april_prev_achievement = $row_target['apr_prev_y_achievement'];
			$may_target = $row_target['may_31_target'];
			$may_achievement = $row_target['may_31_achievement'];
			$may_prev_target = $row_target['may_prev_y_target'];
			$may_prev_achievement = $row_target['may_prev_y_achievement'];
			$june_target = $row_target['jun_30_target'];
			$june_achievement = $row_target['jun_30_achievement'];
			$june_prev_target = $row_target['jun_prev_y_target'];
			$june_prev_achievement = $row_target['jun_prev_y_achievement'];
			$july_target = $row_target['jul_31_target'];
			$july_achievement = $row_target['jul_31_achievement'];
			$july_prev_target = $row_target['jul_prev_y_target'];
			$july_prev_achievement = $row_target['jul_prev_y_achievement'];
			$august_target = $row_target['aug_31_target'];
			$august_achievement = $row_target['aug_31_achievement'];
			$august_prev_target = $row_target['aug_prev_y_target'];
			$august_prev_achievement = $row_target['aug_prev_y_achievement'];
			$september_target = $row_target['sep_30_target'];
			$september_achievement = $row_target['sep_30_achievement'];
			$september_prev_target = $row_target['sep_prev_y_target'];
			$september_prev_achievement = $row_target['sep_prev_y_achievement'];
			$october_target = $row_target['oct_31_target'];
			$october_achievement = $row_target['oct_31_achievement'];
			$october_prev_target = $row_target['oct_prev_y_target'];
			$october_prev_achievement = $row_target['oct_prev_y_achievement'];
			$november_target = $row_target['nov_30_target'];
			$november_achievement = $row_target['nov_30_achievement'];
			$november_prev_target = $row_target['nov_prev_y_target'];
			$november_prev_achievement = $row_target['nov_prev_y_achievement'];
			$december_target = $row_target['dec_31_target'];
			$december_achievement = $row_target['dec_31_achievement'];
			$december_prev_target = $row_target['dec_prev_y_target'];
			$december_prev_achievement = $row_target['dec_prev_y_achievement'];
			$january_target = $row_target['jan_31_target'];
			$january_achievement = $row_target['jan_31_achievement'];
			$january_prev_target = $row_target['jan_prev_y_target'];
			$january_prev_achievement = $row_target['jan_prev_y_achievement'];
			$february_target = $row_target['feb_28_target'];
			$february_achievement = $row_target['feb_28_achievement'];
			$february_prev_target = $row_target['feb_prev_y_target'];
			$february_prev_achievement = $row_target['feb_prev_y_achievement'];
			$march_target = $row_target['mar_31_target'];
			$march_achievement = $row_target['mar_31_achievement'];
			$march_prev_target = $row_target['mar_prev_y_target'];
			$march_prev_achievement = $row_target['mar_prev_y_achievement'];

			$contents_target .= $customer_code . "," . $customer_name . "," . $april_target . "," . $april_achievement . "," . $april_prev_target . "," . $april_prev_achievement . "," . $may_target . "," . $may_achievement . "," . $may_prev_target . "," . $may_prev_achievement . "," . $june_target . "," . $june_achievement . "," . $june_prev_target . "," . $june_prev_achievement . "," . $july_target . "," . $july_achievement . "," . $july_prev_target . "," . $july_prev_achievement . "," . $august_target . "," . $august_achievement . "," . $august_prev_target . "," . $august_prev_achievement . "," . $september_target . "," . $september_achievement . "," . $september_prev_target . "," . $september_prev_achievement . "," . $october_target . "," . $october_achievement . "," . $october_prev_target . "," . $october_prev_achievement . "," . $november_target . "," . $november_achievement . "," . $november_prev_target . "," . $november_prev_achievement . "," . $december_target . "," . $december_achievement . "," . $december_prev_target . "," . $december_prev_achievement . "," . $january_target . "," . $january_achievement . "," . $january_prev_target . "," . $january_prev_achievement . "," . $february_target . "," . $february_achievement . "," . $february_prev_target . "," . $february_prev_achievement . "," . $march_target . "," . $march_achievement . "," . $march_prev_target . "," . $march_prev_achievement . "\n";
			$count++;
		}
		$datacontentstarget = $header_target . "\n" . $contents_target;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/Star Dealer Target.csv", "wb");
		fwrite($fp, $datacontentstarget);
		fclose($fp);
		echo "$foldername/Star Dealer Target.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}

if ($attribute == 'vendor_details') {
	$sql_vendor_list = "SELECT * FROM vendor_details";
	$res_vendor_list = mysqli_query($link, $sql_vendor_list);
	$total_vendor_list = mysqli_num_rows($res_vendor_list);
	//exit();
	$contents_vendor_list = '';
	if ($total_vendor_list > 0) {
		$header_vendor_list = "ooh_site_code" . "," . "state" . "," . "area" . "," . "city" . "," . "location" . "," . "facing" . "," . "media" . "," . "type" . "," . "L_R" . "," . "T_B" . "," . "qty" . "," . "fascia" . "," . "sq_ft" . "," . "vendor" . "," . "vendor_code" . "," . "location_facing" . "," . "is_active";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($rowvendordetails = mysqli_fetch_assoc($res_vendor_list)) {

			$ooh_site_code  = (($rowvendordetails['ooh_site_code'] != '') ? $rowvendordetails['ooh_site_code'] : '');
			$state  = '"' . (($rowvendordetails['state'] != '') ? $rowvendordetails['state'] : '') . '"';
			$area  = '"' . (($rowvendordetails['area'] != '') ? $rowvendordetails['area'] : '') . '"';
			$city  = '"' . (($rowvendordetails['city'] != '') ? $rowvendordetails['city'] : '') . '"';
			$location = '"' . (($rowvendordetails['location'] != '') ? $rowvendordetails['location'] : '') . '"';
			$facing = '"' . (($rowvendordetails['facing'] != '') ? $rowvendordetails['facing'] : '') . '"';
			$media = '"' . (($rowvendordetails['media'] != '') ? $rowvendordetails['media'] : '') . '"';
			$type = '"' . (($rowvendordetails['type'] != '') ? $rowvendordetails['type'] : '') . '"';
			$L_R = (($rowvendordetails['L_R'] != '') ? $rowvendordetails['L_R'] : '');
			$T_B = (($rowvendordetails['T_B'] != '') ? $rowvendordetails['T_B'] : '');
			$qty = (($rowvendordetails['qty'] != '') ? $rowvendordetails['qty'] : '');
			$fascia = '"' . (($rowvendordetails['fascia'] != '') ? $rowvendordetails['fascia'] : '') . '"';
			$sq_ft = (($rowvendordetails['sq_ft'] != '') ? $rowvendordetails['sq_ft'] : '');
			$vendor = '"' . (($rowvendordetails['vendor'] != '') ? $rowvendordetails['vendor'] : '') . '"';
			$vendor_code = (($rowvendordetails['vendor_code'] != '') ? $rowvendordetails['vendor_code'] : '');
			$location_facing = '"' . (($rowvendordetails['location_facing'] != '') ? $rowvendordetails['location_facing'] : '') . '"';
			$acedns = (($rowvendordetails['acedns'] != '') ? $rowvendordetails['acedns'] : '');

			$contents_vendor_list .= $ooh_site_code . "," . $state . "," . $area . "," . $city . "," . $location . "," . $facing . "," . $media . "," . $type . "," . $L_R . "," . $T_B . "," . $qty . "," . $fascia . "," . $sq_ft . "," . $vendor . "," . $vendor_code . "," . $location_facing . "," . $acedns . "\n";
			$count++;
		}
		$datacontentsvendor = $header_vendor_list . "\n" . $contents_vendor_list;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/OOH Site Master.csv", "wb");
		fwrite($fp, $datacontentsvendor);
		fclose($fp);
		echo "$foldername/OOH Site Master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'warehouse_master') {
	$sql_warehouse = "SELECT * FROM warehouse_master";
	$res_warehouse = mysqli_query($link, $sql_warehouse);
	$total_warehouse = mysqli_num_rows($res_warehouse);
	//exit();
	$contents_warehouse = '';
	if ($total_warehouse > 0) {
		$header_warehouse = "SFA employee code" . "," . "warehouse code" . "," . "Warehouse" . "," . "Diversion" . "," . "Coordinator" . "," . "Incharge" . "," . "Zone" . "," . "Godown Standard Open Time" . "," . "Godown Standard Close Time" . "," . "hours format" . "," . "Active/Inactive";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_warehouse = mysqli_fetch_assoc($res_warehouse)) {
			$emp_code = $row_warehouse['emp_code'];
			$emp_code = str_replace(',', ';', $emp_code);
			$emp_code = str_replace("'", "", $emp_code);
			$warehouse_code = $row_warehouse['warehouse_code'];
			$warehouse = $row_warehouse['warehouse'];
			$diversion = $row_warehouse['diversion'];
			$coordinator = $row_warehouse['coordinator'];
			$incharge = $row_warehouse['incharge'];
			$zone = $row_warehouse['zone'];
			$godown_open_time = $row_warehouse['godown_open_time'];
			$godown_close_time = $row_warehouse['godown_close_time'];
			$hours_format = $row_warehouse['hours_format'];
			$is_active = $row_warehouse['is_active'];


			$contents_warehouse .= $emp_code . "," . $warehouse_code . "," . $warehouse . "," . $diversion . "," . $coordinator . "," . $incharge . "," . $zone . "," . $godown_open_time . "," . $godown_close_time . "," . $hours_format . "," . $is_active . "\n";
			$count++;
		}
		$datacontentsweightage = $header_warehouse . "\n" . $contents_warehouse;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/warehouse master.csv", "wb");
		fwrite($fp, $datacontentsweightage);
		fclose($fp);
		echo "$foldername/warehouse master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
if ($attribute == 'pop_product') {
	$sql_pop_product = "SELECT * FROM pop_product_master";
	$res_pop_product = mysqli_query($link, $sql_pop_product);
	$total_pop_product = mysqli_num_rows($res_pop_product);
	//exit();
	$contents_pop_product = '';
	if ($total_pop_product > 0) {
		$header_pop_product = "Dns Prod Code" . "," . "Prod Desc" . "," . "Prod Image" . "," . "Min order qty" . "," . "Price per piece" . "," . "Gst rate" . "," . "Status" . "," . "Payment gateway " . "," . "Dealer Login" . "," . "RSSD Login";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_pop_product = mysqli_fetch_assoc($res_pop_product)) {

			$dns_prod_code = $row_pop_product['dns_prod_code'];
			$prod_desc = str_replace(',', ';', $row_pop_product['prod_desc']);
			$prod_image = $row_pop_product['prod_image'];
			$min_order_qty = $row_pop_product['min_order_qty'];
			$price_per_piece = $row_pop_product['price_per_piece'];
			$GST_rate = $row_pop_product['GST_rate'];
			$status = $row_pop_product['status'];
			$payment_gateway = $row_pop_product['payment_gateway'];
			$dealer_login = $row_pop_product['dealer_login'];
			$sp_login = $row_pop_product['sp_login'];
			$contents_pop_product .= $dns_prod_code . "," . $prod_desc . "," . $prod_image . "," . $min_order_qty . "," . $price_per_piece . "," . $GST_rate . "," . $status . "," . $payment_gateway . "," . $dealer_login . "," . $sp_login . "\n";
			$count++;
		}
		$datacontentspop_product = $header_pop_product . "\n" . $contents_pop_product;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen("$basepath$foldername/pop product master.csv", "wb");
		fwrite($fp, $datacontentspop_product);
		fclose($fp);
		echo "$foldername/pop product master.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
// var_dump($attribute);
// exit;
if ($attribute == 'branch_pop_product') {
	$sql_pop_product_br = "SELECT * FROM branch_pop_product";
	$res_pop_product_br = mysqli_query($link, $sql_pop_product_br);
	$total_pop_product_br = mysqli_num_rows($res_pop_product_br);
	//exit();
	echo $total_pop_product_br;

	if ($total_pop_product_br > 0) {
		$header_pop_product_br = "Branch Code" . "," . "Branch name" . "," . "Dns Prod Code" . "," . "Prod Desc" . "," . "Status";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while ($row_pop_product_br = mysqli_fetch_assoc($res_pop_product_br)) {
			$branch_code = $row_pop_product_br['branch_code'];
			$branch_name = str_replace(',', ';', $row_pop_product_br[branch_name]);
			$dns_prod_code = $row_pop_product_br['dns_prod_code'];
			$prod_desc = str_replace(',', ';', $row_pop_product_br[prod_desc]);
			$dns_prod_code = $row_pop_product_br['dns_prod_code'];
			$status = $row_pop_product_br['status'];
			$contents_pop_product_br .= $branch_code . "," . $branch_name . "," . $dns_prod_code . "," . $prod_desc . "," . $status . "\n";
			$count++;
		}
		$datacontentspop_product_br = $header_pop_product_br . "\n" . $contents_pop_product_br;
		$folderPath = __DIR__ . "/dump/$foldername";

		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0777, true);
			chmod($folderPath, 0777);
		}
		$fp = fopen($basepath . $foldername . "/branch pop product.csv", "wb");
		fwrite($fp, $datacontentspop_product_br);
		fclose($fp);
		echo "$foldername/branch pop product.csv";
		//header("Content-type: application/octet-stream");
		//header("Content-Disposition: attachment; filename=Order_Download_Report.xls");
		//print "$datacontents";
		//$nick_name=strtoupper($_SESSION['nick_name']);
		//echo $datacontents;
	} else {
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}
}
