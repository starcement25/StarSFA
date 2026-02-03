<?php
include "asl_connection.php";
$product_master = "product_master";
$product_unit_coversion_matrix = "product_unit_coversion_matrix";

$sqlall = "select * from $product_unit_coversion_matrix where `mapped_prod_code`!='' order by `mapped_prod_code` asc";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$the_mapped_prod_code = $row11["mapped_prod_code"] ? addslashes(trim($row11["mapped_prod_code"])) : "";
		if($the_mapped_prod_code!=""){
			$sqlall2 = "select `prod_desc` from $product_master where `dns_prod_code`='$the_mapped_prod_code'";
			$resall2 = mysqli_query($link,$sqlall2);
			$totall2 = mysqli_num_rows($resall2);
			if($totall2>0){
				$row112=mysqli_fetch_assoc($resall2);
				$the_prod_desc = $row112["prod_desc"] ? addslashes(trim($row112["prod_desc"])) : "";
				$sql2 = "update $product_unit_coversion_matrix set `mapped_prod_desc`='$the_prod_desc' where `mapped_prod_code`='$the_mapped_prod_code'";
				$res2 = mysqli_query($link,$sql2);
			}
		}
	
	}	
}
echo "done";
mysqli_close();
?>