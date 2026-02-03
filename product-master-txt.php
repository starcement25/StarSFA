<?php
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$first_login=$_REQUEST['first_login'];
$is_master=$_REQUEST['is_master'];

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$condition_one=" AND PM.vertical_value='".$emp_vertical_value."'";
}
else
{
	$condition_one="";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

$sqlmrp="SELECT COUNT(mrp_code) AS no_of_mrp FROM mrp WHERE 1";
$rsmrp=mysqli_query($link,$sqlmrp);
$rowmrp=mysqli_fetch_assoc($rsmrp);
$no_of_mrp=$rowmrp['no_of_mrp'];

if($countbranches>1)
{
	if(($first_login=='yes') ||($first_login=='no' && $is_master=='Y'))
	{
		if($no_of_mrp>0){
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
					FROM product_master PM,state_product_group_master SPGM,employee_master EM,mrp MRP
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
					AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' 
					".$condition_one." ORDER BY PM.prod_desc ASC";
		}
		else
		{
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
					FROM product_master PM,state_product_group_master SPGM,employee_master EM
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
					AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ORDER BY PM.prod_desc ASC";
		}
	}
	if(($first_login=='no') ||($first_login=='no' && $is_master=='N'))
	{
		if($no_of_mrp>0){
			/*$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master_temp PM,state_product_group_master SPGM,employee_master EM,mrp MRP
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
						AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ORDER BY PM.prod_desc ASC";*/
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master PM,state_product_group_master SPGM,employee_master EM,mrp MRP
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
						AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ORDER BY PM.prod_desc ASC";			
		}
		else
		{
			 /*$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master_temp PM,state_product_group_master SPGM,employee_master EM
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
						AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ORDER BY PM.prod_desc ASC";*/
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master PM,state_product_group_master SPGM,employee_master EM
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc <>'' 
						AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ".$condition_one." ORDER BY PM.prod_desc ASC";
		}
	}
}
else
{
	if(($first_login=='yes') ||($first_login=='no' && $is_master=='Y'))
	{
		if($no_of_mrp>0){
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND PM.prod_desc <>'' AND  
							PM.acedns='Y' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";
					/*$sqlquery="SELECT PM .* FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' ORDER BY PM.prod_desc ASC";*/		
			}	
			else
			{
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.acedns='Y' AND PM.prod_desc <>'' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";
			}	
	}
	if(($first_login=='no') ||($first_login=='no' && $is_master=='N'))
	{
		if($no_of_mrp>0){
				/*$sqlquery="SELECT DISTINCT PM.* FROM product_master_temp PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND PM.prod_desc <>'' AND 
						PM.acedns='Y' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";*/
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND PM.prod_desc <>'' AND 
						PM.acedns='Y' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";
		
			}
			else
			{
				/*$sqlquery="SELECT DISTINCT PM.* FROM product_master_temp PM WHERE PM.acedns='Y' AND PM.prod_desc <>'' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";*/
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.acedns='Y' AND PM.prod_desc <>'' AND PM.black_list='N' ".$condition_one." ORDER BY PM.prod_desc ASC";
			}		
	}
}
			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	/*$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";*/
	$contentsrowcolumn  =$count.'¥'.'7';
	if($count>0){
		while($rowproduct = mysqli_fetch_assoc($result))
		{
				/*$contents.="<data>";
				$contents .='
				<prod_code><![CDATA['.mb_convert_encoding($rowproduct['prod_code'], 'UTF-8', 'UTF-8').']]></prod_code>
				<product_group_code><![CDATA['.mb_convert_encoding($rowproduct['product_group_code'], 'UTF-8', 'UTF-8').']]></product_group_code>
				<product_sub_group_code><![CDATA['.mb_convert_encoding($rowproduct['product_sub_group_code'], 'UTF-8', 'UTF-8').']]></product_sub_group_code>
				<product_brand_code><![CDATA['.mb_convert_encoding($rowproduct['product_brand_code'], 'UTF-8', 'UTF-8').']]></product_brand_code>
				<prod_desc><![CDATA['.mb_convert_encoding($rowproduct['prod_desc'], 'UTF-8', 'UTF-8').']]></prod_desc>
				<acedns><![CDATA['.mb_convert_encoding($rowproduct['acedns'], 'UTF-8', 'UTF-8').']]></acedns>
				<black_list><![CDATA['.mb_convert_encoding($rowproduct['black_list'], 'UTF-8', 'UTF-8').']]></black_list>';
				$contents.="</data>";
				//echo $cnt++;
				
			$contents  = $rowproduct['prod_code'].",";
			$contents .= $rowproduct['product_group_code'].",";
			$contents .= $rowproduct['product_sub_group_code'].",";
			$contents .= $rowproduct['product_brand_code'].",";
			$contents .= $rowproduct['prod_desc'].",";
			$contents .= $rowproduct['acedns'].",";
			$contents .= $rowproduct['black_list'].",";*/
			
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_code']!='')?$rowproduct['product_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_code']!='')?$rowproduct['product_sub_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_code']!='')?$rowproduct['product_brand_code']: ' ')."^";
			$contents  .= (($rowproduct['prod_desc']!='')?$rowproduct['prod_desc']: ' ')."^";
			$contents  .= (($rowproduct['acedns']!='')?$rowproduct['acedns']: ' ')."^";
			$contents  .= (($rowproduct['black_list']!='')?$rowproduct['black_list']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=product_master.txt");
	print "$datacontents";	
?>
