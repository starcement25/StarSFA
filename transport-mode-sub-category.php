<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM transport_mode_sub_category ORDER BY transport_mode_sub_cat_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowptransportmodecat = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<transport_mode_sub_cat_id><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_sub_cat_id'], 'UTF-8', 'UTF-8').']]></transport_mode_sub_cat_id>
							<transport_mode_sub_cat_name><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_sub_cat_name'], 'UTF-8', 'UTF-8').']]></transport_mode_sub_cat_name>
							<transport_mode_cat_id><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_cat_id'], 'UTF-8', 'UTF-8').']]></transport_mode_cat_id>';
							';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
