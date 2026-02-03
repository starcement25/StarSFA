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
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><stockist_visit><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[SVE000120220518123430]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2022-05-18 12:34:30]]></date></location><stockist_visit_details><VISIT_TRANS_ID><![CDATA[SVE000120220518123430]]></VISIT_TRANS_ID><stockist_code><![CDATA[C/0000035]]></stockist_code><customer_code><![CDATA[C/0000001]]></customer_code><sale><![CDATA[3]]></sale><folder><![CDATA[6]]></folder></stockist_visit_details><stockist_visit_details><VISIT_TRANS_ID><![CDATA[SVE000120220518123430]]></VISIT_TRANS_ID><stockist_code><![CDATA[C/0000035]]></stockist_code><customer_code><![CDATA[C/0000002]]></customer_code><sale><![CDATA[63]]></sale><folder><![CDATA[3]]></folder></stockist_visit_details><stockist_visit_details><VISIT_TRANS_ID><![CDATA[SVE000120220518123430]]></VISIT_TRANS_ID><stockist_code><![CDATA[C/0000035]]></stockist_code><customer_code><![CDATA[C/0000007]]></customer_code><sale><![CDATA[52]]></sale><folder><![CDATA[66]]></folder></stockist_visit_details></stockist_visit></root>";*/

$stockist_visit_emp_code="*ROOT*STOCKIST_VISIT*LOCATION*EMP_CODE";
$stockist_visit_trans_id = "*ROOT*STOCKIST_VISIT*LOCATION*TRANS_ID";
$stockist_visit_latt = "*ROOT*STOCKIST_VISIT*LOCATION*LATT";
$stockist_visit_longi = "*ROOT*STOCKIST_VISIT*LOCATION*LONGI";
$stockist_visit_trans_date="*ROOT*STOCKIST_VISIT*LOCATION*DATE";
$visit_trans_id = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*VISIT_TRANS_ID";
$stockist_code = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*STOCKIST_CODE";
$customer_code = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*CUSTOMER_CODE";
$sale = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*SALE";
$folder = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*FOLDER";
$prod_code = "*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS*PROD_CODE";

$stockist_visit_array=array();
$stockist_visit_details_array=array();

$counter = 0;
$countervisit=0;

class xml_stockist_visit{
	var $stockist_visit_emp_code,$stockist_visit_trans_id,$stockist_visit_latt,$stockist_visit_longi,$stockist_visit_trans_date;	
}
class xml_stockist_visit_details{
	var $visit_trans_id,$stockist_code,$customer_code,$sale,$folder,$prod_code;
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
    global $current_tag,$counter,$countervisit,$stockist_visit_array,$stockist_visit_details_array,
$stockist_visit_emp_code,$stockist_visit_trans_id,$stockist_visit_latt,$stockist_visit_longi,$stockist_visit_trans_date,$visit_trans_id,$stockist_code,$customer_code,$sale,$folder,$prod_code;	

//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,20)=='*ROOT*STOCKIST_VISIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $stockist_visit_emp_code:
				$stockist_visit_array[$counter] = new xml_stockist_visit();
				$stockist_visit_array[$counter]->stockist_visit_emp_code = $data;
				break;
			case $stockist_visit_trans_id:
				$stockist_visit_array[$counter]->stockist_visit_trans_id = $data;
				break;
			case $stockist_visit_latt:
				$stockist_visit_array[$counter]->stockist_visit_latt = $data;
				break;
			case $stockist_visit_longi:
				$stockist_visit_array[$counter]->stockist_visit_longi = $data;
				break;
			case $stockist_visit_trans_date:
				$stockist_visit_array[$counter]->stockist_visit_trans_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,43)=='*ROOT*STOCKIST_VISIT*STOCKIST_VISIT_DETAILS')
		{
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $visit_trans_id:
					$stockist_visit_details_array[$countervisit] = new xml_stockist_visit_details();
					$stockist_visit_details_array[$countervisit]->visit_trans_id = $data;
					break;
				case $stockist_code:
					$stockist_visit_details_array[$countervisit]->stockist_code = $data;
					break;
				case $customer_code:
					$stockist_visit_details_array[$countervisit]->customer_code = $data;
					break;
				case $sale:
					$stockist_visit_details_array[$countervisit]->sale = $data;
					break;
				case $folder:
					$stockist_visit_details_array[$countervisit]->folder = $data;
					break;
				case $prod_code:
					$stockist_visit_details_array[$countervisit]->prod_code = $data;
					$countervisit++;
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
$stockist_visit_trans_id_exists=array();
if(count($stockist_visit_array)>0)
{
	for($x=0;$x<count($stockist_visit_array);$x++){

		$stockist_visit_emp_code=$stockist_visit_array[$x]->stockist_visit_emp_code;
		$stockist_visit_trans_id=$stockist_visit_array[$x]->stockist_visit_trans_id;
		$stockist_visit_latt=$stockist_visit_array[$x]->stockist_visit_latt;
		$stockist_visit_longi=$stockist_visit_array[$x]->stockist_visit_longi;
		$stockist_visit_trans_date=$stockist_visit_array[$x]->stockist_visit_trans_date;
		
		//For checking that trans id exist or not for mt
		$sqlchkstockistvisitlocation="SELECT trans_id FROM location WHERE trans_id='".$stockist_visit_trans_id."'";
		$reschkstockistvisitlocation = mysqli_query($link,$sqlchkstockistvisitlocation) or die(mysqli_error()." Error in check stockist visit location: ".$sqlchkstockistvisitlocation); 
		$countchkstockistvisitlocation=mysqli_num_rows($reschkstockistvisitlocation);
		
		//For update the location table for existing trans id for bp
		if($countchkstockistvisitlocation>0)
		{
			//$audit_trans_id_chk=substr($audit_trans_id,1,19);
			if(!in_array($stockist_visit_trans_id,$stockist_visit_trans_id_exists))
			{
				array_push($stockist_visit_trans_id_exists,$stockist_visit_trans_id);
			}
			$sqlupdatestockistvisit="UPDATE location SET emp_code='".$stockist_visit_emp_code."',
									latt='".$stockist_visit_latt."',
									longi='".$stockist_visit_longi."'
									WHERE trans_id='".$stockist_visit_trans_id."'";
			$rsupdatestockistvisit=mysqli_query($link,$sqlupdatestockistvisit) or die(mysqli_error()." Error in update gift location: ".$sqlupdatestockistvisit);
			if($rsupdatestockistvisit)
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
			$sqlinsertstockistvisit="INSERT INTO location SET emp_code='".$stockist_visit_emp_code."',
								  trans_id='".$stockist_visit_trans_id."',
								  latt='".$stockist_visit_latt."',
								  longi='".$stockist_visit_longi."',
								  date='".$stockist_visit_trans_date."',
								  updatetime='".$location_date."'";
			if(mysqli_query($link,$sqlinsertstockistvisit))
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
	//print_r($stockist_visit_trans_id_exists);
		if(count($stockist_visit_details_array)>0 && !in_array($stockist_visit_trans_id,$stockist_visit_trans_id_exists))
		{
			for($i=0;$i<count($stockist_visit_details_array);$i++){
				
				$visit_trans_id=$stockist_visit_details_array[$i]->visit_trans_id;
				$stockist_code=$stockist_visit_details_array[$i]->stockist_code;
				$customer_code=$stockist_visit_details_array[$i]->customer_code;
				$sale=$stockist_visit_details_array[$i]->sale;
				$folder=$stockist_visit_details_array[$i]->folder;
				$prod_code=$stockist_visit_details_array[$i]->prod_code;

				$sqlinsertstockistvisitdet="INSERT INTO stockist_visit SET visit_trans_id ='".$visit_trans_id."',
									  stockist_code 					='".$stockist_code."',
									  customer_code 					='".$customer_code."',
									  sale 								='".addslashes($sale)."',
									  folder 							='".addslashes($folder)."',
									   prod_code 						='".addslashes($prod_code)."'";											  
				if(mysqli_query($link,$sqlinsertstockistvisitdet))
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

 /* --------------------END QUERY FOR STOCKIST VISIT------------------------------------------------------------------------------------------------------------*/
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
