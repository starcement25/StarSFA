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
		$emp_hierarchy_value_condition = " AND emp_code IN(".$emp_hierarchy_value.") ";
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
<center>
	<span style="font-weight:bold; font-size:14px;">Work Summary Report</span><br><br>
    <table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px">
      <tr class="TDHEAD_SUB">
      	<td align="right">Employee</td>
        <td align="left">
        <select id="employee" onChange="javascript:emp_route(this.value);">
        	<option value="">Select</option>
           
        <?php
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns = 'Y' ".$emp_hierarchy_value_condition." ORDER BY emp_name ASC";
		$res_emp = mysql_query($sql_emp);
		while($row_emp = mysql_fetch_array($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
			$emp_code_string .= "'".$emp_code."',";
		}
		$emp_code_string = rtrim($emp_code_string,",");
		?>
         <option value="<?php echo $emp_code_string;?>">All</option>
        </select>
        </td>
        </tr>
        <tr class="TDHEAD_SUB">
      	<td align="right">Route</td>
        <td align="left">
       	<div id="route_select_div"></div>
        </td>
        </tr>
          <tr class="TDHEAD_SUB">
            <td align="right">Date</td>
            <td>
            From:<input type="date" name="start_date" id="start_date" style="height:15px;" />&nbsp;
            To:<input type="date" name="end_date" id="end_date" style="height:15px;" />
            </td>
          </tr>
          
       
      <tr class="TDHEAD_SUB">
      <td colspan="2" align="right">
      <input type="submit" name="submit" value="Submit" onClick="display_result();" />
      </td>
      </tr>
    </table>
     <br />
       <div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />

</center>
    <script>
	function emp_route(employee){
		if(document.getElementById("employee").value.search(/\S/) == -1)
			return false;
		var employee = encodeURIComponent(employee);
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?employee='+employee+'&type=emproute','route_select_div',0);
	}
	function display_result(){
		
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		else
		var employee_id = document.getElementById("employee").value;
		var route= document.getElementById("route").value;
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
			
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('work_summary_report_data.php?employee_id='+employee_id+'&route='+route+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	
	function Popup(data) 
	{
		var mywindow = window.open('', 'Work Summary Report', 'height=400,width=600');
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
	a.download = 'Work Summary Report' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
	
	</script>
    <?php
}
?>