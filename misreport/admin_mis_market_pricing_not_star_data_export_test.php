<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();
require("adminUtils.php");

// echo $_SESSION['nick_name'];
$table_data_not_star='';
$storedArray=array();
// echo $_SESSION['competitor_name_array_not_star'];
if(isset($_SESSION['competitor_name_array_not_star'])){
	$storedArray = $_SESSION['competitor_name_array_not_star'];
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
$header_star = "SI. No"."\t"."Date of Visit"."\t"."Emp Code"."\t"."Emp Name"."\t"."Branch"."\t"."Cust Category"."\t"."Cust Code"."\t"."Cust Name"."\t"."Route Name"."\t"."Contact No"."\t";

$competitor_name_array=array();
// while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
// 	$competitor_name1 = $row_competitor_name['competitor_name'];
foreach($storedArray as $competitor_name){

	$competitor_string .= "'".$competitor_name."',";
	$header_star.= $competitor_name."\t".""."\t".""."\t"."\t"."\t";
	array_push($competitor_name_array,$competitor_name);
}
// echo "<pre>";
// print_r($storedArray);
$competitor_string = rtrim($competitor_string,",");

$header_star.="\n";
$header_star.=""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t".""."\t";

foreach($storedArray as  $competitorheaderval2){

    $header_star.="Billing"."\t";
    $header_star.="WSP"."\t";
    $header_star.="RSP"."\t";
    $header_star.="NOD"."\t";
    
    }
		
	// }

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
$res_competitor_stock = mysqli_query($link,$sql_competitor_stock);
$count_competitor_stock=mysqli_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
	$customer_emp_date_array=array();
	
while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)){
	
	$competitorStockData[] = $row_competitor_stock;

	$dns_emp_code = $row_competitor_stock['dns_emp_code'];
	$visit_date = $row_competitor_stock['visit_date'];
	$dns_customer_code = $row_competitor_stock['dns_customer_code'];
	$emp_name = $row_competitor_stock['emp_name'];
	${'emp_name'.$dns_emp_code.$dns_customer_code.$visit_date}=$emp_name;
	${'customer_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['customer_name'];
	${'phone_no'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['phone_no'];
	${'cust_type'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['cust_type'];
	${'route_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['route_name'];
	${'competitor_name_db'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['competitor_name'];

	${'PTD'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
	${'PTR'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
	${'PTC'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
	${'PV'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];

	${'branch_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['branch_name'];
	$customer_emp_date_string=$dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
	
	$table_data_not_star .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";

	/*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysqli_query($link,$sql_emp_details);
	$row_emp_details = mysqli_fetch_assoc($res_emp_details);
	$emp_name = $row_emp_details['emp_name'];
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	
	$sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_details = mysqli_query($link,$sql_customer_details);
	$row_customer_details = mysqli_fetch_assoc($res_customer_details);
	$customer_name = $row_customer_details['customer_name'];
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$cust_type = $row_customer_details['cust_type'];*/
	if(!in_array($customer_emp_date_string,$customer_emp_date_array))
	{
		array_push($customer_emp_date_array,$customer_emp_date_string);
	}
}

// echo "<pre>";
// print_r($row_competitor_stock['competitor_name']);
for($i=0;$i < count($customer_emp_date_array);$i++)
	{
		$customer_emp_date_string_val=explode("#",$customer_emp_date_array[$i]);
		$dns_emp_code_val=$customer_emp_date_string_val[0];
		$dns_customer_code_val=$customer_emp_date_string_val[1];
		$date_val=$customer_emp_date_string_val[2];

		$table_data_not_star .= $count."\t".$date_val."\t".$dns_emp_code_val."\t".${'emp_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'branch_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'cust_type'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".$dns_customer_code_val."\t".${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'phone_no'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t";

	foreach($storedArray as  $competitorval){

		// if($competitorval == ${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval}){

			$competitor_name_val=${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};

				$total_PTD=${'PTD'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
				$total_PTR=${'PTR'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
				$total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
                $total_PV=${'PV'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};

				if($total_PTD==0){
					$total_PTD='-';
				}
				if($total_PTR==0){
					$total_PTR='-';
				}
				if($total_PTC==0){
					$total_PTC='-';
				}if($total_PV==0){
					$total_PV='-';
				}

				$table_data_not_star .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";
				
			
		}

		

		// else
		// {
		// 	// $total_PTD='-';
		// 	// $total_PTR='-';	
		// 	$total_PTC='-';	
		// 	// $total_PV='-';
		// 	$total_PTD_ex='-';
		// 	$total_PTD_for='-';
		// 	$total_PTR_ex='-';
		// 	$total_PTR_for='-';
			
		// }
		
		$table_data_not_star .="\n";
		$count++;
	}	
	
	// $table_data_not_star .="\n";
	// $table_data_not_star .="\n";
	// $count++;
	}
// }
// $table_data_not_star="test";
$table_data_not_star.="test1";
$customer_emp_date_array=array();
$count=1;	
$table_data_not_star.="test1.0";

$table_data_not_star.="test1.001";					
$res_competitor_stock1 = mysqli_query($link,$sql_competitor_stock);
$count_competitor_stock1=mysqli_num_rows($res_competitor_stock1);
$table_data_not_star.="test1.002";
$table_data_not_star.=$count_competitor_stock1;

$table_data_not_star="test1.1";
// while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)){
	$table_data_not_star="test1.2";
	// $dns_emp_code = $row_competitor_stock1['dns_emp_code'];
	// $visit_date = $row_competitor_stock1['visit_date'];
	// $dns_customer_code = $row_competitor_stock1['dns_customer_code'];
	// $emp_name = $row_competitor_stock1['emp_name'];
	// ${'emp_name'.$dns_emp_code.$dns_customer_code.$visit_date}=$emp_name;
	// ${'customer_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock1['customer_name'];
	// ${'phone_no'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock1['phone_no'];
	// ${'cust_type'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock1['cust_type'];
	// ${'route_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock1['route_name'];
	// ${'competitor_name_db'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock1['competitor_name']} = $row_competitor_stock1['competitor_name'];

	${'PTD'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
	${'PTR'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
	${'PTC'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
	${'PV'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];
	$table_data_not_star.="test2.0";
	${'branch_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['branch_name'];
	$customer_emp_date_string=$dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
	
	array_push($customer_emp_date_array,$customer_emp_date_string);
	$table_data_not_star.="test2";
// }

// for($i=0;$i < count($customer_emp_date_array);$i++)
// 	{
$competitorStockData = array();
while ($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)) {
	$competitorStockData[] = $row_competitor_stock;
}
$table_data_not_star='';
$count = 1;
foreach ($competitorStockData as $row_competitor_stock) {
	
	$customer_emp_date_string_val=explode("#",$customer_emp_date_array[$i]);
		$dns_emp_code_val=$customer_emp_date_string_val[0];
		$dns_customer_code_val=$customer_emp_date_string_val[1];
		$date_val=$customer_emp_date_string_val[2];
		// $table_data_not_star.="test4";

		// $table_data_not_star .= $count."\t".$date_val."\t".$dns_emp_code_val."\t".${'emp_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'branch_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'cust_type'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".$dns_customer_code_val."\t".${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t".${'phone_no'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."\t";

		$table_data_not_star .= $count . "\t" . $date_val . "\t" . $dns_emp_code_val . "\t" . ${'emp_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t" . ${'branch_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t" . ${'cust_type' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t" . $dns_customer_code_val . "\t" . ${'customer_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t" . ${'route_name' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t" . ${'phone_no' . $dns_emp_code_val . $dns_customer_code_val . $date_val} . "\t";

		// $table_data_not_star.=$storedArray[2];

	// $table_data_not_star.="test8";
	// $table_data_not_star .="\n";
	// $count++;
}
echo "test2";
print_r($storedArray);
foreach($storedArray as $competitorval){
		echo "test3";
	// if($competitorval == ${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval}){
		// $table_data_not_star.="test6";
		$competitor_name_val=${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
		
		// if($_SESSION['nick_name']!='STAR'){
			echo "test4";
			$total_PTD=${'PTD'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
			$total_PTR=${'PTR'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
			$total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
			$total_PV=${'PV'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};

			if($total_PTD==0){
				$total_PTD='-';
			}
			if($total_PTR==0){
				$total_PTR='-';
			}
			if($total_PTC==0){
				$total_PTC='-';
			}
			if($total_PV==0){
				$total_PV='-';
			}
			echo "test5";
			// $table_data_not_star.="test7";	
			$table_data_not_star .=$total_PTD."\t".$total_PTR."\t".$total_PTC."\t".$total_PV."\t";
			
			$table_data_not_star .="\n";
			$count++;

	}
echo $table_data_not_star;
// $table_data_not_star .="Test7";

// print_r($table_data);
// $table_data_not_star.="test9";
// if($count_competitor_stock > 0){	
	
// 		// echo ucwords($header_star)."\n".$table_data_not_star;
// 		$excel_data = ucwords($header_star) . "\n" . $table_data_not_star;
		
// 		header("Content-type: application/octet-stream"); 
// 		header("Content-Disposition: attachment; filename=Market feedback price.xls"); 
// 		header("Pragma: no-cache"); 
// 		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header.
// 		echo $excel_data;
// 	}else{
// 		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
// 	}
	

mysqli_close($link);

?>

