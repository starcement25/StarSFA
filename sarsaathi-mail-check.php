<?php
function return_employee_upper_hierarchy($emp_code) {
    $emphierarchy = array();
    employee_upper_hierarchy_details($emp_code, $emphierarchy);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
    return $emphierarchystring;
}
function employee_upper_hierarchy_details($emp_code,&$emphierarchy){
  $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			$reporting_to=$rowemphierarchy['reporting_to'];
			if(strpos($reporting_to,',')!=false){
				$reporting_to_Arr=explode(',',$reporting_to);
			
				for($cn=0;$cn<count($reporting_to_Arr);$cn++)
				{
					$emphierarchy[] = "'".$reporting_to_Arr[$cn]."'";
					employee_upper_hierarchy_details($reporting_to_Arr[$cn],$emphierarchy);
				}
			}
			else
			{
				$emphierarchy[] = "'".$reporting_to."'";
				employee_upper_hierarchy_details($reporting_to,$emphierarchy);
			}
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
}

function send_the_mail($to_email,$subject,$bodyml){
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require_once('class.phpmailer.php');
require_once('class.smtp.php');
$sts = "FALSE";
$to_email_arr = array();
$to_email = $to_email ? trim($to_email) : "";
$subject = $subject ? trim($subject) : "";
$bodyml = $bodyml ? trim($bodyml) : "";
if($to_email!="" && $subject!="" && $bodyml!=""){
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
//$bodyml             = eregi_replace("[\]",'',$bodyml);
$mail->IsSMTP(); // telling the class to use SMTP
//$mail->Host       = "mail.starsaathi.com"; // SMTP server (For gmail "mail.coral.in")
$mail->Host       = "mail.starcement.co.in";
$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Host       = "96.45.76.75";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "starsaathi-starcement";  // GMAIL username
$mail->Password   = "BVhf@_745hw";            // GMAIL password

$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);
foreach($to_email_arr as $to_email_arr_val){
	if(trim($to_email_arr_val)!=""){
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
		$mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
}

$mlsts = $mail->Send();
if(!$mlsts) {
  $sts = "FALSE";
} else {
 $sts = "TRUE";
}
}
}
return $sts;
}
define("SERVERREMOTE","103.242.119.68");
define("USERREMOTE","acedns_dnsprod");
define("PASSWORDREMOTE","dnsprod1234#");
define("DBREMOTE","acedns_STAR");
	
$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");

		$customer_code='C/0079681';
		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsdealeremp=mysqli_query($link,$sqldealeremp,$link);
		while($rowdealeremp=mysqli_fetch_assoc($rsdealeremp))
		{
			$emp_code_db=$rowdealeremp['emp_code'];
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
			$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
			$rsemailhierarchy=mysqli_query($link,$sqlemailhierarchy);
			while($rowemailhierarchy=mysqli_fetch_assoc($rsemailhierarchy))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
			}
			$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='".$emp_code_db."' AND emp_code IN(SELECT emp_code FROM employee_master WHERE UPPER(sale_access)='PRIMARY' AND acedns='Y') ";
			$rsregistrationid=mysqli_query($link,$sqlregistrationid,$link);
			$rowregistrationid=mysqli_fetch_assoc($rsregistrationid);
			$registrationid=$rowregistrationid['registrationid'];
			if($registrationid!='')
			{
				if(!in_array($registrationid,$registrationid_array))
				{
					array_push($registrationid_array,$registrationid);
					array_push($emp_code_array,$emp_code_db);
				}
			}
		}
		/*$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='E0555'";
		$rsregistrationid=mysqli_query($link,$sqlregistrationid,$link);
		$rowregistrationid=mysqli_fetch_assoc($rsregistrationid);
		$registrationid=$rowregistrationid['registrationid'];*/
		$email_broker='';
		$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsbroker=mysqli_query($link,$sqlbroker,$link);
		while($rowbroker=mysqli_fetch_assoc($rsbroker))
		{
			$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
			$rsemailbroker=mysqli_query($link,$sqlemailbroker,$link);
			while($rowemailbroker=mysqli_fetch_assoc($rsemailbroker))
			{
				$email_broker=$email_broker.$rowemailbroker['mail_id'].',';
			}
		}
		
		$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
		echo $final_email=substr($email_hierarchy,0,-1).','.','.','.substr($email_broker,0,-1).','.'dipankarc@coral.in';
		/*$subject = "Order for branch ".strtoupper($branch_name)." ";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
		$message="Order received from $customer_name @ $hour:$minute\n";
		$message.= '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_format.'<br>
					<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
					<b>Customer Name: </b> '.$customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_desc.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
		$send_mail = send_the_mail($final_email,$subject,$message);*/




$subject='Test mail from STARSAATHI';
$message='Message Test mail from STARSAATHI';
//$final_email='dipankarc@coral.in,';
//$final_email='dipsome2006@gmail.com';
send_the_mail($final_email,$subject,$message);