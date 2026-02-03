<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_STAR");
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	//require("include/config-email-setup.php");
		
	//$date_val=date('Y-m-d', strtotime(date('Y-m-d') ." -1 day"));
	//$notaccessiblearray=array('business_prospect','collection','market_feedback','order','route_plan');
	//$notaccessiblearray=array('collection','market_feedback','order','tour_exp','route_plan');
	//$notaccessiblearray=array('self_appraisal','check_in_out');
	//$notaccessiblearray=array('joint_work','manager_activity');
	//$notaccessiblearray=array('collection','market_feedback','order','route_plan','self_appraisal','activity_report','branchwise_scheme_PDF','golden_rules','yellow_card','stk_audit','attendance','KYC','Technical Meets','Site Visit');
	//$notaccessiblearray=array('DO_status');
	//$notaccessiblearray=array('DO_status');
	//$notaccessiblearray=array('activity_report','app_order_approval','attendance','branding','branchwise_scheme_PDF','check_in_out','collection','DO_status','golden_rules','KYC','manager_activity','market_feedback','order','outstanding','outstanding_ageing','route_plan','RSP','self_appraisal','sis_report','Site Visit','stk_audit','Technical Meets','tour_exp','tour_expense','WSP','yellow_card');
	//$notaccessiblearray=array('Site Lead and Conversion Tracking','Complaint Report');

$notaccessiblearray=array('activity_report','app_order_approval','attendance','branchwise_scheme_PDF','Branding','Branding Verification','collection','Complaint Report','Corporate Branding','Counter Branding','Dhalai Services','DO_status','golden_rules','KYC','manager_activity','market_feedback','order','outstanding','outstanding_ageing','route_plan','RSP','self_appraisal','sis_report','Site Lead and Conversion Tracking','Site Visit','stk_audit','survey','Technical Meets','tour_exp','tour_expense','WSP','yellow_card');
	
	//$sqlcustomer="SELECT emp_code FROM employee_master WHERE acedns='Y' and designation!='FOS' ORDER BY emp_code ASC";
	//$sqlcustomer="SELECT emp_code FROM employee_master WHERE acedns='Y' AND (designation LIKE '%TECHNICAL%' OR designation LIKE '%LOGISTICS%') ORDER BY emp_code ASC";
	//$sqlcustomer="SELECT emp_code,emp_name FROM employee_master WHERE emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master)";
	//$sqlcustomer="select emp_code  FROM employee_master WHERE sale_access='logistics' and acedns='Y' AND emp_code!='E0970'";
	//$sqlcustomer="select emp_code FROM employee_master WHERE sale_access IN('Branding verification','Branding verification vendor') and acedns='Y'";
	$sqlcustomer="select emp_code  FROM employee_master WHERE sale_access='logistics' and acedns='Y'";
	//$sqlcustomer="SELECT * FROM `employee_master` WHERE `sale_access` NOT LIKE '%technical%' and acedns='Y'";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$emp_code=$rowcustomer['emp_code'];
			foreach($notaccessiblearray as $notaccessibleval)
			{
				echo $sqlchkexists="SELECT not_accessible_menu FROM menu_access WHERE emp_code='".$emp_code."' and not_accessible_menu='".$notaccessibleval."'";
				$rschkexists=mysqli_query($link,$sqlchkexists);
				$emp_exists=mysqli_num_rows($rschkexists);
				if($emp_exists==0)
				{
				echo $sqlinsert="INSERT INTO menu_access SET emp_code='".$emp_code."',not_accessible_menu='".$notaccessibleval."'";
				mysqli_query($link,$sqlinsert);
				}
			}
			//exit();
	}