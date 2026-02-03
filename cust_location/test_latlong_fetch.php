<?php
include "star_connection.php";
$customer_master_STAR = "customer_master_STAR";
function get_latlong_from_address($address){
	$address_arr = array("latitude"=>"","longitude"=>"");
	$address = trim($address);
	if($address!=""){
$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$geo = curl_exec($ch);
$geo = json_decode($geo, true);
if($geo['status'] = 'OK') {
$latitude_from = $geo['results'][0]['geometry']['location']['lat'];
$longitude_from = $geo['results'][0]['geometry']['location']['lng'];
$address_arr = array("latitude"=>$latitude_from,"longitude"=>$longitude_from);
}
}	
return $address_arr;
}


$sql2 = "select * from $customer_master_STAR where `latitude`='' and `longitude`='' and `address`!='DEACTIVE' and `address`!='0' group by `dns_customer_code` order by `customer_name` asc";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
if($totres2>0){
	while($row2=mysql_fetch_assoc($res2)){
		$dns_customer_code = $row2["dns_customer_code"];
		$the_latitude = $row2["latitude"];
		$the_longitude = $row2["longitude"];
		$address = $row2["address"] ? trim($row2["address"]) : "";
		$response_arr = array();
		if($address!=""){
			$response_arr = get_latlong_from_address($address);
			$the_latitude = $response_arr["latitude"];
			$the_longitude = $response_arr["longitude"];
			if($the_latitude!="" && $the_longitude!=""){
				$upd = "update $customer_master_STAR set `latitude`='$the_latitude',`longitude`='$the_longitude' where `dns_customer_code`='$dns_customer_code'";
				$resupd = mysql_query($upd);
			}
		}
		
	}
}

?>