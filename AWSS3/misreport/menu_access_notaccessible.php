<?php
ob_start();
	session_start();
	//require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
		if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
		require ("attribute_selection.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");
mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

if(!$_GET)
	disphtml("main();");
	
ob_end_flush();
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
<body >
<center>
<?php
	$hidden = " hidden";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Not Accessible Menu</span><br><br>";
	echo "<br>";
	attribute_selection($hidden);
	echo "<br>";
?>
<!--<div style="height:30px; width:90%; text-align:center;">
Designation:<input class="INPUT" type="text" name="designation_name" id="designation_name" value="" />&nbsp;&nbsp;
<input type="submit" class="inplogin" name="search" value="Search" onClick="search_record(designation_name.value);" />
</div>-->
<br>
<div id="display" style="max-height: 500px; overflow-y: scroll; overflow-x: scroll; width:85%;">
</div>
</center>
</body>

<script>

/*-------> Function To Search Emp Details Using Designation <-------*/
function search_record(designation_name)
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('allocation_access.php?search_designation_name='+designation_name,'display',0);
}

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
	function display_result(){
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var employee = document.getElementById("employee").value;
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		var url="menu_access_notaccessible_data.php";
		xmlHttp.onreadystatechange=employeeaccessemplist;
		xmlHttp.open("POST",url,true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.send("employee="+employee);
	}
	function employeeaccessemplist()
	 {
		if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 {
			var val=xmlHttp.responseText;
			document.getElementById("display").innerHTML =val; 
			//document.getElementById("print_export").hidden = false;
		 }
	 }

/*-------> Function To Display Emp Details(body Onload) <-------*/
function show_emp_data()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('menu_access_notaccessible_data.php','display',0);
}

function update_data(emp_code)
{
	//alert(emp_code);
	var empArrayinput = new Array;
	$('.input_chk_'+emp_code+':checked').each(function() {
        empArrayinput.push($(this).val());
    });
	
	//alert(JSON.stringify(empArrayinput)); //to alert array
	
	$.post("update_menu_access_notaccessible.php",
    {
		emp_code: emp_code,
		emparray: empArrayinput
    },
    function(data, status){
        alert(data);
		var employee = document.getElementById("employee").value;
		//alert(employee);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('menu_access_notaccessible_data.php?employee='+employee,'display',0);
	});
	
}
</script>

<?php
}
?>