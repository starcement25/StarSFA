<?php
// define some variables
$local_file = 'CGS6000001.XLS';
$server_file = 'CGS6000001.XLS';

$ftp_server="182.18.176.60";
$ftp_user_name="anonymous";
$ftp_user_pass="anonymous@acedns.in";
// set up basic connection
$conn_id = ftp_connect($ftp_server);

// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// try to download $server_file and save to $local_file
if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
    echo "Successfully written to $local_file\n";
} else {
    echo "There was a problem\n";
}

// close the connection
ftp_close($conn_id);

?>