<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/functions.php");

	$emp_code=$_REQUEST['emp_code'];
	$last_update_time=$_REQUEST['last_update_time'];
	$last_update_time=str_replace('€',' ',$last_update_time);
	
	/*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$result = mysqli_query($link,$sqlquery);
	$countdatarefresh=mysqli_num_rows($result);*/

	$folderName = $nick_name;
	if ( !file_exists("upload/check")){
		mkdir("upload/check");
		chmod("upload/check", 0777);
	}

	$upload_dir="upload/check/";
	$file_name = $_FILES['file']['name'];
	$tmp_name=$_FILES['file']['tmp_name'];
	$file_size=$_FILES['file']['size'];
	$file_type 	= 'general';
		
	if($file_name != "")// && $file_size < 2097152
	{
		$upload_file = $upload_dir.$file_name;
		if(move_uploaded_file($tmp_name,$upload_file))
		{
			$filenamezip="upload/check/$file_name";
			$zip = new ZipArchive;
			if ($zip->open($filenamezip)) {
				$zip->extractTo("upload/check/");
				$zip->close();
			}	
			$sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."',	uploaded_image='".var_dump($file_name)."' 
			
			WHERE emp_code='".$emp_code."'";
			$rsupdatelastoperationtime=mysqli_query($link,$sqlupdatelastoperationtime);	 
			 
			/* if($countdatarefresh >0)
			 {
				 echo $flag=2;
			 }
			 else
			 {*/
				echo $flag=1;
			 //}
		}
		else
		{
			echo $flag=0;
		}
	}//end of size and file checking
	else
	{
		echo $flag=0;
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));

	$url = APICALLLOGURL."/operationdb-attachment-export-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
