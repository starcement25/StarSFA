<?php
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);
$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);

$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);	

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><merchandising_data><location><emp_code><![CDATA[C0007]]></emp_code><trans_id><![CDATA[MCC000720141018165159]]></trans_id><latt><![CDATA[0.0]]></latt><longi><![CDATA[0.0]]></longi><date><![CDATA[2014-10-18 16:51:59]]></date></location><merchandising_details><merchandising_id><![CDATA[MCC000720141018165159]]></merchandising_id><emp_code><![CDATA[C0007]]></emp_code><prod_code><![CDATA[12003]]></prod_code><remarks><![CDATA[STRUCTURE;DAMAGE]]></remarks><attachment_id><![CDATA[C000720141018165133.png]]></attachment_id><trans_type><![CDATA[REPORTING ISSUE]]></trans_type><client_id><![CDATA[C/0000002]]></client_id></merchandising_details></merchandising_data></root>";*/


$merchandising_location_emp_code="*ROOT*MERCHANDISING_DATA*LOCATION*EMP_CODE";
$merchandising_location_trans_id = "*ROOT*MERCHANDISING_DATA*LOCATION*TRANS_ID";
$merchandising_location_latt = "*ROOT*MERCHANDISING_DATA*LOCATION*LATT";
$merchandising_location_longi = "*ROOT*MERCHANDISING_DATA*LOCATION*LONGI";
$merchandising_location_date="*ROOT*MERCHANDISING_DATA*LOCATION*DATE";
$merchandising_details_id = "*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*MERCHANDISING_ID";
$merchandising_details_emp_code = "*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*EMP_CODE";
$merchandising_details_prod_code ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*PROD_CODE";
$merchandising_details_remarks ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*REMARKS";
$merchandising_details_attachment_id ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*ATTACHMENT_ID";
$merchandising_details_trans_type ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*TRANS_TYPE";
$merchandising_details_client_id ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*CLIENT_ID";
$merchandising_details_issue_status ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*ISSUE_STATUS";
$merchandising_details_rectifying_issue_id ="*ROOT*MERCHANDISING_DATA*MERCHANDISING_DETAILS*RECTIFYING_ISSUE_ID";


$merchandising_array = array();
$merchandising_trans_id_array=array();
$merchandising_emp_code_array=array();
$counter = 0;

class xml_merchandising{
	var $merchandising_location_emp_code,$merchandising_location_trans_id,$merchandising_location_latt,$merchandising_location_longi,$merchandising_location_date,$merchandising_details_id,$merchandising_details_emp_code,$merchandising_details_prod_code,$merchandising_details_remarks,$merchandising_details_attachment_id,$merchandising_details_trans_type,$merchandising_details_client_id,$merchandising_details_issue_status,$merchandising_details_rectifying_issue_id;
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
    global $merchandising_location_emp_code,$merchandising_location_trans_id,$merchandising_location_latt,$merchandising_location_longi,$merchandising_location_date,$merchandising_details_id,$merchandising_details_emp_code,$merchandising_details_prod_code,$merchandising_details_remarks,$merchandising_details_attachment_id,$merchandising_details_trans_type,$merchandising_details_client_id,$merchandising_details_issue_status,$merchandising_details_rectifying_issue_id,$current_tag,$merchandising_array,$counter;
	
	if(substr($current_tag,0,24)=='*ROOT*MERCHANDISING_DATA')
	{
		switch($current_tag){
			case $merchandising_location_emp_code:
				$merchandising_array[$counter] = new xml_merchandising();
				$merchandising_array[$counter]->merchandising_location_emp_code = $data;
				break;
			case $merchandising_location_trans_id:
				$merchandising_array[$counter]->merchandising_location_trans_id = $data;
				break;
			case $merchandising_location_latt:
				$merchandising_array[$counter]->merchandising_location_latt = $data;
				break;
			case $merchandising_location_longi:
				$merchandising_array[$counter]->merchandising_location_longi = $data;
				break;
			case $merchandising_location_date:
				$merchandising_array[$counter]->merchandising_location_date = $data;
				break;		
			case $merchandising_details_id:
				$merchandising_array[$counter]->merchandising_details_id = $data;
				break;
			case $merchandising_details_emp_code:
				$merchandising_array[$counter]->merchandising_details_emp_code = $data;
				break;
			case $merchandising_details_prod_code:
				$merchandising_array[$counter]->merchandising_details_prod_code = $data;
				break;
			case $merchandising_details_remarks:
				$merchandising_array[$counter]->merchandising_details_remarks = $data;
				break;
			case $merchandising_details_attachment_id:
				$merchandising_array[$counter]->merchandising_details_attachment_id = $data;
				break;
			
			case $merchandising_details_client_id:
				$merchandising_array[$counter]->merchandising_details_client_id = $data;
				break;
			case $merchandising_details_issue_status:
				$merchandising_array[$counter]->merchandising_details_issue_status = $data;
				break;
			case $merchandising_details_rectifying_issue_id:
				$merchandising_array[$counter]->merchandising_details_rectifying_issue_id = $data;
				break;		
			case $merchandising_details_trans_type:
				$merchandising_array[$counter]->merchandising_details_trans_type = $data;
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
/* -----------------------------------------------------START QUERY FOR MERCHAINDISING----------------------------------------------------------------------*/
if(count($merchandising_array)>0)
{
	//$count=1;
	for($x=0;$x<count($merchandising_array);$x++){
		$merchandising_location_emp_code=$merchandising_array[$x]->merchandising_location_emp_code;
		$merchandising_location_trans_id=$merchandising_array[$x]->merchandising_location_trans_id;
		$merchandising_location_latt=$merchandising_array[$x]->merchandising_location_latt;
		$merchandising_location_longi=$merchandising_array[$x]->merchandising_location_longi;
		$merchandising_location_date=$merchandising_array[$x]->merchandising_location_date;
		$merchandising_details_id=$merchandising_array[$x]->merchandising_details_id;
		$merchandising_details_emp_code=$merchandising_array[$x]->merchandising_details_emp_code;
		$merchandising_details_prod_code=$merchandising_array[$x]->merchandising_details_prod_code;
		$merchandising_details_remarks=$merchandising_array[$x]->merchandising_details_remarks;
		$merchandising_details_attachment_id=$merchandising_array[$x]->merchandising_details_attachment_id;
		$merchandising_details_trans_type=$merchandising_array[$x]->merchandising_details_trans_type;
		$merchandising_details_client_id=$merchandising_array[$x]->merchandising_details_client_id;
		$merchandising_details_issue_status=$merchandising_array[$x]->merchandising_details_issue_status;
		$merchandising_details_rectifying_issue_id=$merchandising_array[$x]->merchandising_details_rectifying_issue_id;
		
		//For checking that trans id exist or not for merchandising
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$merchandising_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check feight expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for merchandising
		if($countchkorlocation>0)
		{
			if(!in_array($merchandising_location_trans_id,$merchandising_trans_id_array))
			{
				array_push($merchandising_trans_id_array,$merchandising_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$merchandising_location_emp_code."',
									latt='".$merchandising_location_latt."',
									longi='".$merchandising_location_longi."'
									WHERE trans_id='".$merchandising_location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update merchandising location: ".$sqlupdateorlocation);
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of merchandising
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			
			//For Insert into the location table for new trans id regarding merchandising
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$merchandising_location_emp_code."',
									trans_id='".$merchandising_location_trans_id."',
									latt='".$merchandising_location_latt."',
									longi='".$merchandising_location_longi."',
									date='".$merchandising_location_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the merchandising_details table for new trans id
			$sqlinsertmerchandising="INSERT INTO merchandising_details SET 	merchandising_id='".$merchandising_details_id."',
									  emp_code 			='".$merchandising_details_emp_code."',
									  prod_code 		='".$merchandising_details_prod_code."',
									  remarks 			='".$merchandising_details_remarks."',
									  trans_type		='".$merchandising_details_trans_type."',
									  client_id			='".$merchandising_details_client_id."',
									  issue_status		='".$merchandising_details_issue_status."',
									  rectifying_issue_id='".$merchandising_details_rectifying_issue_id."',
									  attachment_file 	='".$merchandising_details_attachment_id."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertmerchandising))
				{
					$flag=5;
					$sqlupdatemerchandising="UPDATE merchandising_details SET issue_status='".$merchandising_details_issue_status."' 
											WHERE merchandising_id='".$merchandising_details_rectifying_issue_id."'";
					if(mysqli_query($link,$sqlupdatemerchandising))
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
				$sqlemployee="SELECT emp_code,emp_name FROM employee_master WHERE emp_code='".$merchandising_details_emp_code."'";
				$resemployee=mysqli_query($link,$sqlemployee) or die(mysqli_error()." Error in select employee: ".$sqlemployee);
				$rowemployee=mysqli_fetch_assoc($resemployee);
				$emp_code=$rowemployee['emp_code'];
				$emp_name=$rowemployee['emp_name'];
				
				$sqlroute="SELECT route_name FROM route_master WHERE emp_code='".$merchandising_details_emp_code."'";
				$resroute=mysqli_query($link,$sqlroute) or die(mysqli_error()." Error in select route: ".$sqlroute);
				$rowroute=mysqli_fetch_assoc($resroute);
				$route_name=$rowroute['route_name'];
				
				$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$merchandising_details_client_id."'";
				$rescustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer: ".$sqlcustomer);
				$rowcustomer=mysqli_fetch_assoc($rescustomer);
				$customer_name=$rowcustomer['customer_name'];

				if($merchandising_details_trans_type=='RECTIFYING ISSUE')
				{
					$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc,MD.remarks FROM 
									product_master PM,product_group_master PGM,merchandising_details MD
									WHERE PM.product_group_code=PGM.product_group_code  AND 
									MD.prod_code=PM.prod_code AND MD.merchandising_id='".$merchandising_details_rectifying_issue_id."'";
				}
				else
				{
					$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM 
									product_master PM,product_group_master PGM
									WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$merchandising_details_prod_code."'";
				}
				$rsproductdetails=mysqli_query($link,$sqlproductdetails);
				$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
				$prod_desc=$rowproductdetails['prod_desc'];
				$product_group_name=$rowproductdetails['product_group_name'];
					
				if($product_group_name=='Hoarding' && $merchandising_details_trans_type!='RECTIFYING ISSUE')
				{
					$merchandising_details_Array=explode(';',$merchandising_details_remarks);
					$issue_type=$merchandising_details_Array[0];
					$issue_detail=$merchandising_details_Array[1];
					$issue_note=$merchandising_details_Array[2];
				}
				else if($product_group_name=='Hoarding' && $merchandising_details_trans_type=='RECTIFYING ISSUE')
				{
					$reporting_issue_remarks=$rowproductdetails['remarks'];
					$merchandising_details_Array=explode(';',$reporting_issue_remarks);
					$issue_type=$merchandising_details_Array[0];
					$issue_detail=$merchandising_details_Array[1];
					$issue_note=$merchandising_details_remarks;
				}
				else
				{
					$issue_type='';
					$issue_detail='';
					$issue_note=$merchandising_details_remarks;
				}
				$image_name=URL.'/upload/'.$nick_name.'/'.$merchandising_details_attachment_id;
				
				if($merchandising_details_trans_type=='REPORTING ISSUE')
				{
					$emailsubjtext="Issue Reported";
					$emailbodytext="<html><head><title>REPORTING ISSUE</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1>
									<tr style=background-color:AliceBlue>
										<th style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Date</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Property Type</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Location</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Zone</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Type</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Detail</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Reported by</span></strong></th>
										<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Note</span></strong></th>
									</tr>
									<tr>
										<td style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".date('d-M-y',strtotime($merchandising_location_date))."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$product_group_name."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$prod_desc."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$route_name."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_type."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_detail."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$emp_name."</span></strong></td>
										<td style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_note."</span></strong></td>
									</tr>
								</table>";	
				}
				else if($merchandising_details_trans_type=='CHECK STATUS')
				{
					$emailsubjtext="Status Checked";
					$emailbodytext="<html><head><title>CHECK STATUS</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1>
									<tr style=background-color:AliceBlue>
										<th style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Date</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Property Type</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Location</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Zone</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Client name</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Reported by</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Image</span></strong></th>
									</tr>
									<tr>
										<td style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".date('d-M-y',strtotime($merchandising_location_date))."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$product_group_name."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$prod_desc."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$route_name."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$customer_name."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$emp_name."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'><a href=".$image_name.">IMAGE</a></span></strong></td>
									</tr>
								</table>";
				}
				else
				{
					$sqlemployeereported="SELECT EM.emp_name FROM employee_master EM,merchandising_details MD
									WHERE EM.emp_code=MD.emp_code AND MD.merchandising_id='".$merchandising_details_rectifying_issue_id."'";
					$resemployeereported=mysqli_query($link,$sqlemployeereported) or die(mysqli_error()." Error in select reported employee: ".$sqlemployeereported);
					$rowemployeereported=mysqli_fetch_assoc($resemployeereported);
					$emp_name_reported=$rowemployeereported['emp_name'];

					$emailsubjtext="Issue Rectified";
					$emailbodytext="<html><head><title>RECTIFYING ISSUE</title></head>
							<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
							.$emp_name. "</b><br><br><br><table border=1>
									<tr style=background-color:AliceBlue>
										<th style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Date</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Property Type</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Location</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Zone</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Type</span></strong></th>
										<th style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Detail</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Reported by</span></strong></th>
										<th style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Issue Note</span></strong></th>
										<th style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>Resolved by</span></strong></th>
									</tr>
									<tr>
										<td style='width:100px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".date('d-M-y',strtotime($merchandising_location_date))."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$product_group_name."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$prod_desc."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$route_name."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_type."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_detail."</span></strong></td>
										<td style='width:100px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$emp_name_reported."</span></strong></td>
										<td style='width:200px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$issue_note."</span></strong></td>
										<td style='width:160px;min-height:21px;text-align:left'><strong><span style='font-size:10pt;font-family:Arial 
										CE'>".$emp_name."</span></strong></td>
									</tr>
								</table>";	
				}
				
				$emailsubj="$emailsubjtext on ".date('d-m-Y',strtotime($merchandising_location_date))." @".date('H:i:s',strtotime($merchandising_location_date)).
							' hrs.'." by ".$emp_name;

				$emailbody = "<html><head><title>$emailsubjtext</title></head>
								<body><table></table><br><br>".$emailbodytext."
								<br><br><br>Powered By aceDNS<br></body></html>";					
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
				if(mail(ORDEREMAILRECIPENTS, $emailsubj, $emailbody, $headers,'-facedns@acedns.in'))
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
   }// End of for loop
	//if($flag==5){}
}
 /* --------------------END QUERY FOR MERCHAINDISING-------------------------------------------------------------------------------------------------------*/
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
mysqli_close($link);
?>