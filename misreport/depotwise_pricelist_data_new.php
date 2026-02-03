<?php
error_reporting(0);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
?>

<?php
$branch_code = $_REQUEST['branch_code'];
$branch_code_count=count(explode(',',$branch_code));
$product_group_code = $_REQUEST['product_group_code'];
$product_group_code_count=count(explode(',',$product_group_code));
$from_date = $_REQUEST['from_date'];
$from_date_check=date('Y-m-d',strtotime($from_date));
$from_date_final=$from_date.' 23:59:59';
$from_date_final=date('Y-m-d',strtotime($from_date_final));
$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code=".$product_group_code;
$res_prodgroup_name = mysqli_query($link,$sql_prodgroup_name);
$row_prodgroup_name = mysqli_fetch_assoc($res_prodgroup_name);
$prodgroupname = $row_prodgroup_name['product_group_name'];

if($_SESSION['admin_login'] != 'admin' && $_SESSION['admin_login'] != 'supervisor')
{
 $sql_employee_branch_code = "SELECT emp_name FROM employee_master WHERE emp_code = '$_SESSION[admin_login]' AND FIND_IN_SET( ".$branch_code.", branch_code )";
$res_employee_branch_code = mysqli_query($link,$sql_employee_branch_code);
$total_emp_rows = mysqli_num_rows($res_employee_branch_code);
}
else
$total_emp_rows = 1;

if($total_emp_rows>0)
{
	$branch_code_array = array();
	$dns_prod_code_array = array();
	$prod_desc_array = array();
	$loose_rate_ton_array = array();
/*echo $sql_product_details = "SELECT PM.prod_desc, SMR.sale_rate, BM.branch_name FROM product_master PM, sauda_mrp SMR, branch_master BM WHERE PM.product_group_code IN ($product_group_code) AND PM.branch_code IN ($branch_code) AND PM.prod_code = SMR.product_code AND PM.branch_code = SMR.branch_code AND PM.acedns='Y' AND PM.branch_code=BM.branch_code ORDER BY BM.branch_name ASC";
$res_product_details = mysqli_query($link,$sql_product_details);
$total_rows = mysqli_num_rows($res_product_details);*/

?>
	<table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
      <tr>
        <td colspan="3" style="FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #DCE6F1;" align="center">Product Sale Rate - <?php echo $prodgroupname; ?></td>
      </tr>
      <br />
      <!--tr class="TDHEAD_SUB">
      	<td>SI</td>
        <td>Product Description</td>
        <td>Sale Rate</td>
      </tr-->
      <?php
	  		echo "<tr>";
		echo "<td   align=\"left\" style=\"	FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #DCE6F1;\">Product Description</td>";

		/*$sql_select_branch_name = "SELECT branch_code,branch_name,plant_name FROM branch_master WHERE branch_code IN($branch_code) ";
		$res_select_branch_name = mysqli_query($link,$sql_select_branch_name);
		while($row_select_branch_name = mysqli_fetch_assoc($res_select_branch_name))
		{
			echo "<td  align=\"left\" style=\"	FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #DCE6F1;\">".$row_select_branch_name['branch_name']."</td>";
			$branch_code=$row_select_branch_name['branch_code'];
			$plant_name=$row_select_branch_name['plant_name'];
			$sql_dnsproduct_details = "SELECT prod_desc,dns_prod_code,product_group_code FROM product_master WHERE 
			product_group_code IN ($product_group_code) AND branch_code='".$branch_code."' AND acedns='Y'  ORDER BY prod_desc ASC";
	 		$res_dnsproduct_details = mysqli_query($link,$sql_dnsproduct_details);
			while($row_dnsproduct_details=mysqli_fetch_assoc($res_dnsproduct_details))
			{
				$dns_prod_code=$row_dnsproduct_details['dns_prod_code'];
				$prod_desc=$row_dnsproduct_details['prod_desc'];
				$product_group_code_val=$row_dnsproduct_details['product_group_code'];
				$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code_val."' AND 
						plant_name='".$plant_name."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
				$rslooserate=mysqli_query($link,$sqllooserate);
				$rowlooserate=mysqli_fetch_assoc($rslooserate);
				$loose_rate_ton=$rowlooserate['loose_rate_ton'];

				if(!in_array($dns_prod_code, $dns_prod_code_array)){
					array_push($dns_prod_code_array,$dns_prod_code);
					array_push($prod_desc_array,$prod_desc);
					array_push($loose_rate_ton_array,$loose_rate_ton);
				}
			}
			if(!in_array($branch_code, $branch_code_array)){
				array_push($branch_code_array,$branch_code);
			}
		}*/
		if($branch_code_count >1)
		{
			$branch_code='';
			$sqlpricingbranch="SELECT branch_code,branch_name,plant_name FROM branch_master WHERE plant_name IN(SELECT DISTINCT plant_name FROM 
							pricing_detials WHERE SUBSTRING(datetime,1,10)='".$from_date_check."')";
			$rspricingbranch=mysqli_query($link,$sqlpricingbranch);
			$cntpricingbranch=mysqli_num_rows($rspricingbranch);
			if($cntpricingbranch==0)
			{
				echo "<b>Pricing of the particular date does not exist.</b>";
				exit();
			}
			else
			{
				while($rowpricingbranch=mysqli_fetch_assoc($rspricingbranch))
				{
					$branch_code=$rowpricingbranch['branch_code'];
					$plant_name=$rowpricingbranch['plant_name'];
					$sql_dnsproduct_details = "SELECT prod_desc,dns_prod_code,product_group_code FROM product_master WHERE 
					product_group_code IN (SELECT DISTINCT product_group_code FROM pricing_detials WHERE SUBSTRING(datetime,1,10)='".$from_date_check."' 
					AND plant_name='".$plant_name."' AND product_group_code IN ($product_group_code)) AND branch_code='".$branch_code."' ORDER BY prod_desc ASC";
					$res_dnsproduct_details = mysqli_query($link,$sql_dnsproduct_details);
					while($row_dnsproduct_details=mysqli_fetch_assoc($res_dnsproduct_details))
					{
						$dns_prod_code=$row_dnsproduct_details['dns_prod_code'];
						$prod_desc=$row_dnsproduct_details['prod_desc'];
						$product_group_code_val=$row_dnsproduct_details['product_group_code'];
						$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code_val."' AND 
								plant_name='".$plant_name."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
						$rslooserate=mysqli_query($link,$sqllooserate);
						$rowlooserate=mysqli_fetch_assoc($rslooserate);
						$loose_rate_ton=$rowlooserate['loose_rate_ton'];
		
						if(!in_array($dns_prod_code, $dns_prod_code_array)){
							array_push($dns_prod_code_array,$dns_prod_code);
							array_push($prod_desc_array,$prod_desc);
							array_push($loose_rate_ton_array,$loose_rate_ton);
						}
						if(!in_array($branch_code, $branch_code_array)){
						echo "<td  align=\"left\" style=\"	FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #DCE6F1;\">".$rowpricingbranch['branch_name']."</td>";
							array_push($branch_code_array,$branch_code);
						}
					}
				}
			}
			if(count($dns_prod_code_array)==0)
				{
					echo "<b>Pricing of the particular date and depot does not exist.</b>";
					exit();
				}
		}
		else
		{
			$sqlpricingbranch="SELECT branch_code,plant_name,branch_name FROM branch_master WHERE branch_code IN(".$branch_code.") AND plant_name 
								IN(SELECT plant_name FROM pricing_detials WHERE SUBSTRING(datetime,1,10)='".$from_date_check."')";
			$rspricingbranch=mysqli_query($link,$sqlpricingbranch);
			$cntpricingbranch=mysqli_num_rows($rspricingbranch);
			if($cntpricingbranch==0)
			{
				echo "<b>Pricing of the particular date and depot does not exist.</b>";
				exit();
			}
			else
			{
				$rowpricingbranch=mysqli_fetch_assoc($rspricingbranch);
				$branch_code=$rowpricingbranch['branch_code'];
				$plant_name=$rowpricingbranch['plant_name'];
				$sql_dnsproduct_details = "SELECT prod_desc,dns_prod_code,product_group_code FROM product_master WHERE 
						product_group_code IN (SELECT DISTINCT product_group_code FROM pricing_detials WHERE SUBSTRING(datetime,1,10)='".$from_date_check."' 
						AND plant_name='".$plant_name."' AND product_group_code IN ($product_group_code)) AND branch_code='".$branch_code."' 
						ORDER BY prod_desc ASC";
				$res_dnsproduct_details = mysqli_query($link,$sql_dnsproduct_details);
				while($row_dnsproduct_details=mysqli_fetch_assoc($res_dnsproduct_details))
				{
					$dns_prod_code=$row_dnsproduct_details['dns_prod_code'];
					$prod_desc=$row_dnsproduct_details['prod_desc'];
					$product_group_code_val=$row_dnsproduct_details['product_group_code'];
					$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code_val."' AND 
							plant_name='".$plant_name."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
					$rslooserate=mysqli_query($link,$sqllooserate);
					$rowlooserate=mysqli_fetch_assoc($rslooserate);
					$loose_rate_ton=$rowlooserate['loose_rate_ton'];
		
					if(!in_array($dns_prod_code, $dns_prod_code_array)){
						array_push($dns_prod_code_array,$dns_prod_code);
						array_push($prod_desc_array,$prod_desc);
						array_push($loose_rate_ton_array,$loose_rate_ton);
					}
					if(!in_array($branch_code, $branch_code_array)){
						echo "<td  align=\"left\" style=\"	FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #000000;BACKGROUND-COLOR: #DCE6F1;\">".$rowpricingbranch['branch_name']."</td>";
						array_push($branch_code_array,$branch_code);
					}
				}
				if(count($dns_prod_code_array)==0)
				{
					echo "<b>Pricing of the particular date and depot does not exist.</b>";
					exit();
				}
			}
		}
		echo "</tr>";
	
		for($i=0;$i < count($dns_prod_code_array);$i++)
		{
			echo "<tr>";
			echo "<td>".$prod_desc_array[$i]."</td>";
			foreach($branch_code_array as $branch_code_values){
				$sql_product_details = "SELECT prod_code,conversion_factor,conversion_factor_two,acedns FROM product_master WHERE 
										dns_prod_code='".$dns_prod_code_array[$i]."' AND branch_code='".$branch_code_values."' ORDER BY prod_code DESC LIMIT 0,1";
				$res_product_details = mysqli_query($link,$sql_product_details);
				$row_product_details = mysqli_fetch_assoc($res_product_details);
				$prod_code=$row_product_details['prod_code'];
				${acedns.$dns_prod_code_array[$i]}=$row_product_details['acedns'];
				${conversion_factor.$dns_prod_code_array[$i]}=$row_product_details['conversion_factor'];
				${conversion_factor_two.$dns_prod_code_array[$i]}=$row_product_details['conversion_factor_two'];

				if($from_date=='')
				{
					$sqlpackingprodwise="SELECT no_pc_one_case FROM packing_master WHERE dns_prod_code='".$dns_prod_code_array[$i]."' 
										ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
					$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
					$no_pc_one_case=$rowpackingprodwise['no_pc_one_case'];
	
					$sql_rate = "SELECT sale_rate FROM sauda_mrp SMR WHERE branch_code='".$branch_code_values."' AND product_code='".$prod_code."' ";
					$rs_rate=mysqli_query($link,$sql_rate);
					$row_rate=mysqli_fetch_assoc($rs_rate);
					$rate=$row_rate['sale_rate'];
					${final_rate.$dns_prod_code_array[$i]}=$rate/$no_pc_one_case;
				}
				else
				{
					$sqlpackingprodwise="SELECT packing_cost,no_pc_one_case FROM packing_master WHERE dns_prod_code='".$dns_prod_code_array[$i]."' AND 
										datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";
					$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
					$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
					${packing_cost.$dns_prod_code_array[$i]}=$rowpackingprodwise['packing_cost'];
					${no_pc_one_case.$dns_prod_code_array[$i]}=$rowpackingprodwise['no_pc_one_case'];
					if(${packing_cost.$dns_prod_code_array[$i]}=='')  ${packing_cost.$dns_prod_code_array[$i]}=0;
					
					$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$dns_prod_code_array[$i]."' 
										AND branch_code='".$branch_code_values."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";					
					$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
					$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
					${depot_cost.$dns_prod_code_array[$i]}=$rowdepotcostprodwise['depot_cost'];
					if(${depot_cost.$dns_prod_code_array[$i]}=='')  ${depot_cost.$dns_prod_code_array[$i]}=0;
					
					$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$dns_prod_code_array[$i]."' 
										AND branch_code='".$branch_code_values."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";					
					$rsfreightcostprodwise=mysqli_query($link,$sqlfreightcostprodwise);
					$rowfreightcostprodwise=mysqli_fetch_assoc($rsfreightcostprodwise);
					${freight_cost.$dns_prod_code_array[$i]}=$rowfreightcostprodwise['freight_cost'];
					if(${freight_cost.$dns_prod_code_array[$i]}=='')      ${freight_cost.$dns_prod_code_array[$i]}=0;
					
					$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$dns_prod_code_array[$i]."' 
										AND branch_code='".$branch_code_values."' AND datetime <='".$from_date_final."' ORDER BY datetime DESC LIMIT 0,1";					
					$rsmargincostprodwise=mysqli_query($link,$sqlmargincostprodwise);
					$rowmargincostprodwise=mysqli_fetch_assoc($rsmargincostprodwise);
					${margin_cost.$dns_prod_code_array[$i]}=$rowmargincostprodwise['margin_cost'];
					if(${margin_cost.$dns_prod_code_array[$i]}=='')      ${margin_cost.$dns_prod_code_array[$i]}=0;
					
					$loose_rate_case_prodwise=round(($loose_rate_ton_array[$i]/${conversion_factor_two.$dns_prod_code_array[$i]}),2);
					$loose_rate_case_prodwise=round(($loose_rate_case_prodwise*${conversion_factor.$dns_prod_code_array[$i]}),2);
					
					${mrp_prodwise.$dns_prod_code_array[$i]}=$loose_rate_case_prodwise+${freight_cost.$dns_prod_code_array[$i]}+${packing_cost.$dns_prod_code_array[$i]}+${depot_cost.$dns_prod_code_array[$i]}+${margin_cost.$dns_prod_code_array[$i]};
					${mrp_prodwise.$dns_prod_code_array[$i]}=round(${mrp_prodwise.$dns_prod_code_array[$i]},2).'< /br>';
					${final_rate.$dns_prod_code_array[$i]}=${mrp_prodwise.$dns_prod_code_array[$i]}/${no_pc_one_case.$dns_prod_code_array[$i]};
				}
				if($branch_code_count >1 && $product_group_code_count >1)
				{
					if(${acedns.$dns_prod_code_array[$i]}=='')
					{
						${acedns.$dns_prod_code_array[$i]}='N';
					}
					echo "<td align=\"right\">".number_format(${final_rate.$dns_prod_code_array[$i]},2)."(".${acedns.$dns_prod_code_array[$i]}.")</td>";
				}
				else
				{
					echo "<td align=\"right\">".number_format(${final_rate.$dns_prod_code_array[$i]},2)."</td>";
				}
			}
			echo "</tr>";
		}
	echo "</table>";
/*else
{
	echo "<strong><font color=\"red\">No records</font></strong>";
}*/
}
else
{
	echo "<font color=\"red\"><strong>You are not tagged with this branch</strong></font>";
}
mysqli_close($link);
?>
