<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	$type=$_REQUEST['type'];
	if($type=='transportmode'){
	$plant_name=$_REQUEST['plant_name'];
	
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">SELECT</option>';
    $sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' ORDER BY transport_mode ASC";
	$rstransportmode=mysqli_query($link,$sqltransportmode);
	while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
	{		
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
	}
	$content.='</select>';
	}
	if($type=="loadcapacity"){
		//$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		
		$content='<select name="truck_load_distribution" id="truck_load_distribution" onChange="javascript:previous_cost();">';
		$content.='<option value="">SELECT</option>';
		/*$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";*/
		$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";					
		$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);
		while($rowloadcapacity=mysqli_fetch_assoc($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['load_capacity']."'>".$rowloadcapacity['load_capacity']."</option>";
			//$capacity_string .= "'".$rowloadcapacity['load_capacity']."',";
		}
		//$capacity_string = rtrim($capacity_string,",");
		//$content.="<option value=\"".$capacity_string."\">All</option>";
		$content.='</select>';

	}
	if($type=="loadcapacitystate"){
		//$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		$state=$_REQUEST['state'];
		if($transport_mode!='')
		{
			$transport_mode_condition=" AND transport_mode='".$transport_mode."'";
		}
		else
		{
			$transport_mode_condition="";
		}
		$content='<select name="truck_load_distribution" id="truck_load_distribution" >';
		$content.='<option value="">SELECT</option>';
		/*$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";*/
		$sqlloadcapacity="SELECT DISTINCT loadability_ton FROM customer_master 
							WHERE  acedns='Y' AND 
							 state_code IN(".$state.")  AND loadability_ton >0 ".$transport_mode_condition." ORDER BY loadability_ton ASC";					
		$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);
		while($rowloadcapacity=mysqli_fetch_assoc($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['loadability_ton']."'>".$rowloadcapacity['loadability_ton']."</option>";
			$capacity_string .= $rowloadcapacity['loadability_ton'].",";
		}
		$capacity_string = rtrim($capacity_string,",");
		$content.="<option value=\"".$capacity_string."\">All</option>";
		$content.='</select>';

	}
	if($type=="loadcapacitystateroute"){
		//$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		$state=$_REQUEST['state'];
		$route=$_REQUEST['route'];
		if($transport_mode!='')
		{
			$transport_mode_condition=" AND transport_mode='".$transport_mode."'";
		}
		else
		{
			$transport_mode_condition="";
		}
		$content='<select name="truck_load_distribution" id="truck_load_distribution" >';
		$content.='<option value="">SELECT</option>';
		/*$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";*/
		$sqlloadcapacity="SELECT DISTINCT loadability_ton FROM customer_master 
							WHERE  acedns='Y' AND cust_type='D' AND 
							 state_code IN(".$state.") AND route_code IN(".$route.")  AND loadability_ton >0 ".$transport_mode_condition." 
							 ORDER BY loadability_ton ASC";					
		$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);
		while($rowloadcapacity=mysqli_fetch_assoc($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['loadability_ton']."'>".$rowloadcapacity['loadability_ton']."</option>";
			$capacity_string .= $rowloadcapacity['loadability_ton'].",";
		}
		$capacity_string = rtrim($capacity_string,",");
		$content.="<option value=\"".$capacity_string."\">All</option>";
		$content.='</select>';
	}
	if($type=="loadcapacityfreight"){
		$plant_name=$_REQUEST['plant_name'];
		$transport_mode=$_REQUEST['transport_mode'];
		
		$content='<select name="truck_load" id="truck_load" onChange="javascript:previous_truckload_hirecost();">';
		$content.='<option value="">SELECT</option>';
		$sqlloadcapacity="SELECT DISTINCT load_capacity FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' 
							AND transport_mode='".$transport_mode."' ORDER BY load_capacity ASC";
		$rsloadcapacity=mysqli_query($link,$sqlloadcapacity);
		while($rowloadcapacity=mysqli_fetch_assoc($rsloadcapacity))
		{		
			$content.="<option value='".$rowloadcapacity['load_capacity']."'>".$rowloadcapacity['load_capacity']."</option>";
		}
		$content.='</select>';
	}
	if($type=='honeycombcost'){
		$plant_name=$_REQUEST['plant_name'];
		
		$content='<select name="transport_mode" id="transport_mode" onChange="javascript:previous_honey_comb_cost();">';
		$content.='<option value="">SELECT</option>';
		$sqltransportmode="SELECT DISTINCT transport_mode FROM plantwise_load_capacity WHERE plant_name='".$plant_name."' ORDER BY transport_mode ASC";
		$rstransportmode=mysqli_query($link,$sqltransportmode);
		while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
		{		
			$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
		}
		$content.='</select>';
	}
	echo $content;
	mysqli_close($link);
?>