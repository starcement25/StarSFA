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
	$branchcondition=' WHERE 1';
}
else
{
	$branchcondition=" WHERE branch_code='".$branch."'";
}
$competitor_name_array = array();
$sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master WHERE acedns='yes' and group_name='WSP' ORDER BY competitor_name ASC";
$res_competitor_name = mysql_query($sql_competitor_name);
$countcompetitor=mysql_num_rows($res_competitor_name);
$colspanheader=10+($countcompetitor*4);
?>
<table border="1" style="border-collapse:collapse;" class="border" width="150%">
<tr class="TDHEAD"><td colspan="<?php echo $colspanheader;?>" align="center">Market Feedback Details - WSP</td></tr>
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
while($row_competitor_name = mysql_fetch_array($res_competitor_name)){
	$competitor_name = $row_competitor_name['competitor_name'];
	echo "<td align=\"center\">".$competitor_name."</td>";
	array_push($competitor_name_array,$competitor_name);
	$competitor_string .= "'".$competitor_name."',";
}
$competitor_string = rtrim($competitor_string,",");
//print_r($competitor_name_array);
?>
  </tr>
   <!--tr class="TDHEAD_SUB">
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td-->
<?php /*foreach($competitor_name_array as  $competitorheaderval){
	//echo "<td>Billing Ex</td>";
	//echo "<td>WSP EX</td>";
	echo "<td>WSP</td>";
	//echo "<td>NOD</td>";
}*/
$count=1;
$sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,CM.dns_customer_code,RM.route_name,EM.emp_name,EM.dns_emp_code,
						MF.PTD,MF.PTR,MF.PTC,MF.PV,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,BM.branch_name,
						MF.competitor_name 
						FROM market_feedback MF,customer_master CM,employee_master EM,route_master RM,branch_master BM 
						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND 
						SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND  SUBSTRING(MF.market_feedback_id,3,5)  IN (".$employee_arg.") AND 
						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
						MF.competitor_name IN (".$competitor_string .") AND CM.branch_code=BM.branch_code AND MF.PTR >0
						GROUP BY DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,MF.competitor_name ASC";
$res_competitor_stock = mysql_query($sql_competitor_stock);
$count_competitor_stock=mysql_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
	$emp_code_array=array();
	$customer_emp_date_array=array();
	$date_array=array();
while($row_competitor_stock = mysql_fetch_array($res_competitor_stock)){
	$dns_emp_code = $row_competitor_stock['dns_emp_code'];
	$visit_date = $row_competitor_stock['visit_date'];
	$dns_customer_code = $row_competitor_stock['dns_customer_code'];
	$emp_name = $row_competitor_stock['emp_name'];
	${'emp_name'.$dns_emp_code.$dns_customer_code.$visit_date}=$emp_name;
	${'customer_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['customer_name'];
	${'phone_no'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['phone_no'];
	${'cust_type'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['cust_type'];
	${'route_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['route_name'];
	${'competitor_name_db'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['competitor_name'];
	${'PTD'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
	${'PTR'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
	${'PTC'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
	${'PV'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];
	
	${'branch_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['branch_name'];
	$customer_emp_date_string=$dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
	
	if(!in_array($customer_emp_date_string,$customer_emp_date_array))
	{
		array_push($customer_emp_date_array,$customer_emp_date_string);
	}
	
}
	for($i=0;$i < count($customer_emp_date_array);$i++)
	{
		$customer_emp_date_string_val=explode("#",$customer_emp_date_array[$i]);
		$dns_emp_code_val=$customer_emp_date_string_val[0];
		$dns_customer_code_val=$customer_emp_date_string_val[1];
		$date_val=$customer_emp_date_string_val[2];
		
		echo "<tr>
			<td>".$count."</td>
			<td>".$date_val."</td>
			<td>".$dns_emp_code_val."</td>
			<td>".${'emp_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
			<td>".${'branch_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
			<td>".${'cust_type'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
			<td>".$dns_customer_code_val."</td>
			<td>".${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val} ."</td>
			<td>".${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
			<td>".${'phone_no'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
			";
	foreach($competitor_name_array as  $competitorval){
		if($competitorval == ${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval}){
			$competitor_name_val=${'competitor_name_db'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitorval};
			$total_PTD=${'PTD'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
			$total_PTR=${'PTR'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
			$total_PTC=${'PTC'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
			$total_PV=${'PV'.$dns_emp_code_val.$dns_customer_code_val.$date_val.$competitor_name_val};
		}
		else
		{
			$total_PTD='-';
			$total_PTR='-';	
			$total_PTC='-';	
			$total_PV='-';		
		}
		echo "<td align=\"right\">".$total_PTR."</td>";	
	}	
	
	echo "</tr>";
	$count++;
	}
}
else
{
	echo "<tr><td colspan=".$colspanheader.">No Records found</td></tr>";
}
mysql_close($link);
?>
</table>
