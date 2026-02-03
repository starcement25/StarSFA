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

$sqlquery="SELECT * FROM menu_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowsmenudetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<menu_id><![CDATA['.mb_convert_encoding($rowsmenudetails['menu_id'], 'UTF-8', 'UTF-8').']]></menu_id>
							<user_id><![CDATA['.mb_convert_encoding($rowsmenudetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<attendance><![CDATA['.mb_convert_encoding($rowsmenudetails['attendance'], 'UTF-8', 'UTF-8').']]></attendance>
							<route_plan><![CDATA['.mb_convert_encoding($rowsmenudetails['route_plan'], 'UTF-8', 'UTF-8').']]></route_plan>
							<order><![CDATA['.mb_convert_encoding($rowsmenudetails['order'], 'UTF-8', 'UTF-8').']]></order>
							<collection><![CDATA['.mb_convert_encoding($rowsmenudetails['collection'], 'UTF-8', 'UTF-8').']]></collection>
							<stk_audit><![CDATA['.mb_convert_encoding($rowsmenudetails['stk_audit'], 'UTF-8', 'UTF-8').']]></stk_audit>
							<business_prospect><![CDATA['.mb_convert_encoding($rowsmenudetails['business_prospect'], 'UTF-8', 'UTF-8').']]></business_prospect>
							<tour_exp><![CDATA['.mb_convert_encoding($rowsmenudetails['tour_exp'], 'UTF-8', 'UTF-8').']]></tour_exp>
							<capture_image><![CDATA['.mb_convert_encoding($rowsmenudetails['capture_image'], 'UTF-8', 'UTF-8').']]></capture_image>
							<notes_and_info><![CDATA['.mb_convert_encoding($rowsmenudetails['notes_and_info'], 'UTF-8', 'UTF-8').']]></notes_and_info>
							<activity_report><![CDATA['.mb_convert_encoding($rowsmenudetails['activity_report'], 'UTF-8', 'UTF-8').']]></activity_report>
							<loyalty><![CDATA['.mb_convert_encoding($rowsmenudetails['loyalty'], 'UTF-8', 'UTF-8').']]></loyalty>
							<self_appraisal><![CDATA['.mb_convert_encoding($rowsmenudetails['self_appraisal'], 'UTF-8', 'UTF-8').']]></self_appraisal>
							<schemes><![CDATA['.mb_convert_encoding($rowsmenudetails['schemes'], 'UTF-8', 'UTF-8').']]></schemes>
							<loading_freight><![CDATA['.mb_convert_encoding($rowsmenudetails['loading_freight'], 'UTF-8', 'UTF-8').']]></loading_freight>
							<mis_report><![CDATA['.mb_convert_encoding($rowsmenudetails['mis_report'], 'UTF-8', 'UTF-8').']]></mis_report>
							<delete_transaction><![CDATA['.mb_convert_encoding($rowsmenudetails['delete_transaction'], 'UTF-8', 'UTF-8').']]></delete_transaction>
							<sauda_allocation><![CDATA['.mb_convert_encoding($rowsmenudetails['sauda_allocation'], 'UTF-8', 'UTF-8').']]></sauda_allocation>
							<survey><![CDATA['.mb_convert_encoding($rowsmenudetails['survey'], 'UTF-8', 'UTF-8').']]></survey>
							<product_promotion><![CDATA['.mb_convert_encoding($rowsmenudetails['product_promotion'], 'UTF-8', 'UTF-8').']]></product_promotion>
							<replacement><![CDATA['.mb_convert_encoding($rowsmenudetails['replacement'], 'UTF-8', 'UTF-8').']]></replacement>
							<market_feedback><![CDATA['.mb_convert_encoding($rowsmenudetails['market_feedback'], 'UTF-8', 'UTF-8').']]></market_feedback>
							<sauda_allocation_app><![CDATA['.mb_convert_encoding($rowsmenudetails['sauda_allocation_app'], 'UTF-8', 'UTF-8').']]></sauda_allocation_app>
							<pending_contract><![CDATA['.mb_convert_encoding($rowsmenudetails['pending_contract'], 'UTF-8', 'UTF-8').']]></pending_contract>
							<sauda_mis><![CDATA['.mb_convert_encoding($rowsmenudetails['sauda_mis'], 'UTF-8', 'UTF-8').']]></sauda_mis>
							<order_status><![CDATA['.mb_convert_encoding($rowsmenudetails['order_status'], 'UTF-8', 'UTF-8').']]></order_status>
							<checkout><![CDATA['.mb_convert_encoding($rowsmenudetails['checkout'], 'UTF-8', 'UTF-8').']]></checkout>
							<sauda_outstanding><![CDATA['.mb_convert_encoding($rowsmenudetails['sauda_outstanding'], 'UTF-8', 'UTF-8').']]></sauda_outstanding>
							<sale_performance><![CDATA['.mb_convert_encoding($rowsmenudetails['sale_performance'], 'UTF-8', 'UTF-8').']]></sale_performance>
							<check_in_out><![CDATA['.mb_convert_encoding($rowsmenudetails['check_in_out'], 'UTF-8', 'UTF-8').']]></check_in_out>
							<outstanding><![CDATA['.mb_convert_encoding($rowsmenudetails['outstanding'], 'UTF-8', 'UTF-8').']]></outstanding>
							<outstanding_ageing><![CDATA['.mb_convert_encoding($rowsmenudetails['outstanding_ageing'], 'UTF-8', 'UTF-8').']]></outstanding_ageing>
							<target_achievement><![CDATA['.mb_convert_encoding($rowsmenudetails['target_achievement'], 'UTF-8', 'UTF-8').']]></target_achievement>
							<wholesaler_info><![CDATA['.mb_convert_encoding($rowsmenudetails['wholesaler_info'], 'UTF-8', 'UTF-8').']]></wholesaler_info>
							<yellow_card><![CDATA['.mb_convert_encoding($rowsmenudetails['yellow_card'], 'UTF-8', 'UTF-8').']]></yellow_card>
							<catalogue><![CDATA['.mb_convert_encoding($rowsmenudetails['catalogue'], 'UTF-8', 'UTF-8').']]></catalogue>
							<catalogue_url><![CDATA['.mb_convert_encoding($rowsmenudetails['catalogue_url'], 'UTF-8', 'UTF-8').']]></catalogue_url>
							<tele_tran><![CDATA['.mb_convert_encoding($rowsmenudetails['tele_tran'], 'UTF-8', 'UTF-8').']]></tele_tran>
							<TD_allocation_app><![CDATA['.mb_convert_encoding($rowsmenudetails['TD_allocation_app'], 'UTF-8', 'UTF-8').']]></TD_allocation_app>
							<catalogue_dependency><![CDATA['.mb_convert_encoding($rowsmenudetails['catalogue_dependency'], 'UTF-8', 'UTF-8').']]></catalogue_dependency>
							<last_update_time><![CDATA['.mb_convert_encoding($contentsdatetime, 'UTF-8', 'UTF-8').']]></last_update_time>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/menu-details-incremental-7.0.6.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/menu-details-incremental-6.0.2.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode"."\r\n";
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