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
$body_xml=str_replace("'",'"',$body);
	/*$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);*/
//$body=str_replace("'",'"',$body);

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><call_duration><call_duration_details><transaction_id><![CDATA[OE008920170710162032]]></transaction_id><customer_code><![CDATA[C/0000085]]></customer_code><callduration><![CDATA[00:01:08]]></callduration></call_duration_details></call_duration></root>";*/

$transaction_id = "*ROOT*CALL_DURATION*CALL_DURATION_DETAILS*TRANSACTION_ID";
$customer_code = "*ROOT*CALL_DURATION*CALL_DURATION_DETAILS*CUSTOMER_CODE";
$callduration ="*ROOT*CALL_DURATION*CALL_DURATION_DETAILS*CALLDURATION";

$call_duration_array = array();
$call_duration_trans_id_array=array();
$counter = 0;

class xml_call_duration{
	var $transaction_id,$customer_code,$callduration;
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
    global $current_tag,$call_duration_array,$counter,$transaction_id,$customer_code,$callduration;
	if(substr($current_tag,0,19)=='*ROOT*CALL_DURATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $transaction_id:
				$call_duration_array[$counter] = new xml_call_duration();
				$call_duration_array[$counter]->transaction_id = $data;
				break;
			case $customer_code:
				$call_duration_array[$counter]->customer_code = $data;
				break;
			case $callduration:
				$call_duration_array[$counter]->callduration = $data;
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
/* -------------------------------------------------------START QUERY FOR Call Duration-----------------------------------------------------------------------*/
if(count($call_duration_array)>0)
{
	for($x=0;$x<count($call_duration_array);$x++){
		$transaction_id=$call_duration_array[$x]->transaction_id;
		$customer_code=$call_duration_array[$x]->customer_code;
		$callduration=$call_duration_array[$x]->callduration;
		
		//For checking that trans id exist or not for call duration
		$sqlchk="SELECT * FROM call_duration WHERE 	transaction_id='".$transaction_id."'";
		$reschk = mysqli_query($link,$sqlchk) or die(mysqli_error()." Error in check  callduration: ".$sqlchk); 
		$rowchk = mysqli_fetch_assoc($reschk);
		$countchk=mysqli_num_rows($reschk);
		
		//For update the call duration table for existing trans id 
		if($countchk>0)
		{
			$sqlupdatecallduration="UPDATE call_duration SET customer_code='".$customer_code."' 
									WHERE transaction_id='".$transaction_id."'";
			$rsupdatecallduration=mysqli_query($link,$sqlupdatecallduration) or die(mysqli_error()." Error in update call duration: ".$sqlupdatecallduration);
			if($rsupdatecallduration)
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
			$sqlinsertcallduration="INSERT INTO call_duration SET transaction_id='".$transaction_id."',
								  	customer_code 					='".$customer_code."',
								  	call_duration 					='".$callduration."'";
			if(mysqli_query($link,$sqlinsertcallduration))
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
 /* --------------------END QUERY FOR Call Duration--------------------------------------------------------------------------------------------------------*/
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