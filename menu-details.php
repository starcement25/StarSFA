<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM menu_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
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
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
