<?php
error_reporting(0);
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=="ED01"){
		require("adminUtils_HBC_SFATS.php");
	}
	else
	{
		require("adminUtils.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");

date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');
$today = str_replace("-","",$today);
$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysqli_query($link,$sql_sauda_filter);
$row_sauda_filter = mysqli_fetch_assoc($res_sauda_filter);
$sauda_filter_value = $row_sauda_filter['sauda_allocation_basedon_filter'];

if($sauda_filter_value == 1){
	$sauda_table_value = 'product_group_master';
	$field_name1 = 'product_group_code';
	$field_name2 = 'product_group_name';
	$acronym = "PGM";
}
else if($sauda_filter_value == 2){
	$sauda_table_value = 'product_sub_group_master';
	$field_name1 = 'product_sub_group_code';
	$field_name2 = 'product_sub_group_name';
	$acronym = "PSGM";
}
else if($sauda_filter_value == 3){
	$sauda_table_value = 'product_brand_master';
	$field_name1 = 'product_brand_code';
	$field_name2 = 'product_brand_name';
	$acronym = "PBM";
}
else if($sauda_filter_value == 4){
	$sauda_table_value = 'product_master';
	$field_name1 = 'product_code';
	$field_name2 = 'product_name';
	$acronym = "PM";
}
$group_name = $acronym.".".$field_name2;
$group_code = $acronym.".".$field_name1;

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'supervisor' || $_SESSION['admin_login'] == 'system'){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
	if(vertical_fields=='yes'){
		$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
		$rsempvertical=mysqli_query($link,$sqlempvertical);
		$rowempvertical=mysqli_fetch_assoc($rsempvertical);
		$emp_vertical_value=$rowempvertical['vertical_value'];
		$emp_vertical_value_array=explode(',',$emp_vertical_value);
		//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
		$condition_one=" WHERE (";
		$condition_three=" AND (";
		$condition_two='';
		foreach($emp_vertical_value_array as $emp_vertical_values)
		{
			$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
		}
		$condition_two=substr($condition_two,0,-2);
		$condition_one.=$condition_two.")";
		$condition_three .= $condition_two.")";
	}
}
if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$condition = " AND substring(SAL.allocation_date,1,10) LIKE '%$today%'";
	$order_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$value = 1;
	$sauda_duration = "Today's Sauda Booking Status";
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = " AND (YEAR(substring(SAL.allocation_date,1,10)) ='".$year."' AND MONTH(substring(SAL.allocation_date,1,10)) ='".$month."')";
	$order_condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$value = 2;
	$sauda_duration = "Sauda Booking Status MTD";
}
else if($_GET['type'] == 'custom'){
	$start_date = str_replace("-","",$_GET['start_date']);
	$end_date = str_replace("-","",$_GET['end_date']);
	$condition = " AND DATE_FORMAT(SAL.allocation_date,'%Y-%m-%d') BETWEEN '".$_GET['start_date']."' AND '".$_GET['end_date']."'";
	$order_condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$value = 3;
	$sauda_duration = "Sauda Booking Status Custom";
}
else{
	$today = date('Y-m-d');
	$condition = " AND substring(SAL.allocation_date,1,10) LIKE '%$today%'";
	$order_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$value = 1;
	$sauda_duration = "Today's Sauda Booking Status";
}
$product_group_code_array=array();
$sauda_group_code_array=array();


echo "<table border=\"1\" width=\"100%\" style=\"border-collapse:collapse;\" class=\"border\">
		<tr>
			<td align=\"center\" class=\"TDHEAD\" colspan=\"5\">$sauda_duration</td>
		</tr>
		<tr align=\"center\" style=\"font-weight:bold;\" class=\"TDHEAD_SUB\">
			<td>Group</td>
			<td>Alloted</td>
			<td colspan=\"2\">Booked</td>
			<td>Balance</td>
		</tr>
		<tr align=\"center\" style=\"font-weight:bold;\" class=\"TDHEAD_SUB\">
			<td></td>
			<td></td>
			<td>MT</td>
			<td>Case</td>
			<td></td>
		</tr>";
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == "supervisor")
{
	$vertical_val_condition="WHERE 1 ";
	$vertical_val_condition_one="";
}
else
{
	$vertical_val_condition=" WHERE PGM.vertical_value='".$_SESSION['vertical_value']."'";
	$vertical_val_condition_one=" AND PM.vertical_value='".$_SESSION['vertical_value']."'";
}
		
if($_SESSION['vertical_value']=='Specialty Fats'){
		
		if($_GET['type'] == 'today' || $_GET['type'] == '')
		{
			if($_SESSION['admin_login'] == 'admin' || $_SESSION['admin_login'] == 'supervisor' || $_SESSION['admin_login'] == 'system')
			{
				$sql_sauda_alloted = "SELECT PGM.vertical_value,$group_code, $group_name,SUM(SAL.qty) as qty FROM $sauda_table_value $acronym 
									 LEFT JOIN sauda_allocation SAL ON $group_code = SAL.product_filter_code AND SAL.emp_code 
									 IN(SELECT emp_code FROM employee_master WHERE 
									reporting_to IN(SELECT emp_code FROM employee_master WHERE reporting_to='' AND acedns!='N') AND acedns!='N') 
									WHERE $acronym.acedns='Y' AND PGM.vertical_value!='HBC:Rasoi:BIB' 
									GROUP BY $group_code ORDER BY PGM.vertical_value,$group_name ASC";
				/*$sql_sauda_alloted = "SELECT PGM.vertical_value,$group_code, $group_name,SUM(SAL.qty) as qty FROM $sauda_table_value $acronym 
									 LEFT JOIN sauda_allocation SAL ON $group_code = SAL.product_filter_code AND SAL.emp_code 
									 IN(SELECT emp_code FROM employee_master WHERE reporting_to IN(SELECT emp_code FROM employee_master WHERE reporting_to='' AND acedns!='N') AND acedns!='N' UNION SELECT emp_code FROM employee_master WHERE reporting_to='' ) 
									
									WHERE $acronym.acedns='Y' GROUP BY $group_code ORDER BY PGM.vertical_value,$group_name ASC";*/					
			}
			else
			{ 
				$sql_sauda_alloted = "SELECT PGM.vertical_value,$group_code, $group_name,SUM(SAL.qty) as qty FROM $sauda_table_value $acronym LEFT JOIN sauda_allocation SAL ON $group_code = SAL.product_filter_code AND SAL.emp_code IN(SELECT emp_code FROM employee_master 
								WHERE reporting_to='".$_SESSION['admin_login']."' AND acedns!='N')".$condition_one." AND $acronym.acedns='Y'  
								AND PGM.vertical_value!='HBC:Rasoi:BIB' GROUP BY $group_code ORDER BY PGM.vertical_value,$group_name ASC";
			}
		}
		else
		{
			if($_SESSION['admin_login'] == 'admin' || $_SESSION['admin_login'] == 'supervisor' || $_SESSION['admin_login'] == 'system')
			{
				$sql_sauda_alloted = "SELECT * FROM (SELECT PGM.vertical_value,$group_code, $group_name,SAL.qty,SUBSTRING(SAL.allocation_date,1,10) AS allocation_date,SAL.emp_code FROM $sauda_table_value $acronym, sauda_allocation_log SAL 
									WHERE $group_code = SAL.product_filter_code AND SAL.emp_code IN(SELECT emp_code FROM employee_master WHERE 
									reporting_to IN(SELECT emp_code FROM employee_master WHERE reporting_to='' AND acedns!='N') AND acedns!='N')
									".$condition." AND $acronym.acedns='Y' AND PGM.vertical_value!='HBC:Rasoi:BIB' ORDER BY SAL.allocation_date DESC) AS SAT GROUP BY 5,6,2 ORDER BY 1,3 ASC";
			}
			else
			{ 
				$sql_sauda_alloted = "SELECT * FROM (SELECT PGM.vertical_value,$group_code, $group_name,SAL.qty,SUBSTRING(SAL.allocation_date,1,10) AS allocation_date,SAL.emp_code FROM $sauda_table_value $acronym, sauda_allocation_log SAL WHERE 
									$group_code = SAL.product_filter_code AND SAL.emp_code IN(SELECT emp_code FROM employee_master 
									WHERE reporting_to='".$_SESSION['admin_login']."' AND acedns!='N'".$condition_three.")".$condition." AND 
									$acronym.acedns='Y' AND PGM.vertical_value!='HBC:Rasoi:BIB' ORDER BY SAL.allocation_date DESC) AS SAT GROUP BY 5,6,2  ORDER BY 1,3 ASC";
			}
		}
	$res_sauda_alloted = mysqli_query($link,$sql_sauda_alloted);
	
	while($row_sauda_alloted = mysqli_fetch_assoc($res_sauda_alloted)){
		$sauda_group_code = $row_sauda_alloted[$field_name1];
		
		$sauda_group_name = $row_sauda_alloted[$field_name2];
		$vertical=$row_sauda_alloted['vertical_value'];
		
		$sauda_group_name_array[$sauda_group_code]=$sauda_group_name;
		$sauda_group_alloted[$sauda_group_code] += $row_sauda_alloted['qty'];
		$vertical_name_array[$sauda_group_code]=$vertical;
		if(${verticalcount.$vertical}=='') ${verticalcount.$vertical}=0;
		if($_GET['type'] == 'today' || $_GET['type'] == ''){
			${verticalcount.$vertical}=${verticalcount.$vertical}+1;
		}
		else
		{
			if(!in_array($sauda_group_code,$sauda_group_code_array))
			{
				${verticalcount.$vertical}=${verticalcount.$vertical}+1;
			}
			array_push($sauda_group_code_array,$sauda_group_code);
		}
			
		$sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as case_booked FROM sauda_details SD, product_master PM 
							WHERE PM.$field_name1='$row_sauda_alloted[$field_name1]' $order_condition $emp_hierarchy_condition_one AND SD.sku_code=PM.prod_code";
		$res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
		$row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
		 
		if($row_quantity_booked['mt_booked'] != '')	
			$quantity_booked = $row_quantity_booked['mt_booked'];
		else
			$quantity_booked = 0;
		
		$total_alloted += $row_sauda_alloted['qty'];
		if(!in_array($sauda_group_code,$product_group_code_array)){
			$sauda_group_booked[$sauda_group_code] += $row_quantity_booked['mt_booked'];
			$sauda_group_booked_case[$sauda_group_code] += $row_quantity_booked['case_booked'];
			//$balance_quantity[$sauda_group_code] = $row_sauda_alloted['qty']-$row_quantity_booked['mt_booked'];
	
			$total_booked  += $row_quantity_booked['mt_booked'];
			$total_booked_case  += $row_quantity_booked['case_booked'];
			//$total_remain  += $balance_quantity[$sauda_group_code];
			array_push($product_group_code_array,$sauda_group_code);
		}
	}
$vertical_value_array=array();
$verticalcount=1;
foreach($sauda_group_alloted as $key=>$val){
	$balance_qty=$sauda_group_alloted[$key]-$sauda_group_booked[$key];
	$total_remain  += $balance_qty;
	if(count(${vertical_value_name_array.$vertical_name_array[$key]})==0)
	{
		${vertical_value_name_array.$vertical_name_array[$key]}=array();

	}
	if(!in_array($vertical_name_array[$key],$vertical_value_array))
	{
		echo "<tr>
			<td align=\"center\" colspan=\"5\"><b>".$vertical_name_array[$key]."</b></td>
		  </tr>";
	  array_push($vertical_value_array, $vertical_name_array[$key]);
	}
	array_push(${vertical_value_name_array.$vertical_name_array[$key]}, $vertical_name_array[$key]);
	
	${valtotal.$vertical_name_array[$key]}=${valtotal.$vertical_name_array[$key]}+$val;
	${valbookedMT.$vertical_name_array[$key]}=${valbookedMT.$vertical_name_array[$key]}+$sauda_group_booked[$key];
	${valbookedcase.$vertical_name_array[$key]}=${valbookedcase.$vertical_name_array[$key]}+$sauda_group_booked_case[$key];
	${valremain.$vertical_name_array[$key]}=${valremain.$vertical_name_array[$key]}+$balance_qty;
	
	echo "<tr>
			<td >".$sauda_group_name_array[$key]."</td>
			<td align=\"right\">$val</td>
			<td align=\"right\">".$sauda_group_booked[$key]."</td>
			<td align=\"right\">".$sauda_group_booked_case[$key]."</td>
			<td align=\"right\">".$balance_qty."</td>
		  </tr>";
	if(${verticalcount.$vertical_name_array[$key]}==count(${vertical_value_name_array.$vertical_name_array[$key]}))
	{
		echo "<tr style=\"font-weight:bold;\">
			<td>Total</td>
			<td align=\"right\">".${valtotal.$vertical_name_array[$key]}."</td>
			<td align=\"right\">".${valbookedMT.$vertical_name_array[$key]}."</td>
			<td align=\"right\">".${valbookedcase.$vertical_name_array[$key]}."</td>
			<td align=\"right\">".${valremain.$vertical_name_array[$key]}."</td>
		  </tr>";
	}
	  
   	$verticalcount++;
}
echo "<tr style=\"font-weight:bold;\">
			<td>Grand Total</td>
			<td align=\"right\">".$total_alloted."</td>
			<td align=\"right\">".$total_booked."</td>
			<td align=\"right\">".$total_booked_case."</td>
			<td align=\"right\">".$total_remain."</td>
		  </tr>";
		  
echo "</table>";

if(empty($sauda_group_alloted))
	echo "<font color=\"#FF0000\"><strong>No records found</strong></font>";
}
else if($_SESSION['vertical_value']=='HBC:Rasoi:BIB')
{
		$sql_count_product = "SELECT  PGM.product_group_code,PGM.product_group_name FROM $sauda_table_value $acronym  
					".$vertical_val_condition." AND PGM.acedns='Y' AND PGM.product_group_code IN
					(SELECT DISTINCT PM.product_group_code FROM product_master PM,sauda_details SD WHERE PM.prod_code=SD.sku_code 
						$vertical_val_condition_one  $order_condition) ORDER BY $group_name ASC";
		$res_count_product = mysqli_query($link,$sql_count_product);
		while($row_count_product=mysqli_fetch_assoc($res_count_product))
		{
		$sauda_group_code = $row_count_product['product_group_code'];
		
		$sauda_group_name = $row_count_product['product_group_name'];
		$vertical='HBC:Rasoi:BIB';
		
		$sauda_group_name_array[$sauda_group_code]=$sauda_group_name;
		$vertical_name_array[$sauda_group_code]=$vertical;
		if(${verticalcount.$vertical}=='') ${verticalcount.$vertical}=0;
		if($_GET['type'] == 'today' || $_GET['type'] == ''){
			${verticalcount.$vertical}=${verticalcount.$vertical}+1;
		}
		else
		{
			if(!in_array($sauda_group_code,$sauda_group_code_array))
			{
				${verticalcount.$vertical}=${verticalcount.$vertical}+1;
			}
			array_push($sauda_group_code_array,$sauda_group_code);
		}
			
		$sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked, sum(SD.qty) as case_booked FROM sauda_details SD, product_master PM 
							WHERE PM.product_group_code='".$row_count_product['product_group_code']."'
							$order_condition $emp_hierarchy_condition_one AND SD.sku_code=PM.prod_code";
		$res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
		$row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
		 
		if($row_quantity_booked['mt_booked'] != '')	
			$quantity_booked = $row_quantity_booked['mt_booked'];
		else
			$quantity_booked = 0;
		
		$total_alloted = 0;
		if(!in_array($sauda_group_code,$product_group_code_array)){
			$sauda_group_booked[$sauda_group_code] += $row_quantity_booked['mt_booked'];
			$sauda_group_booked_case[$sauda_group_code] += $row_quantity_booked['case_booked'];
			//$balance_quantity[$sauda_group_code] = $row_sauda_alloted['qty']-$row_quantity_booked['mt_booked'];
	
			$total_booked  += $row_quantity_booked['mt_booked'];
			$total_booked_case  += $row_quantity_booked['case_booked'];
			//$total_remain  += $balance_quantity[$sauda_group_code];
			array_push($product_group_code_array,$sauda_group_code);
		}
	}
	$vertical_value_array=array();
	$verticalcount=1;
 foreach($sauda_group_name_array as $key=>$val){
	$balance_qty=$sauda_group_booked[$key];
	$total_remain  += $balance_qty;
	if(count(${vertical_value_name_array.$vertical_name_array[$key]})==0)
	{
		${vertical_value_name_array.$vertical_name_array[$key]}=array();

	}
	if(!in_array($vertical_name_array[$key],$vertical_value_array))
	{
		echo "<tr>
			<td align=\"center\" colspan=\"5\"><b>".$vertical_name_array[$key]."</b></td>
		  </tr>";
	  array_push($vertical_value_array, $vertical_name_array[$key]);
	}
	array_push(${vertical_value_name_array.$vertical_name_array[$key]}, $vertical_name_array[$key]);
	
	${valtotal.$vertical_name_array[$key]}=${valtotal.$vertical_name_array[$key]}+0;
	${valbookedMT.$vertical_name_array[$key]}=${valbookedMT.$vertical_name_array[$key]}+$sauda_group_booked[$key];
	${valbookedcase.$vertical_name_array[$key]}=${valbookedcase.$vertical_name_array[$key]}+$sauda_group_booked_case[$key];
	${valremain.$vertical_name_array[$key]}=${valremain.$vertical_name_array[$key]}+$balance_qty;
	
	echo "<tr>
			<td >".$sauda_group_name_array[$key]."</td>
			<td align=\"right\">0</td>
			<td align=\"right\">".$sauda_group_booked[$key]."</td>
			<td align=\"right\">".$sauda_group_booked_case[$key]."</td>
			<td align=\"right\">".$balance_qty."</td>
		  </tr>";
	if(${verticalcount.$vertical_name_array[$key]}==count(${vertical_value_name_array.$vertical_name_array[$key]}))
	{
		echo "<tr style=\"font-weight:bold;\">
			<td>Total</td>
			<td align=\"right\">0</td>
			<td align=\"right\">".${valbookedMT.$vertical_name_array[$key]}."</td>
			<td align=\"right\">".${valbookedcase.$vertical_name_array[$key]}."</td>
			<td align=\"right\">".${valremain.$vertical_name_array[$key]}."</td>
		  </tr>";
	}
	  
   	$verticalcount++;
}
echo "<tr style=\"font-weight:bold;\">
			<td>Grand Total</td>
			<td align=\"right\">0</td>
			<td align=\"right\">".$total_booked."</td>
			<td align=\"right\">".$total_booked_case."</td>
			<td align=\"right\">".$total_remain."</td>
		  </tr>";
		  
echo "</table>";
}

?>