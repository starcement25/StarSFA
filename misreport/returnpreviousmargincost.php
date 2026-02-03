<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_code=$_REQUEST['prod_code'];
	$branch_code=$_REQUEST['branch_code'];
	$selectpreviousmargincost="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$prod_code."' AND branch_code='".$branch_code."' ORDER BY datetime DESC LIMIT 0,1";
	$rspreviousmargincost=mysqli_query($link,$selectpreviousmargincost);
	$countpreviousmargincost=mysqli_num_rows($rspreviousmargincost);
	if($countpreviousmargincost >0)
	{
		$rowpreviousmargincost=mysqli_fetch_assoc($rspreviousmargincost);
		$previous_margincost=$rowpreviousmargincost['margin_cost'];
		if($previous_margincost >0)  $margin_cost=$previous_margincost;
		else						$margin_cost=0;
	}
	else
	{
		$margin_cost=0;
	}

echo $margin_cost;
mysqli_close($link);
?>