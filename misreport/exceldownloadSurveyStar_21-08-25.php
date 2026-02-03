<?php
//ob_start();
// error_reporting(E_ALL);
echo 656253635;
die;
ini_set('display_errors', 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (strpos(strtolower($_SESSION['sale_access']), 'vendor') != false && (strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START')) {
	require("adminUtils_branding.php");
} else {
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");

require("attribute_selection.php");
if ($_SESSION['admin_login'] == "")  		header("location:index.php");

$mode = $_REQUEST['mode'];
if ($mode == 'excel_download')		excelDownload();

else disphtml("main();");
ob_end_flush();

function main()
{
	require("include/dbcon.php");
?>
	<form name="frmSearch" method="post" action="<?= $_SERVER['PHP_SELF'] ?>" onSubmit="javascript:return check();">
		<input type="hidden" name="mode" value="excel_download">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="ajax1.js"></script>
		<script language="JavaScript" src="calendar3.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="ajax1.js"></script>
		<script type="text/javascript" src="jquery.highlight.js"></script>
		<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
		<!-- polyfiller file to detect and load polyfills -->
		<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
		<script>
			webshims.setOptions('waitReady', false);
			webshims.setOptions('forms-ext', {
				types: 'date'
			});
			webshims.polyfill('forms forms-ext');
		</script>
		<?php
		$hidden = "";
		echo "<center>";
		echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
		echo "<span style=\"font-weight:bold; font-size:14px;\">Download Site Lead Conversion and Complaint Excel</span><br><br>";
		attribute_selection($hidden, $create_control = '');
		echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
		echo "<br>";
		?>
		<?php //if(strtoupper($_SESSION['nick_name'])!='DURO'){
		?>
		<script language="javascript">
			function check() {
				var start_date = document.getElementById("start_date").value;
				var end_date = document.getElementById("end_date").value;
				if (document.getElementById("zone").value.search(/\S/) == -1) {
					alert('Please Select Zone');
					return false;
				} else if (document.getElementById("state").value.search(/\S/) == -1) {
					alert('Please Select State');
					return false;
				} else if (document.getElementById("branch").value.search(/\S/) == -1) {
					alert('Please Select Branch');
					return false;
				} else if (document.getElementById("sale_access").value.search(/\S/) == -1) {
					alert('Please Select Department');
					return false;
				} else if (document.getElementById("employee").value.search(/\S/) == -1) {
					alert('Please Select Employee');
					return false;
				} else if (document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1) {
					alert("Please provide start date/end date");
					return false;
				} else if (start_date > end_date) {
					alert("Start date cannot be greater than end date");
					return false;
				}
				return true;

			}
		</script>

		<!--table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Download Survey Excel</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? /*echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <!--table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="excel_download">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB"> 
                        <td width="15%" align="center"></td>
                        <table width="65%" align="center" border="0" cellpadding="5" cellspacing="1"  class="border">
                       
                            <tr id="datedropdown" >
                                <td align="left" width="15%">From Date:</td>
                                <td align="left" width="30%" style="vertical-align:top;">
                                		<?php $from_date=$_REQUEST['from_date'];?>
                                      <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date"></input>&nbsp;
                                        <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                                        cal5.year_scroll = true;
                                        cal5.time_comp = false;
                                        //-->
                                    <!--/script>
                                </td>
                                <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
                                <td width="" style="vertical-align:top;">
                                    <?php $to_date=$_REQUEST['to_date'];?>
                                     <input id="textinput3" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="to_date"></input>&nbsp;
                                        <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" width="20" height="18" ></a>
                                    </label>
                                    <script language="JavaScript" type="text/javascript">
                                        <!-- // create calendar object(s) just after form tag closed
                                         // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                                         // note: you can have as many calendar objects as you need for your application
                                        var cal6 = new calendar3(document.forms['frmSearch'].elements['to_date']);
                                        cal6.year_scroll = true;
                                        cal6.time_comp = false;
                                        //-->
                                    <!--/script>
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="4" align="center">Select Employee:<select name="search_emp_name">
                                <option value="">Select</option>
                                <option value="all">All</option>                                
                                <?php
								$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
								$res_select_emp = mysqli_query($link,$sql_select_emp);
								while($row_select_emp = mysqli_fetch_assoc($res_select_emp))
								{
									if($_POST['search_emp_name'] == $row_select_emp['emp_code'])
										echo "<option value='$row_select_emp[emp_code]' selected>".$row_select_emp['emp_name']."</option>";
									else
										echo "<option value='$row_select_emp[emp_code]'>".$row_select_emp['emp_name']."</option>";
								}*/
														?>
                                </select>
                                </td>
                            </tr>
                            <tr>
                                 <td align="center" width="" style="padding-left:10px;" colspan="4">
                                    <input type="submit" value="Submit" class="inplogin" onclick="javascript:showRdswisesalesDeatails();">
                                    <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"-->
		<!--/td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table-->
		<?php //} 
		?>
		<br />
	</form>
<?php } //End of main()

?>