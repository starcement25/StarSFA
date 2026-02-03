<?php
session_start();
ini_set('memory_limit', '999M');
set_time_limit(0);
require("adminUtils.php");

$emp_code = $_REQUEST['emp_code'];
$customer_Array = $_REQUEST['customer_Array'];

	
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$curr_date = date("jS_M_Y_h_m_s_A");
$curr_date_format=date('Y-m-d');
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));

$cur_month=date('m');
$cur_day=date('d');

$the_file_name = "tagging_data_".$curr_date.".xls";
$output = "";
$output .= "sl no"."\t"."Customer Code"."\t"."Customer Name"."\t"."Phone No"."\t"."Route"."\t"."Customer Type"."\t"."Branch"."\t"."Address"."\t\n";
$count=1;
foreach($customer_Array as $customer_code_val){	
  $sqlcustomerdetails="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,RM.route_name,CM.phone_no,CM.cust_type,CM.base_latt,
						CM.base_longi,CM.address,BM.branch_name FROM customer_master CM,route_master RM,branch_master BM
						WHERE CM.route_code=RM.route_code 
						AND CM.branch_code=BM.branch_code AND CM.customer_code='".$customer_code_val."'";					
	$rescustomerdetails = mysqli_query($link,$sqlcustomerdetails);
	$totalcustomerdetails = mysqli_num_rows($rescustomerdetails);


while ($row1 = mysqli_fetch_assoc($rescustomerdetails)) {
$dns_customer_code = $row1["dns_customer_code"];
$customer_name = $row1["customer_name"];
	
$phone_no = $row1["phone_no"];
$route_name = $row1["route_name"];
$cust_type = $row1["cust_type"];
$branch_name = $row1["branch_name"];
$address = $row1["address"];

$output .= $count."\t".$dns_customer_code."\t".$customer_name."\t".$phone_no."\t".$route_name."\t".$cust_type."\t".$branch_name."\t".$address."\t"."\n";
$count++;
}
}

// Download the file

$filename = $the_file_name;

$fp = fopen("/home/acedns/public_html/warehouse/$filename","wb");
fwrite($fp,$output);
fclose($fp);
echo $filename;

/*header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;*/
exit;
mysqli_close($conn);
?>