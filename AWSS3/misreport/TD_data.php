<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysql_query($sql_sauda_filter);
$row_sauda_filter = mysql_fetch_array($res_sauda_filter);

$sauda_filter_value = $row_sauda_filter['sauda_allocation_basedon_filter'];

if($sauda_filter_value == 1)
{
	$sauda_table_value = 'product_group_master';
	$field_name1 = 'product_group_code';
	$field_name2 = 'product_group_name';
	$acronym = "PGM";
}
else if($sauda_filter_value == 2)
{
	$sauda_table_value = 'product_sub_group_master';
	$field_name1 = 'product_sub_group_code';
	$field_name2 = 'product_sub_group_name';
	$acronym = "PSGM";
}
else if($sauda_filter_value == 3)
{
	$sauda_table_value = 'product_brand_master';
	$field_name1 = 'product_brand_code';
	$field_name2 = 'product_brand_name';
	$acronym = "PBM";
}
else if($sauda_filter_value == 4)
{
	$sauda_table_value = 'product_master';
	$field_name1 = 'product_code';
	$field_name2 = 'product_name';
	$acronym = "PM";
}

$group_name = $acronym.".".$field_name2;
$group_code = $acronym.".".$field_name1;

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition=' WHERE 1';
	$emp_hierarchy_condition_one='';
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=' WHERE reporting_to IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
}
	
if($_SESSION['admin_login']=="admin")
{
	$emp_condition = " WHERE 1";
	$reporting_to='';
}
else
{
	$emp_condition = " WHERE reporting_to IN(".$emp_hierarchy.")";
	$reporting_to=$_SESSION['admin_login'];
}
	
$sql_count_product = "SELECT distinct($group_name) FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);
$td_width = round(70/$total_product,2);

function Populate_employee_hierarchy_html($emp_code,$padding,$td_width,$today)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_details_html($emp_code, $emphierarchyhtml,$padding,$td_width,$today);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_details_html($emp_code,&$emphierarchyhtml,$padding,$td_width,$today){
   $sqlemphierarchy="SELECT emp_code,emp_name,designation FROM employee_master WHERE reporting_to='".$emp_code."' ORDER BY emp_name ASC";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		$padding=$padding+10;
		if($padding<'20')         $color='#7FAAFF'; 
		else if($padding<'30') 	$color='#D4FF7F';
		else if($padding<'40')    $color='#FFAA7F';
		else if($padding<'50')    $color='#FFFFAA';
		$count=1;
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			/* if($rowemphierarchy['emp_code']!=$logged_in_emp)
			 {
			 	$emp_hierarchy_condition=return_employee_hierarchy($rowemphierarchy['emp_code']);
			 }*/
			 $sqlchkaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
			 $rschkaccess=mysql_query($sqlchkaccess);
			 $rowchkaccess=mysql_fetch_array($rschkaccess);
			 $get_allocation=$rowchkaccess['get_allocation'];
			 if($get_allocation=='yes' )
			 {
				 $emp_name=$rowemphierarchy['emp_name'];
				 $emphierarchyhtml="<tr>";
				 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"width:27%;padding-left:".$padding."px;\">".$count.'.'.$emp_name."</td>";
				 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master ORDER BY product_group_name ASC ";
				 $res_product = mysql_query($sql_product);
				  while($row_product = mysql_fetch_array($res_product))
				  {
					   $product_group_code=$row_product['product_group_code'];
					   $sql_sauda_details = "SELECT TD FROM TD_allocation_log WHERE emp_code='".$rowemphierarchy['emp_code']."' AND 
											product_filter_code='".$product_group_code."' AND DATE_FORMAT(SUBSTRING(allocation_date,1,10),'%Y%m%d') 
											LIKE '%$today%' ORDER BY allocation_date DESC LIMIT 0,1";
					   $res_sauda_details = mysql_query($sql_sauda_details);
					   $row_sauda_details = mysql_fetch_array($res_sauda_details);
					 
		  			  $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_sauda_details[TD]</td>";
	 			 }
	  		  $emphierarchyhtml.="<td align=\"right\"><a href=\"#\" style=\"color:blue;\" onclick=\"GenericAjaxFunction('add_edit_TD.php?mode=edit&emp_code=$rowemphierarchy[emp_code]','edit_form',0);\" width=\"3%\">Edit</a></td></tr>";
			  echo $emphierarchyhtml.="</tr>";
			  employee_hierarchy_details_html($rowemphierarchy['emp_code'],$emphierarchyhtml,$padding,$td_width,$today);
			  $count++;
			}
		}
	}
}
?>
<div style="position:absolute; width:inherit;">
<table width="100%" style="border-collapse:collapse;" border="1">
  <tr>
  	<td colspan="<?php echo (($total_product*2)+3); ?>" class="TDHEAD" align="center"><b>TD Details&nbsp;&nbsp;<?php echo $sauda_date; ?></b></td>
  </tr>

  <tr class="TDHEAD_SUB" align="center">
    <td rowspan="2" width="27%"><b>Employee</b></td>
    <?php
	$res_count_product = mysql_query($sql_count_product);
	while($row_count_product = mysql_fetch_array($res_count_product))
		echo "<td align=\"center\"  width=\"$td_width%\"><b>$row_count_product[$field_name2]</b></td>";
	?>
    <td rowspan="2" width="3%">Edit</td>
  </tr>

  <tr class="TDHEAD_SUB" align="center">
    <?php
	for($i=1;$i<=$total_product;$i++)
		echo "<td width=\"$td_width%\" align=\"center\"><b>Alloted</b></td>";
	?>
  </tr>
</table>
</div>
<br />
<br />
<br />
<br />
<br />
<table width="100%" border="1" style="border-collapse:collapse;">
  <?php
  echo "<form>";
   $padding=4;
   if($_SESSION['admin_login']!='admin')
   {
	   $sqlemphierarchy="SELECT emp_code,emp_name,designation FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
	   $rsemphierarchy=mysql_query($sqlemphierarchy);
	   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
		if($cntemphierarchy>0)
		{
			$color='#2AFFFF';
			$count=1;
			while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
			{
				 $sqlchkaccess="SELECT get_allocation FROM sauda_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
				 $rschkaccess=mysql_query($sqlchkaccess);
				 $rowchkaccess=mysql_fetch_array($rschkaccess);
				 $get_allocation=$rowchkaccess['get_allocation'];
				 if($get_allocation=='yes' )
				 {
					 $emp_name=$rowemphierarchy['emp_name'];
					 $emphierarchyhtml="<tr>";
					 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"width:27%;padding-left:".$padding."px;\">".$count.'.'.$emp_name."</td>";
					 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master ORDER BY product_group_name ASC ";
					 $res_product = mysql_query($sql_product);
					  while($row_product = mysql_fetch_array($res_product))
					  {
						   $product_group_code=$row_product['product_group_code'];
						   $sql_sauda_details = "SELECT TD FROM TD_allocation_log WHERE emp_code='".$rowemphierarchy['emp_code']."' AND 
												product_filter_code='".$product_group_code."' AND DATE_FORMAT(SUBSTRING(allocation_date,1,10),'%Y%m%d') 
												LIKE '%$today%' ORDER BY allocation_date DESC LIMIT 0,1";
						   $res_sauda_details = mysql_query($sql_sauda_details);
						   $row_sauda_details = mysql_fetch_array($res_sauda_details);
						 
						  $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_sauda_details[TD]</td>";
					 }
				  $emphierarchyhtml.="<td align=\"right\">--</td></tr>";
				  echo $emphierarchyhtml.="</tr>";
				 }
			}
		}
   }
  echo Populate_employee_hierarchy_html($reporting_to,$padding,$td_width,$today,$_SESSION['admin_login']);
  echo "</form>";
  ?>
</table>
<?php
mysql_close($link);
?>
