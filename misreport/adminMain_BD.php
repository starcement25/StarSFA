<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
	session_start();

	/* adminUtils.php only DEFINES the page constants (SITETITLE, ADMIN_CSS, ...) and
	   sets up the nick_name context. It does NOT emit any HTML unless disphtml() is
	   called - which this page deliberately does not do, so that we can render our own
	   stripped-down BD dashboard header with only a single REPORT menu. */
	require("adminUtils.php");

	if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] == '') {
		header("Location: index.php");
		exit;
	}

	/* Same includes disphtml() pulls in, needed here for ROOT_BASE_URL and the DB handle. */
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/functions.php");

	/* ---- Header data (mirrors disphtml() in adminUtils.php) ---- */
	$emp_name = '';
	$sql_select_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$_SESSION['admin_login']."'";
	$res_select_emp_name = mysqli_query($link, $sql_select_emp_name);
	$row_select_emp_name = mysqli_fetch_assoc($res_select_emp_name);
	$emp_name = isset($row_select_emp_name['emp_name']) ? $row_select_emp_name['emp_name'] : '';

	if($emp_name != '')
		$welcome_message = "Welcome ".$emp_name;
	else if($_SESSION['admin_login'] == 'supervisor')
		$welcome_message = "Welcome Supervisor";
	else if($_SESSION['admin_login'] == 'emovesfa_hr')
		$welcome_message = "Welcome HR";
	else
		$welcome_message = "Welcome Admin";

	$app_version = '';
	$res_app_version = mysqli_query($link, "SELECT version_code FROM app_version");
	$row_app_version = mysqli_fetch_assoc($res_app_version);
	$app_version = isset($row_app_version['version_code']) ? $row_app_version['version_code'] : '';

	$db_version = '';
	$res_db_version = mysqli_query($link, "SELECT version_code FROM db_version");
	$row_db_version = mysqli_fetch_assoc($res_db_version);
	$db_version = isset($row_db_version['version_code']) ? $row_db_version['version_code'] : '';

	/* ---- Logo (mirrors disphtml() in adminUtils.php) ---- */
	$logo = strtoupper(isset($_SESSION['nick_name']) ? $_SESSION['nick_name'] : '');
	$final_logo = $logo.'.png';
	if(!file_exists($_SERVER['DOCUMENT_ROOT']."/logo/".$final_logo))
		$final_logo = 'CSPL.png';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?=SITETITLE?></title>
		<link href="<?=ADMIN_CSS?>" rel="stylesheet" type="text/css" />
		<style type="text/css">
			/* Self-contained single REPORT menu with an OTHER REPORTS sub-menu (CSS hover dropdown) */
			.bd-menu { position: relative; display: inline-block; }
			.bd-menu > a.bd-menu-top { display: inline-block; cursor: pointer; }
			.bd-submenu {
				display: none; position: absolute; left: 0; top: 100%;
				background: #A92A61; min-width: 160px; z-index: 1000;
				box-shadow: 0 2px 6px rgba(0,0,0,0.3);
			}
			.bd-menu:hover .bd-submenu { display: block; }
			.bd-submenu a {
				display: block; padding: 7px 14px; color: #FFFFFF;
				text-decoration: none; white-space: nowrap; font-weight: bold;
			}
			.bd-submenu a:hover { background: #7d1f48; }
		</style>
	</head>

	<form name="frm_logout" action="login.php" method="post">
		<input name="mode" type="hidden" value="logout">
	</form>

	<body>
		<script language="JavaScript" type="text/javascript" src="adminEssential.js"></script>

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
			<tr>
				<td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<!-- ===== Header / logo / logout / versions / welcome ===== -->
						<tr>
							<td class="header" style="background: url(<?=ROOT_BASE_URL."/logo/".$final_logo?>) 2% 50% no-repeat #FFFFFF; background-size: 70px 40px;"><b>Administrator Control Panel</b>
								<br>
								<a href="#" style="color: #e40000" onclick="javascript:logout();">
									<img src="images/log-out.png" alt="Logout" /></a><br />
								<b style="color:#A92A61;">Logout</b><br />
								<b style="font-size:9px;">APP Version: <?=$app_version?><br /> DB Version: <?=$db_version?></b>
								<div style="width:50%; font-size:14px; color:#990000; text-align:left; font-style:italic; margin-top:15px;"><strong><?=$welcome_message?></strong></div>
							</td>
						</tr>

						<!-- ===== Menu bar: single REPORT menu with OTHER REPORTS sub-menu ===== -->
						<tr>
							<td class="menubar">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<div class="bd-menu">
												<a href="#" class="bd-menu-top" onclick="return false;">REPORT</a>
												<div class="bd-submenu">
													<a href="star_survey_report_modified_one_BD.php">OTHER REPORTS</a>
														<?php if (strtoupper($_SESSION['admin_login']) == 'E0658') { ?>
													<a href="admin_ocr_report.php" class="bd-menu-top">OCR REPORT</a>
													<a href="exceldownloadSurveyStar.php" class="bd-menu-top">Survey Download</a>
													<a href="api_ship_to_party_location_report.php" class="bd-menu-top">Ship To Party Location Report</a>
													<a href="site_lead_latt_long_edit.php" class="bd-menu-top">Site Lead Latt Long Reset</a>
														<?php } ?>
												
												
											</div>
</div>
											<?php if (strtoupper($_SESSION['admin_login']) == 'ADMIN' || strtoupper($_SESSION['admin_login']) == 'SUPERVISOR') { ?>
											<div class="bd-menu" style="margin-left: 20px;">
												<a href="#" class="bd-menu-top" onclick="return false;">PERMISSION</a>
												<div class="bd-submenu">
													<a href="menu_access_bd.php">BD Menu Permission</a>
												</div>
											</div>
											<?php } ?>
											<?php if (strtoupper($_SESSION['admin_login']) == 'E0658') { ?>
											<div class="bd-menu" style="margin-left: 20px;">
												<a href="attendance_download_star_modified.php" class="bd-menu-top">ATTENDANCE DOWNLOAD</a>
												
											</div>
<?php } ?>
											<div class="bd-menu" style="margin-left: 20px;">
												<a href="#" class="bd-menu-top" onclick="return false;">ACCOUNT</a>
												<div class="bd-submenu">
													<a href="adminChangepassword_BD.php">Change Password</a>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td style="height:5px">&nbsp;</td>
						</tr>

						<!-- ===== Body: BD dashboard tile ===== -->
						<tr>
							<td valign="top">
								<table width="60%" align="center" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td valign="top" class="butterfly" id="fly2">
											<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td valign="top" class="butterfly" id="fly1"><a href="star_survey_report_modified_one_BD.php" style="cursor:pointer;"><img src="images/mis.png" alt="report" /></a></td>
												</tr>
												<tr>
													<td align="center" valign="top"><a href="star_survey_report_modified_one_BD.php" style="cursor:pointer; color:blue; font-weight:bold; font-size:14px; text-decoration:none;">OTHER REPORTS</a></td>
												</tr>
											</table>
										</td>
										<td valign="top" class="butterfly" id="fly2">
											<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td valign="top" class="butterfly" id="fly1"><a href="adminAttendanceTracker_BD.php" style="cursor:pointer;"><img src="images/attendance-tracker.png" alt="attendance" /></a></td>
												</tr>
												<tr>
													<td align="center" valign="top"><a href="adminAttendanceTracker_BD.php" style="cursor:pointer; color:blue; font-weight:bold; font-size:14px; text-decoration:none;">ATTENDANCE TRACKER</a></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td>&nbsp;</td>
						</tr>

						<!-- ===== Footer ===== -->
						<tr>
							<td valign="bottom" align="center" height="20">
								<font style="color:#0A246A;size=1;face:Verdana;">Copyright &copy; <?=date('Y');?> - <?=(date('Y')+1);?>  - All Rights Reserved</font>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
<?php
	if(isset($link))
		mysqli_close($link);
	ob_end_flush();
?>