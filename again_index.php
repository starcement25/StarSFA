<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;

//require 'vendor/autoload.php';

//use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$bucketName = "starcement1-sbinfo-upload";
$region     = "ap-south-1";

// Create S3 client with explicit credentials
$s3 = new S3Client([
    'version'     => 'latest',
    'region'      => $region,
    'credentials' => [
        'key'    => "AKIA3XNZJDFD7QSJGKCS",
        'secret' => "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",
    ]
]);

if (isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];

    try {
        $result = $s3->putObject([
            'Bucket'     => $bucketName,
            'Key'        => $fileName,
            'SourceFile' => $fileTmp
            // 'ACL'     => 'public-read' // ❌ remove if Block Public Access is on
        ]);

        echo "✅ File uploaded successfully: " . $result['ObjectURL'];

    } catch (AwsException $e) {
        echo "❌ Error: " . $e->getAwsErrorMessage();
    }
}
?>
<!doctype html>
<html>
  <body>
    <h2>Upload to S3</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="file" required>
      <button type="submit">Upload</button>
    </form>
  </body>
</html>