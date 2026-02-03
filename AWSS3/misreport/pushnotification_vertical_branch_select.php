<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

disphtml("main();");
	
function main()
{
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
    <?php
	$sql_branch = "SELECT branch_code, branch_name FROM branch_master ORDER BY branch_name ASC";
	$res_branch = mysql_query($sql_branch);
	$total_rows = mysql_num_rows($res_branch);
	
	$vertical_value_array = array();
	$sql_distinct_vertical = "SELECT DISTINCT vertical_value as vertical_value FROM employee_master WHERE vertical_value != ''";
	$res_distinct_vertical = mysql_query($sql_distinct_vertical);
	while($row_distinct_vertical = mysql_fetch_array($res_distinct_vertical)){
		$distinct_vertical_value = $row_distinct_vertical['vertical_value'];
		if(strpos($distinct_vertical_value,',') == true){
			$vertical_explode = explode(",",$distinct_vertical_value);
			foreach($vertical_explode as $val){
				if(!in_array($val,$vertical_value_array)){
					array_push($vertical_value_array,$val);
				}
			}
		}
		else{
			if(!in_array($distinct_vertical_value,$vertical_value_array)){
				array_push($vertical_value_array,$distinct_vertical_value);
			}
		}
	}
	sort($vertical_value_array);
	if(vertical_fields == 'yes' && $total_rows>0){
		echo "<table border=\"1\" style=\"border-collapse:collapse;\" width=\"40%\" cellpadding=\"8\">
			  <tr class=\"TDHEAD\" align=\"center\">
			  	<td colspan=\"2\">Push Notification</td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
				<td>Vertical</td>
				<td><select name='vertical' id='vertical' onchange='get_branch(this.value);'><option value=''>Select</option>";
				foreach($vertical_value_array as $value){
					echo "<option>".$value."</option>";
				}
				echo "</select></td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
				<td>Branch</td>
				<td><div name='branch' id='branch' style=\"max-height:150px; overflow-y: scroll;\"></div></td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
			  	<td></td>
				<td align='left'><input name=\"submit\" type=\"button\" value=\"Submit\" id=\"submit\" onclick=\"show_emp();\" /></td>
			  </tr>
			  </table>";
	}
	else if(vertical_fields == 'yes'){
		echo "<table border=\"1\" style=\"border-collapse:collapse;\" width=\"30%\" cellpadding=\"8\">
			  <tr class=\"TDHEAD\" align=\"center\">
			  	<td colspan=\"2\">Push Notification</td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
				<td>Vertical</td>
				<td>
				<select name='vertical' id='vertical'><option>Select</option>";
				foreach($vertical_value_array as $value){
					echo "<option>$value</option>";
				}
				echo "</select></td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
			  	<td></td>
				<td align='left'><input name=\"submit\" type=\"button\" value=\"Submit\" id=\"submit\" onclick=show_emp_one(); /></td>
			  </tr>
			  </table>";
	}
	else if($total_rows>0){
		echo "<table border=\"1\" style=\"border-collapse:collapse;\" width=\"30%\" cellpadding=\"8\">
			  <tr class=\"TDHEAD\" align=\"center\">
			  	<td colspan=\"2\">Push Notification</td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
				<td>Branch</td>
				<td><select name='branch' id='branch'><option value=''>Select</option>";
				$res_branch = mysql_query($sql_branch);
				while($row_branch = mysql_fetch_array($res_branch)){
					$br_code = $row_branch['branch_code'];
					$br_name = $row_branch['branch_name'];
					echo "<option value='$br_code'>$br_name</option>";
				}
				echo "</select></td>
			  </tr>
			  <tr class=\"TDHEAD_SUB\">
			  	<td></td>
				<td align='left'><input name=\"submit\" type=\"button\" value=\"Submit\" id=\"submit\" onclick=\"show_emp_two();\" /></td>
			  </tr>
			  </table>";
	}    
}
?>
</center>
</body>
<script>
function get_branch(vertical_value){
	//alert(vertical_value);
	document.getElementById("branch").style.background = 'white';
	document.getElementById("branch").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('push_notification_get_branch.php?vertical_value='+vertical_value,'branch',0);
}
function show_emp(){
	var vertical_name = document.getElementById("vertical").value;
	var branch_code = document.getElementById("branch").value;
	
	var branchArrayinput = new Array;
	$('.input_chk:checked').each(function() {
        branchArrayinput.push($(this).val());
    });
	
	if(document.getElementById("vertical").value.search(/\S/) == -1){
		alert('Select vertical');
		return false;
	}
	if(branchArrayinput.length == 0){
		alert('Provide branch');
		return false;
	}
	/*if(document.getElementById("branch").value.search(/\S/) == -1){
		alert('Select branch');
		return false;
	}*/
	//alert(JSON.stringify(branchArrayinput));
	window.open('adminPushNotification.php?vertical_name='+vertical_name+'&branch_code='+branchArrayinput,'_self');
	//alert(vertical_name+branch_code);
}

function show_emp_one(){
	var vertical_name = document.getElementById("vertical").value;
	if(document.getElementById("vertical").value.search(/\S/) == -1){
		alert('Select vertical');
		return false;
	}
	window.open('adminPushNotification.php?vertical_name='+vertical_name,'_self');
}

function show_emp_two(){
	var branch_code = document.getElementById("branch").value;
	if(document.getElementById("branch").value.search(/\S/) == -1){
		alert('Select branch');
		return false;
	}
	window.open('adminPushNotification.php?branch_code='+branch_code,'_self');
}
</script>