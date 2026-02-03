<?php
$the_date = "2022-08-31T00:00:00";
$the_from_time = "PT01H00M00S";
$the_to_time = "PT02H00M00S";
$title1 = "<b>1.Delivery [ZSD_CUSTOMER_DELIVERY_SRV]</b><br>";

//$the_filter = '&$filter=(InvDate eq datetime\''.$the_date.'\' and (InvTime ge time\''.$the_from_time.'\' and InvTime le time\''.$the_to_time.'\') )';
//$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_CUSTOMER_DELIVERY_SRV/ZDELV_OUTSet?$format=json'.str_replace(" ","%20",$the_filter);

//$url_show = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_CUSTOMER_DELIVERY_SRV/ZDELV_OUTSet?$format=json&$filter=(InvDate eq datetime\'2022-08-31T00:00:00\' and (InvTime ge time\'PT01H00M00S\' and InvTime le time\'PT02H00M00S\') )';

$prev_date_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
$the_date = date("Y-m-d",strtotime($prev_date_time));
$the_hour = date("H",strtotime($prev_date_time));
$the_minute = date("i",strtotime($prev_date_time));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;
$the_date = "2023-04-27T00:00:00";
//$the_date = $the_date."T00:00:00";
$the_from_time = "PT00H00M00S";
$the_to_time = "PT23H59M59S";

//https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZOVW_DELIVERY_CDS/ZOVW_DELIVERY(p_Dt=datetime'2023-04-18T00:00:00',p_tmFrm=time'PT12H00M01S',p_tmTo=time'PT13H00M59S')/Set?$format=json&sap-client=900

echo $url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_DELIVERY_CDS/ZOVW_DELIVERY(p_Dt=datetime\''.$the_date.'\',p_tmFrm=time\''.$the_from_time.'\',p_tmTo=time\''.$the_to_time.'\')/Set?$format=json&sap-client=900';


function show_data_new($url_ck){
$useragent = $_SERVER['HTTP_USER_AGENT'];
$username = "MOBILEAPP";
$password = "Star@#2021";
$ch_sheader = curl_init();
curl_setopt($ch_sheader, CURLOPT_URL,$url_ck);
curl_setopt($ch_sheader, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");
//curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
	
$body_for_mcode = curl_exec($ch_sheader);
	if (curl_errno($ch_sheader)) {
    $error_msg = curl_error($ch_sheader);
}
	print_r($error_msg);
//echo "<pre>";
$info = curl_getinfo($ch_sheader);
//print_r($info);
curl_close($ch_sheader);
return $body_for_mcode;
}
function isJsonCk($str) {
    $json = json_decode($str);
    return $json && $str != $json;
}
$username = "MOBILEAPP";
$password = "Star@#2021";
echo "<br>Using this credentials<br>User ID: ".$username;
echo "<br>Password: ".$password;
echo "<br><br><br>";


echo "<br>InvDate: ".$so_date;
echo "<br>".$title1;
echo "Link: ".$url_show;
echo "<br>Json Response:<br>";
$body_for_mcode1 = show_data_new($url_ck1);
echo "--------------------------------<br>";
echo $body_for_mcode1;
echo "<br>--------------------------------<br><br>";
/*if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	print_r($json_decoded);
}*/
/*if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		
		echo "<br>kunnr: ".$kunnr;
		echo "<br>name1: ".$name1;
		echo "<br>name2: ".$name2;
		echo "<br>name3: ".$name3;
		echo "<br>credit_limit: ".$credit_limit;
		echo "<br>credit_expose: ".$credit_expose;
		}
		
	}
}*/

?>