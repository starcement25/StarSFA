<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
	}
}
//$body=str_replace("'",'"',$body);
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND NAR.receiver_id IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND NAR.receiver_id='".$emp_code."'";
}
if(in_array('transporter',$menu_access_array)) //Start of transporter
{
	$sqlquery="SELECT NAR.notification_id,NM.message,NM.sender_id FROM notification_master NM,notification_ack_relation NAR 
				  WHERE NM.notification_id=NAR.notification_id AND NAR.ack_id='' AND type_of_notification='Broadcast OTP'";
}
else
{
	if($emp_code!='C0007'){
		$sqlquery="SELECT NAR.notification_id,NM.message,NM.sender_id FROM notification_master NM,notification_ack_relation NAR 
				  WHERE NM.notification_id=NAR.notification_id AND NAR.ack_id='' ".$emp_hierarchy_condition."";
	}
	else
	{
		$sqlquery="SELECT NAR.notification_id,NM.message,NM.sender_id FROM notification_master NM,notification_ack_relation NAR 
				  WHERE NM.notification_id=NAR.notification_id AND NAR.ack_id=''";
	}
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		while($rownotification = mysqli_fetch_assoc($result))
		{
			$contents  = (($rownotification['notification_id']!='')?$rownotification['notification_id']: ' ')."^";
			$contents  .= (($rownotification['message']!='')?$rownotification['message']: ' ')."^";
			$contents  .= (($rownotification['sender_id']!='')?$rownotification['sender_id']: ' ');
			$linecontents  .= $contents."\n";	
		}
		$datacontents = str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=pending_notification.txt");
	print "$datacontents";	
	mysqli_close($link);	
?>
