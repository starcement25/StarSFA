<?php


// ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);
 
 require("include/common.php");
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/


//echo $root_base_url;
define("SERVER","localhost");
define("USER","root");
define("PASSWORD","Passw0rd123#$");
define("APICALLLOGURL",ROOT_BASE_URL);

		define("SERVERREMOTE","52.66.101.239");
        define("USERREMOTE","root");
        define("PASSWORDREMOTE","cmcl@123");

$nick_name=$_REQUEST['nick_name'];

//$ddb = "acedns_".$nick_name;
$ddb = "acedns_STAR";

/*define("SERVERREMOTE","52.66.101.239");
define("USERREMOTE","root");
define("PASSWORDREMOTE","cmcl@123");*/
//define("DBREMOTE","$nick_name");
define("DB","$ddb");
//echo"<pre>";print_r($ddb);die;
$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];
$mode=$_REQUEST['mode'];

function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
	$dbnamefinal="acedns_".$dbname;
	/*$link=mysqli_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
	mysqli_select_db($dbnamefinal,$link) or die("could not connect the database for invalid nick name");*/
	$link=mysqli_connect(SERVER,USER,PASSWORD,DB) or die("Database Connection Error.");
	
	return $link;
}

$linksetup=mysqli_connect("localhost","root","Passw0rd123#$","acedns_acednsproduct") or die("Setup Database Connection Error.");
//$linksetup=connecttodb(SERVER,"acednsproduct",USER,PASSWORD);

$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
$rsdbaccessdetails=mysqli_query($linksetup,$sqldbaccessdetails);
$rowdbaccessdetails=mysqli_fetch_assoc($rsdbaccessdetails);
$remote_db_access=$rowdbaccessdetails['remote_db_access'];

if($remote_db_access=='yes')
{
    
    
    
    
	$link=connecttodb(SERVERREMOTE,DBREMOTE,USERREMOTE,PASSWORDREMOTE);
}
else
{
    $link=connecttodb(SERVER,"$nick_name",USER,PASSWORD);
}

$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysqli_query($link,$sqlselectversion);
$rowselectversion=mysqli_fetch_assoc($rsselectversion);
$versionCode=$rowselectversion['version_code'];



function insertapilog($datetime,$emp_code,$url,$nick_name,$link)
{
	//mysqli_select_db("acedns_".$nick_name);
	$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
					  emp_code='".$emp_code."',
					  url='".$url."'";
	mysqli_query($link,$sqlinsertapilog);				  
}

$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/table-structure-details-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name,$link);

if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM table_structure_master ORDER BY  t_structure_id ASC";
	$result = mysqli_query($link,$sqlquery) or die(mysqli_error());
	$counttable=mysqli_num_rows($result);
	if($device_id!='')
	{
		if($emp_code!='')
		{
			$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
		}
		else
		{
			$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
		}
		$rsselect=mysqli_query($link,$sqlselect);
		$count=mysqli_num_rows($rsselect);
		
		if($count<1){
			$sqlInsert="INSERT INTO table_structure_updation SET
						emp_code='".$emp_code."',
						db_version_code='".$versionCode."',
						device_id='".$device_id."',
						is_update='0'";
			mysqli_query($link,$sqlInsert);
		}
	}
	
}
else{
	if($emp_code!='' && $device_id!='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	}
	else if($device_id=='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE emp_code='".$emp_code."' AND db_version_code='".$versionCode."'";
	}
	else
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
	}
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
		//echo $count;
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		$user_db_version_code=$rowselect['db_version_code'];
		$version_chk= ($versionCode-$user_db_version_code)*10;
		$chkval=1;
		//echo intval($chkval);
		if((intval($version_chk) > intval($chkval)) && $is_update==1)
		{
			$sqlquery="SELECT * FROM app_db_update_execution WHERE db_version > ".$user_db_version_code."  
						AND db_version <= ".$versionCode."  ORDER BY table_name ASC ";
						
			$result = mysqli_query($linksetup,$sqlquery) or die(mysqli_error());
			$counttable=mysqli_num_rows($result);
		}
		else if($is_update==1){
			$sqlquery="SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id DESC";
			//echo $sqlquery;
			$result = mysqli_query($link,$sqlquery);
			$counttable=mysqli_num_rows($result);
			//echo $counttable;
			$sqlcntupdation="SELECT COUNT(device_id) AS no_of_updated_users FROM table_structure_updation WHERE is_update='0'";
			//echo $sqlcntupdation;
			$rscntupdation=mysqli_query($link,$sqlcntupdation);
			$rowcntupdation=mysqli_fetch_assoc($rscntupdation);
			$no_of_updated_users=$rowcntupdation['no_of_updated_users'];
			
			/*if($no_of_updated_users==no_of_licensed_users){
				$sqlupdatetable="UPDATE table_structure_master SET need_update='N',is_transaction='N',is_master='N'";
				mysqli_query($link,$sqlupdatetable);
			}*/
		}
		else
		{
			$counttable=0;		
		}
	}
}
//echo $counttable;
if($counttable>0){
	$sqlquerybaseurl="SELECT previous_baseurl_app,current_baseurl_app FROM user_details WHERE nick_name='".$nick_name."'";
	$resultbaseurl = mysqli_query($linksetup,$sqlquerybaseurl) or die(mysqli_error());
	$rowbaseurl=mysqli_fetch_assoc($resultbaseurl);
	$previous_baseurl_app=$rowbaseurl['previous_baseurl_app'];
	$current_baseurl_app=$rowbaseurl['current_baseurl_app'];
	if($previous_baseurl_app!=$current_baseurl_app || $mode=='INSTALL')
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
					<transaction><![CDATA['.mb_convert_encoding($rowstructuredetails['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
					<master><![CDATA['.mb_convert_encoding($rowstructuredetails['is_master'], 'UTF-8', 'UTF-8').']]></master>
					<db_version><![CDATA['.mb_convert_encoding($versionCode, 'UTF-8', 'UTF-8').']]></db_version>
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