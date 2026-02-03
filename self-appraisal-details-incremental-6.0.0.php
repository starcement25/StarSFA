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

$sqlquery="SELECT * FROM self_appraisal_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowselfappraisaldetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<self_appraisal_id><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['self_appraisal_id'], 'UTF-8', 'UTF-8').']]></self_appraisal_id>
							<user_id><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<multiple_target_achievement><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['multiple_target_achievement'], 'UTF-8', 'UTF-8').']]></multiple_target_achievement>
							<multiple_target_achievement_val><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['multiple_target_achievement_val'], 'UTF-8', 'UTF-8').']]></multiple_target_achievement_val>
							<volume_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['volume_wise'], 'UTF-8', 'UTF-8').']]></volume_wise>
							<value_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['value_wise'], 'UTF-8', 'UTF-8').']]></value_wise>
							<product_group_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['product_group_wise'], 'UTF-8', 'UTF-8').']]></product_group_wise>
							<product_sub_group_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['product_sub_group_wise'], 'UTF-8', 'UTF-8').']]></product_sub_group_wise>
							<product_brand_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['product_brand_wise'], 'UTF-8', 'UTF-8').']]></product_brand_wise>
							<product_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['product_wise'], 'UTF-8', 'UTF-8').']]></product_wise>
							<employee_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['employee_wise'], 'UTF-8', 'UTF-8').']]></employee_wise>
							<customer_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['customer_wise'], 'UTF-8', 'UTF-8').']]></customer_wise>
							<branch_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['branch_wise'], 'UTF-8', 'UTF-8').']]></branch_wise>
							<HQ_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['HQ_wise'], 'UTF-8', 'UTF-8').']]></HQ_wise>
							<route_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['route_wise'], 'UTF-8', 'UTF-8').']]></route_wise>
							<on_total><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['on_total'], 'UTF-8', 'UTF-8').']]></on_total>
							<on_individual><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['on_individual'], 'UTF-8', 'UTF-8').']]></on_individual>
							<month_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['month_wise'], 'UTF-8', 'UTF-8').']]></month_wise>
							<week_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['week_wise'], 'UTF-8', 'UTF-8').']]></week_wise>
							<day_wise><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['day_wise'], 'UTF-8', 'UTF-8').']]></day_wise>
							<UOM_val><![CDATA['.mb_convert_encoding($rowselfappraisaldetails['UOM_val'], 'UTF-8', 'UTF-8').']]></UOM_val>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/self-appraisal-details-incremental-6.0.0.php?nick_name=$nick_name&last_update_time=$last_update_time&incremental_download=$incremental_download&mode=$mode";
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
