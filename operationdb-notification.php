<?php
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

$notification_emp_code = "*ROOT*NOTIFICATION*LOCATION*EMP_CODE";
$notification_trans_id = "*ROOT*NOTIFICATION*LOCATION*TRANS_ID";
$notification_latt = "*ROOT*NOTIFICATION*LOCATION*LATT";
$notification_longi = "*ROOT*NOTIFICATION*LOCATION*LONGI";
$notification_date = "*ROOT*NOTIFICATION*LOCATION*DATE";
$notification_id = "*ROOT*NOTIFICATION*LOCATION*NOTIFICATION_ID";

$notification_array = array();

$counter = 0;

class xml_notification{
    var $notification_emp_code, $notification_trans_id,$notification_latt,$notification_longi,$notification_date,$notification_id;
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
    global $current_tag, $notification_emp_code, $notification_trans_id,$notification_latt,$notification_longi,$notification_date,$notification_id,$counter,$notification_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,18)=='*ROOT*NOTIFICATION')
	{
		switch($current_tag){
			case $notification_emp_code:
				$notification_array[$counter] = new xml_notification();
				$notification_array[$counter]->notification_emp_code = $data;
				break;
			case $notification_trans_id:
				$notification_array[$counter]->notification_trans_id = $data;
				break;
			case $notification_latt:
				$notification_array[$counter]->notification_latt = $data;
				break;
			case $notification_longi:
				$notification_array[$counter]->notification_longi = $data;
				break;
			case $notification_date:
				$notification_array[$counter]->notification_date = $data;
				break;
			case $notification_id:
				$notification_array[$counter]->notification_id = $data;
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
//print_r($checkout_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");

$flag=1;
/* ------------------------------------------------START QUERY FOR NOTIFICATION-----------------------------------------------------------------------------*/
if(count($notification_array)>0)
{
	for($x=0;$x<count($notification_array);$x++){
		$notification_emp_code=$notification_array[$x]->notification_emp_code;
		$notification_trans_id=$notification_array[$x]->notification_trans_id;
		$notification_latt=$notification_array[$x]->notification_latt;
		$notification_longi=$notification_array[$x]->notification_longi;
		$notification_date=$notification_array[$x]->notification_date;
		$notification_id=$notification_array[$x]->notification_id;
		
		//For updating the lattitude and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$notification_latt."',longi='".$notification_longi."' 
									WHERE emp_code='".$notification_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not
		$sqlchkchecklocation="SELECT * FROM location WHERE trans_id='".$notification_trans_id."'";
		$reschkchecklocation = mysqli_query($link,$sqlchkchecklocation) or die(mysqli_error()." Error in check checkout location: ".$sqlchkchecklocation); 
		$rowchkchecklocation = mysqli_fetch_assoc($reschkchecklocation);
		$countchkchecklocation=mysqli_num_rows($reschkchecklocation);
		
		//For update the location table for existing trans id
		if($countchkchecklocation>0)
		{
			$sqlupdatechecklocation="UPDATE location SET emp_code='".$notification_emp_code."',
									latt='".$notification_latt."',
									longi='".$notification_longi."'
									WHERE trans_id='".$notification_trans_id."'";
			$rsupdatechecklocation=mysqli_query($link,$sqlupdatechecklocation) or die(mysqli_error()." Error in update checkout location: ".$sqlupdatechecklocation);
			if($rsupdatechecklocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of checkout
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertchecklocation="INSERT INTO location SET emp_code='".$notification_emp_code."',
									trans_id='".$notification_trans_id."',
									latt='".$notification_latt."',
									longi='".$notification_longi."',
									date='".$notification_date."',
									updatetime='".$location_date."'";
			$sqlupdatenotificationack="UPDATE notification_ack_relation SET ack_id='".$notification_trans_id."' 
									  WHERE notification_id='".$notification_id."' AND receiver_id='".$emp_code."'";						
			if(mysqli_query($link,$sqlinsertchecklocation) && mysqli_query($link,$sqlupdatenotificationack))
				{
					$flag=5;
					
					$last_operation_datetime=$notification_date;
					// For Sending email to recipents for checkout
				 	/*$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$notification_emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
					$vertical_value=$rowempname['vertical_value'];
					$branch_code=$rowempname['branch_code'];
					
					if(branch_vertical_operation_wise_email=='yes')
					{
						$operation_type='Attendance';
						$checkout_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
					}
					else
					{
						$checkout_email=ATTENDANCEEMAILRECIPENTS;
					}
					$address=getReverseGeo($checkout_latt,$checkout_longi);
					$checkoutemailsubj="Check out - ".$emp_name." on ".date('d-m-Y',strtotime($checkout_date))." @".date('H:i:s',strtotime($checkout_date)).' hrs.';
					$checkoutmailbody = "<html><head><title>Checkout</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." marked as cheked out on <b>".date('d-m-Y H:i:s',strtotime($checkout_date))."</b> 
										at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($checkout_email, $checkoutemailsubj, $checkoutmailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}*/
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
			
		}// End of else
	}// End for loop
}// End checkout array if 

 /* ---------------------------------------------END QUERY FOR NOTIFICATION-----------------------------------------------------------------------------------*/
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
$url = APICALLLOGURL."/operationdb-notification.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-notification.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
?>
