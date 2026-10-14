<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/
require_once("../sfa_connection.php");

header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn    = $localDB->conn;

/*
|--------------------------------------------------------------------------
| Read JSON body
|--------------------------------------------------------------------------
| Expected body:
| {
|     "dns_ship_to_party_code": "...",
|     "dealer_code": "...",
|     "latitude": "...",
|     "longitude": "..."
| }
*/
$rawBody = file_get_contents("php://input");
$input   = json_decode($rawBody, true);

if (!is_array($input)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid or missing JSON body'
    ]);
    exit;
}

$requiredFields = ['dns_ship_to_party_code', 'dealer_code', 'latitude', 'longitude'];
$missing        = [];

foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || trim((string) $input[$field]) === '') {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Missing required field(s): ' . implode(', ', $missing)
    ]);
    exit;
}

$dns_ship_to_party_code = trim($input['dns_ship_to_party_code']);
$dealer_code            = trim($input['dealer_code']);
$latitude               = trim($input['latitude']);
$longitude              = trim($input['longitude']);

/*
|--------------------------------------------------------------------------
| Confirm the row exists (identified by dns_ship_to_party_code + dealer_code)
|--------------------------------------------------------------------------
| NOTE: assuming this pair uniquely identifies one row. If a single
| dns_ship_to_party_code can legitimately have multiple dealer_code rows
| (or vice versa), this is still correct — it just means every row
| matching BOTH fields will be updated, not necessarily just one.
*/
$stmt = mysqli_prepare($conn, "
    SELECT id
    FROM ship_to_party_location
    WHERE dns_ship_to_party_code = ? AND dealer_code = ?
");
mysqli_stmt_bind_param($stmt, "ss", $dns_ship_to_party_code, $dealer_code);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$matchedIds = [];
while ($row = mysqli_fetch_assoc($result)) {
    $matchedIds[] = $row['id'];
}
mysqli_stmt_close($stmt);

if (empty($matchedIds)) {
    echo json_encode([
        'success' => false,
        'error'   => 'No matching row found for the given dns_ship_to_party_code and dealer_code'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Update latitude/longitude
|--------------------------------------------------------------------------
*/
$stmt = mysqli_prepare($conn, "
    UPDATE ship_to_party_location
    SET latitude = ?, longitude = ?, updated_at = NOW()
    WHERE dns_ship_to_party_code = ? AND dealer_code = ?
");
mysqli_stmt_bind_param($stmt, "ssss", $latitude, $longitude, $dns_ship_to_party_code, $dealer_code);

$success = mysqli_stmt_execute($stmt);

if (!$success) {
    echo json_encode([
        'success' => false,
        'error'   => mysqli_stmt_error($stmt)
    ]);
    mysqli_stmt_close($stmt);
    exit;
}

$affectedRows = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

echo json_encode([
    'success'                 => true,
    'dns_ship_to_party_code'  => $dns_ship_to_party_code,
    'dealer_code'             => $dealer_code,
    'latitude'                => $latitude,
    'longitude'               => $longitude,
    'rows_matched'            => count($matchedIds),
    'rows_updated'            => $affectedRows
]);