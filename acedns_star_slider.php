<?php
include "star_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$start_slider = "start_slider";
$slider_folder = "slider/";
$dir_name = "slider/";
$start_slider_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$sqlall = "select * from $start_slider order by `id` asc";
$resall = mysqli_query($link,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$image_name = $row11["image_name"] ? trim($row11["image_name"]) : "";
		if($image_name!=""){
			if(file_exists($dir_name.$image_name)){
				$start_slider_data[] = $server_url1.$slider_folder.$image_name;
			}
		}
	}
	$res_data = array("process_status"=>"YES","process_message"=>"Success.","start_slider_data"=>$start_slider_data);	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No slider found","start_slider_data"=>$start_slider_data);
}
echo json_encode($res_data);
mysqli_close();
?>