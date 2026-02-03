<?php
$path = "/home/acedns/public_html/STAR-bkup"; 
//$path = "../STAR-bkup/"; 
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);
    return ($files) ? $files : false;
}
$result=scan_dir($path);
$newest_file = $result[0];
header("Content-disposition: attachment; filename=$newest_file");
header('Content-type: application/zip');
readfile("../STAR-bkup/".$newest_file);
?>