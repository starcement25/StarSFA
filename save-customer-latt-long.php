<?php
include "school_connection.php";
$customer_master = "customer_master";
$inputJSON = file_get_contents('php://input');
$input= json_decode($inputJSON);
//print_r($input);
$id_array=array();
foreach($input as $inputvalue)
{
	//print_r($inputvalue);
	$countval=1;
	foreach($inputvalue as $inputvalpair)
	{
		//echo $countval;
		foreach($inputvalpair as $key=>$inputval)
		{	
			//echo $key;
			//echo $inputval;	
			if(!in_array($countval,$id_array))
			{
				array_push($id_array,$countval);
			}
			if($key=='dns_customer_code') ${dns_customer_code.$countval}=$inputval;
			if($key=='latt') 	  		  ${latt.$countval}=$inputval;
			if($key=='longi') 	  		${longi.$countval}=$inputval;
		}
		$countval++;
	}
}
foreach($id_array as $id_val){	
		$sqlupdate="UPDATE $customer_master SET base_latt='".addslashes(${latt.$id_val})."',base_longi='".addslashes(${longi.$id_val})."'
					WHERE dns_customer_code='".${dns_customer_code.$id_val}."'";
				$rsinsert = mysqli_query($link,$sqlupdate);
				if(!$sqlupdate){
					$successval=0;
				}
				else
				{
					$successval=1;
				}
}
if($successval==1 )
	{
		$res_data = array("process_status"=>"YES","process_message"=>"Record Updated");
	}else{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
mysqli_close($conn);
?>