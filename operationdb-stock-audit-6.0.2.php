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

$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$result = mysqli_query($link,$sqlquery);
$countdatarefresh=mysqli_num_rows($result);

$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".$body_xml."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	


//echo "RESPONSE OF $nick_name is --------------------------\n".$body;
//exit();
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><stock_audit><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[SE000120191128141243]]></trans_id><latt><![CDATA[22.564316]]></latt><longi><![CDATA[88.3568708]]></longi><date><![CDATA[2019-11-28 14:12:43]]></date></location><stock_audit_details><transaction_id><![CDATA[SE000120191128141243]]></transaction_id><customer_code><![CDATA[C/0000099]]></customer_code><product_code><![CDATA[12018]]></product_code><prod_mrp><![CDATA[]]></prod_mrp><prod_details><![CDATA[]]></prod_details><remarks><![CDATA[]]></remarks><HINT_REMARKS><![CDATA[]]></HINT_REMARKS><mfd_date><![CDATA[abab]]></mfd_date><quantity><![CDATA[12]]></quantity></stock_audit_details></stock_audit></root>";*/

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
$stockaudit_hint_remarks = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*HINT_REMARKS";
$stockaudit_prod_details = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*PROD_DETAILS";
$stockaudit_mfd_date = "*ROOT*STOCK_AUDIT*STOCK_AUDIT_DETAILS*MFD_DATE";

$audit_array=array();
$stock_audit_array=array();

$counter = 0;
$counteraudit=0;
$counterstockaudit=0;

class xml_audit{
	var $audit_emp_code,$audit_trans_id,$audit_latt,$audit_longi,$audit_trans_date;	
}
class xml_stock_audit{
	var $stockaudit_trans_id,$stockaudit_customer_code,$stockaudit_product_code,$stockaudit_qty,$stockaudit_prod_mrp,$stockaudit_prod_details,
	$stockaudit_remarks,$stockaudit_hint_remarks,$stockaudit_mfd_date;
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
	$audit_emp_code,$audit_trans_id,$audit_latt,$audit_longi,$audit_trans_date,$stockaudit_trans_id,$stockaudit_customer_code,$stockaudit_product_code,$stockaudit_qty,$stockaudit_prod_mrp,$stockaudit_prod_details,$stockaudit_remarks,$stockaudit_hint_remarks,$stockaudit_mfd_date;
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
				case $stockaudit_prod_details:
					$stock_audit_array[$counterstockaudit]->stockaudit_prod_details = $data;
					break;
				case $stockaudit_remarks:
					 $stock_audit_array[$counterstockaudit]->stockaudit_remarks = $data;
					 break;	
				case $stockaudit_hint_remarks:
					 $stock_audit_array[$counterstockaudit]->stockaudit_hint_remarks = $data;
					 break;
				case $stockaudit_mfd_date:
					 $stock_audit_array[$counterstockaudit]->stockaudit_mfd_date = $data;
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
$audit_array_receipt_id_exists=array();
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
			if(!in_array($audit_trans_id,$audit_array_receipt_id_exists))
			{
				array_push($audit_array_receipt_id_exists,$audit_trans_id);
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
	$stockaudit_hint_remarks_array_mailbody=array();
	//For Insert into the audit transaction table for new trans id
		if(count($stock_audit_array)>0)
		{
			for($i=0;$i<count($stock_audit_array);$i++){
				$stockaudit_trans_id=$stock_audit_array[$i]->stockaudit_trans_id;
				$stockaudit_customer_code=$stock_audit_array[$i]->stockaudit_customer_code;
				$stockaudit_product_code=$stock_audit_array[$i]->stockaudit_product_code;
				$stockaudit_prod_mrp=$stock_audit_array[$i]->stockaudit_prod_mrp;
				$stockaudit_prod_details=$stock_audit_array[$i]->stockaudit_prod_details;
				$stockaudit_remarks=$stock_audit_array[$i]->stockaudit_remarks;
				$stockaudit_hint_remarks=$stock_audit_array[$i]->stockaudit_hint_remarks;
				$stockaudit_qty=$stock_audit_array[$i]->stockaudit_qty;
				$stockaudit_mfd_date=$stock_audit_array[$i]->stockaudit_mfd_date;
				
					if($i==0)
					{
						$sqlcustomertype="SELECT cust_type FROM customer_master WHERE customer_code='".$stockaudit_customer_code."'";
						$rscustomertype=mysqli_query($link,$sqlcustomertype);
						$rowcustomertype=mysqli_fetch_assoc($rscustomertype);
						$cust_type=$rowcustomertype['cust_type'];
					}
					if(!in_array($stockaudit_trans_id,$stockaudit_transid_array_mailbody))
					{
						array_push($stockaudit_customer_array_mailbody,$stockaudit_customer_code);
						array_push($stockaudit_transid_array_mailbody,$stockaudit_trans_id);
						array_push($stockaudit_instruction_array_mailbody,$stockaudit_remarks);
						array_push($stockaudit_hint_remarks_array_mailbody,$stockaudit_hint_remarks);
					}
					//check for provided transaction id exist or not in location table through the particular array audit_array_receipt_id
					if(stk_audit_mfd_date=='yes')
					{
						$sqlinsertcond=" manufacturing_date='".$stockaudit_mfd_date."',";
					}
					else $sqlinsertcond='';
						
					if(!in_array($stockaudit_trans_id,$audit_array_receipt_id_exists))
					{
						$sqlinsertstockaudit="INSERT INTO stock_audit SET transaction_id ='".$stockaudit_trans_id."',
											  customer_code 		='".$stockaudit_customer_code."',
											  product_code 			='".$stockaudit_product_code."',
											  quantity 				='".$stockaudit_qty."',
											  product_mrp 			='".$stockaudit_prod_mrp."',
											  product_details		='".$stockaudit_prod_details."',
											  hint_remarks			='".$stockaudit_hint_remarks."',".$sqlinsertcond."
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
					   //Start for STAR mis data details stock audit qty updation
					  	if($nick_name=='STAR')
						{
							$qty=$stockaudit_qty;
							$trans_type='S';
							$trans_sub_type='';
							update_transaction_STAR($emp_code,$stockaudit_trans_id,$qty,$trans_type,$trans_sub_type);
							
						}
					  //End for STAR mis data details stock audit qty updation
					}
					else
					{
						$sqlselectstockaudit="SELECT transaction_id FROM stock_audit 
											  WHERE transaction_id ='".$stockaudit_trans_id."' AND
											  customer_code ='".$stockaudit_customer_code."' AND product_code ='".$stockaudit_product_code."'
											  AND quantity ='".$stockaudit_qty."' AND product_mrp='".$stockaudit_prod_mrp."'";
						$resselectstockaudit = mysqli_query($link,$sqlselectstockaudit) or die(mysqli_error()." Error in chk trans id for stock audit: ".$sqlselectstockaudit); 
						$countselectstockaudit=mysqli_num_rows($resselectstockaudit);
						
							if(stk_audit_mfd_date=='yes')
							{
								$sqlinsertcond=" manufacturing_date='".$stockaudit_mfd_date."',";
							}
							else $sqlinsertcond='';
							if($countselectstockaudit==0)
							{
								$sqlinsertstockaudit="INSERT INTO stock_audit SET transaction_id ='".$stockaudit_trans_id."',
													  customer_code 		='".$stockaudit_customer_code."',
													  product_code 			='".$stockaudit_product_code."',
													  quantity 				='".$stockaudit_qty."',
													  product_mrp 			='".$stockaudit_prod_mrp."',
													  product_details		='".$stockaudit_prod_details."',
													   hint_remarks			='".$stockaudit_hint_remarks."',".$sqlinsertcond."
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
								//Start for STAR mis data details stock audit qty updation
								if($nick_name=='STAR')
								{
									$qty=$stockaudit_qty;
									$trans_type='S';
									$trans_sub_type='';
									update_transaction_STAR($emp_code,$stockaudit_trans_id,$qty,$trans_type,$trans_sub_type);
									
								}
							  //End for STAR mis data details stock audit qty updation
							}
					  }
					  if($flag==5)
					  {
						  //For checking the prevous stock counting table
						  if($cust_type != 'R')
						  {
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
													visit3_date='".$audit_trans_date."',
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
													visit1_date='0000-00-00 00:00:00',
													visit_2 ='0',
													visit2_date='0000-00-00 00:00:00',
													visit_3 ='".$stockaudit_qty."',
													visit3_date='".$audit_trans_date."',
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
							$sqlproductdetails="SELECT product_group_code FROM product_master WHERE prod_code='".$stockaudit_product_code."'";
							$rsproductdetails=mysqli_query($link,$sqlproductdetails);
							$rowproductdetails=mysqli_fetch_assoc($rsproductdetails);
							$product_group_code=$rowproductdetails['product_group_code'];
							if(strtoupper($nick_name)=='ARCHITA'){
							//For closing stock preperation
							$sqlchkcustomerstk="SELECT last_stk_audit_qty FROM customer_productwise_stock 
												WHERE customer_code='".$stockaudit_customer_code."' AND 	prod_code='".$stockaudit_product_code."'";
							$rschkcustomerstk=mysqli_query($link,$sqlchkcustomerstk);
							$countchkcustomerstk=mysqli_num_rows($rschkcustomerstk);
							$rowchkcustomerstk=mysqli_fetch_assoc($rschkcustomerstk);
							if($countchkcustomerstk>0)
							{
								$sqlupdateclstk="UPDATE customer_productwise_stock SET 
													last_stk_audit_qty ='".$stockaudit_qty."',
													last_stk_audit_date='".$audit_trans_date."',
													purchase_after_audit =0,
													purchase_date='0000-00-00 00:00:00',
													sale_after_audit =0,
													sale_date='0000-00-00 00:00:00',
													cl_stk=(($stockaudit_qty+purchase_after_audit)-sale_after_audit),
													download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$stockaudit_customer_code."' 
													AND prod_code='".$stockaudit_product_code."'";
								mysqli_query($link,$sqlupdateclstk);
							}
							else
							{
								$sqlinsertclstk="INSERT INTO customer_productwise_stock SET
												 customer_code='".$stockaudit_customer_code."',
												 prod_code='".$stockaudit_product_code."',
												  product_group_code='".$product_group_code."',
												 last_stk_audit_qty ='".$stockaudit_qty."',
												 last_stk_audit_date='".$audit_trans_date."', 
												 purchase_after_audit =0,
												 purchase_date='0000-00-00 00:00:00',
												 sale_after_audit =0,
												 sale_date='0000-00-00 00:00:00',
												 cl_stk=(($stockaudit_qty+purchase_after_audit)-sale_after_audit),
												  download_time=CURRENT_TIMESTAMP() ";
								mysqli_query($link,$sqlinsertclstk);
							}
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
							//For competitor stock log generation
							if($nick_name=='STAR'){
								if(strpos($prod_desc,'PPC')!=false){
									$sqlcompetitorstkchk="SELECT customer_code FROM competitor_stock WHERE customer_code='".$stockaudit_customer_code."' 
												AND emp_code='".$audit_emp_code."' AND date_time=CURDATE()";
									$rscompetitorstkchk=mysqli_query($link,$sqlcompetitorstkchk);
									$countcompetitorstkchk=mysqli_num_rows($rscompetitorstkchk);
									if($countcompetitorstkchk >0)
									{
										$sqlupdatecompetitorstk="UPDATE competitor_stock SET star='".$stockaudit_qty."' WHERE emp_code ='".$audit_emp_code."' AND 
																	date_time=CURDATE() AND customer_code ='".$stockaudit_customer_code."'";
										mysqli_query($link,$sqlupdatecompetitorstk);
									}
									else
									{
										$sqlinsertcompetitorstk="INSERT INTO competitor_stock SET emp_code	='".$audit_emp_code."',
																	date_time=CURDATE(),
																	customer_code ='".$stockaudit_customer_code."',star='".$stockaudit_qty."'";
										mysqli_query($link,$sqlinsertcompetitorstk);							
									}
								}
							}//End of STAR if
							//End for competitor stock log generation
							
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
								<span style='font-family:Arial CE;font-size:10pt'>".$stockaudit_prod_details."</span>&nbsp;</td>
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
				$mail_hint_remarks=$stockaudit_hint_remarks_array_mailbody[$y];
				if(!in_array($audit_receipt_id,$audit_array_receipt_id_exists) && $audit_receipt_id!='')
				{
					$sqlempname="SELECT emp_name,reporting_to FROM employee_master WHERE emp_code='".$mail_emp_code."'";
					$rsempname=mysqli_query($link,$sqlempname);
					$rowempname=mysqli_fetch_assoc($rsempname);
					$emp_name=title_case_emp($rowempname['emp_name']);
					$reporting_to=$rowempname['reporting_to'];
					
					$sqlcustomername="SELECT customer_name FROM customer_master WHERE customer_code='".$mail_customer_code."'";
					$rscustomername=mysqli_query($link,$sqlcustomername);
					$rowcustomername=mysqli_fetch_assoc($rscustomername);
					$customer_name=$rowcustomername['customer_name'];
					$addressaudit=getReverseGeo($mail_latt,$mail_longi);
					
					
					if($nick_name=='RUPA' || $nick_name=='RUPAT')
					{
					 $audit_email='';
					}
					else
					{
					 $audit_email=AUDITEMAILRECIPENTS;
					}
					if($nick_name=='STAR' || $nick_name=='START')
					{
						if($reporting_to !='')
						{
							if($mail_hint_remarks=='Branding Requirement' || $mail_hint_remarks=='Technical Requirement')
							{
							$visit_date=date('d-m-Y H:i:s',strtotime($mail_date_time));
							send_hint_remarks_email_sms($mail_hint_remarks,$reporting_to,$visit_date,$emp_name,$mail_customer_code,$mail_d_instruction,$nick_name,$audit_receipt_id);
							}
						}
					}

					if(substr($audit_receipt_id,0,1)=='N')
					{
					   //Start for STAR mis data details No stock audit data updation
						if($nick_name=='STAR')
						{
							$qty='';
							$trans_type='NS';
							$trans_sub_type='';
							update_transaction_STAR(substr($audit_receipt_id,2,5),$audit_receipt_id,$qty,$trans_type,$trans_sub_type);
						}
					  //End for STAR mis data details No stock audit data updation
	
						$nostockauditemailsubj="$nick_name - No stock audit of ".$customer_name. " on ".date('d-m-Y H:i:s',strtotime($mail_date_time)).' - '.$emp_name;
						$nostockauditemailbody = "<html><head><title>No Stock Audit</title></head>
											<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
											.$emp_name. "</b><br><br>".$emp_name." visited ".$customer_name." at ".$addressaudit.". 
											No stock audit happened. </table><br /><br />Refference no: <b>".$audit_receipt_id."</b><br />
											<b>Remarks: </b> ".strtoupper($mail_d_instruction)."
											<br><br><br>Powered By aceDNS<br></body></html>";
						$headers  = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=UTF-8\n";
						$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
									"Bcc: ".BCCEMAIL." \r\n".
									'X-Mailer: PHP/' . phpversion();
				
						//if(mail(AUDITEMAILRECIPENTS, $nostockauditemailsubj, $nostockauditemailbody, $headers))
						//if(mail($stock_audit_email, $nostockauditemailsubj, $nostockauditemailbody, $headers,'-facedns@coral.in'))
						if(mail($audit_email, $nostockauditemailsubj, $nostockauditemailbody, $headers,'-facedns@coral.in'))
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
						$stockauditmailsubj="$nick_name - Stock audit of ".$customer_name." on ".date('d-m-Y H:i:s',strtotime($mail_date_time)).' - '.$emp_name;
						$stockauditmailbody = "<html><head><title>Stock Audit Details</title></head>
									<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
									.$emp_name. "</b><br /><br /><br />Refference no: <b>".$audit_receipt_id."</b><br /><br />
									<table border=1 style=background-color:AliceBlue>
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
								<span style='font-size:10pt;font-family:Arial CE'>IMEI NO</span></strong></th>
								<th style='width:60px;min-height:21px;text-align:center'><strong>
								<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th></tr>
						".${a.$stockaudit_transid_array_mailbody[$y]}."</table>
						<br /><br /><b>Remarks: </b> ".strtoupper($mail_d_instruction)." <br /><br />Powered By aceDNS<br></body></html>";
						$headers  = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=UTF-8\n";
						$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
									"Bcc: ".BCCEMAIL." \r\n" .
									'X-Mailer: PHP/' . phpversion();
						//echo 	$stockauditmailbody;		
						//if(mail(AUDITEMAILRECIPENTS, $stockauditmailsubj, $stockauditmailbody, $headers))
						//if(mail($stock_audit_email, $stockauditmailsubj, $stockauditmailbody, $headers,'-facedns@coral.in'))
						if(mail($audit_email, $stockauditmailsubj, $stockauditmailbody, $headers,'-facedns@coral.in'))
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
		}//End of for loop of sending mail
	}// End of if of sending mail

}//End of stock audit if

 /* --------------------END QUERY FOR STOCK AUDIT------------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
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
