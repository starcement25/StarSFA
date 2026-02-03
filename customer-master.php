<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$first_login=$_REQUEST['first_login'];

$sqlroute="SELECT route_code FROM route_master WHERE 1";
$rsroute=mysqli_query($link,$sqlroute);
$countroute=mysqli_num_rows($rsroute);
if($first_login=='yes' && $emp_code!='C0007')
{
	if($countroute>0){
	   $sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns FROM customer_master c1,route_master r, employee_master em WHERE c1.acedns='Y' AND c1.emp_code ='".$emp_code."' OR ( r.emp_code = em.emp_code AND c1.route_code = r.route_code AND em.emp_code ='".$emp_code."' AND c1.acedns='Y') ORDER BY c1.customer_name ASC";
	}
	else
	{
		$sqlquery="SELECT DISTINCT c1.customer_code, c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns FROM customer_master c1, employee_master em WHERE c1.acedns='Y' AND c1.emp_code ='".$emp_code."' ORDER BY c1.customer_name ASC";
	}
}
else if($first_login=='no' && $emp_code!='C0007')
{
	$sqlquery="SELECT DISTINCT * FROM customer_master_temp WHERE emp_code ='".$emp_code."' ORDER BY customer_name ASC";
}
else if($emp_code=='C0007')
{
	$sqlquery="SELECT DISTINCT * FROM customer_master WHERE 1 ORDER BY customer_name ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$contents.="<data>";
			$contents .='<customer_code><![CDATA['.mb_convert_encoding($rowsemp['customer_code'], 'UTF-8', 'UTF-8').']]></customer_code>
						<customer_name><![CDATA['.mb_convert_encoding(stripslashes($rowsemp['customer_name']), 'UTF-8', 'UTF-8').']]></customer_name>
						<route_code><![CDATA['.mb_convert_encoding($rowsemp['route_code'], 'UTF-8', 'UTF-8').']]></route_code>
						<emp_code><![CDATA['.mb_convert_encoding($rowsemp['emp_code'], 'UTF-8', 'UTF-8').']]></emp_code>
						<current_balance><![CDATA['.mb_convert_encoding($rowsemp['current_balance'], 'UTF-8', 'UTF-8').']]></current_balance>
						<credit_limit><![CDATA['.mb_convert_encoding($rowsemp['credit_limit'], 'UTF-8', 'UTF-8').']]></credit_limit>
						<acedns><![CDATA['.mb_convert_encoding($rowsemp['acedns'], 'UTF-8', 'UTF-8').']]></acedns>
						<black_list><![CDATA['.mb_convert_encoding($rowsemp['black_list'], 'UTF-8', 'UTF-8').']]></black_list>';
			$contents.="</data>";	
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
