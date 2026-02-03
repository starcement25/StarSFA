<?php
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("APICALLLOGURL","http://salesmpower.acedns.in");

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

function insertapilog($datetime,$emp_code,$url,$nick_name,$link)
{
	mysqli_select_db("acedns_".$nick_name);
	$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
					  emp_code='".$emp_code."',
					  url='".$url."'";
	mysqli_query($link,$sqlinsertapilog,$link);				  
}

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
			
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$url = APICALLLOGURL."/OTP-table-structure-details.php?nick_name=$nick_name";
insertapilog($contentsdatetime,$emp_code,$url,$nick_name,$link);

if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM OTP_table_structure_master ORDER BY  t_structure_id ASC";
	$result = mysqli_query($link,$sqlquery,$link) or die(mysqli_error());
	$counttable=mysqli_num_rows($result);
	if($emp_code!='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	}
	else
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
	}
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
	if($emp_code!='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	}
	else
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
	}
	$rsselect=mysqli_query($link,$sqlselect,$link);
	$count=mysqli_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$user_db_version_code=$rowselect['db_version_code'];
		//echo intval($chkval);
		if($is_update==1){
			$sqlquery="SELECT * FROM OTP_table_structure_master WHERE need_update='Y' ORDER BY t_structure_id DESC";
			$result = mysqli_query($link,$sqlquery,$link);
			$counttable=mysqli_num_rows($result);
		}
		else
		{
			$counttable=0;		
		}
	}
}
if($counttable>0){
	$sqlquerybaseurl="SELECT previous_baseurl_app,current_baseurl_app FROM user_details WHERE nick_name='".$nick_name."'";
	$resultbaseurl = mysqli_query($link,$sqlquerybaseurl,$linksetup) or die(mysqli_error());
	$rowbaseurl=mysqli_fetch_assoc($resultbaseurl);
	$previous_baseurl_app=$rowbaseurl['previous_baseurl_app'];
	$current_baseurl_app=$rowbaseurl['current_baseurl_app'];
	if($previous_baseurl_app!=$current_baseurl_app)
	{
		$baseurlchanged='Y';
	}
	else
	{
		$baseurlchanged='N';
	}

	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	while($rowstructuredetails = mysqli_fetch_assoc($result))
	{
		$contents.="<data>";	
		$contents .='<table_name><![CDATA['.mb_convert_encoding($rowstructuredetails['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
					<table_structure><![CDATA['.mb_convert_encoding($rowstructuredetails['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
					<base_url_changed><![CDATA['.mb_convert_encoding($baseurlchanged, 'UTF-8', 'UTF-8').']]></base_url_changed>
					<current_baseurl_app><![CDATA['.mb_convert_encoding($current_baseurl_app, 'UTF-8', 'UTF-8').']]></current_baseurl_app>
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
mysqli_close($link);
?>