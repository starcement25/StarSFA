<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$branch = $_REQUEST['branch'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
if($branch=="all")
{
	$branchcondition='';
}
else
{
	$branchcondition=" WHERE branch_code='".$branch."'";
}
$competitor_name_array = array();
$sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master $branchcondition ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
$res_competitor_name = mysql_query($sql_competitor_name);
$countcompetitor=mysql_num_rows($res_competitor_name);
$colspanheader=10+($countcompetitor*4);
$header = "SI. No"."\t"."Date of Visit"."\t"."Emp Code"."\t"."Emp Name"."\t"."Branch"."\t"."Cust Category"."\t"."Cust Code"
	."\t"."Cust Name"."\t"."Route Name"."\t"."Contact No"."\t";

$competitor_name_array=array();
while($row_competitor_name = mysql_fetch_array($res_competitor_name)){
	$competitor_name = $row_competitor_name['competitor_name'];
	$header.= $competitor_name."\t".""."\t".""."\t".""."\t";
	array_push($competitor_name_array,$competitor_name);
	$competitor_string .= "'".$competitor_name."',";
}
$competitor_string = rtrim($competitor_string,",");
//print_r($competitor_name_array);
$header.="\n";
$header.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";
  	
foreach($competitor_name_array as  $competitorheaderval){
	$header.="Billing Ex"."\t";
	$header.="WSP EX"."\t";
	$header.="RSP EX"."\t";
	$header.="NOD"."\t";
}
//$header.="\n";
$count=1;
$sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
						MF.PTD,MF.PTR,MF.PTC,MF.PV,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
						MF.competitor_name 
						FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
						SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND  SUBSTRING(MF.market_feedback_id,3,5)  IN (".$employee_arg.") AND 
						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
						MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code 
						GROUP BY DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,MF.competitor_name ASC";
$res_competitor_stock = mysql_query($sql_competitor_stock);
$count_competitor_stock=mysql_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
while($row_competitor_stock = mysql_fetch_array($res_competitor_stock)){
	$emp_name = $row_competitor_stock['emp_name'];
	$dns_emp_code = $row_competitor_stock['dns_emp_code'];
	$customer_name = $row_competitor_stock['customer_name'];
	$phone_no = $row_competitor_stock['phone_no'];
	$cust_type = $row_competitor_stock['cust_type'];
	$dns_customer_code = $row_competitor_stock['dns_customer_code'];
	$route_name = $row_competitor_stock['route_name'];
	$competitor_name_db = $row_competitor_stock['competitor_name'];
	${PTD.$competitor_name_db} = $row_competitor_stock['PTD'];
	${PTR.$competitor_name_db} = $row_competitor_stock['PTR'];
	${PTC.$competitor_name_db} = $row_competitor_stock['PTC'];
	${PV.$competitor_name_db} = $row_competitor_stock['PV'];
	$visit_date = $row_competitor_stock['visit_date'];
	$branch_name = $row_competitor_stock['branch_name'];
	
	/*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysql_query($sql_emp_details);
	$row_emp_details = mysql_fetch_array($res_emp_details);
	$emp_name = $row_emp_details['emp_name'];
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	
	$sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_details = mysql_query($sql_customer_details);
	$row_customer_details = mysql_fetch_array($res_customer_details);
	$customer_name = $row_customer_details['customer_name'];
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$cust_type = $row_customer_details['cust_type'];*/
	$table_data .= $count."\t".$visit_date."\t".$dns_emp_code."\t".$emp_name."\t".$branch_name."\t".$cust_type."\t".$dns_customer_code."\t".$customer_name."\t".$route_name."\t".
	$phone_no."\t";

	foreach($competitor_name_array as  $competitorval){
		if($competitorval == $competitor_name_db){
			$total_PTD=${PTD.$competitor_name_db};
			$total_PTR=${PTR.$competitor_name_db};
			$total_PTC=${PTC.$competitor_name_db};
			$total_PV=${PV.$competitor_name_db};
		}
		else
		{
			$total_PTD='-';
			$total_PTR='-';	
			$total_PTC='-';	
			$total_PV='-';		
		}
		$table_data .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";
	}	
	
	$table_data .="\n";
	$count++;
}
}
if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Market feedback price.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
mysql_close($link);
?>
