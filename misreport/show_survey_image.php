<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");


$survey_id = $_REQUEST['survey_id'];

$sql_select_image = "SELECT value FROM survey_output WHERE survey_id LIKE '%$survey_id%' AND value LIKE '%.jpeg%'";
$res_select_image = mysqli_query($link,$sql_select_image);
$total_rows = mysqli_num_rows($res_select_image);

if($total_rows>0)
{
	$res_select_image = mysqli_query($link,$sql_select_image);
	$row_select_image = mysqli_fetch_assoc($res_select_image);
	$image = $row_select_image['value'];
	$image = rtrim($image," ");
	$image = rtrim($image,";");
	$image_array = explode(";",$image);
	$link = "../upload/".strtoupper($_SESSION['nick_name'])."/";
	$count = 1;
	echo "<center><table width=\"60%\" cellpadding=\"6px\">";
	echo "<tr>";
	foreach($image_array as $val)
	{
		$val = ltrim($val," ");
		$val = rtrim($val,";");
		echo "<td><img src='".$link.$val."'></td>";
		
		if($count%2 == 0)
		{
			echo "</tr>";
			echo "<tr>";
		}
		$count++;
	}
	echo "</table>";
}
?>