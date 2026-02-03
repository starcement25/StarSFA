<?php
$folder_path = "upload/SUPERSHAKTI"; 
   
// List of name of files inside 
// specified folder 
$files = glob($folder_path.'/*');  
   
// Deleting all the files in the list 
foreach($files as $file) { 
    //echo $file.'<br />';
	$file_parts=explode("/",$file);
	//echo substr($file_parts[2],5,4);
	if(substr($file_parts[2],5,4)=='2022')
	{
		if(unlink($file))
		{
			echo 'deleted'.$file.'<br />';
		}
		//echo $file.'<br />';
	}
	//exit();
   // Delete the given file 
} 
?>