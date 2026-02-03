<?php
set_time_limit(1000);
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$first_login=$_REQUEST['first_login'];


$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

$sqlmrp="SELECT COUNT(mrp_code) AS no_of_mrp FROM mrp WHERE 1";
$rsmrp=mysqli_query($link,$sqlmrp);
$rowmrp=mysqli_fetch_assoc($rsmrp);
$no_of_mrp=$rowmrp['no_of_mrp'];

if($countbranches>1)
{
	if($first_login=='yes')
	{
		if($no_of_mrp>0){
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
					FROM product_master PM,state_product_group_master SPGM,employee_master EM,mrp MRP
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
					AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
		}
		else
		{
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
					FROM product_master PM,state_product_group_master SPGM,employee_master EM
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
					AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
		}
	}
	if($first_login=='no')
	{
		if($no_of_mrp>0){
			$sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master_temp PM,state_product_group_master SPGM,employee_master EM,mrp MRP
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
						AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
		}
		else
		{
			 $sqlquery="SELECT DISTINCT PM.*,SPGM.branch_code
						FROM product_master_temp PM,state_product_group_master SPGM,employee_master EM
						WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
						AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
		}
	}
}
else
{
	if($first_login=='yes')
	{
		if($no_of_mrp>0){
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND 
							PM.acedns='Y' AND PM.black_list='N' ORDER BY PM.prod_desc ASC";
					/*$sqlquery="SELECT PM .* FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N' ORDER BY PM.prod_desc ASC";*/		
			}	
			else
			{
				$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.acedns='Y' AND PM.black_list='N' ORDER BY PM.prod_desc ASC";
			}	
	}
	if($first_login=='no')
	{
		if($no_of_mrp>0){
				$sqlquery="SELECT DISTINCT PM.* FROM product_master_temp PM,mrp MRP WHERE PM.prod_code=MRP.product_code AND 
						PM.acedns='Y' AND PM.black_list='N' ORDER BY PM.prod_desc ASC";
			}
			else
			{
				$sqlquery="SELECT DISTINCT PM.* FROM product_master_temp PM WHERE PM.acedns='Y' AND PM.black_list='N' ORDER BY PM.prod_desc ASC";
			}		
	}
}
			
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	/*$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";*/
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
				//echo $cnt++;*/
				
			$contents  = $rowproduct['prod_code'].",";
			$contents .= $rowproduct['product_group_code'].",";
			$contents .= $rowproduct['product_sub_group_code'].",";
			$contents .= $rowproduct['product_brand_code'].",";
			$contents .= $rowproduct['prod_desc'].",";
			$contents .= $rowproduct['acedns'].",";
			$contents .= $rowproduct['black_list'].",";
		
			$linecontents  .= $contents."\n";
		}
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	$datacontents = str_replace("\r","",$linecontents);
	
	header("Content-type: application/csv"); 
	header("Content-Disposition: attachment; filename=product_master.csv");
	print "$datacontents"; 		
?>
