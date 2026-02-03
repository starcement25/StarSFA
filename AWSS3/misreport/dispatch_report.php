<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
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

<script>
function max_value_check(dispatch_value,billed_quantity,dispatch_qty_id,max_value,dispatch_qty){
	//alert(this_value+max_val);
	if(isNaN(dispatch_value)){
		alert("Please provide numeric data");
		document.getElementById(dispatch_qty_id).value = '';
	}
	else if(dispatch_value>billed_quantity){
		alert('Dispatch quantity cannot greater than billed quantity');
		document.getElementById(dispatch_qty_id).value = '';
	}
	else if(dispatch_value>max_value){
		alert("Exceeding dispatch limit");
		document.getElementById(dispatch_qty_id).value = dispatch_qty;
		return false;
	}
	else if(dispatch_value<billed_quantity){
		var remarks = 'remarks_'+dispatch_qty_id;
		alert("Please Provide Remarks");
		document.getElementById(remarks).readOnly = false;
	}
}

function show_invoice(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('dispatch_invoice_details.php','display',0);
}

function get_invoice_details(invoice_no){
	document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('dispatch_get_invoice_details.php?invoice_no='+invoice_no,'display_details',0);
}

function submit_dispatch_details(order_product_id){
	var order_product_array = order_product_id.split("^");
	var i;
	var dispatch_qty_array = new Array();
	var remarks_array = new Array();
	for(i=0;i<order_product_array.length;i++){
		var order_product_element_id = order_product_array[i];
		var remarks_id = 'remarks_'+order_product_array[i];
		
		var dispatch_qty = document.getElementById(order_product_element_id).value;
		if(dispatch_qty == '')
			dispatch_qty = 0;
			
		var dispatch_qty_val = order_product_element_id+'_'+dispatch_qty;
		dispatch_qty_array.push(dispatch_qty_val);
		
		var remarks = document.getElementById(remarks_id).value;
		var remarks_val = order_product_element_id+'_'+remarks;
		remarks_array.push(remarks_val);
	}
		
	var order_no = document.getElementById("order_no").value;
	var invoice_no = document.getElementById("invoice_no").value;
	var invoice_amount = document.getElementById("invoice_amount").value;
			
	if(document.getElementById("dispatch_through").value.search(/\S/) == -1){
		alert("Please provide dispatch through");
		return false;
	}
	var dispatch_through = document.getElementById("dispatch_through").value;
		
	$.post("dispatch_details_update.php",
    {
		dispatch_qty_array: dispatch_qty_array,
		remarks_array: remarks_array,
		invoice_no: invoice_no,
		invoice_amount: invoice_amount,
		order_no: order_no,
		dispatch_through: dispatch_through
    },
    function(data, status){
        alert(data);
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('dispatch_invoice_details.php','display',0);
		//document.getElementById("order_submit_id").disabled = false;
	});
}
</script>
</head>
<body onLoad="show_invoice();">
<center>
<div id="display" style="max-height: 350px; width:40%; overflow-y: scroll;" align="center">
</div>
<br>
<div id="display_details" style="width:40%;" align="center">
</div>
</center>
</body>
<?php
}
?>
