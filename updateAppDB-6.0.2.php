<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

$deviceId=$_REQUEST['deviceId'];
$versionCode=$_REQUEST['versionCode'];
$versionCodeDB=$_REQUEST['versionCodeDB'];
$emp_code=$_REQUEST['emp_code'];
//$deviceId='911530150537029';
//$versionCode='5.4.5.6';
//$sqlquery="select * from employee_master";
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/updateAppDB-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&deviceId=$deviceId&versionCode=$versionCode";
insertapilog($datetime,$emp_code,$url,$nick_name);

$sqlempname="SELECT emp_name,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempname=mysqli_query($link,$sqlempname);
$rowempname=mysqli_fetch_assoc($rsempname);
$emp_name=$rowempname['emp_name'];
$vertical_value=$rowempname['vertical_value'];

if(vertical_fields=='yes' && (strtoupper($_REQUEST['nick_name'])=='EMAMI' || strtoupper($_REQUEST['nick_name'])=='EMAMIT')){
	$sqlselectappversion="SELECT * FROM app_version WHERE vertical_value='".$vertical_value."'";
}
else{
	$sqlselectappversion="SELECT * FROM table_structure_updation";
}
//echo $sqlselectappversion;
$rsselectappversion=mysqli_query($link,$sqlselectappversion);
$rowselectappversion=mysqli_fetch_assoc($rsselectappversion);
$app_version_latest=$rowselectappversion['version_code'] ??'';
$release_date=date('d/m/Y',strtotime($rowselectappversion['date'] ??''));

	$sqlselect="SELECT * FROM table_structure_updation WHERE device_id='".$deviceId."'";
	//echo $sqlselect;
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
	$rowselect=mysqli_fetch_assoc($rsselect);
	if($count>0)
	{
		if($rowselect['is_update']==1)
		{
			$sqlUpdate="UPDATE app_updation SET
						version_code='".$versionCode."',
						is_update='0'
						WHERE device_id='".$deviceId."'";
						//echo $sqlUpdate;
			if(mysqli_query($link,$sqlUpdate))
			{
			    echo "0";
			    /*$sqlUpdate="UPDATE table_structure_updation SET
						db_version_code='".$versionCode."',
						is_update='0'
						WHERE device_id='".$deviceId."'";
				if(mysqli_query($link,$sqlUpdate))
			        {
			            if($versionCode > $rowselect['version_code']){ 									
                			echo "0";
                			}
                		else
                		{
                			echo "10";
                		}
			        }*/
				
			}
			else
			{
				echo "11";
			}
		

		}else
			{
				echo "4";
			}
	
	}
	else
	{
		$sqlInsert="INSERT INTO app_updation SET
					version_code='".$versionCode."',
					device_id='".$deviceId."',
					is_update='0'";
					//echo $sqlInsert;
		if(mysqli_query($link,$sqlInsert))
		{
			echo "4".'/'.$app_version_latest;
		}
		else
		{
			echo "3";
		}
	}

mysqli_close($link);
?>
