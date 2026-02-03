<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

/*$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_code ASC";
$rsbranch=mysqli_query($link,$sqlbranch);
while($rowbranch=mysqli_fetch_assoc($rsbranch))
{
	$branch_code_cl_stk=$rowbranch['branch_code'];*/
	$sqlprod="SELECT * FROM `customer_master` WHERE `customer_code` NOT IN(SELECT customer_code FROM customer_branch_relation)";
	$rsprod=mysqli_query($link,$sqlprod);
	while($rowprod=mysqli_fetch_assoc($rsprod))
	{
		$customer_code=$rowprod['customer_code'];
		$branch_code_name='BC35,BC27,BC18';
		$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";
		$rsbranchcode=mysqli_query($link,$sqlbranchcode);
		while($rowbranchcode=mysqli_fetch_assoc($rsbranchcode))
		{
			$branch_code=$rowbranchcode['branch_code'];
			$sqlcustomerbranch="SELECT customer_code FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";
				$rscustomerbranch=mysqli_query($link,$sqlcustomerbranch);
				$countcustomerbranch=mysqli_num_rows($rscustomerbranch);
				if($countcustomerbranch <1)
				{
					$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";
					$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";
					$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";
					mysqli_query($link,$sqlinsertcustomerbranch);
				}
			}

	}
//}
//echo "Successfull";
?>
