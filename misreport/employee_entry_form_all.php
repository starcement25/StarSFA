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
<center>
<table width="40%" border="1" style="border-collapse:collapse;" cellpadding="8">
  <tr class="TDHEAD">
  	<td colspan="2" align="center">Employee Details</td>
  </tr>
  <?php if(providing_code == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td>DNS Code</td>
    <td><input name="dns_code" id="dns_code" type="text" /></td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td>Employee Name</td>
    <td><input name="emp_name" id="emp_name" type="text" onBlur="check_empname_exist(this.value)" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Designation</td>
    <td><input name="designation" id="designation" type="text" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Headquarter</td>
    <td><input name="hq" id="hq" type="text" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>State</td>
    <td>
    <select id="state">
        <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
        <option value="Andhra Pradesh">Andhra Pradesh</option>
        <option value="Arunachal Pradesh">Arunachal Pradesh</option>
        <option value="Assam">Assam</option>
        <option value="Bihar">Bihar</option>
        <option value="Chandigarh">Chandigarh</option>
        <option value="Chhattisgarh">Chhattisgarh</option>
        <option value="Dadra and Nagar Haveli">Dadra and Nagar Haveli</option>
        <option value="Daman and Diu">Daman and Diu</option>
        <option value="Delhi">Delhi</option>
        <option value="Goa">Goa</option>
        <option value="Gujarat">Gujarat</option>
        <option value="Haryana">Haryana</option>
        <option value="Himachal Pradesh">Himachal Pradesh</option>
        <option value="Jammu and Kashmir">Jammu and Kashmir</option>
        <option value="Jharkhand">Jharkhand</option>
        <option value="Karnataka">Karnataka</option>
        <option value="Kerala">Kerala</option>
        <option value="Lakshadweep">Lakshadweep</option>
        <option value="Madhya Pradesh">Madhya Pradesh</option>
        <option value="Maharashtra">Maharashtra</option>
        <option value="Manipur">Manipur</option>
        <option value="Meghalaya">Meghalaya</option>
        <option value="Mizoram">Mizoram</option>
        <option value="Nagaland">Nagaland</option>
        <option value="Orissa">Orissa</option>
        <option value="Pondicherry">Pondicherry</option>
        <option value="Punjab">Punjab</option>
        <option value="Rajasthan">Rajasthan</option>
        <option value="Sikkim">Sikkim</option>
        <option value="Tamil Nadu">Tamil Nadu</option>
        <option value="Tripura">Tripura</option>
        <option value="Uttaranchal">Uttaranchal</option>
        <option value="Uttar Pradesh">Uttar Pradesh</option>
        <option value="West Bengal">West Bengal</option>
    </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Zone</td>
    <td><input name="zone" id="zone" type="text" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Phone No</td>
    <td><input name="phn" id="phn" type="text" maxlength="10" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Email</td>
    <td><input name="email" id="email" type="text" /></td>
  </tr>
  <?php if($total_rows>0){?>
  <tr class="TDHEAD_SUB">
  	<td>Branch Name</td>
    <td><div id="show_branch" style="max-height:150px; overflow-y: scroll; background:#FFFFFF;">
    <?php
	$res_check_branchdata = mysqli_query($link,$sql_check_branchdata);
	while($row_check_branchdata = mysqli_fetch_assoc($res_check_branchdata)){
		$branch_code = $row_check_branchdata['branch_code'];
		$branch_name = $row_check_branchdata['branch_name'];
		echo "<input name=\"menu_branch_checked[]\" type=\"checkbox\" value=\"".$branch_code."\" class=\"input_chk_branch\" />:".$branch_name."<br>";
	}
	?>
    </div>
    </td>
  </tr>
  <?php } ?>
  <?php if(employeewise_hierarchy == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td>Reporting To</td>
    <td><select id="reporting_to"><option value="" selected>Select</option>
    <?php
	$sql_get_emp = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
	$res_get_emp = mysqli_query($link,$sql_get_emp);
	while($row_get_emp = mysqli_fetch_assoc($res_get_emp)){
		$emp_code = $row_get_emp['emp_code'];
		$emp_name = $row_get_emp['emp_name'];
		echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
	}
	?>
    </select>
    </td>
  </tr>
  <?php } ?>
  <?php if(vertical_fields == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td>Vertical Value</td>
    <td><div id="show_vertical" style="max-height:150px; overflow-y: scroll; background:#FFFFFF;">
    <?php
	$vertical_value_array = array();
	$sql_vertical = "SELECT vertical_value FROM product_group_master ORDER BY product_group_name ASC";
	$res_vertical = mysqli_query($link,$sql_vertical);
	while($row_vertical = mysqli_fetch_assoc($res_vertical)){
		$vertical_value = $row_vertical['vertical_value'];
		
		if(strpos($vertical_value,',') == true){
		$vertical_value_explode = explode(',',$vertical_value);
		foreach($vertical_value_explode as $value){
			if(!in_array($value,$vertical_value_array)){
				array_push($vertical_value_array,$value);
				//$vertical_value_string .= "'".$value."',";
			}
		}
		}
		else{
			if(!in_array($vertical_value,$vertical_value_array)){
				array_push($vertical_value_array,$vertical_value);
				//$vertical_value_string .= "'".$branch_code."',";
			}
		}
	}
	foreach($vertical_value_array as $vert_val){
		echo "<input name=\"menu_vertical_checked[]\" type=\"checkbox\" value=\"".$vert_val."\" class=\"input_chk_vertical\" />:".$vert_val."<br>";
	}
	?>
    </div>
    </td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td>Sale Access</td>
    <td><select id="sale_access">
    <option>Primary</option>
    <option>Secondary</option>
    <option>Tertiary</option>
    </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>ACEdns</td>
    <td><select id="acedns"><option value="Y">Yes</option><option value="N">No</option></select></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td colspan="2" align="right"><input name="submit" type="button" id="submit" value="Submit" onClick="submit_data();" /></td>
  </tr>
</table>
</center>
</body>
<script>
function submit_data(){
	<?php if(providing_code == 'yes'){ ?>
	var dns_code = document.getElementById("dns_code").value;
	if(document.getElementById("dns_code").value.search(/\S/) == -1){
		alert('Provide dns code');
		return false;
	}
	<?php } ?>
	
	var emp_name = document.getElementById("emp_name").value;
	if(document.getElementById("emp_name").value.search(/\S/) == -1){
		alert('Provide employee name');
		return false;
	}
	
	var designation = document.getElementById("designation").value;
	if(document.getElementById("designation").value.search(/\S/) == -1){
		alert('Provide designation');
		return false;
	}
	
	var hq = document.getElementById("hq").value;
	if(document.getElementById("hq").value.search(/\S/) == -1){
		alert('Provide headquarter');
		return false;
	}
	
	var zone = document.getElementById("zone").value;
	if(document.getElementById("zone").value.search(/\S/) == -1){
		alert('Provide zone');
		return false;
	}
	
	var phn = document.getElementById("phn").value;
	if(document.getElementById("phn").value.search(/\S/) == -1){
		alert('Provide phone number');
		return false;
	}
	if (isNaN(phn)){
		alert("Phone number should be numeric");
		return false;
  	}
	
	var email=document.getElementById("email").value;
	var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if(atpos< 1 || dotpos<(atpos+2) || (dotpos+2)>=email.length || document.getElementById("email").value.search(/\S/)==-1)
	{
	alert ("Enter valid email");
	return false;
	}
	
	<?php if($total_rows>0){ ?>
	var branchArrayinput = new Array;
	$('.input_chk_branch:checked').each(function() {
        branchArrayinput.push($(this).val());
    });
	
	if(branchArrayinput.length == 0){
		alert('Provide branch');
		return false;
	}
	<?php } ?>
	
	<?php if(employeewise_hierarchy == 'yes'){ ?>
	var reporting_to = document.getElementById("reporting_to").value;
	if(document.getElementById("reporting_to").value.search(/\S/) == -1){
		alert('Provide reporting to');
		return false;
	}
	<?php } ?>
	
	<?php if(vertical_fields == 'yes'){ ?>
	var verticalArrayinput = new Array;
	$('.input_chk_vertical:checked').each(function() {
        verticalArrayinput.push($(this).val());
    });
	
	if(verticalArrayinput.length == 0){
		alert('Provide vertical');
		return false;
	}
	<?php } ?>
	
	//alert(JSON.stringify(branchArrayinput));
	//alert(JSON.stringify(verticalArrayinput));
	//return false;
	var state = document.getElementById("state").value;
	var sale_access = document.getElementById("sale_access").value;
	var acedns = document.getElementById("acedns").value;
	
	$.post("employee_details_insert_all.php",
    {
		<?php if(providing_code == 'yes'){ ?>
		dns_code: dns_code,
		<?php } ?>
		emp_name: emp_name,
        designation: designation,
		hq: hq,
		state: state,
		zone: zone,
		phn: phn,
		email: email,
		<?php if($total_rows>0){ ?>
		branch: branchArrayinput,
		<?php } ?>
		<?php if(employeewise_hierarchy == 'yes'){ ?>
		reporting_to: reporting_to,
		<?php } ?>
		<?php if(vertical_fields == 'yes'){ ?>
		vertical_fields: verticalArrayinput,
		<?php } ?>
		sale_access: sale_access,
		acedns: acedns
    },
    function(data, status){
        alert(data);
		window.open('http://acedns.in/acednsproduct/misreport/employee_entry_form_all.php','_self');
    });
}

function check_empname_exist(emp_name){
	$.post("check_emp_name.php",
    {
		emp_name: emp_name
    },
    function(data, status){
		if(data != 'success'){
        	alert(data);
			document.getElementById("emp_name").value = '';
		}
    });
}
</script>
<?php
}
?>