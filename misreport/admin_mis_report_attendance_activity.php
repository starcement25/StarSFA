<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$val = $_REQUEST['val'];
$employee = $_REQUEST['employee'];
$employee = str_replace("^","'",$employee);
$employee = str_replace("#",",",$employee);
$emp_hierarchy_condition=' AND LO.emp_code IN('.$employee.') ';


if($val == 'T'){
	$date = date('Y-m-d');
	$date_condition = " AND SUBSTRING(LO.date,1,10) = '".$date."' ";
	$column_name = "Time";
}
else if($val == 'MTD'){
	$current_month = date('m');
	$mtd_date = date('m');
	$date_condition = " AND SUBSTRING(LO.date,6,2) = '".$current_month."' ";
	$column_name = "Days On Field";
}
else if($val == 'YTD'){
	$current_date = $date = date('Y-m-d');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		$previous_year_date = date('Y-04-01');
	}
	$date_condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$previous_year_date."' AND '".$current_date."') ";
	$column_name = "Days On Field";
}

?>
<table width="100%" border="1" style="border-collapse:collapse;" class="border">
  <tr class="TDHEAD" align="center">
  	<td>SI</td>
    <td>Emp Code</td>
    <td>Emp Name</td>
    <td><?php echo $column_name; ?></td>
    <td>DCR</td>
    <td>Locate</td>
  </tr>
  
<?php
$count = 1;
$sql_emp_code = "SELECT emp_code, emp_name FROM employee_master WHERE emp_code IN(".$employee.") ORDER BY emp_name ASC";
$res_emp_code = mysqli_query($link,$sql_emp_code);
while($row_emp_code = mysqli_fetch_assoc($res_emp_code)){
	$emp_code = $row_emp_code['emp_code'];
	$emp_name = $row_emp_code['emp_name'];
	
	$sql_attendance = "SELECT COUNT(LO.trans_id) FROM location WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = '".$emp_code."'".$date_condition;
	$res_attendance = mysqli_query($link,$sql_attendance);
	$row_attendance = mysqli_fetch_assoc($res_attendance);
	$total_count = $row_attendance['COUNT(LO.trans_id)'];
	if($total_count>0){
		if($val == 'MTD' || $val == 'YTD'){
			$sql_mtd_ytd = "SELECT COUNT(LO.trans_id) FROM location WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = '".$emp_code."'".$date_condition;
			$res_mtd_ytd = mysqli_query($link,$sql_mtd_ytd);
			$row_mtd_ytd = mysqli_fetch_assoc($res_mtd_ytd);
			$atd_result = $row_mtd_ytd['COUNT(LO.trans_id)'];
		}
		else if($val == 'T'){
			$sql_mtd_ytd = "SELECT DATE_FORMAT(LO.date,'%h:%i:%s') AS atd_time FROM location WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = '".$emp_code."'".$date_condition;
			$res_mtd_ytd = mysqli_query($link,$sql_mtd_ytd);
			$row_mtd_ytd = mysqli_fetch_assoc($res_mtd_ytd);
			$atd_result = $row_mtd_ytd['atd_time'];
		}
		
		if(need_DCR == 'yes')
		{
			$DCR = "<a href=\"adminDCR.php?emp_code=$emp_code&mode=$val&page=misreport&order_received=true\"style=\"color:#930;font-weight:bold;\" target=\"_blank\">DCR</a>"."";
			$DCR_mail = "<a href=\"javascript:void(0)\"   style=\"color:#930;font-weight:bold;\" onClick=\"javascript:sendDcrMail('".$emp_code."','".strtoupper($_SESSION['nick_name'])."','".$order_date."');\">MAIL DCR</a>";
		}
		else
		{
			$DCR='';
			$DCR_mail="";
		}
		
		echo "<tr>
					<td>".$count."</td>
					<td>".$emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$atd_result."</td>
					<td align=\"center\">".$DCR."--".$DCR_mail."</td>
					<td><a href=\"customerLocate.php?trans_id=$stock_audit_id&customer_code=$customer_code&
							emp_code=$emp_code&date=$date&from_date=$from_date&to_date=$to_date&mode=$mode&page=$page&visit_map=true\" 	style=\"color:brown;font-weight:bold;text-decoration:none;\" target=\"_blank\">Locate</a></td>
				  </tr>";
		$count++;
	}
}
mysqli_close($link);
?>

