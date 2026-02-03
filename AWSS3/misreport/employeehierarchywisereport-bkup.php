<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");

function main()
{
date_default_timezone_set("Asia/Kolkata"); 
$today = date('Y-m-d');
$today = str_replace("-","",$today);

if($_SESSION['admin_login']=="admin"){
		$reporting_to='';
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND SUBSTRING(OH.order_no,2,5) IN('.$emp_hierarchy.')';
		$reporting_to=$_SESSION['admin_login'];
	}
?>
<table width="80%" border="1" style="border-collapse:collapse;" align="center">
  <tr>
  	<td colspan="8" class="TDHEAD" align="center"><b>Employee Hierarchy Wise Report</b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center" style="width:10%;"><b>Vertical Value</b></td>
    <td align="center" style="width:42%;"><b>Employee Name</b></td>
    <td align="center" style="width:16%;" colspan="2"><b>TODAY</b></td>
    <td align="center" style="width:16%;" colspan="2"><b>MTD</b></td>
    <td align="center" style="width:16%;" colspan="2"><b>YTD</b></td>
  </tr>
   <tr class="TDHEAD_SUB">
  	<td align="center" style="width:10%;"></td>
    <td align="center" style="width:42%;"></td>
    <td align="center" style="width:8%;"><b>Primary</b></td>
    <td align="center" style="width:8%;"><b>Secondary</b></td>
    <td align="center" style="width:8%;"><b>Primary</b></td>
    <td align="center" style="width:8%;"><b>Secondary</b></td>
    <td align="center" style="width:8%;"><b>Primary</b></td>
    <td align="center" style="width:8%;"><b>Secondary</b></td>
  </tr>
<?php
  $padding=4;
  echo Populate_employee_hierarchy_html($reporting_to,$padding);
	echo "<tr style=\"font-weight:bold\">
			<td colspan=\"2\" align=\"center\">Total</td>";
			/*$sql_total_qty_today = "SELECT sum(qty) as total_booked_today FROM vertical_branch_employeewise_details 
								WHERE operation_date=CURDATE()".$emp_hierarchy_condition;
		    $res_total_qty_today = mysql_query($sql_total_qty_today);
		    $row_total_qty_today = mysql_fetch_array($res_total_qty_today);
		    $total_booked_today = $row_total_qty_today['total_booked_today'];
			
			$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
 			$mtddate = explode("-",$curdateserver);
			$year = $mtddate[0];
			$month = $mtddate[1];
			$sql_total_qty_MTD = "SELECT sum(qty) as total_booked_MTD FROM vertical_branch_employeewise_details WHERE 
							YEAR(operation_date)=".$year." AND MONTH(operation_date)=".$month."".$emp_hierarchy_condition;
		    $res_total_qty_MTD = mysql_query($sql_total_qty_MTD);
		    $row_total_qty_MTD = mysql_fetch_array($res_total_qty_MTD);
		    $total_booked_MTD = $row_total_qty_MTD['total_booked_MTD'];
			
			$sql_total_qty_YTD = "SELECT sum(qty) as total_booked_YTD FROM vertical_branch_employeewise_details WHERE 
							YEAR(operation_date)=".$year."".$emp_hierarchy_condition;
		    $res_total_qty_YTD = mysql_query($sql_total_qty_YTD);
		    $row_total_qty_YTD = mysql_fetch_array($res_total_qty_YTD);
		    $total_booked_YTD = $row_total_qty_YTD['total_booked_YTD'];*/  
			$sql_total_qty_today = "SELECT SUM(CASE WHEN CM.cust_type='D' THEN OD.qty ELSE 0 END) as total_booked_primary_today,
								  SUM(CASE WHEN CM.cust_type='R' THEN OD.qty ELSE 0 END) as total_booked_secondary_today
								   FROM order_header OH,order_details OD,customer_master CM
								  WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND 
								  DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d')=CURDATE()".$emp_hierarchy_condition;
		    $res_total_qty_today = mysql_query($sql_total_qty_today);
		    $row_total_qty_today = mysql_fetch_array($res_total_qty_today);
		    $total_booked_primary_today = $row_total_qty_today['total_booked_primary_today'];
			$total_booked_secondary_today = $row_total_qty_today['total_booked_secondary_today'];
			
			$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
 			$mtddate = explode("-",$curdateserver);
			$year = $mtddate[0];
			$month = $mtddate[1];
			$sql_total_qty_MTD = "SELECT  SUM(CASE WHEN CM.cust_type='D' THEN OD.qty ELSE 0 END) as total_booked_primary_MTD,
								SUM(CASE WHEN CM.cust_type='R' THEN OD.qty ELSE 0 END) as total_booked_secondary_MTD
								 FROM order_header OH,order_details OD,customer_master CM 
								WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND 
							    YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$year." AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$month."".$emp_hierarchy_condition;
		    $res_total_qty_MTD = mysql_query($sql_total_qty_MTD);
		    $row_total_qty_MTD = mysql_fetch_array($res_total_qty_MTD);
		    $total_booked_primary_MTD = $row_total_qty_MTD['total_booked_primary_MTD']; 
			$total_booked_secondary_MTD = $row_total_qty_MTD['total_booked_secondary_MTD'];
			
			$sql_total_qty_YTD = "SELECT SUM(CASE WHEN CM.cust_type='D' THEN OD.qty ELSE 0 END) as total_booked_primary_YTD,
								SUM(CASE WHEN CM.cust_type='R' THEN OD.qty ELSE 0 END) as total_booked_secondary_YTD
								 FROM order_header OH,order_details OD,customer_master CM 
								WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND 
							   YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$year."".$emp_hierarchy_condition;
		    $res_total_qty_YTD = mysql_query($sql_total_qty_YTD);
		    $row_total_qty_YTD = mysql_fetch_array($res_total_qty_YTD);
		    $total_booked_primary_YTD = $row_total_qty_YTD['total_booked_primary_YTD'];
			$total_booked_secondary_YTD = $row_total_qty_YTD['total_booked_secondary_YTD'];


			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_primary_today."</b></td>";
			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_secondary_today."</b></td>";
			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_primary_MTD."</b></td>";
			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_secondary_MTD."</b></td>";
			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_primary_YTD."</b></td>";
			echo "<td align=\"right\" style=\"width:8%;\"><b>".$total_booked_secondary_YTD."</b></td>";
			echo "</tr></table>";
			  //echo 'euryerueyreureuryereuryeuryeur'.$grandtotal_emp_booked_secondary_today;

/*else
{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}*/
}

function Populate_employee_hierarchy_html($emp_code,$padding)
{
	$emphierarchyhtml = '';
	$vertical_value_array=array();
    $emphierarchyhtmlstring= employee_hierarchy_details_html($emp_code, $emphierarchyhtml,$padding,$vertical_value_array);
	return $emphierarchyhtmlstring;
}
function employee_hierarchy_details_html($emp_code,&$emphierarchyhtml,$padding,&$vertical_value_array){
   //$emp_hierarchy_condition_top=return_employee_hierarchy($emp_code);
  /*$sqlemphierarchy="SELECT EM.emp_code,EM.emp_name FROM employee_master EM,sauda_details_bkup SD  WHERE EM.reporting_to='".$emp_code."' AND  
   					substring(SD.sauda_no,3,5) IN(".$emp_hierarchy_condition_top.") $sauda_booked_condition ORDER BY EM.emp_name ASC";*/
   $sqlemphierarchy="SELECT emp_code,emp_name,vertical_value FROM employee_master WHERE 
   					reporting_to='".$emp_code."'  ORDER BY vertical_value,emp_name ASC";					
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		$padding=$padding+10;
		if($padding<'20')         $color='#7FAAFF'; 
		else if($padding<'30') 	$color='#D4FF7F';
		else if($padding<'40')    $color='#FFAA7F';
		else if($padding<'50')    $color='#FFFFAA';
		
		if($padding<'30' && $padding>'20')         $colorROW='D4FF7F';
		else 					  $colorROW='';

		$count=1;
		
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			$emp_hierarchy_condition=return_employee_hierarchy($rowemphierarchy['emp_code']);
			 
			$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
 			$mtddateemp = explode("-",$curdateserver);
			$yearemp = $mtddateemp[0];
			$monthemp = $mtddateemp[1];
			$sql_emp_total_qty_today = "SELECT SUM(CASE WHEN CM.cust_type='D' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d')=CURDATE() THEN OD.qty ELSE 0 END) as total_emp_booked_primary_today,
								SUM(CASE WHEN CM.cust_type='R' AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d')=CURDATE() THEN OD.qty ELSE 0 END) as total_emp_booked_secondary_today,
								 SUM(CASE WHEN CM.cust_type='D' AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$yearemp." AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$monthemp." THEN OD.qty ELSE 0 END) as total_emp_booked_primary_MTD,
								SUM(CASE WHEN CM.cust_type='R' AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$yearemp." AND MONTH(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$monthemp." THEN OD.qty ELSE 0 END) as total_emp_booked_secondary_MTD,
								SUM(CASE WHEN CM.cust_type='D' AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$yearemp." THEN OD.qty ELSE 0 END) as total_emp_booked_primary_YTD,
								SUM(CASE WHEN CM.cust_type='R' AND YEAR(DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y%-%m-%d'))=".$yearemp." THEN OD.qty ELSE 0 END) as total_emp_booked_secondary_YTD
								 FROM order_header OH,order_details OD,customer_master CM 
								WHERE OH.customer_code=CM.customer_code AND OH.order_no=OD.order_no AND SUBSTRING(OH.order_no,2,5) 
								IN(".$emp_hierarchy_condition.")";
		    $res_emp_total_qty_today = mysql_query($sql_emp_total_qty_today);
		    $row_emp_total_qty_today = mysql_fetch_array($res_emp_total_qty_today);
		    $total_emp_booked_primary_today = $row_emp_total_qty_today['total_emp_booked_primary_today'];
			$total_emp_booked_secondary_today = $row_emp_total_qty_today['total_emp_booked_secondary_today'];
		    $total_emp_booked_primary_MTD = $row_emp_total_qty_today['total_emp_booked_primary_MTD'];
			$total_emp_booked_secondary_MTD = $row_emp_total_qty_today['total_emp_booked_secondary_MTD'];
		    $total_emp_booked_primary_YTD = $row_emp_total_qty_today['total_emp_booked_primary_YTD'];
			$total_emp_booked_secondary_YTD = $row_emp_total_qty_today['total_emp_booked_secondary_YTD'];

			 $emp_name=$rowemphierarchy['emp_name'];
			 $vertical_value=$rowemphierarchy['vertical_value'];
			 if($total_emp_booked_primary_today=='') $total_emp_booked_primary_today=0;
			 if($total_emp_booked_secondary_today=='') $total_emp_booked_secondary_today=0;
			 if($total_emp_booked_primary_MTD=='') $total_emp_booked_primary_MTD=0;
			 if($total_emp_booked_secondary_MTD=='') $total_emp_booked_secondary_MTD=0;
			 if($total_emp_booked_primary_YTD=='') $total_emp_booked_primary_YTD=0;
			 if($total_emp_booked_secondary_YTD=='') $total_emp_booked_secondary_YTD=0;	
				
				//$grandtotal_emp_booked_secondary_today=$grandtotal_emp_booked_secondary_today+$total_emp_booked_secondary_today;
				 
				 /*if($total_emp_booked_primary_today >0 || $total_emp_booked_secondary_today >0 ||  $total_emp_booked_primary_MTD >0 ||  $total_emp_booked_secondary_MTD >0 ||  $total_emp_booked_primary_YTD >0 || $total_emp_booked_secondary_YTD >0)
				 {*/
					if($emp_code=='')
					 {
						 $vertical_value='';
					 }
					 else
					 {
						 $vertical_value=$vertical_value;
					 }
					if(!in_array($vertical_value,$vertical_value_array))
					{
						$vertical_value=$vertical_value;
						array_push($vertical_value_array,$vertical_value);
						
					}
					else
					{
						$vertical_value='';
					}
					 
					 $emphierarchyhtml="<tr>";
					 $emphierarchyhtml.="<td style=\"width:10%;\" align=\"left\" >".$vertical_value."</td><td  align=\"left\"  bgcolor=\"$color\" style=\"width:45%;padding-left:".$padding."px\">".$count.'.'.$emp_name."</td>";
					 $emphierarchyhtml.="<td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_primary_today."</td><td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_secondary_today."</td>
					 				<td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_primary_MTD."</td><td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_secondary_MTD."</td>
										<td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_primary_YTD."</td><td style=\"width:8%;\" align=\"right\" bgcolor=\"$colorROW\">".$total_emp_booked_secondary_YTD."</td>";
				  echo $emphierarchyhtml.="</tr>";
				  employee_hierarchy_details_html($rowemphierarchy['emp_code'],$emphierarchyhtml,$padding,$vertical_value_array);
				  $count++;
				 //}
			}
		}
	}
?>