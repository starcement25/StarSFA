<?php
header('Content-Type: text/csv');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

/* ================= GET TYPE PARAM ================= */
$type = isset($_GET['type']) ? strtoupper(trim($_GET['type'])) : '';

if($type != 'NE' && $type != 'ROE'){
    echo "Invalid type. Use NE or ROE";
    exit;
}

/* ================= FILE NAME ================= */
$file_name = "sample_".$type.".csv";
header("Content-Disposition: attachment;filename=$file_name");

/* ================= GET COMPETITORS ================= */
$sql = "SELECT competitor_name 
        FROM competitor_group_master_potential
        WHERE status='yes' 
        AND competitor_type='".mysqli_real_escape_string($link,$type)."'
        ORDER BY id ASC";

$res = mysqli_query($link, $sql);

/* ================= HEADER ================= */
$header = array();
$header[] = "Customer SFA Code";
//$header[] = "Quantity(MT)";
$header[] = "Type";

/* Dynamic columns */
$competitors = array();

while($row = mysqli_fetch_assoc($res)){
    $competitors[] = $row['competitor_name'];
    $header[] = $row['competitor_name'];
}

$header[] = "Active";

/* ================= OUTPUT ================= */
$output = fopen("php://output", "w");

/* Header */
fputcsv($output, $header);

/* Sample row */
$sample = array();
$sample[] = "WBM008";
//$sample[] = "10";
$sample[] = $type;

/* Dynamic competitor values */
foreach($competitors as $c){
    $sample[] = "0";
}

$sample[] = "yes";

fputcsv($output, $sample);

fclose($output);
exit;
?>