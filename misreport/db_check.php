<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

define("SERVERREMOTE", "103.87.174.95");
define("USERREMOTE", "starsaat_dnsprod");
define("PASSWORDREMOTE", "dnsprod1234#");
//define("DBREMOTE", "starsaat_START");
define("DBREMOTE", "starsaathi_STARS");

// Attempt to connect
$link = mysqli_connect(SERVERREMOTE, USERREMOTE, PASSWORDREMOTE, DBREMOTE);

// Check connection
if (!$link) {
    die("Database Connection Error: " . mysqli_connect_error());
} else {
    echo "Connection successful to database: " . DBREMOTE;
}

// Run the SHOW TABLES query
$sql = "SHOW TABLES";
$result = mysqli_query($link, $sql);

if ($result) {
    echo "Tables in database '" . DBREMOTE . "':<br><ul>";
    while ($row = mysqli_fetch_row($result)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error running query: " . mysqli_error($link);
}

?>
