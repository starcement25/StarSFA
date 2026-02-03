<?php
ob_start();
session_start();
if(strtoupper($_SESSION['nick_name']) == 'TECPL')
{
	require("adminUtils_tecpl.php");
}
else
{
	require("adminUtils.php");
}

if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
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
  <tr class="TDHEAD_SUB">
  	<td>Employee Id</td>
    <td><input name="emp_id" id="emp_id" type="text" onBlur="javascript:check_empid_exist(this.value);" /></td>
  </tr>

  <tr class="TDHEAD_SUB">
  	<td>Employee Name</td>
    <td><input name="emp_name" id="emp_name" type="text" onBlur="javascript:check_empname_exist(this.value);" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Designation</td>
    <td><select id="designation" name="designation" onChange="javascript:show_others('designation',this.value);"><option value='' selected>Select</option>
    <?php
	$sql_check_designation="SELECT  DISTINCT designation FROM employee_master ORDER BY designation ASC";
	$res_check_designation = mysql_query($sql_check_designation);
	while($row_check_designation = mysql_fetch_array($res_check_designation)){
		$designation = $row_check_designation['designation'];
		echo "<option value=\"".$designation."\">".$designation."</option>";
	}
	echo "<option value=\"Other\">Other</option>";
	?>
    </select></td>
  </tr>
   <tr class="TDHEAD_SUB" id="otherdesignation" style="display:none">
  	<td>Other</td>
    <td><input name="other_designation" id="other_designation" type="text" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td>Mobile</td>
    <td><input name="mobile" id="mobile" type="text" maxlength="10" /></td>
  </tr>
   <tr class="TDHEAD_SUB">
  	<td>Zone</td>
    <td><select id="zone" name="zone" onChange="show_others('zone',this.value);zone_region();"><option value='' selected>Select</option>
    <?php
	$sql_check_zone="SELECT  DISTINCT zone FROM customer_master ORDER BY zone ASC";
	$res_check_zone = mysql_query($sql_check_zone);
	while($row_check_zone = mysql_fetch_array($res_check_zone)){
		$zone = $row_check_zone['zone'];
		echo "<option value=\"".$zone."\">".$zone."</option>";
	}
	echo "<option value=\"Other\">Other</option>";
	?>
    </select></td>
  </tr>
  <tr class="TDHEAD_SUB" id="otherzone" style="display:none">
  	<td>Other</td>
    <td><input name="other_zone" id="other_zone" type="text" /></td>
  </tr>

  <tr class="TDHEAD_SUB">
  	<td>Region</td>
    <td><!--select id="region" name="region" onChange="javascript:show_others('region',this.value);"><option value='' selected>Select</option>
    <?php
	/*$sql_check_region="SELECT  DISTINCT district FROM customer_master ORDER BY district ASC";
	$res_check_region = mysql_query($sql_check_region);
	while($row_check_region = mysql_fetch_array($res_check_region)){
		$region = $row_check_region['district'];
		echo "<option value=\"".$region."\">".$region."</option>";
	}
	echo "<option value=\"Other\">Other</option>";*/
	?>
    </select-->
    	<div id="region_select_div"></div>
    </td>
  </tr>
   <tr class="TDHEAD_SUB" id="otherregion" style="display:none">
  	<td>Other</td>
    <td><input name="other_region" id="other_region" type="text" /></td>
  </tr>

   <tr class="TDHEAD_SUB">
  	<td>Division</td>
    <td><!--select id="division" name="division" onChange="javascript:show_others('division',this.value);"><option value='' selected>Select</option>
    <?php
	/*$sql_check_division="SELECT  DISTINCT route_name FROM route_master ORDER BY route_name ASC";
	$res_check_division = mysql_query($sql_check_division);
	while($row_check_division = mysql_fetch_array($res_check_division)){
		$division = $row_check_division['route_name'];
		echo "<option value=\"".$division."\">".$division."</option>";
	}
	echo "<option value=\"Other\">Other</option>";*/
	?>
    </select--><div id="division_select_div"></div></td>
  </tr>
   <tr class="TDHEAD_SUB" id="otherdivision" style="display:none">
  	<td>Other</td>
    <td><input name="other_division" id="other_division" type="text" /></td>
  </tr>
<tr class="TDHEAD_SUB">
  	<td>CCC</td>
    <td><!--select id="ccc" name="ccc" onChange="javascript:show_others('ccc',this.value);"><option value='' selected>Select</option>
    <?php
	/*$sql_check_ccc="SELECT  DISTINCT customer_name FROM customer_master ORDER BY customer_name ASC";
	$res_check_ccc = mysql_query($sql_check_ccc);
	while($row_check_ccc = mysql_fetch_array($res_check_ccc)){
		$customer_name = $row_check_ccc['customer_name'];
		echo "<option value=\"".$customer_name."\">".$customer_name."</option>";
	}
	echo "<option value=\"Other\">Other</option>";*/
	?>
    </select--><div id="CCC_select_div"></div></td>
  </tr>
  <tr class="TDHEAD_SUB" id="otherccc" style="display:none">
  	<td>Other</td>
    <td><input name="other_ccc" id="other_ccc" type="text" /></td>
  </tr>

    <tr class="TDHEAD_SUB">
        <td>KIOSK Id</td>
        <td><!--select id="kiosk_id" name="kiosk_id" onChange="javascript:show_others('kiosk_id',this.value);"><option value='' selected>Select</option>
        <?php
       /* $sql_check_kiosk="SELECT  DISTINCT dns_customer_code FROM customer_master ORDER BY dns_customer_code ASC";
        $res_check_kiosk = mysql_query($sql_check_kiosk);
        while($row_check_kiosk = mysql_fetch_array($res_check_kiosk)){
            $customer_code = $row_check_kiosk['dns_customer_code'];
            echo "<option value=\"".$customer_code."\">".$customer_code."</option>";
        }*/
		echo "<option value=\"Other\">Other</option>";
        ?>
        </select--><div id="kiosk_select_div"></div></td>
      </tr>
   <tr class="TDHEAD_SUB" id="otherkiosk" style="display:none">
  	<td>Other</td>
    <td><input name="other_kiosk" id="other_kiosk" type="text" /></td>
  </tr>
  
  <?php if(employeewise_hierarchy == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td>Reporting To</td>
    <td><select id="reporting_to"><option value="" selected>Select</option>
    <?php
	$sql_get_emp = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
	$res_get_emp = mysql_query($sql_get_emp);
	while($row_get_emp = mysql_fetch_array($res_get_emp)){
		$emp_code = $row_get_emp['emp_code'];
		$emp_name = $row_get_emp['emp_name'];
		echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
	}
	?>
    </select>
    </td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td colspan="2" align="right">
    <input name="submit" type="button" id="submit" value="Submit" onClick="submit_data();" />
    <input name="cancel" type="button" id="cancel" value="Cancel" onClick="window.location.href='kiosk_cash_cheque_report.php?type=Cheque%20Deposit'" />
    </td>
  </tr>
</table>
</center>
</body>
<script>
function show_others(val,dataval)
{
	if(val=='designation')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherdesignation").style.display='';
		}
		else
		{
			document.getElementById("otherdesignation").style.display='none';
			document.getElementById("other_designation").value='';
		}
	}
	if(val=='zone')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherzone").style.display='';
		}
		else
		{
			document.getElementById("otherzone").style.display='none';
			document.getElementById("other_zone").value='';
		}
	}
	if(val=='region')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherregion").style.display='';
		}
		else
		{
			document.getElementById("otherregion").style.display='none';
			document.getElementById("other_region").value='';
		}
	}
	if(val=='division')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherdivision").style.display='';
		}
		else
		{
			document.getElementById("otherdivision").style.display='none';
			document.getElementById("other_division").value='';
		}
	}
	if(val=='ccc')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherccc").style.display='';
		}
		else
		{
			document.getElementById("otherccc").style.display='none';
			document.getElementById("other_ccc").value='';
		}
	}
	if(val=='kiosk_id')
	{
		if(dataval=='Other')
		{
			document.getElementById("otherkiosk").style.display='';
		}
		else
		{
			document.getElementById("otherkiosk").style.display='none';
			document.getElementById("other_kiosk").value='';
		}
	}
}
function submit_data(){
	var emp_id = document.getElementById("emp_id").value;
	if(document.getElementById("emp_id").value.search(/\S/) == -1){
		alert('Provide employee id');
		document.getElementById("emp_id").focus();
		return false;
	}
	var emp_name = document.getElementById("emp_name").value;
	if(document.getElementById("emp_name").value.search(/\S/) == -1){
		alert('Provide employee name');
		document.getElementById("emp_name").focus();
		return false;
	}
	var designation = document.getElementById("designation").value;
	if(document.getElementById("designation").value.search(/\S/) == -1){
		alert('Provide designation');
		document.getElementById("designation").focus();
		return false;
	}
	var other_designation=document.getElementById("other_designation").value;
	if(document.getElementById("designation").value=='Other')
	{
		if(document.getElementById("other_designation").value.search(/\S/) == -1)
		{
			alert('Provide Other designation');
			document.getElementById("other_designation").focus();
			return false;
		}
	}
	var mobile=document.getElementById("mobile").value;
	if (isNaN(mobile)){
		alert("Mobile number should be numeric");
		document.getElementById("mobile").focus();
		return false;
  	}
	var zone = document.getElementById("zone").value;
	if(document.getElementById("zone").value.search(/\S/) == -1){
		alert('Provide zone');
		document.getElementById("zone").focus();
		return false;
	}
	var other_zone=document.getElementById("other_zone").value;
	if(document.getElementById("zone").value=='Other')
	{
		if(document.getElementById("other_zone").value.search(/\S/) == -1)
		{
			alert('Provide Other zone');
			document.getElementById("other_zone").focus();
			return false;
		}
	}

	var region=document.getElementById("region").value;
	if(document.getElementById("region").value.search(/\S/) == -1){
		alert('Provide region');
		document.getElementById("region").focus();
		return false;
	}
	var other_region=document.getElementById("other_region").value;
	if(document.getElementById("region").value=='Other')
	{
		if(document.getElementById("other_region").value.search(/\S/) == -1)
		{
			alert('Provide Other region');
			document.getElementById("other_region").focus();
			return false;
		}
	}

	var division=document.getElementById("division").value;
	if(document.getElementById("division").value.search(/\S/) == -1){
		alert('Provide division');
		document.getElementById("division").focus();
		return false;
	}
	var other_division=document.getElementById("other_division").value;
	if(document.getElementById("division").value=='Other')
	{
		if(document.getElementById("other_division").value.search(/\S/) == -1)
		{
			alert('Provide Other division');
			document.getElementById("other_division").focus();
			return false;
		}
	}
	var ccc=document.getElementById("ccc").value;
	/*if(document.getElementById("ccc").value.search(/\S/) == -1){
		alert('Provide ccc');
		document.getElementById("ccc").focus();
		return false;
	}*/
	var other_ccc=document.getElementById("other_ccc").value;
	if(document.getElementById("ccc").value=='Other')
	{
		if(document.getElementById("other_ccc").value.search(/\S/) == -1)
		{
			alert('Provide Other ccc');
			document.getElementById("other_ccc").focus();
			return false;
		}
	}

	var kiosk_id=document.getElementById("kiosk_id").value;
	/*if(document.getElementById("kiosk_id").value.search(/\S/) == -1){
		alert('Provide kiosk id');
		document.getElementById("kiosk_id").focus();
		return false;
	}*/
	var other_kiosk=document.getElementById("other_kiosk").value;
	if(document.getElementById("kiosk_id").value=='Other')
	{
		if(document.getElementById("other_kiosk").value.search(/\S/) == -1)
		{
			alert('Provide Other kiosk');
			document.getElementById("other_kiosk").focus();
			return false;
		}
	}
	var reporting_to=document.getElementById("reporting_to").value;
	$.post("kiosk_details_insert.php",
    {
		emp_id: emp_id,
		emp_name: emp_name,
        designation: designation,
		other_designation: other_designation,
		mobile: mobile,
		zone: zone,
		other_zone: other_zone,
		region: region,
		other_region: other_region,
		division: division,
		other_division: other_division,
		ccc: ccc,
		other_ccc: other_ccc,
		kiosk_id: kiosk_id,
		other_kiosk: other_kiosk,
		<?php if(employeewise_hierarchy == 'yes'){ ?>
		reporting_to: reporting_to,
		<?php } ?>
    },
    function(data, status){
        alert(data);
		window.open('http://acedns.in/acednsproduct/misreport/kiosk_entry_form.php','_self');
    });
}

function check_empid_exist(emp_id){
	$.post("check_emp_id.php",
    {
		emp_id: emp_id
    },
    function(data, status){
		if(data != 'success'){
        	alert(data);
			document.getElementById("emp_id").value = '';
		}
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
	function zone_region(){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone=document.getElementById("zone").value;	
		zone = encodeURIComponent(zone);
		document.getElementById("region_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_region_data_zonewise.php?zone='+zone,'region_select_div',0);
	}
	function region_division(){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("region").value.search(/\S/) == -1)
			return false;
		var zone=document.getElementById("zone").value;	
		var region=document.getElementById("region").value;	
		document.getElementById("division_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_division_data_regionwise.php?zone='+zone+'&region='+region,'division_select_div',0);
	}
	function division_ccc(){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("region"))
		{
			if(document.getElementById("region").value.search(/\S/) == -1)
			return false;
		}
		if(document.getElementById("division"))
		{	
			if(document.getElementById("division").value.search(/\S/) == -1)
			return false;
		}

		var zone=document.getElementById("zone").value;
		if(document.getElementById("region"))
		{
			var region=document.getElementById("region").value;
		}
		if(document.getElementById("division"))
		{
			var division=document.getElementById("division").value;
		}

		//alert(region);
		document.getElementById("CCC_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_CCC_data_divisionwise.php?zone='+zone+'&division='+division+'&region='+region,'CCC_select_div',0);
		document.getElementById("kiosk_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_kiosk_related_data.php?zone='+zone+'&division='+division+'&region='+region,'kiosk_select_div',0);
	}
	function ccc_kiosk(){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("region"))
		{
			if(document.getElementById("region").value.search(/\S/) == -1)
			return false;
		}
		if(document.getElementById("division"))
		{	
			if(document.getElementById("division").value.search(/\S/) == -1)
			return false;
		}
		if(document.getElementById("ccc").value.search(/\S/) == -1)
			return false;

		var zone=document.getElementById("zone").value;
		if(document.getElementById("region"))
		{
			var region=document.getElementById("region").value;
		}
		if(document.getElementById("division"))
		{
			var division=document.getElementById("division").value;
		}
		var ccc=document.getElementById("ccc").value;

		//alert(region);
		document.getElementById("kiosk_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_kiosk_related_data.php?zone='+zone+'&division='+division+'&region='+region+'&ccc='+ccc,'kiosk_select_div',0);
	}
</script>
<?php
}
?>