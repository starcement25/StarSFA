<?php
ob_start();
	session_start();
	 /*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
	require("adminUtils.php");
	require ("attribute_selection.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	if($mode == 'send')						   send_message();
	else    									 disphtml("main();");
ob_end_flush();


require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

function main()
{

require("include/dbcon.php");
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
	$vertical_name = $_REQUEST['vertical_name'];
	$branch_code = $_REQUEST['branch_code'];
	if($vertical_name != '' && $branch_code != ''){
		$vertical_branch_condition = " AND FIND_IN_SET('$vertical_name',EM.vertical_value) AND (";
		$branchcodearray = explode(",",$branch_code);
		foreach($branchcodearray as $branch_val){
			if(end($branchcodearray) == $branch_val)
				$vertical_branch_condition .= "FIND_IN_SET('$branch_val', EM.branch_code)";
			else
				$vertical_branch_condition .= "FIND_IN_SET('$branch_val', EM.branch_code) OR ";
		}
		$vertical_branch_condition .= ") ";
	}
	else if($vertical_name != ''){
		$vertical_branch_condition = " AND FIND_IN_SET('$vertical_name',EM.vertical_value) ";
	}
	else if($branch_code != ''){
		$vertical_branch_condition = " AND FIND_IN_SET('$branch_code', EM.branch_code) ";
	}
	else{
		$vertical_branch_condition = "";
	}
?>
<script language="JavaScript" type="text/javascript">
function check(form)
{
	/*if(document.getElementById('emp_name').value==0)
	{
		alert("Please select a employee.");
		document.getElementById('emp_name').focus();
		return false;
	}*/
	var flag=false;
	var cbs = document.getElementsByTagName('input');
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
		  if(cbs[i].checked ==true)
		  {
			  var flag=true;
		  }
		}
	  }
	  if(flag==false)
	  {
		  alert("Please select at least one employee");
		  return false;
	  }
	if(form.message.value.search(/\S/)==-1)
	{
		alert("Please enter message");
		form.message.focus();
		return false;
	}
	return true;
}
function checked_all()
{
  var cbs = document.getElementsByTagName('input');
  if(document.getElementById("all_checked").checked==true)
  {
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
		  cbs[i].checked =true;
		}
	  }
  }
  else
  {
	  for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
		  cbs[i].checked =false;
		}
	  }
  }
}
</script>

<!--table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Broadcast</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
			<br>
			<br>
			<form name="frmadd" method="post" action="adminPushNotification.php" onSubmit="return check(this);">
			<input type="hidden" name="mode" value="send">			
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD"> 
				  <td colspan="3" align="left">Send Message</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandatory.</td>
				</tr>
				<?php /*if($GLOBALS['err_msg']!=""){?>
				<tr>
					<td align="center" colspan="3" class="ERR"><strong><font color="#FF0000"><?=$GLOBALS['err_msg']?></font></strong></td>
				</tr>
				<?php }
				?>
                <tr>
                    <td align="right" width="25%" class="tbllogin" valign="top">Employee<font color="#FF0000"><strong>*</strong></font></td>
                    <td width="5%" align="center" valign="top" class="tbllogin">:</td>
                    <td align="left" width="" style="vertical-align:top;height: 300px;overflow-y: scroll;display:block;" >
                    <table width="100%" align="left">
                    	<tr>
                    		<td align="left">
                       			 <input type="checkbox" name="all_checked" id="all_checked" value="all" onchange="javascript:checked_all();"/>ALL
                        	</td>
                         </tr>   
                        <?php
							if($_REQUEST['emp_name']!='')
							{
								$emp_name=implode(',',$_REQUEST['emp_name']);
								$emp_name_Array=explode(',',$emp_name);
							}

                            $sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH 
                                       WHERE CH.emp_code=EM.emp_code AND EM.acedns!='N' AND app_access = 'Y' AND CH.is_licensed='1' ".$vertical_branch_condition.$emp_hierarchy_condition." ORDER BY EM.emp_name ASC";
                            $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                            $countemp=mysqli_num_rows($resultqueryemp);
                            if($countemp>0){
                                while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
                                {
                                ?>
                                <tr>
                    				<td align="left">
                                		<input type="checkbox" name="emp_name[]" value="<?php echo $rowqueryemp['emp_code'];?>" /><?php echo $rowqueryemp['emp_name'];?>
                                    </td>
                                 </tr>   
                                <?php	
                                }
                            }
                        ?>
                    </table>
                        <!--select name="emp_name[]" id="emp_name" multiple style="width:300px;height:200px;">
                        <option value="">SELECT</option> 
                        <?php 
                        /*$sqlqueryemp="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CH 
                                      WHERE CH.emp_code=EM.emp_code AND CH.is_licensed='1' ".$emp_hierarchy_condition." ORDER BY EM.emp_name ASC";
                        $resultqueryemp = mysqli_query($link,$sqlqueryemp);
                        $countemp=mysqli_num_rows($resultqueryemp);
                        if($countemp>0){
							while($rowqueryemp = mysqli_fetch_assoc($resultqueryemp))
							{
							?>
								<option value="<?php echo $rowqueryemp['emp_code'];?>" <?php if( $_REQUEST['emp_name']==$rowqueryemp['emp_code']){echo 'selected';}?>><?php echo $rowqueryemp['emp_name'];?></option>
							<?php
							}
                        }*/
                        ?>	
                        </select-->
                    <!--/td>
                </tr>
				<tr>
					<td width="25%" align="right" valign="top" class="tbllogin">Message<font color="#FF0000"><strong>*</strong></font></td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="message" class="inplogin" style="width:300px;height:30px;" value="<?php echo $_REQUEST['message'];?>"/></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" value=" Send " class="inplogin"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table-->
<?php
	if($GLOBALS['err_msg']!=""){
		$errmsg="<table width='100%'><tr><td align=\"center\" colspan=\"3\" class=\"ERR\"><strong><font color=\"#FF0000\">".$GLOBALS['err_msg']."</font></strong></td></tr></table>";
	}
	else
	{
		$errmsg="";
	}
	$create_control = "<tr><td align=\"right\" valign=\"top\">Message:</td><td><input type=\"text\" name=\"message\" id=\"message\"  style=\"width:200px;height:30px;\"/></td></tr><tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit1\" value=\"Submit\" ></td></tr>";
	echo "<center><form name=\"frmadd\" method=\"post\" action=\"adminPushNotificationStar.php\" onSubmit=\"return display_result();\"><input type=\"hidden\" name=\"mode\" value=\"send\">	";
	
	echo $errmsg."<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Send Message</span><br><br>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
	</table>";
	echo "<br>";
	echo "</form></center>";
}//End of main()
function send_message()
{
	$message=$_REQUEST['message'];
	$emp_name=str_replace("'","",$_REQUEST['employee']);
	$emp_name_Array=explode(',',$emp_name);
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$location_date=$year.$month.$date.$hour.$minute.$second;

	$notification_id='PN'.strtoupper($_SESSION['admin_login']).$location_date;
	if(count($emp_name_Array) >1)
	{
		$notification_type='Broadcast';
	}
	else
	{
		$notification_type='Individual';
	}
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

	$apiKey='AAAAUCNxsT8:APA91bE-MLw1RwEHr7sZrKRmRiipBv1NnW-p6Gz1ajW_vOESL7jM3Wx0Rd0z5sUJoEM3DVqo_OS_kN0aQwXt1v1aciNzAir0FAlbCkefWvGFTDjYI-Dm7mTELSwgwCoo9zhqnHEtBkE6';
	$collapseKey=rand();
	$sqlsendername="SELECT emp_name FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
	$rssendername=mysqli_query($link,$sqlsendername);
	$rowsendername=mysqli_fetch_assoc($rssendername);
	$sender_name=$rowsendername['emp_name'];
	if($sender_name=='')
	{
		$sender_name='ADMIN';
	}
	$registration_ids=array();
	//$messageandroid=$notification_id.'∞'.$message.'∞'.$sender_name;
	foreach ($emp_name_Array as $values)
	{
		$sqlregdetails="SELECT registrationid FROM changepassword WHERE emp_code='".$values."'";
		$rsregdetails=mysqli_query($link,$sqlregdetails);
		$rowregdetails=mysqli_fetch_assoc($rsregdetails);
		$registrationid=$rowregdetails['registrationid'];
		
		 //Starting push notification
		//$gcmurl='https://android.googleapis.com/gcm/send';
		 /*$headers = array('Authorization:key=' . $apiKey);  
		 $data = array(    
		 'registration_id' =>  $registrationid,    
		 'collapse_key' => $collapseKey,    
		 'data.message' => $messageandroid);
		 $ch = curl_init();    
		 curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");    
		 if ($headers)    
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);    
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
		 curl_setopt($ch, CURLOPT_POST, true);    
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {    
			//request failed    
			$successval=0; 
		} 
		else
		{
			$successval=1;	
		}
		curl_close($ch);*/
		
		// sending push message to multiple users by firebase registration ids
       /* $fields = array(
            'to' => $registration_ids,
            'data' => $message,
        );*/
		//Title of the Notification.
		$title = "";
		$message=$message;
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $message);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		if(strtoupper($_SESSION['nick_name'])=='GOLDSTONE')
		{
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $message, 'image' => "http://salesmpower.acedns.in/upload/GOLDSTONE/teachers_day.jpg" , 
		'action' => "url", 'action_destination' => "http://salesmpower.acedns.in"); 
		}
		else
		{
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => strtoupper($_SESSION['admin_login']), 'body' => $message); 
		}
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid, 'data'=>$data);
		
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
	echo	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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
 
		$sqlnotification  = "INSERT INTO notification_ack_relation ";
		$sqlnotification .= " SET notification_id='".$notification_id."'";
		$sqlnotification .= " ,receiver_id='".$values."'";
		$sqlnotification .= " ,ack_id='".$values."'";
		
		mysqli_query($link,$sqlnotification) or die(mysqli_error()." Error in notification insertion.");
	}
	if($successval==1)
	 {
		 	$sqlnotificationmaster  = "INSERT INTO notification_master ";
			$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
			$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
			$sqlnotificationmaster .= " ,sender_id='".strtoupper($_SESSION['admin_login'])."'";
			$sqlnotificationmaster .= " ,message='".addslashes($message)."'";
			$sqlnotificationmaster .= " ,transferred='YES'";
			mysqli_query($link,$sqlnotificationmaster) or die(mysqli_error()." Error in notificatin insertion.");

		 
		 $GLOBALS['err_msg']='1 message(s) successfully delivered to ' .count($emp_name_Array).' device(s)';
		 disphtml("main();");
	 }
	 else
	 {
		  $GLOBALS['err_msg']='message(s) deliver unsuccessful to ' .count($emp_name_Array).' device(s)';
		 disphtml("main();");
	 }
	//End of push notification
}
?>
<script>
	function display_result(){
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		if(document.getElementById("sale_access").value.search(/\S/) == -1){
			alert('Please Select Department');
			return false;
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		if(document.getElementById("message").value.search(/\S/) == -1){
			alert('Please input Message');
			return false;
		}
		//document.frmadd.submit();
		//return true;
	}
</script>
