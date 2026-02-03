<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE 1 ORDER BY PM.prod_desc ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowproduct = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='
				<prod_code><![CDATA['.mb_convert_encoding($rowproduct['prod_code'], 'UTF-8', 'UTF-8').']]></prod_code>
				<product_group_code><![CDATA['.mb_convert_encoding($rowproduct['product_group_code'], 'UTF-8', 'UTF-8').']]></product_group_code>
				<product_sub_group_code><![CDATA['.mb_convert_encoding($rowproduct['product_sub_group_code'], 'UTF-8', 'UTF-8').']]></product_sub_group_code>
				<product_brand_code><![CDATA['.mb_convert_encoding($rowproduct['product_brand_code'], 'UTF-8', 'UTF-8').']]></product_brand_code>
				<prod_desc><![CDATA['.mb_convert_encoding($rowproduct['prod_desc'], 'UTF-8', 'UTF-8').']]></prod_desc>
				<acedns><![CDATA['.mb_convert_encoding($rowproduct['acedns'], 'UTF-8', 'UTF-8').']]></acedns>
				<black_list><![CDATA['.mb_convert_encoding($rowproduct['black_list'], 'UTF-8', 'UTF-8').']]></black_list>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo gzencode($contents, 9,FORCE_GZIP);		
?>
