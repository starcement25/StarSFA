<?php
$dir    = (__DIR__);
/*$files1 = scandir($dir);
//$files2 = scandir($dir, 1);

echo "<pre>";
print_r($files1);
echo "</pre>";
//print_r($files2);*/

foreach (glob("*.php") as $filename) {
    $files[]=$filename;
}

/*echo "<pre>";
print_r($files);
echo "</pre>";*/
$count = 1;
foreach($files as $value){
	$filepath = $dir."/".$value;
	$getcontents = file_get_contents($filepath);
	//echo strpos('route_master',$getcontents);
	if(strpos($getcontents,'route_master') == true)
	{
		$contents = 'Contains route_master';
		$route_flag = 1;
	}
	
	if(strpos($getcontents,'customer_master') == true)
	{
		$contents_one = 'Contains customer_master';
		$customer_flag = 1;
	}
	
	if($route_flag == 1 || $customer_flag == 1){
		$new_value = $value." ".$contents." ".$contents_one;
		echo $count.". ".$new_value."<br>";
		$count++;
	}
	$contents = '';
	$contents_one = '';
	$customer_flag = '';
	$route_flag = '';
	//break;
}
?>