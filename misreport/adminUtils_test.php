<?php
define("SITETITLE"," Welcome To ACEDNS PRODUCT Administrator Control Panel ");
define("ADMIN_CSS","http://www.acedns.in/acednsproduct/css/adminStyle.css");
define("ADMIN_MASTER","admin_master");
error_reporting(0);

if($_SESSION['nick_name']!='' || $_REQUEST['nick_name']!=''){

	if($_REQUEST['nick_name']!='') $nick_name=strtoupper($_REQUEST['nick_name']);
	if($_SESSION['nick_name']!='') $nick_name=strtoupper($_SESSION['nick_name']);
		
	$linksetupadmin=mysqli_connect("localhost","acedns_dnsprod","dnsprod1234") or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");
	
	$sqlnickname="SELECT nick_name FROM user_details WHERE nick_name='".$nick_name."'";
	$rsnickname=mysqli_query($link,$sqlnickname,$linksetupadmin);
	$cntnickname=mysqli_num_rows($rsnickname);
	
	$sqlstkaudit="SELECT stk_audit FROM menu_details WHERE nick_name='".$nick_name."'";
	$rsstkaudit=mysqli_query($link,$sqlstkaudit,$linksetupadmin);
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
	require("include/config-email-setup.php");
	
	if(sauda_allocation == 'yes')
	require("include/saudacheck.php");
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
@$res_select_emp_name = mysqli_query($link,$sql_select_emp_name);
@$row_select_emp_name = mysqli_fetch_assoc($res_select_emp_name);
$emp_name = $row_select_emp_name['emp_name'];
if($emp_name != '')
	$welcome_message = "Welcome ".$emp_name;
else if($_SESSION['admin_login'] == 'supervisor')
	$welcome_message = "Welcome Supervisor";
else
	$welcome_message = "Welcome Admin";
	
$sql_app_version = "SELECT version_code FROM app_version";
$res_app_version = mysqli_query($link,$sql_app_version);
$row_app_version = mysqli_fetch_assoc($res_app_version);
$app_version = $row_app_version['version_code'];

$sql_branch = "SELECT branch_code, branch_name FROM branch_master";
$res_branch = mysqli_query($link,$sql_branch);
$total_rows = mysqli_num_rows($res_branch);
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<tr>
		<td colspan="2">
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
                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">App Version: ".$app_version."</b>"; }?> 
                    <div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><?php if($_SESSION['admin_login']!=''){ echo "<strong>".$welcome_message."</strong>"; } ?></div></td>
				</tr>
				<!--<tr>
					<td class="menubar">
						
						
					</td>
				</tr>
				<tr>
					<td style="height:5px">&nbsp;</td>
				</tr>-->
				
			</table>
		</td>
	</tr>
    <tr>
    	<td width="25%" bgcolor="#A92A61">
        	<div style="width:100%; position:fixed; background:#A92A61; top:170px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
								<?php 
							 if($_SESSION['admin_login']!='' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')
							{
								if(sauda_allocation == 'yes')
								{ ?>
								<a href="sauda_report_main.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141404_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>
								<?php }
								else
								{ 
									if(strtoupper($_SESSION['nick_name']) == 'STORE' || strtoupper($_SESSION['nick_name']) == 'LIPL')
									{?>
										<a href="adminMain.php" name="link36" id="link36" onMouseOver="MM_showMenu(window.mm_menu_0716141136_0,0,26,null,'link36')" onMouseOut="MM_startTimeout();">REPORT</a>
								<?php
									}
									else
									{?>
										<a href="adminMain.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141133_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>
									<?php }
								}
							}
?>
                                </td>
                                <tr>
                                <td><?php if($_SESSION['admin_login']=='admin' && strtoupper($_SESSION['nick_name'])=='RKBKL'){?><a href="adminAttendanceTracker.php" name="link10" id="link10" >Attendance Tracker</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                 <?php if($_SESSION['admin_login']=='superadmin'){?><a href="adminMain.php" name="link6" id="link6" onMouseOver="MM_showMenu(window.mm_menu_0716141833_0,0,26,null,'link6')" onMouseOut="MM_startTimeout();">REPORT</a><?php }?>
                                </td>
                                </tr>
                                <tr>
								<td>
								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){ if(vertical_fields == 'no'){?><a href="adminEmployeeAccess.php" name="link44" id="link44" onMouseOver="MM_showMenu(window.mm_menu_0716141144_0,0,26,null,'link44')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } else if(vertical_fields == 'yes' && sauda_allocation == 'yes') { ?><a href="employee_access.php" name="link4" id="link4"  onMouseOver="MM_showMenu(window.mm_menu_0716141101_0,0,26,null,'link4')" onMouseOut="MM_startTimeout();" >Employee Access</a><?php } else if(vertical_fields == 'yes'){?><a href="employee_access_verticalwise.php" name="link4" id="link4">Employee Access</a><?php } }?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if(($_SESSION['admin_login']=='admin' || strtolower($_SESSION['admin_login'])=='c0007') && ace_integration=='yes'){?><a href="adminCustomerAccess.php" name="link21" id="link21">Customer Access</a><?php }?></td>
                                </tr>
                                <tr>
                                 <td><?php if(($_SESSION['admin_login']=='admin' || strtolower($_SESSION['admin_login'])=='c0007') && ace_integration=='yes'){?><a href="adminProductAccess.php" name="link23" id="link23">Product Access</a><?php }?></td>
                                 </tr>
                                 <tr>
                                   <td><?php if($_SESSION['admin_login']=='admin' && (strtoupper($_SESSION['nick_name'])!='RKBK' && strtoupper($_SESSION['nick_name'])!='EMAMI' && strtoupper($_SESSION['nick_name'])!='EMAMIT' && strtoupper($_SESSION['nick_name'])!='STORE') && strtoupper($_SESSION['nick_name'])!='LIPL'){?><a href="adminCustomerMapping.php" name="link25" id="link25">Customer Employee Relation</a><?php }?></td>
                                 </tr>
                                 <tr>
                                   <td><?php if(sauda_allocation == 'yes'){if($_SESSION['admin_login']=='admin' || $_SESSION['flag'] == 'yes'){?><a href="sauda_main.php" name="link27" id="link27">Sauda Allocation</a><?php }}?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                <?php if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='EMAMI'){?><a href="depotwise_pricelist_report.php" name="link29" id="link29">Depotwise Pricelist</a><?php } ?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if($_SESSION['admin_login']=='admin' && business_prospect == 'yes'){?><a href="business_prospect.php" name="link28" id="link28" >Business Prospects</a><?php } ?>
                                </td>
                                </tr>
                                <tr>
                                   <td><?php if(($_SESSION['admin_login']=='admin' || $_SESSION['admin_login']=='c0007') && (strtoupper($_SESSION['nick_name'])=='MDPL' || 
								    strtoupper($_SESSION['nick_name'])=='OJBH' || strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT' || strtoupper($_SESSION['nick_name'])=='LTS' || 
									strtoupper($_SESSION['nick_name'])=='STORE' || strtoupper($_SESSION['nick_name'])=='LIPL' || strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='RUPAT' || strtoupper($_SESSION['nick_name'])=='KFPL' || strtoupper($_SESSION['nick_name'])=='MINU' || strtoupper($_SESSION['nick_name'])=='JPHARMA' || strtoupper($_SESSION['nick_name'])=='EMARK' || strtoupper($_SESSION['nick_name'])=='RKBK')){ if(sauda_allocation == 'no' && sale=='no'){?><a href="adminCsvReadIncremental.php" name="link26" id="link26">Upload Data</a><?php }else if(sauda_allocation == 'no' && sale=='yes'){?><a href="adminCsvReadIncrementalSalePurchase.php" name="link26" id="link26">Upload Data</a><?php }else if(sauda_allocation == 'yes' && strtoupper($_SESSION['nick_name'])!='EMAMIT'){ ?><a href="adminCsvReadIncrementalSauda.php" name="link26" id="link26" onMouseOver="MM_showMenu(window.mm_menu_0716141753_0,0,26,null,'link26')" onMouseOut="MM_startTimeout();">Upload Data</a><?php }
									else if(sauda_allocation == 'yes' && strtoupper($_SESSION['nick_name'])=='EMAMIT'){ ?><a href="adminCsvReadIncrementalSauda.php" name="link43" id="link43" onMouseOver="MM_showMenu(window.mm_menu_0716141743_0,0,26,null,'link43')" onMouseOut="MM_startTimeout();">Upload Data</a><?php } }?>
                                   </td>
                                   </tr>
                                <!--td><? /*if($_SESSION['admin_login']=='admin'){?><a href="zipUploadData.php" name="link7" id="link7">
                                Upload Data</a><?php }*/?></td-->
                                <!--td><?php /*if($_SESSION['admin_login']=='admin' && strtoupper($_SESSION['nick_name'])=='VIPL'){?><a href="CsvDownloadVIPL.php" name="link7" id="link7">Download Data</a><?php }*/?>
                                </td-->
                                <tr>
                                 <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) 
								 && (strtoupper($_SESSION['nick_name'])=='RKBK'  )){?><a href="adminAllTransactionDataDownload.php" name="link7" id="link7" onMouseOver="MM_showMenu(window.mm_menu_0716141933_0,0,26,null,'link7')" onMouseOut="MM_startTimeout();">Download</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])!='RKBK' && strtoupper($_SESSION['nick_name'])!='RKBKT') || $_SESSION['admin_login'] == 'supervisor'){ if(sauda_allocation == 'no' && survey=='no'){?><a href="adminMonthlyAttendencePrint.php" name="link19" id="link19" onMouseOver="MM_showMenu(window.mm_menu_0716141733_0,0,26,null,'link19')" onMouseOut="MM_startTimeout();">Download Data</a><?php
								}								else if(sauda_allocation == 'no' && survey=='yes' && $_SESSION['admin_login']!='E0002' && strtoupper($_SESSION['nick_name'])=='LIPL'){?>
                                <a href="exceldownloadSurvey.php" name="link31" id="link31" onMouseOver="MM_showMenu(window.mm_menu_0716141731_0,0,26,null,'link31')" onMouseOut="MM_startTimeout();">Transaction Download</a>
								<? }else if(sauda_allocation == 'no' && survey=='yes' && $_SESSION['admin_login']=='E0002' && strtoupper($_SESSION['nick_name'])=='LIPL'){?>
								 <a href="attendance_download.php" name="link41" id="link41">Attendance Download</a>
								<?php } 
							    else if(sauda_allocation == 'no' && survey=='yes'){?>
                                <a href="exceldownloadSurvey.php" name="link31" id="link31" onMouseOver="MM_showMenu(window.mm_menu_0716141731_0,0,26,null,'link31')" onMouseOut="MM_startTimeout();">Transaction Download</a>
								<?php }else {?><a href="adminMonthlyAttendencePrint.php" name="link24" id="link24" onMouseOver="MM_showMenu(window.mm_menu_0716141223_0,0,26,null,'link24')" onMouseOut="MM_startTimeout();">Download Data</a><?php } }?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if($_SESSION['admin_login']!='' && survey == 'yes'){?><a href="survey_output_edit.php" name="link30" id="link30" onMouseOver="MM_showMenu(window.mm_menu_0716141230_0,0,26,null,'link30')" onMouseOut="MM_startTimeout();" >Survey</a><?php } ?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php 
	if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])!='RKBK') && $_SESSION['admin_login']!='supervisor'){
	if(vertical_fields == 'yes' && $total_rows>0){
?>
	<a href="pushnotification_vertical_branch_select.php" name="link46" id="link46" onMouseOver="MM_showMenu(window.mm_menu_0716141046_0,0,26,null,'link46')" onMouseOut="MM_startTimeout();" >Broadcast</a><?php }else{?>
<a href="adminPushNotification.php" name="link22" id="link22" onMouseOver="MM_showMenu(window.mm_menu_0716141022_0,0,26,null,'link22')" onMouseOut="MM_startTimeout();" >Broadcast</a>
<?php }}?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VIPL' && $_SESSION['admin_login']=='admin'){?><a href="showMultiRouteVIPL.php" name="link8" id="link8">WOD</a><?php }?></td>
                                </tr>
                                <tr>
                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VALVO' && $_SESSION['admin_login']=='admin'){?>
                                <a href="adminRoutePlanAccess.php" name="link9" id="link9">Route Plan Access</a><?php }?></td>
                                </tr>
                                <tr>
                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VALVO' && $_SESSION['admin_login']=='admin'){?><a href="adminRoutePlanChange.php" name="link10" id="link10">Route Plan Change</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                 <td><?php if(strtoupper($_SESSION['nick_name'])=='AMPL' && $_SESSION['admin_login']=='admin'){?>
                                <a href="adminRoutePlanAccess.php" name="link13" id="link13">Route Plan Access</a><?php }?></td>
                                </tr>
                                <tr>
                                <td><?php if(strtoupper($_SESSION['nick_name'])=='AMPL' && $_SESSION['admin_login']=='admin'){?>
                                <a href="adminMailAccess.php" name="link14" id="link14">Mail Access</a><?php }?></td>
                                
                                <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK')){?><a href="adminLoyaltyReport.php" 
                                name="link11" id="link11" onMouseOver="MM_showMenu(window.mm_menu_0716141633_0,0,26,null,'link11')" onMouseOut="MM_startTimeout();">Loyalty Report</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK' || strtoupper($_SESSION['nick_name'])=='RKBKT')){?><a href="adminSaleReport.php" 
                                name="link13" id="link13" >Transaction Details</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                 <td><?php if((strtolower($_SESSION['admin_login'])=='c0007' || strtolower($_SESSION['admin_login'])=='c0008') && (strtoupper($_SESSION['nick_name'])=='RKBK' || strtoupper($_SESSION['nick_name'])=='RKBKT'))
								 {?><a href="adminTransactionDeletion.php" name="link12" id="link12" onMouseOver="MM_showMenu(window.mm_menu_0716141833_0,0,26,null,'link12')" onMouseOut="MM_startTimeout();">Transaction Deletion</a><?php }?></td>
                                 </tr>
                                 <tr>
                                 <td><?php if(sauda_allocation == 'yes' && strtolower($_SESSION['admin_login'])=='system'){ ?><a href="saudawise_quantity_edit_report.php" name="link20" id="link20">Edit Sauda</a><?php }?></td>
                                 </tr>
                                 <tr>
                                 <td>
                                 <?php if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='RUPA'){?><a href="rupa_verticalwise_report.php" name="link33" id="link33" onMouseOver="MM_showMenu(window.mm_menu_0716141839_0,0,26,null,'link33')" onMouseOut="MM_startTimeout();">Vertical Wise Report</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                 <?php if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='RUPA'){ ?><a href="excel_download_new_customer.php" name="link34" id="link34" onMouseOver="MM_showMenu(window.mm_menu_0716141834_0,0,26,null,'link34')" onMouseOut="MM_startTimeout();">Customize Report</a><?php } else if($_SESSION['admin_login']!='' && vertical_fields != 'yes'){ ?>
                                 <a href="excel_download_new_customer.php" name="link37" id="link37" onMouseOver="MM_showMenu(window.mm_menu_0716141837_0,0,26,null,'link37')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php } else if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT')){ ?>
                                 <a href="emami_daily_activity_analysis.php" name="link42" id="link42" onMouseOver="MM_showMenu(window.mm_menu_0716141842_0,0,26,null,'link42')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php } ?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                 <?php if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='STORE' || strtoupper($_SESSION['nick_name'])== 'LIPL')){?><a href="mall_add_edit.php" name="link35" id="link35" onMouseOver="MM_showMenu(window.mm_menu_0716141835_0,0,26,null,'link35')" onMouseOut="MM_startTimeout();">Master Input</a><?php }?>
                                </td>
                                </tr>
                                <tr>
                                <td>
								<?php 
							 if(strtoupper($_SESSION['admin_login']) =='SYSTEM')
							{
								if(sauda_allocation == 'yes')
								{ ?>
								<a href="product_promotion.php" name="link38" id="link38">Product Promotion</a>
								<?php }
							}
								?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                <?php
									if($_SESSION['admin_login']!=''){
											if(stk_audit == 'yes'){
								?>
                                <a href="stock_audit_report.php" name="link39" id="link39">Stock Audit</a>
                                <?php
											}}
								?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                <?php
									if($_SESSION['admin_login']!=''){
											if(tour_exp == 'yes'){
								?>
                                <a href="tourtravelexpensereport.php" name="link40" id="link40">Tours &amp; Travel Expenses</a>
                                <?php
											}}
								?>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                <?php
									if($_SESSION['admin_login']!=''){
											if(sauda_allocation == 'yes' && strtoupper($_SESSION['nick_name']) == 'EMAMIT'){
								?>
                                <a href="generate_pricing_details.php" name="link45" id="link45" onMouseOver="MM_showMenu(window.mm_menu_0716141845_0,0,26,null,'link45')" onMouseOut="MM_startTimeout();">Pricing</a>
                                <?php
											}}
								?>
                                </td>
                                </tr>
                                 <!--td><?php /*if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK')){?><a href="adminMonthlySaleReportDownload.php" 
                                name="link18" id="link18" >Sale & Purchase Download</a!--><?php }*/?>
                                <!--td><a href="#" name="link4" id="link4" onMouseOver="MM_showMenu(window.mm_menu_0716142019_0,0,26,null,'link4')" onMouseOut="MM_startTimeout();">Other Tools</a></td>
								<!--td><a href="#" name="link7" id="link3" onMouseOver="MM_showMenu(window.mm_menu_0716142232_0,0,26,null,'link7')" onMouseOut="MM_startTimeout();">Content Management</a></td-->
								<!--td><a href="#" name="link6" id="link6" onMouseOver="MM_showMenu(window.mm_menu_0716142530_0,0,26,null,'link6')" onMouseOut="MM_startTimeout();">Generate Report</a></td-->
							</tr>
						</table>
</div>
        </td>
        <td>
            <table>
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
}
?>