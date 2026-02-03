<?php
//ob_clean();
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT logo FROM user_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		$rowlogo=mysqli_fetch_assoc($result);
		$logo=$rowlogo['logo'];
		$logourl='logo/'.$logo;
		//ob_flush();
        //flush();
		echo $handle_path = APICALLLOGURL."/$logourl";
		$handle = fopen("$handle_path", "b");
		set_file_buffer($handle,0); 
		//$handle = file_get_contents("http://www.acedns.in/acednsproduct/$logourl",);
		$contents = stream_get_contents($handle); 
		fclose($handle);
		print_r($contents);
	}
	else
	{
		echo '0';
	}
mysqli_close($link);
?>
