<?php
ob_start();

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_acednsproduct");

$linkdbaccess=mysqli_connect(SERVER,USER,PASSWORD,DB);
//mysqli_select_db(DB,$linkdbaccess);
$nick_name=$_POST['nick_name'];
if($_POST){
	$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsdbaccessdetails=mysqli_query($linkdbaccess,$sqldbaccessdetails);
	$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
	$remote_db_access=$rowdbaccessdetails['remote_db_access'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container-fluid" style="padding-top:120px; padding-left:15%; padding-right:15%;">
<div style="height:40%; background:#A92A61; font-family:'Times New Roman', Times, serif; font-weight:bold; font-size:18px; text-align:center; color:#FFFFFF;">ACEdns apk download</div>
  

<?php
if($_POST)
{
    $ddb = "acedns_".strtoupper($nick_name);

    define("DBL","$ddb");
    
    
	define("PASSWORDL","dnsprod1234#");
    
    $link=mysqli_connect(SERVER,USER,PASSWORDL,DBL) or die("Database Connection Error.");
	if($remote_db_access=='yes')
	{
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_$nick_name");
		$link = mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	}
	$db_nick_name = "acedns_".strtoupper($_POST['nick_name']);
	$dir = dirname(__FILE__);
	$vertical_value=$_POST['vertical_value'];
		
	if($link)
	{
		if($vertical_value=='' && strtoupper($_POST['nick_name'])!='EMAMI' && strtoupper($_POST['nick_name'])!='EMAMIT')
		{
			$sql_check_app = "SELECT * FROM app_version";
		}
		else
		{
			$sql_check_app = "SELECT * FROM app_version WHERE vertical_value='".$vertical_value."'";
		}
		$res_check_app = mysqli_query($link,$sql_check_app);
		$row_check_app = mysqli_fetch_assoc($res_check_app);
		//echo $url="http://salesmpower.acedns.in/app_update/".$row_check_app['version_code']."/ACEdns.apk";
		$download = "location:http://salesmpower.acedns.in/app_update/".$row_check_app['version_code']."/ACEdns.apk";
		$exist_check = "app_update/".$row_check_app['version_code']."/ACEdns.apk";
		
		if(file_exists($exist_check))
			header($download);
		else
		header('location:downloadapk.php?filenotfound=1');
	}
	else
	header('location:downloadapk.php?databasenotfound=1');
	
}
?>
<script>
function validate()
{
	if(document.nick_name_check.nick_name.value.search(/\S/) == -1)
	{
		alert("Enter Nick Name");
		return false;
	}
	/*var nick_name=document.nick_name_check.nick_name.value;
	var nick_name_upper=nick_name.toUpperCase();
	if(nick_name_upper=='EMAMI' || nick_name_upper=='EMAMIT'){
		if(document.getElementById('vertical_value').value=="")
		{
			alert("Please select vertical");
			return false;
		}
	}*/
	return true;
}
function check_vertical()
{
	var nick_name=document.nick_name_check.nick_name.value;
	var nick_name_upper=nick_name.toUpperCase();
	if(nick_name_upper=='EMAMI' || nick_name_upper=='EMAMIT'){
		document.getElementById('show_vertical').style.display='';
	}
}
</script>
<center>
<div  style="border:solid; border-color:#A92A61; padding-top:20%; padding-bottom:20%;">
<form name="nick_name_check" method="POST" action="" onsubmit="return validate();">
<table cellspacing="4" cellpadding="5">
<tr>
    <td style="font-family:'Times New Roman', Times, serif; font-size:120%; font-weight:bold; color:black;">Nick Name&nbsp;&nbsp;</td>
    <td>&nbsp;<input type="text" name="nick_name" style="width:75%;" onBlur="javascript:check_vertical();" autocomplete="off"/></td>
</tr>
<tr>
    <td style="font-family:'Times New Roman', Times, serif; font-size:120%; font-weight:bold; color:black;">&nbsp;</td>
    <td>&nbsp;</td>
</tr>
<tr id="show_vertical" style="display:none">
    <td style="font-family:'Times New Roman', Times, serif; font-size:120%; font-weight:bold; color:black;">Vertical value&nbsp;&nbsp;</td>
    <td>&nbsp;<select name="vertical_value" id="vertical_value"><option value="">SELECT VERTICAL</option><option value="HBC:Rasoi:BIB">HBC:Rasoi:BIB</option><option value="Specialty Fats">Specialty Fats</option></select></td>
</tr>
<tr>
<td></td>
<td align="left" colspan="2" style="padding-top:10%;"><input type="submit" name="submit" value="" style="background:url(submit.png); background-size:110% 110%; width:75%; height:40px; border-radius:8px;"/></td>
</tr>
</table>
</form>
</div>
<?php
if($_GET['filenotfound'] == 1)
echo "<font color=\"black\">Requested file doesnot exists</font>";

if($_GET['databasenotfound'] == 1)
echo "<font color=\"black\">Database not found</font>";
?>
</center>
</div>
</body>
</html>