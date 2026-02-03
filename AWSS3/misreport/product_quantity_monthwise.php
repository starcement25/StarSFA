<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	$current_date_array = explode("-",$current_date);
	$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	
	if($_SESSION['admin_login']=="admin")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.")";
	}
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
<body onLoad="show_data();">
<center><br />
	<div>
    	Employee:<select name="emp_name" id="emp_name" onChange="show_customer_name(this.value);">
         					<option value="" selected>Select</option>
                            <?php
							$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE ".$emp_hierarchy_condition." AND EM.emp_code NOT LIKE 'C%' ORDER BY EM.emp_name ASC";
							$res_select_emp = mysql_query($sql_select_emp);
							while($row_select_emp = mysql_fetch_array($res_select_emp)){
								$emp_code = $row_select_emp['emp_code'];
								$emp_name = $row_select_emp['emp_name'];
								
								$sql_check_menu_access = "SELECT emp_code FROM menu_access WHERE emp_code='".$emp_code."' AND not_accessible_menu = 'order'";
								$res_check_menu_access = mysql_query($sql_check_menu_access);
								$menu_access_rows = mysql_num_rows($res_check_menu_access);
								if($menu_access_rows == 0)
									echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
							}
							?>
                        </select>
         &nbsp;&nbsp;
         Customer:<select name="cust_name" id="cust_name" >
         					<option value="" selected>Select</option>
                         </select>
         &nbsp;&nbsp;                
         <input name="submit" type="button" value="Submit" id="submit" onClick="submitdata();">
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <span style="text-align:right; font-weight:bold; margin-bottom:8px;">*UOM: Pcs</span>
    </div>
    <br />
    <div id="display" style="max-height: 500px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center"></div>
    <br />
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
</center>
</body>
<script>
function exporttocsv()
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Stock Audit' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
	
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Stock Audit', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Stock Audit</title>');
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

function show_customer_name(emp_code){
	//alert(emp_code);
	GenericAjaxFunction('product_quantity_monthwise_getcustomer.php?emp_code='+emp_code,'cust_name',0);
}

function submitdata(){
	if(document.getElementById("emp_name").value.search(/\S/) == -1){
		alert('Provide Employee');
		return false;
	}
	if(document.getElementById("cust_name").value.search(/\S/) == -1){
		alert('Provide Customer');
		return false;
	}
	var emp_code = document.getElementById("emp_name").value;
	var cust_name = document.getElementById("cust_name").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('prod_month_data.php?emp_code='+emp_code+'&cust_name='+cust_name,'display',0);
}

function show_data(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('prod_month_data.php','display',0)
}
</script>
<?php
}
?>