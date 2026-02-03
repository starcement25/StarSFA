<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 
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

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160521]]></trans_id><latt><![CDATA[22.5643652]]></latt><longi><![CDATA[88.3568814]]></longi><date><![CDATA[2016-04-18 16:05:21]]></date></location>
<NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160521]]></NOTES_INFO_ID><FEEDBACK><![CDATA[vygvgvgv]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160804]]></trans_id><latt><![CDATA[22.5643697]]></latt><longi><![CDATA[88.3568751]]></longi><date><![CDATA[2016-04-18 16:08:04]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160804]]></NOTES_INFO_ID><FEEDBACK><![CDATA[HV yg gbgb]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418161036]]></trans_id><latt><![CDATA[22.5643596]]></latt><longi><![CDATA[88.3568777]]></longi><date><![CDATA[2016-04-18 16:10:36]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418161036]]></NOTES_INFO_ID><FEEDBACK><![CDATA[fctftvtg]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO></root>";*/

/*$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);*/
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url =APICALLLOGURL."/operationdb-device-information.php?nick_name=$nick_name&emp_code=$emp_code";
insertapilog($datetime,$emp_code,$url,$nick_name);

	
$emp_code="*ROOT*DEVICE_INFO*EMP_CODE";
$device_id = "*ROOT*DEVICE_INFO*DEVICE_ID";
$manufacturer = "*ROOT*DEVICE_INFO*MANUFACTURER";
$model = "*ROOT*DEVICE_INFO*MODEL";
$os_version="*ROOT*DEVICE_INFO*OS_VERSION";
$app_installation_time="*ROOT*DEVICE_INFO*APP_INSTALLATION_TIME";

$device_info_array=array();

$counter = 0;

class xml_device_info{
	var $emp_code,$device_id,$manufacturer,$model,$os_version,$app_installation_time;	
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
    global $current_tag,$counter,$emp_code,$device_id,$manufacturer,$model,$os_version,$app_installation_time,$device_info_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,17)=='*ROOT*DEVICE_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $emp_code:
				$device_info_array[$counter] = new xml_device_info();
				$device_info_array[$counter]->emp_code = $data;
				break;
			case $device_id:
				$device_info_array[$counter]->device_id = $data;
				break;
			case $manufacturer:
				$device_info_array[$counter]->manufacturer = $data;
				break;
			case $model:
				$device_info_array[$counter]->model = $data;
				break;
			case $os_version:
				$device_info_array[$counter]->os_version = $data;
				break;
			case $app_installation_time:
				$device_info_array[$counter]->app_installation_time = $data;
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
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR DEVICE INFO ------------------------------------------------------------------------------------------*/
//print_r($device_info_array);
$device_info_array_trans_id=array();
if(count($device_info_array)>0)
{
		for($x=0;$x<count($device_info_array);$x++){
			$emp_code=$device_info_array[$x]->emp_code;
			$device_id=$device_info_array[$x]->device_id;
			$manufacturer=$device_info_array[$x]->manufacturer;
			$model=$device_info_array[$x]->model;
			$os_version=$device_info_array[$x]->os_version;
			$app_installation_time=$device_info_array[$x]->app_installation_time;
			
			//For checking that device id exist or not for device info
			$sqlchkdeviceinfo="SELECT device_id FROM device_information WHERE device_id='".$device_id."'";
			$reschkdeviceinfo = mysqli_query($link,$sqlchkdeviceinfo) or die(mysqli_error()." Error in check device info: ".$sqlchkdeviceinfo); 
			$rowchkdeviceinfo = mysqli_fetch_assoc($reschkdeviceinfo);
			$countchkdeviceinfo=mysqli_num_rows($reschkdeviceinfo);

			//For updating the device info table
			if($countchkdeviceinfo > 0)
			{
				$sqlupdatedeviceinfo="UPDATE device_information SET emp_code='".$emp_code."',
									manufacturer='".$manufacturer."',	model='".$model."',os_version='".$os_version."',
									app_installation_time='".$app_installation_time."' WHERE device_id='".$device_id."'";
				$resupdatedeviceinfo= mysqli_query($link,$sqlupdatedeviceinfo) or die(mysqli_error()." Error in update device info: ".$sqlupdatedeviceinfo); 
				echo $flag=1;
				//echo $sqlupdatedeviceinfo;
			}
			else
			{
			/*$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;*/
	
			//For Insert into the device_information
			$sqlinsertdeviceinfo="INSERT INTO device_information SET emp_code='".$emp_code."',
								device_id='".$device_id."',
								manufacturer='".$manufacturer."',
								model='".$model."',
								os_version='".$os_version."',
								sl_no = '0',
								app_installation_time='".$app_installation_time."',
								download_time=CURRENT_TIMESTAMP()"; 
								//echo $sqlinsertdeviceinfo;
		  if(mysqli_query($link,$sqlinsertdeviceinfo))
			{
				$flag=5;
				echo $flag=1;
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
 /* --------------------END QUERY FOR DEVICE INFO--------------------------------------------------------------------------------------------------------*/

if($flag==50)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==60)
{
     $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
mysqli_close($link);
?>
