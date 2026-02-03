<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
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

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><update_customer><customer_code><![CDATA[C/0005546]]></customer_code><phone_no><![CDATA[9830744094]]></phone_no><pin_code><![CDATA[123456]]></pin_code></update_customer></root>";*/

$customer_code = "*ROOT*UPDATE_CUSTOMER*CUSTOMER_CODE";
$phone_no = "*ROOT*UPDATE_CUSTOMER*PHONE_NO";
$pin_code = "*ROOT*UPDATE_CUSTOMER*PIN_CODE";
$address = "*ROOT*UPDATE_CUSTOMER*ADDRESS";

$update_customer_array=array();

$counter = 0;

class xml_update_customer{
	var $customer_code,$phone_no,$pin_code,$address;	
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
    global $current_tag,$counter,$customer_code,$phone_no,$pin_code,$address,$update_customer_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,21)=='*ROOT*UPDATE_CUSTOMER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $customer_code:
				$update_customer_array[$counter] = new xml_update_customer();
				$update_customer_array[$counter]->customer_code = $data;
				break;
			case $phone_no:
				$update_customer_array[$counter]->phone_no = $data;
				break;
			case $pin_code:
				$update_customer_array[$counter]->pin_code = $data;
				break;
			case $address:
				$update_customer_array[$counter]->address = $data;
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
//print_r($attendance_array);
//echo count($attendance_array);
//print_r($order_array);
//print_r($order_details_array);
//print_r($payment_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR Update customer------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
if(count($update_customer_array)>0)
{
	for($x=0;$x<count($update_customer_array);$x++){
		$customer_code=$update_customer_array[$x]->customer_code;
		$phone_no=$update_customer_array[$x]->phone_no;
		$pin_code=$update_customer_array[$x]->pin_code;
		$address=$update_customer_array[$x]->address;
		
		//For update customer
		$sqlupdatecustomer="UPDATE customer_master SET 
						   phone_no				='".addslashes($phone_no)."',
						   pin					='".$pin_code."',address='".$address."',download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
		if(mysqli_query($link,$sqlupdatecustomer))
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
 /* --------------------END QUERY FOR update customer--------------------------------------------------------------------------------------------------------*/
 $sqlupdatelastoperationtime= date("Y-m-d H:i:s");
 
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
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-updatecustomer-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-newcustomer-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}

	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/
	mysqli_close($link);
?>
