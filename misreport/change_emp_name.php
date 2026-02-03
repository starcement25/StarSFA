<?php

//ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

require("include/config.php");
	require("include/config-setup.php");
	
	disphtml("main();");	
	function main(){
		require("include/dbcon.php");
		$emp_code = $_GET['emp_code'];
		$sql_emp_name = "SELECT emp_name, reporting_to, email, phone_no,dns_emp_code,DOJ,district,designation,vertical_value FROM employee_master WHERE emp_code = '".$emp_code."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name = $row_emp_name['emp_name'];
		$reporting_to = $row_emp_name['reporting_to'];
		$email = $row_emp_name['email'];
		$phone_no = $row_emp_name['phone_no'];
		$dns_emp_code = $row_emp_name['dns_emp_code'];
		$DOJ_db = $row_emp_name['DOJ'];
		$district = $row_emp_name['district'];
		$designation = $row_emp_name['designation'];
		$vertical_value = $row_emp_name['vertical_value'];
				
		$emp_string = '';
		$emp_code_string = '';
		if($reporting_to == ''){
			$emp_string = '';
			$emp_code_string = '';
		}
		else{
			$emp_array = array();
			if(strpos($reporting_to,",") != TRUE){
				array_push($emp_array,$reporting_to);
			}
			else{
				$emp_array = explode(",",$reporting_to);
			}
			
			foreach($emp_array as $emp_val){
				$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_val."'";
				$res_emp_name = mysqli_query($link,$sql_emp_name);
				while($row_emp_name = mysqli_fetch_assoc($res_emp_name)){
					$emp_string .= $row_emp_name['emp_name'].",";
					$emp_code_string .= $emp_val.",";
				}
			}
			$emp_string = rtrim($emp_string,",");
			$emp_code_string = rtrim($emp_code_string,",");
		}
		
		$multi_select = "<select name=\"multiempselect[]\" multiple>";

		$sql_emp_list = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_name ASC";
		$res_emp_list = mysqli_query($link,$sql_emp_list);
		while($row_emp_list = mysqli_fetch_assoc($res_emp_list)){
			$list_emp_code = $row_emp_list['emp_code'];
			$list_emp_name = $row_emp_list['emp_name'];
			
			if(in_array($list_emp_code,$emp_array))
				$selected = ' selected';
			else
				$selected = '';
			
			$multi_select .= "<option value=\"".$list_emp_code."\" $selected>".$list_emp_name."</option>";
		}
		$multi_select .= "</select>";
		
		
		
		if($_POST['submit'] == 'Submit'){
			if($_POST['chng_emp_name'] == '')
				$emp_name = $_POST['emp_name'];
			else if($_POST['chng_emp_name'] != '')
				$emp_name = $_POST['chng_emp_name'];
			if($_POST['chng_dns_emp_code'] == '')
				$dns_emp_code = $_POST['dns_emp_code'];
			else if($_POST['chng_dns_emp_code'] != '')
				$dns_emp_code = $_POST['chng_dns_emp_code'];	
				
			$emp_code = $_POST['emp_code'];
			$reporting_to = $_POST['reporting_to'];
			$email = $_POST['email'];
			$phone_no = $_POST['phone_no'];
			$DOJ = $_POST['start_date'];
			$district = $_POST['district'];
			$designation = $_POST['designation'];
			$vertical = $_POST['vertical'];
			/*if(strpos($DOJ,'/')!=false){
			 $DOJArr=explode('/',$DOJ);
			}
			if(strpos($DOJ,'-')!=false){
			 $DOJArr=explode('-',$DOJ);
			}
			if(strlen($DOJArr[2])==2)
			{
				$year='20'.$DOJArr[2];
			}
			else
			{
				$year=$DOJArr[2];
			}
			$DOJ=$year.'-'.$DOJArr[0].'-'.$DOJArr[1];*/
			$multiempselect = $_POST['multiempselect'];
			
			foreach($multiempselect as $multiempval){
				$multiemp_string .= $multiempval.",";
			}
			$multiemp_string = rtrim($multiemp_string,",");
			
			if($DOJ!='')
			{
				$DOJ_condition=",DOJ = '".$DOJ."'";
			}
			else
			{
				$DOJ_condition='';
			}
						
			$sql_update_emp_name = "UPDATE employee_master SET dns_emp_code='".$dns_emp_code."',emp_name = '".$emp_name."', reporting_to = '".$multiemp_string."', 
								email = '".$email."', phone_no = '".$phone_no."'$DOJ_condition,district = '".$district."',designation = '".$designation."',vertical_value = '".$vertical."',download_time=CURRENT_TIMESTAMP() 
			WHERE emp_code = '".$emp_code."'";
			$res_update_emp_name = mysqli_query($link,$sql_update_emp_name);
			if(modified_customer_emp_route=='yes')
			{
				$sqlupdate="UPDATE customer_route_emp_relation SET download_time=CURRENT_TIMESTAMP() WHERE emp_code = '".$emp_code."'";
				mysqli_query($link,$sqlupdate);
			}
			else
			{
				$sqlupdate="UPDATE customer_master SET download_time=CURRENT_TIMESTAMP() WHERE emp_code = '".$emp_code."'";
				mysqli_query($link,$sqlupdate);
			}
			echo "<center><font color='green'><strong>Emp info successfully updated</strong></font></center>";
		}
?>
<script>
function validate(){
	if(document.getElementById("chng_emp_name").value.search(/\S/) == -1){
		//alert('Please Provide Changed Employee Name');
		document.getElementById("chng_emp_name").value = '';
		//return false;
	}
	
	/*var x = document.getElementById("email").value;
	var atpos = x.indexOf("@");
	var dotpos = x.lastIndexOf(".");
	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
		alert("Not a valid e-mail address");
		return false;
	}*/
	
	if(document.getElementById("phone_no").value.search(/\S/) == -1){
		alert('Please Provide Phone Number');
		return false;
	}
	
	var phone_no = document.getElementById("phone_no").value;
	
	if (isNaN(phone_no)) 
  	{
		alert("Phone number should be numeric");
		return false;
  	}
}
function emplist_populate(){
	document.getElementById("emplist_div").hidden = false;
}
</script>
<center>
<br /><br />
<form method="POST" action="" onsubmit="return validate();">
<table class="border" width="40%" border="1" cellpadding="4" style="border-collapse:collapse;">
  <tr>
  	<td colspan="2" class="TDHEAD" align="center">Edit Employee Details</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee Name:</td>
    <td align="left"><input type="text" name="emp_name" id="emp_name" value="<?php echo $emp_name; ?>" readonly /><input type="hidden" name="emp_code" value="<?php echo $emp_code; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Change Employee Name:</td>
    <td align="left"><input type="text" name="chng_emp_name" id="chng_emp_name" /></td>
  </tr>
  <?php 
  if(providing_code=='yes'){?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee Code:</td>
    <td align="left"><input type="text" name="dns_emp_code" id="dns_emp_code" value="<?php echo $dns_emp_code; ?>" readonly />
   </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Change Employee Code:</td>
    <td align="left"><input type="text" name="chng_dns_emp_code" id="chng_dns_emp_code" /></td>
  </tr>
  <?php }?>
  <tr class="TDHEAD_SUB">
  	<td align="right">Reporting To:</td>
    <td align="left"><input type="text" name="reporting_to" id="reporting_to" value="<?php echo $emp_string; ?>" readonly  /><span style="background:#FFF; color:#666; width:40px; height:18px; cursor:pointer;" onclick="emplist_populate();">EDIT</span>
    <br /><br />
    <div id="emplist_div" hidden>
    <?php
		echo $multi_select;
	?>
    </div>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Email:</td>
    <td align="left"><input type="text" name="email" id="email" value="<?php echo $email; ?>"  /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Phone No:</td>
    <td align="left"><input type="text" name="phone_no" id="phone_no" value="<?php echo $phone_no; ?>"  /></td>
  </tr>
   <tr class="TDHEAD_SUB">
  	<td align="right">DOJ:</td>
    <td align="left"><input type="date" name="start_date" id="start_date" value="<?php echo $DOJ_db; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">District:</td>
    <td align="left"><input type="text" name="district" id="district" value="<?php echo $district; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Designation:</td>
    <td align="left"><input type="text" name="designation" id="designation" value="<?php echo $designation; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Vertical:</td>
    <td align="left"><input type="text" name="vertical" id="vertical" value="<?php echo $vertical_value; ?>" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input type="submit" name="submit" value="Submit" /></td>
  </tr>
</table>
</form>
<?php if(vertical_fields == 'no' && $_SESSION['nick_name']!='STAR'){ ?>
<a href="adminEmployeeAccess.php" style="color:#0033FF; font-weight:bold;">Back</a>
<? } else if($_SESSION['nick_name']=='STAR'){?>
<a href="adminEmployeeAccess_selectionwise.php" style="color:#0033FF; font-weight:bold;">Back</a>
<?php }else {?>
<a href="employee_access_verticalwise.php" style="color:#0033FF; font-weight:bold;">Back</a>
<?php } ?>
</center>
<?php
}
?>