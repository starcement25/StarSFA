<?php
define("SITETITLE"," Welcome To ACEDNS PRODUCT Administrator Control Panel ");
define("ADMIN_CSS","http://salesmpower.acedns.in/css/adminStyle.css");
define("ADMIN_MASTER","admin_master");
error_reporting(0);


if($_SESSION['nick_name']!='' || $_REQUEST['nick_name']!=''){

	if($_REQUEST['nick_name']!='') $nick_name=strtoupper($_REQUEST['nick_name']);
	if($_SESSION['nick_name']!='') $nick_name=strtoupper($_SESSION['nick_name']);
		
	$linksetupadmin=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#") or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");
	
	$sqlnickname="SELECT nick_name, remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsnickname=mysqli_query($link,$sqlnickname,$linksetupadmin);
	$row_nick_name = mysqli_fetch_assoc($rsnickname);
	$cntnickname=mysqli_num_rows($rsnickname);
	$remote_db_access = $row_nick_name['remote_db_access'];
	if($cntnickname<1){
		$GLOBALS['err_msg'] = "Invalid Nick name.";     
		disphtml("showLogin();");
		exit();
	}
	mysqli_close($linksetupadmin);
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
   require("include/functions.php");
	require("include/config-email-setup.php");
}

function disphtml($what)
{
	//echo $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=SITETITLE?></title>
<link href="<?=ADMIN_CSS?>" rel="stylesheet" type="text/css" />
<?php if($_SERVER['PHP_SELF']=='/misreport/adminAttendanceLocate.php'  || $_SERVER['PHP_SELF']=='/misreport/customerLocate.php') 
{
?>	
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfile.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>
<?php }
if($_SERVER['PHP_SELF']=='/misreport/adminRouteTracker.php' || $_SERVER['PHP_SELF']=='/misreport/adminRouteLocate.php' )
{
?>	
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE&amp;sensor=true" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfilemultiple.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>
<?php } 
if($_SERVER['PHP_SELF']=='/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/misreport/showMultiRouteVIPL.php'){
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE
&amp;sensor=true" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfilemultipleAttendance.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>
<?php }?>
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
<script type="text/javascript" src="jquery.freezeheader.js"></script>
<script type="text/javascript">
$(document).ready(function(){ 
    $("table").freezeHeader({ top: true, left: true }); 
}); 
</script> 

</head>
<form name="frm_logout"	action="login.php" method="post">
<input name="mode" type="hidden" value="logout">
</form>
<body>
<script language="JavaScript" type="text/javascript" src="adminEssential.js"></script>
<script language="JavaScript" type="text/javascript" src="mm_menu.js"></script>

<!--script type="text/javascript" src="popcalendar.js"></script!-->

<script language="JavaScript" src="calendar3.js"></script>

<script language="JavaScript1.2" type="text/javascript">mmLoadMenus();</script>
<?php
$sql_select_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$_SESSION['admin_login']."'";
@$res_select_customer_name = mysqli_query($link,$sql_select_customer_name);
@$row_select_customer_name = mysqli_fetch_assoc($res_select_customer_name);
$customer_name = $row_select_customer_name['customer_name'];
if($customer_name != '')
	$welcome_message = "Welcome ".$customer_name;

	
$sql_app_version = "SELECT version_code FROM app_version";
$res_app_version = mysqli_query($link,$sql_app_version);
$row_app_version = mysqli_fetch_assoc($res_app_version);
$app_version = $row_app_version['version_code'];

$sql_db_version = "SELECT version_code FROM db_version";
$res_db_version = mysqli_query($link,$sql_db_version);
$row_db_version = mysqli_fetch_assoc($res_db_version);
$db_version = $row_db_version['version_code'];


?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
                	<?php $logo=strtoupper($_SESSION['nick_name']);
					$final_logo=$logo.'.png';
					
					if(file_exists("/home/acedns/public_html/logo/$final_logo"))
					{
						$final_logo=$logo.'.png';
					}
					else
					{
						$final_logo='CSPL.png';
					}
					?>
					<td class="header" style="background: url(http://salesmpower.acedns.in/logo/<?=$final_logo?>) 2% 50% no-repeat #FFFFFF; background-size: 70px 40px;"" ><b>Vendor Control Panel</b>
					<br>
					<a href="#" style="color: #e40000" onclick="javascript:logout();">
                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">APP Version: ".$app_version."<br /> DB Version: ".$db_version."</b></a>";?> 
                    <div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><?php if($_SESSION['admin_login']!=''){ echo "<strong>".$welcome_message."</strong>"; } ?></div></td>
				</tr>
				<tr>
                    <td class="menubar">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
								<a href="order_download_distributor.php" name="link60" id="link60" >Order Report</a>
                                </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="height:5px">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" ><?=eval($what);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="bottom" align="center" height="20">
						<font style="color:#0A246A;size=1;face:Verdana;">Copyright &copy; <?=date('Y');?> - <?=(date('Y')+1);?>  - All Rights Reserved</font>
						<br><a href="" target="_blank" class="ll">Site By: Coral Software</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
<? 
mysqli_close($link);
ob_end_flush();
}
?>