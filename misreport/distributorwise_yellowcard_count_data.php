<?php
ob_start();
session_start();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// show only fatal errors
error_reporting(E_ERROR);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$state = $_REQUEST['state'];
$zone = $_REQUEST['zone'];
$branch = $_REQUEST['branch'];
$RSSD_filter = $_REQUEST['RSSD_filter'];
if ($_SESSION['admin_login'] == "admin" || $_SESSION['admin_login'] == 'emovesfa_do' || strtoupper($_SESSION['admin_login']) == 'ACCOUNTS') {
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$designation_condition = " WHERE designation != '' ";
} else {
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(" . $emp_hierarchy . ") ";
	$emp_hierarchy_condition_one = " AND EM.emp_code IN(" . $emp_hierarchy . ") ";
	$designation_condition = " AND designation != '' ";
}

if ($zone != '') {
	if ($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(" . $zone . ") ";
}
if ($state != '') {
	if ($state == 'all')
		$state_condition = " state!=''";
	else
		$state_condition = " state IN(" . $state . ") ";
}

$curdate = date('Y-m-d');
$date = gmdate('d', strtotime('+330 minute'));
$month = gmdate('m', strtotime('+330 minute'));
$year = gmdate('y', strtotime('+330 minute'));
$date = gmdate('d', strtotime('+330 minute'));
$month = gmdate('m', strtotime('+330 minute'));
$curryear = gmdate('Y', strtotime('+330 minute'));

$hour = gmdate('H', strtotime('+330 minute'));
$minute = gmdate('i', strtotime('+330 minute'));
$second = gmdate('s', strtotime('+330 minute'));
$curr_date = $curryear . '-' . $month . '-' . $date;

if ($month < 4) {
	$latest_f_year = $year - 1;
	$next_f_year = $year;
} else {
	$latest_f_year = $year;
	$next_f_year = $year + 1;
}
$sqlselfappraisal = "SELECT DISTINCT SCW.customer_code,SCW.jan_31_target,SCW.jan_31_achievement,SCW.feb_28_target,SCW.feb_28_achievement,
		SCW.mar_31_target,SCW.mar_31_achievement,SCW.apr_30_target,SCW.apr_30_achievement,SCW.may_31_target,SCW.may_31_achievement,
		SCW.jun_30_target,SCW.jun_30_achievement,SCW.jul_31_target,SCW.jul_31_achievement,SCW.aug_31_target,SCW.aug_31_achievement,
		SCW.sep_30_target,SCW.sep_30_achievement,SCW.oct_31_target,SCW.oct_31_achievement,SCW.nov_30_target,SCW.nov_30_achievement,SCW.dec_31_target,
		SCW.dec_31_achievement FROM self_appraisal_customer_wise SCW,customer_master CMA WHERE 
		SCW.customer_code = CMA.dns_customer_code AND CMA.cust_type='Dealer'";
$rsselfappraisal = mysqli_query($link, $sqlselfappraisal);
while ($rowselfappraisal = mysqli_fetch_assoc($rsselfappraisal)) {
	${'curr_ach_01' . $rowselfappraisal['customer_code']} = $rowselfappraisal['jan_31_achievement'];
	${'curr_ach_02' . $rowselfappraisal['customer_code']} = $rowselfappraisal['feb_28_achievement'];
	${'curr_ach_03' . $rowselfappraisal['customer_code']} = $rowselfappraisal['mar_31_target'];
	${'curr_ach_04' . $rowselfappraisal['customer_code']} = $rowselfappraisal['apr_30_achievement'];
	${'curr_ach_05' . $rowselfappraisal['customer_code']} = $rowselfappraisal['may_31_achievement'];
	${'curr_ach_06' . $rowselfappraisal['customer_code']} = $rowselfappraisal['jun_30_achievement'];
	${'curr_ach_07' . $rowselfappraisal['customer_code']} = $rowselfappraisal['jul_31_achievement'];
	${'curr_ach_08' . $rowselfappraisal['customer_code']} = $rowselfappraisal['aug_31_achievement'];
	${'curr_ach_09' . $rowselfappraisal['customer_code']} = $rowselfappraisal['sep_30_achievement'];
	${'curr_ach_10' . $rowselfappraisal['customer_code']} = $rowselfappraisal['oct_31_achievement'];
	${'curr_ach_11' . $rowselfappraisal['customer_code']} = $rowselfappraisal['nov_30_achievement'];
	${'curr_ach_12' . $rowselfappraisal['customer_code']} = $rowselfappraisal['dec_31_achievement'];

	${'grand_total_ach' . $rowselfappraisal['customer_code']} = ${'curr_ach_01' . $rowselfappraisal['customer_code']} + ${'curr_ach_02' . $rowselfappraisal['customer_code']} + ${'curr_ach_03' . $rowselfappraisal['customer_code']} + ${'curr_ach_04' . $rowselfappraisal['customer_code']} + ${'curr_ach_05' . $rowselfappraisal['customer_code']} +
		${'curr_ach_06' . $rowselfappraisal['customer_code']} + ${'curr_ach_07' . $rowselfappraisal['customer_code']} + ${'curr_ach_08' . $rowselfappraisal['customer_code']} + ${'curr_ach_09' . $rowselfappraisal['customer_code']} + ${'curr_ach_10' . $rowselfappraisal['customer_code']} + ${'curr_ach_11' . $rowselfappraisal['customer_code']} + ${'curr_ach_12' . $rowselfappraisal['customer_code']};

	//if($rowselfappraisal[])
}
$sqlsubdealerstat = "SELECT CRR.acedns,CRR.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE CRR.customer_code=CM.customer_code 
						AND CM.cust_type='Sub Dealer' ORDER BY CRR.acedns DESC";
$rssubdealerstat = mysqli_query($link, $sqlsubdealerstat);
$sub_dealer_stat_array = array();
while ($rowsubdealerstat = mysqli_fetch_assoc($rssubdealerstat)) {
	if (!in_array($rowsubdealerstat['customer_code'], $sub_dealer_stat_array)) {
		${'sub_dealer_stat' . $rowsubdealerstat['customer_code']} = $rowsubdealerstat['acedns'];
		array_push($sub_dealer_stat_array, $rowsubdealerstat['customer_code']);
	}
}
if ($RSSD_filter == 'RSSD') {
	$filter_condition = " AND CM.customer_name LIKE '%(RS%'";
} else {
	$filter_condition = " AND CM.customer_name NOT LIKE '%(RS%'";
}
// $sql_branch_yellowcard = "SELECT BM.branch_name,BM.branch_code,CM.dns_customer_code,RYC.*,CM.customer_name AS customer_name_db,
// 				CM.appointment_date AS appointment_date_db,CM.rds_tag,(SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS linked_dealer_name,
// 				(SELECT CMB.dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS dealer_dns_code
// 				 FROM branch_master BM,RSSD_yellow_card_count RYC,customer_master CM WHERE
// 					BM.branch_code=CM.branch_code AND CM.customer_code=RYC.customer_code AND  
// 		FIND_IN_SET(CM.branch_code,'".$branch."') ".$filter_condition." ORDER BY BM.branch_name,RYC.linked_dealer_name ASC,RYC.customer_name ASC";
$sql_branch_yellowcard = "
		SELECT 
    BM.branch_name,
    BM.branch_code,
    CM.dns_customer_code,
    RYC.*,
    CM.customer_name AS customer_name_db,
    CM.appointment_date AS appointment_date_db,
    CM.rds_tag,
    CMB.customer_name AS linked_dealer_name,
    CMB.dns_customer_code AS dealer_dns_code 
FROM 
    branch_master BM
JOIN 
    customer_master CM ON BM.branch_code = CM.branch_code
JOIN 
    RSSD_yellow_card_count RYC ON CM.customer_code = RYC.customer_code
JOIN 
    customer_master CMB ON CMB.customer_code = CM.rds_tag
WHERE 
    FIND_IN_SET(CM.branch_code,'$branch') 
    $filter_condition
ORDER BY 
    BM.branch_name,
    CMB.customer_name ASC,
    CM.customer_name ASC
		";
// echo	$sql_branch_yellowcard;
$res_branch_yellowcard = mysqli_query($link, $sql_branch_yellowcard);
$countbranchyellowcard = mysqli_num_rows($res_branch_yellowcard);
if ($countbranchyellowcard > 0) {
	$count = 1;
	$loopcount = 1;
?>
	<table border="1" style="border-collapse:collapse;" class="border" width="60%" align="center" id="display_table">
		<tr class="TDHEAD_SUB">
			<td colspan="25" align="center">RSSD Yellow Card Details</td>
		</tr>
		<tr class="TDHEAD" align="center">
			<td width="4%">SI</td>
			<td width="7%">Area</td>
			<td width="8%">Linked Dealer Name</td>
			<td width="4%">Linked Dealer Code</td>
			<td width="4%">SFA CODE </td>
			<td width="4%">RSSD Code</td>
			<td width="8%">Sub Dealer Name</td>
			<td width="3%">Status</td>
			<td width="5%">DOA</td>
			<td width="5%">Apr-<?php echo $latest_f_year; ?></td>
			<td width="3%">May-<?php echo $latest_f_year; ?></td>
			<td width="3%">Jun-<?php echo $latest_f_year; ?></td>
			<td width="3%">Jul-<?php echo $latest_f_year; ?></td>
			<td width="3%">Aug-<?php echo $latest_f_year; ?></td>
			<td width="3%">Sep-<?php echo $latest_f_year; ?></td>
			<td width="3%">Oct-<?php echo $latest_f_year; ?></td>
			<td width="3%">Nov-<?php echo $latest_f_year; ?></td>
			<td width="3%">Dec-<?php echo $latest_f_year; ?></td>
			<td width="3%">Jan-<?php echo ($latest_f_year + 1); ?></td>
			<td width="3%">Feb-<?php echo ($latest_f_year + 1); ?></td>
			<td width="3%">Mar-<?php echo ($latest_f_year + 1); ?></td>
			<td width="5%">Grand Total(FY<?php echo $latest_f_year . '_' . ($latest_f_year + 1) ?>)</td>
			<td width="5%">Avg</td>
			<td width="5%">(FY<?php echo ($latest_f_year - 1) . '_' . ($latest_f_year) ?>)</td>
		</tr>
		<?php
		//For Sub Dealer Total Avg
		$souce_month_date_subd = $curryear . '-04-01';
		//$souce_month_date= '2020-04-29';
		$dest_month_date_subd = $curr_date;
		//$dest_month_date= '2020-08-01';
		$ts1subd = strtotime($souce_month_date_subd);
		$ts2subd = strtotime($dest_month_date_subd);
		$year1subd = date('Y', $ts1subd);
		$year2subd = date('Y', $ts2subd);
		$month1subd = date('m', $ts1subd);
		$month2subd = date('m', $ts2subd);
		$monthdiffsubd = (($year2subd - $year1subd) * 12) + ($month2subd - $month1subd);
		//For Dealer Total and Branch Avg
		$souce_month_date_d = $curryear . '-04-01';
		//$souce_month_date= '2020-04-29';
		$dest_month_date_d = $curr_date;
		//$dest_month_date= '2020-08-01';
		$ts1d = strtotime($souce_month_date_d);
		$ts2d = strtotime($dest_month_date_d);
		$year1d = date('Y', $ts1d);
		$year2d = date('Y', $ts2d);
		$month1d = date('m', $ts1d);
		$month2d = date('m', $ts2d);
		$monthdiffd = (($year2d - $year1d) * 12) + ($month2d - $month1d);
		$monthdiffd = $monthdiffd + 1;
		while ($rowbranchyellowcard = mysqli_fetch_assoc($res_branch_yellowcard)) {
			$branch_name = $rowbranchyellowcard['branch_name'];
			$branch_code = $rowbranchyellowcard['branch_code'];
			$subdealer_customer_code = $rowbranchyellowcard['dns_customer_code'];
			$sfa_customer_code = $rowbranchyellowcard['customer_code'];
			$subdealer_name = $rowbranchyellowcard['customer_name_db'];
			$RSSD_code = strstr($subdealer_name, '(RS');
			$rds_tag = $rowbranchyellowcard['rds_tag'];
			/*$sql_dealer="SELECT customer_code,dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$rds_tag."'";
			$rs_dealer=mysqli_query($link,$sql_dealer);
			$row_dealer=mysqli_fetch_assoc($rs_dealer);
			$linked_dealer_code=$row_dealer['customer_code'];*/
			$linked_dealer_name = $rowbranchyellowcard['linked_dealer_name'];
			//$linked_dealer_name = $rowbranchyellowcard['linked_dealer_name'];
			//$linked_dealer_code = $rowbranchyellowcard['linked_dealer_code'];
			$appointment_date_fromat = $rowbranchyellowcard['appointment_date_db'];
			$appointment_date = date('d-m-Y', strtotime($rowbranchyellowcard['appointment_date_db']));

			$prev_01 = $rowbranchyellowcard['prev_01'] / 20;
			$prev_02 = $rowbranchyellowcard['prev_02'] / 20;
			$prev_03 = $rowbranchyellowcard['prev_03'] / 20;
			$prev_04 = $rowbranchyellowcard['prev_04'] / 20;
			$prev_05 = $rowbranchyellowcard['prev_05'] / 20;
			$prev_06 = $rowbranchyellowcard['prev_06'] / 20;
			$prev_07 = $rowbranchyellowcard['prev_07'] / 20;
			$prev_08 = $rowbranchyellowcard['prev_08'] / 20;
			$prev_09 = $rowbranchyellowcard['prev_09'] / 20;
			$prev_10 = $rowbranchyellowcard['prev_10'] / 20;
			$prev_11 = $rowbranchyellowcard['prev_11'] / 20;
			$prev_12 = $rowbranchyellowcard['prev_12'] / 20;
			$curr_01 = $rowbranchyellowcard['curr_01'] / 20;
			$curr_02 = $rowbranchyellowcard['curr_02'] / 20;
			$curr_03 = $rowbranchyellowcard['curr_03'] / 20;
			$curr_04 = $rowbranchyellowcard['curr_04'] / 20;
			$curr_05 = $rowbranchyellowcard['curr_05'] / 20;
			$curr_06 = $rowbranchyellowcard['curr_06'] / 20;
			$curr_07 = $rowbranchyellowcard['curr_07'] / 20;
			$curr_08 = $rowbranchyellowcard['curr_08'] / 20;
			$curr_09 = $rowbranchyellowcard['curr_09'] / 20;
			$curr_10 = $rowbranchyellowcard['curr_10'] / 20;
			$curr_11 = $rowbranchyellowcard['curr_11'] / 20;
			$curr_12 = $rowbranchyellowcard['curr_12'] / 20;
			$monthdiff = 0;
			$grand_total_crr = $curr_01 + $curr_02 + $curr_03 + $curr_04 + $curr_05 + $curr_06 + $curr_07 + $curr_08 + $curr_09 + $curr_10 + $curr_11 + $curr_12;
			$grand_total_prev = $prev_01 + $prev_02 + $prev_03 + $prev_04 + $prev_05 + $prev_06 + $prev_07 + $prev_08 + $prev_09 + $prev_10 + $prev_11 + $prev_12;
			if ($appointment_date_fromat != '0000-00-00') {
				/*if($curr_01 > 0 ) $avg_total=$avg_total+1;
			if($curr_02 > 0 ) $avg_total=$avg_total+1;
			if($curr_03 > 0) $avg_total=$avg_total+1;
			if($curr_04 > 0) $avg_total=$avg_total+1;
			if($curr_05 > 0) $avg_total=$avg_total+1;
			if($curr_06 > 0) $avg_total=$avg_total+1;
			if($curr_07 > 0 ) $avg_total=$avg_total+1;
			if($curr_08 > 0 ) $avg_total=$avg_total+1;
			if($curr_09 > 0 ) $avg_total=$avg_total+1;
			if($curr_10 > 0) $avg_total=$avg_total+1;
			if($curr_11 > 0 ) $avg_total=$avg_total+1;
			if($curr_12 > 0 ) $avg_total=$avg_total+1;*/
				if (strtotime($appointment_date_fromat) > strtotime($curryear . '-04-01')) {
					$souce_month_date = $appointment_date_fromat;
				} else {
					$souce_month_date = $curryear . '-04-01';
				}
				//$souce_month_date= '2020-04-29';
				$dest_month_date = $curr_date;
				//$dest_month_date= '2020-08-01';
				$ts1 = strtotime($souce_month_date);
				$ts2 = strtotime($dest_month_date);
				$year1 = date('Y', $ts1);
				$year2 = date('Y', $ts2);
				$month1 = date('m', $ts1);
				$month2 = date('m', $ts2);
				$monthdiff = (($year2 - $year1) * 12) + ($month2 - $month1);

				//$monthdiff =$monthdiff ;
			}

			//$grand_total_avg=ceil($grand_total_crr/12);
			//$grand_total_avg=$grand_total_crr/$avg_total;
			// check for DivisionByZeroError
			if ($monthdiff == 0) {
				$grand_total_avg = 0;
			} else {
				$grand_total_avg = $grand_total_crr / $monthdiff;
			}
			// $grand_total_avg = $grand_total_crr / $monthdiff;
			/*$sqllinkdealercode="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$linked_dealer_code."'";
			$rslinkdealercode=mysqli_query($link,$sqllinkdealercode);
			$rowlinkdealercode=mysqli_fetch_assoc($rslinkdealercode);*/
			$dealer_dns_code = $rowbranchyellowcard['dealer_dns_code'];

			/*if($curr_01 >0 || $curr_02 >0 || $curr_03 >0 ||$curr_04 >0 ||$curr_05 >0 ||$curr_06 >0 || $curr_07 >0 ||$curr_08 >0 || $curr_09 >0 ||$curr_10 >0 
			|| $curr_11 >0 || $curr_12 >0 ){*/
			${'curr_01' . $dealer_dns_code} = ${'curr_01' . $dealer_dns_code} + $curr_01;
			${'curr_02' . $dealer_dns_code} = ${'curr_02' . $dealer_dns_code} + $curr_02;
			${'curr_03' . $dealer_dns_code} = ${'curr_03' . $dealer_dns_code} + $curr_03;
			${'curr_04' . $dealer_dns_code} = ${'curr_04' . $dealer_dns_code} + $curr_04;
			${'curr_05' . $dealer_dns_code} = ${'curr_05' . $dealer_dns_code} + $curr_05;
			${'curr_06' . $dealer_dns_code} = ${'curr_06' . $dealer_dns_code} + $curr_06;
			${'curr_07' . $dealer_dns_code} = ${'curr_07' . $dealer_dns_code} + $curr_07;
			${'curr_08' . $dealer_dns_code} = ${'curr_08' . $dealer_dns_code} + $curr_08;
			${'curr_09' . $dealer_dns_code} = ${'curr_09' . $dealer_dns_code} + $curr_09;
			${'curr_10' . $dealer_dns_code} = ${'curr_10' . $dealer_dns_code} + $curr_10;
			${'curr_11' . $dealer_dns_code} = ${'curr_11' . $dealer_dns_code} + $curr_11;
			${'curr_12' . $dealer_dns_code} = ${'curr_12' . $dealer_dns_code} + $curr_12;
			if ($count > 1 && $previous_dealer_code != $dealer_dns_code) {
				${'grand_total_curr' . $previous_dealer_code} = ${'curr_01' . $previous_dealer_code} + ${'curr_02' . $previous_dealer_code} + ${'curr_03' . $previous_dealer_code} + ${'curr_04' . $previous_dealer_code} + ${'curr_05' . $previous_dealer_code} + ${'curr_06' . $previous_dealer_code} + ${'curr_07' . $previous_dealer_code} + ${'curr_08' . $previous_dealer_code} + ${'curr_09' . $previous_dealer_code} + ${'curr_10' . $previous_dealer_code} + ${'curr_11' . $previous_dealer_code} + ${'curr_12' . $previous_dealer_code};
				// check for DivisionByZeroError
				if ($monthdiffd == 0) {
					${'grand_total_avg' . $previous_dealer_code} = 0;
				} else {
					${'grand_total_avg' . $previous_dealer_code} = ${'grand_total_curr' . $previous_dealer_code} / $monthdiffd;
				}
				// ${'grand_total_avg' . $previous_dealer_code} = ${'grand_total_curr' . $previous_dealer_code} / $monthdiffsubd;

				${'curr_01' . $previous_branch_code} = ${'curr_01' . $previous_branch_code} + ${'curr_01' . $previous_dealer_code};
				${'curr_02' . $previous_branch_code} = ${'curr_02' . $previous_branch_code} + ${'curr_02' . $previous_dealer_code};
				${'curr_03' . $previous_branch_code} = ${'curr_03' . $previous_branch_code} + ${'curr_03' . $previous_dealer_code};
				${'curr_04' . $previous_branch_code} = ${'curr_04' . $previous_branch_code} + ${'curr_04' . $previous_dealer_code};
				${'curr_05' . $previous_branch_code} = ${'curr_05' . $previous_branch_code} + ${'curr_05' . $previous_dealer_code};
				${'curr_06' . $previous_branch_code} = ${'curr_06' . $previous_branch_code} + ${'curr_06' . $previous_dealer_code};
				${'curr_07' . $previous_branch_code} = ${'curr_07' . $previous_branch_code} + ${'curr_07' . $previous_dealer_code};
				${'curr_08' . $previous_branch_code} = ${'curr_08' . $previous_branch_code} + ${'curr_08' . $previous_dealer_code};
				${'curr_09' . $previous_branch_code} = ${'curr_09' . $previous_branch_code} + ${'curr_09' . $previous_dealer_code};
				${'curr_10' . $previous_branch_code} = ${'curr_10' . $previous_branch_code} + ${'curr_10' . $previous_dealer_code};
				${'curr_11' . $previous_branch_code} = ${'curr_11' . $previous_branch_code} + ${'curr_11' . $previous_dealer_code};
				${'curr_12' . $previous_branch_code} = ${'curr_12' . $previous_branch_code} + ${'curr_12' . $previous_dealer_code};
				${'grand_total_curr' . $previous_branch_code} = ${'grand_total_curr' . $previous_branch_code} + ${'grand_total_curr' . $previous_dealer_code};

				${'curr_ach_01' . $previous_branch_code} = ${'curr_ach_01' . $previous_branch_code} + ${'curr_ach_01' . $previous_dealer_code};
				${'curr_ach_02' . $previous_branch_code} = ${'curr_ach_02' . $previous_branch_code} + ${'curr_ach_02' . $previous_dealer_code};
				${'curr_ach_03' . $previous_branch_code} = ${'curr_ach_03' . $previous_branch_code} + ${'curr_ach_03' . $previous_dealer_code};
				${'curr_ach_04' . $previous_branch_code} = ${'curr_ach_04' . $previous_branch_code} + ${'curr_ach_04' . $previous_dealer_code};
				${'curr_ach_05' . $previous_branch_code} = ${'curr_ach_05' . $previous_branch_code} + ${'curr_ach_05' . $previous_dealer_code};
				${'curr_ach_06' . $previous_branch_code} = ${'curr_ach_06' . $previous_branch_code} + ${'curr_ach_06' . $previous_dealer_code};
				${'curr_ach_07' . $previous_branch_code} = ${'curr_ach_07' . $previous_branch_code} + ${'curr_ach_07' . $previous_dealer_code};
				${'curr_ach_08' . $previous_branch_code} = ${'curr_ach_08' . $previous_branch_code} + ${'curr_ach_08' . $previous_dealer_code};
				${'curr_ach_09' . $previous_branch_code} = ${'curr_ach_09' . $previous_branch_code} + ${'curr_ach_09' . $previous_dealer_code};
				${'curr_ach_10' . $previous_branch_code} = ${'curr_ach_10' . $previous_branch_code} + ${'curr_ach_10' . $previous_dealer_code};
				${'curr_ach_11' . $previous_branch_code} = ${'curr_ach_11' . $previous_branch_code} + ${'curr_ach_11' . $previous_dealer_code};
				${'curr_ach_12' . $previous_branch_code} = ${'curr_ach_12' . $previous_branch_code} + ${'curr_ach_12' . $previous_dealer_code};

				${'grand_total_curr_ach' . $previous_branch_code} = ${'grand_total_curr_ach' . $previous_branch_code} + ${'grand_total_ach' . $previous_dealer_code};

				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>SUB-DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_04' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_05' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_06' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_07' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_08' . $previous_dealer_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_09' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_10' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_11' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_12' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_01' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_02' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_03' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . round(${'grand_total_avg' . $previous_dealer_code}, 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>$previous_dealer_name</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_ach_04' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_05' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_06' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_07' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_08' . $previous_dealer_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_ach_09' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_10' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_11' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_12' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_01' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_02' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_03' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_ach' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . round((${'grand_total_ach' . $previous_dealer_code} / $monthdiffd), 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				//   check for DivisionByZeroError
				${'percent_04' . $previous_dealer_code} = 0;
				${'percent_05' . $previous_dealer_code} = 0;
				${'percent_06' . $previous_dealer_code} = 0;
				${'percent_07' . $previous_dealer_code} = 0;
				${'percent_08' . $previous_dealer_code} = 0;
				${'percent_09' . $previous_dealer_code} = 0;
				${'percent_10' . $previous_dealer_code} = 0;
				${'percent_11' . $previous_dealer_code} = 0;
				${'percent_12' . $previous_dealer_code} = 0;
				${'percent_01' . $previous_dealer_code} = 0;
				${'percent_02' . $previous_dealer_code} = 0;
				${'percent_03' . $previous_dealer_code} = 0;
				${'percent_total' . $previous_dealer_code} = 0;
				// echo "Error: ". ${'curr_04' . $previous_dealer_code} . " - " . ${'curr_ach_04' . $previous_dealer_code} . "-" . (${'curr_04' . $previous_dealer_code} > 0) . "<br>";
				if (${'curr_ach_04' . $previous_dealer_code} > 0) ${'percent_04' . $previous_dealer_code} = round(((${'curr_04' . $previous_dealer_code} / ${'curr_ach_04' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_05' . $previous_dealer_code} > 0) ${'percent_05' . $previous_dealer_code} = round(((${'curr_05' . $previous_dealer_code} / ${'curr_ach_05' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_06' . $previous_dealer_code} > 0) ${'percent_06' . $previous_dealer_code} = round(((${'curr_06' . $previous_dealer_code} / ${'curr_ach_06' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_07' . $previous_dealer_code} > 0) ${'percent_07' . $previous_dealer_code} = round(((${'curr_07' . $previous_dealer_code} / ${'curr_ach_07' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_08' . $previous_dealer_code} > 0) ${'percent_08' . $previous_dealer_code} = round(((${'curr_08' . $previous_dealer_code} / ${'curr_ach_08' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_09' . $previous_dealer_code} > 0) ${'percent_09' . $previous_dealer_code} = round(((${'curr_09' . $previous_dealer_code} / ${'curr_ach_09' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_10' . $previous_dealer_code} > 0) ${'percent_10' . $previous_dealer_code} = round(((${'curr_10' . $previous_dealer_code} / ${'curr_ach_10' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_11' . $previous_dealer_code} > 0) ${'percent_11' . $previous_dealer_code} = round(((${'curr_11' . $previous_dealer_code} / ${'curr_ach_11' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_12' . $previous_dealer_code} > 0) ${'percent_12' . $previous_dealer_code} = round(((${'curr_12' . $previous_dealer_code} / ${'curr_ach_12' . $previous_dealer_code}) * 100), 2);
				if (${'grand_total_curr_ach' . $previous_dealer_code} > 0) ${'percent_total' . $previous_dealer_code} = round(((${'grand_total_curr' . $previous_dealer_code} / ${'grand_total_curr_ach' . $previous_dealer_code}) * 100), 2);

				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>% CONTRIBUTION</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'percent_04' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_05' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_06' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_07' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_08' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_09' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_10' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_11' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_12' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_01' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_02' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_03' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_total' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b></b></td>
					<td align='right'></td>
				  </tr>";
				// echo "<tr>
				// 	<td ></td>
				// 	<td >" . $previous_branch_name . "</td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ><b>% CONTRIBUTION</b></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td align='right'><b>" . round(((${'curr_04' . $previous_dealer_code} / ${'curr_ach_04' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_05' . $previous_dealer_code} / ${'curr_ach_05' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_06' . $previous_dealer_code} / ${'curr_ach_06' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_07' . $previous_dealer_code} / ${'curr_ach_07' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_08' . $previous_dealer_code} / ${'curr_ach_08' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_09' . $previous_dealer_code} / ${'curr_ach_09' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_10' . $previous_dealer_code} / ${'curr_ach_10' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_11' . $previous_dealer_code} / ${'curr_ach_11' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_12' . $previous_dealer_code} / ${'curr_ach_12' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_01' . $previous_dealer_code} / ${'curr_ach_01' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_02' . $previous_dealer_code} / ${'curr_ach_02' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_03' . $previous_dealer_code} / ${'curr_ach_03' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'grand_total_curr' . $previous_dealer_code} / ${'grand_total_ach' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b></b></td>
				// 	<td align='right'></td>
				//   </tr>";
			}
			if ($count > 1 && $previous_branch_code != $branch_code) {
				//For Branch Total Avg
				/*${'avg_total_branch'.$previous_branch_code}=0;
					if(${'curr_01'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_02'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_03'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_04'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_05'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_06'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_07'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_08'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_09'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_10'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_11'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;
					if(${'curr_12'.$previous_branch_code} > 0) ${'avg_total_branch'.$previous_branch_code}=${'avg_total'.$previous_branch_code}+1;*/

				${'grand_total_avg' . $previous_branch_code} = ${'grand_total_curr' . $previous_branch_code} / $monthdiffd;
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>$previous_branch_name SUB-DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_04' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_05' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_06' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_07' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_08' . $previous_branch_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_09' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_10' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_11' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_12' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_01' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_02' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_03' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . round(${'grand_total_avg' . $previous_branch_code}, 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_ach_04' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_05' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_06' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_07' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_08' . $previous_branch_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_ach_09' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_10' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_11' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_12' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_01' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_02' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_03' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr_ach' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . round((${'grand_total_curr_ach' . $previous_branch_code} / $monthdiffd), 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				//   check for DivisionByZeroError
				${'percent_04' . $previous_branch_code} = 0;
				${'percent_05' . $previous_branch_code} = 0;
				${'percent_06' . $previous_branch_code} = 0;
				${'percent_07' . $previous_branch_code} = 0;
				${'percent_08' . $previous_branch_code} = 0;
				${'percent_09' . $previous_branch_code} = 0;
				${'percent_10' . $previous_branch_code} = 0;
				${'percent_11' . $previous_branch_code} = 0;
				${'percent_12' . $previous_branch_code} = 0;
				${'percent_01' . $previous_branch_code} = 0;
				${'percent_02' . $previous_branch_code} = 0;
				${'percent_03' . $previous_branch_code} = 0;
				${'percent_total' . $previous_branch_code} = 0;
				if (${'curr_ach_04' . $previous_branch_code} > 0) ${'percent_04' . $previous_branch_code} = round(((${'curr_04' . $previous_branch_code} / ${'curr_ach_04' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_05' . $previous_branch_code} > 0) ${'percent_05' . $previous_branch_code} = round(((${'curr_05' . $previous_branch_code} / ${'curr_ach_05' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_06' . $previous_branch_code} > 0) ${'percent_06' . $previous_branch_code} = round(((${'curr_06' . $previous_branch_code} / ${'curr_ach_06' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_07' . $previous_branch_code} > 0) ${'percent_07' . $previous_branch_code} = round(((${'curr_07' . $previous_branch_code} / ${'curr_ach_07' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_08' . $previous_branch_code} > 0) ${'percent_08' . $previous_branch_code} = round(((${'curr_08' . $previous_branch_code} / ${'curr_ach_08' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_09' . $previous_branch_code} > 0) ${'percent_09' . $previous_branch_code} = round(((${'curr_09' . $previous_branch_code} / ${'curr_ach_09' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_10' . $previous_branch_code} > 0) ${'percent_10' . $previous_branch_code} = round(((${'curr_10' . $previous_branch_code} / ${'curr_ach_10' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_11' . $previous_branch_code} > 0) ${'percent_11' . $previous_branch_code} = round(((${'curr_11' . $previous_branch_code} / ${'curr_ach_11' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_12' . $previous_branch_code} > 0) ${'percent_12' . $previous_branch_code} = round(((${'curr_12' . $previous_branch_code} / ${'curr_ach_12' . $previous_branch_code}) * 100), 2);
				if (${'grand_total_curr_ach' . $previous_branch_code} > 0) ${'percent_total' . $previous_branch_code} = round(((${'grand_total_curr' . $previous_branch_code} / ${'grand_total_curr_ach' . $previous_branch_code}) * 100), 2);

				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>% CONTRIBUTION</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'percent_04' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_05' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_06' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_07' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_08' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_09' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_10' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_11' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_12' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_01' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_02' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_03' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_total' . $previous_branch_code} . "%</b></td>
					<td align='right'><b></b></td>
					<td align='right'></td>
				  </tr>";
				// echo "<tr>
				// 	<td ></td>
				// 	<td >" . $previous_branch_name . "</td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ><b>% CONTRIBUTION</b></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td align='right'><b>" . round(((${'curr_04' . $previous_branch_code} / ${'curr_ach_04' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_05' . $previous_branch_code} / ${'curr_ach_05' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_06' . $previous_branch_code} / ${'curr_ach_06' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_07' . $previous_branch_code} / ${'curr_ach_07' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_08' . $previous_branch_code} / ${'curr_ach_08' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_09' . $previous_branch_code} / ${'curr_ach_09' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_10' . $previous_branch_code} / ${'curr_ach_10' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_11' . $previous_branch_code} / ${'curr_ach_11' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_12' . $previous_branch_code} / ${'curr_ach_12' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_01' . $previous_branch_code} / ${'curr_ach_01' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_02' . $previous_branch_code} / ${'curr_ach_02' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_03' . $previous_branch_code} / ${'curr_ach_03' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'grand_total_curr' . $previous_branch_code} / ${'grand_total_curr_ach' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b></b></td>
				// 	<td align='right'></td>
				//   </tr>";
			}
			echo "<tr>
					<td >" . $count . "</td>
					<td >" . $branch_name . "</td>
					<td >" . $linked_dealer_name . "</td>
					<td >" . $dealer_dns_code . "</td>
					<td >" . $subdealer_customer_code . "</td>
					<td >" . $RSSD_code . "</td>
					<td >" . $subdealer_name . "</td>
					<td >" . ${'sub_dealer_stat' . $sfa_customer_code} . "</td>
					<td >" . $appointment_date . "</td>
					<td align='right'>" . $curr_04 . "</td>
					<td align='right'>" . $curr_05 . "</td>
					<td align='right'>" . $curr_06 . "</td>
					<td align='right'>" . $curr_07 . "</td>
					<td align='right'>" . $curr_08 . "</td>
					<td align='right' >" . $curr_09 . "</td>
					<td align='right'>" . $curr_10 . "</td>
					<td align='right'>" . $curr_11 . "</td>
					<td align='right'>" . $curr_12 . "</td>
					<td align='right'>" . $curr_01 . "</td>
					<td align='right'>" . $curr_02 . "</td>
					<td align='right'>" . $curr_03 . "</td>
					<td align='right'>" . $grand_total_crr . "</td>
					<td align='right'>" . round($grand_total_avg, 2) . "</td>
					<td align='right'>" . $grand_total_prev . "</td>
				  </tr>";
			if ($countbranchyellowcard == $loopcount) {
				${'grand_total_curr' . $previous_dealer_code} = ${'curr_01' . $previous_dealer_code} + ${'curr_02' . $previous_dealer_code} + ${'curr_03' . $previous_dealer_code} + ${'curr_04' . $previous_dealer_code} + ${'curr_05' . $previous_dealer_code} + ${'curr_06' . $previous_dealer_code} + ${'curr_07' . $previous_dealer_code} + ${'curr_08' . $previous_dealer_code} + ${'curr_09' . $previous_dealer_code} + ${'curr_10' . $previous_dealer_code} + ${'curr_11' . $previous_dealer_code} + ${'curr_12' . $previous_dealer_code};
				${'grand_total_avg' . $previous_dealer_code} = ceil(${'grand_total_curr' . $previous_dealer_code} / 12);

				// echo 'ggkkll'.${'curr_05'.'B0044'};
				//echo '<br />';
				// echo 'ggkkdd'.${'curr_05'.'A020'};
				/* ${'curr_01'.$branch_code}=0; 
					  ${'curr_02'.$branch_code}=0; 
					  ${'curr_03'.$branch_code}=0;
					  ${'curr_04'.$branch_code}=0;
					  ${'curr_05'.$branch_code}=0; 
					  ${'curr_06'.$branch_code}=0; 
				      ${'curr_07'.$branch_code}=0; 
					  ${'curr_08'.$branch_code}=0; 
					  ${'curr_09'.$branch_code}=0; 
					  ${'curr_10'.$branch_code}=0; 
					  ${'curr_11'.$branch_code}=0; 
					  ${'curr_12'.$branch_code}=0;   
						${'grand_total_curr'.$branch_code}=0;	*/
				//For Sub dealer Total Avg
				// check for DivisionByZeroError
				${'grand_total_avg' . $previous_dealer_code} = 0;
				if ($monthdiffsubd > 0) {
					${'grand_total_avg' . $previous_dealer_code} = ${'grand_total_curr' . $previous_dealer_code} / $monthdiffsubd;
				}


				${'curr_01' . $branch_code} = ${'curr_01' . $branch_code} + ${'curr_01' . $previous_dealer_code};
				${'curr_02' . $branch_code} = ${'curr_02' . $branch_code} + ${'curr_02' . $previous_dealer_code};
				${'curr_03' . $branch_code} = ${'curr_03' . $branch_code} + ${'curr_03' . $previous_dealer_code};
				${'curr_04' . $branch_code} = ${'curr_04' . $branch_code} + ${'curr_04' . $previous_dealer_code};
				${'curr_05' . $branch_code} = ${'curr_05' . $branch_code} + ${'curr_05' . $previous_dealer_code};
				${'curr_06' . $branch_code} = ${'curr_06' . $branch_code} + ${'curr_06' . $previous_dealer_code};
				${'curr_07' . $branch_code} = ${'curr_07' . $branch_code} + ${'curr_07' . $previous_dealer_code};
				${'curr_08' . $branch_code} = ${'curr_08' . $branch_code} + ${'curr_08' . $previous_dealer_code};
				${'curr_09' . $branch_code} = ${'curr_09' . $branch_code} + ${'curr_09' . $previous_dealer_code};
				${'curr_10' . $branch_code} = ${'curr_10' . $branch_code} + ${'curr_10' . $previous_dealer_code};
				${'curr_11' . $branch_code} = ${'curr_11' . $branch_code} + ${'curr_11' . $previous_dealer_code};
				${'curr_12' . $branch_code} = ${'curr_12' . $branch_code} + ${'curr_12' . $previous_dealer_code};
				${'grand_total_curr' . $branch_code} = ${'grand_total_curr' . $branch_code} + ${'grand_total_curr' . $previous_dealer_code};

				${'curr_ach_01' . $previous_branch_code} = ${'curr_ach_01' . $previous_branch_code} + ${'curr_ach_01' . $previous_dealer_code};
				${'curr_ach_02' . $previous_branch_code} = ${'curr_ach_02' . $previous_branch_code} + ${'curr_ach_02' . $previous_dealer_code};
				${'curr_ach_03' . $previous_branch_code} = ${'curr_ach_03' . $previous_branch_code} + ${'curr_ach_03' . $previous_dealer_code};
				${'curr_ach_04' . $previous_branch_code} = ${'curr_ach_04' . $previous_branch_code} + ${'curr_ach_04' . $previous_dealer_code};
				${'curr_ach_05' . $previous_branch_code} = ${'curr_ach_05' . $previous_branch_code} + ${'curr_ach_05' . $previous_dealer_code};
				${'curr_ach_06' . $previous_branch_code} = ${'curr_ach_06' . $previous_branch_code} + ${'curr_ach_06' . $previous_dealer_code};
				${'curr_ach_07' . $previous_branch_code} = ${'curr_ach_07' . $previous_branch_code} + ${'curr_ach_07' . $previous_dealer_code};
				${'curr_ach_08' . $previous_branch_code} = ${'curr_ach_08' . $previous_branch_code} + ${'curr_ach_08' . $previous_dealer_code};
				${'curr_ach_09' . $previous_branch_code} = ${'curr_ach_09' . $previous_branch_code} + ${'curr_ach_09' . $previous_dealer_code};
				${'curr_ach_10' . $previous_branch_code} = ${'curr_ach_10' . $previous_branch_code} + ${'curr_ach_10' . $previous_dealer_code};
				${'curr_ach_11' . $previous_branch_code} = ${'curr_ach_11' . $previous_branch_code} + ${'curr_ach_11' . $previous_dealer_code};
				${'curr_ach_12' . $previous_branch_code} = ${'curr_ach_12' . $previous_branch_code} + ${'curr_ach_12' . $previous_dealer_code};

				${'grand_total_curr_ach' . $previous_branch_code} = ${'grand_total_curr_ach' . $previous_branch_code} + ${'grand_total_ach' . $previous_dealer_code};



				//echo 'ggkkll'.${'curr_05'.'B0044'};


				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>SUB-DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_04' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_05' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_06' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_07' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_08' . $previous_dealer_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_09' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_10' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_11' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_12' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_01' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_02' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_03' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . round(${'grand_total_avg' . $previous_dealer_code}, 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>$previous_dealer_name</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_ach_04' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_05' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_06' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_07' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_08' . $previous_dealer_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_ach_09' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_10' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_11' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_12' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_01' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_02' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_03' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_ach' . $previous_dealer_code} . "</b></td>
					<td align='right'><b>" . round((${'grand_total_ach' . $previous_dealer_code} / $monthdiffd), 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				//   check for DivisionByZeroError
				${'percent_04' . $previous_dealer_code} = 0;
				${'percent_05' . $previous_dealer_code} = 0;
				${'percent_06' . $previous_dealer_code} = 0;
				${'percent_07' . $previous_dealer_code} = 0;
				${'percent_08' . $previous_dealer_code} = 0;
				${'percent_09' . $previous_dealer_code} = 0;
				${'percent_10' . $previous_dealer_code} = 0;
				${'percent_11' . $previous_dealer_code} = 0;
				${'percent_12' . $previous_dealer_code} = 0;
				${'percent_01' . $previous_dealer_code} = 0;
				${'percent_02' . $previous_dealer_code} = 0;
				${'percent_03' . $previous_dealer_code} = 0;
				${'percent_total' . $previous_dealer_code} = 0;
				if (${'curr_ach_04' . $previous_dealer_code} > 0) ${'percent_04' . $previous_dealer_code} = round(((${'curr_04' . $previous_dealer_code} / ${'curr_ach_04' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_05' . $previous_dealer_code} > 0) ${'percent_05' . $previous_dealer_code} = round(((${'curr_05' . $previous_dealer_code} / ${'curr_ach_05' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_06' . $previous_dealer_code} > 0) ${'percent_06' . $previous_dealer_code} = round(((${'curr_06' . $previous_dealer_code} / ${'curr_ach_06' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_07' . $previous_dealer_code} > 0) ${'percent_07' . $previous_dealer_code} = round(((${'curr_07' . $previous_dealer_code} / ${'curr_ach_07' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_08' . $previous_dealer_code} > 0) ${'percent_08' . $previous_dealer_code} = round(((${'curr_08' . $previous_dealer_code} / ${'curr_ach_08' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_09' . $previous_dealer_code} > 0) ${'percent_09' . $previous_dealer_code} = round(((${'curr_09' . $previous_dealer_code} / ${'curr_ach_09' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_10' . $previous_dealer_code} > 0) ${'percent_10' . $previous_dealer_code} = round(((${'curr_10' . $previous_dealer_code} / ${'curr_ach_10' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_11' . $previous_dealer_code} > 0) ${'percent_11' . $previous_dealer_code} = round(((${'curr_11' . $previous_dealer_code} / ${'curr_ach_11' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_12' . $previous_dealer_code} > 0) ${'percent_12' . $previous_dealer_code} = round(((${'curr_12' . $previous_dealer_code} / ${'curr_ach_12' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_01' . $previous_dealer_code} > 0) ${'percent_01' . $previous_dealer_code} = round(((${'curr_01' . $previous_dealer_code} / ${'curr_ach_01' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_02' . $previous_dealer_code} > 0) ${'percent_02' . $previous_dealer_code} = round(((${'curr_02' . $previous_dealer_code} / ${'curr_ach_02' . $previous_dealer_code}) * 100), 2);
				if (${'curr_ach_03' . $previous_dealer_code} > 0) ${'percent_03' . $previous_dealer_code} = round(((${'curr_03' . $previous_dealer_code} / ${'curr_ach_03' . $previous_dealer_code}) * 100), 2);
				if (${'grand_total_curr_ach' . $previous_dealer_code} > 0) ${'percent_total' . $previous_dealer_code} = round(((${'grand_total_curr' . $previous_dealer_code} / ${'grand_total_curr_ach' . $previous_dealer_code}) * 100), 2);
				
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>% CONTRIBUTION</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'percent_04' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_05' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_06' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_07' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_08' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_09' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_10' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_11' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_12' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_01' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_02' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_03' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_total' . $previous_dealer_code} . "%</b></td>
					<td align='right'><b></b></td>
					<td align='right'></td>
				  </tr>";
				// echo "<tr>
				// 	<td ></td>
				// 	<td >" . $previous_branch_name . "</td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ><b>% CONTRIBUTION</b></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td align='right'><b>" . round(((${'curr_04' . $previous_dealer_code} / ${'curr_ach_04' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_05' . $previous_dealer_code} / ${'curr_ach_05' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_06' . $previous_dealer_code} / ${'curr_ach_06' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_07' . $previous_dealer_code} / ${'curr_ach_07' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_08' . $previous_dealer_code} / ${'curr_ach_08' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_09' . $previous_dealer_code} / ${'curr_ach_09' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_10' . $previous_dealer_code} / ${'curr_ach_10' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_11' . $previous_dealer_code} / ${'curr_ach_11' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_12' . $previous_dealer_code} / ${'curr_ach_12' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_01' . $previous_dealer_code} / ${'curr_ach_01' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_02' . $previous_dealer_code} / ${'curr_ach_02' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_03' . $previous_dealer_code} / ${'curr_ach_03' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'grand_total_curr' . $previous_dealer_code} / ${'grand_total_ach' . $previous_dealer_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b></b></td>
				// 	<td align='right'></td>
				//   </tr>";
			}
			//echo $countbranchyellowcard;
			// echo $loopcount;
			//echo '<br />';

			if ($countbranchyellowcard == $loopcount) {
				//echo 'gfgfgghgjhjhkjkjkjlklklklkklklklk';
				${'grand_total_avg' . $previous_branch_code} = ${'grand_total_curr' . $previous_branch_code} / $monthdiffd;
				//${'grand_total_avg'.$branch_code}=ceil(${'grand_total_curr'.$branch_code}/12);
				//For Branch Ach Avg
				/* ${'avg_total'_ach_branch.$previous_branch_code}=0;
					if(${'curr_ach_01'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_02'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_03'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_04'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_05'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_06'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_07'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_08'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_09'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_10'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_11'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;
					if(${'curr_ach_12'.$previous_branch_code} > 0) ${'avg_total'_ach_branch.$previous_branch_code}=${'avg_total'_ach_branch.$previous_branch_code}+1;*/

				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>$previous_branch_name SUB-DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_04' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_05' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_06' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_07' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_08' . $previous_branch_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_09' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_10' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_11' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_12' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_01' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_02' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_03' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . round(${'grand_total_avg' . $previous_branch_code}, 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>DEALER TOTAL</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'curr_ach_04' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_05' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_06' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_07' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_08' . $previous_branch_code} . "</b></td>
					<td align='right' ><b>" . ${'curr_ach_09' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_10' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_11' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_12' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_01' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_02' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'curr_ach_03' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . ${'grand_total_curr_ach' . $previous_branch_code} . "</b></td>
					<td align='right'><b>" . round((${'grand_total_curr_ach' . $previous_branch_code} / $monthdiffd), 2) . "</b></td>
					<td align='right'></td>
				  </tr>";
				//   check for DivisionByZeroError
				${'percent_04' . $previous_branch_code} = 0;
				${'percent_05' . $previous_branch_code} = 0;
				${'percent_06' . $previous_branch_code} = 0;
				${'percent_07' . $previous_branch_code} = 0;
				${'percent_08' . $previous_branch_code} = 0;
				${'percent_09' . $previous_branch_code} = 0;
				${'percent_10' . $previous_branch_code} = 0;
				${'percent_11' . $previous_branch_code} = 0;
				${'percent_12' . $previous_branch_code} = 0;
				${'percent_01' . $previous_branch_code} = 0;
				${'percent_02' . $previous_branch_code} = 0;
				${'percent_03' . $previous_branch_code} = 0;
				${'percent_total' . $previous_branch_code} = 0;
				if (${'curr_ach_04' . $previous_branch_code} > 0) ${'percent_04' . $previous_branch_code} = round(((${'curr_04' . $previous_branch_code} / ${'curr_ach_04' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_05' . $previous_branch_code} > 0) ${'percent_05' . $previous_branch_code} = round(((${'curr_05' . $previous_branch_code} / ${'curr_ach_05' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_06' . $previous_branch_code} > 0) ${'percent_06' . $previous_branch_code} = round(((${'curr_06' . $previous_branch_code} / ${'curr_ach_06' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_07' . $previous_branch_code} > 0) ${'percent_07' . $previous_branch_code} = round(((${'curr_07' . $previous_branch_code} / ${'curr_ach_07' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_08' . $previous_branch_code} > 0) ${'percent_08' . $previous_branch_code} = round(((${'curr_08' . $previous_branch_code} / ${'curr_ach_08' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_09' . $previous_branch_code} > 0) ${'percent_09' . $previous_branch_code} = round(((${'curr_09' . $previous_branch_code} / ${'curr_ach_09' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_10' . $previous_branch_code} > 0) ${'percent_10' . $previous_branch_code} = round(((${'curr_10' . $previous_branch_code} / ${'curr_ach_10' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_11' . $previous_branch_code} > 0) ${'percent_11' . $previous_branch_code} = round(((${'curr_11' . $previous_branch_code} / ${'curr_ach_11' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_12' . $previous_branch_code} > 0) ${'percent_12' . $previous_branch_code} = round(((${'curr_12' . $previous_branch_code} / ${'curr_ach_12' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_01' . $previous_branch_code} > 0) ${'percent_01' . $previous_branch_code} = round(((${'curr_01' . $previous_branch_code} / ${'curr_ach_01' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_02' . $previous_branch_code} > 0) ${'percent_02' . $previous_branch_code} = round(((${'curr_02' . $previous_branch_code} / ${'curr_ach_02' . $previous_branch_code}) * 100), 2);
				if (${'curr_ach_03' . $previous_branch_code} > 0) ${'percent_03' . $previous_branch_code} = round(((${'curr_03' . $previous_branch_code} / ${'curr_ach_03' . $previous_branch_code}) * 100), 2);
				if (${'grand_total_curr_ach' . $previous_branch_code} > 0) ${'percent_total' . $previous_branch_code} = round(((${'grand_total_curr' . $previous_branch_code} / ${'grand_total_curr_ach' . $previous_branch_code}) * 100), 2);

				echo "<tr>
					<td ></td>
					<td >" . $previous_branch_name . "</td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ><b>% CONTRIBUTION</b></td>
					<td ></td>
					<td ></td>
					<td align='right'><b>" . ${'percent_04' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_05' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_06' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_07' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_08' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_09' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_10' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_11' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_12' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_01' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_02' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_03' . $previous_branch_code} . "%</b></td>
					<td align='right'><b>" . ${'percent_total' . $previous_branch_code} . "%</b></td>
					<td align='right'><b></b></td>
					<td align='right'></td>
				  </tr>";
				// echo "<tr>
				// 	<td ></td>
				// 	<td >" . $previous_branch_name . "</td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td ><b>% CONTRIBUTION</b></td>
				// 	<td ></td>
				// 	<td ></td>
				// 	<td align='right'><b>" . round(((${'curr_04' . $previous_branch_code} / ${'curr_ach_04' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_05' . $previous_branch_code} / ${'curr_ach_05' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_06' . $previous_branch_code} / ${'curr_ach_06' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_07' . $previous_branch_code} / ${'curr_ach_07' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_08' . $previous_branch_code} / ${'curr_ach_08' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_09' . $previous_branch_code} / ${'curr_ach_09' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_10' . $previous_branch_code} / ${'curr_ach_10' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_11' . $previous_branch_code} / ${'curr_ach_11' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_12' . $previous_branch_code} / ${'curr_ach_12' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_01' . $previous_branch_code} / ${'curr_ach_01' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_02' . $previous_branch_code} / ${'curr_ach_02' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'curr_03' . $previous_branch_code} / ${'curr_ach_03' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b>" . round(((${'grand_total_curr' . $previous_branch_code} / ${'grand_total_curr_ach' . $previous_branch_code}) * 100), 2) . "%</b></td>
				// 	<td align='right'><b></b></td>
				// 	<td align='right'></td>
				//   </tr>";
			}
			$count++;

			$previous_dealer_code = $dealer_dns_code;
			$previous_branch_code = $branch_code;
			$previous_branch_name = $branch_name;
			$previous_dealer_name = $linked_dealer_name;
			//}
			$loopcount++;
		}
		?>
	</table>
	<br /><br />
	<p>
	<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
		<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();">
	</div>
	</p>
<?php
} else {
	echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
}
?>