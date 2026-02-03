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
function updatecustomerrequisition($requisition_id,$requisition_qty,$location_date,$rds_tag,$prod_code,$allocation_id)
{
  $sqltotalrequisition="SELECT SUM(requisition_qty) AS total_requisition FROM stock_balance_details WHERE 
						allocation_id_consolidated='".$allocation_id."' AND prod_code   ='".$prod_code."' AND active_flag='Y'";
  $rstotalrequisition=mysqli_query($link,$sqltotalrequisition);
  $rowtotalrequisition=mysqli_fetch_assoc($rstotalrequisition);
  $total_requisition=$rowtotalrequisition['total_requisition'];

  $sqlselrequisitionupdationrow="SELECT requisition_qty,sl_no,customer_code,allocation_qty 
								FROM stock_balance_details WHERE customer_code 
								IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')
								AND prod_code   ='".$prod_code."' AND (allocation_qty-requisition_qty) >0 
								ORDER BY download_time ASC ,customer_code ASC LIMIT 0,1";
  $rsselrequisitionupdationrow=mysqli_query($link,$sqlselrequisitionupdationrow);
  $rowselrequisitionupdationrow=mysqli_fetch_assoc($rsselrequisitionupdationrow);
  $requisition_qty_exists=$rowselrequisitionupdationrow['requisition_qty'];
  $sl_no=$rowselrequisitionupdationrow['sl_no'];
	//$requisition_qty_updated=$requisition_qty_exists+$requisition_qty;
  $customer_code_stockbalance=$rowselrequisitionupdationrow['customer_code'];
  $allocation_qty=$rowselrequisitionupdationrow['allocation_qty'];
  $requisition_qty_updated=$requisition_qty-$total_requisition;
	if($requisition_qty_updated > $allocation_qty)
	{
		$requisition_qty_final=$allocation_qty;
	}
	else { 
		$requisition_qty_final=$requisition_qty_updated;
	}
	$sqlupdatestockbalance="UPDATE stock_balance_details SET requisition_id='".$requisition_id."',
				requisition_qty ='".$requisition_qty_final."',	requisition_date='".$location_date."' WHERE 
				customer_code='".$customer_code_stockbalance."' AND prod_code='".$prod_code."' AND 	active_flag='Y'";
  if(mysqli_query($link,$sqlupdatestockbalance))
  {
	  $flagval='SUCCESS';
	  $total_requisition= $total_requisition+$requisition_qty_final;
  }
  else
  {
	  $flagval='FAIL';
  }
  if($total_requisition==$requisition_qty)
  {
	  return $flagval;
  }
  else
  {
	 /*echo $requisition_qty_left=$requisition_qty-$total_requisition; 
	 if($requisition_qty_left <0){
	    exit();
	 }*/
	 updatecustomerrequisition($requisition_id,$requisition_qty,$location_date,$rds_tag,$prod_code,$allocation_id); 
  }
}

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'] ?? '';
$last_update_time=str_replace('€',' ',$last_update_time);

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><REQUISITION_DETAILS><location><emp_code><![CDATA[E0161]]></emp_code><trans_id><![CDATA[RDE016120190124115946]]></trans_id><latt><![CDATA[22.5643386]]></latt><longi><![CDATA[88.3569023]]></longi><date><![CDATA[2019-01-24 11:59:46]]></date></location><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12017]]></PROD_CODE><ALLOT_QTY><![CDATA[12.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[10]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12019]]></PROD_CODE><ALLOT_QTY><![CDATA[4.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[4]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12029]]></PROD_CODE><ALLOT_QTY><![CDATA[4.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[4]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12030]]></PROD_CODE><ALLOT_QTY><![CDATA[8.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[8]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12013]]></PROD_CODE><ALLOT_QTY><![CDATA[49.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[45]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12025]]></PROD_CODE><ALLOT_QTY><![CDATA[102.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[88]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12003]]></PROD_CODE><ALLOT_QTY><![CDATA[36.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[32]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12031]]></PROD_CODE><ALLOT_QTY><![CDATA[25.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[25]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12032]]></PROD_CODE><ALLOT_QTY><![CDATA[20.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[18]]></REQUISITION_QTY></REQUISITION_DATA><REQUISITION_DATA><ALLOCATION_ID><![CDATA[CAC#000014420190123175933]]></ALLOCATION_ID><PROD_CODE><![CDATA[12022]]></PROD_CODE><ALLOT_QTY><![CDATA[17.0]]></ALLOT_QTY><REQUISITION_ID><![CDATA[RDE016120190124115946]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[17]]></REQUISITION_QTY></REQUISITION_DATA></REQUISITION_DETAILS></root>";*/

$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*REQUISITION_DETAILS*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*REQUISITION_DETAILS*LOCATION*TRANS_ID";
$location_latt = "*ROOT*REQUISITION_DETAILS*LOCATION*LATT";
$location_longi = "*ROOT*REQUISITION_DETAILS*LOCATION*LONGI";
$location_date="*ROOT*REQUISITION_DETAILS*LOCATION*DATE";

$allocation_id = "*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*ALLOCATION_ID";
$prod_code="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*PROD_CODE";
$allot_qty="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*ALLOT_QTY";
$requisition_id="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*REQUISITION_ID";
$requisition_qty="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*REQUISITION_QTY";

$requisition_array=array();
$requisition_details_array=array();

$counter = 0;
$counterrequisition=0;
class xml_requisition{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_requisition_details{
	var $allocation_id,$prod_code,$allot_qty,$requisition_id,$requisition_qty;
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
    global $current_tag,$counter,$counterrequisition,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$allocation_id,$prod_code,$allot_qty,$requisition_id,$requisition_qty,$requisition_array,$requisition_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,25)=='*ROOT*REQUISITION_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$requisition_array[$counter] = new xml_requisition();
				$requisition_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$requisition_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$requisition_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$requisition_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$requisition_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,42)=='*ROOT*REQUISITION_DETAILS*REQUISITION_DATA')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $allocation_id:
					$requisition_details_array[$counterrequisition] = new xml_requisition_details();
					$requisition_details_array[$counterrequisition]->allocation_id = $data;
					break;
				case $prod_code:
					$requisition_details_array[$counterrequisition]->prod_code = $data;
					break;
				case $allot_qty:
					$requisition_details_array[$counterrequisition]->allot_qty = $data;
					break;
				case $requisition_id:
					$requisition_details_array[$counterrequisition]->requisition_id = $data;
					break;
				case $requisition_qty:
					$requisition_details_array[$counterrequisition]->requisition_qty = $data;
					$counterrequisition++;
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
/* --------------------START QUERY FOR REQUISITION ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$requtisition_array_trans_id=array();
$registration_id_array=array();
$emp_code_array=array();
if(count($requisition_array)>0)
{
		for($x=0;$x<count($requisition_array);$x++){
			$location_emp_code=$requisition_array[$x]->location_emp_code;
			$location_trans_id=$requisition_array[$x]->location_trans_id;
			$location_latt=$requisition_array[$x]->location_latt;
			$location_longi=$requisition_array[$x]->location_longi;
			$location_date=$requisition_array[$x]->location_date;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysqli_query($link,$sqlupdatelatlongzero) or die(mysqli_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
	
			//For checking that trans id exist or not for notes info
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysqli_query($link,$sqlchkorlocation) or die(mysqli_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for notes info
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$requtisition_array_trans_id))
				{
					array_push($requtisition_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update notes info location: ".$sqlupdateorlocation);
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
			//For  Push Notification
			$sqlcustname="SELECT CM.customer_name FROM customer_master CM,customer_route_emp_relation CRR 
						WHERE CRR.customer_code=CM.customer_code AND CRR.emp_code='".$emp_code."'";
			$rscustname=mysqli_query($link,$sqlcustname);	
			$rowcustname=mysqli_fetch_assoc($rscustname);
			$customer_name=$rowcustname['customer_name'];		
			$sqlemdetails="SELECT CH.registrationid,EM.reporting_to FROM changepassword CH,
							employee_master EM WHERE CH.emp_code=EM.emp_code AND EM.emp_code='".$emp_code."' ";
			$rsempdetails=mysqli_query($link,$sqlemdetails);
			while($rowempdetails=mysqli_fetch_assoc($rsempdetails))
			{
				$registrationid=$rowempdetails['registrationid'];
				$reporting_to=$rowempdetails['reporting_to'];
				if(!in_array($registrationid,$registration_id_array))
				{
					array_push($registration_id_array,$registrationid);
					array_push($emp_code_array,$emp_code);
				}
				$sqlregdetailsreportingto="SELECT registrationid FROM changepassword WHERE emp_code='".$reporting_to."'";
				$rsregdetailsreportingto=mysqli_query($link,$sqlregdetailsreportingto);
				$rowregdetailsreportingto=mysqli_fetch_assoc($rsregdetailsreportingto);
				$registrationidreportingto=$rowregdetailsreportingto['registrationid'];
				if(!in_array($registrationidreportingto,$registration_id_array))
				{
					array_push($registration_id_array,$registrationidreportingto);
					array_push($emp_code_array,$reporting_to);
				}
			}
	
			//Creation of code random no parameter
			$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempname=mysqli_query($link,$sqlempname);
			$rowempname=mysqli_fetch_assoc($rsempname);
			$emp_name=$rowempname['emp_name'];
			$branch_code=$rowempname['branch_code'];
			$vertical_value=$rowempname['vertical_value'];
			$notificatiomessage="Hi,<br>Requisition done by ".$customer_name."<br>";
			
			$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
			$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
			//For Insert into the location table for new trans id regarding market feedback stock audit
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
		}
	 }//End of for loop
	 
	 if(count($requisition_details_array)>0)
		{
			for($i=0;$i<count($requisition_details_array);$i++){
				$allocation_id=$requisition_details_array[$i]->allocation_id;
				$prod_code=$requisition_details_array[$i]->prod_code;
				$allot_qty=$requisition_details_array[$i]->allot_qty;
				$requisition_id=$requisition_details_array[$i]->requisition_id;
				$requisition_qty=$requisition_details_array[$i]->requisition_qty;
				
				$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
				$rsproddesc=mysqli_query($link,$sqlproddesc);
				$rowproddesc=mysqli_fetch_assoc($rsproddesc);
				$prod_desc=$rowproddesc['prod_desc'];
				$notificatiomessage.=$prod_desc.'-'.$requisition_qty."<br>";

				if(!in_array($requisition_id,$requtisition_array_trans_id))
				{
					$sqlinsertrequisition="INSERT INTO requisition_details SET allocation_id ='".$allocation_id."',
										  prod_code 		    ='".$prod_code."',
										  allot_qty 			='".$allot_qty."',
										  requisition_id 		='".$requisition_id."',
										  requisition_qty 		='".$requisition_qty."'";											  
					if(mysqli_query($link,$sqlinsertrequisition))
					{
						$flag=5;
						
						//update requisition
						$sqlselcustomer="SELECT CPA.customer_code,CM.cust_type,CM.rds_tag,CM.retailer_app FROM customer_product_allocation CPA,
										customer_master CM WHERE CPA.customer_code=CM.customer_code AND 
										(CPA.allocation_id ='".$allocation_id."' OR CPA.allocation_id_consolidated='".$allocation_id."')";
						$rsselcustomer=mysqli_query($link,$sqlselcustomer);
						$rowselcustomer=mysqli_fetch_assoc($rsselcustomer);
						$customer_code=$rowselcustomer['customer_code'];
						$cust_type=$rowselcustomer['cust_type'];
						$rds_tag=$rowselcustomer['rds_tag'];
						$retailer_app=$rowselcustomer['retailer_app'];
						if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='R')
						{
						 $sqlupdatestockbalance="UPDATE stock_balance_details SET requisition_id='".$requisition_id."',
										requisition_qty ='".$requisition_qty."',	requisition_date='".$location_date."' WHERE 
										allocation_id='".$allocation_id."' AND prod_code='".$prod_code."' AND 	active_flag='Y'";
		 			    if(mysqli_query($link,$sqlupdatestockbalance))
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
						else if($retailer_app=='yes' && $rds_tag!='' && $cust_type=='R')
						{
							$updateresponse=updatecustomerrequisition($requisition_id,$requisition_qty,$location_date,$rds_tag,$prod_code,$allocation_id);
							/*if($updateresponse=='SUCCESS')
							{
								$flag=5;
							}
							else
							{
								mysqli_query($link,"ROLLBACK");
								echo $flag=0;
								return;
							}*/
							/*$sqltotalallocation="SELECT SUM(allocation_qty) AS total_allocation FROM stock_balance_details WHERE 
												allocation_id_consolidated='".$allocation_id."' AND prod_code   ='".$prod_code."'";
							$rstotalallocation=mysqli_query($link,$sqltotalallocation);
							$rowtotalallocation=mysqli_fetch_assoc($rstotalallocation);
							$total_allocation=$rowtotalallocation['total_allocation'];
												
							echo $sqlselrequisitionupdationrow="SELECT requisition_qty,sl_no,customer_code,allocation_qty 
															FROM stock_balance_details WHERE customer_code 
													IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y' AND cust_type='R')
													AND prod_code   ='".$prod_code."' AND (allocation_qty-requisition_qty) >0 
													ORDER BY download_time ASC ,customer_code ASC";
							$rsselrequisitionupdationrow=mysqli_query($link,$sqlselrequisitionupdationrow);
							while($rowselrequisitionupdationrow=mysqli_fetch_assoc($rsselrequisitionupdationrow))
							{
								$requisition_qty_exists=$rowselrequisitionupdationrow['requisition_qty'];
								$sl_no=$rowselrequisitionupdationrow['sl_no'];
								//$requisition_qty_updated=$requisition_qty_exists+$requisition_qty;
								$customer_code_stockbalance=$rowselrequisitionupdationrow['customer_code'];
								$allocation_qty=$rowselrequisitionupdationrow['allocation_qty'];
								if($requisition_qty > $allocation_qty)
								{
									$requisition_qty_final=$allocation_qty;
								}
								else { 
									$requisition_qty_final=$requisition_qty;
								}
								$requisition_qty_left=$requisition_qty-$requisition_qty_final;
								if($requisition_qty_left >=0)
								{
									echo $sqlupdatestockbalance="UPDATE stock_balance_details SET requisition_id='".$requisition_id."',
												requisition_qty ='".$requisition_qty_final."',	requisition_date='".$location_date."' WHERE 
												customer_code='".$customer_code_stockbalance."' AND prod_code='".$prod_code."' AND 	active_flag='Y'";
									if(mysqli_query($link,$sqlupdatestockbalance))
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
						   }*/
						}
					}
					else
					{
						mysqli_query($link,"ROLLBACK");
						echo $flag=0;
						return;
					}
				}
			}
		}
	$notification_type='Broadcast';
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$location_date_notification=$year.$month.$date.$hour.$minute.$second;
	$apiKey='AAAAdCu4Fjw:APA91bHHl7RnWyOj4Pb42NBuPfJZQAkOlmxKCoGL9flYk8xfhsqMY7_YtOtBKXPHNgx5szKyD2T1HriSFZ5NHmcBu874v9uCym0VEQlpNYbuUOjHBUmaVvtIXXYu-FOjhuwUksA4Geob';
	$collapseKey=rand();
	$notification_id='PN'.$emp_code.$location_date_notification;
	  for ($m=0;$m<count($registration_id_array);$m++)
	    {
		//Title of the Notification.
		$title = "";
		$message="<font color='#009933'>".$notificatiomessage." THANKS,<br>VCONNECT</font>";
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $message);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $message); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registration_id_array[$m], 'data'=>$data);
		
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
 
        // Execute post
        $result = curl_exec($ch);
        /*if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }*/
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {    
			//request failed    
			$successval=0; 
		} 
		else
		{
			$successval=1;	
		}
        // Close connection
        curl_close($ch);
		$sqlnotificationmaster  = "INSERT INTO notification_master ";
		$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
		$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
		$sqlnotificationmaster .= " ,sender_id='VCONNECT'";
		$sqlnotificationmaster .= " ,message='".addslashes($message)."'";
		$sqlnotificationmaster .= " ,transferred='YES'";
		mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notification insertion.");
		$sqlnotification  = "INSERT INTO notification_ack_relation ";
		$sqlnotification .= " SET notification_id='".$notification_id."'";
		$sqlnotification .= " ,receiver_id='".$emp_code_array[$m]."'";
		mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");
		}
}
 /* --------------------END QUERY FOR RQUISITION--------------------------------------------------------------------------------------------------------*/
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
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url =APICALLLOGURL."/operationdb-requisition-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
