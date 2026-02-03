<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$branch_code=$_REQUEST['branch_code'];
	$plant_name=$_REQUEST['plant_name'];
	$transport_mode=$_REQUEST['transport_mode'];
	$truck_load=$_REQUEST['truck_load'];
	$selectprevioustruckload="SELECT truck_load,hire_cost FROM basic_freight WHERE 
	branch_code='".$branch_code."' AND  plant_name='".$plant_name."' 
	AND transport_mode='".$transport_mode."' AND truck_load='".$truck_load."'  ORDER BY datetime DESC LIMIT 0,1";
	$rsprevioustruckload=mysqli_query($link,$selectprevioustruckload);
	$countprevioustruckload=mysqli_num_rows($rsprevioustruckload);
	if($countprevioustruckload >0)
	{
		$rowprevioustruckload=mysqli_fetch_assoc($rsprevioustruckload);
		$previos_truck_load=$rowprevioustruckload['truck_load'];
		$previos_hire_cost=$rowprevioustruckload['hire_cost'];
		if($previos_truck_load >0)  $truck_load=$previos_truck_load;
		else						$truck_load=0;
		if($previos_hire_cost >0)  $hire_cost=$previos_hire_cost;
		else						$hire_cost=0;
	}
	else
	{
		$truck_load=0;
		$hire_cost=0;
	}

echo $truck_load.'#'.$hire_cost;
mysqli_close($link);
?>