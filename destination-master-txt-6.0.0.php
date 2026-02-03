<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$sqlquery="SELECT destination_code,destination_name FROM destination_master WHERE 1 ORDER BY destination_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
	if($count>0){
		while($rowdestination = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowdestination['destination_code']!='')?$rowdestination['destination_code']: ' ')."^";
			$contents  .= (($rowdestination['destination_name']!='')?$rowdestination['destination_name']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=destination_master.txt");
	print "$datacontents"; 		
?>
