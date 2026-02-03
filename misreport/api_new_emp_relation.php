<?php

require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection (must be a mysqli connection object)
$localDB = new sfa_connection();
$conn = $localDB->conn;

// If $conn is not a mysqli resource/object, stop early
if (!$conn || !function_exists('mysqli_query')) {
    header("Content-Type: text/plain");
    echo json_encode([
        "process_status" => "No",
        "process_message" => "DB connection or mysqli not available"
    ]);
    exit;
}

// --------------------------------------------------
// 1. EMPLOYEE NAME LIST
// --------------------------------------------------

$employeeNamesString = "
Biswarup Naskar,Bhramar Ghosh,Subham Kumar Basak,Shubhayan Jana,
Aakash Kumar Sah,Samim Mondal,Subrata Das,Tausif Alam,Sovan Biswas,
Arindam Bardhan,Vacant,Sanjib Mandal,Manajit Paul,Vacant,Manotosh Mal,
Md Shadab,Pradeep Kumar,Manzar Alam,Kuldeep Yadav,Vinod Kumar Singh,
Nityananda Singh,Md Masum,Arun Kumar Pandit,Md Nasim Alam,Md Nasim Alam,
Manoj Kumbhakar,Vacant,Krishna Kumar,Lawkush Kumar,JAHIDUL ISLAM AKANDA,
DEBASISH BORDOLOI,PAMCHONG,KARTIK PAUL,ALI AMJAD LASKAR,PARAG KUMAR DAS,
JANARDAN BHATTACHARYA,ROHAN KUMAR,TRINAYAN BHUYAN,SUGREEV KUMAR VERMA,
KHOI KHIAMNIUNGAN,KILEMSUNGBA AO,JANARDAN BHUYAN,BAHARUL ISLAM,
KUSHAL BASUMATARY,RAJESH MEDOK,VACANT,CHOW ASAWON,KOKMA PRANJAL,
KURMI ABHILASH BHATTACHARYA,MANAS JYOTI GOGOI,ROKTIM DUARAH,MUKESH GUPTA,
HARIPRASAD SAIKIA,NIRANJAN DUTTA,NEELOMJYOTI HAZARIKA,RAJIBUL ALI,
TRIJYOTI DAS,N. NUTESHOR SINGH,BUNGCHA MOIRANGTHEM,RITESH KHATIWADA,
ELIZER SHADAP,HRISHIKESH SHARMA,NABAJYOTI BHARALI,KALLOL DAS,
MASUM AHMED,SAJNUR ALI AHMED,DEEP JYOTI PATGIRI,ABINASH KALITA,
HIMANGSHU DEBNATH,ANKUR DAS,MAZNUR HOQUE,JAYANTA KALITA,SHYAMAL DAS,
ARIFUL ISLAM,MANDEEP BARMAN,KOUSHIK GOSWAMI,TASNUR ISLAM,ASHIK NIYAZ AHMED,
MIRJA MASUD GALIB,PARAMANIK SUNNY SAHA,SOFIUR RAHMAN,MANJIT SARKAR,
BHARGAB DAS,RAJSHEKHAR ROY,SHUBHANKAR DAS,MASHUD AHMED LASKAR,
SULTAN AHMED LASKAR,DIPU LAISHRAM,ZORAMMAWII,AJOY MAHISHYA DAS,
PURBITA DEB,TRISHA LASKAR,TRISHIT BISWAS,ATANU SANTRA,RAKESH DEBNATH,
JOYANTA MALAKAR,GARMEN JOY REANG,RAHUL SEN GUPTA
";

$employeeNames = array_map('trim', explode(",", $employeeNamesString));

// --------------------------------------------------
// 2. FIND EMPLOYEES (NO emp_code INPUT REQUIRED)
// --------------------------------------------------

$selectedEmployees = [];

foreach ($employeeNames as $name) {

    if ($name === "" || strtolower($name) === 'vacant') continue;

    // Prefer exact name match; if your DB has different case or trailing spaces, adjust the query.
    $nameEsc = mysqli_real_escape_string($conn, $name);

    $sql = "SELECT emp_code, emp_name, level 
            FROM employee_master 
            WHERE emp_name = '$nameEsc' AND acedns='Y' LIMIT 1";

    $res = mysqli_query($conn, $sql);

    if ($res === false) {
        // log or continue on error
        error_log("Query failed: " . mysqli_error($conn) . " -- SQL: $sql");
        continue;
    }

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if (!empty($row['emp_code'])) {
            $selectedEmployees[] = $row;
        }
    }
}

// --------------------------------------------------
// 3. BUILD FINAL HIERARCHY
// --------------------------------------------------

$final = [];

foreach ($selectedEmployees as $emp) {

    $empCode = $emp['emp_code'];

    $hier = getReportingChain($conn, $empCode);

    foreach ($hier as $h) {

        $managerCode = $h['reports_to_code'];

        // sanitize manager code for the query
        $mgrEsc = mysqli_real_escape_string($conn, $managerCode);

        $q = "SELECT emp_code, emp_name, level 
              FROM employee_master 
              WHERE emp_code='$mgrEsc' AND acedns='Y'";

        $r = mysqli_query($conn, $q);

        if ($r && mysqli_num_rows($r) > 0) {
            while ($row = mysqli_fetch_assoc($r)) {
                $final[$row['emp_code']] = $row; // unique by emp_code
            }
        }
    }
}

// --------------------------------------------------
// 4. OUTPUT CSV STYLE
// --------------------------------------------------

header("Content-Type: text/plain");

echo count($final) . "¥3\n";

foreach ($final as $e) {
    echo $e['emp_code'] . "^" . $e['emp_name'] . "^" . $e['level'] . "\n";
}

exit;


// --------------------------------------------------
// 5. REPORTING CHAIN USING mysqli_*
// --------------------------------------------------

function getReportingChain($conn, $empCode, &$visited = [])
{
    $chain = [];

    $empCodeEsc = mysqli_real_escape_string($conn, $empCode);

    if (in_array($empCodeEsc, $visited)) return $chain;

    $visited[] = $empCodeEsc;

    $sql = "SELECT emp_code, emp_name, reporting_to, level 
            FROM employee_master 
            WHERE emp_code='$empCodeEsc' AND acedns='Y' LIMIT 1";

    $res = mysqli_query($conn, $sql);

    if ($res === false) {
        error_log("getReportingChain query failed: " . mysqli_error($conn) . " -- SQL: $sql");
        return $chain;
    }

    if ($row = mysqli_fetch_assoc($res)) {

        $reportingTo = isset($row['reporting_to']) ? $row['reporting_to'] : '';

        // reporting_to may be comma-separated list of manager codes
        $managers = array_filter(array_map('trim', explode(",", $reportingTo)));

        foreach ($managers as $mgr) {

            if ($mgr === '') continue;

            $chain[] = [
                "reports_to_code" => $mgr,
                "level" => $row["level"]
            ];

            // recursive climb
            $chain = array_merge($chain, getReportingChain($conn, $mgr, $visited));
        }
    }

    return $chain;
}
