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
		$emp_condition = ' 1';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_condition = " emp_code IN(".$emp_hierarchy_value.") ";
	}
	
}
else{
	$order_condition = " SUBSTRING(order_no,2,5) IN(".$employee.") AND ";
	$payment_condition = " SUBSTRING(receipt_id,2,5) IN(".$employee.") AND ";
	$checkinout_condition = " SUBSTRING(trans_id,3,5) IN('".$employee."') AND ";
	$emp_condition = " emp_code IN('".$employee."') ";
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
sort($date_array);
//$outer_customer_code=substr($outer_customer_code,0,-1);
//if(!empty($date_array)){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
      <?php if(providing_code=='yes'){?>
        <tr class="TDHEAD">
      	<td width="9%">Date</td>
        <td width="7%">DNS Customer Code</td>
        <td width="">Customer Name</td>
        <td width="8%">Cust Type</td>
         <td width="10%">Check in Time</td>
         <td width="10%">Check out Time</td>
         <td width="12%">Duration</td>
         <td width="9%">Locate</td>
      </tr>
      <?php }else{?>
      <tr class="TDHEAD">
      	<td width="5%">Sl no</td>
      	<td width="10%">Date</td>
        <td width="%">Customer Name</td>
         <td width="8%">Cust Type</td>
         <td width="10%">Check in Time</td>
         <td width="10%">Check out Time</td>
         <td width="12%">Duration</td>
         <?php if(strtoupper($_SESSION['nick_name'])!='ABDOS'){?>
         <td width="10%">Locate</td>
         <?php
		 }?>
      </tr>
      <?php }?>
    <?
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE ".$emp_condition." ORDER BY emp_code ASC";
	$res_emp = mysql_query($sql_emp);
	$displaycnt=1;
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		
		${customer_code_array.$emp_code}=array();
		${outer_customer_code.$emp_code}=substr(${outer_customer_code.$emp_code},0,-1);
		$sqlcustomercheck="SELECT DISTINCT customer_code,dns_customer_code,cust_type,customer_name,route_code FROM customer_master WHERE customer_code IN(".${outer_customer_code.$emp_code}.") ORDER BY FIELD(customer_code, ${outer_customer_code.$emp_code})";
		$rscustomercheck=mysql_query($sqlcustomercheck);
		$customer_row_check=mysql_num_rows($rscustomercheck);
		
		//exit();
		if($customer_row_check>0){
			echo "<tr><td colspan = '8' class = 'TDHEAD_SUB' align = 'center'>$dns_emp_code - $emp_name</td></tr>";
			while($rowcustomercheck=mysql_fetch_array($rscustomercheck)){
				$customer_code=$rowcustomercheck['customer_code'];
				$customer_name=$rowcustomercheck['customer_name'];
				$dns_customer_code=$rowcustomercheck['dns_customer_code'];
				$cust_type=$rowcustomercheck['cust_type'];
				$route_code=$rowcustomercheck['route_code'];
				foreach($date_array as $date_array_val)
				{
						
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
								${check_in_time_final.$customer_code}=${check_in_time_final.$customer_code}.${check_in_time.$customer_code}.'<br /><br />';
								${check_out_time_final.$customer_code}=${check_out_time_final.$customer_code}.${check_out_time.$customer_code}.'<br /><br />';
								${time_duration_final.$customer_code}=${time_duration_final.$customer_code}.$time_duration.'<br /><br />';
								${checkinout_remarks_final.$customer_code}=${checkinout_remarks_final.$customer_code}.$checkinout_remarks.'<br /><br />';
						 	}
						}
						else
						{
							$time_duration='';
							$time_difference_final='';
							$checkinout_remarks='';
						}
						${customer_name.$customer_code.$date_array_val.$emp_code}=$customer_name;
						${dns_customer_code.$customer_code.$date_array_val.$emp_code}=$dns_customer_code;
						${cust_type.$customer_code.$date_array_val.$emp_code}=$cust_type;
						${check_in_time.$customer_code.$date_array_val.$emp_code}=${check_in_time_final.$customer_code};
						${check_out_time.$customer_code.$date_array_val.$emp_code}=${check_out_time_final.$customer_code};
						${time_duration.$customer_code.$date_array_val.$emp_code}=${time_duration_final.$customer_code};
						${time_difference_final.$customer_code.$date_array_val.$emp_code}=$time_difference_final;
						${latt.$customer_code.$date_array_val.$emp_code}=${latt.$customer_code};
						${longi.$customer_code.$date_array_val.$emp_code}=${longi.$customer_code};
						${check_in_out_transid.$customer_code.$date_array_val.$emp_code}=${check_in_out_transid.$customer_code};
					  }
					  	array_push(${customer_code_array.$emp_code},$customer_code);
					}
					foreach($date_array as $date_array_val)
					{
									
						foreach(${customer_code_array.$emp_code} as $customer_code_val)
						{
							if(${customer_name.$customer_code_val.$date_array_val.$emp_code} != '' && ${time_difference_final.$customer_code_val.$date_array_val.$emp_code} >0){
								if(providing_code=='yes'){
									
									echo "<tr>
									<td>".date('d-m-Y',strtotime($date_array_val))."</td>
									<td>".${dns_customer_code.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${customer_name.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${cust_type.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_in_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_out_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${time_duration.$customer_code_val.$date_array_val.$emp_code}."</td>";
									if(strtoupper($_SESSION['nick_name'])!='ABDOS'){
									echo"<td><a href=\"customerLocate.php?trans_id=${check_in_out_transid.$customer_code_val.$date_array_val.$emp_code}&customer_code=$customer_code_val&
							emp_code=$emp_code&date=$date_array_val&page=activitydetails\" style=\"color:#930;font-weight:bold;\" target=\"_blank\">Locate</a></td>";
									}
								  echo "</tr>";
								}
								else
								{
								/*echo "<tr>
									<td>".date('d-m-Y',strtotime($date_array_val))."</td>
									<td>".${customer_name.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${cust_type.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_in_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_out_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${time_duration.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td><a href=\"customerLocate.php?trans_id=${check_in_out_transid.$customer_code_val.$date_array_val.$emp_code}&customer_code=$customer_code_val&
							emp_code=$emp_code&date=$date_array_val&page=activitydetails\" style=\"color:#930;font-weight:bold;\" target=\"_blank\">Locate</a>
							</td>
								  </tr>";*/
								  echo "<tr>
								  	<td>".$displaycnt++."</td>
									<td>".date('d-m-Y',strtotime($date_array_val))."</td>
									<td>".${customer_name.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${cust_type.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_in_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${check_out_time.$customer_code_val.$date_array_val.$emp_code}."</td>
									<td>".${time_duration.$customer_code_val.$date_array_val.$emp_code}."</td>";
									if(strtoupper($_SESSION['nick_name'])!='ABDOS'){
									echo "<td><a href=\"customerLocate.php?trans_id=${check_in_out_transid.$customer_code_val.$date_array_val.$emp_code}&customer_code=$customer_code_val&
							emp_code=$emp_code&date=$date_array_val&page=activitydetails\" style=\"color:red;font-weight:bold;\" target=\"_blank\">Locate</a></td>";
									}
							echo "</tr>";
								}
							}
						}
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
mysql_close($link);
?>


