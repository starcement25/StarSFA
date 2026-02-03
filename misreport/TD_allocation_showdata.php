<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
	if(strtoupper($_SESSION['admin_login'])=='SFATS')
	{
		$admin_login_value='SFATS';
		$_SESSION['admin_login']='E0076';
	}
	else  $admin_login_value='';

date_default_timezone_set("Asia/Kolkata"); 
$today_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy='';
	$emp_hierarchy_condition=' WHERE 1';
	$emp_hierarchy_condition_one='';
}
else
{
	if(strtoupper($_SESSION['admin_login'])=="GMHBC")
	{
		$emp_hierarchy=return_employee_hierarchy('E0042');
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	}
	$emp_hierarchy_condition=' WHERE reporting_to IN('.$emp_hierarchy.')';
	$emp_hierarchy_condition_one='';
}
	
if($_SESSION['admin_login']=="admin")
{
	$emp_condition = " WHERE 1";
	$reporting_to='';
	$condition_one="";
}
else
{
	$emp_condition = " WHERE reporting_to IN(".$emp_hierarchy.")";
	$reporting_to=$_SESSION['admin_login'];
	if(vertical_fields=='yes'){
		$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
		$rsempvertical=mysqli_query($link,$sqlempvertical);
		$rowempvertical=mysqli_fetch_assoc($rsempvertical);
		$emp_vertical_value=$rowempvertical['vertical_value'];
		$emp_vertical_value_array=explode(',',$emp_vertical_value);
		//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
		$condition_one=" AND (";
		$condition_two='';
		foreach($emp_vertical_value_array as $emp_vertical_values)
		{
			$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',vertical_value) OR";
		}
		$condition_two=substr($condition_two,0,-2);
		$condition_one.=$condition_two.")";
	}
}

$sql_count_product = "SELECT product_group_name FROm product_group_master WHERE acedns='Y' ".$condition_one." ORDER BY product_group_name ASC";
$res_count_product = mysqli_query($link,$sql_count_product);
$total_product = mysqli_num_rows($res_count_product);
$td_width = ceil(82/$total_product);


function Populate_employee_hierarchy_html($emp_code,$padding,$td_width,$today,$condition_one)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_details_html($emp_code, $emphierarchyhtml,$padding,$td_width,$today,$condition_one);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_details_html($emp_code,&$emphierarchyhtml,$padding,$td_width,$today,$condition_one){
	if($emp_code=='')
	{
		 $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE reporting_to=''  AND acedns!='N' ORDER BY emp_name ASC";
	}
	else
	{
   $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE FIND_IN_SET( '".$emp_code."', reporting_to)  
   					AND acedns!='N' ORDER BY emp_name ASC";
	}
   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		$padding=$padding+10;
		if($padding<'20')         $color='#7FAAFF'; 
		else if($padding<'30') 	$color='#D4FF7F';
		else if($padding<'40')    $color='#FFAA7F';
		else if($padding<'50')    $color='#FFFFAA';
		$count=1;
		while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
		{
			/* if($rowemphierarchy['emp_code']!=$logged_in_emp)
			 {
			 	$emp_hierarchy_condition=return_employee_hierarchy($rowemphierarchy['emp_code']);
			 }*/
			 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
			 $sqlchkaccess="SELECT get_allocation FROM TD_allocation_access WHERE  emp_code='".$rowemphierarchy['emp_code']."'";
			 $rschkaccess=mysqli_query($link,$sqlchkaccess);
			 $rowchkaccess=mysqli_fetch_assoc($rschkaccess);
			 $get_allocation=$rowchkaccess['get_allocation'];
			 if($get_allocation=='yes' )
			 {
				 $emp_name=$rowemphierarchy['emp_name'];
				 $emphierarchyhtml="<tr >";
				 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"padding-left:".$padding."px;\" width=\"15%\">".$count.'.'.$emp_name."</td>";
				 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master PGM WHERE 1 AND PGM.acedns='Y' ".$condition_one." ORDER BY product_group_name ASC ";
				 $res_product = mysqli_query($link,$sql_product);
				  while($row_product = mysqli_fetch_assoc($res_product))
				  {
					   $product_group_code=$row_product['product_group_code'];
						$sql_TD_details="SELECT TD FROM TD_allocation WHERE product_filter_code ='".$product_group_code."' 
						   					AND emp_code='".$rowemphierarchy['emp_code']."'";						
					   $res_TD_details = mysqli_query($link,$sql_TD_details);
					   $row_TD_details = mysqli_fetch_assoc($res_TD_details);
		  			   $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_TD_details[TD]</td>";
	 			 }
	  		  $emphierarchyhtml.="<td align=\"right\" width=\"3%\"><a href=\"#\" style=\"color:blue;\" onclick=\"GenericAjaxFunction('add_edit_TD_allocation.php?mode=edit&emp_code=$rowemphierarchy[emp_code]','edit_form',0);\" >Edit</a></td></tr>";
			 echo $emphierarchyhtml.="</tr>";
			  employee_hierarchy_details_html($rowemphierarchy['emp_code'],$emphierarchyhtml,$padding,$td_width,$today,$condition_one);
			  $count++;
			}
		}
	}
}
?>
<!--div style="position:absolute; width:inherit;"-->
<table width="100%"   id="maintable" border="1" style="border-collapse: collapse;">
<thead >
  <tr>
  	<th colspan="<?php echo (($total_product*2)+3); ?>" class="TDHEAD" align="center"><b>TD allocation Details&nbsp;&nbsp;<?php echo $today_date; ?></b></th>
  </tr>

  <tr class="TDHEAD_SUB" align="center">
    <th rowspan="2" width="15%"><b>Employee</b></th>
    <?php
	$res_count_product = mysqli_query($link,$sql_count_product);
	while($row_count_product = mysqli_fetch_assoc($res_count_product))
		echo "<th align=\"center\"  width=\"$td_width%\"><b>$row_count_product[product_group_name]</b></th>";
	?>
    <th rowspan="2" width="3%">Edit</th>
  </tr>
  <tr class="TDHEAD_SUB" align="center">
    <?php
	for($i=1;$i<=$total_product;$i++)
		echo "<th width=\"$td_width%\" align=\"center\"><b>Alloted</b></th>";
	?>
  </tr>
  </thead>
  <?php
  echo "<form>";
   $padding=4;
   if($_SESSION['admin_login']!='admin')
   {
	   $sqlemphierarchy="SELECT emp_code,emp_name,designation,reporting_to FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
	   $rsemphierarchy=mysqli_query($link,$sqlemphierarchy);
	   $cntemphierarchy=mysqli_num_rows($rsemphierarchy);
		if($cntemphierarchy>0)
		{
			$color='#2AFFFF';
			$count=1;
			while($rowemphierarchy=mysqli_fetch_assoc($rsemphierarchy))
			{
				 $reporting_to_hierarchy=$rowemphierarchy['reporting_to'];
					 $sqlchkaccess="SELECT get_allocation FROM TD_allocation_access WHERE designation='".$rowemphierarchy['designation']."'";
					 $rschkaccess=mysqli_query($link,$sqlchkaccess);
					 $rowchkaccess=mysqli_fetch_assoc($rschkaccess);
					 $get_allocation=$rowchkaccess['get_allocation'];
					 if($get_allocation=='yes' )
					 {
						 $emp_name=$rowemphierarchy['emp_name'];
						 $emphierarchyhtml="<tr style=\"overflow: auto;\">";
						 $emphierarchyhtml.="<td  align=\"left\" bgcolor=\"$color\" style=\"width:15%;padding-left:".$padding."px;\">".$count.'.'.$emp_name."</td>";
						 $sql_product = "SELECT product_group_code,product_group_name FROM product_group_master PGM WHERE 1 AND PGM.acedns='Y' ".$condition_one." ORDER BY product_group_name ASC ";
						 $res_product = mysqli_query($link,$sql_product);
						  while($row_product = mysqli_fetch_assoc($res_product))
						  {
							   $product_group_code=$row_product['product_group_code'];
								$sql_TD_details="SELECT TD FROM TD_allocation WHERE product_filter_code ='".$product_group_code."' 
													AND emp_code='".$rowemphierarchy['emp_code']."'";						
							   $res_TD_details = mysqli_query($link,$sql_TD_details);
							   $row_TD_details = mysqli_fetch_assoc($res_TD_details);
							 
							  $emphierarchyhtml.="<td  id=\"$rowemphierarchy[emp_code]_$product_group_code\" align=\"right\" width=\"$td_width%\">$row_TD_details[TD]</td>";
						 }
					  $emphierarchyhtml.="<td align=\"right\">--</td></tr>";
					 echo $emphierarchyhtml.="</tr>";
					}
			}
		}
   }
  echo Populate_employee_hierarchy_html($reporting_to,$padding,$td_width,$today,$condition_one);
  echo "</form>";
	if($admin_login_value=='SFATS')
	{
		$_SESSION['admin_login']='SFATS';
	}
  mysqli_close($link);
  ?>
</table>
