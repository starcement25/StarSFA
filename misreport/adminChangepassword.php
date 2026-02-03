<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");


if($_POST)
	postdata();
else 
	disphtml("getdata();");
	
ob_end_flush();

function getdata()
{
		$sql_pwd = "SELECT admin_pwd FROM admin_master WHERE admin_login = 'admin'";
		$res_pwd = mysqli_query($link,$sql_pwd);
		$row_pwd = mysqli_fetch_assoc($res_pwd);
		
		
		//echo "Hello";
		echo "<center>";
		echo "
		<form name=\"provide_access\" method=\"POST\" action=\"adminChangepassword.php\" onsubmit=\"return validate();\">
		<input type=\"hidden\" name=\"emp_code\" value=\"admin\">
		<table width=\"500px\" >
			<tr> 
					<td width=\"90%\" align=\"center\" ><font color='#00CC33'><strong>".$GLOBALS['msg']."</strong></font></td>
					<td width=\"\" align=\"right\"></td>
				</tr>
		</table>		
		<table width=\"500px\" style=\"border-collapse:collapse;\" border=\"1\" class=\"border\">
		  <tr class=\"TDHEAD\">
			<td colspan=\"2\" align=\"center\" >Change Password</td>
		  </tr>
		  <tr>
			<td colspan=\"2\">All <font color='red'><strong>*</strong></font> fields are mandatory</td>
		  </tr>
		  <tr>
			<td align=\"right\">Old password</td>
			<td><strong>".$row_pwd['admin_pwd']."</strong></td>
		  </tr>
		  <tr>
			<td align=\"right\">Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"password\" class=\"INPUT\" name=\"new_pass\" id=\"new_pass\" value='".$row_provideaccess['admin_pwd']."'></td>
		  </tr>
		  <tr>
			<td align=\"right\">Confirm Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"password\" class=\"INPUT\" name=\"confirm_new_pass\" id=\"confirm_new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td></td>
			<td align=\"left\">
			<input type=\"submit\" name=\"submit\" value=\"Change\" class=\"inplogin\" />&nbsp;&nbsp;&nbsp;
			<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" class=\"inplogin\" onclick=\"window.location='adminMain.php';\">
			</td>
		  </tr>
		</table>
		</form>
		";
		echo "</center>";
?>
	<script>
	function validate()
	{
		var new_pass = document.getElementById("new_pass").value;
		if(document.getElementById("new_pass").value.search(/\S/) == -1)
		{
			alert("Enter password");
			document.getElementById("new_pass").focus();
			return false;
		}
		
		var confirm_new_pass = document.getElementById("confirm_new_pass").value;
		if(document.getElementById("confirm_new_pass").value.search(/\S/) == -1)
		{
			alert("Confirm password");
			document.getElementById("confirm_new_pass").focus();
			return false;
		}
		
		if(new_pass != confirm_new_pass)
		{
			alert("Confirm password doesnot match new password");
			return false;
		}
		
		if(document.getElementById("cancel").value || document.getElementById("new_pass").value == '')
		return true;
	}
	</script>
<?php
}
?>

<?php
function postdata()
{
	if($_POST['submit'] == 'Change')
	{
		$emp_code=$_POST['emp_code'];
		$sql_backend_access = "SELECT admin_login,admin_pwd FROM admin_master WHERE admin_login='".$emp_code."'";
		$res_backend_access = mysqli_query($link,$sql_backend_access);
		$count_backend_access=mysqli_num_rows($res_backend_access);
		if($count_backend_access >0)
		{

		$sql_update_access = "UPDATE admin_master SET admin_pwd = '".$_POST[new_pass]."' WHERE admin_login = '".$_POST[emp_code]."'";
		if(mysqli_query($link,$sql_update_access))
			{
				
				$GLOBALS['msg']="Password successfully changed";
				disphtml("getdata();");
			}
			else
			{
				$GLOBALS['msg']="Error in password changing";
				disphtml("getdata();");
			}

		}
		else
		{
			$sql_insert_access = "INSERT INTO admin_master SET admin_pwd = '".$_POST[new_pass]."', 
														      admin_login = '".$_POST[emp_code]."'";
		   if(mysqli_query($link,$sql_insert_access))
			{
				echo $GLOBALS['msg']="Password successfully created";
				disphtml("getdata();");
			}
			else
			{
				echo $GLOBALS['msg']="Error in password creation";
				disphtml("getdata();");
			}
		}
	}
}
?>
</center>


