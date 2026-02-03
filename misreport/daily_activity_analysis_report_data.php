<?php
ob_start();
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
	session_start();
	require("adminUtils.php");
	require("include/config.php");
    require("include/config-setup.php");
    require("include/dbcon.php");
    require("include/functions.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if($_GET['start_date'] != '' && $_GET['end_date'] != '')
{
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$condition = " AND (SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$count_transid_condition = " ,COUNT(LO.trans_id) ";
	$primary_secondary_quantity_condition = "AND  (DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."')";
	$group_by = " GROUP BY EM.emp_code,SUBSTRING(LO.date,1,10) ";
	//$attendance_condition = " COUNT(LO.trans_id) as attendance ";
	//$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	//$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
		
}
else
{
	$start_date = date('Y-m-d');
	$table_columnname = 'Attendance';
	$condition = " AND SUBSTRING(LO.date,1,10)='".$start_date."' ";
	$attendance_condition = " SUBSTRING(LO.date,12) as attendance ";
	$primary_secondary_quantity_condition = "AND  DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d')='".$start_date."'";
}

if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " AND EM.emp_code IN(".$emp_hierarchy_value.")";
	}

$count = 1;
/*---------------------------------> Count of productive call <--------------------------------*/
		$sql_productive_call = "SELECT COUNT(LO.trans_id),LO.emp_code,SUBSTRING(LO.date,1,10) as productive_date  FROM location LO, employee_master EM WHERE LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id NOT LIKE 'PA%' 
								AND SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY LO.emp_code,SUBSTRING(LO.date,1,10) ORDER BY LO.emp_code ASC";
		$res_productive_call = mysqli_query($link,$sql_productive_call);
		while($row_productive_call = mysqli_fetch_assoc($res_productive_call))
		{
			$emp_code_productive=$row_productive_call['emp_code'];
			$date_productive=$row_productive_call['productive_date'];
			$productive_call = $row_productive_call['COUNT(LO.trans_id)'];
			${'productive_call'.$emp_code_productive.$date_productive}=$productive_call;
		}
		
	/*---------------------------------> Count of non-productive call <--------------------------------*/
		$sql_non_productive_call = "SELECT COUNT(LO.trans_id),LO.emp_code,SUBSTRING(LO.date,1,10) as non_productive_date FROM location LO, employee_master EM WHERE  LO.emp_code=EM.emp_code AND (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
								AND SUBSTRING(LO.date,1,10) BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY LO.emp_code,SUBSTRING(LO.date,1,10) ORDER BY LO.emp_code ASC";
		$res_non_productive_call = mysqli_query($link,$sql_non_productive_call);
		while($row_non_productive_call = mysqli_fetch_assoc($res_non_productive_call))
		{
			$emp_code_non_productive=$row_non_productive_call['emp_code'];
			$date_non_productive=$row_non_productive_call['non_productive_date'];
			$non_productive_call = $row_non_productive_call['COUNT(LO.trans_id)'];
			${'non_productive_call'.$emp_code_non_productive.$date_non_productive}=$non_productive_call;
		}
		
		
		/*---------------------------------> Total primary sales <--------------------------------*/
		/*$sql_total_primary = "SELECT SUM(OD.qty) FROM order_details OD, customer_master CM, order_header OH WHERE OH.order_no=OD.order_no AND SUBSTRING(OD.order_no,2,5)='".$emp_code."' ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code AND CM.cust_type='D'";
		$res_total_primary = mysqli_query($link,$sql_total_primary);
		$row_total_primary = mysqli_fetch_assoc($res_total_primary);
		$primary_quantity = $row_total_primary['SUM(OD.qty)'];
		$total_primary_quantity += $primary_quantity;*/
		
		/*---------------------------------> Total secondary sales <--------------------------------*/
		
		$sql_get_details = "SELECT RM.route_code,RM.route_name,RP.emp_code,RP.visit_date FROM route_plan RP,route_master RM WHERE RM.route_code=RP.route_code AND 
							RP.visit_date BETWEEN '".$start_date."' AND '".$end_date."' ORDER BY RP.emp_code,RM.route_name ASC";
		$res_get_details = mysqli_query($link,$sql_get_details);
		$total_route_check = mysqli_num_rows($res_get_details);
		if($total_route_check>0){
			while($row_get_details = mysqli_fetch_assoc($res_get_details)){
				$emp_code_route=$row_get_details['emp_code'];
				$visit_date_route=$row_get_details['visit_date'];
				$route_code = $row_get_details['route_code'];
				$route_name = $row_get_details['route_name'];
				${'route_name_string'.$emp_code_route.$visit_date_route} = ${'route_name_string'.$emp_code_route.$visit_date_route}.$route_name.", ";
				}
			}
		//$route_name_string='';	
		$sql_total_secondary = "SELECT SUM(OD.qty),SUM(OD.amount) as tot_amount,DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d') AS order_date,SUBSTRING(OD.order_no,2,5) AS order_emp FROM order_details OD, customer_master CM, order_header OH 
							WHERE OH.order_no=OD.order_no  ".$primary_secondary_quantity_condition." AND OH.customer_code=CM.customer_code 
							AND CM.cust_type='R' GROUP BY DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%Y-%m-%d'),SUBSTRING(OD.order_no,2,5) ORDER BY SUBSTRING(OD.order_no,2,5) ASC";
		$res_total_secondary = mysqli_query($link,$sql_total_secondary);
		while($row_total_secondary = mysqli_fetch_assoc($res_total_secondary))
		{
			$emp_code_secondary=$row_total_secondary['order_emp'];
			$visit_date_secondary=$row_total_secondary['order_date'];
			$secondary_quantity = $row_total_secondary['SUM(OD.qty)'];
			$secondary_amount = $row_total_secondary['tot_amount'];
			${'secondary_quantity'.$emp_code_secondary.$visit_date_secondary}=$secondary_quantity;
			${'secondary_amount'.$emp_code_secondary.$visit_date_secondary}=$secondary_amount;
		}
		
		
		$sql_delivery_qty = "SELECT SUM(delivery_qty) as tot_delivery,SUM(visit_amount) as tot_delivery_amount,SUBSTRING(order_no,2,5) AS delivery_emp,SUBSTRING(delivery_date,1,10) AS delivery_date FROM 
						order_delivery_log WHERE  SUBSTRING(delivery_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY SUBSTRING(delivery_date,1,10),SUBSTRING(order_no,2,5) ORDER BY SUBSTRING(order_no,2,5) ASC";
		$res_delivery_qty = mysqli_query($link,$sql_delivery_qty);
		while($row_delivery_qty = mysqli_fetch_assoc($res_delivery_qty))
		{
			$emp_code_delivery=$row_delivery_qty['delivery_emp'];
			$delivery_date=$row_delivery_qty['delivery_date'];
			$tot_delivery = $row_delivery_qty['tot_delivery'];
			$tot_delivery_amount = $row_delivery_qty['tot_delivery_amount'];
			${'tot_delivery'.$emp_code_delivery.$delivery_date}=$tot_delivery;
			${'delivery_amount'.$emp_code_delivery.$delivery_date}=$tot_delivery_amount;
		}
		
		if(strtoupper($_SESSION['nick_name'])=='PALSONS')
		{
			$sqlcall="SELECT COUNT(customer_code) AS no_of_call,emp_code,DATE_FORMAT(SUBSTRING(joint_work_id,-14,8),'%Y-%m-%d') AS joint_work_date FROM  joint_work_observation 
					WHERE SUBSTRING(joint_work_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."' 
					GROUP BY emp_code,SUBSTRING(joint_work_id,-14,8) ORDER BY emp_code ASC"; 
			$rscall=mysqli_query($link,$sqlcall);
			while($rowcall=mysqli_fetch_assoc($rscall))
			{
				$no_of_call=$rowcall['no_of_call'];
				$emp_code_joint_work=$rowcall['emp_code'];
				$joint_work_date=$rowcall['joint_work_date'];
				${'no_of_call'.$emp_code_joint_work.$joint_work_date}=$no_of_call;
			}
			
			$sql_prospect = "SELECT substring(trans_id,3,5) As emp_code,count(customer_code) AS tot_prospect,DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') AS prospect_date 
							FROM prospective_customer_header WHERE SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."' 
							GROUP BY substring(trans_id,3,5),SUBSTRING(trans_id,-14,8) ORDER BY substring(trans_id,3,5) ASC";
			$res_prospect = mysqli_query($link,$sql_prospect);
			while($row_prospect=mysqli_fetch_assoc($res_prospect))
			{
			$tot_prospect=$row_prospect['tot_prospect'];
			$emp_code_prospect=$row_prospect['emp_code'];
			$prospect_date=$row_prospect['prospect_date'];
			${'prospect_call'.$emp_code_prospect.$prospect_date}=$tot_prospect;
			}
		}
		//echo 'sdsdsdsdsds'.${'no_of_callE00542022-11-17'};
		
$sql_get_empdetails = "SELECT LO.emp_code, EM.emp_name,SUBSTRING(LO.date,1,10) AS trans_date ,".$attendance_condition."FROM location LO, employee_master EM WHERE LO.trans_id LIKE 'A%' AND LO.emp_code = EM.emp_code AND EM.acedns != 'N'
					".$emp_hierarchy_value_condition.$condition.$group_by."ORDER BY EM.emp_name ASC,SUBSTRING(LO.date,1,10) ASC";
$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
$total_rows = mysqli_fetch_assoc($res_get_empdetails);
if($total_rows>0){
	
	if(strtoupper($_SESSION['nick_name'])=='PALSONS')
		{
			$additional_tr="<td>Total Joint Work</td><td >Total Prospect</td>";
		}
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='13' align='center'>Daily Activity Analysis</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Date</td>
			<td>Employee Name</td>
			<td>Attendance</td>
			<td>Beat</td>
			<td>Total Call</td>
			<td>Productive Call</td>
			<td>Total Secondary Value</td>
			<td>Total Secondary Amount</td>
			<td>Delivery Value</td>
			<td >Delivery Amount</td>".$additional_tr."
		  </tr>";
		  
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		$emp_code = $row_get_empdetails['emp_code'];
		$emp_name = $row_get_empdetails['emp_name'];
		$attendance = $row_get_empdetails['attendance'];
		$trans_date_fetched=$row_get_empdetails['trans_date'];
		$trans_date=date('d-m-Y',strtotime($trans_date_fetched));
		
		$total_call=${'productive_call'.$emp_code.$trans_date_fetched}+${'non_productive_call'.$emp_code.$trans_date_fetched};
		${'route_name_string'.$emp_code.$trans_date_fetched} = rtrim(${'route_name_string'.$emp_code.$trans_date_fetched}," ");
		${'route_name_string'.$emp_code.$trans_date_fetched} = rtrim(${'route_name_string'.$emp_code.$trans_date_fetched},",");
				//$total_secondary_value += $secondary_amount;

		//$tot_delivery_amount = $row_delivery_qty['tot_delivery_amount'];
		if(strtoupper($_SESSION['nick_name'])=='PALSONS')
		{
			$additional_td="<td align='right'>".${'no_of_call'.$emp_code.$trans_date_fetched}."</td><td align='right'>".${'prospect_call'.$emp_code.$trans_date_fetched}."</td>";
		}
		echo "<tr>
					<td>".$count."</td>
					<td>".$trans_date."</td>
					<td >".$emp_name."</td>
					<td align='right'>".$attendance."</td>
					<td>".${'route_name_string'.$emp_code.$trans_date_fetched}."</td>
					<td align='right'>".$total_call."</td>
					<td align='right'>".${'productive_call'.$emp_code.$trans_date_fetched}."</td>
					<td align='right'>".${'secondary_quantity'.$emp_code.$trans_date_fetched}."</td>
					<td align='right'>".number_format(${'secondary_amount'.$emp_code.$trans_date_fetched},2)."</td>
					<td align='right'>".${'tot_delivery'.$emp_code.$trans_date_fetched}."</td>
					<td align='right'>".number_format(${'delivery_amount'.$emp_code.$trans_date_fetched},2)."</td>".$additional_td."
				  </tr>";
				  
		$count++;
	}
	
	/*echo "<tr style='font-weight:bold;'>
			<td colspan='3' align='center'>Total</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".$total_non_productive_call."</td>
			$table_column_data
		  </tr>";*/
	echo "</table>";
	?>
    <br />
    <br>
    <div style="width:90%;" align="right">
        <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
    </div>
    <?php
}
else{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>
