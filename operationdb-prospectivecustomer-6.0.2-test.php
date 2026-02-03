<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');
$body=str_replace('"','&quot;',$body);
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><prospective_customer_header><emp_code>4458</emp_code><trans_id>DM445820130522125651</trans_id><datetime>2013-05-22 12:56:51</datetime><latt>0.0</latt><longi>0.0</longi><address>kolkata</address><pin>700001</pin><area>Lalbazar</area><phone_no>4356789</phone_no><garrage_name>N.M motors</garrage_name><customer_name>Neel Mohan</customer_name></prospective_customer_header>
<prospective_customer_details><trans_id>DM445820130522125651</trans_id><sku_code>GFATFDX2-500ML</sku_code></prospective_customer_details>
<prospective_customer_details><trans_id>DM445820130522125651</trans_id><sku_code>GFATFTYPA-210LT</sku_code></prospective_customer_details>
</prospective_customer></root>";*/

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><location><emp_code><![CDATA[E0005]]></emp_code><trans_id><![CDATA[DCE000520171226183042]]></trans_id><latt><![CDATA[22.5644413]]></latt><longi><![CDATA[88.3567865]]></longi><date><![CDATA[2017-12-26 18:30:42]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE000520171226183042]]></trans_id><customer_code><![CDATA[DCE000520171226183042]]></customer_code><address><![CDATA[BANAMALIPUR]]></address><pin><![CDATA[700125]]></pin><area><![CDATA[RT/13]]></area><phone_no><![CDATA[9836389441]]></phone_no><remarks><![CDATA[test new]]></remarks><tagged_customer_code><![CDATA[C/0000037]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Amit Pqul]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE000520171226183042]]></trans_id><product_code><![CDATA[30845]]></product_code></prospective_customer_details></prospective_customer></root>";*/

$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211209164241]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-09 16:42:41]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211209164241]]></trans_id><customer_code><![CDATA[DCE007620211209164241]]></customer_code><address><![CDATA[KOLKATA]]></address><pin><![CDATA[743428]]></pin><area><![CDATA[RT/1269]]></area><phone_no><![CDATA[9933059485]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.By phone Discuss He\"s Shope is Close.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Dishani Builders]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211209164241]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211209164241]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211209164241]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211209164241]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211210105752]]></trans_id><latt><![CDATA[22.6882036]]></latt><longi><![CDATA[88.6580474]]></longi><date><![CDATA[2021-12-10 10:57:52]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211210105752]]></trans_id><customer_code><![CDATA[DCE007620211210105752]]></customer_code><address><![CDATA[DENGANGA]]></address><pin><![CDATA[743423]]></pin><area><![CDATA[Denganga]]></area><phone_no><![CDATA[9836996663]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss,And Branding,And Present Scime Discuss, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Sumanglam Concert Products Pvt Ltd]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211210105752]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210105752]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210105752]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210105752]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211210115028]]></trans_id><latt><![CDATA[22.7150645]]></latt><longi><![CDATA[88.5011918]]></longi><date><![CDATA[2021-12-10 11:50:28]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211210115028]]></trans_id><customer_code><![CDATA[DCE007620211210115028]]></customer_code><address><![CDATA[KAZIPARA]]></address><pin><![CDATA[700124]]></pin><area><![CDATA[RT/1242]]></area><phone_no><![CDATA[9143524282]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Krishnachura Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211210115028]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210115028]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210115028]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211210115028]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211211130243]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-11 13:02:43]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211211130243]]></trans_id><customer_code><![CDATA[DCE007620211211130243]]></customer_code><address><![CDATA[KANKRGA]]></address><pin><![CDATA[743424]]></pin><area><![CDATA[RT/1262]]></area><phone_no><![CDATA[8448440911]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Mondal Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211211130243]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211211130243]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211211130243]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211211130243]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211213120614]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-13 12:06:14]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211213120614]]></trans_id><customer_code><![CDATA[DCE007620211213120614]]></customer_code><address><![CDATA[CHAMPAPUKUR]]></address><pin><![CDATA[743291]]></pin><area><![CDATA[Champapukur]]></area><phone_no><![CDATA[9153159732]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[M/s Abdul Traders]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211213120614]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211213120614]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211213120614]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211213120614]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211215110722]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-15 11:07:22]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211215110722]]></trans_id><customer_code><![CDATA[DCE007620211215110722]]></customer_code><address><![CDATA[MATIA (ARBELIA)]]></address><pin><![CDATA[743728]]></pin><area><![CDATA[RT/1272]]></area><phone_no><![CDATA[8777626365]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss,Fe 550+Sd Quality, Integrated Palant, Captive Power Plan, Flexibility Forcxtra Ductile Property, Better Tensile Straight etc,And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Ankita Builders]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211215110722]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215110722]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215110722]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215110722]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211215165757]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-15 16:57:57]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211215165757]]></trans_id><customer_code><![CDATA[DCE007620211215165757]]></customer_code><address><![CDATA[MATIA]]></address><pin><![CDATA[743437]]></pin><area><![CDATA[RT/1272]]></area><phone_no><![CDATA[9733721778]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[New Mondal Iron]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211215165757]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215165757]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215165757]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215165757]]></trans_id><product_code><![CDATA[12001]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215165757]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211215183136]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-15 18:31:36]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211215183136]]></trans_id><customer_code><![CDATA[DCE007620211215183136]]></customer_code><address><![CDATA[BADURIA]]></address><pin><![CDATA[743401]]></pin><area><![CDATA[RT/2966]]></area><phone_no><![CDATA[9732599860]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[M/s Ashirbad Builders & Hardware]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211215183136]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215183136]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215183136]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215183136]]></trans_id><product_code><![CDATA[12001]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211215183136]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211216095909]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-16 09:59:09]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211216095909]]></trans_id><customer_code><![CDATA[DCE007620211216095909]]></customer_code><address><![CDATA[KHARIBARI]]></address><pin><![CDATA[700128]]></pin><area><![CDATA[RT/1245]]></area><phone_no><![CDATA[8017029790]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Kamakar Hardware]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211216095909]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216095909]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216095909]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216095909]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211216100542]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-16 10:05:42]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211216100542]]></trans_id><customer_code><![CDATA[DCE007620211216100542]]></customer_code><address><![CDATA[KHARIBARI]]></address><pin><![CDATA[700128]]></pin><area><![CDATA[RT/1245]]></area><phone_no><![CDATA[9800312483]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Ehshan Iron Stores]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211216100542]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216100542]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216100542]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216100542]]></trans_id><product_code><![CDATA[12001]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216100542]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211216115848]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-16 11:58:48]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211216115848]]></trans_id><customer_code><![CDATA[DCE007620211216115848]]></customer_code><address><![CDATA[PACURIA]]></address><pin><![CDATA[743502]]></pin><area><![CDATA[RT/1277]]></area><phone_no><![CDATA[9874235559]]></phone_no><remarks><![CDATA[New Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Maa Laxmi Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211216115848]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216115848]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216115848]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216115848]]></trans_id><product_code><![CDATA[12001]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211216130533]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-16 13:05:33]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211216130533]]></trans_id><customer_code><![CDATA[DCE007620211216130533]]></customer_code><address><![CDATA[ANADAPUR]]></address><pin><![CDATA[700162]]></pin><area><![CDATA[RT/1251]]></area><phone_no><![CDATA[9831157277]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Palash Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211216130533]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216130533]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216130533]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216130533]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211216150831]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-16 15:08:31]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211216150831]]></trans_id><customer_code><![CDATA[DCE007620211216150831]]></customer_code><address><![CDATA[RAJARHAT]]></address><pin><![CDATA[700136]]></pin><area><![CDATA[RT/1249]]></area><phone_no><![CDATA[9836903788]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Papular Steel Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211216150831]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216150831]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216150831]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216150831]]></trans_id><product_code><![CDATA[12001]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211216150831]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211217172752]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-17 17:27:52]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211217172752]]></trans_id><customer_code><![CDATA[DCE007620211217172752]]></customer_code><address><![CDATA[KADAMOGACHI]]></address><pin><![CDATA[700125]]></pin><area><![CDATA[RT/1243]]></area><phone_no><![CDATA[9830332496]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Nargis Supplier Agency]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211217172752]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211217172752]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211217172752]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211217172752]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211220113036]]></trans_id><latt><![CDATA[22.8067109]]></latt><longi><![CDATA[88.609215]]></longi><date><![CDATA[2021-12-20 11:30:36]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211220113036]]></trans_id><customer_code><![CDATA[DCE007620211220113036]]></customer_code><address><![CDATA[GUMA]]></address><pin><![CDATA[743704]]></pin><area><![CDATA[RT/4138]]></area><phone_no><![CDATA[9832588070]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[New Loknath Enterprise]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211220113036]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220113036]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220113036]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220113036]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211220120039]]></trans_id><latt><![CDATA[22.8234713]]></latt><longi><![CDATA[88.5573419]]></longi><date><![CDATA[2021-12-20 12:00:39]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211220120039]]></trans_id><customer_code><![CDATA[DCE007620211220120039]]></customer_code><address><![CDATA[GUMA]]></address><pin><![CDATA[743702]]></pin><area><![CDATA[RT/4138]]></area><phone_no><![CDATA[9933843422]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Protap Ghosh]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211220120039]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220120039]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220120039]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220120039]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211220122219]]></trans_id><latt><![CDATA[22.8246606]]></latt><longi><![CDATA[88.5503386]]></longi><date><![CDATA[2021-12-20 12:22:19]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211220122219]]></trans_id><customer_code><![CDATA[DCE007620211220122219]]></customer_code><address><![CDATA[GUMA(RAJIBPUR)]]></address><pin><![CDATA[743702]]></pin><area><![CDATA[RT/4138]]></area><phone_no><![CDATA[9830777090]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Radhamadhab Trading Co]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211220122219]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220122219]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220122219]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220122219]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer><prospective_customer><location><emp_code><![CDATA[E0076]]></emp_code><trans_id><![CDATA[DCE007620211220134851]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2021-12-20 13:48:51]]></date></location><prospective_customer_header><trans_id><![CDATA[DCE007620211220134851]]></trans_id><customer_code><![CDATA[DCE007620211220134851]]></customer_code><address><![CDATA[BIRA]]></address><pin><![CDATA[743234]]></pin><area><![CDATA[Bira]]></area><phone_no><![CDATA[9836439560]]></phone_no><remarks><![CDATA[Dealer Visit And Material Discuss, And Branding, And About New Material Order Discretion.]]></remarks><tagged_customer_code><![CDATA[]]></tagged_customer_code><cust_type><![CDATA[R]]></cust_type><customer_name><![CDATA[Raisha Rod & Cement Center]]></customer_name></prospective_customer_header><prospective_customer_details><trans_id><![CDATA[DCE007620211220134851]]></trans_id><product_code><![CDATA[12004]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220134851]]></trans_id><product_code><![CDATA[12005]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220134851]]></trans_id><product_code><![CDATA[12006]]></product_code></prospective_customer_details><prospective_customer_details><trans_id><![CDATA[DCE007620211220134851]]></trans_id><product_code><![CDATA[12003]]></product_code></prospective_customer_details></prospective_customer></root>";

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}

$emp_code = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*EMP_CODE";
$trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*TRANS_ID";
$prospect_date = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*DATE";
$latt = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LATT";
$longi = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LONGI";

$prospectiveheader_trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*TRANS_ID";
$prospectiveheader_customer_code = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUSTOMER_CODE";
$customer_name = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUSTOMER_NAME";
$address = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*ADDRESS";
$pin = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*PIN";
$area = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*AREA";
$phone_no = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*PHONE_NO";
$remarks="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*REMARKS";
$tagged_customer_code="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*TAGGED_CUSTOMER_CODE";
$cust_type="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUST_TYPE";

$prospectivedetails_trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS*TRANS_ID";
$prospectivedetails_product_code = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS*PRODUCT_CODE";

$prospective_customer_array=array();
$prospective_customer_details_array=array();

$counter = 0;
$counterdetails=0;

class xml_customer_header{
    var $emp_code, $trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$prospectiveheader_customer_code,$customer_name,$address,$pin,$area,$phone_no,$remarks,$tagged_customer_code,$cust_type;
}
class xml_customer_details{
	var $prospectivedetails_trans_id,$prospectivedetails_product_code;
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
    global $current_tag, $emp_code,$trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$prospectiveheader_customer_code,$customer_name,$address,$pin,$area,$phone_no,$remarks,$tagged_customer_code,$cust_type,$counter, $counterdetails,$prospectivedetails_trans_id,$prospectivedetails_product_code,$prospective_customer_array,$prospective_customer_details_array;
	echo $current_tag.'<br />';
	echo $data;
	if(substr($current_tag,0,26)=='*ROOT*PROSPECTIVE_CUSTOMER')
	{
		switch($current_tag){
			case $emp_code:
				$prospective_customer_array[$counter] = new xml_customer_header();
				$prospective_customer_array[$counter]->emp_code = $data;
				break;
			case $trans_id:
				$prospective_customer_array[$counter]->trans_id = $data;
				break;
			case $prospect_date:
				$prospective_customer_array[$counter]->prospect_date = $data;
				break;	
			case $latt:
				$prospective_customer_array[$counter]->latt = $data;
				break;
			case $longi:
				$prospective_customer_array[$counter]->longi = $data;
				break;
			case $prospectiveheader_trans_id:
				$prospective_customer_array[$counter]->prospectiveheader_trans_id = $data;
				break;
			case $prospectiveheader_customer_code:
				$prospective_customer_array[$counter]->prospectiveheader_customer_code = $data;
				break;
			case $address:
				$prospective_customer_array[$counter]->address = $data;
				break;
			case $pin:
				$prospective_customer_array[$counter]->pin = $data;
				break;
			case $area:
				$prospective_customer_array[$counter]->area = $data;
				break;
			case $phone_no:
				$prospective_customer_array[$counter]->phone_no = $data;
				break;
			case $remarks:
				$prospective_customer_array[$counter]->remarks = $data;
				break;
			case $tagged_customer_code:
				$prospective_customer_array[$counter]->tagged_customer_code = $data;
				break;	
			case $cust_type:
				$prospective_customer_array[$counter]->cust_type = $data;
				break;	
			case $customer_name:
				$prospective_customer_array[$counter]->customer_name = $data;
				$counter++;
				break;		
		}
	}
	if(substr($current_tag,0,55)=='*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_DETAILS')
	{
		echo $current_tag.'<br />';
		echo $data.'<br />';
		switch($current_tag){
			case $prospectivedetails_trans_id:
				$prospective_customer_details_array[$counterdetails] = new xml_customer_details();
				$prospective_customer_details_array[$counterdetails]->prospectivedetails_trans_id = $data;
				break;
			case $prospectivedetails_product_code:
				$prospective_customer_details_array[$counterdetails]->prospectivedetails_product_code = $data;
				$counterdetails++;
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
/* ------------------------------------------------START QUERY FOR PROSPECTIVE CUSTOMER---------------------------------------------------------------------*/
$prospectivecustomer_array_trans_id=array();
$prospectivecustomerheader_array_mailbody=array();
$prospectivecustomerdate_array_mailbody=array();
$prospectivecustomerdetails_array_mailbody=array();
$prospectivecustomeremp_array_mailbody=array();
$prospectivecustomertansid_array_mailbody=array();
$remark_array_mailbody=array();

if(count($prospective_customer_array)>0)
{
	$prospective_array_trans_id=array();
	for($x=0;$x<count($prospective_customer_array);$x++){
		$emp_code=$prospective_customer_array[$x]->emp_code;
		$trans_id=$prospective_customer_array[$x]->trans_id;
		$prospect_date=$prospective_customer_array[$x]->prospect_date;
		$latt=$prospective_customer_array[$x]->latt;
		$longi=$prospective_customer_array[$x]->longi;
		$prospectiveheader_trans_id=$prospective_customer_array[$x]->prospectiveheader_trans_id;
		$prospectiveheader_customer_code=$prospective_customer_array[$x]->prospectiveheader_customer_code;
		$address=$prospective_customer_array[$x]->address;
		$pin=$prospective_customer_array[$x]->pin;
		$area=$prospective_customer_array[$x]->area;
		$phone_no=$prospective_customer_array[$x]->phone_no;
		$tagged_customer_code=$prospective_customer_array[$x]->tagged_customer_code;
		$cust_type=$prospective_customer_array[$x]->cust_type;
		$customer_name=$prospective_customer_array[$x]->customer_name;	
		$remarks=$prospective_customer_array[$x]->remarks;
		if(strtoupper($cust_type)=='D')
		{
			$cust_type_insert='DISTRIBUTOR';
		}
		if($cust_type=='R')
		{
			$cust_type_insert='RETAILER';
		}	
		
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
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check prospective location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0)
		{
			if(!in_array($trans_id,$prospective_array_trans_id))
			{
				array_push($prospective_array_trans_id,$trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update prospective customer location: ".$sqlupdateorlocation);
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
			// create the data for prospective customer header table date field , by checking the current date and time and the actual date and time of occurrence
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding prospective customer
			$sqlinsertprolocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$prospect_date."',
									updatetime='".$location_date."'"; 

			//For Insert into the prospective customer header table for new trans id regarding prospective customer
			echo $sqlinsertproheader="INSERT INTO prospective_customer_header SET trans_id='".$prospectiveheader_trans_id."',
									customer_code='".addslashes($prospectiveheader_customer_code)."',
									customer_name='".addslashes($customer_name)."',
									address='".addslashes($address)."',
									pin='".$pin."',
									area='".$area."',
									phone_no='".$phone_no."',
									remarks='".$remarks."',
									cust_type='".$cust_type."',
									tagged_customer_code='".$tagged_customer_code."'";
			
			if(mysqli_query($link,$sqlinsertprolocation) && mysqli_query($link,$sqlinsertproheader))
				{
				   $flag=5;
				  echo  $sqlinsertprospectcustomer="INSERT INTO prospective_customer_master SET 
												   customer_code ='".$prospectiveheader_trans_id."',
												   emp_code ='".$emp_code."',
												   customer_name				='".addslashes($customer_name)."',
												   address						='".addslashes($address)."',
												   pin							='".$pin."',
												   area							='".$area."',
												   phone_no						='".$phone_no."',
												   cust_type					='".$cust_type."',
												   tagged_customer_code         ='".$tagged_customer_code."'";
					if(mysqli_query($link,$sqlinsertprospectcustomer))
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
				// Add new route to route master
				/*if(substr($area,0,1)=='N'){
				 $sqlroute="SELECT route_name FROM route_master WHERE route_code='".$area."'";
				 $rsroute=mysqli_query($link,$sqlroute);
				 $countroute=mysqli_num_rows($rsroute);
				 if($countroute<1)
				 {
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$area."'";
					$sqlroute .= " ,dns_route_code=''";
					$sqlroute .= " ,route_name='".$area_name."'";
					$sqlroute .= " , emp_code='".$emp_code."'";
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
				
				//Regarding Email Sending
				$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				$branch_code=$rowempname['branch_code'];
				$vertical_value=$rowempname['vertical_value'];
		
				if(branch_vertical_operation_wise_email=='yes'){
					$operation_type='Prospectivecustomeradd';
					$prospect_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				}
				else
				{
					if($nick_name=='RUPA')
					{
					 $prospect_email='';
					}
					else
					{
					 $prospect_email=PROSPECTEMAILRECIPENTS;
					}
				}
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$tagged_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$tagged_customer_name=$rowcustomername['customer_name'];
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$area."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
				
				//For constructing the email body for PROSPECTIVE CUSTOMER HEADER if Customer addition has performed
				$prospectivecustomermailbody = "<html><head><title>Prospective Customer</title></head>
							<body>Auto generated mail for <b>".$nick_name." aceDNS</b> mobile application from<b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue cell cellpadding=0 cellspacing=4>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center' colspan='2'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Prospect Name</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:250px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Tagged To Customer</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white' colspan='2'>
							<span style='font-family:Arial CE;font-size:10pt' >
							".strtoupper($customer_name)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($prospect_date))."</span>&nbsp;</td>
							<td style='width:250px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$tagged_customer_name."</span>&nbsp;</td>
							</tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Address</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Pin</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Area</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Type</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							".strtoupper($address)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$pin."</span>&nbsp;</td>
							<td style='width:165px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($route_name)."</span>&nbsp;</td>
							<td style='width:165px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($cust_type_insert)."</span>&nbsp;</td>
							</tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Phone no</span></strong></th></tr>
							<tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							+91-".$phone_no."</span>&nbsp;</td></tr></table>";
							
							array_push($prospectivecustomerheader_array_mailbody,$prospectivecustomermailbody);
							$remarkmailbody="Remark: <b>".strtoupper($remarks)."</b>";
							array_push($remark_array_mailbody,$remarkmailbody);
							array_push($prospectivecustomerdate_array_mailbody,$prospect_date);
							array_push($prospectivecustomeremp_array_mailbody,$emp_name);
							array_push($prospectivecustomertansid_array_mailbody,$trans_id);
					}//End of else
		 }// End for loop
	}//End of if
	//For Insert into the Prospective Customer Details table for new trans id
		if(count($prospective_customer_details_array)>0)
		{
			for($i=0;$i<count($prospective_customer_details_array);$i++){

				$prospectivedetails_trans_id=$prospective_customer_details_array[$i]->prospectivedetails_trans_id;
				$prospectivedetails_product_code=$prospective_customer_details_array[$i]->prospectivedetails_product_code;
				
					if(no_of_filter==1){
						$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==2){
						$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM product_master PM,product_group_master PGM 
											WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==3){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc FROM 
											product_master PM,product_group_master PGM,product_sub_group_master PSGM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code 
											AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					if(no_of_filter==4){
						$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc
											FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
											WHERE PM.product_group_code=PGM.product_group_code AND PM.product_sub_group_code=PSGM.product_sub_group_code
											AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$prospectivedetails_product_code."'";
					}
					$rsproductdetails=mysqli_query($link,$sqlproductdetails);
					$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
					$prod_desc=$rowproductdetails['prod_desc'];
					$product_group_name=$rowproductdetails['product_group_name'];
					$product_brand_name=$rowproductdetails['product_brand_name'];
					$product_sub_group_name=$rowproductdetails['product_sub_group_name'];

					
					if(!in_array($prospectivedetails_trans_id,$prospective_array_trans_id))
					{
						echo $sqlinsertprodetails="INSERT INTO prospective_customer_details SET trans_id='".$prospectivedetails_trans_id."',
												product_code 	='".$prospectivedetails_product_code."'";
						if(mysqli_query($link,$sqlinsertprodetails))
						{
							$flag=5;
						}
						else
						{
							mysqli_query($link,"ROLLBACK");
							echo $flag=0;
							return;
						}
						
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

						//For constructing the email body for PROSPECTIVE CUTOMER DETAILS
						 ${a.$prospectivedetails_trans_id} .="
								<tr>".$product_details_TD."</tr>";
					}
			}//End for loop
		}
		//End Insert into the Prospective Customer Details table for new trans id
		//For sending email for Prospective customer
		if(count($prospectivecustomerheader_array_mailbody)>0)
		{
			for($countarr=0;$countarr<count($prospectivecustomerheader_array_mailbody);$countarr++)
			{
				if (strpos($prospectivecustomerheader_array_mailbody[$countarr],'Garage Name') !== false) {
					$table_header_variable='Mechanic visited by';
				}
				else
				{
					$table_header_variable='New prospect visited by ';
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

				$prospectivecustomeremailsubj=$table_header_variable.$prospectivecustomeremp_array_mailbody[$countarr]." on ".date('d-m-Y',strtotime($prospectivecustomerdate_array_mailbody[$countarr]))." @".date('H:i:s',strtotime($prospectivecustomerdate_array_mailbody[$countarr])).' hrs.';
				$prospectivecustomermailbody = $prospectivecustomerheader_array_mailbody[$countarr]."<br><table border=1 style=background-color:AliceBlue>
							<tr>".$product_details_TH."</tr>".${a.$prospectivecustomertansid_array_mailbody[$countarr]}."</table>
				<br><br><table>".$remark_array_mailbody[$countarr]."</table><br /><br /><br />Powered By <b>aceDNS</b><br></body></html>";
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n" .
							'X-Mailer: PHP/' . phpversion();
				if(mail($prospect_email, $prospectivecustomeremailsubj, $prospectivecustomermailbody, $headers,$spam_filter))
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

 /* --------------------END QUERY FOR PROSPECTIVE CUSTOMER---------------------------------------------------------------------------------------------*/
//$countdatarefresh=returndatarefresh($emp_code);
if($flag==5)
{
	 mysqli_query($link,"COMMIT");
	 /*if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {*/
	 	echo $flag=1;
	 //}
}
if($flag==6)
{
	mysqli_query($link,"COMMIT");
	/* if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else

	 {*/
	 	echo $flag=1;
	 //}
}
?>
