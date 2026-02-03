<?php
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);
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

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><STOCK_REALLOCATION><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[]]></trans_id><latt><![CDATA[22.5643358]]></latt><longi><![CDATA[88.3567553]]></longi><date><![CDATA[2018-12-04 15:58:16]]></date></location><STOCK_REALLOCATION_DATA><ALLOCATION_ID><![CDATA[]]></ALLOCATION_ID><CUSTOMER_CODE><![CDATA[]]></CUSTOMER_CODE><PROD_CODE><![CDATA[]]></PROD_CODE><QTY><![]]></QTY></STOCK_REALLOCATION_DATA></STOCK_REALLOCATION></root>";*/
/*$body="<?xml version='1.0' encoding='UTF-8'?><root><STOCK_REALLOCATION><location><emp_code><![CDATA[E0005]]></emp_code><trans_id><![CDATA[RAE000520181210173713]]></trans_id><latt><![CDATA[]]></latt><longi><![CDATA[]]></longi><date><![CDATA[2018-12-10 17:37:13]]></date></location><STOCK_REALLOCATION_DATA><ALLOCATION_ID><![CDATA[RAE000520181210173713]]></ALLOCATION_ID><CUSTOMER_CODE><![CDATA[C/0000019]]></CUSTOMER_CODE><PROD_CODE><![CDATA[12011]]></PROD_CODE><QTY><![CDATA[3]]></QTY></STOCK_REALLOCATION_DATA><STOCK_REALLOCATION_DATA><ALLOCATION_ID><![CDATA[RAE000520181210173713]]></ALLOCATION_ID><CUSTOMER_CODE><![CDATA[C/0000019]]></CUSTOMER_CODE><PROD_CODE><![CDATA[12012]]></PROD_CODE><QTY><![CDATA[1]]></QTY></STOCK_REALLOCATION_DATA></STOCK_REALLOCATION></root>";*/

$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysqli_query($link,$sqlinsert_xml_data);
	
$location_emp_code="*ROOT*STOCK_REALLOCATION*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*STOCK_REALLOCATION*LOCATION*TRANS_ID";
$location_latt = "*ROOT*STOCK_REALLOCATION*LOCATION*LATT";
$location_longi = "*ROOT*STOCK_REALLOCATION*LOCATION*LONGI";
$location_date="*ROOT*STOCK_REALLOCATION*LOCATION*DATE";

$allocation_id = "*ROOT*STOCK_REALLOCATION*STOCK_REALLOCATION_DATA*ALLOCATION_ID";
$customer_code = "*ROOT*STOCK_REALLOCATION*STOCK_REALLOCATION_DATA*CUSTOMER_CODE";
$prod_code="*ROOT*STOCK_REALLOCATION*STOCK_REALLOCATION_DATA*PROD_CODE";
$qty="*ROOT*STOCK_REALLOCATION*STOCK_REALLOCATION_DATA*QTY";

$reallocation_array=array();
$reallocation_details_array=array();

$counter = 0;
$counterreallocation=0;
class xml_stock_reallocation{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_stock_reallocation_details{
	var $allocation_id,$customer_code,$prod_code,$qty;
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
    global $current_tag,$counter,$counterreallocation,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$allocation_id,$prod_code,$customer_code,$qty,$reallocation_array,$reallocation_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,24)=='*ROOT*STOCK_REALLOCATION')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$reallocation_array[$counter] = new xml_stock_reallocation();
				$reallocation_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$reallocation_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$reallocation_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$reallocation_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$reallocation_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,48)=='*ROOT*STOCK_REALLOCATION*STOCK_REALLOCATION_DATA')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $allocation_id:
					$reallocation_details_array[$counterreallocation] = new xml_stock_reallocation_details();
					$reallocation_details_array[$counterreallocation]->allocation_id = $data;
					break;
				case $customer_code:
					$reallocation_details_array[$counterreallocation]->customer_code = $data;
					break;	
				case $prod_code:
					$reallocation_details_array[$counterreallocation]->prod_code = $data;
					break;
				case $qty:
					$reallocation_details_array[$counterreallocation]->qty = $data;
					$counterreallocation++;
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
/* --------------------START QUERY FOR REALLOCATION------------------------------------------------------------------------------------------*/
//print_r($reallocation_array);
$reallocation_array_trans_id=array();
$emp_code_array=array();
if(count($reallocation_array)>0)
{
		for($x=0;$x<count($reallocation_array);$x++){
			$location_emp_code=$reallocation_array[$x]->location_emp_code;
			$location_trans_id=$reallocation_array[$x]->location_trans_id;
			$location_latt=$reallocation_array[$x]->location_latt;
			$location_longi=$reallocation_array[$x]->location_longi;
			$location_date=$reallocation_array[$x]->location_date;
			
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
			//$rowchkorlocation = mysqli_fetch_assoc($reschkorlocation);
			$countchkorlocation=mysqli_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for notes info
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$reallocation_array_trans_id))
				{
					array_push($reallocation_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysqli_query($link,$sqlupdateorlocation) or die(mysqli_error()." Error in update reallocation location: ".$sqlupdateorlocation);
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
	 $customer_code_array=array();
	 if(count($reallocation_details_array)>0)
		{
			for($i=0;$i<count($reallocation_details_array);$i++){
				$allocation_id=$reallocation_details_array[$i]->allocation_id;
				$customer_code=$reallocation_details_array[$i]->customer_code;
				$prod_code=$reallocation_details_array[$i]->prod_code;
				$qty=$reallocation_details_array[$i]->qty;
				
				/*$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
				$rsproddesc=mysqli_query($link,$sqlproddesc);
				$rowproddesc=mysqli_fetch_assoc($rsproddesc);
				$prod_desc=$rowproddesc['prod_desc'];
				$notificatiomessage.=$prod_desc.'-'.$requisition_qty."\n";*/

				if(!in_array($allocation_id,$reallocation_array_trans_id))
				{
				  $sqlselvaliddate="SELECT from_date,to_date FROM customer_product_allocation WHERE customer_code='".$customer_code."' AND  
				  					prod_code='".$prod_code."' AND acedns='Y'";
				  $rsselvaliddate=mysqli_query($link,$sqlselvaliddate);
				  $rowselvaliddate=mysqli_fetch_assoc($rsselvaliddate);
				  if($validtodate ==''){					
				  	$validtodate=$rowselvaliddate['to_date'];
				  }
				  else $validtodate=$validtodate;
				  $sqlempcode="SELECT CRR.emp_code,EM.reporting_to FROM customer_route_emp_relation CRR,
							employee_master EM WHERE CRR.emp_code=EM.emp_code AND CRR.customer_code='".$customer_code."' AND CRR.acedns='Y'";
				  $rsempcode=mysqli_query($link,$sqlempcode);
				  $rowempcode=mysqli_fetch_assoc($rsempcode);
				  $emp_code_reallocation=$rowempcode['emp_code'];
				  $reporting_to_emp_code=$rowempcode['reporting_to'];

				  if(!in_array($customer_code,$customer_code_array))
		  			{
				  		$sqldelete="DELETE FROM customer_product_allocation WHERE customer_code='".$customer_code."'";
				 		mysqli_query($link,$sqldelete);
				  		$sqlupdatestockbalance="UPDATE stock_balance_details SET active_flag='N',download_time=CURRENT_TIMESTAMP() 
											WHERE customer_code='".$customer_code."'";
				  		mysqli_query($link,$sqlupdatestockbalance);
						array_push($customer_code_array,$customer_code);
					}					
					$sqlinsertreallocation="INSERT INTO customer_product_allocation SET allocation_id ='".$allocation_id."',
										  prod_code 		    ='".$prod_code."',
										  customer_code 		='".$customer_code."',
										  qty 					='".$qty."',
										  from_date				=CURDATE(),
										  to_date				='".$validtodate."',
										  acedns 				='Y',
										  download_time			=CURRENT_TIMESTAMP()";											  
					if(mysqli_query($link,$sqlinsertreallocation))
					{
						$flag=5;
						
					    $sqlinsertstockbalance="INSERT INTO stock_balance_details SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',
		  							prod_code='".$prod_code."',allocation_qty='".$qty."',
									allocation_date=CURRENT_TIMESTAMP(),download_time=CURRENT_TIMESTAMP()";
		 				 if(mysqli_query($link,$sqlinsertstockbalance))
						 {
							 $flag=5;
							 $sqlupdatereallocation="UPDATE stock_reallocation SET balance_qty=(balance_qty-$qty),reallocation_date=CURRENT_TIMESTAMP() 
							 						WHERE emp_code='".$reporting_to_emp_code."' AND prod_code='".$prod_code."'";
							 if(mysqli_query($link,$sqlupdatereallocation))
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
	/*$notification_type='Broadcast';
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
		$message=$notificatiomessage." THANKS,\nVCONNECT";
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
        $result = curl_exec($ch);*/
        /*if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }*/
		/*$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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
		}*/
}
 /* --------------------END QUERY FOR REALLOCATION--------------------------------------------------------------------------------------------------------*/
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
$url =APICALLLOGURL."/opeartiondb-stock-reallocation.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
