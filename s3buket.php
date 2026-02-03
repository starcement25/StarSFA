<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// AWS credentials
$accessKey = "AKIA3XNZJDFD7QSJGKCS";
$secretKey = "ATObOdktV3/hJYyDeoKKrNp6XaL2o1noZ7rDnsCW";
$bucketName = "starcement1-sbinfo-upload";   // replace with your bucket
$region = "ap-south-1";             // replace with your bucket region

 // File to upload
    //$filePath = __DIR__ . "/testfile.txt";  // local file
    // $filePath = sys_get_temp_dir() . "/testfile.txt";
    // $keyName  = "testfile.txt";     // S3 key (path in bucket)
    $filePath = __DIR__ . "/myimage.jpg"; 
    $keyName  = "myimage.jpg";
    // Check if file exists, if not create it
        if (!file_exists($filePath)) {
            file_put_contents($filePath, "This is a test file uploaded at " . date("Y-m-d H:i:s"));
            echo "Created local file: $filePath\n";
        }
    // Upload file

try {
    // Create an S3 client
    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => $region,
        'credentials' => [
            'key'    => $accessKey,
            'secret' => $secretKey,
        ]
    ]);

//    $result = $s3->getBucketAcl([
//     'Bucket' => 'starcement1-sbinfo-upload'
// ]);
 
// print_r($result);die;

    $result = $s3->putObject([
        'Bucket'     => $bucketName,
        'Key'        => $keyName,
        'SourceFile' => $filePath,
        'ACL'        => 'public-read' // or 'private'
    ]);

    echo "File uploaded successfully: " . $result['ObjectURL'] . PHP_EOL;

} catch (AwsException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
