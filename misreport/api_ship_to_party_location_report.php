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

$localDB = new sfa_connection();
$conn = $localDB->conn;

header("product:index.php");
disphtml("main();");

function main(){
require("include/dbcon.php");
global $conn;

date_default_timezone_set("Asia/Kolkata");
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="JavaScript" src="calendar3.js"></script>
<script type="text/javascript" src="jquery.highlight.js"></script>
<?php
echo "<center>";

/*
|--------------------------------------------------------------------------
| Filters: dealer_code, dns_ship_to_party_code, branch
|--------------------------------------------------------------------------
| NOTE: "branch" is filtered here on branch_code. If your users actually
| type/select branch_name instead, swap the column below (or filter both
| with an OR — say the word and I'll add it).
*/
$srch_dealer_code = isset($_GET['dealer_code']) ? trim($_GET['dealer_code']) : null;
$srch_dns_code    = isset($_GET['dns_ship_to_party_code']) ? trim($_GET['dns_ship_to_party_code']) : null;
$srch_branch_code = isset($_GET['branch_code']) ? trim($_GET['branch_code']) : null;

$page   = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| Build WHERE clause dynamically from whichever filters were supplied
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Total count (for pagination)
|--------------------------------------------------------------------------
*/
$countSql  = "SELECT COUNT(*) AS total FROM ship_to_party_location $whereSql";
$countStmt = mysqli_prepare($conn, $countSql);
if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}
mysqli_stmt_execute($countStmt);
$countResult  = mysqli_stmt_get_result($countStmt);
$totalRecords = (int) (mysqli_fetch_assoc($countResult)['total'] ?? 0);
mysqli_stmt_close($countStmt);

$totalPages = $totalRecords > 0 ? (int) ceil($totalRecords / $limit) : 0;

/*
|--------------------------------------------------------------------------
| Fetch the page of records (all columns except id are displayed; id is
| still selected so the Reset Location button can target the right row)
|--------------------------------------------------------------------------
*/
$dataSql = "
    SELECT id, dns_ship_to_party_code, ship_to_party_name, customer_type,
           branch_code, branch_name, dealer_code, dealer_name,
           latitude, longitude, destination_name, status, approve_by,
           created_at, updated_at
    FROM ship_to_party_location
    $whereSql
    ORDER BY id DESC
    LIMIT ? OFFSET ?
";
$dataStmt   = mysqli_prepare($conn, $dataSql);
$dataTypes  = $types . 'ii';
$dataParams = array_merge($params, [$limit, $offset]);
mysqli_stmt_bind_param($dataStmt, $dataTypes, ...$dataParams);
mysqli_stmt_execute($dataStmt);
$dataResult = mysqli_stmt_get_result($dataStmt);

$records = [];
while ($row = mysqli_fetch_assoc($dataResult)) {
    $records[] = $row;
}
mysqli_stmt_close($dataStmt);
?>
<h2>Ship To Party Location Report (<?php echo $totalRecords; ?>)</h2>

<form method="GET">

   <label for="dealer_code">Dealer/Sub Dealer Code:</label>
   <input type="text" name="dealer_code" id="dealer_code" value="<?= isset($_GET['dealer_code']) ? htmlspecialchars($_GET['dealer_code']) : '' ?>">

   <label for="dns_ship_to_party_code">Ship To Party DNS Code:</label>
   <input type="text" name="dns_ship_to_party_code" id="dns_ship_to_party_code" value="<?= isset($_GET['dns_ship_to_party_code']) ? htmlspecialchars($_GET['dns_ship_to_party_code']) : '' ?>">

   <label for="branch_code">Branch Code:</label>
   <input type="text" name="branch_code" id="branch_code" value="<?= isset($_GET['branch_code']) ? htmlspecialchars($_GET['branch_code']) : '' ?>">

    <button type="submit">Search</button>
    <a href="ship_to_party_location_report.php" class="btn btn-default">
        <button type="button">Reset</button>
    </a>
</form>

<div id="display" style="max-width:1200px;" align="right">
    <form method="GET" action="api_ship_to_party_location_export.php" target="_blank" style="display:inline;">
        <input type="hidden" name="dealer_code" value="<?= isset($_GET['dealer_code']) ? htmlspecialchars($_GET['dealer_code']) : '' ?>">
        <input type="hidden" name="dns_ship_to_party_code" value="<?= isset($_GET['dns_ship_to_party_code']) ? htmlspecialchars($_GET['dns_ship_to_party_code']) : '' ?>">
        <input type="hidden" name="branch_code" value="<?= isset($_GET['branch_code']) ? htmlspecialchars($_GET['branch_code']) : '' ?>">
        <button type="submit" class="btn btn-default">Export</button>
    </form>
</div>

<br><br>

<div id="display" style="max-height: 350px; max-width: 1200px; overflow-y: scroll; overflow-x: scroll; margin: 0 auto;">
    <table border="1" style="border-collapse:collapse;" class="border" width="100%">
        <thead>
            <tr class="TDHEAD">
                <th>Sr. No</th>
                <th>Ship To Party DNS Code</th>
                <th>Ship To Party Name</th>
                <th>Customer Type</th>
                <th>Branch Code</th>
                <th>Branch Name</th>
                <th>Dealer/Sub Dealer Code</th>
                <th>Dealer/Sub Dealer Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Destination Name</th>
                <!-- <th>Status</th>
                <th>Approve By</th> -->
                <th>Created At</th>
                <th>Updated At</th>
                <th>Reset Location</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($records)): ?>
            <tr>
                <td colspan="16" style="text-align: center;">No records found.</td>
            </tr>
        <?php else: ?>
            <?php $sr = $offset + 1; foreach ($records as $row): ?>
                <tr data-id="<?= (int)$row['id'] ?>">
                    <td><?= $sr++ ?></td>
                    <td><?= htmlspecialchars($row['dns_ship_to_party_code']) ?></td>
                    <td><?= htmlspecialchars($row['ship_to_party_name']) ?></td>
                    <td><?= htmlspecialchars($row['customer_type']) ?></td>
                    <td><?= htmlspecialchars($row['branch_code']) ?></td>
                    <td><?= htmlspecialchars($row['branch_name']) ?></td>
                    <td><?= htmlspecialchars($row['dealer_code']) ?></td>
                    <td><?= htmlspecialchars($row['dealer_name']) ?></td>
                    <td class="lat-cell"><?= htmlspecialchars($row['latitude']) ?></td>
                    <td class="lng-cell"><?= htmlspecialchars($row['longitude']) ?></td>
                    <td><?= htmlspecialchars($row['destination_name']) ?></td>
                    <!-- <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['approve_by']) ?></td> -->
                    <td><?= htmlspecialchars(date('d-m-Y h:i A', strtotime($row['created_at']))) ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y h:i A', strtotime($row['updated_at']))) ?></td>
                    <td align="center">
                        <a href="javascript:void(0);"
                           onclick="resetLatLong(<?= (int)$row['id'] ?>, this)"
                           title="Reset latitude/longitude to 0"
                           style="color:#F00;">
                            <img src="images/edit_icon.gif" alt="Reset">
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';
        }
        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="' . ($page == 1 ? 'active' : '') . '">1</a>';
        }

        $start = max(2, $page - 2);
        $end   = min($totalPages - 1, $page + 2);

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
</div>

<script>
function resetLatLong(id, linkEl) {
    if (!confirm("Reset latitude/longitude for this record?")) {
        return;
    }
    fetch("api_reset_ship_to_party_latlong.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            var row = linkEl.closest("tr");
            row.querySelector(".lat-cell").textContent = "0";
            row.querySelector(".lng-cell").textContent = "0";
            alert("Location reset successfully.");
        } else {
            alert("Failed to reset location: " + (data.message || "Unknown error"));
        }
    })
    .catch(function(err) {
        alert("Request failed: " + err);
    });
}
</script>

<?php
echo "</center>";
?>
<style>
    .table {
        width: fit-content;
        block-size: fit-content;
    }
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