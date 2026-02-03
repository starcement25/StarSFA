<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db("acedns_EMAMI");

$current_date_time = date('d-m-Y H:m:s');
$nick_name = 'EMAMI';
$sql = 'SELECT * FROM location';
$msc = microtime(true);
mysqli_query($link,$sql);
$msc = microtime(true)-$msc;
//echo $msc . ' sec'; // in seconds
//echo ($msc * 1000) . ' ms'; // in millseconds


$myfile = fopen("query_log.txt", "a") or die("Unable to open file!");
$txt = $current_date_time."\t".$nick_name."\t".$sql."\t"."profiling_test.php"."\t".$msc."sec"."\n";
fwrite($myfile, $txt);
fclose($myfile);

?>