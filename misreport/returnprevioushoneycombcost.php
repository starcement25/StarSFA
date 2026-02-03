<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_code=$_REQUEST['prod_code'];
	$plant_name=$_REQUEST['plant_name'];
	$transport_mode=$_REQUEST['transport_mode'];
	//$customer_state=$_REQUEST['customer_state'];
	$branch_code=$_REQUEST['branch_code'];
	
	$sqlconversionfactor="SELECT conversion_factor,conversion_factor_two FROM product_master WHERE 
									dns_prod_code='".$prod_code."'";
	$rsconversionfactor=mysqli_query($link,$sqlconversionfactor);
	$rowconversionfactor=mysqli_fetch_assoc($rsconversionfactor);

	$conversion_factor=$rowconversionfactor['conversion_factor'];
	$conversion_factor_two=$rowconversionfactor['conversion_factor_two'];
	
    $selectprevioushoneycomb="SELECT honeycomb_cost FROM honeycomb_cost WHERE 
							prod_code='".$prod_code."' AND plant_name='".$plant_name."' AND 	
							transport_mode='".$transport_mode."' AND branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rsprevioushoneycomb=mysqli_query($link,$selectprevioushoneycomb);
	$countprevioushoneycomb=mysqli_num_rows($rsprevioushoneycomb);
	if($countprevioushoneycomb >0)
	{
		$rowpreviousoneycomb=mysqli_fetch_assoc($rsprevioushoneycomb);
		$previous_honeycomb_cost=$rowpreviousoneycomb['honeycomb_cost'];
		$previous_honeycomb_cost=$previous_honeycomb_cost*$conversion_factor_two;
		$previous_honeycomb_cost=round(($previous_honeycomb_cost/$conversion_factor),2);
		if($previous_honeycomb_cost >0)  $honeycomb_cost=$previous_honeycomb_cost;
		else						     $honeycomb_cost=0;
	
	}
	else
	{
		$honeycomb_cost=0;
	}

echo $honeycomb_cost;
mysqli_close($link);
?>