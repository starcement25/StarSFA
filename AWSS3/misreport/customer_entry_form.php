<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
	$sql_check_branchdata = "SELECT * FROM branch_master ORDER BY branch_name ASC";
	$res_check_branchdata = mysql_query($sql_check_branchdata);
	$total_rows = mysql_fetch_array($res_check_branchdata);
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
  	<td colspan="2" align="center">Customer Details</td>
  </tr><br />
  <?php if(providing_code == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">DNS Code</td>
    <td><input name="dns_code" id="dns_code" type="text"  /></td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Customer Name</td>
    <td><input name="customer_name" id="customer_name" type="text"  /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Phone Number</td>
    <td><input name="phn" id="phn" type="text" maxlength="10" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Address</td>
    <td><textarea name="address" id="address" cols="30" rows="4"></textarea></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Pin</td>
    <td><input name="pin" id="pin" type="text" /></td>
  </tr>
  <?php if($total_rows>0){?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Branch Name</td>
    <td><select id="branch" onChange="get_emp_branch(this.value);">
    	<option value="">Select</option>
    <?php
		$res_check_branchdata = mysql_query($sql_check_branchdata);
		while($row_check_branchdata = mysql_fetch_array($res_check_branchdata)){
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
    <td><select id="emp_code">
    	<option value="">Select</option>
	</select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Route</td>
    <td><select id="route">
    	<option value="">Select</option>
    </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Current Balance</td>
    <td><input name="current_balance" id="current_balance" type="text" /></td>
  </tr>
  <?php if(credit_limit == 'yes'){?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Credit Limit</td>
    <td><input name="credit_limit" id="credit_limit" type="text" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Credit Days</td>
    <td><input name="credit_days" id="credit_days" type="text" /></td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">ACEdns</td>
    <td><select id="acedns"><option value="Y">Yes</option><option value="N">No</option></select></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Blacklist</td>
    <td><select id="blacklist"><option value="Y">Yes</option><option value="N">No</option></select></td>
  </tr>
  <?php if(TD_type == 'customer vertical wise'){?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Trade Discount</td>
    <td><input name="td" id="td" type="text" /></td>
  </tr>
  <?php } ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Customer Type</td>
    <td><select id="customer_type" onChange="check_rdstag(this.value)"><option value="D">Distributor</option><option value="R">Retailer</option></select></td>
  </tr>
  <tr class="TDHEAD_SUB" id="rds_tag_row" hidden>
  	<td align="right">RDS Tag</td>
    <td><select id="rds_tag" >
    	<?php
		$sql_customer = "SELECT customer_code, customer_name FROM customer_master ORDER BY customer_name ASC";
		$res_customer = mysql_query($sql_customer);
		while($row_customer = mysql_fetch_array($res_customer)){
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			echo "<option value=\"".$customer_code."\">".$customer_name."</option>";
		}
		?>
    	</select>
    </td>
  </tr>
  <?php if(sauda_allocation == 'yes'){ ?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Sauda Validity Period</td>
    <td><input name="sauda_valid_period" id="sauda_valid_period" type="text" /></td>
  </tr>
  <?php } ?>
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
		alert('Provide dns code');
		return false;
	}
	<?php } ?>
	var customer_name = document.getElementById("customer_name").value;
	if(document.getElementById("customer_name").value.search(/\S/) == -1){
		alert('Provide customer name');
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
	
	var address = document.getElementById("address").value;
	if(document.getElementById("address").value.search(/\S/) == -1){
		alert('Provide Address');
		return false;
	}
	
	var pin = document.getElementById("pin").value;
	if(document.getElementById("pin").value.search(/\S/) == -1){
		alert('Provide pin');
		return false;
	}
	
	<?php if($total_rows>0){?>
	//alert(branch_code)
	var branch = document.getElementById("branch").value;
	if(document.getElementById("branch").value.search(/\S/) == -1){
		alert('Provide branch');
		return false;
	}
	<?php } ?>
	
	var emp_code = document.getElementById("emp_code").value;
	if(document.getElementById("emp_code").value.search(/\S/) == -1){
		alert('Provide Employee');
		return false;
	}
	
	var route = document.getElementById("route").value;
	if(document.getElementById("route").value.search(/\S/) == -1){
		alert('Provide Route');
		return false;
	}
	
	var current_balance = document.getElementById("current_balance").value;
	
	<?php if(credit_limit == 'yes'){?>
	var credit_limit = document.getElementById("credit_limit").value;
	if(document.getElementById("credit_limit").value.search(/\S/) == -1){
		alert('Provide credit limit');
		return false;
	}
	
	var credit_days = document.getElementById("credit_days").value;
	if(document.getElementById("credit_days").value.search(/\S/) == -1){
		alert('Provide credit days');
		return false;
	}
	<?php } ?>
	
	var acedns = document.getElementById("acedns").value;
	var blacklist = document.getElementById("blacklist").value;
	
	<?php if(TD_type == 'customer vertical wise'){?>
	var td = document.getElementById("td").value;
	if(document.getElementById("td").value.search(/\S/) == -1){
		alert('Provide trade discount');
		return false;
	}
	<?php } ?>
	
	var customer_type = document.getElementById("customer_type").value;
	if(customer_type == 'R')
		var rds_tag = document.getElementById("rds_tag").value;
	else
		var rds_tag = '';
	
	<?php if(sauda_allocation == 'yes'){ ?>
	var sauda_valid_period = document.getElementById("sauda_valid_period").value;
	<?php } ?>
	
	$.post("customer_details_insert.php",
    {
		<?php if(providing_code == 'yes'){ ?>
		dns_code: dns_code,
		<?php } ?>
		customer_name: customer_name,
        phn: phn,
		address: address,
		pin: pin,
		emp_code: emp_code,
		route: route,
		<?php if($total_rows>0){ ?>
		branch: branch,
		<?php } ?>
		current_balance: current_balance,
		<?php if(credit_limit == 'yes'){?>
		credit_limit: credit_limit,
		credit_days: credit_days,
		<?php } ?>
		acedns: acedns,
		blacklist: blacklist,
		<?php if(TD_type == 'customer vertical wise'){?>
		td: td,
		<?php } ?>
		customer_type: customer_type,
		rds_tag: rds_tag,
		<?php if(sauda_allocation == 'yes'){ ?>
		sauda_valid_period: sauda_valid_period,
		<?php } ?>
		mode: 'insert'
    },
    function(data, status){
        alert(data);
		//window.open('http://acedns.in/acednsproduct/misreport/customer_entry_form.php','_self');
    });
}

function check_rdstag(cust_type){
	if(cust_type == 'D')
		document.getElementById("rds_tag_row").hidden = true;
	else
		document.getElementById("rds_tag_row").hidden = false;
}

function get_emp_branch(branch_code){
	//alert('hi');
	GenericAjaxFunction('customer_entry_getemp.php?branch_code='+branch_code,'emp_code',0);
	GenericAjaxFunction('customer_entry_getroute.php?branch_code='+branch_code,'route',0);
}
</script>
<?php
}
?>