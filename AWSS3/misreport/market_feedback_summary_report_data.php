<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);
$competitor_name_array = array();
$sql_competitor_name = "SELECT DISTINCT competitor_name FROM competitor_group_master 
ORDER BY FIELD(competitor_name, 'Black Tiger','STAR PPC','STAR ANTIRUST','DALMIA','DALMIA DSP','TOPCEM','TOPCEM SDC','AMRIT','Max',
'ULTRATECH','BIRLA GOLD','Others','Dalmia Cement','Star Cement','Suryagold')  ASC";
$res_competitor_name = mysql_query($sql_competitor_name);
$countcompetitor=mysql_num_rows($res_competitor_name);
$colspanheader=4+($countcompetitor*4);
?>
<table border="1" style="border-collapse:collapse;" class="border" width="150%">
<tr class="TDHEAD"><td colspan="<?php echo $colspanheader;?>" align="center">Market Feedback Summary Details</td></tr>
  <tr class="TDHEAD_SUB">
  	<td>SI. No</td>
  	<td>Date</td>
    <td>Customer Name</td>
    <td>Area</td>
<?php
$competitor_name_array=array();
while($row_competitor_name = mysql_fetch_array($res_competitor_name)){
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
<?php foreach($competitor_name_array as  $competitorheaderval){
	echo "<td>Billing - EX</td>";
	echo "<td>WSP - EX</td>";
	echo "<td>RSP - EX</td>";
	echo "<td>NOD - EX</td>";
}
$count=1;
$sql_competitor_stock = "SELECT CM.customer_name,CM.phone_no,CM.cust_type,MF.customer_code,RM.route_name,
						MF.PTD,MF.PTR,MF.PTC,MF.PV,DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,
						MF.competitor_name, SUBSTRING(MF.market_feedback_id,3,5) AS emp_code
						FROM market_feedback MF,customer_master CM,route_master RM
						WHERE MF.customer_code=CM.customer_code AND CM.route_code=RM.route_code AND  SUBSTRING(MF.market_feedback_id,3,5)  
						IN ('".$employee_arg."') AND 
						(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
						MF.competitor_name IN (".$competitor_string .") 
						GROUP BY DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d'),MF.customer_code,MF.competitor_name ORDER BY 
						DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') DESC,FIELD(competitor_name, 'Black Tiger','STAR PPC','STAR ANTIRUST','DALMIA','DALMIA DSP','TOPCEM','TOPCEM SDC','AMRIT','Max',
'ULTRATECH','BIRLA GOLD','Others','Dalmia Cement','Star Cement','Suryagold')  ASC";
$res_competitor_stock = mysql_query($sql_competitor_stock);
$count_competitor_stock=mysql_num_rows($res_competitor_stock);
if($count_competitor_stock >0){
	$emp_code_array=array();
	$customer_emp_date_array=array();
	$date_array=array();
while($row_competitor_stock = mysql_fetch_array($res_competitor_stock)){
	$visit_date = $row_competitor_stock['visit_date'];
	$dns_customer_code = $row_competitor_stock['customer_code'];
		$dns_emp_code= $row_competitor_stock['emp_code'];
	${'customer_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['customer_name'];
	${'phone_no'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['phone_no'];
	${'cust_type'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['cust_type'];
	${'route_name'.$dns_emp_code.$dns_customer_code.$visit_date} = $row_competitor_stock['route_name'];
	${'competitor_name_db'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['competitor_name'];
	${'PTD'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTD'];
	${'PTR'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTR'];
	${'PTC'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PTC'];
	${'PV'.$dns_emp_code.$dns_customer_code.$visit_date.$row_competitor_stock['competitor_name']} = $row_competitor_stock['PV'];
	
	$customer_emp_date_string=$dns_emp_code.'#'.$dns_customer_code.'#'.$visit_date;
	
	/*$total_quantity = ($star + $ambuja + $ultratech + $lafarge + $dalmia + $topcem + $acc + $birla_gold);
	
	$sql_emp_details = "SELECT emp_name, dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_details = mysql_query($sql_emp_details);
	$row_emp_details = mysql_fetch_array($res_emp_details);
	$emp_name = $row_emp_details['emp_name'];
	$dns_emp_code = $row_emp_details['dns_emp_code'];
	
	$sql_customer_details = "SELECT customer_name, dns_customer_code, cust_type FROM customer_master WHERE customer_code = '".$customer_code."'";
	$res_customer_details = mysql_query($sql_customer_details);
	$row_customer_details = mysql_fetch_array($res_customer_details);
	$customer_name = $row_customer_details['customer_name'];
	$dns_customer_code = $row_customer_details['dns_customer_code'];
	$cust_type = $row_customer_details['cust_type'];*/
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
			<td>".${'customer_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val} ."</td>
			<td>".${'route_name'.$dns_emp_code_val.$dns_customer_code_val.$date_val}."</td>
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
mysql_close($link);
?>
</table>
<br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
