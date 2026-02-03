<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_code=$_REQUEST['prod_code'];
	//$plant_name=$_REQUEST['plant_name'];
	$transport_mode=$_REQUEST['transport_mode'];
	$truck_load_distribution=$_REQUEST['truck_load_distribution'];
	
	$selectprevioustruckloadqty="SELECT qty_truck_load,truck_load FROM load_distribution WHERE 
									prod_code='".$prod_code."' AND transport_mode='".$transport_mode."' AND truck_load='".$truck_load_distribution."' ORDER BY datetime DESC LIMIT 0,1";
	$rsprevioustruckloadqty=mysql_query($selectprevioustruckloadqty);
	$countprevioustruckloadqty=mysql_num_rows($rsprevioustruckloadqty);
	if($countprevioustruckloadqty >0)
	{
		$rowprevioustruckloadqty=mysql_fetch_array($rsprevioustruckloadqty);
		$previous_qty_truck_load=$rowprevioustruckloadqty['qty_truck_load'];
		$previos_truck_load=$rowprevioustruckloadqty['truck_load'];
		//$previos_packing_cost=$rowprevioustruckloadqty['packing_cost'];
		if($previous_qty_truck_load >0)  $qty_truck_load=$previous_qty_truck_load;
		else						     $qty_truck_load=0;
		if($previos_truck_load >0)  $truck_load=$previos_truck_load;
		else						$truck_load=0;
		/*if($previos_packing_cost >0)  $packing_cost=$previos_packing_cost;
		else						  $packing_cost=0;*/
	}
	else
	{
		$qty_truck_load=0;
		$truck_load=0;
		//$packing_cost=0;
	}
	/*$selectpreviouspacking="SELECT packing_cost FROM packing_master WHERE dns_prod_code='".$prod_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rspreviouspacking=mysql_query($selectpreviouspacking);
	$countpreviouspacking=mysql_num_rows($rspreviouspacking);
	if($countpreviouspacking >0)
	{
		$rowpreviouspacking=mysql_fetch_array($rspreviouspacking);
		$previous_packing=$rowpreviouspacking['packing_cost'];
		if($previous_packing >0)  $packing_cost=$previous_packing;
		else					  $packing_cost=0;
	}
	else
	{
		$packing_cost=0;
	}*/

echo $qty_truck_load.'#'.$truck_load;
mysql_close($link);
?>