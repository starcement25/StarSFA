<?php
define("SITETITLE"," Welcome To ACEDNS PRODUCT Administrator Control Panel ");

define("ADMIN_CSS","http://salesmpower.acedns.in/css/adminStyle.css");

define("ADMIN_MASTER","admin_master");

error_reporting(0);



if($_SESSION['nick_name']!='' || $_REQUEST['nick_name']!=''){



	if($_REQUEST['nick_name']!='') $nick_name=strtoupper($_REQUEST['nick_name']);

	if($_SESSION['nick_name']!='') $nick_name=strtoupper($_SESSION['nick_name']);



	$linksetupadmin=mysql_connect("localhost","acedns_dnsprod","dnsprod1234#") or die("Setup Database Connection Error.");

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

	if(strtoupper($_SESSION['nick_name'])=='KHMER')

	{

		$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";

		$rsempfunctionality=mysql_query($sqlempfunctionality);

		$rowempfunctionality=mysql_fetch_array($rsempfunctionality);

		$functionality=$rowempfunctionality['functionality'];

		$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];

	}
	/*if(strtoupper($_SESSION['nick_name'])=='ASL')

	{
		$url="http://salesmpower.acedns.in/setbargainsettlement.php";
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);

		//curl_setopt($ch,CURLOPT_POSTFIELDS, "$branch_ins_fields_string");
		//execute post
		$result = curl_exec($ch);
		//echo $result;
		print_r($result);
		//close connection
		curl_close($ch);
	}*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?=SITETITLE?></title>

<link href="<?=ADMIN_CSS?>" rel="stylesheet" type="text/css" />

<?php if(($_SERVER['PHP_SELF']=='/misreport/adminAttendanceLocate.php'  || $_SERVER['PHP_SELF']=='/misreport/customerLocate.php' ) && DCR_map=='yes')

{

?>

<!--script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE" type="text/javascript"></script-->

<!--script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE&callback=initMap" type="text/javascript"></script-->

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo&callback=initMap" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript" src="mapfile.js"></script>

<script language="JavaScript" type="text/javascript" src="prototype.js"></script>

<?php }

if(strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START'){?>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo&callback=initMap" type="text/javascript"></script>

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

if(($_SERVER['PHP_SELF']=='/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/misreport/showMultiRouteVIPL.php')  && DCR_map=='yes'){

?>

<!--script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyAoIVUvCmDTsiZNKFzngR1u21QrNIIbYiE

&amp;sensor=true" type="text/javascript"></script-->

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo&callback=initMap" type="text/javascript"></script>

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

<body  <?php if($_SERVER['PHP_SELF']=='/misreport/adminTransactionDetails.php' ||  $_SERVER['PHP_SELF']=='/misreport/adminCustomerLocation.php' ||  $_SERVER['PHP_SELF']=='/misreport/customerLocate.php'){ ?> onload="forload(Lat,Lon,Place,customer,route,emp_name,time);" onunload="GUnload()" <?php }

if($_SERVER['PHP_SELF']=='/misreport/adminAttendanceLocate.php'){?>onload="forload(Lat,Lon,Place,customer,route,emp_name,time,trans_id_parts);" onunload="GUnload()" <?php }

if($_SERVER['PHP_SELF']=='/misreport/adminRouteTracker.php' || $_SERVER['PHP_SELF']=='/misreport/adminRouteLocate.php' || $_SERVER['PHP_SELF']=='/misreport/adminMultiAttendanceLocate.php' || $_SERVER['PHP_SELF']=='/misreport/showMultiRouteVIPL.php'){?> onload="forload(routes);" onunload="GUnload()" <?php }if($_SERVER['PHP_SELF']=='/misreport/adminMisReport.php'){?> onload="javascript:timedRefresh(300000);" <?php }?>>

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

	

if(strtoupper($_SESSION['admin_id']) == '38')

	$welcome_message = "Welcome GMHBC";	

else if(strtoupper($_SESSION['admin_id']) == '39')

	$welcome_message = "Welcome GMSFATS";

	



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

/*if(check_in_out == 'yes' && strtoupper($_SESSION['nick_name'])!='STAR')

{

	$sqlcheckinout="SELECT trans_id,check_in_time FROM `check_in_out_details` WHERE 

					DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')!=SUBSTRING(`check_in_time`,1,10)";

	$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select checkin out: ".$sqlcheckinout);

	while($rowcheckinout=mysql_fetch_array($rscheckinout))

	{

		$trans_id=$rowcheckinout['trans_id'];

		$check_in_time=$rowcheckinout['check_in_time'];

		

		$trans_id_prefix_parts=substr($trans_id,0,7);

		$check_in_time_replace=str_replace('-','',$check_in_time);

		$check_in_time_replace=str_replace(' ','',$check_in_time_replace);

		$check_in_time_replace=str_replace(':','',$check_in_time_replace);

		

		$trans_id_suffix_parts=$check_in_time_replace;

		$final_trans_id=$trans_id_prefix_parts.$trans_id_suffix_parts;

		

		$sqlupdatecheckInout="UPDATE `check_in_out_details` SET trans_id='".$final_trans_id."' WHERE trans_id='".$trans_id."'";

		mysql_query($sqlupdatecheckInout);

		$sqlupdatelocation="UPDATE location SET trans_id='".$final_trans_id."' WHERE trans_id='".$trans_id."'";

		mysql_query($sqlupdatelocation);

	}

}*/

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

					<td class="header" style="background: url(http://salesmpower.acedns.in/logo/<?=$final_logo?>) 2% 50% no-repeat #FFFFFF; background-size: 70px 40px;"" ><b>Administrator Control Panel</b>

					<br>

					<? if($_SESSION['admin_login']=='admin'){?><a href="#" style="color: #e40000" onclick="javascript:logout();">

                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">APP Version: ".$app_version."<br /> DB Version: ".$db_version."</b><br /><a href=\"adminChangepassword.php\" >

					<b style=\"color:#A92A61;\">Change Password</b></a>"; }else if($_SESSION['admin_login']!='admin' && $_SESSION['admin_login']!=''){?><a href="#" style="color: #e40000" onclick="javascript:logout();">

                    <img src="images/log-out.png" alt="Logout" /></a><br /><b style="color:#A92A61;">Logout</b><br /><?php echo "<b style=\"font-size:9px;\">APP Version: ".$app_version."<br /> DB Version: ".$db_version."</b></a>"; }?>

                    <div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><?php if($_SESSION['admin_login']!=''){ echo "<strong>".$welcome_message."</strong>"; } if(strtoupper($_SESSION['nick_name'])=='EMAMIT' && $_SESSION['admin_login']!=''){ ?><br /><font size="+2"><strong>Quality Testing</strong></font><?php }?></div></td>

				</tr>

				<tr>

					<td class="menubar">



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

									if(strtoupper($_SESSION['nick_name']) == 'STORE1' || strtoupper($_SESSION['nick_name']) == 'LIPL' )

									{?>

										<a href="adminMain.php" name="link36" id="link36" onMouseOver="MM_showMenu(window.mm_menu_0716141136_0,0,26,null,'link36')" onMouseOut="MM_startTimeout();">REPORT</a>

								<?php

									}

									else if(survey == 'yes' && check_in_out == 'yes' && strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'CASHLESS' && strtoupper($_SESSION['nick_name']) != 'AJANTA'){?>

										<a href="adminMain.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141165_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">REPORT</a>

									<?php

									}

									else if(survey == 'yes' && strtoupper($_SESSION['nick_name']) != 'STAR' &&

									strtoupper($_SESSION['nick_name']) != 'CASHLESS' && strtoupper($_SESSION['nick_name']) != 'RUPA' && strtoupper($_SESSION['nick_name']) != 'AJANTA'){?>

										<a href="adminMain.php" name="link55" id="link55" onMouseOver="MM_showMenu(window.mm_menu_0716141155_0,0,26,null,'link55')" onMouseOut="MM_startTimeout();">REPORT</a>

									<?php

									}

									else if(survey == 'yes' && strtoupper($_SESSION['nick_name']) == 'CASHLESS'){?>

										<a href="adminMain.php" name="link61" id="link61" onMouseOver="MM_showMenu(window.mm_menu_0716141161_0,0,26,null,'link61')" onMouseOut="MM_startTimeout();">REPORT</a>

									<?php

									}
									else if(strtoupper($_SESSION['nick_name']) == 'ILS'  || strtoupper($_SESSION['nick_name']) == 'PROPELLO' ){?>

										<a href="adminAttendanceTracker.php" name="link61" id="link61" onMouseOver="MM_showMenu(window.mm_menu_0716141161_0,0,26,null,'link61')" onMouseOut="MM_startTimeout();">REPORT</a>

									<?php

									}

									else if(survey == 'yes' && (strtoupper($_SESSION['nick_name']) == 'STAR') && $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do'){?>

										<a href="adminMain.php" name="link59" id="link59" onMouseOver="MM_showMenu(window.mm_menu_0716141159_0,0,26,null,'link59')" onMouseOut="MM_startTimeout();">REPORT</a>

									<?php

									}

									else

									{

										if(check_in_out == 'yes'){?>

											<a href="adminMisReport.php" name="link53" id="link53" onMouseOver="MM_showMenu(window.mm_menu_0716141153_0,0,26,null,'link53')" onMouseOut="MM_startTimeout();">REPORT</a><?php



										} else if(survey != 'yes' && strtoupper($_SESSION['nick_name']) != 'HALDIRAM'){?>

										<a href="adminMain.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141133_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>

							  	  <?php } else if(survey != 'yes' && strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){?>

										<a href="adminMain.php" name="link74" id="link74" onMouseOver="MM_showMenu(window.mm_menu_0716141174_0,0,26,null,'link74')" onMouseOut="MM_startTimeout();">REPORT</a>

							  	  <?php }else {?>

										<a href="adminMain.php" name="link5" id="link5" onMouseOver="MM_showMenu(window.mm_menu_0716141133_0,0,26,null,'link5')" onMouseOut="MM_startTimeout();">REPORT</a>

							  	  <?php }

								    }

								}

							}

?>

                                </td>

                                <td><?php if($_SESSION['admin_login']=='admin' && strtoupper($_SESSION['nick_name'])=='RKBKL'){?><a href="adminAttendanceTracker.php" name="link10" id="link10" >Attendance Tracker</a><?php }?>

                                </td>

                                <td>

                                 <?php if($_SESSION['admin_login']=='superadmin'){?><a href="adminMain.php" name="link6" id="link6" onMouseOver="MM_showMenu(window.mm_menu_0716141833_0,0,26,null,'link6')" onMouseOut="MM_startTimeout();">REPORT</a><?php }?>

                                </td>



								<td>

								<?php if(($_SESSION['admin_login']=='admin' || strtolower(substr($_SESSION['admin_login'],0,1))=='c')){ 

								if(vertical_fields == 'no' && strtoupper($_SESSION['nick_name']) == 'LIPL'){?><a href="adminEmployeeAccess.php" name="link44" id="link44" onMouseOver="MM_showMenu(window.mm_menu_0716141144_0,0,26,null,'link44')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } 

								else if(vertical_fields == 'no' && strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'ASL'){?><a href="adminEmployeeAccess.php" name="link49" id="link49"  onMouseOver="MM_showMenu(window.mm_menu_0716141149_0,0,26,null,'link49')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } 

								else if(vertical_fields == 'no' && strtoupper($_SESSION['nick_name']) == 'STAR' && $_SESSION['admin_login']!='emovesfa_do'){?><a href="adminEmployeeAccess_selectionwise.php" name="link56" id="link56"  onMouseOver="MM_showMenu(window.mm_menu_0716141156_0,0,26,null,'link56')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } 

								else if(vertical_fields == 'yes' && sauda_allocation == 'yes') { ?><a href="employee_access.php" name="link4" id="link4"  onMouseOver="MM_showMenu(window.mm_menu_0716141101_0,0,26,null,'link4')" onMouseOut="MM_startTimeout();" >Employee Access</a><?php } 

								else if(vertical_fields == 'yes'){?><a href="employee_access_verticalwise.php" name="link50" id="link50"  onMouseOver="MM_showMenu(window.mm_menu_0716141150_0,0,26,null,'link50')" onMouseOut="MM_startTimeout();">Employee Access</a>

								<?php } 

								else if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='STAR'){?><a href="adminEmployeeAccess.php" name="link71" id="link71"  onMouseOver="MM_showMenu(window.mm_menu_0716141171_0,0,26,null,'link71')" onMouseOut="MM_startTimeout();">Employee Access</a>

                               <?php }

							   else if(strtoupper($_SESSION['nick_name'])=='ASL'){?><a href="adminEmployeeAccess.php" name="link123" id="link123"  onMouseOver="MM_showMenu(window.mm_menu_0716141123_0,0,26,null,'link123')" onMouseOut="MM_startTimeout();">Employee Access</a>

                               <?php }

							   }?>

                                <?php if((strtoupper($_SESSION['nick_name'])=='EMAMI')  && $_SESSION['admin_login']=='E0076'){?><a href="employee_access_verticalwise.php" name="link172" id="link172"  onMouseOver="MM_showMenu(window.mm_menu_0716141173_0,0,26,null,'link172')" onMouseOut="MM_startTimeout();">Employee Access</a>

                                 <?php }else if(strtoupper($_SESSION['nick_name'])=='EMAMIT' && $_SESSION['admin_login']=='E0076'){?>

                                 <a href="employee_access_verticalwise.php" name="link172" id="link172"  onMouseOver="MM_showMenu(window.mm_menu_0716141173_0,0,26,null,'link172')" onMouseOut="MM_startTimeout();">Employee Access</a>

                                 <?php }

								 else if((strtoupper($_SESSION['nick_name'])=='EMAMIT' || strtoupper($_SESSION['nick_name'])=='EMAMI') 

								 && ($_SESSION['admin_login']=='E0042' || strtoupper($_SESSION['admin_login'])=="GMHBC")){?><a href="adminEmployeeAccess.php" name="link172" id="link172"  onMouseOver="MM_showMenu(window.mm_menu_0716141173_0,0,26,null,'link172')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } 

								else if(strtoupper($_SESSION['nick_name'])=='RUPA' && $_SESSION['admin_login']=='E1093'){?><a href="employee_access_verticalwise.php" name="link50" id="link50"  onMouseOver="MM_showMenu(window.mm_menu_0716141150_0,0,26,null,'link50')" onMouseOut="MM_startTimeout();">Employee Access</a><?php } 



								 ?>

                                </td>

                                <td><?php if(($_SESSION['admin_login']=='admin' || strtolower($_SESSION['admin_login'])=='c0007') && ace_integration=='yes'){?><a href="adminCustomerAccess.php" name="link21" id="link21">Customer Access</a><?php }?></td>

                                 <td><?php if(($_SESSION['admin_login']=='admin' || strtolower($_SESSION['admin_login'])=='c0007') && ace_integration=='yes'){?><a href="adminProductAccess.php" name="link23" id="link23">Product Access</a><?php } else if(($_SESSION['admin_login']=='admin' || strtolower($_SESSION['admin_login'])=='c0007') && strtoupper($_SESSION['nick_name'])=='MAITHAN'){?><a href="adminProductAccess.php" name="link23" id="link23">Product Access</a><?php }?></td>

                                   <!--<td><?php /*if(($_SESSION['admin_login']=='admin' && (strtoupper($_SESSION['nick_name'])!='RKBK' && strtoupper($_SESSION['nick_name'])!='EMAMI' && strtoupper($_SESSION['nick_name'])!='DNV' && strtoupper($_SESSION['nick_name'])!='HALDIRAM' && strtoupper($_SESSION['nick_name'])!='EMAMIT' && strtoupper($_SESSION['nick_name'])!='STORE') && strtoupper($_SESSION['nick_name'])!='LIPL' && strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='PARLE')|| (strtoupper($_SESSION['nick_name'])=='CDNS' && strtoupper($_SESSION['admin_login'])=='E0056')){?><a href="adminCustomerMapping.php" name="link25" id="link25">Customer Employee Relation</a><?php }*/?></td>-->

                                    <?php if(strtoupper($_SESSION['nick_name'])=='EMAMIT' || strtoupper($_SESSION['nick_name'])=='ASL') { ?>

                                   <td><a href="TD_allocation_main.php" name="link27" id="link27">TD Allocation</a>

                                </td>

                                  <?php } elseif(strtoupper($_SESSION['nick_name'])=='EMAMI') {?>

                                   <td><a href="TD_allocation_main.php" name="link27" id="link27">TD Allocation</a>

                                </td>

                                 <?php } ?>

                                   <?php if(strtoupper($_SESSION['nick_name'])=='EMAMIT' && $_SESSION['admin_login']!='E0042') { ?>

                                   <td><?php if(sauda_allocation == 'yes'){if($_SESSION['admin_login']=='admin' || $_SESSION['flag'] == 'yes'){?><a href="sauda_main.php" name="link27" id="link27">Sauda Allocation</a><?php }}?>

                                </td>

                                <?php } elseif(strtoupper($_SESSION['nick_name'])=='EMAMI') {?>

                                <td><?php if(sauda_allocation == 'yes'){if($_SESSION['admin_login']=='admin' || $_SESSION['flag'] == 'yes'){?><a href="sauda_main.php" name="link27" id="link27">Sauda Allocation</a><?php }}?>

                                </td>

                                <?php } ?>

                                <td>

                                <?php //if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='EMAMI'){?><!--<a href="depotwise_pricelist_report.php" name="link29" id="link29" onMouseOver="MM_showMenu(window.mm_menu_0716141129_0,0,26,null,'link29')" onMouseOut="MM_startTimeout();">Pricelist</a>--><?php //} ?>

                                <?php if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT')){?><a href="depotwise_pricelist_report_verticalwise.php" name="link90" id="link90" onMouseOver="MM_showMenu(window.mm_menu_0716141190_0,0,26,null,'link90')" onMouseOut="MM_startTimeout();">Pricelist</a><?php } ?>

                                </td>

                                <td><?php if(strtoupper($_SESSION['admin_login'])=='ADMIN' && business_prospect == 'yes' && strtoupper($_SESSION['nick_name'])!='CDNS' && strtoupper($_SESSION['nick_name'])!='AJANTA'){?><a href="business_prospect.php" name="link28" id="link28" >Business Prospects</a><?php } else if(strtoupper($_SESSION['nick_name']) == 'CDNS' && business_prospect == 'yes' && (strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='E0056')){?>

                                <a href="business_prospect.php" name="link28" id="link28" >Business Prospects</a>

                                <? } ?>

                                </td>

                                   <td><?php  if($functionality!='DOS'){
									   if((strtolower($_SESSION['admin_login'])=='admin' || $_SESSION['admin_login']=='c0007' ) && 
									   (strtoupper($_SESSION['nick_name'])=='MDPL' ||

								    strtoupper($_SESSION['nick_name'])=='OJBH'  || strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT' || strtoupper($_SESSION['nick_name'])=='LTS' ||

									strtoupper($_SESSION['nick_name'])=='CONCEPT' || strtoupper($_SESSION['nick_name'])=='LIPL' || strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='RUPAT' || strtoupper($_SESSION['nick_name'])=='KFPL' || strtoupper($_SESSION['nick_name'])=='MINU' || strtoupper($_SESSION['nick_name'])=='JPHARMA' || strtoupper($_SESSION['nick_name'])=='EMARK' || strtoupper($_SESSION['nick_name'])=='TT' || strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='EGEN' || strtoupper($_SESSION['nick_name'])=='CASHLESS' || strtoupper($_SESSION['nick_name'])=='PARLE' || strtoupper($_SESSION['nick_name'])=='RKBK' || strtoupper($_SESSION['nick_name'])=='YELLOWSTR'  
									|| strtoupper($_SESSION['nick_name'])=='SANICO' || strtoupper($_SESSION['nick_name'])=='GLOSTER' || strtoupper($_SESSION['nick_name'])=='VIPL' || strtoupper($_SESSION['nick_name'])=='JASWANI' || strtoupper($_SESSION['nick_name'])=='KUNJSALE' || strtoupper($_SESSION['nick_name'])=='CDNS' || strtoupper($_SESSION['nick_name'])=='SHAKERS' || strtoupper($_SESSION['nick_name'])=='GANESH' || strtoupper($_SESSION['nick_name'])=='GWAAL' || strtoupper($_SESSION['nick_name'])=='MAITHAN' || strtoupper($_SESSION['nick_name'])=='DAKSH' || strtoupper($_SESSION['nick_name'])=='SMPDEMO' || strtoupper($_SESSION['nick_name'])=='SAVERA' || strtoupper($_SESSION['nick_name'])=='SKIPPER'  || strtoupper($_SESSION['nick_name'])=='SWETA' || strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='OSHEA' || strtoupper($_SESSION['nick_name'])=='KFTPL' || strtoupper($_SESSION['nick_name'])=='KARMA' || strtoupper($_SESSION['nick_name'])=='ABDOS' || strtoupper($_SESSION['nick_name'])=='SHYAM' || strtoupper($_SESSION['nick_name'])=='NORDUSK' || strtoupper($_SESSION['nick_name'])=='DEEDO' 

									|| strtoupper($_SESSION['nick_name'])=='MOREISH' || strtoupper($_SESSION['nick_name'])=='SUPRA' || strtoupper($_SESSION['nick_name'])=='EDIBLEH' || strtoupper($_SESSION['nick_name'])=='JAYATRI' || strtoupper($_SESSION['nick_name'])=='PRAKASH' || strtoupper($_SESSION['nick_name'])=='KHMER' || strtoupper($_SESSION['nick_name'])=='PANORAMA' || strtoupper($_SESSION['nick_name'])=='RDG' || 

									strtoupper($_SESSION['nick_name'])=='AIDIAS' || strtoupper($_SESSION['nick_name'])=='ARCHITA' || strtoupper($_SESSION['nick_name'])=='AUTOCRAFT' || strtoupper($_SESSION['nick_name'])=='DAA' || strtoupper($_SESSION['nick_name'])=='XIAOMI' 

									|| strtoupper($_SESSION['nick_name'])=='VCONNECT' || strtoupper($_SESSION['nick_name'])=='JSW' || strtoupper($_SESSION['nick_name'])=='AKASH' || strtoupper($_SESSION['nick_name'])=='START' 

									|| strtoupper($_SESSION['nick_name'])=='DNV'  ||strtoupper($_SESSION['nick_name'])=='NHPL' || strtoupper($_SESSION['nick_name'])=='GOLDSTONE' || strtoupper($_SESSION['nick_name'])=='AJANTA'  || strtoupper($_SESSION['nick_name'])=='NIMBUS' || strtoupper($_SESSION['nick_name'])=='DNVFOODS' || strtoupper($_SESSION['nick_name'])=='SUPRA' || strtoupper($_SESSION['nick_name'])=='EDIBLE' || strtoupper($_SESSION['nick_name'])=='MAGIK' || strtoupper($_SESSION['nick_name'])=='SAI' || strtoupper($_SESSION['nick_name'])=='ELEGANT' || strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI' || strtoupper($_SESSION['nick_name'])=='ILS' || strtoupper($_SESSION['nick_name'])=='PALSONS' )){ if(sauda_allocation == 'no' && sale=='no' && strtoupper($_SESSION['nick_name'])!='VCONNECT' && strtoupper($_SESSION['nick_name'])!='START' && strtoupper($_SESSION['nick_name'])!='STAR'){?><a href="adminCsvReadIncremental.php" name="link26" id="link26">Upload Data</a><?php }else if(sauda_allocation == 'no' && sale=='yes'){?><a href="adminCsvReadIncrementalSalePurchase.php" name="link26" id="link26">Upload Data</a><?php }

									else if(sauda_outstanding == 'yes' && strtoupper($_SESSION['nick_name'])=='EMAMI'){ 

									?><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link43" id="link43" onMouseOver="MM_showMenu(window.mm_menu_0716141743_0,0,26,null,'link43')" onMouseOut="MM_startTimeout();">Upload Data</a><?php } 

									else if(strtoupper($_SESSION['nick_name'])=='VCONNECT'){ 

									?><a href="adminCsvReadIncremental.php" name="link91" id="link91" onMouseOver="MM_showMenu(window.mm_menu_0716141791_0,0,26,null,'link91')" onMouseOut="MM_startTimeout();">Upload Data</a><?php }

									else if( strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START' ){ 

									?><a href="adminCsvReadIncremental.php" name="link105" id="link105" onMouseOver="MM_showMenu(window.mm_menu_07161417105_0,0,26,null,'link105')" onMouseOut="MM_startTimeout();">Upload Data</a><?php }

									}?>

                                   </td><?php

                            if(($_SESSION['admin_login']=='E0076') && (strtoupper($_SESSION['nick_name'])=='EMAMIT' || 

							strtoupper($_SESSION['nick_name'])=='EMAMI')){

								   ?>

                                   <td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link63" id="link63" onMouseOver="MM_showMenu(window.mm_menu_0716141763_0,0,26,null,'link63')" onMouseOut="MM_startTimeout();">Upload Data</a></td><?php } /*if($_SESSION['admin_login']=='E0076' && sauda_outstanding == 'yes' && (strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT')) {?>

                                   <!--td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link94" id="link94">Upload Data</a></td-->

                                   <?php }*/if(($_SESSION['admin_login']=='E0042'  || strtoupper($_SESSION['admin_login'])=="GMHBC" )  && sauda_outstanding == 'yes' && (strtoupper($_SESSION['nick_name'])=='EMAMI' )) {

									   ?>

                                   <td><a href="adminCsvReadIncrementalSaudaverticalwise.php" name="link43" id="link43" onMouseOver="MM_showMenu(window.mm_menu_0716141743_0,0,26,null,'link43')" onMouseOut="MM_startTimeout();">Upload Data</a></td>

                                   <?php }

								   /*if(strtoupper($_SESSION['nick_name'])=='PRABHUJI' && strtoupper($_SESSION['admin_login']) == 'ADMIN') {

									   ?>

                                   <td><a href="adminCsvReadIncrementalHaldiram.php" name="link26" >Upload Data</a></td>

                                   <?php }*/

								   if(strtoupper($_SESSION['nick_name'])=='PRABHUJI'){ 

									?><td><a href="adminCsvReadIncrementalHaldiram.php" name="link43" id="link43" >Upload Data</a></td><?php }

                            		if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='ASL'){

								   ?>

                                   <td><a href="adminCsvReadIncremental.php" name="link108" id="link108" onMouseOver="MM_showMenu(window.mm_menu_07161417108_0,0,26,null,'link108')" onMouseOut="MM_startTimeout();">Upload Data</a></td>

                                   <?php } 

								   if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='DURO'){

								   ?>

                                   <td><a href="adminCsvReadIncremental.php" name="link26" id="link26" >Upload Data</a></td>

                                   <?php } 

								   }

								   ?>

                            

                                 <td><?php if(strtoupper($_SESSION['admin_login'])=='ADMIN' && (strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='NIMBUS' || strtoupper($_SESSION['nick_name'])=='DNV' || strtoupper($_SESSION['nick_name'])=='MAGIK')){?><a href="adminCatalogueUpload.php" name="Upload Catalogue" >Upload Catalogue</a><?php }?>

                                </td>

                                 <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c'))

								 && (strtoupper($_SESSION['nick_name'])=='RKBK'  )){?><a href="adminAllTransactionDataDownload.php" name="link7" id="link7" onMouseOver="MM_showMenu(window.mm_menu_0716141933_0,0,26,null,'link7')" onMouseOut="MM_startTimeout();">Download</a><?php }?>

                                </td>

                                <td><?php if($_SESSION['admin_login']!='' && ( strtoupper($_SESSION['nick_name'])!='RKBK' && strtoupper($_SESSION['nick_name'])!='RKBKT') || $_SESSION['admin_login'] == 'supervisor' || strtoupper($_SESSION['admin_login'])=='GMHBC'){ if(sauda_allocation == 'no' && survey=='no' && strtoupper($_SESSION['nick_name'])!='PARLE' && strtoupper($_SESSION['nick_name'])!='RUPA' && strtoupper($_SESSION['nick_name'])!='DNV' && strtoupper($_SESSION['nick_name'])!='SKIPPER' && strtoupper($_SESSION['nick_name'])!='ILS' && strtoupper($_SESSION['nick_name'])!='PROPELLO'){?><a href="adminMonthlyAttendencePrint.php" name="link19" id="link19" onMouseOver="MM_showMenu(window.mm_menu_0716141733_0,0,26,null,'link19')" onMouseOut="MM_startTimeout();">Download Data</a><?php

								}

								else if(strtoupper($_SESSION['admin_login']) == 'ADMIN' && (strtoupper($_SESSION['nick_name']) == 'DNV' )){

									?>

                                    <a href="adminMonthlyAttendencePrint.php" name="link19" id="link19" onMouseOver="MM_showMenu(window.mm_menu_0716141733_0,0,26,null,'link19')" onMouseOut="MM_startTimeout();">Download Data</a>

                                    <?php

								}

								else if(strtoupper($_SESSION['admin_login']) != '' && (strtoupper($_SESSION['nick_name']) == 'SKIPPER' )){

									?>

                                    <a href="adminMonthlyAttendencePrint.php" name="link73" id="link73" onMouseOver="MM_showMenu(window.mm_menu_0716141773_0,0,26,null,'link73')" onMouseOut="MM_startTimeout();">Download Data</a>

                                    <?php

								}

								else if(strtoupper($_SESSION['nick_name']) == 'RUPA'){

									?>

                                    <a href="adminMonthlyAttendencePrint.php" name="link69" id="link69" onMouseOver="MM_showMenu(window.mm_menu_0716141769_0,0,26,null,'link69')" onMouseOut="MM_startTimeout();">Download Data</a>

                                    <?php

								}
								else if(strtoupper($_SESSION['nick_name'])=='ILS'){?>

                                <a href="#" name="link62" id="link62" onMouseOver="MM_showMenu(window.mm_menu_0716141762_0,0,26,null,'link62')" onMouseOut="MM_startTimeout();">Download Data</a>

								<?php }
								else if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='PARLE' ||

								strtoupper($_SESSION['nick_name'])=='MAITHAN') )

								{?><a href="adminMonthlyAttendencePrint.php" name="link57" id="link57" onMouseOver="MM_showMenu(window.mm_menu_0716141757_0,0,26,null,'link57')" onMouseOut="MM_startTimeout();">Download Data</a><?php

								}

								/*else if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='KUNJ')

								{?><a href="adminMonthlyAttendencePrint.php" name="link57" id="link57" onMouseOver="MM_showMenu(window.mm_menu_0716141757_0,0,26,null,'link57')" onMouseOut="MM_startTimeout();">Download Data</a><?php

								}*/

								else if(sauda_allocation == 'no' && survey=='yes' && $_SESSION['admin_login']!='E0002'

								&& strtoupper($_SESSION['nick_name'])=='LIPL'){

									if($_SESSION['admin_login']!='emovesfa_do'){

										

									?>

                                <a href="exceldownloadSurvey.php" name="link31" id="link31" onMouseOver="MM_showMenu(window.mm_menu_0716141731_0,0,26,null,'link31')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<? } }else if(sauda_allocation == 'no' && survey=='yes' && $_SESSION['admin_login']=='E0002' && strtoupper($_SESSION['nick_name'])=='LIPL'){?>

								 <a href="attendance_download.php" name="link41" id="link41">Attendance Download</a>

								<?php }

								else if(sauda_allocation == 'no' && survey=='yes' && (strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='ECEMENT')

								&& $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do'){?>

                                <a href="exceldownloadSurvey.php" name="link52" id="link52" onMouseOver="MM_showMenu(window.mm_menu_0716141752_0,0,26,null,'link52')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<?php }

							    else if(sauda_allocation == 'no' && survey=='yes' && $_SESSION['admin_login']!='emovesfa_hr'

								&& strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='DURO'  && strtoupper($_SESSION['nick_name'])!='SUPERSHAKTI' && $_SESSION['admin_login']!='emovesfa_do'){

									if(strtoupper($_SESSION['nick_name'])!='SHYAM' && $functionality!='DOS'){

									?>

                                <a href="exceldownloadSurvey.php" name="link31" id="link31" onMouseOver="MM_showMenu(window.mm_menu_0716141731_0,0,26,null,'link31')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<?php }else{ if($functionality!='DOS'){?>

                                  <a href="exceldownloadSurvey.php" name="link102" id="link102" onMouseOver="MM_showMenu(window.mm_menu_07161417102_0,0,26,null,'link102')" onMouseOut="MM_startTimeout();">Transaction Download</a>

                                <?php }}

								}

								else if(sauda_allocation == 'no' && survey=='yes' && strtoupper($_SESSION['nick_name'])=='CASHLESS'){?>

                                <a href="#" name="link62" id="link62" onMouseOver="MM_showMenu(window.mm_menu_0716141762_0,0,26,null,'link62')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<?php }
								else if(sauda_allocation == 'no' && survey=='yes' && strtoupper($_SESSION['nick_name'])=='DURO'){?>

                                <a href="#" name="link128" id="link128" onMouseOver="MM_showMenu(window.mm_menu_07161417128_0,0,26,null,'link128')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<?php }
								else if(sauda_allocation == 'no' && survey=='yes' && strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){?>

                                <a href="#" name="link133" id="link133" onMouseOver="MM_showMenu(window.mm_menu_07161417133_0,0,26,null,'link133')" onMouseOut="MM_startTimeout();">Transaction Download</a>

								<?php }
								else {

									if((strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || 

										strtoupper($_SESSION['admin_login'])=='E0042') &&  strtoupper($_SESSION['nick_name'])=='EMAMI'){

									?><a href="adminMonthlyAttendencePrint.php" name="link24" id="link24" onMouseOver="MM_showMenu(window.mm_menu_0716141223_0,0,26,null,'link24')" onMouseOut="MM_startTimeout();">Download Data</a><?php }

									} }?>

                                </td>

                                <td><?php if($_SESSION['admin_login']!='' && survey == 'yes' && strtoupper($_SESSION['nick_name'])!='RUPA' && strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='AJANTA' 

								&& strtoupper($_SESSION['nick_name'])!='EMAMI' 

								&& strtoupper($_SESSION['nick_name'])!='EMAMIT' && strtoupper($_SESSION['nick_name'])!='DURO'){if($functionality!='DOS' && strtoupper($_SESSION['nick_name'])!='KHMER'){?><a href="survey_output_edit.php" name="link30" id="link30" onMouseOver="MM_showMenu(window.mm_menu_0716141230_0,0,26,null,'link30')" onMouseOut="MM_startTimeout();" >Survey</a><?php }}

								if($_SESSION['admin_login']!='' && survey == 'yes' && strtoupper($_SESSION['nick_name'])=='AJANTA'){ ?><a href="adminSurveyDownload.php">Survey</a><?php }

                                if($_SESSION['admin_login']!='' && survey == 'yes' && strtoupper($_SESSION['nick_name'])=='DURO'){ ?><a href="exceldownloadSurvey.php" name="link107" id="link107" onMouseOver="MM_showMenu(window.mm_menu_07161412107_0,0,26,null,'link107')" onMouseOut="MM_startTimeout();">Survey</a><?php } ?>

                                </td>

                                <td><?php

	/*if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])!='RKBK') && $_SESSION['admin_login']!='supervisor'){

	if(vertical_fields == 'yes' && $total_rows>0){

?>

	<a href="pushnotification_vertical_branch_select.php" name="link46" id="link46" onMouseOver="MM_showMenu(window.mm_menu_0716141046_0,0,26,null,'link46')" onMouseOut="MM_startTimeout();" >Broadcast</a><?php }else{

		if($_SESSION['admin_login']!='emovesfa_hr'){

		?>

<a href="adminPushNotification.php" name="link22" id="link22" onMouseOver="MM_showMenu(window.mm_menu_0716141022_0,0,26,null,'link22')" onMouseOut="MM_startTimeout();" >Broadcast</a>

<?php

		}

}

}*/

if($_SESSION['admin_login']!='' && ((strtoupper($_SESSION['nick_name'])=='SKIPPER') || (strtoupper($_SESSION['nick_name'])=='RUPA') || (strtoupper($_SESSION['nick_name'])=='GOLDSTONE') || (strtoupper($_SESSION['nick_name'])=='NIMBUS') || (strtoupper($_SESSION['nick_name'])=='ABDOS') || (strtoupper($_SESSION['nick_name'])=='ARCHITA')  || (strtoupper($_SESSION['nick_name'])=='PERFUME'))){

		?>

<a href="adminPushNotification.php" name="link22" id="link22" onMouseOver="MM_showMenu(window.mm_menu_0716141022_0,0,26,null,'link22')" onMouseOut="MM_startTimeout();" >Broadcast</a>

<?php

	}

	if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START'))

	{?>

		<a href="adminPushNotificationStar.php" name="link119" id="link119" onMouseOver="MM_showMenu(window.mm_menu_07161410119_0,0,26,null,'link119')" onMouseOut="MM_startTimeout();" >Broadcast</a>

	<?php

	}

?>

                                </td>

                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VIPL' && $_SESSION['admin_login']=='admin'){?><a href="showMultiRouteVIPL.php" name="link8" id="link8">WOD</a><?php }?></td>

                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VALVO' && $_SESSION['admin_login']=='admin'){?>

                                <a href="adminRoutePlanAccess.php" name="link9" id="link9">Route Plan Access</a><?php }?></td>

                                <td><?php if(strtoupper($_SESSION['nick_name'])=='VALVO' && $_SESSION['admin_login']=='admin'){?><a href="adminRoutePlanChange.php" name="link10" id="link10">Route Plan Change</a><?php }?>

                                </td>

                                 <td><?php if(strtoupper($_SESSION['nick_name'])=='AMPL' && $_SESSION['admin_login']=='admin'){?>

                                <a href="adminRoutePlanAccess.php" name="link13" id="link13">Route Plan Access</a><?php }?></td>

                                <td><?php if(strtoupper($_SESSION['nick_name'])=='AMPL' && $_SESSION['admin_login']=='admin'){?>

                                <a href="adminMailAccess.php" name="link14" id="link14">Mail Access</a><?php }?></td>



                                <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK')){?><a href="adminLoyaltyReport.php"

                                name="link11" id="link11" onMouseOver="MM_showMenu(window.mm_menu_0716141633_0,0,26,null,'link11')" onMouseOut="MM_startTimeout();">Loyalty Report</a><?php }?>

                                </td>

                                <td><?php if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK' || strtoupper($_SESSION['nick_name'])=='RKBKT')){?><a href="adminSaleReport.php"

                                name="link13" id="link13" >Transaction Details</a><?php }?>

                                </td>

                                 <td><?php if((strtolower($_SESSION['admin_login'])=='c0007' || strtolower($_SESSION['admin_login'])=='c0008') && (strtoupper($_SESSION['nick_name'])=='RKBK' || strtoupper($_SESSION['nick_name'])=='RKBKT'))

								 {?><a href="adminTransactionDeletion.php" name="link12" id="link12" onMouseOver="MM_showMenu(window.mm_menu_0716141833_0,0,26,null,'link12')" onMouseOut="MM_startTimeout();">Transaction Deletion</a><?php }?></td>

                                 <td><?php if(sauda_allocation == 'yes' && strtolower($_SESSION['admin_login'])=='system'){ ?><a href="saudawise_quantity_edit_report.php" name="link20" id="link20">Edit Sauda</a><?php }?></td>

                                 <td>

                                 <?php if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='RUPA'){?>

                                 <a href="rupa_verticalwise_report.php" name="link33" id="link33" onMouseOver="MM_showMenu(window.mm_menu_0716141839_0,0,26,null,'link33')" onMouseOut="MM_startTimeout();">Vertical Wise Report</a><?php }?>

                                </td>

                                <td>

                                 <?php if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='RUPA')){ ?>

                                 <a href="excel_download_new_customer.php" name="link34" id="link34" onMouseOver="MM_showMenu(window.mm_menu_0716141834_0,0,26,null,'link34')" onMouseOut="MM_startTimeout();">Customize Report</a><?php } 

								 else if($_SESSION['admin_login']!='' && vertical_fields != 'yes' && strtoupper($_SESSION['nick_name'])!='MINU'){

			if(collection == 'yes' && check_in_out=='yes' && strtoupper($_SESSION['nick_name'])!='MAITHAN' && strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='START' && strtoupper($_SESSION['nick_name'])!='HALDIRAM' && strtoupper($_SESSION['nick_name'])!='PRABHUJI' 

			&& strtoupper($_SESSION['nick_name'])!='ARCHITA'  && strtoupper($_SESSION['nick_name'])!='ECEMENT' 

			&& strtoupper($_SESSION['nick_name'])!='VCONNECT' && strtoupper($_SESSION['nick_name'])!='ALNADIR' && strtoupper($_SESSION['nick_name'])!='ASL' && strtoupper($_SESSION['nick_name'])!='PERFUME' && strtoupper($_SESSION['nick_name'])!='GOLDSTONE' && strtoupper($_SESSION['nick_name'])!='SUPERSHAKTI'  && strtoupper($_SESSION['nick_name'])!='ELEGANT'){

				?><a href="excel_download_new_customer.php" name="link47" id="link47" onMouseOver="MM_showMenu(window.mm_menu_0716141847_0,0,26,null,'link47')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }else if($_SESSION['admin_login']!=''

								 && (strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='PRABHUJI'))

								 { ?>

                                 <a href="excel_download_new_customer.php" name="link110" id="link110" onMouseOver="MM_showMenu(window.mm_menu_0716141855_0,0,26,null,'link110')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }else if(strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='START' && strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='PARLE' && strtoupper($_SESSION['nick_name'])!='UCLINDIA'

								 && strtoupper($_SESSION['nick_name'])!='MAITHAN' && strtoupper($_SESSION['nick_name'])!='ECEMENT' && strtoupper($_SESSION['nick_name'])!='VCONNECT' && strtoupper($_SESSION['nick_name'])!='SHYAM' && $functionality!='DOS' && strtoupper($_SESSION['nick_name'])=='KHMER' && strtoupper($_SESSION['nick_name'])!='GOLDSTONE'){

								 ?>

            <a href="excel_download_new_customer.php" name="link37" id="link37" onMouseOver="MM_showMenu(window.mm_menu_0716141837_0,0,26,null,'link37')" onMouseOut="MM_startTimeout();">Action Reports</a>

                                 <?php }else if(strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='START' 
								 && strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='PARLE' && strtoupper($_SESSION['nick_name'])!='UCLINDIA'

								 && strtoupper($_SESSION['nick_name'])!='MAITHAN' && strtoupper($_SESSION['nick_name'])!='ECEMENT' && strtoupper($_SESSION['nick_name'])!='GOLDSTONE' && strtoupper($_SESSION['nick_name'])!='VCONNECT' && strtoupper($_SESSION['nick_name'])!='ALNADIR' && strtoupper($_SESSION['nick_name'])!='PERFUME' && strtoupper($_SESSION['nick_name'])!='SHYAM' && strtoupper($_SESSION['nick_name'])!='PALSONS' && strtoupper($_SESSION['nick_name'])!='SAI' && strtoupper($_SESSION['nick_name'])!='PROPELLO' && strtoupper($_SESSION['nick_name'])!='ILS' && strtoupper($_SESSION['nick_name'])!='MAGIK' && strtoupper($_SESSION['nick_name'])!='ABDOS' && strtoupper($_SESSION['nick_name'])!='NILESH' && strtoupper($_SESSION['nick_name'])!='DURO' && $functionality!='DOS' && collection=='no'){

								 ?>

            <a href="excel_download_new_customer.php" name="link37" id="link37" onMouseOver="MM_showMenu(window.mm_menu_0716141837_0,0,26,null,'link37')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 else if(strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='START' && strtoupper($_SESSION['nick_name'])!='CASHLESS' && strtoupper($_SESSION['nick_name'])!='PARLE' && strtoupper($_SESSION['nick_name'])!='UCLINDIA'

								 && strtoupper($_SESSION['nick_name'])!='MAITHAN' && strtoupper($_SESSION['nick_name'])!='ECEMENT' && strtoupper($_SESSION['nick_name'])!='DNVFOODS' && strtoupper($_SESSION['nick_name'])!='VCONNECT' && strtoupper($_SESSION['nick_name'])!='ALNADIR' && strtoupper($_SESSION['nick_name'])!='SHYAM' && strtoupper($_SESSION['nick_name'])!='NILESH'  && strtoupper($_SESSION['nick_name'])!='SUPERSHAKTI' 
								 && strtoupper($_SESSION['nick_name'])!='ELEGANT' && $functionality!='DOS' && collection=='yes'){

								 ?>

            <a href="excel_download_new_customer.php" name="link104" id="link104" onMouseOver="MM_showMenu(window.mm_menu_07161418104_0,0,26,null,'link104')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								  else if(strtoupper($_SESSION['nick_name'])=='SHYAM'){ 

								  ?>

            <a href="excel_download_new_customer.php" name="link103" id="link103" onMouseOver="MM_showMenu(window.mm_menu_07161418103_0,0,26,null,'link103')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }
								  else if(strtoupper($_SESSION['nick_name'])=='PALSONS'){ 
								  ?>
            <a href="excel_download_new_customer.php" name="link130" id="link130" onMouseOver="MM_showMenu(window.mm_menu_07161418130_0,0,26,null,'link130')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								 else if(strtoupper($_SESSION['nick_name'])=='SAI'){ 
								  ?>
            <a href="excel_download_new_customer.php" name="link135" id="link135" onMouseOver="MM_showMenu(window.mm_menu_07161418135_0,0,26,null,'link135')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								 else if(strtoupper($_SESSION['nick_name'])=='PROPELLO'){ 
								  ?>
            <a href="sales_executive_wise_report.php" name="link136" id="link136" onMouseOver="MM_showMenu(window.mm_menu_07161418136_0,0,26,null,'link136')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								 else if(strtoupper($_SESSION['nick_name'])=='ILS'){ 
								  ?>
            <a href="excel_download_new_customer.php" name="link134" id="link134" onMouseOver="MM_showMenu(window.mm_menu_07161418134_0,0,26,null,'link134')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								 else if(strtoupper($_SESSION['nick_name'])=='MAGIK'){ 
								  ?>
            <a href="excel_download_new_customer.php" name="link131" id="link131" onMouseOver="MM_showMenu(window.mm_menu_07161418131_0,0,26,null,'link131')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								  else if(strtoupper($_SESSION['nick_name'])=='ABDOS'){ 
								  ?>
            <a href="excel_download_new_customer.php" name="link132" id="link132" onMouseOver="MM_showMenu(window.mm_menu_07161418132_0,0,26,null,'link132')" onMouseOut="MM_startTimeout();">Customize Report</a>
                                 <?php }
								  else if(strtoupper($_SESSION['nick_name'])=='DNVFOODS'){ 

								  ?>

            <a href="excel_download_new_customer.php" name="link120" id="link120" onMouseOver="MM_showMenu(window.mm_menu_07161418120_0,0,26,null,'link120')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 else if(strtoupper($_SESSION['nick_name'])=='UCLINDIA'){ ?>

            <a href="excel_download_new_customer.php" name="link67" id="link67" onMouseOver="MM_showMenu(window.mm_menu_0716141867_0,0,26,null,'link67')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 else if(strtoupper($_SESSION['nick_name'])=='MAITHAN'){ ?>

            <!--a href="excel_download_new_customer.php" name="link68" id="link68" onMouseOver="MM_showMenu(window.mm_menu_0716141868_0,0,26,null,'link68')" onMouseOut="MM_startTimeout();">Customize Report</a-->

             <a href="excel_download_new_customer.php" name="link100" id="link100" onMouseOver="MM_showMenu(window.mm_menu_07161418100_0,0,26,null,'link100')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 else if(strtoupper($_SESSION['nick_name'])=='PARLE'){ ?>

            <a href="#" name="link63" id="link63" onMouseOver="MM_showMenu(window.mm_menu_0716141863_0,0,26,null,'link63')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php } else if((strtoupper($_SESSION['nick_name'])=='STAR') && $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do' && strtoupper($_SESSION['admin_login'])=='ADMIN'){?>

                                 <a href="excel_download_new_customer.php" name="link116" id="link116" onMouseOver="MM_showMenu(window.mm_menu_07161418116_0,0,26,null,'link116')" onMouseOut="MM_startTimeout();">Customize Report</a>

								<?php }

								else if(strtoupper($_SESSION['nick_name'])=='START')

								{

								?>

                                 <a href="excel_download_new_customer.php" name="link116" id="link116" onMouseOver="MM_showMenu(window.mm_menu_07161418116_0,0,26,null,'link116')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                <?php	

								}

								else if((strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START') 
								&& $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do' && strtoupper($_SESSION['admin_login'])!='ADMIN' && strtoupper($_SESSION['admin_login'])!='E1071' && strtoupper($_SESSION['admin_login'])!='E0555' && strtoupper($_SESSION['admin_login'])!='E0556' && strtoupper($_SESSION['admin_login'])!='E0938'){?>

                                 <a href="excel_download_new_customer.php" name="link93" id="link93" onMouseOver="MM_showMenu(window.mm_menu_0716141893_0,0,26,null,'link93')" onMouseOut="MM_startTimeout();">Customize Report</a>

								<?php }
								else if((strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START') && $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do' 
								&& (strtoupper($_SESSION['admin_login'])=='E1071' || strtoupper($_SESSION['admin_login'])=='E0555' || strtoupper($_SESSION['admin_login'])=='E0556')){?>

                                 <a href="excel_download_new_customer.php" name="link126" id="link126" onMouseOver="MM_showMenu(window.mm_menu_07161418126_0,0,26,null,'link126')" onMouseOut="MM_startTimeout();">Customize Report</a>

								<?php }
								else if((strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START') && $_SESSION['admin_login']!='emovesfa_hr' && $_SESSION['admin_login']!='emovesfa_do' && strtoupper($_SESSION['admin_login'])=='E0938'){?>

                                 <a href="excel_download_new_customer.php" name="link127" id="link127" onMouseOver="MM_showMenu(window.mm_menu_07161418127_0,0,26,null,'link127')" onMouseOut="MM_startTimeout();">Customize Report</a>

								<?php }

								 else if(strtoupper($_SESSION['nick_name'])=='ECEMENT')
								 { 
								 ?>
                                 <a href="excel_download_new_customer.php" name="link97" id="link97" onMouseOver="MM_showMenu(window.mm_menu_0716141897_0,0,26,null,'link97')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }

								  else if(strtoupper($_SESSION['nick_name'])=='VCONNECT')
								 { 
								 ?>
                                 <a href="excel_download_new_customer.php" name="link98" id="link98" onMouseOver="MM_showMenu(window.mm_menu_0716141898_0,0,26,null,'link98')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }

								  else if(strtoupper($_SESSION['nick_name'])=='ALNADIR')

								 { 

								 ?>

                                 <a href="excel_download_new_customer.php" name="link100" id="link100" onMouseOver="MM_showMenu(window.mm_menu_07161418100_0,0,26,null,'link100')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }

								 elseif(strtoupper($_SESSION['nick_name'])=='NILESH'){?>

                                <a href="excel_download_new_customer.php" name="link99" id="link99" onMouseOver="MM_showMenu(window.mm_menu_0716141899_0,0,26,null,'link99')" onMouseOut="MM_startTimeout();">Customize Report</a>

								<?php

                                }

								 else if(strtoupper($_SESSION['nick_name'])=='GOLDSTONE'){ 

								  ?>

                            <a href="excel_download_new_customer.php" name="link106" id="link106" onMouseOver="MM_showMenu(window.mm_menu_07161418106_0,0,26,null,'link106')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								  else if(strtoupper($_SESSION['nick_name'])=='PERFUME'){ 

								  ?>

            <a href="daily_activity_SKIPPER.php" name="link114" id="link114" onMouseOver="MM_showMenu(window.mm_menu_07161418114_0,0,26,null,'link114')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }
								 else if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI')
								 { ?>

                                 <a href="excel_download_new_customer.php" name="link54" id="link54" onMouseOver="MM_showMenu(window.mm_menu_0716141854_0,0,26,null,'link54')" onMouseOut="MM_startTimeout();">Customize Report</a><?php 
								 }
								 else if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='ELEGANT')
								 { ?>

                                 <a href="excel_download_new_customer.php" name="link139" id="link139" onMouseOver="MM_showMenu(window.mm_menu_0716141139_0,0,26,null,'link139')" onMouseOut="MM_startTimeout();">Customize Report</a><?php 
								 }

								} 

								else if($_SESSION['admin_login']!='' && vertical_fields != 'yes' && strtoupper($_SESSION['nick_name'])=='MINU'){

?><a href="excel_download_new_customer.php" name="link48" id="link48" onMouseOver="MM_showMenu(window.mm_menu_0716141848_0,0,26,null,'link48')" onMouseOut="MM_startTimeout();">Customize Report</a>

<?php } else if(strtoupper($_SESSION['admin_login'])=='ADMIN' && (strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT')){ ?>

                                 <a href="emami_daily_activity_analysis.php" name="link42" id="link42" onMouseOver="MM_showMenu(window.mm_menu_0716141842_0,0,26,null,'link42')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }else if(strtoupper($_SESSION['admin_login'])

								 !='ADMIN' && strtoupper($_SESSION['admin_login'])

								 !='' && (strtoupper($_SESSION['nick_name'])=='EMAMI' || strtoupper($_SESSION['nick_name'])=='EMAMIT')){ ?>

                                 <a href="pending_contract_ageing_report.php" name="link92" id="link92" onMouseOver="MM_showMenu(window.mm_menu_0716141892_0,0,26,null,'link92')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php } 

								 else if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='VIPL'

								 || strtoupper($_SESSION['nick_name'])=='DNV'))

								 { ?>

                                 <a href="excel_download_new_customer.php" name="link54" id="link54" onMouseOver="MM_showMenu(window.mm_menu_0716141854_0,0,26,null,'link54')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }

								  else if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='PANORAMA'){

								 ?>

            <a href="excel_download_new_customer.php" name="link37" id="link37" onMouseOver="MM_showMenu(window.mm_menu_0716141837_0,0,26,null,'link37')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 else if($_SESSION['admin_login']!=''

								 && (strtoupper($_SESSION['nick_name'])=='ARCHITA'))

								 { ?>

                                 <a href="excel_download_new_customer.php" name="link111" id="link111" onMouseOver="MM_showMenu(window.mm_menu_07161418111_0,0,26,null,'link111')" onMouseOut="MM_startTimeout();">Customize Report</a><?php }

								else if(strtoupper($_SESSION['nick_name'])=='ASL'){ 

								  ?>

            <a href="excel_download_new_customer.php" name="link103" id="link103" onMouseOver="MM_showMenu(window.mm_menu_07161418103_0,0,26,null,'link103')" onMouseOut="MM_startTimeout();">Customize Report</a>

                                 <?php }

								 ?>

                                </td>

                                <td>

                                 <?php if($_SESSION['admin_login']!='' && (strtoupper($_SESSION['nick_name'])=='STORE' || strtoupper($_SESSION['nick_name'])== 'LIPL')){?><a href="mall_add_edit.php" name="link35" id="link35" onMouseOver="MM_showMenu(window.mm_menu_0716141835_0,0,26,null,'link35')" onMouseOut="MM_startTimeout();">Master Input</a><?php }?>

                                </td>

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

                                <?php

								if((strtoupper($_SESSION['admin_login']) != '') && (strtoupper($_SESSION['nick_name'])=='ABDOS' || strtoupper($_SESSION['nick_name'])=='ABDOST')){

									?>

                                    <td>

                                    <a href="../abdos/login.php?username=admin" target="_blank" name="link64" id="link64">Additional MIS</a>

                                    </td>

                                    <?php

								}

								?>

                                <td>

                                <?php

									if($_SESSION['admin_login']!=''){

											if(stk_audit == 'yes' && $functionality!='DOS'){

									    if(strtoupper($_SESSION['admin_login'])=='ADMIN' &&  strtoupper($_SESSION['nick_name'])=='EMAMI'){

								?>

                                <a href="stock_audit_report.php" name="link39" id="link39">Stock Audit</a>

                                <?php

										}elseif(strtoupper($_SESSION['nick_name'])!='EMAMI' &&

												strtoupper($_SESSION['nick_name'])!='STAR' && strtoupper($_SESSION['nick_name'])!='DNVFOODS' && $_SESSION['admin_login']!='emovesfa_hr'

										&& $_SESSION['admin_login']!='emovesfa_do'){?>



									 <a href="stock_audit_report.php" name="link39" id="link39">Stock Audit</a>

										<?php

										}elseif( strtoupper($_SESSION['nick_name'])=='STAR' && $_SESSION['admin_login']!='emovesfa_hr'

										&& $_SESSION['admin_login']!='emovesfa_do'){?>



									 <a href="star_stock_audit_report.php" name="link109" id="link109">Stock Audit</a>

										<?php

										}

										}}

								?>

                                </td>

                                <?php 

									if(strtoupper($_SESSION['nick_name'])=='STAR' && strtoupper($_SESSION['admin_login'])=='ADMIN')

									{ 

								?>

                                	<td><a href="yellow_card_qty_update.php" name="link140" id="link140">Yellow Card Edit</a></td>

                                <?php } ?>

                                <td>

                                <?php

									if($_SESSION['admin_login']!='' && tour_exp == 'yes'){

											if(strtoupper($_SESSION['nick_name'])=='SKIPPER'){

								?>

                                <a href="tour_fooding_lodging_expenses.php" name="link101" id="link101" onMouseOver="MM_showMenu(window.mm_menu_07161411015_0,0,26,null,'link101')" onMouseOut="MM_startTimeout();">Tours &amp; Travel Expenses</a>

                                <?php }else if(strtoupper($_SESSION['nick_name'])=='MDPL' || strtoupper($_SESSION['nick_name'])=='SHYAM' || strtoupper($_SESSION['nick_name'])=='ARCHITA'){?>

								<a href="tour_fooding_lodging_expenses.php" name="link40" id="link40">Tours &amp; Travel Expenses</a>

								<?php }else{?>

											<a href="tour_expense_report.php" name="" id="">Tours &amp; Travel Expenses</a>

											<?php

                                            }

										}

								?>

                                </td>

                                <td>

                                <?php

									if($_SESSION['admin_login']!=''){

											if((sauda_allocation == 'yes' && $reporting_to_total>0 && $_SESSION['admin_login'] != 'E0076' && 

											strtoupper($_SESSION['admin_login']) != 'GMSFATS') || 

											(sauda_allocation == 'yes' && $_SESSION['admin_login'] == 'admin')){

									/*if(strtoupper($_SESSION['nick_name']) == 'EMAMI'){

									?>

									<a href="generate_pricing_details.php" name="link45" id="link45" onMouseOver="MM_showMenu(window.mm_menu_0716141845_0,0,26,null,'link45')" onMouseOut="MM_startTimeout();">Pricing</a>

									<?php

									}*/if(strtoupper($_SESSION['nick_name']) == 'EMAMI'){

										?>

									<a href="generate_pricing_details_formulationwise_modified.php" name="link51" id="link51" onMouseOver="MM_showMenu(window.mm_menu_0716141851_0,0,26,null,'link51')" onMouseOut="MM_startTimeout();">Pricing</a>

										<?php

									}

									if(strtoupper($_SESSION['nick_name']) == 'EMAMIT')

									{?>

										<a href="generate_pricing_details_formulation.php" name="link81" id="link81" onMouseOver="MM_showMenu(window.mm_menu_0716141881_0,0,26,null,'link81')" onMouseOut="MM_startTimeout();">Pricing</a>

										<?php

									}

								  }

								  ?>

                                  </td>

                                  <?php

								  	if(strtolower($_SESSION['admin_login'])=='emovesfa_hr' && strtoupper($_SESSION['nick_name']) == 'STAR')

									 {?>

									      <td><a href="attendance_download_star.php" name="link60" id="link60" >Attendance Download</a> </td>

                                          <td><a href="attendance_download_star_modified.php" name="link118" id="link118" >Attendance Download Modified</a></td>

 									<?php

									 }

								 }

								?>

                                

                                 <!--td><?php /*if(($_SESSION['admin_login']=='admin' || (strtolower(substr($_SESSION['admin_login'],0,1))=='c')) && (strtoupper($_SESSION['nick_name'])=='RKBK')){?><a href="adminMonthlySaleReportDownload.php"

                                name="link18" id="link18" >Sale & Purchase Download</a!--><?php }*/?>

                                <!--td><a href="#" name="link4" id="link4" onMouseOver="MM_showMenu(window.mm_menu_0716142019_0,0,26,null,'link4')" onMouseOut="MM_startTimeout();">Other Tools</a></td>

								<!--td><a href="#" name="link7" id="link3" onMouseOver="MM_showMenu(window.mm_menu_0716142232_0,0,26,null,'link7')" onMouseOut="MM_startTimeout();">Content Management</a></td-->

								<!--td><a href="#" name="link6" id="link6" onMouseOver="MM_showMenu(window.mm_menu_0716142530_0,0,26,null,'link6')" onMouseOut="MM_startTimeout();">Generate Report</a></td-->

                                <td>

                                <?php

								if(strtoupper($_SESSION['nick_name']) == 'VIPL' && strtoupper($_SESSION['admin_login']) == 'E0001'){

								?>

                                <a href="order_tracking_report.php" name="link66" id="link66" onMouseOver="MM_showMenu(window.mm_menu_0716141866_0,0,26,null,'link66')" onMouseOut="MM_startTimeout();">Order Tracking</a>

                                <?php

								}

								?>

                                </td>

                                <td>

                                <?php

								if(($_SESSION['admin_login'] == 'E1412') && strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){

								?>

                                <a href="customer_allocation_CRM.php">Retailer CRM</a>

                                <?php

								}

								?>

                                </td>

                                <!--td>

                                <?php

								/*if($_SESSION['admin_login'] && strtoupper($_SESSION['nick_name']) != 'EMAMI' ){

									if($_SESSION['admin_login'] != 'emovesfa_do'){

								?>

                                	<a href="product_wise_sales.php">Productwise Order Analysis</a>

                                <?php

									}

								}*/

								?>

                                </td-->

                                -<td>

                                <?php

								if(strtoupper($_SESSION['admin_login']) == 'ADMIN' && strtoupper($_SESSION['nick_name']) == 'EMAMI' ){

								?>

                                <a href="emamigui/emamigui.php">Graphical Report</a>

                                <?php

								}

								?>

                                </td>

                                <td>

                                <?php

								if(strtoupper($_SESSION['admin_login']) == 'ADMIN' && strtoupper($_SESSION['nick_name']) == 'STAR'){

								?>

                                <a href="download_STAR_dbbackup.php">DB Download</a>

                                <?php

								}

								?>

                                </td>

                                <td>

                                <?php

								if(strtolower($_SESSION['admin_login']) == 'emovesfa_do' && strtoupper($_SESSION['nick_name']) == 'STAR'){

								?>

                                <a href="admin_mis_order_register.php">Order Generation</a>

                                <?php

								}

								?>

                                </td>

                                 <td>

                                <?php

								if(strtolower($_SESSION['admin_login']) == 'admin' && (strtoupper($_SESSION['nick_name']) == 'SKIPPER' || strtoupper($_SESSION['nick_name']) == 'HALDIRAM' || strtoupper($_SESSION['nick_name']) == 'EMAMI' 

								 || strtoupper($_SESSION['nick_name']) == 'SHYAM') || strtoupper($_SESSION['nick_name']) == 'KARMA' || strtoupper($_SESSION['nick_name']) == 'PRABHUJI'){

								?>

                                <a href="dump_download.php">Dump Download</a>

                                <?php

								}

								else if((strtoupper($_SESSION['admin_login']) == 'E0042' || strtoupper($_SESSION['admin_login'])=="GMHBC" || 

										strtoupper($_SESSION['admin_login']) == 'E0076' 

								|| strtoupper($_SESSION['admin_login'])=='HBC' || strtoupper($_SESSION['admin_login'])=='PRICEHBC' 

								|| strtoupper($_SESSION['admin_login'])=='SFATS' || strtoupper($_SESSION['admin_login'])=='PRICESFATS') && (strtoupper($_SESSION['nick_name']) == 'EMAMI' || strtoupper($_SESSION['nick_name']) == 'EMAMIT')){

								?>

                                <a href="dump_download_verticalwise.php">Dump Download</a>

                                <?php }else if(strtoupper($_SESSION['admin_login'])=='ADMIN' && (strtoupper($_SESSION['nick_name']) == 'NHPL' || strtoupper($_SESSION['nick_name']) == 'PRAKASH' || strtoupper($_SESSION['nick_name']) == 'ARCHITA' || strtoupper($_SESSION['nick_name']) == 'RUPA' || strtoupper($_SESSION['nick_name']) == 'AKASH' || strtoupper($_SESSION['nick_name']) == 'AJANTA' || strtoupper($_SESSION['nick_name']) == 'VCONNECT' || strtoupper($_SESSION['nick_name']) == 'EDIBLE' 

								|| strtoupper($_SESSION['nick_name']) == 'DNV' || strtoupper($_SESSION['nick_name']) == 'ASL'  || strtoupper($_SESSION['nick_name']) == 'MOREISH' || strtoupper($_SESSION['nick_name']) == 'EDIBLEH' || strtoupper($_SESSION['nick_name']) == 'NIMBUS' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONE' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONE' || strtoupper($_SESSION['nick_name']) == 'MDPL'  || strtoupper($_SESSION['nick_name']) == 'DNVFOODS' || strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name'])=='ILS' || strtoupper($_SESSION['nick_name']) == 'ABDOS' || strtoupper($_SESSION['nick_name']) == 'ABDOST' || strtoupper($_SESSION['nick_name']) == 'MAGIK' || strtoupper($_SESSION['nick_name']) == 'SUPERSHAKTI' || strtoupper($_SESSION['nick_name']) == 'SAI' || strtoupper($_SESSION['nick_name']) == 'ELEGANT')){?><a href="dump_download_new.php">Dump Download</a><?php 

								}else if(strtoupper($_SESSION['admin_login'])!='' && strtoupper($_SESSION['nick_name']) == 'PALSONS'){?>

                                <a href="dump_download_new.php">Dump Download</a>

                                <?php }
								
								else if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name']) == 'DURO'){?>

                                <a href="dump_download_new.php">Dump Download</a>

                                <?php }?>

                                </td>

                                  <td>

                                <?php

								if(strtoupper($_SESSION['nick_name']) != 'EMAMIT' && strtolower($_SESSION['admin_login']) == 'admin' && focus_product=='yes'){

								?>

                                <a href="generate_focus_product.php">Focus product</a>

                                <?php

								}

								?>

                                </td>

                                  <td>

                                <?php

                                if( $_SESSION['admin_login']!='' && $functionality=='DOS'){?>

                                 <a href="order_approval_process_modified.php">Order Approval</a><?php }?></td>

                                 <?php if(($_SESSION['admin_login']=='E0076') && (strtoupper($_SESSION['nick_name'])=='EMAMIT' || strtoupper($_SESSION['nick_name'])=='EMAMI')){?>

                                 <td><a href="generate_pricing_details_formulationSF.php" name="link77" id="link77" onMouseOver="MM_showMenu(window.mm_menu_0716141877_0,0,26,null,'link77')" onMouseOut="MM_startTimeout();">Pricing</a>

                                </td>

                                 <td><a href="margin-data-download.php" name="link65" id="link65" onMouseOver="MM_showMenu(window.mm_menu_0716141765_0,0,26,null,'link65')" onMouseOut="MM_startTimeout();">Costing Report</a></td>

                                 <?php }

								 if($_SESSION['admin_login']!='' && strtoupper($_SESSION['nick_name'])=='ASL' ){?>

                                 <td><a href="generate_pricing_formulationwise.php" name="link112" id="link112" onMouseOver="MM_showMenu(window.mm_menu_07161418112_0,0,26,null,'link112')" onMouseOut="MM_startTimeout();">Pricing</a>

                                </td>

                                 <td><a href="margin-data-download.php" name="link113" id="link113" onMouseOver="MM_showMenu(window.mm_menu_07161417113_0,0,26,null,'link113')" onMouseOut="MM_startTimeout();">Costing Report</a></td>

                                 <?php }

								//if((strtoupper($_SESSION['admin_login']) == 'ADMIN' && distributor_route_emp_relation=='yes') && (strtoupper($_SESSION['nick_name']) == 'PRABHUJI' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONE' || strtoupper($_SESSION['nick_name']) == 'EDIBLEH') ){

								if(strtoupper($_SESSION['admin_login']) == 'ADMIN' && distributor_route_emp_relation=='yes'){

								?><td>

                                <a href="adminEmployeeRouteSwapping.php">Territory Reshuffle</a></td>

                                <?php

								}

								if((strtoupper($_SESSION['nick_name'])=='VCONNECT' || strtoupper($_SESSION['nick_name'])=='VCONNECTT') 

								&& strtoupper($_SESSION['admin_login']) == 'ADMIN'){?>

                                <td>

                                <a href="adminCustomerMapping.php">Change ISP Mapping</a></td>

                                <?php

								}

								/*if((strtoupper($_SESSION['nick_name'])=='START') 

								&& strtoupper($_SESSION['admin_login']) == 'ADMIN'){?>

                                <td>

                                <a href="adminBranchwiseSchemeUpload.php">Branch PDF Upload</a></td>

                                <?php

								}*/

								if((strtoupper($_SESSION['nick_name'])=='DNV' || strtoupper($_SESSION['nick_name'])=='DNVFOODS') 

								&& strtoupper($_SESSION['admin_login']) == 'ADMIN'){?>

                                <td><a href="adminExcelReadProductData.php">SKU and mrp Upload</a></td>

                                <?php if(strtoupper($_SESSION['nick_name'])=='DNVFOODS')

								{?>

                                <td><a href="stockAnalysisReport.php" name="link117" id="link117" onMouseOver="MM_showMenu(window.mm_menu_07161417117_0,0,26,null,'link117')" onMouseOut="MM_startTimeout();">Stock Reports</a></td>

                                <?php

								 }

								}

								if(((strtoupper($_SESSION['nick_name'])=='ARCHITA') || add_customer_activation=='yes' || (strtoupper($_SESSION['nick_name'])=='GOLDSTONE')) && 

								strtoupper($_SESSION['admin_login']) != ''){?>

                                <td>

                                <a href="adminNewCustomerUnblocked.php">New Customer Activation</a></td>

                                <?php

								}

								

								if((strtoupper($_SESSION['nick_name'])=='AJANTA') && strtoupper($_SESSION['admin_login']) != ''){?>

                                <td>

                                <a href="adminSample.php">Sample Upload</a></td>

                                <?php

								}

								if(strtoupper($_SESSION['nick_name'])=='MOREISH'){?>

                                <td>

                                <a href="van_stock_allocation.php">Van Stock Alloation</a></td>

                                <td>

                                <a href="van_stock_allocation_main.php">Van Stock Alloation Report</a></td>

                                <?php

								}

								if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='DURO'){?>

                                <td>

                                <a href="adminExcelReadDealerTransaction.php">Upload Dealer Transaction</a></td>

                                <?php }

								if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='ASL'){?>

                                <td>

                                <a href="adminDOapproved.php" name="link121" id="link121" onMouseOver="MM_showMenu(window.mm_menu_07161417121_0,0,26,null,'link121')" onMouseOut="MM_startTimeout();">Approval</a></td>

                                <td><a href="bargainwisereport.php" name="link115" id="link115" onMouseOver="MM_showMenu(window.mm_menu_07161417115_0,0,26,null,'link115')" onMouseOut="MM_startTimeout();">Bargain Report</a></td>
                                <td><a href="adminDORegister.php" name="link129" id="link129" >DO Report</a></td>
                                   <td><a href="adminBargainTransfer.php" name="link122" id="link122" onMouseOver="MM_showMenu(window.mm_menu_07161417122_0,0,26,null,'link122')" onMouseOut="MM_startTimeout();">Bargain Transfer</a></td>


                                <td><a href="adminCustomerAddModifiedfinal.php" name="link120" id="link120" onMouseOver="MM_showMenu(window.mm_menu_07161417120_0,0,26,null,'link120')" onMouseOut="MM_startTimeout();">Master Info</a></td>
                                <?php }if((strtoupper($_SESSION['admin_login'])=='ADMIN')  && strtoupper($_SESSION['nick_name'])=='ASL'){?>
                                <td>
                                <a href="adminApprovalMatrixListing.php" name="link125" id="link125" onMouseOver="MM_showMenu(window.mm_menu_07161417125_0,0,26,null,'link125')" onMouseOut="MM_startTimeout();">Approval Matrix</a></td>
                                <?php }if(strtoupper($_SESSION['nick_name'])=='ASL' && $_SESSION['admin_login']!='' && strtoupper($_SESSION['admin_login'])
								!='ADMIN'){?>
								 <td>
                                <a href="adminBargainApprovedTierwise.php" name="link127" id="link127" onMouseOver="MM_showMenu(window.mm_menu_07161417125_0,0,26,null,'link125')" onMouseOut="MM_startTimeout();">Bargain Approval</a></td>
                                <?php
								}
								if(strtoupper($_SESSION['admin_login'])=='ADMIN' && (strtoupper($_SESSION['nick_name'])=='STAR' || strtoupper($_SESSION['nick_name'])=='START')){
									if(strtoupper($_SESSION['nick_name'])=='STAR'){
									?>
                                <td>
                                <a href="branding_verification_with_location_modified.php">Branding Verification</a></td>
                                <?php 
									}
									if(strtoupper($_SESSION['nick_name'])=='START'){
									?>
                                <td>
                                <a href="branding_verification_with_location_modified.php">Branding Verification</a></td>
                                <?php 
									}
								
								}
									if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='MAGIK'){?>
                                <td>
                                <a href="adminschemeupload.php">Scheme Upload</a></td>

                                <?php }
									if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name'])=='PROPELLO'){?>
                                <td>
                                <a href="adminTelecallerSubmit.php">Telecaller Form</a></td>

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

mysql_close($link);

ob_end_flush();

}

?>