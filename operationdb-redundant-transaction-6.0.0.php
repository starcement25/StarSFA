<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
echo 1;die;
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
$last_update_time=$_REQUEST['last_update_time'] ?? '';
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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><redundant_transaction><location><emp_code><![CDATA[E0020]]></emp_code><trans_id><![CDATA[RTE002020160905180537]]></trans_id><latt><![CDATA[22.5642224]]></latt><longi><![CDATA[88.3568565]]></longi><date><![CDATA[2016-09-05 18:05:37]]></date></location><redundantdata><redundant_trans_id><![CDATA[RTE002020160905180537]]></redundant_trans_id><foot_soldier_id><![CDATA[FSE003720160527123342]]></foot_soldier_id><mall_id><![CDATA[Maidan Market]]></mall_id><business_name><![CDATA[Anmol Sports]]></business_name><status><![CDATA[REDUNDANT]]></status></redundantdata></redundant_transaction><redundant_transaction><location><emp_code><![CDATA[E0020]]></emp_code><trans_id><![CDATA[RTE002020160905181052]]></trans_id><latt><![CDATA[22.5641707]]></latt><longi><![CDATA[88.3568957]]></longi><date><![CDATA[2016-09-05 18:10:52]]></date></location><redundantdata><redundant_trans_id><![CDATA[RTE002020160905181052]]></redundant_trans_id><foot_soldier_id><![CDATA[FSE003720160527143451]]></foot_soldier_id><mall_id><![CDATA[M0167]]></mall_id><business_name><![CDATA[Arora Center]]></business_name><status><![CDATA[REDUNDANT]]></status></redundantdata></redundant_transaction></root>";*/

$location_emp_code="*ROOT*REDUNDANT_TRANSACTION*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*REDUNDANT_TRANSACTION*LOCATION*TRANS_ID";
$location_latt = "*ROOT*REDUNDANT_TRANSACTION*LOCATION*LATT";
$location_longi = "*ROOT*REDUNDANT_TRANSACTION*LOCATION*LONGI";
$location_date="*ROOT*REDUNDANT_TRANSACTION*LOCATION*DATE";

$redundant_trans_id = "*ROOT*REDUNDANT_TRANSACTION*REDUNDANTDATA*REDUNDANT_TRANS_ID";
$foot_soldier_id = "*ROOT*REDUNDANT_TRANSACTION*REDUNDANTDATA*FOOT_SOLDIER_ID";
$mall_id = "*ROOT*REDUNDANT_TRANSACTION*REDUNDANTDATA*MALL_ID";
$business_name = "*ROOT*REDUNDANT_TRANSACTION*REDUNDANTDATA*BUSINESS_NAME";
$status = "*ROOT*REDUNDANT_TRANSACTION*REDUNDANTDATA*STATUS";

$redundant_transaction_array=array();

$counter = 0;
class xml_redundant_transaction{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$redundant_trans_id,$foot_soldier_id,$mall_id,$business_name,$status;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$redundant_trans_id,$foot_soldier_id,$mall_id,$business_name,$status,$redundant_transaction_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,27)=='*ROOT*REDUNDANT_TRANSACTION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$redundant_transaction_array[$counter] = new xml_redundant_transaction();
				$redundant_transaction_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$redundant_transaction_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$redundant_transaction_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$redundant_transaction_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$redundant_transaction_array[$counter]->location_date = $data;
				break;
			case $redundant_trans_id:
				$redundant_transaction_array[$counter]->redundant_trans_id = $data;
				break;
			case $foot_soldier_id:
				$redundant_transaction_array[$counter]->foot_soldier_id = $data;
				break;
			case $mall_id:
				$redundant_transaction_array[$counter]->mall_id = $data;
				break;	
			case $business_name:
				$redundant_transaction_array[$counter]->business_name = $data;
				break;
			case $status:
				$redundant_transaction_array[$counter]->status = $data;
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
//print_r($redundant_transaction_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR REDUNDANT TRANSACTION------------------------------------------------------------------------------------------------*/
$redundant_transaction_array_trans_id=array();
if(count($redundant_transaction_array)>0)
{
	for($x=0;$x<count($redundant_transaction_array);$x++){

		$location_emp_code=$redundant_transaction_array[$x]->location_emp_code;
		$location_trans_id=$redundant_transaction_array[$x]->location_trans_id;
		$location_latt=$redundant_transaction_array[$x]->location_latt;
		$location_longi=$redundant_transaction_array[$x]->location_longi;
		$location_date=$redundant_transaction_array[$x]->location_date;
		$redundant_trans_id=$redundant_transaction_array[$x]->redundant_trans_id;
		$foot_soldier_id=$redundant_transaction_array[$x]->foot_soldier_id;
		$mall_id=$redundant_transaction_array[$x]->mall_id;
		$business_name=$redundant_transaction_array[$x]->business_name;
		$status=$redundant_transaction_array[$x]->status;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for redundant transaction
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check Product Promotion: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for redundant transaction
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$redundant_transaction_array_trans_id))
			{
				array_push($redundant_transaction_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update redundant transaction location: ".$sqlupdateorlocation);
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
		$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=$rowempname['emp_name'];
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding redundant transaction
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
		// Add new redundant transaction
		$sqlinsertredundanttransaction="INSERT INTO redundant_transaction SET redundant_trans_id ='".$redundant_trans_id."',
							   	foot_soldier_id				='".$foot_soldier_id."',
								mall_id						='".$mall_id."',
							    business_name				='".addslashes($business_name)."',
							    status						='".addslashes($status)."',
								download_time				=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinsertredundanttransaction))
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
 /* --------------------END QUERY For REDUNDANT TRANSACTION-------------------------------------------------------------------------------------------------*/
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
//echo $flag=2;
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url="http://www.acedns.in/acednsproduct/operationdb-redundant-transaction-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
