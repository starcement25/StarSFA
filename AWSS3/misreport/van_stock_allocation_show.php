<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
$today_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition=' WHERE 1';
	$emp_hierarchy_condition_one='';
	$reporting_to='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' WHERE reporting_to IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one='';
	$reporting_to=$_SESSION['admin_login'];
}
	
$sql_count_product = "SELECT prod_desc FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);
$td_width = ceil(82/$total_product);

function Populate_employee_hierarchy_html($emp_code,$padding,$td_width,$today)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_details_html($emp_code, $emphierarchyhtml,$padding,$td_width,$today);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_details_html($emp_code,&$emphierarchyhtml,$padding,$td_width,$today){
  $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE reporting_to='".$emp_code."'  
   					AND acedns!='N' ORDER BY emp_name ASC";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		$padding=$padding+10;
		if($padding<'20')         $color='BLUE'; 
		else if($padding<'30') 	$color='LIGHTGREEN';
		else if($padding<'40')    $color='KHAKI';
		else if($padding<'50')    $color='LIGHTYELLOW';
		$count=1;
		$color_balance="LIGHTPINK";
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			/* if($rowemphierarchy['emp_code']!=$logged_in_emp)
			{
			$emp_hierarchy_condition=return_employee_hierarchy($rowemphierarchy['emp_code']);
			}*/
			 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
			 $emp_name=$rowemphierarchy['emp_name'];
			 $emphierarchyhtml="<tr >";
			 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"padding-left:".$padding."px;\" width=\"15%\">".$count.'.'.$emp_name."</td>";
			 $sql_product = "SELECT prod_code,prod_desc FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC ";
			 $res_product = mysql_query($sql_product);
			  while($row_product = mysql_fetch_array($res_product))
			  {
				$prod_code=$row_product['prod_code'];
				$sql_van_stock_details="SELECT allocated_qty,balance_qty FROM van_stock_allocation WHERE prod_code ='".$prod_code."' 
									AND emp_code='".$rowemphierarchy['emp_code']."' AND 	allocation_date='".$today."'";						
			   $res_van_stock_details = mysql_query($sql_van_stock_details);
			   $row_van_stock_details = mysql_fetch_array($res_van_stock_details);
			   $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$prod_code\" align=\"right\" width=\"$td_width%\">$row_van_stock_details[allocated_qty]</td>";
			   $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$prod_code\" bgcolor=\"$color_balance\"  align=\"right\" width=\"$td_width%\">$row_van_stock_details[balance_qty]</td>";

			 }
			//$emphierarchyhtml.="<td align=\"right\" width=\"3%\"><a href=\"#\" style=\"color:blue;\" onclick=\"GenericAjaxFunction('add_edit_TD_allocation.php?mode=edit&emp_code=$rowemphierarchy[emp_code]','edit_form',0);\" >Edit</a></td></tr>";
			//$emphierarchyhtml.="</tr>";
			echo $emphierarchyhtml.="</tr>";
			employee_hierarchy_details_html($rowemphierarchy['emp_code'],$emphierarchyhtml,$padding,$td_width,$today);
			$count++;
		}
	}
}
?>
<!--div style="position:absolute; width:inherit;"-->
<table width="100%"   id="maintable" border="1" style="border-collapse: collapse;">
<thead >
  <tr>
  	<th colspan="<?php echo (($total_product*2)+3); ?>" class="TDHEAD" align="center"><b>Van stock allocation Details&nbsp;&nbsp;<?php echo $today_date; ?></b></th>
  </tr>

  <tr class="TDHEAD_SUB" align="center">
    <th rowspan="2" width="15%"><b>Employee</b></th>
    <?php
	$res_count_product = mysql_query($sql_count_product);
	while($row_count_product = mysql_fetch_array($res_count_product))
		echo "<th align=\"center\"  width=\"(2*$td_width)%\" colspan=\"2\"><b>$row_count_product[prod_desc]</b></th>";
	?>
    <!--th rowspan="2" width="3%">Edit</th-->
  </tr>
  <tr class="TDHEAD_SUB" align="center">
    <?php
	for($i=1;$i<=$total_product;$i++){
		echo "<th width=\"$td_width%\" align=\"center\"><b>Alloted</b></th>";
		echo "<th width=\"$td_width%\" align=\"center\"><b>Balance</b></th>";
	}
	?>
  </tr>
  </thead>
  <?php
  echo "<form>";
   $padding=4;
  /* if($_SESSION['admin_login']!='admin')
   {
	   $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
	   $rsemphierarchy=mysql_query($sqlemphierarchy);
	   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
		if($cntemphierarchy>0)
		{
			$color='#2AFFFF';
			$count=1;
			while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
			{
				 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
					 $sqlchkaccess="SELECT get_allocation FROM TD_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
					 $rschkaccess=mysql_query($sqlchkaccess);
					 $rowchkaccess=mysql_fetch_array($rschkaccess);
					 $get_allocation=$rowchkaccess['get_allocation'];
					 if($get_allocation=='yes' )
					 {
						 $emp_name=$rowemphierarchy['emp_name'];
						 $emphierarchyhtml="<tr style=\"overflow: auto;\">";
						 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"width:15%;padding-left:".$padding."px;\">".$count.'.'.$emp_name."</td>";
						 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master PGM WHERE 1 AND PGM.acedns='Y' ".$condition_one." ORDER BY product_group_name ASC ";
						 $res_product = mysql_query($sql_product);
						  while($row_product = mysql_fetch_array($res_product))
						  {
							   $product_group_code=$row_product['product_group_code'];
								$sql_TD_details="SELECT TD FROM TD_allocation WHERE product_filter_code ='".$product_group_code."' 
													AND emp_code='".$rowemphierarchy['emp_code']."'";						
							   $res_TD_details = mysql_query($sql_TD_details);
							   $row_TD_details = mysql_fetch_array($res_TD_details);
							 
							  $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_TD_details[TD]</td>";
						 }
					  $emphierarchyhtml.="<td align=\"right\">--</td></tr>";
					 echo $emphierarchyhtml.="</tr>";
					}
			}
		}
   }*/
  echo Populate_employee_hierarchy_html($reporting_to,$padding,$td_width,$today);
  echo "</form>";
  mysql_close($link);
  ?>
</table>
<br>
<div style="width:70%;" align="center"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
