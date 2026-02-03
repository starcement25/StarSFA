<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_group_code=$_REQUEST['prod_group_code'];
	$content='<select name="product_sub_group_code" id="product_sub_group_code" onChange="javascript:select_product();">';
	$content.='<option value="">SELECT</option>';
    $sqlprodsubgroupdesc="SELECT product_sub_group_code,product_sub_group_name FROM product_sub_group_master WHERE product_group_code='".$prod_group_code."' 
					 ORDER BY product_sub_group_name ASC";
	$rsprodsubgroupdesc=mysql_query($sqlprodsubgroupdesc);
	while($rowprodsubgroupdesc=mysql_fetch_array($rsprodsubgroupdesc))
	{
		$content.="<option value='".$rowprodsubgroupdesc['product_sub_group_code']."'>".$rowprodsubgroupdesc['product_sub_group_name']."</option>";
	}
	 $content.='</select>';

	echo $content;
	mysql_close($link);
?>