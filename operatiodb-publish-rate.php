<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
header('Content-Type: text/plain');
// $data = file_get_contents("php://input");

// if (empty($data)) {
//     die("Error: Empty XML input");
// }

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
$last_update_time=$_REQUEST['last_update_time'] ??'';
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";

						
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12003]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12005]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12005]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12007]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12007]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12008]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12008]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12009]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12015]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12015]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12014]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12014]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12017]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12017]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12023]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12023]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12028]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BAKERY PRODUCTS]]></product_sub_group_name><prod_code><![CDATA[12030]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BY PRODUCT]]></product_sub_group_name><prod_code><![CDATA[12125]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[BY PRODUCT]]></product_sub_group_name><prod_code><![CDATA[12126]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (CSO)]]></product_sub_group_name><prod_code><![CDATA[12144]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (CSO)]]></product_sub_group_name><prod_code><![CDATA[12143]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (MAIZE)]]></product_sub_group_name><prod_code><![CDATA[12121]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALM)]]></product_sub_group_name><prod_code><![CDATA[12116]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALM)]]></product_sub_group_name><prod_code><![CDATA[12118]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12137]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12138]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12139]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12133]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12134]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12132]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12136]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (PALMOLEIN)]]></product_sub_group_name><prod_code><![CDATA[12135]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (RMO)]]></product_sub_group_name><prod_code><![CDATA[12145]]></prod_code><publish_rate_type><![CDATA[]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE (SOYABEAN)]]></product_sub_group_name><prod_code><![CDATA[12123]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[LOOSE PALM KERNEL OIL]]></product_sub_group_name><prod_code><![CDATA[12122]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[2020-03-18 19:09:17]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[MUSTARD OIL]]></product_sub_group_name><prod_code><![CDATA[12111]]></prod_code><publish_rate_type><![CDATA[]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[MUSTARD OIL]]></product_sub_group_name><prod_code><![CDATA[12109]]></prod_code><publish_rate_type><![CDATA[]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PALMOLEIN]]></product_sub_group_name><prod_code><![CDATA[12073]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PALMOLEIN]]></product_sub_group_name><prod_code><![CDATA[12038]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PALMOLEIN]]></product_sub_group_name><prod_code><![CDATA[12071]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PALMOLEIN]]></product_sub_group_name><prod_code><![CDATA[12075]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12050]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12041]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12060]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12061]]></prod_code><publish_rate_type><![CDATA[Current]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12058]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12057]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[PL VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12059]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12101]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[2020-03-26 17:00:25]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12040]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[2020-03-26 17:00:25]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12102]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[2020-03-26 17:00:25]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[SOYABEAN]]></product_sub_group_name><prod_code><![CDATA[12098]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[2020-03-26 17:00:25]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12034]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12085]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12086]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12099]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12037]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12076]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12035]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12093]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12077]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12124]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12081]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info><publish_rate_info><product_sub_group_code><![CDATA[]]></product_sub_group_code><product_sub_group_name><![CDATA[VANASPATI]]></product_sub_group_name><prod_code><![CDATA[12078]]></prod_code><publish_rate_type><![CDATA[Previous]]></publish_rate_type><publish_rate_date><![CDATA[]]></publish_rate_date></publish_rate_info></root>";*/

$product_sub_group_code = "*ROOT*PUBLISH_RATE_INFO*PRODUCT_SUB_GROUP_CODE";
$product_sub_group_name = "*ROOT*PUBLISH_RATE_INFO*PRODUCT_SUB_GROUP_NAME";
$prod_code = "*ROOT*PUBLISH_RATE_INFO*PROD_CODE";
$publish_rate_type = "*ROOT*PUBLISH_RATE_INFO*PUBLISH_RATE_TYPE";
$publish_rate_date = "*ROOT*PUBLISH_RATE_INFO*PUBLISH_RATE_DATE";

$publish_rate_info_array=array();
$counter = 0;
class xml_publish_rate{
   var $product_sub_group_code,$product_sub_group_name,$prod_code,$publish_rate_type,$publish_rate_date;	
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
    global $current_tag,$product_sub_group_code, $product_sub_group_name,$prod_code,$publish_rate_type,$publish_rate_date,$counter,$publish_rate_info_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,23)=='*ROOT*PUBLISH_RATE_INFO')
	{
		switch($current_tag){
			case $product_sub_group_code:
				$publish_rate_info_array[$counter] = new xml_publish_rate();
				$publish_rate_info_array[$counter]->product_sub_group_code = $data;
				break;
			case $product_sub_group_name:
				$publish_rate_info_array[$counter]->product_sub_group_name = $data;
				break;
			case $prod_code:
				$publish_rate_info_array[$counter]->prod_code = $data;
				break;
			case $publish_rate_type:
				$publish_rate_info_array[$counter]->publish_rate_type = $data;
				break;
			case $publish_rate_date:
				$publish_rate_info_array[$counter]->publish_rate_date = $data;
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
// if(!(xml_parse($xml_parser, $data, true))){
//     die("Error on line " . xml_get_current_line_number($xml_parser));
// }
// echo 5626565;
// die;
xml_parser_free($xml_parser);
//print_r($attendance_array);
//print_r($payment_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* ------------------------------------------------START QUERY FOR ATTENDANCE---------------------------------------------------------------------*/
if(count($publish_rate_info_array)>0)
{
	$sqllatestcreatedate="select MAX(create_date) AS max_create_date FROM sauda_mrp";
	$rslatestcreatedate=mysqli_query($link,$sqllatestcreatedate);
	$rowlatestcreatedate=mysqli_fetch_assoc($rslatestcreatedate);
	$latest_create_date=$rowlatestcreatedate['max_create_date'];
	$latest_create_date_day=substr($latest_create_date,0,10);
	$latest_create_date_final=substr($latest_create_date,0,16);
	
	$sqlprevcreatedate="select MAX(create_date) AS max_create_date_prev FROM sauda_mrp where create_date < '".$latest_create_date."'";
	$rsprevcreatedate=mysqli_query($link,$sqlprevcreatedate);
	$rowprevcreatedate=mysqli_fetch_assoc($rsprevcreatedate);
	$prev_create_date=$rowprevcreatedate['max_create_date_prev'];
	//$latest_create_date_day=substr($latest_create_date,0,10);
	$prev_create_date_final=substr($prev_create_date,0,16);
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$price_release_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

	for($x=0;$x<count($publish_rate_info_array);$x++){
		$product_sub_group_code=$publish_rate_info_array[$x]->product_sub_group_code;
		$product_sub_group_name=$publish_rate_info_array[$x]->product_sub_group_name;
		$prod_code=$publish_rate_info_array[$x]->prod_code;
		$publish_rate_type=$publish_rate_info_array[$x]->publish_rate_type;
		$publish_rate_date=$publish_rate_info_array[$x]->publish_rate_date;
		
		$sqldnsprod="SELECT dns_prod_code FROM product_master WHERE prod_code='".$prod_code."'";
		$rsdnsprod=mysqli_query($link,$sqldnsprod);
		$rowdnsprod=mysqli_fetch_assoc($rsdnsprod);
		$dns_prod_code=$rowdnsprod['dns_prod_code'];
		
	  if(strtolower($publish_rate_type)=='previous' && $publish_rate_date!=''){
		$sqlupdateprevrate="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".$prod_code."'";
		 mysqli_query($link,$sqlupdateprevrate);
		$sqlupdatemrpprev="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$emp_code."' WHERE product_code='".$prod_code."' AND release_date='".$publish_rate_date."'"; 
	  /*$sqlupdatemrpprevious="UPDATE sauda_mrp SET release_date=CURRENT_TIMESTAMP()
							WHERE product_code='".$prod_code[$i-1]."' AND acedns='Y'";*/
	   if(mysqli_query($link,$sqlupdatemrpprev)){
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
						  			 mapped_prod_code='".$dns_prod_code."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
			  $rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];
			  $sqlupdateprevratedependent="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	   if(mysqli_query($link,$sqlupdateprevratedependent))
			    {
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				
			  $sqlupdatemrpprevdependent="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$emp_code."' 
			  							WHERE product_code='".${prod_code.$dependent_prod_val}."' AND release_date='".$publish_rate_date."'";
			  if(mysqli_query($link,$sqlupdatemrpprevdependent))
			    {
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			   $sqlupdateindustrialratedependent="UPDATE industrial_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	 if(mysqli_query($link,$sqlupdateindustrialratedependent))
			    {
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			  
			  $sqlupdateindustrialprevdependent="UPDATE industrial_rate SET acedns='Y',publish_date=CURRENT_TIMESTAMP(),published_by='".$emp_code."' 
			  									WHERE product_code='".${prod_code.$dependent_prod_val}."' 
			  										AND release_date='".$publish_rate_date."'";
			 if(mysqli_query($link,$sqlupdateindustrialprevdependent))
			    {
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
	      }
	   }
      }
	  if(strtolower($publish_rate_type)=='current'){
		  	$sqlupdateprevrate="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".$prod_code."'";
		  mysqli_query($link,$sqlupdateprevrate);
		  /*$sqlupdatemrpcurrent="UPDATE sauda_mrp SET release_date=CURRENT_TIMESTAMP(),acedns='Y'
								WHERE product_code='".$prod_code[$i-1]."' AND acedns='N' AND 
								release_date='0000-00-00 00:00:00'";*/
		   $sqlupdatemrpcurrent="UPDATE sauda_mrp SET release_date='".$price_release_date."',acedns='Y',
		   						publish_date=CURRENT_TIMESTAMP(),published_by='".$emp_code."' WHERE product_code='".$prod_code."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";						
		  if(mysqli_query($link,$sqlupdatemrpcurrent))
			{
				$flag=6;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		   $sqlfetchdependentprod="SELECT DISTINCT prod_code FROM product_unit_coversion_matrix WHERE 
								 mapped_prod_code='".$dns_prod_code."' AND acedns='Y' AND mapped_prod_code!=prod_code";
		   $rsfetchdependentprod=mysqli_query($link,$sqlfetchdependentprod);
		   while($rowfetchdependentprod=mysqli_fetch_assoc($rsfetchdependentprod))
		   {
			  $dependent_prod_val=$rowfetchdependentprod['prod_code'];
			  $sqlconversiondependent="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$dependent_prod_val."'";
			  $rsconversiondependent=mysqli_query($link,$sqlconversiondependent);
			  $rowconversiondependent=mysqli_fetch_assoc($rsconversiondependent);
			  ${prod_code.$dependent_prod_val}=$rowconversiondependent['prod_code'];
			  ${prod_desc.$dependent_prod_val}=$rowconversiondependent['prod_desc'];

			  $sqlupdateprevratedependent="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
				if(mysqli_query($link,$sqlupdateprevratedependent))
				{
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			  $sqlupdateindustrialratedependent="UPDATE industrial_rate SET acedns='N' WHERE product_code='".${prod_code.$dependent_prod_val}."'";
		 	 if(mysqli_query($link,$sqlupdateindustrialratedependent))
				{
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			$sqlupdatemrpcurrdependent="UPDATE sauda_mrp SET release_date='".$price_release_date."',acedns='Y',publish_date=CURRENT_TIMESTAMP(),
										published_by='".$emp_code."' WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";
			  if(mysqli_query($link,$sqlupdatemrpcurrdependent))
				{
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			$sqlupdateindustrialcurrdependent="UPDATE industrial_rate SET release_date='".$price_release_date."',acedns='Y',publish_date=CURRENT_TIMESTAMP()
								,published_by='".$emp_code."' WHERE product_code='".${prod_code.$dependent_prod_val}."' AND acedns='N' AND 
								SUBSTRING(create_date,1,16)='".$latest_create_date_final."'";
			  if(mysqli_query($link,$sqlupdateindustrialcurrdependent))
				{
					$flag=6;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
		    }
		  }
	  $flg=1;
    }
	$sqlselduplicate="SELECT product_code FROM sauda_mrp where acedns='Y' AND 
					release_date='".$price_release_date."' GROUP BY product_code HAVING count(product_code) > 1 ";
	$rsselduplicate=mysqli_query($link,$sqlselduplicate);
	while($rowselduplicate=mysqli_fetch_assoc($rsselduplicate))
	{
		$prod_code_duplicate=$rowselduplicate['product_code'];
		$sqlupdateparentrate="UPDATE sauda_mrp SET acedns='N' WHERE product_code='".$prod_code_duplicate."' AND parent_child='parent' 
								AND release_date='".$price_release_date."'";
		if(mysqli_query($link,$sqlupdateparentrate))
		{
			$flag=6;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		$sqlupdatechildrate="UPDATE sauda_mrp SET acedns='Y',publish_date=CURRENT_TIMESTAMP()
								,published_by='".$emp_code."' WHERE product_code='".$prod_code_duplicate."' AND parent_child='child' 
								AND release_date='".$price_release_date."'";
		if(mysqli_query($link,$sqlupdatechildrate))
		{
			$flag=6;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}						
	}
	//exit();
}// End attendance array if 

 /* --------------------END QUERY FOR ATTENDANCE------------------------------------------------------------------------------------------------------------*/

if($flag==6)
{
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operatiodb-publish-rate.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
