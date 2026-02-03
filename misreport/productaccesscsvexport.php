<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$excelheader = "DNSProductCode"."\t"."ProductDesc"."\t"."BrandName"."\t"."BrandFormName"."\t"."BrandSubFormName"."\t"."Acedns"."\t"."Blacklist"."\t"."UOM1"."\t"."UOM2"."\n";

$sql_select_product = "SELECT prod_code, dns_prod_code, product_group_code, product_sub_group_code, product_brand_code, prod_desc, acedns, black_list, UOM1, UOM2 FROM product_master";
$res_select_product = mysqli_query($link,$sql_select_product);
while($row_select_product = mysqli_fetch_assoc($res_select_product))
{
	$prod_code = $row_select_product['prod_code'];
	$dns_prod_code = $row_select_product['dns_prod_code'];
	$product_group_code = $row_select_product['product_group_code'];
	$product_sub_group_code = $row_select_product['product_sub_group_code'];
	$product_brand_code = $row_select_product['product_brand_code'];
	$prod_desc = $row_select_product['prod_desc'];
	$acedns = $row_select_product['acedns'];
	$black_list = $row_select_product['black_list'];
	$UOM1 = $row_select_product['UOM1'];
	$UOM2 = $row_select_product['UOM2'];
	
	$sql_productgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '$product_group_code'";
	$res_productgroup_name = mysqli_query($link,$sql_productgroup_name);
	$row_productgroup_name = mysqli_fetch_assoc($res_productgroup_name);
	$product_group_name = $row_productgroup_name['product_group_name'];
	
	$sql_product_subgroup_name = "SELECT product_sub_group_name FROM product_sub_group_master WHERE product_sub_group_code = '$product_sub_group_code'";
	$res_product_subgroup_name = mysqli_query($link,$sql_product_subgroup_name);
	$row_product_subgroup_name = mysqli_fetch_assoc($res_product_subgroup_name);
	$product_subgroup_name = $row_product_subgroup_name['product_sub_group_name'];
	
	$sql_brand_name = "SELECT product_brand_name FROM product_brand_master WHERE product_brand_code = '$product_brand_code'";
	$res_brand_name = mysqli_query($link,$sql_brand_name);
	$row_brand_name = mysqli_fetch_assoc($res_brand_name);
	$brand_name = $row_brand_name['product_brand_name'];
	
	$excelcontents .= $dns_prod_code."\t".$prod_desc."\t".$product_group_name."\t".$product_subgroup_name."\t".$brand_name."\t".$acedns."\t".$black_list."\t".$UOM1."\t".$UOM2."\n";
	
}header("Content-type: application/x-msdownload"); 

    header("Content-Disposition: attachment; filename=sku master.xls"); 

    header("Pragma: no-cache"); 

    header("Expires: 0"); 

    //print "$header\n$data";
    echo $excelheader;
	echo $excelcontents;
    
	
	mysqli_close($link);
?>