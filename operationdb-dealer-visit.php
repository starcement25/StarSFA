<?php
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
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><business_prospect><location><emp_code><![CDATA[E0002]]></emp_code><trans_id><![CDATA[BE000220210622185658]]></trans_id><latt><![CDATA[37.421998333333335]]></latt><longi><![CDATA[-122.08400000000002]]></longi><date><![CDATA[2021-06-22 18:56:58]]></date></location><business_prospect_details><prospect_id><![CDATA[BE000220210622185658]]></prospect_id><emp_code><![CDATA[E0002]]></emp_code><create_date><![CDATA[2021-06-12]]></create_date><referring_cust_area><![CDATA[behala]]></referring_cust_area><referring_cust_phone><![CDATA[88888888888888]]></referring_cust_phone><referring_cust_tagged_dealer><![CDATA[C/000001]]></referring_cust_tagged_dealer><referred_person_name><![CDATA[AMI]]></referred_person_name><referred_person_profession><![CDATA[owner]]></referred_person_profession><referred_person_phone><![CDATA[0099999999]]></referred_person_phone><referred_person_email><![CDATA[omi@gmail.com]]></referred_person_email><referred_person_firm><![CDATA[BC]]></referred_person_firm><referred_person_district><![CDATA[Kolkata]]></referred_person_district><referred_person_zone><![CDATA[Kolkata]]></referred_person_zone><referred_person_route><![CDATA[RT/12]]></referred_person_route></business_prospect_details></business_prospect></root>";*/

$business_prospect_emp_code="*ROOT*BUSINESS_PROSPECT*LOCATION*EMP_CODE";
$business_prospect_trans_id = "*ROOT*BUSINESS_PROSPECT*LOCATION*TRANS_ID";
$business_prospect_latt = "*ROOT*BUSINESS_PROSPECT*LOCATION*LATT";
$business_prospect_longi = "*ROOT*BUSINESS_PROSPECT*LOCATION*LONGI";
$business_prospect_trans_date="*ROOT*BUSINESS_PROSPECT*LOCATION*DATE";
$prospect_id = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*PROSPECT_ID";
$emp_code = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*EMP_CODE";
$create_date = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*CREATE_DATE";
$referring_cust_area = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRING_CUST_AREA";
$referring_cust_phone = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRING_CUST_PHONE";
$referring_cust_tagged_dealer = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRING_CUST_TAGGED_DEALER";
$referred_person_name = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_NAME";
$referred_person_profession = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_PROFESSION";
$referred_person_phone = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_PHONE";
$referred_person_email = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_EMAIL";
$referred_person_firm = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_FIRM";
$referred_person_district = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_DISTRICT";
$referred_person_zone = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_ZONE";
$referred_person_route = "*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS*REFERRED_PERSON_ROUTE";

$business_prospect_array=array();
$business_prospect_details_array=array();

$counter = 0;
$counterprospect=0;

class xml_business_prospect{
	var $business_prospect_emp_code,$business_prospect_trans_id,$business_prospect_latt,$business_prospect_longi,$business_prospect_trans_date;	
}
class xml_business_prospect_details{
	var $prospect_id,$emp_code,$create_date,$referring_cust_area,$referring_cust_phone,$referring_cust_tagged_dealer,$referred_person_name,$referred_person_profession,$referred_person_phone,$referred_person_email,$referred_person_firm,$referred_person_district,$referred_person_zone,$referred_person_route;
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
    global $current_tag,$counter,$counterprospect,$business_prospect_array,$business_prospect_details_array,
$business_prospect_emp_code,$business_prospect_trans_id,$business_prospect_latt,$business_prospect_longi,$business_prospect_trans_date,$prospect_id,$emp_code,$create_date,$referring_cust_area,$referring_cust_phone,$referring_cust_tagged_dealer,$referred_person_name,$referred_person_profession,$referred_person_phone,$referred_person_email,$referred_person_firm,$referred_person_district,$referred_person_zone,$referred_person_route;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,23)=='*ROOT*BUSINESS_PROSPECT')
	{
		echo $current_tag.'<br />';
		echo $data.'<br />';
		switch($current_tag){
			case $business_prospect_emp_code:
				$business_prospect_array[$counter] = new xml_business_prospect();
				$business_prospect_array[$counter]->business_prospect_emp_code = $data;
				break;
			case $business_prospect_trans_id:
				$business_prospect_array[$counter]->business_prospect_trans_id = $data;
				break;
			case $business_prospect_latt:
				$business_prospect_array[$counter]->business_prospect_latt = $data;
				break;
			case $business_prospect_longi:
				$business_prospect_array[$counter]->business_prospect_longi = $data;
				break;
			case $business_prospect_trans_date:
				$business_prospect_array[$counter]->business_prospect_trans_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,49)=='*ROOT*BUSINESS_PROSPECT*BUSINESS_PROSPECT_DETAILS')
		{
			//echo $current_tag.'<br />';
			//echo $data.'<br />';

			switch($current_tag){
				case $prospect_id:
					$business_prospect_details_array[$counterprospect] = new xml_business_prospect_details();
					$business_prospect_details_array[$counterprospect]->prospect_id = $data;
					break;
				case $emp_code:
					$business_prospect_details_array[$counterprospect]->emp_code = $data;
					break;
				case $create_date:
					$business_prospect_details_array[$counterprospect]->create_date = $data;
					break;
				case $referring_cust_area:
					$business_prospect_details_array[$counterprospect]->referring_cust_area = $data;
					break;
				case $referring_cust_phone:
					$business_prospect_details_array[$counterprospect]->referring_cust_phone = $data;
					break;
				case $referring_cust_tagged_dealer:
					 $business_prospect_details_array[$counterprospect]->referring_cust_tagged_dealer = $data;
					 break;	
				case $referred_person_name:
					 $business_prospect_details_array[$counterprospect]->referred_person_name = $data;
					 break;
				case $referred_person_profession:
					 $business_prospect_details_array[$counterprospect]->referred_person_profession = $data;
					 break; 
				case $referred_person_phone:
					$business_prospect_details_array[$counterprospect]->referred_person_phone = $data;
					break;	 	  
				case $referred_person_email:
					$business_prospect_details_array[$counterprospect]->referred_person_email = $data;
					break;
				case $referred_person_firm:
					$business_prospect_details_array[$counterprospect]->referred_person_firm = $data;
					break;
				case $referred_person_district:
					$business_prospect_details_array[$counterprospect]->referred_person_district = $data;
					break;
				case $referred_person_zone:
					$business_prospect_details_array[$counterprospect]->referred_person_zone = $data;
					break;
				case $referred_person_route:
					$business_prospect_details_array[$counterprospect]->referred_person_route = $data;
					$counterprospect++;
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
/* -----------------------------------------------START QUERY FOR business prospect--------------------------------------------------------------------------*/
$business_prospect_trans_id_exists=array();
if(count($business_prospect_array)>0)
{
	for($x=0;$x<count($business_prospect_array);$x++){

		$business_prospect_emp_code=$business_prospect_array[$x]->business_prospect_emp_code;
		$business_prospect_trans_id=$business_prospect_array[$x]->business_prospect_trans_id;
		$business_prospect_latt=$business_prospect_array[$x]->business_prospect_latt;
		$business_prospect_longi=$business_prospect_array[$x]->business_prospect_longi;
		$business_prospect_trans_date=$business_prospect_array[$x]->business_prospect_trans_date;
		
		//For checking that trans id exist or not for mt
		$sqlchkgiftlocation="SELECT trans_id FROM location WHERE trans_id='".$business_prospect_trans_id."'";
		$reschkgiftlocation = mysqli_query($link,$sqlchkgiftlocation) or die(mysqli_error()." Error in check gift location: ".$sqlchkgiftlocation); 
		$countchkgiftlocation=mysqli_num_rows($reschkgiftlocation);
		
		//For update the location table for existing trans id for bp
		if($countchkgiftlocation>0)
		{
			//$audit_trans_id_chk=substr($audit_trans_id,1,19);
			if(!in_array($business_prospect_trans_id,$business_prospect_trans_id_exists))
			{
				array_push($business_prospect_trans_id_exists,$business_prospect_trans_id);
			}
			$sqlupdategiftlocation="UPDATE location SET emp_code='".$business_prospect_emp_code."',
									latt='".$business_prospect_latt."',
									longi='".$business_prospect_longi."'
									WHERE trans_id='".$business_prospect_trans_id."'";
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
			$sqlinsertgiftlocation="INSERT INTO location SET emp_code='".$business_prospect_emp_code."',
								  trans_id='".$business_prospect_trans_id."',
								  latt='".$business_prospect_latt."',
								  longi='".$business_prospect_longi."',
								  date='".$business_prospect_trans_date."',
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
		if(count($business_prospect_details_array)>0 && !in_array($business_prospect_trans_id,$business_prospect_trans_id_exists))
		{
			for($i=0;$i<count($business_prospect_details_array);$i++){
				
				$prospect_id=$business_prospect_details_array[$i]->prospect_id;
				$emp_code=$business_prospect_details_array[$i]->emp_code;
				$create_date=$business_prospect_details_array[$i]->create_date;
				$referring_cust_area=$business_prospect_details_array[$i]->referring_cust_area;
				$referring_cust_phone=$business_prospect_details_array[$i]->referring_cust_phone;
				$referring_cust_tagged_dealer=$business_prospect_details_array[$i]->referring_cust_tagged_dealer;
				$referred_person_name=$business_prospect_details_array[$i]->referred_person_name;
				$referred_person_profession=$business_prospect_details_array[$i]->referred_person_profession;
				$referred_person_phone=$business_prospect_details_array[$i]->referred_person_phone;
				$referred_person_email=$business_prospect_details_array[$i]->referred_person_email;
				$referred_person_firm=$business_prospect_details_array[$i]->referred_person_firm;
				$referred_person_district=$business_prospect_details_array[$i]->referred_person_district;
				$referred_person_zone=$business_prospect_details_array[$i]->referred_person_zone;
				$referred_person_route=$business_prospect_details_array[$i]->referred_person_route;

				$sqlinsertprospect="INSERT INTO business_prospect_details SET prospect_id ='".$prospect_id."',
									  emp_code 							='".$emp_code."',
									  create_date 						='".$create_date."',
									  referring_cust_area 				='".addslashes($referring_cust_area)."',
									  referring_cust_phone 				='".addslashes($referring_cust_phone)."',
									  referring_cust_tagged_dealer		='".addslashes($referring_cust_tagged_dealer)."',
									  referred_person_name				='".addslashes($referred_person_name)."',
									  referred_person_profession		='".addslashes($referred_person_profession)."',
									  referred_person_phone				='".addslashes($referred_person_phone)."',
									  referred_person_email				='".addslashes($referred_person_email)."',
									  referred_person_firm				='".addslashes($referred_person_firm)."',
									  referred_person_district			='".addslashes($referred_person_district)."',
									  referred_person_zone				='".addslashes($referred_person_zone)."',
									  referred_person_route 			='".addslashes($referred_person_route)."'";											  
				if(mysqli_query($link,$sqlinsertprospect))
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
