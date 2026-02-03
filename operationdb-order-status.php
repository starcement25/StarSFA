<?php
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
$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

if($nick_name=='PALSONS')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	
}

/*$body='<?xml version="1.0" encoding="UTF-8"?><root><order_status><order_no><![CDATA[OE007820151124191811]]></order_no><product_code><![CDATA[12264]]></product_code><delivery_qty><![CDATA[21]]></delivery_qty><remarks><![CDATA[]]></remarks><status><![CDATA[pending]]></status></order_status><order_status><order_no><![CDATA[OE007820151124191811]]></order_no><product_code><![CDATA[12240]]></product_code><delivery_qty><![CDATA[19]]></delivery_qty><remarks><![CDATA[]]></remarks><status><![CDATA[pending]]></status></order_status></root>';*/

$order_no = "*ROOT*ORDER_STATUS*ORDER_NO";
$product_code = "*ROOT*ORDER_STATUS*PRODUCT_CODE";
$delivery_qty = "*ROOT*ORDER_STATUS*DELIVERY_QTY";
$status = "*ROOT*ORDER_STATUS*STATUS";
$remarks = "*ROOT*ORDER_STATUS*REMARKS";

$order_status_array=array();

$counter = 0;

class xml_order_status{
	var $order_no,$product_code,$delivery_qty,$status,$remarks;	
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
    global $current_tag,$counter,$order_no,$product_code,$delivery_qty,$status,$remarks,$order_status_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,18)=='*ROOT*ORDER_STATUS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $order_no:
				$order_status_array[$counter] = new xml_order_status();
				$order_status_array[$counter]->order_no = $data;
				break;
			case $product_code:
				$order_status_array[$counter]->product_code = $data;
				break;
			case $delivery_qty:
				$order_status_array[$counter]->delivery_qty = $data;
				break;
			case $remarks:
				$order_status_array[$counter]->remarks = $data;
				break;
			case $status:
				$order_status_array[$counter]->status = $data;
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

/* --------------------START QUERY FOR ORDER STATUS------------------------------------------------------------------------------------------------*/
//print_r($order_status_array);
if(count($order_status_array)>0)
{
	for($x=0;$x<count($order_status_array);$x++){
		$order_no=$order_status_array[$x]->order_no;
		$product_code=$order_status_array[$x]->product_code;
		$delivery_qty=$order_status_array[$x]->delivery_qty;
		$remarks=$order_status_array[$x]->remarks;
		$status=$order_status_array[$x]->status;
		
		//For checking that order no exist or not for order status
		$sqlchkorderstatus="SELECT * FROM prev_order_counting_master WHERE order_no='".$order_no."' AND product_code='".$product_code."'";
		$reschkorderstatus = mysqli_query($link,$sqlchkorderstatus) or die(mysqli_error()." Error in check Order Status: ".$sqlchkorderstatus); 
		$rowchkorderstatus = mysqli_fetch_assoc($reschkorderstatus);
		$countchkorderstatus=mysqli_num_rows($reschkorderstatus);
		
		//For update the location table for existing trans id for order
		if($countchkorderstatus>0)
		{
			$sqlupdateorderstatus="UPDATE prev_order_counting_master SET delivery_qty='".$delivery_qty."',
									status='".$status."',
									remarks='".$remarks."',
									delivery_date=CURRENT_TIMESTAMP(),
									download_time=CURRENT_TIMESTAMP()
									WHERE order_no='".$order_no."' AND product_code='".$product_code."'";
			$rsupdateorderstatus=mysqli_query($link,$sqlupdateorderstatus) or die(mysqli_error()." Error in update Order status: ".$sqlupdateorderstatus);
			if($rsupdateorderstatus)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
			if(strtoupper($nick_name)=='PALSONS')
			{
				$customer_code=$rowchkorderstatus['customer_code'];
				$visit_qty=$rowchkorderstatus['visit_qty'];
				$rate=$rowchkorderstatus['rate'];
				$amount=$rowchkorderstatus['amount'];
				$visit_date=$rowchkorderstatus['visit_date'];
				$sqlinsertlog="INSERT INTO order_delivery_log 
								SET customer_code='".$customer_code."',
								product_code='".$product_code."',
								visit_qty='".$visit_qty."',
								visit_date='".$visit_date."',
								order_no='".$order_no."',
								delivery_qty='".$delivery_qty."',
								delivery_date=CURRENT_TIMESTAMP,
								rate='".$rate."',
								visit_amount='".$amount."'";
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
				$sqlseldelivery="SELECT delivery_qty FROM order_delivery_log WHERE order_no='".$order_no."' AND product_code='".$product_code."' 
								ORDER BY delivery_date DESC LIMIT 1,1";
				$rsseldelivery=mysqli_query($link,$sqlseldelivery);
				$rowseldelivery=mysqli_fetch_assoc($rsseldelivery);
				$prev_delivery=$rowseldelivery['delivery_qty'];
				if($prev_delivery=='') $prev_delivery=0;
				//$latest_delivery_qty=$delivery_qty-$prev_delivery;
				/*if($visit_qty==$delivery_qty)
				{
					$latest_delivery_qty=($delivery_qty-$prev_delivery)+1;
				}
				else
				{*/
					$latest_delivery_qty=$visit_qty-$delivery_qty;
				//}
				
				$sqlupdatestock="UPDATE customer_product_stock SET stock=(stock+$latest_delivery_qty) WHERE 	
							customer_code=(SELECT rds_tag FROM customer_master WHERE customer_code='".$customer_code."') AND prod_code='".$product_code."'";
				mysqli_query($link,$sqlupdatestock);			
				/*if(mysqli_query($link,$sqlupdatestock))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}*/	
			}
		}
	}
}

 /* --------------------END QUERY FOR Product Promotion--------------------------------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-order-status.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
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
