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
    <span style="font-weight:bold; font-size:14px;">PJP CHANGE REQUEST REPORT</span><br><br>
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
        <td colspan="2" align="center">
        <div id="date_div" >
    From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
    To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
    
    </div>
        </td>
      </tr>
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
		else
			var employee = document.getElementById("employee").value;
		
					
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
		
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('route_plan_change_request_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
	function display_control(visitdateval)
	{
		var visitdate=visitdateval.substring(4, 8)+'-'+visitdateval.substring(2, 4)+'-'+visitdateval.substring(0, 2);
		 var current_date =new Date().toISOString().slice(0,10); 
		 //alert(current_date);
		 
		 if( visitdate > current_date)
		 {
			if(document.getElementById('route_id_display_'+visitdateval).style.display=='block' )
			{
				document.getElementById('route_id_control_'+visitdateval).style.display='block';
				document.getElementById('route_id_display_'+visitdateval).style.display='none';
			}
			else if(document.getElementById('route_id_control_'+visitdateval).style.display=='block' )
			{
				document.getElementById('route_id_control_'+visitdateval).style.display='none';
				document.getElementById('route_id_display_'+visitdateval).style.display='block';
			}
		 }
		 else
		 { 
		   alert(" Visit date must be greater than current date");
		 }
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
		var mywindow = window.open('', 'PJP CHANGE REQUEST', 'height=400,width=600');
		mywindow.document.write('<html><head><title>PJP Report</title>');
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
			a.download = 'PJP CHANGE REQUEST' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	
	</script>
    <?php
}
?>