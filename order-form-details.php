<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM order_form_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowsorderformdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<order_form_id><![CDATA['.mb_convert_encoding($rowsorderformdetails['order_form_id'], 'UTF-8', 'UTF-8').']]></order_form_id>
							<user_id><![CDATA['.mb_convert_encoding($rowsorderformdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<credit_limit><![CDATA['.mb_convert_encoding($rowsorderformdetails['credit_limit'], 'UTF-8', 'UTF-8').']]></credit_limit>
							<cl_stk><![CDATA['.mb_convert_encoding($rowsorderformdetails['cl_stk'], 'UTF-8', 'UTF-8').']]></cl_stk>
							<mrp_input_dropdown><![CDATA['.mb_convert_encoding($rowsorderformdetails['mrp_input_dropdown'], 'UTF-8', 'UTF-8').']]></mrp_input_dropdown>
							<mrp><![CDATA['.mb_convert_encoding($rowsorderformdetails['mrp'], 'UTF-8', 'UTF-8').']]></mrp>
							<TD><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD'], 'UTF-8', 'UTF-8').']]></TD>
							<TD_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_type'], 'UTF-8', 'UTF-8').']]></TD_type>
							<sale_rate><![CDATA['.mb_convert_encoding($rowsorderformdetails['sale_rate'], 'UTF-8', 'UTF-8').']]></sale_rate>
							<sale_rate_input_dropdown><![CDATA['.mb_convert_encoding($rowsorderformdetails['sale_rate_input_dropdown'], 'UTF-8', 'UTF-8').']]></sale_rate_input_dropdown>
							<add_customer><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer'], 'UTF-8', 'UTF-8').']]></add_customer>
							<tagged_customer_for_business_prospect><![CDATA['.mb_convert_encoding($rowsorderformdetails['tagged_customer_for_business_prospect'], 'UTF-8', 'UTF-8').']]></tagged_customer_for_business_prospect>
							<attached_printer><![CDATA['.mb_convert_encoding($rowsorderformdetails['attached_printer'], 'UTF-8', 'UTF-8').']]></attached_printer>
						<printer_mandatory><![CDATA['.mb_convert_encoding($rowsorderformdetails['printer_mandatory'], 'UTF-8', 'UTF-8').']]></printer_mandatory>
						<payment_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['payment_type'], 'UTF-8', 'UTF-8').']]></payment_type>
						<sale><![CDATA['.mb_convert_encoding($rowsorderformdetails['sale'], 'UTF-8', 'UTF-8').']]></sale>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
