<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$type = $_REQUEST['type'];
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

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	var customerdatadiv = document.getElementById("customerdata").innerHTML;
   var productdiv = document.getElementById("product").innerHTML;
   var customerwisediv = document.getElementById("customerwise").innerHTML;
   var view = displaydiv+'<br>'+customerdatadiv+'<br>'+productdiv+'<br>'+customerwisediv;
   Popup(view);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Customerwise Sauda Report', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Customerwise Sauda Report</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(divid)
{
	//alert(divid);
        //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var displaydiv = document.getElementById("display").innerHTML;
		var customerdatadiv = document.getElementById("customerdata").innerHTML;
	    var productdiv = document.getElementById("product").innerHTML;
	    var customerwisediv = document.getElementById("customerwise").innerHTML;
		var view = displaydiv+'<br>'+customerdatadiv+'<br>'+productdiv+'<br>'+customerwisediv;
		document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('view');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Customerwise Sauda Report' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
</script>

<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>');">
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>

<div id="view">
<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>
<br>
<div id="customerdata" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
</div>
<br>
<div id="product" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
<br>
<div id="customerwise" style="max-height: 350px; width:70%; overflow-y: scroll; margin-left:10px;" align="center"></div>
<br>
</div>	
<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" <?php if($type == 'today') echo "checked"; ?> onClick="hide_date_div(); show_saudawise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" <?php if($type == 'mtd') echo "checked"; ?> onClick="hide_date_div(); show_saudawise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" <?php if($type == 'custom') echo "checked"; ?> onClick="show_date_div();" />
</div>
	
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_saudawise();" />
</div>
<br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
<br>
<div><a href="#" style="color:blue;" onClick="show_prev_page();">Back</a></div>
    
</center>
</body>

<script>
function show_saudawise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	document.getElementById("product").innerHTML = "";
	document.getElementById("customerwise").innerHTML = '';
	document.getElementById("customerdata").innerHTML = '';
	
	if(document.getElementById("today").checked == true)
	{
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true)
	{
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true)
	{
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			var response1 = 0;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			var response2 = 0;
		}
	}
	
	if(type != 'custom')
		GenericAjaxFunction('customerwisedata.php?type='+type,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('customerwisedata.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
		
}

function show_customer(product_code, customer_code, customer_name, product_name, condition_value, start_date, end_date)
{
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('customer_product_details.php?customer_code='+customer_code+'&customer_name='+customer_name+'&product_code='+product_code+'&product_name='+product_name+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date,'product',0);
}


function show_customerdata_datewise(customer_code, customer_name, emp_code, condition_value, start_date, end_date)
{
	document.getElementById("customerwise").innerHTML = '';
	customer_name = encodeURIComponent(customer_name);	//function handles any special characters while passing via URL
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('customer_data_datewise.php?customer_code='+customer_code+'&customer_name='+customer_name+'&emp_code='+emp_code+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date,'product',0);
	
	
}

function show_customerdata(customer_name,product_name,condition_value,product_group_code,selected_date)
{
	document.getElementById("customerwise").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	customer_name = encodeURIComponent(customer_name);
	GenericAjaxFunction('customer_product_details.php?customer_name='+customer_name+'&product_group_code='+product_group_code+'&condition_value='+condition_value+'&product_name='+product_name+'&selected_date='+selected_date,'customerwise',0);
}

function show_empdata(emp_code, condition_value, start_date, end_date){
	//alert(emp_code+product_group_code+condition_value+start_date+end_date);
	document.getElementById("customerdata").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('customerwisedata.php?emp_code='+emp_code+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date,'customerdata',0);
	document.getElementById("product").innerHTML = "";
	document.getElementById("customerwise").innerHTML = '';
}

function loadbodydata(type,start_date,end_date){
	<?php
	if($type == 'custom'){?>
		document.getElementById("date_div").hidden = false;<?php
	}
	?>
	GenericAjaxFunction('customerwisedata.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0)
}

function show_prev_page(){
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	}
	window.open('http://acedns.in/acednsproduct/misreport/sauda_report_main_selected.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
</script>
<?php
}
?>
