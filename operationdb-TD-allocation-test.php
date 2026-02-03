<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

if($nick_name=='EMAMI')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	
}
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><TD_ALLOCATION><location><emp_code><![CDATA[E0077]]></emp_code><trans_id><![CDATA[TDAE007720170824153922]]></trans_id><latt><![CDATA[22.5644067]]></latt><longi><![CDATA[88.3568017]]></longi><date><![CDATA[2017-08-24 15:39:22]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE007720170824153922]]></allocation_id><allocation_date><![CDATA[2017-08-24 15:39:22]]></allocation_date><emp_code><![CDATA[E0042]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[40]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE007720170824153922]]></allocation_id><allocation_date><![CDATA[2017-08-24 15:39:22]]></allocation_date><emp_code><![CDATA[E0042]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[30]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0077]]></emp_code><trans_id><![CDATA[TDAE007720170824154103]]></trans_id><latt><![CDATA[22.5644067]]></latt><longi><![CDATA[88.3568017]]></longi><date><![CDATA[2017-08-24 15:41:03]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE007720170824154103]]></allocation_id><allocation_date><![CDATA[2017-08-24 15:41:03]]></allocation_date><emp_code><![CDATA[E0042]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[40]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE007720170824154103]]></allocation_id><allocation_date><![CDATA[2017-08-24 15:41:03]]></allocation_date><emp_code><![CDATA[E0042]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[30]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION></root>";*/

$body="<?xml version='1.0' encoding='UTF-8'?><root><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171115214032]]></trans_id><latt><![CDATA[21.4684746]]></latt><longi><![CDATA[83.9739301]]></longi><date><![CDATA[2017-11-15 21:40:32]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[18]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[30]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171115214032]]></allocation_id><allocation_date><![CDATA[2017-11-15 21:40:32]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171213194153]]></trans_id><latt><![CDATA[20.4675139]]></latt><longi><![CDATA[85.8951487]]></longi><date><![CDATA[2017-12-13 19:41:54]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[23]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171213194153]]></allocation_id><allocation_date><![CDATA[2017-12-13 19:41:53]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171220200431]]></trans_id><latt><![CDATA[18.8633959]]></latt><longi><![CDATA[82.5711489]]></longi><date><![CDATA[2017-12-20 20:04:31]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[5]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220200431]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:04:31]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171220201417]]></trans_id><latt><![CDATA[18.863357]]></latt><longi><![CDATA[82.571149]]></longi><date><![CDATA[2017-12-20 20:14:17]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[6]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171220201417]]></allocation_id><allocation_date><![CDATA[2017-12-20 20:14:17]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171222193801]]></trans_id><latt><![CDATA[19.1735469]]></latt><longi><![CDATA[83.4144873]]></longi><date><![CDATA[2017-12-22 19:38:01]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[8]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222193801]]></allocation_id><allocation_date><![CDATA[2017-12-22 19:38:01]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171222200038]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2017-12-22 20:00:38]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[8]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171222200038]]></allocation_id><allocation_date><![CDATA[2017-12-22 20:00:38]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171227195540]]></trans_id><latt><![CDATA[20.4585571]]></latt><longi><![CDATA[85.896675]]></longi><date><![CDATA[2017-12-27 19:55:40]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[14]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227195540]]></allocation_id><allocation_date><![CDATA[2017-12-27 19:55:40]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820171227200023]]></trans_id><latt><![CDATA[20.4585571]]></latt><longi><![CDATA[85.896675]]></longi><date><![CDATA[2017-12-27 20:00:23]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[14]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820171227200023]]></allocation_id><allocation_date><![CDATA[2017-12-27 20:00:23]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820180101202947]]></trans_id><latt><![CDATA[22.7589659]]></latt><longi><![CDATA[88.3713304]]></longi><date><![CDATA[2018-01-01 20:29:47]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[10]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180101202947]]></allocation_id><allocation_date><![CDATA[2018-01-01 20:29:47]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820180102172252]]></trans_id><latt><![CDATA[22.5177288]]></latt><longi><![CDATA[88.4007901]]></longi><date><![CDATA[2018-01-02 17:22:52]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[10]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172252]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:22:52]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820180102172937]]></trans_id><latt><![CDATA[22.5177288]]></latt><longi><![CDATA[88.4007901]]></longi><date><![CDATA[2018-01-02 17:29:37]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[10]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102172937]]></allocation_id><allocation_date><![CDATA[2018-01-02 17:29:37]]></allocation_date><emp_code><![CDATA[E0151]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820180102181827]]></trans_id><latt><![CDATA[22.5644605]]></latt><longi><![CDATA[88.3567669]]></longi><date><![CDATA[2018-01-02 18:18:27]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[5]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102181827]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:18:27]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION><TD_ALLOCATION><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[TDAE002820180102182032]]></trans_id><latt><![CDATA[22.5644567]]></latt><longi><![CDATA[88.3567699]]></longi><date><![CDATA[2018-01-02 18:20:32]]></date></location><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR7]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR17]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR2]]></product_filter_code><TD><![CDATA[5]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR9]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR4]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR3]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS><TD_ALLOCATION_DETAILS><allocation_id><![CDATA[TDAE002820180102182032]]></allocation_id><allocation_date><![CDATA[2018-01-02 18:20:32]]></allocation_date><emp_code><![CDATA[E0033]]></emp_code><product_filter_code><![CDATA[BR20]]></product_filter_code><TD><![CDATA[0]]></TD></TD_ALLOCATION_DETAILS></TD_ALLOCATION></root>";


$TD_allocation_location_emp_code="*ROOT*TD_ALLOCATION*LOCATION*EMP_CODE";
$TD_allocation_location_trans_id = "*ROOT*TD_ALLOCATION*LOCATION*TRANS_ID";
$TD_allocation_latt = "*ROOT*TD_ALLOCATION*LOCATION*LATT";
$TD_allocation_longi = "*ROOT*TD_ALLOCATION*LOCATION*LONGI";
$TD_allocation_location_date="*ROOT*TD_ALLOCATION*LOCATION*DATE";

$TD_allocation_trans_id = "*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS*ALLOCATION_ID";
$TD_allocation_date = "*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS*ALLOCATION_DATE";
$TD_allocation_emp_code = "*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS*EMP_CODE";
$TD_allocation_product_filter_code ="*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS*PRODUCT_FILTER_CODE";
$TD_allocation_TD ="*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS*TD";

$TD_allocation_array = array();
$TD_allocation_details_array=array();
$counter = 0;
$counterdata = 0;

class xml_TD_allocation{
	var $TD_allocation_location_emp_code,$TD_allocation_location_trans_id,$TD_allocation_latt,$TD_allocation_longi,$TD_allocation_location_date;	
}
class xml_TD_allocation_details{
	var $TD_allocation_trans_id,$TD_allocation_date,$TD_allocation_emp_code,$TD_allocation_product_filter_code,$TD_allocation_TD;
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
    global $TD_allocation_location_emp_code,$TD_allocation_location_trans_id,$TD_allocation_latt,$TD_allocation_longi,$TD_allocation_location_date,$current_tag,$TD_allocation_trans_id,$TD_allocation_date,$TD_allocation_emp_code,$TD_allocation_product_filter_code,$TD_allocation_TD,$TD_allocation_array,$TD_allocation_details_array,$counter,$counterdata;
	if(substr($current_tag,0,19)=='*ROOT*TD_ALLOCATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
       switch($current_tag){
			case $TD_allocation_location_emp_code:
				$TD_allocation_array[$counter] = new xml_TD_allocation();
				$TD_allocation_array[$counter]->TD_allocation_location_emp_code = $data;
				break;
			case $TD_allocation_location_trans_id:
				$TD_allocation_array[$counter]->TD_allocation_location_trans_id = $data;
				break;
			case $TD_allocation_latt:
				$TD_allocation_array[$counter]->TD_allocation_latt = $data;
				break;
			case $TD_allocation_longi:
				$TD_allocation_array[$counter]->TD_allocation_longi = $data;
				break;
			case $TD_allocation_location_date:
				$TD_allocation_array[$counter]->TD_allocation_location_date = $data;
				$counter++;
				break;
	   }
	}
   if(substr($current_tag,0,41)=='*ROOT*TD_ALLOCATION*TD_ALLOCATION_DETAILS')
	  {
		//echo $current_tag.'<br />';
        //echo $data.'<br />';
		switch($current_tag){
			case $TD_allocation_trans_id:
				$TD_allocation_details_array[$counterdata] = new xml_TD_allocation_details();
				$TD_allocation_details_array[$counterdata]->TD_allocation_trans_id = $data;
				break;
			case $TD_allocation_date:
				$TD_allocation_details_array[$counterdata]->TD_allocation_date = $data;
				break;	
			case $TD_allocation_emp_code:
				$TD_allocation_details_array[$counterdata]->TD_allocation_emp_code = $data;
				break;
			case $TD_allocation_product_filter_code:
				$TD_allocation_details_array[$counterdata]->TD_allocation_product_filter_code = $data;
				break;
			case $TD_allocation_TD:
				$TD_allocation_details_array[$counterdata]->TD_allocation_TD = $data;
				$counterdata++;
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
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
$TD_allocation_array_trans_id=array();
//print_r($TD_allocation_array);
/* -------------------------------------------------------START QUERY FOR TD ALLOCATION-----------------------------------------------------------------------*/
if(count($TD_allocation_array)>0)
{
	//$count=1;
	for($x=0;$x<count($TD_allocation_array);$x++){
		$TD_allocation_location_emp_code=$TD_allocation_array[$x]->TD_allocation_location_emp_code;
		$TD_allocation_location_trans_id=$TD_allocation_array[$x]->TD_allocation_location_trans_id;
		$TD_allocation_latt=$TD_allocation_array[$x]->TD_allocation_latt;
		$TD_allocation_longi=$TD_allocation_array[$x]->TD_allocation_longi;
		$TD_allocation_location_date=$TD_allocation_array[$x]->TD_allocation_location_date;

		//For checking that trans id exist or not for TD allocation
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$TD_allocation_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check  location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for TD allocation
		if($countchkorlocation>0)
		{
			if(!in_array($TD_allocation_location_trans_id,$TD_allocation_array_trans_id))
			{
				array_push($TD_allocation_array_trans_id,$TD_allocation_location_trans_id);
			}
			echo $sqlupdateorlocation="UPDATE location SET emp_code='".$TD_allocation_location_emp_code."',
									latt='".$TD_allocation_latt."',
									longi='".$TD_allocation_longi."'
									WHERE trans_id='".$TD_allocation_location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update tour location: ".$sqlupdateorlocation);
			if($rsupdateorlocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of TD allocation
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding TD allocation
			echo $sqlinsertTDlocation="INSERT INTO location SET emp_code='".$TD_allocation_location_emp_code."',
									trans_id='".$TD_allocation_location_trans_id."',
									latt='".$TD_allocation_latt."',
									longi='".$TD_allocation_longi."',
									date='".$TD_allocation_location_date."',
									updatetime='".$location_date."'"; 
			if(mysqli_query($link,$sqlinsertTDlocation))
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
   }// End of for loop
   //print_r($TD_allocation_details_array);
   	for($i=0;$i<count($TD_allocation_details_array);$i++){
		$TD_allocation_trans_id=$TD_allocation_details_array[$i]->TD_allocation_trans_id;
		$TD_allocation_date=$TD_allocation_details_array[$i]->TD_allocation_date;
		$TD_allocation_emp_code=$TD_allocation_details_array[$i]->TD_allocation_emp_code;
		$TD_allocation_product_filter_code=$TD_allocation_details_array[$i]->TD_allocation_product_filter_code;
		$TD_allocation_TD=$TD_allocation_details_array[$i]->TD_allocation_TD;

		// For insert and update of TD allocation table 
		$sql_check_exists = "SELECT TD FROM TD_allocation WHERE emp_code = '".$TD_allocation_emp_code."' AND 
							product_filter_code = '".$TD_allocation_product_filter_code."'";
		$res_check_exists = mysqli_query($link,$sql_check_exists);
		$total_rows = mysqli_num_rows($res_check_exists);
		
		if($total_rows>0)
		{
			$row_check_exists=mysqli_fetch_assoc($res_check_exists);
			echo $sql_update_TD_allocation = "UPDATE TD_allocation SET 
											TD = '".$TD_allocation_TD."'
										    WHERE emp_code = '".$TD_allocation_emp_code."' AND product_filter_code = '".$TD_allocation_product_filter_code."'";
			$res_update_TD_allocation = mysqli_query($link,$sql_update_TD_allocation);
		}
		else
		{
			echo $sql_insert_TD_allocation = "INSERT INTO TD_allocation SET 
											emp_code = '".$TD_allocation_emp_code."', 
											product_filter_code = '".$TD_allocation_product_filter_code."', 
											TD = '".$TD_allocation_TD."'";
			if(mysqli_query($link,$sql_insert_TD_allocation))
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
		//For addition TD allocation log
		echo $sqlinsertTDallocationlog="INSERT INTO TD_allocation_log SET allocation_id	='".$TD_allocation_trans_id."',
								 allocation_date	   ='".$TD_allocation_date."',
								 emp_code			   ='".$TD_allocation_emp_code."',
								 product_filter_code   ='".$TD_allocation_product_filter_code."',
								 TD				   ='".$TD_allocation_TD."'";
		if(mysqli_query($link,$sqlinsertTDallocationlog))
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
}
 /* --------------------END QUERY FOR TD allocation----------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
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
	$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);
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