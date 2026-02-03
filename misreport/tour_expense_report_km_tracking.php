<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require ("attribute_selection.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if(!$_GET)
	disphtml("main();");
	
function main()
{
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

<center><br />
<?php
	echo "<span style=\"font-weight:bold; font-size:14px;\">Travel Bill</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='".$backURL."' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	if(strtoupper($_SESSION['nick_name'])=='START' || strtoupper($_SESSION['nick_name'])=='STAR'){
	attribute_selection($hidden,$create_control);
	}
	else {
		attribute_selection_pjp($hidden,$create_control);
	}
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";    
	?>
    <div id="display" style="max-height: 500px; width:98%; overflow-y: scroll; margin-left:10px;" align="center"></div>
</center>
<script>
function show_emp_name(statename)
{
	var state_name = statename;
	GenericAjaxFunction('excel_download_select_emp.php?state_name='+state_name,'emp_name',0);
}

function show_route_name(empcode)
{
	var emp_code = empcode;
	GenericAjaxFunction('excel_download_select_route.php?emp_code='+emp_code,'route',0);
}

/*function exporttocsv()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var emp_code = document.getElementById("emp_name").value;
	
	window.open('excel_download_customer_report.php?emp_code='+emp_code+'&start_date='+start_date+'&end_date='+end_date,'mywindow')	;
	
}*/

function exporttocsv(divid)
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	/*document.write('<div id=\'view\'>');
	document.write(view);
	document.write('<div>');*/
	//creating a temporary HTML link element (they support setting file names)*/
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	//var table_html = encodeURIComponent(table_div.outerHTML.replace(/ /g, '%20'));
	//alert(table_html);return false;
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Tour Expense Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
function display_result(){
	var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
	if(nickname=='START' || nickname=='STAR'){
	if(document.getElementById("zone").value.search(/\S/) == -1){
		alert('Please Select Zone');
		return false;
	}
	if(document.getElementById("state").value.search(/\S/) == -1){
		alert('Please Select State');
		return false;
	}
	if(document.getElementById("branch").value.search(/\S/) == -1){
		alert('Please Select Branch');
		return false;
	}
	if(document.getElementById("sale_access").value.search(/\S/) == -1){
		alert('Please Select Department');
		return false;
	}
	if(document.getElementById("employee").value.search(/\S/) == -1){
		alert('Please Select Employee');
		return false;
	}
	
		
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
		alert("Please provide start date/end date");
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
			
	var employee = document.getElementById("employee").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
  GenericAjaxFunction('tour_expense_report_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	else
	{
	  if(document.getElementById("employee").value.search(/\S/) == -1){
		alert('Please Select Employee');
		return false;
		}
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}
				
		var employee = document.getElementById("employee").value;
		
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	  GenericAjaxFunction('tour_expense_report_km_tracking_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Tour Expenses Report', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Tour Expenses Report</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}
</script>
<?php } ?>