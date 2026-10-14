<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/
// require_once("../sfa_connection.php");

// header("Content-type: application/text");

// $localDB = new sfa_connection();
// $conn    = $localDB->conn;

// /*
// |--------------------------------------------------------------------------
// | Check emp_code
// |--------------------------------------------------------------------------
// */
// if (!isset($_GET['emp_code']) || trim($_GET['emp_code']) === '') {
//     echo "emp_code is required";
//     exit;
// }

// $emp_Code = trim($_GET['emp_code']);

// /*
// |--------------------------------------------------------------------------
// | Step 1: Validate the given emp_code -> must be designation SO, app_access Y
// |--------------------------------------------------------------------------
// | NOTE: employee_master has both an `acedns` column (varchar) and an
// | `app_access` enum('Y','N') column. This assumes "acedns Y" means the
// | app_access flag. If you actually meant the `acedns` column, change the
// | column name in the two SELECTs below (and the WHERE clause).
// */
// $stmt = mysqli_prepare($conn, "
//     SELECT emp_code, designation, app_access, reporting_to,sale_access,level,branch_code
//     FROM employee_master
//     WHERE emp_code = ?
//     LIMIT 1
// ");
// mysqli_stmt_bind_param($stmt, "s", $emp_Code);
// mysqli_stmt_execute($stmt);
// $result = mysqli_stmt_get_result($stmt);
// $root   = mysqli_fetch_assoc($result);
// mysqli_stmt_close($stmt);

// if (!$root) {
//     echo "emp_code not found";
//     exit;
// }

// if (strcasecmp(trim($root['sale_access']), 'Primary') !== 0 || strcasecmp(trim($root['app_access']), 'Y') !== 0) {
//     echo "emp_code is not an active SO";
//     exit;
// }

// /*
// |--------------------------------------------------------------------------
// | Step 2: Find subordinates -> employees whose reporting_to contains
// | this emp_code (reporting_to is comma-separated, so we pre-filter with
// | LIKE for performance, then verify exactly with explode/trim in PHP to
// | avoid partial-match false positives, e.g. EMP1 matching EMP10).
// |--------------------------------------------------------------------------
// */
// // $likeParam = '%' . $emp_Code . '%';
// // $stmt = mysqli_prepare($conn, "
// //     SELECT emp_code, designation, app_access, reporting_to ,branch_code
// //     FROM employee_master
// //     WHERE reporting_to LIKE ?
// // ");
// // mysqli_stmt_bind_param($stmt, "s", $likeParam);
// // mysqli_stmt_execute($stmt);
// // $result = mysqli_stmt_get_result($stmt);

// // $subordinates = [];
// // while ($row = mysqli_fetch_assoc($result)) {
// //     if (empty($row['reporting_to'])) {
// //         continue;
// //     }

// //     $reportingToList = array_map('trim', explode(',', $row['reporting_to']));

// //     if (!in_array($emp_Code, $reportingToList, true)) {
// //         continue; // LIKE matched a substring, not an exact code
// //     }

// //     // Only keep subordinates that are also active SOs
// //     if (strcasecmp(trim($row['sale_access']), 'Primary') !== 0 || strcasecmp(trim($row['app_access']), 'Y') !== 0) {
// //         continue;
// //     }

// //     $subordinates[] = $row['emp_code'];
// // }
// // mysqli_stmt_close($stmt);

// /*
// |--------------------------------------------------------------------------
// | Step 3: Build the full emp_code set (root SO + qualifying subordinates)
// |--------------------------------------------------------------------------
// | Remove this line if you only want subordinates' data, not the root SO's.
// */
// // $empCodeSet = array_values(array_unique(array_merge([$emp_Code], $subordinates)));

// // if (empty($empCodeSet)) {
// //     echo "0¥10\n";
// //     echo date('Y-m-d') . "€" . date('H:i:s') . "\n";
// //     exit;
// // }

// /*
// |--------------------------------------------------------------------------
// | Step 4: Get related customer_codes from customer_route_emp_relation
// |--------------------------------------------------------------------------
// */
// // $placeholders = implode(',', array_fill(0, count($empCodeSet), '?'));
// // $types        = str_repeat('s', count($empCodeSet));

// // $sql = "
// //     SELECT DISTINCT customer_code
// //     FROM customer_route_emp_relation
// //     WHERE emp_code IN ($placeholders)
// // ";
// // $stmt = mysqli_prepare($conn, $sql);
// // mysqli_stmt_bind_param($stmt, $types, ...$empCodeSet);
// // mysqli_stmt_execute($stmt);
// // $result = mysqli_stmt_get_result($stmt);

// // $customerCodes = [];
// // while ($row = mysqli_fetch_assoc($result)) {
// //     $customerCodes[] = $row['customer_code'];
// // }
// // mysqli_stmt_close($stmt);

// // if (empty($customerCodes)) {
// //     echo "0¥10\n";
// //     echo date('Y-m-d') . "€" . date('H:i:s') . "\n";
// //     exit;
// // }
// // print_r(343434343);die;
// /*
// |--------------------------------------------------------------------------
// | Step 5: Map customer_code -> SAP_customer_code via customer_master
// |--------------------------------------------------------------------------
// | NOTE: assuming customer_master has columns `customer_code` and
// | `SAP_customer_code`. Change the column names below if they differ.
// */
// // $placeholders2 = implode(',', array_fill(0, count($customerCodes), '?'));
// // $types2        = str_repeat('s', count($customerCodes));

// // $sql2 = "
// //     SELECT customer_code, SAP_customer_code
// //     FROM customer_master
// //     WHERE customer_code IN ($placeholders2)
// // ";

// // $stmt2 = mysqli_prepare($conn, $sql2);
// // mysqli_stmt_bind_param($stmt2, $types2, ...$customerCodes);
// // mysqli_stmt_execute($stmt2);
// // $result2 = mysqli_stmt_get_result($stmt2);

// // $sapCustomerCodes = [];
// // while ($row = mysqli_fetch_assoc($result2)) {
// //     if (!empty($row['SAP_customer_code'])) {
// //         $sapCustomerCodes[] = $row['SAP_customer_code'];
// //     }
// // }
// // mysqli_stmt_close($stmt2);
// // $sapCustomerCodes = array_values(array_unique($sapCustomerCodes));

// // if (empty($sapCustomerCodes)) {
// //     echo "0¥10\n";
// //     echo date('Y-m-d') . "€" . date('H:i:s') . "\n";
// //     exit;
// // }

// /*
// |--------------------------------------------------------------------------
// | Step 6: Find matching rows in ship_to_party_location by dealer_code,
// | using the SAP_customer_code instead of the raw customer_code.
// |--------------------------------------------------------------------------
// */
// // $placeholders3 = implode(',', array_fill(0, count($sapCustomerCodes), '?'));
// // $types3        = str_repeat('s', count($sapCustomerCodes));

// $sql3 = "
//     SELECT dns_ship_to_party_code, ship_to_party_name, customer_type,
//            branch_code, branch_name, dealer_code, dealer_name,
//            latitude, longitude, destination_name, status, approve_by,
//            created_at, updated_at
//     FROM ship_to_party_location
//     WHERE FIND_IN_SET(branch_code, ?)
// ";

// $stmt3 = mysqli_prepare($conn, $sql3);
// mysqli_stmt_bind_param($stmt3, $types3, ...$sapCustomerCodes);
// mysqli_stmt_execute($stmt3);
// $result3 = mysqli_stmt_get_result($stmt3);

// $locations = [];
// while ($row = mysqli_fetch_assoc($result3)) {
//     $locations[] = $row;
// }
// mysqli_stmt_close($stmt3);

// /*
// |--------------------------------------------------------------------------
// | Output text format
// |--------------------------------------------------------------------------
// | Header line 1: row count "¥" 10  (10 kept as a fixed tag, matching the
// | sample format's "count¥10" line — change the "10" if it means something
// | specific in your other feeds, e.g. a record-type/version code)
// | Header line 2: date "€" time
// | Then one "^"-delimited line per ship_to_party_location row, with every
// | column except id, in table order.
// */
// $current_date = date('Y-m-d');
// $current_time = date('H:i:s');

// echo count($locations) . "¥14\n";
// echo $current_date . "€" . $current_time . "\n";
// echo "dns_ship_to_party_code#ship_to_party_name#customer_type#branch_code#branch_name#dealer_code#dealer_name#latitude#longitude#destination_name#status#approve_by#created_at#updated_at\n";

// foreach ($locations as $loc) {
//     echo trim($loc['dns_ship_to_party_code']) . "^" .
//          trim($loc['ship_to_party_name'])     . "^" .
//          trim($loc['customer_type'])          . "^" .
//          trim($loc['branch_code'])            . "^" .
//          trim($loc['branch_name'])            . "^" .
//          trim($loc['dealer_code'])            . "^" .
//          trim($loc['dealer_name'])            . "^" .
//          trim($loc['latitude'])               . "^" .
//          trim($loc['longitude'])              . "^" .
//          trim($loc['destination_name'])       . "^" .
//          trim($loc['status'])                 . "^" .
//          trim($loc['approve_by'])             . "^" .
//          trim($loc['created_at'])             . "^" .
//          trim($loc['updated_at'])             . "\n";
// }





/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/

require_once("../sfa_connection.php");

header("Content-Type: text/plain; charset=UTF-8");

$localDB = new sfa_connection();
$conn    = $localDB->conn;


/*
|--------------------------------------------------------------------------
| Step 1: Check emp_code
|--------------------------------------------------------------------------
*/

if (!isset($_GET['emp_code']) || trim($_GET['emp_code']) === '') {
    echo "emp_code is required";
    exit;
}

$emp_Code = trim($_GET['emp_code']);


/*
|--------------------------------------------------------------------------
| Step 2: Get main employee details
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        emp_code,
        designation,
        app_access,
        reporting_to,
        sale_access,
        level,
        branch_code
    FROM employee_master
    WHERE emp_code = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Database prepare error";
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $emp_Code);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$root = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| Step 3: Check employee exists
|--------------------------------------------------------------------------
*/

if (!$root) {
    echo "emp_code not found";
    exit;
}


/*
|--------------------------------------------------------------------------
| Step 4: Validate main employee
|--------------------------------------------------------------------------
|
| Main employee must be:
|
| sale_access = Primary
| app_access  = Y
|
|--------------------------------------------------------------------------
*/

if (
    strcasecmp(trim($root['sale_access']), 'Primary') !== 0 ||
    strcasecmp(trim($root['app_access']), 'Y') !== 0
) {
    echo "emp_code is not an active SO";
    exit;
}


/*
|--------------------------------------------------------------------------
| Step 5: Get main employee branch codes
|--------------------------------------------------------------------------
|
| branch_code can contain:
|
| WB01
|
| OR
|
| WB01,WB02,WB03
|
| OR
|
| WB01, WB02, WB03
|
|--------------------------------------------------------------------------
*/

$branchCodes = [];

if (!empty($root['branch_code'])) {

    $rootBranches = explode(',', $root['branch_code']);

    foreach ($rootBranches as $branch) {

        $branch = trim($branch);

        if ($branch !== '') {
            $branchCodes[] = $branch;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Step 6: Find employees whose reporting_to contains this emp_code
|--------------------------------------------------------------------------
|
| reporting_to is comma-separated.
|
| Example:
|
| E1002 -> E1001,E0400
| E1003 -> E1001
| E1004 -> E1002,E1001
|
| If emp_code = E1001
|
| E1002, E1003 and E1004 will be found.
|
|--------------------------------------------------------------------------
*/

$sqlSubordinates = "
    SELECT
        emp_code,
        designation,
        app_access,
        reporting_to,
        sale_access,
        level,
        branch_code
    FROM employee_master
    WHERE FIND_IN_SET(?, REPLACE(reporting_to, ' ', '')) > 0
";

$stmtSub = mysqli_prepare($conn, $sqlSubordinates);

if (!$stmtSub) {
    echo "Database prepare error";
    exit;
}

mysqli_stmt_bind_param($stmtSub, "s", $emp_Code);

mysqli_stmt_execute($stmtSub);

$resultSub = mysqli_stmt_get_result($stmtSub);


/*
|--------------------------------------------------------------------------
| Step 7: Collect subordinate employee branch codes
|--------------------------------------------------------------------------
*/

$subordinates = [];

while ($row = mysqli_fetch_assoc($resultSub)) {

    /*
    |--------------------------------------------------------------
    | Keep subordinate employee information
    |--------------------------------------------------------------
    */

    $subordinates[] = $row;


    /*
    |--------------------------------------------------------------
    | Get subordinate branch codes
    |--------------------------------------------------------------
    */

    if (!empty($row['branch_code'])) {

        $subordinateBranches = explode(',', $row['branch_code']);

        foreach ($subordinateBranches as $branch) {

            $branch = trim($branch);

            if ($branch !== '') {
                $branchCodes[] = $branch;
            }
        }
    }
}

mysqli_stmt_close($stmtSub);


/*
|--------------------------------------------------------------------------
| Step 8: Remove duplicate branch codes
|--------------------------------------------------------------------------
*/

$branchCodes = array_values(array_unique($branchCodes));


/*
|--------------------------------------------------------------------------
| Step 9: Check whether any branch codes were found
|--------------------------------------------------------------------------
*/

if (empty($branchCodes)) {

    echo "0¥14\n";
    echo date('Y-m-d') . "€" . date('H:i:s') . "\n";

    echo "dns_ship_to_party_code#ship_to_party_name#customer_type#branch_code#branch_name#dealer_code#dealer_name#latitude#longitude#destination_name#status#approve_by#created_at#updated_at\n";

    exit;
}


/*
|--------------------------------------------------------------------------
| Step 10: Convert branch codes into comma-separated string
|--------------------------------------------------------------------------
|
| Example:
|
| WB01
| WB02
| WB03
| WB04
|
| becomes:
|
| WB01,WB02,WB03,WB04
|
|--------------------------------------------------------------------------
*/

$branchCodeList = implode(',', $branchCodes);


/*
|--------------------------------------------------------------------------
| Step 11: Get ship_to_party_location records
|--------------------------------------------------------------------------
|
| branch_code in ship_to_party_location is matched against
| the branch codes collected above.
|
|--------------------------------------------------------------------------
*/

$sqlLocation = "
    SELECT
        dns_ship_to_party_code,
        ship_to_party_name,
        customer_type,
        branch_code,
        branch_name,
        dealer_code,
        dealer_name,
        latitude,
        longitude,
        destination_name,
        status,
        approve_by,
        created_at,
        updated_at
    FROM ship_to_party_location
    WHERE FIND_IN_SET(
        TRIM(branch_code),
        ?
    ) > 0
";

$stmtLocation = mysqli_prepare($conn, $sqlLocation);

if (!$stmtLocation) {
    echo "Database prepare error";
    exit;
}

mysqli_stmt_bind_param(
    $stmtLocation,
    "s",
    $branchCodeList
);

mysqli_stmt_execute($stmtLocation);

$resultLocation = mysqli_stmt_get_result($stmtLocation);


/*
|--------------------------------------------------------------------------
| Step 12: Store locations
|--------------------------------------------------------------------------
*/

$locations = [];

while ($row = mysqli_fetch_assoc($resultLocation)) {
    $locations[] = $row;
}

mysqli_stmt_close($stmtLocation);


/*
|--------------------------------------------------------------------------
| Step 13: Current date/time
|--------------------------------------------------------------------------
*/

$current_date = date('Y-m-d');
$current_time = date('H:i:s');


/*
|--------------------------------------------------------------------------
| Step 14: Output header
|--------------------------------------------------------------------------
*/

echo count($locations) . "¥14\n";

echo $current_date . "€" . $current_time . "\n";


/*
|--------------------------------------------------------------------------
| Step 15: Column header
|--------------------------------------------------------------------------
*/

echo "dns_ship_to_party_code#ship_to_party_name#customer_type#branch_code#branch_name#dealer_code#dealer_name#latitude#longitude#destination_name#status#approve_by#created_at#updated_at\n";


/*
|--------------------------------------------------------------------------
| Step 16: Output location data
|--------------------------------------------------------------------------
*/

foreach ($locations as $loc) {

    echo
        trim($loc['dns_ship_to_party_code']) . "^" .
        trim($loc['ship_to_party_name'])     . "^" .
        trim($loc['customer_type'])          . "^" .
        trim($loc['branch_code'])            . "^" .
        trim($loc['branch_name'])            . "^" .
        trim($loc['dealer_code'])            . "^" .
        trim($loc['dealer_name'])            . "^" .
        trim($loc['latitude'])               . "^" .
        trim($loc['longitude'])              . "^" .
        trim($loc['destination_name'])       . "^" .
        trim($loc['status'])                 . "^" .
        trim($loc['approve_by'])             . "^" .
        trim($loc['created_at'])             . "^" .
        trim($loc['updated_at'])             . "\n";
}

