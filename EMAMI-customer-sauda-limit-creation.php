<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMIT");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$sqlquery="SELECT refresh_date_time FROM pending_contract_data_refresh_log ORDER BY refresh_date_time DESC LIMIT 0,1";
$result = mysqli_query($link,$sqlquery);
$rowquery=mysqli_fetch_assoc($result);
$last_pending_contract_uploading_date=substr($rowquery['refresh_date_time'],0,10);
$cur_date=date('Y-m-d');
$datediff = strtotime($cur_date) - strtotime($last_pending_contract_uploading_date);
$datediffdays=round($datediff / (60 * 60 * 24));
$date_diff_array=array();
if($datediffdays > 0)
{
	for($i=1;$i<=$datediffdays ;$i++)
	{
		$date_array_incremental=date('Y-m-d', strtotime($last_pending_contract_uploading_date ." +$i day"));
		array_push($date_diff_array,$date_array_incremental);
	}
}
$sqlcustomerlist="SELECT customer_code,sauda_limit,sauda_limit_one FROM customer_master WHERE acedns='Y' AND black_list='N'";
$rscustomerlist=mysqli_query($link,$sqlcustomerlist);
while($rowcustomerlist=mysqli_fetch_assoc($rscustomerlist))
{
	$customer_code=$rowcustomerlist['customer_code'];
	$sauda_limit=$rowcustomerlist['sauda_limit'];
	$sauda_limit_one=$rowcustomerlist['sauda_limit_one'];
	
	$sqlquerypendingcontract="SELECT (SUM(PCA.qty_0_15)+SUM(PCA.qty_16_30)+SUM(PCA.qty_31_45)+SUM(PCA.qty_46_60)+SUM(PCA.qty_greater_60)) 
							AS pending_qty FROM pending_contract_ageing PCA WHERE customer_code='".$customer_code."'";
	$rsquerypendingcontract=mysqli_query($link,$sqlquerypendingcontract);	
	$rowquerypendingcontract=mysqli_fetch_assoc($rsquerypendingcontract);
	${pending_contract.$customer_code}=$rowquerypendingcontract['pending_qty'];
	
	if(count($date_diff_array) >0)
	{
		foreach($date_diff_array as $date_diff_val)
		{
			$sqlcustomer_booking_qty="SELECT sum(SD.convert_qty_two) as mt_booked FROM sauda_details SD,sauda_header SH 
									WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$customer_code."' 
									AND DATE_FORMAT('%Y-%m-%d',SUBSTRING(SD.sauda_no,-14,8))='".$date_diff_val."'";
			$rscustomer_booking_qty=mysqli_query($link,$sqlcustomer_booking_qty);
			$rowcustomer_booking_qty=mysqli_fetch_assoc($rscustomer_booking_qty);						
			${sauda_booked_qty.$customer_code}=${sauda_booked_qty.$customer_code}+$rowcustomer_booking_qty['mt_booked'];						
		}
	}
	${final_sauda_limit.$customer_code}=$sauda_limit_one-(${pending_contract.$customer_code} +${sauda_booked_qty.$customer_code});
	
	$sqlupdatesaudalimit="UPDATE customer_master SET sauda_limit='".${final_sauda_limit.$customer_code}."',download_time=CURRENT_TIMESTAMP()
						 WHERE customer_code='".$customer_code."'";
	mysqli_query($link,$sqlupdatesaudalimit);
}
?>