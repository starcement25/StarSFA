<?php
//echo "Work In Progress";die;
ob_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");
define("DB","acedns_PARLE");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require("include/config-email-setup.php");

$current_date = date('Y-m-d');
//$current_date = '2016-11-07';
$datecondition = " AND SUBSTRING(LO.date,1,10)='".$current_date."' ";
$primary_secondary_quantity_condition = " AND SUBSTRING(order_no,-14,8) = '".str_replace("-","",$current_date)."' ";
//For ASM
$sqlselbaselevel="SELECT emp_code,emp_name,email,designation,reporting_to FROM employee_master WHERE level='2'";
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
	${avl_log_count.$base_emp_code}=0;
	
	$sql_get_DSM_mail="SELECT email FROM employee_master WHERE emp_code='".$base_emp_reporting_to."'";
	$rs_get_DSM_mail=mysqli_query($link,$sql_get_DSM_mail);
	$row_get_DSM_mail=mysqli_fetch_assoc($rs_get_DSM_mail);
	$DSM_mail=$row_get_DSM_mail['email'];
	/*$sql_get_empdetails = "SELECT LO.emp_code,SUBSTRING(LO.date,12) as attendance,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE  (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' )".$datecondition ."
						AND ".${emp_hierarchy_condition.$base_emp_code}." GROUP BY LO.emp_code ORDER BY LO.date ASC";*/
	$sql_get_empdetails ="SELECT emp_code,emp_name FROM employee_master WHERE ".${emp_hierarchy_condition.$base_emp_code}." AND acedns='Y' 
						AND emp_code <>'".$base_emp_code."' ORDER BY emp_name ASC";					
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	$total_rows = mysqli_fetch_assoc($res_get_empdetails);
	$count=1;
	if($total_rows>0){
	${table.$base_emp_code}="<table width='100%' border='1' style='border-collapse:collapse;' cellpadding='6px'>";
	${table.$base_emp_code}.="<tr  align=\"center\" style=\"background:yellow;\">
			<td>SL No</td>
			<td>FF Name</td>
			<td>FF AVL</td>
			<td>FF LOG</td>
			<td>TC</td>
			<td>PC</td>
			<td>PC%</td>
			<td>LPC</td>
			<td>Value</td>
		  </tr>";
	$res_get_empdetails = mysqli_query($link,$sql_get_empdetails);
	while($row_get_empdetails = mysqli_fetch_assoc($res_get_empdetails)){
		$emp_code_level1 = $row_get_empdetails['emp_code'];
		$emp_name_level1 = $row_get_empdetails['emp_name'];
		
		$sql_get_locdetails = "SELECT LO.emp_code,SUBSTRING(LO.date,12) as attendance,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE  (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' )".$datecondition ."
						AND LO.emp_code='".$emp_code_level1."' GROUP BY LO.emp_code ORDER BY LO.date ASC";
		$res_get_locdetails = mysqli_query($link,$sql_get_locdetails);
		$row_get_locdetails=mysqli_fetch_assoc($res_get_locdetails);
		$attendance_level1 = $row_get_locdetails['attendance'];
		if($attendance_level1 !='')
		{
			${avl_log_count.$base_emp_code} +=1;
		}
		$total_order_count_level1 = $row_get_locdetails['total_order_count'];
		
		/*----> Get employee name <----*/
		/*$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code_level1."'";
		$res_emp_name = mysqli_query($link,$sql_emp_name);
		$row_emp_name = mysqli_fetch_assoc($res_emp_name);
		$emp_name_level1 = $row_emp_name['emp_name'];*
			
		/*---------------------------------> Count of productive  and non-productive call <--------------------------------*/
		$sql_productive_nonproductive_call = "SELECT SUM(CASE WHEN (LO.trans_id LIKE 'O%' OR LO.trans_id LIKE 'S%' OR LO.trans_id LIKE 'P%') AND LO.trans_id 
									NOT LIKE 'PA%' THEN 1 ELSE 0 END ) AS productive_call,SUM(CASE WHEN (LO.trans_id LIKE 'NO%' OR LO.trans_id LIKE 'NC%') 
									THEN 1 ELSE 0 END ) AS non_productive_call  FROM location LO WHERE LO.emp_code='".$emp_code_level1."'".$datecondition;
		$res_productive_nonproductive_call = mysqli_query($link,$sql_productive_nonproductive_call);
		$row_productive_nonproductive_call = mysqli_fetch_assoc($res_productive_nonproductive_call);
		$productive_call_level1 = $row_productive_nonproductive_call['productive_call'];
		if($productive_call_level1=='')
		{
			$productive_call_level1=0;
		}
		$non_productive_call_level1 = $row_productive_nonproductive_call['non_productive_call'];
		$total_call_level1=$productive_call_level1+$non_productive_call_level1;
		if($productive_call_level1 >0 && $total_call_level1 >0)
		{
			$pc_percent_level1=($productive_call_level1/$total_call_level1)*100;
		}
		else
		{
			$pc_percent_level1=0;
		}
		${total_productive_call.$base_emp_code} += $productive_call_level1;
		${total_non_productive_call.$base_emp_code} += $non_productive_call_level1;
		${total_call.$base_emp_code}  +=$total_call_level1;
		${total_pc_percent.$base_emp_code}  +=$pc_percent_level1;
		
		$LPPC_level1=0;
		if($total_order_count_level1>0){
			$productive_call_count_level1 = '';
			$order_details_count_level1 = '';
			${avl_productive_count.$base_emp_code} +=1;
			/*---------------------------------> Total primary and secondary sales & amount<--------------------------------*/
			$sql_total_primary_secondary = "SELECT COUNT(DISTINCT order_no) AS order_header_count, COUNT(order_no) AS order_details_count,
											SUM(amount) AS amount FROM prev_order_counting_master WHERE SUBSTRING(order_no,2,5) = '".$emp_code_level1."' 
											AND order_no LIKE 'O%'".$primary_secondary_quantity_condition;
			$res_total_primary_secondary = mysqli_query($link,$sql_total_primary_secondary);
			$row_total_primary_secondary = mysqli_fetch_assoc($res_total_primary_secondary);
			
			$amount_level1 = round($row_total_primary_secondary['amount'],2);
			${total_amount.$base_emp_code} += $amount_level1;

			$productive_call_count_level1 = $row_total_primary_secondary['order_header_count'];
			$order_details_count_level1 = $row_total_primary_secondary['order_details_count'];
			$LPPC_level1 = round(($order_details_count_level1/$productive_call_count_level1),2);
			${total_LPPC.$base_emp_code} += $LPPC_level1;
		}
		else
		{
			$amount_level1 = 0;
			${total_amount.$base_emp_code} += $amount_level1;

			$LPPC_level1 = 0;
			${total_LPPC.$base_emp_code} += $LPPC_level1;
		}
			/*---------------------------------> Total secondary sales & amount <--------------------------------*/
		${table.$base_emp_code}.="<tr>
					<td>".$count."</td>
					<td align='left'>".$emp_name_level1."</td>
					<td></td>
					<td align='right'>".$attendance_level1."</td>
					<td align='right'>".$total_call_level1."</td>
					<td align='right'>".$productive_call_level1."</td>
					<td align='right'>".round($pc_percent_level1,2)."</td>
					<td align='right'>".$LPPC_level1."</td>
					<td align='right'>".number_format($amount_level1,2)."</td>
				  </tr>";
		$count++;
	}
	${table.$base_emp_code}.="<tr style='font-weight:bold;background:yellow;'>
			<td align='center' colspan='2'>Total</td>
			<td align='right'>".(count(explode(',',${employee_hierarchy.$base_emp_code}))-1)."</td>
			<td align='right'>".${avl_log_count.$base_emp_code}."</td>
			<td align='right'>".${total_call.$base_emp_code}."</td>
			<td align='right'>".${total_productive_call.$base_emp_code}."</td>
			<td align='right'>".round((${total_productive_call.$base_emp_code}/${total_call.$base_emp_code})*100,2)."</td>
			<td align='right'>".round((${total_LPPC.$base_emp_code}/${avl_log_count.$base_emp_code}),2)."</td>
			<td align='right'>".number_format(${total_amount.$base_emp_code},2)."</td>
		  </tr></table>";
	//echo "</table>";
	//echo ${table.$base_emp_code};
	$email_to=$base_emp_email.','.$DSM_mail.','.'parleconsent@gmail.com';
	$base_emp_mailsubj="FF Daily Activity Report - ".$base_emp_name."(".$base_emp_designation.")";
	$base_emp_mail_headers  = "MIME-Version: 1.0\r\n";
	$base_emp_mail_headers .= "Content-type: text/html; charset=UTF-8\n";
	$base_emp_mail_headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $base_emp_mailsubj, ${table.$base_emp_code}, $base_emp_mail_headers);
	}
}
//End for ASM
//For DSM
$sqlseldsmlevel="SELECT emp_code,emp_name,email,designation FROM employee_master WHERE level='3'";
$rsseldsmlevel=mysqli_query($link,$sqlseldsmlevel);
while($rowseldsmlevel=mysqli_fetch_assoc($rsseldsmlevel))
{
	$dsm_emp_code=$rowseldsmlevel['emp_code'];
	$dsm_emp_name=$rowseldsmlevel['emp_name'];
	$dsm_emp_email=$rowseldsmlevel['email'];
	$dsm_emp_designation=$rowseldsmlevel['designation'];
	//${dsm_avl_ff.$dsm_emp_code}=count(explode(',',${employee_hierarchy.$dsm_emp_code}))-1;
	
	$sql_get_empdetails_asm ="SELECT emp_code,emp_name FROM employee_master WHERE reporting_to='".$dsm_emp_code."' ORDER BY emp_name ASC";					
	$res_get_empdetails_asm = mysqli_query($link,$sql_get_empdetails_asm);
	$total_rows_asm = mysqli_num_rows($res_get_empdetails_asm);
	$countasm=1;
	if($total_rows_asm>0){
		${employee_hierarchy.$dsm_emp_code}=0;
	${table.$dsm_emp_code}="<table width='100%' border='1' style='border-collapse:collapse;' cellpadding='6px'>";
	${table.$dsm_emp_code}.="<tr  align=\"center\" style=\"background:yellow;\">
			<td>SL No</td>
			<td>ASM Name</td>
			<td>FF AVL</td>
			<td>FF LOG</td>
			<td>TC</td>
			<td>PC</td>
			<td>PC%</td>
			<td>Avg Pc/<br />FF Log</td>
			<td>LPC</td>
			<td>Value</td>
			<td>Value/<br />FF Log</td>
		  </tr>";
	while($row_get_empdetails_asm = mysqli_fetch_assoc($res_get_empdetails_asm)){
		$emp_code_level2 = $row_get_empdetails_asm['emp_code'];
		$emp_name_level2 = $row_get_empdetails_asm['emp_name'];
		
		$sql_get_locdetails_asm = "SELECT LO.emp_code,SUBSTRING(LO.date,12) as attendance,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE  (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' )".$datecondition ."
						AND LO.emp_code='".$emp_code_level2."' GROUP BY LO.emp_code ORDER BY LO.date ASC";
		$res_get_locdetails_asm = mysqli_query($link,$sql_get_locdetails_asm);
		$row_get_locdetails_asm=mysqli_fetch_assoc($res_get_locdetails_asm);
		$attendance_level2 = $row_get_locdetails_asm['attendance'];
		$total_order_count_level2 = $row_get_locdetails_asm['total_order_count'];
		

		${total_productive_call.$dsm_emp_code} += ${total_productive_call.$emp_code_level2};
		${total_non_productive_call.$dsm_emp_code} += ${total_non_productive_call.$emp_code_level2};
		${total_call.$dsm_emp_code}  +=${total_call.$emp_code_level2};
		
		${avl_log_count.$dsm_emp_code} +=${avl_log_count.$emp_code_level2};
		${total_amount.$dsm_emp_code} += ${total_amount.$emp_code_level2};
		
		if(${avl_log_count.$emp_code_level2} ==0)
		{
			${avl_log_count_ASM.$dsm_emp_code}=${avl_log_count_ASM.$dsm_emp_code}+0;
		}
		else
		{
			${avl_log_count_ASM.$dsm_emp_code}=${avl_log_count_ASM.$dsm_emp_code}+1;
		}
		
		${final_total_LPPC.$emp_code_level2}=round((${total_LPPC.$emp_code_level2}/${avl_log_count.$emp_code_level2}),2);	
		${total_LPPC.$dsm_emp_code} += ${total_LPPC.$emp_code_level2};
		${final_total_LPPC.$dsm_emp_code} +=${final_total_LPPC.$emp_code_level2};
		${final_total_productive_call.$dsm_emp_code} +=round((${total_productive_call.$emp_code_level2}/${avl_log_count.$emp_code_level2}),2);
			/*---------------------------------> Total secondary sales & amount <--------------------------------*/
		${table.$dsm_emp_code}.="<tr>
					<td>".$countasm."</td>
					<td align='left'>".$emp_name_level2."</td>
					<td align='right'>".(count(explode(',',${employee_hierarchy.$emp_code_level2}))-1)."</td>
					<td align='right'>".${avl_log_count.$emp_code_level2}."</td>
					<td align='right'>".${total_call.$emp_code_level2}."</td>
					<td align='right'>".${total_productive_call.$emp_code_level2}."</td>
					<td align='right'>".round((${total_productive_call.$emp_code_level2}/${total_call.$emp_code_level2})*100,2)."</td>
					<td align='right'>".round((${total_productive_call.$emp_code_level2}/${avl_log_count.$emp_code_level2}),2)."</td>
					<td align='right'>".${final_total_LPPC.$emp_code_level2}."</td>
					<td align='right'>".number_format(${total_amount.$emp_code_level2},2)."</td>
					<td align='right'>".round((${total_amount.$emp_code_level2}/${avl_log_count.$emp_code_level2}),2)."</td>
				  </tr>";
		${employee_hierarchy.$dsm_emp_code} +=(count(explode(',',${employee_hierarchy.$emp_code_level2}))-1);		  
		$countasm++;
	}
	/*<td align='right'>".round((${total_LPPC.$dsm_emp_code}/${avl_log_count.$dsm_emp_code}),2)."</td>
	<td align='right'>".round((${total_productive_call.$dsm_emp_code}/${avl_log_count.$dsm_emp_code}),2)."</td>
	*/
	${table.$dsm_emp_code}.="<tr style='font-weight:bold;background:yellow;'>
			<td align='center' colspan='2'>Total</td>
			<td align='right'>".${employee_hierarchy.$dsm_emp_code}."</td>
			<td align='right'>".${avl_log_count.$dsm_emp_code}."</td>
			<td align='right'>".${total_call.$dsm_emp_code}."</td>
			<td align='right'>".${total_productive_call.$dsm_emp_code}."</td>
			<td align='right'>".round((${total_productive_call.$dsm_emp_code}/${total_call.$dsm_emp_code})*100,2)."</td>
			<td align='right'>".round((${final_total_productive_call.$dsm_emp_code}/${avl_log_count_ASM.$dsm_emp_code}),2)."</td>
			<td align='right'>".round((${final_total_LPPC.$dsm_emp_code}/${avl_log_count_ASM.$dsm_emp_code}),2)."</td>
			<td align='right'>".number_format(${total_amount.$dsm_emp_code},2)."</td>
			<td align='right'>".round((${total_amount.$dsm_emp_code}/${avl_log_count.$dsm_emp_code}),2)."</td>
		  </tr></table>";
	//echo "</table>";
	//echo ${table.$dsm_emp_code};
	$email_to_dsm=$dsm_emp_email.','.'parleconsent@gmail.com';
	$dsm_emp_mailsubj="FF Daily Activity Report - ".$dsm_emp_name."(".$dsm_emp_designation.")";
	
	$dsm_emp_mail_headers  = "MIME-Version: 1.0\r\n";
	$dsm_emp_mail_headers .= "Content-type: text/html; charset=UTF-8\n";
	$dsm_emp_mail_headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to_dsm, $dsm_emp_mailsubj, ${table.$dsm_emp_code}, $dsm_emp_mail_headers);
	//@mail('dipankarc@coral.in', $dsm_emp_mailsubj, ${table.$dsm_emp_code}, $dsm_emp_mail_headers);
	}
}

//End for DSM
//For ZSM
$sqlselzsmlevel="SELECT emp_code,emp_name,email,designation FROM employee_master WHERE level='4'";
$rsselzsmmlevel=mysqli_query($link,$sqlselzsmlevel);
while($rowselzsmlevel=mysqli_fetch_assoc($rsselzsmmlevel))
{
	$zsm_emp_code=$rowselzsmlevel['emp_code'];
	$zsm_emp_name=$rowselzsmlevel['emp_name'];
	$zsm_emp_email=$rowselzsmlevel['email'];
	$zsm_emp_designation=$rowselzsmlevel['designation'];
	//${dsm_avl_ff.$dsm_emp_code}=count(explode(',',${employee_hierarchy.$dsm_emp_code}))-1;
	
	$sql_get_empdetails_dsm="SELECT emp_code,emp_name FROM employee_master WHERE reporting_to='".$zsm_emp_code."' ORDER BY emp_name ASC";					
	$res_get_empdetails_dsm = mysqli_query($link,$sql_get_empdetails_dsm);
	$total_rows_dsm = mysqli_num_rows($res_get_empdetails_dsm);
	$countzsm=1;
	if($total_rows_dsm>0){
	${table.$zsm_emp_code}="<table width='100%' border='1' style='border-collapse:collapse;' cellpadding='6px'>";
	${table.$zsm_emp_code}.="<tr  align=\"center\" style=\"background:yellow;\">
			<td>SL No</td>
			<td>DSM Name</td>
			<td>FF AVL</td>
			<td>FF LOG</td>
			<td>TC</td>
			<td>PC</td>
			<td>PC%</td>
			<td>Avg Pc/<br />FF Log</td>
			<td>LPC</td>
			<td>Value</td>
			<td>Value/<br />FF Log</td>
		  </tr>";
	while($row_get_empdetails_dsm = mysqli_fetch_assoc($res_get_empdetails_dsm)){
		$emp_code_level3 = $row_get_empdetails_dsm['emp_code'];
		$emp_name_level3 = $row_get_empdetails_dsm['emp_name'];
		
		$sql_get_locdetails_dsm = "SELECT LO.emp_code,SUBSTRING(LO.date,12) as attendance,SUM(CASE WHEN LO.trans_id LIKE 'O%' THEN 1 ELSE 0
						END ) AS total_order_count FROM location LO WHERE  (LO.trans_id LIKE 'A%' OR LO.trans_id LIKE 'O%' )".$datecondition ."
						AND LO.emp_code='".$emp_code_level3."' GROUP BY LO.emp_code ORDER BY LO.date ASC";
		$res_get_locdetails_dsm = mysqli_query($link,$sql_get_locdetails_dsm);
		$row_get_locdetails_dsm=mysqli_fetch_assoc($res_get_locdetails_dsm);
		$attendance_level3 = $row_get_locdetails_dsm['attendance'];
		$total_order_count_level3 = $row_get_locdetails_dsm['total_order_count'];
		//echo $emp_name_level3.${total_LPPC.$emp_code_level3};
		${total_productive_call.$zsm_emp_code} += ${total_productive_call.$emp_code_level3};
		${total_non_productive_call.$zsm_emp_code} += ${total_non_productive_call.$emp_code_level3};
		${total_call.$zsm_emp_code}  +=${total_call.$emp_code_level3};
		
		${avl_log_count.$zsm_emp_code} +=${avl_log_count.$emp_code_level3};
		${total_amount.$zsm_emp_code} += ${total_amount.$emp_code_level3};
		if(${avl_log_count.$emp_code_level3} ==0)
		{
			${avl_log_count_DSM.$zsm_emp_code}=${avl_log_count_DSM.$zsm_emp_code}+0;
		}
		else
		{
			${avl_log_count_DSM.$zsm_emp_code}=${avl_log_count_DSM.$zsm_emp_code}+1;
		}
		
		${total_LPPC.$zsm_emp_code} += ${total_LPPC.$emp_code_level3};

		${final_total_LPPC.$emp_code_level3}=round((${total_LPPC.$emp_code_level3}/${avl_log_count.$emp_code_level3}),2);
		${final_total_LPPC.$zsm_emp_code} +=${final_total_LPPC.$emp_code_level3};
		${final_total_productive_call.$zsm_emp_code} +=round((${total_productive_call.$emp_code_level3}/${avl_log_count.$emp_code_level3}),2);
	
		${table.$zsm_emp_code}.="<tr>
					<td>".$countzsm."</td>
					<td align='left'>".$emp_name_level3."</td>
					<td align='right'>".${employee_hierarchy.$emp_code_level3}."</td>
					<td align='right'>".${avl_log_count.$emp_code_level3}."</td>
					<td align='right'>".${total_call.$emp_code_level3}."</td>
					<td align='right'>".${total_productive_call.$emp_code_level3}."</td>
					<td align='right'>".round((${total_productive_call.$emp_code_level3}/${total_call.$emp_code_level3})*100,2)."</td>
					<td align='right'>".round((${total_productive_call.$emp_code_level3}/${avl_log_count.$emp_code_level3}),2)."</td>
					<td align='right'>".${final_total_LPPC.$emp_code_level3}."</td>
					<td align='right'>".number_format(${total_amount.$emp_code_level3},2)."</td>
					<td align='right'>".round((${total_amount.$emp_code_level3}/${avl_log_count.$emp_code_level3}),2)."</td>
				  </tr>";
		${employee_hierarchy.$zsm_emp_code} +=${employee_hierarchy.$emp_code_level3};		  
		$countzsm++;
	}
	/*			<td align='right'>".round((${total_productive_call.$zsm_emp_code}/${avl_log_count.$zsm_emp_code}),2)."</td>
			<td align='right'>".round((${total_LPPC.$zsm_emp_code}/${avl_log_count.$zsm_emp_code}),2)."</td>
*/
	${table.$zsm_emp_code}.="<tr style='font-weight:bold;background:yellow;'>
			<td align='center' colspan='2'>Total</td>
			<td align='right'>".${employee_hierarchy.$zsm_emp_code}."</td>
			<td align='right'>".${avl_log_count.$zsm_emp_code}."</td>
			<td align='right'>".${total_call.$zsm_emp_code}."</td>
			<td align='right'>".${total_productive_call.$zsm_emp_code}."</td>
			<td align='right'>".round((${total_productive_call.$zsm_emp_code}/${total_call.$zsm_emp_code})*100,2)."</td>
			<td align='right'>".round((${final_total_productive_call.$zsm_emp_code}/${avl_log_count_DSM.$zsm_emp_code}),2)."</td>
			<td align='right'>".round((${final_total_LPPC.$zsm_emp_code}/${avl_log_count_DSM.$zsm_emp_code}),2)."</td>
			<td align='right'>".number_format(${total_amount.$zsm_emp_code},2)."</td>
			<td align='right'>".round((${total_amount.$zsm_emp_code}/${avl_log_count.$zsm_emp_code}),2)."</td>
		  </tr></table>";
	//echo "</table>";
	//echo ${table.$zsm_emp_code};
	$email_to_zsm=$zsm_emp_email.','.'vikram.singh1@parle.biz';
	$zsm_emp_mailsubj="FF Daily Activity Report - ".$zsm_emp_name."(".$zsm_emp_designation.")";
	
	$zsm_emp_mail_headers  = "MIME-Version: 1.0\r\n";
	$zsm_emp_mail_headers .= "Content-type: text/html; charset=UTF-8\n";
	$zsm_emp_mail_headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
				"Reply-To:".FROMEMAIL." \r\n" .
				"Bcc: ".BCCEMAIL." \r\n".
				'X-Mailer: PHP/' . phpversion();
	@mail($email_to_zsm, $zsm_emp_mailsubj, ${table.$zsm_emp_code}, $zsm_emp_mail_headers);
	//@mail('dipankarc@coral.in', $zsm_emp_mailsubj, ${table.$zsm_emp_code}, $zsm_emp_mail_headers);
	}
}

//End for DSM

ob_end_flush();
?>
