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

/*if($nick_name=='EMAMIT')
{
	$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	
}*/
$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><foot_soldier><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[FSE000220150306180952]]></trans_id><latt><![CDATA[22.5641689]]></latt><longi><![CDATA[88.3569041]]></longi><date><![CDATA[2015-03-06 18:09:52]]></date></location><footsoldierdata><foot_soldier_id><![CDATA[FSE000220150306180952]]></foot_soldier_id><mall_name><![CDATA[south city]]></mall_name>
<pincode><![CDATA[700034]]></pincode><business_name><![CDATA[Spencer]]></business_name><type><![CDATA[mall]]></type></footsoldierdata></foot_soldier></root>";*/

$location_emp_code="*ROOT*FOOT_SOLDIER*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*FOOT_SOLDIER*LOCATION*TRANS_ID";
$location_latt = "*ROOT*FOOT_SOLDIER*LOCATION*LATT";
$location_longi = "*ROOT*FOOT_SOLDIER*LOCATION*LONGI";
$location_date="*ROOT*FOOT_SOLDIER*LOCATION*DATE";

$foot_soldier_id = "*ROOT*FOOT_SOLDIER*FOOTSOLDIERDATA*FOOT_SOLDIER_ID";
$mall_name = "*ROOT*FOOT_SOLDIER*FOOTSOLDIERDATA*MALL_NAME";
$pincode = "*ROOT*FOOT_SOLDIER*FOOTSOLDIERDATA*PINCODE";
$business_name = "*ROOT*FOOT_SOLDIER*FOOTSOLDIERDATA*BUSINESS_NAME";
$type = "*ROOT*FOOT_SOLDIER*FOOTSOLDIERDATA*TYPE";

$foot_soldier_array=array();

$counter = 0;
class xml_foot_soldier{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$foot_soldier_id,$mall_name,$pincode,$business_name,$type;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$foot_soldier_id,$mall_name,$pincode,$business_name,$type,$foot_soldier_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,18)=='*ROOT*FOOT_SOLDIER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$foot_soldier_array[$counter] = new xml_foot_soldier();
				$foot_soldier_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$foot_soldier_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$foot_soldier_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$foot_soldier_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$foot_soldier_array[$counter]->location_date = $data;
				break;
			case $foot_soldier_id:
				$foot_soldier_array[$counter]->foot_soldier_id = $data;
				break;
			case $mall_name:
				$foot_soldier_array[$counter]->mall_name = $data;
				break;
			case $pincode:
				$foot_soldier_array[$counter]->pincode = $data;
				break;
			case $business_name:
				$foot_soldier_array[$counter]->business_name = $data;
				break;
			case $type:
				$foot_soldier_array[$counter]->type = $data;
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
//print_r($check_in_out_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;

/* --------------------START QUERY FOR Foot soldier------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$foot_soldier_array_trans_id=array();
if(count($foot_soldier_array)>0)
{
	for($x=0;$x<count($foot_soldier_array);$x++){

		$location_emp_code=$foot_soldier_array[$x]->location_emp_code;
		$location_trans_id=$foot_soldier_array[$x]->location_trans_id;
		$location_latt=$foot_soldier_array[$x]->location_latt;
		$location_longi=$foot_soldier_array[$x]->location_longi;
		$location_date=$foot_soldier_array[$x]->location_date;
		$foot_soldier_id=$foot_soldier_array[$x]->foot_soldier_id;
		$mall_name=$foot_soldier_array[$x]->mall_name;
		$pincode=$foot_soldier_array[$x]->pincode;
		$business_name=$foot_soldier_array[$x]->business_name;
		$type=$foot_soldier_array[$x]->type;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for foot soldier
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check Product Promotion: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for foot soldier
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$foot_soldier_array_trans_id))
			{
				array_push($foot_soldier_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update foot soldier location: ".$sqlupdateorlocation);
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
		
		/*$sqlmallid="SELECT mall_id FROM mall_master WHERE mall_name='".addslashes($mall_name)."'";
		$rsmallid=mysqli_query($link,$sqlmallid);
		$rowmallid=mysqli_fetch_assoc($rsmallid);
		$mall_id=$rowmallid['mall_id'];*/
		
		$sql_mall_master = "SELECT mall_id FROM mall_master WHERE mall_name = '".addslashes($mall_name)."'";
		$res_mall_master = mysqli_query($link,$sql_mall_master);
		while($row_mall_master = mysqli_fetch_assoc($res_mall_master)){
			$mall_id = $row_mall_master['mall_id'];
			
			$sql_mall_emp_check = "SELECT mall_id FROM mall_emp_fs_relation WHERE emp_code = '".$emp_code."' AND mall_id = '".$mall_id."'";
			$res_mall_emp_check = mysqli_query($link,$sql_mall_emp_check);
			$total_rows_check = mysqli_num_rows($res_mall_emp_check);
			if($total_rows_check>0){
				$row_mall_emp_check=mysqli_fetch_assoc($res_mall_emp_check);
				$mall_id_fs=$row_mall_emp_check['mall_id'];
			}
		}

		$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
		$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding foot soldier
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
		// Add new foot soldier
		$sqlinsertfootsoldier="INSERT INTO foot_soldier SET foot_soldier_id ='".$foot_soldier_id."',
							   	mall_name					='".addslashes($mall_name)."',
								mall_id						='".$mall_id_fs."',
							   business_name				='".addslashes($business_name)."',
							   pin_code						='".addslashes($pincode)."',
							   download_time				=CURRENT_TIMESTAMP(),
							   type							='".addslashes($type)."'";
		if(mysqli_query($link,$sqlinsertfootsoldier))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		/*$operation_date=$date.'-'.$month.'-'.$year.' @ '.$hour.':'.$minute.':'.$second;
		
		$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomername=mysqli_query($link,$sqlcustomername);
		$rowcustomername=mysqli_fetch_assoc($rscustomername);
		$customer_name=$rowcustomername['customer_name'];
		
		$footsoldiermailsubj="$nick_name - ​Customer Activity by ".$emp_name." on ".date('d-m-Y',strtotime($location_date))." @".date('H:i:s',strtotime($location_date)).' hrs.';
		
		$check_in_out_TR="<html><head><title>Check in out Details</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br /><br />Refference no: <b>".$location_trans_id."</b><br /><br /><table border=1 style=background-color:AliceBlue><tr>
							<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Check in time</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Check out time</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Duration</span></strong></th>
								<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th></tr>";
				
		$check_in_out_TD="<tr><td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".date('d-m-Y H:i:s',strtotime($check_in_time))."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".date('d-m-Y H:i:s',strtotime($check_out_time))."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$time_duration."</span>&nbsp;</td>
								<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$remarks."</span>&nbsp;</td></tr></table>";
		$footsoldiermailbody = $check_in_out_TR.$check_in_out_TD."
								<br><br><br>Powered By aceDNS<br>";
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		if(mail('', $footsoldiermailsubj, $footsoldiermailbody, $headers,$spam_filter))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}*/
	  }//End of else
	}
}
 /* --------------------END QUERY For Foot soldier--------------------------------------------------------------------------------------------------------*/
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
$url = "http://www.acedns.in/acednsproduct/operationdb-foot-soldier.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
?>
