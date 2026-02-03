<?php
define("SITETITLE"," Welcome To ACEDNS PRODUCT Administrator Control Panel ");
define("ADMIN_CSS","http://www.acedns.in/acednsproduct/css/adminStyle.css");
define("ADMIN_MASTER","admin_master");
error_reporting(0);


if($_SESSION['nick_name']!='' || $_REQUEST['nick_name']!=''){

	if($_REQUEST['nick_name']!='') $nick_name=strtoupper($_REQUEST['nick_name']);
	if($_SESSION['nick_name']!='') $nick_name=strtoupper($_SESSION['nick_name']);
		
	$linksetupadmin=mysql_connect("localhost","acedns_dnsprod","dnsprod1234") or die("Setup Database Connection Error.");
	mysql_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");
	
	$sqlnickname="SELECT nick_name, remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsnickname=mysql_query($sqlnickname,$linksetupadmin);
	$row_nick_name = mysql_fetch_array($rsnickname);
	$cntnickname=mysql_num_rows($rsnickname);
	$remote_db_access = $row_nick_name['remote_db_access'];
	
	$sqlstkaudit="SELECT stk_audit FROM menu_details WHERE nick_name='".$nick_name."'";
	$rsstkaudit=mysql_query($sqlstkaudit,$linksetupadmin);
	$rowstkaudit=mysql_fetch_array($rsstkaudit);
	$stk_audit=$rowstkaudit['stk_audit'];
	define('stk_audit',$stk_audit);
	
	if($cntnickname<1){
		$GLOBALS['err_msg'] = "Invalid Nick name.";     
		disphtml("showLogin();");
		exit();
	}
	mysql_close($linksetupadmin);
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
   require("include/functions.php");
	require("include/config-email-setup.php");
	
	if(sauda_allocation == 'yes'){
		require("include/saudacheck.php");
	}
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
<?php if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminAttendanceLocate.php'  || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/customerLocate.php') 
{
?>	
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfile.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>
<?php }
if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminRouteTracker.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminRouteLocate.php' )
{
?>	
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE&amp;sensor=true" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfilemultiple.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>
<?php } 
if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/showMultiRouteVIPL.php'){
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
<body  <?php if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminTransactionDetails.php' ||  $_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminCustomerLocation.php' ||  $_SERVER['PHP_SELF']=='/acednsproduct/misreport/customerLocate.php'){ ?> onload="forload(Lat,Lon,Place,customer,route,emp_name,time);" onunload="GUnload()" <?php }
if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminRouteTracker.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminRouteLocate.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/acednsproduct/misreport/showMultiRouteVIPL.php'){?> onload="forload(routes);" onunload="GUnload()" <?php }if($_SERVER['PHP_SELF']=='/acednsproduct/misreport/adminMisReport.php'){?> onload="javascript:timedRefresh(300000);" <?php }?>>
<script language="JavaScript" type="text/javascript" src="adminEssential.js"></script>
<script language="JavaScript" type="text/javascript" src="mm_menu.js"></script>

<!--script type="text/javascript" src="popcalendar.js"></script!-->

<script language="JavaScript" src="calendar3.js"></script>

<script language="JavaScript1.2" type="text/javascript">mmLoadMenus();</script>
<?php
$sql_select_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$_SESSION['admin_login']."'";
@$res_select_emp_name = mysql_query($sql_select_emp_name);
@$row_select_emp_name = mysql_fetch_array($res_select_emp_name);
$emp_name = $row_select_emp_name['emp_name'];
if($emp_name != '')
	$welcome_message = "Welcome ".$emp_name;
else if($_SESSION['admin_login'] == 'supervisor')
	$welcome_message = "Welcome Supervisor";
else if($_SESSION['admin_login'] == 'emovesfa_hr')
	$welcome_message = "Welcome HR";	
else
	$welcome_message = "Welcome Admin";
	
$sql_app_version = "SELECT version_code FROM app_version";
$res_app_version = mysql_query($sql_app_version);
$row_app_version = mysql_fetch_array($res_app_version);
$app_version = $row_app_version['version_code'];

$sql_db_version = "SELECT version_code FROM db_version";
$res_db_version = mysql_query($sql_db_version);
$row_db_version = mysql_fetch_array($res_db_version);
$db_version = $row_db_version['version_code'];

$sql_branch = "SELECT branch_code, branch_name FROM branch_master";
$res_branch = mysql_query($sql_branch);
$total_rows = mysql_num_rows($res_branch);

if(sauda_allocation == 'yes'){
	if($_SESSION['admin_login'] != 'admin'){
		$sql_reporting_to = "SELECT emp_code FROM employee_master WHERE reporting_to = '' AND emp_code='".$_SESSION['admin_login']."'";
		$res_reporting_to = mysql_query($sql_reporting_to);
		$reporting_to_total = mysql_num_rows($res_reporting_to);
		//echo "L".$reporting_to_total;
	}
}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
                	<?php $logo=strtoupper($_SESSION['nick_name']);
					$final_logo=$logo.'.png';
					
					if(file_exists("/home/acedns/public_html/acednsproduct/logo/$final_logo"))
					{
						$final_logo=$logo.'.png';
					}
					else
					{
						$final_logo='CSPL.png';
					}
					?>
					<td class="header" style="background: url(http://www.acedns.in/acednsproduct/logo/<?=$final_logo?>) 2% 50% no-repeat #FFFFFF; background-size: 70px 40px;"" ><b>Administrator Control Panel</b>
					<br>
					<? if($_SESSION['admin_login']!=''){?><a href="#" style="color: #e40000" onclick="javascript:logout();">
                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">APP Version: ".$app_version."<br /> DB Version: ".$db_version."</b>"; }?> 
                    <div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><?php if($_SESSION['admin_login']!=''){ echo "<strong>".$welcome_message."</strong>"; } ?></div></td>
				</tr>
				<tr>
					<td class="menubar">
						
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){?><a href="#" name="link69" id="link69" onMouseOver="MM_showMenu(window.mm_menu_0716141869_0,0,26,null,'link69')" onMouseOut="MM_startTimeout();">KIOSK</a><?php } ?>
                                </td>
                                <td>
								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){?><a href="adminEmployeeAccess.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141167_0,26,null,'link67')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } ?>
                                </td>
                                <td>
								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){?><a href="kiosk_entry_form.php" name="">Employee Entry Form</a><?php } ?>
                                </td>
                                 <td>
								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){?><a href="adminAttendanceTracker.php" name="" >Attendance Tracker</a><?php } ?>
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
mysql_close($link);
ob_end_flush();
}
?>