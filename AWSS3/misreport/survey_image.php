<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
	disphtml("main();");
ob_end_flush();	
?>
<center>
<?php
function main()
{
	$survey_id = $_REQUEST['survey_id'];
	$sql_select_image = "SELECT value FROM survey_output WHERE survey_id LIKE '%$survey_id%' AND value LIKE '%.jpeg%'";
	$res_select_image = mysql_query($sql_select_image);
	$total_rows = mysql_num_rows($res_select_image);
	
	if($total_rows>0)
	{
		$res_select_image = mysql_query($sql_select_image);
		$row_select_image = mysql_fetch_array($res_select_image);
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
			if(file_exists($link.$val))
				echo "<td><img src='".$link.$val."'></td>";
			else
				echo "<td>Image not found</td>";
			
			if($count%2 == 0)
			{
				echo "</tr>";
				echo "<tr>";
			}
			$count++;
		}
		echo "</table>";
		echo "<a href=\"survey_output_edit.php?show=data\" style=\"color:blue;\"><strong>Back</strong></a>";
		echo "</center>";
		
		
	}
	else
	{
		echo "<center>";
		echo "<div style=\"border:solid 1px; border-color:#990000; width:200px; color:red; font-size:12px; font-weight:bold; padding:6px;\">No images found</div><br>";
		echo "<a href=\"survey_output_edit.php?show=data\" style=\"color:blue;\"><strong>Back</strong></a>";
		echo "</center>";
	}
	
?>
<?php
}
?>