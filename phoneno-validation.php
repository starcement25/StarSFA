<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$phone_no=$_REQUEST['phone_no'];

$sqlquery="SELECT prospect_name,pin,street_name,street_no,building_no,apartment_no,phone_no,email,oil_used FROM 
			product_promotion WHERE phone_no='".$phone_no."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
	$rowresult=mysqli_fetch_assoc($result);	
	$prospect_name=$rowresult['prospect_name'];
	$pin=$rowresult['pin'];
	$street_name=$rowresult['street_name'];
	$street_no=$rowresult['street_no'];
	$building_no=$rowresult['building_no'];
	$apartment_no=$rowresult['apartment_no'];
	$email=$rowresult['email'];
	$oil_used=$rowresult['oil_used'];
	
	$contents  = 'Name: '.(($prospect_name!='')?$prospect_name: ' ')."#";
	$contents  .= 'PIN Code: '.(($pin!='')?$pin: ' ')."#";
	$contents  .= 'Street Name: '.(($street_name!='')?$street_name: ' ')."#";
	$contents  .= 'Street No: '.(($street_no!='')?$street_no: ' ')."#";
	$contents  .= 'Building No: '.(($building_no!='')?$building_no: ' ')."#";
	$contents  .= 'Apartment No: '.(($apartment_no!='')?$apartment_no: ' ')."#";
	$contents  .= 'Email Address: '.(($email!='')?$email: ' ')."#";
	$contents  .= 'OIL used: '.(($oil_used!='')?$oil_used: ' ');
	  echo '1#'.$contents;
	}
	else
	{
		echo '0';
	}
	mysqli_close($link);
?>
