<?php
include "star_connection.php";
$customer_master_STAR = "customer_master_STAR";
$server_url = "https://" . $_SERVER['SERVER_NAME']."/cust_location/";
$res_msg=array();

$sql2 = "select * from $customer_master_STAR where `latitude`!='' and `longitude`!='' group by `dns_customer_code` order by `customer_name` asc";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
if($totres2>0){
	while($row2=mysql_fetch_assoc($res2)){
		$dns_customer_code = $row2["dns_customer_code"];
		$the_latitude = $row2["latitude"];
		$the_longitude = $row2["longitude"];			
		$customer_name = $row2["customer_name"];
		$address = $row2["address"] ? trim($row2["address"]) : "";
		$phone_no = $row2["phone_no"] ? trim($row2["phone_no"]) : "";
		if($phone_no!=""){
		$phone_no = 'Phone No: '.$phone_no;	
		}

		$store_data_wrt_brand .='<div class="store" data-latitude="'.$the_latitude.'" data-longitude="'.$the_longitude.'" style="position:relative;"><img src="'.$server_url.'img/arrow_image_for_brand_locator.png" class="arrow_on_st_dtls"/>
		<span class="title" data-type="title">'.$customer_name.'</span><br><span data-type="mallname">'.$phone_no.'</span><br><span data-type="city">'.$address.'</span><br><span data-type="viewmore"></span><br></div>';
		
	}
	
	
$res_msg = array("process_status"=>"YES","process_msg"=>"Success","store_data_wrt_brand"=>$store_data_wrt_brand);	
		
}else{
$res_msg = array("process_status"=>"NO","process_msg"=>"No data found.");
}
	

echo json_encode($res_msg);
mysql_close();
?>