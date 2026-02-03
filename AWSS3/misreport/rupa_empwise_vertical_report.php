<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
?>
<script src="dist/js/jquery.js"></script>
<script src="dist/js/hoverIntent.js"></script>
<script src="dist/js/superfish.js"></script>
<script language="JavaScript" src="calendar3.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script type="text/javascript" src="jquery.highlight.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
<!-- polyfiller file to detect and load polyfills -->
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
  webshims.setOptions('waitReady', false);
  webshims.setOptions('forms-ext', {types: 'date'});
  webshims.polyfill('forms forms-ext');
</script>
<?php
function main()
{
	$vertical_name_array=array();
	if(strtoupper($_SESSION['admin_login']) == 'ADMIN'){
		$sql_attendance_verticalwise = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', -1) as distinct_vertical_value FROM employee_master 
		EM WHERE SUBSTRING_INDEX( EM.vertical_value, ',', -1 ) != ''";
		$res_attendance_verticalwise = mysql_query($sql_attendance_verticalwise);
		$total_rows = mysql_num_rows($res_attendance_verticalwise);
		if($total_rows>0){
			$res_attendance_verticalwise = mysql_query($sql_attendance_verticalwise);
			while($row_attendance_verticalwise = mysql_fetch_array($res_attendance_verticalwise)){
				$dist_vert_value = trim($row_attendance_verticalwise['distinct_vertical_value']);
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($dist_vert_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				
				if($dist_vert_value != ''){
					if(!in_array($dist_vert_value,$vertical_name_array))
						array_push($vertical_name_array,$dist_vert_value);
				}
			}
		}
		
	$reporting_to='';
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_master_cond = ' 1 ';
	}
	else{
		$sql_get_vertical = "SELECT vertical_value FROM employee_master WHERE emp_code = '".$_SESSION['admin_login']."'";
		$res_get_vertical = mysql_query($sql_get_vertical);
		$row_get_vertical = mysql_fetch_array($res_get_vertical);
		$get_vertical = $row_get_vertical['vertical_value'];
		if(strpos($get_vertical,',') != false){
			$get_vertical_array = explode(",",$get_vertical);
			//print_r($get_vertical_array);
			foreach($get_vertical_array as $get_vertical_value){
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($get_vertical_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				else{
					$dist_vert_value = $get_vertical_value;
				}
				
				if(!in_array($dist_vert_value,$vertical_name_array))
				array_push($vertical_name_array,$dist_vert_value);
			}
		}
		else{
			array_push($vertical_name_array,$get_vertical);
		}
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND SUBSTRING(OH.order_no,2,5) IN('.$emp_hierarchy.')';
	$reporting_to=$_SESSION['admin_login'];
	$emp_master_cond = " emp_code IN(".$emp_hierarchy.") ";
	}
	?>
    <center>
    <div id="display" style="max-height: 350px; width:100%; overflow-y: scroll;" align="center"></div>
    <br />
    <table class="border" width="54%" style="border-collapse:collapse;" border="1">
      <tr class="TDHEAD">
      	<td colspan="2" align="center">Select Criteria</td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td align="right">Select Vertical</td>
        <td align="left">
        <select id="select_vertical" >
          <option value="">Select</option>
          <?php
		  foreach($vertical_name_array as $vertical_val){
			  echo "<option>$vertical_val</option>";
		  }
          ?>
        </select>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
        <td align="right">Employee</td>
        <td align="left">
        <select id="emp" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
			$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE ".$emp_master_cond." ORDER BY emp_name ASC";
			$res_emp_code = mysql_query($sql_emp_code);
			while($row_emp_code = mysql_fetch_array($res_emp_code)){
				$emp_code = $row_emp_code['emp_code'];
				$emp_name = $row_emp_code['emp_name'];
				echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
			}
		?>
        </select>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td align="right">Select Date</td>
        <td>
        <div id="date_div" >
            From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
            To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
		</div>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td colspan="2" align="center">
        <input type="submit" name="submit" value="Submit" onClick="show_data();" />
        </td>
      </tr>
    </table>    
    </center>
<script>
function show_data(){
	
	if(document.getElementById("select_vertical").value.search(/\S/) == -1){
		alert('Please select vertical');
		return false;
	}
	
	if(document.getElementById("emp").value.search(/\S/) == -1){
		alert('Please select employee');
		return false;
	}
	
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	if(start_date>end_date)
	{
		alert("Start date cannot be greater than end date");
		return false;
	}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
	{
		alert("Start date/End date cannot be empty");
		return false;
	}
	
	var emp_code = document.getElementById("emp").value;
	var vertical = document.getElementById("select_vertical").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('rupa_empwise_vertical_data.php?emp_code='+emp_code+'&vertical='+vertical+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	
}
</script>
    <?php
}
?>