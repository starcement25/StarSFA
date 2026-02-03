<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code = $_REQUEST['emp_code'] ?? '';
$last_update_time = $_REQUEST['last_update_time'] ?? '';
$last_update_time = str_replace('€', ' ', $last_update_time);
$incremental_download = $_REQUEST['incremental_download'] ?? '';
$data_download_time = $_REQUEST['data_download_time'] ?? '';
$data_download_time = str_replace('€', ' ', $data_download_time);

if ($incremental_download == 'no') {
	$login_condition = "";
	$login_condition_one = "";
} else {
	$login_condition = " AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('" . $last_update_time . "')";
	$login_condition_one = " AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('" . $last_update_time . "')";
}
$sqlmenuaccess = "SELECT not_accessible_menu FROM menu_access WHERE emp_code='" . $emp_code . "'";
$rsmenuaccess = mysqli_query($link, $sqlmenuaccess);
$countmenuaccess = mysqli_num_rows($rsmenuaccess);
$menu_access_array = array();
if ($countmenuaccess > 0) {
	while ($rowmenuaccess = mysqli_fetch_assoc($rsmenuaccess)) {
		array_push($menu_access_array, $rowmenuaccess['not_accessible_menu']);
	}
}
$datacontents='';
if (!in_array('GRN', $menu_access_array)) // Start of loading
{

	if (strtoupper(substr($emp_code, 0, 1)) == 'C') {
		$sqlquery = "SELECT DISTINCT DT.DO_no,DT.dns_DO_no,DT.sku_code,DT.DO_qty,PM.prod_desc,DDD.loading_qty 
				FROM DO_transaction DT,product_master PM,DO_despatch_details DDD WHERE DT.sku_code=PM.prod_code AND DT.DO_no=DDD.DO_no AND DT.DO_status 
					IN('despatch')  AND DT.customer_code='" . $emp_code . "'";



		$result = mysqli_query($link, $sqlquery);
		$count = mysqli_num_rows($result);
		if ($count > 0) {
			$date = gmdate('d', strtotime('+330 minute'));
			$month = gmdate('m', strtotime('+330 minute'));
			$year = gmdate('Y', strtotime('+330 minute'));

			$hour = gmdate('H', strtotime('+330 minute'));
			$minute = gmdate('i', strtotime('+330 minute'));
			$second = gmdate('s', strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime = $year . '-' . $month . '-' . $date . '€' . $hour . ':' . $minute . ':' . $second . "\n";
			$dns_route_code = '';
			while ($rowDOtrans = mysqli_fetch_assoc($result)) {
				$dns_DO_no = str_replace('DO//', 'DO/BHD/', $rowDOtrans['dns_DO_no']);
				$contents  = (($rowDOtrans['DO_no'] != '') ? $rowDOtrans['DO_no'] : ' ') . "^";
				$contents  .= (($dns_DO_no != '') ? $dns_DO_no : ' ') . "^";
				$contents  .= (($rowDOtrans['sku_code'] != '') ? $rowDOtrans['sku_code'] : ' ') . "^";
				$contents  .= (($rowDOtrans['prod_desc'] != '') ? $rowDOtrans['prod_desc'] : ' ') . "^";
				$contents  .= (($rowDOtrans['DO_qty'] != '') ? $rowDOtrans['DO_qty'] : ' ') . "^";
				$contents  .= (($rowDOtrans['loading_qty'] != '') ? $rowDOtrans['loading_qty'] : ' ') . "^";
				$contents  .= ' ';
				$linecontents  .= $contents . "\n";
			}
			$contentsrowcolumn = $count . '¥' . '7';
			$datacontents = $contentsrowcolumn . "\n" . $contentsdatetime . str_replace("\r", "", $linecontents);
		} else {
			$last_update_time = str_replace('?', '', $last_update_time);
			$data_download_time = str_replace('?', '', $data_download_time);
			if (strtotime($data_download_time) >= strtotime($last_update_time)) {
				$datacontents = '0' . '¥' . '0';
			} else {
				$datacontents = '0' . '¥' . '7';
			}
		}
	}
}
$datetime = gmdate('Y-m-d H:m:s', strtotime('+330 minute'));
$url = APICALLLOGURL . "/GRN-DO-download.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime, $emp_code, $url, $nick_name);
header("Content-type: application/text");
header("Content-Disposition: attachment; filename=GRN_DO.txt");
print "$datacontents";
mysqli_close($link);
