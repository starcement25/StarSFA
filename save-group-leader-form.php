<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
$group_leader_form_details = "group_leader_form_details";
$emp_code=$_REQUEST['emp_code'];
$inputJSON = file_get_contents('php://input');
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".addslashes($inputJSON)."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	
/*$inputJSON='{"group_l_form_id":"GFE000420220518115704","customer_name":"testGL","mobile_no":"9888453423","demo_achieved":"no","group_l_photo":"GFE000420220421115704.jpg","group_l_photo_datetime":"2022-04-25 14:29:10","group_l_photo_latt":"18","group_l_photo_longi":"22","tent_photo":"TFE000420220421115704.jpg","tent_photo_datetime":"2022-04-25 14:29:10 ","tent_photo_latt":"19",
"tent_photo_longi":"22","demo_photo": "DFE000420220421115704.jpg","demo_photo_datetime":"2022-04-25 14:29:10","demo_photo_latt":"18","demo_photo_longi":"22","night_meet_photo":"NFE000420220421115704.jpg ","night_meet_photo_datetime":" 2022-04-25 14:29:10 ","night_meet_photo_latt":"18","night_meet_photo_longi":"21","update_by":"E0004"}';*/
$input= json_decode($inputJSON,true);
//print_r($input);
/*$value='{"group_l_form_id":"GFE000420220421115704","customer_name":"test","mobile_no":"9888453423","demo_achieved":"no","group_l_photo":"GFE000420220421115704.jpg","group_l_photo_datetime":"2022-04-25 14:29:10","group_l_photo_latt":"18","group_l_photo_longi":"22","tent_photo":"TFE000420220421115704.jpg","tent_photo_datetime":"2022-04-25 14:29:10 ","tent_photo_latt":"19",
"tent_photo_longi":"22","demo_photo": "DFE000420220421115704.jpg","demo_photo_datetime":"2022-04-25 14:29:10","demo_photo_latt":"18","demo_photo_longi":"22","night_meet_photo":"NFE000420220421115704.jpg ","night_meet_photo_datetime":" 2022-04-25 14:29:10 ","night_meet_photo_latt":"18","night_meet_photo_longi":"21","update_by":"E0004"};*/

$group_l_form_id=$input['group_l_form_id'];
if($group_l_form_id!=''){
		$customer_name=$input['customer_name'];
		$mobile_no=$input['mobile_no'];
		$demo_achieved=$input['demo_achieved'];
		$group_l_photo=$input['group_l_photo'];
		$group_l_photo_datetime=$input['group_l_photo_datetime'];
		$group_l_photo_latt=$input['group_l_photo_latt'];
		$group_l_photo_longi=$input['group_l_photo_longi'];
		$tent_photo=$input['tent_photo'];
		$tent_photo_datetime=$input['tent_photo_datetime'];
		$tent_photo_latt=$input['tent_photo_latt'];
		$tent_photo_longi=$input['tent_photo_longi'];
		$demo_photo=$input['demo_photo'];
		$demo_photo_datetime=$input['demo_photo_datetime'];
		$demo_photo_latt=$input['demo_photo_latt'];
		$demo_photo_longi=$input['demo_photo_longi'];
		$night_meet_photo=$input['night_meet_photo'];
		$night_meet_photo_datetime=$input['night_meet_photo_datetime'];
		$night_meet_photo_latt=$input['night_meet_photo_latt'];
		$night_meet_photo_longi=$input['night_meet_photo_longi'];
		$update_by=$input['update_by'];
		
		$sqlchkgroupleaderform="SELECT group_l_form_id FROM $group_leader_form_details WHERE group_l_form_id='".addslashes($group_l_form_id)."'";
		$rschkgroupleaderform=mysqli_query($link,$sqlchkgroupleaderform);
		$countchkgroupleaderform=mysqli_num_rows($rschkgroupleaderform);
		if($countchkgroupleaderform==0){
		$sqlinsertgroupleaderdetails="INSERT INTO $group_leader_form_details SET group_l_form_id='".addslashes($group_l_form_id)."',
							customer_name='".addslashes($customer_name)."',
							mobile_no='".addslashes($mobile_no)."',
							demo_achieved='".addslashes($demo_achieved)."',
							group_l_photo='".addslashes($group_l_photo)."',
							group_l_photo_datetime='".addslashes($group_l_photo_datetime)."',
							group_l_photo_latt='".addslashes($group_l_photo_latt)."',
							group_l_photo_longi='".addslashes($group_l_photo_longi)."',
							tent_photo='".addslashes($tent_photo)."',
							 tent_photo_datetime='".addslashes($tent_photo_datetime)."',
							tent_photo_latt='".addslashes($tent_photo_latt)."',
							tent_photo_longi='".addslashes($tent_photo_longi)."',
							demo_photo='".addslashes($demo_photo)."',
							demo_photo_datetime='".addslashes($demo_photo_datetime)."',
							demo_photo_latt='".addslashes($demo_photo_latt)."',
							demo_photo_longi='".addslashes($demo_photo_longi)."',
							night_meet_photo='".addslashes($night_meet_photo)."',
							night_meet_photo_datetime='".addslashes($night_meet_photo_datetime)."',
							night_meet_photo_latt='".addslashes($night_meet_photo_latt)."',
							night_meet_photo_longi='".addslashes($night_meet_photo_longi)."',
							update_by='".addslashes($update_by)."',
							update_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinsertgroupleaderdetails))
		{
			$res_data = array("process_status"=>"YES","process_message"=>'Group Leader Form Details saved successfully');
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
		}
	  }
	  else
	  {
		  $res_data = array("process_status"=>"YES","process_message"=>'Group Leader Form Details saved successfully');
	  }
	}else{
	   $res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
//mysqli_close();
?>