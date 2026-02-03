 <?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//ob_start();
	session_start();

	if(isset($_POST['mode']) && $_POST['mode']=="logout") {
    
    $_SESSION = array();
    
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
   
    session_destroy();
   
    header("Location: login.php");
    exit;
}
	if (isset($_SESSION['admin_login']) && $_SESSION['admin_login'] != "") {
    header("Location: adminMain.php");
    exit;
}

	require("adminUtils.php");

	



	require("include/config.php");

//	/require("include/config-setup.php");



	if(isset($_POST['mode']) && $_POST['mode']=="login")  							login();

	elseif(isset($_POST['mode']) && $_POST['mode']=="logout")   						logout();

	elseif(isset($_POST['mode']) && $_POST['mode']=="forgot_password")   			disphtml("forgot_password();");

	elseif(isset($_POST['mode']) && $_POST['mode']=="send_password") 				send_password();

	else  													disphtml("showLogin();");



//ob_end_flush();



function showLogin()
{
	if(isset($_SESSION['admin_login']) && $_SESSION['admin_login']!="")
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

			<td align="center" class="ERR" colspan="3"><?=isset($GLOBALS['err_msg']) ?? $GLOBALS['err_msg'];?></td>

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

<?php

}



function login()
{
	error_reporting(E_ALL);
ini_set('display_errors', '1');
// require("include/config.php");

	require("include/dbcon.php");
	$login_sql = "SELECT * FROM ".ADMIN_MASTER." WHERE admin_login = '".addslashes($_POST['admin_login'])."' AND BINARY admin_pwd = '".addslashes($_POST['admin_pwd'])."'";
//	exit();

	$login_rs = mysqli_query($link,$login_sql) or die(mysqli_error()." Error in Login: ".$login_sql);

	$nick_name = trim($_POST['nick_name']);
	
	if(strtoupper($nick_name) == "LIPL")
	{
		session_destroy();
		header('location:login.php');
	}

	if($login_row=mysqli_fetch_assoc($login_rs))
	{	
		//sk add start line 130525
		//echo"<pre>";print_r($login_row);die;
		$admin_login=$login_row['admin_login'];
		$sql = "SELECT level FROM employee_master WHERE emp_code = '".$admin_login."' LIMIT 1";
		//echo"<pre>";print_r($sql);die;

		$result = mysqli_query($link,$sql);
		
		$level = null;
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$level = $row["level"];
		}
		
		$_SESSION['level']=$level;
		//sk add end line 130525

		//session_register("admin_id");
		//session_register("admin_login");
		$_SESSION['admin_id'] 		= $login_row['admin_id'];
		$_SESSION['admin_login'] 	= $login_row['admin_login'];
		$_SESSION['nick_name']	 =$nick_name;
		$_SESSION['mobile_no']=$login_row['admin_phone_no'];
		
	
        /*if(sauda_allocation == 'yes' && strtoupper($_SESSION['admin_login']) == 'ADMIN' && strtoupper($_SESSION['nick_name']) == 'EMAMI')
		{
			//header("Location: sauda_report_main.php");
			$sql_emp_vertical=mysqli_fetch_assoc(mysqli_query($link,"SELECT `vertical_value` FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
			$_SESSION['vertical_value']=$sql_emp_vertical['vertical_value'];
			header("Location: emamigui/emamigui.php");
		}
		elseif(sauda_allocation == 'yes' && (strtoupper($_SESSION['admin_login']) == 'HBC' || strtoupper($_SESSION['admin_login']) == 'SFATS' || 
		strtoupper($_SESSION['admin_login']) == 'E0193'))
		{
			if(strtoupper($_SESSION['admin_login']) == 'HBC' || strtoupper($_SESSION['admin_login']) == 'E0193')
			{
				$vertical="HBC:Rasoi:BIB";
			}
			if(strtoupper($_SESSION['admin_login']) == 'SFATS')
			{
				$vertical="Specialty Fats";
			}
			$_SESSION['vertical_value']=$vertical;
			header("Location: CsvDownloadSaudaModified.php	");
		}
		elseif(sauda_allocation == 'yes' && (strtoupper($_SESSION['admin_login']) == 'PRICEHBC'))
		{
			if(strtoupper($_SESSION['admin_login']) == 'PRICEHBC')
			{
				$vertical="HBC:Rasoi:BIB";
			}
			$_SESSION['vertical_value']=$vertical;
			header("Location: depotwise_pricelist_report_verticalwise.php");
		}
		elseif(sauda_allocation == 'yes' && (strtoupper($_SESSION['admin_login']) == 'PRICESFATS'))
		{
			if(strtoupper($_SESSION['admin_login']) == 'PRICESFATS')
			{
				$vertical="Specialty Fats";
			}
			$_SESSION['vertical_value']=$vertical;
			header("Location: depotwise_pricelist_report_verticalwise.php");
		}
		elseif(sauda_allocation == 'yes' && strtoupper($_SESSION['admin_login']) == 'ADMIN' && strtoupper($_SESSION['nick_name']) == 'EMAMIT')
		{
			$sql_emp_vertical=mysqli_fetch_assoc(mysqli_query($link,"SELECT `vertical_value` FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
			$_SESSION['vertical_value']=$sql_emp_vertical['vertical_value'];
			header("Location: emamigui/emamigui.php");
		}
		elseif(sauda_allocation == 'yes' && (strtoupper($_SESSION['admin_login']) == 'E0042' || strtoupper($_SESSION['admin_login']) == 'E0076' 
		|| strtoupper($_SESSION['admin_login'])=="ED01" || strtoupper($_SESSION['admin_login'])=="GMHBC" || strtoupper($_SESSION['admin_login'])=="GMSFATS") 
		&& (strtoupper($_SESSION['nick_name']) == 'EMAMIT' || strtoupper($_SESSION['nick_name']) == 'EMAMI'))
		{
			
			$sql_emp_vertical=mysqli_fetch_assoc(mysqli_query($link,"SELECT `vertical_value` FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
			$_SESSION['vertical_value']=$sql_emp_vertical['vertical_value'];
			if(strtoupper($_SESSION['admin_login'])=="ED01") 	 $_SESSION['vertical_value']='HBC:Rasoi:BIB';
			 if(strtoupper($_SESSION['admin_login'])=="GMHBC")
			 {
				 $_SESSION['admin_login']='E0042'; 
				 $_SESSION['vertical_value']='HBC:Rasoi:BIB';
			 }
			if(strtoupper($_SESSION['admin_login'])=="GMSFATS"){
				$_SESSION['admin_login']='E0076';   
				$_SESSION['vertical_value']='Specialty Fats';
			}
			if(strtoupper($_SESSION['nick_name']) == 'EMAMIT')
			{
				header("Location: sauda_report_main.php");
			}
			if(strtoupper($_SESSION['nick_name']) == 'EMAMI')
			{
				header("Location: emamigui/emamiguinew.php");
			}
		}
		else if(sauda_allocation == 'yes' && strtoupper($_SESSION['admin_login']) != 'ADMIN' && strtoupper($_SESSION['admin_login']) != 'E0042' && strtoupper($_SESSION['admin_login']) != 'E0076'){
			
			$sql_emp_vertical=mysqli_fetch_assoc(mysqli_query($link,"SELECT `vertical_value` FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
			$_SESSION['vertical_value']=$sql_emp_vertical['vertical_value'];
			header("Location: sauda_report_main.php");
		}
		else
		{*/
			if(strtoupper($_SESSION['nick_name'])=='RKBKL'){
			header('location: adminLoyaltyReport.php');
			}
			else if((strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START') && $_SESSION['admin_login']!='E1697')
			{
				$sql_emp_sale_access=mysqli_fetch_assoc(mysqli_query($link,"SELECT sale_access FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
				if(strpos(strtolower($sql_emp_sale_access['sale_access']),'vendor')!=false){
					$_SESSION['sale_access']=$sql_emp_sale_access['sale_access'];
					 header("Location: branding_verification_vendor.php");
				}
				else
				{
					$_SESSION['sale_access']=$sql_emp_sale_access['sale_access'];
					header("Location: adminMain.php");
				}
			}
			else if(strtoupper($_SESSION['nick_name']) == 'PALSONS' && strtoupper(substr($_SESSION['admin_login'],0,1))=='C')
			{
				$sql_cust_type=mysqli_fetch_assoc(mysqli_query($link,"SELECT cust_type FROM customer_master WHERE customer_code='".$_SESSION['admin_login']."'"));
				if(strtoupper($sql_cust_type['cust_type'])=='D'){
					 header("Location: order_download_distributor.php");
				}
			}
			else if(strtoupper($_SESSION['admin_login'])=='ADMIN' && strtoupper($_SESSION['nick_name']) == 'SCHOOL')
				{
					 header("Location: latt_long_display.php");
				}
			else{
				if(($_SESSION['admin_login']=='emovesfa_hr' || $_SESSION['admin_login']=='E0674') && strtoupper($_SESSION['nick_name']) == 'STAR')
				{
					 header("Location: attendance_download_star.php");
				}
				else if((strtoupper($_SESSION['admin_login'])=='ACCOUNTS' || $_SESSION['admin_login']=='E1697') && strtoupper($_SESSION['nick_name']) == 'STAR')
				{
					 header("Location: star_pjp_report.php");
				}
				else if(strtoupper($_SESSION['nick_name']) == 'TECPL')
				{
					 header("Location: kiosk_cash_cheque_report.php?type=Cheque%20Deposit");
				}
				else if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM' || strtoupper($_SESSION['nick_name']) == 'PRABHUJI') 
				{
					$sql_emp_designation=mysqli_fetch_assoc(mysqli_query($link,"SELECT designation FROM `employee_master` WHERE `emp_code`='".$_SESSION['admin_login']."'"));
					$_SESSION['designation']=$sql_emp_designation['designation'];
					if($_SESSION['designation']=='CRM'){
					 	header("Location: customer_allocation_CRM.php");
					}
					else if($_SESSION['designation']=='CRE')
					{
						header("Location: cre_verification_modified.php");
					}
					else
					{
						header("Location: adminMain.php");
					}
				}
				else if(strtoupper($nick_name) == "AJANTA")
				{
					header('location:adminMisReport.php');
				}
				else if(strtoupper($nick_name) == "MAGIK" && strtoupper($_SESSION['admin_login']) == 'E0001')
				{
					header('location:adminOrderSubmit.php');
				}
				else if(strtoupper($nick_name) == "MAGIK" && strtoupper($_SESSION['admin_login']) == 'ADMIN')
				{
					header('location:adminOrderSubmit.php');
				}
				else
					header("Location: adminMain.php");
			//}
		}
	}
	else
	{
		$GLOBALS['err_msg']="Invalid Login or Password.";
		disphtml("showLogin();");
	}
}



// function logout()

// {

// 	if($_SESSION['admin_login'] != "")  
// 	{
// 		 $_SESSION['admin_id'] == "";
// 		 $_SESSION['admin_login'] == "";

// 		 $_SESSION['flag'] = "";
		 
// 		 unset($_SESSION['flag']);

// 		 unset($_SESSION['admin_id']);
// 		 unset($_SESSION['admin_login']);

		

// 		 session_destroy();
// 	}

// 	$GLOBALS['err_msg']="You are Logged Out.";

// 	disphtml("showLogin();");

// }
function logout()
{

	
    if($_SESSION['admin_login'] != "")  
    {
         $_SESSION['admin_id'] = "";      
         $_SESSION['admin_login'] = "";  
         
         $_SESSION['flag'] = "";
         
         unset($_SESSION['flag']);
         unset($_SESSION['admin_id']);
         unset($_SESSION['admin_login']);
         unset($_SESSION['nick_name']);    
         unset($_SESSION['mobile_no']);   
         unset($_SESSION['level']);        
         unset($_SESSION['vertical_value']); 
         unset($_SESSION['designation']);  
         unset($_SESSION['sale_access']);  
        
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

		<td align="center" class="ERR" colspan="3"><?=isset($GLOBALS['error_msg'])?$GLOBALS['error_msg']:'';?></td>

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

<?php }



function send_password()

{

	$sql = "SELECT admin_pwd FROM ".ADMIN_MASTER." WHERE admin_login = '".addslashes($_POST['username'])."' AND admin_email = '".addslashes($_POST['email'])."'";

	$rs = mysqli_query($link,$sql) or die(mysqli_error($link)." Error in Forgot Password.");



	if(mysqli_num_rows($rs)>0)

	{	    

		$rec = mysqli_fetch_assoc($rs);

		

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

