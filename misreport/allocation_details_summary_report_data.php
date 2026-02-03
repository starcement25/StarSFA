<?php
//ob_start();
session_start();
/*ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
require("adminUtils.php");
$allocation_details = "allocation_details";
$branch_master = "branch_master";
$customer_master="customer_master";
define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");
	//mysqli_select_db(DBREMOTE,$link) or die("could not connect the database");

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

/*$subdealer_parts=str_replace("'","",$subdealer);
$subdealer_parts=explode(",",$subdealer_parts);
$subdealer_sap_code="";
foreach($subdealer_parts as $subdealer_parts_val)
{
	echo $sub_subdealer_SAP_code="select customer_id FROM $customer_master where customer_code='$subdealer_parts_val'";
	$res_subdealer_SAP_code=mysqli_query($link,$sub_subdealer_SAP_code);
	$row_subdealer_SAP_code=mysqli_fetch_array($res_subdealer_SAP_code);
	$sub_dealer_sap_cod_val = $row_subdealer_SAP_code["customer_id"];
	$subdealer_sap_code=$subdealer_sap_code."'".$sub_dealer_sap_cod_val."'".',';
}
$subdealer_sap_code=substr($subdealer_sap_cod,0,-1);*/

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

$sql_allocation_details = "select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code and $allocation_details.sub_dealer_id IN(".$subdealer.") AND 
DATE_FORMAT($allocation_details.`date_and_time`, '%Y%m') = '".$month_data."' order by $allocation_details.`customer_id` asc,$allocation_details.`APPORDERNO` asc,$allocation_details.`dns_prod_code` asc,$allocation_details.`dispatch_date` asc";
/*$sql_allocation_details = "select allocation_details.*,allocation_details.allocation_qty AS total_allocation_qty,customer_master.dns_customer_code,customer_master.customer_name,branch_master.branch_name from allocation_details,customer_master,branch_master Where allocation_details.customer_id=customer_master.customer_id AND customer_master.branch_code=branch_master.branch_code and allocation_details.sub_dealer_id IN('1500016303','1500000103') AND DATE_FORMAT(allocation_details.`date_and_time`, '%Y%m') = '202409'";*/
$res_allocation_details = mysqli_query($link,$sql_allocation_details);
$allocation_row_check = mysqli_num_rows($res_allocation_details);
if($allocation_row_check>0){
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
		   <td>Allocation&nbsp;Date&nbsp;Time</td>
		   <td>APPORDERNO</td>
		  <td>Dispatch&nbsp;Date</td>
        <td>Linked Dealer Code</td>
		 <td>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</td> 
        <td>Linked Dealer Name</td>
        <td>Sub Dealer Code</td>
		  <td>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</td> 
        <td>Sub Dealer Name</td>
        <td>Branch</td>
        <td>Month</td>
		<td>Product&nbsp;Name</td>
		<td>Total Dispatch qty</td>
		<td>Allocated qty.</td>
		<td>Remaining Allocation qty.</td>
		<td>Challan&nbsp;No.</td>
      </tr>
    <?php
	$count = 1;
	$ppc_total = 0;
	$psc_total = 0;
	$arc_total = 0;
	$opc_total = 0;
	$grand_total = 0;
	
	$res_allocation_details = mysqli_query($link,$sql_allocation_details);
	$sub_dealer_array=array();
	$dealer_array=array();
	while($row1= mysqli_fetch_assoc($res_allocation_details)){
		$APPORDERNO = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
										$date_and_time = $row1["date_and_time"] ? trim($row1["date_and_time"]) : "";
										$linked_dealer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
										$linked_dealer_sap_code = $row1["customer_id"] ? trim($row1["customer_id"]) : "";
										$linked_dealer_name = $row1["customer_name"] ? trim($row1["customer_name"]) : "";
										$sub_dealer_rssd_sap_code = $row1["sub_dealer_id"] ? trim($row1["sub_dealer_id"]) : "";
										$sub_dealer_details="select dns_customer_code,customer_name FROM $customer_master where customer_id='$sub_dealer_rssd_sap_code'";
										$res_sub_dealer_details=mysqli_query($link,$sub_dealer_details);
										$row_sub_dealer_details=mysqli_fetch_array($res_sub_dealer_details);
										$sub_dealer_rssd_code = $row_sub_dealer_details["dns_customer_code"]? trim($row_sub_dealer_details["dns_customer_code"]) : "";
										$sub_dealer_rssd_name = $row_sub_dealer_details["customer_name"] ? trim($row_sub_dealer_details["customer_name"]) : "";
										$branch = $row1["branch_name"] ? trim($row1["branch_name"]) : "";
										$dns_prod_code = $row1["dns_prod_code"] ? trim($row1["dns_prod_code"]) : "";
										$prod_display_name = $row1["prod_desc"] ? trim($row1["prod_desc"]) : "";		
		$monthval=substr($row1["date_and_time"],5,2);		
								/*$sqldespatchqty="select SUM(CHALLANQTY) as total_despatch_qty from $T_DOCHALLAN where `dns_customer_code`='$linked_dealer_code'  AND dns_prod_code='$dns_prod_code' AND substring(CHALLANDT,6,2)='$monthval'";
								$resdespatchqty=mysql_query($sqldespatchqty);
								$rowdespatchqty=mysql_fetch_array($resdespatchqty);
								$total_despatch_qty = $rowdespatchqty["total_despatch_qty"] ? trim($rowdespatchqty["total_despatch_qty"]) : "";*/
								$total_despatch_qty = $row1["dispatch_qty"] ? trim($row1["dispatch_qty"]) : "";	
								$dispatch_date = $row1["dispatch_date"] ? trim($row1["dispatch_date"]) : "";			
								${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date} =${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date}+ $row1["total_allocation_qty"];
								$challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
										// $month = $row1["month"] ? trim($row1["month"]) : "";
										$month = "";
										if ($row1["date_and_time"] != "") {
											$month = date("M-y", strtotime($row1["date_and_time"]));
										}
										$remaining_allocation_qty=($total_despatch_qty-${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date} );
								?>
										<tr>
											<td><?php echo $date_and_time;?></td>
											<td><?php echo $APPORDERNO;?></td>
											<td><?php echo $dispatch_date;?></td>
											<td><?php echo $linked_dealer_code; ?></td>
											<td><?php echo $linked_dealer_sap_code; ?></td>
											<td><?php echo $linked_dealer_name; ?></td>
											<td><?php echo $sub_dealer_rssd_code; ?></td>
											<td><?php echo $sub_dealer_rssd_sap_code; ?></td>
											<td><?php echo $sub_dealer_rssd_name; ?></td>
											<td><?php echo $branch; ?></td>
											<td><?php echo $month; ?></td>
											<td><?php echo $prod_display_name; ?></td>
											<td><?php echo $total_despatch_qty; ?></td>
											<td><?php echo $row1["total_allocation_qty"] ; ?></td>
											<td><?php echo $remaining_allocation_qty; ?></td>
											<td><?php echo $challan_no; ?></td>
											
										</tr>
		<?php
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


