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
$body=file_get_contents('php://input');


/*$body="<?xml version='1.0' encoding='UTF-8'?><root><loyalty><location><loyalty_card_no><![CDATA[900010762892]]></loyalty_card_no><trans_id><![CDATA[LC000720140615182914]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5644859]]></latt><longi><![CDATA[88.3565915]]></longi><date><![CDATA[2014-06-15 18:29:14]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140615182914]]></transaction_id><purchase_value><![CDATA[6500]]></purchase_value><outlet_code><![CDATA[O0002]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[900010762894]]></loyalty_card_no><trans_id><![CDATA[LC000720140615183055]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5645232]]></latt><longi><![CDATA[88.3566062]]></longi><date><![CDATA[2014-06-15 18:30:55]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140615183055]]></transaction_id><purchase_value><![CDATA[3500]]></purchase_value><outlet_code><![CDATA[O0004]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[900010762894]]></loyalty_card_no><trans_id><![CDATA[LC000720140615183128]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5646244]]></latt><longi><![CDATA[88.3566697]]></longi><date><![CDATA[2014-06-15 18:31:28]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140615183128]]></transaction_id><purchase_value><![CDATA[650]]></purchase_value><outlet_code><![CDATA[O0006]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[900010762876]]></loyalty_card_no><trans_id><![CDATA[LC000720140615183205]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5645391]]></latt><longi><![CDATA[88.3566373]]></longi><date><![CDATA[2014-06-15 18:32:05]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140615183205]]></transaction_id><purchase_value><![CDATA[65000]]></purchase_value><outlet_code><![CDATA[O0007]]></outlet_code></loyaltydata></loyalty><loyalty><location><loyalty_card_no><![CDATA[900010762875]]></loyalty_card_no><trans_id><![CDATA[LC000720140615183406]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5645463]]></latt><longi><![CDATA[88.3566837]]></longi><date><![CDATA[2014-06-15 18:34:06]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140615183406]]></transaction_id><purchase_value><![CDATA[2588]]></purchase_value><outlet_code><![CDATA[O0002]]></outlet_code></loyaltydata></loyalty></root>";

$body="<?xml version='1.0' encoding='UTF-8'?><root><loyalty><location><loyalty_card_no><![CDATA[900010762889]]></loyalty_card_no><trans_id><![CDATA[LC000720140609162812]]></trans_id><outlet_user_code><![CDATA[C0007]]></outlet_user_code><latt><![CDATA[22.5644568]]></latt><longi><![CDATA[88.3568016]]></longi><date><![CDATA[2014-06-09 16:28:12]]></date></location><loyaltydata><transaction_id><![CDATA[LC000720140609162812]]></transaction_id><purchase_value><![CDATA[25]]></purchase_value><trans_type><![CDATA[FMCG]]></trans_type><outlet_code><![CDATA[O0007]]></outlet_code></loyaltydata></loyalty></root>";*/


$location_loyalty_card_no = "*ROOT*LOYALTY*LOCATION*LOYALTY_CARD_NO";
$loyalty_trans_id = "*ROOT*LOYALTY*LOCATION*TRANS_ID";
$loyalty_user_code = "*ROOT*LOYALTY*LOCATION*OUTLET_USER_CODE";
$loyalty_latt = "*ROOT*LOYALTY*LOCATION*LATT";
$loyalty_longi = "*ROOT*LOYALTY*LOCATION*LONGI";
$loyalty_date = "*ROOT*LOYALTY*LOCATION*DATE";
$loyaltydata_transaction_id = "*ROOT*LOYALTY*LOYALTYDATA*TRANSACTION_ID";
$loyaltydata_purchase_value = "*ROOT*LOYALTY*LOYALTYDATA*PURCHASE_VALUE";
$loyaltydata_trans_type = "*ROOT*LOYALTY*LOYALTYDATA*TRANS_TYPE";
$loyaltydata_outlet_code = "*ROOT*LOYALTY*LOYALTYDATA*OUTLET_CODE";

$loyalty_array = array();
$counter = 0;

class xml_loyalty{
	var $location_loyalty_card_no,$loyalty_trans_id,$loyalty_user_code,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_outlet_code;	
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
    global $current_tag,$counter,$location_loyalty_card_no,$loyalty_trans_id,$loyalty_user_code,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_outlet_code,$loyalty_array;
	/*echo $current_tag.'<br />';
	echo $data;*/
	if(substr($current_tag,0,13)=='*ROOT*LOYALTY')
	{
	/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $location_loyalty_card_no:
				$loyalty_array[$counter] = new xml_loyalty();
				$loyalty_array[$counter]->location_loyalty_card_no = $data;
				break;
			case $loyalty_trans_id:
				$loyalty_array[$counter]->loyalty_trans_id = $data;
				break;
			case $loyalty_user_code:
				$loyalty_array[$counter]->loyalty_user_code = $data;
				break;	
			case $loyalty_latt:
				$loyalty_array[$counter]->loyalty_latt = $data;
				break;
			case $loyalty_longi:
				$loyalty_array[$counter]->loyalty_longi = $data;
				break;
			case $loyalty_date:
				$loyalty_array[$counter]->loyalty_date = $data;
				break;
			case $loyaltydata_transaction_id:
				$loyalty_array[$counter]->loyaltydata_transaction_id = $data;
				break;
			case $loyaltydata_purchase_value:
				$loyalty_array[$counter]->loyaltydata_purchase_value = $data;
				break;	
			case $loyaltydata_trans_type:
				$loyalty_array[$counter]->loyaltydata_trans_type = $data;
				break;	
			case $loyaltydata_outlet_code:
				$loyalty_array[$counter]->loyaltydata_outlet_code = $data;
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
//print_r($loyalty_array);
//echo count($loyalty_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* ------------------------------------------------START QUERY FOR LOYALTY---------------------------------------------------------------------*/
if(count($loyalty_array)>0)
{
	for($x=0;$x<count($loyalty_array);$x++){
		$location_loyalty_card_no=$loyalty_array[$x]->location_loyalty_card_no;
		$loyalty_trans_id=$loyalty_array[$x]->loyalty_trans_id;
		$loyalty_user_code=$loyalty_array[$x]->loyalty_user_code;
		$loyalty_latt=$loyalty_array[$x]->loyalty_latt;
		$loyalty_longi=$loyalty_array[$x]->loyalty_longi;
		$loyalty_date=$loyalty_array[$x]->loyalty_date;
		$loyaltydata_transaction_id=$loyalty_array[$x]->loyaltydata_transaction_id;
		$loyaltydata_purchase_value=$loyalty_array[$x]->loyaltydata_purchase_value;
		$loyaltydata_trans_type=$loyalty_array[$x]->loyaltydata_trans_type;
		$loyaltydata_outlet_code=$loyalty_array[$x]->loyaltydata_outlet_code;

		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($loyalty_latt>0 && $loyalty_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE loyalty_location_transaction SET latt='".$loyalty_latt."',longi='".$loyalty_longi."' WHERE emp_code='".$loyalty_user_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkloyaltylocation="SELECT * FROM loyalty_location_transaction WHERE transaction_id='".$loyalty_trans_id."'";
		$reschkloyaltylocation = mysqli_query($link,$sqlchkloyaltylocation) or die(mysqli_error()." Error in check loyalty location: ".$sqlchkattlocation); 
		$rowchkloyaltylocation = mysqli_fetch_assoc($reschkloyaltylocation);
		$countchkloyaltylocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkloyaltylocation>0)
		{
			$sqlupdateloyaltylocation="UPDATE loyalty_location_transaction SET emp_code='".$loyalty_user_code."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."'
									WHERE transaction_id='".$loyalty_trans_id."'";
			$rsupdateloyaltylocation=mysqli_query($link,$sqlupdateloyaltylocation) or die(mysqli_error()." Error in update loyalty location: ".$sqlupdateloyaltylocation);
			if($rsupdateloyaltylocation)
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
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertloyaltylocation="INSERT INTO loyalty_location_transaction SET emp_code='".$loyalty_user_code."',
									transaction_id='".$loyalty_trans_id."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."',
									date='".$loyalty_date."',
									update_time='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertloyaltycard="INSERT INTO card_transaction SET transaction_id='".$loyaltydata_transaction_id."',
									loyalty_card_no='".$location_loyalty_card_no."',
									outlet_code='".$loyaltydata_outlet_code."',
									trans_type='".$loyaltydata_trans_type."',
								 purchase_value='".$loyaltydata_purchase_value."'";	
			if(mysqli_query($link,$sqlinsertloyaltylocation) && mysqli_query($link,$sqlinsertloyaltycard))
				{
					$flag=5;
					
					// For Sending email to recipents for attendance
				 	$sqloutletname="SELECT outlet_name FROM outlet_master WHERE outlet_code='".$loyaltydata_outlet_code."'";
					$rsoutletname=mysqli_query($link,$sqloutletname);
					$rowoutletname=mysqli_fetch_assoc($rsoutletname);
					$outlet_name=$rowoutletname['outlet_name'];
					
					$sqlcardholderdetails="SELECT loyalty_card_holder_name,total_reward_point FROM loyalty_card_holder_master WHERE loyalty_card_no='".$location_loyalty_card_no."'";
					$rscardholderdetails=mysqli_query($link,$sqlcardholderdetails);
					$rowcardholderdetails=mysqli_fetch_assoc($rscardholderdetails);
					$loyalty_card_holder_name=$rowcardholderdetails['loyalty_card_holder_name'];
					$total_reward_point=$rowcardholderdetails['total_reward_point'];

					$address=getReverseGeo($latt,$longi);
					$loyaltyemailsubj="Transaction of - ".$outlet_name." on ".date('d-m-Y',strtotime($loyalty_date))." @".date('H:i:s',strtotime($loyalty_date)).' hrs.';
					$loyaltyemailbody = "<html><head><title>Loyalty</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application for <b>LOYALTY POINTS</b>.<br /><br /><br />
										<b>Vertical: " .strtoupper($loyaltydata_trans_type). "</b><br /><br />
										<b>Outlet Name: " .$outlet_name. "</b><br /><br />
										<b>Card Holder Name: " .$loyalty_card_holder_name. "</b><br /><br />
										<b>Card Holder No.: " .$location_loyalty_card_no. "</b><br /><br />
										<b>Purchase Value: " .number_format($loyaltydata_purchase_value,2). "</b><br /><br />
										<b>Accumulated Point: " .number_format($total_reward_point,2). " (Excluding this transaction)</b><br /><br />
										</table><br /><br />Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail(LOYALTYEMAILRECIPENTS, $loyaltyemailsubj, $loyaltyemailbody, $headers,'-facedns@coral.in'))
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
}// End attendance array if 

 /* --------------------END QUERY FOR LOYALTY------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 mysqli_query($link,"COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
	mysqli_query($link,"COMMIT");
	echo $flag=1;
}
?>
