<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$start_date_search=str_replace('-','',$start_date);
$end_date = $_REQUEST['end_date'];
$end_date_search=str_replace('-','',$end_date);
$date_array = array();
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	if(strtoupper($_SESSION['admin_login'])=="ADMIN"){
		$emp_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_condition = " AND LO.emp_code IN(".$emp_hierarchy_value.") ";
	}
}
else{
	$order_condition = " SUBSTRING(order_no,2,5) IN(".$employee.") AND ";
	$payment_condition = " SUBSTRING(receipt_id,2,5) IN(".$employee.") AND ";
	$checkinout_condition = " SUBSTRING(trans_id,3,5) IN('".$employee."') AND ";
	$emp_condition = " AND LO.emp_code IN('".$employee."') ";
}
$emp_date_checkout_array=array();
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
        <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <td width="15%">Employee Name</td>
        <td width="15%">Outlet Name</td>
         <td width="15%">Branch</td>
          <td width="15%">Distributor</td>
        <td width="10%">Check in Time</td>
         <td width="20%">Location</td>
      </tr>
    <?
	$emp_date_outlet_array=array();
		//For Check in and Check out
		$sqlcheckinoutlet="SELECT EM.emp_name,EM.emp_code,LO.trans_id,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%m%d') AS formatted_date,
						DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%d-%m-%Y') AS visit_date,
						DATE_FORMAT(SUBSTRING(LO.trans_id,-14,14),'%H:%i:%s') AS visit_time,LO.address,CM.customer_name,BM.branch_name,
						(SELECT customer_name FROM customer_master WHERE customer_code=CM.rds_tag) AS distributor
						FROM employee_master EM,location LO,order_header OH,customer_master CM,branch_master BM
						WHERE LO.emp_code=EM.emp_code AND LO.trans_id=OH.order_no AND OH.customer_code=CM.customer_code AND CM.branch_code=BM.branch_code
						 $emp_condition AND
					 	(SUBSTRING(LO.trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
					 	 ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(LO.trans_id,-14,14),'%Y-%m-%d %H:%i:%s') ASC";
		$rscheckinoutlet=mysqli_query($link,$sqlcheckinoutlet) or die(mysqli_error()." Error in select check in out details ".$sqlcheckinoutlet);
		$countcheckinoutlet=mysqli_num_rows($rscheckinoutlet);
		if($countcheckinoutlet >0)
		{
			while($rowcheckinoutlet=mysqli_fetch_assoc($rscheckinoutlet))
			{
				$emp_code=$rowcheckinoutlet['emp_code'];
				$emp_name=$rowcheckinoutlet['emp_name'];
				$formatted_date=$rowcheckinoutlet['formatted_date'];
				$visit_date=$rowcheckinoutlet['visit_date'];
				$visit_time=$rowcheckinoutlet['visit_time'];
				$address=$rowcheckinoutlet['address'];
				$customer_name=$rowcheckinoutlet['customer_name'];
				$branch_name=$rowcheckinoutlet['branch_name'];
				$distributor=$rowcheckinoutlet['distributor'];
				
				$emp_date_string=$emp_code.'#'.$visit_date;
				
				if(!in_array($emp_date_string,$emp_date_outlet_array))
				{
					echo "<tr>
					<td>".$visit_date."</td>
					<td>".$emp_name."</td>
					<td>".$customer_name."</td>
					<td>".$branch_name."</td>
					<td>".$distributor."</td>
					<td>".$visit_time."</td>
					<td>".$address."</td>";
					echo "</tr>";
					array_push($emp_date_outlet_array,$emp_date_string);
				}
			  }
		}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
/*}
else{
	echo "No Records";
}*/
mysqli_close($link);
?>


