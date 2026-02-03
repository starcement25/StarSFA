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
define("APICALLLOGURL","http://demo.acedns.in");



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
//$nick_name="DURO";
//$nick_name="ILS";



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

$table_name = "orderdata";

$table_name = "order_details";

$table_name = "order_header";

$table_name = "route_master";

$table_name = "site_lead_conversion_master";

$table_name = "survey_output_test";

$table_name = "t_att_checkout_info";

$table_name = "yellow_card_details";

$table_name = "attendence";


$table_date="2024-01-08";
 
 /////////////////////////////////////////////////location////////////////////
$Tables_in_acedns_STAR = "Tables_in_acedns_".$nick_name;

$rstable=mysqli_query($link,"SHOW TABLES");
while($row_table_data = mysqli_fetch_assoc($rstable)){
    
    
    $table_name = $row_table_data[$Tables_in_acedns_STAR];
    
    if($table_name=="mis_data_details"){
    
$sql="SELECT * FROM $table_name";
$rst=mysqli_query($link,$sql);
    
    //$row_data = mysqli_fetch_assoc($rst);
		//$columns_total=mysqli_num_rows($rst);
$columns_total = mysqli_num_fields($rst);

echo "-total-".$columns_total;

    
   // while ($rowt = mysqli_fetch_array($rst)) {
for ($i = 0; $i < $columns_total; $i++) {
    $heading = mysqli_fetch_field_direct($rst, $i)->name;
    $heading_type = mysqli_fetch_field_direct($rst, $i)->type;

 // echo $heading."--".$heading_type."\n"; 
 
    if($heading_type=="5"){
        
        $sqlu="ALTER TABLE $table_name CHANGE $heading $heading DOUBLE NOT NULL DEFAULT '0'";
        
        echo $sqlu.";";
        $rsu=mysqli_query($link,$sqlu);
        
        //echo $heading."--".$heading_type."\n";
    }
    
     
    if($heading_type=="252"){
        
        $sqlu="ALTER TABLE $table_name CHANGE $heading $heading TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL";
        
        //echo $sqlu;
         //$rsu=mysqli_query($link,$sqlu);
        
        //echo $heading."--".$heading_type."\n";
    }
    
    if($heading_type=="253_"){
        if(!str_contains($heading, 'id') && !str_contains($heading, 'code')){
        $sqlu="ALTER TABLE `".$table_name."` CHANGE `".$heading."` `".$heading."` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;";
        
         
       //  $rsu=mysqli_query($link,$sqlu);
         if($rsu){
             echo $sqlu;
         }else{
            // echo "-error-".$sqlu;
         }
        
        //echo $heading."--".$heading_type."\n";
        }
    }
    
    
    
}
//$output .="\n";
}


}  
    
    
    
    
//}//table name while end

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