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

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');


/*$body="<?xml version='1.0' encoding='UTF-8'?><root><loyalty><location><emp_code><![CDATA[C0008]]></emp_code><trans_id><![CDATA[LC000820141029165139]]></trans_id><latt><![CDATA[22.5641811]]></latt><longi><![CDATA[88.3569741]]></longi><date><![CDATA[2014-10-29 16:51:39]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LC000820141029165139]]></transaction_id><loyalty_card_holder_code><![CDATA[LC000820141029165131]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Sbasu]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[987654321]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-10-29 16:51:31]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8981477017]]></phone_no><address><![CDATA[kol]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LC000820141029165139]]></transaction_id><purchase_value><![CDATA[2582.85]]></purchase_value><trans_type><![CDATA[]]></trans_type><rds_code><![CDATA[C/0000236]]></rds_code></loyaltydata></loyalty><loyalty><location><emp_code><![CDATA[C0008]]></emp_code><trans_id><![CDATA[LC000820141029165556]]></trans_id><latt><![CDATA[22.5642086]]></latt><longi><![CDATA[88.3570727]]></longi><date><![CDATA[2014-10-29 16:55:56]]></date></location><loyaltycustomerdata><transaction_id><![CDATA[LC000820141029165556]]></transaction_id><loyalty_card_holder_code><![CDATA[LC000820141029165131]]></loyalty_card_holder_code><loyalty_card_holder_name><![CDATA[Sbasu]]></loyalty_card_holder_name><loyalty_card_no><![CDATA[987654321]]></loyalty_card_no><card_type><![CDATA[]]></card_type><total_purchase_value><![CDATA[0]]></total_purchase_value><total_reward_point><![CDATA[0]]></total_reward_point><last_update_on><![CDATA[2014-10-29 16:51:31]]></last_update_on><redeemed><![CDATA[no]]></redeemed><phone_no><![CDATA[8981477017]]></phone_no><address><![CDATA[kol]]></address></loyaltycustomerdata><loyaltydata><transaction_id><![CDATA[LC000820141029165556]]></transaction_id><purchase_value><![CDATA[3256.00]]></purchase_value><trans_type><![CDATA[]]></trans_type><rds_code><![CDATA[C/0000236]]></rds_code></loyaltydata></loyalty></root>
";*/

$loyalty_user_code = "*ROOT*LOYALTY*LOCATION*EMP_CODE";
$loyalty_trans_id = "*ROOT*LOYALTY*LOCATION*TRANS_ID";
$loyalty_latt = "*ROOT*LOYALTY*LOCATION*LATT";
$loyalty_longi = "*ROOT*LOYALTY*LOCATION*LONGI";
$loyalty_date = "*ROOT*LOYALTY*LOCATION*DATE";
$loyaltycustdata_transaction_id = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TRANSACTION_ID";
$loyaltycustdata_card_holder_code = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_HOLDER_CODE";
$loyaltycustdata_card_holder_name = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_HOLDER_NAME";
$loyaltycustdata_card_no = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LOYALTY_CARD_NO";
$loyaltycustdata_card_type = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*CARD_TYPE";
$loyaltycustdata_total_purchase_value = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TOTAL_PURCHASE_VALUE";
$loyaltycustdata_total_reward_point = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*TOTAL_REWARD_POINT";
$loyaltycustdata_last_update_on = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*LAST_UPDATE_ON";
$loyaltycustdata_redeemed = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*REDEEMED";
$loyaltycustdata_phone_no = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*PHONE_NO";
$loyaltycustdata_address = "*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*ADDRESS";
$loyaltycustdata_vehicle_no="*ROOT*LOYALTY*LOYALTYCUSTOMERDATA*VEHICLE_NO";
$loyaltydata_transaction_id = "*ROOT*LOYALTY*LOYALTYDATA*TRANSACTION_ID";
$loyaltydata_purchase_value = "*ROOT*LOYALTY*LOYALTYDATA*PURCHASE_VALUE";
$loyaltydata_trans_type = "*ROOT*LOYALTY*LOYALTYDATA*TRANS_TYPE";
$loyaltydata_rds_code = "*ROOT*LOYALTY*LOYALTYDATA*RDS_CODE";

$loyalty_array = array();
$counter = 0;

class xml_loyalty{
	var $loyalty_trans_id,$loyalty_user_code,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltycustdata_transaction_id,$loyaltycustdata_card_holder_code,$loyaltycustdata_card_holder_name,$loyaltycustdata_card_no,$loyaltycustdata_card_type,$loyaltycustdata_total_purchase_value,$loyaltycustdata_total_reward_point,$loyaltycustdata_last_update_on,$loyaltycustdata_redeemed,$loyaltycustdata_phone_no,$loyaltycustdata_address,$loyaltycustdata_vehicle_no,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_rds_code;	
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
    global $current_tag,$counter,$loyalty_user_code,$loyalty_trans_id,$loyalty_latt,$loyalty_longi,$loyalty_date,$loyaltycustdata_transaction_id,$loyaltycustdata_card_holder_code,$loyaltycustdata_card_holder_name,$loyaltycustdata_card_no,$loyaltycustdata_card_type,$loyaltycustdata_total_purchase_value,$loyaltycustdata_total_reward_point,$loyaltycustdata_last_update_on,$loyaltycustdata_redeemed,$loyaltycustdata_phone_no,$loyaltycustdata_address,$loyaltycustdata_vehicle_no,$loyaltydata_transaction_id,$loyaltydata_purchase_value,$loyaltydata_trans_type,$loyaltydata_rds_code,$loyalty_array;
	/*echo $current_tag.'<br />';
	echo $data;*/
	if(substr($current_tag,0,13)=='*ROOT*LOYALTY')
	{
	/*echo $current_tag.'<br />';
		echo $data.'<br />';*/
		switch($current_tag){
			case $loyalty_user_code:
				$loyalty_array[$counter] = new xml_loyalty();
				$loyalty_array[$counter]->loyalty_user_code = $data;
				break;
			case $loyalty_trans_id:
				$loyalty_array[$counter]->loyalty_trans_id = $data;
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
			case $loyaltycustdata_transaction_id:
				$loyalty_array[$counter]->loyaltycustdata_transaction_id = $data;
				break;
			case $loyaltycustdata_card_holder_code:
				$loyalty_array[$counter]->loyaltycustdata_card_holder_code = $data;
				break;
			case $loyaltycustdata_card_holder_name:
				$loyalty_array[$counter]->loyaltycustdata_card_holder_name = $data;
				break;
			case $loyaltycustdata_card_no:
				$loyalty_array[$counter]->loyaltycustdata_card_no = $data;
				break;	
			case $loyaltycustdata_card_type:
				$loyalty_array[$counter]->loyaltycustdata_card_type = $data;
				break;
			case $loyaltycustdata_total_purchase_value:
				$loyalty_array[$counter]->loyaltycustdata_total_purchase_value = $data;
				break;
			case $loyaltycustdata_total_reward_point:
				$loyalty_array[$counter]->loyaltycustdata_total_reward_point = $data;
				break;
			case $loyaltycustdata_last_update_on:
				$loyalty_array[$counter]->loyaltycustdata_last_update_on = $data;
				break;
			case $loyaltycustdata_redeemed:
				$loyalty_array[$counter]->loyaltycustdata_redeemed = $data;
				break;
			case $loyaltycustdata_phone_no:
				$loyalty_array[$counter]->loyaltycustdata_phone_no = $data;
				break;
			case $loyaltycustdata_address:
				$loyalty_array[$counter]->loyaltycustdata_address = $data;
				break;	
			case $loyaltycustdata_vehicle_no:
				$loyalty_array[$counter]->loyaltycustdata_vehicle_no = $data;
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
			case $loyaltydata_rds_code:
				$loyalty_array[$counter]->loyaltydata_rds_code = $data;
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
		$loyalty_trans_id=$loyalty_array[$x]->loyalty_trans_id;
		$loyalty_user_code=$loyalty_array[$x]->loyalty_user_code;
		$loyalty_latt=$loyalty_array[$x]->loyalty_latt;
		$loyalty_longi=$loyalty_array[$x]->loyalty_longi;
		$loyalty_date=$loyalty_array[$x]->loyalty_date;
		$loyaltycustdata_transaction_id=$loyalty_array[$x]->loyaltycustdata_transaction_id;
		$loyaltycustdata_card_holder_code=$loyalty_array[$x]->loyaltycustdata_card_holder_code;
		$loyaltycustdata_card_holder_name=$loyalty_array[$x]->loyaltycustdata_card_holder_name;
		$loyaltycustdata_card_no=$loyalty_array[$x]->loyaltycustdata_card_no;
		$loyaltycustdata_card_type=$loyalty_array[$x]->loyaltycustdata_card_type;
		$loyaltycustdata_total_purchase_value=$loyalty_array[$x]->loyaltycustdata_total_purchase_value;
		$loyaltycustdata_total_reward_point=$loyalty_array[$x]->loyaltycustdata_total_reward_point;
		$loyaltycustdata_last_update_on=$loyalty_array[$x]->loyaltycustdata_last_update_on;
		$loyaltycustdata_redeemed=$loyalty_array[$x]->loyaltycustdata_redeemed;
		$loyaltycustdata_phone_no=$loyalty_array[$x]->loyaltycustdata_phone_no;
		$loyaltycustdata_address=$loyalty_array[$x]->loyaltycustdata_address;
		$loyaltycustdata_vehicle_no=$loyalty_array[$x]->loyaltycustdata_vehicle_no;
		$loyaltydata_transaction_id=$loyalty_array[$x]->loyaltydata_transaction_id;
		$loyaltydata_purchase_value=$loyalty_array[$x]->loyaltydata_purchase_value;
		$loyaltydata_trans_type=$loyalty_array[$x]->loyaltydata_trans_type;
		$loyaltydata_rds_code=$loyalty_array[$x]->loyaltydata_rds_code;
		
		$loyaltycustdata_rewarded_points=$loyaltydata_purchase_value/100;
		if($loyaltydata_trans_type=='FMCG')      $loyaltydata_scheme_value=($loyaltydata_purchase_value)*(0.00);
		if($loyaltydata_trans_type=='HSD')       $loyaltydata_scheme_value=($loyaltydata_purchase_value)*(0.0030);
		if($loyaltydata_trans_type=='MS')        $loyaltydata_scheme_value=($loyaltydata_purchase_value)*(0.0035);
		if($loyaltydata_trans_type=='LUBE')      $loyaltydata_scheme_value=($loyaltydata_purchase_value)*(0.00);   

		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($loyalty_latt>0 && $loyalty_longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$loyalty_latt."',longi='".$loyalty_longi."' WHERE emp_code='".$loyalty_user_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		//For checking that trans id exist or not
		$sqlchkloyaltylocation="SELECT * FROM location WHERE trans_id='".$loyalty_trans_id."'";
		$reschkloyaltylocation = mysqli_query($link,$sqlchkloyaltylocation) or die(mysqli_error()." Error in check location: ".$sqlchkattlocation); 
		$rowchkloyaltylocation = mysqli_fetch_assoc($reschkloyaltylocation);
		$countchkloyaltylocation=mysqli_num_rows($reschkloyaltylocation);
		
		//For update the location table for existing trans id
		if($countchkloyaltylocation>0)
		{
			$sqlupdateloyaltylocation="UPDATE location SET emp_code='".$loyalty_user_code."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."'

									WHERE trans_id='".$loyalty_trans_id."'";
			$rsupdateloyaltylocation=mysqli_query($link,$sqlupdateloyaltylocation) or die(mysqli_error()." Error in update location: ".$sqlupdateloyaltylocation);
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
			$sqlinsertloyaltylocation="INSERT INTO location SET emp_code='".$loyalty_user_code."',
									trans_id='".$loyalty_trans_id."',
									latt='".$loyalty_latt."',
									longi='".$loyalty_longi."',
									date='".$loyalty_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the attendance table for new trans id
			$sqlinsertloyaltycard="INSERT INTO card_transaction SET transaction_id='".$loyaltydata_transaction_id."',
									loyalty_card_no='".$loyaltycustdata_card_no."',
									rds_code='".$loyaltydata_rds_code."',
									trans_type='".$loyaltydata_trans_type."',
									scheme_value='".$loyaltydata_scheme_value."',
								 	purchase_value='".$loyaltydata_purchase_value."'";
										
			$sqlchkloyaltycardholder="SELECT * FROM loyalty_card_holder_master WHERE loyalty_card_holder_code ='".$loyaltycustdata_card_holder_code."' 
									AND loyalty_card_no='".$loyaltycustdata_card_no."'";
			$reschkloyaltycardholder = mysqli_query($link,$sqlchkloyaltycardholder) or die(mysqli_error()." Error in check loyalty card holder: ".$sqlchkloyaltycardholder); 
			$rowchkloyaltycardholder = mysqli_fetch_assoc($reschkloyaltycardholder);
			$countchkloyaltycardholder=mysqli_num_rows($reschkloyaltycardholder);
			
			//For Insertion of new card holder
			$sqlinsertloyaltycardholder="INSERT INTO loyalty_card_holder_master SET loyalty_card_holder_code ='".$loyaltycustdata_card_holder_code."',
										 loyalty_card_holder_name='".$loyaltycustdata_card_holder_name."',
										  loyalty_card_no='".$loyaltycustdata_card_no."',
										  car_type='".$loyaltycustdata_card_type."',
										  total_purchase_value='".$loyaltycustdata_total_purchase_value."',
										  total_reward_point='".$loyaltycustdata_rewarded_points."',
										  last_update_on='".$loyaltycustdata_last_update_on."',
										  redeemed='".$loyaltycustdata_redeemed."',
										  address='".$loyaltycustdata_address."',
										  vehicle_no='".$loyaltycustdata_vehicle_no."',
										  download_time=CURRENT_TIMESTAMP(),
										  phone_no='".$loyaltycustdata_phone_no."'";	
								 
			if($countchkloyaltycardholder<1)
			{
				if(mysqli_query($link,$sqlinsertloyaltylocation) && mysqli_query($link,$sqlinsertloyaltycard) && mysqli_query($link,$sqlinsertloyaltycardholder))
				{
					$flag=5;
				}
			}
			else
			{
				if(mysqli_query($link,$sqlinsertloyaltylocation) && mysqli_query($link,$sqlinsertloyaltycard))
				{
					$sqlupdateloyaltycardholder="UPDATE loyalty_card_holder_master SET 
											  total_purchase_value=(total_purchase_value+$loyaltydata_purchase_value),
											  total_reward_point=(total_reward_point+$loyaltycustdata_rewarded_points),
											  download_time=CURRENT_TIMESTAMP() WHERE loyalty_card_no='".$loyaltycustdata_card_no."'";
					if(mysqli_query($link,$sqlupdateloyaltycardholder))
					{						  	
						$flag=5;
					}
				}
			}
			if($flag==5)
				{
					//$flag=5;
					
					// For Sending email to recipents for attendance
				 	//$sqlrdsname="SELECT rds_name FROM rds_master WHERE rds_code='".$loyaltydata_rds_code."'";
					$sqlrdsname="SELECT rds_name FROM rds_master WHERE emp_code='".$loyalty_user_code."'";
					$rsrdsname=mysqli_query($link,$sqlrdsname);
					$rowrdsname=mysqli_fetch_assoc($rsrdsname);
					$rds_name=$rowrdsname['rds_name'];
					
					$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$loyalty_user_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=$rowempname['emp_name'];
					
					$sqlcardholderdetails="SELECT loyalty_card_holder_name,total_reward_point FROM loyalty_card_holder_master 
											WHERE loyalty_card_no='".$loyaltycustdata_card_no."'";
					$rscardholderdetails=mysqli_query($link,$sqlcardholderdetails);
					$rowcardholderdetails=mysqli_fetch_assoc($rscardholderdetails);
					$loyalty_card_holder_name=$rowcardholderdetails['loyalty_card_holder_name'];
					$total_reward_point=$rowcardholderdetails['total_reward_point'];

					$address=getReverseGeo($latt,$longi);
					$loyaltyemailsubj="Transaction of - ".$emp_name." on ".date('d-m-Y',strtotime($loyalty_date))." @".date('H:i:s',strtotime($loyalty_date)).' hrs.';
					$loyaltyemailbody = "<html><head><title>Loyalty</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application for <b>LOYALTY POINTS</b>.<br /><br /><br />
										<b>Vertical: " .strtoupper($loyaltydata_trans_type). "</b><br /><br />
										<b>Outlet Name: " .$rds_name. "</b><br /><br />
										<b>Car Type: " .$loyaltycustdata_card_type. "</b><br /><br />
										<b>Vehicle No: " .$loyaltycustdata_vehicle_no. "</b><br /><br />
										<b>Card Holder Name: " .$loyaltycustdata_card_holder_name. "</b><br /><br />
										<b>Card Holder No.: " .$loyaltycustdata_card_no. "</b><br /><br />
										<b>Card Holder Phone No.: " .$loyaltycustdata_phone_no. "</b><br /><br />
										<b>Card Holder Address: " .$loyaltycustdata_address. "</b><br /><br />
										<b>Purchase Value: " .number_format($loyaltydata_purchase_value,2). "</b><br /><br />
										<b>Accumulated Point: " .number_format($total_reward_point,2). " (Including this transaction)</b><br /><br />
										</table><br /><br />Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail(LOYALTYEMAILRECIPENTS, $loyaltyemailsubj, $loyaltyemailbody, $headers,'-facedns@acedns.in'))
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
?>
