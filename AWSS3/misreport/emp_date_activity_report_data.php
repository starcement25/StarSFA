<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$month = $_REQUEST['month'];
$month_year = explode("-",$month);
$monthvalue = date('m',strtotime($month_year[0]));
$year = $month_year[1];

function getDatesBetween2Dates($startTime, $endTime) {
    $day = 86400;
    $format = 'Y-m-d';
    $startTime = strtotime($startTime);
    $endTime = strtotime($endTime);
    $numDays = round(($endTime - $startTime) / $day) + 1;
    $days = array();
        
    for ($i = 0; $i < $numDays; $i++) {
        $days[] = date($format, ($startTime + ($i * $day)));
    }
        
    return $days;
}
$dateArray=array();
// Start date
$start_date = $year.'/'.$monthvalue.'/01';
// End date
$d = new DateTime( $start_date ); 
$end_date = $d->format('Y/m/t');

$days = getDatesBetween2Dates($start_date, $end_date);

foreach($days as $key => $value){
   array_push($dateArray,$value);
}
/*if($emp_code == 'all'){
	if(strtoupper($_SESSION['admin_login']) != "ADMIN"){
		$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
		$order_check_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
		$employee_select_condition = " AND LO.emp_code IN (".$emp_hierarchy.") ";
	}
	else{
		$order_check_condition = "";
		$employee_select_condition = "";
	}
	$order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
}
else{
	$sql_empname = "SELECT emp_name,emp_code,state,designation FROM employee_master WHERE emp_code='".$emp_code."'";
	$res_empname = mysql_query($sql_empname);
	$row_empname = mysql_fetch_array($res_empname);
	$emp_name = $row_empname['emp_name'];
	$emp_code = $row_empname['emp_code'];
	$state = $row_empname['state'];
	$designation = $row_empname['designation'];
	
	$emp_code = $_REQUEST['emp_code'];
	$emp_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
	
	$order_condition = " AND SUBSTRING(OH.order_no,-14,4)='".$year."' AND SUBSTRING(OH.order_no,-10,2)='".$monthvalue."' ";
	$order_check_condition = " AND SUBSTRING(OH.order_no,2,5)=EM.emp_code ";
	
	$employee_select_condition = " AND LO.emp_code = '".$emp_code."' ";
}*/
$current_date = date('Y-m-d');
$count = 1;
?>
<table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
              <tr class="TDHEAD">
                <td>Date</td>
                <td>Total Calls</td>
                <td>Productive Calls</td>
                <td>Login Time</td>
                <td>Logout Time</td>
                <td>Sale</td>
              </tr>
            <?php
		//foreach($dateArray as $key=>$value){
				$final_date=date('Ymd',strtotime($value));
				$visit_date_array=array();
				$sql_emp_login_logout_time="SELECT (CASE WHEN trans_id LIKE 'A%' THEN date END) AS logindate,(CASE WHEN trans_id LIKE 'CH%' THEN date END) AS logoutdate,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%d-%m-%Y') AS visit_date
							FROM `location` where emp_code='".$emp_code."' AND SUBSTRING(trans_id,-14,4)='".$year."' AND SUBSTRING(trans_id,-10,2)='".$monthvalue."' AND (trans_id LIKE 'A%' OR trans_id LIKE 'CH%') ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') DESC";
				$res_emp_login_logout_time = mysql_query($sql_emp_login_logout_time); 
				while($row_emp_login_logout_time = mysql_fetch_array($res_emp_login_logout_time))
				{
					$visitdate=$row_emp_login_logout_time['visit_date'];	
					if($row_emp_login_logout_time['logindate']!='')
					{
					${logintime.$visitdate}=date('H:i:s',strtotime($row_emp_login_logout_time['logindate']));
					}
					//$sql_emp_logout_time=mysql_fetch_array(mysql_query("SELECT date FROM `location` where emp_code='".$emp_code."' and trans_id LIKE 'CH%' AND SUBSTRING(trans_id,-14,8)='".$final_date."'"));
					if($row_emp_login_logout_time['logoutdate']!='')
					{
					${logouttime.$visitdate}=date('H:i:s',strtotime($row_emp_login_logout_time['logoutdate']));
					}
					if(!in_array($visitdate,$visit_date_array))
					{
						array_push($visit_date_array,$visitdate);
					}
				}
				$sql_prod_nonprod_call = "SELECT COUNT(DISTINCT (CASE WHEN order_no LIKE 'O%' THEN CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) END)) AS productive_calls,
										COUNT(DISTINCT (CASE WHEN order_no LIKE 'NO%' THEN CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) END)) AS non_productive_calls,
										sum(amount) as AMT,DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') AS visit_date
										FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."'  AND 
										SUBSTRING(order_no,-14,4)='".$year."' AND SUBSTRING(order_no,-10,2)='".$monthvalue."' 
										GROUP BY DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') DESC";
				$res_prod_nonprod_call = mysql_query($sql_prod_nonprod_call); 
				while($row_prod_nonprod_call = mysql_fetch_array($res_prod_nonprod_call))
				{
					$visitdate=$row_prod_nonprod_call['visit_date'];	
					${prod_call.$visitdate} = $row_prod_nonprod_call['productive_calls'];
					${non_prod_call.$visitdate} = $row_prod_nonprod_call['non_productive_calls'];
					${sal_amt.$visitdate}=$row_prod_nonprod_call['AMT'];
					if(!in_array($visitdate,$visit_date_array))
					{
						array_push($visit_date_array,$visitdate);
					}
				}
				/*$sql_nonprod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,8)='".$final_date."'";
				$res_nonprod_call = mysql_query($sql_nonprod_call);
				$row_nonprod_call = mysql_fetch_array($res_nonprod_call);
				$non_prod_call = $row_nonprod_call['non_productive_calls'];*/
				
				/*$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];*/
				
				/*$sql_order_emp=mysql_fetch_array(mysql_query("SELECT sum(amount) as AMT FROM `order_details` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' and SUBSTRING(order_no,-14,8) ='".$final_date."'"));
				$sal_amt=$sql_order_emp['AMT'];*/
				
				foreach($visit_date_array as $visit_date_val)
				{
				$total_calls = ${prod_call.$visit_date_val} + ${non_prod_call.$visit_date_val};
				$gtotal_calls=$gtotal_calls+$total_calls;
				$gprod_call=${prod_call.$visit_date_val}+$gprod_call;
				$gtamt=$gtamt+${sal_amt.$visit_date_val};
				
				/*----> Productive Calls % <----*/
				echo "<tr>
						<td>".date('d-m-Y',strtotime($visit_date_val))."</td>
						<td align=\"right\">".$total_calls."</td>
						<td align=\"right\">".${prod_call.$visit_date_val}."</td>
						<td >".${logintime.$visit_date_val}."</td>
						<td >".${logouttime.$visit_date_val}."</td>
						<td align=\"right\">".number_format(${sal_amt.$visit_date_val},2)."</td>
					  </tr>";
				
				}
			?>
            <tr>
            <td>Total</td>
            <td align="right"><?php echo $gtotal_calls;?></td>
            <td align="right"><?php echo $gprod_call;?></td>
            <td></td>
            <td></td>
            <td align="right"><?php echo number_format($gtamt,2) ?></td>
            </tr>
            </table>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
           



