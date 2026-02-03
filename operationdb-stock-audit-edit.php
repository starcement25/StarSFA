<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

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
$body=file_get_contents('php://input');

	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

/*$body='<?xml version="1.0" encoding="UTF-8"?><root><stock_audit_edit><transaction_id><![CDATA[SE000220210128131450]]></transaction_id><customer_code><![CDATA[C/0030564]]></customer_code><product_code><![CDATA[12021]]></product_code><edit_qty><![CDATA[2320]]></edit_qty></stock_audit_edit></root>';*/

$transaction_id = "*ROOT*STOCK_AUDIT_EDIT*TRANSACTION_ID";
$customer_code = "*ROOT*STOCK_AUDIT_EDIT*CUSTOMER_CODE";
$product_code = "*ROOT*STOCK_AUDIT_EDIT*PRODUCT_CODE";
$edit_qty = "*ROOT*STOCK_AUDIT_EDIT*EDIT_QTY";

$stock_audit_edit_array=array();
$counter = 0;
class xml_stock_audit_edit{
	var $transaction_id,$customer_code,$product_code,$edit_qty;	
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
    global $current_tag,$counter,$transaction_id,$customer_code,$product_code,$edit_qty,$stock_audit_edit_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,22)=='*ROOT*STOCK_AUDIT_EDIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $transaction_id:
				$stock_audit_edit_array[$counter] = new xml_stock_audit_edit();
				$stock_audit_edit_array[$counter]->transaction_id = $data;
				break;
			case $customer_code:
				$stock_audit_edit_array[$counter]->customer_code = $data;
				break;	
			case $product_code:
				$stock_audit_edit_array[$counter]->product_code = $data;
				break;
			case $edit_qty:
				$stock_audit_edit_array[$counter]->edit_qty = $data;
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
//print_r($order_edit_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR STOCK AUDIT EDIT------------------------------------------------------------------------------------------------*/
//print_r($order_status_array);
if(count($stock_audit_edit_array)>0)
{
	for($x=0;$x<count($stock_audit_edit_array);$x++){
		$transaction_id=$stock_audit_edit_array[$x]->transaction_id;
		$customer_code=$stock_audit_edit_array[$x]->customer_code;
		$product_code=$stock_audit_edit_array[$x]->product_code;
		$edit_qty=$stock_audit_edit_array[$x]->edit_qty;
		
		$sqlexistingeditdet="SELECT visit_qty,visit_date FROM stock_audit_edit_log 
							WHERE transaction_id='".$transaction_id."' AND product_code='".$product_code."' ORDER BY download_time DESC LIMIT 0,1";
		$rsexistingeditdet=mysqli_query($link,$sqlexistingeditdet);	
		$countexistingeditdet=mysqli_num_rows($rsexistingeditdet);
		if($countexistingeditdet > 0)
		{
			$rowexistingeditdet=mysqli_fetch_assoc($rsexistingeditdet);
			$first_visit_qty=$rowexistingeditdet['visit_qty'];
			$visit_date=date('Y-m-d',strtotime($rowexistingeditdet['visit_date']));
		}
		else
		{
		$sqlselexistingdet="SELECT quantity,SUBSTRING(transaction_id,-14,8) as transaction_date FROM stock_audit WHERE transaction_id='".$transaction_id."' AND product_code='".$product_code."'";
		$rsselexistingdet=mysqli_query($link,$sqlselexistingdet);
		$rowselexistingdet=mysqli_fetch_assoc($rsselexistingdet);
		$first_visit_qty=$rowselexistingdet['quantity'];
		$visit_date=date('Y-m-d',strtotime($rowselexistingdet['transaction_date']));
		}
		
			$sqlupdatestockaudit="UPDATE stock_audit SET quantity='".$edit_qty."'
									WHERE transaction_id='".$transaction_id."' AND product_code='".$product_code."'";
			$rsupdatestockaudit=mysqli_query($link,$sqlupdatestockaudit) or die(mysqli_error()." Error in update stock audit: ".$sqlupdatestockaudit);
			
			if($rsupdatestockaudit)
			{
				$flag=6;
				//INSERT LOG
				$sqlinsertlog="INSERT INTO stock_audit_edit_log 
								SET customer_code='".$customer_code."',
								product_code='".$product_code."',
								visit_qty='".$first_visit_qty."',
								visit_date='".$transaction_date."',
								transaction_id='".$transaction_id."',
								edited_qty='".$edit_qty."',
								download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertlog))
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
			else
			{
				mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
			}
	}
}

 /* --------------------END QUERY FOR STOCK AUDIT EDIT--------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
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
$url = APICALLLOGURL."/operationdb-stock-audit-edit.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	mysqli_close($link);
?>
