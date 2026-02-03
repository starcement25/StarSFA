<?php
define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	//define("DB","acedns_VCONNECT");
	define("DB","acedns_MAGIK");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database");

//$folder_path = "masterdata"; 
   
// List of name of files inside 
// specified folder 
//$files = glob($folder_path.'/*');  
   
$sqltargetach="SELECT customer_code,SUM(visit_qty) AS total_qty,SUM(amount) AS total_amt,COUNT(DISTINCT product_code) AS sku_count FROM  prev_order_counting_master 
				WHERE SUBSTRING(visit_date,1,10) >= '2021-04-01' GROUP BY customer_code";
$rstargetach=mysqli_query($link,$sqltargetach);
$counttargetach=mysqli_num_rows($rstargetach);
			
								//target Ach update START
								/*if(strtotime(substr($visit_date,0,10)) >=strtotime($initial_start_date) && strtotime(substr($visit_date,0,10)) <=strtotime( $initial_end_date))
								{
									$final_start_date= $initial_start_date;
									$final_end_date=$initial_end_date;
								}*/
while($rowtargetach=mysqli_fetch_assoc($rstargetach))
{										
	$sqlupdate  = "update retailer_wise_target_ach SET";
	$sqlupdate .= " value_slab_ach=(value_slab_ach+'".$rowtargetach['total_amt']."')";
	$sqlupdate .= " , sku_count_ach=(sku_count_ach+'".$rowtargetach['sku_count']."')";
	$sqlupdate .= " , apr_freq_ach=(apr_freq_ach+'".$rowtargetach['total_qty']."')";
	$sqlupdate .= " , may_freq_ach='0'";
	$sqlupdate .= " , jun_freq_ach='0'";
	echo $sqlupdate .= " , upload_time=CURRENT_TIMESTAMP() WHERE customer_code='".addslashes($rowtargetach['customer_code'])."' AND acedns='Y'";
	
	mysqli_query($link,$sqlupdate);
} 
?>