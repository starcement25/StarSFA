<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
	session_start();

	if(strtoupper($_SESSION['nick_name']) == 'STAR' && (strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' || strtoupper($_SESSION['admin_login']) == 'E0674'))
	{
		require("adminUtils_accounts.php");
	}
	else
	{
		require("adminUtils.php");
	}
	//if($_SESSION['admin_login'] == '')   		header('location: index.php');
	if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] == '') {
    header("Location: index.php");
    exit;
}

	disphtml("main();");

ob_end_flush();
function main()
{
	if(strtoupper($_SESSION['nick_name']) == 'STAR')
		$mis_url = "admin_mis_report_star.php";
	else if(strtoupper($_SESSION['nick_name']) == 'CASHLESS')
		$mis_url = "adminMisReportSurvey.php";
	else if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM')
		$mis_url = "adminMisReportEmphierarchy.php";
	else
		$mis_url = "adminMisReport.php";
		
	if(strtoupper($_SESSION['nick_name']) == 'PROPELLO')
		$activity_url = "sales_executive_wise_report.php";
	else
		$activity_url = "adminActivity.php";	
?>
    <table width="40%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	 <?php
         	if(strtoupper($_SESSION['nick_name']) != 'PROPELLO'){?>
            <td  valign="middle"   class="butterfly" id="fly2"><a href="<?php echo $mis_url; ?>" style="cursor:pointer;"><img src="images/mis.png" alt="" /></a></td>
            <?php }else{?>
            <td  valign="middle"   class="butterfly" id="fly2">&nbsp;</td>
            <?php }?>
            <td width="">
            	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                	<tr>
                    	<td  valign="top"  class="butterfly" id="fly1"><a href="adminAttendanceTracker.php" style="cursor:pointer;"><img src="images/attendance-tracker.png" alt="" /></a></td>
                    </tr>
                    <?php
                    if(strtoupper($_SESSION['nick_name']) != 'PALSONS'){?>
                    <tr>
                    	
                        <td  valign="top"  class="butterfly" id="fly2"><a href=<?php echo $activity_url;?> style="cursor:pointer;"><img src="images/activity.png" alt="" /></a></td>
                    </tr>
                     <?php }?>
                </table>
            </td>
        </tr>
        <?php
        /*--------> CUSTOMER VISIT REPORT DAYWISE - PRIMARY <--------*/
        /*  Shown only to STAR logins whose department (sale_access) is Primary  */
        $menu_sale_access = isset($_SESSION['sale_access']) ? strtoupper(trim($_SESSION['sale_access'])) : '';
        if(strtoupper($_SESSION['nick_name']) == 'STAR' && $menu_sale_access == 'PRIMARY'){ ?>
        <tr>
            <td colspan="2" align="center" style="padding-top:18px;">
                <a href="star_customer_visit_report_daywise_primary.php" title="CUSTOMER VISIT REPORT DAYWISE - PRIMARY"
                   style="display:inline-block; text-decoration:none; background:#9c1a55; color:#ffffff; font-weight:bold; font-size:13px; padding:10px 18px; border-radius:6px; border:1px solid #7a1243; cursor:pointer; white-space:nowrap;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:8px;">
                        <rect x="3" y="4" width="18" height="17" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                        <polyline points="8 15 11 18 16 13"></polyline>
                    </svg><span style="vertical-align:middle;">CUSTOMER VISIT REPORT DAYWISE - PRIMARY</span>
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php 
}
?>