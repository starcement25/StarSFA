<?php
// set_time_limit(0);
// require_once("sfa_connection.php");
// date_default_timezone_set("Asia/Kolkata");
// $mysqlDatabaseName ='acedns_STAR';
// $mysqlUserName ='root';
// $mysqlPassword ='Passw0rd123#$';
// $mysqlHostName ='localhost';

// /*require 'aws-autoloader.php';
// 	use Aws\S3\S3Client;
// 	use Aws\S3\ObjectUploader;*/

// //$file = 'aceshop_dbnew2.sql.gz';
// //$file = "STAR-db-backup-".date("Y_m_d_h_i_s").".sql.gz";
// $date=gmdate('d',strtotime('+330 minute'));
// $month=gmdate('m',strtotime('+330 minute'));
// $year=gmdate('Y',strtotime('+330 minute'));
// $hour=gmdate('H',strtotime('+330 minute'));
// $minute=gmdate('i',strtotime('+330 minute'));
// $second=gmdate('s',strtotime('+330 minute'));

// $file = 'STAR-db-backup-'.$date.$month.$year.$hour.$minute.$second.'.sql.gz';
// $remote_file = 'STAR-bkup/'.$file;

// $mysqlExportPath ='public_html/'.$remote_file;

// //DO NOT EDIT BELOW THIS LINE   // $mysqlPassword ='PharmacyFR12#';
// //Export the database and output the status to the page
// $command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' | gzip > ~/' .$mysqlExportPath;

// $output=array();
// exec($command,$output,$worked);
// switch($worked){
// case 0:
// echo 'Database <b>' .$mysqlDatabaseName .'</b> successfully exported to <b>~/' .$mysqlExportPath .'</b>';
// /** AWS S3 Bucket Name */

// break;
// case 1:
// echo 'There was a warning during the export of <b>' .$mysqlDatabaseName .'</b> to <b>~/' .$mysqlExportPath .'</b>';
// break;
// case 2:
// echo 'There was an error during export. Please check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr></table>';
// break;
// }
// mysqli_close();



error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
require_once("sfa_connection.php");
date_default_timezone_set("Asia/Kolkata");


$localDB = new sfa_connection();

$mysqlHostName   = $localDB->host;
$mysqlUserName   = $localDB->user;
$mysqlPassword   = $localDB->password;
$mysqlDatabaseName = $localDB->database;


$date   = date('d');
$month  = date('m');
$year   = date('Y');
$hour   = date('H');
$minute = date('i');
$second = date('s');

$file = "STAR-db-backup-{$date}{$month}{$year}{$hour}{$minute}{$second}.sql.gz";
$remote_file = "STAR-bkup/{$file}";


$mysqlExportPath = "public_html/{$remote_file}";


$command = "mysqldump --opt -h {$mysqlHostName} -u {$mysqlUserName} -p\"{$mysqlPassword}\" {$mysqlDatabaseName} | gzip > ~/{$mysqlExportPath}";


$output = [];
exec($command, $output, $worked);


switch ($worked) {
    case 0:
        echo "Database <b>{$mysqlDatabaseName}</b> successfully exported to <b>~/$mysqlExportPath</b>";
        break;
    case 1:
        echo " Warning during export of <b>{$mysqlDatabaseName}</b> to <b>~/$mysqlExportPath</b>";
        break;
    case 2:
        echo " Error during export. Please check your credentials:<br><br>
              <table border='1' cellpadding='5'>
                  <tr><td>MySQL Database Name:</td><td><b>{$mysqlDatabaseName}</b></td></tr>
                  <tr><td>MySQL User Name:</td><td><b>{$mysqlUserName}</b></td></tr>
                  <tr><td>MySQL Password:</td><td><b>NOT SHOWN</b></td></tr>
                  <tr><td>MySQL Host Name:</td><td><b>{$mysqlHostName}</b></td></tr>
              </table>";
        break;
}

mysqli_close($localDB->conn);
?>


