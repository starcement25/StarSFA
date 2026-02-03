<?php
//echo "Work In Progress";die;
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	$emp_hierarchy = "";
	$emp_hierarchy_condition = "";
}
else{
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " AND SUBSTRING(POCM.order_no,-19,5) IN (".$emp_hierarchy.") ";
}
	

$current_date=date('Y-m-d');
$currentmonth=date('n',strtotime($current_date));
$prevmonth=$currentmonth-1;
$currentyear=date('Y',strtotime($current_date));
$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month));
$year = $month_year[1];

//echo "Current week range from $this_week_sd to $this_week_ed ";
	$sql_get_empdetails = "SELECT DISTINCT EM.emp_name,EM.designation,EM.branch_code,EM.emp_code,SUBSTRING(POCM.visit_date,1,10) AS visit_date,COUNT(DISTINCT CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.order_no END ) AS productive_call, COUNT(DISTINCT CASE WHEN POCM.order_no LIKE 'NO%' THEN POCM.order_no END ) AS non_productive_call,SUM(CASE WHEN POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS order_amount,GROUP_CONCAT( DISTINCT POCM.customer_code SEPARATOR ',') AS unique_customer FROM employee_master EM LEFT JOIN `prev_order_counting_master` POCM ON SUBSTRING(POCM.order_no,-19,5)=EM.emp_code 
						AND MONTH(POCM.visit_date) IN('".$monthvalue."') AND YEAR(POCM.visit_date)='".$year."' WHERE EM.acedns='Y' 
	".$emp_hierarchy_condition."  GROUP BY EM.emp_code,SUBSTRING(POCM.visit_date,1,10) ORDER BY EM.emp_name ASC,POCM.visit_date DESC";			
$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
$total_rows = mysqli_fetch_assoc($res_get_empdetails);
if($total_rows>0){

	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='20' align='center'>Dashboard </td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td></td>
			<td ></td>
			<td></td>
			<td></td>
			<td colspan='6' style='background: Gray'>Monthly Summary (".date('F',strtotime($month)).")</td>
		  </tr>";
	echo "<tr class='TDHEAD_SUB'>
			<td align='center'>SI</td>
			<td align='center'>Branch</td>
			<td align='center'>Name of TSM/LAS</td>
			<td align='center'>Designation</td>
			<td>Total Call</td>
			<td>Total URB</td>
			<td>Total New URB</td>
			<td>Total Productive Calls</td>
			<td>Total Order Value</td>
			<td>Average Time spent in an outlet</td>
		  </tr>";
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	$emp_code_array=array();
	$branch_code_array=array();
	$emp_name_array=array();
	$designation_array=array();
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		
		$emp_code = $row_get_empdetails['emp_code'];
		$emp_name = $row_get_empdetails['emp_name'];
		$designation = $row_get_empdetails['designation'];
		$branch_code = $row_get_empdetails['branch_code'];
		$productive_call = $row_get_empdetails['productive_call'];
		$non_productive_call = $row_get_empdetails['non_productive_call'];
		$total_call_individual=$productive_call+$non_productive_call;
		$order_amount = $row_get_empdetails['order_amount'];
		$visit_date = $row_get_empdetails['visit_date'];
		$visit_date_month = substr($visit_date,5,2);
		$unique_customer = $row_get_empdetails['unique_customer'];
		$unique_customer_array=explode(',',$unique_customer);
		
		$unique_new_customer='';
		foreach($unique_customer_array as $unique_customer_val)
		{
			if(substr($unique_customer_val,0,1)=='N' && $unique_customer_val!='')
			{
				$unique_new_customer=$unique_new_customer.$unique_customer_val.',';
			}
		}
		$unique_new_customer=substr($unique_new_customer,0,-1);
		
		if(!in_array($emp_code,$emp_code_array))
		{
			array_push($emp_code_array,$emp_code);
			array_push($emp_name_array,$emp_name);
			array_push($branch_code_array,$branch_code);
			array_push($designation_array,$designation);
		}
		
		
		if($visit_date_month==$monthvalue)
		{
		${'productive_call_month'.$emp_code}=${'productive_call_month'.$emp_code}+$productive_call;
		${'non_productive_call_month'.$emp_code}=${'non_productive_call_month'.$emp_code}+$non_productive_call;
		${'order_amount_month'.$emp_code}=${'order_amount_month'.$emp_code}+$order_amount;
		${'unique_customer_month'.$emp_code}=${'unique_customer_month'.$emp_code}.$unique_customer.',';
		if($unique_new_customer!='')
		{
			${'unique_new_customer_month'.$emp_code}=${'unique_new_customer_month'.$emp_code}.$unique_new_customer.',';
		}
		else
		{
			${'unique_new_customer_month'.$emp_code}=${'unique_new_customer_month'.$emp_code}.$unique_new_custome;
		}
		}
	}
	$count=1;
	//print_r($emp_code_array);
	//echo ${'unique_new_customer_monthE0033'};
	for($i=0;$i< count($emp_code_array);$i++){
		
		${'total_call_month'.$emp_code_array[$i]}=${'productive_call_month'.$emp_code_array[$i]}+${'non_productive_call_month'.$emp_code_array[$i]};
		
		$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$branch_code_array[$i]."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$branch_name=$rowbranch['branch_name'];
		
		${'unique_customer_month'.$emp_code_array[$i]}=substr(${'unique_customer_month'.$emp_code_array[$i]},0,-1);
		${'unique_customer_month_arr'.$emp_code_array[$i]}=explode(',',${'unique_customer_month'.$emp_code_array[$i]});
		${'unique_customer_month_arr'.$emp_code_array[$i]}=array_unique(${'unique_customer_month_arr'.$emp_code_array[$i]});
		${'unique_new_customer_month'.$emp_code_array[$i]}=substr(${'unique_new_customer_month'.$emp_code_array[$i]},0,-1);
		${'unique_new_customer_month_arr'.$emp_code_array[$i]}=explode(',',${'unique_new_customer_month'.$emp_code_array[$i]});
		${'unique_new_customer_month_arr'.$emp_code_array[$i]}=array_unique(${'unique_new_customer_month_arr'.$emp_code_array[$i]});
		if(${'total_call_month'.$emp_code_array[$i]}==0) ${'unique_customer_month_arr'.$emp_code_array[$i]}=0;
		if(${'total_call_month'.$emp_code_array[$i]}==0) ${'unique_new_customer_month_arr'.$emp_code_array[$i]}=0;
		
		//print_r( ${'unique_new_customer_month_arrE0033'});
		echo "<tr>
				<td>".$count."</td>
				<td >".$branch_name."</td>
				<td >".$emp_name_array[$i]."</td>
				<td >".$designation_array[$i]."</td>
				<td align='right'>".${'total_call_month'.$emp_code_array[$i]}."</td>
				<td align='right'>".((${'total_call_month'.$emp_code_array[$i]}==0)?0:count(${'unique_customer_month_arr'.$emp_code_array[$i]}))."</td>
				<td align='right'>".((${'total_call_month'.$emp_code_array[$i]}==0)?0:count(${'unique_new_customer_month_arr'.$emp_code_array[$i]}))."</td>
				<td align='right'>".${'productive_call_month'.$emp_code_array[$i]}."</td>
				<td align='right'>".number_format(${'order_amount_month'.$emp_code_array[$i]},2)."</td>
				<td align='right'></td>
			  </tr>";
				  
		$count++;
	}
	echo "</table>";
	echo "<br>
<div style=\"width:100%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
    <input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"exporttocsv();\" >
</div>";

}
else{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>
