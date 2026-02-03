<?php
$config = 'test.txt';
$file=fopen($config,"r+") or exit("Unable to open file!");

$date = date("F j, Y");
$time = date("H:i:s");

$username = "user";
$password = "pass";
$email = "email";
$newuser = $username . " " . $password . " " . $email . " " . $date . " " .    $time."\r\n";   // I added new line after new user
$insertPos=0;  // variable for saving //Users position
while (!feof($file)) {
$line=fgets($file);
if (strpos($line, '//Users')!==false) {
    $insertPos=ftell($file);    // ftell will tell the position where the pointer moved, here is the new line after //Users.
    $newline =  $newuser;
}
else
{
$newline.=$line;   // append existing data with new data of user
}
}

fseek($file,$insertPos);   // move pointer to the file position where we saved above 
fwrite($file, $newline);

fclose($file);
?>