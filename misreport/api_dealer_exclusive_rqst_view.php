<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR')
{
	require("adminUtils_accounts.php");
}
else
{
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require("attribute_selection.php");
require_once("../sfa_connection.php");
header("product:index.php");
disphtml("main();");

 function main(){
require("include/dbcon.php");


date_default_timezone_set("Asia/Kolkata");
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="JavaScript" src="calendar3.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script type="text/javascript" src="jquery.highlight.js"></script>
<script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>

<script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
<script>
    webshims.setOptions('waitReady', false);
    webshims.setOptions('forms-ext', {
        types: 'date'
    });
    webshims.polyfill('forms forms-ext');
</script>
<?php
$hidden = " hidden";
echo "<center>";



//$sathi_url = "https://dev.starsaathi.com/SAP/";
$sathi_url = SAATHI_URL;
$apiUrl = $sathi_url . "/SAP/api_report_for_dealer_exclusive.php";


$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = ($page - 1) * $limit;

$month = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;
$approval_status = isset($_GET['status']) ? $_GET['status'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$emp_code=$_SESSION['admin_login']  ?? '';
// echo"<pre>";print_r($approval_status);die;
$queryParams = [
    'emp_code' => $emp_code,
    'page' => $page,
    'limit' => $limit,
    'month' => $month,
    'year' => $year,
    'status'=>$approval_status,
    'search' => $search,
];


$queryParams = array_filter($queryParams, function ($v) {
    return !is_null($v) && $v !== '';
});


$queryString = http_build_query($queryParams);


$fullUrl = $apiUrl . '?' . $queryString;
// print_r($fullUrl);die;
$response = file_get_contents($fullUrl);


if ($response === false) {
    echo "Error fetching data from API.";
    exit;
}

$data = json_decode($response, true);
$data = json_decode($response, true);
//echo"<pre>";print_r($data);die;
$records = $data['data'] ?? [];
$totalRecords = $data['total_records'] ?? 0;
$totalPages = ceil($totalRecords / $limit);
// echo"<pre>";print_r($data);die;


?>
<h2>Exclusive Dealer Declaration Report </h2>

<form method="GET">

   <label for="search">Search (Dealer Name / ID / Code):</label>
<input type="text" name="search" id="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">

    <label for="year">Year:</label>
    
    <select name="year" id="year">
        <option value="">Select Year</option>
        <?php
        $startYear = 2023;
        $endYear = date('Y');

        for ($y = $startYear; $y <= $endYear; $y++) {
            $selected = (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '';
            echo "<option value=\"$y\" $selected>$y</option>";
        }
        ?>
    </select>

  
    <label for="month">Month:</label>
    <select name="month" id="month">
        <option value="">Select Month</option>
        <?php
        $months = [
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December"
        ];

        foreach ($months as $key => $value) {
            $selected = (isset($_GET['month']) && $_GET['month'] == $key) ? 'selected' : '';
            echo "<option value=\"$key\" $selected>$value</option>";
        }
        ?>
    </select>


     <label for="year">Approval Status:</label>
    <select name="status" id="status">
    <option value="">Select Status</option>
    <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Rejected</option>
    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Approved</option>
     <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Pending</option>
</select>

    <button type="submit">Search</button>
</form>

<br><br><br><br>

    <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="right">
        <?php if (!empty($records)): ?>
        <a href="<?php echo $sathi_url; ?>/SAP/api_export_dealer_exclusive.php?emp_code=<?php echo $_SESSION['admin_login']  ?>&month=<?php echo $_GET['month'] ?>&year=<?php echo $_GET['year'] ?>&status=<?php echo $_GET['status'] ?>&search=<?php echo $_GET['search'] ?>" class="btn btn-default" value="Export" style="align='center'">
            <button>Export</button>
        </a>
        <?php endif ?>
        <a href="api_dealer_exclusive_rqst_view.php" class="btn btn-default" value="Export" style="align='center'">
            <button>Reset</button>
        </a>
    </div>

<div id="display" style="max-height: 350px; max-width: 1100px; overflow-y: scroll; overflow-x: scroll; margin: 0 auto;">




    


        <table border="1" style="border-collapse:collapse;" class="border" width="100%">
            <thead>
                <tr class="TDHEAD">
                    <th>Sr. No</th>
                    <th>Dealer Code</th>
                    <th>Dealer ID</th>
                    <th>Dealer Name</th>
                    <th>Month</th>
                    <th>Branch</th>
                    <th>Lifting Qty(MT)</th>
                    <th>ASM Approval Status</th>
                    <th>Approved By ASM</th>
                    <th>ASM Approved Date</th>
                    <th>RSM Approval Status</th>
                    <th>Approved By RSM</th>
                    <th>RSM Approved Date</th>
                     <th>Reason For Rejection</th>
                     <th>Exclusive Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="17" style="text-align: center;">No records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $row): ?>
                      
                        <tr>
                           
                            <td><?= htmlspecialchars($row['Sr. No']) ?></td>
                            <td><?= htmlspecialchars($row['Dealer Code']) ?></td>
                             <td><?= htmlspecialchars($row['Dealer ID']) ?></td>
                            <td><?= htmlspecialchars($row['Dealer Name']) ?></td>
                            <td><?= htmlspecialchars($row['Month']) ?></td>
                            <td><?= htmlspecialchars($row['Branch']) ?></td>
                            <td><?= htmlspecialchars($row['Lifting Qty']) ?></td>
                            <td><?= htmlspecialchars($row['ASM Approval Status']) ?></td>
                            <td><?= htmlspecialchars($row['Approved By ASM']) ?></td>
                            <td><?= htmlspecialchars($row['ASM Approved Date']) ?></td>
                            <td><?= htmlspecialchars($row['RSM Approval Status']) ?></td>
                            <td><?= htmlspecialchars($row['Approved By RSM']) ?></td>
                            <td><?= htmlspecialchars($row['RSM Approved Date']) ?></td>
                            <td><?= htmlspecialchars($row['Reason for rejection']) ?></td>
                              <td><?= htmlspecialchars($row['Status']) ?></td>
                            <td><?= htmlspecialchars($row['Created At']) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
   





    <div class="pagination">
        <?php
        /// Get current page and total pages dynamically
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $totalPages = $totalPages; // You must define this earlier from your data

        // Previous button
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';
        }

        // Always show first page if page > 1
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="' . ($page == 1 ? 'active' : '') . '">1</a>';
        }

        // Determine start and end of visible range
        $start = max(2, $page - 2);
        $end = min($totalPages - 1, $page + 2);

        // Ellipsis before the range
        if ($start > 2) {
            echo '<span class="ellipsis">...</span>';
        }

        // Visible page numbers
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="' . $active . '">' . $i . '</a>';
        }

        // Ellipsis after the range
        if ($end < $totalPages - 1) {
            echo '<span class="ellipsis">...</span>';
        }

        // Always show last page
        if ($totalPages > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="' . ($page == $totalPages ? 'active' : '') . '">' . $totalPages . '</a>';
        }

        // Next button
        if ($page < $totalPages) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page + 1])) . '">Next &raquo;</a>';
        }
        ?>
    </div>
    <?php

    echo "</center>";
    ?>
    <style>
        .table {
            width: fit-content;
            block-size: fit-content;
        }

        .view {
            margin: auto;
            width: 600px;
        }

        .wrapper {
            position: relative;
            overflow: auto;
            border: 1px solid black;
            white-space: nowrap;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: #A92A61;
        }

        .sticky-cols {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
        }

        .first-col {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            left: 0px;
        }

        .second-col {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            left: 100px;
        }

        .id-col {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            left: 2px;
        }

        .max-col {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
            left: 2px;
        }
    </style>
    <style>
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px 0;
            gap: 6px;
        }

        .pagination a,
        .pagination span.ellipsis {
            padding: 8px 14px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #007bff;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
            font-weight: bold;
        }

        .pagination .ellipsis {
            color: #777;
            pointer-events: none;
        }
    </style>
    <?php
    }
    ?>