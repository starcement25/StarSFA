<?php
ob_start();
	session_start();
	require("adminUtils.php");
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
<table class="border" width="45%" style="border-collapse:collapse;" cellpadding="4">
<form name="KYC_download" method="post" onSubmit="return submitdata();" action="KYC_report_download.php">
<input type="hidden" name="mode" value="download_KYC" />
        <?php
		if($_SESSION['admin_login']=="admin")
		{
			$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
			$emp_hierarchy_condition="";
			
		}
		else
		{
			$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
			$emp_hierarchy_condition=" AND emp_code IN (".$emp_hierarchy.")";
		}
        ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee:</td>
    <td align="left">
    <select name="emp_name" id="emp_name" onChange="show_route_name(this.value);">
        <option value="" selected>Select Employee</option>
        <option value="all">All</option>
        <?php
		  $sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE 1 ".$emp_hierarchy_condition." ORDER BY emp_name ASC";
		  $res_emp = mysql_query($sql_emp);
		  while($row_emp = mysql_fetch_array($res_emp)){
			  $emp_code = $row_emp['emp_code'];
			  $emp_name = $row_emp['emp_name'];
			  
			  echo "<option value=\"".$emp_code."\" >".$emp_name."</option>";
		  }
		?>
     </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Date:</td>
  	<td>
    From:<input type="date" name="start_date" id="start_date" style="height:15px;" />&nbsp;
	To:<input type="date" name="end_date" id="end_date" style="height:15px;" />
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit1" type="submit" value="Submit" ></td>
  </tr>
  </form>
</table>
	
    <br />
    <div id="display" style="max-height: 500px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
</center>

<script>
function submitdata()
{
	if(document.getElementById('emp_name').value.search(/\S/) == -1){
		alert('Please Select Employee');
		return false;
	}
	var emp_code = document.getElementById("emp_name").value;
	
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
	
	return true;
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'New Customer Download Details', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Report Details</title>');
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