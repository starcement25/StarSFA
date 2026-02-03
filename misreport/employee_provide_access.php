<?php

// check error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

ob_start();
session_start();
require("adminUtils.php");

require("attribute_selection.php");

if ($_SESSION['admin_login'] == "")  		header("product:index.php");
// echo 456354634563;
disphtml("main();");


function main()
{
	require("include/dbcon.php");

	$emp_code = $_REQUEST['emp_code'];
	$sql_changepassword = "SELECT * FROM changepassword WHERE emp_code = '" . $emp_code . "'";
	$res_changepassword = mysqli_query($link, $sql_changepassword);
	$row_changepassword = mysqli_fetch_assoc($res_changepassword);

	$is_licensed = $row_changepassword['is_licensed'];
	$oldpassword = $row_changepassword['oldpassword'];
	$newpassword = $row_changepassword['newpassword'];

	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '" . $emp_code . "'";
	$res_emp_name = mysqli_query($link, $sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$emp_name = $row_emp_name['emp_name'];

	if ($_POST) {
		$emp_code = $_REQUEST['row_id'];
		$pwd_sql = "SELECT * FROM changepassword WHERE  emp_code = '" . $emp_code . "'";
		$pwd_res = mysqli_query($link, $pwd_sql) or die(mysqli_error($link) . " Error in fetch details");

		$pwd_sql_admin = "SELECT * FROM admin_master WHERE  admin_login = '" . $emp_code . "'";
		$pwd_res_admin = mysqli_query($link, $pwd_sql_admin) or die(mysqli_error($link) . " Error in fetch admin details");


		$sqlemp = "SELECT emp_name FROM employee_master WHERE emp_code = '" . $emp_code . "'";
		$rsemp = mysqli_query($link, $sqlemp) or die(mysqli_error($link) . " Error in show emp name: " . $sqlemp);
		$rowemp = mysqli_fetch_assoc($rsemp);
		$emp_name	= $rowemp['emp_name'];

		$sqlemail = "SELECT admin_email FROM admin_master WHERE admin_id = 1";
		$rsemail = mysqli_query($link, $sqlemail) or die(mysqli_error($link) . " Error in fetch admin email: " . $sqlemail);
		$rowemail = mysqli_fetch_assoc($rsemail);
		$admin_email = $rowemail['admin_email'];
		// check if $_POST['newpassword'] is not empty
		$oldpassword = $_POST['oldpassword'] ?? '';
		$newpassword = $_POST['newpassword'] ?? '';

		if (mysqli_num_rows($pwd_res) > 0) {
			
			if (trim($newpassword) != '' && trim($oldpassword) != '') {
				$upd_sql = "UPDATE changepassword SET newpassword ='" . trim($newpassword) . "',
					 oldpassword ='" . trim($oldpassword) . "' WHERE emp_code = '" . $emp_code . "'";
				mysqli_query($link, $upd_sql) or die(mysqli_error($link) . " Error in pasword updation.");
			}

			$sqlupdateemp = "UPDATE employee_master SET acedns='" . $_POST['status'] . "',acedns_changed_date=CURRENT_TIMESTAMP 
								WHERE emp_code = '" . $emp_code . "'";
			mysqli_query($link, $sqlupdateemp) or die(mysqli_error($link) . " Error in employee acedns updation.");

			$GLOBALS['err_msg'] = "Employee Access has been Provided Successfully.";
		} else {
			$ins_sql = "INSERT INTO  changepassword SET 
					  newpassword ='" . trim($newpassword) . "',
					  oldpassword ='" . trim($oldpassword) . "',
					  emp_code = '" . $emp_code . "'";
			mysqli_query($link, $ins_sql) or die(mysqli_error($link) . " Error in pasword insertion.");

			$GLOBALS['err_msg'] = "Employee Access has been Provided Successfully.";
		}
		if (mysqli_num_rows($pwd_res_admin) > 0) {
			$upd_sql_admin = "UPDATE admin_master SET admin_pwd ='" . trim($newpassword) . "' WHERE admin_login = '" . $emp_code . "'";
			mysqli_query($link, $upd_sql_admin) or die(mysqli_error($link) . " Error in pasword updation admin.");
		}
		echo "<script>";
		echo "alert('Updated Successfully');";
		echo "window.location.href='employee_provide_access.php?emp_code=" . $emp_code . "'";
		echo "</script>";
	}

?>
	<center>
		<form name="frmedit" method="POST" action="#">
			<input type="hidden" name="row_id" value="<?= $emp_code ?>">
			<table width="50%" align="center" class="border" cellpadding="5" cellspacing="2">
				<tr class="TDHEAD">
					<td colspan="3" align="left">Provide Access To "<?= $emp_name ?>"</td>
				</tr>
				<tr>
					<td align="left" colspan="3">All <font color="#FF0000"><strong>*</strong></font> marked fields are mandetory.</td>
				</tr>
				<? if ($GLOBALS['err_msg'] != "") { ?>
					<tr>
						<td align="center" colspan="3" class="ERR"><strong>
								<font color="#FF0000"><?= $GLOBALS['err_msg'] ?></font>
							</strong></td>
					</tr>
				<? } ?>
				<tr>
					<td width="45%" align="right" valign="top" class="tbllogin">Old Password<font color="#FF0000"><strong>*</strong></font>
					</td>
					<td width="5%" align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="oldpassword" value="<?= $oldpassword ?>" class="inplogin" maxlength="20"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">New Password<font color="#FF0000"><strong>*</strong></font>
					</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="newpassword" value="<?= $newpassword ?>" class="inplogin" maxlength="20"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Confirm New Password<font color="#FF0000"><strong>*</strong></font>
					</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top"><input type="text" name="conf_newpassword" value="" class="inplogin" maxlength="20"></td>
				</tr>
				<tr>
					<td align="right" valign="top" class="tbllogin">Status</td>
					<td align="center" valign="top" class="tbllogin">:</td>
					<td align="left" valign="top">
						<select name="status" class="inplogin" id="status">
							<option value="Y" <?php if ($is_licensed == 'Y') {
													echo 'selected';
												} ?>>unblocked</option>
							<option value="N" <?php if ($is_licensed == 'N') {
													echo 'selected';
												} ?>>blocked</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="submit" name="submit" value=" Change " class="inplogin">&nbsp;&nbsp;<input type="button" name="btn" value="Cancel" onClick="close_window();" class="inplogin"></td>
				</tr>
			</table>
		</form>
	</center>
	<script>
		function close_window() {
			window.close();
		}
	</script>
<?php
}

?>