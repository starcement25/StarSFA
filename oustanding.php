<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
if($emp_code!='C0007'){
	$sqlquery="SELECT DISTINCT c.customer_code,c.recid, cc.customer_name, c.invoice_id, c.date, c.invoice_amount, c.due_amount 
			FROM outstanding c, customer_master cc WHERE 
			c.customer_code = cc.customer_code AND cc.emp_code = '".$emp_code."' 
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

	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowoutstanding = mysqli_fetch_assoc($result))
		{
			$contents.="<data>";
			$contents .='<customer_code><![CDATA['.mb_convert_encoding($rowoutstanding['customer_code'], 'UTF-8', 'UTF-8').']]></customer_code>
						<customer_name><![CDATA['.mb_convert_encoding($rowoutstanding['customer_name'], 'UTF-8', 'UTF-8').']]></customer_name>
						<invoice_id><![CDATA['.mb_convert_encoding($rowoutstanding['invoice_id'], 'UTF-8', 'UTF-8').']]></invoice_id>
						<recid><![CDATA['.mb_convert_encoding($rowoutstanding['recid'], 'UTF-8', 'UTF-8').']]></recid>
						<date><![CDATA['.mb_convert_encoding($rowoutstanding['date'], 'UTF-8', 'UTF-8').']]></date>
						<invoice_amount><![CDATA['.mb_convert_encoding($rowoutstanding['invoice_amount'], 'UTF-8', 'UTF-8').']]></invoice_amount>
						<due_amount><![CDATA['.mb_convert_encoding($rowoutstanding['due_amount'], 'UTF-8', 'UTF-8').']]></due_amount>';
			$contents.="</data>";	
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
