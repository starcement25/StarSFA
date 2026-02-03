<?php
ob_start();
session_start();
require("adminUtils.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$date_array = array();
if($employee == 'all'){
	$emp_condition = '';
	$emp_condition_open = '';
}
else{
	$emp_condition = " AND SUBSTRING(POCM.order_no,-19,5) IN(".$employee.") ";
	$emp_condition_open =  " AND emp_code IN(".$employee.") ";
}
if($start_date!='' && $end_date!='')
{
	$date_condition=" AND SUBSTRING(POCM.order_no,-14,8) >= '".str_replace("-","",$start_date)."' 
		AND SUBSTRING(POCM.order_no,-14,8) <='".str_replace("-","",$end_date)."'";
}
else
{
	$date_condition='';
}
$open_date_array=array();
$sql_open_customer = "SELECT customer_code,DATE_FORMAT(SUBSTRING(customer_code,-14,8),'%d-%m-%Y') as open_date,SUBSTRING(customer_code,2,5) 
					as emp_code FROM customer_route_emp_relation  
					WHERE acedns='Y' AND customer_code LIKE 'N%' ".$emp_condition_open." AND SUBSTRING(customer_code,-14,8) >= '".str_replace("-","",$start_date)."' AND  
				SUBSTRING(customer_code,-14,8) <= '".str_replace("-","",$end_date)."'";
$rs_open_customer=mysqli_query($link,$sql_open_customer);
while($row_open_customer=mysqli_fetch_assoc($rs_open_customer))
{
	$open_date=$row_open_customer['open_date'];
	$emp_code=$row_open_customer['emp_code'];
	${'open_customer'.$emp_code.$open_date}=${'open_customer'.$emp_code.$open_date}+1;
	if(!in_array($open_date,$open_date_array) && $open_date!='')
	{
		array_push($open_date_array,$open_date);
	}
}
$employee_formated=str_replace("'","",$employee);
$employee_formated_array=explode(",",$employee_formated);
$listed_emp_array=array();
$visited_emp_array=array();
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
    <tr><td colspan = '9' align = 'center' class = 'TDHEAD_SUB'>Vistaar Dashboard From <?php echo date('d-m-Y',strtotime($start_date));?> to <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
      <tr class="TDHEAD">
      	<td>SI</td>
        <td>Branch</td>
        <td>Name of TSM/LAS</td>
        <td>Designation</td>
        <td>Date</td>
        <td>City/Town</td>
        <td>No. of New Outlets visited</td>
	  	<td>No. of New Outlets opened</td>
        <td>Order Value from new outlet</td>
      </tr>
    <?php
/*$sqlinformation="SELECT BM.branch_name,EM.emp_name,EM.emp_code,EM.dns_emp_code,DATE_FORMAT(SUBSTRING(POCM.visit_date,1,10),'%d-%m-%Y') 
				as visit_date,POCM.amount,POCM.customer_code FROM  branch_master BM,prev_order_counting_master POCM,employee_master EM WHERE EM.branch_code=BM.branch_code AND SUBSTRING(POCM.order_no,-19,5)=EM.emp_code 
					AND POCM.customer_code LIKE 'N%' ".$emp_condition.$date_condition." 
					ORDER BY EM.emp_name ASC,DATE_FORMAT(SUBSTRING(POCM.visit_date,1,10),'%d-%m-%Y') DESC,POCM.customer_code ASC ";*/
	$sqlinformation="SELECT BM.branch_name,EM.emp_name,EM.designation,EM.emp_code,EM.dns_emp_code,DATE_FORMAT(SUBSTRING(POCM.visit_date,1,10),'%d-%m-%Y') as visit_date,POCM.amount,POCM.customer_code,POCM.order_no FROM employee_master EM LEFT JOIN prev_order_counting_master POCM ON SUBSTRING(POCM.order_no,-19,5)=EM.emp_code AND POCM.customer_code LIKE 'N%' ".$emp_condition.$date_condition."  LEFT JOIN branch_master BM ON EM.branch_code=BM.branch_code ORDER BY EM.emp_name ASC,SUBSTRING(POCM.visit_date,1,10) DESC,POCM.customer_code ASC";				
	$resinformation=mysqli_query($link,$sqlinformation) or die(mysqli_error()." Error in select transaction information: ".$sqlinformation);
	$count=mysqli_num_rows($resinformation);
	if($count >0){
		$cnt=1;
		$emp_code_code_array=array();
		$new_customer_create_array=array();
		$branch_name_array=array();
		$emp_name_array=array();
		$designation_array=array();		
		$visit_date_array=array();
		$order_no_array=array();	
	while($rowinformation = mysqli_fetch_assoc($resinformation)){
		$emp_name = $rowinformation['emp_name'];
		$designation = $rowinformation['designation'];
		$branch_name = $rowinformation['branch_name'];
		$visit_date = $rowinformation['visit_date'];
		$amount = $rowinformation['amount'];
		$emp_code=$rowinformation['emp_code'];
		$dns_emp_code=$rowinformation['dns_emp_code'];
		$customer_code=$rowinformation['customer_code'];
		$order_no=$rowinformation['order_no'];
		$visit_date_formated=date('Ymd',strtotime($visit_date));
		if(!in_array($emp_code,$emp_code_code_array))
		{
			array_push($emp_code_code_array,$emp_code);
			array_push($branch_name_array,$branch_name);
			array_push($emp_name_array,$emp_name);
			array_push($designation_array,$designation);
		}
		if(!in_array($visit_date,$visit_date_array))
		{
			array_push($visit_date_array,$visit_date);
		}
		if($visit_date!='')
		{
			if(!in_array($order_no,$order_no_array))
			{
				${'new_counter_visited'.$emp_code.$visit_date}=${'new_counter_visited'.$emp_code.$visit_date}+1;
				array_push($order_no_array,$order_no);
			}
			//${'new_counter_visited'.$emp_code.$visit_date}=${'new_counter_visited'.$emp_code.$visit_date}+1;
			${'new_counter_order_value'.$emp_code.$visit_date}=${'new_counter_order_value'.$emp_code.$visit_date}+$amount;
			if(!in_array($emp_code,$visited_emp_array))
			{
				array_push($visited_emp_array,$emp_code);
			}
		}
		if(substr($customer_code,1,5)==$emp_code && substr($customer_code,6,8)==$visit_date_formated && !in_array($customer_code,$new_customer_create_array))
		{
			${'new_counter_opened'.$emp_code.$visit_date}=${'new_counter_opened'.$emp_code.$visit_date}+1;
			array_push($new_customer_create_array,$customer_code);
		}
	}
	//array_unique(array_merge($array1,$array2), SORT_REGULAR);
	//print_r($open_date_array);
	//print_r($visit_date_array);
	$visit_open_merge_array=array();
	foreach($visit_date_array as $visit_date_val)
	{
		if(!in_array($visit_date_val,$visit_open_merge_array)){
			array_push($visit_open_merge_array,$visit_date_val);
		}
	}
	foreach($open_date_array as $open_date_val)
	{
		if(!in_array($open_date_val,$visit_open_merge_array)){
			array_push($visit_open_merge_array,$open_date_val);
		}
	}
	//$visit_open_merge_array=array_unique(array_merge($open_date_array,$visit_date_array));	
	for($i=0;$i< count($emp_code_code_array);$i++){
		if(in_array($emp_code_code_array[$i],$employee_formated_array)){
	//print_r($visit_open_merge_array);

			for($k=0;$k< count($visit_open_merge_array);$k++){
				if((!in_array($emp_code_code_array[$i],$listed_emp_array) && !in_array($emp_code_code_array[$i],$visited_emp_array)) || 
				${'open_customer'.$emp_code_code_array[$i].$visit_open_merge_array[$k]} >0 || ${'new_counter_visited'.$emp_code_code_array[$i].$visit_open_merge_array[$k]} > 0){
			echo "<tr>
				<td>".$cnt."</td>
				<td>".$branch_name_array[$i]."</td>
				<td>".$emp_name_array[$i]."</td>
				<td>".$designation_array[$i]."</td>
				<td>".$visit_open_merge_array[$k]."</td>
				<td></td>
				<td>".${'new_counter_visited'.$emp_code_code_array[$i].$visit_open_merge_array[$k]}."</td>
				<td>".${'open_customer'.$emp_code_code_array[$i].$visit_open_merge_array[$k]}."</td>
				<td>".${'new_counter_order_value'.$emp_code_code_array[$i].$visit_open_merge_array[$k]}."</td>
			  </tr>";
			  $cnt++;
			  	array_push($listed_emp_array,$emp_code_code_array[$i]);
				}
			}
		}
	  }
	}
	else{
	echo "<tr><td colspan='10' align='center'>No Records</td><tr>";
	}
	?>
    </table>
    <br />
    <br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
mysqli_close($link);
?>


