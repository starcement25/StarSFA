<?php 
    
$zipName = 'files.zip';

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($zipName));

readfile($zipName);

    exit;


?>