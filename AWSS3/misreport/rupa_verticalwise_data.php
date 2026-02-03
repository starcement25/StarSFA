<?php
	/*define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	mysql_connect(SERVER,USER,PASSWORD);
	mysql_select_db("acedns_RUPA");*/
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$type = $_REQUEST['type'];
	$vertical = $_REQUEST['vertical'];
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	
	@$current_date = date('Y-m-d');
	
	if($type == 'today')
	{
		$condition = "VBED.operation_date LIKE '%".$current_date."%'";
		$report_details = 'Report: Today';
	}
	else if($type == 'mtd')
	{
		$month = explode("-",$current_date);
		$condition = "(YEAR(VBED.operation_date)=2015 AND MONTH(VBED.operation_date) = 08)";
		$report_details = 'Report: MTD';
	}
	else
	{
		$condition = "(VBED.operation_date BETWEEN '".$start_date."' AND '".$end_date."')";
		$startdate = date('d-m-Y',strtotime($start_date));
		$enddate = date('d-m-Y',strtotime($end_date));
		$report_details = "Report: From ".$startdate." To ".$enddate;
	}
	
		
	$sql_vertical_wise_data = "SELECT EM.emp_code, EM.emp_name, VBED.calls_made, VBED.productive, sum(VBED.qty) as quantity, round(((VBED.productive/VBED.calls_made)*100),2) as conversion_percentage FROM employee_master EM, vertical_branch_employeewise_details VBED WHERE VBED.emp_code=EM.emp_code AND VBED.vertical_value='".$vertical."' AND ".$condition." GROUP BY EM.emp_code ORDER BY EM.emp_name ASC";
	$res_vertical_wise_data = mysql_query($sql_vertical_wise_data);
	$total_vertical_rows = mysql_num_rows($res_vertical_wise_data);
	
	if($total_vertical_rows>0)
	{
		echo "<table class=\"border\" id=\"report_data\" border=\"1\" width=\"100%\" style=\"border-collapse:collapse;\" cellpadding=\"6px\">
				<tr class=\"TDHEAD\"><td colspan=\"6\" align=\"center\">".$report_details."</td></tr>
				<tr align=\"center\" class=\"TDHEAD_SUB\">
					<td><b>SI</b></td>
					<td><b>Emp Name</b></td>
					<td><b>Calls Made</b></td>
					<td><b>Productive Calls</b></td>
					<td><b>Quantity</b></td>
					<td><b>PCP</b></td>
				</tr>";
				
		$count = 1;
		$res_vertical_wise_data = mysql_query($sql_vertical_wise_data);
		while($row_vertical_wise_data = mysql_fetch_array($res_vertical_wise_data))
		{
			$total_calls_made += $row_vertical_wise_data['calls_made'];
			$total_productive_calls += $row_vertical_wise_data['productive'];
			$total_quantity += $row_vertical_wise_data['quantity'];
			echo "<tr>
					<td>".$count."</td>
					<td>".$row_vertical_wise_data['emp_name']."</td>
					<td align=\"right\">".$row_vertical_wise_data['calls_made']."</td>
					<td align=\"right\">".$row_vertical_wise_data['productive']."</td>
					<td align=\"right\">".$row_vertical_wise_data['quantity']."</td>
					<td align=\"right\">".$row_vertical_wise_data['conversion_percentage']."</td>
				  </tr>";
			$count++;
		}
		echo "<tr>
				<td colspan=\"2\"><b>Total</b></td>
				<td>".$total_calls_made."</td>
				<td>".$total_productive_calls."</td>
				<td>".$total_quantity."</td>
				<td></td>
			  </tr>";
		echo "</table>";
	}
	else
	{
		echo "No records found";
	}
	mysql_close($link);
?>