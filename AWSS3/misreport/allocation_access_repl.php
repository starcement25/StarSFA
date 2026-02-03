<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?><head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>



<table class="border" border="1" width="100%" style="border-collapse:collapse;">
  <tr>
  	<td class="TDHEAD" colspan="6" align="center">Sauda Allocation Access Permission</td>
  </tr>
  <tr align="center" class="TDHEAD_SUB">
  	<td>Serial</td>
    <td>Employee Name</td>
    <td>DNS Emp Code</td>
    <td>Designation</td>
    <td>Sauda Alloaction</td>
    <td>Get Allocation</td>
  </tr>
  
<?php
if($_GET)
	$condition = " WHERE designation LIKE '%$_GET[search_designation_name]%' ";
else
	$condition = "";
	
$count = 1;
/*$sql_designation = "SELECT designation FROM employee_master ".$condition." GROUP BY designation";
$res_designation = mysql_query($sql_designation);
while($row_designation = mysql_fetch_array($res_designation))
{
	$sql_designation_flag = "SELECT flag, get_allocation FROM sauda_allocation_access WHERE designation = '$row_designation[designation]'";
	if(mysql_query($sql_designation_flag))
	{
		$res_designation_flag = mysql_query($sql_designation_flag);
		$row_designation_flag = mysql_fetch_array($res_designation_flag);
		$flag = $row_designation_flag['flag'];
		$get_allocation = $row_designation_flag['get_allocation'];
		
		if($flag == 'yes')
			$flag_selected_yes = 'checked';
		else
			$flag_selected_yes = '';
			
		if($flag == 'no')
			$flag_selected_no = 'checked';
		else 
			$flag_selected_no = '';
			
			
		if($get_allocation == 'yes')
			$get_allocation_selected_yes = 'checked';
		else
			$get_allocation_selected_yes = '';
		
		if($get_allocation == 'no')
			$get_allocation_selected_no = 'checked';
		else
			$get_allocation_selected_no = '';
		
		
	echo "<tr>
			<td>$count</td>
			<td id=\"$row_designation[designation]\">$row_designation[designation]</td>
			<td>Yes:<input type=\"radio\" name=\"allocate_sauda_$count\" id=\"allocate_sauda_yes\" value=\"yes\" onclick=\"set_yes('$row_designation[designation]');\" $flag_selected_yes>&nbsp;&nbsp;
				No:<input type=\"radio\" name=\"allocate_sauda_$count\" id=\"allocate_sauda_no\" value=\"no\" onclick=\"set_no('$row_designation[designation]');\" $flag_selected_no>
			</td>
			<td>Yes:<input type=\"radio\" name=\"get_allocate_sauda_$count\" id=\"get_allocate_sauda_yes\" value=\"yes\" onclick=\"set_getallocation_yes('$row_designation[designation]');\" $get_allocation_selected_yes>&nbsp;&nbsp;
				No:<input type=\"radio\" name=\"get_allocate_sauda_$count\" id=\"get_allocate_sauda_no\" value=\"no\" onclick=\"set_getallocation_no('$row_designation[designation]');\" $get_allocation_selected_no>
			</td>
		  </tr>";
		  
	}
	$count++;
}*/

$sql_select_emp = "SELECT * FROM employee_master".$condition;
$res_select_emp = mysql_query($sql_select_emp);
while($row_select_emp = mysql_fetch_array($res_select_emp))
{
	$emp_code = $row_select_emp['emp_code'];
	$dns_emp_code = $row_select_emp['dns_emp_code'];
	$emp_name = $row_select_emp['emp_name'];
	$designation = $row_select_emp['designation'];
	
	$sql_designation_flag = "SELECT flag, get_allocation FROM sauda_allocation_access WHERE emp_code = '".$emp_code."'";
	$res_designation_flag = mysql_query($sql_designation_flag);
	$row_designation_flag = mysql_fetch_array($res_designation_flag);
	
	$flag = $row_designation_flag['flag'];
	$get_allocation = $row_designation_flag['get_allocation'];
	
	if($flag == 'yes')
		$flag_selected_yes = 'checked';
	else
		$flag_selected_yes = '';
		
	if($flag == 'no')
		$flag_selected_no = 'checked';
	else 
		$flag_selected_no = '';
		
		
	if($get_allocation == 'yes')
		$get_allocation_selected_yes = 'checked';
	else
		$get_allocation_selected_yes = '';
	
	if($get_allocation == 'no')
		$get_allocation_selected_no = 'checked';
	else
		$get_allocation_selected_no = '';
		
	
	echo "<tr>
			<td>$count</td>
			<td>$emp_name</td>
			<td>$dns_emp_code</td>
			<td>$designation</td>
			<td>Yes:<input type=\"radio\" name=\"allocate_sauda_$count\" id=\"allocate_sauda_yes\" value=\"yes\" onclick=\"set_yes('$emp_code');\" $flag_selected_yes>&nbsp;&nbsp;
				No:<input type=\"radio\" name=\"allocate_sauda_$count\" id=\"allocate_sauda_no\" value=\"no\" onclick=\"set_no('$emp_code');\" $flag_selected_no>
			</td>
			<td>Yes:<input type=\"radio\" name=\"get_allocate_sauda_$count\" id=\"get_allocate_sauda_yes\" value=\"yes\" onclick=\"set_getallocation_yes('$emp_code');\" $get_allocation_selected_yes>&nbsp;&nbsp;
				No:<input type=\"radio\" name=\"get_allocate_sauda_$count\" id=\"get_allocate_sauda_no\" value=\"no\" onclick=\"set_getallocation_no('$emp_code');\" $get_allocation_selected_no>
			</td>
		  </tr>";
	
	$count++;
	
}

?>
</table>