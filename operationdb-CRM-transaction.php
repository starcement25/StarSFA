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

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);


/*$body="<?xml version='1.0' encoding='UTF-8'?><root><CRM_info><location><emp_code><![CDATA[E1411]]></emp_code><trans_id><![CDATA[CRME141120180201121201]]></trans_id><latt><![CDATA[22.5644218]]></latt><longi><![CDATA[88.3568047]]></longi><date><![CDATA[2018-02-01 12:12:01]]></date></location><CRM_details><call_id><![CDATA[CRME141120180201121201]]></call_id><customer_code><![CDATA[NE047720170624121415]]></customer_code><call_duration><![CDATA[00:00:33]]></call_duration><recorded_file><![CDATA[E141120180201121127.3gp]]></recorded_file></CRM_details></CRM_info><CRM_info><location><emp_code><![CDATA[E1411]]></emp_code><trans_id><![CDATA[CRME141120180201124507]]></trans_id><latt><![CDATA[22.5644213]]></latt><longi><![CDATA[88.3568048]]></longi><date><![CDATA[2018-02-01 12:45:07]]></date></location><CRM_details><call_id><![CDATA[CRME141120180201124507]]></call_id><customer_code><![CDATA[NE047720170624102141]]></customer_code><call_duration><![CDATA[00:00:06]]></call_duration><recorded_file><![CDATA[E141120180201124500.3gp]]></recorded_file></CRM_details></CRM_info><CRM_info><location><emp_code><![CDATA[E1411]]></emp_code><trans_id><![CDATA[CRME141120180201124748]]></trans_id><latt><![CDATA[22.5644215]]></latt><longi><![CDATA[88.3568167]]></longi><date><![CDATA[2018-02-01 12:47:48]]></date></location><CRM_details><call_id><![CDATA[CRME141120180201124748]]></call_id><customer_code><![CDATA[NE047720170624101322]]></customer_code><call_duration><![CDATA[00:00:08]]></call_duration><recorded_file><![CDATA[E141120180201124733.3gp]]></recorded_file></CRM_details></CRM_info></root>";*/
$location_emp_code="*ROOT*CRM_INFO*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*CRM_INFO*LOCATION*TRANS_ID";
$location_latt = "*ROOT*CRM_INFO*LOCATION*LATT";
$location_longi = "*ROOT*CRM_INFO*LOCATION*LONGI";
$location_date="*ROOT*CRM_INFO*LOCATION*DATE";

$call_id = "*ROOT*CRM_INFO*CRM_DETAILS*CALL_ID";
$customer_code = "*ROOT*CRM_INFO*CRM_DETAILS*CUSTOMER_CODE";
$call_duration = "*ROOT*CRM_INFO*CRM_DETAILS*CALL_DURATION";
$recorded_file = "*ROOT*CRM_INFO*CRM_DETAILS*RECORDED_FILE";

$CRM_array=array();

$counter = 0;

class xml_CRM{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$call_id,$customer_code,$call_duration,$recorded_file;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$call_id,$customer_code,$call_duration,$recorded_file,$CRM_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,14)=='*ROOT*CRM_INFO')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$CRM_array[$counter] = new xml_CRM();
				$CRM_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$CRM_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$CRM_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$CRM_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$CRM_array[$counter]->location_date = $data;
				break;
			case $call_id:
				$CRM_array[$counter]->call_id = $data;
				break;
			case $customer_code:
				$CRM_array[$counter]->customer_code = $data;
				break;
			case $call_duration:
				$CRM_array[$counter]->call_duration = $data;
				break;
			case $recorded_file:
				$CRM_array[$counter]->recorded_file = $data;
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

/* --------------------START QUERY FOR CRM INFO------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$CRM_array_trans_id=array();
if(count($CRM_array)>0)
{
	for($x=0;$x<count($CRM_array);$x++){
		$location_emp_code=$CRM_array[$x]->location_emp_code;
		$location_trans_id=$CRM_array[$x]->location_trans_id;
		$location_latt=$CRM_array[$x]->location_latt;
		$location_longi=$CRM_array[$x]->location_longi;
		$location_date=$CRM_array[$x]->location_date;
		$call_id=$CRM_array[$x]->call_id;
		$customer_code= $CRM_array[$x]->customer_code;
		$call_duration=$CRM_array[$x]->call_duration;
		$recorded_file=$CRM_array[$x]->recorded_file;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for CRM
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$CRM_array_trans_id))
			{
				array_push($CRM_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update wholesaler info location: ".$sqlupdateorlocation);
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
		
		$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
		$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding wholesaler info
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
		$sqlinsertCRM="INSERT INTO CRM_transaction SET call_id ='".$call_id."',
						   customer_code 		='".$customer_code."',
						   call_duration		='".$call_duration."',
						   recorded_file		='".$recorded_file."'";
		if(mysqli_query($link,$sqlinsertCRM))
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
 /* --------------------END QUERY FOR CRM INFO--------------------------------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-CRM-transaction.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
