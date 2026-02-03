<?php
// define("SERVER","localhost");
// define("USER","root");
// define("PASSWORD","Passw0rd123#$");
// require("include/config-setup.php");
//define("DB","acedns_STAR");
// require("include/functions.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//define("DB","acedns_ARCHITA");
// $link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
// mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
//require_once("sfa_connection.php");

// $localDB = new sfa_connection();
// $link = $localDB->conn;
// function return_employee_hierarchy_active($emp_code) {
//     $emphierarchy = array();
//     employee_hierarchy_details_active($emp_code, $emphierarchy,$link);
// 	foreach($emphierarchy as $hierarchyval)
// 	{
// 		$emphierarchystring.=$hierarchyval.',';
// 	}
// 	$emphierarchystring=substr($emphierarchystring,0,-1);
// 	if(count(explode(',',$emphierarchystring))==1 && $emphierarchystring=="'".$emp_code."'")
// 	{
// 		$emphierarchystring=$emphierarchystring;
// 	}
// 	else
// 	{
// 		$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
// 	}
//     return $emphierarchystring;
// }
// function employee_hierarchy_details_active($emp_code,&$emphierarchy){
//      //$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
// 	 require_once("sfa_connection.php");

// $localDB = new sfa_connection();
// $link = $localDB->conn;
//    //$sqlemphierarchy="SELECT emp_code FROM employee_master WHERE reporting_to='".$emp_code."'";
//    $sqlemphierarchy="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to) AND acedns='Y'";
//    $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
//    $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
// 	if($cntemphierarchy>0)
// 	{
// 		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
// 		{
// 			$emphierarchy[] = "'".$rowemphierarchy['emp_code']."'";
// 			employee_hierarchy_details_active($rowemphierarchy['emp_code'],$emphierarchy);
// 		}
// 	}
// 	else
// 	{
// 		if(!in_array("'".$emp_code."'",$emphierarchy))
// 		{
// 			$emphierarchy[] ="'".$emp_code."'";
// 		}
// 	}
// }
// $date = gmdate('d', strtotime('+330 minute'));
// $month = gmdate('m', strtotime('+330 minute'));
// $year = gmdate('Y', strtotime('+330 minute'));

// $hour = gmdate('H', strtotime('+330 minute'));
// $minute = gmdate('i', strtotime('+330 minute'));
// $second = gmdate('s', strtotime('+330 minute'));
// $current_date_month = $month . '-' . $date;

// //$employee_hierarchy=return_employee_hierarchy($emp_code);
// //$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';

// $sqltruncate = "TRUNCATE birth_anniversary_customer_list";
// mysqli_query($link, $sqltruncate);

// //$sqlselemp="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' AND emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master)";
// $sqlselemp = "SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_code ASC";
// $rsselemp = mysqli_query($link, $sqlselemp);

// while ($rowselemp = mysqli_fetch_assoc($rsselemp)) {
// 	$emp_code = $rowselemp['emp_code'];
// 	$emp_name = $rowselemp['emp_name'];
// 	$sqlregistrationid = "SELECT registrationid FROM changepassword WHERE emp_code='" . $emp_code . "'";
// 	$rsregistrationid = mysqli_query($link, $sqlregistrationid);

// 	$rowregistrationid = mysqli_fetch_assoc($rsregistrationid);
// 	$registrationid = $rowregistrationid['registrationid'];
// // 		
// 	$employee_hierarchy = return_employee_hierarchy($emp_code);

// 	$sqlcustomerlistbirth = "SELECT GROUP_CONCAT(DISTINCT CM.dns_customer_code SEPARATOR ',') AS birth_customer FROM customer_master CM
// 								INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code
// 								AND CRR.emp_code IN (" . $employee_hierarchy . ") AND CRR.acedns='Y' AND
// 								SUBSTRING(CM.date_of_birth,6,5)='" . $current_date_month . "'";
// 	$rscustomerlistbirth = mysqli_query($link, $sqlcustomerlistbirth);
// 	$rowcustomerlistbirth = mysqli_fetch_assoc($rscustomerlistbirth);
// 	$customerlistbirth = $rowcustomerlistbirth['birth_customer'];

// 	$sqlcustomerlistanniversary = "SELECT GROUP_CONCAT(DISTINCT CM.dns_customer_code SEPARATOR ',') AS anniversary_customer FROM customer_master CM
// 								INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code=CM.customer_code
// 								AND CRR.emp_code IN (" . $employee_hierarchy . ") AND CRR.acedns='Y'
// 								AND SUBSTRING(CM.date_of_anniversary,6,5)='" . $current_date_month . "'";
// 	$rscustomerlistanniversary = mysqli_query($link, $sqlcustomerlistanniversary);
// 	$rowcustomerlistanniversary = mysqli_fetch_assoc($rscustomerlistanniversary);
// 	$customerlistanniversary = $rowcustomerlistanniversary['anniversary_customer'];

// 	$sqlinsbirthanniversarylist  = "INSERT INTO birth_anniversary_customer_list ";
// 	$sqlinsbirthanniversarylist .= " SET emp_code='" . $emp_code . "'";
// 	$sqlinsbirthanniversarylist .= " ,registrationid='" . $registrationid . "'";
// 	$sqlinsbirthanniversarylist .= " ,	birthday_customer='" . $customerlistbirth . "'";
// 	$sqlinsbirthanniversarylist .= " ,anniversary_customer='" . $customerlistanniversary . "'";
// 	mysqli_query($link, $sqlinsbirthanniversarylist) or die(mysqli_error() . " Error in birth anniversary insertion.");
// }

// echo "SUCCESS";

// mysqli_close($link);



error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");


require_once("sfa_connection.php");
$localDB = new sfa_connection();
$link = $localDB->conn;


function return_employee_hierarchy_active($emp_code) {
    global $link; 
    $emphierarchy = array();
    employee_hierarchy_details_active($emp_code, $emphierarchy);

    $emphierarchystring = implode(',', $emphierarchy);

    if (count($emphierarchy) == 1 && $emphierarchy[0] == "'$emp_code'") {
        return $emphierarchystring;
    } else {
        return $emphierarchystring . ',' . "'$emp_code'";
    }
}


function employee_hierarchy_details_active($emp_code, &$emphierarchy) {
    global $link;

    $sqlemphierarchy = "SELECT emp_code FROM employee_master WHERE FIND_IN_SET('$emp_code', reporting_to) AND acedns='Y'";
    $rsemphierarchy = mysqli_query($link, $sqlemphierarchy);

    if (mysqli_num_rows($rsemphierarchy) > 0) {
        while ($row = mysqli_fetch_assoc($rsemphierarchy)) {
            $code = "'{$row['emp_code']}'";
            if (!in_array($code, $emphierarchy)) {
                $emphierarchy[] = $code;
                employee_hierarchy_details_active($row['emp_code'], $emphierarchy);
            }
        }
    } else {
        $code = "'$emp_code'";
        if (!in_array($code, $emphierarchy)) {
            $emphierarchy[] = $code;
        }
    }
}


$date = gmdate('d', strtotime('+330 minute'));
$month = gmdate('m', strtotime('+330 minute'));
$year = gmdate('Y', strtotime('+330 minute'));
$hour = gmdate('H', strtotime('+330 minute'));
$minute = gmdate('i', strtotime('+330 minute'));
$second = gmdate('s', strtotime('+330 minute'));
$current_date_month = $month . '-' . $date;


$sqltruncate = "TRUNCATE birth_anniversary_customer_list";
mysqli_query($link, $sqltruncate);


$sqlselemp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_code ASC";
$rsselemp = mysqli_query($link, $sqlselemp);


while ($rowselemp = mysqli_fetch_assoc($rsselemp)) {
    $emp_code = $rowselemp['emp_code'];
    $emp_name = $rowselemp['emp_name'];

 
    $sqlregistrationid = "SELECT registrationid FROM changepassword WHERE emp_code = '$emp_code'";
    $rsregistrationid = mysqli_query($link, $sqlregistrationid);
    $registrationid = '';
    if ($rsregistrationid && mysqli_num_rows($rsregistrationid) > 0) {
        $rowregistrationid = mysqli_fetch_assoc($rsregistrationid);
        $registrationid = $rowregistrationid['registrationid'];
    }

 
    $employee_hierarchy = return_employee_hierarchy_active($emp_code);


    $sqlcustomerlistbirth = "
        SELECT GROUP_CONCAT(DISTINCT CM.dns_customer_code SEPARATOR ',') AS birth_customer
        FROM customer_master CM
        INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code = CM.customer_code
        WHERE CRR.emp_code IN ($employee_hierarchy)
        AND CRR.acedns = 'Y'
        AND SUBSTRING(CM.date_of_birth, 6, 5) = '$current_date_month'";
    $rscustomerlistbirth = mysqli_query($link, $sqlcustomerlistbirth);
    $rowcustomerlistbirth = mysqli_fetch_assoc($rscustomerlistbirth);
    $customerlistbirth = $rowcustomerlistbirth['birth_customer'];


    $sqlcustomerlistanniversary = "
        SELECT GROUP_CONCAT(DISTINCT CM.dns_customer_code SEPARATOR ',') AS anniversary_customer
        FROM customer_master CM
        INNER JOIN customer_route_emp_relation CRR ON CRR.customer_code = CM.customer_code
        WHERE CRR.emp_code IN ($employee_hierarchy)
        AND CRR.acedns = 'Y'
        AND SUBSTRING(CM.date_of_anniversary, 6, 5) = '$current_date_month'";
    $rscustomerlistanniversary = mysqli_query($link, $sqlcustomerlistanniversary);
    $rowcustomerlistanniversary = mysqli_fetch_assoc($rscustomerlistanniversary);
    $customerlistanniversary = $rowcustomerlistanniversary['anniversary_customer'];


    $sqlinsbirthanniversarylist = "
        INSERT INTO birth_anniversary_customer_list
        SET emp_code = '$emp_code',
            registrationid = '$registrationid',
            birthday_customer = '$customerlistbirth',
            anniversary_customer = '$customerlistanniversary'";
    mysqli_query($link, $sqlinsbirthanniversarylist) or die(mysqli_error($link) . " Error in birth anniversary insertion.");
}

echo "SUCCESS";


mysqli_close($link);
?>

