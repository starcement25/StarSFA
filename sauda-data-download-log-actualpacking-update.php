<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
	//$from_date=date('2017-09-11');
	$from_date='2017-09-01';
	$to_date=date('Y-m-d');
	if($from_date!='' && $to_date!='')
	{
		 $date_condition=" AND DATE_FORMAT(SUBSTRING(sauda_no,-14,18),'%Y-%m-%d') >='".$from_date."' AND
					  	DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d') <='".$to_date."'";
	}
	$sql_sauda_header_download = "SELECT branch_code,PR00,prod_code,sl_no FROM sauda_download_log WHERE 1 ".$date_condition."
									ORDER BY DATE_FORMAT(SUBSTRING(sauda_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	$rs_sauda_header_download = mysqli_query($link,$sql_sauda_header_download) or die(mysqli_error()." Error in sauda data download: ".$sql_sauda_header_download);
	while($rec_sauda_header_download = mysqli_fetch_assoc($rs_sauda_header_download))
	{
		$branch_code = $rec_sauda_header_download['branch_code'];
		$dns_prod_code = $rec_sauda_header_download['prod_code'];
		$sl_code = $rec_sauda_header_download['sl_no'];
		$sqlplant="SELECT plant_name FROM branch_master WHERE dns_branch_code='".$branch_code."'";
		$rsplant=mysqli_query($link,$sqlplant);
		$rowplant=mysqli_fetch_assoc($rsplant);
		$plant_name=$rowplant['plant_name'];
		
		$sqlconversion="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE dns_prod_code='".$dns_prod_code."'";
		$rsconversion=mysqli_query($link,$sqlconversion);
		$rowconversion=mysqli_fetch_assoc($rsconversion);
		$conversion_one = $rowconversion['conversion_factor'];
		$conversion_two = $rowconversion['conversion_factor_two'];
		
		$sqlpackingprodwise="SELECT packing_realization FROM packing_master WHERE dns_prod_code='".$dns_prod_code."'
							AND plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
		$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);

		echo $PR00=$rec_sauda_header_download['PR00'];
		echo $packing_realization=$rowpackingprodwise['packing_realization'];
		if($packing_realization=='')   $packing_realization=0;
		
		$realization_per_case=$PR00-$packing_realization;
		$realization_per_case=round($realization_per_case,2);
		$realization_per_MT=round((($realization_per_case*$conversion_two)/$conversion_one),2);
		
		echo $sqlupdatesaudadownloadlog="Update sauda_download_log SET realization_per_case='".$realization_per_case."',
									realization_per_MT='".$realization_per_MT."',
									packing_realization='".$packing_realization."',download_time=CURRENT_TIMESTAMP() WHERE 	sl_no='".$sl_code."'";
		mysqli_query($link,$sqlupdatesaudadownloadlog);
		
		//exit();
	}
?>
