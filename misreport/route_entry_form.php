<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
	$sql_check_branchdata = "SELECT * FROM branch_master ORDER BY branch_name ASC";
	$res_check_branchdata = mysqli_query($link,$sql_check_branchdata);
	$total_rows = mysqli_fetch_assoc($res_check_branchdata);
	
	
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
	$emp_upper_hierarchy='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	$emp_upper_hierarchy=return_employee_upper_hierarchy($_SESSION['admin_login']);
}
?>
<head>
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
</head>
<body>
<center><br />
<table width="40%" style="border-collapse:collapse;" cellpadding="8" >
  <tr class="TDHEAD">
  	<td colspan="2" align="center">Route Details</td>
  </tr>
  <?php if(providing_code == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">DNS Code</td>
    <td><input name="dns_code" id="dns_code" type="text"  /></td>
  </tr>
  <?php } ?>
  <?php if($total_rows>0){?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Branch Name</td>
    <td><select id="branch" onChange="get_emp_branch(this.value);">
    	<option value="">Select</option>
    <?php
		$res_check_branchdata = mysqli_query($link,$sql_check_branchdata);
		while($row_check_branchdata = mysqli_fetch_assoc($res_check_branchdata)){
			$branch_code = $row_check_branchdata['branch_code'];
			$branch_name = $row_check_branchdata['branch_name'];
			echo "<option value=\"".$branch_code."\">".$branch_name."</option>";
		}
	?>
    </select>
    </td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee</td>
    <td>
    <?php if($total_rows>0){ ?>
    <div id="show_emp" style="max-height:150px; overflow-y: scroll; background:#FFFFFF; padding-left:10px;"></div>
    <?php } else { 
	echo "<div id=\"show_emp_one\" style=\"max-height:150px; overflow-y: scroll; background:#FFFFFF; padding-left:10px;\">";
    $sql_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE 1".$emp_hierarchy_condition_one." AND EM.acedns!='N' AND EM.emp_code NOT IN (SELECT reporting_to FROM employee_master) ORDER BY EM.emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		echo "<input name=\"menu_checked[]\" type=\"checkbox\" value=\"".$emp_code."\" class=\"input_chk\" />:".$emp_name."<br>";
	}
	echo "</div>";
	 } ?>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Route Name</td>
    <td><input name="route_name" id="route_name" type="text"  /><input type="hidden" id="routecheck"></td>
  </tr>
  
  <tr class="TDHEAD_SUB">
  <td></td>
  <td align="left"><input name="submit" type="button" id="submit" value="Submit" onClick="submit_data();" /></td>
  </tr>
</table>
</center>
</body>
<script>
function submit_data(){
	<?php if(providing_code == 'yes'){ ?>
	var dns_code = document.getElementById("dns_code").value;
	if(document.getElementById("dns_code").value.search(/\S/) == -1){
		alert('Provide dns route code');
		return false;
	}
	<?php } ?>
	
	<?php if($total_rows>0){?>
	//alert(branch_code)
	var branch = document.getElementById("branch").value;
	if(document.getElementById("branch").value.search(/\S/) == -1){
		alert('Provide branch');
		return false;
	}
	<?php } ?>
	
	var empArrayinput = new Array;
	$('.input_chk:checked').each(function() {
        empArrayinput.push($(this).val());
    });
	
	if(empArrayinput.length == 0){
		alert('Provide employee');
		return false;
	}
	
	var route_name = document.getElementById("route_name").value;
	if(document.getElementById("route_name").value.search(/\S/) == -1){
		alert('Provide route name');
		return false;
	}
	
	$.post("duplicate_route_check.php",
    {
		route_name: route_name
    },
    function(data, status){
        if(data == 'EXIST'){
			alert('Route already exists. Choose different route name');
			document.getElementById("route_name").value = '';
			//document.getElementById("routecheck").value = data;
		}
		else{
			$.post("route_details_insert.php",
    {
		<?php if(providing_code == 'yes'){ ?>
		dns_code: dns_code,
		<?php } ?>
		<?php if($total_rows>0){?>
		branch: branch,
		<?php } ?>
		emp_array: empArrayinput,
		route_name: route_name
    },
    function(data, status){
        alert(data);
		window.open('http://acedns.in/acednsproduct/misreport/route_entry_form.php','_self');
    });
		}
    });
	
	/*if(document.getElementById("routecheck").value == 'EXIST'){
		alert('Route already exists. Choose different route name');
		document.getElementById("route_name").value = '';
		return false;
	}*/
	
	//return false;
	
	
}

function get_emp_branch(branch_code){
	//alert(branch_code);
	GenericAjaxFunction('route_entry_getemp.php?branch_code='+branch_code,'show_emp',0);
	
}

function check_duplicate_route(route_name){
	var duplicate_route = '0';
	alert(duplicate_route)
	$.post("duplicate_route_check.php",
    {
		route_name: route_name
    },
    function(data, status){
        //alert(data);
		//return data;
		if(data == 'EXIST'){
			/*alert('Route already exists. Choose different route name');
			document.getElementById("route_name").value = '';*/
			duplicate_route = '1';
		}
    });
	return duplicate_route;
}
</script>
<?php } ?>