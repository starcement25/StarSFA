<?php
define("USER_CSS","http://salesmpower.acedns.in/css/adminStyle.css");

if($_REQUEST['nick_name']!=''){

	$nick_name=substr(strtoupper($_REQUEST['nick_name']),0,4);
	//require("include/config.php");
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
		require("include/config-setup.php");

	$linkdbaccess=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysqli_select_db("acedns_$nick_name",$linkdbaccess) or die("could not connect the setup database");
	//require("include/dbcon.php");
	require("include/config-email-setup.php");
	//require("include/functions.php");
	//if($_SERVER['PHP_SELF']!="/acednsproduct/update-order-approval.php")
	//{
		//require("include/functions.php");
	//}
}
//exit();
function disphtml($what)
{
	//echo $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER APPROVAL</title>
<link href="<?=USER_CSS?>" rel="stylesheet" type="text/css" />
</head>
<body >
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
                	<?php $logo=strtoupper($_REQUEST['nick_name']);
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
					<td class="header" style="background: url(http://www.acedns.in/acednsproduct/logo/<?=$final_logo?>) 2% 50% no-repeat #FFFFFF;"" >
					<br>
					</td>
				</tr>
				<tr>
					<td class="menubar">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
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
}
?>