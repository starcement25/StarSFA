<?php
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ?? '';
$last_update_time=str_replace('€',' ',$last_update_time);

$body=file_get_contents('php://input');
//$body=str_replace("'",'"',$body);
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
						xml='".$body_xml."',
						insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><CASH_DEPOSITRECEIVE><location><emp_code><![CDATA[E0040]]></emp_code><trans_id><![CDATA[EXE004020181106144450]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2018-11-06 14:44:50]]></date></location><cash_deposit_recv_id><![CDATA[EXE004020181106144450]]></cash_deposit_recv_id><emp_code><![CDATA[E0040]]></emp_code><trans_type><![CDATA[EXD]]></trans_type><date><![CDATA[2018-11-05]]></date><amount><![CDATA[1]]></amount><remarks><![CDATA[rest;2018-11-06;1]]></remarks></CASH_DEPOSITRECEIVE></root>";*/

$cash_depositreceive_location_emp_code="*ROOT*CASH_DEPOSITRECEIVE*LOCATION*EMP_CODE";
$cash_depositreceive_location_trans_id = "*ROOT*CASH_DEPOSITRECEIVE*LOCATION*TRANS_ID";
$cash_depositreceive_latt = "*ROOT*CASH_DEPOSITRECEIVE*LOCATION*LATT";
$cash_depositreceive_longi = "*ROOT*CASH_DEPOSITRECEIVE*LOCATION*LONGI";
$cash_depositreceive_date="*ROOT*CASH_DEPOSITRECEIVE*LOCATION*DATE";
$cash_depositreceive_id = "*ROOT*CASH_DEPOSITRECEIVE*CASH_DEPOSIT_RECV_ID";
$cash_depositreceive_emp_code = "*ROOT*CASH_DEPOSITRECEIVE*EMP_CODE";
$cash_depositreceive_trans_type ="*ROOT*CASH_DEPOSITRECEIVE*TRANS_TYPE";
$cash_depositreceive_details_date ="*ROOT*CASH_DEPOSITRECEIVE*DATE";
$cash_depositreceive_remarks ="*ROOT*CASH_DEPOSITRECEIVE*REMARKS";
$cash_depositreceive_amount ="*ROOT*CASH_DEPOSITRECEIVE*AMOUNT";


$cash_depositreceive_array = array();
$cash_depositreceive_trans_id_array=array();
$cash_depositreceive_emp_code_array=array();
$counter = 0;

class xml_cash_depositreceive{
	var $cash_depositreceive_location_emp_code,$cash_depositreceive_location_trans_id,$cash_depositreceive_latt,$cash_depositreceive_longi,$cash_depositreceive_date,$cash_depositreceive_id,$cash_depositreceive_emp_code,$cash_depositreceive_trans_type,$cash_depositreceive_details_date,$cash_depositreceive_amount,$cash_depositreceive_remarks;
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
    global $cash_depositreceive_location_emp_code,$cash_depositreceive_location_trans_id,$cash_depositreceive_latt,$cash_depositreceive_longi,$cash_depositreceive_date,$current_tag,$cash_depositreceive_id,$cash_depositreceive_emp_code,$cash_depositreceive_trans_type,$cash_depositreceive_details_date,$cash_depositreceive_amount,$cash_depositreceive_remarks,$cash_depositreceive_array,$counter;
	if(substr($current_tag,0,25)=='*ROOT*CASH_DEPOSITRECEIVE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $cash_depositreceive_location_emp_code:
				$cash_depositreceive_array[$counter] = new xml_cash_depositreceive();
				$cash_depositreceive_array[$counter]->cash_depositreceive_location_emp_code = $data;
				break;
			case $cash_depositreceive_location_trans_id:
				$cash_depositreceive_array[$counter]->cash_depositreceive_location_trans_id = $data;
				break;
			case $cash_depositreceive_latt:
				$cash_depositreceive_array[$counter]->cash_depositreceive_latt = $data;
				break;
			case $cash_depositreceive_longi:
				$cash_depositreceive_array[$counter]->cash_depositreceive_longi = $data;
				break;
			case $cash_depositreceive_date:
				$cash_depositreceive_array[$counter]->cash_depositreceive_date = $data;
				break;		
			case $cash_depositreceive_id:
				$cash_depositreceive_array[$counter]->cash_depositreceive_id = $data;
				break;
			case $cash_depositreceive_emp_code:
				$cash_depositreceive_array[$counter]->cash_depositreceive_emp_code = $data;
				break;
			case $cash_depositreceive_trans_type:
				$cash_depositreceive_array[$counter]->cash_depositreceive_trans_type = $data;
				break;
			case $cash_depositreceive_details_date:
				$cash_depositreceive_array[$counter]->cash_depositreceive_details_date = $data;
				break;
			case $cash_depositreceive_amount:
				$cash_depositreceive_array[$counter]->cash_depositreceive_amount = $data;
				break;	
		   case $cash_depositreceive_remarks:
				$cash_depositreceive_array[$counter]->cash_depositreceive_remarks = $data;
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
/* -----------------------------------------------------START QUERY FOR CASH DEPOSIT RECEIVE-----------------------------------------------------------------*/
if(count($cash_depositreceive_array)>0)
{
	//$count=1;
	for($x=0;$x<count($cash_depositreceive_array);$x++){
		$cash_depositreceive_location_emp_code=$cash_depositreceive_array[$x]->cash_depositreceive_location_emp_code;
		$cash_depositreceive_location_trans_id=$cash_depositreceive_array[$x]->cash_depositreceive_location_trans_id;
		$cash_depositreceive_latt=$cash_depositreceive_array[$x]->cash_depositreceive_latt;
		$cash_depositreceive_longi=$cash_depositreceive_array[$x]->cash_depositreceive_longi;
		$cash_depositreceive_date=$cash_depositreceive_array[$x]->cash_depositreceive_date;
		$cash_depositreceive_id=$cash_depositreceive_array[$x]->cash_depositreceive_id;
		$cash_depositreceive_emp_code=$cash_depositreceive_array[$x]->cash_depositreceive_emp_code;
		$cash_depositreceive_trans_type=$cash_depositreceive_array[$x]->cash_depositreceive_trans_type;
		$cash_depositreceive_details_date=$cash_depositreceive_array[$x]->cash_depositreceive_details_date;
		$cash_depositreceive_amount=$cash_depositreceive_array[$x]->cash_depositreceive_amount;
		$cash_depositreceive_remarks=$cash_depositreceive_array[$x]->cash_depositreceive_remarks;
		
		//For checking that trans id exist or not for cash deposit
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$cash_depositreceive_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check feight expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for cash deposit
		if($countchkorlocation>0)
		{
			if(!in_array($cash_depositreceive_location_trans_id,$cash_depositreceive_trans_id_array))
			{
				array_push($cash_depositreceive_trans_id_array,$cash_depositreceive_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$cash_depositreceive_location_emp_code."',
									latt='".$cash_depositreceive_latt."',
									longi='".$cash_depositreceive_longi."'
									WHERE trans_id='".$cash_depositreceive_location_trans_id."'";
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
			// create the data for location table date field , by checking the current date and time and the actual date and time of feight expenses
			$date=gmdate('d',strtotime('+329 minute'));
			$month=gmdate('m',strtotime('+329 minute'));
			$year=gmdate('Y',strtotime('+329 minute'));

			$hour=gmdate('H',strtotime('+329 minute'));
			$minute=gmdate('i',strtotime('+329 minute'));
			$second=gmdate('s',strtotime('+329 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$cash_depositreceive_details_date_db=date('Y-m-d',strtotime($cash_depositreceive_details_date));
			
			//For Insert into the location table for new trans id regarding cash depositreceive
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$cash_depositreceive_location_emp_code."',
									trans_id='".$cash_depositreceive_location_trans_id."',
									latt='".$cash_depositreceive_latt."',
									longi='".$cash_depositreceive_longi."',
									date='".$cash_depositreceive_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the cash depositreceive table for new trans id
			$sqlinsertcashdeposit="INSERT INTO cash_deposit_receive_details SET cash_deposit_recv_id='".$cash_depositreceive_id."',
									  emp_code 			='".$cash_depositreceive_emp_code."',
									  trans_type 		='".$cash_depositreceive_trans_type."',
									  date 				='".$cash_depositreceive_details_date."',
									  amount 			='".$cash_depositreceive_amount."',
									  remarks			='".$cash_depositreceive_remarks."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertcashdeposit))
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
			}					  
   }// End of for loop
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