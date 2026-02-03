<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];

$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysqli_query($link,$sqlselectversion);
$rowselectversion=mysqli_fetch_assoc($rsselectversion);
$versionCode=$rowselectversion['version_code'];
if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM table_structure_master ORDER BY t_structure_id";
	$result = mysqli_query($link,$sqlquery) or die(mysqli_error());
	$counttable=mysqli_num_rows($result);
	
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."'";
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
else{
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."'";
	$rsselect=mysqli_query($link,$sqlselect);
	$count=mysqli_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysqli_fetch_assoc($rsselect);
		$is_update=$rowselect['is_update'];
		if($is_update==1){
			$sqlquery="SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
			$result = mysqli_query($link,$sqlquery);
			$counttable=mysqli_num_rows($result);
			
			$sqlUpdate="UPDATE table_structure_updation SET
						db_version_code='".$versionCode."',
						is_update='0'
						WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
			mysqli_query($link,$sqlUpdate);
			
			$sqlcntupdation="SELECT COUNT(device_id) AS no_of_updated_users FROM table_structure_updation WHERE is_update='0'";
			$rscntupdation=mysqli_query($link,$sqlcntupdation);
			$rowcntupdation=mysqli_fetch_assoc($rscntupdation);
			$no_of_updated_users=$rowcntupdation['no_of_updated_users'];
			
			if($no_of_updated_users==no_of_licensed_users){
				$sqlupdatetable="UPDATE table_structure_master SET need_update='N',is_transaction='N'";
				mysqli_query($link,$sqlupdatetable);
			}
		}
		else  
		{
			$counttable=0;		
		}
	}
}
if($counttable>0){
	/*$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";*/
	while($rowstructuredetails = mysqli_fetch_assoc($result))
	{
			/*$contents.="<data>";
			$contents .='<table_name><![CDATA['.mb_convert_encoding($rowstructuredetails['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
						<table_structure><![CDATA['.mb_convert_encoding($rowstructuredetails['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
						<transaction><![CDATA['.mb_convert_encoding($rowstructuredetails['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
						';
			$contents.="</data>";*/
			//echo $cnt++;
			$contents  = (($rowstructuredetails['table_name']!='')?$rowstructuredetails['table_name']: ' ')."^";
			$contents  .= (($rowstructuredetails['table_structure']!='')?$rowstructuredetails['table_structure']: ' ')."^";
			$contents  .= (($rowstructuredetails['is_transaction']!='')?$rowstructuredetails['is_transaction']: ' ')."^";
			$linecontents .= $contents."\n";
	}
	/*$contents .= "</recordset>";
	echo $contents;	*/
	//$datacontents = str_replace("\r","",$linecontents);
	$datacontents =$linecontents;
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=table_structure_details.txt");
	print "$datacontents"; 		
}
else
{
	echo '0';
}
?>