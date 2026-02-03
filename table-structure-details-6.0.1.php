<?php
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");

$nick_name=$_REQUEST['nick_name'];
$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];
$mode=$_REQUEST['mode'];

function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
	$dbnamefinal="acedns_".$dbname;
	$link=mysqli_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
	mysqli_select_db($dbnamefinal,$link) or die("could not connect the database for invalid nick name");
	return $link;
}
$linksetup=connecttodb(SERVER,"acednsproduct",USER,PASSWORD);
$link=connecttodb(SERVER,"$nick_name",USER,PASSWORD);

$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysqli_query($link,$sqlselectversion,$link);
$rowselectversion=mysqli_fetch_assoc($rsselectversion);
$versionCode=$rowselectversion['version_code'];

if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM table_structure_master ORDER BY t_structure_id";
	$result = mysqli_query($link,$sqlquery,$link) or die(mysqli_error());
	$counttable=mysqli_num_rows($result);
	
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect,$link);
	$count=mysqli_num_rows($rsselect);
	
	if($count<1){
		$sqlInsert="INSERT INTO table_structure_updation SET
						emp_code='".$emp_code."',
						db_version_code='".$versionCode."',
						device_id='".$device_id."',
						is_update='0'";
		mysqli_query($link,$sqlInsert,$link);
	}
}
else{
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysqli_query($link,$sqlselect,$link);
	$count=mysqli_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$user_db_version_code=$rowselect['db_version_code'];
		//echo ($versionCode-$user_db_version_code);
		if(($versionCode-$user_db_version_code) > '.1' && $is_update==1)
		{
			$sqlquery="SELECT * FROM app_db_update_execution WHERE db_version > '".$user_db_version_code."'  
						AND db_version <= '".$versionCode."'  ORDER BY app_db_u_exe_id ASC ";
			$result = mysqli_query($link,$sqlquery,$linksetup) or die(mysqli_error());
			$counttable=mysqli_num_rows($result);
		}
		else if($is_update==1){
			$sqlquery="SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
			$result = mysqli_query($link,$sqlquery,$link);
			$counttable=mysqli_num_rows($result);
			
			$sqlcntupdation="SELECT COUNT(device_id) AS no_of_updated_users FROM table_structure_updation WHERE is_update='0'";
			$rscntupdation=mysqli_query($link,$sqlcntupdation,$link);
			$rowcntupdation=mysqli_fetch_assoc($rscntupdation);
			$no_of_updated_users=$rowcntupdation['no_of_updated_users'];
			
			if($no_of_updated_users==no_of_licensed_users){
				$sqlupdatetable="UPDATE table_structure_master SET need_update='N',is_transaction='N',is_master='N'";
				mysqli_query($link,$sqlupdatetable,$link);
			}
		}
		else
		{
			$counttable=0;		
		}
	}
}
if($counttable>0){
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	while($rowstructuredetails = mysqli_fetch_assoc($result))
	{
			$contents.="<data>";
			$contents .='<table_name><![CDATA['.mb_convert_encoding($rowstructuredetails['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
						<table_structure><![CDATA['.mb_convert_encoding($rowstructuredetails['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
						<transaction><![CDATA['.mb_convert_encoding($rowstructuredetails['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
						<master><![CDATA['.mb_convert_encoding($rowstructuredetails['is_master'], 'UTF-8', 'UTF-8').']]></master>
						<db_version><![CDATA['.mb_convert_encoding($versionCode, 'UTF-8', 'UTF-8').']]></db_version>
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