<?php
//ob_start();
session_start();
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
require("adminUtils.php");
require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

$current_date = date('Y-m-d');
$month_date = date('Y-m');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$previous_year_date = $previous_year."-04-01";
}
else{
	$previous_year_date = date('Y-04-01');
}
$employee = $_REQUEST['employee'];
//$emp_hierarchy=return_employee_hierarchy(str_replace("'","",$employee));
$emp_hierarchy_condition_YCD = " AND SUBSTRING(YCD.yellow_card_no,2,5) IN(".$employee.") ";

$month_data = $_REQUEST['month_data'];

$year_month_split = explode("-",$month_data);
$monthNum  = $year_month_split[1];
$year = $year_month_split[0];
$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));

/*if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition_one=' AND LO.emp_code IN('.$employee.')';
}*/

$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$branch = $_REQUEST['branch'];
$department = $_REQUEST['department'];

if(strpos($zone,",") == FALSE)	$zone = str_replace("'","",$zone);
else								$zone = "All";

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($branch,",") == FALSE)	{
	$branch = str_replace("'","",$branch);
	if($branch=='all')
	{
		$branch_condition="";
	}
	else
	{
	$branch_condition=" AND CM.branch_code IN('".$branch."')";
	}
}
else{								
$branch = "All";
$branch_condition="";
}

if(strpos($department,",") == FALSE)	$department = str_replace("'","",$department);
else									$department = "All";

$subdealer=$_REQUEST['subdealer'];

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
/*
$sql_emp_branch = "SELECT branch_code FROM employee_master WHERE emp_code IN(".$employee.")";
$res_emp_branch = mysqli_query($link,$sql_emp_branch);
$row_emp_branch = mysqli_fetch_assoc($res_emp_branch);
$branch_emp_code = $row_emp_branch['branch_code'];

$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_emp_code."'";
$res_branch_name = mysqli_query($link,$sql_branch_name);
$row_branch_name = mysqli_fetch_assoc($res_branch_name);
$branch_name = $row_branch_name['branch_name'];*/
$month_data_parts=substr($month_data,5,2);
if($month_data_parts==1)
{
	$column_target='jan_31_target';
	$column_ach='jan_31_achievement';
}
if($month_data_parts==2)
{
	$column_target='feb_28_target';
	$column_ach='feb_28_achievement';
}
if($month_data_parts==3)
{
	$column_target='mar_31_target';
	$column_ach='mar_31_achievement';
}
if($month_data_parts==4)
{
	$column_target='apr_30_target';
	$column_ach='apr_30_achievement';
}
if($month_data_parts==5)
{
	$column_target='may_31_target';
	$column_ach='may_31_achievement';
}
if($month_data_parts==6)
{
	$column_target='jun_30_target';
	$column_ach='jun_30_achievement';
}
if($month_data_parts==7)
{
	$column_target='jul_31_target';
	$column_ach='jul_31_achievement';
}
if($month_data_parts==8)
{
	$column_target='aug_31_target';
	$column_ach='aug_31_achievement';
}
if($month_data_parts==9)
{
	$column_target='sep_30_target';
	$column_ach='sep_30_achievement';
}
if($month_data_parts==10)
{
	$column_target='oct_31_target';
	$column_ach='oct_31_achievement';
}
if($month_data_parts==11)
{
	$column_target='nov_30_target';
	$column_ach='nov_30_achievement';
}
if($month_data_parts==12)
{
	$column_target='dec_31_target';
	$column_ach='dec_31_achievement';
}
$header_string = "Zone:".$zone."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month: ".$monthName." ".$year;

$sql_yellow_card_details = "SELECT DATE_FORMAT(SUBSTRING(YCD.yellow_card_no,-14,8),'%d-%m-%Y') AS entry_date, SUBSTRING(YCD.yellow_card_no,-19,5) AS emp_code, YCD.yellow_card_no, DATE_FORMAT(YCD.challan_date,'%d-%m-%Y') AS chall_date, YCD.challan_no,YCD.qty,YCD.qty_UOM,EM.emp_name,
CM.customer_name,YCD.linked_dealer_code AS rds_tag,CM.dns_customer_code,CM.customer_code,SUBSTRING_INDEX(CM.branch_code,',',1) AS branch_code,CM.bank_name,CM.bank_account_number,CM.beneficiary_name,CM.IFS_code FROM yellow_card_details YCD INNER JOIN customer_master CM 
 INNER JOIN employee_master EM 
WHERE YCD.customer_code=CM.customer_code  AND 
SUBSTRING(YCD.yellow_card_no,2,5)=EM.emp_code AND  
YCD.customer_code IN(".$subdealer.") AND 
SUBSTRING(YCD.challan_date,1,7) = '".$month_data."' ".$emp_hierarchy_condition_YCD." ".$branch_condition." ORDER BY rds_tag ASC,customer_code ASC";
$res_yellow_card_details = mysqli_query($link,$sql_yellow_card_details);
$yellowcard_row_check = mysqli_num_rows($res_yellow_card_details);
if($yellowcard_row_check>0){
	?>
    <table class="border" width="100%">
      <tr class="TDHEAD">
      	<td><?php echo $header_string; ?></td>
      </tr>
    </table>
    <br /><br />
    <div id="display_final">
    <table width="100%" style="border-collapse:collapse;" border="1"  cellpadding="2px">
      <tr class="TDHEAD" align="center">
        <td>Linked Dealer Code</td>
        <td>Linked Dealer Name</td>
        <td>Sub Dealer Code</td>
        <td>Sub Dealer Name</td>
        <td>Branch</td>
        <td>SI</td>
        <td>Challan Date</td>
        <td>Challan No.</td>
        <td>PPC</td>
        <td>PSC</td>
        <td>ARC</td>
        <td>OPC</td>
        <td>Total(Bags)</td>
        <td>Sign &amp; Date Of Visit By Co. Officer</td>
        <td>A/c No</td>
        <td>Name Of Beneficiary</td>
        <td>IFS Code</td>
        <td>Bank Name</td>
      </tr>
    <?php
	$count = 1;
	$ppc_total = 0;
	$psc_total = 0;
	$arc_total = 0;
	$opc_total = 0;
	$grand_total = 0;
	
	$res_yellow_card_details = mysqli_query($link,$sql_yellow_card_details);
	$sub_dealer_array=array();
	$dealer_array=array();
	while($row_yellow_card_details = mysqli_fetch_assoc($res_yellow_card_details)){
		$entry_date = $row_yellow_card_details['entry_date'];
		$yc_emp_code = $row_yellow_card_details['emp_code'];
		$yello_card_no = $row_yellow_card_details['yello_card_no'];
		//$chall_date = date('m-d-Y',strtotime($row_yellow_card_details['chall_date']));
		$chall_date = $row_yellow_card_details['chall_date'];
		$challan_no = $row_yellow_card_details['challan_no'];
		$qty = $row_yellow_card_details['qty'];
		$qty_UOM = $row_yellow_card_details['qty_UOM'];
		$yc_emp_name = $row_yellow_card_details['emp_name'];
		//$yc_branch_name=$row_yellow_card_details['branch_name'];
		$yc_branch_code=$row_yellow_card_details['branch_code'];
		
		$yc_customer_name=$row_yellow_card_details['customer_name'];
		$yc_dns_customer_code=$row_yellow_card_details['dns_customer_code'];
		$yc_customer_code=$row_yellow_card_details['customer_code'];
		$yc_rds_tag=$row_yellow_card_details['rds_tag'];
		$bank_name=$row_yellow_card_details['bank_name'];
		$bank_account_number=$row_yellow_card_details['bank_account_number'];
		$beneficiary_name=$row_yellow_card_details['beneficiary_name'];
		$IFS_code=$row_yellow_card_details['IFS_code'];
		
		$sql_dealer="SELECT customer_code,dns_customer_code,customer_name FROM customer_master WHERE customer_code='".$yc_rds_tag."'";
		$rs_dealer=mysqli_query($link,$sql_dealer);
		$row_dealer=mysqli_fetch_assoc($rs_dealer);
		$dealer_code=$row_dealer['dns_customer_code'];
		$dealer_name=$row_dealer['customer_name'];
		
		$sqlbranch="SELECT branch_name FROM branch_master WHERE branch_code='".$yc_branch_code."'";
		$rsbranch=mysqli_query($link,$sqlbranch);
		$rowbranch=mysqli_fetch_assoc($rsbranch);
		$yc_branch_name=$rowbranch['branch_name'];
		
		$sqltargetach="SELECT $column_ach FROM self_appraisal_customer_wise WHERE customer_code='".$prev_dealer_code."'";
		$rstargetach=mysqli_query($link,$sqltargetach);
		$rowtargetach=mysqli_fetch_assoc($rstargetach);
		${'dealer_ach'.$dealer_code}=$rowtargetach[$column_ach];
		//${dealer_ach.$dealer_code}='';

		if($qty_UOM == 'PPC'){
			${'ppc_total'.$yc_dns_customer_code} += $qty;
			${'ppc_total'.$dealer_code} += $qty;
		}
		if($qty_UOM == 'PSC'){
			${'psc_total'.$yc_dns_customer_code} += $qty;
			${'psc_total'.$dealer_code} += $qty;
		}
		if($qty_UOM == 'ARC'){
			${'arc_total'.$yc_dns_customer_code} += $qty;
			${'arc_total'.$dealer_code} += $qty;
		}
		if($qty_UOM == 'OPC'){
			${'opc_total'.$yc_dns_customer_code} += $qty;
			${'opc_total'.$dealer_code} += $qty;
		}
		${'grand_total'.$yc_dns_customer_code} += $qty;
		${'grand_total'.$dealer_code} += $qty;
		if(!isset(${'countrec'.$yc_dns_customer_code}))  ${'countrec'.$yc_dns_customer_code}=1;
		
		
		/*${dealer_code.$yc_dns_customer_code.$dealer_code}=$dealer_code;
		${dealer_name.$yc_dns_customer_code.$dealer_code}=$dealer_name;
		${sub_dealer_code.$yc_dns_customer_code.$dealer_code}=$yc_dns_customer_code;
		${sub_dealer_name.$yc_dns_customer_code.$dealer_code}=$yc_customer_name;
		${branch_name.$yc_dns_customer_code.$dealer_code}=$yc_branch_name;
		${chall_date.$yc_dns_customer_code.$dealer_code}=$chall_date;
		${challan_no.$yc_dns_customer_code.$dealer_code}=$challan_no;
		${dealer_code.$yc_dns_customer_code.$dealer_code}=$dealer_code;
		${dealer_code.$yc_dns_customer_code.$dealer_code}=$dealer_code;*/
		
		if(($yc_customer_name!=$prev_sub_dealer_name) && $prev_sub_dealer_name!='')
		{
			echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>".$prev_sub_dealer_name." TOTAL</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"><b>".${'ppc_total'.$prev_sub_dealer_code}."</b></td>
				<td align=\"right\"><b>".${'psc_total'.$prev_sub_dealer_code}."</b></td>
				<td align=\"right\"><b>".${'arc_total'.$prev_sub_dealer_code}."</b></td>
				<td align=\"right\"><b>".${'opc_total'.$prev_sub_dealer_code}."</b></td>
				<td align=\"right\"><b>".${'grand_total'.$prev_sub_dealer_code}."</b></td>
				<td align=\"center\"></td>";
		}
		if(($dealer_code!=$prev_dealer_code) && $prev_dealer_code!='')
		{
			echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>TOTAL SUBDEALER SALE</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"><b>".${'grand_total'.$prev_dealer_code}."</b></td>
				<td align=\"center\"></td>";
			if(strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START'){		
				echo "<tr>
					<td align=\"left\"></td>
					<td align=\"left\"></td>
					<td align=\"left\"></td>
					<td align=\"left\"><b>".$prev_dealer_name." TOTAL</b></td>
					<td align=\"left\"></td>
					<td></td>
					<td align=\"center\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".(${'dealer_ach'.$dealer_code}*20)."</b></td>
					<td align=\"center\"><b>".round((${'grand_total'.$prev_dealer_code}/(${'dealer_ach'.$dealer_code}*20)*100),2)."%</b></td>";
			}
			else
			{
				echo "<tr>
					<td align=\"left\"></td>
					<td align=\"left\"></td>
					<td align=\"left\"></td>
					<td align=\"left\"><b>".$prev_dealer_name." TOTAL</b></td>
					<td align=\"left\"></td>
					<td></td>
					<td align=\"center\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"></td>
					<td align=\"right\"><b>".${'grand_total'.$prev_dealer_code}."</b></td>
					<td align=\"center\"><b>".round((${'grand_total'.$prev_dealer_code}/(${'dealer_ach'.$dealer_code}*20)*100),2)."%</b></td>";
				
			}
		}
				
		echo "<tr>
				<td align=\"left\">".$dealer_code."</td>
				<td align=\"left\">".$dealer_name."</td>
				<td align=\"left\">".$yc_dns_customer_code."</td>
				<td align=\"left\">".$yc_customer_name."</td>
				<td align=\"left\">".$yc_branch_name."</td>
				<td>".${'countrec'.$yc_dns_customer_code}."</td>
				<td align=\"center\">".$chall_date."</td>
				<td align=\"right\">".$challan_no."</td>";
		
		if($qty_UOM == 'PPC'){
			echo "<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>";
		}
		else if($qty_UOM == 'PSC'){
			echo "<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>";
		}
		else if($qty_UOM == 'ARC'){
			echo "<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>
					<td align=\"center\">-</td>";
			
		}
		else if($qty_UOM == 'OPC'){
			echo "<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"center\">-</td>
					<td align=\"right\">".$qty."</td>";
		}
		
		
		echo "<td align=\"right\">".$qty."</td>
			  <td align=\"center\">".$yc_emp_name." ".$entry_date."</td>
			  <td align=\"center\">".$bank_account_number."</td>
			  <td align=\"center\">".$beneficiary_name."</td>
			  <td align=\"center\">".$IFS_code."</td>
			  <td align=\"center\">".$bank_name."</td>
			  </tr>";
			  
		if($yellowcard_row_check==$count)
		{
			echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>".$yc_customer_name." TOTAL</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\">".${'ppc_total'.$yc_dns_customer_code}."</td>
				<td align=\"right\">".${'psc_total'.$yc_dns_customer_code}."</td>
				<td align=\"right\">".${'arc_total'.$yc_dns_customer_code}."</td>
				<td align=\"right\">".${'opc_total'.$yc_dns_customer_code}."</td>
				<td align=\"right\">".${'grand_total'.$yc_dns_customer_code}."</td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				";
				
			echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>TOTAL SUBDEALER SALE</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"><b>".${'grand_total'.$dealer_code}."</b></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				";
				if(strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START'){
			echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>".$dealer_name." TOTAL</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"><b>".(${'dealer_ach'.$dealer_code}*20)."</b></td>
				<td align=\"center\"><b>".round((${'grand_total'.$dealer_code}/(${'dealer_ach'.$dealer_code}*20)*100),2)."%</b></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				";	
				}
				else
				{
					echo "<tr>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"></td>
				<td align=\"left\"><b>".$dealer_name." TOTAL</b></td>
				<td align=\"left\"></td>
				<td></td>
				<td align=\"center\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"></td>
				<td align=\"right\"><b>".${'grand_total'.$dealer_code}."</b></td>
				<td align=\"center\"><b>".round((${'grand_total'.$dealer_code}/(${'dealer_ach'.$dealer_code}*20)*100),2)."%</b></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				<td align=\"center\"></td>
				";	
				}
				
		}
		$prev_sub_dealer_name=$yc_customer_name;
		$prev_sub_dealer_code=$yc_dns_customer_code;
		$prev_dealer_name=$dealer_name;
		$prev_dealer_code=$dealer_code;
		array_push($sub_dealer_array,$yc_customer_code);
		${'countrec'.$yc_dns_customer_code}++;
		
		$count++;
	}
	?>
    </table>
    </div>
    <br />
    <div style="width:65%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
    <?php
}
else{
	echo "No Records Found";
}

//$emp_hierarchy_condition_one = ' AND SUBSTRING(OH.order_no,2,5) IN('.$employee.') ';
?>


