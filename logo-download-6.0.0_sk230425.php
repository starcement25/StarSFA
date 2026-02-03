<?php
require("api_con.php");

$link = con_setup();

$nick_name=$_REQUEST['nick_name'];


$sqlquery="SELECT logo FROM user_details WHERE nick_name='".$nick_name."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		$rowlogo=mysqli_fetch_assoc($result);
		$logo=$rowlogo['logo'];
		$logourl='logo/'.$logo;
		/*$handle = "http://www.acedns.in/acednsproduct/$logourl";*/
		//$im = imagecreatefrompng("$logourl");
		/*$handle = file_get_contents("http://www.acedns.in/acednsproduct/$logourl",);
		$contents = stream_get_contents($handle); 
		fclose($handle);
		print_r($contents);*/
		header("Content-type: image/png"); 
		header("Content-Disposition: attachment; filename=$logo");
		//imagepng($im);
		//imagedestroy($im);
		readfile("$logourl");
	}
	else
	{
		echo '0';
	}
	mysqli_close($link);
?>
