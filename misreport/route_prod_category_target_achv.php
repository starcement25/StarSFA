<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = " hidden";
	$create_control = "";
	
	echo "<center>";?>
    <span style="font-weight:bold; font-size:14px;">Target vs Achv Report</span><br><br>
	<table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px">
      <tr class="TDHEAD_SUB">
      	<td align="right">Employee</td>
        <td align="left">
        <select id="employee" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns = 'Y' ".$emp_hierarchy_value_condition." ORDER BY emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
		}
		?>
        </select>
        </td>
        </tr>
        <tr class="TDHEAD_SUB">
      	<td align="right">Route</td>
        <td align="left">
        <select id="route" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
		$sql_route = "SELECT route_code, route_name FROM route_master WHERE route_code IN(SELECT DISTINCT route_code FROM self_appraisal_route_product_group_wise)" ;
		$res_route = mysqli_query($link,$sql_route);
		while($row_route = mysqli_fetch_assoc($res_route)){
			$route_code = $row_route['route_code'];
			$route_name = $row_route['route_name'];
			echo "<option value=\"".$route_code."\">".$route_name."</option>";
		}
		?>
        </select>
        </td>
        </tr>
         <tr class="TDHEAD_SUB">
      	<td align="right">Category</td>
        <td align="left">
        <select id="category" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
		$sql_category = "SELECT DISTINCT category FROM product_master WHERE acedns = 'Y'";
		$res_category = mysqli_query($link,$sql_category);
		while($row_category = mysqli_fetch_assoc($res_category)){
			$category = $row_category['category'];
			echo "<option value=\"".$category."\">".$category."</option>";
		}
		?>
        </select>
        </td>
        </tr>
        
     
      <?php
      $current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
		$previous_year_date_format=$previous_year."0401";
	}
	else{
		$previous_year_date = date('Y-03-01');
		$previous_year_date_format=date('Y0301');
	}
	
	$create_control = "<tr class=\"TDHEAD_SUB\"><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(order_no,-14,6) AS distinct_datetime FROM prev_order_counting_master WHERE 
						DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') > '".$previous_year_date."' ORDER BY SUBSTRING(order_no,-14,8) ASC";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		//$year_month_split = explode("-",$distinct_date);
		//$monthNum  = $year_month_split[1];
		$monthNum=substr($distinct_date,4,2);
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".substr($distinct_date,0,4)."</option>";
	}
	echo $create_control .= "</select></td></tr>";
	?>
     <tr class="TDHEAD_SUB">
      <td colspan="2" align="right">
      <input type="submit" name="submit" value="Submit" onClick="display_result();" />
      </td>
      </tr>
    </table>
    
	<input type="hidden" id="report_name" />
    <br  /><br  />
        <div id="display" style="max-height: 350px; width:90%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />

    <?php
	echo "</center>";
	?>
    <script>
	function display_result(){
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		if(document.getElementById("route").value.search(/\S/) == -1){
			alert('Please Select Route');
			return false;
		}
		if(document.getElementById("category").value.search(/\S/) == -1){
			alert('Please Select Category');
			return false;
		}
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}
		var employee = document.getElementById("employee").value;
		var route = document.getElementById("route").value;
		var category = document.getElementById("category").value;
		var month_data = document.getElementById("month_select").value;				
		
					
		/*var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}*/
		
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('route_prod_category_target_achv_data.php?employee='+employee+'&route='+route+'&category='+category+'&month_data='+month_data,'display',0);
	}
	
	function PrintElem(elem)
	{
		var displaydivval = 'display';
		var displaydiv = document.getElementById(displaydivval).innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'Target vs Achv Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Target vs Achv Report</title>');
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
			
			/*document.write('<div id=\'view\'>');
			document.write(view);
			document.write('<div>');*/
			//creating a temporary HTML link element (they support setting file names)*/
			var a = document.createElement('a');
			//getting data from our div that contains the HTML table
			var data_type = 'data:application/vnd.ms-excel';
			var table_div = document.getElementById('display');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = 'Target vs Achv Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	
	</script>
    <?php
}
?>