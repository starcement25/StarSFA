<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
	session_start();

	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require_once("adminUtils.php");
	}
	else
	{
		require_once("adminUtils_HBC_SFATS.php");
	}
	require_once("include/config.php");
	require_once("include/config-setup.php");
	require_once("include/dbcon.php");
	// include/functions.php is loaded inside disphtml() (adminUtils.php) with a plain
	// require, so loading it here as well would redeclare every function in it.
	require_once("menu_access_bd_schema.php");

	ensure_menu_access_bd_schema($link);

	if(!$_GET)
		disphtml("main();");
?>

<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<?php
function main()
{
?>
<body onload="show_emp_data();">
<center>
<?php
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain_BD.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">BD Menu Permission</span><br>";
	echo "<span style=\"font-size:12px;\">Tick the types an employee is NOT allowed to see. An unticked type is permitted.</span>";
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
?>
<br>
<div id="display" style="max-height: 500px; overflow-y: scroll; overflow-x: scroll; width:85%;">
</div>
</center>
</body>

<script>
function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

/*-------> Load every BD employee as soon as the page opens <-------*/
function show_emp_data()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('menu_access_bd_data.php','display',0);
}

function update_data(emp_code)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	} 
	
	var empArrayinput = new Array;
	var checkBoxval = document.getElementsByName('input_chk_'+emp_code);
	
	for (let i = 0; i < checkBoxval.length; i++) {
		if (checkBoxval[i].checked) {
			empArrayinput.push(checkBoxval[i].value);
		}
	}
	
	var url="update_menu_access_bd.php";
	xmlHttp.onreadystatechange=upemployeeaccessemplist;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send("emp_code="+emp_code+"&emparray="+empArrayinput);
}

// Global update for customer if needed by copy
function update_data_customer(customer_code)
{
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	} 
	
	var empArrayinput = new Array;
	var checkBoxval = document.getElementsByClassName('input_chk_'+customer_code);
	
	for (let i = 0; i < checkBoxval.length; i++) {
		if (checkBoxval[i].checked) {
			empArrayinput.push(checkBoxval[i].value);
		}
	}
	
	var url="update_menu_access_bd.php";
	xmlHttp.onreadystatechange=upemployeeaccessemplist;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send("emp_code="+customer_code+"&emparray="+empArrayinput);
}
	
function upemployeeaccessemplist()
{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		show_emp_data();
	}
}
</script>

<?php
}
?>
