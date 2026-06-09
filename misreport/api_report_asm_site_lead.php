<?php
ob_start();
session_start();
require_once("../sfa_connection.php");

if (strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR') {
    require("adminUtils_accounts.php");
} else {
    require("adminUtils.php");
}

disphtml("main();");

function main()
{
    require("include/dbcon.php");
    date_default_timezone_set("Asia/Kolkata");

    /* ── Pagination ── */
    $page   = isset($_GET['page'])  ? intval($_GET['page'])  : 1;
    $limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = ($page - 1) * $limit;

    /* ── Filters ── */
    $search    = $_GET['search']    ?? null;
    $asm_filter = $_GET['asm_id']   ?? null;
    $from_date = $_GET['from_date'] ?? null;
    $to_date   = $_GET['to_date']   ?? null;

    /* ── Filter SQL ── */
    $filterSql = "WHERE 1=1";

    if (!empty($search)) {
        $s = mysqli_real_escape_string($link, $search);
        $filterSql .= " AND (
            m.cust_name   LIKE '%$s%'
            OR m.unique_id LIKE '%$s%'
            OR v.emp_name  LIKE '%$s%'
            OR m.district  LIKE '%$s%'
            OR m.state     LIKE '%$s%'
            OR v.asm_name  LIKE '%$s%'
        )";
    }

    if (!empty($asm_filter)) {
        $a = mysqli_real_escape_string($link, $asm_filter);
        $filterSql .= " AND v.asm_id = '$a'";
    }

    if (!empty($from_date)) {
        $filterSql .= " AND v.created_at >= '" . mysqli_real_escape_string($link, $from_date) . " 00:00:00'";
    }

    if (!empty($to_date)) {
        $filterSql .= " AND v.created_at <= '" . mysqli_real_escape_string($link, $to_date) . " 23:59:59'";
    }

    /* ── Count ── */
    $countSql = "SELECT COUNT(v.id) as total
                 FROM new_site_lead_visit_master AS v
                 LEFT JOIN new_site_lead_master  AS m ON v.new_site_lead_id = m.id
                 $filterSql";
    $countResult  = mysqli_query($link, $countSql);
    $countRow     = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'] ?? 0;
    $totalPages   = ceil($totalRecords / $limit);

    /* ── Data ── */
    $sql_main = "SELECT m.*, v.*
                 FROM new_site_lead_visit_master AS v
                 LEFT JOIN new_site_lead_master AS m ON v.new_site_lead_id = m.id
                 $filterSql
                 ORDER BY v.created_at DESC
                 LIMIT $offset, $limit";
    $res_main = mysqli_query($link, $sql_main);

    /* ── Unique ASMs for dropdown ── */
    $sql_asms = "SELECT DISTINCT asm_id, asm_name
                 FROM new_site_lead_visit_master
                 WHERE asm_id IS NOT NULL
                   AND asm_name IS NOT NULL
                   AND TRIM(asm_name) <> ''
                 ORDER BY asm_name ASC";
    $res_asms = mysqli_query($link, $sql_asms);
    $asm_list = [];
    while ($a = mysqli_fetch_assoc($res_asms)) {
        $asm_list[] = $a;
    }

    /* ── Caches ── */
    $emp_cache    = [];
    $branch_cache = [];
    $seen         = [];
    $rn           = $offset + 1;
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<style>
    thead > tr > th {
        border: #524d4f 1px solid;
    }
    td, th {
        text-align: center;
        vertical-align: middle;
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
    @media print {
        form, .pagination, .btn { display: none !important; }
    }
</style>

<h2>New Site Lead Visit Report</h2>

<form method="GET">

    <!-- <label for="search">Search :</label>
    <input type="text" name="search" placeholder="Customer Name / Site ID / District / ASM Name"
           id="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"> -->

    <label for="asm_id">Filter by ASM :</label>
    <select name="asm_id" id="asm_id">
        <option value="">-- All ASMs --</option>
        <?php foreach ($asm_list as $a): ?>
            <option value="<?= htmlspecialchars($a['asm_id']) ?>"
                <?= (isset($_GET['asm_id']) && $_GET['asm_id'] == $a['asm_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['asm_name']) ?> (<?= htmlspecialchars($a['asm_id']) ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label for="from_date">From Date :</label>
    <input type="date" name="from_date" id="from_date"
           value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>">

    <label for="to_date">To Date :</label>
    <input type="date" name="to_date" id="to_date"
           value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>">

    <button type="submit">Search</button>
</form>

<br><br>

<div id="display" style="max-height:350px; max-width:1100px; overflow-y:scroll; overflow-x:scroll;" align="right">
    <button type="button" onclick="exportCSV()" class="btn btn-default">Export</button>
    <a href="?" class="btn btn-default"><button type="button">Reset</button></a>
    <input type="button" value="Print" onclick="window.print();" class="btn btn-default">
</div>

<div id="display" style="max-height:350px; max-width:1100px; overflow-y:scroll; overflow-x:scroll; margin:0 auto;">

<table border="1" style="border-collapse:collapse;" class="border" width="100%" id="rpt">
    <thead>
        <tr class="TDHEAD">
            <th>#</th>
            <th>Transaction ID</th>
            <th>Site Unique ID</th>
            <th>Site Status</th>
            <th>Site Creation Date</th>
            <th>Visit Date &amp; Time</th>
            <th>Employee Code</th>
            <th>Employee Name</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Zone</th>
            <th>Branch</th>
            <th>District</th>
            <th>State</th>
            <th>Customer Name</th>
            <th>Customer Contact No.</th>
            <th>Full Address</th>
            <th>Petty Contractor Regd. In Star Link (Yes/No)</th>
            <th>Petty Contractor - Head Mason Name</th>
            <th>Petty Contractor - Head Mason Contact No.</th>
            <th>Engineer Regd. In Star Stellar (Yes/No)</th>
            <th>Engineer Name</th>
            <th>Engineer Contact No.</th>
            <th>Meeting Person</th>
            <th>Decision Maker</th>
            <th>Site Segment</th>
            <th>Visit Type</th>
            <th>Project Segment</th>
            <th>Type of Construction</th>
            <th>Current Stage of Construction</th>
            <th>Built Up Area</th>
            <th>Site Potential (No. of Bags)</th>
            <th>Consumed Till Date (No. of Bags)</th>
            <th>Balance Potential (No. of Bags)</th>
            <th>Site Category</th>
            <th>Cement Brand Used</th>
            <th>Price Per Bag (RSP)</th>
            <th>Conversion (Yes/No/Retention/Upgrade)</th>
            <th>If Yes - Select Product</th>
            <th>Requested Date of Delivery</th>
            <th>No. of Bags Ordered</th>
            <th>Counter Type</th>
            <th>Counter Code</th>
            <th>Counter Name (Dealer/RSAR/SD)</th>
            <th>Reason for Non-Conversion</th>
            <th>Weather Shield Demo</th>
            <th>Approval Status</th>
            <th>Approval/Reject Date &amp; Time</th>
            <th>ASM Name</th>
            <th>ASM Employee ID</th>
            <th>Actual Date of Delivery</th>
            <th>Delivery Remarks</th>
            <th>Reason for Not Delivery</th>
            <th>Site Status (Master)</th>
        </tr>
    </thead>
    <tbody>
<?php

    if (mysqli_num_rows($res_main) > 0):
        while ($row = mysqli_fetch_assoc($res_main)):

            $uid = $row['unique_id'] ?? '';

            /* new vs existing */
            $sflag = in_array($uid, $seen) ? 'existing' : 'new';
            if ($sflag === 'new') $seen[] = $uid;

            /* employee dns code (cached) */
            $ec = $row['emp_code'] ?? '';
            if ($ec && !isset($emp_cache[$ec])) {
                $r = mysqli_fetch_assoc(mysqli_query($link,
                    "SELECT dns_emp_code FROM employee_master WHERE emp_code='"
                    . mysqli_real_escape_string($link, $ec) . "' LIMIT 1"));
                $emp_cache[$ec] = $r['dns_emp_code'] ?? $ec;
            }
            $emp_dns = $ec ? ($emp_cache[$ec] ?? $ec) : '';

            /* asm dns code (cached) */
            $ai = $row['asm_id'] ?? '';
            if ($ai && !isset($emp_cache[$ai])) {
                $r = mysqli_fetch_assoc(mysqli_query($link,
                    "SELECT dns_emp_code FROM employee_master WHERE emp_code='"
                    . mysqli_real_escape_string($link, $ai) . "' LIMIT 1"));
                $emp_cache[$ai] = $r['dns_emp_code'] ?? $ai;
            }
            $asm_dns = $ai ? ($emp_cache[$ai] ?? $ai) : '';

            /* branch name (cached) */
            $bc = $row['branch'] ?? '';
            if ($bc && !isset($branch_cache[$bc])) {
                $r = mysqli_fetch_assoc(mysqli_query($link,
                    "SELECT branch_name FROM branch_master WHERE branch_code='"
                    . mysqli_real_escape_string($link, $bc) . "' LIMIT 1"));
                $branch_cache[$bc] = $r['branch_name'] ?? $bc;
            }
            $bname = $bc ? ($branch_cache[$bc] ?? $bc) : '';

            /* UTC -> IST */
            $dt = new DateTime($row['created_at'] ?? 'now', new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
            $ist       = $dt->format('d-m-Y h:i A');
            $site_date = $dt->format('d-m-Y');

            /* approval datetime */
            $appr_dt = '';
            if (!empty($row['approval_date_time']))
                $appr_dt = date('d-m-Y h:i A', strtotime($row['approval_date_time']));

            $e = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

            echo "<tr>";
            echo "<td>" . $rn++ . "</td>";
            echo "<td>" . $e($row['transaction_id']) . "</td>";
            echo "<td>" . $e($uid) . "</td>";
            echo "<td>" . ucfirst($sflag) . "</td>";
            echo "<td>" . $site_date . "</td>";
            echo "<td>" . $ist . "</td>";
            echo "<td>" . $e($emp_dns) . "</td>";
            echo "<td>" . $e($row['emp_name']) . "</td>";
            echo "<td>" . $e($row['latitude']) . "</td>";
            echo "<td>" . $e($row['longitude']) . "</td>";
            echo "<td>" . $e($row['zone']) . "</td>";
            echo "<td>" . $e($bname) . "</td>";
            echo "<td>" . $e($row['district']) . "</td>";
            echo "<td>" . $e($row['state']) . "</td>";
            echo "<td>" . $e($row['cust_name']) . "</td>";
            echo "<td>" . $e($row['cust_phn_no']) . "</td>";
            echo "<td>" . $e($row['address']) . "</td>";
            echo "<td>" . $e($row['petty_contractor_registered']) . "</td>";
            echo "<td>" . $e($row['head_mason_name']) . "</td>";
            echo "<td>" . $e($row['head_mason_contact']) . "</td>";
            echo "<td>" . $e($row['engg_registered']) . "</td>";
            echo "<td>" . $e($row['engg_name']) . "</td>";
            echo "<td>" . $e($row['engg_contact']) . "</td>";
            echo "<td>" . $e($row['meeting_person']) . "</td>";
            echo "<td>" . $e($row['decision_maker']) . "</td>";
            echo "<td>" . $e($row['site_segment']) . "</td>";
            echo "<td>" . $e($row['visit_type']) . "</td>";
            echo "<td>" . $e($row['project_segment']) . "</td>";
            echo "<td>" . $e($row['type_of_const']) . "</td>";
            echo "<td>" . $e($row['current_stage_of_construction']) . "</td>";
            echo "<td>" . $e($row['built_up_area']) . "</td>";
            echo "<td>" . $e($row['site_potential']) . "</td>";
            echo "<td>" . $e($row['consumed_till_date']) . "</td>";
            echo "<td>" . $e($row['balance_potential']) . "</td>";
            echo "<td>" . $e($row['site_category']) . "</td>";
            echo "<td>" . $e($row['brand_used']) . "</td>";
            echo "<td>" . $e($row['price_per_bag']) . "</td>";
            echo "<td>" . $e($row['conversion']) . "</td>";
            echo "<td>" . $e($row['select_product']) . "</td>";
            echo "<td>" . $e($row['requested_date']) . "</td>";
            echo "<td>" . $e($row['no_of_bags_ordered']) . "</td>";
            echo "<td>" . $e($row['counter_type']) . "</td>";
            echo "<td>" . $e($row['counter_code']) . "</td>";
            echo "<td>" . $e($row['counter_name']) . "</td>";
            echo "<td>" . $e($row['reason_for_non_conversion']) . "</td>";
            echo "<td>" . $e($row['weather_shield_demo']) . "</td>";
            echo "<td>" . $e($row['approval_status']) . "</td>";
            echo "<td>" . $appr_dt . "</td>";
            echo "<td>" . $e($row['asm_name']) . "</td>";
            echo "<td>" . $e($asm_dns) . "</td>";
            echo "<td>" . $e($row['actual_date_of_delivery']) . "</td>";
            echo "<td>" . $e($row['delivery_remarks']) . "</td>";
            echo "<td>" . $e($row['reason_for_not_delivery']) . "</td>";
            echo "<td>" . $e($row['site_status']) . "</td>";
            echo "</tr>\n";

        endwhile;
    else:
        echo "<tr><td colspan='54' style='text-align:center;'>No records found.</td></tr>";
    endif;

    mysqli_close($link);
?>
    </tbody>
</table>

<!-- ── Pagination ── -->
<div class="pagination">
<?php
    if ($page > 1)
        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';

    if ($page > 1)
        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="' . ($page == 1 ? 'active' : '') . '">1</a>';

    $start = max(2, $page - 2);
    $end   = min($totalPages - 1, $page + 2);

    if ($start > 2)
        echo '<span class="ellipsis">...</span>';

    for ($i = $start; $i <= $end; $i++)
        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';

    if ($end < $totalPages - 1)
        echo '<span class="ellipsis">...</span>';

    if ($totalPages > 1)
        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '" class="' . ($page == $totalPages ? 'active' : '') . '">' . $totalPages . '</a>';

    if ($page < $totalPages)
        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page + 1])) . '">Next &raquo;</a>';
?>
</div>

</div><!-- /#display -->

<script>
function exportCSV() {
    var rows = document.querySelectorAll('#rpt tr');
    var csv  = [];
    rows.forEach(function(tr) {
        var row = [];
        tr.querySelectorAll('td,th').forEach(function(cell) {
            var t = (cell.innerText || cell.textContent || '')
                    .replace(/[\r\n]+/g,' ').replace(/\s+/g,' ').trim();
            if (t.indexOf(',') !== -1 || t.indexOf('"') !== -1)
                t = '"' + t.replace(/"/g,'""') + '"';
            row.push(t);
        });
        csv.push(row.join(','));
    });
    var d  = new Date();
    var fn = 'SiteLead_' + d.getDate() + '-' + (d.getMonth()+1) + '-' + d.getFullYear() + '.csv';
    var b  = new Blob(["\uFEFF" + csv.join('\n')], {type:'text/csv;charset=utf-8;'});
    if (navigator.msSaveOrOpenBlob) { navigator.msSaveOrOpenBlob(b, fn); return; }
    var a  = document.createElement('a');
    a.href = URL.createObjectURL(b); a.download = fn;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
}
</script>

<?php
} // end main()
?>