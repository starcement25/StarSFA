<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT * FROM order_form_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
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
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
							<tag_distributor><![CDATA['.mb_convert_encoding($rowsorderformdetails['tagged_distributor_for_order'], 'UTF-8', 'UTF-8').']]></tag_distributor>
							<sale><![CDATA['.mb_convert_encoding($rowsorderformdetails['sale'], 'UTF-8', 'UTF-8').']]></sale>
							<instruction><![CDATA['.mb_convert_encoding($rowsorderformdetails['instruction'], 'UTF-8', 'UTF-8').']]></instruction>
							<VAT><![CDATA['.mb_convert_encoding($rowsorderformdetails['VAT'], 'UTF-8', 'UTF-8').']]></VAT>
							<VAT_details><![CDATA['.mb_convert_encoding($rowsorderformdetails['VAT_details'], 'UTF-8', 'UTF-8').']]></VAT_details>
							<branch_rds_transfer><![CDATA['.mb_convert_encoding($rowsorderformdetails['branch_rds_transfer'], 'UTF-8', 'UTF-8').']]></branch_rds_transfer>
							<amount><![CDATA['.mb_convert_encoding($rowsorderformdetails['amount'], 'UTF-8', 'UTF-8').']]></amount>
							<VAT_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['VAT_type'], 'UTF-8', 'UTF-8').']]></VAT_type>
							<TD_calc><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_calc'], 'UTF-8', 'UTF-8').']]></TD_calc>
							<TD_trans_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_trans_type'], 'UTF-8', 'UTF-8').']]></TD_trans_type>
							<VAT_calc_on><![CDATA['.mb_convert_encoding($rowsorderformdetails['VAT_calc_on'], 'UTF-8', 'UTF-8').']]></VAT_calc_on>
							<TD_validation><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_validation'], 'UTF-8', 'UTF-8').']]></TD_validation>
							<TD_calc_basedon><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_calc_basedon'], 'UTF-8', 'UTF-8').']]></TD_calc_basedon>
							<premium><![CDATA['.mb_convert_encoding($rowsorderformdetails['premium'], 'UTF-8', 'UTF-8').']]></premium>
							<previous_order><![CDATA['.mb_convert_encoding($rowsorderformdetails['previous_order'], 'UTF-8', 'UTF-8').']]></previous_order>
							<add_customer_OTP><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_OTP'], 'UTF-8', 'UTF-8').']]></add_customer_OTP>
							<customer_information_check><![CDATA['.mb_convert_encoding($rowsorderformdetails['customer_information_check'], 'UTF-8', 'UTF-8').']]></customer_information_check>
							<add_customer_route_creation><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_route_creation'], 'UTF-8', 'UTF-8').']]></add_customer_route_creation>
							<order_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['order_type'], 'UTF-8', 'UTF-8').']]></order_type>
							<freight_component><![CDATA['.mb_convert_encoding($rowsorderformdetails['freight_component'], 'UTF-8', 'UTF-8').']]></freight_component>
							<tax_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['tax_type'], 'UTF-8', 'UTF-8').']]></tax_type>
							<destination><![CDATA['.mb_convert_encoding($rowsorderformdetails['destination'], 'UTF-8', 'UTF-8').']]></destination>
							<input_screen_normal><![CDATA['.mb_convert_encoding($rowsorderformdetails['input_screen_normal'], 'UTF-8', 'UTF-8').']]></input_screen_normal>
							<input_screen_special><![CDATA['.mb_convert_encoding($rowsorderformdetails['input_screen_special'], 'UTF-8', 'UTF-8').']]></input_screen_special>
							<add_customer_details><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_details'], 'UTF-8', 'UTF-8').']]></add_customer_details>
							<add_customer_trade_nontrade><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_trade_nontrade'], 'UTF-8', 'UTF-8').']]></add_customer_trade_nontrade>
							<printer_type><![CDATA['.mb_convert_encoding($rowsorderformdetails['printer_type'], 'UTF-8', 'UTF-8').']]></printer_type>
							<printer_menu><![CDATA['.mb_convert_encoding($rowsorderformdetails['printer_menu'], 'UTF-8', 'UTF-8').']]></printer_menu>
							<hint_remarks><![CDATA['.mb_convert_encoding($rowsorderformdetails['hint_remarks'], 'UTF-8', 'UTF-8').']]></hint_remarks>
					<hint_remarks_val><![CDATA['.mb_convert_encoding($rowsorderformdetails['hint_remarks_val'], 'UTF-8', 'UTF-8').']]></hint_remarks_val>
					<add_customer_image_creation><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_image_creation'], 'UTF-8', 'UTF-8').']]></add_customer_image_creation>
					<distributor_route_emp_relation><![CDATA['.mb_convert_encoding($rowsorderformdetails['distributor_route_emp_relation'], 'UTF-8', 'UTF-8').']]></distributor_route_emp_relation>
					<multiple_UOM><![CDATA['.mb_convert_encoding($rowsorderformdetails['multiple_UOM'], 'UTF-8', 'UTF-8').']]></multiple_UOM>
					<input_screen_planwise><![CDATA['.mb_convert_encoding($rowsorderformdetails['input_screen_planwise'], 'UTF-8', 'UTF-8').']]></input_screen_planwise>
          			<input_screen_price_validation><![CDATA['.mb_convert_encoding($rowsorderformdetails['input_screen_price_validation'], 'UTF-8', 'UTF-8').']]></input_screen_price_validation>
					<TD_type_input_dropdown><![CDATA['.mb_convert_encoding($rowsorderformdetails['TD_type_input_dropdown'], 'UTF-8', 'UTF-8').']]></TD_type_input_dropdown>
					<input_screen_planwise_filter1wise><![CDATA['.mb_convert_encoding($rowsorderformdetails['input_screen_planwise_filter1wise'], 'UTF-8', 'UTF-8').']]></input_screen_planwise_filter1wise>
					<sauda_sale_rate_input_dropdown><![CDATA['.mb_convert_encoding($rowsorderformdetails['sauda_sale_rate_input_dropdown'], 'UTF-8', 'UTF-8').']]></sauda_sale_rate_input_dropdown>
					<cust_type_wise_UOM><![CDATA['.mb_convert_encoding($rowsorderformdetails['cust_type_wise_UOM'], 'UTF-8', 'UTF-8').']]></cust_type_wise_UOM>
					<cust_type_wise_UOM_val><![CDATA['.mb_convert_encoding($rowsorderformdetails['cust_type_wise_UOM_val'], 'UTF-8', 'UTF-8').']]></cust_type_wise_UOM_val>
					<visit_sequence><![CDATA['.mb_convert_encoding($rowsorderformdetails['visit_sequence'], 'UTF-8', 'UTF-8').']]></visit_sequence>
					<add_customer_activation><![CDATA['.mb_convert_encoding($rowsorderformdetails['add_customer_activation'], 'UTF-8', 'UTF-8').']]></add_customer_activation>
					<multiple_distributor><![CDATA['.mb_convert_encoding($rowsorderformdetails['multiple_distributor'], 'UTF-8', 'UTF-8').']]></multiple_distributor>
					<GEO_fencing_variance><![CDATA['.mb_convert_encoding($rowsorderformdetails['GEO_fencing_variance'], 'UTF-8', 'UTF-8').']]></GEO_fencing_variance>
					<route_wise_distributor><![CDATA['.mb_convert_encoding($rowsorderformdetails['route_wise_distributor'], 'UTF-8', 'UTF-8').']]></route_wise_distributor>
					<mrp_sale_rate_text><![CDATA['.mb_convert_encoding($rowsorderformdetails['mrp_sale_rate_text'], 'UTF-8', 'UTF-8').']]></mrp_sale_rate_text>
					<order_status_remarks><![CDATA['.mb_convert_encoding($rowsorderformdetails['order_status_remarks'], 'UTF-8', 'UTF-8').']]></order_status_remarks>
					<tagged_distributor_business_prospect><![CDATA['.mb_convert_encoding($rowsorderformdetails['tagged_distributor_business_prospect'], 'UTF-8', 'UTF-8').']]></tagged_distributor_business_prospect>
					<no_order_remarks><![CDATA['.mb_convert_encoding($rowsorderformdetails['no_order_remarks'], 'UTF-8', 'UTF-8').']]></no_order_remarks>
					';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/order-form-details-incremental-7.1.9.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/order-form-details-incremental-6.0.2.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode"."\r\n";
	$insertPos=0;  // variable for saving //Users position
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above
	fwrite($file, $newline);
	fclose($file);*/

	echo $contents;
	mysqli_close($link);
?>
