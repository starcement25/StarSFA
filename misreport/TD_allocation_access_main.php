<?php
ob_start();
	session_start();
	//require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
		if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

/*$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","$db");
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);*/

if(!$_GET)
	disphtml("main();");
	
ob_end_flush();
?>

<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<?php
function main()
{
?>
<body onLoad="show_emp_data();">
<center>
<div style="height:30px; width:90%; text-align:center;">
Designation:<input class="INPUT" type="text" name="designation_name" id="designation_name" value="" />&nbsp;&nbsp;
<input type="submit" class="inplogin" name="search" value="Search" onClick="search_record(designation_name.value);" />
</div>
<br>
<div id="display" style="max-height: 350px; overflow-y: scroll; overflow-x: scroll; width:80%;">
</div>
</center>
</body>

<script>
function set_yes(emp_code)
{
	var flag = document.getElementById("allocate_TD_yes").value;
	var search_designation_name = document.getElementById("designation_name").value;
	
	$.post("TD_allocation_set.php",
    {
		emp_code: emp_code,
        flag: flag
    },
    function(data, status){
        alert("Data: " + data);
	if(search_designation_name != '')
		GenericAjaxFunction('TD_allocation_access.php?search_designation_name='+search_designation_name,'display',0);
	else
		GenericAjaxFunction('TD_allocation_access.php','display',0);
    });
}

function set_no(emp_code)
{
	var flag = document.getElementById("allocate_TD_no").value;
	var search_designation_name = document.getElementById("designation_name").value;
	
	$.post("TD_allocation_set.php",
    {
		emp_code: emp_code,
        flag: flag
    },
    function(data, status){
        alert("Data: " + data);
	if(search_designation_name != '')
		GenericAjaxFunction('TD_allocation_access.php?search_designation_name='+search_designation_name,'display',0);
	else
		GenericAjaxFunction('TD_allocation_access.php','display',0);
    });
}

/*----------------------*/
function set_getallocation_yes(emp_code)
{
	var get_allocation = document.getElementById("get_allocate_TD_yes").value;
	var search_designation_name = document.getElementById("designation_name").value;
	//alert(emp_code+get_allocation);
	$.post("TD_getallocation_set.php",
    {
		emp_code: emp_code,
        get_allocation: get_allocation
    },
    function(data, status){
        alert("Data: " + data);
		if(search_designation_name != '')
		GenericAjaxFunction('TD_allocation_access.php?search_designation_name='+search_designation_name,'display',0);
		else
		GenericAjaxFunction('TD_allocation_access.php','display',0);
    });
}

function set_getallocation_no(emp_code)
{
	var get_allocation = document.getElementById("get_allocate_TD_no").value;
	var search_designation_name = document.getElementById("designation_name").value;
	//alert(emp_code+get_allocation);
	$.post("TD_getallocation_set.php",
    {
		emp_code: emp_code,
        get_allocation: get_allocation
    },
    function(data, status){
        alert("Data: " + data);
	if(search_designation_name != '')
		GenericAjaxFunction('TD_allocation_access.php?search_designation_name='+search_designation_name,'display',0);
	else
		GenericAjaxFunction('TD_allocation_access.php','display',0);
    });
}
/*----------------------*/


function search_record(designation_name)
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('TD_allocation_access.php?search_designation_name='+designation_name,'display',0);
}

function show_emp_data()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('TD_allocation_access.php','display',0);
}
</script>

<?php
}
?>