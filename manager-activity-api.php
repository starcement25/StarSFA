<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$nick_name=$_REQUEST['nick_name'];
/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(updatetime) > UNIX_TIMESTAMP('".$last_update_time."') ";
}*/
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$currentdate=$year.'-'.$month.'-'.$date;
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$currentdate=$_REQUEST['dateval'];
$current_time=$_REQUEST['timeval'] ?? '';
$currentdatetime =$year.'-'.$month.'-'.$date.' '.$current_time;

$date_string = $currentdate . ' ' . $current_time;
$date_timestamp = strtotime($date_string);

// if(employeewise_hierarchy=='yes'){
// 	$employee_hierarchy=return_employee_hierarchy($emp_code);
// 	$emp_val_rds=' AND (LO.emp_code IN('.$employee_hierarchy.'))';
// }
// else
// {
// 	$emp_val_rds=" AND emp_code='".$emp_code."'";
// }
// $sqllocation = "SELECT EM.emp_code,EM.emp_name,DATE_FORMAT(LO.date,'%T') as att_time FROM employee_master EM INNER  JOIN location LO 
// 			    ON LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,14),'%Y-%m-%d %H:%i:%s') 
// 				<='".$currentdatetime."' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')='".$currentdate."'
// 				".$emp_val_rds." ORDER BY EM.emp_name ASC";
				
				
// $resultlocation = mysqli_query($link,$sqllocation);
// $count=mysqli_num_rows($resultlocation);
// 	if($count>0){
// 		$contentsrowcolumn=$count.'¥'.'4';
// 		while($rowlocation = mysqli_fetch_assoc($resultlocation))
// 		{
// 		   $emp_code_lower=$rowlocation['emp_code'];
// 		   $emp_name=$rowlocation['emp_name'];
// 		   ${att_time.$emp_code_lower}=$rowlocation['att_time'];

// 		   if(strtoupper($nick_name)=='STAR' || strtoupper($nick_name)=='START')
// 		   {
// 		   	$sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM customer_visit_details WHERE emp_code='".$emp_code_lower."' 
// 							AND DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') <='".$currentdatetime."' AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')='".$currentdate."'";
// 		   }
// 		   else
// 		   {
// 			  $sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM prev_order_counting_master WHERE 
// 			  					SUBSTRING(order_no,-19,5)='".$emp_code_lower."' 
// 							AND DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') <='".$currentdatetime."' AND DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')='".$currentdate."'";
// 		   }
// 		   $rscustomervisit=mysqli_query($link,$sqlcustomervisit);
// 		   $rowcustomervisit=mysqli_fetch_assoc($rscustomervisit);
// 		   $total_customer_visit=$rowcustomervisit['tot_customer_visit'];
$emp_code_lower = strtolower($emp_code);

if (strtoupper($nick_name) == 'SHAKTI' || strtoupper($nick_name) == 'CORAL') {
    $cust_order_sql = "SELECT `trans_id` FROM `location` WHERE `emp_code` = '$emp_code' AND `date` LIKE '$currentdate%'";
    
    $cust_query = mysqli_query($link,$cust_order_sql);
    echo $cust_order_sql;
    // Initialize arrays to store values for URL and contents
    $urlValues = array();
    $contentsArray = array();

while ($cust_row = mysqli_fetch_assoc($cust_query)) {
    $cust_trans_id = $cust_row['trans_id'];
    // echo $cust_trans_id."<br/>";
    if (substr($cust_trans_id, 0, 3) == 'NOE' || substr($cust_trans_id, 0, 2) == 'OE') {
        $customer_code_sql = "SELECT `customer_code` FROM `order_header` WHERE `order_no` = '$cust_trans_id'";
        // echo $customer_code_sql;
        $customer_query = mysqli_query($link,$customer_code_sql);

        if (!$customer_query) {
            die('Error in customer_code query: ' . mysqli_error());
        }

        $customer_code_row = mysqli_fetch_assoc($customer_query);
        $customer_code = $customer_code_row['customer_code'];
        // echo "cust_trans_id= ".$cust_trans_id."<br/>";
        $cust_order_sql = "SELECT * FROM `order_details` WHERE `order_no`='$cust_trans_id'";
        // echo $cust_order_sql;
        $cust_order_query = mysqli_query($link,$cust_order_sql);

        if (!$cust_order_query) {
            die('Error in cust_order query: ' . mysqli_error());
        }

        $cust_order_row = mysqli_fetch_assoc($cust_order_query);
        $cust_order_qty = $cust_order_row['qty'];
        // echo "cust_order_qty= ".$cust_order_qty."<br/>";
        $cust_order_amount = $cust_order_row['amount'];
        // echo "cust_order_amount= ".$cust_order_amount."<br/>";

        $cust_name_sql = "SELECT `customer_name` FROM `customer_master` WHERE `customer_code`='$customer_code'";
        $cust_name_query = mysqli_query($link,$cust_name_sql);

        if (!$cust_name_query) {
            die('Error in cust_name query: ' . mysqli_error());
        }

        $cust_name_row = mysqli_fetch_assoc($cust_name_query);
        $customer_name = $cust_name_row['customer_name'];
        // echo $customer_name."<br/>";
        $contents = (($emp_code_lower != '') ? $emp_code_lower : ' ') . "^";
        $contents .= (($customer_name != '') ? $customer_name : ' ') . "^";
        $contents .= (($cust_order_qty != '') ? $cust_order_qty : ' ') . "^";
        $contents .= (($cust_order_amount != '') ? $cust_order_amount : ' ') . "^";
        $contents .= "\n";
        $contentsArray[] = $contents;
    }
}
    if (!empty($contentsArray)) {
        foreach ($contentsArray as $content) {
            $contentValues = explode("^", $content);
            // Construct the URL with arrays
            $url = "http://salesmpower.acedns.in/manager-activity-download-6.0.2.php?nick_name=$nick_name";

            $url .= "&emp_codes[]=" . urlencode($contentValues[0]);
            $url .= "&customer_names[]=" . urlencode($contentValues[1]);
            $url .= "&qtys[]=" . urlencode($contentValues[2]);
            $url .= "&amounts[]=" . urlencode($contentValues[3]);
    
            $urlValues[] = array(
                'emp_code' => $contentValues[0],
                'customer_name' => $contentValues[1],
                'qty' => $contentValues[2],
                'amount' => $contentValues[3],
            );
            
        }
        // echo $url;
        $datetime = gmdate('Y-m-d H:m:s', strtotime('+330 minute'));
        insertapilog($datetime, $emp_code, $url, $nick_name);
    
        header("Content-type: application/text");
        header("Content-Disposition: attachment; filename=manager-activity.txt");
        print implode("", $contentsArray);
    } else {
        $datacontents = '0' . '¥' . '0';
    }

}

?>
