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

// ---------------------------------------------------------------------
// Helpers for employee-hierarchy based customer restriction.
// These run against the LOCAL db ($conn / acedns_STAR), which holds
// its own employee_master with emp_code / dns_emp_code / reporting_to.
// ---------------------------------------------------------------------

/**
 * Walks reporting_to downward from $rootEmpCode and returns the root
 * employee plus every subordinate found (any depth), as emp_code values.
 *
 * NOTE: reporting_to is a comma-separated list on this table (an employee
 * can report to more than one manager, e.g. "E2352,E0938,E1147,E0555"),
 * not a single value — so we can't use a plain IN() match against the
 * whole column. FIND_IN_SET() checks membership within that CSV list
 * correctly (and unlike LIKE '%E0555%', it won't false-match on a code
 * that's a substring of another, e.g. E0555 inside E05551).
 */
function getAllSubordinateEmpCodes($conn, $rootEmpCode) {
    $allEmpCodes = [$rootEmpCode]; // manager sees their own records too
    $currentLevel = [$rootEmpCode];

    while (!empty($currentLevel)) {
        $conditions = array_fill(0, count($currentLevel), 'FIND_IN_SET(?, reporting_to)');
        $sql = "SELECT emp_code FROM employee_master WHERE " . implode(' OR ', $conditions);
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            break;
        }
        $types = str_repeat('s', count($currentLevel));
        mysqli_stmt_bind_param($stmt, $types, ...$currentLevel);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $nextLevel = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $code = $row['emp_code'];
            if (!in_array($code, $allEmpCodes, true)) {
                $allEmpCodes[] = $code;
                $nextLevel[] = $code;
            }
        }
        mysqli_stmt_close($stmt);
        $currentLevel = $nextLevel;
    }

    return $allEmpCodes;
}

/**
 * Given local emp_code values, returns their dns_emp_code values
 * (skipping any that are empty/NULL).
 */
function getDnsEmpCodes($conn, $empCodes) {
    if (empty($empCodes)) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($empCodes), '?'));
    $sql = "SELECT dns_emp_code FROM employee_master
            WHERE emp_code IN ($placeholders)
              AND dns_emp_code IS NOT NULL AND dns_emp_code != ''";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return [];
    }
    $types = str_repeat('s', count($empCodes));
    mysqli_stmt_bind_param($stmt, $types, ...$empCodes);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $dnsCodes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dnsCodes[] = $row['dns_emp_code'];
    }
    mysqli_stmt_close($stmt);
    return array_values(array_unique($dnsCodes));
}

// ---------------------------------------------------------------------
// Shared HTTP fetch helper with real error visibility.
// file_get_contents() on an https:// URL just returns false on ANY
// failure (DNS, TLS handshake, timeout, non-2xx response, etc.) with
// no detail unless you dig through php.ini error settings — so we use
// curl instead and log exactly what failed and where.
// ---------------------------------------------------------------------
function fetchFromApi($url, $postFields = null, $timeoutSeconds = 15) {
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeoutSeconds,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true,
    ];
    // POST keeps params (esp. long customer_ids lists) out of the URL,
    // avoiding "Request-URI Too Long" once the list grows past a few
    // hundred codes. Only fall back to a plain GET when no fields given.
    if ($postFields !== null) {
        $opts[CURLOPT_POST]       = true;
        $opts[CURLOPT_POSTFIELDS] = $postFields; // array -> multipart/form-data, both fine with $_REQUEST
    }
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErrNo = curl_errno($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("[allocation_history] curl fetch failed (errno $curlErrNo): $curlErr | url: $url");
        return false;
    }
    if ($httpCode >= 400) {
        error_log("[allocation_history] API returned HTTP $httpCode | url: $url | body: " . substr($response, 0, 500));
        return false;
    }
    return $response;
}

/**
 * Calls the new starsaathi endpoint to resolve dns_emp_codes into the
 * customer_codes those employees are allowed to see. Returns [] on any
 * failure — callers must treat [] as "no access", not "no filter".
 */
function fetchAuthorizedCustomerCodes($sathiUrl, $dnsEmpCodes) {
    if (empty($dnsEmpCodes)) {
        return [];
    }
    // NOTE: get_authorized_dealer_reallocation.php also needs to read
    // dns_emp_codes via $_REQUEST (not just $_GET) for this POST to work —
    // same change as reallocation_history_report.php below.
    $url = $sathiUrl . "/SAP/get_authorized_dealer_reallocation.php";
    $resp = fetchFromApi($url, ['dns_emp_codes' => implode(',', $dnsEmpCodes)]);
    if ($resp === false) {
        return [];
    }
    $data = json_decode($resp, true);
    if (empty($data['success'])) {
        error_log("[allocation_history] get_authorized_dealer_reallocation.php did not return success | url: $url | body: " . substr($resp, 0, 500));
        return [];
    }
    return $data['customer_codes'] ?? [];
}

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

// -----------------------------------------------------------------
// IMPORTANT: this was hardcoded to the dev subdomain, which is very
// likely why "Error fetching data from API" was showing — if this
// admin panel is deployed anywhere other than your dev/local box,
// dev.starsaathi.com may not be reachable from it at all (VPN/IP
// restriction/different host). Point this at the real environment
// constant, and only override to the dev URL when you're actually
// testing against dev.
// -----------------------------------------------------------------
$sathi_url =  "https://starsaathi.com";
// $sathi_url = "https://dev.starsaathi.com"; // uncomment ONLY for local/dev testing
$apiUrl = $sathi_url . "/SAP/reallocation_history_report.php";

// -----------------------------------------------------------------
// Access scoping: admin sees everything (unchanged). A regular
// employee is restricted to customer_codes reachable through their
// own reporting hierarchy.
// -----------------------------------------------------------------
$isAdmin = (strtoupper($_SESSION['admin_login'] ?? '') == 'ACCOUNTS'
            && strtoupper($_SESSION['nick_name'] ?? '') == 'STAR');

$restrictByCustomer = false;
$authorizedCustomerCodes = [];

$hasSaleAccess = $isAdmin; // admins bypass the sale_access gate
$loggedInEmpCode = null;

if (!$isAdmin) {
    $restrictByCustomer = true;

    // ASSUMPTION: adjust this session key to whatever your login flow
    // actually stores the logged-in employee's own emp_code under.
    $loggedInEmpCode = $_SESSION['emp_code'] ?? null;

    if (!empty($loggedInEmpCode)) {
        // Gate: only employees whose own sale_access is 'primary' may
        // view this report at all — checked BEFORE building the
        // hierarchy or calling the remote API, so a non-primary
        // employee never triggers that work.
        $accessSql = "SELECT sale_access FROM employee_master WHERE emp_code = ?";
        $accessStmt = mysqli_prepare($conn, $accessSql);
        if ($accessStmt) {
            mysqli_stmt_bind_param($accessStmt, 's', $loggedInEmpCode);
            mysqli_stmt_execute($accessStmt);
            $accessResult = mysqli_stmt_get_result($accessStmt);
            $accessRow = mysqli_fetch_assoc($accessResult);
            mysqli_stmt_close($accessStmt);

            if ($accessRow && strtoupper(trim($accessRow['sale_access'] ?? '')) === 'PRIMARY') {
                $hasSaleAccess = true;
            }
        }
    }
}

if (!$hasSaleAccess) {
    echo "<h3>You do not have access to view this report.</h3>";
    echo "</center>";
    return;
}

if ($restrictByCustomer) {
    $subordinateEmpCodes = getAllSubordinateEmpCodes($conn, $loggedInEmpCode);
    $dnsEmpCodes = getDnsEmpCodes($conn, $subordinateEmpCodes);

    $authorizedCustomerCodes = fetchAuthorizedCustomerCodes($sathi_url, $dnsEmpCodes);
    //   print_r( $subordinateEmpCodes);die;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = ($page - 1) * $limit;

$srch_apporderno  = isset($_GET['apporderno'])  ? $_GET['apporderno']  : null;
$srch_inv_no      = isset($_GET['inv_no'])      ? $_GET['inv_no']      : null;
$srch_appr_status = isset($_GET['appr_status']) ? $_GET['appr_status'] : null;

$queryParams = [
    'apporderno'  => $srch_apporderno,
    'inv_no'      => $srch_inv_no,
    'appr_status' => $srch_appr_status,
    'page'        => $page,
    'limit'       => $limit,
];

if ($restrictByCustomer) {
    // NOTE: reallocation_history_report.php on the starsaathi side needs
    // a matching update to accept & filter on this 'customer_ids' param
    // (comma-separated), matched against h.old_sub_dealer_id /
    // h.new_sub_dealer_id — the same customer_master.customer_id values
    // used to resolve the "Old/New Sub Dealer" display names. See the
    // get_authorized_customers.php header comment for the customer_code
    // vs customer_id assumption this relies on.
    $queryParams['customer_ids'] = implode(',', $authorizedCustomerCodes);
}

$queryParams = array_filter($queryParams, function ($v) {
    return !is_null($v) && $v !== '';
});

// Safety net: a restricted employee with zero authorized customer_codes
// must see nothing. Never call the remote API with an effectively-empty
// filter, in case it (today, or after a future change) treats "no
// customer_codes" as "no restriction".
if ($restrictByCustomer && empty($authorizedCustomerCodes)) {
    $records = [];
    $totalRecords = 0;
    $totalPages = 0;
} else {
    // POST, not GET: customer_ids can run to hundreds of codes, which
    // blows past the web server's URL-length limit (the "Request-URI
    // Too Long" error) if sent as a query string.
    // print_r($queryParams);
    $response = fetchFromApi($apiUrl, $queryParams);
    // print_r($response);
    if ($response === false) {
        // Real reason (DNS/TLS/timeout/HTTP status/body snippet) is now
        // in the PHP error log — check that first when this shows up.
        echo "Error fetching data from API.";
        exit;
    }

    $data = json_decode($response, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("[allocation_history] API returned non-JSON response | url: $apiUrl | body: " . substr($response, 0, 500));
        echo "Error fetching data from API.";
        exit;
    }
    $records = $data['data'] ?? [];
    $totalRecords = $data['total_records'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);
}
// echo "<pre>";
// print_r($records[0] ?? "NO RECORDS");
// echo "</pre>";
// --------------------------------------------------------------
// Resolve "Approved By" employee names locally in ONE batch query,
// since employee_master lives only on this server.
// "Approved By" holds a dns_emp_code (e.g. MISZD), unlike
// "Created By" which is already a customer_id resolved by the API.
// --------------------------------------------------------------
$emp_name_map = [];

if (!empty($records)) {
    $approved_by_codes = array_values(array_unique(array_filter(array_column($records, 'Approved By'))));

    if (!empty($approved_by_codes)) {
        $placeholders = implode(',', array_fill(0, count($approved_by_codes), '?'));
        $emp_sql = "SELECT dns_emp_code, emp_name FROM employee_master WHERE dns_emp_code IN ($placeholders)";
        $emp_stmt = mysqli_prepare($conn, $emp_sql);

        if ($emp_stmt) {
            $types = str_repeat('s', count($approved_by_codes));
            mysqli_stmt_bind_param($emp_stmt, $types, ...$approved_by_codes);
            mysqli_stmt_execute($emp_stmt);
            $emp_result = mysqli_stmt_get_result($emp_stmt);

            while ($emp_row = mysqli_fetch_assoc($emp_result)) {
                $emp_name_map[$emp_row['dns_emp_code']] = $emp_row['emp_name'];
            }
            mysqli_stmt_close($emp_stmt);
        } else {
            // temporary debug - remove once confirmed working
            echo "<pre>EMP QUERY PREPARE FAILED: " . htmlspecialchars(mysqli_error($conn)) . "</pre>";
        }
    }
}
?>
<h2>Sub Dealer Allocation Change History (<?php echo $totalRecords; ?>)</h2>

<form method="GET">

   <label for="apporderno">Search APPORDERNO:</label>
   <input type="text" name="apporderno" id="apporderno" value="<?= isset($_GET['apporderno']) ? htmlspecialchars($_GET['apporderno']) : '' ?>">

   <label for="inv_no">Search Invoice No:</label>
   <input type="text" name="inv_no" id="inv_no" value="<?= isset($_GET['inv_no']) ? htmlspecialchars($_GET['inv_no']) : '' ?>">

   <label for="appr_status">Approval Status:</label>
   <select name="appr_status" id="appr_status">
       <option value="">All Approval Status</option>
       <option value="Pending"  <?= (isset($_GET['appr_status']) && $_GET['appr_status'] == 'Pending')  ? 'selected' : '' ?>>Pending</option>
       <option value="Approved" <?= (isset($_GET['appr_status']) && $_GET['appr_status'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
       <option value="Rejected" <?= (isset($_GET['appr_status']) && $_GET['appr_status'] == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
   </select>

    <button type="submit">Search</button>
</form>

<br><br><br><br>

    <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="right">
        <?php if (!empty($records)):
            $exportParams = [
                'apporderno'  => $srch_apporderno,
                'inv_no'      => $srch_inv_no,
                'appr_status' => $srch_appr_status,
            ];
            if ($restrictByCustomer) {
                // Keep export scoped the same way the on-screen list is scoped.
                // Requires the same customer_ids filter added to
                // export_allocation_details_invoicewise_history.php on the
                // starsaathi side, or a restricted employee could bypass the
                // restriction by hitting Export directly.
                $exportParams['customer_ids'] = implode(',', $authorizedCustomerCodes);
            }
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($sathi_url); ?>/SAP/export_allocation_details_invoicewise_history.php" target="_blank" style="display:inline;">
            <?php foreach ($exportParams as $exp_key => $exp_val): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($exp_key); ?>" value="<?php echo htmlspecialchars($exp_val); ?>">
            <?php endforeach; ?>
            <!-- was a GET link before — same "Request-URI Too Long" risk as the
                 main report once customer_ids gets long. export_allocation_details_invoicewise_history.php
                 on the starsaathi side needs to read via $_REQUEST (not just $_GET) for this to work. -->
            <button type="submit" class="btn btn-default">Export</button>
        </form>
        <?php endif ?>
        <a href="allocation_details_invoicewise_history_view.php" class="btn btn-default" value="Reset" style="align='center'">
            <button>Reset</button>
        </a>
    </div>

<div id="display" style="max-height: 350px; max-width: 1100px; overflow-y: scroll; overflow-x: scroll; margin: 0 auto;">

        <table border="1" style="border-collapse:collapse;" class="border" width="100%">
            <thead>
                <tr class="TDHEAD">
                    <th>Sr. No</th>
                    <th>Request Date</th>
                     <th>Approved Date</th>
                    
                    <th>APPORDERNO</th>
                     <th>Invoice No</th>
                     <th>Invoice Date</th>
                      <th>Old Sub Dealer</th>
                    <th>New Sub Dealer</th>
 <th>Created By</th>

                     <th>Approved By</th>
                    
                     <th>Product Name</th>
                     <th>Branch</th>
                      <th>Month</th>
                    <th>Old Allocated Qty</th>
                    <th>New Allocated Qty</th>
                    
                    <!-- <th>Allocation Date</th> -->
                    <th>Approval Status</th>
                     
                   
                   
                    <th>Invoice Cancel</th>

                   
                   
                    
                </tr>
            </thead>
           <tbody>
    <?php if (empty($records)): ?>
        <tr>
            <td colspan="17" style="text-align: center;">No records found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($records as $row): ?>
            <?php
                $approved_by_code = $row['Approved By'];
                $approved_by_name = isset($emp_name_map[$approved_by_code]) ? $emp_name_map[$approved_by_code] : '';
                $approved_by_display = $approved_by_code . (!empty($approved_by_name) ? " - " . $approved_by_name : "");
            ?>
            <tr>
                <td><?= htmlspecialchars($row['Sr. No']) ?></td>
                <td><?php
    $date = new DateTime($row['Logged At'], new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Asia/Kolkata'));

    echo htmlspecialchars($date->format('d-m-Y H:i:s'));
?></td>
                <td><?= htmlspecialchars($row['Approved Date']) ?></td>
                <td><?= htmlspecialchars($row['APPORDERNO']) ?></td>
                 <td><?= htmlspecialchars($row['Invoice No']) ?></td>
                  <td><?= htmlspecialchars($row['Invoice Date']) ?></td>
                   <td><?= htmlspecialchars($row['Old Sub Dealer']) ?></td>
                <td><?= htmlspecialchars($row['New Sub Dealer']) ?></td>
                <td><?= htmlspecialchars($row['Created By']) ?></td>
                <td><?= htmlspecialchars($approved_by_display) ?></td>
                 <td><?= htmlspecialchars($row['Product Name']) ?></td>
               <td><?= htmlspecialchars($row['Branch']) ?></td>
                <td><?= htmlspecialchars($row['Month']) ?></td>
                <td><?= htmlspecialchars($row['Old Qty']) ?></td>
                <td><?= htmlspecialchars($row['New Qty']) ?></td>
                <!-- <td><?= htmlspecialchars($row['Allocation Date']) ?></td> -->
                <td><?= htmlspecialchars($row['Approval Status']) ?></td>
               
               
               
                <td><?= htmlspecialchars($row['Invoice Cancel']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
        </table>

    <div class="pagination">
        <?php
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $totalPages = $totalPages;

        if ($page > 1) {
            echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $page - 1])) . '">&laquo; Prev</a>';
        }

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