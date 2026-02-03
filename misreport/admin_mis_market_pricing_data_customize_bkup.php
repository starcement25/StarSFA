<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$branch = $_REQUEST['branch'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
if($branch=="all")
{
	$branchcondition='';
}
else
{
	$branchcondition=" WHERE branch_code='".$branch."'";
}
$competitor_name_array = array();
$sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master $branchcondition ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
$res_competitor_name = mysqli_query($link,$sql_competitor_name);
$countcompetitor=mysqli_num_rows($res_competitor_name);
$colspanheader=10+($countcompetitor*4);
?>
<table border="1" style="border-collapse:collapse;" class="border" width="150%">
<tr class="TDHEAD"><td colspan="<?php echo $colspanheader;?>" align="center">Market Feedback Details</td></tr>
  <tr class="TDHEAD_SUB">
  	<td>SI. No</td>
  	<td>Date of Visit</td>
    <td>Emp Code</td>
    <td>Emp Name</td>
    <td>Branch</td>
    <td>Cust Category</td>
    <td>Cust Code</td>
    <td>Cust Name</td>
    <td>Route Name</td>
    <td>Contact No</td>
<?php
$competitor_name_array=array();
while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
	$competitor_name = $row_competitor_name['competitor_name'];
	echo "<td colspan=\"4\" align=\"center\">".$competitor_name."</td>";
	array_push($competitor_name_array,$competitor_name);
	$competitor_string .= "'".$competitor_name."',";
}
$competitor_string = rtrim($competitor_string,",");
//print_r($competitor_name_array);
?>
  </tr>
   <tr class="TDHEAD_SUB">
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
<?php foreach($competitor_name_array as  $competitorheaderval){
	echo "<td>Billing Ex</td>";
	echo "<td>WSP EX</td>";
	echo "<td>RSP EX</td>";
	echo "<td>NOD</td>";
}
$count=1;
$sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
						MF.PTD,MF.PTR,MF.PTC,MF.PV,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
						MF.competitor_name 
						FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
						SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND  SUBSTRING(MF.market_feedback_id,3,5)  IN (".$employee_arg.") AND 
						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
						MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code 
						GROUP BY DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,MF.competitor_name ASC";
$res_competitor_stock = mysqli_query($link,$sql_competitor_stock);
$count_competitor_stock=mysqli_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
while($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)){
	$emp_name = $row_competitor_stock['emp_name'];
	$dns_emp_code = $row_competitor_stock['dns_emp_code'];
	$customer_name = $row_competitor_stock['customer_name'];
	$phone_no = $row_competitor_stock['phone_no'];
	$cust_type = $row_competitor_stock['cust_type'];
	$dns_customer_code = $row_competitor_stock['dns_customer_code'];
	$route_name = $row_competitor_stock['route_name'];
	$competitor_name_db = $row_competitor_stock['competitor_name'];
	${PTD.$competitor_name_db} = $row_competitor_stock['PTD'];
	${PTR.$competitor_name_db} = $row_competitor_stock['PTR'];
	${PTC.$competitor_name_db} = $row_competitor_stock['PTC'];
	${PV.$competitor_name_db} = $row_competitor_stock['PV'];
	$visit_date = $row_competitor_stock['visit_date'];
	$branch_name = $row_competitor_stock['branch_name'];
	
	/*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysqli_query($link,$sql_emp_details);
	$row_emp_details = mysqli_fetch_assoc($res_emp_details);
	$emp_name = $row_emp_details['emp_name'];
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	
	$sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_details = mysqli_query($link,$sql_customer_details);
	$row_customer_details = mysqli_fetch_assoc($res_customer_details);
	$customer_name = $row_customer_details['customer_name'];
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$cust_type = $row_customer_details['cust_type'];*/
	echo "<tr>
			<td>".$count."</td>
			<td>".$visit_date."</td>
			<td>".$dns_emp_code."</td>
			<td>".$emp_name."</td>
			<td>".$branch_name."</td>
			<td>".$cust_type."</td>
			<td>".$dns_customer_code."</td>
			<td>".$customer_name."</td>
			<td>".$route_name."</td>
			<td>".$phone_no."</td>
			";
	foreach($competitor_name_array as  $competitorval){
		if($competitorval == $competitor_name_db){
			$total_PTD=${PTD.$competitor_name_db};
			$total_PTR=${PTR.$competitor_name_db};
			$total_PTC=${PTC.$competitor_name_db};
			$total_PV=${PV.$competitor_name_db};
		}
		else
		{
			$total_PTD='-';
			$total_PTR='-';	
			$total_PTC='-';	
			$total_PV='-';		
		}
		echo "<td align=\"right\">".$total_PTD."</td>";
		echo "<td align=\"right\">".$total_PTR."</td>";	
		echo "<td align=\"right\">".$total_PTC."</td>";	
		echo "<td align=\"right\">".$total_PV."</td>";		
	}	
	
	echo "</tr>";
	$count++;
}
}
else
{
	echo "<tr><td colspan=".$colspanheader.">No Records found</td></tr>";
}
mysqli_close($link);
?>
</table>
