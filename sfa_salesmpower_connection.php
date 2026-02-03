<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// define("SERVERREMOTE", "103.242.119.68");
// define("USERREMOTE", "acedns_dnsprod");
// define("PASSWORDREMOTE", "dnsprod1234#");
// define("DBREMOTE", "acedns_STAR");

// define("SERVER", "localhost");
// define("USER", "root");
// define("PASSWORD", "Passw0rd123#$");
// define("DB", "acedns_STAR");

// Remote DB connection
// $linkremote = mysqli_connect(SERVERREMOTE, USERREMOTE, PASSWORDREMOTE, DBREMOTE);

// if (!$linkremote) {
//     die("Remote Database Connection Error: " . mysqli_connect_error());
// }

// Local DB connection
// $link = mysqli_connect(SERVER, USER, PASSWORD, DB);

// if (!$link) {
//     die("Local Database Connection Error: " . mysqli_connect_error());
// }

$mysqli = new mysqli("localhost", "root", "Passw0rd123#$", "acedns_STAR");

if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

$from_date = '2025-08-07';

//$from_date = '2025-08-07';

$sql = "
INSERT INTO competitor_pricing (
    sl, emp_code, customer_code,
    star_PTD, star_PTR, star_PTC, star_PV,
    ambuja_PTD, ambuja_PTR, ambuja_PTC, ambuja_PV,
    ultratech_PTD, ultratech_PTR, ultratech_PTC, ultratech_PV,
    lafarge_PTD, lafarge_PTR, lafarge_PTC, lafarge_PV,
    dalmia_PTD, dalmia_PTR, dalmia_PTC, dalmia_PV,
    topcem_PTD, topcem_PTR, topcem_PTC, topcem_PV,
    acc_PTD, acc_PTR, acc_PTC, acc_PV,
    birla_gold_PTD, birla_gold_PTR, birla_gold_PTC, birla_gold_PV,
    date_time
)
SELECT
    sl, emp_code, customer_code,
    star_PTD, star_PTR, star_PTC, star_PV,
    ambuja_PTD, ambuja_PTR, ambuja_PTC, ambuja_PV,
    ultratech_PTD, ultratech_PTR, ultratech_PTC, ultratech_PV,
    lafarge_PTD, lafarge_PTR, lafarge_PTC, lafarge_PV,
    dalmia_PTD, dalmia_PTR, dalmia_PTC, dalmia_PV,
    topcem_PTD, topcem_PTR, topcem_PTC, topcem_PV,
    acc_PTD, acc_PTR, acc_PTC, acc_PV,
    birla_gold_PTD, birla_gold_PTR, birla_gold_PTC, birla_gold_PV,
    date_time
FROM competitor_pricing_live
WHERE date_time >= '2025-08-06'
ON DUPLICATE KEY UPDATE
    emp_code = VALUES(emp_code),
    customer_code = VALUES(customer_code),
    star_PTD = VALUES(star_PTD),
    star_PTR = VALUES(star_PTR),
    star_PTC = VALUES(star_PTC),
    star_PV = VALUES(star_PV),
    ambuja_PTD = VALUES(ambuja_PTD),
    ambuja_PTR = VALUES(ambuja_PTR),
    ambuja_PTC = VALUES(ambuja_PTC),
    ambuja_PV = VALUES(ambuja_PV),
    ultratech_PTD = VALUES(ultratech_PTD),
    ultratech_PTR = VALUES(ultratech_PTR),
    ultratech_PTC = VALUES(ultratech_PTC),
    ultratech_PV = VALUES(ultratech_PV),
    lafarge_PTD = VALUES(lafarge_PTD),
    lafarge_PTR = VALUES(lafarge_PTR),
    lafarge_PTC = VALUES(lafarge_PTC),
    lafarge_PV = VALUES(lafarge_PV),
    dalmia_PTD = VALUES(dalmia_PTD),
    dalmia_PTR = VALUES(dalmia_PTR),
    dalmia_PTC = VALUES(dalmia_PTC),
    dalmia_PV = VALUES(dalmia_PV),
    topcem_PTD = VALUES(topcem_PTD),
    topcem_PTR = VALUES(topcem_PTR),
    topcem_PTC = VALUES(topcem_PTC),
    topcem_PV = VALUES(topcem_PV),
    acc_PTD = VALUES(acc_PTD),
    acc_PTR = VALUES(acc_PTR),
    acc_PTC = VALUES(acc_PTC),
    acc_PV = VALUES(acc_PV),
    birla_gold_PTD = VALUES(birla_gold_PTD),
    birla_gold_PTR = VALUES(birla_gold_PTR),
    birla_gold_PTC = VALUES(birla_gold_PTC),
    birla_gold_PV = VALUES(birla_gold_PV),
    date_time = VALUES(date_time)



";

if (!$mysqli->query($sql)) {
    die("Insert error: " . $mysqli->error);
}

echo "Rows inserted: " . $mysqli->affected_rows;

$mysqli->close();
?>