<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

//echo "RESPONSE OF $nick_name is --------------------------\n".$body;
//exit();
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><gift_delivery><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[GDE000220210512185658]]></trans_id><latt><![CDATA[37.421998333333335]]></latt><longi><![CDATA[-122.08400000000002]]></longi><date><![CDATA[2021-05-12 18:56:58]]></date></location><gift_delivery_details><delivery_id><![CDATA[GDE000220210512185658]]></delivery_id><delivery_date><![CDATA[2021-05-12]]></delivery_date><gift_id><![CDATA[3]]></gift_id><gift_name><![CDATA[Refrigerator]]></gift_name><customer_broad_option><![CDATA[customers]]></customer_broad_option><customer_code><![CDATA[C/0000002]]></customer_code><gift_delivery_option><![CDATA[owner]]></gift_delivery_option><owner_name><![CDATA[ABC test]]></owner_name><employee_name><![CDATA[]]></employee_name><employee_mobile><![CDATA[]]></employee_mobile><employee_relation_owner><![CDATA[]]></employee_relation_owner><address><![CDATA[Kolkata]]></address><route_code><![CDATA[RT/12]]></route_code><image_1><![CDATA[]]></image_1><image_2><![CDATA[]]></image_2></gift_delivery_details></gift_delivery></root>";*/

$gift_delivery_emp_code="*ROOT*GIFT_DELIVERY*LOCATION*EMP_CODE";
$gift_delivery_trans_id = "*ROOT*GIFT_DELIVERY*LOCATION*TRANS_ID";
$gift_delivery_latt = "*ROOT*GIFT_DELIVERY*LOCATION*LATT";
$gift_delivery_longi = "*ROOT*GIFT_DELIVERY*LOCATION*LONGI";
$gift_delivery_trans_date="*ROOT*GIFT_DELIVERY*LOCATION*DATE";
$delivery_id = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*DELIVERY_ID";
$delivery_date = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*DELIVERY_DATE";
$gift_id = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*GIFT_ID";
$gift_name = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*GIFT_NAME";
$customer_broad_option = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*CUSTOMER_BROAD_OPTION";
$customer_code = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*CUSTOMER_CODE";
$gift_delivery_option = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*GIFT_DELIVERY_OPTION";
$owner_name = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*OWNER_NAME";
$employee_name = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*EMPLOYEE_NAME";
$employee_mobile = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*EMPLOYEE_MOBILE";
$employee_relation_owner = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*EMPLOYEE_RELATION_OWNER";
$address = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*ADDRESS";
$route_code = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*ROUTE_CODE";
$image_1 = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*IMAGE_1";
$image_2 = "*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS*IMAGE_2";

$gift_delivery_array=array();
$gift_delivery_details_array=array();

$counter = 0;
$countergift=0;

class xml_gift{
	var $gift_delivery_emp_code,$gift_delivery_trans_id,$gift_delivery_latt,$gift_delivery_longi,$gift_delivery_trans_date;	
}
class xml_gift_delivery{
	var $delivery_id,$delivery_date,$gift_id,$gift_name,$customer_broad_option,$customer_code,$gift_delivery_option,$owner_name,$employee_name,$employee_mobile,$employee_relation_owner,$address,$route_code,$image_1,$image_2;
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
    global $current_tag,$counter,$countergift,$gift_delivery_array,$gift_delivery_details_array,
$gift_delivery_emp_code,$gift_delivery_trans_id,$gift_delivery_latt,$gift_delivery_longi,$gift_delivery_trans_date,$delivery_id,$delivery_date,$gift_id,$gift_name,$customer_broad_option,$customer_code,$gift_delivery_option,$owner_name,$employee_name,$employee_mobile,$employee_relation_owner,$address,$route_code,$image_1,$image_2;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,19)=='*ROOT*GIFT_DELIVERY')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $gift_delivery_emp_code:
				$gift_delivery_array[$counter] = new xml_gift();
				$gift_delivery_array[$counter]->gift_delivery_emp_code = $data;
				break;
			case $gift_delivery_trans_id:
				$gift_delivery_array[$counter]->gift_delivery_trans_id = $data;
				break;
			case $gift_delivery_latt:
				$gift_delivery_array[$counter]->gift_delivery_latt = $data;
				break;
			case $gift_delivery_longi:
				$gift_delivery_array[$counter]->gift_delivery_longi = $data;
				break;
			case $gift_delivery_trans_date:
				$gift_delivery_array[$counter]->gift_delivery_trans_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,41)=='*ROOT*GIFT_DELIVERY*GIFT_DELIVERY_DETAILS')
		{
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $delivery_id:
					$gift_delivery_details_array[$countergift] = new xml_gift_delivery();
					$gift_delivery_details_array[$countergift]->delivery_id = $data;
					break;
				case $delivery_date:
					$gift_delivery_details_array[$countergift]->delivery_date = $data;
					break;
				case $gift_id:
					$gift_delivery_details_array[$countergift]->gift_id = $data;
					break;
				case $gift_name:
					$gift_delivery_details_array[$countergift]->gift_name = $data;
					break;
				case $customer_broad_option:
					$gift_delivery_details_array[$countergift]->customer_broad_option = $data;
					break;
				case $customer_code:
					 $gift_delivery_details_array[$countergift]->customer_code = $data;
					 break;	
				case $gift_delivery_option:
					 $gift_delivery_details_array[$countergift]->gift_delivery_option = $data;
					 break;
				case $owner_name:
					 $gift_delivery_details_array[$countergift]->owner_name = $data;
					 break; 
				case $employee_name:
					$gift_delivery_details_array[$countergift]->employee_name = $data;
					break;	 	  
				case $employee_mobile:
					$gift_delivery_details_array[$countergift]->employee_mobile = $data;
					break;
				case $employee_relation_owner:
					$gift_delivery_details_array[$countergift]->employee_relation_owner = $data;
					break;
				case $address:
					$gift_delivery_details_array[$countergift]->address = $data;
					break;
				case $route_code:
					$gift_delivery_details_array[$countergift]->route_code = $data;
					break;
				case $image_1:
					$gift_delivery_details_array[$countergift]->image_1 = $data;
					break;
				case $image_2:
					$gift_delivery_details_array[$countergift]->image_2 = $data;
					$countergift++;
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
//print_r($audit_array);
//print_r($stock_audit_array);
mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* -----------------------------------------------START QUERY FOR GIFT--------------------------------------------------------------------------*/
$gift_delivery_trans_id_exists=array();
if(count($gift_delivery_array)>0)
{
	for($x=0;$x<count($gift_delivery_array);$x++){

		$gift_delivery_emp_code=$gift_delivery_array[$x]->gift_delivery_emp_code;
		$gift_delivery_trans_id=$gift_delivery_array[$x]->gift_delivery_trans_id;
		$gift_delivery_latt=$gift_delivery_array[$x]->gift_delivery_latt;
		$gift_delivery_longi=$gift_delivery_array[$x]->gift_delivery_longi;
		$gift_delivery_trans_date=$gift_delivery_array[$x]->gift_delivery_trans_date;
		
		//For checking that trans id exist or not for mt
		$sqlchkgiftlocation="SELECT trans_id FROM location WHERE trans_id='".$gift_delivery_trans_id."'";
		$reschkgiftlocation = mysqli_query($link,$sqlchkgiftlocation) or die(mysqli_error()." Error in check gift location: ".$sqlchkgiftlocation); 
		$countchkgiftlocation=mysqli_num_rows($reschkgiftlocation);
		
		//For update the location table for existing trans id for mt
		if($countchkgiftlocation>0)
		{
			//$audit_trans_id_chk=substr($audit_trans_id,1,19);
			if(!in_array($gift_delivery_trans_id,$gift_delivery_trans_id_exists))
			{
				array_push($gift_delivery_trans_id_exists,$gift_delivery_trans_id);
			}
			$sqlupdategiftlocation="UPDATE location SET emp_code='".$gift_delivery_emp_code."',
									latt='".$gift_delivery_latt."',
									longi='".$gift_delivery_longi."'
									WHERE trans_id='".$gift_delivery_trans_id."'";
			$rsupdategiftlocation=mysqli_query($link,$sqlupdategiftlocation) or die(mysqli_error()." Error in update gift location: ".$sqlupdategiftlocation);
			if($rsupdategiftlocation)
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of transaction
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//For Insert into the location table for new trans id regarding mt
			$sqlinsertgiftlocation="INSERT INTO location SET emp_code='".$gift_delivery_emp_code."',
								  trans_id='".$gift_delivery_trans_id."',
								  latt='".$gift_delivery_latt."',
								  longi='".$gift_delivery_longi."',
								  date='".$gift_delivery_trans_date."',
								  updatetime='".$location_date."'";
			if(mysqli_query($link,$sqlinsertgiftlocation))
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
	}// End for loop
		if(count($gift_delivery_details_array)>0 && !in_array($gift_delivery_trans_id,$gift_delivery_trans_id_exists))
		{
			for($i=0;$i<count($gift_delivery_details_array);$i++){
				$delivery_id=$gift_delivery_details_array[$i]->delivery_id;
				$delivery_date=$gift_delivery_details_array[$i]->delivery_date;
				$gift_id=$gift_delivery_details_array[$i]->gift_id;
				$gift_name=$gift_delivery_details_array[$i]->gift_name;
				$customer_broad_option=$gift_delivery_details_array[$i]->customer_broad_option;
				$customer_code=$gift_delivery_details_array[$i]->customer_code;
				$gift_delivery_option=$gift_delivery_details_array[$i]->gift_delivery_option;
				$owner_name=$gift_delivery_details_array[$i]->owner_name;
				$employee_name=$gift_delivery_details_array[$i]->employee_name;
				$employee_mobile=$gift_delivery_details_array[$i]->employee_mobile;
				$employee_relation_owner=$gift_delivery_details_array[$i]->employee_relation_owner;
				$address=$gift_delivery_details_array[$i]->address;
				$route_code=$gift_delivery_details_array[$i]->route_code;
				$image_1=$gift_delivery_details_array[$i]->image_1;
				$image_2=$gift_delivery_details_array[$i]->image_2;
				

				$sqlinsertgift="INSERT INTO gift_delivery_details SET delivery_id ='".$delivery_id."',
									  delivery_date 				='".$delivery_date."',
									  gift_id 						='".$gift_id."',
									  gift_name 					='".$gift_name."',
									  customer_broad_option 		='".$customer_broad_option."',
									  customer_code					='".$customer_code."',
									  gift_delivery_option			='".$gift_delivery_option."',
									  owner_name					='".$owner_name."',
									  employee_name					='".$employee_name."',
									  employee_mobile				='".$employee_mobile."',
									  employee_relation_owner		='".$employee_relation_owner."',
									  location						='".addslashes($address)."',
									 route_code						='".$route_code."',
									  image_1 						='".addslashes($image_1)."',
									  image_2 						='".addslashes($image_2)."'
									  ";											  
				if(mysqli_query($link,$sqlinsertgift))
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
	if($flag==5)
	{	
		$sqlcustomertype="SELECT phone_no,customer_name,owner_name FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomertype=mysqli_query($link,$sqlcustomertype);
		$rowcustomertype=mysqli_fetch_assoc($rscustomertype);
		$phone_no=$rowcustomertype['phone_no'];
		$customer_name=$rowcustomertype['customer_name'];
		$owner_name_db=$rowcustomertype['owner_name'];
		
		if($owner_name!='')  $gift_received_by=$owner_name;
		else  				 $gift_received_by=$employee_name;
		 
		$smsstringval=$gift_name.' delivered to the '.$customer_name. ' On '.date('d/m/Y',strtotime($delivery_date)).'.Gift received by '.$gift_received_by;
		$url="http://smslive.in/push/default.aspx?user=ACRAFT&pws=acraft123&Receipent=".$phone_no."&sms=".urlencode($smsstringval)."";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_exec($ch);
	}
}//End of stock audit if

 /* --------------------END QUERY FOR STOCK AUDIT------------------------------------------------------------------------------------------------------------*/
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
mysqli_close($link);
?>
