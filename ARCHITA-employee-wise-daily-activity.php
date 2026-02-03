<?php
//echo "Work In Progress";die;
ob_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");
define("DB","acedns_ARCHITA");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require("include/config-email-setup.php");
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$current_date =$year.'-'.$month.'-'.$date;
$dateprevious=date('Y-m-d', strtotime("-1 days,$current_date "));
 $first_date='01'.'-'.$month.'-'.$year;
 $current_month_date=$year.'-'.$month;
 $month_abrev=date('M',strtotime($first_date));
 $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
 $column_target=strtolower($month_abrev).'_'.$days.'_target';
 $column_achievement=strtolower($month_abrev).'_'.$days.'_achievement';

//$current_date = '2018-09-19';
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$dateprevious."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$dateprevious)."' ";
//Header preperation
$vertical_array=array();
$common_header="<table width='100%' border='1' style='border-collapse:collapse;' cellpadding='6px'>";
$sqldistinctvertical="SELECT DISTINCT vertical_value FROM product_master WHERE acedns='Y' ORDER BY vertical_value ASC";
$rsdistinctvertical=mysqli_query($link,$sqldistinctvertical);
$cntvertical=mysqli_num_rows($rsdistinctvertical);
$common_header.="<tr  align=\"center\">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan=\"2\">Sec.Sale(Value)</td>
				<td colspan=\"$cntvertical\">Pr.Order (Value)</td>";
$k=0;				
while($rowdistinctvertical=mysqli_fetch_assoc($rsdistinctvertical))
{
    $distinct_vertical=$rowdistinctvertical['vertical_value'];
	$sqlprodgroupcnt="SELECT product_group_code,product_group_name FROM product_group_master WHERE vertical_value='".$distinct_vertical."'";
	$rsprodgroupcnt=mysqli_query($link,$sqlprodgroupcnt);
	$cntprodgroupcnt=mysqli_num_rows($rsprodgroupcnt);
	$colspangroupcount=$cntprodgroupcnt+1;
	$i=0;
	while($rowprodgroupcnt=mysqli_fetch_assoc($rsprodgroupcnt))
	{
		$commmon_header_sub.="<td>$rowprodgroupcnt[product_group_name]</td>";
		$i++;
		if($cntprodgroupcnt==$i)
		{
			$commmon_header_sub.="<td>Total</td>";
		}
	}
	$common_header.="<td colspan=\"$colspangroupcount\">Brand wise Sec. Sale ($distinct_vertical)</td>";
	$common_header_vertical.="<td>$distinct_vertical</td>";
	$k++;
	if($cntvertical==$k)
	{
		$common_header.="</tr>";
	}
	if(!in_array($distinct_vertical,$vertical_array))
	{
		array_push($vertical_array,$distinct_vertical);
	}
}
	$common_header.="<tr  align=\"center\" >
			<td>SI No</td>
			<td>SR Name</td>
			<td>TC</td>
			<td>PC</td>
			<td>TGT</td>
			<td>ACH</td>".$common_header_vertical.$commmon_header_sub."
		  </tr>";
////Header preperation End	
//For SR
$sqlselbaselevel="SELECT emp_code,emp_name,email,designation,reporting_to FROM employee_master WHERE designation='SO'";
$rsselbaselevel=mysqli_query($link,$sqlselbaselevel);
while($rowbaselevel=mysqli_fetch_assoc($rsselbaselevel))
{
	$base_emp_code=$rowbaselevel['emp_code'];
	${employee_hierarchy.$base_emp_code}=return_employee_hierarchy($base_emp_code);
	${emp_hierarchy_condition.$base_emp_code}=' emp_code IN('.${employee_hierarchy.$base_emp_code}.')';
	$base_emp_name=$rowbaselevel['emp_name'];
	$base_emp_email=$rowbaselevel['email'];
	$base_emp_reporting_to=$rowbaselevel['reporting_to'];
	$base_emp_designation=$rowbaselevel['designation'];
	${avl_productive_count.$base_emp_code}=0;
	
	//For self
    $sql_productive_nonproductive_call = "SELECT SUM(CASE WHEN LO.trans_id LIKE 'O%' AND CM.cust_type='R' THEN 1 ELSE 0 END ) AS productive_call,
										SUM(CASE WHEN LO.trans_id LIKE 'NO%' THEN 1 ELSE 0 END ) AS non_productive_call  FROM location LO,
											order_header OH,customer_master CM 
										WHERE LO.trans_id=OH.order_no AND OH.customer_code=CM.customer_code AND LO.emp_code='".$base_emp_code."'".$datecondition;
	$res_productive_nonproductive_call = mysqli_query($link,$sql_productive_nonproductive_call);
	$row_productive_nonproductive_call = mysqli_fetch_assoc($res_productive_nonproductive_call);
	$productive_call_base_emp = $row_productive_nonproductive_call['productive_call'];
	if($productive_call_level1=='')
	{
		$productive_call_level1=0;
	}
	$non_productive_call_base_emp = $row_productive_nonproductive_call['non_productive_call'];
	$total_call_base_emp=$productive_call_base_emp+$non_productive_call_base_emp;
	$sqlquerytargetach="SELECT  SUM(SCW.$column_target) AS target_emp_wise,SUM(SCW.$column_achievement) AS achievement_emp_wise  
						FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM WHERE 
						SCW.customer_code = CM.customer_code  AND CM.emp_code='".$base_emp_code."'";
	$rsquerytargetach=mysqli_query($link,$sqlquerytargetach);
	$rowquerytargetach=mysqli_fetch_assoc($rsquerytargetach);
	$target_base_emp=$rowquerytargetach['target_emp_wise'];
	$achievement_base_emp=$rowquerytargetach['achievement_emp_wise'];
	foreach($vertical_array as $vertical_val)
	{
		$sqlquerypurchase="SELECT  SUM(PD.amount) AS purchase_amount FROM purchase_details PD,customer_route_emp_relation CM WHERE 
							PD.distributor_code = CM.customer_code  AND CM.emp_code='".$base_emp_code."' AND PD.prod_code IN
							(SELECT DISTINCT prod_code FROM product_master WHERE acedns='Y' 
							AND vertical_value='".$vertical_val."') AND PD.invoice_date='".$dateprevious."'";
		$rsquerypurchase=mysqli_query($link,$sqlquerypurchase);
		$rowquerypurchase=mysqli_fetch_assoc($rsquerypurchase);
		$purchase_base_emp=$rowquerypurchase['purchase_amount'];
		${purchase_base_emp_self.$vertical_val} =$purchase_base_emp;
		${tablepurchaseself.$base_emp_code}.="<td align='right'>".$purchase_base_emp."</td>";
		$sqlprodgroupcnt="SELECT product_group_code FROM product_group_master WHERE vertical_value='".$vertical_val."'";
		$rsprodgroupcnt=mysqli_query($link,$sqlprodgroupcnt);
		$cntprodgroupcnt=mysqli_num_rows($rsprodgroupcnt);
		$n=0;
		while($rowprodgroupcnt=mysqli_fetch_assoc($rsprodgroupcnt))
		{
			$product_group_code_level1=$rowprodgroupcnt['product_group_code'];
			$sqlsecsalelevel1="SELECT SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.amount ELSE 0 END) AS secondary_sale  
						 FROM  prev_order_counting_master POCM WHERE 
						  SUBSTRING(POCM.order_no,2,5)='".$base_emp_code."' AND POCM.product_code IN
						 (SELECT DISTINCT prod_code FROM product_master WHERE acedns='Y' 
						 AND vertical_value='".$vertical_val."' AND product_group_code='".$product_group_code_level1."') 
						 AND SUBSTRING(POCM.visit_date,1,10)='".$dateprevious."'";
			$rssecsalelevel1=mysqli_query($link,$sqlsecsalelevel1);
			$rowsecsalelevel1=mysqli_fetch_assoc($rssecsalelevel1);
			$secondary_sale_base_emp=$rowsecsalelevel1['secondary_sale'];
			${total_secondary_sale_self.$base_emp_code.$product_group_code_level1} +=$secondary_sale_base_emp;
			${total_secondary_sale_self.$base_emp_code.$vertical_val} +=${total_secondary_sale_self.$base_emp_code.$product_group_code_level1};
			$n++;
			${tablesecondary_sale_self.$base_emp_code}.="<td align='right'>".number_format($secondary_sale_base_emp,2)."</td>";

			if($cntprodgroupcnt==$n)
			{
				${tablesecondary_sale_self.$base_emp_code}.="<td align='right'>".${total_secondary_sale_self.$base_emp_code.$vertical_val}."</td>";
			}
			//${tablesecondary_sale.$base_emp_code}='';
		}
	}
	//Enf For self
	//Start for lower leaves
	
	/*$sql_get_DSM_mail="SELECT email FROM employee_master WHERE emp_code='".$base_emp_reporting_to."'";
	$rs_get_DSM_mail=mysqli_query($link,$sql_get_DSM_mail);
	$row_get_DSM_mail=mysqli_fetch_assoc($rs_get_DSM_mail);
	$DSM_mail=$row_get_DSM_mail['email'];*/
	$sql_get_empdetails ="SELECT emp_code,emp_name FROM employee_master WHERE ".${emp_hierarchy_condition.$base_emp_code}." 
						AND acedns='Y' AND designation='SR'
						AND emp_code <>'".$base_emp_code."' ORDER BY emp_name ASC";					
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	$total_rows = mysqli_num_rows($res_get_empdetails);
	$count=1;
	if($total_rows>0){
	${table.$base_emp_code}=$common_header;
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		$emp_code_level1 = $row_get_empdetails['emp_code'];
		$emp_name_level1 = $row_get_empdetails['emp_name'];
		
		/*$sql_get_locdetails = "SELECT LO.emp_code,SUBSTRING(LO.date,12) as attendance,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE  (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' )".$datecondition ."
						AND LO.emp_code='".$emp_code_level1."' GROUP BY LO.emp_code ORDER BY LO.date ASC";
		$res_get_locdetails = mysqli_query($link,$sql_get_locdetails);
		$row_get_locdetails=mysqli_fetch_assoc($res_get_locdetails);
		$attendance_level1 = $row_get_locdetails['attendance'];
		if($attendance_level1 !='')
		{
			${avl_log_count.$base_emp_code} +=1;
		}
		$total_order_count_level1 = $row_get_locdetails['total_order_count'];*/
		
		/*----> Get employee name <----*/
		/*$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code_level1."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name_level1 = $row_emp_name['emp_name'];*
			
		/*---------------------------------> Count of productive  and non-productive call <--------------------------------*/
		$sql_productive_nonproductive_call = "SELECT SUM(CASE WHEN LO.trans_id LIKE 'O%' AND CM.cust_type='R' THEN 1 ELSE 0 END ) AS productive_call,
										SUM(CASE WHEN LO.trans_id LIKE 'NO%' THEN 1 ELSE 0 END ) AS non_productive_call  FROM location LO,
											order_header OH,customer_master CM 
										WHERE LO.trans_id=OH.order_no AND OH.customer_code=CM.customer_code AND LO.emp_code='".$emp_code_level1."'".$datecondition;
		$res_productive_nonproductive_call = mysqli_query($link,$sql_productive_nonproductive_call);
		$row_productive_nonproductive_call = mysqli_fetch_assoc($res_productive_nonproductive_call);
		$productive_call_level1 = $row_productive_nonproductive_call['productive_call'];
		if($productive_call_level1=='')
		{
			$productive_call_level1=0;
		}
		$non_productive_call_level1 = $row_productive_nonproductive_call['non_productive_call'];
		$total_call_level1=$productive_call_level1+$non_productive_call_level1;
		${total_productive_call.$base_emp_code} += $productive_call_level1;
		${total_non_productive_call.$base_emp_code} += $non_productive_call_level1;
		${total_call.$base_emp_code}  +=$total_call_level1;
		/*--------------------------------->End of Count of productive  and non-productive call <--------------------------------*/
		/*---------------------------------> For  Target and Achievement<--------------------------------*/
		$sqlquerytargetach="SELECT  SUM(SCW.$column_target) AS target_emp_wise,SUM(SCW.$column_achievement) AS achievement_emp_wise  
							FROM self_appraisal_customer_wise SCW,customer_route_emp_relation CM WHERE 
							SCW.customer_code = CM.customer_code  AND CM.emp_code='".$emp_code_level1."'";
		$rsquerytargetach=mysqli_query($link,$sqlquerytargetach);
		$rowquerytargetach=mysqli_fetch_assoc($rsquerytargetach);
		$target_emp_level1=$rowquerytargetach['target_emp_wise'];
		$achievement_emp_level1=$rowquerytargetach['achievement_emp_wise'];
	    ${total_target.$base_emp_code} += $target_emp_level1;
		${total_achievement.$base_emp_code} += $achievement_emp_level1;
	   /*---------------------------------> End For  Target and Achievement<--------------------------------*/
	  // if($total_call_level1 >0 || $target_emp_level1 >0 || $achievement_emp_level1 > 0){
	   ${table.$base_emp_code}.="<tr>
					<td>".$count."</td>
					<td align='left'>".$emp_name_level1."</td>
					<td align='right'>".$total_call_level1."</td>
					<td align='right'>".$productive_call_level1."</td>
					<td align='right'>".number_format($target_emp_level1,2)."</td>
					<td align='right'>".number_format($achievement_emp_level1,2)."</td>";
	   /*---------------------------------> For Purchase details and secondary sale<--------------------------------*/
		foreach($vertical_array as $vertical_val)
		{
			$sqlquerypurchase="SELECT  SUM(PD.amount) AS purchase_amount FROM purchase_details PD,customer_route_emp_relation CM WHERE 
								PD.distributor_code = CM.customer_code  AND CM.emp_code='".$emp_code_level1."' AND PD.prod_code IN
								(SELECT DISTINCT prod_code FROM product_master WHERE acedns='Y' 
								AND vertical_value='".$vertical_val."') AND PD.invoice_date='".$dateprevious."'";
			$rsquerypurchase=mysqli_query($link,$sqlquerypurchase);
			$rowquerypurchase=mysqli_fetch_assoc($rsquerypurchase);
			$purchase_emp_level1=$rowquerypurchase['purchase_amount'];
			${total_purchase.$base_emp_code} += $purchase_emp_level1;
			${tablepurchase.$emp_code_level1}.="<td align='right'>".$purchase_emp_level1."</td>";
			if($total_rows==$count)
			{
				${tablepurchase.$base_emp_code}.="<td align='right'>".(${total_purchase.$base_emp_code}+$purchase_base_emp)."</td>";
			}
			
			$sqlprodgroupcnt="SELECT product_group_code FROM product_group_master WHERE vertical_value='".$vertical_val."'";
			$rsprodgroupcnt=mysqli_query($link,$sqlprodgroupcnt);
			$cntprodgroupcnt=mysqli_num_rows($rsprodgroupcnt);
			$m=0;
			while($rowprodgroupcnt=mysqli_fetch_assoc($rsprodgroupcnt))
			{
				$product_group_code_level1=$rowprodgroupcnt['product_group_code'];
				$sqlsecsalelevel1="SELECT SUM(CASE WHEN POCM.order_no LIKE 'O%' AND POCM.cust_type='R' THEN POCM.amount ELSE 0 END) AS secondary_sale  
							 FROM  prev_order_counting_master POCM WHERE 
							SUBSTRING(POCM.order_no,2,5)='".$emp_code_level1."' AND POCM.product_code IN
							 (SELECT DISTINCT prod_code FROM product_master WHERE acedns='Y' 
							 AND vertical_value='".$vertical_val."' AND product_group_code='".$product_group_code_level1."') 
							 AND SUBSTRING(POCM.visit_date,1,10)='".$dateprevious."'";
				$rssecsalelevel1=mysqli_query($link,$sqlsecsalelevel1);
				$rowsecsalelevel1=mysqli_fetch_assoc($rssecsalelevel1);
				$secondary_sale_level1=$rowsecsalelevel1['secondary_sale'];
				${total_secondary_sale.$emp_code_level1.$vertical_val} +=$secondary_sale_level1;
				${total_secondary_sale.$base_emp_code.$product_group_code_level1} += $secondary_sale_level1;
				$m++;
				${tablesecondary_sale.$emp_code_level1}.="<td align='right'>".number_format($secondary_sale_level1,2)."</td>";

				if($total_rows==$count)
				{
				${tablesecondary_sale.$base_emp_code}.="<td align='right'>".(${total_secondary_sale.$base_emp_code.$product_group_code_level1}+${total_secondary_sale_self.$base_emp_code.$product_group_code_level1})."</td>";
				}
				if($cntprodgroupcnt==$m)
				{
					${tablesecondary_sale.$emp_code_level1}.="<td align='right'>".${total_secondary_sale.$emp_code_level1.$vertical_val}."</td>";
				}
				//${tablesecondary_sale.$base_emp_code}='';
			}
			${total_secondary_sale.$base_emp_code.$vertical_val} +=${total_secondary_sale.$emp_code_level1.$vertical_val} ;
			if($total_rows==$count)
			{
			${tablesecondary_sale.$base_emp_code}.="<td align='right'>".(${total_secondary_sale.$base_emp_code.$vertical_val}+${total_secondary_sale_self.$base_emp_code.$vertical_val})."</td>";
			}
		}
	 /*---------------------------------> End For Purchase details and secondary sale<--------------------------------*/
		${table.$base_emp_code}.=${tablepurchase.$emp_code_level1}.${tablesecondary_sale.$emp_code_level1}."</tr>";
		$count++;
		//}
	}
		${table.$base_emp_code}.="<tr>
					<td align='center' colspan='2'>SO - SELF</td>
					<td align='right'>".$total_call_base_emp."</td>
					<td align='right'>".$productive_call_base_emp."</td>
					<td align='right'>".number_format($target_base_emp,2)."</td>
					<td align='right'>".number_format($achievement_base_emp,2)."</td>".${tablepurchaseself.$base_emp_code}.${tablesecondary_sale_self.$base_emp_code}."</tr>";
	//End For self	
	${table.$base_emp_code}.="<tr style='font-weight:bold;background:yellow;'>
			<td align='center' colspan='2'>SO - Total</td>
			<td align='right'>".(${total_call.$base_emp_code}+$total_call_base_emp)."</td>
			<td align='right'>".(${total_productive_call.$base_emp_code}+$productive_call_base_emp)."</td>
			<td align='right'>".number_format((${total_target.$base_emp_code}+$target_base_emp),2)."</td>
			<td align='right'>".number_format((${total_achievement.$base_emp_code}+$achievement_base_emp),2)."</td>
			".${tablepurchase.$base_emp_code}.${tablesecondary_sale.$base_emp_code}."
		  </tr></table>";
	//echo "</table>";
	//echo ${table.$base_emp_code};
	//$email_to=$base_emp_email;
	$base_emp_mailsubj="SR Wise  Daily Report - ".$base_emp_name."(".$base_emp_designation.")";
	$base_emp_mail_headers  = "MIME-Version: 1.0\r\n";
	$base_emp_mail_headers .= "Content-type: text/html; charset=UTF-8\n";
	$base_emp_mail_headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	 $email_to=$base_emp_email.',sales@archita.in,arpansfa@gmail.com,acedns@coral.in';			
	 @mail($email_to, $base_emp_mailsubj, ${table.$base_emp_code}, $base_emp_mail_headers);
	}
	$baselevel++;
}
//End for SR
ob_end_flush();
?>
