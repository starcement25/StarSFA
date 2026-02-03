<?php
ob_start();

	session_start();

	require("adminUtils.php");



	if($_POST['mode']=="login")  						login();

	elseif($_POST['mode']=="logout")   					logout();

	elseif($_POST['mode']=="forgot_password")   			disphtml("forgot_password();");

	elseif($_POST['mode']=="send_password") 			  send_password();

	else  												 disphtml("showLogin();");



ob_end_flush();



function showLogin()
{
	if($_SESSION['admin_login']!="")
	{
		header("Location: adminMain.php");
	}
?>
<script language="JavaScript">

function set_mode()

{

	document.frm_login.mode.value = "forgot_password";

	document.frm_login.submit();

	return true;

}

function check(form)
{
	if (document.frm_login.nick_name.value.search(/\S/)==-1) 
	{

		alert('Please enter your Nick name');

		document.frm_login.nick_name.focus();

		return false;
	}
	if (document.frm_login.admin_login.value.search(/\S/)==-1) 
	{

		alert('Please enter your Login');

		document.frm_login.admin_login.focus();

		return false;
	}

	if (document.frm_login.admin_pwd.value.search(/\S/)==-1) 

	{

		alert('Please enter your password.');

		document.frm_login.admin_pwd.focus();

		return false;

	}

	return true;

}

</script>

	

	<form name="frm_login" action="login.php" method="post" onSubmit="return check(this);">

	<input type="hidden" name="mode" value="login">

	<table>

		<tr>

			<td><br><br></td>

		</tr>

	</table>

	<br><br><br>

	<table width="40%" cellpadding="5" cellspacing="0" border="0" align="center" class="border">

		<tr>

			<td class="TDHEAD" colspan="3">Administration Page Login</td>

		</tr>

		<tr>

			<td align="center" class="ERR" colspan="3"><?=$GLOBALS['err_msg'];?></td>

		</tr>
		<tr>

			<td width="25%" align="right">Nick name</td>

			<td width="5%" align="center">:</td>

			<td align="left"><input name="nick_name" type="text" value="" maxlength="50" class="inplogin" autofocus="autofocus"></td>

		</tr>
		<tr>

			<td width="25%" align="right">Login</td>

			<td width="5%" align="center">:</td>

			<td align="left"><input name="admin_login" type="text" value="" maxlength="50" class="inplogin"></td>

		</tr>

		<tr>

			<td valign="top" align="right">Password</td>

			<td valign="top" align="center">:</td>

			<td align="left"><input name="admin_pwd" type="password" value="" maxlength="20" class="inplogin"></td>

		</tr>

		<tr>

			<td colspan="2">&nbsp;</td>

			<td align="left"><input type="submit" value=" Login " class="inplogin">&nbsp;&nbsp;&nbsp;<a href="#" onClick="javascript:set_mode();" class="l2">Forgot Password?</a></td>

		</tr>

	</table>

	</form>

<script language="JavaScript">
document.frm_login.admin_login.focus();
</script>
<?
}

function login()
{

	$login_sql = "SELECT * FROM ".ADMIN_MASTER." WHERE admin_login = '".$_POST[admin_login]."' AND BINARY admin_pwd = '".$_POST[admin_pwd]."'";

	$login_rs = mysql_query($login_sql) or die(mysql_error()." Error in Login: ".$login_sql);



	if($login_row=mysql_fetch_array($login_rs))
	{	
		session_register("admin_id");
		session_register("admin_login");

		

		$_SESSION['admin_id'] 		= $login_row['admin_id'];
		$_SESSION['admin_login'] 	= $login_row['admin_login'];


		header('location: adminMain.php');
	}

	else
	{
		$GLOBALS['err_msg']="Invalid Login or Password.";
		disphtml("showLogin();");
	}
}

function logout()
{
	if($_SESSION['admin_login'] != "")  
	{
		 $_SESSION['admin_id'] = "";
		 $_SESSION['admin_login'] = "";
		 $_SESSION['flag'] = "";
		 unset($_SESSION['admin_id']);
		 unset($_SESSION['admin_login']);
		 unset($_SESSION['flag']);
		 session_destroy();
	}

	$GLOBALS['err_msg']="You are Logged Out.";
	disphtml("showLogin();");
}



function forgot_password()
{
?>

	<script type="text/javascript">

	function forgot_pass()

	{

		if (document.frm_email.username.value.search(/\S/)==-1) 

		{

			alert('Please enter your Login Id');

			document.frm_email.username.focus();

			return false;

		}

	   if (document.frm_email.email.value.search(/\S/)==-1) 

		{

			alert('Please enter your email.');

			document.frm_email.email.focus();

			return false;

		}

	   if(document.frm_email.email.value)

		{

		 var x = document.frm_email.email.value;

		 var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		 if (filter.test(x)==false)

		  {	

			alert("Enter valid Email Id.");

			document.frm_email.email.value="";

			document.frm_email.email.focus();

			return false;

		 }

	   }

	return true;

	}

	</script>

	<form name="frm_email" action="<?=$_SERVER['PHP_SELF'];?>" method="post" onSubmit="return forgot_pass();">

	<input type="hidden" name="mode" value="send_password">

	<table><tr><td><br><br></td></tr></table><br><br><br>

	<table width="45%" cellpadding="5" cellspacing="0" border="0" align="center" class="border">



	<tr>

		<td class="TDHEAD" colspan="3">Forgot Password?</td>

	</tr>

	<tr>

		<td align="center" class="ERR" colspan="3"><?=$GLOBALS['error_msg'];?></td>

	</tr>

	<tr>

		<td width="40%" align="right">Login Id</td>

		<td width="5%" align="center">:</td>

		<td align="left"><input name="username" type="text" value="" maxlength="50" class="inplogin"></td>

	</tr>

	<tr>

		<td width="40%" align="right">Email</td>

		<td width="5%" align="center">:</td>

		<td align="left"><input name="email" type="text" value="" maxlength="50" class="inplogin"></td>

	</tr>

	<tr>

		<td colspan="2">&nbsp;</td>

		<td><input type="submit" value="Submit" class="inplogin">&nbsp;&nbsp;&nbsp;<input type="button" onClick="javascript:window.location='login.php';" value="Cancel" class="inplogin"></td>

	</tr>

	</table>

	</form>

	

	<script language="JavaScript">

	<!--

	document.frm_email.username.focus();

	function check_mail(form)

	{

		var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";

		var jChars = " ";

		if(form.username.value.search(/\S/)==-1)

		{	

			alert("Login Id should not be blank.");

			form.username.focus();

			return false;

		}

		

	   for (var i = 0; i < form.username.value.length; i++) 

	   {

			if (iChars.indexOf(form.username.value.charAt(i)) != -1)

			{

				alert ("Login Id has special characters. \nThese are not allowed.\n Please try again.");

				form.username.value="";

				form.username.focus();

				return false;

			}

		}

		for (var j = 0; j < form.username.value.length; j++) 

		{

			if (jChars.indexOf(form.username.value.charAt(j)) != -1)

			{

				alert ("Login Id has Space. \nThis is not allowed.\n Please try again.");

				form.username.value="";

				form.username.focus();

				return false;

			}

		}

		if (form.email.value.search(/\S/)==-1) 

		{

			alert('Please enter your email address');

			form.email.focus();

			return(false);

		}

		if(form.email.value)

		{

			var x = form.email.value;

			var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

			if (filter.test(x)==false)

			{	

				alert("Enter valid email Id.");

				form.email.value="";

				form.email.focus();

				return false;

			}

		}

		return true;

	}

	//-->

	</script>

<? }



function send_password()

{

	$sql = "SELECT admin_pwd FROM ".ADMIN_MASTER." WHERE admin_login = '".$_POST['username']."' AND admin_email = '".$_POST['email']."'";

	$rs = mysql_query($sql) or die(mysql_error()." Error in Forgot Password.");



	if(mysql_num_rows($rs)>0)

	{	    

		$rec = mysql_fetch_array($rs);

		

		$uname = $_POST['username'];

		$user_pwd = $rec['admin_pwd'];

		$user_email = $_POST['email'];

		

		$subject = "Your password information in FORCEPOWER Administrator Control Panel!";	

		

		$message  = "Hello ".$uname.",";	

		$message .= "<br><br>As per your request, here is your Username and Password:<br>";

		$message .= "<strong>Username :</strong> ".$uname."<br>";

		$message .= "<strong>Password :</strong> ".$user_pwd."<br>";

		$message .= "Thanks you very much. <br><br>";

		$message .= "Sincerely, <br>";

		$message .= "Admin.<br>";

		$message .= "(FORCEPOWER)<br>\n\n";

				

		$headers  = "MIME-Version: 1.0\r\n";

		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

		$headers .= "From: aceDns <acedns@coral.in>\r\n";

		

		mail($user_email, $subject, $message, $headers);

		

		$GLOBALS['err_msg'] = "password Info has been sent to your email.";     

		disphtml("showLogin();");

	}

	else

	{

		$GLOBALS['error_msg']="This Email address does not exist.";

		disphtml("forgot_password();");

	}

}	

?>

