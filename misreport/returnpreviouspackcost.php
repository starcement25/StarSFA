<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$plant_name=$_REQUEST['plant_name'];
	$product_group_code=$_REQUEST['product_group_code'];
	$selectpreviouspackcost="SELECT packing_cost FROM pricing_detials WHERE plant_name='".$plant_name."' AND 
							product_group_code='".$product_group_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rspreviouspackcost=mysqli_query($link,$selectpreviouspackcost);
	$countpreviouspackcost=mysqli_num_rows($rspreviouspackcost);
	if($countpreviouspackcost >0)
	{
		$rowpreviouspackcost=mysqli_fetch_assoc($rspreviouspackcost);
		$previos_packing_cost=$rowpreviouspackcost['packing_cost'];
		if($previos_packing_cost >0)  $packing_cost=$previos_packing_cost;
		else						  $packing_cost=0;
	}
	else
	{
		$packing_cost=0;
	}

echo $packing_cost;
mysqli_close($link);
?>