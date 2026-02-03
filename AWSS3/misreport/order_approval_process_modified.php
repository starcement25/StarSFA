<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
$rsempfunctionality=mysql_query($sqlempfunctionality);
$rowempfunctionality=mysql_fetch_array($rsempfunctionality);
$functionality=$rowempfunctionality['functionality'];
$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];
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
function show_order_status(distributor){
	/*if(document.getElementById("order_date").value.search(/\S/) == -1){
		return false;
	}*/
	//alert(distributor);
	var order_date=document.getElementById("order_date").value;
	if(order_date=='')
	{
		order_date='04-06-2018';
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_approve_details_modified.php?order_date='+order_date+'&distributor='+distributor,'display',0);
}
function edit_order_details(select_control_val){
	var select_id = 'select_'+select_control_val;
	var select_val = document.getElementById(select_id).value;
	if(document.getElementById(select_id).value.search(/\S/) == -1){
		return false;
	}
		
	document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_approve_details_edit.php?select_control_val='+select_control_val+'&select_val='+select_val,'display_details',0);
	alert("Order approval process successful.");
	window.location.href='order_approval_process.php';
	//window.location.href = '#display_details';
}
function submit_reason(order_no,reason_type){
	//alert(reason_type);
	if(document.getElementById("reason").value.search(/\S/) == -1){
		alert('Please provide reason');
		return false;
	}
	var reason_text = document.getElementById("reason").value;
	document.getElementById("submit_reason").disabled = true;
	var branch_code = document.getElementById("branch").value;
	
	$.post("order_track_update.php",
    {
		order_no: order_no,
		reason_type: reason_type,
		reason_text: reason_text
    },
    function(data, status){
        alert(data);
		document.getElementById("submit_reason").disabled = false;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('order_tracking_details.php?branch_code='+branch_code,'display',0);
    });
}

function submit_order(order_product_id){
	var order_product_array = order_product_id.split("^");
	var i;
	var billed_qty_array = new Array();
	var billed_qty_flag = 0;
	for(i=0;i<order_product_array.length;i++){
		var order_product_element_id = order_product_array[i];
		var order_qty_id = 'order_'+order_product_element_id;
		var billed_qty_id = 'billed_'+order_product_element_id;
		
		var order_qty = parseInt(document.getElementById(order_qty_id).innerHTML);
		var billed_qty = document.getElementById(billed_qty_id).value;
		
		if(billed_qty>order_qty){
			alert('Billed quantity cannot be greated than order quantity');
			return false;
		}
		
		if(billed_qty<0){
			alert("Billed quantity cannot be negative");
			return false;
		}
		
		if(isNaN(billed_qty)){
			alert("Only numbers allowed");
			return false;
		}
		
		if(billed_qty == '')
			billed_qty = 0;
			
		if(billed_qty>0)
			billed_qty_flag = 1;
			
		var billed_qty_val = order_product_element_id+'_'+billed_qty;
		billed_qty_array.push(billed_qty_val);
	}
	
	if(billed_qty_flag == 0){
		alert("Please provide billed quantity");
		return false;
	}
	
	if(document.getElementById("invoice_no").value.search(/\S/) == -1){
		alert('Please provide invoice no');
		return false;
	}
	var invoice_no = document.getElementById("invoice_no").value;
	
	if(document.getElementById("invoice_amt").value.search(/\S/) == -1){
		alert('Please provide invoice amount');
		return false;
	}
	var invoice_amt = document.getElementById("invoice_amt").value;
	var branch_code = document.getElementById("branch").value;
	
	$.post("order_track_update.php",
    {
		reason_type: 'billed',
		billed_qty_array: billed_qty_array,
		invoice_no: invoice_no,
		invoice_amt: invoice_amt
    },
    function(data, status){
        alert(data);
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('order_tracking_details.php?branch_code='+branch_code,'display',0);
		//document.getElementById("order_submit_id").disabled = false;
	});
}

function max_value_check(this_value,billed_quantity,order_quantity,billed_qty_id){
	//alert(this_value+max_val);
	if(isNaN(this_value)){
		alert("Please provide numeric data");
		document.getElementById(billed_qty_id).value = billed_quantity;
	}
	else{
		var qty_remain = order_quantity - this_value;
		if(billed_quantity>qty_remain){
			alert("Exceeding order limit");
			document.getElementById(billed_qty_id).value = billed_quantity;
			return false;
		}
	}
}
</script>
</head>

<body onLoad="show_order_status('<?=$functionality_rel_val?>');">
<center>
<table cellpadding="8px">
  <tr style="background:#EEE8CD;">
  	<td align="left">Choose Date</td>
    <td align="right">
    <select id="order_date" onChange="show_order_status('<?=$functionality_rel_val?>');">
    	<option value="">Select</option>
    <?php
	//$sqlcustomer = "SELECT customer_code,customer_name FROM customer_master WHERE cust_type='D' AND acedns='Y' AND customer_code IN(SELECT functionality_rel_val FROM employee_master WHERE functionality='DOS' AND reporting_to='E0018') ORDER BY customer_name ASC";
	$sqldate = "SELECT  DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date FROM order_header OH,customer_master CM WHERE 
	 OH.status ='' AND OH.customer_code=CM.customer_code AND (OH.customer_code IN(SELECT customer_code FROM customer_master WHERE cust_type='R' 
	 AND rds_tag='".$functionality_rel_val."') OR OH.customer_code IN('".$functionality_rel_val."')) GROUP BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') ORDER BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') DESC";
	$resdate = mysql_query($sqldate);
	while($rowdate = mysql_fetch_array($resdate)){
		$order_date = $rowdate['order_date'];
		echo "<option value=\"".$order_date."\">".$order_date."</option>";
	}
	?>
    </select>
    </td>
  </tr>
</table>
<br>
<div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center">
</div>
<br><br>
<div id="display_details" style="max-height: 350px; width:35%; overflow-y: scroll;" align="center">
</div>
<br><br><br><br>
<a name="display_details"></a>
</center>
</body>
<?php
}
?>
