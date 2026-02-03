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
/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><invoice_information><invoice_details><invoice_no><![CDATA[RKBKT/E0036/00001]]></invoice_no><invoice_date><![CDATA[2017-01-11]]></invoice_date><order_no><![CDATA[OE000620161201]]></order_no><customer_code><![CDATA[C/00001]]></customer_code><chronological_no><![CDATA[00001]]></chronological_no></invoice_details>
<invoice_details><invoice_no><![CDATA[RKBKT/E0036/00002]]></invoice_no><invoice_date><![CDATA[2017-01-11]]></invoice_date><order_no><![CDATA[OE000620161202]]></order_no><customer_code><![CDATA[C/00002]]></customer_code><chronological_no><![CDATA[00002]]></chronological_no></invoice_details></invoice_information></root>";*/

$invoice_no = "*ROOT*INVOICE_INFORMATION*INVOICE_DETAILS*INVOICE_NO";
$invoice_date = "*ROOT*INVOICE_INFORMATION*INVOICE_DETAILS*INVOICE_DATE";
$order_no = "*ROOT*INVOICE_INFORMATION*INVOICE_DETAILS*ORDER_NO";
$customer_code = "*ROOT*INVOICE_INFORMATION*INVOICE_DETAILS*CUSTOMER_CODE";
$chronological_no = "*ROOT*INVOICE_INFORMATION*INVOICE_DETAILS*CHRONOLOGICAL_NO";

$invoice_array=array();

$counter = 0;

class xml_invoice_information{
	var $invoice_no,$invoice_date,$order_no,$customer_code,$chronological_no;	
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
    global $current_tag,$counter,$invoice_no,$invoice_date,$order_no,$customer_code,$chronological_no,$invoice_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,25)=='*ROOT*INVOICE_INFORMATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $invoice_no:
				$invoice_array[$counter] = new xml_invoice_information();
				$invoice_array[$counter]->invoice_no = $data;
				break;
			case $invoice_date:
				$invoice_array[$counter]->invoice_date = $data;
				break;
			case $order_no:
				$invoice_array[$counter]->order_no = $data;
				break;
			case $customer_code:
				$invoice_array[$counter]->customer_code = $data;
				break;
			case $chronological_no:
				$invoice_array[$counter]->chronological_no = $data;
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
/* --------------------START QUERY FOR New customer------------------------------------------------------------------------------------------------*/
//print_r($invoice_array);
$invoice_no_array=array();
if(count($invoice_array)>0)
{
	$refreshflag=0;
	for($x=0;$x<count($invoice_array);$x++){
		$invoice_no=$invoice_array[$x]->invoice_no;
		$invoice_date=$invoice_array[$x]->invoice_date;
		$order_no=$invoice_array[$x]->order_no;
		$customer_code=$invoice_array[$x]->customer_code;
		$chronological_no=$invoice_array[$x]->chronological_no;
		
		//For checking that invoice no exists or not
		$sqlchkinvoice="SELECT * FROM invoice_information WHERE invoice_no='".$invoice_no."'";
		$reschkinvoice = mysqli_query($link,$sqlchkinvoice) or die(mysqli_error()." Error in check invoice: ".$sqlchkinvoice); 
		$rowchkinvoice = mysqli_fetch_assoc($reschkinvoice);
		$countchkinvoice=mysqli_num_rows($reschkinvoice);
		
		//For update the invoce information table for existing invoice no
		if($countchkinvoice>0)
		{
			if(!in_array($invoice_no,$invoice_no_array))
			{
				array_push($invoice_no_array,$invoice_no);
			}
			$sqlupdateinvoice="UPDATE invoice_information SET invoice_date='".$invoice_date."',
									order_no='".$order_no."',
									customer_code='".$customer_code."'
									WHERE invoice_no='".$invoice_no."'";
			$rsupdateinvoice=mysqli_query($link,$sqlupdateinvoice) or die(mysqli_error()." Error in update invoice: ".$sqlupdateinvoice);
			if($rsupdateinvoice)
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
		
		//For Insert into the invoce information table for new invoice
		$sqlinsertinvoice="INSERT INTO invoice_information SET invoice_no='".$invoice_no."',
									invoice_date='".$invoice_date."',
									order_no='".$order_no."',
									customer_code='".$customer_code."',
									chronological_no='".$chronological_no."',
									updatetime=CURRENT_TIMESTAMP()"; 
	  if(mysqli_query($link,$sqlinsertinvoice))
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
}
 /* --------------------END QUERY FOR New customer--------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
  mysqli_query($link,"COMMIT");
  echo $flag=1;
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-invoice-generation.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
