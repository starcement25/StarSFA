<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	$type=$_REQUEST['type'];
	
	if($type=='honeycombcost')
	{
		$prod_group_code=$_REQUEST['prod_group_code'];
		$content='<select name="prod_code" id="prod_code">';
		$content.='<option value="">SELECT</option>';
		$sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE product_group_code='".$prod_group_code."' 
						AND acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";
		$rsproddesc=mysqli_query($link,$sqlproddesc);
		while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
		{
			$content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
		}
		 $content.='</select>';
	
		echo $content;
	}
	else
	{
		$prod_group_code=$_REQUEST['prod_group_code'];
		$content='<select name="prod_code" id="prod_code" onChange="javascript:previous_cost();">';
		$content.='<option value="">SELECT</option>';
		$sqlproddesc="SELECT DISTINCT dns_prod_code,prod_desc FROM product_master WHERE product_group_code='".$prod_group_code."' 
						AND acedns='Y' AND black_list='N' ORDER BY prod_desc ASC";
		$rsproddesc=mysqli_query($link,$sqlproddesc);
		while($rowproddesc=mysqli_fetch_assoc($rsproddesc))
		{
			$content.="<option value='".$rowproddesc['dns_prod_code']."'>".$rowproddesc['prod_desc']."</option>";
		}
		 $content.='</select>';
	
		echo $content;
	}
	mysqli_close($link);
?>