<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
		
	if($_SESSION['admin_login']=="admin")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 AND EM.acedns!='N' ";
		$customer_condition=" 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") ";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
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
<body>
<center><br />
	<div>
    <table cellpadding="4">
      <tr class="TDHEAD">
      	<td colspan="2" align="center">Choose any one of the following:</td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>Employee:</td>
        <td>
        <select name="emp_name" id="emp_name" onChange="setblank('emp_name');">
        <option value="" selected>Select</option>
        <?php
        $sql_select_emp = "SELECT DISTINCT EM.emp_code, EM.emp_name FROM employee_master EM, outstanding_ageing OA, customer_master CM WHERE ".$emp_hierarchy_condition." AND OA.customer_code=CM.customer_code AND CM.emp_code=EM.emp_code ORDER BY EM.emp_name ASC";
        $res_select_emp = mysql_query($sql_select_emp);
        while($row_select_emp = mysql_fetch_array($res_select_emp)){
            echo "<option value=\"'".$row_select_emp['emp_code']."'\">".$row_select_emp['emp_name']."</option>";
            $emp_code_string .= "'".$row_select_emp['emp_code']."',";
        }
		 $emp_code_string = rtrim($emp_code_string,",");
         echo "<option value=\"".$emp_code_string."\">All</option>";
        ?>
        </select>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>Customer:</td>
        <td>
        <select name="customer" id="customer" onChange="setblank('customer');" onChange="setblank('customer');">
        			<option value="" selected>Select</option>
        			<?php
					$sql_get_customer = "SELECT DISTINCT CM.customer_name, CM.customer_code FROM customer_master CM, outstanding_ageing OA WHERE ".$customer_condition." AND OA.customer_code=CM.customer_code ORDER BY CM.customer_name ASC";
					$res_get_customer = mysql_query($sql_get_customer);
					while($row_get_customer = mysql_fetch_array($res_get_customer)){
						echo "<option value=\"'".$row_get_customer['customer_code']."'\">".$row_get_customer['customer_name']."</option>";
						$customer_code_string .= "'".$row_get_customer['customer_code']."',";							
					}
					$customer_code_string = rtrim($customer_code_string,",");
                    echo "<option value=\"".$customer_code_string."\">All</option>";
					?>
        </select>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td></td>
        <td><input name="submit" type="button" value="Submit" id="submit" onClick="submitdata();"></td>
      </tr>
    </table>
    </div>
    <br />
    <div id="display" style="max-height: 400px; width:70%; overflow-y: scroll;" align="center"></div>
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
	a.download = 'Outstanding' + postfix + '.xls';
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
	var mywindow = window.open('', 'Outstanding', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Outstanding</title>');
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

function submitdata(){
	if(document.getElementById("emp_name").value.search(/\S/) == -1 && document.getElementById("customer").value.search(/\S/) == -1){
		alert('Provide any one selection');
		return false;
	}
	
	var emp_code = document.getElementById("emp_name").value;
	var customer_name = document.getElementById("customer").value;
		
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('outstanding_ageing_data.php?emp_code='+emp_code+'&customer_name='+customer_name,'display',0);
}

function setblank(value){
	if(value == 'emp_name')
		document.getElementById("customer").value = '';
	if(value == 'customer')
		document.getElementById("emp_name").value = '';
}
</script>
<?php
}
?>