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
function show_order_status(branch_code){
	if(document.getElementById("branch").value.search(/\S/) == -1){
		return false;
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_tracking_details.php?branch_code='+branch_code,'display',0);
}
function edit_product_details(order_no){
	var select_id = 'select_'+order_no;
	var select_val = document.getElementById(select_id).value;
	if(document.getElementById(select_id).value.search(/\S/) == -1){
		return false;
	}
		
	document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_tracking_details_edit.php?order_no='+order_no+'&select_val='+select_val,'display_details',0);
	window.location.href = '#display_details';
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

<body>
<center>
<table cellpadding="8px">
  <tr style="background:#EEE8CD;">
  	<td align="left">Branch</td>
    <td align="right">
    <select id="branch" onChange="show_order_status(this.value);">
    	<option value="">Select</option>
    <?php
	$sql_branch = "SELECT branch_code, branch_name FROM branch_master";
	$res_branch = mysqli_query($link,$sql_branch);
	while($row_branch = mysqli_fetch_assoc($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$branch_name = $row_branch['branch_name'];
		echo "<option value=\"".$branch_code."\">".$branch_name."</option>";
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
