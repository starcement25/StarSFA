<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/

$customer_info_val = $_REQUEST['customer_info_val'];
$fetchval = $_REQUEST['fetchval'];
$customer_code = $_REQUEST['customer_code'];
$distributor = $_REQUEST['distributor'];

/*--------> Branch Data Populate <--------*/
if(rtrim($customer_info_val) == 'last_days_1'){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	 $todaydate =$year.'-'.$month.'-'.$date;
	 $numericprevdate=date('Y-m-d', strtotime("-$fetchval days,$todaydate "));
	$sqlquery="SELECT POCM.order_no,POCM.customer_code,POCM.product_code,POCM.visit_qty,POCM.d_instruction,POCM.rate,POCM.amount,
			SUBSTRING(POCM.visit_date,1,10) as entry_date,PM.prod_desc
			FROM prev_order_counting_master POCM,product_master PM WHERE POCM.product_code=PM.prod_code AND 
			POCM.customer_code='".$customer_code."' AND POCM.order_no LIKE 'O%' AND 
			SUBSTRING(POCM.visit_date,1,10) >='".$numericprevdate."' ORDER BY PM.prod_desc ASC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$select_control='<table cellpadding="5" cellspacing="2" width="60%" style="height: 200px;overflow-y: scroll;display:block;" align="left" class="border"><tr class="TDHEAD_SUB">
                                <td align="left" width="60%"><b>SKU</b></td>
                                <td align="left" width="15%">
                                   <b>Qty</b>
                                </td>
                                <td align="left" width="25%">
                                  <b>Date of Entry</b>
                                </td>
                             </tr >';
	if($count>0){
		while($rowsorderquery = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowsorderquery['customer_code'];
			$order_no=$rowsorderquery['order_no'];
			$product_code=$rowsorderquery['product_code'];
			$order_qty=$rowsorderquery['visit_qty'];
			$entry_date=$rowsorderquery['entry_date'];
			$d_instruction=$rowsorderquery['d_instruction'];
			$rate=$rowsorderquery['rate'];
			$amount=$rowsorderquery['amount'];
			$prod_desc=$rowsorderquery['prod_desc'];
			
                          $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">'.$prod_desc.'</td>
                                 <td align="right">'.$order_qty.'</td>
                                <td align="left">'.date("d/m/Y",strtotime($entry_date)).'</td>
                             </tr>   
							';
		}
	}
	else
	{
		$select_control.='<tr>
                                <td align="left"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
if(rtrim($customer_info_val) == 'last_days_2'){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	 $todaydate =$year.'-'.$month.'-'.$date;
	 $numericprevdate=date('Y-m-d', strtotime("-$fetchval days,$todaydate "));
	/*$sqlquery="SELECT POCM.product_code,SUM(POCM.visit_qty) as total_qty,
			SUBSTRING(POCM.visit_date,1,10) as last_trans_date,PM.prod_desc
			FROM prev_order_counting_master POCM,product_master PM WHERE POCM.product_code=PM.prod_code AND 
			POCM.customer_code='".$customer_code."' AND POCM.order_no LIKE 'O%' AND 
			SUBSTRING(POCM.visit_date,1,10) >='".$numericprevdate."' grouP BY POCM.product_code,SUBSTRING(POCM.visit_date,1,10) 
			ORDER BY SUBSTRING(POCM.visit_date,1,10) DESC";*/
	$sqlquery="SELECT POCM.product_code,SUM(POCM.visit_qty) as total_qty,
			MAX(SUBSTRING(POCM.visit_date,1,10)) as last_trans_date,PM.prod_desc
			FROM prev_order_counting_master POCM,product_master PM WHERE POCM.product_code=PM.prod_code AND 
			POCM.customer_code='".$customer_code."' AND POCM.order_no LIKE 'O%' AND 
			SUBSTRING(POCM.visit_date,1,10) >='".$numericprevdate."' GROUP BY POCM.product_code
			ORDER BY last_trans_date DESC";		
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$select_control='<table cellpadding="5" cellspacing="2" width="60%" style="height: 200px;overflow-y: scroll;display:block;" align="left" class="border"><tr class="TDHEAD_SUB">
                                <td align="left" width="60%"><b>SKU</b></td>
                                <td align="left" width="15%">
                                   <b>Qty</b>
                                </td>
                                <td align="left" width="25%">
                                  <b>Entry Date</b>
                                </td>
                             </tr >';
	if($count>0){
		while($rowsorderquery = mysqli_fetch_assoc($result))
		{
			$product_code=$rowsorderquery['product_code'];
			$total_qty=$rowsorderquery['total_qty'];
			$last_trans_date=$rowsorderquery['last_trans_date'];
			$prod_desc=$rowsorderquery['prod_desc'];
			
                          $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">'.$prod_desc.'</td>
                                 <td align="right">'.$total_qty.'</td>
                                <td align="left">'.date("d/m/Y",strtotime($last_trans_date)).'</td>
                             </tr>   
							';
		}
	}
	else
	{
		$select_control.='<tr>
                                <td align="left"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
if(rtrim($customer_info_val) == 'last_3_months'){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	 $todaydate =$year.'-'.$month.'-'.$date;
	 $numericprevdate=date('Y-m-d', strtotime("-$fetchval days,$todaydate "));
	/*$sqlquery="SELECT POCM.product_code,SUM(POCM.visit_qty) as total_qty,
			SUBSTRING(POCM.visit_date,1,10) as last_trans_date,PM.prod_desc
			FROM prev_order_counting_master POCM,product_master PM WHERE POCM.product_code=PM.prod_code AND 
			POCM.customer_code='".$customer_code."' AND POCM.order_no LIKE 'O%' AND 
			SUBSTRING(POCM.visit_date,1,10) >='".$numericprevdate."' GROUP BY SUBSTRING(POCM.visit_date,1,10),POCM.product_code ORDER BY SUBSTRING(POCM.visit_date,1,10) DESC";*/
	$sqlquery="SELECT POCM.product_code,SUM(POCM.visit_qty) as total_qty,
			MAX(SUBSTRING(POCM.visit_date,1,10)) as last_trans_date,PM.prod_desc
			FROM prev_order_counting_master POCM,product_master PM WHERE POCM.product_code=PM.prod_code AND 
			POCM.customer_code='".$customer_code."' AND POCM.order_no LIKE 'O%' AND 
			SUBSTRING(POCM.visit_date,1,10) >='".$numericprevdate."' GROUP BY POCM.product_code 
			ORDER BY last_trans_date DESC";		
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$select_control='<table cellpadding="5" cellspacing="2" width="60%" style="height: 200px;overflow-y: scroll;display:block;" align="left" class="border"><tr class="TDHEAD_SUB">
                                <td align="left" width="60%"><b>SKU</b></td>
                                <td align="left" width="15%">
                                   <b>Qty</b>
                                </td>
                                <td align="left" width="25%">
                                  <b>Last Trans Date</b>
                                </td>
                             </tr >';
	if($count>0){
		while($rowsorderquery = mysqli_fetch_assoc($result))
		{
			$product_code=$rowsorderquery['product_code'];
			$total_qty=$rowsorderquery['total_qty'];
			$last_trans_date=$rowsorderquery['last_trans_date'];
			$prod_desc=$rowsorderquery['prod_desc'];
			
                          $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">'.$prod_desc.'</td>
                                 <td align="center">'.$total_qty.'</td>
                                <td align="left">'.date("d/m/Y",strtotime($last_trans_date)).'</td>
                             </tr>   
							';
		}
	}
	else
	{
		$select_control.='<tr>
                                <td align="left"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
if(rtrim($customer_info_val) == 'target_ach'){
	$sqlquery="SELECT * FROM retailer_wise_target_ach where customer_code='".$customer_code."' AND acedns='Y'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$select_control='<table cellpadding="5" cellspacing="2" width="60%"  align="left" class="border">
					<tr class="TDHEAD"><td align="center" colspan="3"><b>Scheme Period:1st April , 21 to 30th June , 21</b></td></tr>
					<tr class="TDHEAD_SUB">
                                <td align="left" width="50%"><b>Scheme</b></td>
                                <td align="left" width="25%">
                                   <b>Target</b>
                                </td>
                                <td align="left" width="25%">
                                  <b>Achievement</b>
                                </td>
                             </tr >';
	if($count>0){
		while($rowtarget = mysqli_fetch_assoc($result))
		{
			$value_slab_target=$rowtarget['value_slab_target'];
			$value_slab_ach=$rowtarget['value_slab_ach'];
			$sku_count_target=$rowtarget['sku_count_target'];
			$sku_count_ach=$rowtarget['sku_count_ach'];
			$apr_freq_target=$rowtarget['apr_freq_target'];
			$apr_freq_ach=$rowtarget['apr_freq_ach'];
			$may_freq_target=$rowtarget['may_freq_target'];
			$may_freq_ach=$rowtarget['may_freq_ach'];
			$jun_freq_target=$rowtarget['jun_freq_target'];
			$jun_freq_ach=$rowtarget['jun_freq_ach'];
			
                          $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">Value Slab ( Rs)</td>
                                 <td align="center">'.number_format($value_slab_target).'</td>
                                <td align="center">'.number_format($value_slab_ach).'</td>
                             </tr> 
							  <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">SKU Count</td>
                                 <td align="center">'.$sku_count_target.'</td>
                                <td align="center">'.$sku_count_ach.'</td>
                             </tr>   
							  <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">Frequency April (Rs)</td>
                                 <td align="center">'.number_format($apr_freq_target).'</td>
                                <td align="center">'.number_format($apr_freq_ach).'</td>
                             </tr>   
							  <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">Frequency May (Rs)</td>
                                 <td align="center">'.number_format($may_freq_target).'</td>
                                <td align="center">'.number_format($may_freq_ach).'</td>
                             </tr>   
							  <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">Frequency June (Rs)</td>
                                 <td align="center">'.number_format($jun_freq_target).'</td>
                                <td align="center">'.number_format($jun_freq_ach).'</td>
                             </tr>     
							';
		}
	}
	else
	{
		$select_control.='<tr>
                                <td align="left"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
if(rtrim($customer_info_val) == 'focused_product'){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	 $todaydate =$year.'-'.$month.'-'.$date;
	 $emp_code=$_REQUEST['emp_code'];
	 $sqlbranches="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
	  $rsbranches=mysqli_query($link,$sqlbranches);
	  $rowbranches=mysqli_fetch_assoc($rsbranches);
	  $branch_code=$rowbranches['branch_code'];
	 $numericprevdate=date('Y-m-d', strtotime("-$fetchval days,$todaydate "));
	$sqlquery="SELECT PM.prod_desc,PM.prod_code,PM.pack_size
			FROM product_master PM WHERE focus='Y' ORDER BY PM.prod_desc ASC";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$select_control='<table cellpadding="5" cellspacing="2"  align="left" class="border" style="height: 200px;overflow-y: scroll;display:block;"><tr class="TDHEAD_SUB">
                                <td align="left" width="60%"><b>SKU</b></td>
								<td align="left" width="20%"><b>Price</b></td>
								<td align="left" width="20%"><b>Pack Size</b></td>
                             </tr >';
	if($count>0){
		while($rowsorderquery = mysqli_fetch_assoc($result))
		{
			$prod_code=$rowsorderquery['prod_code'];
			$prod_desc=$rowsorderquery['prod_desc'];
			$pack_size=$rowsorderquery['pack_size'];
			$sqlmrp="SELECT sale_rate FROM mrp where product_code='".$prod_code."' AND branch_code='".$branch_code."'";
			$rsmrp=mysqli_query($link,$sqlmrp);
			$rowmrp=mysqli_fetch_assoc($rsmrp);
			$mrp=$rowmrp['sale_rate'];
			
                          $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">'.$prod_desc.'</td>
								<td align="left">'.number_format($mrp,2).'</td>
								<td align="left">'.$pack_size.'</td>
                             </tr>   
							';
		}
	}
	else
	{
		$select_control.='<tr>
                                <td align="left" colspan="3"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
if(rtrim($customer_info_val) == 'proposed_sku'){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$todaydate =$year.'-'.$month.'-'.$date;
	$dayscount='90';
	$numericprevdate=date('Y-m-d', strtotime("-$dayscount days,$todaydate "));
	$date_condition=" AND SUBSTRING(visit_date,1,10) >='".$numericprevdate."' 
					   AND SUBSTRING(visit_date,1,10) <='".$todaydate."'";
	 
		 $sqlquery="SELECT POCM.customer_code,CM.rds_tag,GROUP_CONCAT(DISTINCT product_code SEPARATOR ',') AS concated_product FROM prev_order_counting_master POCM,customer_master CM WHERE POCM.customer_code='".$customer_code."'  AND POCM.order_no LIKE 'O%' ";
	$result = mysqli_query($link,$sqlquery);
	$rowquery = mysqli_fetch_assoc($result);
			$concated_product=$rowquery['concated_product'];
			$concated_product_array=explode(",",$concated_product);
			$concated_product_string='';
			foreach($concated_product_array as  $concated_product_val)
			{
				$concated_product_string=$concated_product_string."'".$concated_product_val."'".',';
			}
			$concated_product_string=substr($concated_product_string,0,-1);
			$select_control='<table cellpadding="5" cellspacing="2" width="60%" style="height: 200px;overflow-y: scroll;display:block;" align="left" class="border"><tr class="TDHEAD_SUB" width="100%">
                                <td align="left" width="80%"><b>SKU</b></td>
								<td align="left" width="20%"><b>Qty</b></td>
                             </tr >';
			
			$sqlproposedsku="SELECT * FROM(SELECT product_code,SUM(visit_qty) As total_qty FROM prev_order_counting_master WHERE 
							customer_code!='".$customer_code."' AND customer_code 
							IN(SELECT customer_code FROM customer_master WHERE acedns='Y' AND rds_tag='".$distributor."') AND 
							product_code NOT IN(".$concated_product_string.") AND order_no LIKE 'O%' $date_condition GROUP BY product_code ) AS SAT ORDER BY 2 DESC LIMIT 0,10";
			$rsproposedsku=mysqli_query($link,$sqlproposedsku);
			$count=mysqli_num_rows($rsproposedsku);
			if($count>0){
			while($rowproposedsku=mysqli_fetch_assoc($rsproposedsku))
			{				
				${product_code.$customer_code}=$rowproposedsku['product_code'];
				${total_qty.$customer_code}=$rowproposedsku['total_qty'];
				
				$sqlqueryprod="SELECT PM.prod_desc,PM.prod_code FROM product_master PM WHERE prod_code='".${product_code.$customer_code}."'";
				$resultprod = mysqli_query($link,$sqlqueryprod);
				$rowprod=mysqli_fetch_assoc($resultprod);
				$prod_desc=$rowprod['prod_desc'];
				  $select_control.='
							 <tr style="border-top: none; border-bottom: 1px solid #000000; border-left: none; border-right: none; " 
							 onMouseOver="this.bgColor='.SCROLL_COLOR.'" onMouseOut="this.bgColor=" class="body">
                                <td align="left">'.$prod_desc.'</td>
								<td align="left">'.${total_qty.$customer_code}.'</td>
                             </tr>   
							';

			}
			
		}
	else
	{
		$select_control.='<tr>
                                <td align="left"><b>No records.</b>
                                </td>
                             </tr>';
	}
	$select_control.='</table>';
	echo $select_control;
}
/*--------> Sale Access Data Populate <--------*/
if(rtrim($customer_info_val) == 'selectretailerinfo')
  {
	  $phone=$_REQUEST['phone'];
   /*$sqlretailerinfo="SELECT DISTINCT EM.emp_code,EM.emp_name,EM.reporting_to,RM.route_name,CM.customer_code,CM.rds_tag,CM.customer_name  FROM customer_master CM,employee_master EM,customer_route_emp_relation CRER,route_master RM  
   				WHERE CM.customer_code=CRER.customer_code AND CRER.route_code=RM.route_code AND CRER.emp_code=EM.emp_code AND CM.phone_no='".$phone."'";*/
				
	  $sqlretailerinfo="SELECT DISTINCT EM.emp_code,EM.emp_name,EM.reporting_to,RM.route_name,CM.customer_code,CM.rds_tag,CM.customer_name  FROM customer_master CM,employee_master EM,customer_route_emp_relation CRER,route_master RM  
   				WHERE CM.customer_code=CRER.customer_code AND CRER.route_code=RM.route_code AND 
				CRER.emp_code=EM.emp_code AND FIND_IN_SET('".$phone."',REPLACE(CM.phone_no,'/',','))";
			
	$rsretailerinfo=mysqli_query($link,$sqlretailerinfo);
	
	$countretailerinfo=mysqli_num_rows($rsretailerinfo);
	if($countretailerinfo > 0 && $phone!=''){
	$rowretailerinfo=mysqli_fetch_assoc($rsretailerinfo);
	$customer_name=$rowretailerinfo['customer_name'];
	$emp_name=$rowretailerinfo['emp_name'];
	$emp_code=$rowretailerinfo['emp_code'];
	$reporting_to=$rowretailerinfo['reporting_to'];
	$route_name=$rowretailerinfo['route_name'];
	$customer_code=$rowretailerinfo['customer_code'];
	$rds_tag=$rowretailerinfo['rds_tag'];
	
	$sqlreportingname="SELECT emp_name FROM employee_master WHERE emp_code='".$reporting_to."'";
	$rsreportingname=mysqli_query($link,$sqlreportingname);
	$rowreportingname=mysqli_fetch_assoc($rsreportingname);
	$reporting_name=$rowreportingname['emp_name'];
	
	$sqlrdsname="SELECT customer_name FROM customer_master WHERE customer_code='".$rds_tag."'";
	$rsrdsname=mysqli_query($link,$sqlrdsname);
	$rowrdsname=mysqli_fetch_assoc($rsrdsname);
	$rds_name=$rowrdsname['customer_name'];
	
	
	 $content="<table width=\"60%\" class=\"border\" cellpadding=\"5\" cellspacing=\"2\" align=\"left\">
	 			<tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">Customer Name</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">".$customer_name."&nbsp;<span class='error'>&nbsp;<img src='images/remarks.png' alt='remarks' onclick='javascript:show_remarks();' height='25' width='25'/></span><input type='hidden' name='customer_code' id='customer_code' value=\"$customer_code\"></td>
                 </tr>
				 <tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">Distributor Name</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">".$rds_name."</td><input type='hidden' name='distributor' id='distributor' value=\"$rds_tag\">
                 </tr>
				 <tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">Route Name</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">".$route_name."</td>
                 </tr>
				 <tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">Sales Ofiice Name</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">".$emp_name."</td><input type='hidden' name='emp_code' id='emp_code' value=\"$emp_code\">
                 </tr>
				 <tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">BM or RSM Name</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">".$reporting_name."</td>
                 </tr>
				 <tr><td width=\"30%\" align=\"left\" valign=\"top\" class=\"tbllogin\">City</td>
                       <td width=\"1%\" align=\"left\" valign=\"top\" class=\"tbllogin\">:</td>
                 <td align=\"left\" valign=\"top\" width=\"\">N/A</td>
                 </tr>
				 ";
	}
	else
	{
		$content="<table width=\"60%\" class=\"border\" cellpadding=\"5\" cellspacing=\"2\" align=\"left\">
	 			<tr><td width=\"100%\" align=\"center\" valign=\"top\" class=\"tbllogin\" colspan=\"3\">No Records</td></tr>";
	}
	$content.= "</table>";
	echo $content;
  }
/*--------> Headquarter Data Populate <--------*/
else if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
	
	$hq_control = "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	$hq_control .= "<option value=\"\">Select</option>";
	
	
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysqli_query($link,$sql_hq);
	while($row_hq = mysqli_fetch_assoc($res_hq)){
		$hq = $row_hq['hq'];
		$hq_string .= "'".$hq."',";
		$hq_control_option .= "<option value=\"'".$hq."'\">".$hq."</option>";
	}
	$hq_string = rtrim($hq_string,",");
	$hq_control .= "<option value=\"".$hq_string."\">All</option>";
	$hq_control .= $hq_control_option;
	$hq_control .= "</select>";
	echo $hq_control;
}
/*--------> Designation Data Populate <--------*/
else if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysqli_query($link,$sql_designation);
	while($row_designation = mysqli_fetch_assoc($res_designation)){
		$designation = $row_designation['designation'];
		$designation_string .= "'".$designation."',";
		echo "<option value=\"'".$designation."'\">".$designation."</option>";
	}
	$designation_string = rtrim($designation_string,",");
	echo "<option value=\"".$designation_string."\">All</option>";
	echo "</select>";
}
/*--------> Employee Data Populate <--------*/
else if($type == 'emp'){
	//$onclick = "designation_emp(this.value);";

	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	echo "<option value=\"".$emp_code_string_val."\">All</option>";
	echo "</select>";
}
else if($type == 'empbargain'){
	//$onclick = "designation_emp(this.value);";
	/*echo $sqlemp="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM WHERE 
				CRR.emp_code=EM.emp_code AND CRR.customer_code=CM.customer_code AND CRR.acedns='Y' 
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";*/		
	$emp_select_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$emp_select_control .="<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." 
				AND emp_code IN(SELECT SUBSTRING(sauda_no,3,5) FROM sauda_header) ORDER BY emp_name ASC";*/
	$sqlempbargain="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,employee_master EM,DO_master SH WHERE 
				SUBSTRING(SH.sauda_no,3,5)=EM.emp_code AND SH.customer_code=CM.customer_code 
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC"; 			
	$resempbragain = mysqli_query($link,$sqlempbargain);
	while($rowempbargain = mysqli_fetch_assoc($resempbragain)){
		$emp_code = $rowempbargain['emp_code'];
		$emp_name = $rowempbargain['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		$emp_select_control_options .="<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	$emp_select_control .="<option value=\"".$emp_code_string_val."\">All</option>";
	$emp_select_control .=$emp_select_control_options;
	echo $emp_select_control .="</select>";
}
else if($type == 'empdo'){
	//$onclick = "designation_emp(this.value);";
	/*echo $sqlemp="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM WHERE 
				CRR.emp_code=EM.emp_code AND CRR.customer_code=CM.customer_code AND CRR.acedns='Y' 
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";*/		
	$emp_select_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$emp_select_control .="<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." 
				AND emp_code IN(SELECT SUBSTRING(sauda_no,3,5) FROM sauda_header) ORDER BY emp_name ASC";*/
	$sqlempbargain="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,employee_master EM,DO_transaction DT WHERE 
				SUBSTRING(DT.DO_no,3,5)=EM.emp_code AND DT.customer_code=CM.customer_code 
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC"; 			
	$resempbragain = mysqli_query($link,$sqlempbargain);
	while($rowempbargain = mysqli_fetch_assoc($resempbragain)){
		$emp_code = $rowempbargain['emp_code'];
		$emp_name = $rowempbargain['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		$emp_select_control_options .="<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	$emp_select_control .="<option value=\"".$emp_code_string_val."\">All</option>";
	$emp_select_control .=$emp_select_control_options;
	echo $emp_select_control .="</select>";
}
else if($type == 'ASM' || $type == 'SO' || $type == 'TSI' || $type == 'DSM'){
	//$onclick = "emp_lev_two(this.value);";
	
	echo "<select name=\"employee_lev_one\" id=\"employee_lev_one\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND designation='".$type."' AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'stateemp'){
	//$onclick = "designation_emp(this.value);";

	echo "<select name=\"employee\" id=\"employee\" >";
	echo "<option value=\"\">Select</option>";
	
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
/*else if($type == 'stateemplevone'){
	$onclick = "state_route(this.value);";
	
	$sql_emp = "SELECT DISTINCT reporting_to FROM employee_master WHERE reporting_to!=''";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$reporting_to = $row_emp['reporting_to'];
		$emp_code_string .= "'".$reporting_to."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	
	echo "<select name=\"employee_one\" id=\"employee_one\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' 
				AND emp_code NOT IN (".$emp_code_string.") ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	//echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}*/
else if($type == 'statedist'){
	$onclick = "distributor_route(this.value);";
	
	echo "<select name=\"distributor\" id=\"distributor\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_distributor = "SELECT DISTINCT DRR.distributor_code,CM.customer_name FROM distributor_route_relation DRR,customer_master CM WHERE 
						DRR.distributor_code=CM.customer_code AND 
						DRR.emp_code IN(".$emp_code_string.") AND DRR.distributor_code IN(SELECT DISTINCT CMR.rds_tag FROM customer_master CMR,customer_route_emp_relation CRR WHERE CMR.customer_code=CRR.customer_code AND CRR.acedns='Y' AND CMR.cust_type='R') ORDER BY CM.customer_name ASC";
	$res_distributor = mysqli_query($link,$sql_distributor);
	while($row_distributor = mysqli_fetch_assoc($res_distributor)){
		$distributor_code = $row_distributor['distributor_code'];
		$customer_name = $row_distributor['customer_name'];
		echo "<option value=\"'".$distributor_code."'\">".$customer_name."</option>";
	}
	echo "</select>";
}
else if($type == 'route'){
	//$onclick = "designation_emp(this.value);";
	//echo "<select name=\"route\" id=\"route\" >";
	//echo "<option value=\"\">Select</option>";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");*/
	$sqlquerycustomerroute="SELECT DISTINCT DRR.route_code,RM.route_name FROM distributor_route_relation DRR,route_master RM,
								customer_route_emp_relation CRR WHERE DRR.route_code=RM.route_code AND RM.route_name!='' 
								AND CRR.route_code=RM.route_code AND CRR.acedns='Y' AND 
								DRR.distributor_code IN(".$distributor.") AND DRR.emp_code IN(".$emp_code_string.")  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_code=$rowscustomerroute['route_code'];
			$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM 
										WHERE CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$route_code."' AND CM.cust_type='R' AND CRR.acedns='Y' AND CM.rds_tag IN(".$distributor.")";						
			$resultcustomerroutedetails = mysqli_query($link,$sqlquerycustomerroutedetails);
			$countcustomerroutedetails=mysqli_num_rows($resultcustomerroutedetails);

			$route_name=$rowscustomerroute['route_name'];
			//echo "<option value=\"'".$route_code."'\">".$route_name."</option>";
			$content.="<tr>";
			$content.="<td align='left'>";
			$content.="<input type='checkbox' name='route[]' value='".$route_code."'  class='route_class_chk'/>".$route_name." - ".$countcustomerroutedetails."";
			$content.="</td>";
			$content.= "</tr>"; 
		}
	}
	echo $content;
	//echo "</select>";
}
else if($type == 'customersaudalimit'){
	echo "<select name=\"emp_name\" id=\"emp_name\" onChange=\"show_customer_name(this.value);\">";
	echo "<option value=\"\">Select</option>";
	/*$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition." 
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") AND EM.emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master) ORDER BY EM.emp_name ASC";*/
	$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition." 
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") ORDER BY EM.emp_name ASC";				
	$res_select_emp = mysqli_query($link,$sql_select_emp);
	while($row_select_emp = mysqli_fetch_assoc($res_select_emp)){
		$emp_code = $row_select_emp['emp_code'];
		$emp_name = $row_select_emp['emp_name'];
		
		$sql_check_menu_access = "SELECT emp_code FROM menu_access WHERE emp_code='".$emp_code."' AND not_accessible_menu != 'sauda'";
		$res_check_menu_access = mysqli_query($link,$sql_check_menu_access);
		$menu_access_rows = mysqli_num_rows($res_check_menu_access);
		if($menu_access_rows >1)
			echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
			$emp_code_string .= "'".$emp_code."',";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'stateroute'){
	//$onclick = "designation_emp(this.value);";
	//echo "<select name=\"route\" id=\"route\" >";
	//echo "<option value=\"\">Select</option>";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");*/
	echo "<select name=\"route\" id=\"route\" onChange=\"javascript:show_transportmode(this.value);\">";
	echo "<option value=\"\">Choose Route</option>";
	$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_master CM,route_master RM 
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
								CM.state_code IN(".$state.") AND CM.cust_type='D'  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_name=$rowscustomerroute['route_name'];
			$route_code=$rowscustomerroute['route_code'];
			$option_value_string.="<option value=\"'".$route_code."'\">".strtoupper($route_name)."</option>";
			$route_code_string .= "'".$route_code."',";
		}
	}
	$route_code_string = rtrim($route_code_string,",");
	echo "<option value=\"".$route_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
else if($type == 'stateroutecust'){
	echo "<select name=\"route\" id=\"route\" onChange=\"javascript:show_transportmode(this.value);\">";
	echo "<option value=\"\">Choose Route</option>";
	$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_master CM,route_master RM 
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND 
								CM.state_code IN(".$state.") AND CM.cust_type='D'  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_name=$rowscustomerroute['route_name'];
			$route_code=$rowscustomerroute['route_code'];
			$option_value_string.="<option value=\"'".$route_code."'\">".strtoupper($route_name)."</option>";
			$route_code_string .= "'".$route_code."',";
		}
	}
	$route_code_string = rtrim($route_code_string,",");
	//echo "<option value=\"".$route_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
if($type=="statetransport"){
	$state=$_REQUEST['state'];
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">Choose Trasport Mode </option>';
	$sqltransportmode="SELECT DISTINCT transport_mode FROM customer_master 
						WHERE  acedns='Y' AND 
						 state_code IN(".$state.")  AND transport_mode <> '' ORDER BY transport_mode ASC";					
	$rstransportmode=mysqli_query($link,$sqltransportmode);
	while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
	{		
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
	}
	echo $content.='</select>';
}
if($type=="stateroutetransport"){
	$state=$_REQUEST['state'];
	$route=$_REQUEST['route'];
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">Trasport Mode</option>';
	$sqltransportmode="SELECT DISTINCT transport_mode FROM customer_master 
						WHERE  acedns='Y' AND cust_type='D' AND 
						 state_code IN(".$state.") AND route_code IN(".$route.") AND transport_mode <> '' ORDER BY transport_mode ASC";					
	$rstransportmode=mysqli_query($link,$sqltransportmode);
	while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
	{		
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".strtoupper($rowtransportmode['transport_mode'])."</option>";
	}
	echo $content.='</select>';
}
else if($type == 'cust'){
	$empcodearray=explode(",",$empcode);
	$stringval="<select name=\"customer_code\" id=\"customer_code\" >";
	$stringval.="<option value=\"\">Select</option>";
	foreach ($empcodearray as $empcodeval)
	{
		//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
		$sql_customer = "SELECT CRR.customer_code,CM.customer_name FROM customer_route_emp_relation CRR,customer_master CM,employee_master EM WHERE  
						CRR.emp_code=EM.emp_code AND CM.customer_code=CRR.customer_code AND CRR.emp_code 
						IN(SELECT emp_code FROM employee_master WHERE   FIND_IN_SET( '".$empcodeval."',reporting_to)) 
						ORDER BY CM.customer_name ASC";
		$res_customer = mysqli_query($link,$sql_customer);
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$stringval.="<option value=\"'".$customer_code."'\">".$customer_name."</option>";
		}
	}
	$stringval.="<option value=\"all\">All</option>";
	$stringval.="</select>";
	echo $stringval;
}
else if($type == 'custvalidity'){
		echo "<select name=\"customer_code\" id=\"customer_code\" >";
	echo "<option value=\"\">Select</option>";

	$sqlquerycustomer="SELECT customer_code,customer_name FROM customer_master 
							WHERE state_code IN(".$state.") AND  customer_code IN(SELECT DISTINCT customer_code FROM DO_master WHERE is_approved='yes' 
							AND qty >0) ORDER BY customer_name ASC";
    $resultcustomer= mysqli_query($link,$sqlquerycustomer);
	$countcustomer=mysqli_num_rows($resultcustomer);
	if($countcustomer>0){
		while($rowscustomer = mysqli_fetch_assoc($resultcustomer))
		{
			$customer_code=$rowscustomer['customer_code'];
			$customer_name=$rowscustomer['customer_name'];
			$option_value_string.="<option value=\"'".$customer_code."'\">".$customer_name."</option>";
			$customer_code_string .= "'".$customer_code."',";
		}
	}
	$customer_code_string = rtrim($customer_code_string,",");
	echo "<option value=\"".$customer_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
else if($type == 'model'){
	echo "<select name=\"prod_code\" id=\"prod_code\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_product = "SELECT prod_code,prod_desc FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC";
	$res_product = mysqli_query($link,$sql_product);
	while($row_product = mysqli_fetch_assoc($res_product)){
		$prod_code = $row_product['prod_code'];
		$prod_desc = $row_product['prod_desc'];
		echo "<option value=\"'".$prod_code."'\">".$prod_desc."</option>";
	}
	echo "<option value=\"all\">All</option>";
	echo "</select>";
}

mysqli_close($link);
?>