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
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
		$reporting_to=$_SESSION['admin_login'];
	}
?>
<table width="70%" border="1" style="border-collapse:collapse;" align="center">
  <tr>
  	<td colspan="6" class="TDHEAD" align="center"><b>Zone State Wise Report</b></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="center" style="width:18%;"><b>Vertical Value</b></td>
    <td align="center" style="width:18%;"><b>Zone</b></td>
    <td align="center" style="width:18%;"><b>State</b></td>
    <td align="center" style="width:15%;"><b>TODAY</b></td>
    <td align="center" style="width:15%;"><b>MTD</b></td>
    <td align="center" style="width:15%;"><b>YTD</b></td>
  </tr>
<?php
	$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
	$mtddate = explode("-",$curdateserver);
	$year = $mtddate[0];
	$month = $mtddate[1];
  $vertical_value_array=array();
  $sqlzonestatehierarchy="SELECT EM.zone,EM.state,EM.vertical_value,
   						(CASE WHEN YEAR(VBED.operation_date)=".$year." AND MONTH(VBED.operation_date)=".$month." THEN SUM(VBED.qty) ELSE 0 END) AS total_qty_MTD,
						(CASE WHEN YEAR(VBED.operation_date)=".$year." THEN SUM(VBED.qty) ELSE 0 END) AS total_qty_YTD FROM `employee_master` EM,vertical_branch_employeewise_details VBED WHERE EM.emp_code=VBED.emp_code AND EM.vertical_value=VBED.vertical_value 
   					 GROUP BY EM.state,EM.zone,EM.vertical_value ORDER BY EM.vertical_value,EM.zone,EM.state ";					
   $rszonestatehierarchy=mysql_query($sqlzonestatehierarchy);
   $cntzonestatehierarchy=mysql_num_rows($rszonestatehierarchy);
   $previous_value_verticle='';
   while($rowrszonestatehierarchy=mysql_fetch_array($rszonestatehierarchy))
	{
          $vertical_value=$rowrszonestatehierarchy['vertical_value'];
		  $zone=$rowrszonestatehierarchy['zone'];
		  $state=$rowrszonestatehierarchy['state'];
		  $qty_MTD=$rowrszonestatehierarchy['total_qty_MTD'];
		  $qty_YTD=$rowrszonestatehierarchy['total_qty_YTD'];
		  
		  $sqltotalqtytoday="SELECT  SUM(VBED.qty) AS qty_today FROM `employee_master` EM,vertical_branch_employeewise_details VBED WHERE EM.emp_code=VBED.emp_code AND EM.vertical_value=VBED.vertical_value 
   					      AND EM.state='".$state."' AND EM.zone='".$zone."' AND VBED.vertical_value='".$vertical_value."' AND VBED.operation_date=CURDATE() ";					
   		   $rstotalqtytoday=mysql_query($sqltotalqtytoday);
		  $rowtotalqtytoday=mysql_fetch_array($rstotalqtytoday);
		  $qty_TODAY=$rowtotalqtytoday['qty_today'];

		 if($qty_TODAY=='') $qty_TODAY=0;
		 
			if($previous_value_verticle!=$vertical_value && $previous_value_verticle!='')
			{
				echo "<tr><td colspan=\"3\" style=\"font-weight:bold\" align=\"center\" bgcolor=\"#FFFFAA\"><b>Sub Total</b></td>";
				echo "<td style=\"text-align:right;font-weight:bold;\" bgcolor=\"#FFFFAA\">${total_subqty_TODAY.$previous_value_verticle}</td>";
				echo "<td style=\"text-align:right;font-weight:bold;\" bgcolor=\"#FFFFAA\">${total_subqty_MTD.$previous_value_verticle}</td>";
				echo "<td style=\"text-align:right;font-weight:bold;\" bgcolor=\"#FFFFAA\">${total_subqty_YTD.$previous_value_verticle}</td>";
				echo "</tr>";
			}

		  
		 ${total_subqty_TODAY.$vertical_value}=${total_subqty_TODAY.$vertical_value}+ $qty_TODAY;
		 ${total_subqty_MTD.$vertical_value}=${total_subqty_MTD.$vertical_value}+ $qty_MTD;
		 ${total_subqty_YTD.$vertical_value}=${total_subqty_YTD.$vertical_value}+ $qty_YTD;
		 
		  $total_qty_TODAY=$total_qty_TODAY+$qty_TODAY; 
		  $total_qty_MTD=$total_qty_MTD+$qty_MTD;
		  $total_qty_YTD=$total_qty_YTD+$qty_YTD;
		  
			
		 $previous_value_verticle=$vertical_value;
		 
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
		  $emphierarchyhtml.="<td style=\"width:18%;\" align=\"left\" >".$vertical_value."</td><td  align=\"left\"   style=\"width:18%;\">".$zone."</td>";
		  $emphierarchyhtml.="<td style=\"width:18%;\" align=\"left\" >". $state."</td><td style=\"width:15%;\" align=\"right\" >". $qty_TODAY."</td>
		  					<td style=\"width:15%;\" align=\"right\" >". $qty_MTD."</td><td style=\"width:15%;\" align=\"right\" >". $qty_YTD."</td>";
	     echo $emphierarchyhtml.="</tr>";
	}
	echo "<tr><td colspan=\"3\" style=\"font-weight:bold\" align=\"center\" bgcolor=\"#FFFFAA\"><b>Sub Total</b></td>";
	echo "<td style=\"text-align:right;\" bgcolor=\"#FFFFAA\">${total_subqty_TODAY.$previous_value_verticle}</td>";
				echo "<td style=\"text-align:right;\" bgcolor=\"#FFFFAA\">${total_subqty_MTD.$previous_value_verticle}</td>";
				echo "<td style=\"text-align:right;\" bgcolor=\"#FFFFAA\">${total_subqty_YTD.$previous_value_verticle}</td>";
				echo "</tr>";
	echo "<tr style=\"font-weight:bold\" bgcolor=\"#FFAA7F\"><td colspan=\"3\" align=\"center\">Grand Total</td>";
	echo "<td align=\"right\" style=\"width:15%;\" bgcolor=\"#FFAA7F\"><b>$total_qty_TODAY</b></td>";
	echo "<td align=\"right\" style=\"width:15%;\" bgcolor=\"#FFAA7F\"><b>$total_qty_MTD</b></td>";
	echo "<td align=\"right\" style=\"width:15%;\" bgcolor=\"#FFAA7F\"><b>$total_qty_YTD</b></td>";
	echo "</tr></table>";
}
?>