<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
if(!$_GET)
	disphtml("main();");
	
function main()
{
?>

<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
</head>

<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}
</script>

<body>
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>
	

<?php
$date = date('d-m-Y');
$yesterday = date('d-m-Y',strtotime("-1 days"));
$sql_select_date = "SELECT distinct(date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y')) FROM sauda_header SH WHERE date_format(substring(SH.sauda_no,-14,8),'%d-%m-%Y') = '".$date."' AND transaction_type != 'NFT'";
$res_select_date  = mysqli_query($link,$sql_select_date);
$total_rows = mysqli_num_rows($res_select_date);

if($total_rows > 0)
{
	$date_selected = "<option>".$date."</option><option>".$yesterday."</option>";
}
else
{
	$date_selected = "<option>".$yesterday."</option>";
}
?>
    
    
    
<div id="date_div" style="width:60%;">
<table width="60%" cellpadding="5px">
  <tr>
	<td>Select Date:<select name="start_date" id="start_date" onChange="select_date(this.value);" ><option value=" ">Select Date</option><?php echo $date_selected; ?></select></td>
    <td><div id="emp_name" hidden>Select Employee:<select name="select_employee" id="select_employee" onChange="employee_selected(this.value);" ></select></div></td>
    <td><div id="cust_name" hidden>Select Customer:<select name="select_customer" id="select_customer" onChange="customer_selected(this.value);" ></select></div></td>
  </tr>
  <tr>
  	<td colspan="3" align="center"><div id="sauda_no" style="max-height:200px; overflow-y:scroll;" hidden>Select Sauda Number:<div name="select_sauda_no" id="select_sauda_no"></div></div></td>
  </tr>
</table>
</div>
<br>
<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
<br>
<div id="product" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
<br>
<div id="testdiv"  style="width:100%;"></div>
<div><a href="sauda_report_main.php" style="color:blue;">Back</a></div>
    
</center>
</body>

<script>

function show_sauda_product(sauda_number)
{
	//alert(sauda_no);
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('saudawise_product_edit.php?sauda_number='+sauda_number,'product',0);
}

function select_date(date_selected)
{
	//alert(date_selected);
	document.getElementById("emp_name").hidden = false;
	document.getElementById("cust_name").hidden = true;
	document.getElementById("sauda_no").hidden = true;
	document.getElementById("display").innerHTML = '';
	document.getElementById("product").innerHTML = '';
	GenericAjaxFunction('saudawise_select_empl.php?date_selected='+date_selected,'select_employee',0);
}

function employee_selected(employee_selected)
{
	//alert(employee_selected);
	document.getElementById("sauda_no").hidden = true;
	document.getElementById("display").innerHTML = '';
	document.getElementById("product").innerHTML = '';
	document.getElementById("cust_name").hidden = false;
	GenericAjaxFunction('saudawise_select_customer.php?employee_selected='+employee_selected,'select_customer',0);
}

function customer_selected(customer_selected)
{
	//alert(customer_selected);
	document.getElementById("display").innerHTML = '';
	document.getElementById("product").innerHTML = '';
	document.getElementById("sauda_no").hidden = false;
	document.getElementById("select_sauda_no").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('sauda_number_select.php?customer_selected='+customer_selected,'select_sauda_no',0);
}

function show_sauda_data(sauda_number)
{
	//alert(sauda_no);
	document.getElementById("product").innerHTML = '';
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('saudawise_product_edit.php?sauda_number='+sauda_number,'display',0);
}

function edit_data(product_desc, booked, freight_charge, trade_discount, premium, sale_rate, sauda_number)
{
	//alert(product_desc+booked+freight_charge+trade_discount+sale_rate);
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_edit_form.php?product_desc='+product_desc+'&booked='+booked+'&freight_charge='+freight_charge+'&trade_discount='+trade_discount+'&premium='+premium+'&sale_rate='+sale_rate+'&sauda_number='+sauda_number,'product',0);
}

function edit_sauda_product_data()
{
	var sauda_number = document.getElementById("sauda_hidden_no").value;
	var product_code = document.getElementById("product_hidden_code").value;
	var booked = document.getElementById("booked").value;
	var freight = document.getElementById("freight").value;
	var trade_discount = document.getElementById("trade_discount").value;
	var premium = document.getElementById("premium").value;
	
	//alert(sauda_number+product_code+booked+freight+trade_discount);
	
	$.post("sauda_product_update.php",
    {
		sauda_number: sauda_number,
        product_code: product_code,
		booked: booked,
		freight: freight,
		trade_discount: trade_discount,
		premium: premium
    },
    function(data, status){
        alert(data);
		//document.getElementById("testdiv").innerHTML = data;
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('saudawise_product_edit.php?sauda_number='+sauda_number,'display',0);
	if(data == 'Data successfully updated')
	document.getElementById("product").innerHTML = '';
    });
}

function delete_product(sauda_number, product_code, booked_case)
{
    var response = confirm("Are you sure you want to delete?");
    if (response == true) 
	{
        //alert(sauda_number+product_code);
		$.post("sauda_product_delete.php",
    	{
		sauda_number: sauda_number,
        product_code: product_code,
		booked_case: booked_case
    	},
		function(data, status){
			alert(data);
			//document.getElementById("testdiv").innerHTML = data;
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('saudawise_product_edit.php?sauda_number='+sauda_number,'display',0);
		});
		//document.getElementById("testdiv").innerHTML = data;
	}
	//alert("Hii");
	
	
}
</script>
<?php
}
?>
