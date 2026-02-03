<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_code=$_REQUEST['prod_code'];
	$branch_code=$_REQUEST['branch_code'];
	$selectpreviousdepotcost="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$prod_code."' AND branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rspreviousdepotcost=mysql_query($selectpreviousdepotcost);
	$countpreviousdepotcost=mysql_num_rows($rspreviousdepotcost);
	if($countpreviousdepotcost >0)
	{
		$rowpreviousdepotcost=mysql_fetch_array($rspreviousdepotcost);
		$previous_depotcost=$rowpreviousdepotcost['depot_cost'];
		if($previous_depotcost >0)  $depot_cost=$previous_depotcost;
		else						$depot_cost=0;
	}
	else
	{
		$depot_cost=0;
	}

echo $depot_cost;
mysql_close($link);
?>