<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

//require("adminUtils.php");
	$linksetupadmin=mysql_connect("localhost","acedns_dnsprod","dnsprod1234#") or die("Setup Database Connection Error.");
	mysql_select_db("acedns_acednsproduct",$linksetupadmin) or die("could not connect the setup database");
	
	$sqlnickname="SELECT nick_name, remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsnickname=mysql_query($sqlnickname,$linksetupadmin);
	$row_nick_name = mysql_fetch_array($rsnickname);
	$cntnickname=mysql_num_rows($rsnickname);
	$remote_db_access = $row_nick_name['remote_db_access'];
	mysql_close($linksetupadmin);
	if($remote_db_access == 'yes'){
		define("SERVERREMOTE","52.66.101.239");
        define("USERREMOTE","root");
        define("PASSWORDREMOTE","cmcl@123");
        //define("DBREMOTE","acedns_$nick_name");
		mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE);

	}
	else{
		define("SERVER","localhost");
		define("USER","acedns_dnsprod");
		define("PASSWORD","dnsprod1234");
		mysql_connect(SERVER,USER,PASSWORD);
	}

$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("DB","$db");

mysql_select_db(DB);
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
        var table_div = document.getElementById('display');
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
<span style="font-weight:bold; font-size:14px;">MIS REPORT ACCESS</span><br><br>
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
<!--form name="vertical_search" method="POST" action="">
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
</form-->
</div><br />
<div id="display" style="max-height: 480px; overflow-y: scroll; overflow-x: scroll; width:95%;">
<!--div style="position:fixed;  width:inherit;">
<div style="position:relative; width:100%;">-->
<table width="70%" border="1" style="border-collapse:collapse;">
  <tr style="font-weight:bold;">
  	<td align="center" class="TDHEAD" colspan="6">Employee Information</td>
  </tr>
<!--</table>
</div>
<div style="position:relative; width:100%;">
<table width="70%" style="border-collapse:collapse;">-->
  <tr style="font-weight:bold;" class="TDHEAD_SUB">
    <td style="width:5%;" align="center">SI</td>
    <td style="width:15%;" align="center"><?php echo strtoupper($_SESSION['nick_name']); ?> Emp Code</td>
    <td style="width:15%;" align="center">Emp Code</td>
    <td style="width:40%;" align="center">Emp Name</td>
    <td style="width:15%;" align="center">Back End Access</td>
    <td style="width:25%; word-wrap:break-word;" align="center">Provide Access</td>
  </tr>
<!--</table>
</div>
</div>
<br />
<br />
<br />-->
  

<?php
$vertical_array = array();
$emp_code_array = array();
//echo "<table width=\"70%\" border=\"1\" style=\"border-collapse:collapse;\" cellpadding=\"5px\">";
				
    if(strtoupper($_SESSION['nick_name']) == 'PALSONS')
	{
		$sql_employee_access = "SELECT DISTINCT EM.emp_code,EM.dns_emp_code,EM.emp_name FROM employee_master EM,changepassword CP WHERE EM.emp_code = CP.emp_code AND (
							EM.emp_code IN(SELECT DISTINCT reporting_to FROM employee_master WHERE reporting_to NOT LIKE '%,%')
							        OR EM.emp_code IN(
							SELECT DISTINCT SUBSTRING_INDEX( SUBSTRING_INDEX( reporting_to, ',', ( 1 + ( LENGTH( reporting_to ) - 
							LENGTH( REPLACE( reporting_to, ',', '' ) ) ) ) ) , ',', -1 ) FROM employee_master WHERE reporting_to LIKE '%,%'		
							  ) OR EM.emp_code IN('E0904')
							 ) UNION ALL SELECT DISTINCT CM.customer_code AS emp_code,CM.dns_customer_code,CM.customer_name FROM customer_master CM,changepassword CP WHERE CM.customer_code = CP.emp_code 
							 AND CM.cust_type='D' AND CM.acedns='Y' ORDER BY emp_code DESC";
	}
	else
	{
	$sql_employee_access = "SELECT DISTINCT EM.emp_code,EM.dns_emp_code,EM.emp_name FROM employee_master EM,changepassword CP WHERE EM.emp_code = CP.emp_code AND (
							EM.emp_code IN(SELECT DISTINCT reporting_to FROM employee_master WHERE reporting_to NOT LIKE '%,%')
							        OR EM.emp_code IN(
							SELECT DISTINCT SUBSTRING_INDEX( SUBSTRING_INDEX( reporting_to, ',', ( 1 + ( LENGTH( reporting_to ) - 
							LENGTH( REPLACE( reporting_to, ',', '' ) ) ) ) ) , ',', -1 ) FROM employee_master WHERE reporting_to LIKE '%,%'		
							  ) OR EM.emp_code IN('E0904')
							 )ORDER BY EM.emp_code ASC";
	}
							 
	/*$sql_employee_access = "SELECT EM.emp_code,EM.emp_name FROM employee_master EM,changepassword CP 
								WHERE EM.emp_code = CP.emp_code AND 
								EM.emp_code IN(SELECT reporting_to FROM employee_master) ORDER BY EM.emp_code ASC";	*/					
	$res_employee_access = mysql_query($sql_employee_access);
	$count=1;
	while($row_employee_access = mysql_fetch_array($res_employee_access))
	{
		$emp_code = $row_employee_access['emp_code'];
		$dns_emp_code = $row_employee_access['dns_emp_code'];
		$sql_backend_access = "SELECT admin_login,admin_pwd FROM admin_master WHERE admin_login='".$emp_code."'";
		$res_backend_access = mysql_query($sql_backend_access);
		$count_backend_access=mysql_num_rows($res_backend_access);
		if($count_backend_access >0)
		{
			$backend_access="<font color='GREEN'><strong>YES</strong></font>";
		}
		else
		{
			$backend_access="<font color='RED'><strong>NO</strong></font>";
		}
		echo "
		<tr>
		<td style=\"width:5%\">".$count."</td>
		<td style=\"width:15%\">".$row_employee_access['dns_emp_code']."</td>
		<td style=\"width:15%\">".$row_employee_access['emp_code']."</td>
		<td style=\"width:40%\">".$row_employee_access['emp_name']."</td>
		<td style=\"width:15%\">".$backend_access."</td>
		<td style=\"width:25%\"><a href=\"employee_access_backend.php?emp_code=$row_employee_access[emp_code]&emp_name=$row_employee_access[emp_name]&provide=access\" style=\"color:blue;\">Provide Access</a></td>
	  </tr>";
	  
	  $count++;
	}
echo "</table>";

?>
</div>
<!--div><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
<input name="export" type="button" value="Export" id="export" onClick="exporttocsv();"></div-->

<?php
//print_r($emp_code_array);
}
function getdata()
{
	
	if($_GET['emp_code'] && $_GET['emp_name'] && $_GET['provide'] == 'access')
	{
		$sql_provideaccess = "SELECT * FROM admin_master WHERE admin_login = '$_GET[emp_code]'";
		$res_provideaccess = mysql_query($sql_provideaccess);
		$row_provideaccess = mysql_fetch_array($res_provideaccess);
		
		if($row_provideaccess['status'] == 'true')
			$unblocked = 'selected';
		else
			$unblocked = '';
		
		if($row_provideaccess['status'] == 'false')
			$blocked = 'selected';
		else
			$blocked = '';
		
		
		//echo "Hello";
		echo "<center>";
		echo "
		<form name=\"provide_access\" method=\"POST\" action=\"employee_access_backend.php\" onsubmit=\"return validate();\">
		<input type=\"hidden\" name=\"emp_code\" value=\"$_GET[emp_code]\">
		<table width=\"500px\" style=\"border-collapse:collapse;\" border=\"1\" class=\"border\">
		  <tr class=\"TDHEAD\">
			<td colspan=\"2\" align=\"center\" >Provide Access To $_GET[emp_name]</td>
		  </tr>
		  <tr>
			<td colspan=\"2\">All <font color='red'><strong>*</strong></font> fields are mandatory</td>
		  </tr>
		  <tr>
			<td align=\"right\">Login Id<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><strong>".$_GET['emp_code']."</strong></td>
		  </tr>
		  <tr>
			<td align=\"right\">Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"new_pass\" id=\"new_pass\" value='".$row_provideaccess['admin_pwd']."'></td>
		  </tr>
		  <tr>
			<td align=\"right\">Confirm Password<font color='red'><strong>*</strong></font>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><input type=\"text\" class=\"INPUT\" name=\"confirm_new_pass\" id=\"confirm_new_pass\" value=\"\"></td>
		  </tr>
		  <tr>
			<td></td>
			<td align=\"left\">
			<input type=\"submit\" name=\"submit\" value=\"Change\" class=\"inplogin\" />&nbsp;&nbsp;&nbsp;
			<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" class=\"inplogin\" onclick=\"window.location='employee_access_backend.php';\">
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
		var new_pass = document.getElementById("new_pass").value;
		if(document.getElementById("new_pass").value.search(/\S/) == -1)
		{
			alert("Enter password");
			return false;
		}
		
		var confirm_new_pass = document.getElementById("confirm_new_pass").value;
		if(document.getElementById("confirm_new_pass").value.search(/\S/) == -1)
		{
			alert("Confirm password");
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
		$emp_code=$_POST['emp_code'];
		$sql_backend_access = "SELECT admin_login,admin_pwd FROM admin_master WHERE admin_login='".$emp_code."'";
		$res_backend_access = mysql_query($sql_backend_access);
		$count_backend_access=mysql_num_rows($res_backend_access);
		if($count_backend_access >0)
		{
		$sql_update_access = "UPDATE admin_master SET admin_pwd = '".$_POST[new_pass]."' WHERE admin_login = '".$_POST[emp_code]."'";
		if(mysql_query($sql_update_access))
			{
				main();
				echo "<font color='#00CC33'><strong>Access successfully updated</strong></font>";
			}
		}
		else
		{
			$sql_insert_access = "INSERT INTO admin_master SET admin_pwd = '".$_POST[new_pass]."', 
														      admin_login = '".$_POST[emp_code]."'";
		   if(mysql_query($sql_insert_access))
			{
				main();
				echo "<font color='#00CC33'><strong>Access successfully updated</strong></font>";
			}
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


