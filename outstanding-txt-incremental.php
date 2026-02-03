<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND cc.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND cc.emp_code='".$emp_code."'";
}
if($emp_code!='C0007'){
	$sqlquery="SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount 
			FROM outstanding c, customer_master cc WHERE 
			c.customer_code = cc.customer_code ".$emp_hierarchy_condition." 
			ORDER BY c.date ASC";
}
else
{
	$sqlquery="SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount 
				FROM outstanding c, customer_master cc WHERE 
				c.customer_code = cc.customer_code 
				ORDER BY c.date ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn=$count.'¥'.'7';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowoutstanding = mysqli_fetch_assoc($result))
		{
			/*$contents.="<data>";
			$contents .='<customer_code><![CDATA['.mb_convert_encoding($rowoutstanding['customer_code'], 'UTF-8', 'UTF-8').']]></customer_code>
						<customer_name><![CDATA['.mb_convert_encoding($rowoutstanding['customer_name'], 'UTF-8', 'UTF-8').']]></customer_name>
						<invoice_id><![CDATA['.mb_convert_encoding($rowoutstanding['invoice_id'], 'UTF-8', 'UTF-8').']]></invoice_id>
						<recid><![CDATA['.mb_convert_encoding($rowoutstanding['recid'], 'UTF-8', 'UTF-8').']]></recid>
						<date><![CDATA['.mb_convert_encoding($rowoutstanding['date'], 'UTF-8', 'UTF-8').']]></date>
						<invoice_amount><![CDATA['.mb_convert_encoding($rowoutstanding['invoice_amount'], 'UTF-8', 'UTF-8').']]></invoice_amount>
						<due_amount><![CDATA['.mb_convert_encoding($rowoutstanding['due_amount'], 'UTF-8', 'UTF-8').']]></due_amount>';
			$contents.="</data>";*/
			
			$contents  = (($rowoutstanding['customer_code']!='')?$rowoutstanding['customer_code']: ' ')."^";
			$contents  .= (($rowoutstanding['customer_name']!='')?$rowoutstanding['customer_name']: ' ')."^";
			$contents  .= (($rowoutstanding['invoice_id']!='')?$rowoutstanding['invoice_id']: ' ')."^";
			$contents  .= (($rowoutstanding['recid']!='')?$rowoutstanding['recid']: ' ')."^";
			$contents  .= (($rowoutstanding['date']!='')?$rowoutstanding['date']: ' ')."^";
			$contents  .= (($rowoutstanding['invoice_amount']!='')?$rowoutstanding['invoice_amount']: ' ')."^";
			$contents  .= (($rowoutstanding['due_amount']!='')?$rowoutstanding['due_amount']: ' ');
			$linecontents  .= $contents."\n";	
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=outstanding.txt");
	print "$datacontents";		
?>
