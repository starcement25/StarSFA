<?php
ini_set('memory_limit', '2048M');
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
date_default_timezone_set("Asia/Calcutta");

define("SERVER","103.242.119.68");
define("SERVER_L","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("APICALLLOGURL","http://salesmpower.acedns.in");



/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");*/



//echo $sqlupdatelastoperationtime= date("Y-m-d H:i:s");//exit();

$nick_name="STAR";
//$nick_name="GOLDSTONE";
//$nick_name="GOLDSTONET";
//$nick_name="ABDOS";
//$nick_name="ABDOST";
//$nick_name="SHAKTI";
//$nick_name="SUPERSHAKTI";
//$nick_name="CORAL";



$ddb = "acedns_".$nick_name;

define("DB","$ddb");

	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	
	
	$linkL=mysqli_connect(SERVER_L,USER,PASSWORD,DB) or die("Database Connection Error.");

		/*$linksetup=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#","acedns_acednsproduct") or die("Setup Database Connection Error.");*/


$table_name = "competitor_pricing";

$table_name = "branch_destination_freight";
$table_name = "customer_master";

$table_name = "attendence";

$table_name = "location";

//$table_name = "route_plan";

//$table_name = "stock_audit";

$table_name = "survey_output";

//$table_name = "survey_header";

$table_name = "check_in_out_details";

$table_name = "competitor_pricing";

$table_name = "competitor_stock";

$table_name = "customer_visit_details";

$table_name = "facilitator_master";

$table_name = "facilitator_master";

$table_name = "market_feedback";

$table_name = "mf_stk_audit_details";

$table_name = "mf_stk_audit_header";

$table_name = "mis_details_emp_datewise";

$table_name = "notification_ack_relation";

$table_name = "notification_master";

//$table_name = "orderdata";

//$table_name = "order_details";

//$table_name = "order_header";

//$table_name = "route_master";

//$table_name = "site_lead_conversion_master";

//$table_name = "survey_output_test";

//$table_name = "t_att_checkout_info";

//$table_name = "yellow_card_details";

$table_name = "attendence";


$table_date="2024-01-08";
 
 /////////////////////////////////////////////////location////////////////////



if($table_name=="location"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;


if($columns_total>0){
    
    $count=0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['trans_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE trans_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		//For update the location table for existing trans id for order
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $purpose_of_visit = addslashes($row_data['purpose_of_visit']);
    		    
    		    $sql_insert = "INSERT INTO `location`(`emp_code`, `trans_id`, `date`, `updatetime`, `latt`, `longi`, `TA_DA_mode`, `transferred`, `purpose_of_visit`) VALUES (
    		        '".$row_data['emp_code']."',
    		        '".$trans_id."',
    		        '".$row_data['date']."',
    		        '".$row_data['updatetime']."',
    		        '".$row_data['latt']."',
    		        '".$row_data['longi']."',
    		        '".$row_data['TA_DA_mode']."',
    		        '".$row_data['transferred']."',
    		        '".$purpose_of_visit."'
    		        )";
    		        
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		       echo $sql_insert.";";// exit();
    		        $count=$count+1;
    		}
		    

		}		

}

echo $count;

}


/////////////////////////////////////attendence//////////////




if($table_name=="attendence"){
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";


$sql="SELECT * FROM $table_name";
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    
		    //$sql1="SET SQL_MODE='ALLOW_INVALID_DATES'";
		    
		    //$rs1=mysqli_query($link,$sql1);
		    
		        $date_zero = $row_data['date'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1971-01-01 0:00:10";
    		    }
    		    
    		    
    		    $trans_id = $row_data['trans_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE emp_code='".$row_data['emp_code']."' AND trans_id='".$trans_id."' AND date='".$date_zero."' AND transferred='".$row_data['transferred']."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		echo $countchkorlocation."-";
    		//For update the location table for existing trans id for order
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    
		      
    		    $sql_insert = "INSERT INTO `attendence`(`emp_code`, `trans_id`, `date`, `transferred`) VALUES (
    		        '".$row_data['emp_code']."',
    		        '".$row_data['trans_id']."',
    		        '".$date_zero."',
    		        '".$row_data['transferred']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		}      
    		        
    	}
		    
}
echo "attendence_total".$columns_total;
echo "\n";
echo "attendence".$count;
echo "\n";
echo "attendence_error".$count_r;
}



//////////////////////////////route_plan///////////////////////////////

if($table_name=="route_plan"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;


if($columns_total>0){
    
    $count=0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['route_plan_trans_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE route_plan_trans_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		//For update the route_plan_trans_id table for existing trans id for order
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $remarks = addslashes($row_data['remarks']);
    		    
    		    $sql_insert = "INSERT INTO `route_plan`(`route_plan_trans_id`, `emp_code`, `route_code`, `visit_date`, `remarks`, `distributor_code`, `status`, `working_with`, `create_date`, `update_date`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['route_code']."',
    		        '".$row_data['visit_date']."',
    		        '".$remarks."',
    		        '".$row_data['distributor_code']."',
    		        '".$row_data['status']."',
    		        '".$row_data['working_with']."',
    		        '".$row_data['create_date']."',
    		        '".$row_data['update_date']."'
    		        )";
    		        
    		       // echo $sql_insert.";";// exit();
    		        
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "route_plan".$count;

}


//////////////////////////////stock_audit///////////////////////////////

if($table_name=="stock_audit"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;


if($columns_total>0){
    
    $count=0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['transaction_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE transaction_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $remarks = addslashes($row_data['remarks']);
    		    $hint_remarks = addslashes($row_data['hint_remarks']);
    		    
    		    $sql_insert = "INSERT INTO `stock_audit`(`transaction_id`, `customer_code`, `product_code`, `quantity`, `product_mrp`, `product_details`, `remarks`, `hint_remarks`, `weightage`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['product_code']."',
    		        '".$row_data['quantity']."',
    		        
    		        '".$row_data['product_mrp']."',
    		        '".$row_data['product_details']."',
    		        '".$remarks."',
    		        '".$hint_remarks."',
    		        '".$row_data['weightage']."'
    		        
    		        )";
    		        
    		        echo $sql_insert.";";// exit();
    		        
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "stock_audit".$count;

}



//////////////////////////////survey_output///////////////////////////////

if($table_name=="survey_output"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;


if($columns_total>0){
    
    $count=0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['survey_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE survey_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    
    		    $sql_insert = "INSERT INTO `survey_output`(`survey_id`, `row_id`, `action_id`, `value`, `type`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['row_id']."',
    		        '".$row_data['action_id']."',
    		        '".$value."',
    		        '".$row_data['type']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "survey_output".$count;

}


/////////////survey_output_test//////////////////////////

if($table_name=="survey_output_test"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;

$count=0;
if($columns_total>0){
    
    $count=0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['survey_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE survey_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    
    		    $sql_insert = "INSERT INTO `survey_output`(`survey_id`, `row_id`, `action_id`, `value`, `type`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['row_id']."',
    		        '".$row_data['action_id']."',
    		        '".$value."',
    		        '".$row_data['type']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "survey_output".$count;

}

//////////////////////////////survey_header///////////////////////////////

if($table_name=="survey_header"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);

//$row_data = mysqli_fetch_assoc($rs);
		$columns_total=mysqli_num_rows($rs);
//$columns_total = mysqli_num_fields($rs);

//echo "-total-".$columns_total;


if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['survey_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE survey_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    $date_zero = $row_data['PO_submitted_date'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }
    		    
    		    $sql_insert = "INSERT INTO `survey_header`(`verified_id`, `survey_audit_id`, `survey_id`, `survey_type`, `menu_name`, `mall_id`, `mall_hs_name`, `business_name`, `contact_name`, `phone_no`, `time_9_to_12`, `time_12_to_3`, `time_3_to_6`, `time_6_to_9`, `questions_answered`, `status`, `route_code`, `download_time`, `active`, `check_in_time`, `transferred_flag`, `PO_no`, `PO_submitted_by`, `PO_submitted_date`, `status_updated_by`, `status_updated_datetime`, `actual_date_delivery`, `delivery_remarks`, `reason_not_delivery`) VALUES (
    		        '".$row_data['verified_id']."',
    		        '".$row_data['survey_audit_id']."',
    		        '".$trans_id."',
    		        '".$row_data['survey_type']."',
    		        '".$row_data['menu_name']."',
    		        '".$row_data['mall_id']."',
    		        '".$row_data['mall_hs_name']."',
    		        '".$row_data['business_name']."',
    		        '".$row_data['contact_name']."',
    		        '".$row_data['phone_no']."',
    		        '".$row_data['time_9_to_12']."',
    		        '".$row_data['time_12_to_3']."',
    		        '".$row_data['time_3_to_6']."',
    		        '".$row_data['time_6_to_9']."',
    		        '".addslashes($row_data['questions_answered'])."',
    		        '".$row_data['status']."',
    		        '".$row_data['route_code']."',
    		        '".$row_data['download_time']."',
    		        '".$row_data['active']."',
    		        '".$row_data['check_in_time']."',
    		        '".$row_data['transferred_flag']."',
    		        
    		        '".$row_data['PO_no']."',
    		        '".$row_data['PO_submitted_by']."',
    		        '".$date_zero."',
    		        '".$row_data['status_updated_by']."',
    		        '".$row_data['status_updated_datetime']."',
    		        '".$row_data['actual_date_delivery']."',
    		        '".addslashes($row_data['delivery_remarks'])."',
    		        '".addslashes($row_data['reason_not_delivery'])."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "survey_head".$count;
echo "\n";
echo "survey_head_error".$count_r;

}

///////////////////////////////check_in_out_details////////////////////////////

if($table_name=="check_in_out_details"){

$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['trans_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE trans_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    $date_zero = $row_data['check_in_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }
    		    
    		    $sql_insert = "INSERT INTO `check_in_out_details`(`trans_id`, `check_in_time`, `customer_code`, `check_out_time`, `product_tagging`, `hint_remarks`, `remarks`, `uploaded_photo`) VALUES (
    		        '".$trans_id."',
    		        '".$date_zero."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['check_out_time']."',
    		        '".$row_data['product_tagging']."',
    		        '".addslashes($row_data['hint_remarks'])."',
    		        '".addslashes($row_data['remarks'])."',
    		        '".addslashes($row_data['uploaded_photo'])."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "check_in_out_details".$count;
echo "\n";
echo "check_in_out_details_error".$count_r;

}


////////////////////////////competitor_stock///////////////////////

if($table_name=="competitor_stock"){

$sql="SELECT * FROM $table_name";

/* where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['sl'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE sl='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    $date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }
    		    
    		    $sql_insert = "INSERT INTO `competitor_stock`(`sl`, `emp_code`, `customer_code`, `star`, `ambuja`, `ultratech`, `lafarge`, `dalmia`, `topcem`, `acc`, `birla_gold`, `date_time`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['customer_code']."',
    		        '".addslashes($row_data['star'])."',
    		        '".addslashes($row_data['ambuja'])."',
    		        '".addslashes($row_data['ultratech'])."',
    		        '".addslashes($row_data['lafarge'])."',
    		        '".addslashes($row_data['dalmia'])."',
    		        '".addslashes($row_data['topcem'])."',
    		        '".addslashes($row_data['acc'])."',
    		        '".addslashes($row_data['birla_gold'])."',
    		        '".addslashes($row_data['date_time'])."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "competitor_stock".$count;
echo "\n";
echo "competitor_stock_error".$count_r;

}


////////////////////competitor_pricing////////////////////////////


if($table_name=="competitor_pricing"){

$sql="SELECT * FROM $table_name";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['sl'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE sl='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    $date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }
    		    
    		    $sql_insert = "INSERT INTO `competitor_pricing`(`sl`, `emp_code`, `customer_code`, `star_PTD`, `star_PTR`, `star_PTC`, `star_PV`, `ambuja_PTD`, `ambuja_PTR`, `ambuja_PTC`, `ambuja_PV`, `ultratech_PTD`, `ultratech_PTR`, `ultratech_PTC`, `ultratech_PV`, `lafarge_PTD`, `lafarge_PTR`, `lafarge_PTC`, `lafarge_PV`, `dalmia_PTD`, `dalmia_PTR`, `dalmia_PTC`, `dalmia_PV`, `topcem_PTD`, `topcem_PTR`, `topcem_PTC`, `topcem_PV`, `acc_PTD`, `acc_PTR`, `acc_PTC`, `acc_PV`, `birla_gold_PTD`, `birla_gold_PTR`, `birla_gold_PTC`, `birla_gold_PV`, `date_time`) VALUES (
    		        '".$trans_id."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['star_PTD']."',
    		        '".$row_data['star_PTR']."',
    		        '".$row_data['star_PTC']."',
    		        '".$row_data['star_PV']."',
    		        '".$row_data['ambuja_PTD']."',
    		        '".$row_data['ambuja_PTR']."',
      		        '".$row_data['ambuja_PTC']."',
    		        '".$row_data['ambuja_PV']."',
    		        '".$row_data['ultratech_PTD']."',
    		        '".$row_data['ultratech_PTR']."',
      		        '".$row_data['ultratech_PTC']."',
    		        '".$row_data['ultratech_PV']."',
    		        '".$row_data['lafarge_PTD']."',
    		        '".$row_data['lafarge_PTR']."',
      		        '".$row_data['lafarge_PTC']."',
    		        '".$row_data['lafarge_PV']."',
    		        
    		        '".$row_data['dalmia_PTD']."',
    		        '".$row_data['dalmia_PTR']."',
      		        '".$row_data['dalmia_PTC']."',
    		        '".$row_data['dalmia_PV']."',
    		        
    		        '".$row_data['topcem_PTD']."',
    		        '".$row_data['topcem_PTR']."',
      		        '".$row_data['topcem_PTC']."',
    		        '".$row_data['topcem_PV']."',
    		        
    		        '".$row_data['acc_PTD']."',
    		        '".$row_data['acc_PTR']."',
      		        '".$row_data['acc_PTC']."',
    		        '".$row_data['acc_PV']."',
    		        
    		        '".$row_data['birla_gold_PTD']."',
    		        '".$row_data['birla_gold_PTR']."',
      		        '".$row_data['birla_gold_PTC']."',
    		        '".$row_data['birla_gold_PV']."',
    		        
    		        '".$row_data['date_time']."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "competitor_pricing".$count;
echo "\n";
echo "competitor_pricing_error".$count_r;

}



//////////////////////customer_visit_details//////////////////////////

if($table_name=="customer_visit_details"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['trans_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE trans_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    $hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `customer_visit_details`(`row_id`, `emp_code`, `trans_id`, `customer_code`, `customer_name`, `cust_type`, `route_code`, `route_name`, `rds_tag`, `hint_remarks`) VALUES (
    		        '".$row_data['row_id']."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['trans_id']."',
    		        '".$row_data['customer_code']."',
    		        
    		        '".addslashes($row_data['customer_name'])."',
    		        '".addslashes($row_data['cust_type'])."',
    		        '".$row_data['route_code']."',
    		        '".$row_data['route_name']."',
    		        '".$row_data['rds_tag']."',
      		        '".$hint_remarks."'

    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "customer_visit_details".$count;
echo "\n";
echo "customer_visit_details_error".$count_r;

}


/////////////customer_master///////////////////////////

if($table_name=="customer_master"){

$sql="SELECT * FROM $table_name ";
/*$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['customer_code'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE customer_code='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `customer_master`(`customer_code`, `dns_customer_code`, `customer_name`, `address`, `pin`, `phone_no`, `landline_no`, `route_code`, `emp_code`, `current_balance`, `credit_limit`, `credit_days`, `acedns`, `black_list`, `TD`, `cust_type`, `rds_tag`, `sauda_validity_period`, `owner_name`, `owner_phone`, `cust_class`, `weekly_closing_day`, `coverage_type`, `TIN`, `PAN`, `district`, `zone`, `image`, `download_time`, `download_time_credit_limit`, `vertical_value`, `branch_code`, `minimum_stock`, `bank_name`, `bank_account_number`, `email`, `visit_day`, `state_code`, `monthly_potential`, `sauda_limit`, `incoterms`, `transport_mode`, `pending_qty`, `loadability_ton`, `sauda_type`, `visit_sequence`, `activated`, `activated_customer_code`, `retailer_app`, `base_latt`, `base_longi`, `activated_datetime`, `activated_by`, `appointment_date`, `date_of_birth`, `date_of_anniversary`, `whatsapp_no`, `beneficiary_name`, `IFS_code`, `GST_type`, `need_location_update`, `category_of_store`, `instore_activity`, `is_new_customer`, `owner_image`, `firm_name`, `firm_image`, `GST_image`, `aadhar`, `aadhar_image`, `spouse_birth_date`, `is_nlp`, `cluster`, `tagging_order`, `cust_class_update_time`, `cust_class_update_by`) VALUES (
    		        '".$row_data['customer_code']."',
    		        '".$row_data['dns_customer_code']."',
    		        '".$row_data['customer_name']."',
    		        '".$row_data['address']."',
    		        '".$row_data['pin']."',
    		        '".$row_data['phone_no']."',
    		        '".$row_data['landline_no']."',
    		        '".$row_data['route_code']."',
    		        '".$row_data['emp_code']."',
      		        '".$row_data['current_balance']."',
    		        '".$row_data['credit_limit']."',
    		        '".$row_data['credit_days']."',
    		        '".$row_data['acedns']."',
    		        '".$row_data['black_list']."',
    		        '".$row_data['TD']."',
    		        '".$row_data['cust_type']."',
    		        '".$row_data['rds_tag']."',
    		        '".$row_data['sauda_validity_period']."',
    		         '".$row_data['owner_name']."',
    		        '".$row_data['owner_phone']."',
    		        '".$row_data['cust_class']."',
    		        '".$row_data['weekly_closing_day']."',
    		        '".$row_data['coverage_type']."',
    		        '".$row_data['TIN']."',
    		        '".$row_data['PAN']."',
    		        '".$row_data['district']."',
    		        '".$row_data['zone']."',
      		        '".$row_data['image']."',
    		        '".$row_data['download_time']."',
    		        '".$row_data['download_time_credit_limit']."',
    		        '".$row_data['vertical_value']."',
    		        '".$row_data['branch_code']."',
    		        '".$row_data['minimum_stock']."',
    		        '".$row_data['bank_name']."',
    		        '".$row_data['bank_account_number']."',
    		        '".$row_data['email']."',
    		        '".$row_data['visit_day']."',
    		        '".$row_data['state_code']."',
    		        '".$row_data['monthly_potential']."',
    		        '".$row_data['sauda_limit']."',
    		        '".$row_data['incoterms']."',
    		        '".$row_data['transport_mode']."',
    		        '".$row_data['pending_qty']."',
    		        '".$row_data['loadability_ton']."',
    		        '".$row_data['sauda_type']."',
      		        '".$row_data['visit_sequence']."',
    		        '".$row_data['activated']."',
    		        '".$row_data['activated_customer_code']."',
    		        '".$row_data['retailer_app']."',
    		        '".$row_data['base_latt']."',
    		        '".$row_data['base_longi']."',
    		        '".$row_data['activated_datetime']."',
    		        '".$row_data['activated_by']."',
    		        '".$row_data['appointment_date']."',
    		        '".$row_data['date_of_birth']."',
    		        '".$row_data['date_of_anniversary']."',
    		        '".$row_data['whatsapp_no']."',
    		        '".$row_data['beneficiary_name']."',
    		        '".$row_data['IFS_code']."',
    		        '".$row_data['GST_type']."',
    		        '".$row_data['need_location_update']."',
    		        '".$row_data['category_of_store']."',
    		        '".$row_data['instore_activity']."',
      		        '".$row_data['is_new_customer']."',
    		        '".$row_data['owner_image']."',
    		        '".$row_data['firm_name']."',
    		        '".$row_data['firm_image']."',
    		        '".$row_data['GST_image']."',
    		        '".$row_data['aadhar']."',
    		        '".$row_data['aadhar_image']."',
    		        '".$row_data['spouse_birth_date']."',
    		        '".$row_data['is_nlp']."',
                    '".$row_data['cluster']."',
    		        '".$row_data['tagging_order']."',
    		        '".$row_data['cust_class_update_time']."',
      		        '".$row_data['cust_class_update_by']."'
    		        

    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "customer_master".$count;
echo "\n";
echo "customer_master_error".$count_r;

}


////////////////////facilitator_master////////////////////

if($table_name=="facilitator_master"){

$sql="SELECT * FROM $table_name ";
/*$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['f_code'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE f_code='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `facilitator_master`(`f_code`, `dns_f_code`, `facilitator_name`, `emp_code`, `emp_name`, `f_type`, `designation`, `firm_name`, `f_address`, `f_pin`, `f_district`, `f_area`, `mobile_no`, `whatsapp_no`, `email_id`, `dob`, `annniversary`, `acedns`, `branch`, `download_time`) VALUES (
    		        '".$row_data['f_code']."',
    		        '".$row_data['dns_f_code']."',
    		        '".$row_data['facilitator_name']."',
    		        '".$row_data['emp_code']."',
    		        '".addslashes($row_data['emp_name'])."',
    		        '".addslashes($row_data['f_type'])."',
    		        '".$row_data['designation']."',
    		        '".$row_data['firm_name']."',
    		        '".$row_data['f_address']."',
      		        '".addslashes($row_data['f_pin'])."',
    		        '".addslashes($row_data['f_district'])."',
    		        '".$row_data['f_area']."',
    		        '".$row_data['mobile_no']."',
    		        '".$row_data['whatsapp_no']."',
      		        '".addslashes($row_data['email_id'])."',
    		        '".addslashes($row_data['dob'])."',
    		        '".$row_data['annniversary']."',
    		        '".$row_data['acedns']."',
    		        '".$row_data['branch']."',
    		        '".$row_data['download_time']."'

    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "facilitator_master".$columns_total;
echo "\n";
echo "facilitator_master_success".($count-$count_r);
echo "\n";
echo "facilitator_master_error".$count_r;

}


///////////////////////market_feedback/////////////////////

if($table_name=="market_feedback"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(market_feedback_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['market_feedback_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE market_feedback_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `market_feedback`(`sl_no`, `market_feedback_id`, `route_code`, `customer_code`, `product_group`, `competitor_name`, `PTD`, `PTR`, `PTC`, `PV`, `billing_ex_for`, `wsp_ex_for`, `rsp_ex_for`, `nod_ex_for`) VALUES (
    		        '".$row_data['sl_no']."',
    		        '".$row_data['market_feedback_id']."',
    		        '".$row_data['route_code']."',
    		        '".$row_data['customer_code']."',
    		        '".addslashes($row_data['product_group'])."',
    		        '".addslashes($row_data['competitor_name'])."',
    		        '".$row_data['PTD']."',
    		        '".$row_data['PTR']."',
    		        '".$row_data['PTC']."',
      		        '".addslashes($row_data['PV'])."',
    		        '".addslashes($row_data['billing_ex_for'])."',
    		        '".$row_data['wsp_ex_for']."',
    		        '".$row_data['rsp_ex_for']."',
    		        '".$row_data['nod_ex_for']."'

    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "market_feedback".$columns_total;
echo "\n";
echo "market_feedback_success".($count-$count_r);
echo "\n";
echo "market_feedback_error".$count_r;

}

////////////////////mf_stk_audit_details//////////////////////////

if($table_name=="mf_stk_audit_details"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['mf_stk_audit_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE mf_stk_audit_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `mf_stk_audit_details`(`mf_stk_audit_id`, `competitor_name`, `qty_mt`, `scheme_discount`) VALUES (
    		        '".$row_data['mf_stk_audit_id']."',
    		        '".$row_data['competitor_name']."',
    		        '".$row_data['qty_mt']."',
    		        '".$row_data['scheme_discount']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "mf_stk_audit_details".$columns_total;
echo "\n";
echo "mf_stk_audit_details_success".($count-$count_r);
echo "\n";
echo "mf_stk_audit_details_error".$count_r;

}

////////////////////////mf_stk_audit_header/////////////////


if($table_name=="mf_stk_audit_header"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(mf_stk_audit_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['mf_stk_audit_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE mf_stk_audit_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `mf_stk_audit_header`(`mf_stk_audit_id`, `customer_code`, `image`, `remarks`) VALUES (
    		        '".$row_data['mf_stk_audit_id']."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['image']."',
    		        '".$row_data['remarks']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "mf_stk_audit_header".$columns_total;
echo "\n";
echo "mf_stk_audit_header_success".($count-$count_r);
echo "\n";
echo "mf_stk_audit_header_error".$count_r;

}

//////////mis_details_emp_datewise/////////////////////////////



if($table_name=="mis_details_emp_datewise"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(operation_date,'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['sl_no'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE sl_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    //$value = addslashes($row_data['value']);
    		    //$hint_remarks = addslashes($row_data['hint_remarks']);
    		    /*$date_zero = $row_data['date_time'];
    		    if($date_zero=="0000-00-00 00:00:00"){
    		        $date_zero ="1970-01-01 01:00:00";
    		    }*/
    		    
    		    $sql_insert = "INSERT INTO `mis_details_emp_datewise`(`sl_no`, `operation_date`, `emp_code`, `present`, `customer_visited`, `order_received`, `no_transaction`, `stock_audit`, `kyc`, `brand_activity`, `technical_meet`, `site_visit`, `market_feedback`) VALUES (
    		        '".$row_data['sl_no']."',
    		        '".$row_data['operation_date']."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['present']."',
    		        '".$row_data['customer_visited']."',
    		        '".$row_data['order_received']."',
    		        '".$row_data['no_transaction']."',
    		        '".$row_data['stock_audit']."',
    		        '".$row_data['kyc']."',
    		        '".$row_data['brand_activity']."',
    		        '".$row_data['technical_meet']."',
    		        '".$row_data['site_visit']."',
    		        '".$row_data['market_feedback']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "mis_details_emp_datewise".$columns_total;
echo "\n";
echo "mis_details_emp_datewise_success".($count-$count_r);
echo "\n";
echo "mis_details_emp_datewise_error".$count_r;

}

////////////////notification_ack_relation///////////////////////


if($table_name=="notification_ack_relation"){

$sql="SELECT * FROM $table_name ";
/*$sql="SELECT * FROM $table_name where DATE_FORMAT(operation_date,'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['notification_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE notification_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $sql_insert = "INSERT INTO `notification_ack_relation`(`notification_id`, `receiver_id`, `ack_id`) VALUES (
    		        '".$row_data['notification_id']."',
    		        '".$row_data['receiver_id']."',
    		        '".$row_data['ack_id']."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "notification_ack_relation".$columns_total;
echo "\n";
echo "notification_ack_relation_success".($count-$count_r);
echo "\n";
echo "notification_ack_relation_error".$count_r;

}

///////////////////notification_master//////////////

if($table_name=="notification_master"){

$sql="SELECT * FROM $table_name ";
/*$sql="SELECT * FROM $table_name where DATE_FORMAT(operation_date,'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['sl_no'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE sl_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $sql_insert = "INSERT INTO `notification_master`(`sl_no`, `notification_id`, `type_of_notification`, `sender_id`, `message`, `transferred`) VALUES (
    		        '".$row_data['sl_no']."',
    		        '".$row_data['notification_id']."',
    		        '".$row_data['type_of_notification']."',
    		        
    		        '".$row_data['sender_id']."',
    		        '".$row_data['message']."',
    		        '".$row_data['transferred']."',
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "notification_master".$columns_total;
echo "\n";
echo "notification_master_success".($count-$count_r);
echo "\n";
echo "notification_master_error".$count_r;

}


/////////orderdata//////////////

if($table_name=="orderdata"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(insertdatetime,'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $sql_insert = "INSERT INTO `orderdata`(`id`, `data`, `insertdatetime`) VALUES (
    		        '".$row_data['id']."',
    		        '".$row_data['data']."',
    		        '".$row_data['insertdatetime']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "orderdata".$columns_total;
echo "\n";
echo "orderdata_success".($count-$count_r);
echo "\n";
echo "orderdata_error".$count_r;

}


/////////////////order_details////////////////

if($table_name=="order_details"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['order_no'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE order_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    $sql_insert = "INSERT INTO `order_details`(`order_no`, `sku_code`, `qty`, `mrp_code`, `TD`, `premium`, `VAT`, `sale_rate`, `freight_charge`, `UOM`, `amount`, `scheme_type`, `weightage`, `input_size`, `remarks`, `purpose_of_visit`) VALUES (
    		        '".$row_data['order_no']."',
    		        '".$row_data['sku_code']."',
    		        '".$row_data['qty']."',
    		        '".$row_data['mrp_code']."',
    		        '".$row_data['TD']."',
    		        '".$row_data['premium']."',
    		        '".$row_data['VAT']."',
    		        '".$row_data['sale_rate']."',
    		        '".$row_data['freight_charge']."',
    		        '".$row_data['UOM']."',
    		        '".$row_data['amount']."',
    		        '".$row_data['scheme_type']."',
    		        '".$row_data['weightage']."',
    		        '".$row_data['input_size']."',
    		        '".$row_data['remarks']."',
    		        '".$row_data['purpose_of_visit']."'
    		        
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "order_details".$columns_total;
echo "\n";
echo "order_details_success".($count-$count_r);
echo "\n";
echo "order_details_error".$count_r;

}

/////////////////order_header//////////

if($table_name=="order_header"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') >='".$table_date."'";
echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['order_no'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE order_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		        $is_printed = $row_data['is_printed'];
    		        if($is_printed==""){
    		            $is_printed = "no";
    		        }
    		        $reason = $row_data['reason'];
    		        if($reason==""){
    		            $reason = "NULL";
    		        }
    		        $status = $row_data['status'];
    		         if($status==""){
    		            $status = "NULL";
    		        }
    		        
    		        
    		    
    		    $sql_insert = "INSERT INTO `order_header`(`order_no`, `customer_code`, `branch_code`, `destination_code`, `vertical_value`, `d_instruction`, `hint_remarks`, `sale_type`, `order_type`, `order_value`, `TD`, `tag_distributor_code`, `transaction_type`, `GST_type`, `VAT`, `freight_component`, `freight_component_value`, `transferred`, `price_validation_type`, `is_printed`, `reason`, `status`) VALUES (
    		        '".$row_data['order_no']."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['branch_code']."',
    		        '".$row_data['destination_code']."',
    		        '".$row_data['vertical_value']."',
    		        '".$row_data['d_instruction']."',
    		        '".$row_data['hint_remarks']."',
    		        '".$row_data['sale_type']."',
    		        '".$row_data['order_type']."',
    		        '".$row_data['order_value']."',
    		        '".$row_data['TD']."',
    		        '".$row_data['tag_distributor_code']."',
    		        '".$row_data['transaction_type']."',
    		        '".$row_data['GST_type']."',
    		        '".$row_data['VAT']."',
    		        '".$row_data['freight_component']."',
    		        '".$row_data['freight_component_value']."',
    		        '".$row_data['transferred']."',
    		        '".$row_data['price_validation_type']."',
    		        '".$is_printed."',
    		        '".$reason."',
    		        '".$status."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "order_header".$columns_total;
echo "\n";
echo "order_header_success".($count-$count_r);
echo "\n";
echo "order_header_error".$count_r;

}

//////////////////route_master/////////////

if($table_name=="route_master"){

$sql="SELECT * FROM $table_name ";
/*$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') >='".$table_date."'";*/
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['route_code'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE route_code='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    $sql_insert = "INSERT INTO `route_master`(`route_code`, `dns_route_code`, `route_name`, `emp_code`, `branch_code`, `cluster`, `vertical_value`, `area`, `route_no`, `download_time`, `flag`) VALUES (
    		        '".$row_data['route_code']."',
    		        '".$row_data['dns_route_code']."',
    		        '".$row_data['route_name']."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['branch_code']."',
    		        '".$row_data['cluster']."',
    		        '".$row_data['vertical_value']."',
    		        '".$row_data['area']."',
    		        '".$row_data['route_no']."',
    		        '".$row_data['download_time']."',
    		        '".$row_data['flag']."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "route_master".$columns_total;
echo "\n";
echo "route_master_success".($count-$count_r);
echo "\n";
echo "route_master_error".$count_r;

}

/////////////////site_lead_conversion_master/////////////////////

if($table_name=="site_lead_conversion_master"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(site_lead_conversion_id,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['site_lead_conversion_id'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE site_lead_conversion_id='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    $sql_insert = "INSERT INTO `site_lead_conversion_master`(`site_lead_conversion_id`, `emp_code`, `customer_name`, `branch`, `district`, `customer_contact_no`, `full_address`, `petty_contractor_head_mason_name`, `petty_contractor_head_mason_contact_no`, `engineer_name`, `engineer_contact_no`, `engineer_regd_in_star_stellar`, `site_segment`, `visit_type`, `project_segment`, `type_of_construction`, `site_potential_no_of_bags`, `current_stage_of_construction`, `cement_brand_used`, `other_brand`, `consumed_till_date_no_of_bags`, `estimated_requirement_no_of_bags`, `meeting_person`, `decision_maker`, `conversion`, `product`, `requested_date_of_delivery`, `no_of_bags_ordered`, `lead_forwarded_dealer_rssd_name`, `actual_date_of_delivery`, `reason_for_not_delivery`, `reasons_for_non_conversion`, `other_ remarks`, `overall_remarks`, `price_rsp_bags`, `download_time`) VALUES (
    		        '".$row_data['site_lead_conversion_id']."',
    		        '".$row_data['emp_code']."',
    		        '".$row_data['customer_name']."',
    		        '".$row_data['branch']."',
    		        '".$row_data['district']."',
    		        '".$row_data['customer_contact_no']."',
    		        '".$row_data['full_address']."',
    		        '".$row_data['petty_contractor_head_mason_name']."',
    		        '".$row_data['petty_contractor_head_mason_contact_no']."',
    		        '".$row_data['engineer_name']."',
    		        '".$row_data['engineer_contact_no']."',
    		        '".$row_data['engineer_regd_in_star_stellar']."',
    		        '".$row_data['site_segment']."',
    		        '".$row_data['visit_type']."',
    		        '".$row_data['project_segment']."',
    		        '".$row_data['type_of_construction']."',
    		        '".$row_data['site_potential_no_of_bags']."',
    		        '".$row_data['current_stage_of_construction']."',
    		        '".$row_data['cement_brand_used']."',
    		        '".$row_data['other_brand']."',
    		        '".$row_data['consumed_till_date_no_of_bags']."',
    		        '".$row_data['estimated_requirement_no_of_bags']."',
    		        '".$row_data['meeting_person']."',
    		        '".$row_data['decision_maker']."',
    		        '".$row_data['conversion']."',
    		        '".$row_data['product']."',
    		        '".$row_data['requested_date_of_delivery']."',
    		        '".$row_data['no_of_bags_ordered']."',
    		        '".$row_data['lead_forwarded_dealer_rssd_name']."',
    		        '".$row_data['actual_date_of_delivery']."',
    		        '".$row_data['reason_for_not_delivery']."',
    		        '".$row_data['reasons_for_non_conversion']."',
    		        '".$row_data['other_ remarks']."',
    		        '".$row_data['overall_remarks']."',
    		         '".$row_data['price_rsp_bags']."',
    		        '".$row_data['download_time']."'
    		        
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "site_lead_conversion_master".$columns_total;
echo "\n";
echo "site_lead_conversion_master_success".($count-$count_r);
echo "\n";
echo "site_lead_conversion_master_error".$count_r;

}


///////////////t_att_checkout_info////////////////////////////////

if($table_name=="t_att_checkout_info"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(Entry_Date,'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['Emp_Id'];
		    $Entry_Date = $row_data['Entry_Date'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE Emp_Id='".$trans_id."' AND Entry_Date='".$Entry_Date."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    $sql_insert = "INSERT INTO `t_att_checkout_info`(`Emp_Id`, `Emp_Name`, `Entry_Date`, `CheckIN`, `CheckOUT`)  VALUES (
    		        '".$row_data['Emp_Id']."',
    		        '".$row_data['Emp_Name']."',
    		        '".$row_data['Entry_Date']."',
    		        '".$row_data['CheckIN']."',
    		        '".$row_data['CheckOUT']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "t_att_checkout_info".$columns_total;
echo "\n";
echo "t_att_checkout_info_success".($count-$count_r);
echo "\n";
echo "t_att_checkout_info_error".$count_r;

}

/////////////////yellow_card_date_validation_customerwise/////////////////

if($table_name=="yellow_card_date_validation_customerwise"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(validation_create_date,'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['sl_no'];
		    $Entry_Date = $row_data['Entry_Date'];
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE sl_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    $sql_insert = "INSERT INTO `yellow_card_date_validation_customerwise`(`sl_no`, `zone`, `state`, `branch`, `customer_code`, `validation_from`, `validation_to`, `validation_last_date`, `validation_create_date`, `ip_address`)  VALUES (
    		        '".$row_data['sl_no']."',
    		        '".$row_data['zone']."',
    		        '".$row_data['state']."',
    		        '".$row_data['branch']."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['validation_from']."',
    		        '".$row_data['validation_to']."',
    		        '".$row_data['validation_last_date']."',
    		        '".$row_data['validation_create_date']."',
    		        '".$row_data['ip_address']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "yellow_card_date_validation_customerwise".$columns_total;
echo "\n";
echo "yellow_card_date_validation_customerwise_success".($count-$count_r);
echo "\n";
echo "yellow_card_date_validation_customerwise_error".$count_r;

}

/////////////////////yellow_card_details//////////////////

if($table_name=="yellow_card_details"){

$sql="SELECT * FROM $table_name ";
$sql="SELECT * FROM $table_name where DATE_FORMAT(SUBSTRING(yellow_card_no,-14,8),'%Y-%m-%d') >='".$table_date."'";
//echo $sql;
$rs=mysqli_query($link,$sql);
$columns_total=mysqli_num_rows($rs);
if($columns_total>0){
    
    $count=0;
    $count_r = 0;

while($row_data = mysqli_fetch_assoc($rs))
		{
		    $trans_id = $row_data['yellow_card_no'];
		    
		     $sqlchkorlocation="SELECT * FROM $table_name WHERE yellow_card_no='".$trans_id."'";
    		$reschkorlocation = mysqli_query($linkL,$sqlchkorlocation) or die(mysqli_error()." Error in check order $table_name: ".$sqlchkorlocation); 
    		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
    		$countchkorlocation=mysqli_num_rows($reschkorlocation);
    		
    		
    		if($countchkorlocation>0)
    		{
    		    
    		}else{
    		    
    		    
    		    $sql_insert = "INSERT INTO `yellow_card_details`(`yellow_card_no`, `customer_code`, `challan_no`, `challan_date`, `qty`, `qty_UOM`, `linked_dealer_code`)  VALUES (
    		        '".$row_data['yellow_card_no']."',
    		        '".$row_data['customer_code']."',
    		        '".$row_data['challan_no']."',
    		        '".$row_data['challan_date']."',
    		        '".$row_data['qty']."',
    		        '".$row_data['qty_UOM']."',
    		        '".$row_data['linked_dealer_code']."'
    		        )";
    		        
    		        
    		        try{
    		        $res_insert = mysqli_query($linkL,$sql_insert) or die(mysqli_error()." Error in check order $table_name: ".$sql_insert);
    		        }catch (Exception $e) {
    		            echo $sql_insert.";";
    		            echo  "\n";
                        echo 'Caught exception: ',  $e->getMessage(), "\n";
                        $count_r = $count_r+1;
                    }
    		        $count=$count+1;
    		        
    		       // echo "\n".$count;
    		}
		    

		}		

}

echo "yellow_card_details".$columns_total;
echo "\n";
echo "yellow_card_details_success".($count-$count_r);
echo "\n";
echo "yellow_card_details_error".$count_r;

}






//while ($row = mysqli_fetch_array($rs)) {
/*for ($i = 0; $i < $columns_total; $i++) {
    $heading = mysqli_fetch_field_direct($rs, $i)->name;
    $heading_type = mysqli_fetch_field_direct($rs, $i)->type;

  
 
    if($heading_type=="5"){
        
        $sqlu="ALTER TABLE $table_name CHANGE $heading $heading DOUBLE NOT NULL DEFAULT '0'";
        
        echo $sqlu;
       // $rsu=mysqli_query($link,$sqlu);
        
//echo $heading."--".$heading_type."\n";
    }
    
    
    
}*/
//$output .="\n";
//}

?>