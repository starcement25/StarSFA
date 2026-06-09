<?php
// error_reporting(0);

 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
//  ini_set('display_errors', 'Off');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
//include '/home/acedns/public_html/php-calendar/classes/calendar.php';
//include 'misreport/star_email/vendor/microsoft/microsoft-graph/src/Model/Calendar.php';
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

/*$body2 = "<?xml version='1.0' encoding='UTF-8'?><root><route_plan><route_plan_details><route_plan_trans_id><![CDATA[RPE055520240209153521]]></route_plan_trans_id><emp_code><![CDATA[E0555]]></emp_code><route_code><![CDATA[RT/11956]]></route_code><route_name><![CDATA[18 MILE]]></route_name><visit_date><![CDATA[09-02-2024]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2024-02-09 15:35:29]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[]]></distributor_code><WORKING_WITH><![CDATA[]]></WORKING_WITH></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE055520240209153828]]></route_plan_trans_id><emp_code><![CDATA[E0555]]></emp_code><route_code><![CDATA[RT/28939]]></route_code><route_name><![CDATA[4 MAIL]]></route_name><visit_date><![CDATA[09-02-2024]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2024-02-09 15:39:19]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[]]></distributor_code><WORKING_WITH><![CDATA[]]></WORKING_WITH></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE055520240209153936]]></route_plan_trans_id><emp_code><![CDATA[E0555]]></emp_code><route_code><![CDATA[RT/30003]]></route_code><route_name><![CDATA[58 NO GATE]]></route_name><visit_date><![CDATA[09-02-2024]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2024-02-09 15:45:58]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[]]></distributor_code><WORKING_WITH><![CDATA[]]></WORKING_WITH></route_plan_details></route_plan></root>";*/

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><route_plan><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160908095606]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160908095752]]></route_code><route_name><![CDATA[LOCAL MARKET]]></route_name><visit_date><![CDATA[08-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-08 09:57:57]]></create_date><status><![CDATA[]]></status><distributor_code><![CDATA[C/0000476]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160908114149]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/277]]></route_code><route_name><![CDATA[GADHPURA]]></route_name><visit_date><![CDATA[08-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-08 11:42:06]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160909095412]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160909095505]]></route_code><route_name><![CDATA[MAIN MARKET ZERO MILE]]></route_name><visit_date><![CDATA[09-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-09 09:55:10]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000477]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160910102108]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160910102149]]></route_code><route_name><![CDATA[CINEMA CHAWK]]></route_name><visit_date><![CDATA[10-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-10 10:21:54]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160913094233]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/274]]></route_code><route_name><![CDATA[HARIPUR]]></route_name><visit_date><![CDATA[13-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-13 09:42:59]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160916092841]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/278]]></route_code><route_name><![CDATA[MADAIYA]]></route_name><visit_date><![CDATA[16-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-16 09:29:55]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000483]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160917095338]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/276]]></route_code><route_name><![CDATA[BARO]]></route_name><visit_date><![CDATA[17-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-17 09:53:50]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000481]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160919094648]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160909095505]]></route_code><route_name><![CDATA[MAIN MARKET ZERO MILE]]></route_name><visit_date><![CDATA[19-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-19 09:46:59]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000477]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160922095337]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/276]]></route_code><route_name><![CDATA[BARO]]></route_name><visit_date><![CDATA[22-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-22 09:53:50]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000481]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160923095357]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/275]]></route_code><route_name><![CDATA[MAHE SINGHIA]]></route_name><visit_date><![CDATA[23-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-23 09:54:08]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000480]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160923100743]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/275]]></route_code><route_name><![CDATA[MAHE SINGHIA]]></route_name><visit_date><![CDATA[23-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-23 10:07:43]]></create_date><status><![CDATA[inactive]]></status><distributor_code><![CDATA[C/0000480]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160926095241]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160909095505]]></route_code><route_name><![CDATA[MAIN MARKET ZERO MILE]]></route_name><visit_date><![CDATA[26-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-26 09:52:55]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000477]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160928093557]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160928093628]]></route_code><route_name><![CDATA[BUS STAND]]></route_name><visit_date><![CDATA[28-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-28 09:36:32]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160929095026]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/274]]></route_code><route_name><![CDATA[HARIPUR]]></route_name><visit_date><![CDATA[29-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-29 09:50:35]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020160930095411]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/277]]></route_code><route_name><![CDATA[GADHPURA]]></route_name><visit_date><![CDATA[30-09-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-09-30 09:54:21]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161001094735]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/276]]></route_code><route_name><![CDATA[BARO]]></route_name><visit_date><![CDATA[01-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-01 10:22:13]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000481]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161006103342]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161006103412]]></route_code><route_name><![CDATA[BUDHAURA]]></route_name><visit_date><![CDATA[06-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-06 10:34:14]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161007144153]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161007144255]]></route_code><route_name><![CDATA[LOCAL MARKET.]]></route_name><visit_date><![CDATA[07-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-07 14:42:59]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161008143522]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160909095505]]></route_code><route_name><![CDATA[MAIN MARKET ZERO MILE]]></route_name><visit_date><![CDATA[08-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-08 14:35:29]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000477]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161014125030]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160928093628]]></route_code><route_name><![CDATA[BUS STAND]]></route_name><visit_date><![CDATA[14-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-14 12:50:36]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161015095738]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160908095752]]></route_code><route_name><![CDATA[LOCAL MARKET]]></route_name><visit_date><![CDATA[15-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-15 09:57:47]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000476]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161017094953]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161017095018]]></route_code><route_name><![CDATA[GARHARA]]></route_name><visit_date><![CDATA[17-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-17 09:50:20]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000481]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161019095628]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161019095647]]></route_code><route_name><![CDATA[JAMALPUR LOCAL]]></route_name><visit_date><![CDATA[19-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-19 09:56:50]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0001232]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161020095407]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161020095551]]></route_code><route_name><![CDATA[PASRAHA BUNDHERA]]></route_name><visit_date><![CDATA[20-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-20 09:55:54]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0001232]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161021094602]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160908095752]]></route_code><route_name><![CDATA[LOCAL MARKET]]></route_name><visit_date><![CDATA[21-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-21 09:46:12]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000476]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161026095322]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/274]]></route_code><route_name><![CDATA[HARIPUR]]></route_name><visit_date><![CDATA[26-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-26 09:53:50]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161027095800]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161007144255]]></route_code><route_name><![CDATA[LOCAL MARKET.]]></route_name><visit_date><![CDATA[27-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-27 10:07:58]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020161028094427]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161028094457]]></route_code><route_name><![CDATA[ANUMANDAL ROAD]]></route_name><visit_date><![CDATA[28-10-2016]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2016-10-28 09:44:59]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180331095550]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160910102149]]></route_code><route_name><![CDATA[CINEMA CHAWK]]></route_name><visit_date><![CDATA[31-03-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-03-31 09:56:11]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180331095550]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161203095221]]></route_code><route_name><![CDATA[THANA ROAD]]></route_name><visit_date><![CDATA[31-03-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-03-31 09:56:11]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180402093228]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/1227]]></route_code><route_name><![CDATA[MANSI]]></route_name><visit_date><![CDATA[02-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-02 09:32:41]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0002045]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180403093636]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161007144255]]></route_code><route_name><![CDATA[LOCAL MARKET.]]></route_name><visit_date><![CDATA[03-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-03 09:36:47]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180403093636]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/277]]></route_code><route_name><![CDATA[GADHPURA]]></route_name><visit_date><![CDATA[03-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-03 09:36:47]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000482]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180404094124]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020161006103412]]></route_code><route_name><![CDATA[BUDHAURA]]></route_name><visit_date><![CDATA[04-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-04 09:41:38]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180404094124]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[RT/274]]></route_code><route_name><![CDATA[HARIPUR]]></route_name><visit_date><![CDATA[04-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-04 09:41:38]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000479]]></distributor_code></route_plan_details><route_plan_details><route_plan_trans_id><![CDATA[RPE019020180406132257]]></route_plan_trans_id><emp_code><![CDATA[E0190]]></emp_code><route_code><![CDATA[NRT/E019020160928093628]]></route_code><route_name><![CDATA[BUS STAND]]></route_name><visit_date><![CDATA[06-04-2018]]></visit_date><remarks><![CDATA[]]></remarks><create_date><![CDATA[2018-04-06 13:23:43]]></create_date><status><![CDATA[active]]></status><distributor_code><![CDATA[C/0000478]]></distributor_code></route_plan_details></route_plan></root>";*/
if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$month = isset($_GET['m']) ? $_GET['m'] : NULL;
$year  = isset($_GET['y']) ? $_GET['y'] : NULL;
$route_plan_trans_id = "*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_PLAN_TRANS_ID";
$route_plan_emp_code = "*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*EMP_CODE";
$route_plan_route_code ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_CODE";
$route_plan_route_name ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*ROUTE_NAME";
$route_plan_visit_date ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*VISIT_DATE";
$route_plan_remarks ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*REMARKS";
$route_plan_create_date ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*CREATE_DATE";
$route_plan_status ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*STATUS";
$route_plan_distributor_code ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*DISTRIBUTOR_CODE";
$route_plan_working_with ="*ROOT*ROUTE_PLAN*ROUTE_PLAN_DETAILS*WORKING_WITH";

$route_plan_array = array();
$route_plan_trans_id_array=array();
$route_plan_emp_code_array=array();
$route_plan_visit_date_array=array();
$route_plan_create_date_array=array();
$route_plan_visit_date_event_array=array();
$route_plan_del_emp_code_array=array();
$route_plan_del_visit_date_array=array();
$route_plan_route_code_array=array();
$counter = 0;

class xml_route_plan{
	var $route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,$route_plan_create_date,$route_plan_status,$route_plan_distributor_code,$route_plan_working_with;
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
    global $current_tag,$route_plan_trans_id,$route_plan_emp_code,$route_plan_route_code,$route_plan_route_name,$route_plan_visit_date,$route_plan_remarks,
			$route_plan_create_date,$route_plan_status,$route_plan_distributor_code,$route_plan_array,$counter,$route_plan_working_with;
	if(substr($current_tag,0,16)=='*ROOT*ROUTE_PLAN')
	{
		/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $route_plan_trans_id:
				$route_plan_array[$counter] = new xml_route_plan();
				$route_plan_array[$counter]->route_plan_trans_id = $data;
				break;
			case $route_plan_emp_code:
				$route_plan_array[$counter]->route_plan_emp_code = $data;
				break;
			case $route_plan_route_code:
				$route_plan_array[$counter]->route_plan_route_code = $data;
				break;
			case $route_plan_route_name:
				$route_plan_array[$counter]->route_plan_route_name = $data;
				break;	
			case $route_plan_visit_date:
				$route_plan_array[$counter]->route_plan_visit_date = $data;
				break;
			case $route_plan_remarks:
				$route_plan_array[$counter]->route_plan_remarks = $data;
				break;	
			case $route_plan_create_date:
				$route_plan_array[$counter]->route_plan_create_date = $data;
				break;
			case $route_plan_status:
				$route_plan_array[$counter]->route_plan_status = $data;
				break;	
			case $route_plan_distributor_code:
				$route_plan_array[$counter]->route_plan_distributor_code = $data;
				break;
			case $route_plan_working_with:
				$route_plan_array[$counter]->route_plan_working_with = $data;
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
//print_r($attendance_array);exit();
//print_r($audit_array);
//print_r($stock_audit_array);
//echo count($route_plan_array);exit();
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -------------------------------------------------------START QUERY FOR ROUTEPLAN-----------------------------------------------------------------------*/
if(count($route_plan_array)>0)
{
	//$count=1;
	$blank_val='  ';
	for($x=0;$x<count($route_plan_array);$x++){
		$route_plan_trans_id=$route_plan_array[$x]->route_plan_trans_id;
		$route_plan_emp_code=$route_plan_array[$x]->route_plan_emp_code;
		$route_plan_route_code=$route_plan_array[$x]->route_plan_route_code;
		$route_plan_route_name=$route_plan_array[$x]->route_plan_route_name;
		$route_plan_visit_date=$route_plan_array[$x]->route_plan_visit_date;
		$route_plan_remarks=$route_plan_array[$x]->route_plan_remarks;
		$route_plan_create_date=$route_plan_array[$x]->route_plan_create_date;
		$route_plan_status=$route_plan_array[$x]->route_plan_status;
		$route_plan_visit_date_database=date('Y-m-d',strtotime($route_plan_visit_date));
		$route_plan_distributor_code=$route_plan_array[$x]->route_plan_distributor_code;
		$route_plan_working_with=$route_plan_array[$x]->route_plan_working_with;
		if($route_plan_working_with==NULL){
		    $route_plan_working_with="";
		}
		if($route_plan_working_with=="null"){
		    $route_plan_working_with="";
		}
		
		$routeplanemp_visitdate_merge=$route_plan_emp_code.$route_plan_visit_date_database.$route_plan_working_with;
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$update_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
				
		//For checking that trans id exist or not
		$sqlselectrouteplan="SELECT route_plan_trans_id,route_code FROM route_plan 
							  WHERE emp_code ='".$route_plan_emp_code."' AND visit_date	='".$route_plan_visit_date_database."'  AND working_with='".$route_plan_working_with."'";
							  //echo $sqlselectrouteplan;exit();
		$resselectrouteplan = mysqli_query($link,$sqlselectrouteplan) or die(mysqli_error()." Error in chk trans id for route plan: ".$sqlselectrouteplan); 
		$countselectrouteplan=mysqli_num_rows($resselectrouteplan);
		if($countselectrouteplan<1)
		{
			if($route_plan_emp_code==$emp_code){
			$sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
								  emp_code 			='".$route_plan_emp_code."',
								  route_code 		='".$route_plan_route_code."',
								  visit_date 		='".$route_plan_visit_date_database."',
								  remarks			='".addslashes($route_plan_remarks)."',
								  distributor_code	='".$route_plan_distributor_code."',
								  status			='".$route_plan_status."',
								  create_date 		='".$route_plan_create_date."',
								  update_date		='".$update_date."',
								  working_with		='".$route_plan_working_with."'";
			if(mysqli_query($link,$sqlinsertrouteplan))
			{
				$flag=5;
				if(!in_array($routeplanemp_visitdate_merge,$route_plan_del_emp_code_array))
				{
					array_push($route_plan_del_emp_code_array,$routeplanemp_visitdate_merge);
					//array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
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
		else
		{
			if($route_plan_emp_code==$emp_code){
			//For deleting the previous values of same visit date
			if(!in_array($routeplanemp_visitdate_merge,$route_plan_del_emp_code_array))
			{
				$sqldeleterouteplan="DELETE FROM route_plan WHERE  emp_code ='".$route_plan_emp_code."' AND 
									visit_date	='".$route_plan_visit_date_database."' AND working_with='".$route_plan_working_with."'";
				if(mysqli_query($link,$sqldeleterouteplan)){
					array_push($route_plan_del_emp_code_array,$routeplanemp_visitdate_merge);
					//array_push($route_plan_del_visit_date_array,$route_plan_visit_date_database);
				}
			}
			$sqlinsertrouteplan="INSERT INTO route_plan SET route_plan_trans_id ='".$route_plan_trans_id."',
								  emp_code 			='".$route_plan_emp_code."',
								  route_code 		='".$route_plan_route_code."',
								  visit_date 		='".$route_plan_visit_date_database."',
								  create_date 		='".$route_plan_create_date."',
								  remarks			='".addslashes($route_plan_remarks)."',
								  status			='".$route_plan_status."',
								  distributor_code	='".$route_plan_distributor_code."',
								  update_date		='".$update_date."',
								  working_with		='".$route_plan_working_with."'";
								  //echo $sqlinsertrouteplan;
			if(mysqli_query($link,$sqlinsertrouteplan))
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
		if(distributor_route_planning=='yes')
		{
		// Add new route to route master
		if(substr($route_plan_route_code,0,1)=='N'){
			//$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$area."'";
			$sqlroute="select route_name from route_master WHERE route_name='".addslashes($route_plan_route_name)."' AND emp_code='".$route_plan_emp_code."'";
			$rsroute=mysqli_query($link,$sqlroute);
			$countroute=mysqli_num_rows($rsroute);
			if($countroute<1)
			{
				$sqlroute  = "insert into route_master ";
				$sqlroute .= " SET route_code='".$route_plan_route_code."'";
				$sqlroute .= " ,route_name='".addslashes($route_plan_route_name)."'";
				$sqlroute .= " ,emp_code='".$route_plan_emp_code."'";
				$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
				//echo $sqlroute;
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
		  }
		  	$sqlchkdistributorroute="SELECT route_code FROM distributor_route_relation WHERE distributor_code='".$route_plan_distributor_code."' AND 
							route_code='".$route_plan_route_code."' AND emp_code='".$route_plan_emp_code."'";
			$rschkdistributorroute=mysqli_query($link,$sqlchkdistributorroute);
			$cntchkdistributorroute=mysqli_num_rows($rschkdistributorroute);
			if($cntchkdistributorroute ==0)
			{				
				$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$route_plan_distributor_code."',
								route_code='".$route_plan_route_code."',emp_code='".$route_plan_emp_code."', download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertdistributorroute))
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
		if(!in_array($route_plan_trans_id,$route_plan_trans_id_array))
		{
			array_push($route_plan_trans_id_array,$route_plan_trans_id);
			array_push($route_plan_emp_code_array,$route_plan_emp_code);
			array_push($route_plan_visit_date_array,$route_plan_visit_date);
			array_push($route_plan_create_date_array,$route_plan_create_date);
		}
		array_push($route_plan_route_code_array,$route_plan_route_code);
   }// End of for loop
   //print_r($route_plan_visit_date_event_array);
   
	if($flag==5){
		mysqli_query($link,"COMMIT");
		for($y=0;$y<count($route_plan_trans_id_array);$y++){  // strat of for loop of sending mail
		
		// $calendar = Calendar::factory($month, $year);
		//echo $month, $year;die;
		$calendar = buildCalendar($month, $year);
		$route_plan_visit_date_event_month=substr($route_plan_visit_date_array[$y],3,2);
		$route_plan_visit_date_event_year=substr($route_plan_visit_date_array[$y],6,4);
		
		$today = date("Y-m-d");
		$today_time = strtotime($today);
		// $sqlrouteplan="SELECT * FROM (SELECT route_code,visit_date,route_plan_trans_id,create_date,status FROM route_plan WHERE visit_date LIKE 
		// 				'%".$route_plan_visit_date_event_year.'-'.$route_plan_visit_date_event_month."%' AND emp_code='".$route_plan_emp_code_array[$y]."' 
		// 				ORDER BY `create_date` DESC) AS SAT GROUP BY 1 , 2";

		$sqlrouteplan = "
			SELECT rp.route_code,
				rp.visit_date,
				rp.route_plan_trans_id,
				rp.create_date,
				rp.status
			FROM route_plan rp
			INNER JOIN (
				SELECT route_code, visit_date, MAX(create_date) AS max_create_date
				FROM route_plan
				WHERE visit_date LIKE '%".$route_plan_visit_date_event_year.'-'.$route_plan_visit_date_event_month."%'
				AND emp_code = '".$route_plan_emp_code_array[$y]."'
				GROUP BY route_code, visit_date
			) latest 
			ON rp.route_code = latest.route_code
			AND rp.visit_date = latest.visit_date
			AND rp.create_date = latest.max_create_date
			WHERE rp.emp_code = '".$route_plan_emp_code_array[$y]."'
			";
		$rsrouteplan=mysqli_query($link,$sqlrouteplan);
		while($rowrouteplan=mysqli_fetch_assoc($rsrouteplan))
		{
			$route_plan_status=$rowrouteplan['status'];
			if($route_plan_status=='active'){
			$route_plan_visit_date_existing=$rowrouteplan['visit_date'];
			$route_plan_visit_date_existing_time=strtotime($route_plan_visit_date_existing);
			$route_plan_visit_date_existing=date('d-m-Y',strtotime($route_plan_visit_date_existing));
			$route_plan_visit_date_event_existing=substr($route_plan_visit_date_existing,0,2);
			$route_code_existing=$rowrouteplan['route_code'];
			$route_plan_create_date_existing=$rowrouteplan['create_date'];
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code_existing."'";
			$rsroutename=mysqli_query($link,$sqlroutename);
			$rowroutename=mysqli_fetch_assoc($rsroutename);
			$route_name_existing=$rowroutename['route_name'];
			
			if(route_plan_flow=='yes')
			{
				$sqlrdsname="SELECT DISTINCT RM.rds_name from customer_master CM,customer_route_emp_relation CRER,rds_master RM 
							WHERE CRER.customer_code=CM.customer_code AND CRER.route_code IN ('".$route_code_existing."') AND CRER.rds_tag=RM.rds_code ORDER BY RM.rds_name ASC ";
				$rsrdsname=mysqli_query($link,$sqlrdsname);
				$rowrdsname=mysqli_fetch_assoc($rsrdsname);
				$rds_name_existing=$rowrdsname['rds_name'];
			}
			else
			{
				$rds_name_existing='';
			}
		
			if($route_plan_visit_date_existing_time>$today_time)
			{
				$font_color='#0000FF';
			}
			elseif($route_plan_visit_date_existing_time<$today_time)
			{
				$font_color='#990000';
			}
			else
			{
				$font_color='#00CC00';
			}
			$route_code_existing=str_replace('/','-',$route_code_existing);
			/*${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_existing.$route_code_existing} = $calendar->event()
			->condition('timestamp', strtotime(date('F',strtotime($route_plan_visit_date_existing))." $route_plan_visit_date_event_existing, ".date('Y',strtotime($route_plan_visit_date_existing))))
			->title('Hello All')
			->output('<font color="'.$font_color.'">'.$rds_name_existing."<br /><br />".$route_name_existing.'</font>');
			//echo '<font color="'.$font_color.'">'.$route_name_existing.'</font>';
			
			$calendar->attach(${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_existing.$route_code_existing});	
			//array_push($route_plan_visit_date_event_array,$route_plan_visit_date_event_existing);*/
			}
		}
		
		/*for($m=0;$m<count($route_plan_visit_date_event_array);$m++){
			$route_code_existing=str_replace('/','-',$route_plan_route_code_array[$m]);
			echo 'event'.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_array[$m].$route_code_existing;
			$calendar->attach(${event.$route_plan_trans_id_array[$y].$route_plan_visit_date_event_array[$m].$route_code_existing});	
		}*/
		$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$route_plan_emp_code_array[$y]."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		//print_r($rowempname);
		if(branch_vertical_operation_wise_email=='yes'){
				$operation_type='Routeplan';
				$route_plan_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
		}
		else
		{
			$route_plan_email=ROUTEPLANMAILRECIPENTS;
		}
		//print_r($route_plan_email);
		//$route_plan_email=fetch_corresponding_emails($operation_type,$admin_Email_ID,$rds_Email_ID,$emp_Email_ID,$hierarchical_Email_ID);			
		$routeplanmailsubj="$nick_name - Route Plan of ".$emp_name." for ".date('F,Y',strtotime($route_plan_visit_date_array[$y])).' Posted On '.date('d-m-Y H:i:s').' hrs';
		//print_r( $calendar);die;
$routeplanmailbody = "<html><head><title>Route Plan</title></head>
<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
.$emp_name. "</b><br /><br />
<table style='width:100%; border-collapse:collapse;'>
    <thead>
        <tr style='padding-bottom:20px;'>
            <th colspan='5' style='text-align:center; font-size:1.5em;'>"
            .$calendar['month'] . " " . $calendar['year'].
            "</th>
        </tr>
    <tr style='text-align:left;'>";

foreach ($calendar['days'] as $day) {
    $routeplanmailbody .= "<th>".$day."</th>";
}
$routeplanmailbody .= "</tr>
    </thead>
    <tbody>";

foreach ($calendar['weeks'] as $week) {
    $routeplanmailbody .= "<tr>";
    foreach ($week as $day) {
        list($number, $current, $data) = $day;
        
        $classes = array();
        $output  = '';
        $title   = '';

        if (is_array($data)) {
            $classes = $data['classes'] ?? [];
            $title   = $data['title'] ?? '';
            $output  = empty($data['output']) 
                ? '' 
                : '<ul style="margin:0; padding:0 4px; list-style:none;"><li style="margin:0; padding:5px 0; line-height:1em;">'
                    .implode('</li><li style="margin:0; padding:5px 0; line-height:1em;">', $data['output'])
                    .'</li></ul>';
        }

        $routeplanmailbody .= "
        <td style='width:14%; height:100px; vertical-align:top; border:1px solid #CCC;'>
            <span style='display:block; padding:4px; line-height:12px; background:#EEE;' title='".(is_array($title) ? implode(' / ', $title) : $title)."'>".$number."</span>
            <div>".$output."</div>
        </td>";
    }
    $routeplanmailbody .= "</tr>";
}

$routeplanmailbody .= "
    </tbody>
</table><br /><br />Powered By aceDNS<br /></body></html>";

//echo $routeplanmailbody;
		/*$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		//echo 	$routeplanmailbody;		
		if(mail(ROUTEPLANMAILRECIPENTS, $routeplanmailsubj, $routeplanmailbody, $headers,'-facedns@coral.in'))
		{
			$flag=6;
		}
		else
		{
			//mysqli_query($link,"ROLLBACK");
			$flag=0;
			//return;
		}*/
	  }
	  $headers  = "MIME-Version: 1.0\r\n";
	  $headers .= "Content-type: text/html; charset=UTF-8\n";
	  $headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		//echo 	$routeplanmailbody;	
		$clean_email = trim(str_replace(',', '', $route_plan_email));
		if(!empty($clean_email)){
			
if(mail($route_plan_email, $routeplanmailsubj, $routeplanmailbody, $headers,$spam_filter))
		{
			$flag=6;
		}
		else
		{
			//mysqli_query($link,"ROLLBACK");
			$flag=0;
			//return;
		}
		}	
		
    }
}
 /* --------------------END QUERY FOR ROUTEPLAN------------------------------------------------------------------------------------------------------------*/
 //echo $countdatarefresh;die;
 	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/operationdb-route-plan-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	$flag=6;
if($flag==6)
{
	if($countdatarefresh >0)
	 {
		 echo 2;
	 }
	 else
	 {
	 	echo 1;
	 }
}
else
{
	echo 0;
}

function buildCalendar($month, $year) {
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dayOfWeek = date('w', $firstDayOfMonth); // 0 = Sunday
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    $weeks = [];
    $week = [];

    // Fill empty slots before the first day
    for ($i = 0; $i < $dayOfWeek; $i++) {
        $week[] = [null, false, []];
    }

    // Fill days of the month
    for ($day = 1; $day <= $numberDays; $day++) {
        $week[] = [$day, true, []];

        if (count($week) === 7) {
            $weeks[] = $week;
            $week = [];
        }
    }

    // Fill remaining slots in last week
    if (!empty($week)) {
        while (count($week) < 7) {
            $week[] = [null, false, []];
        }
        $weeks[] = $week;
    }

    return [
        'month' => date('F', $firstDayOfMonth), // e.g. August
        'year' => $year,
        'days' => $daysOfWeek,
        'weeks' => $weeks
    ];
}

mysqli_close($link);
?>