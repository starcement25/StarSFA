<?php
$array = array();
random_number_generate($array);
function random_number_generate($array){
	//$array = $array;
	$rand = rand(1,15);
	if(!in_array($rand,$array)){
		array_push($array,$rand);
		if(count($array) == 10){
			echo "<pre>";
			print_r($array);
			die;
		}
		random_number_generate($array);
	}
	else{
		echo $rand." Already Exist<br>";
		sleep(2);
		random_number_generate($array);
	}
}

?>