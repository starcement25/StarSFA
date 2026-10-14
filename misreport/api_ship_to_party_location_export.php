<?php
require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/*
|--------------------------------------------------------------------------
| Same filters as the report page, applied here with no pagination —
| every row matching the current search is exported, not just the
| currently-viewed page.
|--------------------------------------------------------------------------
*/
$srch_dealer_code = isset($_REQUEST['dealer_code']) ? trim($_REQUEST['dealer_code']) : null;
$srch_dns_code    = isset($_REQUEST['dns_ship_to_party_code']) ? trim($_REQUEST['dns_ship_to_party_code']) : null;
$srch_branch_code = isset($_REQUEST['branch_code']) ? trim($_REQUEST['branch_code']) : null;

$whereClauses = [];
$params       = [];
$types        = '';

if (!empty($srch_dealer_code)) {
    $whereClauses[] = "dealer_code = ?";
    $params[]       = $srch_dealer_code;
    $types         .= 's';
}
if (!empty($srch_dns_code)) {
    $whereClauses[] = "dns_ship_to_party_code = ?";
    $params[]       = $srch_dns_code;
    $types         .= 's';
}
if (!empty($srch_branch_code)) {
    $whereClauses[] = "branch_code = ?";
    $params[]       = $srch_branch_code;
    $types         .= 's';
}

$whereSql = !empty($whereClauses) ? ("WHERE " . implode(" AND ", $whereClauses)) : "";

$sql = "
    SELECT dns_ship_to_party_code, ship_to_party_name, customer_type,
           branch_code, branch_name, dealer_code, dealer_name,
           latitude, longitude, destination_name, status, approve_by,
           created_at, updated_at
    FROM ship_to_party_location
    $whereSql
    ORDER BY id DESC
";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| Stream as CSV
|--------------------------------------------------------------------------
*/
$filename = "ship_to_party_location_" . date('Y-m-d_His') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');

fputcsv($out, [
    'Ship To Party DNS Code', 'Ship To Party Name', 'Customer Type',
    'Branch Code', 'Branch Name', 'Dealer Code', 'Dealer Name',
    'Latitude', 'Longitude', 'Destination Name', 'Status', 'Approve By',
    'Created At', 'Updated At'
]);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($out, [
        $row['dns_ship_to_party_code'],
        $row['ship_to_party_name'],
        $row['customer_type'],
        $row['branch_code'],
        $row['branch_name'],
        $row['dealer_code'],
        $row['dealer_name'],
        $row['latitude'],
        $row['longitude'],
        $row['destination_name'],
        $row['status'],
        $row['approve_by'],
        $row['created_at'] ? date('d-m-Y h:i:s A', strtotime($row['created_at'])) : '',
        $row['updated_at'] ? date('d-m-Y h:i:s A', strtotime($row['updated_at'])) : '',
    ]);
}

mysqli_stmt_close($stmt);
fclose($out);
exit;