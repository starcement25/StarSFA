<?php
ob_start();
session_start();
require("adminUtils.php");

$month_data = $_REQUEST['month_data'];
$year_month_split = explode("-",$month_data);
$month_days = cal_days_in_month(CAL_GREGORIAN,$year_month_split[1],$year_month_split[0]);
$monthName = date('M', mktime(0, 0, 0, $year_month_split[1], 10));
$show_month = $monthName."-".$year_month_split[0];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$vertical = $_REQUEST['vertical'];
$state = $_REQUEST['state'];
//$frequency = $_REQUEST['frequency'];

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
/*$header_string = "Vertical:".$vertical."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month:".$month_data;*/
$curdate=date('Y-m-d');
/*if($frequency=='30')
{
 $previous30day=date('Y-m-d', strtotime("-30 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous30day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}
if($frequency=='60')
{
 $previous60day=date('Y-m-d', strtotime("-60 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous60day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}
if($frequency=='90' )
{
 $previous180day=date('Y-m-d', strtotime("-180 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous180day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}*/
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$start_date."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$end_date."'";

	/*$sqlcustomervisit="SELECT CM.dns_customer_code,CM.customer_code, CM.customer_name, COUNT(OH.order_no) AS no_of_visit,CM.rds_tag,CM.emp_code 
	FROM customer_master CM,order_header OH WHERE OH.customer_code=CM.customer_code 
	AND CM.emp_code IN(".$employee.")".$date_condition." GROUP BY OH.customer_code ORDER BY CM.customer_name ASC";*/
	$sqlcustomervisit="SELECT CM.dns_customer_code,CM.customer_code, CM.customer_name,OH.order_no AS no_of_visit,CM.rds_tag,CM.emp_code,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') AS order_date
	FROM customer_master CM,order_header OH WHERE OH.customer_code=CM.customer_code 
	AND CM.emp_code IN(".$employee.")".$date_condition." ORDER BY CM.emp_code ASC";
	$rescustomervisit = mysqli_query($link,$sqlcustomervisit);
	$totalcustomervisit = mysqli_num_rows($rescustomervisit);
	if($totalcustomervisit>0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="13" align="center">Frequency Report(Retailer Wise)</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
            <td>Customer Code</td>
			<td>Customer Name</td>
            <td>Employee Name</td>
            <td>Distributor Code</td>
			<td>Distributor Name</td>
            <?php //if($frequency=='30'){?>
			<td>30 days</td>
            <?php //}if($frequency=='60'){?>
			<td>31-60 days</td>
            <td>61-90 days</td>
            <td>91-120 days</td>
            <td>121-180 days</td>
            <td>181-365 days</td>
		  </tr>
		<?php
		$rescustomervisit = mysqli_query($link,$sqlcustomervisit);
		$emp_code_array=array();
		$customer_code_array=array();
		$rds_tag_array=array();
		$customer_name_array=array();
		$dns_customer_code_array=array();
		while($rowcustomervisit = mysqli_fetch_assoc($rescustomervisit)){
			$emp_code = $rowcustomervisit['emp_code'];
			$customer_code = $rowcustomervisit['customer_code'];
			$dns_customer_code = $rowcustomervisit['dns_customer_code'];
			$customer_name = $rowcustomervisit['customer_name'];
			$no_of_visit=$rowcustomervisit['no_of_visit'];
			$rds_tag=$rowcustomervisit['rds_tag'];
			$order_date=$rowcustomervisit['order_date'];
			
			if(!in_array($customer_code,$customer_code_array)){
				array_push($customer_code_array,$customer_code);
				array_push($emp_code_array,$emp_code);
				array_push($customer_name_array,$customer_name);
				array_push($rds_tag_array,$rds_tag);
				array_push($dns_customer_code_array,$dns_customer_code);
				${no_of_visit_30.$customer_code}=0;
				${no_of_visit_60.$customer_code}=0;
				${no_of_visit_90.$customer_code}=0;
				${no_of_visit_120.$customer_code}=0;
				${no_of_visit_180.$customer_code}=0;
				${no_of_visit_365.$customer_code}=0;
			}
			$datediff =strtotime($end_date)-strtotime($order_date);
			$datediffdays=round($datediff / (60 * 60 * 24));
			if($datediffdays <=30)
			{
				${no_of_visit_30.$customer_code}=${no_of_visit_30.$customer_code}+1;
			}
			if($datediffdays > 30 && $datediffdays <=60)
			{
				${no_of_visit_60.$customer_code}=${no_of_visit_60.$customer_code}+1;
			}
			if($datediffdays > 60 && $datediffdays <=90)
			{
				${no_of_visit_90.$customer_code}=${no_of_visit_90.$customer_code}+1;
			}
			if($datediffdays > 90 && $datediffdays <=120)
			{
				${no_of_visit_120.$customer_code}=${no_of_visit_120.$customer_code}+1;
			}
			if($datediffdays > 120 && $datediffdays <=180)
			{
				${no_of_visit_180.$customer_code}=${no_of_visit_180.$customer_code}+1;
			}
			if($datediffdays > 180 && $datediffdays <=365)
			{
				${no_of_visit_365.$customer_code}=${no_of_visit_365.$customer_code}+1;
			}
		}
		$count=1;
		for($i=0;$i<count($customer_code_array);$i++){
				$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code_array[$i]."'";
				$res_emp = mysqli_query($link,$sql_emp);
				$row_emp = mysqli_fetch_assoc($res_emp);
				$emp_name = $row_emp['emp_name'];
				
				$sql_rds = "SELECT dns_customer_code,customer_name FROM customer_master WHERE customer_code = '".$rds_tag_array[$i]."'";
				$res_rds = mysqli_query($link,$sql_rds);
				$row_rds = mysqli_fetch_assoc($res_rds);
				$rds_name = $row_rds['customer_name'];
				$rds_code = $row_rds['dns_customer_code'];
			echo "<tr>
					<td>".$count."</td>
					<td>".$dns_customer_code_array[$i]."</td>
					<td>".$customer_name_array[$i]."</td>
					<td>".$emp_name."</td>
					<td>".$rds_code."</td>
					<td>".$rds_name."</td>
					<td align=\"right\">".${no_of_visit_30.$customer_code_array[$i]}."</td>
					<td align=\"right\">".${no_of_visit_60.$customer_code_array[$i]}."</td>
					<td align=\"right\">".${no_of_visit_90.$customer_code_array[$i]}."</td>
					<td align=\"right\">".${no_of_visit_120.$customer_code_array[$i]}."</td>
					<td align=\"right\">".${no_of_visit_180.$customer_code_array[$i]}."</td>
					<td align=\"right\">".${no_of_visit_365.$customer_code_array[$i]}."</td>
				  </tr>";
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>
