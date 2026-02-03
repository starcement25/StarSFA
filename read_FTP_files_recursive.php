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
$file_list = ftp_rawlist($conn_id, '/',true);
echo '<br />';
echo ftp_pwd($conn_id);

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
ftp_close($conn_id);
var_dump($file_list);
?>