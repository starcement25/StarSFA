<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_group_code=$_REQUEST['prod_group_code'];
	//$product_sub_group_code=$_REQUEST['product_sub_group_code'];
    /*$sqlproddesc="SELECT prod_code,prod_desc FROM product_master WHERE product_group_code='".$prod_group_code."' AND 
					product_sub_group_code='".$product_sub_group_code."' AND acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";*/
	 $sqlproddesc="SELECT prod_code,prod_desc FROM product_master WHERE product_group_code='".$prod_group_code."' AND acedns='Y' 
	 				AND black_list='N' ORDER BY prod_desc ASC";				
	$rsproddesc=mysql_query($sqlproddesc);
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		$content.="<tr><td>".$rowproddesc['prod_desc']."</td><td><input type=\"hidden\" name=\"prod_code[]\" value=\"$rowproddesc[prod_code]\"><input type=\"text\" name=\"allocated_qty[]\" size=\"8\" value=\"\"></td></tr>";
	}

	echo $content;
	mysql_close($link);
?>