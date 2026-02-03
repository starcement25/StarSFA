<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_acednsproduct");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$current_month = date('m');
$current_year = date('Y');
$current_date = date('Y-m-d');
$two_days_back = date('Y-m-d',strtotime("-2 days"));

$nick_names = array();
$sql_nicknames = "SELECT nick_name FROM user_details WHERE working_mode = 'live' OR nick_name IN('STAR','LIPL') ORDER BY nick_name ASC";
$res_nicknames = mysql_query($sql_nicknames);
while($row_nicknames = mysql_fetch_array($res_nicknames)){
	$nick_names[] = "acedns_".$row_nicknames['nick_name'];
}

function star_db_function(){
	define("SERVERREMOTE","52.66.101.239");
	define("USERREMOTE","root");
	define("PASSWORDREMOTE","cmcl@123");
	$conn = mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE);
}
function lipl_db_function(){
	define("SERVERLIPL","89.107.61.190");
	define("USERLIPL","fmpowera_dnsprod");
	define("PASSWORDLIPL","acednsprod1234");
	$conn = mysql_connect(SERVERLIPL,USERLIPL,PASSWORDLIPL);
	$val = 'fmpowera_LIPL';
}
function other_db_function(){
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	$conn = mysql_connect(SERVER,USER,PASSWORD);
}

function insert_update_transaction_count($atd_col_name,$trans_col_name,$total_attend,$total_trans,$n_name,$year_val){
	other_db_function();
	$sql_check_data_exist = "SELECT nick_name FROM acedns_acednsproduct.transaction_count WHERE nick_name = '".$n_name."' AND year = '".$year_val."'";
	$res_check_data_exist = mysql_query($sql_check_data_exist);
	$total_check_rows = mysql_num_rows($res_check_data_exist);
	
	if($total_check_rows>0){
		$sql_insert_atd = "UPDATE acedns_acednsproduct.transaction_count SET $atd_col_name = '".$total_attend."', $trans_col_name = '".$total_trans."' WHERE nick_name = '".$n_name."' AND year = '".$year_val."'";
	}
	else{
		$sql_insert_atd = "INSERT INTO acedns_acednsproduct.transaction_count SET nick_name = '".$n_name."', year = '".$year_val."', $atd_col_name = '".$total_attend."', $trans_col_name = '".$total_trans."'";
	}
	mysql_query($sql_insert_atd);
}

foreach($nick_names as $val)
{
	if($val == 'acedns_STAR'){
		star_db_function();
	}
	else if($val == 'acedns_LIPL'){
		lipl_db_function();
		$val = 'fmpowera_LIPL';
	}
	else if($val != 'acedns_STAR' && $val != 'acedns_LIPL'){
		other_db_function();
	}
	
	if (mysql_select_db($val)){
		if($val == 'fmpowera_LIPL')
			$n_name = substr($val,9);
		else
			$n_name = substr($val,7);
		
		
		$sql_location_year = "SELECT DISTINCT SUBSTRING(date,1,4) AS year_val FROM location WHERE SUBSTRING(date,1,4) != '0000' ORDER BY SUBSTRING(date,1,4) ASC";
		$res_location_year = mysql_query($sql_location_year);
		while($row_location_year = mysql_fetch_array($res_location_year)){
			$year_val = $row_location_year['year_val'];
			
			$sql_location_month = "SELECT DISTINCT SUBSTRING(date,6,2) AS month_val FROM location WHERE SUBSTRING(date,1,4) = '".$year_val."' ORDER BY SUBSTRING(date,6,2) ASC";
			$res_location_month = mysql_query($sql_location_month);
			while($row_location_month = mysql_fetch_array($res_location_month)){
				$month_val = $row_location_month['month_val'];
				
				if($current_month == $month_val && $current_year == $year_val){
					//$current_year_month_condition = " AND SUBSTRING(date,1,10) != '".$current_date."' ";
					$current_year_month_condition = " AND SUBSTRING(date,1,10) <= '".$two_days_back."' ";
				}
				else{
					$current_year_month_condition = "";
				}
				
				/*----> ATTENDANCE COUNT <----*/
				$sql_total_attend = "SELECT COUNT(DISTINCT emp_code) AS today_atd FROM location WHERE SUBSTRING(date,1,4) = '".$year_val."' AND SUBSTRING(date,6,2) = '".$month_val."' AND trans_id LIKE 'A%' ".$current_year_month_condition;
				$res_total_attend = mysql_query($sql_total_attend);
				$row_total_attend = mysql_fetch_array($res_total_attend);
				$total_attend = $row_total_attend['today_atd'];
					
				/*----> TRANSACTION COUNT <----*/				
				$sql_total_trans = "SELECT COUNT(trans_id) FROM location WHERE SUBSTRING(date,1,4) = '".$year_val."' AND SUBSTRING(date,6,2) = '".$month_val."'".$current_year_month_condition;
				$res_total_trans = mysql_query($sql_total_trans);
				$row_total_trans = mysql_fetch_array($res_total_trans);
				$total_trans = $row_total_trans['COUNT(trans_id)'];
				
				$atd_col_name = 'atd_'.$month_val;
				$trans_col_name = 'trans_'.$month_val;
				
				insert_update_transaction_count($atd_col_name,$trans_col_name,$total_attend,$total_trans,$n_name,$year_val);
				if($val == 'acedns_STAR'){
					star_db_function();
				}
				else if($val == 'fmpowera_LIPL'){
					lipl_db_function();
					//$val = 'fmpowera_LIPL';
				}
				else if($val != 'acedns_STAR' && $val != 'fmpowera_LIPL'){
					other_db_function();
				}
				mysql_select_db($val);
			}
		}
	
	}
}
?>