<?php
// define("SERVER","localhost");
// define("USER","root");
// define("PASSWORD","Passw0rd123#$");
// //require("include/config-setup.php");
// define("DB","acedns_STAR");
// $link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
//mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
//require("include/config-email-setup.php");
require_once("sfa_connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$localDB = new sfa_connection();
$link = $localDB->conn;
$month = gmdate('m', strtotime('+330 minute'));
$year = gmdate('Y', strtotime('+330 minute'));
$emp_code_array = array();
$month_array = array();


$sqlemplist = "SELECT EM.emp_name,EM.dns_emp_code,SUBSTRING(date,12,8) as time,SUBSTRING(date,1,10) as date,LO.trans_id
				FROM location LO,employee_master EM WHERE EM.emp_code=LO.emp_code AND
				 substring(LO.trans_id,1,2) IN('AE','CH')
				AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')>='2023-12-01'
				ORDER BY LO.emp_code ASC,
				DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d') ASC,trans_id ASC";
				//echo $sqlemplist;
				 //die;
$rsemplist = mysqli_query($link, $sqlemplist);
while ($rowemplist = mysqli_fetch_assoc($rsemplist)) {
	$time = $rowemplist['time'];
	$date = $rowemplist['date'];
	$dns_emp_code = $rowemplist['dns_emp_code'];
	$emp_name = $rowemplist['emp_name'];
	$trans_id = $rowemplist['trans_id'];
	$sqlchkattinfo = "SELECT Emp_Id FROM  t_att_checkout_info WHERE Emp_Id='" . $dns_emp_code . "' AND SUBSTRING(Entry_Date,1,10)='" . $date . "'";
	$reschkattinfo = mysqli_query($link, $sqlchkattinfo);
	$countchkattinfo = mysqli_num_rows($reschkattinfo);
	if ($countchkattinfo == 0) {
		echo $sqlinsertattinfo = "INSERT INTO t_att_checkout_info SET Emp_Id='" . $dns_emp_code . "',
								Emp_Name='" . $emp_name . "',Entry_Date='" . $date . "',CheckIN='" . $time . "'";
		$rsinsertattinfo = mysqli_query($link, $sqlinsertattinfo);
	} else {
		if (substr($trans_id, 0, 2) == 'CH') {
			echo $sqlupdateloc = "UPDATE t_att_checkout_info set CheckOUT='" . $time . "' WHERE Emp_Id='" . $dns_emp_code . "'
								AND Entry_Date='" . $date . "' AND CheckOUT IS NULL";
			mysqli_query($link, $sqlupdateloc);
		}
	}
}


 	//////////////
// date_default_timezone_set('Asia/Kolkata');

// $currentHour = date('H:i');
// $yesterday = date('Y-m-d', strtotime('-1 day'));
// $defaultCheckoutTime = '23:59:59';


// if ($currentHour >= '00:00' && $currentHour <= '01:00') {

//     $sqlCheck = "SELECT * FROM t_att_checkout_info 
//                  WHERE SUBSTRING(Entry_Date, 1, 10) = '$yesterday' 
//                  AND (CheckOUT IS NULL OR CheckOUT = '') 
//                  LIMIT 1"; 

//     $resultCheck = mysqli_query($link, $sqlCheck);

//     if (mysqli_num_rows($resultCheck) > 0) {
//         $sqlUpdateMissingCheckout = "UPDATE t_att_checkout_info 
//                                      SET CheckOUT = '$defaultCheckoutTime' 
//                                      WHERE SUBSTRING(Entry_Date, 1, 10) = '$yesterday' 
//                                      AND (CheckOUT IS NULL OR CheckOUT = '')";

//         mysqli_query($link, $sqlUpdateMissingCheckout);
//     }
// }

// date_default_timezone_set('Asia/Kolkata');

// $currentHour = date('H:i');
// $yesterday = date('Y-m-d', strtotime('-1 day'));
// $defaultCheckoutTime = '23:59:59'; // Updated as you want

// // Run only between 00:00 and 01:00
// // if ($currentHour >= '00:00' && $currentHour <= '01:00') {

//     // Check if there are any missing checkout values up to yesterday
//     $sqlCheck = "SELECT 1 FROM t_att_checkout_info 
//                  WHERE DATE(Entry_Date) <= '$yesterday'
//                    AND (CheckOUT IS NULL OR CheckOUT = '')
//                  LIMIT 1";

//     $resultCheck = mysqli_query($link, $sqlCheck);

//     if (mysqli_num_rows($resultCheck) > 0) {
//         // Update all missing checkout values up to yesterday
//         $sqlUpdateMissingCheckout = "UPDATE t_att_checkout_info 
//                                      SET CheckOUT = '$defaultCheckoutTime'
//                                      WHERE DATE(Entry_Date) <= '$yesterday'
//                                        AND (CheckOUT IS NULL OR CheckOUT = '')";

//         mysqli_query($link, $sqlUpdateMissingCheckout);
//     }
// }
///////////////////
echo "SUCCESS";
mysqli_close($link);
