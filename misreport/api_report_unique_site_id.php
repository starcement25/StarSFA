<?php
ob_start();
session_start();
require_once("../sfa_connection.php");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR') {
    require("adminUtils_accounts.php");
} else {
    require("adminUtils.php");
}
// require("include/config.php");
// require("include/config-setup.php");
// require("attribute_selection.php");
// require("include/dbcon.php");
//  header("product:index.php");
disphtml("main();");

function main()
{
    require("include/dbcon.php");


    date_default_timezone_set("Asia/Kolkata");
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"> -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-highlight/3.5.0/jquery.highlight.min.js"></script>
    <script src="jquery.freezeheader.js"></script>


    <script src="https://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <script src="https://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>

    <script src="calendar3.js"></script> <!-- Make sure it exists locally or replace with correct path -->


    <script src="ajax1.js"></script>


    <script>
        webshims.setOptions('waitReady', false);
        webshims.setOptions('forms-ext', {
            types: 'date'
        });
        webshims.polyfill('forms forms-ext');
    </script>
    <script>
        $(document).ready(function() {
            $("#route_name").autocomplete({
                source: "fetch_routes.php",
                minLength: 2,
                select: function(event, ui) {
                    $("#route_name").val(ui.item.label);
                    $("#route_code").val(ui.item.value);
                    return false;
                }
            });
        
$("#visited_by_name").autocomplete({
                source: "fetch_visited_by.php",
                minLength: 2,
                select: function(event, ui) {
                    $("#visited_by_name").val(ui.item.label);
                    $("#visited_by").val(ui.item.value);
                    return false;
                }
            });


       $('#exportBtn').click(function () {
    const search = $('#search').val();
    const routeCode = $('#route_code').val();
    const visitedby = $('#visited_by').val();
    const fromDate = $('#from_date').val();
    const toDate = $('#to_date').val();

    let exportUrl = 'api_report_unique_site_id_export.php?';
    let params = [];

    if (search) params.push('search=' + encodeURIComponent(search));
    if (routeCode) params.push('route_code=' + encodeURIComponent(routeCode));
    if (visitedby) params.push('visited_by=' + encodeURIComponent(visitedby)); 
    if (fromDate) params.push('from_date=' + encodeURIComponent(fromDate));
    if (toDate) params.push('to_date=' + encodeURIComponent(toDate));

    exportUrl += params.join('&');
    window.location.href = exportUrl;
});
        });
    </script>

<style>
thead > tr > th {
    border: #524d4f 1px solid;
}</style>


    <?php


// if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
//     echo "HTTPS";
//     exit;
// } else {
//     echo "HTTP";
//     exit;
// }


    $localDB = new sfa_connection();
    $conn = $localDB->conn;


    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = ($page - 1) * $limit;

    // Filters
    $search         = $_GET['search'] ?? null;
    $route_code     = $_GET['route_code'] ?? null;
    $cust_phone     = $_GET['cust_phone'] ?? null;
    $visited_by     = $_GET['visited_by'] ?? null;
    $from_date      = $_GET['from_date'] ?? null;
    $to_date        = $_GET['to_date'] ?? null;


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

    $countSql = "
    SELECT COUNT(DISTINCT svm.id) as total 
    FROM site_visit_master svm
    LEFT JOIN site_master sm ON sm.id = svm.site_id
    $filterSql
";
     
    $countResult = mysqli_query($conn, $countSql);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);


    $dataSql = "
    SELECT 
        sm.*,
        svm.*
    FROM site_master sm
    LEFT JOIN site_visit_master svm ON sm.id = svm.site_id
    $filterSql
    ORDER BY svm.created_at DESC
    LIMIT $offset, $limit
";
// echo $dataSql;
//     die;

    $dataResult = mysqli_query($conn, $dataSql);

    $records = [];
    if ($dataResult) {
        while ($row = mysqli_fetch_assoc($dataResult)) {
            $records[] = $row;
        }
    } else {
        echo "<p style='color:red;'>SQL Error: " . mysqli_error($conn) . "</p>";
    }
    // echo"<pre>";print_r($records);die;


    ?>

    <h2>KHOJ - Site Visit Report </h2>

    <form method="GET">


        <label for="search">Search :</label>
        <input type="text" name="search" placeholder="Site ID / Site Name / Customer Phone Number" id="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">

        <?php
        $route_codess = isset($_GET['route_code']) ? htmlspecialchars($_GET['route_code']) : '';

        $routeSqls = "SELECT * FROM route_master WHERE route_code = '$route_codess'";
        $routeResults = mysqli_query($conn, $routeSqls);
        $routesRow = mysqli_fetch_assoc($routeResults);
        ?>
        <label for="route_name">Search Route:</label>
        <input type="text" id="route_name" placeholder="Type route name..." value="<?= isset($routesRow['route_name']) ? htmlspecialchars($routesRow['route_name']) : '' ?>">
        <input type="hidden" name="route_code" id="route_code" value="<?= isset($_GET['route_code']) ? htmlspecialchars($_GET['route_code']) : '' ?>">

          <?php
        $emp_codess = isset($_GET['visited_by']) ? htmlspecialchars($_GET['visited_by']) : '';

        $empSqls = "SELECT * FROM employee_master WHERE emp_code = '$emp_codess'";
        $empResults = mysqli_query($conn, $empSqls);
        $emppRow = mysqli_fetch_assoc($empResults);
        ?>

         <label for="route_name">Visited By:</label>
        <input type="text" id="visited_by_name" placeholder="Type Visited By..." value="<?= isset($emppRow['emp_code']) ? htmlspecialchars($emppRow['emp_name']) : '' ?>">
        <input type="hidden" name="visited_by" id="visited_by" value="<?= isset($_GET['visited_by']) ? htmlspecialchars($_GET['visited_by']) : '' ?>">




        <label for="from_date">From Date:</label>
        <input type="date" name="from_date" id="from_date" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">

        <label for="to_date">To Date:</label>
        <input type="date" name="to_date" id="to_date" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">


        <button type="submit">Search</button>
    </form>

    <br><br><br><br>

    <div id="display" style="max-height: 350px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="right">
        <?php if (!empty($records)): ?>
            <button type="button" id="exportBtn" class="btn btn-default">Export</button>
        <?php endif ?>
        <a href="api_report_unique_site_id.php" class="btn btn-default" style="align='center'">
            <button>Reset</button>
        </a>
    </div>

    <div id="display" style="max-height: 350px; max-width: 1100px; overflow-y: scroll; overflow-x: scroll; margin: 0 auto;">







        <table border="1" style="border-collapse:collapse;" class="border" width="100%">
            <thead>
                <tr class="TDHEAD">

                    <th>Site Code</th>
                    <th>Route Code</th>
                    <th>Customer Phone</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Site Name</th>
                    <th>Branch Code</th>
                    <th>State</th>
                    <th>District</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Meeting Person Type</th>
                    <th>Meeting Person Phone</th>
                    <th>Contractor Name</th>
                    <th>Contractor Phone</th>
                    <th>Engineer Name</th>
                    <th>Engineer Phone</th>
                    <th>Engineer Reg Star Stellar</th>
                    <th>Site Segment</th>
                    <th>Project Segment</th>
                    <th>Type Of Construction</th>
                    <th>Site Potential</th>
                    <th>Construction Stage</th>
                    <th>Cement Brand</th>
                    <th>Price Per Bag</th>
                    <th>Consumed Till Date</th>
                    <th>Estimated Requirement</th>
                    <th>Built Up Area</th>
                    <th>Decision Maker</th>
                    <th>Product Demo</th>
                    <th>Remarks</th>
                    <th>Visit Type</th>
                    <th>Visit Sub Type</th>
                    <th>Date Of Delivery</th>
                    <th>Bags Ordered</th>
                    <th>RSSD</th>
                    <th>Approved By</th>
                    <th>Visited By</th>
                    <th>Created At</th>
                    <!-- <th>Updated At</th> -->
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

                            <td><?= htmlspecialchars($row['site_code'] ?? '') ?></td>
                            <?php
                            $route_codes = mysqli_real_escape_string($conn, $row['route_code']);

                            $routeSql = "SELECT * FROM route_master WHERE route_code = '$route_codes'";
                            $routeResult = mysqli_query($conn, $routeSql);
                            $routeRow = mysqli_fetch_assoc($routeResult);
                            ?>
                            <td><?= htmlspecialchars($routeRow['route_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['cust_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['cust_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['site_name'] ?? '') ?></td>

                            <?php
                           $branch_codes = isset($row['branch_code']) ? mysqli_real_escape_string($conn, $row['branch_code']) : '';

                            $branchSql = "SELECT * FROM branch_master WHERE branch_code = '$branch_codes'";
                            $branchResult = mysqli_query($conn, $branchSql);
                            $branchRow = mysqli_fetch_assoc($branchResult);
                            ?>
                            <td><?= htmlspecialchars($branchRow['branch_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['state'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['district'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['latitude'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['longitude'] ?? '') ?></td>


                            <td><?= htmlspecialchars($row['meeting_person_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['meeting_person_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['contractor_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['contractor_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['engineer_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['engineer_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['engg_reg_star_stellar'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['site_segment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['project_segment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['type_of_construction'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['site_potential'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['construction_stage'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['cement_brand'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['price_per_bag'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['consumed_till_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['estimated_req'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['built_up_area'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['decision_maker'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['product_demo'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['remarks'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['visit_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['visit_sub_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['date_of_delivery'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['bags_ordered'] ?? '') ?></td>
                           <?php
                            $cust_codes = mysqli_real_escape_string($conn, $row['rssd']);

                            $custSql = "SELECT * FROM customer_master WHERE customer_code = '$cust_codes'";
                            $custResult = mysqli_query($conn, $custSql);
                            $custRow = mysqli_fetch_assoc($custResult);
                            ?>
                            <td><?= htmlspecialchars($custRow['customer_name'] ?? '') ?></td>
                            <?php
                            $approved_by = mysqli_real_escape_string($conn, $row['approved_by']);

                            $empSql = "SELECT * FROM employee_master WHERE emp_code = '$approved_by'";
                            $empResult = mysqli_query($conn, $empSql);
                            $empRow = mysqli_fetch_assoc($empResult);
                            ?>

                            <td><?= htmlspecialchars($empRow['emp_name'] ?? '') ?></td>
                            <?php
                            $visited_by = mysqli_real_escape_string($conn, $row['visited_by']);

                            $emp1Sql = "SELECT * FROM employee_master WHERE emp_code = '$visited_by'";
                            $emp1Result = mysqli_query($conn, $emp1Sql);
                            $emp1Row = mysqli_fetch_assoc($emp1Result);
                            ?>
                            <td><?= htmlspecialchars($emp1Row['emp_name'] ?? '') ?></td>
                            <td>
                                <?=
                                htmlspecialchars($row['created_at'])
                                ?>
                            </td>
                            <!-- <td><?= htmlspecialchars($row['updated_at'] ?? '') ?></td> -->
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

            td,
            th {
                text-align: center;
                vertical-align: middle;
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