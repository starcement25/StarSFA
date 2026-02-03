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
  $spam_filter='-facedns@coral.in';
}


$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0010]]></emp_code><trans_id><![CDATA[NE001020160425102210]]></trans_id><latt><![CDATA[22.720395]]></latt><longi><![CDATA[87.68254]]></longi><date><![CDATA[2016-04-25 10:22:10]]></date></location><new_customer_details><customer_code><![CDATA[NE001020160425102210]]></customer_code><customer_name><![CDATA[Netta Pati]]></customer_name><Phone_no><![CDATA[9999999999]]></Phone_no><pin_code><![CDATA[999999]]></pin_code><area><![CDATA[NRT/E001220160404092845]]></area><area_name><![CDATA[KHARAR]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[KHARAR]]></address></new_customer_details></new_customer></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160625122123]]></trans_id><latt><![CDATA[12.9957983]]></latt><longi><![CDATA[77.7646817]]></longi><date><![CDATA[2016-06-25 12:21:23]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160625122123]]></customer_code><customer_name><![CDATA[Popular Medical]]></customer_name><Phone_no><![CDATA[9886532642#7347]]></Phone_no><pin_code><![CDATA[560067]]></pin_code><area><![CDATA[RT/227]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[KADUGODI]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160625122351]]></trans_id><latt><![CDATA[12.9958033]]></latt><longi><![CDATA[77.7646367]]></longi><date><![CDATA[2016-06-25 12:23:51]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160625122351]]></customer_code><customer_name><![CDATA[Chowdeshwari Medical]]></customer_name><Phone_no><![CDATA[9845557697#3150]]></Phone_no><pin_code><![CDATA[560067]]></pin_code><area><![CDATA[RT/227]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[KADGOUDI]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160625122603]]></trans_id><latt><![CDATA[12.995805]]></latt><longi><![CDATA[77.7646217]]></longi><date><![CDATA[2016-06-25 12:26:03]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160625122603]]></customer_code><customer_name><![CDATA[Pk Department]]></customer_name><Phone_no><![CDATA[9856321484#7551]]></Phone_no><pin_code><![CDATA[560067]]></pin_code><area><![CDATA[RT/227]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[KADGOUDI]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160625140111]]></trans_id><latt><![CDATA[12.9588103]]></latt><longi><![CDATA[77.6591604]]></longi><date><![CDATA[2016-06-25 14:01:11]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160625140111]]></customer_code><customer_name><![CDATA[24 Health Medical]]></customer_name><Phone_no><![CDATA[9343268956#1512]]></Phone_no><pin_code><![CDATA[560067]]></pin_code><area><![CDATA[RT/228]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[WHITEFIELD]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160625141859]]></trans_id><latt><![CDATA[12.9588103]]></latt><longi><![CDATA[77.6591604]]></longi><date><![CDATA[2016-06-25 14:18:59]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160625141859]]></customer_code><customer_name><![CDATA[Heera Department Store]]></customer_name><Phone_no><![CDATA[9916506343#3881]]></Phone_no><pin_code><![CDATA[560067]]></pin_code><area><![CDATA[RT/227]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[KADGOUDI]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627152155]]></trans_id><latt><![CDATA[13.0353771]]></latt><longi><![CDATA[77.5969571]]></longi><date><![CDATA[2016-06-27 15:21:55]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627152155]]></customer_code><customer_name><![CDATA[Varu Medical]]></customer_name><Phone_no><![CDATA[9900232434#3526]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627152447]]></trans_id><latt><![CDATA[13.0353706]]></latt><longi><![CDATA[77.5969705]]></longi><date><![CDATA[2016-06-27 15:24:47]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627152447]]></customer_code><customer_name><![CDATA[Purana Novelist]]></customer_name><Phone_no><![CDATA[7204229057#1605]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627152750]]></trans_id><latt><![CDATA[13.0353105]]></latt><longi><![CDATA[77.5969307]]></longi><date><![CDATA[2016-06-27 15:27:50]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627152750]]></customer_code><customer_name><![CDATA[MÃ ruti Medicals]]></customer_name><Phone_no><![CDATA[9756238521#3652]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627153132]]></trans_id><latt><![CDATA[13.0353846]]></latt><longi><![CDATA[77.5969646]]></longi><date><![CDATA[2016-06-27 15:31:32]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627153132]]></customer_code><customer_name><![CDATA[Sr Medical]]></customer_name><Phone_no><![CDATA[9449109099#3433]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627153509]]></trans_id><latt><![CDATA[13.0353795]]></latt><longi><![CDATA[77.5969529]]></longi><date><![CDATA[2016-06-27 15:35:09]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627153509]]></customer_code><customer_name><![CDATA[Bhavani Fancy]]></customer_name><Phone_no><![CDATA[9035230198#4831]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160627153713]]></trans_id><latt><![CDATA[13.0353887]]></latt><longi><![CDATA[77.5969672]]></longi><date><![CDATA[2016-06-27 15:37:13]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160627153713]]></customer_code><customer_name><![CDATA[Padmavati Medical]]></customer_name><Phone_no><![CDATA[9988004147#3226]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160606115016]]></area><area_name><![CDATA[]]></area_name><rds_tag><![CDATA[ ]]></rds_tag><address><![CDATA[RAMURTYNAGAR]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160702163528]]></trans_id><latt><![CDATA[13.0346517]]></latt><longi><![CDATA[77.5979386]]></longi><date><![CDATA[2016-07-02 16:35:28]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160702163528]]></customer_code><customer_name><![CDATA[Krishna Pharma]]></customer_name><Phone_no><![CDATA[9538623549#9136]]></Phone_no><pin_code><![CDATA[560017]]></pin_code><area><![CDATA[NRT/E012720160608154745]]></area><area_name><![CDATA[KAGADASPURA]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[ANASANDRAPALUYA]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160702163909]]></trans_id><latt><![CDATA[13.0346517]]></latt><longi><![CDATA[77.5979386]]></longi><date><![CDATA[2016-07-02 16:39:09]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160702163909]]></customer_code><customer_name><![CDATA[Allday  Super Market]]></customer_name><Phone_no><![CDATA[9726356925#7048]]></Phone_no><pin_code><![CDATA[560017]]></pin_code><area><![CDATA[NRT/E012720160608154745]]></area><area_name><![CDATA[KAGADASPURA]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[ANASANDRA PALYA]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160706160909]]></trans_id><latt><![CDATA[12.9729459]]></latt><longi><![CDATA[77.6298067]]></longi><date><![CDATA[2016-07-06 16:09:09]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160706160909]]></customer_code><customer_name><![CDATA[Max Shoppe]]></customer_name><Phone_no><![CDATA[9954632852#6301]]></Phone_no><pin_code><![CDATA[560036]]></pin_code><area><![CDATA[RT/233]]></area><area_name><![CDATA[MARTHALII]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[DODANAKUNDI]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160709143118]]></trans_id><latt><![CDATA[12.9590857]]></latt><longi><![CDATA[77.6622463]]></longi><date><![CDATA[2016-07-09 14:31:18]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160709143118]]></customer_code><customer_name><![CDATA[Milana Pharma]]></customer_name><Phone_no><![CDATA[9945256328#4087]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160608154745]]></area><area_name><![CDATA[KAGADASPURA]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[KADASPURA]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160709143953]]></trans_id><latt><![CDATA[12.9590856]]></latt><longi><![CDATA[77.6622458]]></longi><date><![CDATA[2016-07-09 14:39:53]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160709143953]]></customer_code><customer_name><![CDATA[Nilgiris]]></customer_name><Phone_no><![CDATA[9035692584#7648]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160608154745]]></area><area_name><![CDATA[KAGADASPURA]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[KAGADSPURA]]></address></new_customer_details></new_customer><new_customer><location><emp_code><![CDATA[E0127]]></emp_code><trans_id><![CDATA[NE012720160709144506]]></trans_id><latt><![CDATA[12.9590856]]></latt><longi><![CDATA[77.6622461]]></longi><date><![CDATA[2016-07-09 14:45:06]]></date></location><new_customer_details><customer_code><![CDATA[NE012720160709144506]]></customer_code><customer_name><![CDATA[Care Medical]]></customer_name><Phone_no><![CDATA[9563256412#6919]]></Phone_no><pin_code><![CDATA[560016]]></pin_code><area><![CDATA[NRT/E012720160608154745]]></area><area_name><![CDATA[KAGADASPURA]]></area_name><rds_tag><![CDATA[]]></rds_tag><address><![CDATA[KAGDASPURA]]></address></new_customer_details></new_customer></root>";*/
$location_emp_code="*ROOT*NEW_CUSTOMER*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*NEW_CUSTOMER*LOCATION*TRANS_ID";
$location_latt = "*ROOT*NEW_CUSTOMER*LOCATION*LATT";
$location_longi = "*ROOT*NEW_CUSTOMER*LOCATION*LONGI";
$location_date="*ROOT*NEW_CUSTOMER*LOCATION*DATE";

$new_customer_code = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*CUSTOMER_CODE";
$new_customer_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*CUSTOMER_NAME";
$phone_no = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*PHONE_NO";
$pin_code = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*PIN_CODE";
$area = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AREA";
$area_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AREA_NAME";
$rds_tag = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*RDS_TAG";
$address = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*ADDRESS";

$new_customer_array=array();

$counter = 0;

class xml_new_customer{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$new_customer_code,$new_customer_name,$phone_no,$pin_code,$area,$area_name,$rds_tag,$address;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$new_customer_code,$new_customer_name,$phone_no,$pin_code,$area,$area_name,$rds_tag,$address,$new_customer_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,18)=='*ROOT*NEW_CUSTOMER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$new_customer_array[$counter] = new xml_new_customer();
				$new_customer_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$new_customer_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$new_customer_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$new_customer_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$new_customer_array[$counter]->location_date = $data;
				break;
			case $new_customer_code:
				$new_customer_array[$counter]->new_customer_code = $data;
				break;
			case $new_customer_name:
				$new_customer_array[$counter]->new_customer_name = $data;
				break;
			case $phone_no:
				$new_customer_array[$counter]->phone_no = $data;
				break;
			case $pin_code:
				$new_customer_array[$counter]->pin_code = $data;
				break;
			case $area:
				$new_customer_array[$counter]->area = $data;
				break;	
			case $area_name:
				$new_customer_array[$counter]->area_name = $data;
				break;
			case $rds_tag:
				$new_customer_array[$counter]->rds_tag = $data;
				break;	
			case $address:
				$new_customer_array[$counter]->address = $data;
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
/* --------------------START QUERY FOR New customer------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$newcustomer_array_trans_id=array();
if(count($new_customer_array)>0)
{
	$refreshflag=0;
	for($x=0;$x<count($new_customer_array);$x++){
		$location_emp_code=$new_customer_array[$x]->location_emp_code;
		$location_trans_id=$new_customer_array[$x]->location_trans_id;
		$location_latt=$new_customer_array[$x]->location_latt;
		$location_longi=$new_customer_array[$x]->location_longi;
		$location_date=$new_customer_array[$x]->location_date;
		$new_customer_code=$new_customer_array[$x]->new_customer_code;
		$new_customer_name= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->new_customer_name);
		$phone_no=$new_customer_array[$x]->phone_no;
		$pin_code=$new_customer_array[$x]->pin_code;
		$area=$new_customer_array[$x]->area;
		$area_name= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->area_name);
		$rds_tag=$new_customer_array[$x]->rds_tag;
		$address= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->address);
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$newcustomer_array_trans_id))
			{
				array_push($newcustomer_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update new customer location: ".$sqlupdateorlocation);
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
		$emp_name=title_case_emp($rowempname['emp_name']);
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

		//For Insert into the location table for new trans id regarding order
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
		// Add new route to route master
		if(substr($area,0,1)=='N'){
			if(distributor_route_planning=='yes')
			{
				$sqlroute="select route_name from route_master WHERE route_name='".addslashes($area_name)."' AND emp_code='".$emp_code."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$countroute=mysqli_num_rows($rsroute);
				if($countroute<1)
				{
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$area."'";
					$sqlroute .= " ,route_name='".addslashes($area_name)."'";
					$sqlroute .= " ,emp_code='".$emp_code."'";
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
					$email_tag='New Route Name';
					$refreshflag=1;
				}
				else
				{
					$email_tag='Route Name';
				}
			}
			else
			{
			//$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$area."'";
			$sqlroute="select route_name from route_master WHERE route_name='".addslashes($area_name)."'";
			$rsroute=mysqli_query($link,$sqlroute);
			$countroute=mysqli_num_rows($rsroute);
			if($countroute<1)
			{
				//Creation of route code parameter
				/*$route_prefix="RT";
				$firt_character_route=strtoupper(substr($area_name,0,1));
				$route_code_left_part=$route_prefix.$foldernamerand.$branch_code.$firt_character_route;

				$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, (length('".$route_code_left_part."')+1), (length( route_code ) -length('".$route_code_left_part."'))) AS UNSIGNED )) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
				$rsmaxroutecode=mysqli_query($link,$sqlmaxroutecode);
				$rowmaxroutecode=mysqli_fetch_assoc($rsmaxroutecode);
				$new_route_code=$rowmaxroutecode['new_route_code'];
				
				if($new_route_code=='')
				{
					$max_route_code=$route_code_left_part.'00001';
				}
				else
				{
					$max_route_code=$route_code_left_part.str_pad($new_route_code+1, 5, 0, STR_PAD_LEFT);
					//$max_route_code++;
				}*/

				$sqlroute  = "insert into route_master ";
				$sqlroute .= " SET route_code='".$area."'";
				$sqlroute .= " ,route_name='".addslashes($area_name)."'";
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
				$email_tag='New Route Name';
				$refreshflag=1;
			}
			else
			{
				$email_tag='Route Name';
			}
		  }
		}
		else
		{
			$email_tag='Route Name';
		}
		$route_code=$area;
		$route_name=$area_name;

		/*$preifix_string='CM';
		//Creation of customer code parameter
		$customer_prefix=$preifix_string;
		$firt_character_customer=strtoupper(substr($customer_name,0,1));
		$customer_code_left_part=$customer_prefix.$foldernamerand.$branch_code.$firt_character_customer;

		$sqlmaxcustomercode="SELECT MAX( CAST( SUBSTRING( customer_code, (length('".$customer_code_left_part."')+1), (length( customer_code ) -length('".$customer_code_left_part."'))) AS UNSIGNED )) AS new_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
		$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
		$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
		$new_customer_code=$rowmaxcustomercode['new_customer_code'];
		
		if($new_customer_code=='')
		{
			$max_customer_code=$customer_code_left_part.'00001';
		}
		else
		{
			$max_customer_code=$customer_code_left_part.str_pad($new_customer_code+1, 5, 0, STR_PAD_LEFT);
		}*/		
		
		$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND emp_code='".$emp_code."' AND route_code='".$route_code."' and acedns='Y'";
		$rscustomer=mysqli_query($link,$sqlcustomer);
		$countcustomer=mysqli_num_rows($rscustomer);
		if($countcustomer<1)
		{
		//For addition of new customer
		$sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
						   dns_customer_code ='".$new_customer_code."',
						   customer_name				='".addslashes($new_customer_name)."',
						   emp_code						='".$emp_code."',
						   route_code					='".$route_code."',
						   address						='".addslashes($address)."',
						   pin							='".$pin_code."',
						   phone_no						='".$phone_no."',
						   cust_type					='R',
						   branch_code					='".$branch_code."',
						   rds_tag						='".$rds_tag."',
						   download_time				=CURRENT_TIMESTAMP(),
						   download_time_credit_limit	=CURRENT_TIMESTAMP(),
						   acedns						='Y',
						   black_list					='N'";
	   //For addition of new customer route employee relation					   
	   /*$sqlinsertcustomerrelation = "INSERT INTO customer_route_emp_relation 
							 SET customer_code='".$max_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."'";
		if(mysqli_query($link,$sqlinsertcustomer) && mysqli_query($link,$sqlinsertcustomerrelation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}*/
		if(mysqli_query($link,$sqlinsertcustomer))
		{
			$flag=5;
			$refreshflag=1;
			if(strpos($phone_no, '#')!=false) {
				$valuearray=explode('#',$phone_no);
				$sql_insert_OTP="INSERT INTO OTP_details SET mobile_no='".$valuearray[0]."',
								   OTP 	='".$valuearray[1]."'";
			    mysqli_query($link,$sql_insert_OTP);				   
			}
			if($nick_name=='RUPA')
			{
				$sqlchkverticaldetails="SELECT emp_code FROM vertical_branch_employeewise_details WHERE emp_code='".$emp_code."' AND branch_code='".$branch_code."' 
							AND vertical_value='".$vertical_value."' AND operation_date=CURDATE()";
				$rschkverticaldetails=mysqli_query($link,$sqlchkverticaldetails);
				$countchkverticaldetails=mysqli_num_rows($rschkverticaldetails);
				if($countchkverticaldetails<1)
				{
					//For addition of new record in vertical_branch_employeewise_details
					$sqlinsertvertcaldetails="INSERT INTO vertical_branch_employeewise_details SET vertical_value ='".$vertical_value."',
												emp_code='".$emp_code."',
												branch_code	='".$branch_code."',
												operation_date=CURDATE(),
												calls_made=1,
												productive=0,
												`primary`=0,
												secondary=0";
					mysqli_query($link,$sqlinsertvertcaldetails);							
				}
				else
				{
					//For addition of new record in vertical_branch_employeewise_details
					$sqlupdatevertcaldetails="UPDATE vertical_branch_employeewise_details SET 
												calls_made=(calls_made+1) WHERE vertical_value ='".$vertical_value."' AND
												emp_code='".$emp_code."' AND branch_code	='".$branch_code."' AND  operation_date=CURDATE()";
					mysqli_query($link,$sqlupdatevertcaldetails);	
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
		else
		{
			$flag=5;
		}
		//Sending mail
		$operation_date=$date.'-'.$month.'-'.$year.' @ '.$hour.':'.$minute.':'.$second;

		$addcustomermailsubj="$nick_name - ​New Customer included by ".$emp_name." on ".date('d-m-Y',strtotime($location_date))." @".date('H:i:s',strtotime($location_date)).' hrs.';
		//$addressnewcustomer=getReverseGeo($location_latt,$location_longi);
		
		$addcustomer_TR="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Pincode</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>".$email_tag."</span></strong></th>
								<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Ph. No.</span></strong></th><th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Address</span></strong></th>";
				
		$addcustomer_TD="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$new_customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$pin_code."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$route_name."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								+91-".$phone_no."</span>&nbsp;</td><td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$address."</span>&nbsp;</td>";
		$addcustomermailbody = "<table><tr><td>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b><br><br>".$emp_name." visited ".$new_customer_name.".</td></tr></table>
								<br><br><table border=1 style=background-color:AliceBlue>
							     <tr>".$addcustomer_TR."</tr><tr>".$addcustomer_TD."</tr></table><br><br><br>Powered By aceDNS<br>";
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		if($nick_name=='RUPA')
		{
			$addcustomer_email='';
		}
		else
		{
			$addcustomer_email=ORDEREMAILRECIPENTS;
		}			
		if(mail($addcustomer_email, $addcustomermailsubj, $addcustomermailbody, $headers,$spam_filter))
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
 /* --------------------END QUERY FOR New customer--------------------------------------------------------------------------------------------------------*/
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
	 
	 if($countdatarefresh >0 || $refreshflag==1)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = "http://www.acedns.in/acednsproduct/operationdb-newcustomer-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-newcustomer-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
?>
