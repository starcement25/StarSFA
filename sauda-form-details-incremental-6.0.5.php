<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

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
$sqlquery="SELECT * FROM sauda_form_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowssaudaformdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<sauda_form_id><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_form_id'], 'UTF-8', 'UTF-8').']]></sauda_form_id>
							<user_id><![CDATA['.mb_convert_encoding($rowssaudaformdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<sauda_allocation_carry_forward><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_allocation_carry_forward'], 'UTF-8', 'UTF-8').']]></sauda_allocation_carry_forward>
							<sauda_depot_wise><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_depot_wise'], 'UTF-8', 'UTF-8').']]></sauda_depot_wise>
							<sauda_rate_variable><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_variable'], 'UTF-8', 'UTF-8').']]></sauda_rate_variable>
							<sauda_rate_variable_value><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_variable_value'], 'UTF-8', 'UTF-8').']]></sauda_rate_variable_value>
							<sauda_booked_through><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_booked_through'], 'UTF-8', 'UTF-8').']]></sauda_booked_through>
							<sauda_valid_from><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_valid_from'], 'UTF-8', 'UTF-8').']]></sauda_valid_from>
							<sauda_rate_dependent_on_despatch_point><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_dependent_on_despatch_point'], 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point>
							<sauda_rate_dependent_on_despatch_point_val><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_dependent_on_despatch_point_val'], 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_val>
							<sauda_rate_dependent_on_despatch_point_verticlewise><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_dependent_on_despatch_point_verticlewise'], 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_verticlewise>
							<sauda_rate_dependent_on_despatch_point_verticle_val><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_rate_dependent_on_despatch_point_verticle_val'], 'UTF-8', 'UTF-8').']]></sauda_rate_dependent_on_despatch_point_verticle_val>
							<secondary_freight_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails['secondary_freight_vertical'], 'UTF-8', 'UTF-8').']]></secondary_freight_vertical>
							<special_discount_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails['special_discount_vertical'], 'UTF-8', 'UTF-8').']]></special_discount_vertical>
							<customer_email_check_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails['customer_email_check_vertical'], 'UTF-8', 'UTF-8').']]></customer_email_check_vertical>
							<incoterms_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails['incoterms_vertical'], 'UTF-8', 'UTF-8').']]></incoterms_vertical>
							<sauda_allocation_app_vertical><![CDATA['.mb_convert_encoding($rowssaudaformdetails['sauda_allocation_app_vertical'], 'UTF-8', 'UTF-8').']]></sauda_allocation_app_vertical>
						    <incoterms_price_components><![CDATA['.mb_convert_encoding($rowssaudaformdetails['incoterms_price_components'], 'UTF-8', 'UTF-8').']]></incoterms_price_components>
							';	
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sauda-form-details-incremental-6.0.5.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	echo $contents;	
	mysqli_close($link);	
?>
