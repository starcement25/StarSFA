<?php
$filename = "Customer Master.csv"; // your CSV file

if (!file_exists($filename)) {
    die("File not found!");
}

$rows = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($rows as $i => $line) {
    $cols = str_getcsv($line);
    $count = count($cols);

    // Detect invalid UTF-8 characters
    if (!mb_check_encoding($line, 'UTF-8')) {
        $error = "⚠️ Non-UTF8 character(s) found";
    } 
    // Detect unwanted control/special characters
    elseif (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $line)) {
        $error = "⚠️ Control character(s) found";
    } 
    // Detect unprintable / non-ASCII if required
    elseif (preg_match('/[^\x09\x0A\x0D\x20-\x7E]/', $line)) {
        $error = "⚠️ Non-ASCII or special symbols found";
    } 
    else {
        $error = "";
    }

    echo "Line " . ($i + 1) . " → $count columns";
    if ($error) echo " | $error";
    echo "<br>";
}
?>
