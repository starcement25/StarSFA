<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	$prod_group_code=$_REQUEST['prod_group_code'];
	$prod_group_code_array=explode(',',$prod_group_code);
	$prod_group_code="'".implode("','", $prod_group_code_array)."'";
	$prodtype=$_REQUEST['prodtype'];
	$prodtype_array=explode(',',$prodtype);
	$prodtype="'".implode("','", $prodtype_array)."'";
	$prodbranch=$_REQUEST['prodbranch'];
	$prodbranch_array=explode(',',$prodbranch);
	$prodbranch="'".implode("','", $prodbranch_array)."'";

	/*$content='<select name="prod_code" id="prod_code" onChange="javascript:previous_cost();">';
	$content.='<option value="">SELECT</option>';
    $sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE product_group_code='".$prod_group_code."' 
					AND acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		$content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
	}
	 $content.='</select>';*/
	 
   $sqlproddesc="SELECT DISTINCT PM.dns_prod_code,PM.prod_desc,PM.product_group_code,PGM.product_group_name FROM product_master PM,product_group_master PGM 
   				WHERE PM.product_group_code=PGM.product_group_code AND PM.product_group_code IN(".$prod_group_code.") 
				AND PM.branch_code IN(".$prodbranch.") AND PM.pack_size IN(".$prodtype.") AND PM.acedns='Y' AND PM.black_list='N' 
				ORDER BY PGM.product_group_name,PM.prod_desc ASC";
	$rsproddesc=mysql_query($sqlproddesc);
	$product_group_code_array=array();
	while($rowproddesc=mysql_fetch_array($rsproddesc))
	{
		 $product_group_code=$rowproddesc['product_group_code'];
		 $product_group_name=$rowproddesc['product_group_name'];
		 if(!in_array($product_group_code,$product_group_code_array))
		 {
		  $content.="<tr>";
		  $content.="<td align='left'>";
		  $content.="<b>".$product_group_name."</b>";
		  $content.="</td>";
		  $content.= "</tr>";  
		  array_push($product_group_code_array,$product_group_code);
		 }
		  $content.="<tr>";
          $content.="<td align='left'>";
          $content.="<input type='checkbox' name='prod_code[]' value='".$rowproddesc['dns_prod_code']."' />".$rowproddesc['prod_desc']."";
          $content.="</td>";
          $content.= "</tr>"; 
	}
	echo $content;
	mysql_close($link);
?>