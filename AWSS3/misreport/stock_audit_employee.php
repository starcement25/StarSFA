<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$type = $_REQUEST['type'];

//echo $_SESSION['admin_login'];
if(strtoupper($_SESSION['admin_login']) == 'ADMIN'){
	$emp_hierarchy='';
	$emp_hierarchy_condition='';
	
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' 1 AND SUBSTRING(transaction_id,-19,5) IN('.$emp_hierarchy.') AND ';
}

if($type == 'today'){
	$today = date('Ymd');
	$date_condition = " SUBSTRING(transaction_id,-14,8) = '".$today."' ";
}
else if($type == 'mtd'){
	$current_month = date('Ym');
	$date_condition = " SUBSTRING(transaction_id,-14,6) = '".$current_month."' AND DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
}
else if($type == 'custom'){
	$date_condition = " SUBSTRING(transaction_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."' ";
}

if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
{
	$tonnage_column='<th>Tonnage</th>';
	$searchcondton=" ,weightage ";
	$excel_header_data_cond="\t"."Tonnage";
}
else { $tonnage_column=''; $searchcondton=''; $excel_header_data_cond='';}
$count = 1;
if(stk_audit_mfd_date=='yes')
{
	$searchcond=" ,manufacturing_date ";
}
else $searchcond='';
	$sql_cust_data = "SELECT DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%d-%m-%Y') AS date_selected, SUBSTRING(transaction_id,-19,5) AS emp_code, customer_code, product_code,quantity,remarks,UOM,weightage ".$searchcond.$searchcondton."
					FROM stock_audit 
					WHERE ".$emp_hierarchy_condition.$date_condition." ORDER BY DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%d-%m-%Y')";
	$res_cust_data = mysql_query($sql_cust_data);
	$total_row_check = mysql_num_rows($res_cust_data);
	
	$excel_header_data = "SL"."\t"."Date"."\t"."District"."\t"."Area"."\t"."Route Name"."\t"."Customer Code"."\t"."Customer Name"."\t"."Employee Code"."\t"."Employee Name"."\t"."Sub Brand"."\t"."Prod Description"."\t"."Qty".$excel_header_data_cond."\t"."Sale Rate"."\t"."Amount"."\t"."Customer Type"."\t"."Remarks"."\t".
	"Manufacturing Date";
	
	if($total_row_check>0){
		?>
        <table id="display_table" class="mall_class" border="1" style="width:130%;">
          <tr>
            <th>SL</th>
            <th style="width:10%;">Date</th>
            <th width="5%">District</th>
            <th>Area</th>
            <th>Route Name</th>
            <th>Customer Code</th>
            <th style="width:5%;">Customer Name</th>
            <th style="width:5%;">Employee Code</th>
            <th>Employee Name</th>
            <th>Sub Brand</th>
            <th style="width:5%;">Prod Description</th>
            <th>Qty<?php if(strtoupper($_SESSION['nick_name'])=='ELEGANT'){?>(MT)<?php }?></th><?php echo $tonnage_column;?>
            <th>Sale Rate</th>
            <th>Amount</th>
            <th>Customer Type</th>
            <th>Remarks</th>
             <?php if(stk_audit_mfd_date=='yes'){?>
            <th>Manufacturing Date</th>
             <?php }?>
          </tr>
        <?php
		$res_cust_data = mysql_query($sql_cust_data);
		while($row_cust_data = mysql_fetch_array($res_cust_data)){
			
			$date_selected = $row_cust_data['date_selected'];
			$emp_code = $row_cust_data['emp_code'];
			$customer_code = $row_cust_data['customer_code'];
			$product_code = $row_cust_data['product_code'];
			$visit_qty = $row_cust_data['quantity'];
			$d_instruction = $row_cust_data['remarks'];
			$UOM = $row_cust_data['UOM'];
			
			$sql_emp_name = "SELECT dns_emp_code, emp_name, district,state FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_name = mysql_query($sql_emp_name);
			$row_emp_name = mysql_fetch_array($res_emp_name);
			$emp_name = $row_emp_name['emp_name'];
			$dns_emp_code = $row_emp_name['dns_emp_code'];
			$state=$row_emp_name['state'];
			//$district = $row_emp_name['district'];
			
			$sql_customer_master = "SELECT dns_customer_code, customer_name, route_code, rds_tag, cust_type, coverage_type, district FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_master = mysql_query($sql_customer_master);
			$customer_exist_check = mysql_num_rows($res_customer_master);
			
			$res_customer_master = mysql_query($sql_customer_master);
			$row_customer_master = mysql_fetch_array($res_customer_master);
			$dns_customer_code = $row_customer_master['dns_customer_code'];
			$customer_name = $row_customer_master['customer_name'];
			$route_code = $row_customer_master['route_code'];
			$rds_tag = $row_customer_master['rds_tag'];
			$cust_type = $row_customer_master['cust_type'];
			$coverage_type = $row_customer_master['coverage_type'];
			$district = $row_customer_master['district'];

			if(mrp=='yes'){
				$sql_price = "SELECT mrp FROM mrp WHERE product_code = '".$product_code."'";
				$res_price = mysql_query($sql_price);
				$row_price = mysql_fetch_array($res_price);
				$prod_price = $row_price['mrp'];
			}
			
			if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')
			{
				$sql_price = "SELECT sale_rate FROM mrp WHERE product_code = '".$product_code."'";
				$res_price = mysql_query($sql_price);
				$row_price = mysql_fetch_array($res_price);
				$prod_price = $row_price['sale_rate'];
				if(state_wise_mrp=='yes')
				{
					$sqlstatecode="SELECT state_code FROM state_master WHERE state='".$state."'";
					$rsstatecode=mysql_query($sqlstatecode);
					$rowstatecode=mysql_fetch_array($rsstatecode);
					$state_code=$rowstatecode['state_code'];
					
					if($cust_type=='R'){
						$sql_price = "SELECT sale_rate FROM mrp WHERE product_code = '".$product_code."' AND state_code='".$state_code."'";
					}
					if($cust_type=='D'){
						$sql_price = "SELECT distributor_rate AS sale_rate FROM mrp WHERE product_code = '".$product_code."' AND state_code='".$state_code."'";
					}
					$res_price = mysql_query($sql_price);
					$row_price = mysql_fetch_array($res_price);
					$prod_price = $row_price['sale_rate'];
				}
			}
			
			$sql_prod_desc = "SELECT product_group_code, prod_desc,alias,conversion_factor FROM product_master WHERE prod_code = '".$product_code."'";
			$res_prod_desc = mysql_query($sql_prod_desc);
			$row_prod_desc = mysql_fetch_array($res_prod_desc);
			$product_group_code = $row_prod_desc['product_group_code'];
			$prod_desc = $row_prod_desc['prod_desc'];
			$alias=$row_prod_desc['alias'];
			$conversion_factor=$row_prod_desc['conversion_factor'];
			if($alias!='')
			{
				$prod_desc=$alias;
			}
			
			$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
			$res_prodgroup_name = mysql_query($sql_prodgroup_name);
			$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
			$prod_group_name = $row_prodgroup_name['product_group_name'];
			
			
			$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
			$res_route_name = mysql_query($sql_route_name);
			$row_route_name = mysql_fetch_array($res_route_name);
			$route_name = $row_route_name['route_name'];
			
			$sql_distributor_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
			$res_distributor_name = mysql_query($sql_distributor_name);
			$row_distributor_name = mysql_fetch_array($res_distributor_name);
			$distributor_name = $row_distributor_name['customer_name'];
			
			//$sql_rds_details = "SELECT route_code FROM customer_master WHERE customer_code = '".$rds_tag."'";
			$sql_rds_details = "SELECT GROUP_CONCAT(DISTINCT route_code SEPARATOR ',') FROM customer_master 
							WHERE FIND_IN_SET(customer_code,'".$rds_tag."')";
			$res_rds_details = mysql_query($sql_rds_details);
			$row_rds_details = mysql_fetch_array($res_rds_details);
			$rds_route_code = $row_rds_details['route_code'];
			
			if($cust_type == 'D'){
				$area_name = $route_name;
			}
			else{
				$sql_area_name = "SELECT route_name FROM route_master WHERE FIND_IN_SET(route_code,'".$rds_route_code."')";
				$res_area_name = mysql_query($sql_area_name);
				$area_name='';
				while($row_area_name = mysql_fetch_array($res_area_name)){
				 $area_name =$area_name.$row_area_name['route_name'].",";
				}
				$area_name=substr($area_name,0,-1);
				//$row_area_name = mysql_fetch_array($res_area_name);
				//$area_name = $row_area_name['route_name'];
			}
			if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
				{
					if(strtoupper($UOM)!='CASE' || $UOM=='')
					{
					$qtyconverted=$visit_qty/$conversion_factor;
					$sqlselmrp="SELECT mrp FROM mrp WHERE product_code='".$product_code."' AND UPPER(UOM)='CASE'";
					$rsselmrp=mysql_query($sqlselmrp);
					$rowselmrp=mysql_fetch_array($rsselmrp);
					$mrp=$rowselmrp['mrp'];
					$prod_price=$mrp;
					$visit_qty=$qtyconverted;
					}
					//$qty=number_format($qtyconverted,2);
				}
				if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
				{
					$weightage = $row_cust_data['weightage'];
					$weightage_val=round(($weightage/1000),3);
					$tonnage_row='<td>'.$weightage_val.'</td>';
					$tonnage_excel="\t".$weightage_val;
					
				}
				else {$tonnage_row='';$tonnage_excel='';}
			if($area_name == ''){
				$area_name = '';
			}
			
			if($visit_qty == 0)
				$visit_qty = '';
				
			if($rate == 0)
				$rate = '';
			else
				$rate = number_format($rate,2);
				
			if($amount == 0)
				$amount = '';
			else
				$amount = number_format($amount,2);
			
			if($customer_exist_check>0){
				
				if(stk_audit_mfd_date=='yes')
				{
					$displaycondition="<td align=\"center\">".$row_cust_data['manufacturing_date']."</td>";
					$maufacturing_date=$row_cust_data['manufacturing_date'];
				}
				else 
				{
					$displaycondition="";
					$maufacturing_date="";
				}
					
				$excel_body_data .= $count."\t".$date_selected."\t".$district."\t".$area_name."\t".$route_name."\t".$dns_customer_code."\t".$customer_name."\t".$dns_emp_code."\t".$emp_name."\t".$prod_group_name."\t".$prod_desc."\t".$visit_qty.$tonnage_excel."\t".$prod_price."\t".($visit_qty*$prod_price)."\t".$cust_type.
				"\t".$d_instruction."\t".$maufacturing_date."\n";
				echo "<tr>
						<td>".$count."</td>
						<td>".$date_selected."</td>
						<td>".$district."</td>
						<td>".$area_name."</td>
						<td><div class='retrict'>".$route_name."</div></td>
						<td>".$dns_customer_code."</td>
						<td><div class='retrict'>".$customer_name."</div></td>
						<td>".$dns_emp_code."</td>
						<td>".$emp_name."</td>
						<td>".$prod_group_name."</td>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$visit_qty."</td>".$tonnage_row."
						<td align=\"right\">".$prod_price."</td>
						<td align=\"right\">".($visit_qty*$prod_price)."</td>
						<td align=\"center\">".$cust_type."</td>
						<td align=\"center\"><div class='retrict'>".$d_instruction."</div></td>".$displaycondition."
					  </tr>";
				$count++;
			}
			
		}
		
		$excel_data = $excel_header_data."\n".$excel_body_data;
		$_SESSION['excel_data'] = $excel_data;

?>
</table>
<br>
<div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsvnew();" >
</div>
<?php }
	else{
		echo "<tr><td>No Record Found</td></tr>";
	}
?>