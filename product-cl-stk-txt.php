<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

$sqlbranches="SELECT * FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

$sqlmrp="SELECT COUNT(mrp_code) AS no_of_mrp FROM mrp WHERE 1";
$rsmrp=mysqli_query($link,$sqlmrp);
$rowmrp=mysqli_fetch_assoc($rsmrp);
$no_of_mrp=$rowmrp['no_of_mrp'];

if($countbranches>1)
{
	if($no_of_mrp>0){
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk
					FROM product_master PM,state_product_group_master SPGM,employee_master EM,mrp MRP
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
					AND SPGM.prod_code = PM.prod_code AND PM.prod_code=MRP.product_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
	}
	else
	{
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk
					FROM product_master PM,state_product_group_master SPGM,employee_master EM
					WHERE EM.branch_code = SPGM.branch_code AND PM.acedns='Y' AND PM.black_list='N'
					AND SPGM.prod_code = PM.prod_code AND EM.emp_code ='".$emp_code."' ORDER BY PM.prod_desc ASC";
	}
}
else
{
	if($no_of_mrp>0){
		$sqlquery="select DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk from product_master PM,mrp MRP WHERE PM.acedns='Y' AND PM.black_list='N' 
					AND PM.prod_code=MRP.product_code ORDER BY PM.prod_desc ASC";
	}
	else
	{
		$sqlquery="select DISTINCT PM.prod_code,PM.prod_desc,PM.cl_stk from product_master PM WHERE PM.acedns='Y' AND PM.black_list='N' 
					 ORDER BY PM.prod_desc ASC";
	}
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowproduct = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<prod_code><![CDATA['.mb_convert_encoding($rowproduct['prod_code'], 'UTF-8', 'UTF-8').']]></prod_code>
						<prod_desc><![CDATA['.mb_convert_encoding($rowproduct['prod_desc'], 'UTF-8', 'UTF-8').']]></prod_desc>
						<cl_stk><![CDATA['.mb_convert_encoding($rowproduct['cl_stk'], 'UTF-8', 'UTF-8').']]></cl_stk>';
			$contents.="</data>";*/
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['cl_stk']!='')?$rowproduct['cl_stk']: ' ');
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
	header("Content-Disposition: attachment; filename=product_cl_stk.txt");
	print "$datacontents";		
	
?>
