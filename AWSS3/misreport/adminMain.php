<?php
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
	if($_SESSION['admin_login'] == '')   		header('location: index.php');

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
    </table>
<? 
}
?>