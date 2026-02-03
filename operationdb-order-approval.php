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
$body=file_get_contents('php://input');


$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><ORDER_APPROVAL><location><emp_code><![CDATA[E0555]]></emp_code><trans_id><![CDATA[TAE055520200518193159]]></trans_id><latt><![CDATA[22.4777396]]></latt><longi><![CDATA[88.3432663]]></longi><date><![CDATA[2020-05-18 19:31:59]]></date></location><ORDER_APPROVAL_DETAILS><approval_id><![CDATA[TAE055520200518193159]]></approval_id><customer_code><![CDATA[C/0163316]]></customer_code><changed_sub_dealer_code><![CDATA[null]]></changed_sub_dealer_code><APPORDERNO><![CDATA[SS0344836]]></APPORDERNO><changed_dns_prod_code><![CDATA[null]]></changed_dns_prod_code><QTY_CHANGED><![CDATA[null]]></QTY_CHANGED><changed_dns_destination_code><![CDATA[null]]></changed_dns_destination_code><changed_dump_code><![CDATA[null]]></changed_dump_code><plant_name><![CDATA[null]]></plant_name><approval_status><![CDATA[AUTHORIZE]]></approval_status><approval_done_by><![CDATA[E0555]]></approval_done_by><remarks><![CDATA[null]]></remarks><changed_order_for><![CDATA[null]]></changed_order_for></ORDER_APPROVAL_DETAILS></ORDER_APPROVAL></root>";*/

$location_emp_code="*ROOT*ORDER_APPROVAL*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*ORDER_APPROVAL*LOCATION*TRANS_ID";
$location_latt = "*ROOT*ORDER_APPROVAL*LOCATION*LATT";
$location_longi = "*ROOT*ORDER_APPROVAL*LOCATION*LONGI";
$location_date="*ROOT*ORDER_APPROVAL*LOCATION*DATE";

$approval_id = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*APPROVAL_ID";
$customer_code = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CUSTOMER_CODE";
$changed_sub_dealer_code = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CHANGED_SUB_DEALER_CODE";
$APPORDERNO = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*APPORDERNO";
$changed_dns_prod_code = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CHANGED_DNS_PROD_CODE";
$QTY_CHANGED = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*QTY_CHANGED";
$changed_dns_destination_code = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CHANGED_DNS_DESTINATION_CODE";
$changed_dump_code = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CHANGED_DUMP_CODE";
$plant_name = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*PLANT_NAME";
$approval_status = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*APPROVAL_STATUS";
$approval_done_by = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*APPROVAL_DONE_BY";
$remarks = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*REMARKS";
$changed_order_for = "*ROOT*ORDER_APPROVAL*ORDER_APPROVAL_DETAILS*CHANGED_ORDER_FOR";


$order_approval_array=array();

$counter = 0;

class xml_order_approval{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$approval_id,$APPORDERNO,$customer_code,$changed_sub_dealer_code,$changed_dns_prod_code,$QTY_CHANGED,$changed_dns_destination_code,$changed_dump_code,$plant_name,$approval_status,$approval_done_by,$remarks,$changed_order_for;	
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
    global $current_tag,$counter,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$approval_id,$APPORDERNO,$customer_code,$changed_sub_dealer_code,$changed_dns_prod_code,$QTY_CHANGED,$changed_dns_destination_code,$changed_dump_code,$plant_name,$approval_status,$approval_done_by,
	$remarks,$changed_order_for,$order_approval_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,20)=='*ROOT*ORDER_APPROVAL')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$order_approval_array[$counter] = new xml_order_approval();
				$order_approval_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$order_approval_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$order_approval_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$order_approval_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$order_approval_array[$counter]->location_date = $data;
				break;
			case $approval_id:
				$order_approval_array[$counter]->approval_id = $data;
				break;
			case $APPORDERNO:
				$order_approval_array[$counter]->APPORDERNO = $data;
				break;
			case $customer_code:
				$order_approval_array[$counter]->customer_code = $data;
				break;
			case $changed_sub_dealer_code:
				$order_approval_array[$counter]->changed_sub_dealer_code = $data;
				break;	
			case $changed_dns_prod_code:
				$order_approval_array[$counter]->changed_dns_prod_code = $data;
				break;
			case $QTY_CHANGED:
				$order_approval_array[$counter]->QTY_CHANGED = $data;
				break;
			case $changed_dns_destination_code:
				$order_approval_array[$counter]->changed_dns_destination_code = $data;
				break;	
			case $changed_dump_code:
				$order_approval_array[$counter]->changed_dump_code = $data;
				break;	
			case $plant_name:
				$order_approval_array[$counter]->plant_name = $data;
				break;
			case $approval_status:
				$order_approval_array[$counter]->approval_status = $data;
				break;
			case $approval_done_by:
				$order_approval_array[$counter]->approval_done_by = $data;
				break;
			case $remarks:
				$order_approval_array[$counter]->remarks = $data;
				break;
			case $changed_order_for:
				$order_approval_array[$counter]->changed_order_for = $data;
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

/* --------------------START QUERY FOR ORDER APPROVAL------------------------------------------------------------------------------------------------*/
//print_r($new_customer_array);
$orderapproval_array_trans_id=array();
if(count($order_approval_array)>0)
{
	for($x=0;$x<count($order_approval_array);$x++){
		$location_emp_code=$order_approval_array[$x]->location_emp_code;
		$location_trans_id=$order_approval_array[$x]->location_trans_id;
		$location_latt=$order_approval_array[$x]->location_latt;
		$location_longi=$order_approval_array[$x]->location_longi;
		$location_date=$order_approval_array[$x]->location_date;
		$approval_id=$order_approval_array[$x]->approval_id;
		$APPORDERNO= $order_approval_array[$x]->APPORDERNO;
		$customer_code=$order_approval_array[$x]->customer_code;
		$changed_sub_dealer_code=$order_approval_array[$x]->changed_sub_dealer_code;
		$changed_dns_prod_code=$order_approval_array[$x]->changed_dns_prod_code;
		$QTY_CHANGED=$order_approval_array[$x]->QTY_CHANGED;
		$changed_dns_destination_code= $order_approval_array[$x]->changed_dns_destination_code;
		$changed_dump_code= $order_approval_array[$x]->changed_dump_code;
		$plant_name= $order_approval_array[$x]->plant_name;
		$approval_status= $order_approval_array[$x]->approval_status;
		$approval_done_by= $order_approval_array[$x]->approval_done_by;
		$remarks= $order_approval_array[$x]->remarks;
		$changed_order_for=$order_approval_array[$x]->changed_order_for;
		
		$sqlempcodestar="SELECT emp_name FROM employee_master WHERE emp_code='".$location_emp_code."'";
		$rsempcodestar=mysqli_query($link,$sqlempcodestar);
		$rowempcodestar=mysqli_fetch_assoc($rsempcodestar);
		${emp_name.$APPORDERNO} = $rowempcodestar['emp_name'];
		
		$sqlcustcodestar="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustcodestar=mysqli_query($link,$sqlcustcodestar);
		$rowcustcodestar=mysqli_fetch_assoc($rscustcodestar);
		${dns_customer_code.$APPORDERNO} = $rowcustcodestar['dns_customer_code'];
		
		$sqldestcodestar="SELECT dns_destination_code FROM destination_master WHERE destination_code='".$changed_dns_destination_code."'";
		$rsdestcodestar=mysqli_query($link,$sqldestcodestar);
		$rowdestcodestar=mysqli_fetch_assoc($rsdestcodestar);
		${dns_destination_code.$APPORDERNO} = $rowdestcodestar['dns_destination_code'];
		 
		 if($changed_sub_dealer_code!='')
		 {
			$sqlsubdealercodestar="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$changed_sub_dealer_code."'";
			$rssubdealercodestar=mysqli_query($link,$sqlsubdealercodestar);
			$rowsubdealercodestar=mysqli_fetch_assoc($rssubdealercodestar);
			${dns_subdealer_code.$APPORDERNO} = $rowsubdealercodestar['dns_customer_code'];
		 }
		 else
		 {
			 ${dns_subdealer_code.$APPORDERNO}='';
		 }
		
		//For checking that trans id exist or not for order
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check new customer: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for order
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$orderapproval_array_trans_id))
			{
				array_push($orderapproval_array_trans_id,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$location_latt."',
									longi='".$location_longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update order approval location: ".$sqlupdateorlocation);
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
		/*$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
		$rsempname=mysqli_query($link,$sqlempname);
		$rowempname=mysqli_fetch_assoc($rsempname);
		$emp_name=title_case_emp($rowempname['emp_name']);
		$branch_code=$rowempname['branch_code'];
		$vertical_value=$rowempname['vertical_value'];*/
		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

		//For Insert into the location table for new trans id regarding wholesaler info
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
		
			//For addition of Wholesaler info
			$sqlupdateorderapp="UPDATE T_APPERPDO_APPROVAL SET approval_id='".$approval_id."',
								changed_dns_prod_code ='".$changed_dns_prod_code."',
							   QTY_CHANGED					='".$QTY_CHANGED."',
							   changed_dns_destination_code	='".$changed_dns_destination_code."',
							   changed_dump_code			='".$changed_dump_code."',
							   changed_sub_dealer_code		='".$changed_sub_dealer_code."',
							   plant_name					='".$plant_name."',
							   approval_status				='".$approval_status."',
							   approval_done_by				='".$approval_done_by."',
							   remarks						='".$remarks."',
							   changed_order_for			='".$changed_order_for."',
							   download_time				=CURRENT_TIMESTAMP() WHERE 	APPORDERNO='".$APPORDERNO."' AND customer_code='".$customer_code."'";
			if(mysqli_query($link,$sqlupdateorderapp))
			{
				$flag=5;
				$refreshflag=1;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
	  }//End of else
	}
}
 /* --------------------END QUERY FOR ORDER APPROVAL--------------------------------------------------------------------------------------------------------*/
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
	 
	 if($countdatarefresh >0)
	 {
		 echo $flag=2;
	 }
	 else
	 {
	 	echo $flag=1;
	}
}
if($flag==1 || $flag==2)
{
	define("SERVERSAATHI","103.87.174.95");
	define("USERSAATHI","starsaat_dnsprod");
	define("PASSWORDSAATHI","dnsprod1234#");
	define("DB","starsaat_START");
		
	$linksaathi=mysqli_connect(SERVERSAATHI,USERSAATHI,PASSWORDSAATHI,TRUE) or die("Database Connection Error.");
	mysqli_select_db(starsaat_START,$linksaathi) or die("could not connect the database");
	//mysqli_query($link,"SET AUTOCOMMIT=0");
	//mysqli_query($link,"START TRANSACTION");
	//count($order_approval_array);
	//print_r($order_approval_array);
	if(count($order_approval_array)>0)
	{
	for($k=0;$k<count($order_approval_array);$k++){
		$location_emp_code=$order_approval_array[$k]->location_emp_code;
		$location_trans_id=$order_approval_array[$k]->location_trans_id;
		$location_latt=$order_approval_array[$k]->location_latt;
		$location_longi=$order_approval_array[$k]->location_longi;
		$location_date=$order_approval_array[$k]->location_date;
		$approval_id=$order_approval_array[$k]->approval_id;
		$APPORDERNO= $order_approval_array[$k]->APPORDERNO;
		$customer_code=$order_approval_array[$k]->customer_code;
		$changed_sub_dealer_code=$order_approval_array[$k]->changed_sub_dealer_code;
		$changed_dns_prod_code=$order_approval_array[$k]->changed_dns_prod_code;
		$QTY_CHANGED=$order_approval_array[$k]->QTY_CHANGED;
		$changed_dns_destination_code= $order_approval_array[$k]->changed_dns_destination_code;
		$changed_dump_code= $order_approval_array[$k]->changed_dump_code;
		$plant_name= $order_approval_array[$k]->plant_name;
		$approval_status= $order_approval_array[$k]->approval_status;
		$approval_done_by= $order_approval_array[$k]->approval_done_by;
		$remarks= $order_approval_array[$k]->remarks;
		$changed_order_for=$order_approval_array[$k]->changed_order_for;
				
		$sqlbranchcode="SELECT branch_code,customer_name,address FROM customer_master WHERE dns_customer_code='".${dns_customer_code.$APPORDERNO}."'";
		$rsbranchcode=mysqli_query($link,$sqlbranchcode,$linksaathi);
		$rowbranchcode=mysqli_fetch_assoc($rsbranchcode);
		$branch_code = $rowbranchcode['branch_code'];
		$consignee_name = $rowbranchcode['customer_name'];
		$consignee_address = $rowbranchcode['address'];
		
		$sqlprod = "select prod_code,prod_desc from product_master where `dns_prod_code`='".$changed_dns_prod_code."' 
					AND branch_code='".$branch_code."'";	
		$resprod = mysqli_query($link,$sqlprod,$linksaathi);
		$rowprod = mysqli_fetch_assoc($resprod);
		$prod_code = $rowprod["prod_code"];
		$prod_desc = $rowprod["prod_desc"];
		$sqldestcodesaathi="SELECT destination_code,destination_name FROM destination_master WHERE dns_destination_code='".${dns_destination_code.$APPORDERNO}."'";
		$rsdestcodesaathi=mysqli_query($link,$sqldestcodesaathi,$linksaathi);
		$rowdestcodesaathi=mysqli_fetch_assoc($rsdestcodesaathi);
		$destination_code = $rowdestcodesaathi['destination_code'];
		$destination_name = $rowdestcodesaathi['destination_name'];
		if($changed_sub_dealer_code!='')
		{
			$sqlsubdealercodesaathi="SELECT customer_code,customer_name,address FROM customer_master WHERE dns_customer_code='".${dns_subdealer_code.$APPORDERNO}."'";
			$rssubdealercodesaathi=mysqli_query($link,$sqlsubdealercodesaathi,$linksaathi);
			$rowsubdealercodesaathi=mysqli_fetch_assoc($rssubdealercodesaathi);
			$sub_dealer_code = $rowsubdealercodesaathi['customer_code'];
			$consignee_name = $rowsubdealercodesaathi['customer_name'];
			$consignee_address = $rowsubdealercodesaathi['address'];
		}
		if($changed_order_for!='')
		{
			$consignee_name=$changed_order_for;
			$consignee_address=$changed_order_for;
		}
		$sqldumpname="SELECT dump_name FROM branch_dump WHERE branch_code='".$branch_code."' AND dump_code='".$changed_dump_code."'";
		$rsdumpname=mysqli_query($link,$sqldumpname,$linksaathi);
		$rowdumpname=mysqli_fetch_assoc($rsdumpname);
		$dump_name = $rowdumpname['dump_name'];
		
		if($approval_status=='AUTHORIZE')
		{
			$starsaathi_status='Order authorized';
			$sqlupdateorderappsaathii="UPDATE T_APPERPDO_TEMP  SET  status					='".$starsaathi_status."',
								authorized_by			='".${emp_name.$APPORDERNO}."',
								authorization_date		=CURRENT_TIMESTAMP() WHERE 	
								APPORDERNO='".$APPORDERNO."' AND dns_customer_code='".${dns_customer_code.$APPORDERNO}."'";
		   mysqli_query($link,$sqlupdateorderappsaathii,$linksaathi)or die(mysqli_error().".Internal error occurrs  in T_APPERPDO_TEMP Approval.Please check.");
		}
		 if($approval_status=='MODIFY')
		 {
			 $starsaathi_status='Order authorized';
			 $sqlupdateorderappsaathii="UPDATE T_APPERPDO_TEMP  SET dns_prod_code ='".$changed_dns_prod_code."',
								prod_code ='".$prod_code."',
								prod_display_name ='".$prod_desc."',
							    QTY	='".$QTY_CHANGED."',
							    destination_code	='".$destination_code."',
								destination_name	='".$destination_name."',
								destination_address	='".$destination_name."',
							    dump_code				='".$changed_dump_code."',
								dump_name				='".$dump_name."',
							    sub_dealer_code		    ='".$sub_dealer_code."',
								dns_sub_dealer_code		='".${dns_subdealer_code.$APPORDERNO}."',
								consignee_name			='".$consignee_name."',
								consignee_address		='".$consignee_address."',
							    order_for				='".$changed_order_for."',
								status					='".$starsaathi_status."',
								authorized_by			='".${emp_name.$APPORDERNO}."',
								authorization_date		=CURRENT_TIMESTAMP() 
								WHERE 	
								APPORDERNO='".$APPORDERNO."' AND dns_customer_code='".${dns_customer_code.$APPORDERNO}."'";
		  mysqli_query($link,$sqlupdateorderappsaathii,$linksaathi)or die(mysqli_error().".Internal error occurrs  in T_APPERPDO_TEMP Modify.Please check.");
		 }
		if(rtrim($approval_status)=='CANCEL')
		{
			$starsaathi_status='Order canceled';
			$sqlupdateorderappsaathii="UPDATE T_APPERPDO_TEMP  SET status					='".$starsaathi_status."',
								authorized_by			='".${emp_name.$APPORDERNO}."',
								authorization_date		=CURRENT_TIMESTAMP() 
								WHERE 	
								APPORDERNO='".$APPORDERNO."' AND dns_customer_code='".${dns_customer_code.$APPORDERNO}."'";
		   mysqli_query($link,$sqlupdateorderappsaathii,$linksaathi)or die(mysqli_error().".Internal error occurrs  in T_APPERPDO_TEMP Cancel.Please check.");
		}
	}
	 //mysqli_query($link,"COMMIT");
 }
	
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/operationdb-order-approval.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
