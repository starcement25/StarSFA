<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
ob_start();

// check error
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$contents='';
$count=0;
$attribute = $_REQUEST['attribute'];

//Customer block
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="customer_master.csv"');
header('Pragma: no-cache');
header('Expires: 0');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate DB connection and query

$foldername = 'STAR'; // Example: can be STAR or ABDOS

$sql = "SELECT DISTINCT CM.dns_customer_code,
    CM.customer_name,
    CM.phone_no,
    (SELECT RM.dns_route_code FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CM.route_code AND CM.customer_code=CR.customer_code ORDER BY CM.acedns DESC LIMIT 0,1) AS route_code, 
    (SELECT RM.route_name FROM route_master RM,customer_route_emp_relation CR WHERE RM.route_code = CM.route_code AND CM.customer_code=CR.customer_code ORDER BY CR.acedns DESC LIMIT 0,1) AS route_name,
    (SELECT area FROM route_master WHERE route_code = CRER.route_code) AS area,
    (SELECT route_no FROM route_master WHERE route_code = CRER.route_code) AS route_no,
    (SELECT GROUP_CONCAT(EM.dns_emp_code SEPARATOR ';') FROM employee_master EM,customer_route_emp_relation CRER WHERE EM.emp_code = CRER.emp_code AND CRER.customer_code=CM.customer_code) AS emp_code_name,
    GROUP_CONCAT(CRER.acedns SEPARATOR ';') AS acedns,
    CM.credit_limit,
    CM.credit_days,
    CM.current_balance,
    CM.black_list,
    CM.TD,
    (SELECT dns_branch_code FROM branch_master WHERE branch_code = CM.branch_code) AS branch_code,
    (SELECT branch_name FROM branch_master WHERE branch_code = CM.branch_code) AS branch_name,
    CM.cust_type,
    (SELECT dns_customer_code FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag) AS rds_tag,
    CM.sauda_validity_period,
    CM.address,
    CM.landline_no,
    CM.owner_name,
    CM.owner_phone,
    CM.cust_class,
    CM.weekly_closing_day,
    CM.coverage_type,
    CM.TIN,
    CM.PAN,
    CM.district,
    CM.minimum_stock,
    CM.bank_name,
    CM.bank_account_number,
    CM.email,
    CM.visit_day,
    CM.state_code,
    CM.monthly_potential,
    CM.sauda_limit,
    CM.incoterms,
    CM.loadability_ton,
    CM.transport_mode,
    CM.sauda_type,
    CM.zone,
    CM.visit_sequence,
    CM.appointment_date,
    CM.date_of_birth,
    CM.date_of_anniversary,
    CM.whatsapp_no,
    CM.beneficiary_name,
    CM.IFS_code,
    CM.pin,
    CM.base_latt,
    CM.base_longi,
    CM.retailer_app,
    CM.is_new_customer,
    CM.category_of_store,
    CM.is_nlp,
    CM.cluster,
    CM.SAP_customer_code
    FROM customer_master CM 
    INNER JOIN customer_route_emp_relation CRER ON CM.customer_code = CRER.customer_code  
    GROUP BY CM.dns_customer_code 
    ORDER BY CM.dns_customer_code ASC";

$res = mysqli_query($link, $sql);
if (!$res) {
    die("Query failed: " . mysqli_error($link));
}

// Define CSV headers
$headers = [
    "DNS Customer Code", "Customer Name", "Phone no", "Route code", "Route Name", "Emp Code name", "acedns",
    "Credit Limit", "Credit Days", "Current Balance", "Black list", "TD", "Branch code", "Branch Name", "Cust type",
    "rds tag", "Sauda validity period", "Address", "Landline no", "Owner name", "Owner phone", "Cust class",
    "Weekly closing day", "Coverage type", "GST", "PAN", "District", "Minimum stock", "Bank name",
    "Bank account number", "Email", "Visit Day", "State", "Monthly Potential", "Sauda limit", "Incoterms",
    "Loadability ton", "Transport mode", "Sauda type", "Zone", "Visit sequence", "Appointment date",
    "date_of_birth", "date_of_anniversary", "whatsapp_no", "beneficiary_name", "IFS_code", "pin",
    "Base lattitude", "Base longitude", "Retailer app", "New Customer", "Category of store", "NLP", "Area", "Route No", "Cluster", "SAP customer code"
];

if (strtoupper($foldername) == 'STAR') {
    $headers[] = "Status";
}
if (strtoupper($foldername) == 'ABDOS') {
    $headers[] = "HQ";
}

// Set CSV output file path
$filename = $_SERVER['DOCUMENT_ROOT'] . "/misreport/dump/$foldername/customer_master.csv";
$fp = fopen($filename, "w");

// Write headers
fputcsv($fp, $headers);

// Write rows
while ($row = mysqli_fetch_assoc($res)) {
    if (empty(trim($row['dns_customer_code'])) || empty(trim($row['customer_name']))) {
        continue;
    }

    $acedns_parts = explode(";", $row['acedns']);
    $status = in_array('Y', $acedns_parts) ? 'Y' : 'N';
    $hq = isset($row['HQ']) ? $row['HQ'] : '';

    $line = [];
    foreach ($headers as $col) {
        $key = strtolower(str_replace([' ', '-', '.'], '_', $col));
        switch ($key) {
            case 'status':
                $line[] = $status;
                break;
            case 'hq':
                $line[] = $hq;
                break;
            default:
                $val = isset($row[$key]) ? $row[$key] : '';
                $line[] = preg_replace('/[\r\n]+/', ' ', $val);
        }
    }

    fputcsv($fp, $line);
}

fclose($fp);

// Send download link
echo "$foldername/customer_master.csv";
?>
