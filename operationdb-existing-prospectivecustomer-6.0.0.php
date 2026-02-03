<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);*/
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><prospective_customer><location><emp_code><![CDATA[E0005]]></emp_code><trans_id><![CDATA[DEE000520171227180525]]></trans_id><latt><![CDATA[22.5644528]]></latt><longi><![CDATA[88.3567887]]></longi><date><![CDATA[2017-12-27 18:05:25]]></date></location><prospective_customer_header><trans_id><![CDATA[DEE000520171227180525]]></trans_id><customer_code><![CDATA[DCE000520171227180500]]></customer_code><customer_name> <![CDATA[Sir]]></customer_name><remarks><![CDATA[test existing]]></remarks></prospective_customer_header></prospective_customer></root>";*/

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}

$emp_code = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*EMP_CODE";
$trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*TRANS_ID";
$prospect_date = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*DATE";
$latt = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LATT";
$longi = "*ROOT*PROSPECTIVE_CUSTOMER*LOCATION*LONGI";

$prospectiveheader_trans_id = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*TRANS_ID";
$customer_code = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUSTOMER_CODE";
$customer_name = "*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*CUSTOMER_NAME";
$remarks="*ROOT*PROSPECTIVE_CUSTOMER*PROSPECTIVE_CUSTOMER_HEADER*REMARKS";

$prospective_customer_exists_array=array();

$counter = 0;

class xml_customer_exists_header{
    var $emp_code, $trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$customer_code,$customer_name,$remarks;
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
    global $current_tag, $emp_code,$trans_id,$prospect_date,$latt,$longi,$prospectiveheader_trans_id,$customer_code,$customer_name,$remarks,$counter,$prospective_customer_exists_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,26)=='*ROOT*PROSPECTIVE_CUSTOMER')
	{
		switch($current_tag){
			case $emp_code:
				$prospective_customer_exists_array[$counter] = new xml_customer_exists_header();
				$prospective_customer_exists_array[$counter]->emp_code = $data;
				break;
			case $trans_id:
				$prospective_customer_exists_array[$counter]->trans_id = $data;
				break;
			case $prospect_date:
				$prospective_customer_exists_array[$counter]->prospect_date = $data;
				break;	
			case $latt:
				$prospective_customer_exists_array[$counter]->latt = $data;
				break;
			case $longi:
				$prospective_customer_exists_array[$counter]->longi = $data;
				break;
			case $prospectiveheader_trans_id:
				$prospective_customer_exists_array[$counter]->prospectiveheader_trans_id = $data;
				break;
			case $customer_code:
				$prospective_customer_exists_array[$counter]->customer_code = $data;
				break;
			case $customer_name:
				$prospective_customer_exists_array[$counter]->customer_name = $data;
				break;		
			case $remarks:
				$prospective_customer_exists_array[$counter]->remarks = $data;
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

mysqli_query($link,"SET AUTOCOMMIT=0");
mysqli_query($link,"START TRANSACTION");
$flag=1;
/* ------------------------------------------------START QUERY FOR PROSPECTIVE CUSTOMER EXISTS---------------------------------------------------------------------*/
$prospectivecustomer_array_trans_id=array();

if(count($prospective_customer_exists_array)>0)
{
	$prospective_array_trans_id=array();
	for($x=0;$x<count($prospective_customer_exists_array);$x++){
		$emp_code=$prospective_customer_exists_array[$x]->emp_code;
		$trans_id=$prospective_customer_exists_array[$x]->trans_id;
		$prospect_date=$prospective_customer_exists_array[$x]->prospect_date;
		$latt=$prospective_customer_exists_array[$x]->latt;
		$longi=$prospective_customer_exists_array[$x]->longi;
		$prospectiveheader_trans_id=$prospective_customer_exists_array[$x]->prospectiveheader_trans_id;
		$customer_code=$prospective_customer_exists_array[$x]->customer_code;
		$customer_name=$prospective_customer_exists_array[$x]->customer_name;	
		$remarks=$prospective_customer_exists_array[$x]->remarks;
		
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$latt."',longi='".$longi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkattlocation="SELECT * FROM location WHERE trans_id='".$trans_id."'";
		$reschkattlocation = mysqli_query($link,$sqlchkattlocation) or die(mysqli_error()." Error in check prospective location: ".$sqlchkattlocation); 
		$rowchkattlocation = mysqli_fetch_assoc($reschkattlocation);
		$countchkattlocation=mysqli_num_rows($reschkattlocation);
		
		//For update the location table for existing trans id
		if($countchkattlocation>0)
		{
			if(!in_array($trans_id,$prospective_array_trans_id))
			{
				array_push($prospective_array_trans_id,$trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update prospective customer exisists location: ".$sqlupdateorlocation);
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
			// create the data for prospective customer header table date field , by checking the current date and time and the actual date and time of occurrence
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));
			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding prospective customer
			$sqlinsertprolocation="INSERT INTO location SET emp_code='".$emp_code."',
									trans_id='".$trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$prospect_date."',
									updatetime='".$location_date."'"; 

			//For Insert into the prospective customer header table for new trans id regarding prospective customer
			$sqlinsertproheader="INSERT INTO prospective_customer_header SET trans_id='".$prospectiveheader_trans_id."',
									customer_name='".addslashes($customer_name)."',
									customer_code='".addslashes($customer_code)."',
									remarks='".addslashes($remarks)."'";
			if(mysqli_query($link,$sqlinsertprolocation) && mysqli_query($link,$sqlinsertproheader))
				{
				   $flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				
				//Regarding Email Sending
				/*$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				$branch_code=$rowempname['branch_code'];
				$vertical_value=$rowempname['vertical_value'];
		
				if(branch_vertical_operation_wise_email=='yes'){
					$operation_type='Prospectivecustomeradd';
					$prospect_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				}
				else
				{
					if($nick_name=='RUPA')
					{
					 $prospect_email='';
					}
					else
					{
					 $prospect_email=PROSPECTEMAILRECIPENTS;
					}
				}
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$tagged_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$tagged_customer_name=$rowcustomername['customer_name'];
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$area."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
				
				//For constructing the email body for PROSPECTIVE CUSTOMER HEADER if existing customer Customer remarks addition has performed
				$prospectivecustomermailbody = "<html><head><title>Prospective Customer Exists</title></head>
							<body>Auto generated mail for <b>".$nick_name." aceDNS</b> mobile application from<b>"
							.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue cell cellpadding=0 cellspacing=4>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center' colspan='2'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Prospect Name</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Date & Time</span></strong></th>
							<th style='width:250px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Tagged To Customer</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white' colspan='2'>
							<span style='font-family:Arial CE;font-size:10pt' >
							".strtoupper($customer_name)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($prospect_date))."</span>&nbsp;</td>
							<td style='width:250px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$tagged_customer_name."</span>&nbsp;</td>
							</tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Address</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Pin</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Area</span></strong></th>
							<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
							CE'>Type</span></strong></th>
							</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							".strtoupper($address)."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".$pin."</span>&nbsp;</td>
							<td style='width:165px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($route_name)."</span>&nbsp;</td>
							<td style='width:165px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>".strtoupper($cust_type_insert)."</span>&nbsp;</td>
							</tr>
							<tr>
							<th style='width:260px;min-height:21px;text-align:center'>
							<strong><span style='font-size:10pt;font-family:Arial 
							CE'>Phone no</span></strong></th></tr>
							<tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
							<span style='font-family:Arial CE;font-size:10pt'>
							+91-".$phone_no."</span>&nbsp;</td></tr></table>";*/
							
					}//End of else
		 }// End for loop
	}//End of if
 /* --------------------END QUERY FOR PROSPECTIVE CUSTOMER EXISTS---------------------------------------------------------------------------------------------*/
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
