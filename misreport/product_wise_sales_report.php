<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");

function main(){
	?>
<head>
<style>
.span_class{
	font-weight:bold; 
	font-size:12px; 
	cursor:pointer;
}
.div_tab{
	font-weight:bold; 
	font-size:12px; 
	cursor:pointer;
	background:#F0E68C;
	padding:4px;
}
.para_class{
	width:50%;
	text-align:left;
}
</style>
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
function show_today_data(get_code,type){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_wise_sales_report_data.php?get_code='+get_code+'&listing_type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}

function show_datewise_data(){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide start date');
		return false;
	}
	if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Provide end date');
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_wise_sales_report_data.php?start_date='+start_date+'&end_date='+end_date,'display',0);
	
	if($('#display').is(':visible')){
	}
	else{
		document.getElementById("display_toggle").innerHTML = "- Product Group";
		$('#display').toggle(500);
	}
	
	document.getElementById("empwise_list").innerHTML = '';
	document.getElementById("empwise_list").hidden = true;
	<?php if(tagged_distributor_for_order == 'yes'){ ?>
	document.getElementById("distributorwise_list").innerHTML = '';
	document.getElementById("distributorwise_list").hidden = true;
	<?php } ?>
	document.getElementById("customer_list").innerHTML = '';
	document.getElementById("customer_list").hidden = true;
}

function csvexport(admin_login,start_date,end_date){
	window.open('productwise_sales_report_export.php?admin_login='+admin_login+'&start_date='+start_date+'&end_date='+end_date);
}

function pdffile(admin_login,start_date,end_date){
	$.post("productwise_sales_report_pdf.php",
    {
		admin_login: admin_login,
		start_date: start_date,
		end_date: end_date
    },
    function(data, status){
        //alert(data);
		var mywindow = window.open('', 'Productwise Sales Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Productwise Sales Report</title>');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
    });
}

function get_customerlist(type){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	if(type == 'home'){
		window.location.href = 'product_wise_sales_report.php';
	}
	else{
		if(type == 'customerlist'){
			document.getElementById("empwise_list").innerHTML = '';
			document.getElementById("empwise_list").hidden = true;
			<?php if(tagged_distributor_for_order == 'yes'){ ?>
			document.getElementById("distributorwise_list").innerHTML = '';
			document.getElementById("distributorwise_list").hidden = true;
			<?php } ?>
			document.getElementById("customer_list").hidden = false;
			document.getElementById("customer_list").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			var display_div = 'customer_list';
		}
		else if(type == 'emplist'){
			document.getElementById("empwise_list").hidden = false;
			document.getElementById("empwise_list").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			<?php if(tagged_distributor_for_order == 'yes'){ ?>
			document.getElementById("distributorwise_list").innerHTML = '';
			document.getElementById("distributorwise_list").hidden = true;
			<?php } ?>
			document.getElementById("customer_list").innerHTML = '';
			document.getElementById("customer_list").hidden = true;
			var display_div = 'empwise_list';
		}
		else if(type == 'ditributorlist'){
			document.getElementById("empwise_list").innerHTML = '';
			document.getElementById("customer_list").innerHTML = '';
			document.getElementById("empwise_list").hidden = true;
			document.getElementById("customer_list").hidden = true;
			document.getElementById("distributorwise_list").hidden = false;
			document.getElementById("distributorwise_list").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			var display_div = 'distributorwise_list';
		}
		
		GenericAjaxFunction('productwisesales_listing.php?start_date='+start_date+'&end_date='+end_date+'&type='+type,''+display_div+'',0);
	}	
}

function productwisesales_customerwise(get_code,type){
		
	if($('#display').is(':visible')){
	}
	else{
		$('#display').toggle(500);
	}
	
	show_today_data(get_code,type);
}

function name_search(){
	var name = document.getElementById("customer_name_search").value;
	var pattern = name.toLowerCase();
	var targetId = "";
	var divs = document.getElementsByClassName("search_class");
	var divs_length = document.getElementsByClassName("search_class").length;
   
	for (var i = 0; i < divs_length; i++) {
	  var para = divs[i].innerHTML;
	  var para = para.toLowerCase();
	  var index = para.indexOf(pattern,0);
	  
	  if (index == 0) {
		 //targetId = divs[i].parentNode.id;
		 targetId = document.getElementsByClassName("search_class")[i].getAttribute("id");
		 document.getElementById(targetId).scrollIntoView();
		 document.getElementById(targetId).style.background = '#BCEE68';
		 break;
	  }
	} 
}

function toggle_span(toggle_span_type,span_type){
	$('#'+toggle_span_type+'').toggle(500);
	if(document.getElementById(span_type).innerHTML == '+PRIMARY')
		document.getElementById(span_type).innerHTML = '-PRIMARY';
	else if(document.getElementById(span_type).innerHTML == '-PRIMARY')
		document.getElementById(span_type).innerHTML = '+PRIMARY';
		
	if(document.getElementById(span_type).innerHTML == '+SECONDARY')
		document.getElementById(span_type).innerHTML = '-SECONDARY';
	else if(document.getElementById(span_type).innerHTML == '-SECONDARY')
		document.getElementById(span_type).innerHTML = '+SECONDARY';
}
</script>
</head>

<body onLoad="show_today_data();">
<center>
<span style="font-weight:bold; font-size:14px;">PRODUCTWISE ORDER ANALYSIS</span><br><br>
<table width="100%">
  <tr>
  	<td width="30%" align="right" valign="top">
    <br><br><br>
      <table width="100%" cellpadding="5">
        <tr>
        	<td><div id="product_wise" class="div_tab" onClick="get_customerlist('home');">HOME</div></td>
        </tr>
        <tr>
        	<td><div id="customerwise" class="div_tab" onClick="get_customerlist('customerlist');">RETAILERWISE</div>
            	<div id="customer_list" style="max-height:200px; overflow-y:scroll;" hidden></div>
            </td>
        </tr>
        <tr hidden>
            <td><div id="areawise" class="div_tab" onClick="get_customerlist('arealist');">AREAWISE</div>
            	<div id="areawise_list" style="max-height:200px; overflow-y:scroll;" hidden></div>
            </td>
        </tr>
        <tr>
            <td><div id="empwise" class="div_tab" onClick="get_customerlist('emplist');">EMPLOYEEWISE</div>
            	<div id="empwise_list" style="max-height:200px; max-width:300px; overflow-y:scroll; overflow-x:scroll;" hidden></div>
            </td>
        </tr>
        <?php if(tagged_distributor_for_order == 'yes'){ ?>
        <tr>
            <td><div id="distributorwise" class="div_tab" onClick="get_customerlist('ditributorlist');">DISTRIBUTORWISE</div><br>
            	<div id="distributorwise_list" style="max-height:200px; overflow-y:scroll;" hidden></div>
            </td>
        </tr>
        <?php } ?>
      </table>
    </td>
    <td width="70%" valign="top">
    <div id="display" style="max-height: 480px; width:50%; overflow-y: scroll;" align="center"></div>
    </td>
  </tr>
</table>
<br>
<div id="date_div" style="width:60%;" >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_datewise_data();" />
</div>
</center>
</body>
    <?php
}

?>