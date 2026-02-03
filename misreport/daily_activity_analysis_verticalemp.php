<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	if(strtoupper($_SESSION['admin_login'])=="ADMIN")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1";
		$customer_condition=" 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
	}
	
	/*------------------------------> Select of month (financial year)<-------------------------------*/
$current_month_year = date('M')."-".date('Y');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$current_year = date('Y');
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
}
else{
	$previous_year = date('Y');
	
	$current_year = date('Y', strtotime('+1 year'));
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
	
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='PARLE')
	{
			$months = array ('Jan-'.$previous_year.'','Feb-'.$previous_year.'','Mar-'.$previous_year.'','Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');

	}
}
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
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
<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Monthly Activity Report', 'height=400,width=600');
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
	a.download = 'Daily Activity Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<body>
<center>
<br>
<table width="40%" class="border" style="border-collapse:collapse;" border="1" cellpadding="4">
  <tr  class="TDHEAD">
  	<td colspan="2" align="center">Daily Activity Analysis</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Vertical:</td>
    <td align="left">
    	<?php
		$sql_vertical = "SELECT DISTINCT PM.vertical_value as distinct_vertical_value 
		FROM product_master PM,employee_master EM WHERE  PM.vertical_value != '' AND FIND_IN_SET( PM.vertical_value,EM.vertical_value)
		AND EM.designation IN('RSM','ASM','SO','SR') 
		ORDER BY PM.vertical_value ASC";
		$res_vertical = mysqli_query($link,$sql_vertical);
		$vertical_select_control = "<select name=\"vertical\" id=\"vertical\" onchange=\"".$onclickvertical."\">";
		$vertical_select_control .= "<option value=\"\">Select</option>";
		$dist_vertical_val_array=array();
		while($row_vertical = mysqli_fetch_assoc($res_vertical)){
			$dist_vert_value = trim($row_vertical['distinct_vertical_value']);
				/*if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($dist_vert_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}*/
				if(!in_array($dist_vert_value,$dist_vertical_val_array))
				{
					$vertical_select_control .= "<option value=\"'".$dist_vert_value."'\">".$dist_vert_value."</option>";
					array_push($dist_vertical_val_array,$dist_vert_value);
				}
				$vertical_string .= "'".$dist_vert_value."',";
		}
		$vertical_string = rtrim($vertical_string,",");
		$vertical_select_control .= "<option value=\"all\">All</option>";
		$vertical_select_control .= "</select>";
		echo $vertical_select_control;
		//print_r($dist_vertical_val_array);
		?>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">RSM:</td>
    <td align="left">
    	<select name="employee" id="employee" >
			<option value="">Select</option>
            <!--option value="all">All</option-->
            <?php
			$sql_empl = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE".$emp_hierarchy_condition."  AND EM.designation='RSM' 
						ORDER BY EM.emp_name ASC";
			$res_empl = mysqli_query($link,$sql_empl);
			while($row_empl = mysqli_fetch_assoc($res_empl)){
				$emp_code_string .= "'".$row_empl['emp_code']."',";
				echo "<option value=\"'".$row_empl['emp_code']."'\">".$row_empl['emp_name']."</option>";
			}
			$emp_code_string = rtrim($emp_code_string,",");
			echo "<option value=\"".$emp_code_string."\">All</option>";
			?>
		 </select>
    </td>
  </tr>
  <!--tr class="TDHEAD_SUB">
  	<td align="right">Month:</td>
    <td align="left">
    	<select name="month_select" id="month_select">
        <option value="">Select</option>
    	<?php
		/*foreach($months as $monthvalue){
			echo "<option>".$monthvalue."</option>";
			if($monthvalue == $current_month_year)
				break;
		}*/
		?>
        </select>
    </td>
  </tr-->

  <tr class="TDHEAD_SUB">
  	<td align="right">Choose Date:</td>
  	<td align="left">
    <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
	</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
</table>
<br />
<div id="display" style="max-height: 440px; width:88%; overflow-y: scroll; margin-left:10px;" align="center">
</div>

</center>
</body>
<script>
function remove_selection(){
	//document.getElementById("today").checked = false;
	//document.getElementById("mtd").checked = false;
	//document.getElementById("custom").checked = false;
	document.getElementById("date_div").hidden = true;
	document.getElementById("display").innerHTML = '';
}

function get_data(){
	var emp_code = document.getElementById("employee").value;
	if(document.getElementById("vertical").value.search(/\S/) == -1){
		alert('Provide Vertical');
		return false;
	}
	if(document.getElementById("employee").value.search(/\S/) == -1){
		alert('Provide RSM name');
		return false;
	}
	
	/*var month = document.getElementById("month_select").value;
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Provide month');
		return false;
	}*/
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide start date');
		return false;
	}
    var start_date = document.getElementById("start_date").value;
	//var end_date = document.getElementById("end_date").value;
	var vertical = document.getElementById("vertical").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	GenericAjaxFunction('daily_activity_analysis_verticalemp_data.php?emp_code='+emp_code+'&start_date='+start_date+'&vertical='+vertical,'display',0);
}
</script>
<?php } ?>