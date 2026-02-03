<?php
	require("include/config.php");
	require("include/dbcon.php");
	require("include/functions.php");

	$deviceId=$_REQUEST['deviceId'];

	$folderName = $nick_name;
	if ( !file_exists($folderName)){
		mkdir("dbbackup/$folderName");
		chmod("dbbackup/$folderName", 0777);
	}

	$upload_dir="dbbackup/".$folderName.'/';
	$file_name = $_FILES['file']['name'];
	$tmp_name=$_FILES['file']['tmp_name'];
	$file_size=$_FILES['file']['size'];
	$file_type 	= 'general';
		
	
	if($file_name != "")// && $file_size < 2097152
	{
		$upload_file = $upload_dir.$file_name;
		if(move_uploaded_file($tmp_name,$upload_file))
		{
			$sqlUpdate="UPDATE dbbackupcheck SET
					is_checked='0'
					WHERE device_id='".$deviceId."'";
			if(mysqli_query($link,$sqlUpdate)){
				echo '1';
			}
			else
			{
				echo '0';
			}
		}
	}//end of size and file checking
?>
