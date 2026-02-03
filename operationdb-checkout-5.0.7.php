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
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><checkout><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[CHE000220150307142310]]></trans_id><latt><![CDATA[22.5641849]]></latt><longi><![CDATA[88.3569343]]></longi><date><![CDATA[2015-03-07 14:23:10]]></date></location><checkout_data><emp_code><![CDATA[E0002]]></emp_code><date><![CDATA[2015-03-07]]></date></checkout_data></checkout></root>";*/

$checkout_emp_code = "*ROOT*CHECKOUT*LOCATION*EMP_CODE";
$checkout_trans_id = "*ROOT*CHECKOUT*LOCATION*TRANS_ID";
$checkout_latt = "*ROOT*CHECKOUT*LOCATION*LATT";
$checkout_longi = "*ROOT*CHECKOUT*LOCATION*LONGI";
$checkout_date = "*ROOT*CHECKOUT*LOCATION*DATE";
$checkoutdata_emp_code = "*ROOT*CHECKOUT*CHECKOUT_DATA*EMP_CODE";
$checkoutdata_date = "*ROOT*CHECKOUT*CHECKOUT_DATA*DATE";

$checkout_array = array();

$counter = 0;

class xml_checkout{
    var $checkout_emp_code, $checkout_trans_id,$checkout_latt,$checkout_longi,$checkout_date,$checkoutdata_emp_code,$checkoutdata_date;
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
    global $current_tag, $checkout_emp_code, $checkout_trans_id,$checkout_latt,$checkout_longi,$checkout_date,$checkoutdata_emp_code,
			$checkoutdata_date, $counter,$checkout_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,14)=='*ROOT*CHECKOUT')
	{
		switch($current_tag){
			case $checkout_emp_code:
				$checkout_array[$counter] = new xml_checkout();
				$checkout_array[$counter]->checkout_emp_code = $data;
				break;
			case $checkout_trans_id:
				$checkout_array[$counter]->checkout_trans_id = $data;
				break;
			case $checkout_latt:
				$checkout_array[$counter]->checkout_latt = $data;
				break;
			case $checkout_longi:
				$checkout_array[$counter]->checkout_longi = $data;
				break;
			case $checkout_date:
				$checkout_array[$counter]->checkout_date = $data;
				break;
			case $checkoutdata_emp_code:
				$checkout_array[$counter]->checkoutdata_emp_code = $data;
				break;
			case $checkoutdata_date:
				$checkout_array[$counter]->checkoutdata_date = $data;
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
/* ------------------------------------------------START QUERY FOR CHECKOUT-----------------------------------------------------------------------------*/
if(count($checkout_array)>0)
{
	for($x=0;$x<count($checkout_array);$x++){
		$checkout_emp_code=$checkout_array[$x]->checkout_emp_code;
		$checkout_trans_id=$checkout_array[$x]->checkout_trans_id;
		$checkout_latt=$checkout_array[$x]->checkout_latt;
		$checkout_longi=$checkout_array[$x]->checkout_longi;
		$checkout_date=$checkout_array[$x]->checkout_date;
		$checkoutdata_emp_code=$checkout_array[$x]->checkoutdata_emp_code;
		$checkoutdata_date=$checkout_array[$x]->checkoutdata_date;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$checkout_latt."',longi='".$checkout_longi."' 
									WHERE emp_code='".$checkoutdata_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkchecklocation="SELECT * FROM location WHERE trans_id='".$checkout_trans_id."'";
		$reschkchecklocation = mysqli_query($link,$sqlchkchecklocation) or die(mysqli_error()." Error in check checkout location: ".$sqlchkchecklocation); 
		$rowchkchecklocation = mysqli_fetch_assoc($reschkchecklocation);
		$countchkchecklocation=mysqli_num_rows($reschkchecklocation);
		
		//For update the location table for existing trans id
		if($countchkchecklocation>0)
		{
			$sqlupdatechecklocation="UPDATE location SET emp_code='".$checkout_emp_code."',
									latt='".$checkout_latt."',
									longi='".$checkout_longi."'
									WHERE trans_id='".$checkout_trans_id."'";
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
			$sqlinsertchecklocation="INSERT INTO location SET emp_code='".$checkout_emp_code."',
									trans_id='".$checkout_trans_id."',
									latt='".$checkout_latt."',
									longi='".$checkout_longi."',
									date='".$checkout_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the checkout table for new trans id
			$sqlinsertcheckout="INSERT INTO attendence SET emp_code='".$checkout_emp_code."',
								trans_id='".$checkout_trans_id."',
								 date='".$checkoutdata_date."'";	
			if(mysqli_query($link,$sqlinsertchecklocation) && mysqli_query($link,$sqlinsertcheckout))
				{
					$flag=5;
					
					$last_operation_datetime=$checkout_date;
					// For Sending email to recipents for checkout
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
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
					}
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

 /* ---------------------------------------------END QUERY FOR CHECKOUT-----------------------------------------------------------------------------------*/
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
$url = "http://www.acedns.in/acednsproduct/operationdb-checkout-5.0.7.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-checkout-5.0.7.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
