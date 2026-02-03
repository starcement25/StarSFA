<?php
if (ob_get_length()) ob_end_clean();
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="khoj_site_visit_report.csv"');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$search         = $_GET['search'] ?? null;
$route_code     = $_GET['route_code'] ?? null;
$visited_by     = $_GET['visited_by'] ?? null;
$cust_phone     = $_GET['cust_phone'] ?? null;
$from_date      = $_GET['from_date'] ?? null;
$to_date        = $_GET['to_date'] ?? null;
$limit          = $_GET['limit'] ?? 10000;
$offset         = $_GET['offset'] ?? 0;


$filterSql = "WHERE 1=1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $filterSql .= " AND (
        sm.site_code LIKE '%$search%' 
        OR sm.site_name LIKE '%$search%' 
        OR sm.cust_phone LIKE '%$search%'
    )";
}

if (!empty($route_code)) {
    $filterSql .= " AND sm.route_code = '" . mysqli_real_escape_string($conn, $route_code) . "'";
}

if (!empty($cust_phone)) {
    $filterSql .= " AND sm.cust_phone = '" . mysqli_real_escape_string($conn, $cust_phone) . "'";
}

if (!empty($visited_by)) {
    $filterSql .= " AND svm.visited_by = '" . mysqli_real_escape_string($conn, $visited_by) . "'";
}

if (!empty($from_date)) {
    $filterSql .= " AND svm.created_at >= '$from_date 00:00:00'";
}

if (!empty($to_date)) {
    $filterSql .= " AND svm.created_at <= '$to_date 23:59:59'";
}


   $dataSql = "
   SELECT 
    sm.*, 
    svm.*,
    rm.route_name,
    bm.branch_name,
    cm.customer_name AS rssd,
    em1.emp_name AS approved_by,
    em2.emp_name AS visited_by
FROM site_master sm
LEFT JOIN site_visit_master svm ON sm.id = svm.site_id
LEFT JOIN route_master rm ON sm.route_code = rm.route_code
LEFT JOIN branch_master bm ON sm.branch_code = bm.branch_code
LEFT JOIN customer_master cm ON svm.rssd = cm.customer_code
LEFT JOIN employee_master em1 ON svm.approved_by = em1.emp_code
LEFT JOIN employee_master em2 ON svm.visited_by = em2.emp_code
$filterSql
ORDER BY sm.created_at DESC
LIMIT $offset, $limit
";

$result = mysqli_query($conn, $dataSql);

// echo "<pre>";
// while ($row = mysqli_fetch_assoc($result)) {
//     print_r($row); // or var_dump($row);
// }
// echo "</pre>";
// exit;
//$filename = "unique_site_id_report.csv";
//header('Content-Type: text/csv');
//header('Content-Disposition: attachment; filename="' . $filename . '"');


$output = fopen('php://output', 'w');


fputcsv($output, [
    "Site Code", "Route Name", "Customer Phone", "Customer Name", "Address", "Site Name", 
    "Branch Name", "State", "District", "Latitude", "Longitude", 
    "Meeting Person Type", "Meeting Person Phone", "Contractor Name", "Contractor Phone", 
    "Engineer Name", "Engineer Phone", "Engineer Reg Star Stellar", 
    "Site Segment", "Project Segment", "Type Of Construction", "Site Potential", 
    "Construction Stage", "Cement Brand", "Price Per Bag", "Consumed Till Date", 
    "Estimated Requirement", "Built Up Area", "Decision Maker", "Product Demo", 
    "Remarks", "Visit Type", "Visit Sub Type", "Date Of Delivery", "Bags Ordered", 
    "RSSD", "Approved By", "Visited By", "Created At"
]);


while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['site_code'] ?? '',
        $row['route_name'] ?? '',
        $row['cust_phone'] ?? '',
        $row['cust_name'] ?? '',
        $row['address'] ?? '',
        $row['site_name'] ?? '',
        $row['branch_name'] ?? '',
        $row['state'] ?? '',
        $row['district'] ?? '',
        $row['latitude'] ?? '',
        $row['longitude'] ?? '',
        $row['meeting_person_type'] ?? '',
        $row['meeting_person_phone'] ?? '',
        $row['contractor_name'] ?? '',
        $row['contractor_phone'] ?? '',
        $row['engineer_name'] ?? '',
        $row['engineer_phone'] ?? '',
        $row['engg_reg_star_stellar'] ?? '',
        $row['site_segment'] ?? '',
        $row['project_segment'] ?? '',
        $row['type_of_construction'] ?? '',
        $row['site_potential'] ?? '',
        $row['construction_stage'] ?? '',
        $row['cement_brand'] ?? '',
        $row['price_per_bag'] ?? '',
        $row['consumed_till_date'] ?? '',
        $row['estimated_req'] ?? '',
        $row['built_up_area'] ?? '',
        $row['decision_maker'] ?? '',
        $row['product_demo'] ?? '',
        $row['remarks'] ?? '',
        $row['visit_type'] ?? '',
        $row['visit_sub_type'] ?? '',
        $row['date_of_delivery'] ?? '',
        $row['bags_ordered'] ?? '',
        $row['rssd'] ?? '',
        $row['approved_by'] ?? '',
        $row['visited_by'] ?? '',
        $row['created_at'] ?? ''
    ]);
}

fclose($output);
exit;
