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
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><van_stock_return><location><emp_code><![CDATA[E0030]]></emp_code><trans_id><![CDATA[SR003020170524113142]]></trans_id><latt><![CDATA[22.5644419]]></latt><longi><![CDATA[88.3568301]]></longi><date><![CDATA[2017-05-24 11:31:42]]></date></location><van_stock_return_details><customer_code><![CDATA[C/0111062]]></customer_code><prod_code><![CDATA[12003]]></prod_code><return_trans_id><![CDATA[SR003020170524113142]]></return_trans_id><return_qty><![CDATA[5]]></return_qty><order_no><![CDATA[]]></order_no></van_stock_return_details></van_stock_return></root>";*/

$van_stock_return_emp_code="*ROOT*VAN_STOCK_RETURN*LOCATION*EMP_CODE";
$van_stock_return_trans_id = "*ROOT*VAN_STOCK_RETURN*LOCATION*TRANS_ID";
$van_stock_return_latt = "*ROOT*VAN_STOCK_RETURN*LOCATION*LATT";
$van_stock_return_longi = "*ROOT*VAN_STOCK_RETURN*LOCATION*LONGI";
$van_stock_return_trans_date="*ROOT*VAN_STOCK_RETURN*LOCATION*DATE";
$van_stock_return_customer_code = "*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS*CUSTOMER_CODE";
$van_stock_return_product_code = "*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS*PROD_CODE";
$van_stock_return_transaction_id = "*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS*RETURN_TRANS_ID";
$van_stock_return_qty = "*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS*RETURN_QTY";
$van_stock_return_order_no = "*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS*ORDER_NO";
$van_stock_return_array=array();
$van_stock_details_array=array();

$counter = 0;
$countervanstock=0;
$countervanstockdetails=0;

class xml_van_stock_return{
	var $van_stock_return_emp_code,$van_stock_return_trans_id,$van_stock_return_latt,$van_stock_return_longi,$van_stock_return_trans_date;	
}
class xml_van_stock_return_details{
	var $van_stock_return_transaction_id,$van_stock_return_customer_code,$van_stock_return_product_code,$van_stock_return_qty,$van_stock_return_order_no;
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
    global $current_tag,$counter,$countervanstock,$countervanstockdetails,$van_stock_return_array,$van_stock_details_array,
	$van_stock_return_emp_code,$van_stock_return_trans_id,$van_stock_return_latt,$van_stock_return_longi,$van_stock_return_trans_date,$van_stock_return_transaction_id,$van_stock_return_customer_code,$van_stock_return_product_code,$van_stock_return_qty,$van_stock_return_order_no;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,22)=='*ROOT*VAN_STOCK_RETURN')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $van_stock_return_emp_code:
				$van_stock_return_array[$countervanstock] = new xml_van_stock_return();
				$van_stock_return_array[$countervanstock]->van_stock_return_emp_code = $data;
				break;
			case $van_stock_return_trans_id:
				$van_stock_return_array[$countervanstock]->van_stock_return_trans_id = $data;
				break;
			case $van_stock_return_latt:
				$van_stock_return_array[$countervanstock]->van_stock_return_latt = $data;
				break;
			case $van_stock_return_longi:
				$van_stock_return_array[$countervanstock]->van_stock_return_longi = $data;
				break;
			case $van_stock_return_trans_date:
				$van_stock_return_array[$countervanstock]->van_stock_return_trans_date = $data;
				$countervanstock++;
				break;
		}
	}
	if(substr($current_tag,0,47)=='*ROOT*VAN_STOCK_RETURN*VAN_STOCK_RETURN_DETAILS')
		{
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $van_stock_return_customer_code:
					$van_stock_details_array[$countervanstockdetails] = new xml_van_stock_return_details();
					$van_stock_details_array[$countervanstockdetails]->van_stock_return_customer_code = $data;
					break;
				case $van_stock_return_product_code:
					$van_stock_details_array[$countervanstockdetails]->van_stock_return_product_code = $data;
					break;
				case $van_stock_return_transaction_id:
					$van_stock_details_array[$countervanstockdetails]->van_stock_return_transaction_id = $data;
					break;
				case $van_stock_return_qty:
					$van_stock_details_array[$countervanstockdetails]->van_stock_return_qty = $data;
					break;
				case $van_stock_return_order_no:
					$van_stock_details_array[$countervanstockdetails]->van_stock_return_order_no = $data;
					$countervanstockdetails++;
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
//print_r($van_stock_return_array);
//print_r($van_stock_details_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -----------------------------------------------START QUERY FOR VAN STOCK RETURN--------------------------------------------------------------------------*/
$van_stock_return_array_trans_id_exists=array();
if(count($van_stock_return_array)>0)
{
	for($x=0;$x<count($van_stock_return_array);$x++){
		$van_stock_return_emp_code=$van_stock_return_array[$x]->van_stock_return_emp_code;
		$van_stock_return_trans_id=$van_stock_return_array[$x]->van_stock_return_trans_id;
		$van_stock_return_latt=$van_stock_return_array[$x]->van_stock_return_latt;
		$van_stock_return_longi=$van_stock_return_array[$x]->van_stock_return_longi;
		$van_stock_return_trans_date=$van_stock_return_array[$x]->van_stock_return_trans_date;
		
		//For checking that trans id exist or not for stock return
		$sqlchkvanstocklocation="SELECT trans_id FROM location WHERE trans_id='".$van_stock_return_trans_id."'";
		$reschkvanstocklocation = mysqli_query($link,$sqlchkvanstocklocation) or die(mysqli_error()." Error in check van stock return location: ".$sqlchkvanstocklocation); 
		$countchkvanstocklocation=mysqli_num_rows($reschkvanstocklocation);
		
		//For update the location table for existing trans id for stock return
		if($countchkvanstocklocation>0)
		{
			//$audit_trans_id_chk=substr($audit_trans_id,1,19);
			if(!in_array($van_stock_return_trans_id,$van_stock_return_array_trans_id_exists))
			{
				array_push($van_stock_return_array_trans_id_exists,$van_stock_return_trans_id);
			}
			$sqlupdatevanstocklocation="UPDATE location SET emp_code='".$van_stock_return_emp_code."',
									latt='".$van_stock_return_latt."',
									longi='".$van_stock_return_longi."'
									WHERE trans_id='".$van_stock_return_trans_id."'";
			$rsupdatevanstocklocation=mysqli_query($link,$sqlupdatevanstocklocation) or die(mysqli_error()." Error in update audit location: ".$sqlupdatevanstocklocation);
			if($rsupdatevanstocklocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of transaction
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding mt
			$sqlinsertvanstocklocation="INSERT INTO location SET emp_code='".$van_stock_return_emp_code."',
								  trans_id='".$van_stock_return_trans_id."',
								  latt='".$van_stock_return_latt."',
								  longi='".$van_stock_return_longi."',
								  date='".$van_stock_return_trans_date."',
								  updatetime='".$location_date."'";
			if(mysqli_query($link,$sqlinsertvanstocklocation))
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
	}// End for loop
		if(count($van_stock_details_array)>0)
		{
			for($i=0;$i<count($van_stock_details_array);$i++){
				$van_stock_return_customer_code=$van_stock_details_array[$i]->van_stock_return_customer_code;
				$van_stock_return_product_code=$van_stock_details_array[$i]->van_stock_return_product_code;
				$van_stock_return_transaction_id=$van_stock_details_array[$i]->van_stock_return_transaction_id;
				$van_stock_return_qty=$van_stock_details_array[$i]->van_stock_return_qty;
				$van_stock_return_order_no=$van_stock_details_array[$i]->van_stock_return_order_no;
					
					//check for provided transaction id exist or not in location table through the particular array van_stock_return_array_trans_id_exists
					if(!in_array($van_stock_return_transaction_id,$van_stock_return_array_trans_id_exists))
					{
						$sqlinsertvanstockreturn="INSERT INTO van_stock_return SET 	return_trans_id ='".$van_stock_return_transaction_id."',
											  customer_code 		='".$van_stock_return_customer_code."',
											  prod_code 			='".$van_stock_return_product_code."',
											  return_qty 			='".$van_stock_return_qty."',
											  order_no 				='".$van_stock_return_order_no."'";											  
						if(mysqli_query($link,$sqlinsertvanstockreturn))
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
}//End of van stock return if

 /* --------------------END QUERY FOR VAN STOCK RETURN------------------------------------------------------------------------------------------------------------*/
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
mysqli_close($link);
?>
