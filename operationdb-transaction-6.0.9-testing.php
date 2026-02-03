<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

/*if($nick_name=='ABDOS')
{
	echo $flag=0;
	exit();
}*/
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
$last_git_master_update_time=$_REQUEST['last_git_master_update_time'];
$last_git_master_update_time=str_replace('€',' ',$last_git_master_update_time);
$last_loyalty_purchase_update_time=$_REQUEST['last_loyalty_purchase_update_time'];
$last_loyalty_purchase_update_time=str_replace('€',' ',$last_loyalty_purchase_update_time);

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

//For insertion of fetched xml
if($nick_name=='SKIPPER'  || $nick_name=='DNV' || $nick_name=='ASL' || $nick_name=='GOLDSTONE' || $nick_name=='EDIBLE' || strtoupper($nick_name)=='GOPE')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

	/*$sqlselectdbversion="SELECT is_update FROM table_structure_updation  WHERE emp_code='".$emp_code."' and db_version_code='6.4'";
	$rsselectdbversion=mysqli_query($link,$sqlselectdbversion);
	$rowselectdbversion=mysqli_fetch_assoc($rsselectdbversion);
	$is_update=$rowselectdbversion['is_update'];

	if($is_update==1)
	{
		echo $flag=2;
		exit(); 
	}*/
}

if(sale=='yes')
{
	$sqlquerystockrefresh="SELECT GIT.grn_no AS total_git FROM goods_in_transit GIT  WHERE 
			 			GIT.transaction_type='ST' AND GIT.receiver_code='".$emp_code."' AND UNIX_TIMESTAMP(GIT.download_time) > 
						UNIX_TIMESTAMP('".$last_git_master_update_time."')";
	$resultstockrefresh = mysqli_query($link,$sqlquerystockrefresh);
	$countstockrefresh=mysqli_num_rows($resultstockrefresh);
					
	$sqlquerystocktransferred="SELECT stock_transferred_by FROM stock_refresh_log 
							WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_git_master_update_time."')
							AND emp_code='".$emp_code."' GROUP BY stock_transferred_by";
	$resultstocktransferred = mysqli_query($link,$sqlquerystocktransferred);
	while($rowstocktransferred=mysqli_fetch_assoc($resultstocktransferred))
	{
		$stock_transferred_by_value=$stock_transferred_by_value.$rowstocktransferred['stock_transferred_by'].',';
	}
	$stock_transferred_by_value=substr($stock_transferred_by_value,0,-1);
}
if(loyalty=='yes')
{
	$sqlqueryloyaltyrefresh="SELECT trans_id FROM location WHERE SUBSTRING(trans_id,1,1)='L' AND UNIX_TIMESTAMP(date) > 
	UNIX_TIMESTAMP('".$last_loyalty_purchase_update_time."')";
	$resultloyaltyrefresh = mysqli_query($link,$sqlqueryloyaltyrefresh);
	$countloyaltyrefresh=mysqli_num_rows($resultloyaltyrefresh);
}
if(delete_transaction=='yes')
{
	$sqlrds="SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'";
	$rsrds=mysqli_query($link,$sqlrds);
	$rowrds=mysqli_fetch_assoc($rsrds);
	$rds_code=$rowrds['rds_code'];
	$sqlquerydeleterefresh="SELECT transaction_id FROM activity_log WHERE updated_flag_app='0' AND 
		(rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' OR SUBSTRING(transaction_id,3,5)='".$emp_code."' OR receiver_code='".$emp_code."')";
	$resultdeleterefresh = mysqli_query($link,$sqlquerydeleterefresh);
	$countdeleterefresh=mysqli_num_rows($resultdeleterefresh);
}
$body="<?xml version='1.0' encoding='UTF-8'?><root><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117122939]]></trans_id><latt><![CDATA[27.2945087]]></latt><longi><![CDATA[82.0344133]]></longi><date><![CDATA[2020-01-17 12:29:39]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117122939]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[48076.94]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008069]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12131]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[4414.76]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[8829.52]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12132]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[5269.24]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[10538.48]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1254.88]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[12548.800000000001]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1623.49]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3246.98]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12118]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3246.96]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3246.96]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1730.28]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1730.28]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3140.16]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3140.16]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12113]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3460.56]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3460.56]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117122939]]></Order_no><Sku_code><![CDATA[12081]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1335.2]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1335.2]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117135243]]></trans_id><latt><![CDATA[27.3060143]]></latt><longi><![CDATA[82.0378189]]></longi><date><![CDATA[2020-01-17 13:52:43]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117135243]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[4344.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190715193118]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117135243]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117135243]]></Order_no><Sku_code><![CDATA[12118]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117135243]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117135929]]></trans_id><latt><![CDATA[27.3020395]]></latt><longi><![CDATA[82.0385]]></longi><date><![CDATA[2020-01-17 13:59:29]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117135929]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[3528.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008163]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117135929]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117135929]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1704.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117142535]]></trans_id><latt><![CDATA[27.286554]]></latt><longi><![CDATA[82.0037551]]></longi><date><![CDATA[2020-01-17 14:25:35]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117142535]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[55670.16]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008164]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12118]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3246.96]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3246.96]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12115]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2883.84]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5767.68]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2926.56]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5853.12]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3033.36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3033.36]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12113]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3460.56]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3460.56]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12110]]></Sku_code><qty><![CDATA[4.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3097.44]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[12389.76]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3140.16]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[6280.32]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12122]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[4528.8]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[4528.8]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[4635.6]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[4635.6]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12104]]></Sku_code><qty><![CDATA[4.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1294.8]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5179.2]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142535]]></Order_no><Sku_code><![CDATA[12102]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1294.8]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1294.8]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117142650]]></trans_id><latt><![CDATA[27.286554]]></latt><longi><![CDATA[82.0037551]]></longi><date><![CDATA[2020-01-17 14:26:50]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117142650]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[1520.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820191108201702]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117142650]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[710.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117142650]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[810.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117145016]]></trans_id><latt><![CDATA[27.2817633]]></latt><longi><![CDATA[81.9458442]]></longi><date><![CDATA[2020-01-17 14:50:16]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117145016]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[1227.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190715195434]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117145016]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117145016]]></Order_no><Sku_code><![CDATA[12045]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[400.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117145016]]></Order_no><Sku_code><![CDATA[12190]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[227]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[227.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117150029]]></trans_id><latt><![CDATA[27.3127955]]></latt><longi><![CDATA[81.963867]]></longi><date><![CDATA[2020-01-17 15:00:29]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117150029]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[4000.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008140]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117150029]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117150029]]></Order_no><Sku_code><![CDATA[12042]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117150029]]></Order_no><Sku_code><![CDATA[12043]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117151532]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2020-01-17 15:15:32]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117151532]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[7793.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008159]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117151532]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1944.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117151532]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117151532]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117151532]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1420.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117151532]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[217]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1085.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117160849]]></trans_id><latt><![CDATA[27.2682185]]></latt><longi><![CDATA[81.9839914]]></longi><date><![CDATA[2020-01-17 16:08:49]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117160849]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[2200.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820200117155122]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117160849]]></Order_no><Sku_code><![CDATA[12043]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[200.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117160849]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117160849]]></Order_no><Sku_code><![CDATA[12042]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117160927]]></trans_id><latt><![CDATA[27.2700177]]></latt><longi><![CDATA[81.9826274]]></longi><date><![CDATA[2020-01-17 16:09:27]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117160927]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[3000.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820191108201457]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117160927]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[30.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[46]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1380.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117160927]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1620.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117162737]]></trans_id><latt><![CDATA[27.2669782]]></latt><longi><![CDATA[81.9744994]]></longi><date><![CDATA[2020-01-17 16:27:37]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117162737]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[3110.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820200117162700]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117162737]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[710.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117162737]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117162737]]></Order_no><Sku_code><![CDATA[12042]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117162737]]></Order_no><Sku_code><![CDATA[12043]]></Sku_code><qty><![CDATA[4.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[800.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117165231]]></trans_id><latt><![CDATA[27.2652975]]></latt><longi><![CDATA[81.9767163]]></longi><date><![CDATA[2020-01-17 16:52:32]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117165231]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[16318.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008161]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[30.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[46]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1380.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3888.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3648.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117165231]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[217]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2170.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117172020]]></trans_id><latt><![CDATA[27.2498309]]></latt><longi><![CDATA[81.9895057]]></longi><date><![CDATA[2020-01-17 17:20:20]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117172020]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[4990.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008136]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117172020]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[710.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117172020]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[760.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117172020]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117172020]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117172020]]></Order_no><Sku_code><![CDATA[12045]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200117173750]]></trans_id><latt><![CDATA[27.2464453]]></latt><longi><![CDATA[81.9920817]]></longi><date><![CDATA[2020-01-17 17:37:50]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200117173750]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[2010.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008095]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008141]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200117173750]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117173750]]></Order_no><Sku_code><![CDATA[12178]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[200.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117173750]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[400.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200117173750]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[810.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118130212]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 13:02:12]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118130212]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[39528.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008548]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3888.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[48.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[7296.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3648.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[137]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3288.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[18.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[18.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118130212]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[60.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[10800.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118140544]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 14:05:44]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118140544]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[6612.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008546]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118140544]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[30.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[46]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1380.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118140544]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118140544]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118140618]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 14:06:18]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118140618]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[2000.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008550]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118140618]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2000.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118140654]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 14:06:54]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118140654]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[3540.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008554]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118140654]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1420.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118140654]]></Order_no><Sku_code><![CDATA[12122]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[212]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2120.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118140743]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 14:07:43]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118140743]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[5110.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008567]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118140743]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118140743]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1420.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118140743]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[217]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2170.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118142514]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 14:25:14]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118142514]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[91250.14]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0007979]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118142514]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[50.1]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1821.36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[91250.136]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118150836]]></trans_id><latt><![CDATA[27.073097]]></latt><longi><![CDATA[82.4644966]]></longi><date><![CDATA[2020-01-18 15:08:36]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118150836]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[13536.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008556]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118150836]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3648.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118150836]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118150836]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[36.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[6480.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118164505]]></trans_id><latt><![CDATA[27.0816619]]></latt><longi><![CDATA[82.4598469]]></longi><date><![CDATA[2020-01-18 16:45:05]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118164505]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[6800.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008566]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118164505]]></Order_no><Sku_code><![CDATA[12103]]></Sku_code><qty><![CDATA[40.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[80]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3200.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200118164505]]></Order_no><Sku_code><![CDATA[12045]]></Sku_code><qty><![CDATA[18.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3600.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200118165035]]></trans_id><latt><![CDATA[27.0864183]]></latt><longi><![CDATA[82.451084]]></longi><date><![CDATA[2020-01-18 16:50:35]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200118165035]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[1380.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008540]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008570]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200118165035]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[30.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[46]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1380.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119114350]]></trans_id><latt><![CDATA[27.0934649]]></latt><longi><![CDATA[82.1140633]]></longi><date><![CDATA[2020-01-19 11:43:50]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119114350]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[19280.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008295]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119114350]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[72.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[147]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[10584.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119114350]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[48.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[137]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[6576.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119114350]]></Order_no><Sku_code><![CDATA[12122]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[212]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2120.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119120833]]></trans_id><latt><![CDATA[27.0934649]]></latt><longi><![CDATA[82.1140633]]></longi><date><![CDATA[2020-01-19 12:08:33]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119120833]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[14688.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008296]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119120833]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[60.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[10800.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119120833]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3888.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119123917]]></trans_id><latt><![CDATA[27.0941764]]></latt><longi><![CDATA[82.1137231]]></longi><date><![CDATA[2020-01-19 12:39:17]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119123917]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[54000.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008300]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119123917]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[300.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[54000.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119125154]]></trans_id><latt><![CDATA[27.1008452]]></latt><longi><![CDATA[82.1017691]]></longi><date><![CDATA[2020-01-19 12:51:55]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119125154]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[7200.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008301]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119125154]]></Order_no><Sku_code><![CDATA[12041]]></Sku_code><qty><![CDATA[36.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[200]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[7200.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119140614]]></trans_id><latt><![CDATA[27.1197488]]></latt><longi><![CDATA[82.1526051]]></longi><date><![CDATA[2020-01-19 14:06:15]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119140614]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[14958.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190516135339]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119140614]]></Order_no><Sku_code><![CDATA[12097]]></Sku_code><qty><![CDATA[6.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[250]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1500.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119140614]]></Order_no><Sku_code><![CDATA[12087]]></Sku_code><qty><![CDATA[6.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[590]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3540.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119140614]]></Order_no><Sku_code><![CDATA[12090]]></Sku_code><qty><![CDATA[6.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[365]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2190.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119140614]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119140614]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[4320.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119140648]]></trans_id><latt><![CDATA[27.1197479]]></latt><longi><![CDATA[82.1526248]]></longi><date><![CDATA[2020-01-19 14:06:48]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119140648]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[5920.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008303]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119140648]]></Order_no><Sku_code><![CDATA[12133]]></Sku_code><qty><![CDATA[4.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1480]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5920.0]]></amount><UOM><![CDATA[Container]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119140733]]></trans_id><latt><![CDATA[27.1197479]]></latt><longi><![CDATA[82.1526248]]></longi><date><![CDATA[2020-01-19 14:07:33]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119140733]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[7548.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008299]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119140733]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119140733]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[90.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[46]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[4140.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119142721]]></trans_id><latt><![CDATA[27.118594]]></latt><longi><![CDATA[82.151815]]></longi><date><![CDATA[2020-01-19 14:27:21]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119142721]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[6816.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190516142513]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119142721]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[137]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3288.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119142721]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[147]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3528.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119142808]]></trans_id><latt><![CDATA[27.118594]]></latt><longi><![CDATA[82.151815]]></longi><date><![CDATA[2020-01-19 14:28:08]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119142808]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[2890.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190516143556]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119142808]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[137]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1370.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119142808]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119143751]]></trans_id><latt><![CDATA[27.1191716]]></latt><longi><![CDATA[82.1522051]]></longi><date><![CDATA[2020-01-19 14:37:52]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119143751]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[3140.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190516144029]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119143751]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1520.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119143751]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1620.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119144523]]></trans_id><latt><![CDATA[27.1191704]]></latt><longi><![CDATA[82.1522039]]></longi><date><![CDATA[2020-01-19 14:45:23]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119144523]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[1735.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[NE002820190516144029]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119144523]]></Order_no><Sku_code><![CDATA[12089]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[470]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[470.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144523]]></Order_no><Sku_code><![CDATA[12090]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[365]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[365.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144523]]></Order_no><Sku_code><![CDATA[12092]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[300]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[300.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144523]]></Order_no><Sku_code><![CDATA[12091]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[350]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[350.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144523]]></Order_no><Sku_code><![CDATA[12097]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[250]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[250.0]]></amount><UOM><![CDATA[Bundle]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119144831]]></trans_id><latt><![CDATA[27.1155196]]></latt><longi><![CDATA[82.1490948]]></longi><date><![CDATA[2020-01-19 14:48:31]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119144831]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[17314.00]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008306]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1824.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[142]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3408.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[12.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[162]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[1944.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[152]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3648.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[10.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[217]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2170.0]]></amount><UOM><![CDATA[KG]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119144831]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[24.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[180]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[4320.0]]></amount><UOM><![CDATA[JAR]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200119161415]]></trans_id><latt><![CDATA[27.0468165]]></latt><longi><![CDATA[82.2173064]]></longi><date><![CDATA[2020-01-19 16:14:15]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200119161415]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[78185.51]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[C/0008237]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008237]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1623.49]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[8117.45]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3033.36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[9100.08]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2926.56]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[14632.8]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1730.28]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5190.84]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[8.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3140.16]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[25121.28]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12044]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2670.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[13352.550000000001]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200119161415]]></Order_no><Sku_code><![CDATA[12040]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2670.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2670.51]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200121144740]]></trans_id><latt><![CDATA[27.2941243]]></latt><longi><![CDATA[82.0340727]]></longi><date><![CDATA[2020-01-21 14:47:40]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200121144740]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[46626.82]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0015406]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200121144740]]></Order_no><Sku_code><![CDATA[12137]]></Sku_code><qty><![CDATA[25.6]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1821.36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[46626.816]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order><order><location><emp_code><![CDATA[E0028]]></emp_code><trans_id><![CDATA[OE002820200121163446]]></trans_id><latt><![CDATA[27.294419]]></latt><longi><![CDATA[82.0315655]]></longi><date><![CDATA[2020-01-21 16:34:46]]></date></location><orderdata><order_header><order_no><![CDATA[OE002820200121163446]]></order_no><customer_name><![CDATA[]]></customer_name><Phone_no><![CDATA[]]></Phone_no><address><![CDATA[]]></address><pin_code><![CDATA[]]></pin_code><area><![CDATA[]]></area><area_name><![CDATA[]]></area_name><TD><![CDATA[0]]></TD><sale_type><![CDATA[CREDIT]]></sale_type><order_value><![CDATA[87840.27]]></order_value><d_instruction><![CDATA[]]></d_instruction><tag_distributor_code><![CDATA[]]></tag_distributor_code><cust_type><![CDATA[]]></cust_type><customer_flag><![CDATA[]]></customer_flag><transaction_type><![CDATA[SO]]></transaction_type><vat><![CDATA[0.00]]></vat><grn_no><![CDATA[null]]></grn_no><vertical_value><![CDATA[SP]]></vertical_value><DESTINATION_CODE><![CDATA[]]></DESTINATION_CODE><ORDER_TYPE><![CDATA[]]></ORDER_TYPE><FREIGHT_COMPONENT><![CDATA[]]></FREIGHT_COMPONENT><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><GST_type><![CDATA[]]></GST_type><customer_code><![CDATA[C/0008095]]></customer_code><PRICE_VALIDATION_TYPE><![CDATA[]]></PRICE_VALIDATION_TYPE></order_header><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12116]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2926.56]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5853.12]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12117]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3033.36]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[9100.08]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12118]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3246.96]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3246.96]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12189]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1730.28]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[8651.4]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12111]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3140.16]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3140.16]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12112]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[3246.96]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[3246.96]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12121]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[4635.6]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[9271.2]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12122]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[4528.8]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[9057.6]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12190]]></Sku_code><qty><![CDATA[3.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2424.6]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[7273.799999999999]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12178]]></Sku_code><qty><![CDATA[1.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2670.22]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[2670.22]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12040]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2670.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5341.02]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12043]]></Sku_code><qty><![CDATA[2.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[2670.51]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[5341.02]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12188]]></Sku_code><qty><![CDATA[5.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1623.49]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[8117.45]]></amount><UOM><![CDATA[Canvas Bag]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details><order_details><Order_no><![CDATA[OE002820200121163446]]></Order_no><Sku_code><![CDATA[12047]]></Sku_code><qty><![CDATA[6.0]]></qty><TD><![CDATA[0.00]]></TD><PREMIUM><![CDATA[0]]></PREMIUM><sale_rate><![CDATA[1254.88]]></sale_rate><VAT><![CDATA[0]]></VAT><amount><![CDATA[7529.280000000001]]></amount><UOM><![CDATA[Carton]]></UOM><mrp_code><![CDATA[]]></mrp_code></order_details></orderdata></order></root>";

$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$order_emp_code="*ROOT*ORDER*LOCATION*EMP_CODE";
$order_trans_id = "*ROOT*ORDER*LOCATION*TRANS_ID";
$order_latt = "*ROOT*ORDER*LOCATION*LATT";
$order_longi = "*ROOT*ORDER*LOCATION*LONGI";
$order_date="*ROOT*ORDER*LOCATION*DATE";
$orderdataheader_order_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ORDER_NO";
$orderdataheader_customer_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_CODE";
$orderdataheader_customer_name = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_NAME";
$orderdataheader_phone_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*PHONE_NO";
$orderdataheader_address = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ADDRESS";
$orderdataheader_pin_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*PIN_CODE";
$orderdataheader_area = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*AREA";
$orderdataheader_area_name = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*AREA_NAME";
$orderdataheader_TD = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TD";
$orderdataheader_sale_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*SALE_TYPE";
$orderdataheader_d_instruction = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*D_INSTRUCTION";
$orderdataheader_tag_distributor_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TAG_DISTRIBUTOR_CODE";
$orderdataheader_cust_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUST_TYPE";
$orderdataheader_customer_flag = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*CUSTOMER_FLAG";
$orderdataheader_transaction_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*TRANSACTION_TYPE";
$orderdataheader_VAT= "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*VAT";
$orderdataheader_grn_no = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*GRN_NO";
$orderdataheader_vertical_value = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*VERTICAL_VALUE";
$orderdataheader_destination_code = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*DESTINATION_CODE";
$orderdataheader_order_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*ORDER_TYPE";
$orderdataheader_freight_component = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*FREIGHT_COMPONENT";
$orderdataheader_hint_remarks = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*HINT_REMARKS";
$orderdataheader_GST_type = "*ROOT*ORDER*ORDERDATA*ORDER_HEADER*GST_TYPE";

$orderdatadetails_order_no = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*ORDER_NO";
$orderdatadetails_sku_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SKU_CODE";
$orderdatadetails_qty = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*QTY";
$orderdatadetails_TD = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*TD";
$orderdatadetails_premium = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*PREMIUM";
$orderdatadetails_sale_rate = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*SALE_RATE";
$orderdatadetails_VAT = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*VAT";
$orderdatadetails_amount = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*AMOUNT";
$orderdatadetails_UOM = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*UOM";
$orderdatadetails_mrp_code = "*ROOT*ORDER*ORDERDATA*ORDER_DETAILS*MRP_CODE";

$payment_emp_code="*ROOT*PAYMENT*LOCATION*EMP_CODE";
$payment_trans_id = "*ROOT*PAYMENT*LOCATION*TRANS_ID";
$payment_latt = "*ROOT*PAYMENT*LOCATION*LATT";
$payment_longi = "*ROOT*PAYMENT*LOCATION*LONGI";
$payment_date="*ROOT*PAYMENT*LOCATION*DATE";
$paymentdataheader_receipt_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*RECEIPT_ID";
$paymentdataheader_customer_code = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CUSTOMER_CODE";
$paymentdataheader_amount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*AMOUNT";
$paymentdataheader_cash_cheque = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CASH_CHEQUE";
$paymentdataheader_cheque_no = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*CHEQUE_NO";
$paymentdataheader_date = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*DATE";
$paymentdataheader_bank = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*BANK";
$paymentdataheader_sale_type = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*SALE_TYPE";
$paymentdataheader_p_remark = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_HEADER*P_REMARK";
$paymentdatadetails_receipt_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*RECEIPT_ID";
$paymentdatadetails_invoice_id = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*INVOICE_ID";
$paymentdatadetails_recid = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*RECID";
$paymentdatadetails_amount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*AMOUNT";
$paymentdatadetails_discount = "*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS*DISCOUNT";

$attendance_array = array();
$order_array=array();
$order_details_array=array();
$payment_array=array();
$payment_details_array=array();
$stockist_array=array();
$mt_array=array();

$counter = 0;
$counterorder=0;
$counterorderdetails=0;
$counterpayment=0;
$counterpaymentdetails=0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_order{
	var $order_emp_code,$order_trans_id,$order_latt,$order_longi,$order_date,$orderdataheader_order_no,$orderdataheader_customer_code,$orderdataheader_customer_name,
	$orderdataheader_phone_no,$orderdataheader_address,$orderdataheader_pin_code,$orderdataheader_area,$orderdataheader_area_name,$orderdataheader_TD,$orderdataheader_sale_type,$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_VAT,$orderdataheader_customer_flag,$orderdataheader_grn_no,$orderdataheader_vertical_value,$orderdataheader_destination_code,$orderdataheader_order_type,$orderdataheader_freight_component,$orderdataheader_hint_remarks,$orderdataheader_GST_type;	
}
class xml_order_details{
	var $orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_premium,$orderdatadetails_sale_rate,$orderdatadetails_VAT,$orderdatadetails_amount,$orderdatadetails_UOM,$orderdatadetails_mrp_code;
}
class xml_payment{
	var $payment_emp_code,$payment_trans_id,$payment_latt,$payment_longi,$payment_date,$paymentdataheader_receipt_id,$paymentdataheader_customer_code,$paymentdataheader_amount,$paymentdataheader_cash_cheque,$paymentdataheader_cheque_no,$paymentdataheader_date,$paymentdataheader_bank,$paymentdataheader_sale_type,$paymentdataheader_p_remark;	
}
class xml_payment_details{
	var $paymentdatadetails_receipt_id,$paymentdatadetails_invoice_id,$paymentdatadetails_recid,$paymentdatadetails_amount,$paymentdatadetails_discount;
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
    global $current_tag, $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date, $counter, $counterorder,$counterorderdetails,$counterpayment,$counterpaymentdetails,$attendance_array,$order_array,$order_details_array,$payment_array,$payment_details_array,$stockist_array,$mt_array,
	$order_emp_code,$order_trans_id,$order_latt,$order_longi,$order_date,$orderdataheader_order_no,$orderdataheader_customer_code,$orderdataheader_customer_name,
	$orderdataheader_phone_no,$orderdataheader_address,$orderdataheader_pin_code,$orderdataheader_area,$orderdataheader_area_name,$orderdataheader_TD,$orderdataheader_sale_type,
	$orderdataheader_d_instruction,$orderdataheader_tag_distributor_code,$orderdataheader_cust_type,$orderdataheader_transaction_type,$orderdataheader_VAT,$orderdataheader_grn_no,$orderdataheader_customer_flag,$orderdataheader_vertical_value,$orderdataheader_destination_code,$orderdataheader_order_type,$orderdataheader_freight_component,$orderdataheader_hint_remarks,$orderdataheader_GST_type,$orderdatadetails_order_no,$orderdatadetails_sku_code,$orderdatadetails_qty,$orderdatadetails_TD,$orderdatadetails_premium,$orderdatadetails_sale_rate,$orderdatadetails_VAT,$orderdatadetails_amount,$orderdatadetails_UOM,$orderdatadetails_mrp_code,
	$payment_emp_code,$payment_trans_id,$payment_latt,$payment_longi,$payment_date,$paymentdataheader_receipt_id,$paymentdataheader_customer_code,$paymentdataheader_amount,
	$paymentdataheader_cash_cheque,$paymentdataheader_cheque_no,$paymentdataheader_date,$paymentdataheader_bank,$paymentdataheader_sale_type,$paymentdataheader_p_remark,$paymentdatadetails_receipt_id, $paymentdatadetails_invoice_id,$paymentdatadetails_recid,$paymentdatadetails_amount,$paymentdatadetails_discount;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,16)=='*ROOT*ATTENDANCE')
	{
		switch($current_tag){
			case $attendance_emp_code:
				$attendance_array[$counter] = new xml_attendance();
				$attendance_array[$counter]->emp_code = $data;
				break;
			case $attendance_trans_id:
				$attendance_array[$counter]->trans_id = $data;
				break;
			case $attendance_latt:
				$attendance_array[$counter]->latt = $data;
				break;
			case $attendance_longi:
				$attendance_array[$counter]->longi = $data;
				break;
			case $attendance_date:
				$attendance_array[$counter]->attendance_date = $data;
				break;
			case $attendancedata_emp_code:
				$attendance_array[$counter]->attendancedata_emp_code = $data;
				break;
			case $attendancedata_date:
				$attendance_array[$counter]->attendancedata_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,11)=='*ROOT*ORDER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $order_emp_code:
				$order_array[$counterorder] = new xml_order();
				$order_array[$counterorder]->order_emp_code = $data;
				break;
			case $order_trans_id:
				$order_array[$counterorder]->order_trans_id = $data;
				break;
			case $order_latt:
				$order_array[$counterorder]->order_latt = $data;
				break;
			case $order_longi:
				$order_array[$counterorder]->order_longi = $data;
				break;
			case $order_date:
				$order_array[$counterorder]->order_date = $data;
				break;
			case $orderdataheader_order_no:
				$order_array[$counterorder]->orderdataheader_order_no = $data;
				break;
			case $orderdataheader_customer_name:
				$order_array[$counterorder]->orderdataheader_customer_name = $data;
				break;
			case $orderdataheader_phone_no:
				$order_array[$counterorder]->orderdataheader_phone_no = $data;
				break;
			case $orderdataheader_address:
				$order_array[$counterorder]->orderdataheader_address = $data;
				break;
			case $orderdataheader_pin_code:
				$order_array[$counterorder]->orderdataheader_pin_code = $data;
				break;
			case $orderdataheader_area:
				$order_array[$counterorder]->orderdataheader_area = $data;
				break;
			case $orderdataheader_area_name:
				$order_array[$counterorder]->orderdataheader_area_name = $data;
				break;	
			case $orderdataheader_TD:
				$order_array[$counterorder]->orderdataheader_TD = $data;
				break;					
			case $orderdataheader_sale_type:
				$order_array[$counterorder]->orderdataheader_sale_type = $data;
				break;	
			case $orderdataheader_d_instruction:
				$order_array[$counterorder]->orderdataheader_d_instruction = $data;
				break;
			case $orderdataheader_tag_distributor_code:
				$order_array[$counterorder]->orderdataheader_tag_distributor_code = $data;
				break;
			case $orderdataheader_cust_type:
				$order_array[$counterorder]->orderdataheader_cust_type = $data;
				break;		
			case $orderdataheader_customer_flag:
				$order_array[$counterorder]->orderdataheader_customer_flag = $data;
				break;
			case $orderdataheader_transaction_type:
				$order_array[$counterorder]->orderdataheader_transaction_type = $data;
				break;
			case $orderdataheader_VAT:
				$order_array[$counterorder]->orderdataheader_VAT = $data;
				break;
			case $orderdataheader_grn_no:
				$order_array[$counterorder]->orderdataheader_grn_no = $data;
				break;
			case $orderdataheader_vertical_value:
				$order_array[$counterorder]->orderdataheader_vertical_value = $data;
				break;	
			case $orderdataheader_destination_code:
				$order_array[$counterorder]->orderdataheader_destination_code = $data;
				break;	
			case $orderdataheader_order_type:
				$order_array[$counterorder]->orderdataheader_order_type = $data;
				break;			
			case $orderdataheader_freight_component:
				$order_array[$counterorder]->orderdataheader_freight_component = $data;
				break;
			case $orderdataheader_hint_remarks:
				$order_array[$counterorder]->orderdataheader_hint_remarks = $data;
				break;
			case $orderdataheader_GST_type:
				$order_array[$counterorder]->orderdataheader_GST_type = $data;
				break;
			case $orderdataheader_customer_code:
				$order_array[$counterorder]->orderdataheader_customer_code = $data;
				$counterorder++;
				break;
		}
	}
	if(substr($current_tag,0,35)=='*ROOT*ORDER*ORDERDATA*ORDER_DETAILS')
		{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';			
			switch($current_tag){
				case $orderdatadetails_order_no:
					$order_details_array[$counterorderdetails] = new xml_order_details();

					$order_details_array[$counterorderdetails]->orderdatadetails_order_no = $data;
					break;
				case $orderdatadetails_sku_code:
					$order_details_array[$counterorderdetails]->orderdatadetails_sku_code = $data;
					break;
				case $orderdatadetails_qty:
					$order_details_array[$counterorderdetails]->orderdatadetails_qty = $data;
					break;
				case $orderdatadetails_TD:
					$order_details_array[$counterorderdetails]->orderdatadetails_TD = $data;
					break;
				case $orderdatadetails_premium:
					$order_details_array[$counterorderdetails]->orderdatadetails_premium = $data;
					break;	
				case $orderdatadetails_sale_rate:
					$order_details_array[$counterorderdetails]->orderdatadetails_sale_rate = $data;
					break;
				case $orderdatadetails_VAT:
					$order_details_array[$counterorderdetails]->orderdatadetails_VAT = $data;
					break;
				case $orderdatadetails_amount:
					$order_details_array[$counterorderdetails]->orderdatadetails_amount = $data;
					break;
				case $orderdatadetails_UOM:
					$order_details_array[$counterorderdetails]->orderdatadetails_UOM = $data;
					break;
				case $orderdatadetails_mrp_code:
					$order_details_array[$counterorderdetails]->orderdatadetails_mrp_code = $data;
					$counterorderdetails++;
					break;
		}
	}
	if(substr($current_tag,0,13)=='*ROOT*PAYMENT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';			
		switch($current_tag){
			case $payment_emp_code:
				$payment_array[$counterpayment] = new xml_payment();
				$payment_array[$counterpayment]->payment_emp_code = $data;
				break;
			case $payment_trans_id:
				$payment_array[$counterpayment]->payment_trans_id = $data;
				break;
			case $payment_latt:
				$payment_array[$counterpayment]->payment_latt = $data;
				break;
			case $payment_longi:
				$payment_array[$counterpayment]->payment_longi = $data;
				break;
			case $payment_date:
				$payment_array[$counterpayment]->payment_date = $data;
				break;
			case $paymentdataheader_receipt_id:
				$payment_array[$counterpayment]->paymentdataheader_receipt_id = $data;
				break;
			case $paymentdataheader_amount:
				$payment_array[$counterpayment]->paymentdataheader_amount = $data;
				break;
			case $paymentdataheader_cash_cheque:
				$payment_array[$counterpayment]->paymentdataheader_cash_cheque = $data;
				break;
			case $paymentdataheader_cheque_no:
				$payment_array[$counterpayment]->paymentdataheader_cheque_no = $data;
				break;
			case $paymentdataheader_date:
				$payment_array[$counterpayment]->paymentdataheader_date = $data;
				break;
			case $paymentdataheader_bank:
				$payment_array[$counterpayment]->paymentdataheader_bank = $data;
				break;
			case $paymentdataheader_sale_type:
				$payment_array[$counterpayment]->paymentdataheader_sale_type = $data;
				break;		
			case $paymentdataheader_p_remark:
				$payment_array[$counterpayment]->paymentdataheader_p_remark = $data;
				break;			
			case $paymentdataheader_customer_code:
				$payment_array[$counterpayment]->paymentdataheader_customer_code = $data;
				$counterpayment++;
				break;
		}
	}
	if(substr($current_tag,0,41)=='*ROOT*PAYMENT*PAYMENTDATA*PAYMENT_DETAILS')
		{
		switch($current_tag){
			case $paymentdatadetails_receipt_id:
				$payment_details_array[$counterpaymentdetails] = new xml_payment_details();
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_receipt_id = $data;
				break;
			case $paymentdatadetails_invoice_id:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_invoice_id = $data;
				break;
			case $paymentdatadetails_recid:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_recid = $data;
				break;
			case $paymentdatadetails_amount:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_amount = $data;
				break;
			case $paymentdatadetails_discount:
				$payment_details_array[$counterpaymentdetails]->paymentdatadetails_discount = $data;
				$counterpaymentdetails++;
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
/* ------------------------------------------------START QUERY FOR ATTENDANCE---------------------------------------------------------------------*/
if(count($attendance_array)>0)
{
	for($x=0;$x<count($attendance_array);$x++){
		$emp_code=$attendance_array[$x]->emp_code;
		$trans_id=$attendance_array[$x]->trans_id;
		$latt=$attendance_array[$x]->latt;
		$longi=$attendance_array[$x]->longi;
		$attendance_date=$attendance_array[$x]->attendance_date;
		$attendance_emp_code=$attendance_array[$x]->attendancedata_emp_code;
		$attendancedata_date=$attendance_array[$x]->attendancedata_date;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if(DCR_map=='no')
		{
			if($latt>0 && $longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
		}
		
		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check attendance location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		$sqlchkattlocationempdate="SELECT * FROM location WHERE emp_code='".$emp_code."' AND SUBSTRING(date,1,10)='".substr($attendance_date,0,10)."' AND trans_id LIKE 'A%'";
		$reschkattlocationempdate = mysqli_query($link,$sqlchkattlocationempdate) or die(mysqli_error()." Error in check attendance location emp date: ".$sqlchkattlocationempdate); 
		$countchkattlocationempdate=mysqli_num_rows($reschkattlocationempdate);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0 || $countchkattlocationempdate >0)
		{
			$sqlupdateattlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateattlocation=mysqli_query($link,$sqlupdateattlocation) or die(mysqli_error()." Error in update attendance location: ".$sqlupdateattlocation);
			if($rsupdateattlocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of attendance
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertattlocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$attendance_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertattendance="INSERT INTO attendence SET emp_code='".$attendance_emp_code."',
								 date='".$attendancedata_date."'";	
			if(mysqli_query($link,$sqlinsertattlocation) && mysqli_query($link,$sqlinsertattendance))
				{
					$flag=5;
					
					//Start for STAR mis data details present
					if($nick_name=='STAR')
					{
						$qty='';
						$trans_type='A';
						$trans_sub_type='';
						update_transaction_STAR($emp_code,$trans_id,$qty,$trans_type,$trans_sub_type);
					}
					//End for STAR mis data details present
					$last_operation_datetime=$attendance_date;
					// For Sending email to recipents for attendance
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=title_case_emp($rowempname['emp_name']);
					$vertical_value=$rowempname['vertical_value'];
					$branch_code=$rowempname['branch_code'];
					
					if(branch_vertical_operation_wise_email=='yes')
					{
						$operation_type='Attendance';
						$attendance_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
					}
					else
					{
						$attendance_email=ATTENDANCEEMAILRECIPENTS;
					}
					$address=getReverseGeo($latt,$longi);
					$attendanceemailsubj="$nick_name - Attendance - ".$emp_name." on ".date('d-m-Y',strtotime($attendance_date))." @".date('H:i:s',strtotime($attendance_date)).' hrs.';
					$attendancemailbody = "<html><head><title>Attendance</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." marked as present on <b>".date('d-m-Y H:i:s',strtotime($attendance_date))."</b> 
										at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($attendance_email, $attendanceemailsubj, $attendancemailbody, $headers,$spam_filter))
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
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			
		}// End of else
	}// End for loop
}// End attendance array if 

 /* --------------------END QUERY FOR ATTENDANCE------------------------------------------------------------------------------------------------------------*/
/* --------------------START QUERY FOR ORDER----------------------------------------------------------------------------------------------------------------*/
$order_array_trans_id=array();
$orderheader_array_mailbody=array();
$orderinstruction_array_mailbody=array();
$orderdetails_array_mailbody=array();
$orderemp_array_mailbody=array();
$ordertansid_array_mailbody=array();
$ordercashreceived_array_mailbody=array();
$orderdataheader_sale_type_array=array();
$orderdate_array_mailbody=array();
$orderdataheader_TD_array=array();
$orderdataheader_transaction_type_array=array();
$orderdataheader_entity_name_array=array();
$orderdataheader_customerphone_array=array();
//print_r($order_array);
if(count($order_array)>0)
{
	for($x=0;$x<count($order_array);$x++){
		$order_emp_code=$order_array[$x]->order_emp_code;
		$order_trans_id=$order_array[$x]->order_trans_id;
		$order_latt=$order_array[$x]->order_latt;
		$order_longi=$order_array[$x]->order_longi;
		$order_date=$order_array[$x]->order_date;
		$orderdataheader_order_no=$order_array[$x]->orderdataheader_order_no;
		$orderdataheader_customer_code=$order_array[$x]->orderdataheader_customer_code;
		$orderdataheader_customer_name=$order_array[$x]->orderdataheader_customer_name;
		$orderdataheader_phone_no=$order_array[$x]->orderdataheader_phone_no;
		$orderdataheader_address=$order_array[$x]->orderdataheader_address;
		$orderdataheader_pin_code=$order_array[$x]->orderdataheader_pin_code;
		$orderdataheader_area=$order_array[$x]->orderdataheader_area;
		$orderdataheader_area_name=$order_array[$x]->orderdataheader_area_name;
		$orderdataheader_sale_type=$order_array[$x]->orderdataheader_sale_type;
		$orderdataheader_TD=$order_array[$x]->orderdataheader_TD;
		$orderdataheader_cust_type=$order_array[$x]->orderdataheader_cust_type;
		$orderdataheader_customer_flag=$order_array[$x]->orderdataheader_customer_flag;
		$orderdataheader_transaction_type=$order_array[$x]->orderdataheader_transaction_type;
		$orderdataheader_d_instruction=$order_array[$x]->orderdataheader_d_instruction;
		$orderdataheader_VAT=$order_array[$x]->orderdataheader_VAT;
		$orderdataheader_grn_no=$order_array[$x]->orderdataheader_grn_no;
		$orderdataheader_vertical_value=$order_array[$x]->orderdataheader_vertical_value;
		$orderdataheader_tag_distributor_code=$order_array[$x]->orderdataheader_tag_distributor_code;
		$orderdataheader_destination_code=$order_array[$x]->orderdataheader_destination_code;
		$orderdataheader_order_type=$order_array[$x]->orderdataheader_order_type;
		$orderdataheader_freight_component=$order_array[$x]->orderdataheader_freight_component;
		$orderdataheader_hint_remarks=$order_array[$x]->orderdataheader_hint_remarks;
		$orderdataheader_GST_type=$order_array[$x]->orderdataheader_GST_type;

		if($orderdataheader_sale_type=='CASH'){
		$paymentdataheader_amount=$payment_array[$x]->paymentdataheader_amount;
		}
		if($orderdataheader_sale_type=='' && $orderdataheader_transaction_type=='SB')
		{
			$orderdataheader_sale_type='CREDIT';
		}
		${orderdataheader_transaction_type.$orderdataheader_order_no}=$orderdataheader_transaction_type;
		${orderdataheader_grn_no.$orderdataheader_order_no}=$orderdataheader_grn_no;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if(DCR_map=='no')
		{
			if($order_latt>0 && $order_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$order_latt."',longi='".$order_longi."' WHERE 
										emp_code='".$order_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$order_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check order location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			//$order_trans_id_chk=substr($order_trans_id,1,19);
			if(!in_array($order_trans_id,$order_array_trans_id))
			{
				array_push($order_array_trans_id,$order_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$order_emp_code."',
									latt='".$order_latt."',
									longi='".$order_longi."'
									WHERE trans_id='".$order_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update order location: ".$sqlupdateorlocation);
			if($rsupdateorlocation)
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
			$sqlempname="SELECT emp_name,SUBSTRING_INDEX( branch_code, ',', 1 ) AS branch_code,vertical_value,reporting_to,dns_emp_code FROM employee_master WHERE emp_code='".$order_emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=title_case_emp($rowempname['emp_name']);
			$branch_code=$rowempname['branch_code'];
			$dns_emp_code=$rowempname['dns_emp_code'];
			$vertical_value=$rowempname['vertical_value'];
			$reporting_to=$rowempname['reporting_to'];
			
			$sqlnewcustomerrds="SELECT rds_code FROM rds_master WHERE emp_code='".$order_emp_code."'";
			$rsnewcustomerrds=mysqli_query($link,$sqlnewcustomerrds);
			$rownewcustomerrds=mysqli_fetch_assoc($rsnewcustomerrds);
			$rds_codenewcustomerrds=$rownewcustomerrds['rds_code'];

			// create the data for location table date field , by checking the current date and time and the actual date and time of order
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));

			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$current_date=$year.'-'.$month.'-'.$date;
			//For Insert into the location table for new trans id regarding order
			echo $sqlinsertorlocation="INSERT INTO location SET emp_code='".$order_emp_code."',
									trans_id='".$order_trans_id."',
									latt='".$order_latt."',
									longi='".$order_longi."',
									date='".$order_date."',
									updatetime='".$location_date."'"; 
									
			if(strtoupper($nick_name)=='STAR')
			{
				$branch_condiion=" branch_code	='".$branch_code."',";
			}
			else
			{
				$branch_condiion='';
			}
			//For Insert into the Order Header table for new trans id
			echo $sqlinsertorderheader="INSERT INTO order_header SET order_no='".$orderdataheader_order_no."',
								  customer_code 	='".$orderdataheader_customer_code."',
								  sale_type 		='".$orderdataheader_sale_type."',
								  TD				='".$orderdataheader_TD."',
								  tag_distributor_code='".$orderdataheader_tag_distributor_code."',
								  VAT          		='".$orderdataheader_VAT."',
								  transaction_type  ='".$orderdataheader_transaction_type."',
								  vertical_value    ='".$orderdataheader_vertical_value."',
								  destination_code  ='".$orderdataheader_destination_code."',
								  order_type    	='".$orderdataheader_order_type."',
								  hint_remarks    	='".$orderdataheader_hint_remarks."',
								  freight_component ='".$orderdataheader_freight_component."',".$branch_condiion."
								  GST_type 			='".$orderdataheader_GST_type."',
								  is_printed 		='',
								  d_instruction		='".addslashes($orderdataheader_d_instruction)."'";   
			/*if($orderdataheader_customer_flag=='N'){
			$sqlinsertprospectcustomer="INSERT INTO customer_master SET emp_code='".$order_emp_code."',
									   customer_code				='".$orderdataheader_customer_code."',
									   customer_name				='".addslashes($orderdataheader_customer_name)."',
									   address						='".$orderdataheader_address."',
									   pin							='".$orderdataheader_pin_code."',
									   phone_no						='".$orderdataheader_phone_no."',
									   cust_type					='".$orderdataheader_cust_type."',
									   branch_code					='".$branch_code."',
									   rds_tag						='".$rds_codenewcustomerrds."',
									   download_time				=CURRENT_TIMESTAMP(),
									   download_time_credit_limit	=CURRENT_TIMESTAMP(),
									   acedns						='Y',
									   black_list					='N',
									   route_code					='".$orderdataheader_area."'";						  	
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertorderheader) && mysqli_query($link,$sqlinsertprospectcustomer))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					//ON 14-07-2014
					echo $flag=0;
					//echo $flag=2;
					return;
				}
			}
			else
			{*/
				if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertorderheader))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			//}
			// Add new route to route master
			/*if(substr($orderdataheader_area,0,1)=='N'){
				$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$orderdataheader_area."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$countroute=mysqli_num_rows($rsroute);
				if($countroute<1)
				{
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$orderdataheader_area."'";
					$sqlroute .= " ,dns_route_code=''";
					$sqlroute .= " ,route_name='".addslashes($orderdataheader_area_name)."'";
					$sqlroute .= " , emp_code='".$order_emp_code."'";
					$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlroute))
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
			}*/
			//Add new rds to rds master
				/*if($orderdataheader_cust_type=='D'){
					$sqlrds  = "insert into rds_master ";
					$sqlrds .= " SET rds_code='".$orderdataheader_customer_code."'";
					$sqlrds .= " ,dns_rds_code=''";
					$sqlrds .= " ,rds_name='".addslashes($orderdataheader_customer_name)."'";
					$sqlrds .= " , emp_code='".$order_emp_code."'";
					$sqlrds .= " , rds_type='".$orderdataheader_cust_type."'";
					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlrds))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}*/
				//Regarding Email Sending
				if(branch_vertical_operation_wise_email=='yes')
				{
					$operation_type='Order';
					$order_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				}
				else
				{
					if($nick_name=='RUPA' || $nick_name=='RUPAT')
					{
						$order_email='';
					}
					else if($nick_name=='STAR')
					{
						$sqlbranchmail="SELECT branch_email_id FROM branch_master WHERE branch_code='".$branch_code."'";
						$rsbranchmail=mysqli_query($link,$sqlbranchmail);
						$rowbranchmail=mysqli_fetch_assoc($rsbranchmail);
						$branch_email_id=$rowbranchmail['branch_email_id'];
						if($branch_email_id !='')
						{
							$order_email=ORDEREMAILRECIPENTS.','.$branch_email_id;
						}
						else
						{
							$order_email=ORDEREMAILRECIPENTS;
						}
					}					
					else
					{
						$order_email=ORDEREMAILRECIPENTS;
					}
				}
				if(substr($order_trans_id,0,1)!='N')
				{
					$addressorder=getReverseGeo($order_latt,$order_longi);
				}
				if($orderdataheader_customer_flag=='N'){
				$customer_name=$orderdataheader_customer_name;
				$orderheader_TR="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>New Customer Added</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>
								<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Phone no</span></strong></th>";
				
				$orderheader_TD="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$orderdataheader_area_name."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								+91-".$orderdataheader_phone_no."</span>&nbsp;</td>";
				  $customer_phone_no=$orderdataheader_phone_no;				
				}
				else
				{
					$sqlcustomername="SELECT dns_customer_code,customer_name,route_code,cust_type,cust_class,phone_no FROM customer_master 
									WHERE customer_code='".$orderdataheader_customer_code."'";
					$rscustomername=mysqli_query($link,$sqlcustomername);
					$rowcustomername=mysqli_fetch_assoc($rscustomername);
					$customer_name=$rowcustomername['customer_name'];
					$dns_customer_code=$rowcustomername['dns_customer_code'];
					$route_code=$rowcustomername['route_code'];
					$cust_type=$rowcustomername['cust_type'];
					$phone_no=$rowcustomername['phone_no'];
					
					$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
					$rsroutename=mysqli_query($link,$sqlroutename);
					$rowroutename=mysqli_fetch_assoc($rsroutename);
					$route_name=$rowroutename['route_name'];
					
					if(providing_code=='yes'){
						$orderheader_dns_customer_code_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Code</span></strong></th>";
						$orderheader_dns_customer_code_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$dns_customer_code."</span>&nbsp;</td>";
					}
					else
					{
						$orderheader_dns_customer_code_TR='';
						$orderheader_dns_customer_code_TD=='';
					}
					
					$orderheader_TR=$orderheader_dns_customer_code_TR."<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th><th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>";
					$orderheader_TD=$orderheader_dns_customer_code_TD."<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td><td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$route_name."</span>&nbsp;</td>";
					$customer_phone_no=$phone_no;
				}
				
				//-------------------------------------------For Sale Feature-----------------------------------------------------------------------------
				if($orderdataheader_transaction_type=='PB')
					{ 
						$transaction_type_details='Purchase Bill';
						//Vendor
						$sqlvendor="SELECT vendor_name FROM vendor_master WHERE vendor_code='".$orderdataheader_customer_code."'";
						$rsvendor=mysqli_query($link,$sqlvendor);
						$rowvendor=mysqli_fetch_assoc($rsvendor);
						$vendor_name=$rowvendor['vendor_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Vendor Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$vendor_name."</span>&nbsp;</td>";
					$entity_name=$vendor_name;
					}
					if($orderdataheader_transaction_type=='BT' || $orderdataheader_transaction_type=='ST' || $orderdataheader_transaction_type=='SR')
					{ 
						if($orderdataheader_transaction_type=='BT') $transaction_type_details='Depot Transfer';
						if($orderdataheader_transaction_type=='ST') $transaction_type_details='Stock Transfer';
						if($orderdataheader_transaction_type=='SR') $transaction_type_details='Stock Return';
						
						if($orderdataheader_transaction_type=='BT' || $orderdataheader_transaction_type=='ST')
						{
							//RDS
							$sqlrds="SELECT rds_name,emp_code FROM rds_master WHERE rds_code='".$orderdataheader_customer_code."'";
							$rsrds=mysqli_query($link,$sqlrds);
							$rowrds=mysqli_fetch_assoc($rsrds);
							$rds_name=$rowrds['rds_name'];
							${stock_receive_emp_code.$orderdataheader_order_no}=$rowrds['emp_code'];
							if($orderdataheader_transaction_type=='BT'){ 
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Stock Received From</span></strong></th>";
								$sqlrdsnamestockreceived="SELECT rds_name FROM rds_master WHERE emp_code='".substr($orderdataheader_grn_no,1,5)."'";
								$rsrdsnamestockreceived=mysqli_query($link,$sqlrdsnamestockreceived);
								$rowrdsnamestockreceived=mysqli_fetch_assoc($rsrdsnamestockreceived);
								$rds_name=$rowrdsnamestockreceived['rds_name'];
							}
							else
							{
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Stock Transfer To</span></strong></th>";
							}
							
							$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$rds_name."</span>&nbsp;</td>";
							$entity_name=$rds_name;
							
							if($orderdataheader_transaction_type=='ST')
							{
								// updating data refresh log for stock receiving employee
								$sqlInsertstockrefresh="INSERT INTO stock_refresh_log SET emp_code='".${stock_receive_emp_code.$orderdataheader_order_no}."',refresh_date_time=CURRENT_TIMESTAMP(),stock_transferred_by='".$emp_name."'";
								mysqli_query($link,$sqlInsertstockrefresh);
								
								/*$sqlUpdatetablestructure="UPDATE table_structure_updation SET
														  is_update='1'
														  WHERE emp_code='".$stock_receive_emp_code."' AND db_version_code='6.3'";
								mysqli_query($link,$sqlUpdatetablestructure);*/
							}
						}
						else
						{
							//Branch
							$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$orderdataheader_customer_code."'";
							$rsbranch=mysqli_query($link,$sqlbranch);
							$rowbranch=mysqli_fetch_assoc($rsbranch);
							$branch_name=$rowbranch['branch_name'];
							$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Branch Name</span></strong></th>";
							$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$branch_name."</span>&nbsp;</td>";
							$entity_name=$branch_name;
						}
					}
					if($orderdataheader_transaction_type=='CN')
					{ 
						if($orderdataheader_transaction_type=='CN') $transaction_type_details='Carry In Stock Out';
						//Employee
						$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$orderdataheader_customer_code."'";
						$rsempname=mysqli_query($link,$sqlempname);
						$rowempname=mysqli_fetch_assoc($rsempname);
						$stock_hadnded_over_to=$rowempname['emp_name'];
						$orderheader_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Employee Name</span></strong></th>";
					$orderheader_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$stock_hadnded_over_to."</span>&nbsp;</td>";
					$entity_name=$stock_hadnded_over_to;
						// updating data refresh log for receiving the stock
						/*$sqlInsertdatarefresh="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
						mysqli_query($link,$sqlInsertdatarefresh);*/
					}
					if($orderdataheader_transaction_type=='SB' || $orderdataheader_transaction_type=='SO' || $orderdataheader_transaction_type=='CR' || $orderdataheader_transaction_type=='TO'){
						if($orderdataheader_transaction_type=='SB') $transaction_type_details='Sale Bill';
						if($orderdataheader_transaction_type=='SO') $transaction_type_details='Sale Order';
						if($orderdataheader_transaction_type=='CR') $transaction_type_details='Carry In Return';
						if($orderdataheader_transaction_type=='TO') $transaction_type_details='Telephonic Order';
						//customer
						$sqlcustomername="SELECT dns_customer_code,customer_name,route_code FROM customer_master WHERE 
											customer_code='".$orderdataheader_customer_code."'";
						$rscustomername=mysqli_query($link,$sqlcustomername);
						$rowcustomername=mysqli_fetch_assoc($rscustomername);
						$customer_name=$rowcustomername['customer_name'];
						$dns_customer_code=$rowcustomername['dns_customer_code'];
						$route_code=$rowcustomername['route_code'];
						
						$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
						$rsroutename=mysqli_query($link,$sqlroutename);
						$rowroutename=mysqli_fetch_assoc($rsroutename);
						$route_name=$rowroutename['route_name'];
						
						if(providing_code=='yes'){
							$orderheader_dns_customer_code_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Code</span></strong></th>";
							$orderheader_dns_customer_code_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$dns_customer_code."</span>&nbsp;</td>";
						}
						else
						{
							$orderheader_dns_customer_code_TR='';
							$orderheader_dns_customer_code_TD=='';
						}
					
						$orderheader_TR=$orderheader_dns_customer_code_TR."<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th><th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th>";
					$orderheader_TD=$orderheader_dns_customer_code_TD."<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$customer_name."</span>&nbsp;</td><td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$route_name."</span>&nbsp;</td>";
					$entity_name=$customer_name;
					}
					if($orderdataheader_transaction_type=='SA' || $orderdataheader_transaction_type=='SH')
					{ 
						$transaction_type_details='Material Shortage Booking';
						$orderheader_TR="";
						$orderheader_TD="";
						$entity_name="";
					}
					if($orderdataheader_transaction_type=='RP')
					{ 
						$transaction_type_details='Replacement';
						$entity_name="";
					}
					if($orderdataheader_transaction_type=='ISP')
					{ 
						$transaction_type_details='ISP';
						$entity_name="";
					}
					//-------------------------------------------End For Sale Feature-------------------------------------------------------------------------
				
				if($orderdataheader_tag_distributor_code!='')
				{
					if($nick_name=='RKBK')
					{
						$sqldistributor="SELECT rds_name FROM rds_master WHERE rds_code='".$orderdataheader_tag_distributor_code."'";
					}
					else
					{
						$sqldistributor="SELECT customer_name FROM customer_master WHERE customer_code='".$orderdataheader_tag_distributor_code."'";
					}
					$rsdistributor=mysqli_query($link,$sqldistributor);
					$rowdistributor=mysqli_fetch_assoc($rsdistributor);
					if($nick_name=='RKBK')
					{
						$distributor_name=$rowdistributor['rds_name'];
					}
					else
					{
						$distributor_name=$rowdistributor['customer_name'];
					}

					$orderheader_TR_distributor="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Tagged To Distributor</span></strong></th>";
					$orderheader_TD_distributor="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$distributor_name."</span>&nbsp;</td>";
				}
				else
				{
					$orderheader_TR_distributor="";
					$orderheader_TD_distributor="";
				}
				
				//For sending email to recipents in case of No Order transaction happened
				if($nick_name=='STAR' || $nick_name=='START')
				{
					if($reporting_to !='')
					{
						if($orderdataheader_hint_remarks=='Branding Requirement' || $orderdataheader_hint_remarks=='Technical Requirement')
						{
							$visit_date=date('d-m-Y H:i:s',strtotime($order_date));
							send_hint_remarks_email_sms($orderdataheader_hint_remarks,$reporting_to,$visit_date,$emp_name,$orderdataheader_customer_code,$orderdataheader_d_instruction,$nick_name,$orderdataheader_order_no);
						}
					}
				}
				$last_operation_datetime=$order_date;
				if(substr($order_trans_id,0,1)=='N')
				{
			      //Start for STAR mis data details No order data updation
				  if($nick_name=='STAR')
					{
						$qty='';
						$trans_type='NO';
						$trans_sub_type='';
						update_transaction_STAR($order_emp_code,$orderdataheader_order_no,$qty,$trans_type,$trans_sub_type);
					}
				  //End for STAR mis data details No order data updation
					$noorderemailsubj="$nick_name - Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($order_date))." @".date('H:i:s',strtotime($order_date)).' hrs.';

					$noorderemailbody = "<html><head><title>No Order</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name.". No order happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($orderdataheader_d_instruction)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($order_email,$noorderemailsubj, $noorderemailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}// End of if block for No Order transaction checking
				else{
					if(vertical_fields=='yes')
					{
						$orderheader_TR_vertical="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Vertical Value</span></strong></th>";
						$orderheader_TD_vertical="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$orderdataheader_vertical_value."</span>&nbsp;</td>";
					}
					if(destination_price_list=='yes' || destination_ordertype_price_list=='yes' || destination=='yes')
					{
						$sqldestination="SELECT destination_name FROM destination_master WHERE destination_code='".$orderdataheader_destination_code."'";
						$rsdestination=mysqli_query($link,$sqldestination);
						$rowdestination=mysqli_fetch_assoc($rsdestination);
						$destination_name=$rowdestination['destination_name'];

						$orderheader_TR_destination="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Destination</span></strong></th>";
						$orderheader_TD_destination="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$destination_name."</span>&nbsp;</td>";
						
						/*$orderheader_TR_ordertype="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Type</span></strong></th>";
						$orderheader_TD_ordertype="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$orderdataheader_order_type."</span>&nbsp;</td>";
						
							$orderheader_TR_freight_component="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Freight</span></strong></th>";
						$orderheader_TD_freight_component="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$orderdataheader_freight_component."</span>&nbsp;</td>";*/
					}
						

				//For constructing the email body for ORDER HEADER if ORDER has performed
				$orderemailbody = "<html><head><title>Order Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br /><br />Refference no: <b>".$orderdataheader_order_no."</b><br /><br /><table border=1 style=background-color:AliceBlue>
							<tr>".$orderheader_TR.$orderheader_TR_distributor.$orderheader_TR_vertical."
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Transaction Type</span></strong></th>".$orderheader_TR_destination.$orderheader_TR_ordertype.$orderheader_TR_freight_component."
							</tr><tr>".$orderheader_TD.$orderheader_TD_distributor.$orderheader_TD_vertical."
							<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($order_date))."</span>&nbsp;</td>
							<td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$transaction_type_details."</span>&nbsp;</td>".$orderheader_TD_destination.$orderheader_TD_ordertype.$orderheader_TD_freight_component."
							</tr></table>";
							
							array_push($orderheader_array_mailbody,$orderemailbody);
							if($nick_name=='KARMA')
							{
								$sqldns_branch_code="SELECT dns_branch_code,costcenter FROM branch_master WHERE branch_code='".$branch_code."'";
								$rsdns_branch_code=mysqli_query($link,$sqldns_branch_code);
								$rowdns_branch_code=mysqli_fetch_assoc($rsdns_branch_code);
								${dns_branch_code.$order_trans_id}=$rowdns_branch_code['dns_branch_code'];
								${cost_center.$order_trans_id}=$rowdns_branch_code['costcenter'];
								${branch_code_GST.$order_trans_id}=$branch_code;
								$guid=getGUID();
								
								//$SO_order_date=date('Y-m-dTH:i:sZ',strtotime($order_date));
								$SO_date=gmdate('d',strtotime($order_date));
								$SO_month=gmdate('m',strtotime($order_date));
								$SO_year=gmdate('Y',strtotime($order_date));
					
								$SO_hour=gmdate('H',strtotime($order_date));
								$SO_minute=gmdate('i',strtotime($order_date));
								$SO_second=gmdate('s',strtotime($order_date));
					
								$SO_order_date=$SO_year.'-'.$SO_month.'-'.$SO_date.'T'.$SO_hour.':'.$SO_minute.':'.$SO_second.'Z';

								${xml_order_header.$order_trans_id}='<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" 	xmlns:ent="http://www.ramco.com/iris/EnterpriseServices/"><soap:Header/><soap:Body><ent:sOCreate_Authorize_OP><ent:contextparam><ent:CustomerCode>'.$dns_customer_code.'</ent:CustomerCode><ent:Ouinstance>'.${dns_branch_code.$order_trans_id}.'</ent:Ouinstance><ent:Sessionid>'.$guid.'</ent:Sessionid><ent:Transactionid>'.$guid.'</ent:Transactionid><ent:User>'.$dns_emp_code.'</ent:User></ent:contextparam><ent:dms_socreateauthorize_inout><ent:Add_co1></ent:Add_co1><ent:Advancetype></ent:Advancetype><ent:Bank_cashcode></ent:Bank_cashcode><ent:Card_number></ent:Card_number><ent:Card_type></ent:Card_type><ent:Cardholdername></ent:Cardholdername><ent:Countrycode></ent:Countrycode><ent:Currencycode>INR</ent:Currencycode><ent:Customer_bankname></ent:Customer_bankname><ent:Excha_rate></ent:Excha_rate><ent:Expirymonthyear></ent:Expirymonthyear><ent:Fax></ent:Fax><ent:Mobile></ent:Mobile><ent:Organizationid></ent:Organizationid><ent:Phone></ent:Phone><ent:Receipt_amount>0.00</ent:Receipt_amount><ent:Receiptdate1>1900-01-01T00:00:00Z</ent:Receiptdate1><ent:Saleorder_no>'.$order_trans_id.'</ent:Saleorder_no><ent:So_address1></ent:So_address1><ent:So_countryname></ent:So_countryname><ent:So_createddate>1900-01-01T00:00:00Z</ent:So_createddate><ent:So_customercode>'.$dns_customer_code.'</ent:So_customercode><ent:So_customername></ent:So_customername><ent:So_modifieddate>1900-01-01T00:00:00Z</ent:So_modifieddate><ent:So_orderdate>'.$SO_order_date.'</ent:So_orderdate> <ent:So_receiptdate>1900-01-01T00:00:00Z</ent:So_receiptdate><ent:So_receiptmode></ent:So_receiptmode><ent:So_receiptno>NO</ent:So_receiptno><ent:So_receiptroute></ent:So_receiptroute><ent:So_salespromotionno></ent:So_salespromotionno><ent:So_userid>'.$dns_emp_code.'</ent:So_userid><ent:Soc_payterm></ent:Soc_payterm><ent:Soemail></ent:Soemail><ent:Transactionnumber></ent:Transactionnumber><ent:Zip_code></ent:Zip_code></ent:dms_socreateauthorize_inout>';
							${order_GST_type.$order_trans_id}=$orderdataheader_GST_type;
							${So_lineno.$order_trans_id}=1;
							${So_orderdate.$order_trans_id}=$SO_order_date;
							}
							$orderinstructionemailbody="Delivery Instruction: <b>".strtoupper($orderdataheader_d_instruction)."</b>";
							/*if($orderdataheader_sale_type=='CASH'){
								//$ordercashreceivedemailbody="<br /><table>Received Cash: <b>Rs.".number_format($paymentdataheader_amount,2)."/-</b></table><br />";
								//array_push($ordercashreceived_array_mailbody,$ordercashreceivedemailbody);
							}*/
							array_push($orderdataheader_sale_type_array,$orderdataheader_sale_type);
							array_push($orderdataheader_TD_array,$orderdataheader_TD);
							array_push($orderinstruction_array_mailbody,$orderinstructionemailbody);
							array_push($orderemp_array_mailbody,$emp_name);
							array_push($orderdate_array_mailbody,$order_date);
							array_push($ordertansid_array_mailbody,$order_trans_id);
							array_push($orderdataheader_transaction_type_array,$orderdataheader_transaction_type);
							array_push($orderdataheader_entity_name_array,$entity_name);
							if($nick_name=='DNV' || $nick_name=='HALDIRAM')
							{
								${cust_type.$orderdataheader_order_no}=$cust_type;
							}
							array_push($orderdataheader_customerphone_array,$customer_phone_no);
					}
		 }//End of else
	}// End for loop
	//For Insert into the Order Details table for new trans id
	//print_r($order_details_array);
	//exit();
		if(count($order_details_array)>0)
		{
			for($i=0;$i<count($order_details_array);$i++){
				$orderdatadetails_order_no=$order_details_array[$i]->orderdatadetails_order_no;
				$orderdatadetails_sku_code=$order_details_array[$i]->orderdatadetails_sku_code;
				$orderdatadetails_qty=$order_details_array[$i]->orderdatadetails_qty;
				$orderdatadetails_TD=$order_details_array[$i]->orderdatadetails_TD;
				$orderdatadetails_premium=$order_details_array[$i]->orderdatadetails_premium;
				$orderdatadetails_VAT=$order_details_array[$i]->orderdatadetails_VAT;
				$orderdatadetails_grn_no=$order_details_array[$i]->orderdatadetails_grn_no;
				$orderdatadetails_mrp_code=$order_details_array[$i]->orderdatadetails_mrp_code;
				$orderdatadetails_amount=$order_details_array[$i]->orderdatadetails_amount;
				$orderdatadetails_UOM=$order_details_array[$i]->orderdatadetails_UOM;
				$orderdatadetails_sale_rate=$order_details_array[$i]->orderdatadetails_sale_rate;

				$sqlrdscode="SELECT rds_code FROM rds_master WHERE emp_code='".substr($orderdatadetails_order_no,1,5)."'";
				$rsrdscode=mysqli_query($link,$sqlrdscode);
				$rowrdscode=mysqli_fetch_assoc($rsrdscode);
				$rds_code_stock_update=$rowrdscode['rds_code'];
				
				$sqlmrpdetails="SELECT mrp,sale_rate,UOM,ws_rate,distributor_rate,ss_rate FROM mrp WHERE product_code='".$orderdatadetails_sku_code."' 
								AND mrp_code='".$orderdatadetails_mrp_code."'";
				$rsmrpdetails=mysqli_query($link,$sqlmrpdetails);
				$recmrpdetails=mysqli_fetch_assoc($rsmrpdetails);
				if(stock_audit_rate=='no')
				{
					$mrp=$recmrpdetails['mrp'];
					$sale_rate_db=$recmrpdetails['sale_rate'];
				}
				else
				{
					$mrp=$orderdatadetails_sale_rate;
					$sale_rate_db=$orderdatadetails_sale_rate;
				}
				$UOM=$recmrpdetails['UOM'];
				
				if($nick_name=='DNV' || $nick_name=='HALDIRAM')
				{
					if(${cust_type.$orderdatadetails_order_no}=='R')  $orderdatadetails_sale_rate=$orderdatadetails_sale_rate;
					if(${cust_type.$orderdatadetails_order_no}=='D')  $orderdatadetails_sale_rate=$recmrpdetails['distributor_rate'];
					if(${cust_type.$orderdatadetails_order_no}=='SS') $orderdatadetails_sale_rate=$recmrpdetails['ss_rate'];
					if(${cust_type.$orderdatadetails_order_no}=='WS') $orderdatadetails_sale_rate=$recmrpdetails['ws_rate'];
				}				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_desc,UOM1,UOM2,conversion_factor,dns_prod_code FROM product_master WHERE prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc,PM.conversion_factor,PM.UOM1,PM.UOM2,PM.dns_prod_code 
											FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc,PM.dns_prod_code FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc,PM.dns_prod_code
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$orderdatadetails_sku_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
					$UOM1=$rowproductdetails['UOM1'];
					$UOM2=$rowproductdetails['UOM2'];
					$conversion_factor=$rowproductdetails['conversion_factor'];
					$dns_prod_code=$rowproductdetails['dns_prod_code'];
					
					if(sale=='yes' && delete_transaction=='yes')
					{
						$sqlinsert_condition=" transaction_type ='".${orderdataheader_transaction_type.$orderdatadetails_order_no}."',";
					}
					else
					{
						$sqlinsert_condition="";
					}
					
					//print_r($order_array_trans_id);
					if(!in_array($orderdatadetails_order_no,$order_array_trans_id))
					{
						echo $sqlinsertorderdetails="INSERT INTO order_details SET order_no='".$orderdatadetails_order_no."',
												sku_code 	='".$orderdatadetails_sku_code."',
												qty			='".$orderdatadetails_qty."',
												TD			='".$orderdatadetails_TD."',
												premium		='".$orderdatadetails_premium."',
												sale_rate	='".$orderdatadetails_sale_rate."',
												VAT			='".$orderdatadetails_VAT."',
												UOM			='".$orderdatadetails_UOM."',
												amount		='".$orderdatadetails_amount."', ".$sqlinsert_condition."
												mrp_code	='".$orderdatadetails_mrp_code."'";
						if(mysqli_query($link,$sqlinsertorderdetails))
						{
							$flag=5;
							// For stock transfer and stock rceive push the value in goods_in_transit table
							if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='ST' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SA')
							{
								if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='ST')
								{
									$grn_no_goods_in_transit=$orderdatadetails_order_no;
									$order_no_goods_in_transit='';
									$despatch_qty=$orderdatadetails_qty;
									$rec_qty=0;
									$orderdatadetails_sale_rate=$orderdatadetails_sale_rate;
									$despatcher_code=$order_emp_code;
									$receiver_code=${stock_receive_emp_code.$orderdatadetails_order_no};
								}
								
								if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT' || ${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SA')
								{
									$grn_no_goods_in_transit=${orderdataheader_grn_no.$orderdatadetails_order_no};
									$order_no_goods_in_transit=$orderdatadetails_order_no;
									
									//For selection of despatch qty for the grn_no
									$sqlquerygit="SELECT despatch_qty,sale_rate,despatcher_code,receiver_code FROM goods_in_transit WHERE 
												grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."' AND 
												transaction_type='ST'";
									$rsquerygit=mysqli_query($link,$sqlquerygit);
									$rowquerygit=mysqli_fetch_assoc($rsquerygit);
									$despatch_qty=$rowquerygit['despatch_qty'];
									$sale_rate=$rowquerygit['sale_rate'];
									$rec_qty=$orderdatadetails_qty;
									$orderdatadetails_sale_rate=$sale_rate;
									$despatcher_code=$rowquerygit['despatcher_code'];
									$receiver_code=$rowquerygit['receiver_code'];
								}
								//Insertion of goods_in_transit table
								$sqlinsertgoodsintransit="INSERT INTO goods_in_transit SET grn_no='".$grn_no_goods_in_transit."',
														despatcher_code ='".$despatcher_code."',
														receiver_code	='".$receiver_code."',
														prod_code		='".$orderdatadetails_sku_code."',
														despatch_qty	='".$despatch_qty."',
														rec_qty			='".$rec_qty."',
														sale_rate		='".$orderdatadetails_sale_rate."',
														status			='0',
														transaction_type ='".${orderdataheader_transaction_type.$orderdatadetails_order_no}."',
														download_time=CURRENT_TIMESTAMP(),
														order_no='".$order_no_goods_in_transit."'";
								if(mysqli_query($link,$sqlinsertgoodsintransit))
								{
									$flag=5;
									//Updating status to complete of all the transactions of the particular grn_no if despatch qty=Sum of all the receive qty 	
									$sqlqueryrecgit="SELECT SUM(rec_qty) AS total_rec_qty FROM goods_in_transit WHERE 
												grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."' AND 
												transaction_type='BT'";
									$rsqueryrecgit=mysqli_query($link,$sqlqueryrecgit);
									$rowqueryrecgit=mysqli_fetch_assoc($rsqueryrecgit);
									$total_rec_qty=$rowqueryrecgit['total_rec_qty'];
									if($despatch_qty==$total_rec_qty)
									{
										$sqlupdategitstatus="UPDATE goods_in_transit SET status='1' 
										WHERE grn_no='".$grn_no_goods_in_transit."' AND prod_code='".$orderdatadetails_sku_code."'";
										if(mysqli_query($link,$sqlupdategitstatus))
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
									//End of updating status
								}
								else
								{
									mysqli_query($link,"ROLLBACK");
									echo $flag=0;
									return;
								}
							}
							//Start for STAR mis data details order qty updation
							if($nick_name=='STAR')
							{
								$qty=$orderdatadetails_qty;
								$trans_type='O';
								$trans_sub_type='';
								update_transaction_STAR(substr($orderdatadetails_order_no,1,5),$orderdatadetails_order_no,$qty,$trans_type,$trans_sub_type);
							}
							if($nick_name=='RUPA')
							{
								$qty=$orderdatadetails_qty;
								$order_UOM=$orderdatadetails_UOM;
							 update_ach_RUPA(substr($orderdatadetails_order_no,1,5),$orderdatadetails_order_no,$order_UOM,$qty,$UOM1,$UOM2,$conversion_factor);
							}
					  	   //End for STAR mis data details order qty updation
							//End of goods_in_transit operation	
							//Maintaining the stock for each transaction
							/*if((${orderdataheader_transaction_type.$orderdatadetails_order_no}=='PB') || (${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT'))
							{
								$condition_stock_update=" closing_stk=(closing_stk+$orderdatadetails_qty)";
								$sql_update_branch_rds_product_stk="UPDATE branch_rds_product_wise_stock SET ".$condition_stock_update." 
																WHERE  rds_code='".$rds_code_stock_update."' AND product_code='".$orderdatadetails_sku_code."'";
								if(mysqli_query($link,$sql_update_branch_rds_product_stk))
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
							else if((${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SB') || (${orderdataheader_transaction_type.$orderdatadetails_order_no}=='ST') || (${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SA') || (${orderdataheader_transaction_type.$orderdatadetails_order_no}=='SH')) 
							{
								$condition_stock_update=" closing_stk=(closing_stk-$orderdatadetails_qty)";
								$sql_update_branch_rds_product_stk="UPDATE branch_rds_product_wise_stock SET ".$condition_stock_update." 
																WHERE  rds_code='".$rds_code_stock_update."' AND product_code='".$orderdatadetails_sku_code."'";
								if(mysqli_query($link,$sql_update_branch_rds_product_stk))
								{
									$flag=5;
								}
								else
								{
									mysqli_query($link,"ROLLBACK");
									echo $flag=0;
									return;
								} 
							}*/
						}
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
						//For constructing the email body for ORDER DETAILS if ORDER has performed
						
						if(no_of_filter==1){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==2){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==3){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>

											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(no_of_filter==4){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$product_brand_name."</span>&nbsp;</td>
											<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
						}
						if(providing_code=='yes'){
							$product_details_TD.="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$dns_prod_code."</span>&nbsp;</td>";	
						}
						if(premium=='yes' )
						{
							${premiun.$orderdatadetails_sku_code}=$orderdatadetails_premium*$orderdatadetails_qty;
						}
						else
						{
							${premiun.$orderdatadetails_sku_code}=0;
						}
						if(mrp=='yes' && TD=='yes' && TD_type=='sku wise'){
							if(TD_calc=='percentage')
							{
								$totalmrp=($orderdatadetails_qty*$mrp)-((($orderdatadetails_qty*$mrp)*$orderdatadetails_TD)/100);
							}
							else
							{
								//$totalmrp=($orderdatadetails_qty*$mrp)-$orderdatadetails_TD;
								$totalmrp=$orderdatadetails_qty*($mrp-$orderdatadetails_TD);
							}
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(mrp=='yes' && TD=='no'){
							$totalmrp=($orderdatadetails_qty*$mrp);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='yes' && TD_type=='sku wise'){
							 if(TD_calc=='percentage')
							 {
							 	$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-((($orderdatadetails_qty*$orderdatadetails_sale_rate)*$orderdatadetails_TD)/100);
							 }
							 else
							 {
								//$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate)-$orderdatadetails_TD;
								$totalrate=$orderdatadetails_qty*($orderdatadetails_sale_rate-$orderdatadetails_TD);
							 }
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='no'){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }
						 if(mrp=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							//if($nick_name=='SKIPPER')
							if(multiple_UOM=='yes')
							{
								$totalmrp=$orderdatadetails_amount;
							}
							else
							{
								$totalmrp=($orderdatadetails_qty*$mrp);
							}
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalmrp+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalmrp+${premiun.$orderdatadetails_sku_code};
						 }
						 if(sale_rate=='yes' && TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
							$totalrate=($orderdatadetails_qty*$orderdatadetails_sale_rate);
							${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$totalrate+${premiun.$orderdatadetails_sku_code};
							${totalamount.$orderdatadetails_sku_code}=$totalrate+${premiun.$orderdatadetails_sku_code};
						 }
						
						 if(VAT=='yes' && VAT_details=='amount')
						 {
							 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$orderdatadetails_VAT;
						 }
						 else if(VAT=='yes' && VAT_details=='percentage')
						 {
							 $vat_total=(${grandtotal.$orderdatadetails_order_no}*$orderdatadetails_VAT/100);
							 ${grandtotal.$orderdatadetails_order_no}=${grandtotal.$orderdatadetails_order_no}+$vat_total;
						 }
						
						 if(TD=='yes' && TD_type=='sku wise')
						 {
							$orderemailbody_TD_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_TD,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $orderemailbody_TD_VAL="";
						 }
						 if(premium=='yes' )
						 {
							 $orderemailbody_premium_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_premium,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							$orderemailbody_premium_VAL=="";
						 }
						 if(VAT=='yes')
						 {
							$orderemailbody_VAT_VAL="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".number_format(round($orderdatadetails_VAT,2),2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							 $orderemailbody_VAT_VAL="";
						 }
						 
						 if($orderdatadetails_sale_rate=='')
						 {
							$orderdatadetails_sale_rate=0; 
						 }
						 if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT')
						 {
							$orderdatadetails_despatch_qty="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($despatch_qty,2)."</span>&nbsp;</td>";
						 }
						 else
						 {
							$orderdatadetails_despatch_qty='';
						 }
						 if(amount=='yes')
						 {
							 if($orderdatadetails_amount >0)
							 {
							 	$amount_mail=number_format(round($orderdatadetails_amount,2),2);
							 }
							 else
							 {
								 $amount_mail=number_format(round(${totalamount.$orderdatadetails_sku_code},2),2);
							 }
							 if($orderdatadetails_sale_rate >0)
							 {
								 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
							 }
							 else
							 {
								 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,4),2);
							 }
						 }
						 else
						 {
							 $amount_mail=number_format(${totalamount.$orderdatadetails_sku_code},2);
							 $sale_rate_mail=number_format(round($orderdatadetails_sale_rate,2),2);
						 }
						 if(strpos($orderdatadetails_qty,'.')!=false){
							 $orderdatadetails_qty_mail=$orderdatadetails_qty;
						 }
						 else
						 {
							 $orderdatadetails_qty_mail=number_format($orderdatadetails_qty,2);
						 }
						 
						//For total value
						 ${orderdatadetails_VAT_total.$orderdatadetails_order_no}=${orderdatadetails_VAT_total.$orderdatadetails_order_no}+$orderdatadetails_VAT;
						 ${orderdatadetails_qty_total.$orderdatadetails_order_no}=${orderdatadetails_qty_total.$orderdatadetails_order_no}+$orderdatadetails_qty;
						 if(${orderdataheader_transaction_type.$orderdatadetails_order_no}=='BT')
						 {
							 ${orderdatadetails_despatchqty_total.$orderdatadetails_order_no}=${orderdatadetails_despatchqty_total.$orderdatadetails_order_no}+$despatch_qty;
						 }
						/*if($orderdatadetails_amount >0)
						 {
							${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+${totalamount.$orderdatadetails_sku_code};
						 }
						 else
						 {*/
							${orderdatadetails_amount_total.$orderdatadetails_order_no}=${orderdatadetails_amount_total.$orderdatadetails_order_no}+${totalamount.$orderdatadetails_sku_code};
						 //}
							 if(mrp=='yes')
							 {
								//if($nick_name=='SKIPPER')
								if(multiple_UOM=='yes')
								{
									$mrp_UOM_wise=$orderdatadetails_amount/$orderdatadetails_qty;
									$amount_mail=number_format($orderdatadetails_amount,2);
									$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp_UOM_wise,2)."</span>&nbsp;</td>";
								}
								else
								{
								$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($mrp,2)."</span>&nbsp;</td>";
								}
							 }
							 else if(sale_rate=='yes' && sale_rate_input_dropdown=='input')
							 {
								 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$sale_rate_mail."</span>&nbsp;</td>";
							 }
							 else if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
							 {
								/*if($nick_name=='DNV' || $nick_name=='HALDIRAM')
								{
									$mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$orderdatadetails_sale_rate."</span>&nbsp;</td>";
								}
								else
								{*/
								 $mrp_sale_rate_TD="<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_sale_rate,2)."</span>&nbsp;</td>";
								//}
							 }
						 if(multiple_UOM=='yes')
							{
								$mail_UOM=$orderdatadetails_UOM;
							}
							else $mail_UOM=$UOM1;
						${a.$orderdatadetails_order_no} .="
								<tr>".$product_details_TD.$orderdatadetails_despatch_qty."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($orderdatadetails_qty_mail,2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($mail_UOM)."</span>&nbsp;</td>
								".$mrp_sale_rate_TD.$orderemailbody_TD_VAL.$orderemailbody_premium_VAL."
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$amount_mail."</span>&nbsp;</td>".$orderemailbody_VAT_VAL."
								</tr>";
						if($nick_name=='KARMA')
						{
							$sqlfetchGST="SELECT SGST,CGST,discount_code,tax_code,IGST FROM prodqty_custclass_wise_TD WHERE 
											branch_code='".${branch_code_GST.$orderdatadetails_order_no}."' AND prod_code='".$orderdatadetails_sku_code."'";
							$rsfetchGST=mysqli_query($link,$sqlfetchGST);
							$rowfetchGST=mysqli_fetch_assoc($rsfetchGST);
							$SGST=$rowfetchGST['SGST'];
							$CGST=$rowfetchGST['CGST'];
							$IGST=$rowfetchGST['IGST'];
							
							$rate_discount=($orderdatadetails_sale_rate-(($orderdatadetails_sale_rate*$orderdatadetails_TD)/100));
							${xml_order_details.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_ML_input><ent:So_costcenter>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_costcenter><ent:So_deliverydate>2016-06-02T00:00:00Z</ent:So_deliverydate><ent:So_itemcode>'.$dns_prod_code.'</ent:So_itemcode><ent:So_itemdesc></ent:So_itemdesc><ent:So_lineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_lineno><ent:So_quantity>'.$orderdatadetails_qty.'</ent:So_quantity><ent:So_rate>'.$rate_discount.'</ent:So_rate><ent:So_requireddate>'.${So_orderdate.$orderdatadetails_order_no}.'</ent:So_requireddate><ent:So_shippingat></ent:So_shippingat><ent:So_warehouse></ent:So_warehouse><ent:Soca_uom></ent:Soca_uom></ent:DMS_SOCreateAuthorize_ML_input>';
							if(${order_GST_type.$orderdatadetails_order_no}=='CGST/SGST')
							{
								$GST_val_CGST=$CGST;
								$GST_val_SGST=$SGST;
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_SGST.'</ent:So_tcdcode> <ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_CGST.'</ent:So_tcdcode> <ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							}
							if(${order_GST_type.$orderdatadetails_order_no}=='IGST')
							{
							 $GST_val_IGST=$IGST;
							${xml_order_details_TD.$orderdatadetails_order_no}.='<ent:DMS_SOCreateAuthorize_TCDML_input><ent:So_itemlineno>'.${So_lineno.$orderdatadetails_order_no}.'</ent:So_itemlineno><ent:So_tcdcharge>0.00</ent:So_tcdcharge><ent:So_tcdcode>'.$GST_val_IGST.'</ent:So_tcdcode><ent:So_tcdcostcentre>'.${cost_center.$orderdatadetails_order_no}.'</ent:So_tcdcostcentre><ent:So_tcdtype>Line</ent:So_tcdtype><ent:So_tcdvalue>'.$orderdatadetails_TD.'</ent:So_tcdvalue></ent:DMS_SOCreateAuthorize_TCDML_input>';
							}
							${So_lineno.$orderdatadetails_order_no}=${So_lineno.$orderdatadetails_order_no}+1;
						}
						//For van sales stock updation
						if(van_sales=='yes')
						{
							$sqlupdatevanstock="UPDATE van_stock_allocation SET balance_qty=(balance_qty-$orderdatadetails_qty) WHERE 	
										emp_code='".$order_emp_code."' AND prod_code='".$orderdatadetails_sku_code."' AND allocation_date='".$current_date."'";
						    if(mysqli_query($link,$sqlupdatevanstock))
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
						//End For van sales stock updation
					}
					else if(in_array($orderdatadetails_order_no,$order_array_trans_id) && $nick_name=='DNV')
					{
						$sqlchkorderdetails="SELECT order_no from order_details WHERE order_no='".$orderdatadetails_order_no."' AND 
											sku_code 	='".$orderdatadetails_sku_code."'";
						$rschkorderdetails=mysqli_query($link,$sqlchkorderdetails);
						$cntchkorderdetails=mysqli_num_rows($rschkorderdetails);
						if($cntchkorderdetails ==0)
						{
							$sqlinsertorderdetails="INSERT INTO order_details SET order_no='".$orderdatadetails_order_no."',
													sku_code 	='".$orderdatadetails_sku_code."',
													qty			='".$orderdatadetails_qty."',
													TD			='".$orderdatadetails_TD."',
													premium		='".$orderdatadetails_premium."',
													sale_rate	='".$orderdatadetails_sale_rate."',
													VAT			='".$orderdatadetails_VAT."',
													amount		='".$orderdatadetails_amount."', ".$sqlinsert_condition."
													mrp_code	='".$orderdatadetails_mrp_code."'";
							if(mysqli_query($link,$sqlinsertorderdetails))
							{
								$flag=5;
							}
						}
				    }
			}//End for loop
		}
		//End Insert into the Order Details table for new trans id
		//For sending email for order
		//echo $orderdatadetails_order_no; 
		if(count($orderheader_array_mailbody)>0)
		{
			for($countarr=0;$countarr<count($orderheader_array_mailbody);$countarr++)
			{
				if(TD=='yes' && TD_type=='order value wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD_array[$countarr],2)."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD on order value: <b>".number_format($orderdataheader_TD_array[$countarr],2)."</b></table>";
					}
				}
				if(TD=='yes' && TD_type=='customer wise')
				{
					if(TD_calc=='percentage')
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD_array[$countarr]."%</b></table>";
					}
					else
					{
						$TD_order_val="<br /><table>TD applicable for this customer: <b>".$orderdataheader_TD_array[$countarr]."</b></table>";
					}
				}
				if(TD=='yes' && (TD_type=='order value wise' || TD_type=='customer wise')){
					
					if(TD_calc=='percentage')
					{
						$total_order_amount=(${grandtotal.$ordertansid_array_mailbody[$countarr]}-(${grandtotal.$ordertansid_array_mailbody[$countarr]}*$orderdataheader_TD_array[$countarr])/100);
					}
					else
					{
						$total_order_amount=(${grandtotal.$ordertansid_array_mailbody[$countarr]}- $orderdataheader_TD_array[$countarr]);
					}
				}
				else
				{
					$total_order_amount=${grandtotal.$ordertansid_array_mailbody[$countarr]};
				}
				if($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD'){
					if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="$nick_name - Order received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					}
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
					$orderemailsubj="Sale bill generated by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';

					}
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_sale_type_array[$countarr]=='CASH')
				{
					if($orderdataheader_sale_type_array[$countarr]=='CASH'  && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$orderemailsubj="$nick_name - Cash sale by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					$ordercashreceivedemailbody="<br /><table>Received Cash: <b>Rs.".number_format($total_order_amount,2)."/-</b></table><br />";
					}
					if($orderdataheader_sale_type_array[$countarr]=='CASH'  && $orderdataheader_transaction_type_array[$countarr]=='SB'){
					$orderemailsubj="Sale bill generated by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
					$ordercashreceivedemailbody="";
					}
				}
				//For sales feature
				else if($orderdataheader_transaction_type_array[$countarr]=='BT' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock received value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";

				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='PB' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Purchase bill received by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Purchase value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='ST' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock transfer to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock  transfer value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='CN' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock handed over to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Carry in stock out value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';

				}
				else if($orderdataheader_transaction_type_array[$countarr]=='SR' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock return to ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Stock return value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='CR' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Stock return from ".$orderdataheader_entity_name_array[$countarr]." by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Carry in return value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if(($orderdataheader_transaction_type_array[$countarr]=='SA' || $orderdataheader_transaction_type_array[$countarr]=='SH') && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="Material Shortage booked by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Shortage value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='RP' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="$nick_name - Replacement request receipt from ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>Replacement value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='ISP' && $orderdataheader_sale_type_array[$countarr]==''){
				$orderemailsubj="$nick_name - ISP made by ".$orderemp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($orderdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($orderdate_array_mailbody[$countarr])).' hrs.';
				$total_order_val="<br /><table>ISP value: <b>Rs. ".number_format($total_order_amount,2)."/-</b></table>";
				$ordercashreceivedemailbody='';
				}
				//End for sales feature
				if($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD' || $orderdataheader_transaction_type_array[$countarr]=='SB'){
					if($orderdataheader_sale_type_array[$countarr]=='COD')
					{
						$COD_val='(CASH ON DELIVERY)';
					}
					else
					{
						$COD_val='';
					}
					if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD') && $orderdataheader_transaction_type_array[$countarr]!='SB'){
					$total_order_val="<br /><table>Total order value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if(($orderdataheader_sale_type_array[$countarr]=='CREDIT' || $orderdataheader_sale_type_array[$countarr]=='COD' || $orderdataheader_sale_type_array[$countarr]=='CASH' ) && $orderdataheader_transaction_type_array[$countarr]=='SB')
					{
						$total_order_val="<br /><table>Total sale value: <b>Rs. ".number_format($total_order_amount,2)."/- ".$COD_val."</b></table>";
					}
					else if($orderdataheader_sale_type_array[$countarr]=='CASH' && $orderdataheader_transaction_type_array[$countarr]!='SB')
					{
						$total_order_val='';
					}
				}
				if(no_of_filter==1){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
								
				}
				if(no_of_filter==2){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==3){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(no_of_filter==4){
					$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col3."</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
				}
				if(providing_code=='yes')
				{
					$product_details_TH.="<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>SKU CODE</span></strong></th>";
					$product_details_colspan=no_of_filter+1;
				}
				else
				{
					$product_details_colspan=no_of_filter;
				}			

				if(TD=='yes' && TD_type=='sku wise')
				{
					$orderemailbody_TD_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>TD</span></strong></th>";
					$orderemailbody_TD_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";		
				}
				else 
				{
					$orderemailbody_TD_TH="";
					$orderemailbody_TD_total="";
				}
				if(premium=='yes')
				{
					$order_premiun_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Premium</span></strong></th>";
					$order_premium_total="<td style='width:60px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>";			
				}
				else
				{
					$order_premiun_TR='';
					$order_premium_total='';
				}

				if(VAT=='yes')
				{
					$orderemailbody_VAT_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>VAT</span></strong></th>";
					$orderemailbody_VAT_total="<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_VAT_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></td>";		
				}
				else 
				{
					$orderemailbody_VAT_TH="";
					$orderemailbody_VAT_total="";
				}
				if($orderdataheader_transaction_type_array[$countarr]=='PB' && (sale_rate=='yes' || amount=='yes'))
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Purchase Rate</span></strong></th>";
				}
				else if($orderdataheader_transaction_type_array[$countarr]!='PB' && (sale_rate=='yes' || amount=='yes'))
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Sale Rate</span></strong></th>";
				}
				else if(mrp=='yes')
				{
					$mrp_sale_rate_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>MRP</span></strong></th>";
				}
				else
				{
					$mrp_sale_rate_TR='';
				}
				if($orderdataheader_transaction_type_array[$countarr]=='BT')
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:left'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Despatch Qty</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:left'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Received Qty</span></strong></th>
							";
					$despatch_qty_total_TD="<td style='width:50px;min-height:21px;text-align:right'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>".number_format(${orderdatadetails_despatchqty_total.$ordertansid_array_mailbody[$countarr]},2)."</span></strong></td>";		
				}
				else if($orderdataheader_transaction_type_array[$countarr]=='ST')
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Despatch Qty</span></strong></th>";
					$despatch_qty_total_TD='';		
				}
				else
				{
					$qty_TR="<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>";
					$despatch_qty_total_TD='';		
				}
				 if(strpos(${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]},'.')!=false){
					 ${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}=${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]};
				 }
				 else
				 {
					 ${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}=number_format(${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]},2);
				 }
				 if(strtoupper($nick_name)=='AUTOCRAFT' || strtoupper($nick_name)=='DAA')
				 {
					 if(strtoupper($nick_name)=='AUTOCRAFT')
					 {
						$smsstringval='Thank you for your valued Order. Please contact us for further details.';
					    $url="http://smslive.in/push/default.aspx?user=ACRAFT&pws=acraft123&Receipent=".$orderdataheader_customerphone_array[$countarr]."&sms=".urlencode($smsstringval)."";
					 }
					 if(strtoupper($nick_name)=='DAA')
					 {
						$smsstringval='Thank you for your valued Order. Please contact us for further details.'; 
					    $url="http://smslive.in/push/default.aspx?user=DURGAA&pws=durgaa123&Receipent=".$orderdataheader_customerphone_array[$countarr]."&sms=".urlencode($smsstringval)."";
					 }
					//echo $url;
					if($orderdataheader_customerphone_array[$countarr]!=''){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_TIMEOUT, 20);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_exec($ch);
					}
				 }
				 //KARMA soap calling
				 if($nick_name=='KARMA')
					{
						/*$Url = "http://fmpower.acedns.in/RAMCO-soap-calling.php"; // URL for fmpoweracedns server
						
						$xml_post_string = ${xml_order_header.$order_trans_id}.'<ent:dms_socreateauthorize_ml>'.${xml_order_details.$orderdatadetails_order_no}.'</ent:dms_socreateauthorize_ml><ent:dms_socreateauthorize_tcdml>'.${xml_order_details_TD.$order_trans_id}.'</ent:dms_socreateauthorize_tcdml></ent:sOCreate_Authorize_OP></soap:Body></soap:Envelope>';   // 
						
						  $ch = curl_init();
						  curl_setopt( $ch, CURLOPT_URL, $Url );
						  curl_setopt( $ch, CURLOPT_POST, true );
						  curl_setopt($ch, CURLOPT_TIMEOUT, 400);
						  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
						  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
						  curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml_post_string );
						  $result = curl_exec($ch);
						  curl_close($ch);
						  if($result =='200') 
						  {
							  $flag=5;
						  }
						  else
						  {
							  mysqli_query($link,"ROLLBACK");
								echo $flag=0;
								return;
						  }*/
						    $xml_post_string = ${xml_order_header.$ordertansid_array_mailbody[$countarr]}.'<ent:dms_socreateauthorize_ml>'.${xml_order_details.$ordertansid_array_mailbody[$countarr]}.'</ent:dms_socreateauthorize_ml><ent:dms_socreateauthorize_tcdml>'.${xml_order_details_TD.$ordertansid_array_mailbody[$countarr]}.'</ent:dms_socreateauthorize_tcdml></ent:sOCreate_Authorize_OP></soap:Body></soap:Envelope>'; 
						  $sqlinsertsoapresponse="INSERT INTO soap_request_response_details SET 
						  							request_xml='".$xml_post_string."',
													emp_code='".$emp_code."',
													order_no='".$ordertansid_array_mailbody[$countarr]."',
													download_time=CURRENT_TIMESTAMP()";
							if(mysqli_query($link,$sqlinsertsoapresponse))
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

				 //Order approval process checking
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				//$order_email=ORDEREMAILRECIPENTS;			

				 if(order_approval_process=='no')
				 {
					$orderemailbody = $orderheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
								".$mrp_sale_rate_TR.$orderemailbody_TD_TH.$order_premiun_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$orderemailbody_VAT_TH." 	
								</tr>
					".${a.$ordertansid_array_mailbody[$countarr]}."<tr>
								<td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>".$despatch_qty_total_TD."
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$orderemailbody_TD_total.$order_premium_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_amount_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></th>".$orderemailbody_VAT_total."
								</tr></table>
					".$TD_order_val.$ordercashreceivedemailbody.$total_order_val."
					<br /><br /><table>".$orderinstruction_array_mailbody[$countarr]."</table><br /><br />".$order_approval_mail_body."
					<br /><br /><br />Powered By aceDNS<br /></body></html>";
					if(mail($order_email, $orderemailsubj, $orderemailbody, $headers,$spam_filter))
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
				 else
				 {
					 $order_approval_emailsARR=ORDERAPPROVALEMAIL;
					 $order_emailARR=explode(',',$order_email);
					 foreach($order_emailARR as $order_email_values)
					 {
						 if(strstr($order_approval_emailsARR,$order_email_values)!=FALSE)
						 {
							${orderapprovalemailbody.$order_email_values}="<form name='orderapproval' action=".APICALLLOGURL."/update-order-approval.php?nick_name=".$nick_name."' method='POST'>
						<table wiidth='100%' border=1 style=background-color:AliceBlue>
						<tr>
						<th style='width:350px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
						CE'>Order Approval</span></strong></th>
						</tr>
						<tr>
							<td style='width:350px;text-align:center;min-height:91px;background-color:white;padding-left:10 px;'>
								<input type='radio' name='approval' value='approved' /> Approved
								<input type='radio' name='approval' value='notapproved' /> Not Approved
								
								<input type='submit' name='Submit' value='save' />
								<input type='hidden' name='order_no' value='".$ordertansid_array_mailbody[$countarr]."' />
							</td>
						</tr>
						</table>
					</form>";
						 }
						 else
						 {
							 ${orderapprovalemailbody.$order_email_values}='';
						 }
						 
						 ${orderemailbody.$order_email_values} = $orderheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>".$product_details_TH.$qty_TR."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>UOM</span></strong></th>
								".$mrp_sale_rate_TR.$orderemailbody_TD_TH."
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>".$orderemailbody_VAT_TH." 	
								</tr>
					".${a.$ordertansid_array_mailbody[$countarr]}."<tr>
								<td style='min-height:21px;text-align:center' colspan='".$product_details_colspan."'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td>".$despatch_qty_total_TD."
								<td style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".${orderdatadetails_qty_total.$ordertansid_array_mailbody[$countarr]}."</span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>
								<td style='width:50px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'></span></strong></td>".$orderemailbody_TD_total."<th style='width:50px;min-height:21px;text-align:right'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>".number_format(round(${orderdatadetails_amount_total.$ordertansid_array_mailbody[$countarr]},2),2)."</span></strong></th>".$orderemailbody_VAT_total."
								</tr></table>
					".$TD_order_val.$ordercashreceivedemailbody.$total_order_val."
					<br /><br /><table>".$orderinstruction_array_mailbody[$countarr]."</table><br /><br />".${orderapprovalemailbody.$order_email_values}."
					<br /><br /><br />Powered By aceDNS<br /></body></html>";
						if(mail($order_email_values, $orderemailsubj,${orderemailbody.$order_email_values}, $headers,$spam_filter))
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
		}
}
 /* --------------------END QUERY FOR ORDER---------------------------------------------------------------------------------------------------------------*/
 /* --------------------START QUERY FOR PAYMENT-----------------------------------------------------------------------------------------------------------*/
$payment_array_trans_id=array();
$paymentheader_array_mailbody=array();
$paymentinstruction_array_mailbody=array();
$paymentdetails_array_mailbody=array();
$paymentemp_array_mailbody=array();
$paymentdate_array_mailbody=array();
$paymenttansid_array_mailbody=array();
$paymentdataheader_sale_type_array=array();
$paymentphoneno_array=array();
$paymentheader_array_amount=array();

if(count($payment_array)>0)
{
	for($x=0;$x<count($payment_array);$x++){
		$payment_emp_code=$payment_array[$x]->payment_emp_code;
		$payment_trans_id=$payment_array[$x]->payment_trans_id;
		$payment_latt=$payment_array[$x]->payment_latt;
		$payment_longi=$payment_array[$x]->payment_longi;
		$payment_date=$payment_array[$x]->payment_date;
		$paymentdataheader_receipt_id=$payment_array[$x]->paymentdataheader_receipt_id;
		$paymentdataheader_customer_code=$payment_array[$x]->paymentdataheader_customer_code;
		$paymentdataheader_sale_type=$payment_array[$x]->paymentdataheader_sale_type;
		$paymentdataheader_p_remark=$payment_array[$x]->paymentdataheader_p_remark;
		$paymentdataheader_amount=$payment_array[$x]->paymentdataheader_amount;
		$paymentdataheader_cash_cheque=$payment_array[$x]->paymentdataheader_cash_cheque;
		$paymentdataheader_cheque_no=$payment_array[$x]->paymentdataheader_cheque_no;
		$paymentdataheader_date=$payment_array[$x]->paymentdataheader_date;
		$paymentdataheader_bank=$payment_array[$x]->paymentdataheader_bank;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if(DCR_map=='no')
		{
			if($payment_latt>0 && $payment_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$payment_latt."',longi='".$payment_longi."' WHERE 
										emp_code='".$payment_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
		}

		//For checking that trans id exist or not for payment
		$sqlchkpaylocation="SELECT * FROM location WHERE trans_id='".$payment_trans_id."'";
		$reschkpaylocation = mysqli_query($link,$sqlchkpaylocation) or die(mysqli_error()." Error in check payment location: ".$sqlchkpaylocation); 
		$rowchkpaylocation = mysqli_fetch_assoc($reschkpaylocation);
		$countchkpaylocation=mysqli_num_rows($reschkpaylocation);
		
		//For update the location table for existing trans id for Payment
		if($countchkpaylocation>0)
		{
			//$payment_trans_id_chk=substr($payment_trans_id,1,19);
			if(!in_array($payment_trans_id,$payment_array_trans_id))
			{
				array_push($payment_array_trans_id,$payment_trans_id);
			}
			$sqlupdatepaylocation="UPDATE location SET emp_code='".$payment_emp_code."',
									latt='".$payment_latt."',
									longi='".$payment_longi."'
									WHERE trans_id='".$payment_trans_id."'";
			$rsupdatepaylocation=mysqli_query($link,$sqlupdatepaylocation) or die(mysqli_error()." Error in update payment location: ".$sqlupdatepaylocation);
			if($rsupdatepaylocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of payment
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding payment
			$sqlinsertpaylocation="INSERT INTO location SET emp_code='".$payment_emp_code."',
									trans_id='".$payment_trans_id."',
									latt='".$payment_latt."',
									longi='".$payment_longi."',
									date='".$payment_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the Payment Header table for new trans id
			$sqlinsertpaymentheader="INSERT INTO payment_header SET receipt_id='".$paymentdataheader_receipt_id."',
								  	customer_code 	='".$paymentdataheader_customer_code."',
									amount			='".$paymentdataheader_amount."',
									cash_cheque 	='".$paymentdataheader_cash_cheque."',
									cheque_no		='".$paymentdataheader_cheque_no."',
									date			='".$paymentdataheader_date."',
									bank 			='".addslashes($paymentdataheader_bank)."',
									sale_type		='".$paymentdataheader_sale_type."',
									p_remark		='".addslashes($paymentdataheader_p_remark)."'";	
			if(mysqli_query($link,$sqlinsertpaylocation) && mysqli_query($link,$sqlinsertpaymentheader))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				//Regarding Email Sending
				$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$payment_emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=title_case_emp($rowempname['emp_name']);
				$branch_code=$rowempname['branch_code'];
				$vertical_value=$rowempname['vertical_value'];
				
				if(branch_vertical_operation_wise_email=='yes'){
					$operation_type='Payment';
					$payment_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				}
				else
				{
					if($nick_name=='RUPA' || $nick_name=='RUPAT')
					{
					 $payment_email='';
					}
					else
					{
					 $payment_email=PAYMENTEMAILRECIPENTS;
					}
				}
				if($paymentdataheader_sale_type!='CASH'){
				
				$sqlcustomername="SELECT dns_customer_code,customer_name,route_code,phone_no FROM customer_master 
									WHERE customer_code='".$paymentdataheader_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				$dns_customer_code=$rowcustomername['dns_customer_code'];
				$route_code=$rowcustomername['route_code'];
				$customer_phone_no_collection=$rowcustomername['phone_no'];
						
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
				
				if(providing_code=='yes'){
					$paymentheader_dns_customer_code_TR="<th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Code</span></strong></th>";
					$paymentheader_dns_customer_code_TD="<td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$dns_customer_code."</span>&nbsp;</td>";
				}
				else
				{
					$paymentheader_dns_customer_code_TR='';
					$paymentheader_dns_customer_code_TD=='';
				}
					

				if(substr($payment_trans_id,0,1)!='N')
				{
					$addresspayment=getReverseGeo($payment_latt,$payment_longi);
				}
				//For sending email to recipents in case of No Order transaction happened
				$last_operation_datetime=$payment_date;
				if(substr($payment_trans_id,0,1)=='N')
				{
					$nopaymentemailsubj="$nick_name - Activiy without Transaction ".$emp_name. " on ".date('d-m-Y',strtotime($payment_date))." @".date('H:i:s',strtotime($payment_date)).' hrs.';

					$nopaymentemailbody = "<html><head><title>No Payment</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name.". 
										No payment happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($paymentdataheader_p_remark)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Bcc: ".BCCEMAIL." \r\n" .
								'X-Mailer: PHP/' . phpversion();
					if(mail($payment_email,$nopaymentemailsubj, $nopaymentemailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}// End of if block for No Order transaction checking
				else{
					//For constructing the email body for PAYMENT HEADER if PAYMENT has performed
					if($paymentdataheader_cash_cheque=='CHEQUE' || $paymentdataheader_cash_cheque=='IMPS' || $paymentdataheader_cash_cheque=='RTGS' || $paymentdataheader_cash_cheque=='NEFT' )
					{
						if($paymentdataheader_cash_cheque=='CHEQUE')
						{
							$cheque_no_text='Cheque No.';
							$cheque_date_text='Cheque Date';
						}
						else
						{
							$cheque_no_text='UTR No.';
							$cheque_date_text='UTR Date';
						}
						
						$paymentemailbodytranstypeth="<th style='width:60px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>".$cheque_no_text."</span></strong></th>
							<th style='width:70px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>".$cheque_date_text."</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Bank Name</span></strong></th>";
						$paymentemailbodytranstypetd="<td style='width:60px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_cheque_no."</span>&nbsp;</td>
							<td style='width:70px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_date."</span>&nbsp;</td>
							<td style='width:160px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_bank."</span>&nbsp;</td>";
					}
					
				$paymentemailbody = "<html><head><title>Payment Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue>
							<tr>".$paymentheader_dns_customer_code_TR."
							<th style='width:260px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th><th style='width:260px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Route</span></strong></th><th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Date & Time</span></strong></th><th style='width:60px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Collection Type</span></strong></th>".$paymentemailbodytranstypeth."
							
							<th style='width:82px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Total Amount Received</span></strong></th>
							</tr><tr>".$paymentheader_dns_customer_code_TD."<td style='width:260px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
							".$customer_name."</span>&nbsp;</td><td style='width:260px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$route_name."</span>&nbsp;</td><td style='width:105px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".gmdate('d-m-Y H:i:s',strtotime('+329 minute'))."</span>&nbsp;</td><td style='width:60px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdataheader_cash_cheque."</span>&nbsp;</td>".$paymentemailbodytranstypetd."
							
							<td style='width:82px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdataheader_amount,2)."</span>&nbsp;</td>
							</tr></table>";
							
							array_push($paymentheader_array_mailbody,$paymentemailbody);
							$paymentinstructionemailbody="Remarks: <b>".strtoupper($paymentdataheader_p_remark)."</b>";
							array_push($paymentinstruction_array_mailbody,$paymentinstructionemailbody);
							array_push($paymentemp_array_mailbody,$emp_name);
							array_push($paymentdate_array_mailbody,$payment_date);
							array_push($paymenttansid_array_mailbody,$payment_trans_id);
							array_push($paymentphoneno_array,$customer_phone_no_collection);
							array_push($paymentheader_array_amount,$paymentdataheader_amount);
					}
			}
			array_push($paymentdataheader_sale_type_array,$paymentdataheader_sale_type);
		}
	}// End for loop
	//For Insert into the Payment Details table for new trans id
		if(count($payment_details_array)>0)
		{
			for($k=0;$k<count($payment_details_array);$k++){
				$paymentdatadetails_receipt_id=$payment_details_array[$k]->paymentdatadetails_receipt_id;
				$paymentdatadetails_invoice_id=$payment_details_array[$k]->paymentdatadetails_invoice_id;
				$paymentdatadetails_recid=$payment_details_array[$k]->paymentdatadetails_recid;
				$paymentdatadetails_amount=$payment_details_array[$k]->paymentdatadetails_amount;
				$paymentdatadetails_discount=$payment_details_array[$k]->paymentdatadetails_discount;
				//$paymentdatadetails_receipt_id_chk='P'.$payment_details_array[$k]->paymentdatadetails_receipt_id;
				
				if(!in_array($paymentdatadetails_receipt_id,$payment_array_trans_id))
					{
					$sqlinsertpaymentdetails="INSERT INTO payment_details SET receipt_id='".$paymentdatadetails_receipt_id."',
											invoice_id 	='".$paymentdatadetails_invoice_id."',
											recid		='".$paymentdatadetails_recid."',
											amount		='".$paymentdatadetails_amount."',
											discount	='".$paymentdatadetails_discount."'";
					if(mysqli_query($link,$sqlinsertpaymentdetails))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
					$sqloutstanding="SELECT invoice_amount,due_amount FROM outstanding WHERE invoice_id='".$paymentdatadetails_invoice_id."'";
					$rsoutstanding=mysqli_query($link,$sqloutstanding);
					$rowoutstanding=mysqli_fetch_assoc($rsoutstanding);
					$invoice_amount=$rowoutstanding['invoice_amount'];
					$due_amount=$rowoutstanding['due_amount'];
					
					${invoice_id.$paymentdatadetails_receipt_id}.=$paymentdatadetails_invoice_id;	
					if($paymentdatadetails_invoice_id=='')
					{
						$paymentdatadetails_invoice_id='ON ACCOUNT';
					}
					${a.$paymentdatadetails_receipt_id}.="
								<tr><td style='width:130px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$paymentdatadetails_invoice_id."</span>&nbsp;</td>	
								<td style='width:120px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($invoice_amount,2)."</span>&nbsp;</td>
								<td style='width:110px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($due_amount,2)."</span>&nbsp;</td>
								<td style='width:130px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdatadetails_amount,2)."</span>&nbsp;</td>
								<td style='width:110px;text-align:right;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".number_format($paymentdatadetails_discount,2)."</span>&nbsp;</td>
								</tr>";
				}
		   }
		}
		//End Insert into the Payment Details table for new trans id
		if(count($paymentheader_array_mailbody)>0)
		{
			for($countpayarr=0;$countpayarr<count($paymentheader_array_mailbody);$countpayarr++)
			{
				if(strtoupper($nick_name)=='AUTOCRAFT' || strtoupper($nick_name)=='DAA')
				 {
					 if(strtoupper($nick_name)=='AUTOCRAFT')
					 {
						$smsstringval='Thank you for payment of Rs.'.$paymentheader_array_amount[$countpayarr].' via '.$paymentdataheader_sale_type_array[$countpayarr].'. Please contact us for further details.';
					    $url="http://smslive.in/push/default.aspx?user=ACRAFT&pws=acraft123&Receipent=".$paymentphoneno_array[$countpayarr]."&sms=".urlencode($smsstringval)."";
					 }
					 if(strtoupper($nick_name)=='DAA')
					 {
						$smsstringval='Thank you for payment of Rs.'.$paymentheader_array_amount[$countpayarr].' via '.$paymentdataheader_sale_type_array[$countpayarr].'. Please contact us for further details.'; 
					    $url="http://smslive.in/push/default.aspx?user=DURGAA&pws=durgaa123&Receipent=".$paymentphoneno_array[$countpayarr]."&sms=".urlencode($smsstringval)."";
					 }
					//echo $url;
					if($paymentphoneno_array[$countpayarr]!=''){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_TIMEOUT, 20);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_exec($ch);
					}
				 }
				if($paymentdataheader_sale_type_array[$countpayarr]!='CASH'){
					if(${invoice_id.$paymenttansid_array_mailbody[$countpayarr]}!='')
					{
						$paymentemailsubjpart="$nick_name - Payment received by";
					}
					else
					{
						$paymentemailsubjpart="$nick_name - On account payment received by";
					}
					$paymentemailsubj=$paymentemailsubjpart." ".$paymentemp_array_mailbody[$countpayarr]." on ".date('d-m-Y',strtotime($paymentdate_array_mailbody[$countpayarr]))." @".date('H:i:s',strtotime($paymentdate_array_mailbody[$countpayarr])).' hrs.';
					$paymentemailbody = $paymentheader_array_mailbody[$countpayarr]."<br><table border=1 style=background-color:AliceBlue>
								<tr>
								<th style='width:130px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Invoice No.</span></strong></th>
								<th style='width:120px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Invoice Amount</span></strong></th>
								<th style='width:110px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Due Amount</span></strong></th>
								<th style='width:130px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Amount Received</span></strong></th>
							<th style='width:110px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial CE'>Discount Allowed </span></strong></th></tr>
					".${a.$paymenttansid_array_mailbody[$countpayarr]}."</table>
					<br><br><table>".$paymentinstruction_array_mailbody[$countpayarr]."</table><br /><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Bcc: ".BCCEMAIL." \r\n" .
								'X-Mailer: PHP/' . phpversion();
					if(mail($payment_email,$paymentemailsubj, $paymentemailbody, $headers,$spam_filter))
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
	}

 /* --------------------END QUERY FOR PAYMENT------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 
	 
	 if(($countdatarefresh >0 && $countdeleterefresh >0) || ($countstockrefresh >0 && $countdeleterefresh >0) || ($countloyaltyrefresh >0 && $countdeleterefresh >0))
	 {
		echo $flag=7; 
	 }
	 else if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else if($countdeleterefresh >0)
	 {
		 echo $flag=5;
	 }
	 else if($countstockrefresh >0 && $countloyaltyrefresh >0)
	 {
		 echo $flag=2;
	 }
	 else if($countstockrefresh >0 || $countloyaltyrefresh >0)
	 {
		 if($countstockrefresh >0)
		 {
		 	echo $flag='3'.','.$stock_transferred_by_value;
			//echo $flag=2;
		 }
		 if($countloyaltyrefresh >0)
		 {
			 echo $flag='4';
		 }
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
	
	 if(($countdatarefresh >0 && $countdeleterefresh >0) || ($countstockrefresh >0 && $countdeleterefresh >0) || ($countloyaltyrefresh >0 && $countdeleterefresh >0))
	 {
		echo $flag=7; 
	 }
	 else if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else if($countdeleterefresh >0)
	 {
		 echo $flag=5;
	 }
	 else if($countstockrefresh >0 && $countloyaltyrefresh >0)
	 {
		 echo $flag=2;
	 }
	 else if($countstockrefresh >0 || $countloyaltyrefresh >0)
	 {
		 if($countstockrefresh >0)
		 {
		 	echo $flag='3'.','.$stock_transferred_by_value;
			//echo $flag=2;
		 }
		 if($countloyaltyrefresh >0)
		 {
			 echo $flag='4';
		 }
	 }
	 else
	 {
	 	echo $flag=1;
	 }
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-transaction-6.0.9.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-transaction-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&last_git_master_update_time=$last_git_master_update_time&last_loyalty_purchase_update_time=$last_loyalty_purchase_update_time"."\r\n";
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
