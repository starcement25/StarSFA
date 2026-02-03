<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=="ED01"){
		require("adminUtils_HBC_SFATS.php");
	}
	else
	{
		require("adminUtils.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$glblcnt=1;

if($_GET['type'] == 'today')
{
	$today = date('Y-m-d');
	$today1 = str_replace("-","",$today);
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '%$today%'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}
else if($_GET['type'] == 'mtd')
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "AND YEAR(SUBSTRING(SAL.allocation_date,1,10)) =". $year." AND MONTH(SUBSTRING(SAL.allocation_date,1,10)) =" .$month;
	$sauda_booked_condition = "AND substring(SD.sauda_no,8,4) =$year AND substring(SD.sauda_no,12,2) =$month";
	$sauda_duration = "From : 01-".$month."-".$year." To ".date('d-m-Y');
	$value = 2;
}
else if($_GET['type'] == 'custom')
{
	$start_date = str_replace("-","",$_GET['start_date']);
	$strt = date('d-m-Y',strtotime($start_date));
	$end_date = str_replace("-","",$_GET['end_date']);
	$endt = date('d-m-Y',strtotime($end_date));
	$condition = "AND (SUBSTRING(SAL.allocation_date,1,10) BETWEEN '".$_GET['start_date']." "."12:00:01' AND '".$_GET['end_date']." "."23:59:59')";
	$sauda_booked_condition = "AND (substring(SD.sauda_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.")";
	$sauda_duration = "From :".$strt." to ".$endt;
	$value = 3;
}
else
{
	$today = date('Y-m-d');
	$condition = "AND DATE_FORMAT(SUBSTRING(SAL.allocation_date,1,10),'%Y-%m-%d') LIKE '$today'";
	$sauda_booked_condition = "AND substring(SD.sauda_no,-14,8) LIKE '".str_replace("-","",$today)."'";
	$sauda_duration = date('d-m-Y');
	$value = 1;
}

date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysqli_query($link,$sql_sauda_filter);
$row_sauda_filter = mysqli_fetch_assoc($res_sauda_filter);

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

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == "supervisor")
{
	$vertical_val_condition="WHERE 1 ";
	$vertical_val_condition_one="";
}
else
{
	$vertical_val_condition=" WHERE PGM.vertical_value='".$_SESSION['vertical_value']."'";
	$vertical_val_condition_one=" AND PM.vertical_value='".$_SESSION['vertical_value']."'";
}

/*$sql_count_product = "SELECT distinct($group_name),$group_code FROM $sauda_table_value $acronym  
					".$vertical_val_condition."  ORDER BY $group_name ASC";*/
$sql_count_product = "SELECT  PGM.product_group_code,PGM.product_group_name FROM $sauda_table_value $acronym  
					".$vertical_val_condition." AND PGM.acedns='Y' AND PGM.product_group_code IN
					(SELECT DISTINCT PM.product_group_code FROM product_master PM,sauda_details SD WHERE PM.prod_code=SD.sku_code 
						$vertical_val_condition_one  $sauda_booked_condition) ORDER BY $group_name ASC";
$res_count_product = mysqli_query($link,$sql_count_product);
$total_product = mysqli_num_rows($res_count_product);

if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == "supervisor"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		if(strtoupper($_SESSION['admin_login'])=="ED01"){
			$emp_hierarchy=return_employee_hierarchy('E0042');
		}
		else
		{
			$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		}
		$emp_hierarchy_condition=' AND SAL.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
	}


$count = 1;
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == "supervisor" || $_SESSION['admin_login'] == "system")
{
	$emp_condition = 1;
	$reporting_to='';
}
else
{
	$emp_condition = "reporting_to IN(".$emp_hierarchy.")";
	if(strtoupper($_SESSION['admin_login'])=="ED01"){
		$reporting_to='E0042';
	}
	else
	{
		$reporting_to=$_SESSION['admin_login'];
	}
}

/*$sql_emp = "SELECT EM.emp_name, EM.emp_code FROM employee_master EM, sauda_details SD WHERE ".$emp_condition." 
			AND substring(SD.sauda_no,3,5)=EM.emp_code ".$sauda_booked_condition." GROUP BY EM.emp_code";*/
?>
<div style="position:absolute; width:inherit;">
<table width="100%" border="1" style="border-collapse:collapse;">
  <tr>
  	<td colspan="<?php echo (($total_product*1)+2); ?>" class="TDHEAD" align="center"><b>Employeewise <?php echo $sauda_duration; ?></b></td>
  </tr>
  <tr class="TDHEAD_SUB">
    <td width="20%" align="center"><b>Employee Name</b></td>
    <?php
	$product_group_string='';
	$td_width=(80/($total_product+1));
	$res_count_product = mysqli_query($link,$sql_count_product);
	while($row_count_product = mysqli_fetch_assoc($res_count_product)){
		echo "<td width=\"$td_width%\" align=\"center\"><b>$row_count_product[product_group_name]</b></td>";
		
		$product_group_string=$product_group_string."'".$row_count_product['product_group_code']."'".',';
	}
	$product_group_string=substr($product_group_string,0,-1);
	?>
  </tr>
  <!--tr class="TDHEAD_SUB">
    <td style="width:14%;">&nbsp;</td>
    <?php
	/*for($i=1;$i<=$total_product;$i++)
		echo "<td style=\"width:8%;\" align=\"center\"><b>Booked</b></td>";*/
	?>
  </tr-->
</table>
</div><br /><br />
<table height="35"></table>
<table width="100%" border="1" style="border-collapse:collapse;">
<?php
  $padding=4;
  echo Populate_employee_hierarchy_html($reporting_to,$padding,$sauda_booked_condition,$value,$start_date,$end_date,$product_group_string);
  /*if($glblcnt==1)
  {
	  echo"<tr style=\"font-weight:bold\">
		<td colspan=\"12\" align=\"center\">No records found</td></tr>";
  }*/
echo "<tr style=\"font-weight:bold\">
		<td colspan=\"1\" align=\"center\">Total</td>
	  ";
	$res_product_group = mysqli_query($link,$sql_count_product);
	while($row_product_group = mysqli_fetch_assoc($res_product_group)){
	  	$sql_booked_product_group = "SELECT sum(SD.convert_qty_two) as mt_booked FROM sauda_details SD, product_master PM WHERE SD.sku_code=PM.prod_code $sauda_booked_condition 
	  								AND PM.product_group_code='".$row_product_group[$field_name1]."'";
	   $res_booked_product_group = mysqli_query($link,$sql_booked_product_group);
	   $row_booked_product_group = mysqli_fetch_assoc($res_booked_product_group);
	   $quantity_booked = $row_booked_product_group['mt_booked']; 
		echo "<td align=\"right\" style=\"width:$td_width%;\"><b>$quantity_booked</b></td>";
	}
echo "</tr></table><br /><br /><br /><br />";
/*else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}*/
function Populate_employee_hierarchy_html($emp_code,$padding,$sauda_booked_condition,$value,$start_date,$end_date,$product_group_string)
{
	$emphierarchyhtml = '';
    $emphierarchyhtmlstring= employee_hierarchy_details_html($emp_code, $emphierarchyhtml,$padding,$sauda_booked_condition,$value,$start_date,$end_date,$product_group_string);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_details_html($emp_code,&$emphierarchyhtml,$padding,$sauda_booked_condition,$value,$start_date,$end_date,$product_group_string){
   //$emp_hierarchy_condition_top=return_employee_hierarchy($emp_code);
  /*$sqlemphierarchy="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,sauda_details_bkup SD  WHERE EM.reporting_to='".$emp_code."' AND  
   					substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition_top.") $sauda_booked_condition ORDER BY EM.emp_name ASC";*/
   $sqlemphierarchy="SELECT emp_code,emp_name FROM employee_master WHERE reporting_to='".$emp_code."' ORDER BY emp_name ASC";					
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
			 $emp_hierarchy_condition=return_employee_hierarchy($rowemphierarchy['emp_code']);
			 $sqlchkqty="SELECT sum(SD.convert_qty_two) AS qty_check FROM employee_master EM,sauda_details SD 
			 			WHERE substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition.") $sauda_booked_condition";
			 $rschkqty=mysqli_query($link,$sqlchkqty);
			 $rowchkqty=mysqli_fetch_assoc($rschkqty);
			 $qty_check=$rowchkqty['qty_check'];
			 if($qty_check >0 )
			 {
				 $emp_name=$rowemphierarchy['emp_name'];
				 $emphierarchyhtml="<tr>";
				 $emphierarchyhtml.="<td  align=\"left\"  bgcolor=\"$color\" style=\"width:20%;padding-left:".$padding."px\">".$count.'.'.$emp_name."</td>";
				$sql_product = "SELECT PGM.product_group_code,PGM.product_group_name FROM product_group_master PGM WHERE 
				PGM.product_group_code IN(".$product_group_string.") ORDER BY PGM.product_group_name ASC ";
				 $res_product = mysqli_query($link,$sql_product);
				  while($row_product = mysqli_fetch_assoc($res_product))
				  {
					  $sql_quantity_booked = "SELECT sum(SD.convert_qty_two) as mt_booked,PM.product_group_code FROM sauda_details SD, product_master PM 
									WHERE substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition.")
									$sauda_booked_condition AND SD.sku_code=PM.prod_code AND PM.product_group_code='".$row_product['product_group_code']."'";
					   $res_quantity_booked = mysqli_query($link,$sql_quantity_booked);
					   $row_quantity_booked = mysqli_fetch_assoc($res_quantity_booked);
					 
					   $product_group_code=$row_quantity_booked['product_group_code'];
					   if($row_quantity_booked['mt_booked'] != '')
						$quantity_booked = $row_quantity_booked['mt_booked']; 
					   else
						$quantity_booked = 0;
				
					   if($quantity_booked != '')
					   {
						$emphierarchyhtml.="<td style=\"width:$td_width_%;\" align=\"right\" ><a href=\"#\" style=\"color:blue;\" onclick=\"show_customer('$row_product[product_group_code]','$rowemphierarchy[emp_code]','$rowemphierarchy[emp_name]','$row_product[product_group_name]',
						'$value','$start_date','$end_date');\">".$quantity_booked."</a></td>";
					   }
					   else
						$emphierarchyhtml.="<td style=\"width:$td_width_%;\" align=\"right\">".$quantity_booked."</td>";
			 		}
			  echo $emphierarchyhtml.="</tr>";
			  employee_hierarchy_details_html($rowemphierarchy['emp_code'],$emphierarchyhtml,$padding,$sauda_booked_condition,$value,$start_date,$end_date,$product_group_string);
			  $count++;
			}
		}
	}
}
mysqli_close($link);
?>