<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>

<?php
//require("adminUtils.php");
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<?php
$blank_arg = 'all';
if($_GET)
	disphtml("getdata();");
else if($_POST)
	disphtml("postdata();");
else 
	disphtml("main($blank_arg);");
	
ob_end_flush();

function main($blank_arg)
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
<script>
function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Employee Access', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Employee Access</title>');
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
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('displayexcel');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Employee Access' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
</script>

<center>
<div align="center">
<?php
if($blank_arg == 'all'){$condition = "";}
else if($blank_arg == 'MACROMAN'){$condition = " WHERE SUBSTRING_INDEX(EM.vertical_value, ',', 1) LIKE 'M%'";}
else if($blank_arg != ''){$condition = " WHERE SUBSTRING_INDEX(EM.vertical_value, ',', 1)='".$blank_arg."' ";}
else $condition = "";
	
$vertical_name_array = array();
$sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', 1) as distinct_vertical_value FROM employee_master EM ORDER BY SUBSTRING_INDEX(EM.vertical_value, ',', 1) ASC";
$res_vertical = mysql_query($sql_vertical);
while($row_vertical = mysql_fetch_array($res_vertical))
{
	$dist_vert_value = $row_vertical['distinct_vertical_value'];
	if(strtoupper($_SESSION['nick_name']) == 'RUPA')
	{
		$pos = substr($dist_vert_value,0,1);
		if($pos == 'M')
		{
			$dist_vert_value = 'MACROMAN';
		}
	}
	if(!in_array($dist_vert_value,$vertical_name_array))
		array_push($vertical_name_array,$dist_vert_value);
}

?>
<form name="vertical_search" method="POST" action="">
Vertical Name: <select name="vertical_name">
<option value="all" selected>All</option>
<?php
foreach($vertical_name_array as $val)
{
	if($val == $blank_arg)
		echo "<option selected>".$val."</option>";
	else
		echo  "<option>".$val."</option>";
}
?>

</select>
<input type="submit" name="submit" value="Search" />
</form>
</div><br />
<div id="display" style="max-height: 380px; overflow-y: scroll; overflow-x: scroll; width:95%;">
<div style="position:fixed;  width:inherit;">
<div style="position:relative; width:100%;">
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr style="font-weight:bold;">
  	<td align="center" class="TDHEAD">Blocked Employee Information</td>
  </tr>
</table>
</div>
<div style="position:relative; width:100%;">
<table width="100%" style="border-collapse:collapse;">
  <tr style="font-weight:bold;" class="TDHEAD_SUB">
    <td style="width:2%;" align="center">SI</td>
    <td style="width:13%;" align="center">SAP Emp Code</td>
    <td style="width:10%;" align="center">Emp Code</td>
    <td style="width:13%;" align="center">Emp Name</td>
    <td style="width:13%;" align="center">Headquarter</td>
    <td style="width:13%;" align="center">Location</td>
    <td style="width:13%;" align="center">Designation</td>
    <td style="width:13%; word-wrap:break-word;" align="center">Provide Access</td>
    <td style="width:13%;" align="center">Clear Allocation</td>
  </tr>
</table>
</div>
</div>
<br />
<br />
<br />
  

<?php
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
$excel_header = "<table border=\"1\" style=\"border-collapse:collapse;\">
  <tr style=\"font-weight:bold;\">
    <td align=\"center\">SI</td>
    <td align=\"center\">SAP Emp Code</td>
    <td align=\"center\">Emp Code</td>
    <td align=\"center\">Emp Name</td>
    <td align=\"center\">Headquarter</td>
    <td align=\"center\">Location</td>
    <td align=\"center\">Designation</td>
    <td align=\"center\">Provide Access</td>
    <td align=\"center\">Clear Allocation</td>
  </tr>";
$vertical_array = array();
$emp_code_array = array();
echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" cellpadding=\"5px\">";
$count = 1;
$sql_vertical = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', 1) as distinct_vertical_value FROM employee_master EM ".$condition." ORDER BY SUBSTRING_INDEX(EM.vertical_value, ',', 1) ASC";
$res_vertical = mysql_query($sql_vertical);
while($row_vertical = mysql_fetch_array($res_vertical))
{
	$distinct_vertical_value = rtrim($row_vertical['distinct_vertical_value']);
	$vertical_name = $row_vertical['distinct_vertical_value'];
	
	if(strtoupper($_SESSION['nick_name']) == 'RUPA')
	{
		$pos = substr($vertical_name,0,1);
		if($pos == 'M')
		{
			$vertical_name = 'MACROMAN';
		}
	}
	
			
	if(!in_array($vertical_name,$vertical_array))
	{
		array_push($vertical_array,$vertical_name);
		echo "<tr class=\"TDHEAD_SUB\" align=\"center\"><td colspan=\"9\">".$vertical_name."</td></tr>";
		$excel_header .= "<tr><td colspan=\"9\" align=\"center\" style=\"font-weight:bold;\">".$vertical_name."</td></tr>";
	}
	
	$sql_employee_access = "SELECT EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.HQ, EM.designation, EM.branch_code FROM employee_master 
			EM WHERE FIND_IN_SET('".$distinct_vertical_value."',EM.vertical_value) AND EM.acedns = 'N' ".$emp_hierarchy_condition."  ORDER BY EM.emp_name";
	$res_employee_access = mysql_query($sql_employee_access);
	while($row_employee_access = mysql_fetch_array($res_employee_access))
	{
		$emp_code = $row_employee_access['emp_code'];
		$branch_name = '';
		$branch_code_array = explode(",",$row_employee_access['branch_code']);
		//$pos = strpos($row_employee_access['branch_code'], ",");
		if(!empty($branch_code_array))
		{
			//$branch_code_array = explode(",",$row_employee_access['branch_code']);
			foreach($branch_code_array as $value)
			{
				$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$value'";
				$res_branch_name = mysql_query($sql_branch_name);
				$row_branch_name = mysql_fetch_array($res_branch_name);
				$branch_name .= $row_branch_name['branch_name'].",";
			}
			$branch_name = rtrim($branch_name,",");
		}
		else
		{
			$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$row_employee_access[branch_code]'";
			$res_branch_name = mysql_query($sql_branch_name);
			$row_branch_name = mysql_fetch_array($res_branch_name);
			$branch_name = $row_branch_name['branch_name'];
		}
		
		$sql_device_id = "SELECT deviceid FROM changepassword WHERE emp_code = '".$emp_code."'";
		$res_device_id = mysql_query($sql_device_id);
		$row_device_id = mysql_fetch_array($res_device_id);
		$device_id = $row_device_id['deviceid'];
		
		$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code='$row_employee_access[branch_code]'";
		$res_branch_name = mysql_query($sql_branch_name);
		$row_branch_name = mysql_fetch_array($res_branch_name);
		if($device_id != '')
			$deviceid = "<a href=\"employee_access_verticalwise_blocked.php?emp_code=$row_employee_access[emp_code]&allocation=clear\" style=\"color:blue;\">Clear Allocation</a>";
		else
			$deviceid = "--------";
		
		if(!in_array($emp_code,$emp_code_array))	
		{
		array_push($emp_code_array,$emp_code);
		echo "
		<tr>
		<td style=\"width:2%\">$count</td>
		<td style=\"width:14%\">$row_employee_access[dns_emp_code]</td>
		<td style=\"width:14%\">$row_employee_access[emp_code]</td>
		<td style=\"width:14%\">$row_employee_access[emp_name]<a href='change_emp_name.php?emp_code=$row_employee_access[emp_code]'>&nbsp;&nbsp;<img src='fileedit.png'></a></td>
		<td style=\"width:14%\">$row_employee_access[HQ]</td>
		<td style=\"width:14%\">$branch_name</td>
		<td style=\"width:14%\">$row_employee_access[designation]</td>
		<td style=\"width:14%\"><a href=\"employee_access_verticalwise_blocked.php?emp_code=$row_employee_access[emp_code]&emp_name=$row_employee_access[emp_name]&provide=access\" style=\"color:blue;\">Provide Access</a></td>
		<td style=\"width:13%\">$deviceid</td>
	  </tr>";
	  $excel_header .= "
		<tr>
		<td>$count</td>
		<td>$row_employee_access[dns_emp_code]</td>
		<td>$row_employee_access[emp_code]</td>
		<td>$row_employee_access[emp_name]</td>
		<td>$row_employee_access[HQ]</td>
		<td>$branch_name</td>
		<td>$row_employee_access[designation]</td>
		<td>Provide Access</td>
		<td>$deviceid</td>
	  </tr>";
		}
	  
	  $count++;
	}
}
echo "</table>";
$excel_header .= "</table>";
//print_r($emp_code_array);
?>
</div>
<div id="displayexcel" hidden>
<?php
echo $excel_header;
?>
</div>
<div><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#displayexcel');">&nbsp;
<input name="export" type="button" value="Export" id="export" onClick="exporttocsv();"></div>

<?php
//print_r($emp_code_array);
}
function getdata()
{
	if($_GET['emp_code'] && $_GET['allocation'] == "clear")
	{
		$sql_clear_allocation = "UPDATE changepassword SET deviceid='',registrationid='' WHERE emp_code = '$_GET[emp_code]'";
		if(mysql_query($sql_clear_allocation))
		{
			main();
			//echo "<font color='#00CC33'><strong>Allocation Cleared Successfully</strong></font>";
			echo "<script>";
			echo "alert(\"Allocation Cleared Successfully\");";
			echo "</script>";
		}
	}
	
	else if($_GET['emp_code'] && $_GET['emp_name'] && $_GET['provide'] == 'access')
	{
		$sql_provideaccess = "SELECT acedns FROM employee_master WHERE emp_code = '$_GET[emp_code]'";
		$res_provideaccess = mysql_query($sql_provideaccess);
		$row_provideaccess = mysql_fetch_array($res_provideaccess);
		
		if(strtoupper($row_provideaccess['acedns']) == 'Y')
				$unblocked = 'selected';
		else 	$unblocked = '';
		if(strtoupper($row_provideaccess['acedns']) == 'N')
				$blocked = 'selected';
		else 	$blocked = '';	
		
		
		//echo "Hello";
		echo "<center>";
		echo "
		<form name=\"provide_access\" method=\"POST\" action=\"employee_access_verticalwise_blocked.php\" onsubmit=\"return validate();\">
		<input type=\"hidden\" name=\"emp_code\" value=\"$_GET[emp_code]\">
		<table width=\"500px\" style=\"border-collapse:collapse;\" border=\"1\" class=\"border\">
		  <tr class=\"TDHEAD\">
			<td colspan=\"2\" align=\"center\" >Provide Access To $_GET[emp_name]</td>
		  </tr>
		  <tr>
			<td colspan=\"2\">All <font color='red'><strong>*</strong></font> fields are mandatory</td>
		  </tr>
		  <tr>
			<td align=\"right\">Old Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"old_pass\" id=\"old_pass\" value=\"$row_provideaccess[oldpassword]\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">New Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"new_pass\" id=\"new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">Confirm New Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"confirm_new_pass\" id=\"confirm_new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td align=\"right\">Status&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td>
				<select name=\"status\">
					<option value=\"N\" $blocked>Blocked</option>
					<option value=\"Y\" $unblocked>Unblocked</option>
			</td>
		  </tr>
		  <tr>
			<td></td>
			<td align=\"left\">
			<input type=\"submit\" name=\"submit\" value=\"Change\" class=\"inplogin\" />&nbsp;&nbsp;&nbsp;
			<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" class=\"inplogin\" onclick=\"window.location='employee_access_verticalwise_blocked.php';\">
			</td>
		  </tr>
		</table>
		</form>
		";
		echo "</center>";
	}
?>
	<script>
	function validate()
	{
		var old_pass = document.getElementById("old_pass").value;
		if(document.getElementById("old_pass").value.search(/\S/) == -1)
		{
			alert("Enter old password");
			return false;
		}
		
		var new_pass = document.getElementById("new_pass").value;
		if(document.getElementById("new_pass").value.search(/\S/) == -1)
		{
			alert("Enter new password");
			return false;
		}
		
		var confirm_new_pass = document.getElementById("confirm_new_pass").value;
		if(document.getElementById("confirm_new_pass").value.search(/\S/) == -1)
		{
			alert("Confirm new password");
			return false;
		}
		
		if(new_pass != confirm_new_pass)
		{
			alert("Confirm password doesnot match new password");
			return false;
		}
		
		if(document.getElementById("cancel").value || document.getElementById("new_pass").value == '')
		return true;
	}
	</script>
<?php
}
?>

<?php
function postdata()
{
	if($_POST['submit'] == 'Change')
	{
		//$sql_update_access = "UPDATE changepassword SET newpassword = '$_POST[new_pass]', oldpassword = '$_POST[new_pass]', status = '$_POST[status]' WHERE emp_code = '$_POST[emp_code]' AND oldpassword = '$_POST[old_pass]'";
		$sql_update_access = "UPDATE changepassword SET newpassword = '$_POST[new_pass]', oldpassword = '$_POST[new_pass]' WHERE emp_code = '$_POST[emp_code]' AND oldpassword = '$_POST[old_pass]'";
		if(mysql_query($sql_update_access))
			{
				$sql_update_emp_acedns = "UPDATE employee_master SET acedns='".$_POST['status']."',acedns_changed_date=CURRENT_TIMESTAMP() 
										WHERE emp_code = '".$_POST[emp_code]."'";
				mysql_query($sql_update_emp_acedns);
				main();
				echo "<font color='#00CC33'><strong>Access successfully updated</strong></font>";
			}
		
	}
	else if($_POST['submit'] == 'Search')
	{
		//echo $_POST['vertical_name'];
		main($_POST['vertical_name']);
	}
	else 
	{
		main();
		echo "<font color='#FF0000'><strong>Data update failure</strong></font>";
	}
	
	
}
?>
</center>


