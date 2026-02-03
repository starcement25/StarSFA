<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM product_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowsproductdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<product_id><![CDATA['.mb_convert_encoding($rowsproductdetails['product_id'], 'UTF-8', 'UTF-8').']]></product_id>
							<user_id><![CDATA['.mb_convert_encoding($rowsproductdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<no_of_filter><![CDATA['.mb_convert_encoding($rowsproductdetails['no_of_filter'], 'UTF-8', 'UTF-8').']]></no_of_filter>
							<col1><![CDATA['.mb_convert_encoding($rowsproductdetails['col1'], 'UTF-8', 'UTF-8').']]></col1>
							<col2><![CDATA['.mb_convert_encoding($rowsproductdetails['col2'], 'UTF-8', 'UTF-8').']]></col2>
							<col3><![CDATA['.mb_convert_encoding($rowsproductdetails['col3'], 'UTF-8', 'UTF-8').']]></col3>
							<col4><![CDATA['.mb_convert_encoding($rowsproductdetails['col4'], 'UTF-8', 'UTF-8').']]></col4>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;		
		
?>
