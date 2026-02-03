<?php
ob_start();
session_start();
require("adminUtils.php");
//require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND CRER.emp_code IN(".$emp_hierarchy_value.") ";
	}
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script language="JavaScript" src="calendar3.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript" src="jquery.highlight.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <body onLoad="javascript:display_result();">
<center>
	<span style="font-weight:bold; font-size:14px;">Dealer Visit Feedback</span><br><br>
     <br />
       <div id="display" style="max-height: 350px; width:60%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />

</center>
</body>
    <script>
	function display_result(){
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('dealer_visit_feedback_display_data.php','display',0);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	
	function Popup(data) 
	{
		var mywindow = window.open('', 'Dealer Visit Feedback', 'height=400,width=600');
		mywindow.document.write('<html><head>');
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
	a.download = 'Dealer Visit Feedback' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
	
	</script>
    <?php
}
?>