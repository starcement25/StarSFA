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
$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[NE000120150827112916]]></trans_id><latt><![CDATA[22.5642279]]></latt><longi><![CDATA[88.3568262]]></longi><date><![CDATA[2015-08-27 11:29:16]]></date></location><new_customer_details><customer_code><![CDATA[NE000120150827112916]]></customer_code><customer_name><![CDATA[Test]]></customer_name><Phone_no><![CDATA[1234567890]]></Phone_no><pin_code><![CDATA[123456]]></pin_code><area><![CDATA[RT/3]]></area><area_name><![CDATA[MANISH NAGAR]]></area_name><rds_tag><![CDATA[]]></rds_tag></new_customer_details></new_customer></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><MARKET_FEEDBACK><location><emp_code><![CDATA[E0027]]></emp_code><trans_id><![CDATA[MFE002720150907182231]]></trans_id><latt><![CDATA[22.5642384]]></latt><longi><![CDATA[88.3567992]]></longi><date><![CDATA[2015-09-07 18:22:31]]></date></location><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Fortune]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Ruchira]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182231]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Mahakosh]]></COMPETITOR_NAME><PTD><![CDATA[]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA></MARKET_FEEDBACK><MARKET_FEEDBACK><location><emp_code><![CDATA[E0027]]></emp_code><trans_id><![CDATA[MFE002720150907182809]]></trans_id><latt><![CDATA[22.5642318]]></latt><longi><![CDATA[88.3568094]]></longi><date><![CDATA[2015-09-07 18:28:09]]></date></location><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Fortune]]></COMPETITOR_NAME><PTD><![CDATA[5]]></PTD><PTR><![CDATA[6]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Ruchira]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_DATA><MARKET_FEEDBACK_ID><![CDATA[MFE002720150907182809]]></MARKET_FEEDBACK_ID><route_code><![CDATA[]]></route_code><PRODUCT_GROUP><![CDATA[Rice Bran]]></PRODUCT_GROUP><COMPETITOR_NAME><![CDATA[Mahakosh]]></COMPETITOR_NAME><PTD><![CDATA[0]]></PTD><PTR><![CDATA[]]></PTR><PTC><![CDATA[]]></PTC></MARKET_FEEDBACK_DATA></MARKET_FEEDBACK></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><MARKET_FEEDBACK_STKAUDIT><location><emp_code><![CDATA[E0027]]></emp_code><trans_id><![CDATA[MSE002720150907182231]]></trans_id><latt><![CDATA[22.5642384]]></latt><longi><![CDATA[88.3567992]]></longi><date><![CDATA[2015-09-07 18:22:31]]></date></location><MF_STKAUDIT_DATA><MF_STKAUDIT_HEADER><MF_STKAUDIT_ID><![CDATA[MSE002720150907182231]]></MF_STKAUDIT_ID><customer_code><![CDATA[]]></customer_code><remarks><![CDATA[]]></remarks></MF_STKAUDIT_HEADER><MF_STKAUDIT_DETAILS><MF_STKAUDIT_ID><![CDATA[MSE002720150907182231]]></MF_STKAUDIT_ID><COMPETITOR_NAME><![CDATA[Fortune]]></COMPETITOR_NAME><QTY_MT><![CDATA[0]]></QTY_MT><SCHEME_DISCOUNT><![CDATA[]]></SCHEME_DISCOUNT></MF_STKAUDIT_DETAILS></MF_STKAUDIT_DATA></MARKET_FEEDBACK_STKAUDIT></root>";*/

$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*MARKET_FEEDBACK_STKAUDIT*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*MARKET_FEEDBACK_STKAUDIT*LOCATION*TRANS_ID";
$location_latt = "*ROOT*MARKET_FEEDBACK_STKAUDIT*LOCATION*LATT";
$location_longi = "*ROOT*MARKET_FEEDBACK_STKAUDIT*LOCATION*LONGI";
$location_date="*ROOT*MARKET_FEEDBACK_STKAUDIT*LOCATION*DATE";

$marketfeedbackheader_id = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_HEADER*MF_STKAUDIT_ID";
$marketfeedbackheader_customer_code="*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_HEADER*CUSTOMER_CODE";
$marketfeedbackheader_remarks = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_HEADER*REMARKS";

$marketfeedbackdetails_id = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_DETAILS*MF_STKAUDIT_ID";
$marketfeedbackdetails_competitor_name = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_DETAILS*COMPETITOR_NAME";
$marketfeedbackdetails_qty = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_DETAILS*QTY_MT";
$marketfeedbackdetails_scheme_discount = "*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_DETAILS*SCHEME_DISCOUNT";

$market_feedback_array=array();
$market_feedback_details_array=array();

$counter = 0;
$countermarketdata=0;

class xml_market_feedback{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$marketfeedbackheader_id,$marketfeedbackheader_customer_code,$marketfeedbackheader_remarks;	
}
class xml_market_feedback_details{
	var $marketfeedbackdetails_id,$marketfeedbackdetails_competitor_name,$marketfeedbackdetails_qty,$marketfeedbackdetails_scheme_discount;
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
    global $current_tag,$counter,$countermarketdata,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$marketfeedbackheader_id,$marketfeedbackheader_customer_code,$marketfeedbackheader_remarks,$marketfeedbackdetails_id,$marketfeedbackdetails_competitor_name,$marketfeedbackdetails_qty,$marketfeedbackdetails_scheme_discount,$market_feedback_array,$market_feedback_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,30)=='*ROOT*MARKET_FEEDBACK_STKAUDIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$market_feedback_array[$counter] = new xml_market_feedback();
				$market_feedback_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$market_feedback_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$market_feedback_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$market_feedback_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$market_feedback_array[$counter]->location_date = $data;
				break;
			case $marketfeedbackheader_id:
				$market_feedback_array[$counter]->marketfeedbackheader_id = $data;
				break;
			case $marketfeedbackheader_customer_code:
				$market_feedback_array[$counter]->marketfeedbackheader_customer_code = $data;
				break;
			case $marketfeedbackheader_remarks:
				$market_feedback_array[$counter]->marketfeedbackheader_remarks = $data;
				$counter++;
				break;			
		}
	}
	if(substr($current_tag,0,67)=='*ROOT*MARKET_FEEDBACK_STKAUDIT*MF_STKAUDIT_DATA*MF_STKAUDIT_DETAILS')
	  {
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $marketfeedbackdetails_id:
				$market_feedback_details_array[$countermarketdata] = new xml_market_feedback_details();
				$market_feedback_details_array[$countermarketdata]->marketfeedbackdetails_id = $data;
				break;
			case $marketfeedbackdetails_competitor_name:
				$market_feedback_details_array[$countermarketdata]->marketfeedbackdetails_competitor_name = $data;
				break;	
			case $marketfeedbackdetails_qty:
				$market_feedback_details_array[$countermarketdata]->marketfeedbackdetails_qty = $data;
				break;
			case $marketfeedbackdetails_scheme_discount:
				$market_feedback_details_array[$countermarketdata]->marketfeedbackdetails_scheme_discount = $data;
				$countermarketdata++;
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
/* --------------------START QUERY FOR Market Feedback STOCK AUDIT ------------------------------------------------------------------------------------------*/
//print_r($market_feedback_array);
$market_feedback_array_trans_id=array();
if(count($market_feedback_array)>0)
{
		for($x=0;$x<count($market_feedback_array);$x++){
			$location_emp_code=$market_feedback_array[$x]->location_emp_code;
			$location_trans_id=$market_feedback_array[$x]->location_trans_id;
			$location_latt=$market_feedback_array[$x]->location_latt;
			$location_longi=$market_feedback_array[$x]->location_longi;
			$location_date=$market_feedback_array[$x]->location_date;
			$marketfeedbackheader_id=$market_feedback_array[$x]->marketfeedbackheader_id;
			$marketfeedbackheader_customer_code=$market_feedback_array[$x]->marketfeedbackheader_customer_code;
			$marketfeedbackheader_remarks=$market_feedback_array[$x]->marketfeedbackheader_remarks;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if(DCR_map=='no')
			{
				if($location_latt>0 && $location_longi>0)
				{
					$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
											emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
					$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
				}
			}
	
			//For checking that trans id exist or not for market feedback
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check market feedback: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for market feedback
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$market_feedback_array_trans_id))
				{
					array_push($market_feedback_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update market feedback location: ".$sqlupdateorlocation);
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
			//For Insert into the mf stock audit Header table for new trans id
			$sqlinsertmfstockauditheader="INSERT INTO mf_stk_audit_header SET mf_stk_audit_id='".$marketfeedbackheader_id."',
								  customer_code 	='".$marketfeedbackheader_customer_code."',
								  remarks 			='".$marketfeedbackheader_remarks."'";
					
		  if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertmfstockauditheader))
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
	for($i=0;$i<count($market_feedback_details_array);$i++){
		$marketfeedbackdetails_id=$market_feedback_details_array[$i]->marketfeedbackdetails_id;
		$marketfeedbackdetails_competitor_name=$market_feedback_details_array[$i]->marketfeedbackdetails_competitor_name;
		$marketfeedbackdetails_qty=$market_feedback_details_array[$i]->marketfeedbackdetails_qty;
		$marketfeedbackdetails_scheme_discount=$market_feedback_details_array[$i]->marketfeedbackdetails_scheme_discount;
	
		//For addition of market feedback
		$sqlinsertmarketfeedbackdetails="INSERT INTO mf_stk_audit_details SET mf_stk_audit_id	='".$marketfeedbackdetails_id."',
								competitor_name				='".$marketfeedbackdetails_competitor_name."',
								qty_mt						='".$marketfeedbackdetails_qty."',
								scheme_discount				='".$marketfeedbackdetails_scheme_discount."'";
		if(mysqli_query($link,$sqlinsertmarketfeedbackdetails))
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
 /* --------------------END QUERY FOR Market feedback--------------------------------------------------------------------------------------------------------*/
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
$url = "http://www.acedns.in/acednsproduct/operationdb-market-feedback-stockaudit.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
?>
