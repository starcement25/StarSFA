<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100002157';

$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1)
{
$sqlquery="SELECT DISTINCT PBM.* FROM product_brand_master PBM,state_product_group_master SPGM,employee_master EM,product_master PM WHERE 
			PM.product_brand_code=PBM.product_brand_code AND PM.acedns='Y' AND PM.black_list='N' AND 
			EM.branch_code=SPGM.branch_code AND SPGM.product_brand_code=PBM.product_brand_code AND EM.emp_code='".$emp_code."'";
}
else
{
	$sqlquery="SELECT DISTINCT PBM.* FROM product_brand_master PBM,product_master PM  WHERE 
			PM.product_brand_code=PBM.product_brand_code AND PM.acedns='Y' AND PM.black_list='N' ORDER BY PBM.product_brand_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowproductbrand = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='
				<product_brand_code><![CDATA['.mb_convert_encoding($rowproductbrand['product_brand_code'], 'UTF-8', 'UTF-8').']]></product_brand_code>
				<product_sub_group_code><![CDATA['.mb_convert_encoding($rowproductbrand['product_sub_group_code'], 'UTF-8', 'UTF-8').']]></product_sub_group_code>
				<product_brand_name><![CDATA['.mb_convert_encoding($rowproductbrand['product_brand_name'], 'UTF-8', 'UTF-8').']]></product_brand_name>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
