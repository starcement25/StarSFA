<?php
$ftp_server='203.112.144.200';
$port='1993';
// set up basic ssl connection
$conn_id = ftp_connect($ftp_server,$port);
$ftp_user_name='sambandh';
$ftp_user_pass='Cpil#1986';
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
//$path = "/Dealer App/";		
//ftp_chdir($conn_id,'Dealer App');
//$file_list = ftp_rawlist($conn_id,"-a");

//$file_list = ftp_rawlist($conn_id, '');
echo '<br />';
//echo ftp_cdup($conn_id);
echo ftp_pwd($conn_id);
$file_list = ftp_rawlist($conn_id,"/Dealer App");


foreach ($file_list as $file)
{
    echo $file."\n";
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