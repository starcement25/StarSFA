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

/*$body='<?xml version="1.0" encoding="UTF-8"?><root><order_edit><order_no><![CDATA[OE007820151124191811]]></order_no><customer_code><![CDATA[C/000014]]></customer_code><product_code><![CDATA[12264]]></product_code><edit_qty><![CDATA[21]]></edit_qty><edit_amount><![CDATA[]]></edit_amount></order_edit></root>';*/
/*$body='<?xml version="1.0" encoding="UTF-8"?><root><order_edit><order_no><![CDATA[OE000220201015170517]]></order_no><customer_code><![CDATA[C/0024516]]></customer_code><product_code><![CDATA[12019]]></product_code><edit_qty><![CDATA[150]]></edit_qty><edit_amount><![CDATA[45000.00]]></edit_amount></order_edit></root>';*/

$order_no = "*ROOT*ORDER_EDIT*ORDER_NO";
$customer_code = "*ROOT*ORDER_EDIT*CUSTOMER_CODE";
$product_code = "*ROOT*ORDER_EDIT*PRODUCT_CODE";
$edit_qty = "*ROOT*ORDER_EDIT*EDIT_QTY";
$edit_amount = "*ROOT*ORDER_EDIT*EDIT_AMOUNT";

$order_edit_array=array();
$counter = 0;
class xml_order_edit{
	var $order_no,$customer_code,$product_code,$edit_qty,$edit_amount;	
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
    global $current_tag,$counter,$order_no,$customer_code,$product_code,$edit_qty,$edit_amount,$order_edit_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*ORDER_EDIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $order_no:
				$order_edit_array[$counter] = new xml_order_edit();
				$order_edit_array[$counter]->order_no = $data;
				break;
			case $customer_code:
				$order_edit_array[$counter]->customer_code = $data;
				break;	
			case $product_code:
				$order_edit_array[$counter]->product_code = $data;
				break;
			case $edit_qty:
				$order_edit_array[$counter]->edit_qty = $data;
				break;
			case $edit_amount:
				$order_edit_array[$counter]->edit_amount = $data;
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

/* --------------------START QUERY FOR ORDER EDIT------------------------------------------------------------------------------------------------*/
//print_r($order_status_array);
if(count($order_edit_array)>0)
{
	for($x=0;$x<count($order_edit_array);$x++){
		$order_no=$order_edit_array[$x]->order_no;
		$customer_code=$order_edit_array[$x]->customer_code;
		$product_code=$order_edit_array[$x]->product_code;
		$edit_qty=$order_edit_array[$x]->edit_qty;
		$edit_amount=$order_edit_array[$x]->edit_amount;
		
		$sqlexistingeditdet="SELECT visit_qty,visit_amount,rate,visit_date FROM order_edit_log 
							WHERE order_no='".$order_no."' AND sku_code='".$product_code."' ORDER BY download_time DESC LIMIT 0,1";
		$rsexistingeditdet=mysqli_query($link,$sqlexistingeditdet);	
		$countexistingeditdet=mysqli_num_rows($rsexistingeditdet);
		if($countexistingeditdet > 0)
		{
			$rowexistingeditdet=mysqli_fetch_assoc($rsexistingeditdet);
			$first_visit_qty=$rowexistingeditdet['visit_qty'];
			$first_visit_amount=$rowexistingeditdet['visit_amount'];
			$rate=$rowexistingeditdet['rate'];
			$visit_date=date('Y-m-d',strtotime($rowexistingeditdet['visit_date']));
		}
		else
		{
		$sqlselexistingdet="SELECT qty,amount,sale_rate,SUBSTRING(order_no,-14,8) as order_date FROM order_details WHERE order_no='".$order_no."' AND sku_code='".$product_code."'";
		$rsselexistingdet=mysqli_query($link,$sqlselexistingdet);
		$rowselexistingdet=mysqli_fetch_assoc($rsselexistingdet);
		$first_visit_qty=$rowselexistingdet['qty'];
		$first_visit_amount=$rowselexistingdet['amount'];
		$rate=$rowselexistingdet['sale_rate'];
		$visit_date=date('Y-m-d',strtotime($rowselexistingdet['order_date']));
		}
		
			$sqlupdateorderdetails="UPDATE order_details SET qty='".$edit_qty."',
									amount='".$edit_amount."'
									WHERE order_no='".$order_no."' AND sku_code='".$product_code."'";
			$rsupdateorderdetails=mysqli_query($link,$sqlupdateorderdetails) or die(mysqli_error()." Error in update Order details: ".$sqlupdateorderdetails);
			
			$sqlupdateorderstatus="UPDATE prev_order_counting_master SET visit_qty='".$edit_qty."',
									amount='".$edit_amount."',
									download_time=CURRENT_TIMESTAMP()
									WHERE order_no='".$order_no."' AND product_code='".$product_code."'";
			$rsupdateorderstatus=mysqli_query($link,$sqlupdateorderstatus) or die(mysqli_error()." Error in update Order status: ".$sqlupdateorderstatus);
			if($rsupdateorderstatus && $rsupdateorderdetails)
			{
				$flag=6;
				//INSERT LOG
				$sqlinsertlog="INSERT INTO order_edit_log 
								SET customer_code='".$customer_code."',
								product_code='".$product_code."',
								visit_qty='".$first_visit_qty."',
								visit_date='".$visit_date."',
								order_no='".$order_no."',
								edited_qty='".$edit_qty."',
								rate='".$rate."',
								visit_amount='".$first_visit_amount."',
								edited_amount='".$edit_amount."',
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

 /* --------------------END QUERY FOR ORDER EDIT--------------------------------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-order-edit.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	mysqli_close($link);
?>
