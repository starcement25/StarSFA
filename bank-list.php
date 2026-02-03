<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT * FROM bank_master ORDER BY bank_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowbank = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<bank_id><![CDATA['.mb_convert_encoding($rowbank['bank_id'], 'UTF-8', 'UTF-8').']]></bank_id>
							<bank_name><![CDATA['.mb_convert_encoding($rowbank['bank_name'], 'UTF-8', 'UTF-8').']]></bank_name>';
				$contents.="</data>";
				//echo $cnt++;
		}
	}
	$contents .= "</recordset>";			
	echo $contents;	
	
		
?>
