<?php
ob_start();
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

if (isset($_SESSION['competitor_name_passing_array'])) {
    $storedArray = $_SESSION['competitor_name_passing_array'];
}

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$branch = $_REQUEST['branch'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

// echo "<pre>";

// print_r($storedArray);

if($branch=="all")
{
	$branchcondition='WHERE 1';
}
else
{
	$branchcondition=" WHERE branch_code='".$branch."'";
}
$competitor_name_array = array();

$sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master $branchcondition and acedns='yes' ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
$res_competitor_name = mysqli_query($link,$sql_competitor_name);
$countcompetitor=mysqli_num_rows($res_competitor_name);
//$colspanheader=10+($countcompetitor*4);

$header_not_star = "SI. No"."\t"."Date of Visit"."\t"."Emp Code"."\t"."Emp Name"."\t"."Branch"."\t"."Cust Category"."\t"."Cust Code"."\t"."Cust Name"."\t"."MF Cust Code"."\t"."Route Name"."\t"."Contact No"."\t";

// $table_data_star='';
$table_data_not_star='';
// echo "<pre>";
// print_r($storedArray);

$competitor_name_array=array();
// while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
// 	$competitor_name1 = $row_competitor_name['competitor_name'];
foreach($storedArray as $competitor_name1){
	$competitor_string .= "'".$competitor_name1."',";
	$header_star.= $competitor_name1."\t";
	$header_not_star.= $competitor_name1."\t";
	array_push($competitor_name_array,$competitor_name1);
}
// echo "<pre>";
// print_r($storedArray);
$competitor_string = rtrim($competitor_string,",");

$header_star.="\n";
$header_star.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";

$header_not_star.="\n";
$header_not_star.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";

	foreach($storedArray as  $competitorheaderval){
		$header_not_star.="Qty"."\t";
	}
		
	// }

//$header.="\n";
$count=1;
// $sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
// 						SUM(MF.PTD) PTD,SUM(MF.PTR) PTR,SUM(MF.PTC) PTC,SUM(MF.PV) PV ,SUM(MF.billing_ex_for) billing_ex_for,SUM(MF.wsp_ex_for) wsp_ex_for,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
// 						MF.competitor_name 
// 						FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
// 						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
// 						SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND  SUBSTRING(MF.market_feedback_id,3,5)  IN (".$employee_arg.") AND 
// 						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
// 						MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code 
// 						GROUP BY SUBSTRING(MF.market_feedback_id,3,5),DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
// 						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,MF.competitor_name ASC";
$sql_competitor_stock = "
    SELECT 
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        SUM(MF.PTD) AS PTD,
        SUM(MF.PTR) AS PTR,
        SUM(MF.PTC) AS PTC,
        SUM(MF.PV) AS PV,
        SUM(MF.billing_ex_for) AS billing_ex_for,
        SUM(MF.wsp_ex_for) AS wsp_ex_for,
        DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
        BM.branch_name,
        MF.competitor_name,
        MF.customer_code,
        COALESCE((
            SELECT
                SAD.qty_mt
            FROM
                mf_stk_audit_details SAD
            WHERE
                SUBSTRING(MF.market_feedback_id, 3) = SUBSTRING(SAD.mf_stk_audit_id, 3)
                AND SAD.competitor_name = MF.competitor_name
            LIMIT 1
        ), 0) AS qty_mt
    FROM 
        market_feedback MF
        INNER JOIN customer_master CM ON MF.customer_code = CM.customer_code
        INNER JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
        INNER JOIN route_master RM ON CM.route_code = RM.route_code
        INNER JOIN branch_master BM ON CM.branch_code = BM.branch_code
    WHERE 
        SUBSTRING(MF.market_feedback_id, 3, 5) IN (" . $employee_arg . ")
        AND (DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "')
        AND MF.competitor_name IN (" . $competitor_string . ")
    GROUP BY 
        CM.customer_name,
        CM.phone_no,
        CM.cust_type,
        CM.dns_customer_code,
        RM.route_name,
        EM.emp_name,
        EM.dns_emp_code,
        DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y'),
        BM.branch_name,
        MF.competitor_name,
        SUBSTRING(MF.market_feedback_id, 3, 5),
        MF.customer_code
    ORDER BY 
        visit_date DESC,
        FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
        MF.competitor_name ASC
";
//echo $sql_competitor_stock;exit();
$res_competitor_stock = mysqli_query($link,$sql_competitor_stock);
$count_competitor_stock=mysqli_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
	$data_array = array();

	while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)){
		$dns_emp_code = $row_competitor_stock['dns_emp_code'];
		$visit_date = $row_competitor_stock['visit_date'];
		$dns_customer_code = $row_competitor_stock['dns_customer_code'];
		$competitor_name = $row_competitor_stock['competitor_name'];
		$key = $dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;

		if(!isset($data_array[$key])){
			$data_array[$key] = array(
				'emp_name' => $row_competitor_stock['emp_name'],
				'customer_name' => $row_competitor_stock['customer_name'],
				'customer_code' => $row_competitor_stock['customer_code'],
				'phone_no' => $row_competitor_stock['phone_no'],
				'cust_type' => $row_competitor_stock['cust_type'],
				'route_name' => $row_competitor_stock['route_name'],
				'branch_name' => $row_competitor_stock['branch_name'],
				'competitors' => array()
			);
		}

		$data_array[$key]['competitors'][$competitor_name] = array(
			'qty_mt' => $row_competitor_stock['qty_mt']
		);
	}

	foreach($data_array as $key => $row_data){
		list($dns_emp_code_val, $dns_customer_code_val, $date_val) = explode('#', $key);

		$table_data_not_star .= $count."\t".$date_val."\t".$dns_emp_code_val."\t".$row_data['emp_name']."\t".$row_data['branch_name']."\t".$row_data['cust_type']."\t".$dns_customer_code_val."\t".$row_data['customer_name']."\t".$row_data['customer_code']."\t".$row_data['route_name']."\t".$row_data['phone_no']."\t";

		foreach($storedArray as $competitorval){
			if(isset($row_data['competitors'][$competitorval])){
				$total_qty_mt = $row_data['competitors'][$competitorval]['qty_mt'] == 0 ? '-' : $row_data['competitors'][$competitorval]['qty_mt'];
			}else{
				$total_qty_mt = '-';
			}

			$table_data_not_star .= $total_qty_mt."\t";
		}

		$table_data_not_star .="\n";
		$count++;
	}
}
// }
// $table_data_star="test";

// print_r($table_data);
// $table_data_star.="test9";
if($table_data_not_star!=''){	
	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Market feedback Qty.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header_not_star)."\n".$table_data_not_star;
	
	}else{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
	
	// if($table_data_star!=''){
		// header("Content-type: application/octet-stream");
		// header("Content-Disposition: attachment; filename=Market feedback price1.xls"); 
		// header("Pragma: no-cache"); 
		// header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		// echo ucwords($header_star)."\n".$table_data_star;
	// }else{
	// 	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	// }

// if($table_data_star!='' || $table_data_not_star!=''){
// 	header("Content-type: application/octet-stream"); 
// 	header("Content-Disposition: attachment; filename=Market feedback price.xls"); 
// 	header("Pragma: no-cache"); 
// 	header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
// 	echo ucwords($header_not_star)."\n".$table_data_not_star;
// 	// echo ucwords($header_star)."\n".$table_data_star;
// }else
// 	{
// 		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
// 	}

mysqli_close($link);

?>

