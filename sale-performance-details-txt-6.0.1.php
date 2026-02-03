<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];


if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$employee_hierarchy=str_replace("'","",$employee_hierarchy);
	$employee_hierarchy_value_array=explode(',',$employee_hierarchy);
	$condition_one=" AND (";
	$condition_two='';
	foreach($employee_hierarchy_value_array as $emp_hierarchy_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_hierarchy_values."',emp_code) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";

	$emp_hierarchy_condition=$condition_one;
	$employeeArray=explode(',',$employee_hierarchy);
}
else
{
	$employeeArray=array();
	$emp_hierarchy_condition=" AND FIND_IN_SET('".$emp_code."',emp_code)";
	array_push($employeeArray,$emp_code);
}

if($emp_code!='C0007'){
	$sqlquery="SELECT customer_code,customer_name,emp_code,emp_name,product_group_code,prod_code,prod_desc,YTD_sale,MTD_sale FROM sale_performance_details 
	WHERE 1 ".$emp_hierarchy_condition."";
}
else
{
	$sqlquery="SELECT customer_code,customer_name,emp_code,emp_name,product_group_code,prod_code,prod_desc,YTD_sale,MTD_sale FROM sale_performance_details  
				WHERE 1 ";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
$totcount=0;	
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		
		while($rowsaleperformance = mysqli_fetch_assoc($result))
		{
			$emp_code_val=$rowsaleperformance['emp_code'];
			$emp_code_val_array=explode(',',$emp_code_val);
			if(count($emp_code_val_array) >1)
			{
				$emp_name_val=$rowsaleperformance['emp_name'];
				$emp_name_val_array=explode(',',$emp_name_val);
				for($k=0;$k<count($emp_code_val_array);$k++)
				{
					if(in_array($emp_code_val_array[$k],$employeeArray))
					{
						$contents  = (($rowsaleperformance['customer_code']!='')?$rowsaleperformance['customer_code']: ' ')."^";
						$contents  .= (($rowsaleperformance['customer_name']!='')?$rowsaleperformance['customer_name']: ' ')."^";
						$contents  .= (($emp_code_val_array[$k]!='')?$emp_code_val_array[$k]: ' ')."^";
						$contents  .= (($emp_name_val_array[$k]!='')?$emp_name_val_array[$k]: ' ')."^";
						$contents  .= (($rowsaleperformance['product_group_code']!='')?$rowsaleperformance['product_group_code']: ' ')."^";
						$contents  .= (($rowsaleperformance['prod_code']!='')?$rowsaleperformance['prod_code']: ' ')."^";
						$contents  .= (($rowsaleperformance['prod_desc']!='')?$rowsaleperformance['prod_desc']: ' ')."^";
						$contents  .= (($rowsaleperformance['YTD_sale']!='')?$rowsaleperformance['YTD_sale']: ' ')."^";
						$contents  .= (($rowsaleperformance['MTD_sale']!='')?$rowsaleperformance['MTD_sale']: ' ');
						$linecontents  .= $contents."\n";
						$totcount++;
					}
				}
			}
			else
			{
				$contents  = (($rowsaleperformance['customer_code']!='')?$rowsaleperformance['customer_code']: ' ')."^";
				$contents  .= (($rowsaleperformance['customer_name']!='')?$rowsaleperformance['customer_name']: ' ')."^";
				$contents  .= (($rowsaleperformance['emp_code']!='')?$rowsaleperformance['emp_code']: ' ')."^";
				$contents  .= (($rowsaleperformance['emp_name']!='')?$rowsaleperformance['emp_name']: ' ')."^";
				$contents  .= (($rowsaleperformance['product_group_code']!='')?$rowsaleperformance['product_group_code']: ' ')."^";
				$contents  .= (($rowsaleperformance['prod_code']!='')?$rowsaleperformance['prod_code']: ' ')."^";
				$contents  .= (($rowsaleperformance['prod_desc']!='')?$rowsaleperformance['prod_desc']: ' ')."^";
				$contents  .= (($rowsaleperformance['YTD_sale']!='')?$rowsaleperformance['YTD_sale']: ' ')."^";
				$contents  .= (($rowsaleperformance['MTD_sale']!='')?$rowsaleperformance['MTD_sale']: ' ');
				$linecontents  .= $contents."\n";
				$totcount++;	
			}
		}
		$contentsrowcolumn=$totcount.'¥'.'9';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=sale_performance.txt");
	print "$datacontents";	
	mysqli_close($link);	
?>
