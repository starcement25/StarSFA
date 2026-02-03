<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);

if($countbranches>1 && $emp_code!='C0007')
{
$sqlquery="SELECT DISTINCT MRP.* FROM mrp MRP,employee_master EM WHERE EM.branch_code=MRP.branch_code AND EM.emp_code='".$emp_code."'";
}
else
{
	$sqlquery="SELECT * FROM mrp";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowprice = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='
				<product_code><![CDATA['.mb_convert_encoding($rowprice['product_code'], 'UTF-8', 'UTF-8').']]></product_code>
				<mrp_code><![CDATA['.mb_convert_encoding($rowprice['mrp_code'], 'UTF-8', 'UTF-8').']]></mrp_code>
				<mrp><![CDATA['.mb_convert_encoding($rowprice['mrp'], 'UTF-8', 'UTF-8').']]></mrp>
				<sale_rate><![CDATA['.mb_convert_encoding($rowprice['sale_rate'], 'UTF-8', 'UTF-8').']]></sale_rate>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
?>
