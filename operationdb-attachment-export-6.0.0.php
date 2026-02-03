<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/
// require("include/config.php");
// require("include/config-setup.php");
// require("include/dbcon.php");
// require("include/functions.php");
// //require '/home/acedns/public_html/AWS/aws-autoloader.php';
// require 'aws-autoloader.php';

// use Aws\S3\S3Client;
// use Aws\S3\ObjectUploader;


// $emp_code = $_REQUEST['emp_code'];
// $last_update_time = $_REQUEST['last_update_time'];
// $last_update_time = str_replace('€', ' ', $last_update_time);

// /*$sqlquery="SELECT sl_no FROM data_refresh_log WHERE UNIX_TIMESTAMP(refresh_date_time) > UNIX_TIMESTAMP('".$last_update_time."')";
// 	$result = mysqli_query($link,$sqlquery);
// 	$countdatarefresh=mysqli_num_rows($result);*/

// $folderName = $nick_name;
// if (!file_exists("upload/$folderName")) {
// 	mkdir("upload/$folderName", 0777, true);
// 	chmod("upload/$folderName", 0777);
// }

// $upload_dir = "upload/" . $folderName . '/';
// $file_name = $_FILES['file']['name'];
// //print_r($file_name);die;
// $tmp_name = $_FILES['file']['tmp_name'];
// echo $tmp_name;
// $file_size = $_FILES['file']['size'];
// $file_type 	= 'general';
// // print_r($file_size);die;
// if ($file_name != "") // && $file_size < 2097152
// {
// 	if ($folderName == 'START' || $folderName == 'STAR') {
// 		$upload_dir = "uploadtemp/$folderName/";

// 		$upload_file = $upload_dir . $file_name;
// 		//echo $upload_file;die;
// 		if (move_uploaded_file($tmp_name, $upload_file)) {
// 			$filenamezip = "uploadtemp/$folderName/$file_name";
// 			$zip = new ZipArchive;
// 			if ($zip->open($filenamezip)) {
// 				$zip->extractTo("uploadtemp/$folderName/");
// 				$zip->close();
// 			}
			
// 			$bucket_name = 'starcement1-sbinfo-upload';
// 			$access_key_id = "AKIA3XNZJDFD7QSJGKCS";
// 			$secret = "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";
// 			/** AWS S3 Bucket Name */
// 			//$bucket_name = 'starcement';
// 			/** AWS S3 Bucket Access Key ID */
// 			//$access_key_id    = 'AKIA6DWJVZMOS224H6GG';
// 			/** AWS S3 Bucket Secret Access Key */
// 			//$secret = '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';
// 			/** Let's initialize our AWS Client for the file uploads */
// 			// $s3 = new S3Client([
// 			// 	/** Region you had selected, if don't know check in S3 listing */
// 			// 	'region'  => 'ap-south-1',
// 			// 	'version' => 'latest',
// 			// 	/** Your AWS S3 Credential will be added here */
// 			// 	'credentials' => [
// 			// 		'key'    => $access_key_id,
// 			// 		'secret' => $secret,
// 			// 	]
// 			// ]);
// 			// $folder_path = "uploadtemp/$folderName";
// 			// // List of name of files inside 
// 			// // specified folder 
// 			// $files = glob($folder_path . '/*');

// 			// // Deleting all the files in the list 
// 			// foreach ($files as $file) {
// 			// 	//echo $file.'<br />';
// 			// 	$file_parts = explode("/", $file);
// 			// 	$file_name_latest = $file_parts[2];
// 			// 	// Delete the given file 
// 			// 	$acl = 'public-read';
// 			// 	$source = fopen("uploadtemp/$folderName/$file_name_latest", 'rb');

// 			// 	$uploader = new ObjectUploader(
// 			// 		$s3,
// 			// 		$bucket_name,
// 			// 		$file_name_latest,
// 			// 		$source,
// 			// 		 $acl
// 			// 	);

// 			// 	do {
// 			// 		try {
// 			// 			$result = $uploader->upload();
// 			// 			/*if ($result["@metadata"]["statusCode"] == '200') {
// 			// 				print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');
// 			// 			}
// 			// 			print($result);*/
// 			// 			if ($result["@metadata"]["statusCode"] == '200') {
// 			// 				unlink("uploadtemp/$folderName/$file_name_latest");
// 			// 			}
// 			// 		} catch (MultipartUploadException $e) {
// 			// 			rewind($source);
// 			// 			$uploader = new MultipartUploader($s3Client, $source, [
// 			// 				'state' => $e->getState(),
// 			// 			]);
// 			// 		}
// 			// 	} while (!isset($result));
// 			// }
// 			echo $flag = 1;
// 		}
// 		/*else
// 			{
// 				echo $flag=0;
// 			}*/
// 	} else {
// 		$upload_file = $upload_dir . $file_name;
// 		if (move_uploaded_file($tmp_name, $upload_file)) {
// 			$filenamezip = "upload/$folderName/$file_name";
// 			$zip = new ZipArchive;
// 			if ($zip->open($filenamezip)) {
// 				$zip->extractTo("upload/$folderName/");
// 				$zip->close();
// 			}
// 			$sqlupdatelastoperationtime = "UPDATE changepassword SET last_operation_datetime='" . $last_operation_datetime . "' WHERE emp_code='" . $emp_code . "'";
// 			$rsupdatelastoperationtime = mysqli_query($link, $sqlupdatelastoperationtime);

// 			/* if($countdatarefresh >0)
// 			 {
// 				 echo $flag=2;
// 			 }
// 			 else
// 			 {*/
// 			echo $flag = 1;
// 			//}
// 		} else {
// 			echo $flag = 0;
// 		}
// 	}
// } //end of size and file checking
// else {
// 	echo $flag = 0;
// }
// $datetime = gmdate('Y-m-d H:m:s', strtotime('+330 minute'));

// $url = APICALLLOGURL . "/operationdb-attachment-export-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
// insertapilog($datetime, $emp_code, $url, $nick_name);
// mysqli_close($link);


ini_set('max_execution_time', 600); 
set_time_limit(600);

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
require 'aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$emp_code = $_REQUEST['emp_code'];
$last_update_time = $_REQUEST['last_update_time'];
$last_update_time = str_replace('€', ' ', $last_update_time);

$folderName = $nick_name; 
$tempDir = "uploadtemp/$folderName";


if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
    chmod($tempDir, 0777);
}

$file_name = $_FILES['file']['name'] ?? '';
$tmp_name  = $_FILES['file']['tmp_name'] ?? '';
//echo"<pre>";print_r($_FILES);die;

if ($file_name != "" && strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) === 'zip') {

    $zipPath = $tempDir . "/" . $file_name;

    if (move_uploaded_file($tmp_name, $zipPath)) {

        
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            echo "Could not open ZIP";
            exit;
        }

       
        $bucket_name   = 'starcement1-sbinfo-upload';
        $access_key_id = "AKIA3XNZJDFD7QSJGKCS";
        $secret        = "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";

        $s3 = new S3Client([
            'region'      => 'ap-south-1',
            'version'     => 'latest',
            'credentials' => [
                'key'    => $access_key_id,
                'secret' => $secret,
            ]
        ]);

       
        $files = glob("$tempDir/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        $timestamp = time();

        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
           // $newName = $emp_code . "_" . $timestamp . "." . $ext;
$baseName = basename($file);

            try {
                $result = $s3->putObject([
                    'Bucket'     => $bucket_name,
                    'Key'        => $folderName . '/' . $baseName, 
                    'SourceFile' => $file,
                    'ACL'        => 'private'
                ]);

                
                unlink($file);
//echo "✅ File uploaded successfully: " . $result['ObjectURL'];

            } catch (AwsException $e) {
                echo "Error uploading {$newName}: " . $e->getAwsErrorMessage();
            }
        }

       
        unlink($zipPath);

        
        @rmdir($tempDir);

        echo $flag = 1; 

    } else {
        echo $flag = 0; 
    }
} else {
    echo $flag = 0; 
}


$datetime = gmdate('Y-m-d H:i:s', strtotime('+330 minute'));
$url = APICALLLOGURL . "/operationdb-attachment-export-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime, $emp_code, $url, $nick_name);

mysqli_close($link);

