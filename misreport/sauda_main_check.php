<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

if(!$_GET)
	disphtml("main();");
	
ob_end_flush();
?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<?php
function main()
{
	$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
	$rs_top_emp=mysqli_query($link,$sql_top_emp);
	$row_top_emp=mysqli_fetch_assoc($rs_top_emp);
	$top_emp_code=$row_top_emp['emp_code'];
?>

<body onLoad="GenericAjaxFunction('saudatest_check.php','display',0);">

<center>

<!--div style="height:30px; width:90%; text-align:center;">
Employee:<input class="INPUT" type="text" name="employee_name" id="employee_name" value="" />&nbsp;&nbsp;
<input type="submit" class="inplogin" name="search" value="Search" onClick="search_record(employee_name.value);" /></div-->
<div style="height:30px; width:90%; text-align:right;"><b>* all measurements are in MT</b></div>

<br />

<div id="display" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-y: scroll;overflow-x: scroll; width:95%;">
<img src="ajax-loader.gif" id="ajaxloader">
</div>

<br />

<div id="edit_form" style="width:60%;">
</div>

</center>

</body>

<script>

function show_data(quantity,emp_code,product_code,quantity_booked,old_quantity)
{
	//alert(quantity+" "+emp_code+" "+product_code+" "+quantity_booked+" "+old_quantity);
	//var qty = document.getElementById("quantity").value;
	//alert(qty);
	if($('#'+quantity+'').html()<quantity_booked && $('#'+quantity+'').html()!='')
	{
		$('#'+quantity+'').html(old_quantity);
		alert("Allocation Quantity cannot be less than Booked Quantity")
	}
	
	else
	{
		if($('#'+quantity+'').html()!='')
		{
			$.post("sauda_update.php",
			{
				emp_code: emp_code,
				quantity: $('#'+quantity+'').html(),
				product_code: product_code,
				quantity_booked: quantity_booked
			},
			function(data, status){
				if(data.search('Allot') != -1)
					$('#'+quantity+'').html(old_quantity);
				alert("Data: " + data);
			});
		}
	}
}

function submitdata()
{
	var employee_code = document.getElementById("emp").value;
	var product_group_code = document.getElementById("product_group").value;
	var quantityalloted = document.getElementById("quantity").value;
	
	if(document.getElementById("emp").value.search(/\S/) == -1 || document.getElementById("product_group").value.search(/\S/) == -1 || document.getElementById("quantity").value.search(/\S/) == -1)
	alert("Fields cannot be empty");
	else
	{
	
	$.post("sauda_add.php",
    {
		emp_code: employee_code,
        quantity: quantityalloted,
		product_code: product_group_code
    },
    function(data, status){
        alert("Data: " + data);
    });
	}
	
}

function call_ajax()
{
	var emp_code = document.getElementById("emp").value;
	var product_group = document.getElementById("product_group").value;
	//var quantity = document.getElementById("qty").value;
	
	GenericAjaxFunctionText('sauda_quantity.php?emp_code='+emp_code+'&product_code='+product_group+'','quantity',0);
}

function updatedata()
{
		var employee_code = document.getElementById("emp").value;
		var product_group_code = document.getElementById("product_group").value;
		var quant = document.getElementById("quantity").value;
		
		$.post("sauda_update.php",
		{
			emp_code: employee_code,
			quantity: quant,
			product_code: product_group_code
		},
		function(data, status){
			alert("Data: " + data);
		});
		
		/*if(search_empl_name!='')
			GenericAjaxFunction('saudatest.php?search_empl_name='+search_empl_name,'display',0);
		else*/
	   GenericAjaxFunction('saudatest.php','display',0);
}

function finish()
{
	window.open('http://acedns.in/acednsproduct/misreport/sauda_main.php','_self');
}
function search_record(empl_name_value)
{
	GenericAjaxFunction('saudatest.php?search_empl_name='+empl_name_value,'display',0);
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
function check_sauda_qty(login_emp_code)
{
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	var product_group = document.getElementById("product_group").value;
	var quantity = document.getElementById("quantity").value;
	var emp_code=document.getElementById("emp").value;
	var nick_name="<?php echo $_SESSION['nick_name'];?>";

	var url="check_sauda_qty.php?login_emp_code="+login_emp_code+"&product_group_code="+product_group+"&quantity="+quantity+"&emp_code="+emp_code+"&nick_name="+nick_name;
	xmlHttp.onreadystatechange=checkSaudaqty;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
function checkSaudaqty()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		alert(val);
		if(val==0)
		{
			alert('Exceeds limit');
		}
		else
		{
			updatedata();
		}
	}
 }
</script>

<?php
}
?>