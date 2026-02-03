<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db("acedns_STORE");

$xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><data>";
$sql_select_mall_data = "SELECT * FROM mall_master";
$res_select_mall_data = mysqli_query($link,$sql_select_mall_data);
while($row_select_mall_data = mysqli_fetch_assoc($res_select_mall_data))
{
	$xml_data .= "<mall_details>";
		$xml_data .= "<mall_id>".$row_select_mall_data['mall_id']."</mall_id>";
		$xml_data .= "<mall_name>".$row_select_mall_data['mall_name']."</mall_name>";
		$xml_data .= "<address>".$row_select_mall_data['address']."</address>";
		$xml_data .= "<landmark>".$row_select_mall_data['landmark']."</landmark>";
		$xml_data .= "<area>".$row_select_mall_data['area']."</area>";
		$xml_data .= "<city>".$row_select_mall_data['city']."</city>";
		$xml_data .= "<pincode>".$row_select_mall_data['pincode']."</pincode>";
		$xml_data .= "<state>".$row_select_mall_data['state']."</state>";
		$xml_data .= "<country>".$row_select_mall_data['country']."</country>";
		$xml_data .= "<std_code>".$row_select_mall_data['std_code']."</std_code>";
		$xml_data .= "<closed_on>".$row_select_mall_data['closed_on']."</closed_on>";
	$xml_data .= "</mall_details>";
}
	echo $xml_data .= "</data>";
?>