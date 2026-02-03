<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");
date_default_timezone_set("Asia/Kolkata");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$today = date('Y-m-d');
$curdateone = date('Ymd');

/*$sql_truncate = "TRUNCATE mis_data_details";
$res_truncate = mysql_query($sql_truncate);*/

$sql_select_emp_details = "SELECT emp_code FROM mis_data_details";
$res_select_emp_details = mysql_query($sql_select_emp_details);
while($row_select_emp_details = mysql_fetch_array($res_select_emp_details)){
	$emp_code = $row_select_emp_details['emp_code'];
		for($i=1;$i<=3;$i++){
		/*if($i == 1){
			$stock_audit_col = 'stock_audit_tdy';
		}*/
		if($i == 2){
			$stock_audit_col = 'stock_audit_mtd';
		}
		if($i == 3){
			$stock_audit_col = 'stock_audit_ytd';
		}
				//For MTD OR Month Today
		if($i==2){
			$current_month = date('Ym');
			if(sale=='no' && instruction=='yes')
			{
				$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE()) ";
			}
			else
			{
				$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) AND MONTH(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
			}
			$sl_value='MTD';
			$val='MTD';
		}
		//For YTD OR Year Today
		if($i==3){
			$end_date = date('Y-m-d');
			
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			
			if($month>='04'){
				$fiinancial_year=$year.'-04-01';
			}
			else
			{
				$fiinancial_year=($year-1).'-04-01';
			}

			if(sale=='no' && instruction=='yes')
			{
				//$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
				$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
							AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
			}
			else
			{
				//$date_condition=" AND YEAR(DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')) = YEAR(CURDATE())";
				$date_condition=" AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d') 
							AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') >='".$fiinancial_year."'";
			}
			
			$sl_value='YTD';
			$val='YTD';
		}
		if($i == 2 || $i == 3){
				/*----------> Total Stock Audit <----------*/
			$sqlnostkaudit="SELECT COUNT(DISTINCT transaction_id) AS total_stk_audit FROM location LO,stock_audit SA 
									WHERE SA.transaction_id=LO.trans_id AND (LO.trans_id LIKE 'S%') 
									AND SUBSTRING(LO.emp_code,1,1)!='C' AND LO.emp_code = '".$emp_code."' ".$date_condition;
			$rsnostkaudit=mysql_query($sqlnostkaudit) or die(mysql_error()." Error in total no stk audit: ".$sqlnostkaudit);
			$rownostkaudit=mysql_fetch_array($rsnostkaudit);
			$no_stk_audit=$rownostkaudit['total_stk_audit'];
			
			$sql_update = "UPDATE mis_data_details SET 
							$stock_audit_col = '".$no_stk_audit."' WHERE emp_code = '".$emp_code."'";
			$res_update = mysql_query($sql_update);
		}
	}
}
