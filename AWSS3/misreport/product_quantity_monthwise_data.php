<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysql_query($sql_sauda_filter);
$row_sauda_filter = mysql_fetch_array($res_sauda_filter);

$sauda_filter_value = $row_sauda_filter['sauda_allocation_basedon_filter'];

if($sauda_filter_value == 1)
{
	$sauda_table_value = 'product_group_master';
	$field_name1 = 'product_group_code';
	$field_name2 = 'product_group_name';
	$acronym = "PGM";
}
else if($sauda_filter_value == 2)
{
	$sauda_table_value = 'product_sub_group_master';
	$field_name1 = 'product_sub_group_code';
	$field_name2 = 'product_sub_group_name';
	$acronym = "PSGM";
}
else if($sauda_filter_value == 3)
{
	$sauda_table_value = 'product_brand_master';
	$field_name1 = 'product_brand_code';
	$field_name2 = 'product_brand_name';
	$acronym = "PBM";
}
else if($sauda_filter_value == 4)
{
	$sauda_table_value = 'product_master';
	$field_name1 = 'product_code';
	$field_name2 = 'product_name';
	$acronym = "PM";
}

$group_name = $acronym.".".$field_name2;
$group_code = $acronym.".".$field_name1;

$sql_count_product = "SELECT $group_name, $group_code FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$res_count_product = mysql_query($sql_count_product);
while($row_count_product = mysql_fetch_array($res_count_product)){
	$productgroupcode = $row_count_product[$field_name1];
	$productgroupname = $row_count_product[$field_name2];
	$prod_groupcodename_array[$productgroupcode] = $productgroupname;
}
//print_r($prod_groupcodename_array);	
	$current_date = date('Y-m-d');
	$end_date = date('Y-03-31',strtotime('next year'));
	$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	$emp_code = $_REQUEST['emp_code'];
	$cust_code = $_REQUEST['cust_code'];
	$product_group_name_array = array();
	
	$datearray = array();
	$sql_getdate = "SELECT DISTINCT SUBSTRING(visit_date,1,10) as visit_date FROM prev_order_counting_master WHERE customer_code = 'C/0000833' AND SUBSTRING(order_no,2,5) = 'E0078' AND SUBSTRING(visit_date,6,2) = '11'";
	$res_getdate = mysql_query($sql_getdate);
	while($row_getdate = mysql_fetch_array($res_getdate)){
		$get_date = $row_getdate['visit_date'];
		array_push($datearray,$get_date);
	}
	print_r($datearray);
	
	foreach($datearray as $val){
		foreach($prod_groupcodename_array as $index=>$value){
			$sql_product = "SELECT PM.prod_desc, POCM.visit_qty FROM product_master PM, prev_order_counting_master POCM WHERE POCM.product_code = PM.prod_code AND PM.product_group_code='".$index."' AND POCM.customer_code = 'C/0000833' AND SUBSTRING(POCM.order_no,2,5) = 'E0078' AND SUBSTRING(POCM.visit_date,1,10) = '".$val."'";
			$res_product = mysql_query($sql_product);
			$row_product = mysql_fetch_array($res_product);
			echo $prod_desc = $row_product['prod_desc']; 
		}
	}
	
	echo "<table border=\"1\">";
	echo "<tr>
			<td>Jan</td>
			<td>Feb</td>
			<td>Mar</td>
			<td>Apr</td>
			<td>May</td>
			<td>Jun</td>
			<td>Jul</td>
			<td>Aug</td>
			<td>Sep</td>
			<td>Oct</td>
			<td>Nov</td>
			<td>Dec</td>
		  </tr>"
?>


<?php
	
	$sql_distinct_prod = "SELECT DISTINCT prod_code as product_code FROM sauda_transaction_log WHERE SUBSTRING(sauda_date,6,2) = '".$month."' AND emp_code = '".$emp_code."' AND customer_code = '".$cust_code."'";
	$res_distinct_prod = mysql_query($sql_distinct_prod);
	while($row_distinct_prod = mysql_fetch_array($res_distinct_prod)){
		$prod_code = $row_distinct_prod['product_code'];
		$sql_prodgroup_name = "SELECT PGM.product_group_name, PGM.product_group_code FROM product_group_master PGM, product_master PM WHERE PGM.product_group_code = PM.product_group_code AND PM.prod_code = '".$prod_code."'";
		$res_prodgroup_name = mysql_query($sql_prodgroup_name);
		$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
		$prodgroupcode = $row_prodgroup_name['product_group_code'];;
		$prodgroupname = $row_prodgroup_name['product_group_name'];
		$product_group_name_array[$prodgroupcode] = $prodgroupname;
	}
	//print_r($product_group_name_array);
	//sort($product_group_name_array);
	$prodgroup_date_array = array();
	foreach($product_group_name_array as $index=>$val){
		$sql_getdate = "SELECT DISTINCT SUBSTRING(STL.sauda_date,1,10) as getdate FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND PM.product_group_code = '".$index."' AND SUBSTRING(sauda_date,6,2) = '".$month."'";
		$res_getdate = mysql_query($sql_getdate);
		while($row_getdate = mysql_fetch_array($res_getdate)){
			$get_date = $row_getdate['getdate'];
			$prodgroup_date_array[$index] = $get_date;
		}
	}
	
	if(!empty($product_group_name_array)){
		
	?>
    <table border="1">
      <tr>
        <td><?php echo $month_name; ?></td>
      </tr>
      <tr>
    <?php
		foreach($product_group_name_array as $index=>$val){
			echo "<td>".$val."</td>";
			echo $sql_getdate = "SELECT DISTINCT SUBSTRING(STL.sauda_date,1,10) as getdate FROM sauda_transaction_log STL, product_master PM WHERE STL.prod_code = PM.prod_code AND PM.product_group_code = '".$index."' AND SUBSTRING(STL.sauda_date,6,2) = '".$month."' AND STL.emp_code = '".$emp_code."' AND STL.customer_code = '".$cust_code."'";
			$res_getdate = mysql_query($sql_getdate);
			while($row_getdate = mysql_fetch_array($res_getdate)){
				$get_date = $row_getdate['getdate'];
				
				//$sql_prod_quantity = "SELECT PM.prod_desc, SUM(STL.convert_qty_two) FROM sauda_transaction_log STL, product_master PM WHERE SUBSTRING(STL.sauda_date,1,10) = '".$get_date."' AND STL.emp_code = '".$emp_code."' AND STL.customer_code = '".$cust_code."' AND STL.prod_code = PM.prod_code GROUP BY STL.prod_code ORDER BY PM.prod_desc ASC";
				//$res_prod_quantity = mysql_query($sql_prod_quantity);
				
				echo "<td>".$get_date."</td>";
			}
			echo "</tr>";
			echo "<tr>";
			
		}
	}
	else{
		echo "No records found";
	}
?>
  </tr>
  
<?php
	$prev_date = date('Y-m-d',strtotime('1st April this year'));
	$current_year = date('Y');
	$k = 16;
	for($i=4;$i<$k;$i++){
		if($i == 13){
			$i = 1;
			$k = 4;
			$current_year = date('Y',strtotime('next year'));
		}
		//echo $i."\t";
		$number = cal_days_in_month(CAL_GREGORIAN, $i, $current_year);
		//echo $number."\t";
		for($j=1;$j<=$number;$j++){
			$date_string = $current_year."-".$i."-".$j;
			$date_string = date('Y-m-d',strtotime(''.$date_string.''));
			echo $date_string."\t";
		}
		break;
	}
mysql_close($link);
?>
	
	