<?php 
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);
 session_start();
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
date_default_timezone_set("Asia/Kolkata");

$lead_generation_master = "lead_generation_master";


$transid=$_POST['trans_id'];
 //echo $transid;
 
 $destination       =$_POST['destination'];
 $company_constraint=$_POST['company_constraint'];
 $reason            =$_POST['reason'];
 $nov               =$_POST['nov'];
 $incoterms         =$_POST['incoterms'];
 $serving_location  =$_POST['serving_location'];
 $quoted_price      =$_POST['quoted_price'];
 $tpc               =$_POST['tpc'];
 $payment           =$_POST['payment'];
 $last_price        =$_POST['last_price'];
 $prev_last_price   =$_POST['prev_last_price'];

 $lead_emp_code     =$_POST['lead_emp_code'];
 $lead_party_name   =$_POST['lead_party_name'];
 $lead_date         =$_POST['lead_date'];
 $lead_exist        =$_POST['lead_exist'];
 $product_packaging =$_POST['product_packaging'];
 $qty_req           =$_POST['qty_req'];


// $aa=$_POST['aa'];
// $bb=$_POST['bb'];
// $cc=$_POST['cc'];
// $dd=$_POST['dd'];
// $ee=$_POST['ee'];
// $ff=$_POST['ff'];
// $gg=$_POST['gg'];
// $hh=$_POST['hh'];

// $ii=$_POST['ii'];
// $jj=$_POST['jj'];
// $kk=$_POST['kk'];
// $ll=$_POST['ll'];
// $mm=$_POST['mm'];

$dates = date("Y-m-d H:i:s");

 //echo $aa;
// $sql="update $lead_generation_master set `sales_org`='$aa',`division`='$bb',`distribution_channel`='$cc',`document_type`='$dd' ,`customer_reference_no`='$ee' ,`customer_reference_date`='$ff',`valid_to_date`='$gg',`material_number`='$hh',`sold_to_party`='$ii',`ship_to_party`='$jj',`PO_method`='$kk',`quotation_provided`='$ll',`quotation_provided_date`='$mm',`mis_submission_date`='$dates' where `lead_generation_id`='$transid'";

$sql="update $lead_generation_master set `destination`='$destination',`company_constraint`='$company_constraint', `reason`='$reason', `nov`='$nov', `incoterms`='$incoterms', `serving_location`='$serving_location', `quoted_price`='$quoted_price', `tpc`='$tpc', `payment`='$payment', `last_price`='$last_price', `prev_last_price`='$prev_last_price',`mis_submission_date`='$dates' where `lead_generation_id`='$transid'";

/*$sql="update $lead_generation_master set `sales_org`='$aa' where `lead_generation_id`='$transid'";*/
//echo $sql;

mysqli_query($link,$sql);

// Insert log data while update it added on 15.04.2025
$sql_insert="INSERT INTO lead_input_sheet SET `date`='$lead_date',`sl_no`='$lead_emp_code',`unique_no`='$transid',`customer_name`='$lead_party_name',`existing_type`='$lead_exist',`destination`='$destination',`company_constraint`='$company_constraint', `reason`='$reason', `nov`='$nov', `incoterms`='$incoterms', `serving_location`='$serving_location', `quoted_price`='$quoted_price', `tpc`='$tpc', `payment`='$payment', `last_price`='$last_price', `prev_last_price`='$prev_last_price', `product`='$product_packaging', `quantity`='$qty_req', `status`='1', `created_at`='$dates'";
mysqli_query($link,$sql_insert);




echo json_encode($transid);

?>