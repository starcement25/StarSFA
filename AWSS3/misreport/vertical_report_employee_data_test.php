<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = date('Y-m-d');

$_GET['start_date'] = '2017-05-01';
$_GET['end_date'] = '2017-05-13';

$_GET['vertical_name'] = 'EURO';

/*---------------------------------> ADMIN/Employee hierarchy condition <--------------------------------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition="";
	$emp_hierarchy_condition_one="";
	$emp_hierarchy_order_condition = "";
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=" AND EM.emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_order_condition = " AND SUBSTRING(OH.order_no,-19,5) IN (".$emp_hierarchy.") ";
	
}

/*--------------------> Condition to display data according to vertical name/all data<--------------------*/
if($_GET['vertical_name'])
{
	$table_columnname = 'Attendance';
	if($_GET['vertical_name'] == 'MACROMAN')
	{
		$vertical_condition = " AND SUBSTRING_INDEX(EM.vertical_value, ',', -1) LIKE 'M%' ";
		$vertical_condition_one = " AND SUBSTRING_INDEX(OH.vertical_value, ',', 1) LIKE 'M%' ";
	}
	else
	{
		$vertical_condition = " AND SUBSTRING_INDEX(EM.vertical_value, ',', -1)='".$_GET['vertical_name']."' ";
		$vertical_condition_one = " AND SUBSTRING_INDEX(OH.vertical_value, ',', 1)='".$_GET['vertical_name']."' ";
	}
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
	if($_GET['start_date'] != '' && $_GET['end_date'] != '')
	{
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		$table_columnname = 'No of days present';
		$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
		$count_transid_condition = " ,COUNT(LO.trans_id) ";
		$group_by = " GROUP BY EM.emp_code ";
		$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
	}
}
else if($_GET['start_date'] != '' && $_GET['end_date'] != '')
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$table_columnname = 'No of days present';
	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$count_transid_condition = " ,COUNT(LO.trans_id) ";
	$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
	$group_by = " GROUP BY EM.emp_code ";
}
else
{
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
}
$emp_code_array = array();
$vertical_array = array();
$macroman_flag = 1;

$distinct_vertical_array = array();
/*---------------------------------> Condition to select DISTINCT vertical <--------------------------------*/
/*$sql_attendance_verticalwise = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', 1) as distinct_vertical_value FROM employee_master EM, location LO WHERE EM.emp_code = LO.emp_code ".$condition.$vertical_condition.$emp_hierarchy_condition." UNION DISTINCT SELECT DISTINCT OH.vertical_value as distinct_vertical_value FROM order_header OH, location LO WHERE LO.trans_id=OH.order_no ".$condition.$vertical_condition_one.$emp_hierarchy_order_condition." ORDER BY distinct_vertical_value ASC";
$res_attendance_verticalwise = mysql_query($sql_attendance_verticalwise);
$total_rows = mysql_num_rows($res_attendance_verticalwise);*/

$sql_distinct_vertical_EM = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',',-1) as distinct_vertical_value FROM employee_master EM, location LO WHERE EM.emp_code = LO.emp_code  ".$condition.$vertical_condition.$emp_hierarchy_condition;
$res_distinct_vertical_EM = mysql_query($sql_distinct_vertical_EM);
while($row_distinct_vertical_EM = mysql_fetch_array($res_distinct_vertical_EM)){
	$distinct_vertical_EM = $row_distinct_vertical_EM['distinct_vertical_value'];
	if(!in_array($distinct_vertical_EM,$distinct_vertical_array))
		array_push($distinct_vertical_array,$distinct_vertical_EM);
}
$sql_distinct_vertical_OH = "SELECT DISTINCT OH.vertical_value as distinct_vertical_value FROM order_header OH, location LO WHERE LO.trans_id=OH.order_no ".$condition.$vertical_condition_one.$emp_hierarchy_order_condition;
$res_distinct_vertical_OH = mysql_query($sql_distinct_vertical_OH);
while($row_distinct_vertical_OH = mysql_fetch_array($res_distinct_vertical_OH)){
	$distinct_vertical_OH = $row_distinct_vertical_OH['distinct_vertical_value'];
	if(!in_array($distinct_vertical_OH,$distinct_vertical_array))
		array_push($distinct_vertical_array,$distinct_vertical_OH);
}
sort($distinct_vertical_array);
if(count($distinct_vertical_array)>0)
{
	$count = 1;
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='8' align='center'>Daily Activity Analysis</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>State</td>
			<td>Employee Name</td>
			<td>".$table_columnname."</td>
			<td>Productive Calls</td>
			<td>Non Productive Calls</td>
			<td colspan=\"2\">Total</td>
		  </tr>";
	echo "<tr class='TDHEAD_SUB'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Primary</td>
			<td>Secondary</td>
		  </tr>";
		  
	foreach($distinct_vertical_array as $distinct_vertical_value){
		$vertical_value = $distinct_vertical_value;
		$vertical_name = $distinct_vertical_value;
		
		if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
			$pos = substr($vertical_name,0,1);
			if($pos == 'M'){
				$vertical_name = 'MACROMAN';
			}
		}
		
		if($vertical_name == 'RUPA')
			$color = '#FF0000';
		else if($vertical_name == 'BUMCHUMS')
			$color = '#00BFFF';
		else if($vertical_name == 'EURO')
			$color = '#000000';
		else if($vertical_name == 'TOTS')
			$color = '#00EE76';
		else if($vertical_name == 'SOFTLINE')
			$color = '#FF1493';
		else if($vertical_name == 'JON')
			$color = '#E066FF';
		else if($vertical_name == 'MACROMAN')
			$color = '#8470FF';
		
		if($vertical_name != ''){
			if(!in_array($vertical_name,$vertical_array)){
				array_push($vertical_array,$vertical_name);
				echo "<tr style='color:$color; font-weight:bold; background:#FFFFF0;'><td colspan='7' align='center'>".$vertical_name."</td></tr>";
			}
		}
		
		if($vertical_name == "MACROMAN"){
			if($macroman_flag == 0)
				continue;
			$find_in_set_cond = " AND EM.vertical_value LIKE 'M%' ";
			$macroman_flag = 0;
		}else{
			$find_in_set_cond = " AND FIND_IN_SET('".$vertical_value."',EM.vertical_value) ";
		}
		
		/*if($vertical_value != '')
		echo "<tr style='color:$color; font-weight:bold; background:#FFFFF0;'><td colspan='7' align='center'>".$vertical_value."</td></tr>";*/
		if($vertical_value != ''){
		/*---------------------------------> Select employee <--------------------------------*/
		$sql_empname_time = "SELECT EM.emp_code, EM.emp_name, EM.state, SUBSTRING(LO.date,12) as attendance_time".$count_transid_condition." FROM employee_master EM, location LO WHERE EM.emp_code=LO.emp_code".$find_in_set_cond.$condition.$emp_hierarchy_condition." AND LO.trans_id LIKE 'A%' AND EM.acedns != 'N'".$group_by." ORDER BY EM.emp_name ASC";
		$res_empname_time = mysql_query($sql_empname_time);
		while($row_empname_time = mysql_fetch_array($res_empname_time))
		{
			$emp_name = $row_empname_time['emp_name'];
			$emp_code = $row_empname_time['emp_code'];
			$state = $row_empname_time['state'];
			$attendance_time = $row_empname_time['attendance_time'];
			$no_of_days_present = $row_empname_time['COUNT(LO.trans_id)'];
			
			if($table_columnname == 'No of days present')
				$table_column_value = $no_of_days_present;
			else
				$table_column_value = $attendance_time;
			
			/*---------------------------------> Count of productive call <--------------------------------*/
			$sql_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code".$find_in_set_cond."AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id NOT LIKE 'PA%' ".$condition;
			$res_productive_call = mysql_query($sql_productive_call);
			$row_productive_call = mysql_fetch_array($res_productive_call);
			$productive_call = $row_productive_call['COUNT(LO.trans_id)'];
			
			/*---------------------------------> Count of non-productive call <--------------------------------*/
			$sql_non_productive_call = "SELECT COUNT(LO.trans_id) FROM location LO, employee_master EM WHERE LO.emp_code='".$emp_code."' AND LO.emp_code=EM.emp_code".$find_in_set_cond."AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') ".$condition;
			$res_non_productive_call = mysql_query($sql_non_productive_call);
			$row_non_productive_call = mysql_fetch_array($res_non_productive_call);
			$non_productive_call = $row_non_productive_call['COUNT(LO.trans_id)'];
						
			$total_productive_call += $productive_call;
			$total_non_productive_call += $non_productive_call;
			
			if($productive_call == 0 && $non_productive_call == 0)
				$color_name = '#FF0000';
			else
				$color_name = '';
			
			
			$primary_quantity = '';
			/*---------------------------------> Total primary sales <--------------------------------*/
			$sql_total_primary = "SELECT OD.order_no, OD.sku_code, OD.qty, OD.UOM, PM.UOM1, PM.UOM2, PM.conversion_factor FROM order_details OD, customer_master CM, order_header OH, product_master PM WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='D' AND OD.sku_code = PM.prod_code";
			$res_total_primary = mysql_query($sql_total_primary);
			while($row_total_primary = mysql_fetch_array($res_total_primary)){
				$sku_code_primary = $row_total_primary['sku_code'];
				$qty_primary = $row_total_primary['qty'];
				$UOM_primary = $row_total_primary['UOM'];
				$UOM1_primary = $row_total_primary['UOM1'];
				$UOM2_primary = $row_total_primary['UOM2'];
				$conversion_factor_primary = $row_total_primary['conversion_factor'];
				
				if($UOM_primary == $UOM2_primary && $UOM_primary != ''){
					$qty_primary = $qty_primary/$conversion_factor_primary;
				}
				/*else if($UOM_primary == ''){
					$qty_primary = ($qty_primary+0);
				}*/
				
				$primary_quantity += $qty_primary;
			}
			
			
			//$primary_quantity = $row_total_primary['SUM(OD.qty)'];
			$total_primary_quantity += $primary_quantity;
			
			$secondary_quantity = '';
			/*---------------------------------> Total secondary sales <--------------------------------*/
			$sql_total_secondary = "SELECT OD.order_no, OD.sku_code, OD.qty, OD.UOM, PM.UOM1, PM.UOM2, PM.conversion_factor FROM order_details OD, customer_master CM, order_header OH, product_master PM WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='R' AND OD.sku_code = PM.prod_code";
			$res_total_secondary = mysql_query($sql_total_secondary);
			while($row_total_secondary = mysql_fetch_array($res_total_secondary)){
				$sku_code_secondary = $row_total_secondary['sku_code'];
				$qty_secondary = $row_total_secondary['qty'];
				$UOM_secondary = $row_total_secondary['UOM'];
				$UOM1_secondary = $row_total_secondary['UOM1'];
				$UOM2_secondary = $row_total_secondary['UOM2'];
				$conversion_factor_secondary = $row_total_secondary['conversion_factor'];
				
				if($UOM_secondary == $UOM2_secondary && $UOM_secondary != ''){
					$qty_secondary = $qty_secondary/$conversion_factor_secondary;
				}
				/*else if($UOM_secondary == ''){
					$qty_secondary = ($qty_secondary+0);
				}*/
				
				$secondary_quantity += $qty_secondary;
			}
			
			
			//$secondary_quantity = $row_total_secondary['SUM(OD.qty)'];
			$total_secondary_quantity += $secondary_quantity;
			
			//if(!in_array($emp_code,$emp_code_array))
			//{
				//array_push($emp_code_array,$emp_code);
				echo "<tr>
					<td>".$count."</td>
					<td>".$state."</td>
					<td style='background:$color_name'>".$emp_name."</td>
					<td align='right'>".$table_column_value."</td>
					<td align='right'>".$productive_call."</td>
					<td align='right'>".$non_productive_call."</td>
					<td align='right'>".number_format($primary_quantity,2)."</td>
					<td align='right'>".number_format($secondary_quantity,2)."</td>
				  </tr>";
			//}
			
			$count++;
		}
		}
	}
	echo "<tr style='font-weight:bold;'>
			<td colspan='4'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".$total_primary_quantity."</td>
			<td align='right'>".$total_secondary_quantity."</td>
		  </tr>";
	echo "</table> <br />";
	?>
    <div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
      <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
    <?php
}
else
{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysql_close($link);
?>