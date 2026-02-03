<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
disphtml("main();");

function main()
{
	
    $type       = $_REQUEST['type'];
    
    if ($type == '')
        $type = 'today';
?><head>
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
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>

<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>');">
<center>
<div class="report_heading TDHEAD">Stock Audit Report</div><br>
<div id="display" style="max-height: 350px; width:100%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>
<br />
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
<div class="gobackdiv"><a href="#" style="color:white; font-weight:bold; font-size:16px; text-decoration:none;" onClick="show_prev_page();">BACK</a></div>
    
</center>
</body>

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
	var displaydiv = document.getElementById('display').innerHTML;	
	Popup(displaydiv);
}
function show_saudawise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		
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
		GenericAjaxFunction('stock_audit_employee.php?type='+type,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('stock_audit_employee.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
}

function loadbodydata(type,start_date,end_date){
	<?php
	if($type == 'custom'){?>
		document.getElementById("date_div").hidden = false;<?php
	}
	?>
	GenericAjaxFunction('stock_audit_employee.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}

function Popup(data) 
{
	var mywindow = window.open('', 'Stock Audit Report', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Stock Audit Report</title>');
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

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Master saleswise Report' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}



function exporttocsvnew(){
	window.open("stock_audit_excel_data.php","_blank");
}
</script>
<?php
}

?>