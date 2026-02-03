<?php
/*$destination = "sftp://sftp-09:Magik#09@115.240.2.102:22/sftp-09/Distributor Transactions_2021_02_19.csv";*/
/*$destination = "sftp://sftp-09@115.240.2.102";
//$filedestination = fopen($destination, "r");
//$alldata=file_get_contents($filedestination);
//print_r($alldata);

$files = glob($destination.'/*');
foreach($files as $file) { 
    echo $file.'<br />';
}*/
$connection = ssh2_connect('115.240.2.102', 22);
ssh2_auth_password($connection, 'sftp-09', 'Magik#09');

$sftp = ssh2_sftp($connection);
$sftp_fd = intval($sftp);

$handle = opendir("ssh2.sftp://$sftp_fd/sftp-09/");
echo "Directory handle: $handle\n";
echo "Entries:\n";
while (false != ($entry = readdir($handle))){
    echo "$entry<br />";
}

/*$ftp_server='115.240.2.102';
// set up basic ssl connection
$conn_id = ftp_ssl_connect($ftp_server);
$ftp_user_name='sftp-09';
$ftp_user_pass='Magik#09';
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

if (!$login_result) {
    // PHP will already have raised an E_WARNING level message in this case
    die("can't login");
}

echo ftp_pwd($conn_id); // /

// close the ssl connection
ftp_close($conn_id);

/*$ftp_server='103.87.174.7';
// set up basic ssl connection
$conn_id = ftp_ssl_connect($ftp_server);
$ftp_user_name='ccareforcempower';
$ftp_user_pass='csl@5091';
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

if (!$login_result) {
    // PHP will already have raised an E_WARNING level message in this case
    die("can't login");
}

echo ftp_pwd($conn_id); // /

// close the ssl connection
ftp_close($conn_id);*/
/*include('Net/SFTP.php');

$sftp = new Net_SFTP('115.240.2.102');
if (!$sftp->login('sftp-09', 'Magik#09')) {
    exit('Login Failed');
}

echo $sftp->pwd() . "\r\n";
$sftp->put('filename.ext', 'hello, world!');
print_r($sftp->nlist());*/
?>