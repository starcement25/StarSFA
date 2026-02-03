<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$mode=$_REQUEST['mode'];
$from_date=$_REQUEST['from_date'];
$to_date=$_REQUEST['to_date'];
$customer_code=$_REQUEST['customer_code'];
$employee_hierarchy=return_employee_hierarchy($emp_code);
$emp_hierarchy_condition=' AND CRR.emp_code IN('.$employee_hierarchy.')';
$date_condition = "1"; 
if($mode=='customertoday')
{
	$date=date('Y-m-d');
	$date_condition = " DATE_FORMAT(SUBSTRING(DM.DO_no,-14,8),'%Y-%m-%d') = '".$date."' ";
}
else if($mode=='customermtd')
{
	$date_condition="  YEAR(DATE_FORMAT(SUBSTRING(DM.DO_no,-14,8),'%Y-%m-%d')) = YEAR(CURDATE()) AND MONTH(DATE_FORMAT(SUBSTRING(DM.DO_no,-14,8),'%Y-%m-%d')) = MONTH(CURDATE()) ";
}
else if($mode=='customercustom')
{
	$date_condition="  DATE_FORMAT(SUBSTRING(DM.DO_no,-14,8),'%Y%m%d') <='".$to_date."' 
						AND DATE_FORMAT(SUBSTRING(DM.DO_no,-14,8),'%Y%m%d') >='".$from_date."'";
}

if(strtoupper(substr($emp_code,0,1))=='E')
{
   $sqlquery="SELECT DISTINCT DM.DO_no FROM customer_master CM,DO_transaction DM,product_master PM where DM.sku_code=PM.prod_code and DM.customer_code=CM.customer_code AND DM.customer_code='".$customer_code."'  AND ".$date_condition." ORDER BY CM.customer_name ASC
";
}
if(strtoupper(substr($emp_code,0,1))=='C')
{
    $sqlquery="SELECT DISTINCT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,SUM(DM.qty) as qty,

				(DM.sale_rate+DM.freight_charge) AS sale_rate,SUM(DM.amount) as amount,DM.status,

				DM.incoterms,DM.dns_sauda_no,DM.freight_charge FROM DO_master DM,product_master PM,

				product_sub_group_master PSM

				WHERE DM.status='no' AND DM.is_approved='yes' AND DM.sku_code=PM.prod_code 

				AND PM.product_sub_group_code=PSM.product_sub_group_code

				AND DM.customer_code='".$emp_code."'

				GROUP BY DM.customer_code,DM.sku_code ORDER BY PSM.product_sub_group_name DESC,DM.mapped_sku_code ASC,DM.qty DESC";
}
if(strtoupper(substr($emp_code,0,1))=='B')
{
    $sqlquery="SELECT DISTINCT DM.sauda_no,DM.customer_code,DM.branch_code,DM.sku_code,DM.mapped_sku_code,SUM(DM.qty) as qty,

				(DM.sale_rate+DM.freight_charge) AS sale_rate,SUM(DM.amount) as amount,DM.status,

				DM.incoterms,DM.dns_sauda_no,DM.freight_charge FROM DO_master DM,product_master PM,

				product_sub_group_master PSM

				WHERE DM.status='no' AND DM.is_approved='yes' AND DM.sku_code=PM.prod_code 

				AND PM.product_sub_group_code=PSM.product_sub_group_code

				AND DM.customer_code IN(SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.")) 

				GROUP BY DM.customer_code,DM.sku_code ORDER BY PSM.product_sub_group_name DESC,DM.mapped_sku_code ASC,DM.qty DESC";
}
//echo"<pre>";print_r($sqlquery);die;
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count>0){

		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));

		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowDOtrans = mysqli_fetch_assoc($result))
		{
			$DO_no=$rowDOtrans['DO_no'];
			$contents  = (($DO_no!='')?$DO_no: ' ');
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'1';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'1';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/DO-download-mode-dono-wise-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&mode=$mode&customer_code=$customer_code&from_date=$from_date&to_date=$to_date";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=DO_transaction_mode_saudano_wise.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>