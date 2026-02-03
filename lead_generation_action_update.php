<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
date_default_timezone_set("Asia/Kolkata");
$emp_code=$_REQUEST['emp_code'];
$suid=$_REQUEST['suid'];
$action_on_lead =  $_REQUEST['action_on_lead_update'];
$txt_price =  $_REQUEST['txt_price'];

       $dats=date("Y-m-d h:s:m");
       
       if($txt_price!="0"){
            
$sql_qry = "UPDATE `lead_generation_master` SET `action_on_lead`='".$action_on_lead."' , `approved_price`='".$txt_price."', `hos_submission_date`='".$dats."' WHERE `lead_generation_id`='".$suid."'";
//echo $sql_qry;exit();
	if(mysqli_query($link,$sql_qry)){
	    $res_data_final = array("process_status"=>"YES","process_message"=>"DATA UPDATED SUCCESSFULLY" );
	}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something went wrong" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/lead_generation_action_update.php?nick_name=$nick_name&emp_code=$emp_code&suid=$suid&action_on_lead=$action_on_lead";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);
}else{
    $res_data_final = array("process_status"=>"NO","process_message"=>"Price can not Zero" );
    echo json_encode($res_data_final);
}

?>