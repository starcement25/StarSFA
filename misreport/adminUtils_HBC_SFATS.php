<?php
define("SITETITLE"," Welcome To ACEDNS PRODUCT Administrator Control Panel ");
define("ADMIN_CSS","https://salesmpower.acedns.in/css/adminStyle.css");
define("ADMIN_MASTER","admin_master");
error_reporting(0);


if($_SESSION['nick_name']!='' || $_REQUEST['nick_name']!=''){

	if($_REQUEST['nick_name']!='') $nick_name=strtoupper($_REQUEST['nick_name']);
	if($_SESSION['nick_name']!='') $nick_name=strtoupper($_SESSION['nick_name']);
		
	/*$linksetupadmin=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#") or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");*/
	
	$linksetupadmin=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234#","acedns_acednsproduct") or die("Setup Database Connection Error.");
	
	$sqlnickname="SELECT nick_name, remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsnickname=mysqli_query($linksetupadmin,$sqlnickname);
	$row_nick_name = mysqli_fetch_assoc($rsnickname);
	$cntnickname=mysqli_num_rows($rsnickname);
	$remote_db_access = $row_nick_name['remote_db_access'];
	
	$sqlstkaudit="SELECT stk_audit FROM menu_details WHERE nick_name='".$nick_name."'";
	$rsstkaudit=mysqli_query($linksetupadmin,$sqlstkaudit);
	$rowstkaudit=mysqli_fetch_assoc($rsstkaudit);
	$stk_audit=$rowstkaudit['stk_audit'];
	define('stk_audit',$stk_audit);
	
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
	//require("include/config-email-setup.php");
	
	if(sauda_allocation == 'yes'){
		require("include/saudacheck.php");
	}
}

function disphtml($what)
{
    require("include/dbcon.php");
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
<!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="mapfile.js"></script>
<script language="JavaScript" type="text/javascript" src="prototype.js"></script>-->
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
<body  <?php if($_SERVER['PHP_SELF']=='/misreport/adminAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/misreport/adminTransactionDetails.php' ||  $_SERVER['PHP_SELF']=='/misreport/adminCustomerLocation.php' ||  $_SERVER['PHP_SELF']=='/misreport/customerLocate.php'){ ?> onload="forload(Lat,Lon,Place,customer,route,emp_name,time);" onunload="GUnload()" <?php }
if($_SERVER['PHP_SELF']=='/misreport/adminRouteTracker.php' || $_SERVER['PHP_SELF']=='/misreport/adminRouteLocate.php' || $_SERVER['PHP_SELF']=='/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/misreport/showMultiRouteVIPL.php'){?> onload="forload(routes);" onunload="GUnload()" <?php }if($_SERVER['PHP_SELF']=='/misreport/adminMisReport.php'){?> onload="javascript:timedRefresh(300000);" <?php }?>>
<script language="JavaScript" type="text/javascript" src="adminEssential.js"></script>
<script language="JavaScript" type="text/javascript" src="mm_menu.js"></script>

<!--script type="text/javascript" src="popcalendar.js"></script!-->

<script language="JavaScript" src="calendar3.js"></script>

<script language="JavaScript1.2" type="text/javascript">mmLoadMenus();</script>
<?php
$sql_select_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$_SESSION['admin_login']."'";
@$res_select_emp_name = mysqli_query($link,$sql_select_emp_name);
@$row_select_emp_name = mysqli_fetch_assoc($res_select_emp_name);
$emp_name = $row_select_emp_name['emp_name'];
if($emp_name != '')
	$welcome_message = "Welcome ".$emp_name;
else if($_SESSION['admin_login'] == 'supervisor')
	$welcome_message = "Welcome Supervisor";
else if($_SESSION['admin_login'] == 'emovesfa_hr')
	$welcome_message = "Welcome HR";
else if(strtoupper($_SESSION['admin_login']) == 'ACCOUNTS')
	$welcome_message = "Welcome Accounts";
else if(strtoupper($_SESSION['admin_login']) == 'HBC')
	$welcome_message = "Welcome HBC";
else if(strtoupper($_SESSION['admin_login']) == 'SFATS')
	$welcome_message = "Welcome SFATS";
else if(strtoupper($_SESSION['admin_login']) == 'PRICEHBC')
	$welcome_message = "Welcome PRICEHBC";
else if(strtoupper($_SESSION['admin_login']) == 'PRICESFATS')
	$welcome_message = "Welcome PRICESFATS";
else if(strtoupper($_SESSION['admin_login']) == 'ED01')
	$welcome_message = "Welcome Rajiv";				
else
	$welcome_message = "Welcome Admin";
	
$sql_app_version = "SELECT version_code FROM app_version";
$res_app_version = mysqli_query($link,$sql_app_version);
$row_app_version = mysqli_fetch_assoc($res_app_version);
$app_version = $row_app_version['version_code'];

$sql_db_version = "SELECT version_code FROM db_version";
$res_db_version = mysqli_query($link,$sql_db_version);
$row_db_version = mysqli_fetch_assoc($res_db_version);
$db_version = $row_db_version['version_code'];

$sql_branch = "SELECT branch_code, branch_name FROM branch_master";
$res_branch = mysqli_query($link,$sql_branch);
$total_rows = mysqli_num_rows($res_branch);

if(sauda_allocation == 'yes'){
	if($_SESSION['admin_login'] != 'admin'){
		$sql_reporting_to = "SELECT emp_code FROM employee_master WHERE reporting_to = '' AND emp_code='".$_SESSION['admin_login']."'";
		$res_reporting_to = mysqli_query($link,$sql_reporting_to);
		$reporting_to_total = mysqli_num_rows($res_reporting_to);
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
					
					if(file_exists("/home/acedns/public_html/logo/$final_logo"))
					{
						$final_logo=$logo.'.png';
					}
					else
					{
						$final_logo='CSPL.png';
					}
					?>
					<td class="header" style="background: url(https://salesmpower.acedns.in/logo/<?=$final_logo?>) 2% 50% no-repeat #FFFFFF; background-size: 70px 40px;"" ><b><?php echo $_SESSION['admin_login'];?> Control Panel</b>
					<br>
					<a href="#" style="color: #e40000" onclick="javascript:logout();">
                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">APP Version: ".$app_version."<br /> DB Version: ".$db_version."</b></a>";?> 
                    <div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><?php 
					if($_SESSION['admin_login']!=''){ echo "<strong>".$welcome_message."</strong>"; } if(strtoupper($_SESSION['nick_name'])=='EMAMIT' && $_SESSION['admin_login']!=''){ ?><br /><font size="+2"><strong>Quality Testing</strong></font><?php }?></div></td>
				</tr>
				<tr>
                     <td class="menubar">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
                            	<?php 
								if(strtoupper($_SESSION['admin_login'])=="E0193")
								{?>
                                 <td>
                                <a href="sauda_report_main.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141404_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>
                                </td>
                                <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141767_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Sauda Download</a></td>
                                <?php
								}
								if(strtoupper($_SESSION['admin_login'])=="ED01")
								{?>
                                <td>
                                <a href="sauda_report_main.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141404_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>
                                </td>
                                <td>
                                <a href="depotwise_pricelist_report_verticalwise.php" name="link90" id="link90" onMouseOver="MM_showMenu(window.mm_menu_0716141190_0,0,26,null,'link90')" onMouseOut="MM_startTimeout();">Pricelist</a>
                                </td>
                                <?php
								}
								if(strtoupper($_SESSION['admin_login'])=='HBC'){?>
								<td>
                                <a href="depotwise_pricelist_report_verticalwise.php" name="link90" id="link90" onMouseOver="MM_showMenu(window.mm_menu_0716141190_0,0,26,null,'link90')" onMouseOut="MM_startTimeout();">Pricelist</a>
                                </td>
                                <td>
       <a href="adminMonthlyAttendencePrint.php" name="link24" id="link24" onMouseOver="MM_showMenu(window.mm_menu_0716141223_0,0,26,null,'link24')" onMouseOut="MM_startTimeout();">Download Data</a>
                                </td>
                                 <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
                                 <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141767_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Sauda Download</a></td>
                                 <td><a href="margin-data-download.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141765_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">Costing Report</a></td>
                                <?php }
								if(strtoupper($_SESSION['admin_login'])=='SFATS'){?>
                                <!--td><a href="sauda_report_main.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141404_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a><td-->
								<td><a href="employee_access_verticalwise.php" name="link172" id="link172"  onMouseOver="MM_showMenu(window.mm_menu_0716141173_0,0,26,null,'link172')" onMouseOut="MM_startTimeout();">Employee Access</a><td>
                                <td><a href="TD_allocation_main.php" name="link27" id="link27">TD Allocation</a>
                                <td><a href="sauda_main.php" name="link27" id="link27">Sauda Allocation</a></td>
                                <td><a href="pending_contract_ageing_report.php" name="link92" id="link92" onMouseOver="MM_showMenu(window.mm_menu_0716141892_0,0,26,null,'link92')" onMouseOut="MM_startTimeout();">Customize Report</a></td>
                                <td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link97" id="link97" onMouseOver="MM_showMenu(window.mm_menu_0716141797_0,0,26,null,'link97')" onMouseOut="MM_startTimeout();">Upload Data</a></td>
                                 <td><a href="sale-rate-data-download.php" name="link98" id="link98" onMouseOver="MM_showMenu(window.mm_menu_0716141798_0,0,26,null,'link98')" onMouseOut="MM_startTimeout();">Costing Report</a></td>
                                 <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" >Sauda Download</a></td>
                                  <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
								<!--td>
                                <a href="depotwise_pricelist_report_verticalwise.php" name="link90" id="link90" onMouseOver="MM_showMenu(window.mm_menu_0716141190_0,0,26,null,'link90')" onMouseOut="MM_startTimeout();">Pricelist</a>
                                </td>
                                <td>
       <a href="adminMonthlyAttendencePrint.php" name="link24" id="link24" onMouseOver="MM_showMenu(window.mm_menu_0716141223_0,0,26,null,'link24')" onMouseOut="MM_startTimeout();">Download Data</a>
                                </td>
                                 <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
                                 <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141767_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Sauda Download</a></td-->
                                <?php }
								if(strtoupper($_SESSION['admin_login'])=='PRICESFATS' || strtoupper($_SESSION['admin_login'])=='PRICEHBC'){?>
                                <td>
                                <a href="depotwise_pricelist_report_verticalwise.php" name="link90" id="link90" onMouseOver="MM_showMenu(window.mm_menu_0716141190_0,0,26,null,'link90')" onMouseOut="MM_startTimeout();">Pricelist</a>
                                </td>
                                <?php }
								if(strtoupper($_SESSION['nick_name'])=='EMAMI' && strtoupper($_SESSION['admin_login'])=='PRICESFATS'){?>
                                <td>
       <a href="adminCsvReadPricingGenerationDataFinal.php" name="link27" id="link27" >UPLOAD DATA</a>
                                </td>
                                <td><a href="generate_pricing_details_formulationSF.php" name="link77" id="link77" onMouseOver="MM_showMenu(window.mm_menu_0716141877_0,0,26,null,'link77')" onMouseOut="MM_startTimeout();">Pricing</a>
                                </td>
                                <td><a href="margin-data-download.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141765_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">Costing Report</a></td>
                                <td><a href="saudawise_quantity_edit_report.php" name="link20" id="link20">Edit Sauda</a></td>
                                 <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
                                <?php }
								if(strtoupper($_SESSION['nick_name'])=='EMAMI' && strtoupper($_SESSION['admin_login'])=='PRICEHBC'){?>
								<td>
       <!--a href="generate_pricing_details_formulation.php" name="link51" id="link51" onMouseOver="MM_showMenu(window.mm_menu_0716141851_0,0,26,null,'link51')" onMouseOut="MM_startTimeout();">Pricing</a-->
       <a href="generate_pricing_details_formulationmodified.php" name="link50" id="link50" onMouseOver="MM_showMenu(window.mm_menu_0716141850_0,0,26,null,'link50')" onMouseOut="MM_startTimeout();">Pricing</a>
                                </td>
                                 <td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link43" id="link43" onMouseOver="MM_showMenu(window.mm_menu_0716141743_0,0,26,null,'link43')" onMouseOut="MM_startTimeout();">Upload Data</a></td>
                                <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
                                 <td><a href="margin-data-download.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141765_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">Costing Report</a></td>
                                <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141767_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Sauda Download</a></td>
								<?php }
								else if(strtoupper($_SESSION['nick_name'])=='EMAMIT' && strtoupper($_SESSION['admin_login'])=='PRICEHBC'){?>
								<td>
       <a href="generate_pricing_details_formulationmodified.php" name="link50" id="link50" onMouseOver="MM_showMenu(window.mm_menu_0716141850_0,0,26,null,'link50')" onMouseOut="MM_startTimeout();">Pricing</a>
                                </td>
                                  <td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link63" id="link63" onMouseOver="MM_showMenu(window.mm_menu_0716141763_0,0,26,null,'link63')" onMouseOut="MM_startTimeout();">Upload Data</a></td>
                                <td><a href="dump_download_verticalwise.php">Dump Download</a></td>
                                 <td><a href="margin-data-download.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141765_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">Costing Report</a></td>
                                 <td><a href="CsvDownloadSaudaModified.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141767_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Sauda Download</a></td>
								<?php }
								?>
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