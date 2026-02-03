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
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><audit>
<location><emp_code><![CDATA[100002157]]></emp_code><trans_id><![CDATA[S10000215720131004112118]]></trans_id><latt><![CDATA[22.5643]]></latt><longi><![CDATA[88.3568]]></longi><date><![CDATA[2013-10-04 11:22:50]]></date></location>
<audit_data><stock_audit_transaction><stk_counting_trans_id>S10000215720131004112118</stk_counting_trans_id><prod_code>589926</prod_code>
<rds_stock_qty>2</rds_stock_qty><current_rds_stock_qty>6</current_rds_stock_qty><remarks>test audit</remarks><rds_code>4636</rds_code></stock_audit_transaction><stock_audit_transaction><stk_counting_trans_id>S10000215720131004112118</stk_counting_trans_id><prod_code>561928</prod_code><rds_stock_qty>12</rds_stock_qty><current_rds_stock_qty>13</current_rds_stock_qty><remarks>test audit</remarks><rds_code>4636</rds_code></stock_audit_transaction></audit_data></audit></root>";

$body="<?xml version='1.0' encoding='UTF-8'?><root><stock_audit><location><emp_code><![CDATA[C0007]]></emp_code><trans_id><![CDATA[SC000720140630092551]]></trans_id><latt><![CDATA[22.5643635]]></latt><longi><![CDATA[88.356867]]></longi><date><![CDATA[2014-06-30 09:26:49]]></date></location><stock_audit_details><transaction_id><![CDATA[SC000720140630092551]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[15166]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[27]]></quantity></stock_audit_details><stock_audit_details><transaction_id><![CDATA[SC000720140630092551]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[15985]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[67]]></quantity></stock_audit_details></stock_audit></root>";*/

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><stock_audit><location><emp_code><![CDATA[C0007]]></emp_code><trans_id><![CDATA[SC000720140701080517]]></trans_id><latt><![CDATA[22.5643639]]></latt><longi><![CDATA[88.3567821]]></longi><date><![CDATA[2014-07-01 08:05:17]]></date></location><stock_audit_details><transaction_id><![CDATA[SC000720140701080517]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[15166]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[25]]></quantity></stock_audit_details><stock_audit_details><transaction_id><![CDATA[SC000720140701080517]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[15985]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[45]]></quantity></stock_audit_details><stock_audit_details><transaction_id><![CDATA[SC000720140701080517]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[16210]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[65]]></quantity></stock_audit_details></stock_audit><stock_audit><location><emp_code><![CDATA[C0007]]></emp_code><trans_id><![CDATA[SC000720140701081542]]></trans_id><latt><![CDATA[22.5643009]]></latt><longi><![CDATA[88.356766]]></longi><date><![CDATA[2014-07-01 08:15:42]]></date></location><stock_audit_details><transaction_id><![CDATA[SC000720140701081542]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[15167]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[25]]></quantity></stock_audit_details><stock_audit_details><transaction_id><![CDATA[SC000720140701081542]]></transaction_id><customer_code><![CDATA[C/0000196]]></customer_code><product_code><![CDATA[16207]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><remarks><![CDATA[]]></remarks><quantity><![CDATA[65]]></quantity></stock_audit_details></stock_audit></root>";*/

//echo "RESPONSE OF $nick_name is --------------------------\n".$body;
//exit();
$audit_emp_code="*ROOT*STOCK_AUDIT*LOCATION*EMP_CODE";
$audit_trans_id = "*ROOT*STOCK_AUDIT*LOCATION*TRANS_ID";
$audit_latt = "*ROOT*STOCK_AUDIT*LOCATION*LATT";
$audit_longi = "*ROOT*STOCK_AUDIT*LOCATION*LONGI";
$audit_trans_date="*ROOT*STOCK_AUDIT*LOCATION*DATE";
$stockaudit_trans_id = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*TRANSACTION_ID";
$stockaudit_customer_code = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*CUSTOMER_CODE";
$stockaudit_product_code = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*PRODUCT_CODE";
$stockaudit_qty = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*QUANTITY";
$stockaudit_prod_mrp = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*PROD_MRP";
$stockaudit_remarks = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*REMARKS";

$audit_array=array();
$stock_audit_array=array();

$counter = 0;
$counteraudit=0;
$counterstockaudit=0;

class xml_audit{
	var $audit_emp_code,$audit_trans_id,$audit_latt,$audit_longi,$audit_trans_date;	
}
class xml_stock_audit{
	var $stockaudit_trans_id,$stockaudit_customer_code,$stockaudit_product_code,$stockaudit_qty,$stockaudit_prod_mrp,$stockaudit_remarks;
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
    global $current_tag,$counter,$counteraudit,$counterstockaudit,$audit_array,$stock_audit_array,
	$audit_emp_code,$audit_trans_id,$audit_latt,$audit_longi,$audit_trans_date,$stockaudit_trans_id,$stockaudit_customer_code,$stockaudit_product_code,$stockaudit_qty,$stockaudit_prod_mrp,$stockaudit_remarks;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,17)=='*ROOT*STOCK_AUDIT')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $audit_emp_code:
				$audit_array[$counteraudit] = new xml_audit();
				$audit_array[$counteraudit]->audit_emp_code = $data;
				break;
			case $audit_trans_id:
				$audit_array[$counteraudit]->audit_trans_id = $data;
				break;
			case $audit_latt:
				$audit_array[$counteraudit]->audit_latt = $data;
				break;
			case $audit_longi:
				$audit_array[$counteraudit]->audit_longi = $data;
				break;
			case $audit_trans_date:
				$audit_array[$counteraudit]->audit_trans_date = $data;
				$counteraudit++;
				break;
		}
	}
	if(substr($current_tag,0,37)=='*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS')
		{
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $stockaudit_trans_id:
					$stock_audit_array[$counterstockaudit] = new xml_stock_audit();
					$stock_audit_array[$counterstockaudit]->stockaudit_trans_id = $data;
					break;
				case $stockaudit_customer_code:
					$stock_audit_array[$counterstockaudit]->stockaudit_customer_code = $data;
					break;
				case $stockaudit_product_code:
					$stock_audit_array[$counterstockaudit]->stockaudit_product_code = $data;
					break;
				case $stockaudit_prod_mrp:
					$stock_audit_array[$counterstockaudit]->stockaudit_prod_mrp = $data;
					break;	
				case $stockaudit_remarks:
					 $stock_audit_array[$counterstockaudit]->stockaudit_remarks = $data;
					 break;	
				case $stockaudit_qty:
					$stock_audit_array[$counterstockaudit]->stockaudit_qty = $data;
					$counterstockaudit++;
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
/* -----------------------------------------------START QUERY FOR STOCK AUDIT--------------------------------------------------------------------------*/
$audit_array_receipt_id=array();
$audit_array_emp_code=array();
$audit_array_datetime=array();
$audit_array_latt=array();
$audit_array_longi=array();
//echo count($mt_array);
if(count($audit_array)>0)
{
	for($x=0;$x<count($audit_array);$x++){
		$audit_emp_code=$audit_array[$x]->audit_emp_code;
		$audit_trans_id=$audit_array[$x]->audit_trans_id;
		$audit_latt=$audit_array[$x]->audit_latt;
		$audit_longi=$audit_array[$x]->audit_longi;
		$audit_trans_date=$audit_array[$x]->audit_trans_date;
		
		//For checking that trans id exist or not for mt
		$sqlchkauditlocation="SELECT trans_id FROM location WHERE trans_id='".$audit_trans_id."'";
		$reschkauditlocation = mysqli_query($link,$sqlchkauditlocation) or die(mysqli_error()." Error in check audit location: ".$sqlchkauditlocation); 
		$countchkauditlocation=mysqli_num_rows($reschkauditlocation);
		
		//For update the location table for existing trans id for mt
		if($countchkauditlocation>0)
		{
			//$audit_trans_id_chk=substr($audit_trans_id,1,19);
			if(!in_array($audit_trans_id,$audit_array_receipt_id))
			{
				array_push($audit_array_receipt_id,$audit_trans_id);
			}
			$sqlupdateauditlocation="UPDATE location SET emp_code='".$audit_emp_code."',
									latt='".$audit_latt."',
									longi='".$audit_longi."'
									WHERE trans_id='".$audit_trans_id."'";
			$rsupdateauditlocation=mysqli_query($link,$sqlupdateauditlocation) or die(mysqli_error()." Error in update audit location: ".$sqlupdateauditlocation);
			if($rsupdateauditlocation)
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
			$sqlinsertauditlocation="INSERT INTO location SET emp_code='".$audit_emp_code."',
								  trans_id='".$audit_trans_id."',
								  latt='".$audit_latt."',
								  longi='".$audit_longi."',
								  date='".$audit_trans_date."',
								  updatetime='".$location_date."'";
			if(mysqli_query($link,$sqlinsertauditlocation))
			{
				$flag=5;
			}
			else
			{
				mysqli_query($link,"ROLLBACK");
				echo $flag=0;
				return;
			}
			array_push($audit_array_emp_code,$audit_emp_code);
			array_push($audit_array_datetime,$audit_trans_date);
			array_push($audit_array_receipt_id,$audit_trans_id);
			array_push($audit_array_latt,$audit_latt);
			array_push($audit_array_longi,$audit_longi);
		}
	}// End for loop
	$stockaudit_customer_array_mailbody=array();
	$stockaudit_transid_array_mailbody=array();
	$stockaudit_instruction_array_mailbody=array();
	//For Insert into the audit transaction table for new trans id
		if(count($stock_audit_array)>0)
		{
			for($i=0;$i<count($stock_audit_array);$i++){
				
				$stockaudit_trans_id=$stock_audit_array[$i]->stockaudit_trans_id;
				$stockaudit_customer_code=$stock_audit_array[$i]->stockaudit_customer_code;
				$stockaudit_product_code=$stock_audit_array[$i]->stockaudit_product_code;
				$stockaudit_prod_mrp=$stock_audit_array[$i]->stockaudit_prod_mrp;
				$stockaudit_remarks=$stock_audit_array[$i]->stockaudit_remarks;
				$stockaudit_qty=$stock_audit_array[$i]->stockaudit_qty;
					
					if(!in_array($stockaudit_trans_id,$stockaudit_transid_array_mailbody))
					{
						array_push($stockaudit_customer_array_mailbody,$stockaudit_customer_code);
						array_push($stockaudit_transid_array_mailbody,$stockaudit_trans_id);
						array_push($stockaudit_instruction_array_mailbody,$stockaudit_remarks);
					}
					//check for provided transaction id exist or not in location table through the particular array audit_array_receipt_id
					
					if(!in_array($stockaudit_trans_id,$audit_array_receipt_id))
					{
						$sqlinsertstockaudit="INSERT INTO stock_audit SET transaction_id ='".$stockaudit_trans_id."',
											  customer_code 		='".$stockaudit_customer_code."',
											  product_code 			='".$stockaudit_product_code."',
											  quantity 				='".$stockaudit_qty."',
											  product_mrp 			='".$stockaudit_prod_mrp."',
											  remarks 				='".addslashes($stockaudit_remarks)."'";											  
						if(mysqli_query($link,$sqlinsertstockaudit))
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
						$sqlselectstockaudit="SELECT transaction_id FROM stock_audit 
											  WHERE transaction_id ='".$stockaudit_trans_id."' AND
											  customer_code ='".$stockaudit_customer_code."' AND product_code ='".$stockaudit_product_code."'
											  AND quantity ='".$stockaudit_qty."' AND product_mrp='".$stockaudit_prod_mrp."'";
						$resselectstockaudit = mysqli_query($link,$sqlselectstockaudit) or die(mysqli_error()." Error in chk trans id for stock audit: ".$sqlselectstockaudit); 
						$countselectstockaudit=mysqli_num_rows($resselectstockaudit);
						
							if($countselectstockaudit==0)
							{
								$sqlinsertstockaudit="INSERT INTO stock_audit SET transaction_id ='".$stockaudit_trans_id."',
													  customer_code 		='".$stockaudit_customer_code."',
													  product_code 			='".$stockaudit_product_code."',
													  quantity 				='".$stockaudit_qty."',
													  product_mrp 			='".$stockaudit_prod_mrp."',
													  remarks 				='".addslashes($stockaudit_remarks)."'";
								if(mysqli_query($link,$sqlinsertstockaudit))
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
					  if($flag==5)
					  {
						  	//For checking the prevous stock counting table
							$sqlselectprevstock="SELECT visit_1,visit_2,visit_3 FROM prev_stock_counting_master 
												WHERE customer_code='".$stockaudit_customer_code."' AND product_code='".$stockaudit_product_code."'";
							$rsselectprevstock=mysqli_query($link,$sqlselectprevstock);
							$countselectprevstock=mysqli_num_rows($rsselectprevstock);
							$rowselectprevstock=mysqli_fetch_assoc($rsselectprevstock);
							if($countselectprevstock>0)
							{
								$visit_2=$rowselectprevstock['visit_2'];
								$visit2_date=$rowselectprevstock['visit2_date'];
								$visit_3=$rowselectprevstock['visit_3'];
								$visit3_date=$rowselectprevstock['visit3_date'];
								$sqlupdateprevstock="UPDATE prev_stock_counting_master SET 
													visit_1 ='".$visit_2."',
													visit_2 ='".$visit_3."',
													visit_3 ='".$stockaudit_qty."',
													visit1_date='".$visit2_date."',
													visit2_date='".$visit3_date."',
													visit3_date=CURRENT_TIMESTAMP,
													download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$stockaudit_customer_code."' 
													AND product_code='".$stockaudit_product_code."'";
								if(mysqli_query($link,$sqlupdateprevstock))
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
								$sqlinsertprevstock="INSERT INTO prev_stock_counting_master SET 
													visit_1 ='0',
													visit_2 ='0',
													visit_3 ='".$stockaudit_qty."',
													visit3_date=CURRENT_TIMESTAMP(),
													customer_code='".$stockaudit_customer_code."',
													product_code='".$stockaudit_product_code."',
													download_time=CURRENT_TIMESTAMP()";
								if(mysqli_query($link,$sqlinsertprevstock))
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
							//End of checking the prevous stock counting table
							if(no_of_filter==1){
								$sqlproductdetails="SELECT prod_desc FROM product_master WHERE prod_code='".$stockaudit_product_code."'";
							}
							if(no_of_filter==2){
								$sqlproductdetails="SELECT PGM.product_group_name,PM.prod_desc FROM product_master PM,product_group_master PGM 
													WHERE PM.product_group_code=PGM.product_group_code AND PM.prod_code='".$stockaudit_product_code."'";
							}
							if(no_of_filter==3){
								$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PM.prod_desc FROM 
													product_master PM,product_group_master PGM,product_sub_group_master PSGM
													WHERE PM.product_group_code=PGM.product_group_code 
													AND PM.product_sub_group_code=PSGM.product_sub_group_code 
													AND PM.prod_code='".$stockaudit_product_code."'";
							}
							if(no_of_filter==4){
								$sqlproductdetails="SELECT PGM.product_group_name,PSGM.product_sub_group_name,PBM.product_brand_name,PM.prod_desc
													FROM  product_master PM,product_group_master PGM,product_sub_group_master PSGM,product_brand_master PBM
													WHERE PM.product_group_code=PGM.product_group_code 
													AND PM.product_sub_group_code=PSGM.product_sub_group_code
													AND PM.product_brand_code=PBM.product_brand_code AND PM.prod_code='".$stockaudit_product_code."'";
							}
							$rsproductdetails=mysqli_query($link,$sqlproductdetails);
							$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
							$prod_desc=$rowproductdetails['prod_desc'];
							$product_group_name=$rowproductdetails['product_group_name'];
							$product_brand_name=$rowproductdetails['product_brand_name'];
							$product_sub_group_name=$rowproductdetails['product_sub_group_name'];
							
							$amount=($stockaudit_prod_mrp*$stockaudit_qty);
							//$amount=getAmount($basic_rate,$no_pack,$stockaudit_rds_stock_qty,$tax1_rate,$tax2_rate);
							
							if(no_of_filter==1){
							$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
											<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
							}
							if(no_of_filter==2){
								$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
							}
							if(no_of_filter==3){
								$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
							}
							if(no_of_filter==4){
								$product_details_TD="<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_group_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_sub_group_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$product_brand_name."</span>&nbsp;</td>
												<td style='width:300px;text-align:left;min-height:21px;background-color:white'>
												<span style='font-family:Arial CE;font-size:10pt'>".$prod_desc."</span>&nbsp;</td>	";
							}
						  
						  ${a.$stockaudit_trans_id} .="
								<tr>".$product_details_TD."
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".round($stockaudit_qty,2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$stockaudit_prod_mrp."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".number_format($amount,2)."</span>&nbsp;</td>
								</tr>";
					  }
			}//End for loop
		}
		//End Insert into the stock audit transaction table for new trans id
		
	if($flag==5)// Start of if of sending mail
	{
		for($y=0;$y<count($audit_array);$y++){ // strat of for loop of sending mail
				$audit_receipt_id=$audit_array_receipt_id[$y];
				$mail_customer_code=$stockaudit_customer_array_mailbody[$y];
				$mail_d_instruction=$stockaudit_instruction_array_mailbody[$y];
				$mail_emp_code=$audit_array_emp_code[$y];
				$mail_date_time=$audit_array_datetime[$y];
				$mail_latt=$audit_array_latt[$y];
				$mail_longi=$audit_array_longi[$y];
				
				$sqlempname="SELECT emp_name,reporting_to FROM employee_master WHERE emp_code='".$mail_emp_code."'";
				$rsempname=mysqli_query($link,$sqlempname);
				$rowempname=mysqli_fetch_assoc($rsempname);
				$emp_name=$rowempname['emp_name'];
				
				$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$mail_customer_code."'";
				$rscustomername=mysqli_query($link,$sqlcustomername);
				$rowcustomername=mysqli_fetch_assoc($rscustomername);
				$customer_name=$rowcustomername['customer_name'];
				$addressaudit=getReverseGeo($mail_latt,$mail_longi);
				
				if(substr($audit_receipt_id,0,1)=='N')
				{
					$nostockauditemailsubj="No stock audit of ".$customer_name. " on ".date('d-m-Y H:i:s',strtotime($mail_date_time)).' - '.$emp_name;
					$nostockauditemailbody = "<html><head><title>No Stock Audit</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name." at ".$addressaudit.". 
										No stock audit happened. </table><br><br>
										<b>Remarks: </b> ".strtoupper($mail_d_instruction)."
										<br><br><br>Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
			
					//if(mail(AUDITEMAILRECIPENTS, $nostockauditemailsubj, $nostockauditemailbody, $headers))
					//if(mail($stock_audit_email, $nostockauditemailsubj, $nostockauditemailbody, $headers,'-facedns@coral.in'))
					if(mail(AUDITEMAILRECIPENTS, $nostockauditemailsubj, $nostockauditemailbody, $headers,'-facedns@coral.in'))
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
					$stockauditmailsubj="Stock audit of ".$customer_name." on ".date('d-m-Y H:i:s',strtotime($mail_date_time)).' - '.$emp_name;
					$stockauditmailbody = "<html><head><title>Stock Audit Details</title></head>
								<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b><br><br><br><table border=1 style=background-color:AliceBlue>
								<tr>
								<th style='width:260px;min-height:21px;text-align:center'>
								<strong><span style='font-size:10pt;font-family:Arial 
								CE'>Customer Name</span></strong></th>
								<th style='width:160px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
								CE'>Date & Time</span></strong></th>
								</tr><tr><td style='width:260px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>
								".$customer_name."</span>&nbsp;</td><td style='width:165px;text-align:right;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".date('d-m-Y H:i:s',strtotime($mail_date_time))."</span>&nbsp;</td>
								</tr></table>
									<br /><table border=1 style=background-color:AliceBlue>
										<tr>";
										if(no_of_filter==1){
												$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
															<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
										}
										if(no_of_filter==2){
											$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
										}
										if(no_of_filter==3){
											$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
										}
										if(no_of_filter==4){
											$product_details_TH="<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col1."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col2."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col3."</span></strong></th>
														<th style='width:60px;min-height:21px;text-align:center'><strong>
														<span style='font-size:10pt;font-family:Arial CE'>".col4."</span></strong></th>";
										}
										$stockauditmailbody.=$product_details_TH."<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>MRP</span></strong></th>
							<th style='width:60px;min-height:21px;text-align:center'><strong>
							<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th></tr>
					".${a.$stockaudit_transid_array_mailbody[$y]}."</table>
					<br /><br /><b>Remarks: </b> ".strtoupper($mail_d_instruction)." <br /><br />Powered By aceDNS<br></body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n" .
								'X-Mailer: PHP/' . phpversion();
					//echo 	$stockauditmailbody;		
					//if(mail(AUDITEMAILRECIPENTS, $stockauditmailsubj, $stockauditmailbody, $headers))
					//if(mail($stock_audit_email, $stockauditmailsubj, $stockauditmailbody, $headers,'-facedns@coral.in'))
					if(mail(AUDITEMAILRECIPENTS, $stockauditmailsubj, $stockauditmailbody, $headers,'-facedns@coral.in'))
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
		}//End of for loop of sending mail
	}// End of if of sending mail

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
?>
