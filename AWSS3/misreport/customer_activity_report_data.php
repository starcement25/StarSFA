<?php
ob_start();
session_start();
require("adminUtils.php");

$employee_id = $_REQUEST['employee_id'];
$route = $_REQUEST['route'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
/*if($employee_id=='all')
{
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,-19,5) IN(".$emp_hierarchy_value.")";
	}
}
else
{
	$emp_hierarchy_value_condition = " AND SUBSTRING(POCM.order_no,-19,5)='".$employee_id."'";
}*/
if($route=='all')
{
$sqlqueryemproute="SELECT DISTINCT RM.route_code,RM.route_name FROM customer_route_emp_relation CM,route_master RM 
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
								CM.emp_code IN(".$employee_id.") ORDER BY RM.route_name ASC";
    $resultqueryemproute = mysql_query($sqlqueryemproute);
	$countqueryemproute=mysql_num_rows($resultqueryemproute);
	if($countqueryemproute>0){
		while($rowqueryemproute = mysql_fetch_array($resultqueryemproute))
		{
			$route_name=$rowqueryemproute['route_name'];
			$route_code=$rowqueryemproute['route_code'];
			$route_code_string .= "'".$route_code."',";
		}
		$route_code_string = rtrim($route_code_string,",");
	}
}
else $route_code_string =$route;


			
		$sql_exists_customer = "SELECT CM.route_code,CM.customer_code FROM customer_master CM, order_header OH 
							WHERE OH.customer_code=CM.customer_code AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') 
							BETWEEN '".$start_date."' AND '".$end_date."' AND CM.route_code IN(".$route_code_string.") 
							GROUP BY CM.customer_code";
		$res_exists_customer = mysql_query($sql_exists_customer);
		$customer_code_exists_array=array();
		while($row_exists_customer = mysql_fetch_array($res_exists_customer))
		{
			$customer_code_value=$row_exists_customer['customer_code'];
			if(!in_array($customer_code_value,$customer_code_exists_array))
			{
				array_push($customer_code_exists_array,$customer_code_value);
			}
		}
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1" id="display_table">
    <tr class='TDHEAD'><td colspan='1' align='center'>Customer Activity Report</td></tr>
      <tr class="TDHEAD_SUB">
      	<td>Customer Name</td>
      </tr>
    <?
	$sql_activity="SELECT CM.route_code,CM.customer_name,CM.customer_code FROM customer_master CM WHERE CM.route_code IN(".$route_code_string.") ORDER BY CM.customer_name ASC";
	$res_activity = mysql_query($sql_activity);
	$cnt_activity=mysql_num_rows($res_activity);
	if($cnt_activity >0){
		$count=1;
	while($row_activity = mysql_fetch_array($res_activity)){
		
		$customer_name = $row_activity['customer_name'];
		$customer_code_all = $row_activity['customer_code'];
		$color_name_exists='green';
		$color_name_not_exists='red';
		if(in_array($customer_code_all,$customer_code_exists_array))
		{
			echo "<tr><td style='color:$color_name_exists'><b>".$customer_name."</b></td></tr>";
		}
		else
		{
			echo "<tr><td style='color:$color_name_not_exists'><b>".$customer_name."</b></td></tr>";
		}
	  }
	}
	else{
	echo "<tr><td colspan='2'>No Records</td></tr>";
   }
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
//}
mysql_close($link);
?>