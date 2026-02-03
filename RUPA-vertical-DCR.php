<?php
ob_start();

	/*define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_RUPA");
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");*/
	//mysqli_select_db("acedns_RUPA",$link) or die("could not connect the database");
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	require("include/config-setup.php");
	
	define("DB","acedns_RUPA");
	
	//require("include/dbcon.php");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	define("ADMIN_CSS","http://salesmpower.acedns.in/css/adminStyle.css");
	//$password="@EURO123#";
	//require_once 'misreport/protect.php';
    //Protect\with('misreport/form.php', $password);
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$todaydate =$year.'-'.$month.'-'.$date;
	$numericprevdate=date('Y-m-d', strtotime("-10 days,$todaydate "));
?>
<head>
<link href="<?=ADMIN_CSS?>" rel="stylesheet" type="text/css" />
</head>
<body>
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
<center>
	<span style="font-weight:bold; font-size:14px;">RUPA DCR</span><br><br>
    
   
	<input type="hidden" id="report_name" />
    
    <table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px">
      <tr class="TDHEAD_SUB">
      	<td align="right">Employee</td>
        <td align="left">
        <select id="employee" >
        	<option value="">Select</option>
            <!--option value="all">All</option-->
        <?php
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE FIND_IN_SET('RUPA',vertical_value) AND emp_code 
				IN(SELECT DISTINCT emp_code FROM location  WHERE (SUBSTRING(trans_id,1,1) IN
						('O','P','S') OR SUBSTRING(trans_id,1,2) IN('NO','NC','CI')) 
						AND SUBSTRING(trans_id,1,2) NOT IN('PA','SU') AND SUBSTRING(date,1,10) >='".$numericprevdate."')ORDER BY emp_name ASC";
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
      <td colspan="2" align="right">
      <input type="submit" name="submit" value="Submit" onClick="display_result();" />
      </td>
      </tr>
    </table>
    <br  /><br />
     <div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center"></div>
    
    
</center>
</body>

    <script>
	function display_result(){
		
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		else
			var employee = document.getElementById("employee").value;
		
					
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
		GenericAjaxFunction('RUPA_vertical_DCR_data.php?employee='+employee,'display',0);
		document.getElementById("print_export").style.display = '';
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	
	function Popup(data) 
	{
		var mywindow = window.open('', 'RUPA DCR', 'height=400,width=600');
		mywindow.document.write('<html><head><title>RUPA DCR Report</title>');
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
	a.download = 'RUPA DCR' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
	}
	
	</script>
