<?php
// define("SERVER","localhost");
// define("USER","root");
// define("PASSWORD","Passw0rd123#$");
// //require("include/config-setup.php");
// define("DB","acedns_STAR");
// //define("DB","acedns_ARCHITA");
// $link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
// mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require_once("sfa_connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$localDB = new sfa_connection();
$link = $localDB->conn;
$date = gmdate('d', strtotime('+330 minute'));
$month = gmdate('m', strtotime('+330 minute'));
$year = gmdate('Y', strtotime('+330 minute'));

$hour = gmdate('H', strtotime('+330 minute'));
$minute = gmdate('i', strtotime('+330 minute'));
$second = gmdate('s', strtotime('+330 minute'));
$location_date = $year . $month . $date . $hour . $minute . $second;

$sqltrun = "delete from RSSD_yellow_card_count where 1";
mysqli_query($link, $sqltrun);

$sqlselyellowcarddetails = "SELECT customer_code,challan_date,qty,linked_dealer_code FROM yellow_card_details
							WHERE challan_date >='2022-04-01'";
$rsselyellowcarddetails = mysqli_query($link, $sqlselyellowcarddetails);

$processed = 0;
while ($rowselyellowcarddetails = mysqli_fetch_assoc($rsselyellowcarddetails)) {
	$customer_code = $rowselyellowcarddetails['customer_code'];
	$challan_date = $rowselyellowcarddetails['challan_date'];
	$qty = $rowselyellowcarddetails['qty'];
	$linked_dealer_code = $rowselyellowcarddetails['linked_dealer_code'];

	$challan_date_parts = substr($challan_date, 5, 2);
	$challan_date_parts_one = substr($challan_date, 0, 4);

	$sqlchkyellowcardcount = "SELECT customer_code FROM RSSD_yellow_card_count WHERE customer_code='" . $customer_code . "'";
	$rschkyellowcardcount = mysqli_query($link, $sqlchkyellowcardcount);
	$cntyellowcardcount = mysqli_num_rows($rschkyellowcardcount);
	if (strtotime($challan_date) >= strtotime('2023-04-01')) {
		$activity_column = 'curr_' . $challan_date_parts;
	} else {
		$activity_column = 'prev_' . $challan_date_parts;
	}
	if($cntyellowcardcount >0)
	{
// 		print_r($cntyellowcardcount);
// die;
		$sqlupdateyellowcardcount="UPDATE RSSD_yellow_card_count SET $activity_column=($activity_column+$qty) WHERE customer_code='".$customer_code."'";
		mysqli_query($link,$sqlupdateyellowcardcount);

	}
	else
	{
// 		print_r($cntyellowcardcount);
// die;
		$sqlselcustdetails="SELECT DISTINCT CM.customer_name,RM.route_code,RM.route_name,CM.appointment_date FROM customer_master CM,customer_route_emp_relation CRR,route_master RM
							WHERE CM.customer_code=CRR.customer_code AND CRR.route_code=RM.route_code AND CM.customer_code='".$customer_code."'
							AND CRR.acedns='Y'";
		$rsselcustdetails=mysqli_query($link,$sqlselcustdetails);
		$rowselcustdetils=mysqli_fetch_assoc($rsselcustdetails);
		
		$sub_dealer_name=$rowselcustdetils['customer_name'] ?? '';
		$sub_dealer_route_code=$rowselcustdetils['route_code'] ?? '';
		$sub_dealer_route_name=$rowselcustdetils['route_name'] ?? '';
		$appointment_date=$rowselcustdetils['appointment_date'] ?? null;
$appointment_date_sql = empty($appointment_date) || $appointment_date == '0000-00-00'
    ? "NULL"
    : "'" . mysqli_real_escape_string($link, $appointment_date) . "'";
// 		if (empty($appointment_date) || $appointment_date == '0000-00-00') {
//     $appointment_date_sql = "NULL";
// } else {
//     $appointment_date_sql = "'" . mysqli_real_escape_string($link, $appointment_date) . "'";
// }

		$sqldealername="SELECT customer_name FROM customer_master WHERE customer_code='".$linked_dealer_code."'";
		$rsdealername=mysqli_query($link,$sqldealername);

		$rowdealername=mysqli_fetch_assoc($rsdealername);

		$dealer_name=$rowdealername['customer_name'];

		$rsselcustdetails=mysqli_query($link,$sqlselcustdetails);

		$rowselcustdetils=mysqli_fetch_assoc($rsselcustdetails);

		$sqlinsertyellowcardcount="INSERT INTO RSSD_yellow_card_count SET
									customer_code='".$customer_code."',
									customer_name='".$sub_dealer_name."',
									linked_dealer_code='".$linked_dealer_code."',
									linked_dealer_name='".$dealer_name."',
									route_code='".$sub_dealer_route_code."',
									route_name='".$sub_dealer_route_name."',
									appointment_date=".$appointment_date_sql.",
									$activity_column='".$qty."'";
// echo $sqlinsertyellowcardcount;
// die;
		mysqli_query($link,$sqlinsertyellowcardcount);
// exit();
	}

	// if ($cntyellowcardcount > 0) {
	// 	// Safe arithmetic with NULL handling
	// 	$sqlupdateyellowcardcount = "UPDATE RSSD_yellow_card_count 
    //                              SET $activity_column = IFNULL($activity_column, 0) + $qty 
    //                              WHERE customer_code = '" . mysqli_real_escape_string($link, $customer_code) . "'";
	// 	mysqli_query($link, $sqlupdateyellowcardcount);
	// } else {
	// 	// Get customer details
	// 	$sqlselcustdetails = "SELECT DISTINCT 
    //                           CM.customer_name,
    //                           RM.route_code,
    //                           RM.route_name,
    //                           CM.appointment_date 
    //                       FROM customer_master CM
    //                       JOIN customer_route_emp_relation CRR ON CM.customer_code = CRR.customer_code
    //                       JOIN route_master RM ON CRR.route_code = RM.route_code
    //                       WHERE CM.customer_code = '" . mysqli_real_escape_string($link, $customer_code) . "'
    //                       AND CRR.acedns = 'Y'
    //                       LIMIT 1";

	// 	$rsselcustdetails = mysqli_query($link, $sqlselcustdetails);
	// 	$rowselcustdetils = mysqli_fetch_assoc($rsselcustdetails);

	// 	if (!$rowselcustdetils) {

	// 		continue;
	// 	}

	// 	$sub_dealer_name = mysqli_real_escape_string($link, $rowselcustdetils['customer_name']);
	// 	$sub_dealer_route_code = mysqli_real_escape_string($link, $rowselcustdetils['route_code']);
	// 	$sub_dealer_route_name = mysqli_real_escape_string($link, $rowselcustdetils['route_name']);
	// 	$appointment_date = mysqli_real_escape_string($link, $rowselcustdetils['appointment_date']);


	// 	$sqldealername = "SELECT customer_name FROM customer_master WHERE customer_code = '" . mysqli_real_escape_string($link, $linked_dealer_code) . "' LIMIT 1";
	// 	$rsdealername = mysqli_query($link, $sqldealername);
	// 	$rowdealername = mysqli_fetch_assoc($rsdealername);

	// 	if (!$rowdealername) {

	// 		continue;
	// 	}

	// 	$dealer_name = mysqli_real_escape_string($link, $rowdealername['customer_name']);


	// 	$sqlinsertyellowcardcount = "INSERT INTO RSSD_yellow_card_count SET
    //     customer_code = '" . mysqli_real_escape_string($link, $customer_code) . "',
    //     customer_name = '$sub_dealer_name',
    //     linked_dealer_code = '" . mysqli_real_escape_string($link, $linked_dealer_code) . "',
    //     linked_dealer_name = '$dealer_name',
    //     route_code = '$sub_dealer_route_code',
    //     route_name = '$sub_dealer_route_name',
    //     appointment_date = '$appointment_date',
    //     $activity_column = $qty";

	// 	mysqli_query($link, $sqlinsertyellowcardcount);
	// }

	//exit();
	//$processed++;	
}
echo "SUCCESS";

mysqli_close($link);

?>
