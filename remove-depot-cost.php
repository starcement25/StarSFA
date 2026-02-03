<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");


$sqlselectdistinctbranch="SELECT branch_code,prod_code,dns_prod_code FROM product_master WHERE branch_code IN ('B0003','B0007')";
$rsselectdistinctbranch=mysqli_query($link,$sqlselectdistinctbranch);
while($rowselectdistinctbranch=mysqli_fetch_assoc($rsselectdistinctbranch))
{
	$branch_code=$rowselectdistinctbranch['branch_code'];
	$prod_code=$rowselectdistinctbranch['prod_code'];
	$dns_prod_code=$rowselectdistinctbranch['dns_prod_code'];
	
	$sqldepot="SELECT depot_cost FROM depot_cost WHERE branch_code='".$branch_code."' AND dns_prod_code='".$dns_prod_code."' 
			ORDER BY datetime DESC LIMIT 0,1";
	$rsdepot=mysqli_query($link,$sqldepot);
	$rowdepot=mysqli_fetch_assoc($rsdepot);
	$depot_cost=$rowdepot['depot_cost'];
	if($depot_cost=='') $depot_cost=0;
	
  echo  $sqlupdatemrpprodwise="UPDATE sauda_mrp SET sale_rate=(sale_rate-$depot_cost),download_time=CURRENT_TIMESTAMP() 
							WHERE branch_code='".$branch_code."' AND product_code='".$prod_code."'";
	mysqli_query($link,$sqlupdatemrpprodwise);
}