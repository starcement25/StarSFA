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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><yellowcard_info><location><emp_code><![CDATA[E0046]]></emp_code><trans_id><![CDATA[YE00462017021820170218174204]]></trans_id><latt><![CDATA[22.5640687]]></latt><longi><![CDATA[88.3567542]]></longi><date><![CDATA[2017-02-18 17:42:05]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE00462017021820170218174204]]></yellowcard_no><customer_code><![CDATA[C/0129337]]></customer_code><challan_no><![CDATA[123]]></challan_no><challan_date><![CDATA[18-03-17]]></challan_date><qty><![CDATA[123]]></qty><qty_UOM><![CDATA[PSC]]></qty_UOM></yellowcard_details></yellowcard_info></root>";*/
$body="<?xml version='1.0' encoding='UTF-8'?><root><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204225754]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 22:57:54]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204225754]]></yellowcard_no><customer_code><![CDATA[C/0175677]]></customer_code><challan_no><![CDATA[8000126832]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204230217]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 23:02:17]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204230217]]></yellowcard_no><customer_code><![CDATA[C/0182879]]></customer_code><challan_no><![CDATA[8000126832]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204230656]]></trans_id><latt><![CDATA[27.4548606]]></latt><longi><![CDATA[94.9158517]]></longi><date><![CDATA[2023-02-04 23:06:56]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204230656]]></yellowcard_no><customer_code><![CDATA[C/0161794]]></customer_code><challan_no><![CDATA[8000126916]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[900]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204230832]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 23:08:32]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204230832]]></yellowcard_no><customer_code><![CDATA[C/0161794]]></customer_code><challan_no><![CDATA[8000138169]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204231007]]></trans_id><latt><![CDATA[27.4548605]]></latt><longi><![CDATA[94.9158528]]></longi><date><![CDATA[2023-02-04 23:10:07]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204231007]]></yellowcard_no><customer_code><![CDATA[C/0182879]]></customer_code><challan_no><![CDATA[8000138169]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204231141]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 23:11:41]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204231141]]></yellowcard_no><customer_code><![CDATA[C/0182879]]></customer_code><challan_no><![CDATA[8000150442]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204231329]]></trans_id><latt><![CDATA[27.4549767]]></latt><longi><![CDATA[94.9158756]]></longi><date><![CDATA[2023-02-04 23:13:29]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204231329]]></yellowcard_no><customer_code><![CDATA[C/0063293]]></customer_code><challan_no><![CDATA[8000138894]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[600]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204231554]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 23:15:54]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204231554]]></yellowcard_no><customer_code><![CDATA[C/0063323]]></customer_code><challan_no><![CDATA[8000138894]]></challan_no><challan_date><![CDATA[2023-01-15]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204231738]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 23:17:38]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204231738]]></yellowcard_no><customer_code><![CDATA[C/0063413]]></customer_code><challan_no><![CDATA[8000139839]]></challan_no><challan_date><![CDATA[2023-01-16]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232003]]></trans_id><latt><![CDATA[27.4549955]]></latt><longi><![CDATA[94.9158779]]></longi><date><![CDATA[2023-02-04 23:20:03]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232003]]></yellowcard_no><customer_code><![CDATA[C/0173835]]></customer_code><challan_no><![CDATA[8000140948/8000140951]]></challan_no><challan_date><![CDATA[2023-01-17]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232154]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 23:21:54]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232154]]></yellowcard_no><customer_code><![CDATA[C/0063413]]></customer_code><challan_no><![CDATA[8000144856]]></challan_no><challan_date><![CDATA[2023-01-22]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232337]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 23:23:37]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232337]]></yellowcard_no><customer_code><![CDATA[C/0063323]]></customer_code><challan_no><![CDATA[8000144856]]></challan_no><challan_date><![CDATA[2023-01-22]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232610]]></trans_id><latt><![CDATA[27.4548605]]></latt><longi><![CDATA[94.9158528]]></longi><date><![CDATA[2023-02-04 23:26:10]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232610]]></yellowcard_no><customer_code><![CDATA[C/0173835]]></customer_code><challan_no><![CDATA[8000144855]]></challan_no><challan_date><![CDATA[2023-01-22]]></challan_date><qty><![CDATA[900]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232755]]></trans_id><latt><![CDATA[27.4548606]]></latt><longi><![CDATA[94.9158517]]></longi><date><![CDATA[2023-02-04 23:27:55]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232755]]></yellowcard_no><customer_code><![CDATA[C/0189351]]></customer_code><challan_no><![CDATA[8000150460]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204232936]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 23:29:36]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204232936]]></yellowcard_no><customer_code><![CDATA[C/0173835]]></customer_code><challan_no><![CDATA[8000130689]]></challan_no><challan_date><![CDATA[2023-01-07]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204233250]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 23:32:50]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204233250]]></yellowcard_no><customer_code><![CDATA[C/0063350]]></customer_code><challan_no><![CDATA[8000130690]]></challan_no><challan_date><![CDATA[2023-01-07]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204233454]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 23:34:54]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204233454]]></yellowcard_no><customer_code><![CDATA[C/0173835]]></customer_code><challan_no><![CDATA[8000126088/8000126605]]></challan_no><challan_date><![CDATA[2023-01-02]]></challan_date><qty><![CDATA[160]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204233838]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 23:38:38]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204233838]]></yellowcard_no><customer_code><![CDATA[C/0063398]]></customer_code><challan_no><![CDATA[8000127749]]></challan_no><challan_date><![CDATA[2023-01-04]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204234022]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-04 23:40:22]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204234022]]></yellowcard_no><customer_code><![CDATA[C/0063398]]></customer_code><challan_no><![CDATA[8000139750]]></challan_no><challan_date><![CDATA[2023-01-16]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204234201]]></trans_id><latt><![CDATA[27.4548615]]></latt><longi><![CDATA[94.9158529]]></longi><date><![CDATA[2023-02-04 23:42:01]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204234201]]></yellowcard_no><customer_code><![CDATA[C/0063398]]></customer_code><challan_no><![CDATA[8000149027]]></challan_no><challan_date><![CDATA[2023-01-25]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204234416]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-04 23:44:16]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204234416]]></yellowcard_no><customer_code><![CDATA[C/0063398]]></customer_code><challan_no><![CDATA[8000150571]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204234616]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 23:46:16]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204234616]]></yellowcard_no><customer_code><![CDATA[C/0173934]]></customer_code><challan_no><![CDATA[8000126598/8000126600]]></challan_no><challan_date><![CDATA[2023-01-02]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204234817]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 23:48:17]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204234817]]></yellowcard_no><customer_code><![CDATA[C/0173934]]></customer_code><challan_no><![CDATA[8000129978/129983/129987/130060]]></challan_no><challan_date><![CDATA[2023-01-06]]></challan_date><qty><![CDATA[220]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204235034]]></trans_id><latt><![CDATA[27.4551683]]></latt><longi><![CDATA[94.9158863]]></longi><date><![CDATA[2023-02-04 23:50:34]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204235034]]></yellowcard_no><customer_code><![CDATA[C/0173934]]></customer_code><challan_no><![CDATA[8000139435/139439/139458/139462]]></challan_no><challan_date><![CDATA[2023-01-16]]></challan_date><qty><![CDATA[260]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204235234]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 23:52:34]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204235234]]></yellowcard_no><customer_code><![CDATA[C/0173231]]></customer_code><challan_no><![CDATA[8000134714/135055]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[110]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204235442]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 23:54:42]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204235442]]></yellowcard_no><customer_code><![CDATA[C/0173231]]></customer_code><challan_no><![CDATA[8000146215/146465]]></challan_no><challan_date><![CDATA[2023-01-23]]></challan_date><qty><![CDATA[100]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204235701]]></trans_id><latt><![CDATA[27.4552454]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 23:57:01]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204235701]]></yellowcard_no><customer_code><![CDATA[C/0173231]]></customer_code><challan_no><![CDATA[8000147178/147199/147788/147793]]></challan_no><challan_date><![CDATA[2023-01-24]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204001152]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 00:11:52]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204001152]]></yellowcard_no><customer_code><![CDATA[C/0156765]]></customer_code><challan_no><![CDATA[8000155362]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204001340]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 00:13:40]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204001340]]></yellowcard_no><customer_code><![CDATA[C/0164025]]></customer_code><challan_no><![CDATA[8000151129]]></challan_no><challan_date><![CDATA[2023-01-28]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204001522]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 00:15:22]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204001522]]></yellowcard_no><customer_code><![CDATA[C/0157672]]></customer_code><challan_no><![CDATA[8000143445]]></challan_no><challan_date><![CDATA[2023-01-20]]></challan_date><qty><![CDATA[700]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204001727]]></trans_id><latt><![CDATA[27.4548615]]></latt><longi><![CDATA[94.9158529]]></longi><date><![CDATA[2023-02-04 00:17:27]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204001727]]></yellowcard_no><customer_code><![CDATA[C/0075557]]></customer_code><challan_no><![CDATA[8000150954]]></challan_no><challan_date><![CDATA[2023-01-28]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204001917]]></trans_id><latt><![CDATA[27.4549963]]></latt><longi><![CDATA[94.9158695]]></longi><date><![CDATA[2023-02-04 00:19:17]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204001917]]></yellowcard_no><customer_code><![CDATA[C/0085569]]></customer_code><challan_no><![CDATA[8000150954]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204002115]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 00:21:15]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204002115]]></yellowcard_no><customer_code><![CDATA[C/0164025]]></customer_code><challan_no><![CDATA[8000150895]]></challan_no><challan_date><![CDATA[2023-01-20]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204002256]]></trans_id><latt><![CDATA[27.4552569]]></latt><longi><![CDATA[94.9148106]]></longi><date><![CDATA[2023-02-04 00:22:56]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204002256]]></yellowcard_no><customer_code><![CDATA[C/0156763]]></customer_code><challan_no><![CDATA[8000126834]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[600]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204002427]]></trans_id><latt><![CDATA[27.455234]]></latt><longi><![CDATA[94.9154595]]></longi><date><![CDATA[2023-02-04 00:24:27]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204002427]]></yellowcard_no><customer_code><![CDATA[C/0156763]]></customer_code><challan_no><![CDATA[8000138009]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[700]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204003259]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 00:32:59]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204003259]]></yellowcard_no><customer_code><![CDATA[C/0184833]]></customer_code><challan_no><![CDATA[8000126346/126408/126445]]></challan_no><challan_date><![CDATA[2023-01-02]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204003447]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-04 00:34:47]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204003447]]></yellowcard_no><customer_code><![CDATA[C/0188265]]></customer_code><challan_no><![CDATA[8000126453]]></challan_no><challan_date><![CDATA[2023-01-02]]></challan_date><qty><![CDATA[100]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204003809]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 00:38:09]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204003809]]></yellowcard_no><customer_code><![CDATA[C/0188265]]></customer_code><challan_no><![CDATA[8000126819/127021/127029/127036/127046/127050/127051/127057]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[360]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204004056]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 00:40:56]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204004056]]></yellowcard_no><customer_code><![CDATA[C/0157693]]></customer_code><challan_no><![CDATA[8000127494]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204004901]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 00:49:01]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204004901]]></yellowcard_no><customer_code><![CDATA[C/0117889]]></customer_code><challan_no><![CDATA[8000130593/130596/130600]]></challan_no><challan_date><![CDATA[2023-01-07]]></challan_date><qty><![CDATA[150]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005044]]></trans_id><latt><![CDATA[27.4545419]]></latt><longi><![CDATA[94.9158764]]></longi><date><![CDATA[2023-02-04 00:50:44]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005044]]></yellowcard_no><customer_code><![CDATA[C/0157796]]></customer_code><challan_no><![CDATA[8000128199]]></challan_no><challan_date><![CDATA[2023-01-04]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005235]]></trans_id><latt><![CDATA[27.4545461]]></latt><longi><![CDATA[94.9158761]]></longi><date><![CDATA[2023-02-04 00:52:35]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005235]]></yellowcard_no><customer_code><![CDATA[C/0157796]]></customer_code><challan_no><![CDATA[8000129999]]></challan_no><challan_date><![CDATA[2023-01-06]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005409]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 00:54:09]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005409]]></yellowcard_no><customer_code><![CDATA[C/0117849]]></customer_code><challan_no><![CDATA[8000126819]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005543]]></trans_id><latt><![CDATA[27.4548875]]></latt><longi><![CDATA[94.9159106]]></longi><date><![CDATA[2023-02-04 00:55:43]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005543]]></yellowcard_no><customer_code><![CDATA[C/0169901]]></customer_code><challan_no><![CDATA[8000126819]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005804]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 00:58:04]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005804]]></yellowcard_no><customer_code><![CDATA[C/0169901]]></customer_code><challan_no><![CDATA[8000133357/133396]]></challan_no><challan_date><![CDATA[2023-01-10]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204005955]]></trans_id><latt><![CDATA[27.4548615]]></latt><longi><![CDATA[94.9158529]]></longi><date><![CDATA[2023-02-04 00:59:55]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204005955]]></yellowcard_no><customer_code><![CDATA[C/0179964]]></customer_code><challan_no><![CDATA[8000126844]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204010149]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 01:01:49]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204010149]]></yellowcard_no><customer_code><![CDATA[C/0179964]]></customer_code><challan_no><![CDATA[8000150394]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204010334]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 01:03:34]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204010334]]></yellowcard_no><customer_code><![CDATA[C/0170876]]></customer_code><challan_no><![CDATA[8000150431]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204010610]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 01:06:10]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204010610]]></yellowcard_no><customer_code><![CDATA[C/0170876]]></customer_code><challan_no><![CDATA[8000127751/128038]]></challan_no><challan_date><![CDATA[2023-01-04]]></challan_date><qty><![CDATA[150]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204010750]]></trans_id><latt><![CDATA[27.4548615]]></latt><longi><![CDATA[94.9158529]]></longi><date><![CDATA[2023-02-04 01:07:50]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204010750]]></yellowcard_no><customer_code><![CDATA[C/0164100]]></customer_code><challan_no><![CDATA[8000128408]]></challan_no><challan_date><![CDATA[2023-01-05]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204011023]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 01:10:23]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204011023]]></yellowcard_no><customer_code><![CDATA[C/0164100]]></customer_code><challan_no><![CDATA[8000147151/147154/147158]]></challan_no><challan_date><![CDATA[2023-01-24]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204011420]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 01:14:20]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204011420]]></yellowcard_no><customer_code><![CDATA[C/0063185]]></customer_code><challan_no><![CDATA[8000147160/147163/147165/147168147172/147250]]></challan_no><challan_date><![CDATA[2023-01-24]]></challan_date><qty><![CDATA[440]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204011642]]></trans_id><latt><![CDATA[27.4548793]]></latt><longi><![CDATA[94.915135]]></longi><date><![CDATA[2023-02-04 01:16:42]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204011642]]></yellowcard_no><customer_code><![CDATA[C/0156761]]></customer_code><challan_no><![CDATA[8000154473/155003]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204011858]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 01:18:58]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204011858]]></yellowcard_no><customer_code><![CDATA[C/0158139]]></customer_code><challan_no><![CDATA[8000155007/155013/155021]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[220]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204012141]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 01:21:41]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204012141]]></yellowcard_no><customer_code><![CDATA[C/0117877]]></customer_code><challan_no><![CDATA[8000155026/155044/155099]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204012404]]></trans_id><latt><![CDATA[27.4550251]]></latt><longi><![CDATA[94.9158829]]></longi><date><![CDATA[2023-02-04 01:24:04]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204012404]]></yellowcard_no><customer_code><![CDATA[C/0160470]]></customer_code><challan_no><![CDATA[8000127756/128078]]></challan_no><challan_date><![CDATA[2023-01-04]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204012637]]></trans_id><latt><![CDATA[27.4548082]]></latt><longi><![CDATA[94.9158596]]></longi><date><![CDATA[2023-02-04 01:26:37]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204012637]]></yellowcard_no><customer_code><![CDATA[C/0117881]]></customer_code><challan_no><![CDATA[8000128769/128789/129030/129574]]></challan_no><challan_date><![CDATA[2023-01-05]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204012859]]></trans_id><latt><![CDATA[27.4548353]]></latt><longi><![CDATA[94.9159396]]></longi><date><![CDATA[2023-02-04 01:28:59]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204012859]]></yellowcard_no><customer_code><![CDATA[C/0117885]]></customer_code><challan_no><![CDATA[8000148682]]></challan_no><challan_date><![CDATA[2023-01-25]]></challan_date><qty><![CDATA[690]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204013033]]></trans_id><latt><![CDATA[27.4548905]]></latt><longi><![CDATA[94.9158792]]></longi><date><![CDATA[2023-02-04 01:30:33]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204013033]]></yellowcard_no><customer_code><![CDATA[C/0117885]]></customer_code><challan_no><![CDATA[8000148818]]></challan_no><challan_date><![CDATA[2023-01-25]]></challan_date><qty><![CDATA[50]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204013404]]></trans_id><latt><![CDATA[27.454817]]></latt><longi><![CDATA[94.9158585]]></longi><date><![CDATA[2023-02-04 01:34:04]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204013404]]></yellowcard_no><customer_code><![CDATA[C/0177334]]></customer_code><challan_no><![CDATA[8000130130]]></challan_no><challan_date><![CDATA[2023-01-07]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204013815]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 01:38:15]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204013815]]></yellowcard_no><customer_code><![CDATA[C/0157684]]></customer_code><challan_no><![CDATA[8000130003/130007]]></challan_no><challan_date><![CDATA[2023-01-06]]></challan_date><qty><![CDATA[200]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204014103]]></trans_id><latt><![CDATA[27.4547873]]></latt><longi><![CDATA[94.9158519]]></longi><date><![CDATA[2023-02-04 01:41:03]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204014103]]></yellowcard_no><customer_code><![CDATA[C/0117865]]></customer_code><challan_no><![CDATA[8000143623]]></challan_no><challan_date><![CDATA[2023-01-20]]></challan_date><qty><![CDATA[650]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204014303]]></trans_id><latt><![CDATA[27.454937]]></latt><longi><![CDATA[94.9158833]]></longi><date><![CDATA[2023-02-04 01:43:03]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204014303]]></yellowcard_no><customer_code><![CDATA[C/0117865]]></customer_code><challan_no><![CDATA[8000144854/8000144861]]></challan_no><challan_date><![CDATA[2023-01-21]]></challan_date><qty><![CDATA[140]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204014455]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-04 01:44:55]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204014455]]></yellowcard_no><customer_code><![CDATA[C/0169900]]></customer_code><challan_no><![CDATA[8000144889/144894]]></challan_no><challan_date><![CDATA[2023-01-21]]></challan_date><qty><![CDATA[600]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204014741]]></trans_id><latt><![CDATA[27.4549071]]></latt><longi><![CDATA[94.9158914]]></longi><date><![CDATA[2023-02-04 01:47:41]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204014741]]></yellowcard_no><customer_code><![CDATA[C/0188266]]></customer_code><challan_no><![CDATA[8000134300]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204014932]]></trans_id><latt><![CDATA[27.4548606]]></latt><longi><![CDATA[94.9158517]]></longi><date><![CDATA[2023-02-04 01:49:32]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204014932]]></yellowcard_no><customer_code><![CDATA[C/0157682]]></customer_code><challan_no><![CDATA[8000134300]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204015143]]></trans_id><latt><![CDATA[27.455258]]></latt><longi><![CDATA[94.9159212]]></longi><date><![CDATA[2023-02-04 01:51:43]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204015143]]></yellowcard_no><customer_code><![CDATA[C/0157682]]></customer_code><challan_no><![CDATA[8000135775/135867]]></challan_no><challan_date><![CDATA[2023-01-12]]></challan_date><qty><![CDATA[150]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204015405]]></trans_id><latt><![CDATA[27.4548082]]></latt><longi><![CDATA[94.9158596]]></longi><date><![CDATA[2023-02-04 01:54:05]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204015405]]></yellowcard_no><customer_code><![CDATA[C/0184179]]></customer_code><challan_no><![CDATA[8000134299]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[730]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204015950]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 01:59:50]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204015950]]></yellowcard_no><customer_code><![CDATA[C/0117837]]></customer_code><challan_no><![CDATA[8000134299]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[730]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204020303]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 02:03:03]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204020303]]></yellowcard_no><customer_code><![CDATA[C/0185815]]></customer_code><challan_no><![CDATA[8000153132]]></challan_no><challan_date><![CDATA[2023-01-30]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204020512]]></trans_id><latt><![CDATA[27.4549727]]></latt><longi><![CDATA[94.9158764]]></longi><date><![CDATA[2023-02-04 02:05:12]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204020512]]></yellowcard_no><customer_code><![CDATA[C/0156759]]></customer_code><challan_no><![CDATA[8000131109]]></challan_no><challan_date><![CDATA[2023-01-07]]></challan_date><qty><![CDATA[420]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204020709]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-04 02:07:09]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204020709]]></yellowcard_no><customer_code><![CDATA[C/0156759]]></customer_code><challan_no><![CDATA[8000138133]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204020943]]></trans_id><latt><![CDATA[27.4548557]]></latt><longi><![CDATA[94.9158521]]></longi><date><![CDATA[2023-02-04 02:09:43]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204020943]]></yellowcard_no><customer_code><![CDATA[C/0156759]]></customer_code><challan_no><![CDATA[18000150675/150680/150688]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[260]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204021107]]></trans_id><latt><![CDATA[27.4534793]]></latt><longi><![CDATA[94.9149243]]></longi><date><![CDATA[2023-02-04 02:11:07]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204021107]]></yellowcard_no><customer_code><![CDATA[C/0156759]]></customer_code><challan_no><![CDATA[8000154947]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[900]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204021551]]></trans_id><latt><![CDATA[27.4552606]]></latt><longi><![CDATA[94.9159282]]></longi><date><![CDATA[2023-02-04 02:15:51]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204021551]]></yellowcard_no><customer_code><![CDATA[C/0063434]]></customer_code><challan_no><![CDATA[8000153155/153164/153173/153179/153182/153311/153372/153380/153651/153663/153677]]></challan_no><challan_date><![CDATA[2023-01-30]]></challan_date><qty><![CDATA[680]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204021720]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 02:17:20]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204021720]]></yellowcard_no><customer_code><![CDATA[C/0063434]]></customer_code><challan_no><![CDATA[8000149505]]></challan_no><challan_date><![CDATA[2023-01-26]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204021905]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 02:19:05]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204021905]]></yellowcard_no><customer_code><![CDATA[C/0189040]]></customer_code><challan_no><![CDATA[8000149504]]></challan_no><challan_date><![CDATA[2023-01-26]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204022211]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-04 02:22:11]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204022211]]></yellowcard_no><customer_code><![CDATA[C/0063434]]></customer_code><challan_no><![CDATA[8000144562]]></challan_no><challan_date><![CDATA[2023-01-21]]></challan_date><qty><![CDATA[900]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204022329]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 02:23:29]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204022329]]></yellowcard_no><customer_code><![CDATA[C/0189040]]></customer_code><challan_no><![CDATA[8000144860]]></challan_no><challan_date><![CDATA[2023-01-22]]></challan_date><qty><![CDATA[700]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204022530]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 02:25:30]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204022530]]></yellowcard_no><customer_code><![CDATA[C/0063434]]></customer_code><challan_no><![CDATA[8000142355]]></challan_no><challan_date><![CDATA[2023-01-19]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204022704]]></trans_id><latt><![CDATA[27.4549379]]></latt><longi><![CDATA[94.9159187]]></longi><date><![CDATA[2023-02-04 02:27:04]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204022704]]></yellowcard_no><customer_code><![CDATA[C/0189040]]></customer_code><challan_no><![CDATA[8000138013]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204022800]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 02:28:00]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204022800]]></yellowcard_no><customer_code><![CDATA[C/0063434]]></customer_code><challan_no><![CDATA[8000138015]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204023251]]></trans_id><latt><![CDATA[27.4548046]]></latt><longi><![CDATA[94.9158591]]></longi><date><![CDATA[2023-02-04 02:32:51]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204023251]]></yellowcard_no><customer_code><![CDATA[C/0175380]]></customer_code><challan_no><![CDATA[8000126139/126162/126176/126334]]></challan_no><challan_date><![CDATA[2023-01-02]]></challan_date><qty><![CDATA[310]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204023457]]></trans_id><latt><![CDATA[27.4548608]]></latt><longi><![CDATA[94.9158505]]></longi><date><![CDATA[2023-02-04 02:34:57]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204023457]]></yellowcard_no><customer_code><![CDATA[C/0175380]]></customer_code><challan_no><![CDATA[8000152959/153077]]></challan_no><challan_date><![CDATA[2023-01-30]]></challan_date><qty><![CDATA[140]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204023815]]></trans_id><latt><![CDATA[27.4548586]]></latt><longi><![CDATA[94.9158508]]></longi><date><![CDATA[2023-02-04 02:38:15]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204023815]]></yellowcard_no><customer_code><![CDATA[C/0175380]]></customer_code><challan_no><![CDATA[8000146498/146503/146510]]></challan_no><challan_date><![CDATA[2023-01-23]]></challan_date><qty><![CDATA[240]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204023922]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-04 02:39:22]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204023922]]></yellowcard_no><customer_code><![CDATA[C/0175380]]></customer_code><challan_no><![CDATA[8000150133]]></challan_no><challan_date><![CDATA[2023-01-27]]></challan_date><qty><![CDATA[80]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230204024104]]></trans_id><latt><![CDATA[27.4545461]]></latt><longi><![CDATA[94.9158761]]></longi><date><![CDATA[2023-02-04 02:41:04]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230204024104]]></yellowcard_no><customer_code><![CDATA[C/0175380]]></customer_code><challan_no><![CDATA[8000127251]]></challan_no><challan_date><![CDATA[2023-01-03]]></challan_date><qty><![CDATA[80]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205024529]]></trans_id><latt><![CDATA[27.454803]]></latt><longi><![CDATA[94.915855]]></longi><date><![CDATA[2023-02-05 02:45:29]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205024529]]></yellowcard_no><customer_code><![CDATA[C/0189352]]></customer_code><challan_no><![CDATA[8000153834]]></challan_no><challan_date><![CDATA[2023-01-31]]></challan_date><qty><![CDATA[500]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205025042]]></trans_id><latt><![CDATA[27.4548574]]></latt><longi><![CDATA[94.9158527]]></longi><date><![CDATA[2023-02-05 02:50:42]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205025042]]></yellowcard_no><customer_code><![CDATA[C/0189594]]></customer_code><challan_no><![CDATA[8000152685]]></challan_no><challan_date><![CDATA[2023-01-29]]></challan_date><qty><![CDATA[300]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205025321]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-05 02:53:21]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205025321]]></yellowcard_no><customer_code><![CDATA[C/0063338]]></customer_code><challan_no><![CDATA[8000152684]]></challan_no><challan_date><![CDATA[2023-01-29]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205025631]]></trans_id><latt><![CDATA[27.454862]]></latt><longi><![CDATA[94.9158504]]></longi><date><![CDATA[2023-02-05 02:56:31]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205025631]]></yellowcard_no><customer_code><![CDATA[C/0187997]]></customer_code><challan_no><![CDATA[8000131955]]></challan_no><challan_date><![CDATA[2023-01-08]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205025744]]></trans_id><latt><![CDATA[27.4547433]]></latt><longi><![CDATA[94.9158534]]></longi><date><![CDATA[2023-02-05 02:57:44]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205025744]]></yellowcard_no><customer_code><![CDATA[C/0187997]]></customer_code><challan_no><![CDATA[8000138545]]></challan_no><challan_date><![CDATA[2023-01-14]]></challan_date><qty><![CDATA[600]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205025909]]></trans_id><latt><![CDATA[27.4548602]]></latt><longi><![CDATA[94.9158496]]></longi><date><![CDATA[2023-02-05 02:59:09]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205025909]]></yellowcard_no><customer_code><![CDATA[C/0187997]]></customer_code><challan_no><![CDATA[8000148136]]></challan_no><challan_date><![CDATA[2023-01-25]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205030014]]></trans_id><latt><![CDATA[27.4548605]]></latt><longi><![CDATA[94.9158528]]></longi><date><![CDATA[2023-02-05 03:00:14]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205030014]]></yellowcard_no><customer_code><![CDATA[C/0187998]]></customer_code><challan_no><![CDATA[8000129369]]></challan_no><challan_date><![CDATA[2023-01-06]]></challan_date><qty><![CDATA[800]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205030116]]></trans_id><latt><![CDATA[27.4548609]]></latt><longi><![CDATA[94.9158495]]></longi><date><![CDATA[2023-02-05 03:01:16]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205030116]]></yellowcard_no><customer_code><![CDATA[C/0187998]]></customer_code><challan_no><![CDATA[8000134957]]></challan_no><challan_date><![CDATA[2023-01-11]]></challan_date><qty><![CDATA[250]]></qty><qty_UOM><![CDATA[ARC]]></qty_UOM></yellowcard_details></yellowcard_info><yellowcard_info><location><emp_code><![CDATA[E0206]]></emp_code><trans_id><![CDATA[YE020620230205030218]]></trans_id><latt><![CDATA[27.4548399]]></latt><longi><![CDATA[94.915868]]></longi><date><![CDATA[2023-02-05 03:02:18]]></date></location><yellowcard_details><yellowcard_no><![CDATA[YE020620230205030218]]></yellowcard_no><customer_code><![CDATA[C/0187998]]></customer_code><challan_no><![CDATA[8000148137]]></challan_no><challan_date><![CDATA[2023-01-25]]></challan_date><qty><![CDATA[400]]></qty><qty_UOM><![CDATA[PPC]]></qty_UOM></yellowcard_details></yellowcard_info></root>";
$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";

$location_emp_code="*ROOT*YELLOWCARD_INFO*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*YELLOWCARD_INFO*LOCATION*TRANS_ID";
$location_latt = "*ROOT*YELLOWCARD_INFO*LOCATION*LATT";
$location_longi = "*ROOT*YELLOWCARD_INFO*LOCATION*LONGI";
$location_date="*ROOT*YELLOWCARD_INFO*LOCATION*DATE";

$yellowcard_no = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*YELLOWCARD_NO";
$customer_code = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CUSTOMER_CODE";
$challan_no = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CHALLAN_NO";
$challan_date = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*CHALLAN_DATE";
$qty = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*QTY";
$qty_UOM = "*ROOT*YELLOWCARD_INFO*YELLOWCARD_DETAILS*QTY_UOM";

$attendance_array = array();
$yellowcard_array=array();

$counteratt=0;
$counter = 0;

class xml_attendance{
    var $emp_code, $trans_id,$latt,$longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}
class xml_yellowcard{
   var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$yellowcard_no,$customer_code,$challan_no,$challan_date,
   $qty,$qty_UOM;	
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
    global $current_tag,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$counter,$counteratt,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$yellowcard_no,$customer_code,$challan_no,$challan_date,$qty,$qty_UOM,$yellowcard_array,$attendance_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*ATTENDANCE')
	{
		switch($current_tag){
			case $attendance_emp_code:
				$attendance_array[$counteratt] = new xml_attendance();
				$attendance_array[$counteratt]->emp_code = $data;
				break;
			case $attendance_trans_id:
				$attendance_array[$counteratt]->trans_id = $data;
				break;
			case $attendance_latt:
				$attendance_array[$counteratt]->latt = $data;
				break;
			case $attendance_longi:
				$attendance_array[$counteratt]->longi = $data;
				break;
			case $attendance_date:
				$attendance_array[$counteratt]->attendance_date = $data;
				break;
			case $attendancedata_emp_code:
				$attendance_array[$counteratt]->attendancedata_emp_code = $data;
				break;
			case $attendancedata_date:
				$attendance_array[$counteratt]->attendancedata_date = $data;
				$counteratt++;
				break;
		}
	}
	if(substr($current_tag,0,21)=='*ROOT*YELLOWCARD_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$yellowcard_array[$counter] = new xml_yellowcard();
				$yellowcard_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$yellowcard_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$yellowcard_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$yellowcard_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$yellowcard_array[$counter]->location_date = $data;
				break;
			case $yellowcard_no:
				$yellowcard_array[$counter]->yellowcard_no = $data;
				break;
			case $customer_code:
				$yellowcard_array[$counter]->customer_code = $data;
				break;
			case $challan_no:
				$yellowcard_array[$counter]->challan_no = $data;
				break;
			case $challan_date:
				$yellowcard_array[$counter]->challan_date = $data;
				break;
			case $qty:
				$yellowcard_array[$counter]->qty = $data;
				break;	
			case $qty_UOM:
				$yellowcard_array[$counter]->qty_UOM = $data;
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
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check attendance location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0)
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

/* --------------------START QUERY FOR YELLOWCARD INFO------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$yellowcard_array_trans_id=array();
if(count($yellowcard_array)>0)
{
	for($x=0;$x<count($yellowcard_array);$x++){
		$location_emp_code=$yellowcard_array[$x]->location_emp_code;
		$location_trans_id=$yellowcard_array[$x]->location_trans_id;
		$location_latt=$yellowcard_array[$x]->location_latt;
		$location_longi=$yellowcard_array[$x]->location_longi;
		$location_date=$yellowcard_array[$x]->location_date;
		$yellowcard_no=$yellowcard_array[$x]->yellowcard_no;
		$customer_code= $yellowcard_array[$x]->customer_code;
		$challan_no= $yellowcard_array[$x]->challan_no;
		$challan_date=$yellowcard_array[$x]->challan_date;
		$qty=$yellowcard_array[$x]->qty;
		$qty_UOM=$yellowcard_array[$x]->qty_UOM;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for yellowcard
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for yellowcard
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$yellowcard_array_trans_id))
			{
				array_push($yellowcard_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update yellowcard info location: ".$sqlupdateorlocation);
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
		//Creation of code random no parameter
		/*$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=title_case_emp($rowempname['emp_name']);
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];*/
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding YELLOWCARD info
		echo $sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								updatetime='".$location_date_updatetime."'"; 
	  if(mysqli_query($link,$sqlinsertorlocation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		//For Insert into the yellow card table for new trans id
		echo $sqlinsertyellowcard="INSERT INTO yellow_card_details SET yellow_card_no	='".$yellowcard_no."',
							  customer_code 	='".$customer_code."',
							  challan_no 		='".addslashes($challan_no)."',
							  challan_date		='".$challan_date."',
							  qty				='".$qty."',
							  qty_UOM          	='".$qty_UOM."'";
		if(mysqli_query($link,$sqlinsertyellowcard))
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
 /* --------------------END QUERY FOR YELLOWCARD INFO--------------------------------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-yellowcard-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
