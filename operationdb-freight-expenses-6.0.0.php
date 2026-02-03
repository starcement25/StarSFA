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

$feight_expense_location_emp_code="*ROOT*FREIGHT_EXPENSE*LOCATION*EMP_CODE";
$feight_expense_location_trans_id = "*ROOT*FREIGHT_EXPENSE*LOCATION*TRANS_ID";
$feight_expense_latt = "*ROOT*FREIGHT_EXPENSE*LOCATION*LATT";
$feight_expense_longi = "*ROOT*FREIGHT_EXPENSE*LOCATION*LONGI";
$feight_expense_date="*ROOT*FREIGHT_EXPENSE*LOCATION*DATE";
$feight_expense_trans_id = "*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*FREIGHT_EXP_TRANS_ID";
$feight_expense_emp_code = "*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*EMP_CODE";
$feight_expense_trans_type ="*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*TRANS_TYPE";
$feight_expense_details_date ="*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*DATE";
$feight_expense_remarks ="*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*REMARKS";
$feight_expense_amount ="*ROOT*FREIGHT_EXPENSE*FREIGHT_EXPENSE_DETAILS*AMOUNT";


$feight_expense_array = array();
$feight_expense_trans_id_array=array();
$feight_expense_emp_code_array=array();
$counter = 0;

class xml_feight_expense{
	var $feight_expense_location_emp_code,$feight_expense_location_trans_id,$feight_expense_latt,$feight_expense_longi,$feight_expense_date,$feight_expense_trans_id,$feight_expense_emp_code,$feight_expense_trans_type,$feight_expense_details_date,$feight_expense_amount,$feight_expense_remarks;
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
    global $feight_expense_location_emp_code,$feight_expense_location_trans_id,$feight_expense_latt,$feight_expense_longi,$feight_expense_date,$current_tag,$feight_expense_trans_id,$feight_expense_emp_code,$feight_expense_trans_type,$feight_expense_details_date,$feight_expense_amount,$feight_expense_remarks,$feight_expense_array,$counter;
	if(substr($current_tag,0,21)=='*ROOT*FREIGHT_EXPENSE')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $feight_expense_location_emp_code:
				$feight_expense_array[$counter] = new xml_feight_expense();
				$feight_expense_array[$counter]->feight_expense_location_emp_code = $data;
				break;
			case $feight_expense_location_trans_id:
				$feight_expense_array[$counter]->feight_expense_location_trans_id = $data;
				break;
			case $feight_expense_latt:
				$feight_expense_array[$counter]->feight_expense_latt = $data;
				break;
			case $feight_expense_longi:
				$feight_expense_array[$counter]->feight_expense_longi = $data;
				break;
			case $feight_expense_date:
				$feight_expense_array[$counter]->feight_expense_date = $data;
				break;		
			case $feight_expense_trans_id:
				$feight_expense_array[$counter]->feight_expense_trans_id = $data;
				break;
			case $feight_expense_emp_code:
				$feight_expense_array[$counter]->feight_expense_emp_code = $data;
				break;
			case $feight_expense_trans_type:
				$feight_expense_array[$counter]->feight_expense_trans_type = $data;
				break;
			case $feight_expense_details_date:
				$feight_expense_array[$counter]->feight_expense_details_date = $data;
				break;
			case $feight_expense_remarks:
				$feight_expense_array[$counter]->feight_expense_remarks = $data;
				break;
			case $feight_expense_amount:
				$feight_expense_array[$counter]->feight_expense_amount = $data;
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
/* -----------------------------------------------------START QUERY FOR FREIGHT EXPENSE----------------------------------------------------------------------*/
if(count($feight_expense_array)>0)
{
	//$count=1;
	for($x=0;$x<count($feight_expense_array);$x++){
		$feight_expense_location_emp_code=$feight_expense_array[$x]->feight_expense_location_emp_code;
		$feight_expense_location_trans_id=$feight_expense_array[$x]->feight_expense_location_trans_id;
		$feight_expense_latt=$feight_expense_array[$x]->feight_expense_latt;
		$feight_expense_longi=$feight_expense_array[$x]->feight_expense_longi;
		$feight_expense_date=$feight_expense_array[$x]->feight_expense_date;
		$feight_expense_trans_id=$feight_expense_array[$x]->feight_expense_trans_id;
		$feight_expense_emp_code=$feight_expense_array[$x]->feight_expense_emp_code;
		$feight_expense_trans_type=$feight_expense_array[$x]->feight_expense_trans_type;
		$feight_expense_details_date=$feight_expense_array[$x]->feight_expense_details_date;
		$feight_expense_amount=$feight_expense_array[$x]->feight_expense_amount;
		$feight_expense_remarks=$feight_expense_array[$x]->feight_expense_remarks;
		
		//For checking that trans id exist or not for feight and expense
		$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$feight_expense_location_trans_id."'";
		$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check feight expense location: ".$sqlchkorlocation); 
		$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
		$countchkorlocation=mysqli_num_rows($reschkorlocation);
		
		//For update the location table for existing trans id for feight and expense
		if($countchkorlocation>0)
		{
			if(!in_array($feight_expense_location_trans_id,$feight_expense_trans_id_array))
			{
				array_push($feight_expense_trans_id_array,$feight_expense_location_trans_id);
			}
			$sqlupdateorlocation="UPDATE location SET emp_code='".$feight_expense_location_emp_code."',
									latt='".$feight_expense_latt."',
									longi='".$feight_expense_longi."'
									WHERE trans_id='".$feight_expense_location_trans_id."'";
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
			$feight_expense_details_date_db=date('Y-m-d',strtotime($feight_expense_details_date));
			
			//For Insert into the location table for new trans id regarding feight expenses
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$feight_expense_location_emp_code."',
									trans_id='".$feight_expense_location_trans_id."',
									latt='".$feight_expense_latt."',
									longi='".$feight_expense_longi."',
									date='".$feight_expense_date."',
									updatetime='".$location_date."'"; 
			
			//For Insert into the feighting_expenses table for new trans id
			$sqlinsertfeightexpense="INSERT INTO freight_expenses SET freight_exp_trans_id='".$feight_expense_trans_id."',
									  emp_code 			='".$feight_expense_emp_code."',
									  trans_type 		='".$feight_expense_trans_type."',
									  date 				='".$feight_expense_details_date_db."',
									  amount 			='".$feight_expense_amount."',
									  remarks			='".$feight_expense_remarks."'";
			if(mysqli_query($link,$sqlinsertorlocation) && mysqli_query($link,$sqlinsertfeightexpense))
				{
					$flag=5;
				}
				else
				{
					mysqli_query($link,"ROLLBACK");
					echo $flag=0;
					return;
				}
				$sqlrds="SELECT rds_code,rds_name FROM rds_master WHERE emp_code='".$feight_expense_emp_code."'";
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
				}
				
			}					  
   }// End of for loop
	//if($flag==5){}
}
 /* --------------------END QUERY FOR FREIGHT EXPENSE-------------------------------------------------------------------------------------------------------*/
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