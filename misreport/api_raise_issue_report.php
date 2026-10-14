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
<script language="JavaScript" src="calendar3.js"></script>
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
echo "<center>";

$link = mysqli_connect(SERVER, USER, PASSWORD, DB) or die("Database Connection Error.");

// ---------------------------
// Filters
// ---------------------------
$page   = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = ($page - 1) * $limit;

$emp_code    = isset($_GET['emp_code']) ? trim($_GET['emp_code']) : '';
$branch_code = isset($_GET['branch_code']) ? trim($_GET['branch_code']) : '';
$esc_dept    = isset($_GET['esc_dept']) ? trim($_GET['esc_dept']) : '';
$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$from_date   = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date     = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';

// ---------------------------
// Build WHERE clause dynamically
// ---------------------------
$where = [];

if ($emp_code !== '') {
    $emp_code_esc = mysqli_real_escape_string($link, $emp_code);
    $where[] = "emp_code = '$emp_code_esc'";
}

if ($branch_code !== '') {
    $branch_code_esc = mysqli_real_escape_string($link, $branch_code);
    $where[] = "branch_code = '$branch_code_esc'";
}

if ($esc_dept !== '') {
    $esc_dept_esc = mysqli_real_escape_string($link, $esc_dept);
    $where[] = "esc_dept = '$esc_dept_esc'";
}

if ($status !== '') {
    $status_esc = mysqli_real_escape_string($link, $status);
    $where[] = "status = '$status_esc'";
}

if ($from_date !== '' && $to_date !== '') {
    $from_esc = mysqli_real_escape_string($link, $from_date);
    $to_esc   = mysqli_real_escape_string($link, $to_date);
    $where[] = "DATE(created_at) BETWEEN '$from_esc' AND '$to_esc'";
} elseif ($from_date !== '') {
    $from_esc = mysqli_real_escape_string($link, $from_date);
    $where[] = "DATE(created_at) >= '$from_esc'";
} elseif ($to_date !== '') {
    $to_esc = mysqli_real_escape_string($link, $to_date);
    $where[] = "DATE(created_at) <= '$to_esc'";
}

if ($search !== '') {
    $search_esc = mysqli_real_escape_string($link, $search);
    $where[] = "(emp_name LIKE '%$search_esc%' 
                 OR customer_name LIKE '%$search_esc%' 
                 OR customer_code LIKE '%$search_esc%' 
                 OR branch_name LIKE '%$search_esc%')";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// ---------------------------
// Total count for pagination
// ---------------------------
$countSql = "SELECT COUNT(*) AS cnt FROM raise_issue $whereSql";
$countRs  = mysqli_query($link, $countSql);
$countRow = mysqli_fetch_assoc($countRs);
$totalRecords = $countRow['cnt'] ?? 0;
$totalPages   = ceil($totalRecords / $limit);

// ---------------------------
// Fetch records
// ---------------------------
$dataSql = "SELECT * FROM raise_issue $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$dataRs  = mysqli_query($link, $dataSql);

$records = [];
if ($dataRs) {
    while ($row = mysqli_fetch_assoc($dataRs)) {
        $records[] = $row;
    }
}

?>
<style>
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 20px 25px;
        padding: 20px;
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 25px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-group label {
        font-size: 13px;
        font-weight: 600;
        color: #444;
        white-space: nowrap;
    }

    .filter-group input[type="text"],
    .filter-group input[type="date"],
    .filter-group select {
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        min-width: 160px;
    }

    .filter-group.search-field input[type="text"] {
        min-width: 260px;
    }

    .filter-group.search-btn {
        align-self: flex-end;
    }

    .filter-group.search-btn button {
        padding: 9px 22px;
        background: #A92A61;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        white-space: nowrap;
    }

    .filter-group.search-btn button:hover {
        background: #8a2150;
    }

    h2.report-title {
        text-align: center;
        margin: 25px 0;
    }
</style>

<h2 class="report-title">Raise Issue Report</h2>

<form method="GET" class="filter-form">

    <div class="filter-group search-field">
        <label for="search">Search (Emp Name / Customer Name / Customer Code / Branch)</label>
        <input type="text" name="search" id="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    </div>

    <div class="filter-group">
        <label for="emp_code">Emp Code</label>
        <input type="text" name="emp_code" id="emp_code" value="<?= isset($_GET['emp_code']) ? htmlspecialchars($_GET['emp_code']) : '' ?>">
    </div>

    <div class="filter-group">
        <label for="branch_code">Branch Code</label>
        <input type="text" name="branch_code" id="branch_code" value="<?= isset($_GET['branch_code']) ? htmlspecialchars($_GET['branch_code']) : '' ?>">
    </div>

    <div class="filter-group">
        <label for="esc_dept">Department</label>
        <select name="esc_dept" id="esc_dept">
            <option value="">Select Department</option>
            <?php
            $departments = ["Branding", "Trade", "IS", "BD", "Logistics"];
            foreach ($departments as $dept) {
                $selected = (isset($_GET['esc_dept']) && $_GET['esc_dept'] == $dept) ? 'selected' : '';
                echo "<option value=\"$dept\" $selected>$dept</option>";
            }
            ?>
        </select>
    </div>

    <div class="filter-group">
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="">Select Status</option>
            <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="resolved" <?= (isset($_GET['status']) && $_GET['status'] == 'resolved') ? 'selected' : '' ?>>Resolved</option>
            <option value="forwarded" <?= (isset($_GET['status']) && $_GET['status'] == 'forwarded') ? 'selected' : '' ?>>Forwarded</option>
            <option value="closed" <?= (isset($_GET['status']) && $_GET['status'] == 'closed') ? 'selected' : '' ?>>Closed</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="from_date">From Date</label>
        <input type="date" name="from_date" id="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>">
    </div>

    <div class="filter-group">
        <label for="to_date">To Date</label>
        <input type="date" name="to_date" id="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>">
    </div>

    <div class="filter-group search-btn">
        <button type="submit">Search</button>
    </div>

</form>

<br><br><br><br>

<div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="right">
    <a href="api_raise_issue_report_export.php?<?= http_build_query($_GET) ?>" class="btn btn-default">
        <button>Export</button>
    </a>
    <a href="api_raise_issue_report_view.php" class="btn btn-default">
        <button>Reset</button>
    </a>
</div>

<div id="display" style="max-height: 350px; max-width: 1100px; overflow-y: scroll; overflow-x: scroll; margin: 0 auto;">

    <table border="1" style="border-collapse:collapse;" class="border" width="100%">
        <thead>
            <tr class="TDHEAD">
                <th>Sr. No</th>
                <th>Emp Code</th>
                <th>Emp Name</th>
                <th>Branch Code</th>
                <th>Branch Name</th>
                <th>Customer Code</th>
                <th>Customer Name</th>
                <th>Category</th>
                <th>State</th>
                <th>District</th>
                <th>Escalation Dept</th>
                <th>Issue</th>
                <th>Status</th>
                <th>Status Change Date</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($records)): ?>
                <tr>
                    <td colspan="15" style="text-align: center;">No records found.</td>
                </tr>
            <?php else: ?>
                <?php $sr = $offset + 1; ?>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= $sr++ ?></td>
                        <td><?= htmlspecialchars($row['emp_code']) ?></td>
                        <td><?= htmlspecialchars($row['emp_name']) ?></td>
                        <td><?= htmlspecialchars($row['branch_code']) ?></td>
                        <td><?= htmlspecialchars($row['branch_name']) ?></td>
                        <td><?= htmlspecialchars($row['customer_code']) ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['state']) ?></td>
                        <td><?= htmlspecialchars($row['district']) ?></td>
                        <td><?= htmlspecialchars($row['esc_dept']) ?></td>
                        <td><?= htmlspecialchars($row['issue']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                       <td>
    <?= !empty($row['status_cnange_date']) ? date('d-m-Y', strtotime($row['status_cnange_date'])) : '' ?>
</td>

<td>
    <?= !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : '' ?>
</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        // Previous button
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';
        }

        // Always show first page if page > 1
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="' . ($page == 1 ? 'active' : '') . '">1</a>';
        }

        $start = max(2, $page - 2);
        $end = min($totalPages - 1, $page + 2);

        if ($start > 2) {
            echo '<span class="ellipsis">...</span>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="' . $active . '">' . $i . '</a>';
        }

        if ($end < $totalPages - 1) {
            echo '<span class="ellipsis">...</span>';
        }

        if ($totalPages > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="' . ($page == $totalPages ? 'active' : '') . '">' . $totalPages . '</a>';
        }

        if ($page < $totalPages) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page + 1])) . '">Next &raquo;</a>';
        }
        ?>
    </div>

    <?php
    echo "</div>";
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