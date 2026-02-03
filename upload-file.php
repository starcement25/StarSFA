<?php
/** Following line for just debugging the errors if any,
 * but you can omit it out */
 ini_set('max_execution_time', 0);
 ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/** Autoload the file with composer autoloader */
require 'aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;
/** AWS S3 Bucket Name */
$bucket_name = 'starcement';

/** AWS S3 Bucket Access Key ID */
$access_key_id    = 'AKIA6DWJVZMOS224H6GG';

/** AWS S3 Bucket Secret Access Key */
$secret = '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';

/** You can generate random file name here */
//$file_name          = 'profile.jpg';

/** Full path of the file where it exists */
//$file_location      = '../assets/images/'. $file_name;

/** With the following code I am fetching the MIME type of the file */
//$finfo              = new finfo(FILEINFO_MIME_TYPE);
//$file_mime          = $finfo->file($file_location);

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
$folder_path = "../upload/STAR"; 
   
// List of name of files inside 
// specified folder 
$files = glob($folder_path.'/*');  
   
// Deleting all the files in the list 
foreach($files as $file) { 
    //echo $file.'<br />';
	$file_parts=explode("/",$file);
	$file_name=$file_parts[3];
	//exit();
   // Delete the given file 
$source = fopen("../upload/STAR/$file_name", 'rb');
$acl='public-read';
$uploader = new ObjectUploader(
    $s3,
    $bucket_name,
    $file_name,
    $source,
	$acl
);

do {
    try {
        $result = $uploader->upload();
        /*if ($result["@metadata"]["statusCode"] == '200') {
            print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');
        }
        print($result);*/
    } catch (MultipartUploadException $e) {
        rewind($source);
        $uploader = new MultipartUploader($s3Client, $source, [
            'state' => $e->getState(),
        ]);
    }
} while (!isset($result));
}
echo 'Success';