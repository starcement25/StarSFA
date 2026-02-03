<?php
$ftp_server='103.242.119.68';
$port='21';
// set up basic ssl connection
$conn_id = ftp_connect($ftp_server,$port);
$ftp_user_name='acedns';
$ftp_user_pass='FdA@9105321$#';
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
ftp_set_option($conn_id, FTP_USEPASVADDRESS, false);
ftp_pasv($conn_id, true);
//print_r($login_result);

if (!$login_result) {
    // PHP will already have raised an E_WARNING level message in this case
    die("can't login");
}
else echo 'Connection Established';

//echo ftp_cdup($conn_id);
echo ftp_pwd($conn_id);
$file_list = ftp_rawlist($conn_id,"public_html");



/*$depth='10';

$dir = array('.');
$a = count($dir);
$i = 0;
while (($a != $b) && ($i < $depth)) {
	$i++;
	$a = count($dir) ;
	foreach ($dir as $d) {
		$ftp_dir = $d.'/';
		$newdir = ftp_nlist($conn_id, $ftp_dir);
		foreach ($newdir as $key => $x) {
			if ((strpos($x,'.')) || (strpos($x,'.') === 0)) { unset($newdir[$key]); }
			elseif (!in_array($x,$dir)) { $dir[] = $x; }
		}
	}
	$b = count($dir);
}

print_r($dir);*/

//echo ftp_pwd($conn_id); // /

// close the ssl connection

/*foreach ($file_list as $c)
{ 
     if ($file["type"] == "dir")
    {
        echo $file["name"]."\n";
    }
	else
	{
		 echo $file."\n";
		
		 if(strpos($file,'20221123')!=false){
			  echo 'sdsdsdsdsdsdsd'.realpath($file);
			 echo $file_parts=explode("/",$file);
			 $local_file='V3/'.$file_parts[1];
			//$file_parts=explode("/",$file);
			//echo 'a'.$file_parts[0]."\n";
			//echo 'b'.$file_parts[1]."\n";
			//exit();
			/*if (ftp_get($conn_id, $local_file, $file, FTP_BINARY)) {
				echo "Successfully written to $local_file\n";
			} else {
				echo "There was a problem\n";
			}*/

		 /*}
	}
}*/
foreach ($file_list as $file)
{
    //echo $file."\n";
	$tokens = explode(" ", $file);
    $name = $tokens[count($tokens) - 1];
    $type = $tokens[0][0];

    if ($type == 'd')
    {
        echo "$name\n";
    }
	/*else
	{
	echo "$name\n";
	}*/
}
ftp_close($conn_id);
//var_dump($file_list);
?>