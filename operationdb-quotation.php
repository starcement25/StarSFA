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

/*$body="<?xml version='1.0" encoding='UTF-8'?><root><QUOTATION><QUOTATION_ID><![CDATA[QE000720200410193442]]></QUOTATION_ID><MI_ID><![CDATA[]]></MI_ID><FITTINGS_ACCESSORIES><![CDATA[]]></FITTINGS_ACCESSORIES><FARMER_NAME><![CDATA[]]></FARMER_NAME><MI_REFERENCE_NO><![CDATA[]]></MI_REFERENCE_NO><FARMER_TYPE><![CDATA[]]></FARMER_TYPE><MI_TYPE><![CDATA[]]></MI_TYPE><MI_AREA><![CDATA[700001]]></MI_AREA><TOTAL_AREA><![CDATA[]]></TOTAL_AREA><CROP_NAME><![CDATA[]]></CROP_NAME><SPACING><![CDATA[]]></SPACING><COMPONENT_NAME><![CDATA[]]></COMPONENT_NAME><UNIT><![CDATA[]]></UNIT><SALE_RATE><![CDATA[]]></SALE_RATE><QTY><![CDATA[]]></QTY><TOTAL_SALE_RATE><![CDATA[]]></TOTAL_SALE_RATE></QUOTATION></root>";*/

$quotation_id = "*ROOT*QUOTATION*QUOTATION_ID";
$mi_id = "*ROOT*QUOTATION*MI_ID";
$fittings_accessories = "*ROOT*QUOTATION*FITTINGS_ACCESSORIES";
$farmer_name = "*ROOT*QUOTATION*FARMER_NAME";
$mi_reference_no = "*ROOT*QUOTATION*MI_REFERENCE_NO";
$farmer_type = "*ROOT*QUOTATION*FARMER_TYPE";
$mi_type = "*ROOT*QUOTATION*MI_TYPE";
$mi_area = "*ROOT*QUOTATION*MI_AREA";
$total_area = "*ROOT*QUOTATION*TOTAL_AREA";
$crop_name = "*ROOT*QUOTATION*CROP_NAME";
$spacing = "*ROOT*QUOTATION*SPACING";
$component_name = "*ROOT*QUOTATION*COMPONENT_NAME";
$unit = "*ROOT*QUOTATION*UNIT";
$sale_rate = "*ROOT*QUOTATION*SALE_RATE";
$qty = "*ROOT*QUOTATION*QTY";
$total_sale_rate = "*ROOT*QUOTATION*TOTAL_SALE_RATE";

$quotation_array=array();

$counter = 0;

class xml_quotation{
	var $quotation_id,$mi_id,$fittings_accessories,$farmer_name,$mi_reference_no,$farmer_type,$mi_type,$mi_area,$total_area,$crop_name,$spacing,$component_name,$unit,$sale_rate,$qty,$total_sale_rate;	
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
    global $current_tag,$quotation_id,$mi_id,$fittings_accessories,$farmer_name,$mi_reference_no,$farmer_type,$mi_type,$mi_area,$total_area,$crop_name,$spacing,$component_name,$unit,$sale_rate,$qty,$total_sale_rate,$quotation_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,15)=='*ROOT*QUOTATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $quotation_id:
				$quotation_array[$counter] = new xml_quotation();
				$quotation_array[$counter]->quotation_id = $data;
				break;
			case $mi_id:
				$quotation_array[$counter]->mi_id = $data;
				break;
			case $fittings_accessories:
				$quotation_array[$counter]->fittings_accessories = $data;
				break;
			case $farmer_name:
				$quotation_array[$counter]->farmer_name = $data;
				break;
			case $mi_reference_no:
				$quotation_array[$counter]->mi_reference_no = $data;
				break;
			case $farmer_type:
				$quotation_array[$counter]->farmer_type = $data;
				break;
			case $mi_type:
				$quotation_array[$counter]->mi_type = $data;
				break;
			case $mi_area:
				$quotation_array[$counter]->mi_area = $data;
				break;
			case $total_area:
				$quotation_array[$counter]->total_area = $data;
				break;
			case $crop_name:
				$quotation_array[$counter]->crop_name = $data;
				break;	
			case $spacing:
				$quotation_array[$counter]->spacing = $data;
				break;
			case $component_name:
				$quotation_array[$counter]->component_name = $data;
				break;
			case $unit:
				$quotation_array[$counter]->unit = $data;
				break;
			case $sale_rate:
				$quotation_array[$counter]->sale_rate = $data;
				break;
			case $qty:
				$quotation_array[$counter]->qty = $data;
				break;	
			case $total_sale_rate:
				$quotation_array[$counter]->total_sale_rate = $data;
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
/* --------------------START QUERY FOR QUOTATION------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$quotation_trans_id=array();
if(count($quotation_array)>0)
{
	for($x=0;$x<count($quotation_array);$x++){
		
		$quotation_id=$quotation_array[$x]->quotation_id;
		$mi_id=$quotation_array[$x]->mi_id;
		$fittings_accessories=$quotation_array[$x]->fittings_accessories;
		$farmer_name=$quotation_array[$x]->farmer_name;
		$mi_reference_no=$quotation_array[$x]->mi_reference_no;
		$farmer_type=$quotation_array[$x]->farmer_type;
		$mi_type=$quotation_array[$x]->mi_type;
		$mi_area=$quotation_array[$x]->mi_area;
		$total_area=$quotation_array[$x]->total_area;
		$crop_name=$quotation_array[$x]->crop_name;
		$spacing=$quotation_array[$x]->spacing;
		$component_name=$quotation_array[$x]->component_name;
		$unit=$quotation_array[$x]->unit;
		$sale_rate=$quotation_array[$x]->sale_rate;
		$qty=$quotation_array[$x]->qty;
		$total_sale_rate=$quotation_array[$x]->total_sale_rate;
		
		//For checking that trans id exist or not for order
		$sqlchkquotation="SELECT quotation_id FROM location WHERE quotation_id='".$quotation_id."'";
		$reschkquotation = mysqli_query($link,$sqlchkquotation) or die(mysqli_error()." Error in check quotation: ".$sqlchkquotation); 
		$rowchkquotation = mysqli_fetch_assoc($reschkquotation);
		$countchkquotation=mysqli_num_rows($reschkquotation);
		
		if($countchkquotation==0)
		{
		$sqlinsertquotation="INSERT INTO quotation_master SET quotation_id ='".$quotation_id."',
							   mi_id						='".addslashes($mi_id)."',
							   fittings_accessories			='".$fittings_accessories."',
							   farmer_name					='".addslashes($farmer_name)."',
							   mi_reference_no				='".addslashes($mi_reference_no)."',
							   farmer_type					='".addslashes($farmer_type)."',
							   mi_type						='".addslashes($mi_type)."',
							   mi_area						='".$mi_area."',
							   total_area					='".$total_area."',
							   crop_name					='".$crop_name."',
							   spacing						='".$spacing."',
							   component_name				='".$component_name."',
							   unit							='".$unit."',
							    sale_rate					='".$sale_rate."',
								qty							='".$qty."',
							   total_sale_rate				='".$total_sale_rate."'";
		if(mysqli_query($link,$sqlinsertquotation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	  }//End of else
	}
}
 /* --------------------END QUERY FOR QUOTATION Promotion--------------------------------------------------------------------------------------------------------*/
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
//echo $flag=2;
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-quotation.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-product-promotion.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
