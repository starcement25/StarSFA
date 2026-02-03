<?php
		$nick_name=strtoupper($_REQUEST['nick_name']);
		$upload_dir="csv/$nick_name/";
		$file_name = $_FILES['file']['name'];
		$tmp_name=$_FILES['file']['tmp_name'];
		$file_size=$_FILES['file']['size'];
		$file_type 	= 'general';
	
	if($file_name != "")// && $file_size < 2097152
	{
		$upload_file = $upload_dir.$file_name;
		if(move_uploaded_file($tmp_name,$upload_file))
		{
			echo '1';
		}
		else
		{
			echo '0';
		}
	}//end of size and file checking
?>
