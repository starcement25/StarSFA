<?php
require("include/config.php");
require("include/dbcon.php");

//$nick_name=$_POST['nick_name'];
$sqlquery="SELECT * FROM user_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	if($count>0){
		$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
		while($rowsuserdetails = mysqli_fetch_assoc($result))
		{
				$contents.="<data>";
				$contents .='<user_id><![CDATA['.mb_convert_encoding($rowsuserdetails['user_id'], 'UTF-8', 'UTF-8').']]></user_id>
							<name><![CDATA['.mb_convert_encoding($rowsuserdetails['name'], 'UTF-8', 'UTF-8').']]></name>
							<address><![CDATA['.mb_convert_encoding($rowsuserdetails['address'], 'UTF-8', 'UTF-8').']]></address>
							<phone_no><![CDATA['.mb_convert_encoding($rowsuserdetails['phone_no'], 'UTF-8', 'UTF-8').']]></phone_no>
							<email><![CDATA['.mb_convert_encoding($rowsuserdetails['email'], 'UTF-8', 'UTF-8').']]></email>
							<license_key><![CDATA['.mb_convert_encoding($rowsuserdetails['license_key'], 'UTF-8', 'UTF-8').']]></license_key>
							<no_users><![CDATA['.mb_convert_encoding($rowsuserdetails['no_users'], 'UTF-8', 'UTF-8').']]></no_users>
							<nick_name><![CDATA['.mb_convert_encoding($rowsuserdetails['nick_name'], 'UTF-8', 'UTF-8').']]></nick_name>
							<no_of_branches><![CDATA['.mb_convert_encoding($rowsuserdetails['no_of_branches'], 'UTF-8', 'UTF-8').']]></no_of_branches>
							<email_hierarchywise><![CDATA['.mb_convert_encoding($rowsuserdetails['email_hierarchywise'], 'UTF-8', 'UTF-8').']]></email_hierarchywise>
							<vertical_fields><![CDATA['.mb_convert_encoding($rowsuserdetails['vertical_fields'], 'UTF-8', 'UTF-8').']]></vertical_fields>
							<vertical_fields_value><![CDATA['.mb_convert_encoding($rowsuserdetails['vertical_fields_value'], 'UTF-8', 'UTF-8').']]></vertical_fields_value>
							';
				$contents.="</data>";
				//echo $cnt++;
		}
		$contents .= "</recordset>";
		echo $contents;		
	}
	else
	{
		echo '0';
	}
?>
