<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require ("order_attribute_selection.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	function main(){
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

<body >
<center>
<!--<div id="display" style="max-height: 480px; width:96%; overflow-y: scroll; margin-left:10px;" align="center"></div>

<br>
<div id="date_div" style="width:60%;" >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_emp();" />
</div>-->
<?php
	$hidden = "";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'></td><td width='90%' align='center'>";
	attribute_selection_saletype($hidden,$get_control='');
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
?>
<div id="display" style="max-height: 480px; width:90%; overflow-y: scroll;" align="center"></div>
</center>
</body>

<script>
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
function download_xls(){
	//alert("Break Free");
	if(document.getElementById("brand").value.search(/\S/) == -1){
		alert("Please select Brand");
		return false;
	}
	if(document.getElementById("ordertype").value.search(/\S/) == -1){
		alert("Please select Order Type");
		return false;
	}
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Please provide start date');
		return false;
	}
	if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Please provide end date');
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
	
	//document.download_xls_data.submit();
	//return true;
	document.getElementById('loader').style.display='';

	var brand = document.getElementById("brand").value;
	var ordertype = document.getElementById("ordertype").value;
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var empval="<?php echo $_SESSION['admin_login']?>";
	//window.location="download_order_xls_data.php?start_date="+start_date+"&end_date="+end_date+"&employee="+emp_code;
	
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	}
	var url="download_order_xls_data_SKIPPER.php";
	var params = "start_date="+start_date+"&end_date="+end_date+"&brand="+brand+"&ordertype="+ordertype+"&empval="+empval;
	xmlHttp.open("POST", url, true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
	xmlHttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			var val=xmlHttp.responseText;
			if(val!="" && val!=0)
			 {
				 //alert(val);
				 //alert("Success Download Started");
				 document.getElementById('loader').style.display='none';
				 var valstring=val.replace(" ","");
				 window.location='http://salesmpower.acedns.in/misreport/'+valstring+".xls";
			 }
			 else
			 {
				 alert('NO Records Found');
				 document.getElementById('loader').style.display='none';
			 }
		}
	}
	xmlHttp.send(params);
	/*xmlHttp.onreadystatechange=downloadXls;
	xmlHttp.open("POST",url,true);
	xmlHttp.send(params);*/

	
	/*var vertical = document.getElementById("vertical").value;
	var zone = document.getElementById("zone").value;
	var state = document.getElementById("state").value;
	var emp_code = document.getElementById("employee").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_download_data_all_rupa.php?start_date='+start_date+'&end_date='+end_date+'&vertical='+vertical+'&state='+state+'&emp_code='+emp_code+'&zone='+zone,'display',0);*/
}
function downloadXls()
 {
    if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		alert(val);
		if(val!="")
		 {
			 //alert("Success Download Started");
			 document.getElementById('loader').style.display='none';
			 //window.location='http://salesmpower.acedns.in/misreport/'+val+".xls";
		 }
	}
	else
	{
		document.getElementById('loader').style.display='';
	}
 }
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Order Transaction', 'height=400,width=600');
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
	a.download = 'Order details' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
</script>
<?php
	}
?>