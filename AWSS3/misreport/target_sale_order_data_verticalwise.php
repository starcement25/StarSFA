<?php
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_code'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$vertical=$_REQUEST['vertical'];
$cust_type=$_REQUEST['cust_type'];

$current_date = date('Y-m-d');
$employeeval=str_replace("'","",$emp_code);
$employeevalarray=explode(",",$employeeval);
$employee_hierarchy_val='';
foreach($employeevalarray as $employeevalfinal)
{
$employee_hierarchy=return_employee_hierarchy($employeevalfinal);
$employee_hierarchy_val.="'".$employeevalfinal."'".",".$employee_hierarchy.",";
}
$employee_hierarchy_val=substr($employee_hierarchy_val,0,-1);
$sqldistdesig="SELECT designation FROM employee_master WHERE designation='RSM' AND emp_code IN(".$employee_hierarchy_val.")";
$rsdistdesig=mysql_query($sqldistdesig);
$countdistdesig=mysql_num_rows($rsdistdesig);
//echo $employee_upper_hierarchy_val;
//exit();
$verticalarray=array();
if($vertical=='all')
	{
		$sqldistinctvertical="SELECT DISTINCT vertical_value FROM product_master WHERE acedns='Y'";
		$rsdistinctvertical=mysql_query($sqldistinctvertical);
		$countdistinctvertical=mysql_num_rows($rsdistinctvertical);
		while($rowdistinctvertical=mysql_fetch_array($rsdistinctvertical))
		{
			$secondary_row.= "<td align=\"center\" colspan=\"3\">$rowdistinctvertical[vertical_value]</td>";
			array_push($verticalarray,$rowdistinctvertical['vertical_value']);
		}
		$colspan_vertical=$countdistinctvertical+1;
	}
	else
	{
	 $colspan_vertical='2';
	 $vertical=str_replace("'","",$vertical);
	 $secondary_row= "<td align=\"center\" colspan=\"3\">$vertical</td>";
	 array_push($verticalarray,$vertical);
	}
$colspan_two = '3';
$colspan_header_upper = count($verticalarray);
$count = 1;
$month_abrev=date('M',strtotime($start_date));
//echo $start_date;
$month = date(substr($start_date,5,2));
$year=date(substr($start_date,0,4));
$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
$column_target=strtolower($month_abrev).'_'.$days.'_target';
/*----> Total target & sale <----*/
/*SELECT CM.customer_name,SAS.may_31_target AS target_val,PM.vertical_value,CM.dns_customer_code, PD.amount AS sale_val 
FROM customer_route_emp_relation CRR LEFT JOIN customer_master CM ON CM.customer_code=CRR.customer_code AND CM.customer_name !='' 
AND CM.cust_type!='R' AND CRR.emp_code IN ('E0044','E0049','E0048','E0047','E0050','E0066','E0081','E0045','E0046','E0051','E0052','E0087','E0053','
E0054','E0055','E0044')  LEFT JOIN purchase_details PD ON CM.customer_code=PD.distributor_code AND SUBSTRING(PD.invoice_date,1,10) 
BETWEEN '2019-05-01' AND '2019-05-15' LEFT JOIN product_master PM ON PD.prod_code=PM.prod_code LEFT JOIN self_appraisal_summary SAS 
ON CM.dns_customer_code=SAS.customer_code 
AND PM.vertical_value=SAS.vertical GROUP BY CM.dns_customer_code,PM.vertical_value ORDER BY CM.customer_name ASC,SAS.vertical ASC*/
	   if($cust_type=='primary')
	   {
		   $employee_hierarchy_condition= " CRR.emp_code IN (".$employee_hierarchy_val.")";
		   $cust_type_codition=" AND CM.cust_type!='R'";
		   $sale_condition=" LEFT JOIN purchase_details PD ON CM.customer_code=PD.distributor_code AND SUBSTRING(PD.invoice_date,1,10) 
		   					BETWEEN '".$start_date."' AND '".$end_date."' LEFT JOIN product_master PM ON PD.prod_code=PM.prod_code";
		   $cust_type_sel_val=" PD.amount AS sale_val ";
		   
	   /*$sql_total_target_sale = "SELECT CM.customer_name,PM.vertical_value,CM.dns_customer_code,$cust_type_sel_val 
			FROM customer_route_emp_relation CRR LEFT JOIN customer_master CM ON CM.customer_code=CRR.customer_code AND CM.customer_name IS NOT NULL 
			".$cust_type_codition."  AND ".$employee_hierarchy_condition.$sale_condition."
			GROUP BY CM.customer_code,PM.vertical_value ORDER BY CM.customer_name ASC,PM.vertical_value ASC";*/
	  //exit();
	  	$sql_total_target_sale = "SELECT CM.customer_name,PD.vertical_value,CM.dns_customer_code,PD.total_amount
									FROM   
									(SELECT CMA.customer_name,CMA.dns_customer_code,CMA.customer_code         
       								 FROM   customer_master CMA LEFT JOIN customer_route_emp_relation CRR 
        							ON CMA.customer_code=CRR.customer_code AND CMA.customer_name IS NOT NULL AND CMA.cust_type!='R' AND 
									".$employee_hierarchy_condition." GROUP  BY CRR.customer_code) CM
									LEFT JOIN 
									(SELECT  vertical_value,Sum(amount) as total_amount,distributor_code,invoice_date  
                  					FROM   purchase_details where SUBSTRING(invoice_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."'
                  					GROUP  BY distributor_code,vertical_value) PD 
									 ON CM.customer_code = PD.distributor_code ORDER BY CM.customer_name ASC,PD.vertical_value ASC";
		$res_total_target_sale = mysql_query($sql_total_target_sale);
		$total_rows = mysql_num_rows($res_total_target_sale);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
             <tr class='TDHEAD'><td colspan='20' align='center'>Target Vs Sale Report - <?php echo strtoupper($cust_type);?> - From <?php echo date('d-m-Y',strtotime($start_date));?> To <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
             <tr class="TDHEAD_SUB">
                <td></td>
                <td></td>
                <?php echo $secondary_row;?>
              </tr>
              <tr class="TDHEAD_SUB">
                <td>SL No</td>
                <td>Customer Name</td>
                <?php 
				foreach($verticalarray as $verticalvalfinal)
				{?>
                 <td align="center">TGT</td>
				  <?php if($cust_type=='secondary'){?>
                	<td  align="center">Sale Order</td>
                 	<td align="center">Deficit</td>
                 <?php }?>
                <?php if($cust_type=='primary'){?>
                	<td  align="center">Sale</td>
                 	<td  align="center">Deficit</td>
                 <?php }
				 }?>
              </tr>
            <?php
			$res_total_calls = mysql_query($sql_total_calls);
			$loop_count=0;
			$customer_code_array=array();
			while($row_total_target_sale = mysql_fetch_array($res_total_target_sale)){
				$table_column_data_primary='';
			    $table_column_data_secondary='';
				$sub_total_primary_amount='';
			    $sub_total_secondary_amount='';

				$dns_customer_code=$row_total_target_sale['dns_customer_code'];
				$vertical = $row_total_target_sale['vertical_value'];
				
				//${target.$dns_customer_code.$vertical}=$row_total_target_sale['target_val'];
				${sale.$dns_customer_code.$vertical}=round($row_total_target_sale['total_amount'],0);
				
				//${defficit.$dns_customer_code.$vertical}=${target.$dns_customer_code.$vertical}-${sale.$dns_customer_code.$vertical};
				if(!in_array($dns_customer_code,$customer_code_array) && $dns_customer_code!='')
				{
				  ${customer_name.$dns_customer_code} = $row_total_target_sale['customer_name'];
				  array_push($customer_code_array,$dns_customer_code);
				}
			}
			//print_r($customer_code_array);
			//echo ${customer_name.'C1875'};
			foreach($customer_code_array as $customer_code_val)
			{
				$verticalcnt=1;
				$sqltargetval="SELECT $column_target AS target_val,vertical FROM self_appraisal_summary WHERE customer_code='".$customer_code_val."'";
				$rstargetval=mysql_query($sqltargetval);
				while($rowtargetval=mysql_fetch_array($rstargetval))
				{
					$vertical=$rowtargetval['vertical'];
					${target.$customer_code_val.$vertical}=round($rowtargetval['target_val'],0);
				}
				foreach($verticalarray as $verticalvalfinal)
				{
				  ${defficit.$customer_code_val.$verticalvalfinal}=${target.$customer_code_val.$verticalvalfinal}-${sale.$customer_code_val.$verticalvalfinal};

				  if($verticalcnt==1) $color_name='LIGHTPINK';
				  if($verticalcnt==2) $color_name='KHAKI';
				  if($verticalcnt==3) $color_name='LAVENDER';
				  if($verticalcnt==4) $color_name='LIGHTGREEN';
				  ${totaltargetval.$verticalvalfinal}=${totaltargetval.$verticalvalfinal}+${target.$customer_code_val.$verticalvalfinal};
				  ${totalsaleval.$verticalvalfinal}=${totalsaleval.$verticalvalfinal}+${sale.$customer_code_val.$verticalvalfinal};
				  
				  if(${target.$customer_code_val.$verticalvalfinal}==0) ${target.$customer_code_val.$verticalvalfinal}='-';
				  else   												  ${target.$customer_code_val.$verticalvalfinal}=number_format(${target.$customer_code_val.$verticalvalfinal},2);
				   if(${sale.$customer_code_val.$verticalvalfinal}==0) ${sale.$customer_code_val.$verticalvalfinal}='-';
				  else   												 ${sale.$customer_code_val.$verticalvalfinal}=number_format(${sale.$customer_code_val.$verticalvalfinal},2);
				   if(${defficit.$customer_code_val.$verticalvalfinal}==0) ${defficit.$customer_code_val.$verticalvalfinal}='-';
				  else   												 ${defficit.$customer_code_val.$verticalvalfinal}=number_format(${defficit.$customer_code_val.$verticalvalfinal},2);
	
					
				  ${target_sale_data_TD.$customer_code_val}.= "<td align='right' style='background:$color_name'>".${target.$customer_code_val.$verticalvalfinal}."</td>
				  											<td align='right' style='background:$color_name'>".${sale.$customer_code_val.$verticalvalfinal}."</td>
															<td align='right' style='background:$color_name'>".${defficit.$customer_code_val.$verticalvalfinal}."</td>
															";
					$verticalcnt++;											
				}				
				echo "<tr>
						<td>".$count."</td>
						<td>".${customer_name.$customer_code_val}."</td>".${target_sale_data_TD.$customer_code_val}."
					  </tr>";
				$count++;
			}
			//For Grand total
			echo "<tr style='font-weight:bold;'><td colspan=\"2\">Total</td>";
			 $verticalcnttot=1;
			foreach($verticalarray as $verticalvalfinal)
				{
				  $verticalcnt=1;
				  if($verticalcnttot==1) $color_name='LIGHTPINK';
				  if($verticalcnttot==2) $color_name='KHAKI';
				  if($verticalcnttot==3) $color_name='LAVENDER';
				  if($verticalcnttot==4) $color_name='LIGHTGREEN';

					${totaldefficit.$verticalvalfinal}=${totaltargetval.$verticalvalfinal}-${totalsaleval.$verticalvalfinal};
				  echo"<td align='right' style='background:$color_name'>".number_format(${totaltargetval.$verticalvalfinal},2)."</td>
						<td align='right' style='background:$color_name'>".number_format(${totalsaleval.$verticalvalfinal},2)."</td>
						<td align='right' style='background:$color_name'>".number_format(${totaldefficit.$verticalvalfinal},2)."</td>";
				  $verticalcnttot++;		
				}
		    echo "</tr>";	?>
         </table>   	
		<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		}
		else{
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
	}//End of Primary
	//Start of secondary
	if($cust_type=='secondary')
   	{
	   $employee_hierarchy_condition= " SUBSTRING(POCM.order_no,2,5) IN (".$employee_hierarchy_val.")";
	   $cust_type_codition=" AND POCM.cust_type='R'";
	   $cust_type_sel_val=" SUM(POCM.amount) AS sale_val ";
	  /* SELECT  $cust_type_condion,EM.designation,EM.emp_name,PM.product_group_code,EM.emp_code
						FROM employee_master EM LEFT JOIN `prev_order_counting_master` POCM ON SUBSTRING(POCM.order_no,2,5)=EM.emp_code 
						AND SUBSTRING(POCM.visit_date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' 
						LEFT JOIN `product_master` PM ON POCM.product_code=PM.prod_code AND PM.product_group_code IN(".$product_group_val.")
					WHERE 
					".$employee_hierarchy_condition."  
					GROUP BY EM.emp_code,PM.product_group_code ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val),
					FIELD(PM.product_group_code, $product_group_val)*/
	   /*$sql_total_target_sale = "SELECT EM.emp_name,PM.vertical_value,EM.dns_emp_code,$cust_type_sel_val 
								FROM employee_master EM LEFT JOIN prev_order_counting_master POCM ON EM.emp_code=SUBSTRING(POCM.order_no,2,5) 
								AND SUBSTRING(POCM.visit_date,1,10) 
								BETWEEN '".$start_date."' AND '".$end_date."' ".$cust_type_codition."  AND ".$employee_hierarchy_condition." LEFT JOIN product_master PM ON POCM.product_code=PM.prod_code 
								GROUP BY EM.emp_code,PM.vertical_value ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val),PM.vertical_value ASC";*/
		//exit();
	    if($countdistdesig >1)
	  	{
			$sql_total_target_sale = "SELECT EM.emp_name,PM.vertical_value,EM.dns_emp_code,$cust_type_sel_val 
								FROM employee_master EM LEFT JOIN prev_order_counting_master POCM ON SUBSTRING(POCM.order_no,2,5)=EM.emp_code 
								AND SUBSTRING(POCM.visit_date,1,10) 
								BETWEEN '".$start_date."' AND '".$end_date."' AND ".$employee_hierarchy_condition." AND  EM.designation IN('RSM','ASM','SO','SR')  
								LEFT JOIN product_master PM ON POCM.product_code=PM.prod_code ".$cust_type_codition."
								GROUP BY EM.emp_code,PM.vertical_value ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val),PM.vertical_value ASC";
		}
		else
		{
		   $sql_total_target_sale = "SELECT EM.emp_name,PM.vertical_value,EM.dns_emp_code,$cust_type_sel_val 
					FROM employee_master EM LEFT JOIN prev_order_counting_master POCM ON SUBSTRING(POCM.order_no,2,5)=EM.emp_code 
					AND SUBSTRING(POCM.visit_date,1,10) 
					BETWEEN '".$start_date."' AND '".$end_date."'   
					LEFT JOIN product_master PM ON POCM.product_code=PM.prod_code ".$cust_type_codition."
					WHERE ".$employee_hierarchy_condition." AND  EM.designation IN('RSM','ASM','SO','SR')
					GROUP BY EM.emp_code,PM.vertical_value ORDER BY FIELD(EM.emp_code, $employee_hierarchy_val),PM.vertical_value ASC";
		}
		$res_total_target_sale = mysql_query($sql_total_target_sale);
		$total_rows = mysql_num_rows($res_total_target_sale);
		if($total_rows>0){
			?><table width='100%' class='border' border='1' style='border-collapse:collapse;' cellpadding='6px'>
             <tr class='TDHEAD'><td colspan='20' align='center'>Target Vs Sale Report - <?php echo strtoupper($cust_type);?> - From <?php echo date('d-m-Y',strtotime($start_date));?> To <?php echo date('d-m-Y',strtotime($end_date));?></td></tr>
             <tr class="TDHEAD_SUB">
                <td></td>
                <td></td>
                <?php echo $secondary_row;?>
              </tr>
              <tr class="TDHEAD_SUB">
                <td>SL No</td>
                <td>Employee Name</td>
                <?php 
				foreach($verticalarray as $verticalvalfinal)
				{?>
                 <td align="center">TGT</td>
				  <?php if($cust_type=='secondary'){?>
                	<td  align="center">Sale Order</td>
                 	<td align="center">Deficit</td>
                 <?php }
				 }?>
              </tr>
            <?php
			$res_total_calls = mysql_query($sql_total_calls);
			$loop_count=0;
			$emp_code_array=array();
			while($row_total_target_sale = mysql_fetch_array($res_total_target_sale)){
				$table_column_data_primary='';
			    $table_column_data_secondary='';
				$sub_total_primary_amount='';
			    $sub_total_secondary_amount='';

				$dns_emp_code=$row_total_target_sale['dns_emp_code'];
				$vertical = $row_total_target_sale['vertical_value'];
				
				//${target.$dns_customer_code.$vertical}=$row_total_target_sale['target_val'];
				${sale.$dns_emp_code.$vertical}=round($row_total_target_sale['sale_val'],0);
				
				//${defficit.$dns_customer_code.$vertical}=${target.$dns_customer_code.$vertical}-${sale.$dns_customer_code.$vertical};
				if(!in_array($dns_emp_code,$emp_code_array) && $dns_emp_code!='')
				{
				  ${emp_name.$dns_emp_code} = $row_total_target_sale['emp_name'];
				  array_push($emp_code_array,$dns_emp_code);
				}
			}
			//print_r($customer_code_array);
			//echo ${customer_name.'C1875'};
			foreach($emp_code_array as $emp_code_val)
			{
				$verticalcnt=1;
				$sqltargetval="SELECT $column_target AS target_val,vertical FROM self_appraisal_emp_wise WHERE emp_code='".$emp_code_val."'";
				$rstargetval=mysql_query($sqltargetval);
				while($rowtargetval=mysql_fetch_array($rstargetval))
				{
					$vertical=$rowtargetval['vertical'];
					${target.$emp_code_val.$vertical}=round($rowtargetval['target_val'],0);
				}
				foreach($verticalarray as $verticalvalfinal)
				{
				  ${defficit.$emp_code_val.$verticalvalfinal}=${target.$emp_code_val.$verticalvalfinal}-${sale.$emp_code_val.$verticalvalfinal};

				  if($verticalcnt==1) $color_name='LIGHTPINK';
				  if($verticalcnt==2) $color_name='KHAKI';
				  if($verticalcnt==3) $color_name='LAVENDER';
				  if($verticalcnt==4) $color_name='LIGHTGREEN';
				  ${totaltargetval.$verticalvalfinal}=${totaltargetval.$verticalvalfinal}+${target.$emp_code_val.$verticalvalfinal};
				  ${totalsaleval.$verticalvalfinal}=${totalsaleval.$verticalvalfinal}+${sale.$emp_code_val.$verticalvalfinal};
				  
				  if(${target.$emp_code_val.$verticalvalfinal}==0) ${target.$emp_code_val.$verticalvalfinal}='-';
				  else   												  ${target.$emp_code_val.$verticalvalfinal}=number_format(${target.$emp_code_val.$verticalvalfinal},2);
				   if(${sale.$emp_code_val.$verticalvalfinal}==0) ${sale.$emp_code_val.$verticalvalfinal}='-';
				  else   												 ${sale.$emp_code_val.$verticalvalfinal}=number_format(${sale.$emp_code_val.$verticalvalfinal},2);
				   if(${defficit.$emp_code_val.$verticalvalfinal}==0) ${defficit.$emp_code_val.$verticalvalfinal}='-';
				  else   												 ${defficit.$emp_code_val.$verticalvalfinal}=number_format(${defficit.$emp_code_val.$verticalvalfinal},2);
	
					
				  ${target_sale_data_TD.$emp_code_val}.= "<td align='right' style='background:$color_name'>".${target.$emp_code_val.$verticalvalfinal}."</td>
				  											<td align='right' style='background:$color_name'>".${sale.$emp_code_val.$verticalvalfinal}."</td>
															<td align='right' style='background:$color_name'>".${defficit.$emp_code_val.$verticalvalfinal}."</td>
															";
					$verticalcnt++;											
				}				
				echo "<tr>
						<td>".$count."</td>
						<td>".${emp_name.$emp_code_val}."</td>".${target_sale_data_TD.$emp_code_val}."
					  </tr>";
				$count++;
			}
			//For Grand total
			echo "<tr style='font-weight:bold;'><td colspan=\"2\">Total</td>";
			 $verticalcnttot=1;
			foreach($verticalarray as $verticalvalfinal)
				{
				 
				  if($verticalcnttot==1) $color_name='LIGHTPINK';
				  if($verticalcnttot==2) $color_name='KHAKI';
				  if($verticalcnttot==3) $color_name='LAVENDER';
				  if($verticalcnttot==4) $color_name='LIGHTGREEN';

					${totaldefficit.$verticalvalfinal}=${totaltargetval.$verticalvalfinal}-${totalsaleval.$verticalvalfinal};
				  echo"<td align='right' style='background:$color_name'>".number_format(${totaltargetval.$verticalvalfinal},2)."</td>
						<td align='right' style='background:$color_name'>".number_format(${totalsaleval.$verticalvalfinal},2)."</td>
						<td align='right' style='background:$color_name'>".number_format(${totaldefficit.$verticalvalfinal},2)."</td>";
				  $verticalcnttot++;		
				}
		    echo "</tr>";	?>
         </table>   	
		<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		}
		else{
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
    }//End of Secondary
	mysql_close($link);
	exit();
			    /*$prod_call=$row_total_calls['productive_calls'];
				$non_prod_call=$row_total_calls['non_productive_calls'];
				$total_calls = $prod_call + $non_prod_call;*/
				
				/*----> Productive Calls % <----*/
				/*$prod_call_percentage = ($prod_call/$total_calls)*100;
				$primary_amount = $row_total_calls['primary_amount'];
				$secondary_amount = $row_total_calls['secondary_amount'];
				$avg_secondary_amount=round(($secondary_amount/$days_present),2);*/
				//$lppc = number_format($lppc_count/$prod_call,2);
				/*$sqlstockaudit="SELECT COUNT(LO.trans_id)AS total_stk_audit FROM location LO WHERE  LO.emp_code='".$emp_code."' 
						AND (LO.trans_id LIKE 'SE%') AND SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$monthvalue."'";
				$rsstockaudit=mysql_query($sqlstockaudit) or die(mysql_error()." Error in total stock audit: ".$sqlstockaudit);
				$rowstockaudit=mysql_fetch_array($rsstockaudit);
				if(stk_audit=='yes' || retailer_care=='yes'){
					$stk_audit_customer_TD="<td align=\"right\" >".$rowstockaudit['total_stk_audit']."</td>";
				}*/
				/*if($designation=='RSM')
				{
					$rsm_emp_code=$emp_code;
				}
				
				$total_productive_call += $prod_call;
				$total_non_productive_call += $non_prod_call;
				$total_call +=$total_calls;
				$total_avg_secondary_amount +=$avg_secondary_amount;
				$total_days_present +=$days_present;
				$total_secondary_amount += $secondary_amount;
				//echo $primary_amount.'<br />';
				$total_primary_amount += $primary_amount;
				
				${total_productive_call_RSM.$rsm_emp_code} += $prod_call;
				${total_call_RSM.$rsm_emp_code} +=$total_calls;
				${total_avg_secondary_amount_RSM.$rsm_emp_code} +=$avg_secondary_amount;
				${total_days_present_RSM.$rsm_emp_code} +=$days_present;
				$strike_rate=round((($prod_call/$total_calls)*100),2);

				for($i=0;$i<2;$i++)
				{
					foreach($verticalarray as $verticalvalfinal)
					{
						$sqlverticaltotal="SELECT SUM(CASE WHEN POCM.cust_type='D' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS primary_amount, 
											SUM(CASE WHEN POCM.cust_type='R' AND POCM.order_no LIKE 'O%' THEN POCM.amount ELSE 0 END ) AS secondary_amount
											FROM `prev_order_counting_master` POCM,product_master PM
											WHERE POCM.product_code=PM.prod_code AND SUBSTRING(POCM.visit_date,1,10) BETWEEN '".$start_date."'  AND '".$end_date."' 
											AND SUBSTRING(POCM.order_no,2,5)='".$emp_code."' AND PM.vertical_value='".$verticalvalfinal."'";
						$rsverticaltotal=mysql_query($sqlverticaltotal);
						$rowverticaltotal=mysql_fetch_array($rsverticaltotal);
						${primary_amount.$verticalvalfinal}=$rowverticaltotal['primary_amount'];
						${secondary_amount.$verticalvalfinal}=$rowverticaltotal['secondary_amount'];
						if($i==0)
					    {
						 $sub_total_primary_amount=$sub_total_primary_amount+${primary_amount.$verticalvalfinal};
						 ${total_primary_amount.$verticalvalfinal}=${total_primary_amount.$verticalvalfinal}+${primary_amount.$verticalvalfinal};
						 //For RSM total
						 ${sub_total_primary_amount.$rsm_emp_code}=${sub_total_primary_amount.$rsm_emp_code}+${primary_amount.$verticalvalfinal};
						 ${total_primary_amount.$verticalvalfinal.$rsm_emp_code}=${total_primary_amount.$verticalvalfinal.$rsm_emp_code}+${primary_amount.$verticalvalfinal};
					    }
					   if($i==1)
					   {
						$sub_total_secondary_amount=$sub_total_secondary_amount+${secondary_amount.$verticalvalfinal};
						${total_secondary_amount.$verticalvalfinal}=${total_secondary_amount.$verticalvalfinal}+${secondary_amount.$verticalvalfinal};
						 //For RSM total
						${sub_total_secondary_amount.$rsm_emp_code}=${sub_total_secondary_amount.$rsm_emp_code}+${secondary_amount.$verticalvalfinal};
						${total_secondary_amount.$verticalvalfinal.$rsm_emp_code}=${total_secondary_amount.$verticalvalfinal.$rsm_emp_code}+${secondary_amount.$verticalvalfinal};
					   }
					   if($i==0)
					   {
					     $table_column_data_primary.= "<td align='right'>".number_format(${primary_amount.$verticalvalfinal},2)."</td>";
						 if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
						 {
						 	${table_column_data_primary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${total_primary_amount.$verticalvalfinal.$previous_rsm_emp_code},2)."</td>";
						 }
					   }
					   if($i==1)
					   {
						  $table_column_data_secondary.= "<td align='right'>".number_format(${secondary_amount.$verticalvalfinal},2)."</td>";
						   if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
						  {
						 	${table_column_data_secondary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${total_secondary_amount.$verticalvalfinal.$previous_rsm_emp_code},2)."</td>";
						  }
					   }
					}
				   if($i==0)
				   {
					 $table_column_data_primary.= "<td align='right'>".number_format($sub_total_primary_amount,2)."</td>";
					  if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
					  {
						 ${table_column_data_primary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${sub_total_primary_amount.$previous_rsm_emp_code},2)."</td>";
					  }
				   }
				   if($i==1)
				   {
					  $table_column_data_secondary.= "<td align='right'>".number_format($sub_total_secondary_amount,2)."</td>";
					  if(($designation=='RSM' && $count > 1) || (($loop_count+1)==$total_rows))
					  {
						 ${table_column_data_secondary.$previous_rsm_emp_code}.= "<td align='right'>".number_format(${sub_total_secondary_amount.$previous_rsm_emp_code},2)."</td>";
					  }
				   }
				}
				//echo $loop_count;
				if(($designation=='RSM' && $count > 1))
				{
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total</td>
					<td align='right'>".${total_days_present_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_productive_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".round(((${total_productive_call_RSM.$previous_rsm_emp_code}/${total_call_RSM.$previous_rsm_emp_code})*100),2)."</td>".
					${table_column_data_primary.$previous_rsm_emp_code}.${table_column_data_secondary.$previous_rsm_emp_code}.
					"<td align=\"right\" >".${total_avg_secondary_amount_RSM.$previous_rsm_emp_code}."</td>
				  </tr>";
				}
				/*if($designation=='RSM')
				{
					$rsm_emp_code=$emp_code;
				}*/
				/*echo "<tr>
						<td>".$count."</td>
						<td>".$emp_name."</td>
						<td>".$designation."</td>
						<td align=\"right\">".$days_present."</td>
						<td align=\"right\">".$total_calls."</td>
						<td align=\"right\">".$prod_call."</td>
						<td align=\"right\">".$strike_rate."</td>".$table_column_data_primary.$table_column_data_secondary.
						"<td align=\"right\" >".$avg_secondary_amount."</td>
					  </tr>";
				if(($loop_count+1)==$total_rows)
				{
					echo "<tr style='font-weight:bold;'>
					<td align='center' colspan='3'>$emp_name_previous(RSM) Total</td>
					<td align='right'>".${total_days_present_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".${total_productive_call_RSM.$previous_rsm_emp_code}."</td>
					<td align='right'>".round(((${total_productive_call_RSM.$previous_rsm_emp_code}/${total_call_RSM.$previous_rsm_emp_code})*100),2)."</td>".
					${table_column_data_primary.$previous_rsm_emp_code}.${table_column_data_secondary.$previous_rsm_emp_code}.
					"<td align=\"right\" >".${total_avg_secondary_amount_RSM.$previous_rsm_emp_code}."</td>
				  </tr>";
				}	  
				if($designation=='RSM')
				{	  
					$emp_name_previous = $row_total_calls['emp_name'];
					$previous_rsm_emp_code= $row_total_calls['emp_code'];
				}
				$count++;
				$loop_count++;
			}
			for($i=0;$i<2;$i++)
			 {
				foreach($verticalarray as $verticalvalfinal)
				{
					 if($i==0)
					 {
						$table_column_data_primary_total.= "<td align='right'>".number_format(${total_primary_amount.$verticalvalfinal},2)."</td>";
					 }
					 else
					 {
						$table_column_data_secondary_total.= "<td align='right'>".number_format(${total_secondary_amount.$verticalvalfinal},2)."</td>";
					 }
				}
			  if($i==0)
			   {
				   if(count($verticalarray) > 1)
				   {
					 $table_column_data_primary_total.= "<td align='right'>".number_format($total_primary_amount,2)."</td>";
				   }
				   else $table_column_data_primary_total.= "<td align='right'>".number_format(${total_primary_amount.$vertical},2)."</td>";
			   }
			   else
			   {
				   if(count($verticalarray) > 1)
				   {
					$table_column_data_secondary_total.= "<td align='right'>".number_format($total_secondary_amount,2)."</td>";
				   }
				   else $table_column_data_secondary_total.= "<td align='right'>".number_format(${total_secondary_amount.$vertical},2)."</td>";
			   }
			 }
			echo "<tr style='font-weight:bold;'>
			<td align='center' colspan='3'>Grand Total</td>
			<td align='right'>".$total_days_present."</td>
			<td align='right'>".$total_call."</td>
			<td align='right'>".$total_productive_call."</td>
			<td align='right'>".round((($total_productive_call/$total_call)*100),2)."</td>
			$table_column_data_primary_total".$table_column_data_secondary_total.
			"<td align=\"right\" >".$total_avg_secondary_amount."</td>
		  </tr>";
			?>
            </table>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
       <?php		
		}
		else{
			echo "<div style=\"font-weight:bold; color:red;\">No Records Found</div>";
		}
		mysql_close($link);*/
?>
