<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM transport_mode_sub_category ORDER BY transport_mode_sub_cat_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'3';
	if($count>0){
		while($rowptransportmodecat = mysqli_fetch_assoc($result))
		{
				/*$contents.="<data>";
				$contents .='<transport_mode_sub_cat_id><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_sub_cat_id'], 'UTF-8', 'UTF-8').']]></transport_mode_sub_cat_id>
							<transport_mode_sub_cat_name><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_sub_cat_name'], 'UTF-8', 'UTF-8').']]></transport_mode_sub_cat_name>
							<transport_mode_cat_id><![CDATA['.mb_convert_encoding($rowptransportmodecat['transport_mode_cat_id'], 'UTF-8', 'UTF-8').']]></transport_mode_cat_id>';
							';
				$contents.="</data>";
				//echo $cnt++;*/
				$contents  = (($rowptransportmodecat['transport_mode_sub_cat_id']!='')?$rowptransportmodecat['transport_mode_sub_cat_id']: ' ')."^";
				$contents  .= (($rowptransportmodecat['transport_mode_sub_cat_name']!='')?$rowptransportmodecat['transport_mode_sub_cat_name']: ' ')."^";
				$contents  .= (($rowptransportmodecat['transport_mode_cat_id']!='')?$rowptransportmodecat['transport_mode_cat_id']: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=transport_mode_sub_category.txt");
	print "$datacontents"; 		
?>
