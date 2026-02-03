<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$plant_name = $_REQUEST['plant_name'];
$product_group_code = $_REQUEST['product_group_code'];

$sqlproductgroup="SELECT formulation FROM product_group_master WHERE product_group_code='".$product_group_code."' ORDER BY product_group_name ASC";
$rsproductgroup=mysql_query($sqlproductgroup);
$rowproductgroup=mysql_fetch_array($rsproductgroup);
$formulation=$rowproductgroup['formulation'];

if($formulation=='yes')
{
	//$tabledata="<table align='left' width='40%'>";
	/*$sqlselectformulation="SELECT * FROM (SELECT prod_code,oils,formulation FROM loose_oilrate_formulation WHERE 
							product_group_code='".$product_group_code."' AND plant_name='".$plant_name."' ORDER BY datetime DESC) AS SAT GROUP BY 2 ";*/
	$sqlselectformulation="SELECT DISTINCT oils FROM loose_oilrate_formulation WHERE product_group_code='".$product_group_code."' AND plant_name='".$plant_name."' ";						
	$rsselectformulation=mysql_query($sqlselectformulation);
	while($rowselectformulation=mysql_fetch_array($rsselectformulation))
	{
		$oils=$rowselectformulation['oils'];
		$tabledata.='<tr><td align="left">'.$oils.'(MT):</td><td align="left"><input type="text" name="loose_rate[]"  id="loose_rate_"'.$oils.'" style="height:20px;" /><input type="hidden" name="oils_val[]" value="'.$oils.'" /></td></tr>';
	}
	$tabledata.='<input type="hidden" name="formulation" value="'.$formulation.'" />';
}
else
{
	//$tabledata="<table align='left' width='40%'>";
	$tabledata.='<tr><td align="left">Loose rate(MT):<input type="text" name="loose_rate" id="loose_rate" style="height:20px;"/><font color="#FF0000">*</font>&nbsp;<input type="hidden" name="formulation" value="'.$formulation.'" /></td></tr>';
}
echo $tabledata;
?>