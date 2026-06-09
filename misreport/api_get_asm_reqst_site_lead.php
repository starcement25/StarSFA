<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn; 

if (!$conn) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Database connection failed"
    ]);
    exit;
}


if (!isset($_GET['asm_id'])) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "ASM Code is required"
    ]);
    exit;
}

$emp_code = trim($_GET['asm_id']);
// echo "ASM_ID = " . $emp_code;
// exit;
// $stmt = $conn->prepare("
//     SELECT m.*, v.*
//     FROM new_site_lead_master AS m
//     LEFT JOIN new_site_lead_visit_master AS v 
//         ON v.new_site_lead_id = m.id
//         AND v.id = (
//             SELECT vv.id 
//             FROM new_site_lead_visit_master vv
//             WHERE vv.new_site_lead_id = m.id
//             ORDER BY vv.id DESC 
//             LIMIT 1
//         )
//     WHERE v.asm_id = ?
// ");

$stmt = $conn->prepare("
    SELECT 
        m.*, 
        v.id, 
        v.new_site_lead_id, 
        v.new_site_lead_unique_id, 
        v.petty_contractor_registered, 
        v.head_mason_name, 
        v.contractor_id, 
        v.head_mason_contact, 
        v.engg_registered, 
        v.engg_name, 
        v.engg_id, 

        v.engg_contact, 
        v.meeting_person, 
        v.decision_maker, 
        v.current_stage_of_construction, 
        v.site_potential, 
        v.consumed_till_date, 
        v.balance_potential, 
        v.site_category, 
        v.brand_used, 
        v.price_per_bag, 
        v.select_product, 
        v.no_of_bags_ordered, 
        v.requested_date, 
        v.counter_type, 
        v.counter_name, 
        v.reason_for_non_conversion, 
        v.weather_shield_demo, 
        v.approval_status, 
        v.approval_date_time, 
        v.asm_name, 
        v.asm_id, 
        v.approval_type, 
        v.actual_date_of_delivery, 
        v.delivery_remarks, 
        v.reason_for_not_delivery, 
        v.visit_type, 
        v.conversion, 
        v.site_status, 
        v.floor_count, 
        v.balance_potential_manual, 
        v.remarks, 
        v.created_at, 
        v.updated_at, 
        e.phone_no,
        v.approved_auto 

    FROM new_site_lead_master m

    LEFT JOIN (
        SELECT new_site_lead_id, MAX(id) AS max_id
        FROM new_site_lead_visit_master
        GROUP BY new_site_lead_id
    ) latest 
        ON latest.new_site_lead_id = m.id

    LEFT JOIN new_site_lead_visit_master v 
        ON v.id = latest.max_id

    LEFT JOIN employee_master e
        ON e.emp_code = m.emp_code

    WHERE v.asm_id = ?
");
$stmt->bind_param("s", $emp_code);

$stmt->execute();
$result = $stmt->get_result();
$total_rows = $result->num_rows;
if ($total_rows == 0) {
    echo "0¥0";
    exit;
}

$rows_output = [];
$columns = [];
$total_cols = 0;

while ($row = $result->fetch_assoc()) {
    if (empty($columns)) {
        $columns = array_keys($row);
        $total_cols = count($columns);
    }
    $values = array_map(function($v) {
        return $v === null ? '' : $v;
    }, $row);
    $rows_output[] = implode('^', $values);
}


echo $total_rows . "¥" . $total_cols . "\n";


echo implode('#', $columns) . "\n";

echo implode("\n", $rows_output) . "\n";

exit;
?>
