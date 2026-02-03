<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
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
	$emp_condition = ' 1';
}
else{
	$order_condition = " SUBSTRING(order_no,2,5) IN(".$employee.") AND ";
	$payment_condition = " SUBSTRING(receipt_id,2,5) IN(".$employee.") AND ";
	$checkinout_condition = " SUBSTRING(trans_id,3,5) IN(".$employee.") AND ";
	$emp_condition = " emp_code IN(".$employee.") ";
}

$outer_customer_code_array=array();
$outer_customer_code='';

$sql_checkin_out_date = "SELECT SUBSTRING(trans_id,-14,8) AS checkinout_date,customer_code,SUBSTRING(trans_id,3,5) As emp_code FROM check_in_out_details WHERE 
	".$checkinout_condition." (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') 
	AND trans_id LIKE 'C%' ORDER BY SUBSTRING(trans_id,3,5) ASC,check_in_time ASC";
$res_checkin_out_date = mysql_query($sql_checkin_out_date);
while($row_checkin_out_date = mysql_fetch_array($res_checkin_out_date)){
	$checkinout_date = $row_checkin_out_date['checkinout_date'];
	$customer_code = $row_checkin_out_date['customer_code'];
	$emp_code_fetched=$row_checkin_out_date['emp_code'];
	if(!in_array($checkinout_date,$date_array)){
		array_push($date_array,$checkinout_date);
	}
	$outer_customer_code_val=$customer_code.$emp_code_fetched;
	if(!in_array($outer_customer_code_val,$outer_customer_code_array)){
		array_push($outer_customer_code_array,$outer_customer_code_val);
		${outer_customer_code.$emp_code_fetched}=${outer_customer_code.$emp_code_fetched}."'".$customer_code."'".",";
	}
}
$sql_order_date = "SELECT SUBSTRING(order_no,-14,8) AS order_date,customer_code,SUBSTRING(order_no,2,5) As emp_code FROM order_header WHERE ".$order_condition." (SUBSTRING(order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND order_no LIKE 'O%'";
$res_order_date = mysql_query($sql_order_date);
while($row_order_date = mysql_fetch_array($res_order_date)){
	$order_date = $row_order_date['order_date'];
	$customer_code = $row_order_date['customer_code'];
	$emp_code_fetched=$row_order_date['emp_code'];
	if(!in_array($order_date,$date_array)){
		array_push($date_array,$order_date);
	}
	$outer_customer_code_val=$customer_code.$emp_code_fetched;
	if(!in_array($outer_customer_code_val,$outer_customer_code_array)){
		array_push($outer_customer_code_array,$outer_customer_code_val);
		${outer_customer_code.$emp_code_fetched}=${outer_customer_code.$emp_code_fetched}."'".$customer_code."'".",";
	}
}

$sql_payment_date = "SELECT SUBSTRING(receipt_id,-14,8) AS payment_date,customer_code,SUBSTRING(receipt_id,2,5) As emp_code FROM payment_header WHERE ".$payment_condition." (SUBSTRING(receipt_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND receipt_id LIKE 'P%'";
$res_payment_date = mysql_query($sql_payment_date);
while($row_payment_date = mysql_fetch_array($res_payment_date)){
	$payment_date = $row_payment_date['payment_date'];
	$customer_code = $row_payment_date['customer_code'];
	$emp_code_fetched=$row_payment_date['emp_code'];
	if(!in_array($payment_date,$date_array)){
		array_push($date_array,$payment_date);
	}
	$outer_customer_code_val=$customer_code.$emp_code_fetched;
	if(!in_array($outer_customer_code_val,$outer_customer_code_array)){
		array_push($outer_customer_code_array,$outer_customer_code_val);
		${outer_customer_code.$emp_code_fetched}=${outer_customer_code.$emp_code_fetched}."'".$customer_code."'".",";
	}
}
sort($date_array);
if(providing_code=='yes'){
	$dns_customer_code_string="\t".'DNS Customer Code';
}
else{
	$dns_customer_code_string='';
}
//if(!empty($date_array)){
	$header = "Date".$dns_customer_code_string."\t"."Customer Name"."\t"."Route Name"."\t"."Cust Type"."\t"."Employee Code"."\t"."Employee Name"."\t"."Zone"."\t"."State"."\t"."Designation"."\t"."Order Qty"."\t"."Order Value"."\t"."Collection amount"."\t"."Check in/Att time"."\t"."Check out time"."\t"."Duration"."\t"
	."Remarks"."\t"."Lattitude"."\t"."Longitude";
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name,zone,state,designation FROM employee_master WHERE ".$emp_condition;
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$zone = $row_emp['zone'];
		$state = $row_emp['state'];
		$designation = $row_emp['designation'];
		
		/*$sql_location_check = "SELECT trans_id FROM location WHERE (trans_id LIKE 'O%' OR trans_id LIKE 'P%' OR trans_id LIKE 'CI%') AND 
							emp_code = '".$emp_code."' AND (SUBSTRING(date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
		$res_location_check = mysql_query($sql_location_check);
		$location_row_check = mysql_num_rows($res_location_check);*/
		${customer_code_array.$emp_code}=array();
		${outer_customer_code.$emp_code}=substr(${outer_customer_code.$emp_code},0,-1);

		/*$sqlcustomercheck="SELECT DISTINCT customer_code,dns_customer_code,cust_type,customer_name,route_code FROM customer_master WHERE customer_code IN(SELECT customer_code FROM check_in_out_details WHERE (SUBSTRING(trans_id,-14,8)  BETWEEN '".$start_date_search."' AND '".$end_date_search."') 
						 AND SUBSTRING(trans_id,3,5) = '".$emp_code."' ORDER BY check_in_time ASC) OR customer_code IN(SELECT customer_code FROM order_header WHERE 
						order_no LIKE 'O%' AND (SUBSTRING(order_no,-14,8)  BETWEEN '".$start_date_search."' AND '".$end_date_search."' ) AND SUBSTRING(order_no,2,5) = '".$emp_code."') OR  customer_code IN(SELECT customer_code FROM payment_header WHERE receipt_id LIKE 'P%'
						 AND (SUBSTRING(receipt_id,-14,8)  BETWEEN '".$start_date_search."' AND '".$end_date_search."') AND SUBSTRING(receipt_id,2,5) = '".$emp_code."') 
						 ";*/
		$sqlcustomercheck="SELECT DISTINCT customer_code,dns_customer_code,cust_type,customer_name,route_code FROM customer_master WHERE customer_code IN(".${outer_customer_code.$emp_code}.") ORDER BY FIELD(customer_code, ${outer_customer_code.$emp_code})";
		$rscustomercheck=mysql_query($sqlcustomercheck);
		$customer_row_check=mysql_num_rows($rscustomercheck);
		
		if($customer_row_check>0){
			
			//echo "<tr><td colspan = '7' class = 'TDHEAD_SUB' align = 'center'>$dns_emp_code - $emp_name</td></tr>";
			while($rowcustomercheck=mysql_fetch_array($rscustomercheck)){
				$customer_code=$rowcustomercheck['customer_code'];
				$customer_name=$rowcustomercheck['customer_name'];
				$dns_customer_code=$rowcustomercheck['dns_customer_code'];
				$cust_type=$rowcustomercheck['cust_type'];
				$route_code=$rowcustomercheck['route_code'];
				foreach($date_array as $date_array_val)
				{
					
				$sql_order_details = "SELECT OH.customer_code,SUM(OD.qty) as total_qty,SUM((OD.amount-((OD.amount*OH.TD)/100))) 
										as total_amount,DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') as order_date,OH.transaction_type
										FROM order_header OH INNER JOIN order_details OD 
										WHERE OH.order_no=OD.order_no AND OH.customer_code='".$customer_code."' AND  
										SUBSTRING(OH.order_no,-14,8)='".$date_array_val."' AND SUBSTRING(OH.order_no,2,5) = '".$emp_code."' 
										GROUP BY OH.customer_code";
				$res_order_details = mysql_query($sql_order_details);
				$order_rows = mysql_num_rows($res_order_details);
						$row_order_details = mysql_fetch_array($res_order_details);
						$total_qty = $row_order_details['total_qty'];
						$total_amount = $row_order_details['total_amount'];
						$order_date = $row_order_details['order_date'];
						$transaction_type = $row_order_details['transaction_type'];
						//For payment
						$sql_payment_details = "SELECT SUM(amount) as collection_amount FROM payment_header WHERE customer_code = '".$customer_code."' 
											AND SUBSTRING(receipt_id,-14,8)='".$date_array_val."' AND SUBSTRING(receipt_id,2,5) = '".$emp_code."' 
											GROUP BY customer_code";
						$res_payment_details = mysql_query($sql_payment_details);
						$row_payment_details = mysql_fetch_array($res_payment_details);
						$collection_amount = $row_payment_details['collection_amount'];
						
						//For Check in and Check out
						$sqlcheckinout="SELECT CIO.check_in_time,CIO.check_out_time,CIO.remarks,LO.latt,LO.longi,LO.trans_id FROM 
										check_in_out_details CIO,location LO 
										WHERE LO.trans_id=CIO.trans_id AND CIO.customer_code='".$customer_code."' AND 
									  SUBSTRING(CIO.trans_id,-14,8)='".$date_array_val."' 
									 AND SUBSTRING(CIO.trans_id,3,5) = '".$emp_code."' ORDER BY CIO.check_in_time ASC";
						$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select check in out details ".$sqlcheckinout);
						$countcheckinout=mysql_num_rows($rscheckinout);
						if($countcheckinout >0)
						{
							$time_difference_final=0;
							${check_in_time.$customer_code}='';
							${check_out_time.$customer_code}='';
							while($rowcheckinout=mysql_fetch_array($rscheckinout))
							{
								${check_in_time.$customer_code}=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_in_time']));
								${check_out_time.$customer_code}=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_out_time']));
								${latt.$customer_code}=$rowcheckinout['latt'];
								${longi.$customer_code}=$rowcheckinout['longi'];
								${check_in_out_transid.$customer_code}=$rowcheckinout['trans_id'];
								$time_difference=strtotime($rowcheckinout['check_out_time'])-strtotime($rowcheckinout['check_in_time']);
								$time_difference_final=$time_difference_final+$time_difference;
								$checkinout_remarks=$rowcheckinout['remarks'];
								if($time_difference_final >=3600)
								{
									$hours = floor($time_difference_final / 3600);
									$minutes = floor(($time_difference_final / 60) % 60);
									$seconds = $time_difference_final % 60;
									$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
								}
								else if($time_difference_final >=60 && $time_difference_final<3600)
								{
									$minutes = floor(($time_difference_final / 60) % 60);
									$seconds = $time_difference_final % 60;
									$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
								}
								else
								{
									$seconds = $time_difference_final % 60;
									$time_duration=$seconds.' Second(s)';
								}
								if($countcheckinout >1)
								{
								${check_in_time_final.$customer_code}=${check_in_time_final.$customer_code}.${check_in_time.$customer_code}." ";
								${check_out_time_final.$customer_code}=${check_out_time_final.$customer_code}.${check_out_time.$customer_code}." ";
								${time_duration_final.$customer_code}=${time_duration_final.$customer_code}.$time_duration." ";
								${checkinout_remarks_final.$customer_code}=${checkinout_remarks_final.$customer_code}.$checkinout_remarks." ";
								}
								else
								{
									${check_in_time_final.$customer_code}=${check_in_time_final.$customer_code}.${check_in_time.$customer_code};
									${check_out_time_final.$customer_code}=${check_out_time_final.$customer_code}.${check_out_time.$customer_code};
									${time_duration_final.$customer_code}=${time_duration_final.$customer_code}.$time_duration;
									${checkinout_remarks_final.$customer_code}=${checkinout_remarks_final.$customer_code}.$checkinout_remarks;
								}
							}
						}
						else
						{
							$time_duration='';
							$time_difference_final='';
							$checkinout_remarks='';
							$check_in_time='';
							$check_out_time='';
						}
						if($transaction_type=='TO')
						{
							$checkinout_remarks='Telephonic Order'."\n".$checkinout_remarks;
						}
						${customer_name.$customer_code.$date_array_val.$emp_code}=$customer_name;
						${dns_customer_code.$customer_code.$date_array_val.$emp_code}=$dns_customer_code;
						${cust_type.$customer_code.$date_array_val.$emp_code}=$cust_type;
						${route_code.$customer_code.$date_array_val.$emp_code}=$route_code;
						${total_qty.$customer_code.$date_array_val.$emp_code}=$total_qty;
						${total_amount.$customer_code.$date_array_val.$emp_code}=$total_amount;
						${collection_amount.$customer_code.$date_array_val.$emp_code}=$collection_amount;
						${time_duration.$customer_code.$date_array_val.$emp_code}=${time_duration_final.$customer_code};;
						${time_difference_final.$customer_code.$date_array_val.$emp_code}=$time_difference_final;
						${check_in_time.$customer_code.$date_array_val.$emp_code}=${check_in_time_final.$customer_code};
						${check_out_time.$customer_code.$date_array_val.$emp_code}=${check_out_time_final.$customer_code};
						${checkinout_remarks.$customer_code.$date_array_val.$emp_code}=preg_replace('/[\r\n]+/', '',${checkinout_remarks_final.$customer_code});
						${checkinout_remarks.$customer_code.$date_array_val.$emp_code}=str_replace('"','',${checkinout_remarks.$customer_code.$date_array_val.$emp_code});
						${checkinout_remarks.$customer_code.$date_array_val.$emp_code}=str_replace(',','',${checkinout_remarks.$customer_code.$date_array_val.$emp_code});
						${latt.$customer_code.$date_array_val.$emp_code}=${latt.$customer_code};
						${longi.$customer_code.$date_array_val.$emp_code}=${longi.$customer_code};
						${check_in_out_transid.$customer_code.$date_array_val.$emp_code}=${check_in_out_transid.$customer_code};
					  }
					  	array_push(${customer_code_array.$emp_code},$customer_code);
					}
					foreach($date_array as $date_array_val)
					{
						$sqlatttime="SELECT SUBSTRING(date,12,8) AS att_time,latt,longi,trans_id FROM location WHERE emp_code='".$emp_code."' AND 
										SUBSTRING(trans_id,-14,8)='".$date_array_val."' AND trans_id like 'A%'";
						$rsatttime=mysql_query($sqlatttime);
						$rowatttime=mysql_fetch_array($rsatttime);
						$atttime=$rowatttime['att_time'];
						$trans_id_att=$rowatttime['trans_id'];
						$latt_att=$rowatttime['latt'];
						$longi_att=$rowatttime['longi'];
					
						$table_data .= date('d-m-Y',strtotime($date_array_val))."\t".'Attendance'."\t".''."\t".''."\t".$dns_emp_code."\t".$emp_name."\t".$zone."\t".$state."\t".$designation."\t".''."\t".''."\t".''."\t".$atttime."\t".''."\t".''."\t".''."\t".$latt_att."\t".$longi_att."\n";
						foreach(${customer_code_array.$emp_code} as $customer_code_val)
						{
							if(${customer_name.$customer_code_val.$date_array_val.$emp_code} != '' &&(${total_qty.$customer_code_val.$date_array_val.$emp_code} >0 || ${collection_amount.$customer_code_val.$date_array_val.$emp_code} >0 || ${time_difference_final.$customer_code_val.$date_array_val.$emp_code} >0)){
								if(${total_amount.$customer_code_val.$date_array_val.$emp_code}=='')	${total_amount.$customer_code_val.$date_array_val.$emp_code}='';
								else  ${total_amount.$customer_code_val.$date_array_val.$emp_code}=number_format(${total_amount.$customer_code_val.$date_array_val.$emp_code},2);
								if(${collection_amount.$customer_code_val.$date_array_val.$emp_code}=='')	${collection_amount.$customer_code_val.$date_array_val.$emp_code}='';
								else ${collection_amount.$customer_code_val.$date_array_val.$emp_code}=number_format(${collection_amount.$customer_code_val.$date_array_val.$emp_code},2);
								$sqlroutename="SELECT route_name FROM route_master WHERE 
												route_code='".${route_code.$customer_code_val.$date_array_val.$emp_code}."'";
								$rsroutename=mysql_query($sqlroutename);
								$rowroutename=mysql_fetch_array($rsroutename);
								
								if(providing_code=='yes'){
									$dns_customer_code_val="\t".${dns_customer_code.$customer_code_val.$date_array_val.$emp_code};
								}
								else{
									$dns_customer_code_val='';
								}
								
								$table_data .= date('d-m-Y',strtotime($date_array_val)).$dns_customer_code_val."\t".
								${customer_name.$customer_code_val.$date_array_val.$emp_code}."\t".$rowroutename['route_name']."\t".${cust_type.$customer_code_val.$date_array_val.$emp_code}."\t".$dns_emp_code."\t".$emp_name."\t".$zone."\t".$state."\t".$designation."\t".${total_qty.$customer_code_val.$date_array_val.$emp_code}."\t".${total_amount.$customer_code_val.$date_array_val.$emp_code}."\t".${collection_amount.$customer_code_val.$date_array_val.$emp_code}."\t".${check_in_time.$customer_code_val.$date_array_val.$emp_code}."\t".${check_out_time.$customer_code_val.$date_array_val.$emp_code}."\t".${time_duration.$customer_code_val.$date_array_val.$emp_code}."\t".${checkinout_remarks.$customer_code_val.$date_array_val.$emp_code}."\t".${latt.$customer_code_val.$date_array_val.$emp_code}.
								"\t".${longi.$customer_code_val.$date_array_val.$emp_code}."\n";
								
							}
						}
					}
				}
			}
	if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Daily_Activity_Report_Details.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
mysql_close($link);
?>


