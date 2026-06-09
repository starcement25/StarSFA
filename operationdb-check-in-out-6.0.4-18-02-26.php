<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 
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
$last_operation_datetime='';
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ??'';
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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><check_in_out><location><emp_code><![CDATA[E0555]]></emp_code><trans_id><![CDATA[CIE055520250103212509]]></trans_id><latt><![CDATA[23.1212057]]></latt><longi><![CDATA[88.8599706]]></longi><date><![CDATA[2025-01-03 21:25:09]]></date></location><checkinoutdata><trans_id><![CDATA[CIE055520250103212509]]></trans_id><check_in_time><![CDATA[2025-01-03 21:20:48]]></check_in_time><customer_code><![CDATA[C/0079157]]></customer_code><check_out_time><![CDATA[2025-01-03 21:25:09]]></check_out_time><remarks><![CDATA[test]]></remarks><hint_remarks><![CDATA[]]></hint_remarks><product_tagging><![CDATA[]]></product_tagging><uploaded_photo><![CDATA[]]></uploaded_photo><base_latt><![CDATA[23.1211506]]></base_latt><base_longi><![CDATA[88.8598256]]></base_longi></checkinoutdata></check_in_out></root>";*/
$attendance_emp_code = "*ROOT*ATTENDANCE*LOCATION*EMP_CODE";
$attendance_trans_id = "*ROOT*ATTENDANCE*LOCATION*TRANS_ID";
$attendance_latt = "*ROOT*ATTENDANCE*LOCATION*LATT";
$attendance_longi = "*ROOT*ATTENDANCE*LOCATION*LONGI";
$attendance_date = "*ROOT*ATTENDANCE*LOCATION*DATE";
$attendancedata_emp_code = "*ROOT*ATTENDANCE*ATTENDANCEDATA*EMP_CODE";
$attendancedata_date = "*ROOT*ATTENDANCE*ATTENDANCEDATA*DATE";


$location_emp_code="*ROOT*CHECK_IN_OUT*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*CHECK_IN_OUT*LOCATION*TRANS_ID";
$location_latt = "*ROOT*CHECK_IN_OUT*LOCATION*LATT";
$location_longi = "*ROOT*CHECK_IN_OUT*LOCATION*LONGI";
$location_date="*ROOT*CHECK_IN_OUT*LOCATION*DATE";
//$location_date="*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*DATE";

$trans_id = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*TRANS_ID";
$check_in_time = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*CHECK_IN_TIME";
$customer_code = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*CUSTOMER_CODE";
$check_out_time = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*CHECK_OUT_TIME";
$remarks = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*REMARKS";
$hint_remarks = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*HINT_REMARKS";
$product_tagging = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*PRODUCT_TAGGING";
$uploaded_photo = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*UPLOADED_PHOTO";
$base_latt = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*BASE_LATT";
$base_longi = "*ROOT*CHECK_IN_OUT*CHECKINOUTDATA*BASE_LONGI";
//echo $attendance_emp_code;die;
$check_in_out_array=array();
$attendance_array = array();

$countercheckin = 0;
$counter=0;
class xml_attendance{
    var $attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date;
}

class xml_check_in_out{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$trans_id,$check_in_time,$customer_code,$check_out_time,$remarks,$hint_remarks,$product_tagging,$uploaded_photo,$base_latt,$base_longi;	
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
    global $current_tag,$counter,$countercheckin,$attendance_emp_code, $attendance_trans_id,$attendance_latt,$attendance_longi,$attendance_date,$attendancedata_emp_code,$attendancedata_date,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$trans_id,$check_in_time,$customer_code,$check_out_time,$remarks,$hint_remarks,$product_tagging,$uploaded_photo,$base_latt,$base_longi,$check_in_out_array,$attendance_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,16)=='*ROOT*ATTENDANCE')
	{
		if (!isset($attendance_array[$counter])) {
			$attendance_array[$counter] = new xml_attendance();
		}
		switch($current_tag){
			case $attendance_emp_code:
				//$attendance_array[$counter] = new xml_attendance();
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
	if(substr($current_tag,0,18)=='*ROOT*CHECK_IN_OUT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		if (!isset($check_in_out_array[$countercheckin])) {
			$check_in_out_array[$countercheckin] = new xml_check_in_out();
		}
		switch($current_tag){
			case $location_emp_code:
				//$check_in_out_array[$countercheckin] = new xml_check_in_out();
				$check_in_out_array[$countercheckin]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$check_in_out_array[$countercheckin]->location_trans_id = $data;
				break;
			case $location_latt:
				$check_in_out_array[$countercheckin]->location_latt = $data;
				break;
			case $location_longi:
				$check_in_out_array[$countercheckin]->location_longi = $data;
				break;
			case $location_date:
				$check_in_out_array[$countercheckin]->location_date = $data;
				break;
			case $trans_id:
				$check_in_out_array[$countercheckin]->trans_id = $data;
				break;
			case $check_in_time:
				$check_in_out_array[$countercheckin]->check_in_time = $data;
				break;
			case $customer_code:
				$check_in_out_array[$countercheckin]->customer_code = $data;
				break;
			case $check_out_time:
				$check_in_out_array[$countercheckin]->check_out_time = $data;
				break;
			case $remarks:
				$check_in_out_array[$countercheckin]->remarks = $data;
				break;
			case $hint_remarks:
				$check_in_out_array[$countercheckin]->hint_remarks = $data;
				break;	
			case $product_tagging:
				$check_in_out_array[$countercheckin]->product_tagging = $data;
				break;
			case $uploaded_photo:
				$check_in_out_array[$countercheckin]->uploaded_photo = $data;
				break;
			case $base_latt:
				$check_in_out_array[$countercheckin]->base_latt = $data;
				break;
			case $base_longi:
				$check_in_out_array[$countercheckin]->base_longi = $data;
				$countercheckin++;
				break;							
		}
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;
//print_r($data);die;
if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($attendance_array);
//echo count($attendance_array);
// print_r($order_array);
// print_r($order_details_array);
// print_r($check_in_out_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* --------------------------------------------------START QUERY FOR ATTENDANCE---------------------------------------------------------------------*/
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
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
            if($latt==""){$latt="0";}
            if($longi==""){$longi="0";}
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
					
					$last_operation_datetime=$attendance_date;
					// For Sending email to recipents for attendance
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
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
					// $address=getReverseGeo($latt,$longi);
					// $attendanceemailsubj="$nick_name - Attendance - ".$emp_name." on ".date('d-m-Y',strtotime($attendance_date))." @".date('H:i:s',strtotime($attendance_date)).' hrs.';
					// $attendancemailbody = "<html><head><title>Attendance</title></head>
					// 					<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
					// 					.$emp_name. "</b><br><br>".$emp_name." marked as present on <b>".date('d-m-Y H:i:s',strtotime($attendance_date))."</b> 
					// 					at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
					// $headers  = "MIME-Version: 1.0\r\n";
					// $headers .= "Content-type: text/html; charset=UTF-8\n";
					// $headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					// 			"Reply-To:".FROMEMAIL." \r\n" .
					// 			"Bcc: ".BCCEMAIL." \r\n".
					// 			'X-Mailer: PHP/' . phpversion();
					// if(mail($attendance_email, $attendanceemailsubj, $attendancemailbody, $headers,$spam_filter))
					// {
					// 	$flag=5;
					// }
					// else
					// {
					// 	mysqli_query($link,"ROLLBACK");
					// 	echo $flag=0;
					// 	return;
					// }
				}
				// else
				// {
				// 	mysqli_query($link,"ROLLBACK");
				// 	echo $flag=0;
				// 	return;
				// }
			
		}// End of else
	}// End for loop
}// End attendance array if 

 /* --------------------END QUERY FOR ATTENDANCE------------------------------------------------------------------------------------------------------------*/

/* --------------------START QUERY FOR Check in out------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$check_in_out_array_trans_id=array();
$check_in_out_trans_id_array=array();
if(count($check_in_out_array)>0)
{
	for($x=0;$x<count($check_in_out_array);$x++){

		$location_emp_code=$check_in_out_array[$x]->location_emp_code;
		$location_trans_id=$check_in_out_array[$x]->location_trans_id;
		$location_latt=$check_in_out_array[$x]->location_latt;
		$location_longi=$check_in_out_array[$x]->location_longi;
		$location_date=$check_in_out_array[$x]->location_date;
		$trans_id=$check_in_out_array[$x]->trans_id;
		$check_in_time=$check_in_out_array[$x]->check_in_time;
		$customer_code=$check_in_out_array[$x]->customer_code;
		$check_out_time=$check_in_out_array[$x]->check_out_time;
		$remarks=$check_in_out_array[$x]->remarks;
		$hint_remarks=$check_in_out_array[$x]->hint_remarks;
		$product_tagging=$check_in_out_array[$x]->product_tagging;
		$uploaded_photo=$check_in_out_array[$x]->uploaded_photo;
		$base_latt=$check_in_out_array[$x]->base_latt;
		$base_longi=$check_in_out_array[$x]->base_longi;				
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($location_latt>0 && $location_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
									emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}

		//For checking that trans id exist or not for check in out
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		//echo"<pre>";print_r($sqlchkorlocation);

		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check Product Promotion: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for check in out
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$check_in_out_array_trans_id))
			{
				array_push($check_in_out_array_trans_id,$location_trans_id);
			}
			if($location_latt==""){$location_latt="0";}
            if($location_longi==""){$location_longi="0";}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update Product Promotion location: ".$sqlupdateorlocation);
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
			//echo"<pre>";print_r('ss');die;

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

        if($location_latt==""){$location_latt="0";}
            if($location_longi==""){$location_longi="0";}
		//For Insert into the location table for new trans id regarding check in out
		echo $sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								updatetime='".$location_date_updatetime."'"; die;
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
		// Add new check in out
		$sqlcustomername="SELECT customer_name,base_latt,base_longi,need_location_update,branch_code,SAP_customer_code,cust_type,dns_customer_code FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomername=mysqli_query($link,$sqlcustomername);
		$rowcustomername=mysqli_fetch_assoc($rscustomername);
		$customer_name=$rowcustomername['customer_name'];
		$base_latt_db=$rowcustomername['base_latt'];
		$base_longi_db=$rowcustomername['base_longi'];
		$need_location_update=$rowcustomername['need_location_update'];
		$branch_code=$rowcustomername['branch_code'];
		$SAP_customer_code=$rowcustomername['SAP_customer_code'];
		$dns_customer_code=$rowcustomername['dns_customer_code'];	
		$cust_type=$rowcustomername['cust_type'];	
		//echo"<pre>";print_r($check_in_time);
        if($check_in_time==""){
            $sqlinsertcheckinout="INSERT INTO check_in_out_details SET trans_id ='".$trans_id."',
							   
							   customer_code				='".$customer_code."',
							   check_out_time				='".$check_out_time."',
							   hint_remarks 				='".$hint_remarks."',
							   product_tagging				='".$product_tagging."',
							   remarks						='".addslashes($remarks)."',
							   uploaded_photo				='".$uploaded_photo."'";
        }else{
		$sqlinsertcheckinout="INSERT INTO check_in_out_details SET trans_id ='".$trans_id."',
							   check_in_time				='".$check_in_time."',
							   customer_code				='".$customer_code."',
							   check_out_time				='".$check_out_time."',
							   hint_remarks 				='".$hint_remarks."',
							   product_tagging				='".$product_tagging."',
							   remarks						='".addslashes($remarks)."',
							   uploaded_photo				='".$uploaded_photo."'";
        }
		//echo"<pre>";print_r($sqlinsertcheckinout);

		if(mysqli_query($link,$sqlinsertcheckinout))
		{
			$flag=5;
			/*if(strtoupper($nick_name)=='STAR')
			{
				if($need_location_update=='yes'){
					if($base_latt!='' &&  $base_longi!='')
					{
						$upd_sql="UPDATE customer_master SET base_latt='".$base_latt."',base_longi='".$base_longi."',need_location_update='no',
									download_time=CURRENT_TIMESTAMP() WHERE customer_code = '" .$customer_code."'";
						mysqli_query($link,$upd_sql);
					}
				}
				$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
			}*/
			if(strtoupper($nick_name)=='STAR')
			{
				if(strtoupper($cust_type)=='DEALER')
				{
					${'dealer_visit_survey'.$trans_id}.=$dns_customer_code."#".$SAP_customer_code."#".$emp_code."#".$emp_name."#".$check_out_time;
					if(!in_array($location_trans_id,$check_in_out_trans_id_array))
					{
						array_push($check_in_out_trans_id_array,$location_trans_id);
					}
				}
				$sqlselgeofencing="SELECT geo_fencing FROM branchwise_geo_fencing WHERE branch_code='".$branch_code."'";
				$rsselgeofencing=mysqli_query($link,$sqlselgeofencing);
				$rowselgeofencing=mysqli_fetch_assoc($rsselgeofencing);
				$geo_fencing = $rowselgeofencing['geo_fencing'];

				if($base_latt_db==0 && $base_longi_db==0 && $geo_fencing=='YES')
				{
					$upd_sql="UPDATE customer_master SET base_latt='".$location_latt."',base_longi='".$location_longi."',download_time=CURRENT_TIMESTAMP() 
							WHERE customer_code = '" .$customer_code."'";
					mysqli_query($link,$upd_sql);
				}
				$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
			}
			else if(strtoupper($nick_name)=='START')
			{
				$sqlsaleaccess="SELECT sale_access FROM employee_master WHERE emp_code='".$emp_code."'";
				$rssaleaccress=mysqli_query($link,$sqlsaleaccess);
				$rowsaleaccess=mysqli_fetch_assoc($rssaleaccress);
				$sale_access=$rowsaleaccess['sale_access'];
				$departmentwise_geo_fencing_array=explode('#',departmentwise_geo_fencing_variance);
				if(in_array($sale_access,$departmentwise_geo_fencing_array))
				{
					if($base_latt_db==0 && $base_longi_db==0 )
					{
						$upd_sql="UPDATE customer_master SET base_latt='".$location_latt."',base_longi='".$location_longi."',download_time=CURRENT_TIMESTAMP() 
								WHERE customer_code = '" .$customer_code."'";
						mysqli_query($link,$upd_sql);
					}
					$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
					$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
				}
				else
				{
				$sqlselgeofencing="SELECT geo_fencing FROM branchwise_geo_fencing WHERE branch_code='".$branch_code."'";
				$rsselgeofencing=mysqli_query($link,$sqlselgeofencing);
				$rowselgeofencing=mysqli_fetch_assoc($rsselgeofencing);
				$geo_fencing = $rowselgeofencing['geo_fencing'];

				if($base_latt_db==0 && $base_longi_db==0 && $geo_fencing=='YES')
				{
					$upd_sql="UPDATE customer_master SET base_latt='".$location_latt."',base_longi='".$location_longi."',download_time=CURRENT_TIMESTAMP() 
							WHERE customer_code = '" .$customer_code."'";
					mysqli_query($link,$upd_sql);
				}
				$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
				}
			}
			else if(strtoupper($nick_name)=='ABDOS')
			{
				if($base_latt_db==0 && $base_longi_db==0)
				{
					$upd_sql="UPDATE customer_master SET base_latt='".$location_latt."',base_longi='".$location_longi."',download_time=CURRENT_TIMESTAMP() 
							WHERE customer_code = '" .$customer_code."'";
					mysqli_query($link,$upd_sql);
				}
				$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				//$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
			}
			else if(strtoupper($nick_name)=='PALSONS')
			{
				if($base_latt_db==0 && $base_longi_db==0)
				{
					$upd_sql="UPDATE customer_master SET base_latt='".$location_latt."',base_longi='".$location_longi."',download_time=CURRENT_TIMESTAMP() 
							WHERE customer_code = '" .$customer_code."'";
					mysqli_query($link,$upd_sql);
				}
				$sqlupdatecustomer="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
			}
			else
			{
			    if($base_longi=="" && $base_latt ==""){
			        
			    }else if($base_longi==" " && $base_latt ==" "){
			        
			    }else{
				$sqlupdatecustomer="UPDATE customer_master SET base_latt='".$base_latt."',base_longi='".$base_longi."',
									download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
									// $sqlupdatecustomer;
				$rsupdatecustomer=mysqli_query($link,$sqlupdatecustomer);
			    }
			}
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
		$operation_date=$date.'-'.$month.'-'.$year.' @ '.$hour.':'.$minute.':'.$second;
		
		
		$time_difference=strtotime($check_out_time)-strtotime($check_in_time);
		if($time_difference >=3600)
		{
			$hours = floor($time_difference / 3600);
			$minutes = floor(($time_difference / 60) % 60);
			$seconds = $time_difference % 60;
			$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
		}
		else if($time_difference >=60 && $time_difference<3600)
		{
			$minutes = floor(($time_difference / 60) % 60);
			$seconds = $time_difference % 60;
			$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
		}
		else
		{
			$seconds = $time_difference % 60;
			$time_duration=$seconds.' Second(s)';
		}
		$checkinoutmailsubj="$nick_name - ​Customer Activity by ".$emp_name." on ".date('d-m-Y',strtotime($location_date))." @".date('H:i:s',strtotime($location_date)).' hrs.';
		
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
		$checkinoutmailbody = $check_in_out_TR.$check_in_out_TD."
								<br><br><br>Powered By aceDNS<br>";
		$check_in_out_email=ATTENDANCEEMAILRECIPENTS;
		
		if($check_in_out_email!==''){
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Reply-To:".FROMEMAIL." \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		// if(mail($check_in_out_email, $checkinoutmailsubj, $checkinoutmailbody, $headers,$spam_filter))
		// {
		// 	$flag=5;
		// }
		// else
		// {
		// 	mysqli_query($link,"ROLLBACK");
		// 	echo $flag=0;
		// 	return;
		// }
		}
	  }//End of else
	}
}
 /* --------------------END QUERY For Check in out--------------------------------------------------------------------------------------------------------*/
 if($last_operation_datetime==""){
     $last_operation_datetime = date("Y-m-d H:i:s");
 }
 
 
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
$url = APICALLLOGURL."/operationdb-check-in-out-6.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-product-promotion.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
//print_r($check_in_out_trans_id_array);
// if(strtoupper($nick_name)=='STAR')
// {
// 	define("SERVERREMOTE","103.87.174.95");
// 	define("USERREMOTE","starsaat_dnsprod");
// 	define("PASSWORDREMOTE","dnsprod1234#");
// 	define("DBREMOTE","starsaathi_STARS");
		
// 	$conn=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	
// 	foreach($check_in_out_trans_id_array as $check_in_out_trans_id_val){
// 		$dealer_visit_survey_parts=explode('#',${'dealer_visit_survey'.$check_in_out_trans_id_val});
// 		$dns_customer_code_parts=$dealer_visit_survey_parts[0];
// 		$SAP_customer_code_parts=$dealer_visit_survey_parts[1];
// 		$emp_code_parts=$dealer_visit_survey_parts[2];
// 		$emp_name_parts=$dealer_visit_survey_parts[3];
// 		$check_out_time_parts=$dealer_visit_survey_parts[4];
		
// 		 $sqlchkvisit="SELECT customer_code FROM dealer_sales_team_visit_survey where sap_customer_code='".$SAP_customer_code_parts."' AND emp_code='".$emp_code_parts."' AND visit_datetime='".$check_out_time_parts."' ";
// 		 $rschkvisit=mysqli_query($conn,$sqlchkvisit);
// 		 $countchkvisit=mysqli_num_rows($rschkvisit);
// 		if($countchkvisit==0){
// 			$sqlinsertchkvisit="INSERT INTO dealer_sales_team_visit_survey SET customer_code='".$dns_customer_code_parts."',
// 							   sap_customer_code			='".$SAP_customer_code_parts."',
// 							   emp_code 					='".$emp_code_parts."',
// 							   emp_name						='".$emp_name_parts."',
// 							   visit_datetime				='".$check_out_time_parts."'";
// 			 $rsinsertchkvisit=mysqli_query($conn,$sqlinsertchkvisit);
// 		}
// 	}
// 	mysqli_close($conn);

// }
?>
