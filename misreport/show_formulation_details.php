<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$plant_name = $_REQUEST['plant_name'];

	//$tabledata="<table align='left' width='40%'>";
	/*$sqlselectformulation="SELECT * FROM (SELECT prod_code,oils,formulation FROM loose_oilrate_formulation WHERE 
							product_group_code='".$product_group_code."' AND plant_name='".$plant_name."' ORDER BY datetime DESC) AS SAT GROUP BY 2 ";*/
	$sqlselectformulation="SELECT DISTINCT oils FROM loose_oilrate_formulation WHERE plant_name='".$plant_name."' AND base_oil='Y' AND prod_code 
						IN(SELECT DISTINCT dns_prod_code FROM product_master WHERE acedns='Y')";						
	$rsselectformulation=mysqli_query($link,$sqlselectformulation);
	while($rowselectformulation=mysqli_fetch_assoc($rsselectformulation))
	{
		$oils=$rowselectformulation['oils'];
		$tabledata.='<tr><td align="left">'.$oils.'(MT):</td><td align="left"><input type="text" name="loose_rate[]"  id="loose_rate_"'.$oils.'" style="height:20px;" /><input type="hidden" name="oils_val[]" value="'.$oils.'" /></td></tr>';
	}
echo $tabledata;
?>