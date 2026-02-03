<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_RUPA");
date_default_timezone_set("Asia/Kolkata");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);

$sqlselorderdetails="SELECT order_no,sku_code,qty FROM `order_details` WHERE SUBSTRING(order_no,-14,6)='201704'";
$rsselorderdetails=mysqli_query($link,$sqlselorderdetails);
while($rowselorderdetails=mysqli_fetch_assoc($rsselorderdetails))
{
	$emp_code=substr($rowselorderdetails['order_no'],1,5);
	$sku_code=$rowselorderdetails['sku_code'];
	$qty=$rowselorderdetails['qty'];
	$transdate=date('Y-m-d',strtotime(substr($rowselorderdetails['order_no'],-14,8)));
	$transmonth=substr($rowselorderdetails['order_no'],-10,2);
	
	if($transmonth=='01') {
		$sql_update_clause=" jan_31_achievement=(jan_31_achievement+".$qty.")";
		$sql_in_clause=" jan_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='02'){ 
		$sql_update_clause=" feb_28_achievement=(feb_28_achievement+".$qty.")";
		$sql_in_clause=" feb_28_achievement='".$qty."'"; 
	}
	else if($transmonth=='03'){ 
		$sql_update_clause=" mar_31_achievement=(mar_31_achievement+".$qty.")";
		$sql_in_clause=" mar_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='04'){ 
		$sql_update_clause=" apr_30_achievement=(apr_30_achievement+".$qty.")";
		$sql_in_clause=" apr_30_achievement='".$qty."'"; 
	}
	else if($transmonth=='05'){ 
		$sql_update_clause=" may_31_achievement=(may_31_achievement+".$qty.")";
		$sql_in_clause=" may_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='06'){ 
		$sql_update_clause=" jun_30_achievement=(jun_30_achievement+".$qty.")";
		$sql_in_clause=" jun_30_achievement='".$qty."'"; 
	}
	else if($transmonth=='07'){ 
		$sql_update_clause=" jul_31_achievement=(jul_31_achievement+".$qty.")";
		$sql_in_clause=" jul_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='08'){ 
		$sql_update_clause=" aug_31_achievement=(aug_31_achievement+".$qty.")";
		$sql_in_clause=" aug_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='09'){ 
		$sql_update_clause=" sep_30_achievement=(sep_30_achievement+".$qty.")";
		$sql_in_clause=" sep_30_achievement='".$qty."'"; 
	}
	else if($transmonth=='10'){ 
		$sql_update_clause=" oct_31_achievement=(oct_31_achievement+".$qty.")";
		$sql_in_clause=" oct_31_achievement='".$qty."'"; 
	}
	else if($transmonth=='11'){ 
		$sql_update_clause=" nov_30_achievement=(nov_30_achievement+".$qty.")";
		$sql_in_clause=" nov_30_achievement='".$qty."'"; 
	}
	else if($transmonth=='12'){ 
		$sql_update_clause=" dec_31_achievement=(dec_31_achievement+".$qty.")";
		$sql_in_clause=" dec_31_achievement='".$qty."'"; 
	}
	
	$sqlchkempproductgroup="SELECT emp_code,product_group_code FROM self_appraisal_productgroup_wise 
									WHERE emp_code='".$emp_code."'";
	$rschkempproductgroup=mysqli_query($link,$sqlchkempproductgroup);
	$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);
	if($countchkempproductgroup==0){
		$sqlselfappraisal  = "insert into self_appraisal_productgroup_wise SET ";
		$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";
		$sqlselfappraisal .= " , product_group_code=''";
		$sqlselfappraisal .= " , ".$sql_in_clause."";
		$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";
		mysqli_query($link,$sqlselfappraisal);
	 }
	else
	{
		$sqlselfappraisalupdate  = "update self_appraisal_productgroup_wise SET ";
		$sqlselfappraisalupdate .= "  ".$sql_update_clause."";
		$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
		mysqli_query($link,$sqlselfappraisalupdate);
	}
}
?>