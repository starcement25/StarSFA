<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = date('Y-m-d');

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
$vertical=$_REQUEST['vertical'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$asm_so=$_REQUEST['asm_so'];

/*--------------------> Condition to display data according to vertical name/all data<----------------------------*/
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
	//COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' AND POCM.vertical_value IN(".$vertical.") AND POCM.cust_type='R' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls_secondary
	$sql_total_calls = "SELECT EM.emp_code,EM.emp_name,EM.state,EM.designation,DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') AS trans_date,COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' AND POCM.vertical_value IN(".$vertical.") THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS productive_calls, COUNT( DISTINCT (CASE WHEN POCM.order_no LIKE 'NO%' THEN CONCAT(POCM.customer_code,'^',SUBSTRING(POCM.order_no,-14,8)) END )) AS non_productive_calls,SUM( CASE WHEN POCM.order_no LIKE 'O%' AND POCM.vertical_value IN(".$vertical.") AND POCM.cust_type='R' THEN  POCM.visit_qty END ) AS productive_calls_secondary,GROUP_CONCAT( DISTINCT (CASE WHEN POCM.order_no LIKE 'O%' AND POCM.vertical_value IN(".$vertical.") AND POCM.customer_code LIKE 'N%' THEN (POCM.customer_code) END )) AS productive_new_custome FROM employee_master EM INNER JOIN `prev_order_counting_master` POCM ON SUBSTRING(POCM.order_no,-19,5)=EM.emp_code AND SUBSTRING(POCM.order_no,1,1) IN('N','O') AND 
	DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' AND  SUBSTRING(POCM.order_no,-19,5) IN(".$asm_so.") 
	GROUP BY SUBSTRING(POCM.order_no,-19,5),DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') ORDER BY EM.emp_name ASC ,DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%Y-%m-%d') DESC  ";
	$rs_total_calls=mysqli_query($link,$sql_total_calls);
	$count_total_calls=mysqli_num_rows($rs_total_calls);

if(count($count_total_calls)>0)
{
	$count = 1;
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='14' align='center'>Secondary Sales Report</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SL NO</td>
			<td>Date</td>
			<td>Employee Code</td>
			<td>Employee Name</td>
			<td>Designation </td>
			<td>State</td>
			<td>Brand </td>
			<td>Total Productive Calls</td>
			<td>Total Non Productive Calls</td>
			<td>Total Calls</td>
			<td>Total Secondary Orders Booked(qty)</td>
			<td>1st Booking Time</td>
			<td>Last Booking Time</td>
			<td>Total New Productive customers</td>
		  </tr>";
		while($row_total_calls=mysqli_fetch_assoc($rs_total_calls)){
		$emp_code=$row_total_calls['emp_code'];
		$emp_name=$row_total_calls['emp_name'];
		$trans_date=$row_total_calls['trans_date'];
		$state=$row_total_calls['state'];
		$designation=$row_total_calls['designation'];
		$productive_calls=$row_total_calls['productive_calls'];
		$non_productive_calls=$row_total_calls['non_productive_calls'];
		$productive_calls_secondary=$row_total_calls['productive_calls_secondary'];
		$productive_new_custome=$row_total_calls['productive_new_custome'];
		$productive_new_customer_array=explode(",",$productive_new_custome);
		$total_calls=$productive_calls+$non_productive_calls;
		$actual_productive_new_customer_array=array();
		foreach($productive_new_customer_array as $productive_new_customer_val)
		{
			$sqlactualproductivenewcustomer="SELECT DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') AS order_date_first FROM prev_order_counting_master 
												WHERE customer_code='".$productive_new_customer_val."' 
												ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
			$rsactualproductivenewcustomer=mysqli_query($link,$sqlactualproductivenewcustomer);
			$rowactualproductivenewcustomer=mysqli_fetch_assoc($rsactualproductivenewcustomer);	
			$order_date_first=$rowactualproductivenewcustomer['order_date_first'];
			$newcustomer_create_date=date('Y-m-d',strtotime(substr($productive_new_customer_val,-14,8)));
			if(strtotime($order_date_first)==strtotime($newcustomer_create_date) && strtotime($trans_date)==strtotime($newcustomer_create_date))
			{
				if(!in_array($productive_new_customer_val,$actual_productive_new_customer_array))
				{
					array_push($actual_productive_new_customer_array,$productive_new_customer_val);
				}
			}
		}
		$sqlbookinngtime="SELECT DATE_FORMAT(visit_date,'%H:%i:%s') as visit_time FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,1,1) IN('O') AND 
						DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d')='".$trans_date."' AND  
						SUBSTRING(order_no,-19,5)='".$emp_code."' AND vertical_value IN(".$vertical.") ORDER BY visit_date DESC";
		$rsbookingtime=mysqli_query($link,$sqlbookinngtime);
		$total_bbokingtime=mysqli_num_rows($rsbookingtime);
		$cntbookingtime=1;
		while($rowbookingtime=mysqli_fetch_assoc($rsbookingtime))
		{
			if($cntbookingtime==1)
			{
				$last_booking_time=$rowbookingtime['visit_time'];
			}
			if($total_bbokingtime==$cntbookingtime){
				$first_booking_time=$rowbookingtime['visit_time'];
			}
			$cntbookingtime++;
		}
		if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
			$pos = substr($vertical_name,0,1);
			if($pos == 'M'){
				$vertical_name = 'MACROMAN';
			}
		}
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
			
			//if(!in_array($emp_code,$emp_code_array))
			//{
				//array_push($emp_code_array,$emp_code);
				echo "<tr>
					<td>".$count."</td>
					<td>".date('d-m-Y',strtotime($trans_date))."</td>
					<td>".$emp_code."</td>
					<td >".$emp_name."</td>
					<td >".$designation."</td>
					<td >".$state."</td>
					<td >".str_replace("'","",$vertical)."</td>
					<td align='right'>".$productive_calls."</td>
					<td align='right'>".$non_productive_calls."</td>
					<td align='right'>".$total_calls."</td>
					<td align='right'>".$productive_calls_secondary."</td>
					<td align='right'>".$first_booking_time."</td>
					<td align='right'>".$last_booking_time."</td>
					<td align='right'>".count($actual_productive_new_customer_array)."</td>
				  </tr>";
			//}
			
			$count++;
		}
	/*echo "<tr style='font-weight:bold;'>
			<td colspan='4'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			<td align='right'>".$total_primary_quantity."</td>
			<td align='right'>".$total_secondary_quantity."</td>
		  </tr>";*/
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
mysqli_close($link);
?>