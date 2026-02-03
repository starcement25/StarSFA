<?php
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ??'';
$last_update_time=str_replace('€',' ',$last_update_time);

$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><COLLECTION_FORECAST><location><emp_code><![CDATA[E0154]]></emp_code><trans_id><![CDATA[CFE015420190314162809]]></trans_id><latt><![CDATA[22.7278526]]></latt><longi><![CDATA[88.4906733]]></longi><date><![CDATA[2019-03-14 16:28:09]]></date></location><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000055]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45005]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[11]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000145]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45005]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[22]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000115]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45005]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[33]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000002]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45002]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[44]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000003]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45003]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[55]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000146]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45006]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[66]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000005]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45005]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[77]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000006]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45006]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[88]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000088]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45008]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[99]]></AMOUNT_RECEIVED></FORECAST_DETAILS><FORECAST_DETAILS><FORECAST_ID><![CDATA[CFE015420190314162809]]></FORECAST_ID><CUSTOMER_CODE><![CDATA[C/0000007]]></CUSTOMER_CODE><FORECAST_DATE><![CDATA[2019-03-14]]></FORECAST_DATE><INVOICE_AMOUNT><![CDATA[45007]]></INVOICE_AMOUNT><AMOUNT_RECEIVED><![CDATA[1010]]></AMOUNT_RECEIVED></FORECAST_DETAILS></COLLECTION_FORECAST></root>";*/

$location_emp_code="*ROOT*COLLECTION_FORECAST*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*COLLECTION_FORECAST*LOCATION*TRANS_ID";
$latt = "*ROOT*COLLECTION_FORECAST*LOCATION*LATT";
$longi = "*ROOT*COLLECTION_FORECAST*LOCATION*LONGI";
$location_date = "*ROOT*COLLECTION_FORECAST*LOCATION*DATE";
$forecast_id="*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS*FORECAST_ID";
$customer_code="*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS*CUSTOMER_CODE";
$forecast_date="*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS*FORECAST_DATE";
$invoice_amount = "*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS*INVOICE_AMOUNT";
$amount_received ="*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS*AMOUNT_RECEIVED";

$collection_forecast_array = array();
$collection_forecast_trans_id_array=array();
$collection_forecast_emp_code_array=array();
$collection_forecast_details_array=array();
$counter = 0;
$counterforecast=0;

class xml_collection_forecast{
	var $location_emp_code,$location_trans_id,$latt,$longi,$location_date;
}
class xml_collection_forecast_details{
	var $forecast_id,$customer_code,$forecast_date,$invoice_amount,$amount_received;
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
    global $location_emp_code,$location_trans_id,$latt,$longi,$location_date,$current_tag,$forecast_id,$customer_code,$forecast_date,$invoice_amount,$amount_received,$collection_forecast_array,$collection_forecast_details_array,$counter,$counterforecast;
	if(substr($current_tag,0,25)=='*ROOT*COLLECTION_FORECAST')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$collection_forecast_array[$counter] = new xml_collection_forecast();
				$collection_forecast_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$collection_forecast_array[$counter]->location_trans_id = $data;
				break;
			case $latt:
				$collection_forecast_array[$counter]->latt = $data;
				break;
			case $longi:
				$collection_forecast_array[$counter]->longi = $data;
				break;
			case $location_date:
				$collection_forecast_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,42)=='*ROOT*COLLECTION_FORECAST*FORECAST_DETAILS')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
			case $forecast_id:
				$collection_forecast_details_array[$counterforecast] = new xml_collection_forecast_details();
				$collection_forecast_details_array[$counterforecast]->forecast_id = $data;
				break;
			case $customer_code:
				$collection_forecast_details_array[$counterforecast]->customer_code = $data;
				break;
			case $forecast_date:
				$collection_forecast_details_array[$counterforecast]->forecast_date = $data;
				break;
			case $invoice_amount:
				$collection_forecast_details_array[$counterforecast]->invoice_amount = $data;
				break;
			case $amount_received:
				$collection_forecast_details_array[$counterforecast]->amount_received = $data;
				$counterforecast++;
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
/* -----------------------------------------------------START QUERY FOR COLLECTION FORECAST-----------------------------------------------------------------*/
if(count($collection_forecast_array)>0)
{
	//$count=1;
	for($x=0;$x<count($collection_forecast_array);$x++){
		$location_emp_code=$collection_forecast_array[$x]->location_emp_code;
		$location_trans_id=$collection_forecast_array[$x]->location_trans_id;
		$latt=$collection_forecast_array[$x]->latt;
		$longi=$collection_forecast_array[$x]->longi;
		$location_date=$collection_forecast_array[$x]->location_date;
		
		//For checking that trans id exist or not for collection forecast
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check feight expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for collection forecast
		if($countchkorlocation>0)
		{
			if(!in_array($location_trans_id,$collection_forecast_trans_id_array))
			{
				array_push($collection_forecast_trans_id_array,$location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
									latt='".$latt."',
									longi='".$longi."'
									WHERE trans_id='".$location_trans_id."'";
			$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update feight expense location: ".$sqlupdateorlocation);
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
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_update_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			//$cash_depositreceive_details_date_db=date('Y-m-d',strtotime($cash_depositreceive_details_date));
			
			//For Insert into the location table for new trans id regarding collection forecast
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
									trans_id='".$location_trans_id."',
									latt='".$latt."',
									longi='".$longi."',
									date='".$location_date."',
									updatetime='".$location_update_date."'";
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
		}
	}
	 if(count($collection_forecast_details_array)>0)
		{
			for($i=0;$i<count($collection_forecast_details_array);$i++){
			$forecast_id=$collection_forecast_details_array[$i]->forecast_id;
			$customer_code=$collection_forecast_details_array[$i]->customer_code;
			$forecast_date=$collection_forecast_details_array[$i]->forecast_date;
			$invoice_amount=$collection_forecast_details_array[$i]->invoice_amount;
			$amount_received=$collection_forecast_details_array[$i]->amount_received;
		
			//For Insert into the collection forecast details table for new trans id
			$sqlinsertcollectionforecast="INSERT INTO collection_forecast_details SET forecast_id='".$forecast_id."',
									  customer_code 			='".$customer_code."',
									  forecast_date 			='".$forecast_date."',
									  invoice_amount 			='".$invoice_amount."',
									  amount_received 			='".$amount_received."'";
			if(mysqli_query($link,$sqlinsertcollectionforecast))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				/*$sqlrds="SELECT rds_code,rds_name FROM rds_master WHERE emp_code='".$feight_expense_emp_code."'";
				$resrds=mysqli_query($link,$sqlrds) or die(mysqli_error()." Error in select rds: ".$sqlrds);
				$rowrds=mysqli_fetch_assoc($resrds);
				
				$rds_code=$rowrds['rds_code'];
				$rds_name=$rowrds['rds_name'];

				$emailsubj="$nick_name - Expense Booked on ".date('d-m-Y',strtotime($feight_expense_date))." @".date('H:i:s',strtotime($feight_expense_date)).
							' hrs.'." from ".$rds_name;

				
				$emailbody = "<html><head><title>Freight Expense</title></head>
								<body><table>Expenses booked against <b>".$feight_expense_trans_type."</b> Charges for ".$rds_name."
								<br /><br />Refference no: <b>".$feight_expense_trans_id."</b></table><br><br>
								<table border=1 style=background-color:AliceBlue>
									<tr>
									<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
									CE'>Amount</span></strong></th>
									<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
									CE'>Date</span></strong></th>
									</tr><tr>
								<td style='width:50px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($feight_expense_amount,2)."</span>&nbsp;</td>
								<td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$feight_expense_details_date."</span>&nbsp;</td>
							</tr></table><br />
								<b>Remarks: </b> ".strtoupper($feight_expense_remarks)."
								<br><br><br>Powered By aceDNS<br></body></html>";					
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\n";
				$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
							"Reply-To:".FROMEMAIL." \r\n" .
							"Bcc: ".BCCEMAIL." \r\n".
							'X-Mailer: PHP/' . phpversion();
				if(mail(TOUREMAILRECIPENTS, $emailsubj, $emailbody, $headers,'-facedns@acedns.in'))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}*/
			}// End of for loop					  
   }
	//if($flag==5){}
}
 /* --------------------END QUERY FOR CASH DEPOSIT RECEIVE----------------------------------------------------------------------------------------------------*/
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
?>