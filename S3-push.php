<?php
set_time_limit(0);
//include "s_connection.php";

require 'aws-autoloader.php';
	use Aws\S3\S3Client;
	use Aws\S3\ObjectUploader;

//$file = 'aceshop_dbnew2.sql.gz';
//$file = "STAR-db-backup-".date("Y_m_d_h_i_s").".sql.gz";
/*$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));

$file = 'STAR-db-backup-'.$date.$month.$year.$hour.$minute.$second.'.sql.gz'; 
$remote_file = 'STAR-bkup/'.$file;

$mysqlExportPath ='public_html/'.$remote_file;

//DO NOT EDIT BELOW THIS LINE   // $mysqlPassword ='PharmacyFR12#';
//Export the database and output the status to the page
$command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' | gzip > ~/' .$mysqlExportPath;

$output=array();
exec($command,$output,$worked);
switch($worked){
case 0:
echo 'Database <b>' .$mysqlDatabaseName .'</b> successfully exported to <b>~/' .$mysqlExportPath .'</b>';*/
/** AWS S3 Bucket Name */
			$bucket_name = 'backup-ace';
			/** AWS S3 Bucket Access Key ID */
			$access_key_id    = 'AKIATJ5RAQGVHRZGCPNZ';
			/** AWS S3 Bucket Secret Access Key */
			$secret = '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';
			/** Let's initialize our AWS Client for the file uploads */
			$s3 = new S3Client([
				/** Region you had selected, if don't know check in S3 listing */
				'region'  => 'ap-south-1',
				'version' => 'latest',
				/** Your AWS S3 Credential will be added here */
				'credentials' => [
					'key'    => $access_key_id,
					'secret' => $secret,
				]
			]);
			$folder_path = "../STAR-bkup"; 
			// List of name of files inside 
			// specified folder 
			$files = glob($folder_path.'/*');  
			   
			// Deleting all the files in the list 
			$date=gmdate('d',strtotime('+330 minute'));
			//$date=($date-1);
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			foreach($files as $file) { 
				//echo $file.'<br />';
				$file_parts=explode("/",$file);
				$file_name_latest=$file_parts[2];
			   // Delete the given file 
			   $acl='public-read';
			   $file_parts=explode("-",$file_name_latest);
				//echo substr($file_parts[2],5,4);
				if(substr($file_parts[3],0,8)==($date-1).$month.$year)
				{
					$file_name_previous=$file_name_latest;
				}
				if(substr($file_parts[3],0,8)==$date.$month.$year)
				{
				$source = fopen("../STAR-bkup/$file_name_latest", 'r+');
				
				$uploader = new ObjectUploader(
					$s3,
					$bucket_name,
					'root/' .$file_name_latest,
					$source
				);
			
			do {
				try {
					$result = $uploader->upload();
					if ($result["@metadata"]["statusCode"] == '200') {
						print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');
					}
					print($result);
					if ($result["@metadata"]["statusCode"] == '200') {
						unlink("../STAR-bkup/$file_name_previous");
					}
				} catch (MultipartUploadException $e) {
					rewind($source);
					$uploader = new MultipartUploader($s3Client, $source, [
						'state' => $e->getState(),
					]);
				}
			} while (!isset($result));
			
				}
			
			}
/*break;
case 1:
echo 'There was a warning during the export of <b>' .$mysqlDatabaseName .'</b> to <b>~/' .$mysqlExportPath .'</b>';
break;
case 2:
echo 'There was an error during export. Please check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr></table>';
break;
}
mysqli_close();*/
?>