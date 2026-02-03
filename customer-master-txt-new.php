<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$first_login=$_REQUEST['first_login'];
$is_master=$_REQUEST['is_master'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND c1.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND c1.emp_code='".$emp_code."'";
}
$sqlroute="SELECT route_code FROM route_master WHERE 1";
$rsroute=mysqli_query($link,$sqlroute);
$countroute=mysqli_num_rows($rsroute);
if(($first_login=='yes' && $emp_code!='C0007') ||($first_login=='no' && $emp_code!='C0007' && $is_master=='Y'))
{
	if($countroute>0){
	   $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
	   				c1.TD 
	   				FROM customer_master c1,route_master r, employee_master em WHERE c1.acedns='Y' ".$emp_hierarchy_condition." 
					OR ( r.emp_code = em.emp_code AND 	c1.route_code = r.route_code AND em.emp_code ='".$emp_code."' AND c1.acedns='Y') 
					ORDER BY c1.customer_name ASC";
	}
	else
	{
		$sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,c1.TD 
		FROM customer_master c1, employee_master em WHERE c1.acedns='Y' ".$emp_hierarchy_condition." ORDER BY c1.customer_name ASC";
	}
}
else if($first_login=='no' && $emp_code!='C0007' && $is_master=='N')
{
	$sqlquery="SELECT DISTINCT c1.* FROM customer_master_temp c1 WHERE 1 ".$emp_hierarchy_condition." ORDER BY c1.customer_name ASC";
}
else if($emp_code=='C0007')
{
	$sqlquery="SELECT DISTINCT * FROM customer_master WHERE 1 ORDER BY customer_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'9';
	if($count>0){
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<customer_code><![CDATA['.mb_convert_encoding($rowsemp['customer_code'], 'UTF-8', 'UTF-8').']]></customer_code>
						<customer_name><![CDATA['.mb_convert_encoding(stripslashes($rowsemp['customer_name']), 'UTF-8', 'UTF-8').']]></customer_name>
						<route_code><![CDATA['.mb_convert_encoding($rowsemp['route_code'], 'UTF-8', 'UTF-8').']]></route_code>
						<emp_code><![CDATA['.mb_convert_encoding($rowsemp['emp_code'], 'UTF-8', 'UTF-8').']]></emp_code>
						<current_balance><![CDATA['.mb_convert_encoding($rowsemp['current_balance'], 'UTF-8', 'UTF-8').']]></current_balance>
						<credit_limit><![CDATA['.mb_convert_encoding($rowsemp['credit_limit'], 'UTF-8', 'UTF-8').']]></credit_limit>
						<acedns><![CDATA['.mb_convert_encoding($rowsemp['acedns'], 'UTF-8', 'UTF-8').']]></acedns>
						<black_list><![CDATA['.mb_convert_encoding($rowsemp['black_list'], 'UTF-8', 'UTF-8').']]></black_list>';
			$contents.="</data>";*/
			
			$contents  = (($rowsemp['customer_code']!='')?$rowsemp['customer_code']: ' ')."^";
			$contents  .= (($rowsemp['customer_name']!='')?$rowsemp['customer_name']: ' ')."^";
			$contents  .= (($rowsemp['route_code']!='')?$rowsemp['route_code']: ' ')."^";
			$contents  .= (($rowsemp['emp_code']!='')?$rowsemp['emp_code']: ' ')."^";
			$contents  .= (($rowsemp['current_balance']!='')?$rowsemp['current_balance']: ' ')."^";
			$contents  .= (($rowsemp['credit_limit']!='')?$rowsemp['credit_limit']: ' ')."^";
			$contents  .= (($rowsemp['acedns']!='')?$rowsemp['acedns']: ' ')."^";
			$contents  .= (($rowsemp['black_list']!='')?$rowsemp['black_list']: ' ')."^";
			$contents  .= (($rowsemp['TD']!='')?$rowsemp['TD']: '0');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 		
?>
