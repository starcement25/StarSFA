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
function fetch_recursive_new_customer($new_customer_name,$state,$route_code)
{
	$new_customer_name_numeric=substr($new_customer_name,-1);
	$new_customer_name_numeric=$new_customer_name_numeric+1;
	$new_customer_name_parts=substr($new_customer_name,0,-1);
	$new_customer_name_final=$new_customer_name_parts.$new_customer_name_numeric;
	
	$sqlcustomer="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($new_customer_name_final)."' AND route_code='".$route_code."' 
				AND state_code='".$state."'";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	$cntcustomer=mysqli_num_rows($rscustomer);
	if($cntcustomer >0)
	{
		fetch_recursive_new_customer($new_customer_name_final,$state,$route_code);
	}
	else
	{
		return $new_customer_name_final;
	}
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
  $spam_filter='-facedns@coral.in';
}


$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');

$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
//mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><new_customer><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[NE000120210609192234]]></trans_id><latt><![CDATA[22.5643238]]></latt><longi><![CDATA[88.356893]]></longi><date><![CDATA[2021-06-09 19:22:34]]></date></location><new_customer_details><customer_code><![CDATA[NE000120210609192234]]></customer_code><customer_name><![CDATA[TEST ABHI NEW]]></customer_name><Phone_no><![CDATA[9836114458]]></Phone_no><pin_code><![CDATA[700125]]></pin_code><area><![CDATA[RT/1]]></area><area_name><![CDATA[Akurdi]]></area_name><rds_tag><![CDATA[C/0000001]]></rds_tag><address><![CDATA[ADD]]></address><landline_no><![CDATA[]]></landline_no><owner_name><![CDATA[ABHI DEY]]></owner_name><owner_image><![CDATA[]]></owner_image><firm_name><![CDATA[DEY store]]></firm_name><firm_image><![CDATA[]]></firm_image><GST><![CDATA[]]></GST><GST_image><![CDATA[]]></GST_image><aadhar><![CDATA[123456789]]></aadhar><aadhar_image><![CDATA[]]></aadhar_image><cust_type><![CDATA[R]]></cust_type><weekly_closing_day><![CDATA[]]></weekly_closing_day><email><![CDATA[]]></email><acedns><![CDATA[Y]]></acedns><branch_code><![CDATA[B0003]]></branch_code><base_latt><![CDATA[22.5643238]]></base_latt><base_longi><![CDATA[88.356893]]></base_longi></new_customer_details></new_customer></root>";*/

$location_emp_code="*ROOT*NEW_CUSTOMER*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*NEW_CUSTOMER*LOCATION*TRANS_ID";
$location_latt = "*ROOT*NEW_CUSTOMER*LOCATION*LATT";
$location_longi = "*ROOT*NEW_CUSTOMER*LOCATION*LONGI";
$location_date="*ROOT*NEW_CUSTOMER*LOCATION*DATE";

$new_customer_code = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*CUSTOMER_CODE";
$new_customer_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*CUSTOMER_NAME";
$phone_no = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*PHONE_NO";
$pin_code = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*PIN_CODE";
$area = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AREA";
$area_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AREA_NAME";
$rds_tag = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*RDS_TAG";
$address = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*ADDRESS";	
$landline_no = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*LANDLINE_NO";
$owner_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*OWNER_NAME";
$owner_image = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*OWNER_IMAGE";
$firm_name = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*FIRM_NAME";
$firm_image = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*FIRM_IMAGE";
$GST = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*GST";
$GST_image = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*GST_IMAGE";
$aadhar = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AADHAR";
$aadhar_image = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*AADHAR_IMAGE";
$cust_type = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*CUST_TYPE";
$weekly_closing_day = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*WEEKLY_CLOSING_DAY";
$email = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*EMAIL";
$acedns = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*ACEDNS";
$branch_code = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*BRANCH_CODE";
$base_latt = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*BASE_LATT";
$base_longi = "*ROOT*NEW_CUSTOMER*NEW_CUSTOMER_DETAILS*BASE_LONGI";

$new_customer_array=array();
$counter = 0;

class xml_new_customer{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$new_customer_code,$new_customer_name,$phone_no,$pin_code,$area,$area_name,$rds_tag,$address,$landline_no,$owner_name,$owner_image,$firm_name,$firm_image,$GST,$GST_image,$aadhar,$aadhar_image,$cust_type,$weekly_closing_day,$email,$acedns,$branch_code,$base_latt,$base_longi;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$new_customer_code,$new_customer_name,$phone_no,$pin_code,$area,$area_name,$rds_tag,$address,$landline_no,$owner_name,$owner_image,$firm_name,$firm_image,$GST,$GST_image,$aadhar,$aadhar_image,$cust_type,$weekly_closing_day,$email,$acedns,$branch_code,$base_latt,$base_longi,$new_customer_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,18)=='*ROOT*NEW_CUSTOMER')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$new_customer_array[$counter] = new xml_new_customer();
				$new_customer_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$new_customer_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$new_customer_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$new_customer_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$new_customer_array[$counter]->location_date = $data;
				break;
			case $new_customer_code:
				$new_customer_array[$counter]->new_customer_code = $data;
				break;
			case $new_customer_name:
				$new_customer_array[$counter]->new_customer_name = $data;
				break;
			case $phone_no:
				$new_customer_array[$counter]->phone_no = $data;
				break;
			case $pin_code:
				$new_customer_array[$counter]->pin_code = $data;
				break;
			case $area:
				$new_customer_array[$counter]->area = $data;
				break;	
			case $area_name:
				$new_customer_array[$counter]->area_name = $data;
				break;
			case $rds_tag:
				$new_customer_array[$counter]->rds_tag = $data;
				break;	
			case $address:
				$new_customer_array[$counter]->address = $data;
				break;
			case $landline_no:
				$new_customer_array[$counter]->landline_no = $data;
				break;
			case $owner_name:
				$new_customer_array[$counter]->owner_name = $data;
				break;
			case $owner_image:
				$new_customer_array[$counter]->owner_image = $data;
				break;			
			case $firm_name:
				$new_customer_array[$counter]->firm_name = $data;
				break;
			case $firm_image:
				$new_customer_array[$counter]->firm_image = $data;
				break;
			case $GST:
				$new_customer_array[$counter]->GST = $data;
				break;
			case $GST_image:
				$new_customer_array[$counter]->GST_image = $data;
				break;
			case $aadhar:
				$new_customer_array[$counter]->aadhar = $data;
				break;
			case $aadhar_image:
				$new_customer_array[$counter]->aadhar_image = $data;
				break;
			case $cust_type:
				$new_customer_array[$counter]->cust_type = $data;
				break;
			case $weekly_closing_day:
				$new_customer_array[$counter]->weekly_closing_day = $data;
				break;
			case $email:
				$new_customer_array[$counter]->email = $data;
				break;
			case $acedns:
				$new_customer_array[$counter]->acedns = $data;
				break;
			case $branch_code:
				$new_customer_array[$counter]->branch_code = $data;
				break;
			case $base_latt:
				$new_customer_array[$counter]->base_latt = $data;
				break;
			case $base_longi:
				$new_customer_array[$counter]->base_longi = $data;
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
/* --------------------START QUERY FOR New customer------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$newcustomer_array_trans_id=array();
if(count($new_customer_array)>0)
{
	$refreshflag=0;
	for($x=0;$x<count($new_customer_array);$x++){
		$location_emp_code=$new_customer_array[$x]->location_emp_code;
		$location_trans_id=$new_customer_array[$x]->location_trans_id;
		$location_latt=$new_customer_array[$x]->location_latt;
		$location_longi=$new_customer_array[$x]->location_longi;
		$location_date=$new_customer_array[$x]->location_date;
		$new_customer_code=$new_customer_array[$x]->new_customer_code;
		$new_customer_name= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->new_customer_name);
		$phone_no=$new_customer_array[$x]->phone_no;
		$pin_code=$new_customer_array[$x]->pin_code;
		$area=$new_customer_array[$x]->area;
		$area_name= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->area_name);
		$rds_tag=$new_customer_array[$x]->rds_tag;
		$address= preg_replace('/[\r\n]+/', '',$new_customer_array[$x]->address);
		$landline_no=$new_customer_array[$x]->landline_no;
		$owner_name=$new_customer_array[$x]->owner_name;
		$owner_image=$new_customer_array[$x]->owner_image;
		$firm_name=$new_customer_array[$x]->firm_name;
		$firm_image=$new_customer_array[$x]->firm_image;
		$GST=$new_customer_array[$x]->GST;
		$GST_image=$new_customer_array[$x]->GST_image;
		$aadhar=$new_customer_array[$x]->aadhar;
		$aadhar_image=$new_customer_array[$x]->aadhar_image;
		$cust_type=$new_customer_array[$x]->cust_type;
		$weekly_closing_day=$new_customer_array[$x]->weekly_closing_day;
		$email=$new_customer_array[$x]->email;
		$acedns=$new_customer_array[$x]->acedns;
		$branch_code_xml=$new_customer_array[$x]->branch_code;
		$base_latt=$new_customer_array[$x]->base_latt;
		$base_longi=$new_customer_array[$x]->base_longi;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if(DCR_map=='no')
		{
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
		}

		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$newcustomer_array_trans_id))
			{
				array_push($newcustomer_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";

			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update new customer location: ".$sqlupdateorlocation);
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
		$sqlempname="SELECT emp_name,branch_code,vertical_value,state FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=title_case_emp($rowempname['emp_name']);
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];
		$state=$rowempname['state'];
		
		$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
		$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding order
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
		// Add new route to route master
		if(substr($area,0,1)=='N'){
			if(distributor_route_planning=='yes')
			{
				$sqlroute="select route_name from route_master WHERE route_name='".addslashes($area_name)."' AND emp_code='".$emp_code."'";
				$rsroute=mysqli_query($link,$sqlroute);
				$countroute=mysqli_num_rows($rsroute);
				if($countroute<1)
				{
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$area."'";
					$sqlroute .= " ,route_name='".addslashes($area_name)."'";
					$sqlroute .= " ,emp_code='".$emp_code."'";
					$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
					if(mysqli_query($link,$sqlroute))
					{
						$flag=5;
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
					$email_tag='New Route Name';
					$refreshflag=1;
				}
				else
				{
					$email_tag='Route Name';
				}
			}
			else
			{
			//$sqlroute="SELECT route_name FROM route_master WHERE route_code='".$area."'";
			$sqlroute="select route_name from route_master WHERE route_name='".addslashes($area_name)."'";
			$rsroute=mysqli_query($link,$sqlroute);
			$countroute=mysqli_num_rows($rsroute);
			if($countroute<1)
			{
				$sqlroute  = "insert into route_master ";
				$sqlroute .= " SET route_code='".$area."'";
				$sqlroute .= " ,route_name='".addslashes($area_name)."'";
				$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlroute))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				$email_tag='New Route Name';
				$refreshflag=1;
			}
			else
			{
				$email_tag='Route Name';
			}
		  }
		}
		else
		{
			$email_tag='Route Name';
		}
		$route_code=$area;
		$route_name=$area_name;

		if(modified_customer_emp_route=='yes')
		{
			if(providing_code=='no'){
				if(add_customer_activation=='yes')
				{
					/*$sqlcustomer="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND 
								route_code='".$route_code."' AND state_code='".$state."'";*/
					$sqlcustomer="";			
				}
				else
				{
				/*$sqlcustomer="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND 
						route_code='".$route_code."' and acedns='Y'";*/
				  $sqlcustomer="SELECT CRR.customer_code FROM customer_master CM,customer_route_emp_relation CRR WHERE CRR.customer_code=CM.customer_code
							AND CM.customer_name='".addslashes($new_customer_name)."' AND 
						CM.route_code='".$route_code."' AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";		
				}
			}
			else
			{
				$sqlcustomer="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($new_customer_coe)."' AND 
								route_code='".$route_code."' and acedns='Y'";
			}
			$rscustomer=mysqli_query($link,$sqlcustomer);
			$countcustomer=mysqli_num_rows($rscustomer);
		}
		else
		{
			if(providing_code=='no'){
			$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_name='".addslashes($new_customer_name)."' AND 
						emp_code='".$emp_code."' AND route_code='".$route_code."' and acedns='Y'";
			}
			else
			{
			$sqlcustomer="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($new_customer_code)."' AND 
						emp_code='".$emp_code."' AND route_code='".$route_code."' and acedns='Y'";
			}
			$rscustomer=mysqli_query($link,$sqlcustomer);
			$countcustomer=mysqli_num_rows($rscustomer);
		}
		if($rds_tag!='')
		{
			$sqldistrict="SELECT district FROM customer_master WHERE customer_code='".addslashes($rds_tag)."'";
			$rsdistrict=mysqli_query($link,$sqldistrict);
			$rowdistrict=mysqli_fetch_assoc($rsdistrict);
			$district=$rowdistrict['district'];
		}
		if(add_customer_activation=='yes')
		{
			$acedns='N';
		}
		else
		{
			$acedns='Y';
		}
		//echo geo_fencing_menu;
		if(strpos(geo_fencing_menu, 'add_customer')!==false){
			$sqlinsertparts=",base_latt='".$base_latt."',base_longi='".$base_longi."',image='".$image."'";
		}
		else
		{
			$sqlinsertparts="";
		}
		/*if($countcustomer>0 && add_customer_activation=='yes')
		{
			$modified_new_customer_name=fetch_recursive_new_customer($new_customer_name,$state,$route_code);
			//For addition of new customer
			if(modified_customer_emp_route=='yes')
			{
			   $sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
								   dns_customer_code ='".$new_customer_code."',
								   customer_name				='".addslashes($modified_new_customer_name)."',
								   route_code					='".$route_code."',
								   address						='".addslashes($address)."',
								   pin							='".$pin_code."',
								   phone_no						='".$phone_no."',
								   cust_type					='R',
								   branch_code					='".$branch_code."',
								   rds_tag						='".$rds_tag."',
								   download_time				=CURRENT_TIMESTAMP(),
								   download_time_credit_limit	=CURRENT_TIMESTAMP(),
								   acedns						='".$acedns."',
								   black_list					='N',
								   landline_no					='".$landline_no."',
								   owner_name					='".$owner_name."',
								   owner_phone					='".$owner_phone."',
								   cust_class					='".$cust_class."',
								   weekly_closing_day			='".$weekly_closing_day."',
								   coverage_type				='".$coverage_type."',
								   district						='".$district."',
								   TIN							='".$TIN."',
								   email						='".$email."',
								   state_code					='".$state."',
								   PAN							='".$PAN."'";
				   
				$sqlinsertcustomerrelation = "INSERT INTO customer_route_emp_relation 
							 SET customer_code='".$new_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."',
							 acedns='".$acedns."',
							 download_time	=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertcustomerrelation))
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
			$sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
							   dns_customer_code ='".$new_customer_code."',
							   customer_name				='".addslashes($modified_new_customer_name)."',
							   emp_code						='".$emp_code."',
							   route_code					='".$route_code."',
							   address						='".addslashes($address)."',
							   pin							='".$pin_code."',
							   phone_no						='".$phone_no."',
							   cust_type					='R',
							   branch_code					='".$branch_code."',
							   rds_tag						='".$rds_tag."',
							   download_time				=CURRENT_TIMESTAMP(),
							   download_time_credit_limit	=CURRENT_TIMESTAMP(),
							   acedns						='".$acedns."',
							   black_list					='N',
							   landline_no					='".$landline_no."',
							   owner_name					='".$owner_name."',
							   owner_phone					='".$owner_phone."',
							   cust_class					='".$cust_class."',
							   weekly_closing_day			='".$weekly_closing_day."',
							   coverage_type				='".$coverage_type."',
							   district						='".$district."',
							   TIN							='".$TIN."',
							   email						='".$email."',
							   state_code					='".$state."',
							   PAN							='".$PAN."'";
			}*/
	   //For addition of new customer route employee relation					   
	   /*$sqlinsertcustomerrelation = "INSERT INTO customer_route_emp_relation 
							 SET customer_code='".$max_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."'";
		if(mysqli_query($link,$sqlinsertcustomer) && mysqli_query($link,$sqlinsertcustomerrelation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}*/
		/*if(mysqli_query($link,$sqlinsertcustomer))
		{
			$flag=5;

		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}
	 }*/
	 
		if($countcustomer<1)
		{
		//For addition of new customer
			if(modified_customer_emp_route=='yes')
			{
			   if(strtoupper($nick_name)=='PALSONS')
			   {
				    $sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
								   dns_customer_code ='".$new_customer_code."',
								   customer_name				='".addslashes($new_customer_name)."',
								   route_code					='".$route_code."',
								   address						='".addslashes($address)."',
								   pin							='".$pin_code."',
								   phone_no						='".$phone_no."',
								   cust_type					='R',
								   branch_code					='".$branch_code_xml."',
								   rds_tag						='".$rds_tag."',
								   download_time				=CURRENT_TIMESTAMP(),
								   download_time_credit_limit	=CURRENT_TIMESTAMP(),
								   acedns						='".$acedns."',
								   black_list					='N',
								   landline_no					='".$landline_no."',
								   owner_name					='".$owner_name."',
								   owner_phone					='".$owner_phone."',
								   cust_class					='".$cust_class."',
								   weekly_closing_day			='".$weekly_closing_day."',
								   coverage_type				='".$coverage_type."',
								   district						='".$district."',
								   TIN							='".$TIN."',
								   email						='".$email."',
								    image						='".$image."',
								   state_code					='".$state."',
								   category_of_store			='".$category_of_store."',
								   instore_activity			='".$instore_activity."',
								   PAN							='".$PAN."'".$sqlinsertparts;
			   }
			   else if(strtoupper($nick_name)=='MAGIK')
			   {
					
					$sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
								   dns_customer_code ='".$new_customer_code."',
								   customer_name				='".addslashes($new_customer_name)."',
								   route_code					='".$route_code."',
								   address						='".addslashes($address)."',
								   pin							='".$pin_code."',
								   phone_no						='".$phone_no."',
								   cust_type					='R',
								   branch_code					='".$branch_code_xml."',
								   rds_tag						='".$rds_tag."',
								   download_time				=CURRENT_TIMESTAMP(),
								   download_time_credit_limit	=CURRENT_TIMESTAMP(),
								   acedns						='".$acedns."',
								   black_list					='N',
								   landline_no					='".$landline_no."',
								   owner_name					='".$owner_name."',
								   owner_phone					='',
								   cust_class					='',
								   weekly_closing_day			='".$weekly_closing_day."',
								   coverage_type				='',
								   district						='',
								   TIN							='".$GST."',
								   owner_image					='".$owner_image."',
								   firm_name					='".$firm_name."',
								   firm_image					='".$firm_image."',
								   GST_image					='".$GST_image."',
								   aadhar						='".$aadhar."',
								   aadhar_image					='".$aadhar_image."',
								   is_new_customer				='yes',
								   email						='".$email."',
								   PAN							=''".$sqlinsertparts;
			   }
			   else
			   {
			   $sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
								   dns_customer_code ='".$new_customer_code."',
								   customer_name				='".addslashes($new_customer_name)."',
								   route_code					='".$route_code."',
								   address						='".addslashes($address)."',
								   pin							='".$pin_code."',
								   phone_no						='".$phone_no."',
								   cust_type					='R',
								   branch_code					='".$branch_code_xml."',
								   rds_tag						='".$rds_tag."',
								   download_time				=CURRENT_TIMESTAMP(),
								   download_time_credit_limit	=CURRENT_TIMESTAMP(),
								   acedns						='".$acedns."',
								   black_list					='N',
								   landline_no					='".$landline_no."',
								   owner_name					='".$owner_name."',
								   owner_phone					='".$owner_phone."',
								   cust_class					='".$cust_class."',
								   weekly_closing_day			='".$weekly_closing_day."',
								   coverage_type				='".$coverage_type."',
								   district						='".$district."',
								   TIN							='".$TIN."',
								   email						='".$email."',
								   state_code					='".$state."',
								    category_of_store			='".$category_of_store."',
								   instore_activity			='".$instore_activity."',
								   PAN							='".$PAN."'".$sqlinsertparts;
			   }
				   
				$sqlinsertcustomerrelation = "INSERT INTO customer_route_emp_relation 
							 SET customer_code='".$new_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."',
							 acedns='".$acedns."',
							 download_time	=CURRENT_TIMESTAMP()";
				if(mysqli_query($link,$sqlinsertcustomerrelation))
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
	
		$sqlinsertcustomer="INSERT INTO customer_master SET customer_code ='".$new_customer_code."',
						   dns_customer_code ='".$new_customer_code."',
						   customer_name				='".addslashes($new_customer_name)."',
						   emp_code						='".$emp_code."',
						   route_code					='".$route_code."',
						   address						='".addslashes($address)."',
						   pin							='".$pin_code."',
						   phone_no						='".$phone_no."',
						   cust_type					='R',
						   branch_code					='".$branch_code_xml."',
						   rds_tag						='".$rds_tag."',
						   download_time				=CURRENT_TIMESTAMP(),
						   download_time_credit_limit	=CURRENT_TIMESTAMP(),
						   acedns						='".$acedns."',
						   black_list					='N',
						   landline_no					='".$landline_no."',
						   owner_name					='".$owner_name."',
						   owner_phone					='".$owner_phone."',
						   cust_class					='".$cust_class."',
						   weekly_closing_day			='".$weekly_closing_day."',
						   coverage_type				='".$coverage_type."',
						   district						='".$district."',
						   TIN							='".$TIN."',
						   email						='".$email."',
						   state_code					='".$state."',
						   PAN							='".$PAN."'".$sqlinsertparts;
			}
	   //For addition of new customer route employee relation					   
	   /*$sqlinsertcustomerrelation = "INSERT INTO customer_route_emp_relation 
							 SET customer_code='".$max_customer_code."',
							 route_code='".$route_code."',
							 emp_code='".$emp_code."'";
		if(mysqli_query($link,$sqlinsertcustomer) && mysqli_query($link,$sqlinsertcustomerrelation))
		{
			$flag=5;
		}
		else
		{
			mysqli_query($link,"ROLLBACK");
			echo $flag=0;
			return;
		}*/
		if(mysqli_query($link,$sqlinsertcustomer))
		{
			$flag=5;
			$refreshflag=1;
			if(strpos($phone_no, '#')!=false) {
				$valuearray=explode('#',$phone_no);
				$sql_insert_OTP="INSERT INTO OTP_details SET mobile_no='".$valuearray[0]."',
								   OTP 	='".$valuearray[1]."'";
			    mysqli_query($link,$sql_insert_OTP);				   
			}
			if($nick_name=='RUPA')
			{
				$sqlchkverticaldetails="SELECT emp_code FROM vertical_branch_employeewise_details WHERE emp_code='".$emp_code."' AND branch_code='".$branch_code."' 
							AND vertical_value='".$vertical_value."' AND operation_date=CURDATE()";
				$rschkverticaldetails=mysqli_query($link,$sqlchkverticaldetails);
				$countchkverticaldetails=mysqli_num_rows($rschkverticaldetails);
				if($countchkverticaldetails<1)
				{
					//For addition of new record in vertical_branch_employeewise_details
					$sqlinsertvertcaldetails="INSERT INTO vertical_branch_employeewise_details SET vertical_value ='".$vertical_value."',
												emp_code='".$emp_code."',
												branch_code	='".$branch_code."',
												operation_date=CURDATE(),
												calls_made=1,
												productive=0,
												`primary`=0,
												secondary=0";
					mysqli_query($link,$sqlinsertvertcaldetails);							
				}
				else
				{
					//For addition of new record in vertical_branch_employeewise_details
					$sqlupdatevertcaldetails="UPDATE vertical_branch_employeewise_details SET 
												calls_made=(calls_made+1) WHERE vertical_value ='".$vertical_value."' AND
												emp_code='".$emp_code."' AND branch_code	='".$branch_code."' AND  operation_date=CURDATE()";
					mysqli_query($link,$sqlupdatevertcaldetails);	
				}
			}
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
			$flag=5;
		}
		//Sending mail
		$operation_date=$date.'-'.$month.'-'.$year.' @ '.$hour.':'.$minute.':'.$second;

		$addcustomermailsubj="$nick_name - ​New Customer included by ".$emp_name." on ".date('d-m-Y',strtotime($location_date))." @".date('H:i:s',strtotime($location_date)).' hrs.';
		//$addressnewcustomer=getReverseGeo($location_latt,$location_longi);
		
		$addcustomer_TR="<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Pincode</span></strong></th>
								<th style='width:150px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>".$email_tag."</span></strong></th>
								<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Ph. No.</span></strong></th><th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial CE'>Address</span></strong></th>";
				
		$addcustomer_TD="<td style='width:200px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$new_customer_name."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$pin_code."</span>&nbsp;</td>
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								".$route_name."</span>&nbsp;</td>
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>
								+91-".$phone_no."</span>&nbsp;</td><td style='width:100px;text-align:left;min-height:21px;background-color:white'><span style='font-family:Arial CE;font-size:10pt'>".$address."</span>&nbsp;</td>";
		$addcustomermailbody = "<table><tr><td>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b><br><br>".$emp_name." visited ".$new_customer_name.".</td></tr></table>
								<br><br><table border=1 style=background-color:AliceBlue>
							     <tr>".$addcustomer_TR."</tr><tr>".$addcustomer_TD."</tr></table><br><br><br>Powered By aceDNS<br>";
        $headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\n";
		$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
					"Bcc: ".BCCEMAIL." \r\n" .
					'X-Mailer: PHP/' . phpversion();
		if($nick_name=='RUPA')
		{
			$addcustomer_email='';
		}
		else
		{
			$addcustomer_email=ORDEREMAILRECIPENTS;
		}			
		/*if(mail($addcustomer_email, $addcustomermailsubj, $addcustomermailbody, $headers,$spam_filter))
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
 /* --------------------END QUERY FOR New customer--------------------------------------------------------------------------------------------------------*/
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
	 
	 if($countdatarefresh >0 || $refreshflag==1)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-new-customer-customize-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";

insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");

	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-newcustomer-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
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
