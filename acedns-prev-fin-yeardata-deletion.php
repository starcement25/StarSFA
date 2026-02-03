<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$finalzipfile="acedns-db-backup-".$date.$month.$year.$hour.$minute.$second.'.zip';
//$destination = "ftp://fmpoweracedns:iBf8d)u@]c[h@fmpower.acedns.in/public_html/acedns-bkup/$finalzipfile";

$linksetup = mysqli_connect('localhost','acedns_dnsprod','dnsprod1234'); 
mysqli_select_db("acedns_acednsproduct",$linksetup) or die("could not connect the setup database");

		$tables = array('location','order_header','order_details','payment_header','payment_details','route_plan','route_customer_plan','route_plan_log','stock_audit','apicalllog','attendence','prev_order_counting_master','sauda_header','sauda_details','sauda_transaction_log','sauda_allocation_log','xml_data','	mis_transaction_log');

	$sqluserdetails="SELECT nick_name FROM user_details WHERE working_mode='live' ORDER BY nick_name ASC";
	$rsuserdetails=mysqli_query($link,$sqluserdetails);
	while($rowuserdetails=mysqli_fetch_assoc($rsuserdetails))
	{
		$nick_name=$rowuserdetails['nick_name'];
		mysqli_select_db("acedns_$nick_name", $linksetup); 
		//mysqli_select_db('acedns_LIPL', $link); 
     
		//get all of the tables 
		/*$tables = array(); 
   		$result = mysqli_query($link,'SHOW TABLES'); 
    	while($row = mysqli_fetch_row($result)) 
    	{
			//$tables[] = $row[0]; 
			array_push($tables,$row[0]);
    	} */
		//cycle through 
		foreach($tables as $table) 
		{
			if($table=='location')
			{
				$sqldel="DELETE FROM location WHERE SUBSTRING(`date`,1,10) <'2016-04-01'";
			}
			if($table=='order_header')
			{
				$sqldel="DELETE FROM order_header WHERE DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='order_details' && $nick_name!='RKBK')
			{
				$sqldel="DELETE FROM order_details WHERE DATE_FORMAT(SUBSTRING(order_no,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='payment_header')
			{
				$sqldel="DELETE FROM payment_header WHERE DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='payment_details')
			{
				$sqldel="DELETE FROM payment_details WHERE DATE_FORMAT(SUBSTRING(receipt_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='route_plan')
			{
				$sqldel="DELETE FROM route_plan WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='route_customer_plan')
			{
				$sqldel="DELETE FROM route_customer_plan WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='route_plan_log')
			{
				$sqldel="DELETE FROM route_plan_log WHERE DATE_FORMAT(SUBSTRING(route_plan_trans_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='stock_audit')
			{
				$sqldel="DELETE FROM stock_audit WHERE DATE_FORMAT(SUBSTRING(transaction_id,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='apicalllog')
			{
				$sqldel="DELETE FROM apicalllog WHERE SUBSTRING(date_time,1,10) <'2016-04-01'";
			}
			if($table=='attendence')
			{
				$sqldel="DELETE FROM attendence WHERE SUBSTRING(`date`,1,10) <'2016-04-01'";
			}
			if($table=='prev_order_counting_master')
			{
				$sqldel="DELETE FROM prev_order_counting_master WHERE SUBSTRING(`visit_date`,1,10) <'2016-04-01'";
			}
			if($table=='sauda_header')
			{
				$sqldel="DELETE FROM sauda_header WHERE DATE_FORMAT(SUBSTRING(sauda_no,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}	
			if($table=='sauda_details')
			{
				$sqldel="DELETE FROM sauda_details WHERE DATE_FORMAT(SUBSTRING(sauda_no,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}	
			if($table=='sauda_transaction_log')
			{
				$sqldel="DELETE FROM sauda_transaction_log WHERE DATE_FORMAT(SUBSTRING(sauda_no,-14,8),'%Y-%m-%d') <'2016-04-01'";
			}
			if($table=='sauda_allocation_log')
			{
				$sqldel="DELETE FROM sauda_allocation_log WHERE SUBSTRING(allocation_date,1,10) <'2016-04-01'";
			}
			if($table=='xml_data')
			{
				$sqldel="DELETE FROM xml_data WHERE SUBSTRING(insertdate,1,10) <'2016-04-01'";
			}
			if($table=='mis_transaction_log')
			{
				$sqldel="DELETE FROM mis_transaction_log WHERE SUBSTRING(trans_date,1,10) <'2016-04-01'";
			}
			if(mysqli_query($link,$sqldel))
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		}
	}
	
if($flag==1)
{
	echo 'SUCCESS';
}
else
{
	echo 'FAILURE';
}
//$zip->close();
?>	