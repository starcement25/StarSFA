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

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0077]]></emp_code><trans_id><![CDATA[SOE007720190125130832]]></trans_id><latt><![CDATA[22.5643486]]></latt><longi><![CDATA[88.3568882]]></longi><date><![CDATA[2019-01-25 13:08:32]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE007720190125130832]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12017]]></PROD_CODE><IMEI><![CDATA[864940044063941]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-01-25]]></STOCK_OUT_DATE></STOCK_OUT_DATA></STOCK_OUT_DETAILS></root>";*/

$body="<?xml version='1.0' encoding='UTF-8'?><root><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190210205042]]></trans_id><latt><![CDATA[22.6183993]]></latt><longi><![CDATA[88.4128585]]></longi><date><![CDATA[2019-02-10 20:50:42]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190210205042]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12011]]></PROD_CODE><IMEI><![CDATA[864279045899650]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-10]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190210205042]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12011]]></PROD_CODE><IMEI><![CDATA[864279045899650]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-10]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190210205058]]></trans_id><latt><![CDATA[22.6184253]]></latt><longi><![CDATA[88.4127082]]></longi><date><![CDATA[2019-02-10 20:50:58]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190210205058]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12011]]></PROD_CODE><IMEI><![CDATA[864279045899650]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-10]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190211204624]]></trans_id><latt><![CDATA[22.6184073]]></latt><longi><![CDATA[88.4126985]]></longi><date><![CDATA[2019-02-11 20:46:24]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190211204624]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-11]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190211204808]]></trans_id><latt><![CDATA[22.6184111]]></latt><longi><![CDATA[88.4127007]]></longi><date><![CDATA[2019-02-11 20:48:09]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190211204808]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-11]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190211204903]]></trans_id><latt><![CDATA[22.6184863]]></latt><longi><![CDATA[88.4127114]]></longi><date><![CDATA[2019-02-11 20:49:03]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190211204903]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12021]]></PROD_CODE><IMEI><![CDATA[860980047666997]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-11]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190211204928]]></trans_id><latt><![CDATA[22.6183952]]></latt><longi><![CDATA[88.4126988]]></longi><date><![CDATA[2019-02-11 20:49:28]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190211204928]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12021]]></PROD_CODE><IMEI><![CDATA[860980047666997]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-11]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190211205950]]></trans_id><latt><![CDATA[22.6184164]]></latt><longi><![CDATA[88.4127358]]></longi><date><![CDATA[2019-02-11 20:59:50]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190211205950]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-11]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212112636]]></trans_id><latt><![CDATA[22.6183894]]></latt><longi><![CDATA[88.412706]]></longi><date><![CDATA[2019-02-12 11:26:36]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212112636]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212112704]]></trans_id><latt><![CDATA[22.6184016]]></latt><longi><![CDATA[88.4127093]]></longi><date><![CDATA[2019-02-12 11:27:04]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212112704]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212112725]]></trans_id><latt><![CDATA[22.618414]]></latt><longi><![CDATA[88.412691]]></longi><date><![CDATA[2019-02-12 11:27:25]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212112725]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212112831]]></trans_id><latt><![CDATA[22.618414]]></latt><longi><![CDATA[88.4126922]]></longi><date><![CDATA[2019-02-12 11:28:31]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212112831]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212113105]]></trans_id><latt><![CDATA[22.6184668]]></latt><longi><![CDATA[88.4127577]]></longi><date><![CDATA[2019-02-12 11:31:05]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212113105]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212113123]]></trans_id><latt><![CDATA[22.6184691]]></latt><longi><![CDATA[88.412781]]></longi><date><![CDATA[2019-02-12 11:31:23]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212113123]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212113533]]></trans_id><latt><![CDATA[22.6184751]]></latt><longi><![CDATA[88.4128072]]></longi><date><![CDATA[2019-02-12 11:35:33]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212113533]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212120028]]></trans_id><latt><![CDATA[22.5643717]]></latt><longi><![CDATA[88.3568789]]></longi><date><![CDATA[2019-02-12 12:00:28]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212120028]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212120123]]></trans_id><latt><![CDATA[22.5647835]]></latt><longi><![CDATA[88.3583852]]></longi><date><![CDATA[2019-02-12 12:01:23]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212120123]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS><STOCK_OUT_DETAILS><location><emp_code><![CDATA[E0186]]></emp_code><trans_id><![CDATA[SOE018620190212120228]]></trans_id><latt><![CDATA[22.5643714]]></latt><longi><![CDATA[88.3568798]]></longi><date><![CDATA[2019-02-12 12:02:28]]></date></location><STOCK_OUT_DATA><STOCK_OUT_ID><![CDATA[SOE018620190212120228]]></STOCK_OUT_ID><PROD_CODE><![CDATA[12014]]></PROD_CODE><IMEI><![CDATA[864914042880317]]></IMEI><STOCK_OUT_DATE><![CDATA[2019-02-12]]></STOCK_OUT_DATE><STOCK_OUT_QTY><![CDATA[]]></STOCK_OUT_QTY></STOCK_OUT_DATA></STOCK_OUT_DETAILS></root>";

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*STOCK_OUT_DETAILS*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*STOCK_OUT_DETAILS*LOCATION*TRANS_ID";
$location_latt = "*ROOT*STOCK_OUT_DETAILS*LOCATION*LATT";
$location_longi = "*ROOT*STOCK_OUT_DETAILS*LOCATION*LONGI";
$location_date="*ROOT*STOCK_OUT_DETAILS*LOCATION*DATE";

$stock_out_id = "*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*STOCK_OUT_ID";
$prod_code="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*PROD_CODE";
$IMEI="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*IMEI";
$stock_out_date="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*STOCK_OUT_DATE";
$stock_out_qty="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*STOCK_OUT_QTY";

$stock_out_array=array();
$stock_out_details_array=array();
$counter = 0;
$counterstockout=0;
class xml_stock_out{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_stock_out_details{
	var $stock_out_id,$prod_code,$IMEI,$stock_out_date,$stock_out_qty;
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
    global $current_tag,$counter,$counterstockout,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$stock_out_id,$prod_code,$IMEI,$stock_out_date,$stock_out_qty,$stock_out_array,$stock_out_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,23)=='*ROOT*STOCK_OUT_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$stock_out_array[$counter] = new xml_stock_out();
				$stock_out_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$stock_out_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$stock_out_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$stock_out_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$stock_out_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,38)=='*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $stock_out_id:
					$stock_out_details_array[$counterstockout] = new xml_stock_out_details();
					$stock_out_details_array[$counterstockout]->stock_out_id = $data;
					break;
				case $prod_code:
					$stock_out_details_array[$counterstockout]->prod_code = $data;
					break;
				case $IMEI:
					$stock_out_details_array[$counterstockout]->IMEI = $data;
					break;
				case $stock_out_date:
					$stock_out_details_array[$counterstockout]->stock_out_date = $data;
					break;
			   case $stock_out_qty:
					$stock_out_details_array[$counterstockout]->stock_out_qty = $data;
					$counterstockout++;
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
/* --------------------START QUERY FOR STOCK OUT ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$stock_out_array_trans_id=array();
if(count($stock_out_array)>0)
{
		for($x=0;$x<count($stock_out_array);$x++){
			$location_emp_code=$stock_out_array[$x]->location_emp_code;
			$location_trans_id=$stock_out_array[$x]->location_trans_id;
			$location_latt=$stock_out_array[$x]->location_latt;
			$location_longi=$stock_out_array[$x]->location_longi;
			$location_date=$stock_out_array[$x]->location_date;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
	
			//For checking that trans id exist or not for notes info
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for notes info
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$stock_out_array_trans_id))
				{
					array_push($stock_out_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update notes info location: ".$sqlupdateorlocation);
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
			$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
			$branch_code=$rowempname['branch_code'];
			$vertical_value=$rowempname['vertical_value'];
			
			$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
			$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
			//For Insert into the location table for new trans id regarding market feedback stock audit
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
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
		}
	 }//End of for loop
	 if(count($stock_out_details_array)>0)
		{
			for($i=0;$i<count($stock_out_details_array);$i++){
				
				$sqlselcustomerfromemp="SELECT customer_code FROM customer_route_emp_relation WHERE emp_code='".$emp_code."' AND acedns='Y'";
				$rsselcustomerfromemp=mysqli_query($link,$sqlselcustomerfromemp);
				$rowselcustomerfromemp=mysqli_fetch_assoc($rsselcustomerfromemp);
				$customer_code_fromemp=$rowselcustomerfromemp['customer_code'];

				$stock_out_id=$stock_out_details_array[$i]->stock_out_id;
				$prod_code=$stock_out_details_array[$i]->prod_code;
				$IMEI=$stock_out_details_array[$i]->IMEI;
				$stock_out_date=$stock_out_details_array[$i]->stock_out_date;
				$stock_out_qty=$stock_out_details_array[$i]->stock_out_qty;
				if(!in_array($stock_out_id,$stock_out_array_trans_id))
				{
					$sqlinsertstockout="INSERT INTO stock_out_details SET stock_out_id ='".$stock_out_id."',
										  prod_code     ='".$prod_code."',
										  IMEI 			='".$IMEI."',
										  stock_out_date ='".$stock_out_date."',
										  customer_code='".$customer_code_fromemp."',
										  stock_out_qty ='".$stock_out_qty."'";											  
					if(mysqli_query($link,$sqlinsertstockout))
					{
						$flag=5;
						if($IMEI!='')
						{
						$sqlupdatestockout="UPDATE customer_product_billing SET stock_out_date='".$stock_out_date."' WHERE 
											prod_code   ='".$prod_code."' AND IMEI='".$IMEI."'";
						if(mysqli_query($link,$sqlupdatestockout))
						{
							$flag=5;
							//For stock out qty updation
							$sqlselcustomer="SELECT customer_code FROM customer_product_billing WHERE prod_code='".$prod_code."' AND 
											IMEI='".$IMEI."'";
							$rsselcustomer=mysqli_query($link,$sqlselcustomer);
							$rowselcustomer=mysqli_fetch_assoc($rsselcustomer);
							$customer_code=$rowselcustomer['customer_code'];
							if($customer_code ==$customer_code_fromemp)
							{
								$sqlselstkoutupdationrow="SELECT stock_out_qty,sl_no FROM stock_balance_details WHERE customer_code='".$customer_code."' 
														AND prod_code   ='".$prod_code."' AND (billed_qty-stock_out_qty) >0 ORDER BY download_time ASC 
														LIMIT 0,1";
								$rsselstkoutupdationrow=mysqli_query($link,$sqlselstkoutupdationrow);
								$rowselstkoutupdationrow=mysqli_fetch_assoc($rsselstkoutupdationrow);
								$stock_out_qty=$rowselstkoutupdationrow['stock_out_qty'];
								$sl_no=$rowselstkoutupdationrow['sl_no'];
								$stock_out_qty=$stock_out_qty+1;
								$sqlupdatestockbalance="UPDATE stock_balance_details SET stock_out_qty='".$stock_out_qty."' WHERE sl_no='".$sl_no."'";
								if(mysqli_query($link,$sqlupdatestockbalance))
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
								$sqlupdatestockbalance="UPDATE stock_balance_details SET stock_out_qty=(stock_out_qty+1) WHERE 
													customer_code='".$customer_code_fromemp."' AND prod_code='".$prod_code."' AND billed_qty > 0";
								if(mysqli_query($link,$sqlupdatestockbalance))
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
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}
			}
		}
}
 /* --------------------END QUERY FOR STOCK OUT--------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
     $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url =APICALLLOGURL."/operationdb-stock_out-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
