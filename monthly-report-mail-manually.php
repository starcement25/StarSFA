<?php
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
$linkdbaccess=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
mysqli_select_db("acedns_acednsproduct",$linkdbaccess) or die("could not connect the setup database");
	/*if($nick_name=='ABDOS')
	{
		echo '0'.'¥'.'0';
		exit();
	}*/
	$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsdbaccessdetails=mysqli_query($link,$sqldbaccessdetails,$linkdbaccess);
	$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
	$remote_db_access=$rowdbaccessdetails['remote_db_access'];
define("DB","acedns_PARLE");	

require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

function getReverseGeo($latitude,$longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data,true);
	
	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if(is_array($jsondata )&& $jsondata['status']=='OK')
	{
		  $addr = $jsondata['results']['0']['formatted_address'];
	}		
	return  $addr;	
}
if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
/*$year=date('Y');
$month=str_pad((date('m')-1), 2, "0", STR_PAD_LEFT);
$prev_month_day=$year.'-'.$month.'-01';
$report_month=$year.'-'.$month;*/
$nick_name='PARLE';
$year=date("Y", strtotime("-1 months"));
$month=str_pad((date("m", strtotime("-1 months"))), 2, "0", STR_PAD_LEFT);
$prev_month_day=$year.'-'.$month.'-01';
$report_month=$year.'-'.$month;
//$report_month='2018-04';
$sqlselemp="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_code ASC";
$rsselemp=mysqli_query($link,$sqlselemp);
while($rowselemp=mysqli_fetch_assoc($rsselemp))
{
    $emp_code=$rowselemp['emp_code'];
	$sqlchkreportgeneration="SELECT emp_code FROM monthly_report_generation_log WHERE emp_code='".$emp_code."' AND report_month='".$report_month."'";
	$rschkreportgeneration=mysqli_query($link,$sqlchkreportgeneration);
	$cntchkreportgeneration=mysqli_num_rows($rschkreportgeneration);

if($cntchkreportgeneration ==0){
	$sql_operation_date = "SELECT LO.emp_code, (CASE WHEN LO.trans_id LIKE 'A%' THEN SUBSTRING(LO.date,1,10) END) AS present_date,
						(CASE WHEN LO.trans_id LIKE 'A%' THEN SUBSTRING(LO.date,12,8) END) AS present_time FROM location LO 
						WHERE SUBSTRING(LO.trans_id,-14,4)='".$year."' AND SUBSTRING(LO.trans_id,-10,2)='".$month."' 
						AND LO.emp_code='".$emp_code."' GROUP BY SUBSTRING(LO.date,1,10)";
	$res_operation_date = mysqli_query($link,$sql_operation_date);
	$count_operation_date = mysqli_num_rows($res_operation_date);
	if($count_operation_date>0){
		$table_data = "<table width=\"80%\" class=\"border\" border=\"1\" style=\"border-collapse:collapse;\" id=\"display_table\">
		  <tr class=\"TDHEAD\" align=\"center\">
			<td width=\"16%\">DATE</td>
			<td width=\"16%\">TC</td>
			<td width=\"16%\">PC</td>
			<td width=\"16%\">LOG-IN TIME</td>
			<td width=\"16%\">LOG-OUT TIME</td>
			<td width=\"\">SALES</td>
		  </tr>";
		$date_array=array();
		while($row_operation_date = mysqli_fetch_assoc($res_operation_date)){
			$emp_code = $row_operation_date['emp_code'];
			$present_date = $row_operation_date['present_date'];
			$present_time = $row_operation_date['present_time'];
			$present_date_final=str_replace('-','',$present_date);
			
			$sqlchkouttime="SELECT SUBSTRING(date,12,8) AS checkouttime FROM location WHERE emp_code='".$emp_code."' 
							AND trans_id LIKE 'CH%' AND SUBSTRING(date,1,10)='".$present_date."'";
			$rschkouttime=mysqli_query($link,$sqlchkouttime);
			$rowchkouttime=mysqli_fetch_assoc($rschkouttime);	
			$chkouttime=$rowchkouttime['checkouttime'];			
			
			   $sql_prod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS productive_calls,
							SUM(amount) As order_val 
							FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'O%' 
							AND SUBSTRING(order_no,-14,8)='".$present_date_final."'";
				$res_prod_call = mysqli_query($link,$sql_prod_call);
				$row_prod_call = mysqli_fetch_assoc($res_prod_call);
				$prod_call = $row_prod_call['productive_calls'];
				$order_val=$row_prod_call['order_val'];
		
				$sql_nonprod_call = "SELECT COUNT( DISTINCT CONCAT(customer_code,'^',SUBSTRING(order_no,-14,8)) ) AS non_productive_calls FROM `prev_order_counting_master` WHERE SUBSTRING(order_no,-19,5) = '".$emp_code."' AND order_no LIKE 'NO%' AND SUBSTRING(order_no,-14,8)='".$present_date_final."'";
				$res_nonprod_call = mysqli_query($link,$sql_nonprod_call);
				$row_nonprod_call = mysqli_fetch_assoc($res_nonprod_call);
				$non_prod_call = $row_nonprod_call['non_productive_calls'];

				$table_data.= "<tr>
									<td>".date('d-m-Y',strtotime($present_date))."</td>
									<td align=\"right\">".($prod_call+$non_prod_call)."</td>
									<td align=\"right\">".$prod_call."</td>
									<td align=\"right\">".$present_time."</td>
									<td align=\"right\">".$chkouttime."</td>
									<td align=\"right\">".number_format($order_val,2)."</td>
								  </tr>";
				$total_call=$total_call+($prod_call+$non_prod_call);
				$total_productive_call=$total_productive_call+$prod_call;
				$total_order_val=$total_order_val+$order_val;	
				
				if(!in_array($present_date,$date_array))
				{
					array_push($date_array,$present_date);
				}
				$count++;
			}
			$table_data_total="<tr>
								<td>TOTAL</td>
								<td align=\"right\">".$total_call."</td>
								<td align=\"right\">".$total_productive_call."</td>
								<td></td>
								<td></td>
								<td align=\"right\">".number_format($total_order_val,2)."</td>
							  </tr>";
			$sql_emp_details = "SELECT emp_name, email FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysqli_query($link,$sql_emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$emp_name = $row_emp_details['emp_name'];
			$email = $row_emp_details['email'];
			
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code);
			$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.")";
			$rsemailhierarchy=mysqli_query($link,$sqlemailhierarchy);
			$email_hierarchy='';
			while($rowemailhierarchy=mysqli_fetch_assoc($rsemailhierarchy))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
			}
			$email_hierarchy=substr($email_hierarchy,0,-1);

			$emailsubj="$nick_name - monthly report from - ".$emp_name." of ".date("F", strtotime($prev_month_day)).','.$year;
			$mailbody = "<html><head><title>monthly report</title></head>
						<body>Monthly report from <b>"
						.$emp_name. "</b><br><br>".$table_data.$table_data_total."</table><br><br>Powered By aceDNS</body></html>";
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Bcc: ".BCCEMAIL." \r\n".
						'X-Mailer: PHP/' . phpversion();		
			$emailall=$email.','.$email_hierarchy.','.'vikram.singh1@parle.biz';
			//$emailall='vikram.singh1@parle.biz';
			if(mail($emailall, $emailsubj, $mailbody, $headers,$spam_filter))
			{
				echo 'success';
				$sqlinsertreportgenerationlog="INSERT INTO monthly_report_generation_log 
												SET emp_code='".$emp_code."',
												report_month='".$report_month."',
												report_generate_date_time=CURRENT_TIMESTAMP()";
			   mysqli_query($link,$sqlinsertreportgenerationlog);									
			}
			else
			{
				echo 'failure';
			}
		}
	}
}
mysqli_close($link);
?>
