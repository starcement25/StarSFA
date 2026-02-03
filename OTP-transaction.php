<?php
//error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$operation_type=$_REQUEST['operation_type'];
$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
	}
}
//$body=str_replace("'",'"',$body);
if(in_array('transporter',$menu_access_array) && $operation_type=='transporter') //Start of transporter
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><TRANSPORTER_DETAILS><DO_no><![CDATA[DOE004620190808101341]]></DO_no><VEHICLE_NO><![CDATA[hjffufu]]></VEHICLE_NO><DRIVER_NAME><![CDATA[fjjfhxhx]]></DRIVER_NAME><DRIVER_MOBILE><![CDATA[6886686886]]></DRIVER_MOBILE><ETA_DO_point><![CDATA[11:11]]></ETA_DO_point></TRANSPORTER_DETAILS></OTP></root>";*/

$DO_no = "*ROOT*OTP*TRANSPORTER_DETAILS*DO_NO";	
$vehicle_no = "*ROOT*OTP*TRANSPORTER_DETAILS*VEHICLE_NO";
$driver_name = "*ROOT*OTP*TRANSPORTER_DETAILS*DRIVER_NAME";
$driver_mobile ="*ROOT*OTP*TRANSPORTER_DETAILS*DRIVER_MOBILE";
$ETA_DO_point ="*ROOT*OTP*TRANSPORTER_DETAILS*ETA_DO_point";
$trans_response_id ="*ROOT*OTP*TRANSPORTER_DETAILS*TRANS_RESPONSE_ID";

$transporter_array = array();
$counter = 0;

class xml_transporter{
	var $DO_no,$vehicle_no,$driver_name,$driver_mobile,$ETA_DO_point,$trans_response_id;
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
    global $current_tag,$transporter_array,$counter,$DO_no,$vehicle_no,$driver_name,$driver_mobile,$ETA_DO_point,$trans_response_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $DO_no:
				$transporter_array[$counter] = new xml_transporter();
				$transporter_array[$counter]->DO_no = $data;
				break;
			case $vehicle_no:
				$transporter_array[$counter]->vehicle_no = $data;
				break;
			case $driver_name:
				$transporter_array[$counter]->driver_name = $data;
				break;
			case $driver_mobile:
				$transporter_array[$counter]->driver_mobile = $data;
				break;
			case $ETA_DO_point:
				$transporter_array[$counter]->ETA_DO_point = $data;
				break;
			case $trans_response_id:
				$transporter_array[$counter]->trans_response_id = $data;
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
//print_r($transporter_array);
/* -------------------------------------------------------START QUERY FOR Transporter details-----------------------------------------------------------------------*/
if(count($transporter_array)>0)
{
	for($x=0;$x<count($transporter_array);$x++){
		$DO_no=$transporter_array[$x]->DO_no;
		$vehicle_no=$transporter_array[$x]->vehicle_no;
		$driver_name=$transporter_array[$x]->driver_name;
		$driver_mobile=$transporter_array[$x]->driver_mobile;
		$ETA_DO_point=$transporter_array[$x]->ETA_DO_point;
		$trans_response_id=$transporter_array[$x]->trans_response_id;
		
		$sqltransportername="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
		$rstransportername=mysqli_query($link,$sqltransportername);
		$rowtransportername=mysqli_fetch_assoc($rstransportername);
		$transpoter_name=$rowtransportername['emp_name'];
		
		$sqlDOdetails="SELECT customer_code,destination,DO_date FROM DO_transaction WHERE DO_no='".$DO_no."'";
		$rsDOdetails=mysqli_query($link,$sqlDOdetails);
		$rowDOdetails=mysqli_fetch_assoc($rsDOdetails);
		$customer_code=$rowDOdetails['customer_code'];
		$destination=$rowDOdetails['destination'];
		$DO_date=$rowDOdetails['DO_date'];
		
		$sqlinserttransporter="INSERT INTO DO_tracking SET 
								customer_code ='".$customer_code."',
								destination   ='".$destination."',
								DO_no   ='".$DO_no."',
								DO_date 	='".$DO_date."',
								transporter_name 	='".$transpoter_name."',
								vehicle_no 	='".$vehicle_no."',
								driver_name 	='".$driver_name."',
								phone_no 	='".$driver_mobile."',
								ETA_DO_point 	='".$ETA_DO_point."',
								trans_response_id ='".$trans_response_id."',
								trans_response_date =CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinserttransporter))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
	//if($flag==5){}
}
 /* --------------------END QUERY FOR Transporter details--------------------------------------------------------------------------------------------------------*/
	if($flag==5)
	{
	   mysqli_query($link,"COMMIT");
	   echo $flag=1;
	}
}// End of transporter
if(in_array('gate_keeper_in',$menu_access_array) && $operation_type=='gate_keeper_in') // Start of Gate Keeper 1
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><GATE_KEERER1_DETAILS><VEHICLE_NO><![CDATA[Wb26av8345]]></VEHICLE_NO><ARRIVAL_GATE_ID><![CDATA[AGE000120190909131020]]></ARRIVAL_GATE_ID></GATE_KEERER1_DETAILS></OTP></root>";*/
$vehicle_no = "*ROOT*OTP*GATE_KEERER1_DETAILS*VEHICLE_NO";
$arrival_gate_id= "*ROOT*OTP*GATE_KEERER1_DETAILS*ARRIVAL_GATE_ID";	
$gate_keeper1_array = array();
$countergate1 = 0;
class xml_gate_keeper1{
	var $vehicle_no,$arrival_gate_id;
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
    global $current_tag,$gate_keeper1_array,$countergate1,$vehicle_no,$arrival_gate_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$gate_keeper1_array[$countergate1] = new xml_gate_keeper1();
				$gate_keeper1_array[$countergate1]->vehicle_no = $data;
				break;
			case $arrival_gate_id:
				$gate_keeper1_array[$countergate1]->arrival_gate_id = $data;
				$countergate1++;
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
/* -------------------------------------------------------START QUERY FOR GATE KEEPER1-----------------------------------------------------------------------*/
if(count($gate_keeper1_array)>0)
{
	for($x=0;$x<count($gate_keeper1_array);$x++){
		$vehicle_no=$gate_keeper1_array[$x]->vehicle_no;
		$arrival_gate_id=$gate_keeper1_array[$x]->arrival_gate_id;
		
		$sqlupdategate1="UPDATE DO_tracking SET 
						arrival_gate_id='".$arrival_gate_id."',
						arrival_date_gate =CURRENT_TIMESTAMP() 
						WHERE  	vehicle_no='".$vehicle_no."' AND arrival_date_gate='0000-00-00 00:00:00'";
		if(mysqli_query($link,$sqlupdategate1))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
	//if($flag==5){}
}
 /* -----------------------------------------------------------END QUERY FOR GATE KEEPER1---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End GATE KEEPER1
if(in_array('despatch_in',$menu_access_array) && $operation_type=='despatch_in') // Start of Despatch in
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><DESPATCH_IN_DETAILS><VEHICLE_NO><![CDATA[DL26AV8345]]></VEHICLE_NO><DESPATCH_APPROVAL_ID><![CDATA[DAE000120190909131833]]></DESPATCH_APPROVAL_ID></DESPATCH_IN_DETAILS></OTP></root>";*/
$vehicle_no = "*ROOT*OTP*DESPATCH_IN_DETAILS*VEHICLE_NO";
$despatch_approval_id = "*ROOT*OTP*DESPATCH_IN_DETAILS*DESPATCH_APPROVAL_ID";	
$despatch_in_array = array();
$counterdespatch = 0;
class xml_despatch_in{
	var $vehicle_no,$despatch_approval_id;
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
    global $current_tag,$despatch_in_array,$counterdespatch,$vehicle_no,$despatch_approval_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$despatch_in_array[$counterdespatch] = new xml_despatch_in();
				$despatch_in_array[$counterdespatch]->vehicle_no = $data;
				break;
			case $despatch_approval_id:
				$despatch_in_array[$counterdespatch]->despatch_approval_id = $data;
				$counterdespatch++;
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
/* -------------------------------------------------------START QUERY FOR Despatch in-----------------------------------------------------------------------*/
if(count($despatch_in_array)>0)
{
	for($x=0;$x<count($despatch_in_array);$x++){
		$vehicle_no=$despatch_in_array[$x]->vehicle_no;
		$despatch_approval_id=$despatch_in_array[$x]->despatch_approval_id;
		
		$sqlupdatedespatchin="UPDATE DO_tracking SET 
						despatch_approval_id  ='".$despatch_approval_id."',
						despatch_approval_date =CURRENT_TIMESTAMP() 
						WHERE vehicle_no='".$vehicle_no."' AND despatch_approval_date='0000-00-00 00:00:00'";
		if(mysqli_query($link,$sqlupdatedespatchin))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
	//if($flag==5){}
}
 /* -----------------------------------------------------------END QUERY FOR Despatch in---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End Despatch in
if(in_array('gate_keeper2',$menu_access_array) && $operation_type=='gate_keeper2') // Start of Gate keeper2
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><GATE_KEERER2_DETAILS><VEHICLE_NO><![CDATA[DL26AV8345]]></VEHICLE_NO><GATE_VEHICLE_ID><![CDATA[GVE000120190909135223]]></GATE_VEHICLE_ID></GATE_KEERER2_DETAILS></OTP></root>";*/
$vehicle_no = "*ROOT*OTP*GATE_KEERER2_DETAILS*VEHICLE_NO";
$gate_vehicle_in_id = "*ROOT*OTP*GATE_KEERER2_DETAILS*GATE_VEHICLE_ID";	
$gate_keeper2_array = array();
$countergate2 = 0;
class xml_gate_keeper2{
	var $vehicle_no,$gate_vehicle_in_id;
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
    global $current_tag,$gate_keeper2_array,$countergate2,$vehicle_no,$gate_vehicle_in_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$gate_keeper2_array[$countergate2] = new xml_gate_keeper2();
				$gate_keeper2_array[$countergate2]->vehicle_no = $data;
				break;
			case $gate_vehicle_in_id:
				$gate_keeper2_array[$countergate2]->gate_vehicle_in_id = $data;
				$countergate2++;
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
/* -------------------------------------------------------START QUERY FOR Gate keeper2-----------------------------------------------------------------------*/
if(count($gate_keeper2_array)>0)
{
	for($x=0;$x<count($gate_keeper2_array);$x++){
		$vehicle_no=$gate_keeper2_array[$x]->vehicle_no;
		$gate_vehicle_in_id=$gate_keeper2_array[$x]->gate_vehicle_in_id;
		$sqlupdategate2="UPDATE DO_tracking SET 
							gate_vehicle_in_id='".$gate_vehicle_in_id."',
							gate_vehicle_in_date =CURRENT_TIMESTAMP() 
						 WHERE  vehicle_no='".$vehicle_no."' AND gate_vehicle_in_date='0000-00-00 00:00:00'";
		if(mysqli_query($link,$sqlupdategate2))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR Gate keeper2---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End Gate Keeper2
if(in_array('weighbridge_in',$menu_access_array) && $operation_type=='weighbridge_in') // Start of weigh Bridge
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><WEIGHBRIDGE_IN_DETAILS><WB_VEHICLE_NO><![CDATA[D4]]></WB_VEHICLE_NO><TARE_WEIGHT><![CDATA[8698]]></TARE_WEIGHT><SERIAL_NO><![CDATA[xhxh]]></SERIAL_NO><WB_IN_ID><![CDATA[WIE000120190909163743]]></WB_IN_ID></WEIGHBRIDGE_IN_DETAILS></OTP></root>";*/
$vehicle_no = "*ROOT*OTP*WEIGHBRIDGE_IN_DETAILS*WB_VEHICLE_NO";
$tare_weight = "*ROOT*OTP*WEIGHBRIDGE_IN_DETAILS*TARE_WEIGHT";
$serial_no = "*ROOT*OTP*WEIGHBRIDGE_IN_DETAILS*SERIAL_NO";
$wb_in_id="*ROOT*OTP*WEIGHBRIDGE_IN_DETAILS*WB_IN_ID";		
$weighbridge_in_array = array();
$counterwbin = 0;
class xml_weighbridge_in{
	var $vehicle_no,$gate_vehicle_in_id;
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
    global $current_tag,$weighbridge_in_array,$counterwbin,$vehicle_no,$tare_weight,$serial_no,$wb_in_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$weighbridge_in_array[$counterwbin] = new xml_weighbridge_in();
				$weighbridge_in_array[$counterwbin]->vehicle_no = $data;
				break;
			case $tare_weight:
				$weighbridge_in_array[$counterwbin]->tare_weight = $data;
				break;
			case $serial_no:
				$weighbridge_in_array[$counterwbin]->serial_no = $data;
				break;
			case $wb_in_id:
				$weighbridge_in_array[$counterwbin]->wb_in_id = $data;
				$counterwbin++;
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
/* -------------------------------------------------------START QUERY FOR weighbridge in-----------------------------------------------------------------------*/
if(count($weighbridge_in_array)>0)
{
	for($x=0;$x<count($weighbridge_in_array);$x++){
		$vehicle_no=$weighbridge_in_array[$x]->vehicle_no;
		$tare_weight=$weighbridge_in_array[$x]->tare_weight;
		$serial_no=$weighbridge_in_array[$x]->serial_no;
		$wb_in_id=$weighbridge_in_array[$x]->wb_in_id;
		
		$sqlupdateweighbridgein="UPDATE DO_tracking SET 
								wb_vehicle_no='".$vehicle_no."',
								tare_weight='".$tare_weight."',
								sl_no='".$serial_no."',
								wb_in_id='".$wb_in_id."',
								wb_in_date =CURRENT_TIMESTAMP() 
						 		WHERE  vehicle_no='".$vehicle_no."' AND wb_in_date='0000-00-00 00:00:00'";
		if(mysqli_query($link,$sqlupdateweighbridgein))
		{
			$flag=5;
			$sqlselDOno="SELECT DO_no FROM DO_tracking WHERE wb_vehicle_no='".$vehicle_no."' AND loading_date='0000-00-00 00:00:00'";
			$rsselDOno=mysqli_query($link,$sqlselDOno);
			$rowselDOno=mysqli_fetch_assoc($rsselDOno);
			$DO_no=$rowselDOno['DO_no'];
			$sqlselwblog="SELECT DO_no FROM weighbridge_log WHERE DO_no='".$DO_no."' AND vehicle_no='".$vehicle_no."'";
			$rsselwblog=mysqli_query($link,$sqlselwblog);
			$cntselwblog=mysqli_num_rows($rsselwblog);
			if($cntselwblog==0)
			{
				$sqlinsertwblog="INSERT INTO weighbridge_log SET 	DO_no='".$DO_no."',
									vehicle_no ='".$vehicle_no."',
									in_time		=CURRENT_TIMESTAMP(),
									tare_weight='".$tare_weight."'";
				mysqli_query($link,$sqlinsertwblog);					
			}
			$sqlupdateDostatus="UPDATE DO_transaction SET 	DO_status='weighbridge_in' WHERE DO_no='".$DO_no."' AND DO_status='approved'";
			mysqli_query($link,$sqlupdateDostatus);
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR weighbridge in---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End weigh Bridge
if(in_array('loading',$menu_access_array) && $operation_type=='loading') // Start of loading
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP><DO_TRACKING><VEHICLE_NO><![CDATA[ZZ]]></VEHICLE_NO><LOADING_ID><![CDATA[LOE000120190923171333]]></LOADING_ID><BAY_NO><![CDATA[gg]]></BAY_NO><LOADING_DATE><![CDATA[2019-09-23 17:13]]></LOADING_DATE><GRN_ATTACHMENT><![CDATA[LOE000120190923170619.jpeg]]></GRN_ATTACHMENT><GRN_ATTACHMENT_DATE><![CDATA[2019-09-23 17:13]]></GRN_ATTACHMENT_DATE >      </DO_TRACKING>     <LOADING_DETAILS>         <SAUDA_NO><![CDATA[FTE001620190905124304]]></SAUDA_NO>         <customer_code><![CDATA[C/0000109]]></customer_code>         <DESTINATION><![CDATA[RT/48]]></DESTINATION>         <DO_NO><![CDATA[DOE001620190905124357]]></DO_NO>         <SKU_CODE><![CDATA[12132]]></SKU_CODE>         <BATCH_NO><![CDATA[S001]]></BATCH_NO>         <DO_QTY><![CDATA[1]]></DO_QTY>         <DO_RATE><![CDATA[1040.29]]></DO_RATE>         <DO_AMOUNT><![CDATA[1040.29]]></DO_AMOUNT>         <DO_DATE><![CDATA[2019-09-20 13:36:33]]></DO_DATE>         <LOADING_QTY><![CDATA[99]]></LOADING_QTY>      </LOADING_DETAILS>     <LOADING_DETAILS>         <SAUDA_NO><![CDATA[FTE001620190905124304]]></SAUDA_NO>         <customer_code><![CDATA[C/0000109]]></customer_code>         <DESTINATION><![CDATA[RT/48]]></DESTINATION>         <DO_NO><![CDATA[DOE001620190905124357]]></DO_NO>         <SKU_CODE><![CDATA[12132]]></SKU_CODE>         <BATCH_NO><![CDATA[S034]]></BATCH_NO>         <DO_QTY><![CDATA[1]]></DO_QTY>         <DO_RATE><![CDATA[1040.29]]></DO_RATE>         <DO_AMOUNT><![CDATA[1040.29]]></DO_AMOUNT>         <DO_DATE><![CDATA[2019-09-20 13:36:33]]></DO_DATE>         <LOADING_QTY><![CDATA[222]]></LOADING_QTY>      </LOADING_DETAILS>   </OTP></root>";*/
$vehicle_no = "*ROOT*OTP*DO_TRACKING*VEHICLE_NO";
$loading_id = "*ROOT*OTP*DO_TRACKING*LOADING_ID";
$bay_no = "*ROOT*OTP*DO_TRACKING*BAY_NO";
$loading_date = "*ROOT*OTP*DO_TRACKING*LOADING_DATE";
$loading_date = "*ROOT*OTP*DO_TRACKING*LOADING_DATE";
$grn_attachment = "*ROOT*OTP*DO_TRACKING*GRN_ATTACHMENT";
$grn_attachment_date = "*ROOT*OTP*DO_TRACKING*GRN_ATTACHMENT_DATE";

$sauda_no = "*ROOT*OTP*LOADING_DETAILS*SAUDA_NO";
$customer_code = "*ROOT*OTP*LOADING_DETAILS*CUSTOMER_CODE";
$destination = "*ROOT*OTP*LOADING_DETAILS*DESTINATION";
$DO_no ="*ROOT*OTP*LOADING_DETAILS*DO_NO";
$sku_code ="*ROOT*OTP*LOADING_DETAILS*SKU_CODE";
$batch_no ="*ROOT*OTP*LOADING_DETAILS*BATCH_NO";
$DO_qty ="*ROOT*OTP*LOADING_DETAILS*DO_QTY";
$DO_rate ="*ROOT*OTP*LOADING_DETAILS*DO_RATE";
$DO_amount ="*ROOT*OTP*LOADING_DETAILS*DO_AMOUNT";
$DO_date ="*ROOT*OTP*LOADING_DETAILS*DO_DATE";
$loading_qty ="*ROOT*OTP*LOADING_DETAILS*LOADING_QTY";

$DO_tracking_array=array();
$loading_details_array=array();

$counterDO=0;
$counterloadingdetails=0;
class xml_do_tracking{
	var $vehicle_no,$loading_id,$bay_no,$loading_date,$grn_attachment,$grn_attachment_date;
}
class xml_loading_details{
	var $sauda_no,$customer_code,$destination,$DO_no,$sku_code,$batch_no,$DO_qty,$DO_rate,$DO_amount,$DO_date,$loading_qty;
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
    global $current_tag,$DO_tracking_array,$loading_details_array,$counterDO,$counterloadingdetails,$vehicle_no,$loading_id,$bay_no,$loading_date,$grn_attachment,$grn_attachment_date,$sauda_no,$customer_code,$destination,$DO_no,$sku_code,$batch_no,$DO_qty,$DO_rate,$DO_amount,$DO_date,$loading_qty;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$DO_tracking_array[$counterDO] = new xml_do_tracking();
				$DO_tracking_array[$counterDO]->vehicle_no = $data;
				break;
			case $loading_id:
				$DO_tracking_array[$counterDO]->loading_id = $data;
				break;
			case $bay_no:
				$DO_tracking_array[$counterDO]->bay_no = $data;
				break;	
			case $loading_date:
				$DO_tracking_array[$counterDO]->loading_date = $data;
				break;
			case $grn_attachment:
				$DO_tracking_array[$counterDO]->grn_attachment = $data;
				break;
			case $grn_attachment_date:
				$DO_tracking_array[$counterDO]->grn_attachment_date = $data;
				$counterDO++;
				break;			
		}
	}
	if(substr($current_tag,0,25)=='*ROOT*OTP*LOADING_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';	
			switch($current_tag){
				case $sauda_no:
					$loading_details_array[$counterloadingdetails] = new xml_loading_details();
					$loading_details_array[$counterloadingdetails]->sauda_no = $data;
					break;
				case $customer_code:
					$loading_details_array[$counterloadingdetails]->customer_code = $data;
					break;
				case $destination:
					$loading_details_array[$counterloadingdetails]->destination = $data;
					break;
				case $DO_no:
					$loading_details_array[$counterloadingdetails]->DO_no = $data;
					break;
				case $sku_code:
					$loading_details_array[$counterloadingdetails]->sku_code = $data;
					break;
				case $batch_no:
					$loading_details_array[$counterloadingdetails]->batch_no = $data;
					break;							
				case $DO_qty:
					$loading_details_array[$counterloadingdetails]->DO_qty = $data;
					break;
				case $DO_rate:
					$loading_details_array[$counterloadingdetails]->DO_rate = $data;
					break;
				case $DO_amount:
					$loading_details_array[$counterloadingdetails]->DO_amount = $data;
					break;
				case $DO_date:
					$loading_details_array[$counterloadingdetails]->DO_date = $data;
					break;
				case $loading_qty:
					$loading_details_array[$counterloadingdetails]->loading_qty = $data;
					$counterloadingdetails++;
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
/* -------------------------------------------------------START QUERY FOR loading-----------------------------------------------------------------------*/
if(count($DO_tracking_array)>0)
{
	for($x=0;$x<count($DO_tracking_array);$x++){
		$vehicle_no=$DO_tracking_array[$x]->vehicle_no;
		$loading_id=$DO_tracking_array[$x]->loading_id;
		$bay_no=$DO_tracking_array[$x]->bay_no;
		$loading_date=$DO_tracking_array[$x]->loading_date;
		$grn_attachment=$DO_tracking_array[$x]->grn_attachment;
		$grn_attachment_date=$DO_tracking_array[$x]->grn_attachment_date;
		
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_server=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding sauda
			$sqlupdateloading="UPDATE DO_tracking SET 
									loading_id='".$loading_id."',
									bay_no='".$bay_no."',
									loading_date='".$loading_date."',
									grn_attachment='".$grn_attachment."',
									grn_attached_date='".$grn_attachment_date."',
									vehicle_bay_arrived_date =CURRENT_TIMESTAMP() 
									WHERE  vehicle_no='".$vehicle_no."' AND loading_date='0000-00-00 00:00:00'";
			if(mysqli_query($link,$sqlupdateloading))
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
	}//End of for loop	
	if(count($loading_details_array)>0)
	{
		for($i=0;$i<count($loading_details_array);$i++){
			$sauda_no=$loading_details_array[$i]->sauda_no;
			$customer_code=$loading_details_array[$i]->customer_code;
			$destination=$loading_details_array[$i]->destination;
			$DO_no=$loading_details_array[$i]->DO_no;
			$sku_code=$loading_details_array[$i]->sku_code;
			$batch_no=$loading_details_array[$i]->batch_no;
			$DO_qty=$loading_details_array[$i]->DO_qty;
			$DO_rate=$loading_details_array[$i]->DO_rate;
			$DO_amount=$loading_details_array[$i]->DO_amount;
			$DO_date=$loading_details_array[$i]->DO_date;
			$loading_qty=$loading_details_array[$i]->loading_qty;

			$sqlinsertloadingdetails="INSERT INTO DO_despatch_details SET sauda_no='".$sauda_no."',
									customer_code ='".$customer_code."',
									destination	='".$destination."',
									DO_no		='".$DO_no."',
									sku_code	='".$sku_code."',
									batch_no	='".$batch_no."',
									DO_qty		='".$DO_qty."',
									DO_rate		='".$DO_rate."',
									DO_amount	='".$DO_amount."',
									DO_date		='".$DO_date."',
									loading_qty ='".$loading_qty."',
									download_time='".$location_date_server."'";
			if(mysqli_query($link,$sqlinsertloadingdetails))
			{
				$flag=5;
				$sqlselbatchdate="SELECT batch_date FROM  batch_wise_stock WHERE batch_no='".$batch_no."' AND 	prod_code='".$sku_code."'";
				$rsselbatchdate=mysqli_query($link,$sqlselbatchdate);
				$rowselbatchdate=mysqli_fetch_assoc($rsselbatchdate);
				$batch_date=$rowselbatchdate['batch_date'];
				$sqlinsertbatchdetails="INSERT INTO batch_maintenance_log SET 
									DO_no		='".$DO_no."',
									prod_code	='".$sku_code."',
									batch_no	='".$batch_no."',
									batch_date	='".$batch_date."',
									issued_qty	='".$loading_qty."',
									issued_by ='".$emp_code."',
									issued_date=CURRENT_TIMESTAMP()";
				mysqli_query($link,$sqlinsertbatchdetails);					
				$sqlupdateproductbatch="UPDATE batch_wise_stock SET out_qty=(out_qty+$loading_qty),cl_stock=(cl_stock-$loading_qty) WHERE 
										prod_code ='".$sku_code."' AND batch_no='".$batch_no."'";
				mysqli_query($link,$sqlupdateproductbatch);						
			}					
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
		}
	}
 /* -----------------------------------------------------------END QUERY FOR loading---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End of loading
if(in_array('weighbridge_out',$menu_access_array) && $operation_type=='weighbridge_out') // Start of weigh Bridge out
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><OTP> <WEIGHBRIDGE_OUT_DETAILS><LOADING_VEHICLE_NO><![CDATA[BB]]></LOADING_VEHICLE_NO><GROSS_WEIGHT><![CDATA[55]]></GROSS_WEIGHT><WB_SL_NO><![CDATA[yxxyxy]]></WB_SL_NO><NET_WEIGHT><![CDATA[655631]]></NET_WEIGHT><WB_PHOTO><![CDATA[WOE000120190924134711.jpeg]]></WB_PHOTO><WB_OUT_ID><![CDATA[WOE000120190924134712]]></WB_OUT_ID><WB_OUT_DATE><![CDATA[2019-09-24 13:47]]></WB_OUT_DATE></WEIGHBRIDGE_OUT_DETAILS></OTP></root>";*/
$loading_vehicle_no = "*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*LOADING_VEHICLE_NO";
$gross_weight = "*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*GROSS_WEIGHT";
$wb_sl_no = "*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*WB_SL_NO";
$net_weight="*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*NET_WEIGHT";
$wb_photo="*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*WB_PHOTO";
$wb_out_id="*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*WB_OUT_ID";
$wb_out_date="*ROOT*OTP*WEIGHBRIDGE_OUT_DETAILS*WB_OUT_DATE";
$weighbridge_out_array = array();
$counterwbout = 0;
class xml_weighbridge_out{
	var $loading_vehicle_no,$gross_weight,$wb_sl_no,$net_weight,$wb_photo,$wb_out_id,$wb_out_date;
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
    global $current_tag,$weighbridge_out_array,$counterwbout,$loading_vehicle_no,$gross_weight,$wb_sl_no,$net_weight,$wb_photo,$wb_out_id,$wb_out_date;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $loading_vehicle_no:
				$weighbridge_out_array[$counterwbout] = new xml_weighbridge_out();
				$weighbridge_out_array[$counterwbout]->loading_vehicle_no = $data;
				break;
			case $gross_weight:
				$weighbridge_out_array[$counterwbout]->gross_weight = $data;
				break;
			case $wb_sl_no:
				$weighbridge_out_array[$counterwbout]->wb_sl_no = $data;
				break;
			case $net_weight:
				$weighbridge_out_array[$counterwbout]->net_weight = $data;
				break;
		   case $wb_photo:
				$weighbridge_out_array[$counterwbout]->wb_photo = $data;
				break;		
		  case $wb_out_id:
				$weighbridge_out_array[$counterwbout]->wb_out_id = $data;
				break;
		   case $wb_out_date:
				$weighbridge_out_array[$counterwbout]->wb_out_date = $data;
				$counterwbout++;
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
/* -------------------------------------------------------START QUERY FOR weighbridge out-----------------------------------------------------------------------*/
if(count($weighbridge_out_array)>0)
{
	for($x=0;$x<count($weighbridge_out_array);$x++){
		$loading_vehicle_no=$weighbridge_out_array[$x]->loading_vehicle_no;
		$gross_weight=$weighbridge_out_array[$x]->gross_weight;
		$wb_sl_no=$weighbridge_out_array[$x]->wb_sl_no;
		$net_weight=$weighbridge_out_array[$x]->net_weight;
		$wb_photo=$weighbridge_out_array[$x]->wb_photo;
		$wb_out_id=$weighbridge_out_array[$x]->wb_out_id;
		$wb_out_date=$weighbridge_out_array[$x]->wb_out_date;
		
		$sqlupdateweighbridgeout="UPDATE DO_tracking SET 
								loading_vehicle_no='".$loading_vehicle_no."',
								gross_weight='".$gross_weight."',
								wb_sl_no='".$wb_sl_no."',
								net_weight='".$net_weight."',
								wb_photo='".$wb_photo."',
								wb_out_id='".$wb_out_id."',
								wb_out_date='".$wb_out_date."'
						 		WHERE  vehicle_no='".$loading_vehicle_no."' AND wb_out_date='0000-00-00 00:00:00'";
		if(mysqli_query($link,$sqlupdateweighbridgeout))
		{
			$flag=5;
			$sqlselDOno="SELECT DO_no FROM DO_tracking WHERE wb_vehicle_no='".$loading_vehicle_no."' AND invoice_date='0000-00-00'";
			$rsselDOno=mysqli_query($link,$sqlselDOno);
			$rowselDOno=mysqli_fetch_assoc($rsselDOno);
			$DO_no=$rowselDOno['DO_no'];

				$sqlupdatewblog="UPDATE weighbridge_log SET 	
								gross_weight='".$gross_weight."',
								net_weight='".$net_weight."',
								out_time		=CURRENT_TIMESTAMP() WHERE DO_no='".$DO_no."' AND
								vehicle_no ='".$loading_vehicle_no."'";
				mysqli_query($link,$sqlupdatewblog);					
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR weighbridge out---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End weigh Bridge out
if(in_array('despatch_out',$menu_access_array) && $operation_type=='despatch_out') // Start of Despatch out
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><ROOT><OTP><DESPATCH_OUT_DETAILS><VEHICLE_NO><![CDATA[BB]]></VEHICLE_NO><DO_NO><![CDATA[DOE001620190905124357]]></DO_NO><DESPATCH_TRANS_ID><![CDATA[DEE000120190925130305]]></DESPATCH_TRANS_ID></DESPATCH_OUT_DETAILS></OTP></ROOT>";*/

$vehicle_no = "*ROOT*OTP*DESPATCH_OUT_DETAILS*VEHICLE_NO";
$DO_no = "*ROOT*OTP*DESPATCH_OUT_DETAILS*DO_NO";
$despatch_trans_id = "*ROOT*OTP*DESPATCH_OUT_DETAILS*DESPATCH_TRANS_ID";
$despatch_out_details_array = array();
$counterdespatchout = 0;
class xml_despatch_out{
	var $vehicle_no,$DO_no,$despatch_trans_id;
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
    global $current_tag,$despatch_out_details_array,$counterdespatchout,$vehicle_no,$DO_no,$despatch_trans_id;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$despatch_out_details_array[$counterdespatchout] = new xml_despatch_out();
				$despatch_out_details_array[$counterdespatchout]->vehicle_no = $data;
				break;
			case $DO_no:
				$despatch_out_details_array[$counterdespatchout]->DO_no = $data;
				break;
			case $despatch_trans_id:
				$despatch_out_details_array[$counterdespatchout]->despatch_trans_id = $data;
				$counterdespatchout++;
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
//print_r($despatch_out_details_array);
/* -------------------------------------------------------START QUERY FOR despatch out-----------------------------------------------------------------------*/
if(count($despatch_out_details_array)>0)
{
	for($x=0;$x<count($despatch_out_details_array);$x++){
		$vehicle_no=$despatch_out_details_array[$x]->vehicle_no;
		$DO_no=$despatch_out_details_array[$x]->DO_no;
		$despatch_trans_id=$despatch_out_details_array[$x]->despatch_trans_id;
		
		$invoice_no = rand(1000,9999); 
		$sqlupdatedespatchout="UPDATE DO_tracking SET 
								despatch_trans_id='".$despatch_trans_id."',
								invoice_no='".$invoice_no."',
								invoice_date=CURDATE()
						 		WHERE  vehicle_no='".$vehicle_no."' AND DO_no='".$DO_no."'";
		if(mysqli_query($link,$sqlupdatedespatchout))
		{
			$flag=5;
			//For Outstanding
			$sqlselloadingqty="SELECT loading_qty,DO_rate,customer_code,sku_code FROM DO_despatch_details WHERE DO_no='".$DO_no."'";
			$rsloadingqty=mysqli_query($link,$sqlselloadingqty);
			$total_amount=0;
			while($rowloadingqty=mysqli_fetch_assoc($rsloadingqty))
			{
				$customer_outstanding=$rowloadingqty['customer_code'];
				$loading_qty=$rowloadingqty['loading_qty'];
				$DO_rate=$rowloadingqty['DO_rate'];
				$sku_code=$rowloadingqty['sku_code'];
				
				$sqlGST="SELECT vat FROM product_master WHERE prod_code='".$sku_code."'";
				$rsGST=mysqli_query($link,$sqlGST);
				$rowGST=mysqli_fetch_assoc($rsGST);
				$GST=$rowGST['vat'];
				$amount=(($loading_qty*$DO_rate)+(($loading_qty*$DO_rate)*$GST)/100);
				$total_amount=$total_amount+$amount;
			}
			$sqloutstanding="INSERT INTO outstanding 
							SET customer_code='".$customer_outstanding."',
							invoice_id='".$invoice_no."',
							date=CURDATE(),
							invoice_amount='".$total_amount."',
							due_amount='".$total_amount."'";
		  mysqli_query($link,$sqloutstanding);					
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR Despatch out---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	  $sqlupdateDostatus="UPDATE DO_transaction SET 	DO_status='despatch' WHERE DO_no='".$DO_no."'";
	   mysqli_query($link,$sqlupdateDostatus);
	   echo $flag=1;
	}
}// End Despatch out
if(in_array('gate_keeper_out',$menu_access_array) && $operation_type=='gate_keeper_out') // Start of Gate Keeper2 out
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><ROOT><OTP><GATE_KEEPER2_OUT><VEHICLE_NO><![CDATA[ABCD]]></VEHICLE_NO><EXIT_APPROVAL_ID><![CDATA[EAE000120190928140938]]></EXIT_APPROVAL_ID><EXIT_APPROVAL_DATE><![CDATA[2019-09-28 14:09:38]]></EXIT_APPROVAL_DATE><EXIT_APPROVAL_CHECKLIST><![CDATA[Checklist3#Checklist4]]></EXIT_APPROVAL_CHECKLIST></GATE_KEEPER2_OUT></OTP></ROOT>";*/

$vehicle_no = "*ROOT*OTP*GATE_KEEPER2_OUT*VEHICLE_NO";
$exit_approval_id = "*ROOT*OTP*GATE_KEEPER2_OUT*EXIT_APPROVAL_ID";
$exit_approval_date = "*ROOT*OTP*GATE_KEEPER2_OUT*EXIT_APPROVAL_DATE";
$exit_approval_checklist = "*ROOT*OTP*GATE_KEEPER2_OUT*EXIT_APPROVAL_CHECKLIST";
$gate_keeper2_out_array = array();
$countergatekeeper2out = 0;
class xml_gate_keeper2_out{
	var $vehicle_no,$exit_approval_id,$exit_approval_date,$exit_approval_checklist;
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
    global $current_tag,$gate_keeper2_out_array,$countergatekeeper2out,$vehicle_no,$exit_approval_id,$exit_approval_date,$exit_approval_checklist;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$gate_keeper2_out_array[$countergatekeeper2out] = new xml_gate_keeper2_out();
				$gate_keeper2_out_array[$countergatekeeper2out]->vehicle_no = $data;
				break;
			case $exit_approval_id:
				$gate_keeper2_out_array[$countergatekeeper2out]->exit_approval_id = $data;
				break;
			case $exit_approval_date:
				$gate_keeper2_out_array[$countergatekeeper2out]->exit_approval_date = $data;
				break;
		   case $exit_approval_checklist:
				$gate_keeper2_out_array[$countergatekeeper2out]->exit_approval_checklist = $data;
				$countergatekeeper2out++;
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
//print_r($gate_keeper2_out_array);
/* -------------------------------------------------------START QUERY FOR Gate keeper2 out-----------------------------------------------------------------------*/
if(count($gate_keeper2_out_array)>0)
{
	for($x=0;$x<count($gate_keeper2_out_array);$x++){
		$vehicle_no=$gate_keeper2_out_array[$x]->vehicle_no;
		$exit_approval_id=$gate_keeper2_out_array[$x]->exit_approval_id;
		$exit_approval_date=$gate_keeper2_out_array[$x]->exit_approval_date;
		$exit_approval_checklist=$gate_keeper2_out_array[$x]->exit_approval_checklist;
		
		$sqlupdategatekeeper2out="UPDATE DO_tracking SET 
								exit_approval_id='".$exit_approval_id."',
								exit_approval_date='".$exit_approval_date."',
								exit_approval_checklist='".$exit_approval_checklist."'
						 		WHERE  vehicle_no='".$vehicle_no."'";
		if(mysqli_query($link,$sqlupdategatekeeper2out))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR Gate keeper2 out---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End Gate keeper2 out
if(in_array('security_out',$menu_access_array) && $operation_type=='security_out') // Start of security out
{
$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><ROOT><OTP><SECURITY_OUT><VEHICLE_NO><![CDATA[BB]]></VEHICLE_NO><SECURITY_CHK_ID><![CDATA[SCE000120190928155015]]></SECURITY_CHK_ID><SECURITY_CHK_DATE><![CDATA[2019-09-28 15:50:15]]></SECURITY_CHK_DATE><SECURITY_CHK_CHECKLIST><![CDATA[Checklist1#Checklist3]]></SECURITY_CHK_CHECKLIST></SECURITY_OUT></OTP></ROOT>";*/

$vehicle_no = "*ROOT*OTP*SECURITY_OUT*VEHICLE_NO";
$security_chk_id = "*ROOT*OTP*SECURITY_OUT*SECURITY_CHK_ID";
$security_chk_date = "*ROOT*OTP*SECURITY_OUT*SECURITY_CHK_DATE";
$security_chk_checklist = "*ROOT*OTP*SECURITY_OUT*SECURITY_CHK_CHECKLIST";
$security_out_array = array();
$countersecurityout = 0;
class xml_security_out{
	var $vehicle_no,$security_chk_id,$security_chk_date,$security_chk_checklist;
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
    global $current_tag,$security_out_array,$countersecurityout,$vehicle_no,$security_chk_id,$security_chk_date,$security_chk_checklist;
	if(substr($current_tag,0,9)=='*ROOT*OTP')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $vehicle_no:
				$security_out_array[$countersecurityout] = new xml_security_out();
				$security_out_array[$countersecurityout]->vehicle_no = $data;
				break;
			case $security_chk_id:
				$security_out_array[$countersecurityout]->security_chk_id = $data;
				break;
			case $security_chk_date:
				$security_out_array[$countersecurityout]->security_chk_date = $data;
				break;
		   case $security_chk_checklist:
				$security_out_array[$countersecurityout]->security_chk_checklist = $data;
				$countersecurityout++;
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
//print_r($gate_keeper2_out_array);
/* -------------------------------------------------------START QUERY FOR security out-----------------------------------------------------------------------*/
if(count($security_out_array)>0)
{
	for($x=0;$x<count($security_out_array);$x++){
		$vehicle_no=$security_out_array[$x]->vehicle_no;
		$security_chk_id=$security_out_array[$x]->security_chk_id;
		$security_chk_date=$security_out_array[$x]->security_chk_date;
		$security_chk_checklist=$security_out_array[$x]->security_chk_checklist;
		
		$sqlupdatesecuritychk="UPDATE DO_tracking SET 
								security_chk_id='".$security_chk_id."',
								security_chk_date='".$security_chk_date."',
								security_chk_checklist='".$security_chk_checklist."'
						 		WHERE  vehicle_no='".$vehicle_no."'";
		if(mysqli_query($link,$sqlupdatesecuritychk))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of for loop					  
}
 /* -----------------------------------------------------------END QUERY FOR Gate keeper2 out---------------------------------------------------------------------------*/
	if($flag==5)
	{
	  mysqli_query($link,"COMMIT"); 
	   echo $flag=1;
	}
}// End Despatch ot
mysqli_close($link);
?>