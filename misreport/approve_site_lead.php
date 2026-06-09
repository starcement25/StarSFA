<?php
require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;
$created_at = date('Y-m-d H:i:s');
// $sql1 = "INSERT INTO new_site_lead_log (emp_code, content, created_at, updated_at)
//         VALUES ('testing', 'testing', '$created_at', '$created_at')";
//         mysqli_query($conn, $sql1);


$site_id = $_GET['site_id'] ?? '';
$approval_status = $_GET['approval_status'] ?? '';
//echo"<pre>";print_r($_GET);die;

if($site_id == '' || $approval_status == ''){
    echo "Invalid request";
    exit;
}
//block the update if approval_status is already set (Approved/Rejected). 17-03-25 sk 

$check = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT approval_status 
     FROM new_site_lead_visit_master 
     WHERE new_site_lead_unique_id = '$site_id'
     ORDER BY id DESC LIMIT 1"
));

if ($check) {
    $status = strtolower($check['approval_status']);

    if ($status == 'approved' || $status == 'rejected') {
        echo "<h2 style='color:red;'>Already ".ucfirst($status)."</h2>";
        echo "<br><br>
            <button onclick='closeTab()' 
            style='padding:10px 20px;background:#ff0000;color:#fff;border:none;border-radius:5px;cursor:pointer;'>
            Close Window
            </button>

            <script>
            function closeTab() {
                window.open('', '_self');
                window.close();
            }
            </script>";
        exit;
    }
}

$postData = [
    "site_id" => $site_id,
    "approval_status" => $approval_status,
    "actual_date_of_delivery" => '',
    "delivery_remarks" => '',
    "reason_for_not_delivery" =>'',
    
    
];

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => SFA_URL."api_asm_approve_site_lead.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ]
]);

 $response = curl_exec($ch);
curl_close($ch);

echo "<h2>Site Lead ".ucfirst($approval_status)." Successfully</h2>";


$data = json_decode($response, true);
$site_master = $data['site_master'];
$visit = $data['site_visit_master'];
echo "
<table border='1' cellpadding='8'>

<tr>
<td><b>Customer Name</b></td>
<td>{$site_master['cust_name']}</td>
</tr>

<tr>
<td><b>Address</b></td>
<td>{$site_master['address']}</td>
</tr>

<tr>
<td><b>Dealer</b></td>
<td>{$visit['counter_name']}</td>
</tr>

<tr>
<td><b>Product</b></td>
<td>{$visit['select_product']}</td>
</tr>

<tr>
<td><b>Bags</b></td>
<td>{$visit['no_of_bags_ordered']}</td>
</tr>

<tr>
<td><b>Request Date</b></td>
<td>{$visit['requested_date']}</td>
</tr>

<tr>
<td><b>Status</b></td>
<td>{$visit['approval_status']}</td>
</tr>

<tr>
<td><b>BDE Name</b></td>
<td>{$site_master['emp_name']}</td>
</tr>

</table>
<br><br>
<button onclick='closeTab()' 
style='padding:10px 20px;background:#ff0000;color:#fff;border:none;border-radius:5px;cursor:pointer;'>
Close Window
</button>

<script>
function closeTab() {
    window.open('', '_self');
    window.close();
}
</script>
";
/*
{"process_status":"Yes","process_message":"Success","site_id":"2602267355","site_master":{"id":"1181","transaction_id":"SUE231120260226112436","unique_id":"2602267355","visit_date":"2026-02-26 00:00:00","emp_code":"E2311","emp_name":"TRINAYAN BHUYAN","zone":"NE1","branch":"B220","district":"Sonitpur","state":"Assam","longitude":"88.4356296","latitude":"22.5802295","cust_name":"Prabin Saikia","cust_phn_no":"9365147355","address":"pub dubia","site_segment":"Trade","visit_type":"Star Site","project_segment":"Assam Type House","type_of_const":"Ground","built_up_area":"1000","no_of_bag":null,"conversion":"Retention","site_priority":"Warm","counter_code":"C\/0193529","created_at":"2026-02-27 05:27:27","updated_at":"2026-02-27 05:27:27"},"site_visit_master":{"id":"1364","new_site_lead_id":"1181","new_site_lead_unique_id":"2602267355","petty_contractor_registered":"No","head_mason_name":null,"contractor_id":null,"head_mason_contact":null,"engg_registered":"No","engg_name":null,"engg_id":null,"engg_contact":null,"meeting_person":"IHB","decision_maker":"IHB","current_stage_of_construction":"Brick Work","site_potential":"500","consumed_till_date":"200","balance_potential":"300","site_category":"Medium","brand_used":"STAR PPC","price_per_bag":"480","select_product":"STAR PPC","no_of_bags_ordered":"50","requested_date":"2026-02-21","counter_type":"Sub Dealer","counter_name":"GOPAL'S HARDWARE & STATIONERY (RSSG211)","reason_for_non_conversion":null,"weather_shield_demo":"Yes","approval_status":"rejected","approval_date_time":"2026-03-09 07:15:14","asm_name":"PRAKASH GHIMRE","asm_id":"E1751","approval_type":"0","actual_date_of_delivery":null,"delivery_remarks":null,"reason_for_not_delivery":null,"visit_type":"Star Site","conversion":"Retention","site_status":"Open","floor_count":"Ground","balance_potential_manual":"300","remarks":null,"created_at":"2026-03-09 05:35:08","updated_at":"2026-03-09 05:35:08"}}
*/