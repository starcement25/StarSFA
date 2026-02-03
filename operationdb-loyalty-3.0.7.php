<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
function getReverseGeo($latitude,$longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data,true);
	
	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if(is_array($jsondata )&& $jsondata['status']=='OK')
	{
		  $addr = $jsondata['results']['0']['formatted_address'];
	}		
	return  $addr;	
}
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');


/*$body="<?xml version='1.0' encoding='UTF-8'?><root><loyalty><location><loyalty_card_no><![CDATA[RA000034]]></loyalty_card_no><trans_id><![CDATA[LE000120140904115152]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 11:51:52]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904115152]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140904115140]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Devendra Shah]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000034]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-04 11:51:40]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8923906911]]></phone_no><address><![CDATA[KANODIA KANPUR]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904115152]]></transaction_id><purchase_value><![CDATA[2000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000035]]></loyalty_card_no><trans_id><![CDATA[LE000120140904121509]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 12:15:09]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904121509]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904121509]]></transaction_id><purchase_value><![CDATA[2000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000036]]></loyalty_card_no><trans_id><![CDATA[LE000120140904125045]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 12:50:45]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904125045]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904125045]]></transaction_id><purchase_value><![CDATA[22000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000016]]></loyalty_card_no><trans_id><![CDATA[LE000120140904125721]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 12:57:21]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904125721]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140829124650]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Dharmandra Singh]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000016]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-29 12:46:50]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8417942226]]></phone_no><address><![CDATA[JHUNSI ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904125721]]></transaction_id><purchase_value><![CDATA[2000]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[]]></loyalty_card_no><trans_id><![CDATA[LE000120140904125855]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 12:58:55]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904125855]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904125855]]></transaction_id><purchase_value><![CDATA[1000]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty></root>";




$body="<?xml version='1.0' encoding='UTF-8'?><root><loyalty><location><loyalty_card_no><![CDATA[RA000033]]></loyalty_card_no><trans_id><![CDATA[LE000120140904130109]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 13:01:09]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904130109]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904130109]]></transaction_id><purchase_value><![CDATA[1000]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000037]]></loyalty_card_no><trans_id><![CDATA[LE000120140904130737]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263619]]></latt><longi><![CDATA[81.9393025]]></longi><date><![CDATA[2014-09-04 13:07:37]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904130737]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904130737]]></transaction_id><purchase_value><![CDATA[5000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000029]]></loyalty_card_no><trans_id><![CDATA[LE000120140904142426]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 14:24:26]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904142426]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140902124548]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Rajesh Kumar]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000029]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-02 12:45:48]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9721813945]]></phone_no><address><![CDATA[NARNOL HARAYANA]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904142426]]></transaction_id><purchase_value><![CDATA[500]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000018]]></loyalty_card_no><trans_id><![CDATA[LE000120140904150837]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 15:08:37]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904150837]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140829131409]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Sanjeet Kumar]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000018]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-29 13:14:09]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[7860895981]]></phone_no><address><![CDATA[JHUNSI ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904150837]]></transaction_id><purchase_value><![CDATA[5000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000032]]></loyalty_card_no><trans_id><![CDATA[LE000120140904150919]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 15:09:19]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904150919]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140902141210]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Satya Prakash]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000032]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-02 14:12:10]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8423961300]]></phone_no><address><![CDATA[ANDAWA JHUSI]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904150919]]></transaction_id><purchase_value><![CDATA[6000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000015]]></loyalty_card_no><trans_id><![CDATA[LE000120140904151047]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 15:10:47]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904151047]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140829124051]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Raj Kumar Sharma]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000015]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-29 12:40:51]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8858924925]]></phone_no><address><![CDATA[NAINI ALLAHABAD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904151047]]></transaction_id><purchase_value><![CDATA[60000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000023]]></loyalty_card_no><trans_id><![CDATA[LE000120140904154119]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 15:41:19]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904154119]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140830142309]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Dhramadra Kumar]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000023]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-30 14:23:09]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9956958341]]></phone_no><address><![CDATA[PAKRI ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904154119]]></transaction_id><purchase_value><![CDATA[4000]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000026]]></loyalty_card_no><trans_id><![CDATA[LE000120140904161333]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 16:13:33]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904161333]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140901160728]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Vijay Shanker]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000026]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-01 16:07:28]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9935778096]]></phone_no><address><![CDATA[katka jhunsi ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904161333]]></transaction_id><purchase_value><![CDATA[9677]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000016]]></loyalty_card_no><trans_id><![CDATA[LE000120140904161754]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 16:17:54]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904161754]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140829124650]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Dharmandra Singh]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000016]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-29 12:46:50]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8417942226]]></phone_no><address><![CDATA[JHUNSI ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904161754]]></transaction_id><purchase_value><![CDATA[6050]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000039]]></loyalty_card_no><trans_id><![CDATA[LE000120140904170538]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 17:05:38]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904170538]]></transaction_id><loyalty_card_holder_code><![CDATA[]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[]]></total_purchase_value><total_reward_point><![CDATA[]]></total_reward_point><last_update_on><![CDATA[]]></last_update_on><redeemed><![CDATA[]]></redeemed><phone_no><![CDATA[]]></phone_no><address><![CDATA[]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904170538]]></transaction_id><purchase_value><![CDATA[3000]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000011]]></loyalty_card_no><trans_id><![CDATA[LE000120140904173044]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-04 17:30:44]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140904173044]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140828164442]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Ratan Mishra11]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000011]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-08-28 16:44:42]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9454836340]]></phone_no><address><![CDATA[kotawa alld]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140904173044]]></transaction_id><purchase_value><![CDATA[750]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000040]]></loyalty_card_no><trans_id><![CDATA[LE000120140905090339]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[25.4263944]]></latt><longi><![CDATA[81.9385236]]></longi><date><![CDATA[2014-09-05 09:03:39]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140905090339]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140905090321]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[S.prasad]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000040]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-05 09:03:21]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9454258338]]></phone_no><address><![CDATA[PHULEN SARAY ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140905090339]]></transaction_id><purchase_value><![CDATA[500]]></purchase_value><trans_type><![CDATA[FUEL]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[RA000030]]></loyalty_card_no><trans_id><![CDATA[LE000120140905104401]]></trans_id><outlet_user_code><![CDATA[E0001]]></outlet_user_code><latt><![CDATA[22.5651927]]></latt><longi><![CDATA[88.3545305]]></longi><date><![CDATA[2014-09-05 10:44:01]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LE000120140905104401]]></transaction_id><loyalty_card_holder_code><![CDATA[LE000120140902130544]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[A.k Singh]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[RA000030]]></loyalty_card_no><card_type><![CDATA[ ]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-09-02 13:05:44]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[9453190696]]></phone_no><address><![CDATA[JHUNSI ALLD]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LE000120140905104401]]></transaction_id><purchase_value><![CDATA[5800]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0001]]></outlet_code></loyaltydata></loyalty></root>";*/


$location_loyalty_card_no = "*ROOT*LOYALTY*LOCATION*LOYALTY_CARD_NO";
$loyalty_trans_id = "*ROOT*LOYALTY*LOCATION*TRANS_ID";
$loyalty_user_code = "*ROOT*LOYALTY*LOCATION*OUTLET_USER_CODE";
$loyalty_latt = "*ROOT*LOYALTY*LOCATION*LATT";
$loyalty_longi = "*ROOT*LOYALTY*LOCATION*LONGI";
$loyalty_date = "*ROOT*LOYALTY*LOCATION*DATE";
$loyaltycustdata_transaction_id = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TRANSACTION_ID";
$loyaltycustdata_card_holder_code = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_HOLDER_CODE";
$loyaltycustdata_card_holder_name = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_HOLDER_NAME";
$loyaltycustdata_card_no = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_NO";
$loyaltycustdata_card_type = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*CARD_TYPE";
$loyaltycustdata_total_purchase_value = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TOTAL_PURCHASE_VALUE";
$loyaltycustdata_total_reward_point = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TOTAL_REWARD_POINT";
$loyaltycustdata_last_update_on = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LAST_UPDATE_ON";
$loyaltycustdata_redeemed = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*REDEEMED";
$loyaltycustdata_phone_no = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*PHONE_NO";
$loyaltycustdata_address = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*ADDRESS";
$loyaltydata_transaction_id = "*ROOT*LOYALTY*LOYALTYDATA*TRANSACTION_ID";
$loyaltydata_purchase_value = "*ROOT*LOYALTY*LOYALTYDATA*PURCHASE_VALUE";
$loyaltydata_trans_type = "*ROOT*LOYALTY*LOYALTYDATA*TRANS_TYPE";
$loyaltydata_outlet_code = "*ROOT*LOYALTY*LOYALTYDATA*OUTLET_CODE";

$loyalty_array = array();
$counter = 0;

class xml_loyalty{
	var $location_loyalty_card_no,$loyalty_trans_id,$loyalty_user_code,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltycustdata_transaction_id,$loyaltycustdata_card_holder_code,$loyaltycustdata_card_holder_name,$loyaltycustdata_card_no,$loyaltycustdata_card_type,$loyaltycustdata_total_purchase_value,$loyaltycustdata_total_reward_point,$loyaltycustdata_last_update_on,$loyaltycustdata_redeemed,$loyaltycustdata_phone_no,$loyaltycustdata_address,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_outlet_code;	
}

function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}

function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
    global $current_tag,$counter,$location_loyalty_card_no,$loyalty_trans_id,$loyalty_user_code,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltycustdata_transaction_id,$loyaltycustdata_card_holder_code,$loyaltycustdata_card_holder_name,$loyaltycustdata_card_no,$loyaltycustdata_card_type,$loyaltycustdata_total_purchase_value,$loyaltycustdata_total_reward_point,$loyaltycustdata_last_update_on,$loyaltycustdata_redeemed,$loyaltycustdata_phone_no,$loyaltycustdata_address,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_outlet_code,$loyalty_array;
	/*echo $current_tag.'<br />';
	echo $data;*/
	if(substr($current_tag,0,13)=='*ROOT*LOYALTY')
	{
	/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $location_loyalty_card_no:
				$loyalty_array[$counter] = new xml_loyalty();
				$loyalty_array[$counter]->location_loyalty_card_no = $data;
				break;
			case $loyalty_trans_id:
				$loyalty_array[$counter]->loyalty_trans_id = $data;
				break;
			case $loyalty_user_code:
				$loyalty_array[$counter]->loyalty_user_code = $data;
				break;	
			case $loyalty_latt:
				$loyalty_array[$counter]->loyalty_latt = $data;
				break;
			case $loyalty_longi:
				$loyalty_array[$counter]->loyalty_longi = $data;
				break;
			case $loyalty_date:
				$loyalty_array[$counter]->loyalty_date = $data;
				break;
			case $loyaltycustdata_transaction_id:
				$loyalty_array[$counter]->loyaltycustdata_transaction_id = $data;
				break;
			case $loyaltycustdata_card_holder_code:
				$loyalty_array[$counter]->loyaltycustdata_card_holder_code = $data;
				break;
			case $loyaltycustdata_card_holder_name:
				$loyalty_array[$counter]->loyaltycustdata_card_holder_name = $data;
				break;
			case $loyaltycustdata_card_no:
				$loyalty_array[$counter]->loyaltycustdata_card_no = $data;
				break;	
			case $loyaltycustdata_card_type:
				$loyalty_array[$counter]->loyaltycustdata_card_type = $data;
				break;
			case $loyaltycustdata_total_purchase_value:
				$loyalty_array[$counter]->loyaltycustdata_total_purchase_value = $data;
				break;
			case $loyaltycustdata_total_reward_point:
				$loyalty_array[$counter]->loyaltycustdata_total_reward_point = $data;
				break;
			case $loyaltycustdata_last_update_on:
				$loyalty_array[$counter]->loyaltycustdata_last_update_on = $data;
				break;
			case $loyaltycustdata_redeemed:
				$loyalty_array[$counter]->loyaltycustdata_redeemed = $data;
				break;
			case $loyaltycustdata_phone_no:
				$loyalty_array[$counter]->loyaltycustdata_phone_no = $data;
				break;
			case $loyaltycustdata_address:
				$loyalty_array[$counter]->loyaltycustdata_address = $data;
				break;	
			case $loyaltydata_transaction_id:
				$loyalty_array[$counter]->loyaltydata_transaction_id = $data;
				break;
			case $loyaltydata_purchase_value:
				$loyalty_array[$counter]->loyaltydata_purchase_value = $data;
				break;	
			case $loyaltydata_trans_type:
				$loyalty_array[$counter]->loyaltydata_trans_type = $data;
				break;	
			case $loyaltydata_outlet_code:
				$loyalty_array[$counter]->loyaltydata_outlet_code = $data;
				$counter++;
				break;
		}
	}
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($loyalty_array);
//echo count($loyalty_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* ------------------------------------------------START QUERY FOR LOYALTY---------------------------------------------------------------------*/
if(count($loyalty_array)>0)
{
	for($x=0;$x<count($loyalty_array);$x++){
		$location_loyalty_card_no=$loyalty_array[$x]->location_loyalty_card_no;
		$loyalty_trans_id=$loyalty_array[$x]->loyalty_trans_id;
		$loyalty_user_code=$loyalty_array[$x]->loyalty_user_code;
		$loyalty_latt=$loyalty_array[$x]->loyalty_latt;
		$loyalty_longi=$loyalty_array[$x]->loyalty_longi;
		$loyalty_date=$loyalty_array[$x]->loyalty_date;
		$loyaltycustdata_transaction_id=$loyalty_array[$x]->loyaltycustdata_transaction_id;
		$loyaltycustdata_card_holder_code=$loyalty_array[$x]->loyaltycustdata_card_holder_code;
		$loyaltycustdata_card_holder_name=$loyalty_array[$x]->loyaltycustdata_card_holder_name;
		$loyaltycustdata_card_no=$loyalty_array[$x]->loyaltycustdata_card_no;
		$loyaltycustdata_card_type=$loyalty_array[$x]->loyaltycustdata_card_type;
		$loyaltycustdata_total_purchase_value=$loyalty_array[$x]->loyaltycustdata_total_purchase_value;
		$loyaltycustdata_total_reward_point=$loyalty_array[$x]->loyaltycustdata_total_reward_point;
		$loyaltycustdata_last_update_on=$loyalty_array[$x]->loyaltycustdata_last_update_on;
		$loyaltycustdata_redeemed=$loyalty_array[$x]->loyaltycustdata_redeemed;
		$loyaltycustdata_phone_no=$loyalty_array[$x]->loyaltycustdata_phone_no;
		$loyaltycustdata_address=$loyalty_array[$x]->loyaltycustdata_address;
		$loyaltydata_transaction_id=$loyalty_array[$x]->loyaltydata_transaction_id;
		$loyaltydata_purchase_value=$loyalty_array[$x]->loyaltydata_purchase_value;
		$loyaltydata_trans_type=$loyalty_array[$x]->loyaltydata_trans_type;
		$loyaltydata_outlet_code=$loyalty_array[$x]->loyaltydata_outlet_code;

		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($loyalty_latt>0 && $loyalty_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE loyalty_location_transaction SET latt='".$loyalty_latt."',longi='".$loyalty_longi."' WHERE emp_code='".$loyalty_user_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkloyaltylocation="SELECT * FROM loyalty_location_transaction WHERE transaction_id='".$loyalty_trans_id."'";
		$reschkloyaltylocation = mysqli_query($link,$sqlchkloyaltylocation) or die(mysqli_error()." Error in check loyalty location: ".$sqlchkattlocation); 
		$rowchkloyaltylocation = mysqli_fetch_assoc($reschkloyaltylocation);
		$countchkloyaltylocation=mysqli_num_rows($reschkloyaltylocation);
		
		//For update the location table for existing trans id
		if($countchkloyaltylocation>0)
		{
			$sqlupdateloyaltylocation="UPDATE loyalty_location_transaction SET emp_code='".$loyalty_user_code."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."'
									WHERE transaction_id='".$loyalty_trans_id."'";
			$rsupdateloyaltylocation=mysqli_query($link,$sqlupdateloyaltylocation) or die(mysqli_error()." Error in update loyalty location: ".$sqlupdateloyaltylocation);
			if($rsupdateloyaltylocation)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
		}
		else
		{
			// create the data for location table date field , by checking the current date and time and the actual date and time of attendance
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertloyaltylocation="INSERT INTO loyalty_location_transaction SET emp_code='".$loyalty_user_code."',
									transaction_id='".$loyalty_trans_id."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."',
									date='".$loyalty_date."',
									update_time='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertloyaltycard="INSERT INTO card_transaction SET transaction_id='".$loyaltydata_transaction_id."',
									loyalty_card_no='".$location_loyalty_card_no."',
									outlet_code='".$loyaltydata_outlet_code."',
									trans_type='".$loyaltydata_trans_type."',
								 	purchase_value='".$loyaltydata_purchase_value."'";
										
			$sqlchkloyaltycardholder="SELECT * FROM loyalty_card_holder_master WHERE loyalty_card_holder_code ='".$loyaltycustdata_card_holder_code."' 
									AND loyalty_card_no='".$loyaltycustdata_card_no."'";
			$reschkloyaltycardholder = mysqli_query($link,$sqlchkloyaltycardholder) or die(mysqli_error()." Error in check loyalty card holder: ".$sqlchkloyaltycardholder); 
			$rowchkloyaltycardholder = mysqli_fetch_assoc($reschkloyaltycardholder);
			$countchkloyaltycardholder=mysqli_num_rows($reschkloyaltycardholder);
			
			//For Insertion of new card holder
			$sqlinsertloyaltycardholder="INSERT INTO loyalty_card_holder_master SET loyalty_card_holder_code ='".$loyaltycustdata_card_holder_code."',
										 loyalty_card_holder_name='".$loyaltycustdata_card_holder_name."',
										  loyalty_card_no='".$loyaltycustdata_card_no."',
										  card_type='".$loyaltycustdata_card_type."',
										  total_purchase_value='".$loyaltycustdata_total_purchase_value."',
										  total_reward_point='".$loyaltycustdata_total_reward_point."',
										  last_update_on='".$loyaltycustdata_last_update_on."',
										  redeemed='".$loyaltycustdata_redeemed."',
										  address='".$loyaltycustdata_address."',
										  phone_no='".$loyaltycustdata_phone_no."'";	
								 
			if($countchkloyaltycardholder<1)
			{
				if(mysqli_query($link,$sqlinsertloyaltylocation) && mysqli_query($link,$sqlinsertloyaltycard) && mysqli_query($link,$sqlinsertloyaltycardholder))
				{
					$flag=5;
				}
			}
			else
			{
				if(mysqli_query($link,$sqlinsertloyaltylocation) && mysqli_query($link,$sqlinsertloyaltycard))
				{
					$flag=5;
				}
			}
			if($flag==5)
				{
					//$flag=5;
					
					// For Sending email to recipents for attendance
				 	$sqloutletname="SELECT outlet_name FROM outlet_master WHERE outlet_code='".$loyaltydata_outlet_code."'";
					$rsoutletname=mysqli_query($link,$sqloutletname);
					$rowoutletname=mysqli_fetch_assoc($rsoutletname);
					$outlet_name=$rowoutletname['outlet_name'];
					
					$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$loyalty_user_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
					
					$sqlcardholderdetails="SELECT loyalty_card_holder_name,total_reward_point FROM loyalty_card_holder_master WHERE loyalty_card_no='".$location_loyalty_card_no."'";
					$rscardholderdetails=mysqli_query($link,$sqlcardholderdetails);
					$rowcardholderdetails=mysqli_fetch_assoc($rscardholderdetails);
					$loyalty_card_holder_name=$rowcardholderdetails['loyalty_card_holder_name'];
					$total_reward_point=$rowcardholderdetails['total_reward_point'];

					$address=getReverseGeo($latt,$longi);
					$loyaltyemailsubj="Transaction of - ".$emp_name." on ".date('d-m-Y',strtotime($loyalty_date))." @".date('H:i:s',strtotime($loyalty_date)).' hrs.';
					$loyaltyemailbody = "<html><head><title>Loyalty</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application for <b>LOYALTY POINTS</b>.<br /><br /><br />
										<b>Vertical: " .strtoupper($loyaltydata_trans_type). "</b><br /><br />
										<b>Outlet Name: " .$outlet_name. "</b><br /><br />
										<b>Card Holder Name: " .$loyalty_card_holder_name. "</b><br /><br />
										<b>Card Holder No.: " .$location_loyalty_card_no. "</b><br /><br />
										<b>Card Holder Phone No.: " .$loyaltycustdata_phone_no. "</b><br /><br />
										<b>Card Holder Address: " .$loyaltycustdata_address. "</b><br /><br />
										<b>Purchase Value: " .number_format($loyaltydata_purchase_value,2). "</b><br /><br />
										<b>Accumulated Point: " .number_format($total_reward_point,2). " (Excluding this transaction)</b><br /><br />
										</table><br /><br />Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								'X-Mailer: PHP/' . phpversion();
					if(mail(LOYALTYEMAILRECIPENTS, $loyaltyemailsubj, $loyaltyemailbody, $headers,'-facedns@coral.in'))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
		}// End of else
	}// End for loop
}// End attendance array if 

 /* --------------------END QUERY FOR LOYALTY------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 mysqli_query($link,"COMMIT");
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
if($flag==6)
{
	mysqli_query($link,"COMMIT");
	if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
?>
