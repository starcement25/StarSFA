<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ?? '';
$last_update_time=str_replace('€',' ',$last_update_time);
$body=file_get_contents('php://input');
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><GRN><GRN_DETAILS><GRN_RECEIVED_BY><![CDATA[C/0000860]]></GRN_RECEIVED_BY><GRN_CODE><![CDATA[GRNC/000086020190923171333]]></GRN_CODE><GRN_DATE><![CDATA[2019-09-23 17:13]]></GRN_DATE><DO_NO><![CDATA[DOE000120190923170619]]></DO_NO><SKU_CODE><![CDATA[12116]]></SKU_CODE><DISPATCH_QTY><![CDATA[300]]></DISPATCH_QTY><RECEIVED_QTY><![CDATA[200]]></RECEIVED_QTY><REMARKS><![CDATA[test]]></REMARKS></GRN_DETAILS><GRN_DETAILS><GRN_RECEIVED_BY><![CDATA[C/0000860]]></GRN_RECEIVED_BY><GRN_CODE><![CDATA[GRNC/000086020190923171333]]></GRN_CODE><GRN_DATE><![CDATA[2019-09-23 17:13]]></GRN_DATE><DO_NO><![CDATA[DOE000120190923170619]]></DO_NO><SKU_CODE><![CDATA[12117]]></SKU_CODE><DISPATCH_QTY><![CDATA[320]]></DISPATCH_QTY><RECEIVED_QTY><![CDATA[200]]></RECEIVED_QTY><REMARKS><![CDATA[test1]]></REMARKS></GRN_DETAILS></GRN></root>";*/

$GRN_received_by = "*ROOT*GRN*GRN_DETAILS*GRN_RECEIVED_BY";
$GRN_code = "*ROOT*GRN*GRN_DETAILS*GRN_CODE";
$GRN_date = "*ROOT*GRN*GRN_DETAILS*GRN_DATE";
$DO_no = "*ROOT*GRN*GRN_DETAILS*DO_NO";
$sku_code = "*ROOT*GRN*GRN_DETAILS*SKU_CODE";
$dispatch_qty = "*ROOT*GRN*GRN_DETAILS*DISPATCH_QTY";
$received_qty = "*ROOT*GRN*GRN_DETAILS*RECEIVED_QTY";
$remarks = "*ROOT*GRN*GRN_DETAILS*REMARKS";

$GRN_array=array();
$counter = 0;
class xml_GRN{
	var $GRN_received_by,$GRN_code,$GRN_date,$DO_no,$sku_code,$dispatch_qty,$received_qty,$remarks;	
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
    global $current_tag,$counter,$GRN_received_by,$GRN_code,$GRN_date,$DO_no,$sku_code,$dispatch_qty,$received_qty,$remarks,$GRN_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,21)=='*ROOT*GRN*GRN_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $GRN_received_by:
				$GRN_array[$counter] = new xml_GRN();
				$GRN_array[$counter]->GRN_received_by = $data;
				break;
			case $GRN_code:
				$GRN_array[$counter]->GRN_code = $data;
				break;
			case $GRN_date:
				$GRN_array[$counter]->GRN_date = $data;
				break;
			case $DO_no:
				$GRN_array[$counter]->DO_no = $data;
				break;
			case $sku_code:
				$GRN_array[$counter]->sku_code = $data;
				break;
			case $dispatch_qty:
				$GRN_array[$counter]->dispatch_qty = $data;
				break;
			case $received_qty:
				$GRN_array[$counter]->received_qty = $data;
				break;			
			case $remarks:
				$GRN_array[$counter]->remarks = $data;
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
//print_r($GRN_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR GRN------------------------------------------------------------------------------------------------*/
//print_r($order_status_array);
if(count($GRN_array)>0)
{
	for($x=0;$x<count($GRN_array);$x++){
		$GRN_received_by=$GRN_array[$x]->GRN_received_by;
		$GRN_code=$GRN_array[$x]->GRN_code;
		$GRN_date=$GRN_array[$x]->GRN_date;
		$DO_no=$GRN_array[$x]->DO_no;
		$sku_code=$GRN_array[$x]->sku_code;
		$dispatch_qty=$GRN_array[$x]->dispatch_qty;
		$received_qty=$GRN_array[$x]->received_qty;
		$remarks=$GRN_array[$x]->remarks;
		
		//For checking that order no exist or not for GRN
		$sqlchkGRNstatus="SELECT * FROM GRN_transaction WHERE GRN_code='".$GRN_code."' AND 	sku_code='".$sku_code."'";
		$reschkGRNstatus = mysqli_query($link,$sqlchkGRNstatus) or die(mysqli_error()." Error in check GRN Status: ".$sqlchkGRNstatus); 
		$rowchkchkGRNstatus = mysqli_fetch_assoc($reschkGRNstatus);
		$countchkGRNstatus=mysqli_num_rows($reschkGRNstatus);
		
		//For update the location table for existing code for GRN
		if($countchkGRNstatus==0)
		{
			$sqlinsertGRN="INSERT INTO GRN_transaction SET GRN_received_by='".$GRN_received_by."',
									GRN_code='".$GRN_code."',
									GRN_date='".$GRN_date."',
									DO_no='".$DO_no."',
									sku_code='".$sku_code."',
									dispatch_qty='".$dispatch_qty."',
									received_qty='".$received_qty."',
									remarks='".$remarks."',
									uplaod_date_time=CURRENT_TIMESTAMP()";
			$rsinsertGRN=mysqli_query($link,$sqlinsertGRN) or die(mysqli_error()." Error in insert GRN: ".$rsinsertGRN);
			if($rsinsertGRN)
			{
				$flag=5;
				$sqlupdateDostatus="UPDATE DO_transaction SET 	DO_status='customer_received' WHERE DO_no='".$DO_no."' AND sku_code='".$sku_code."'";
				mysqli_query($link,$sqlupdateDostatus);
			}
			else
			{
				echo $flag=0;
			}
		}
	}
}

 /* --------------------END QUERY FOR GRN --------------------------------------------------------------------------------------------------------*/
if($flag==5){
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
//echo $flag=2;
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."operationdb-GRN-upload.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	mysqli_close($link);
?>
