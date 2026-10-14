
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Kolkata');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION CHECK
|--------------------------------------------------------------------------
*/

if (!$conn) {

    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "Database Connection Failed"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| GET REQUEST PARAMETERS
|--------------------------------------------------------------------------
*/

$dns_emp_code = isset($_GET['dns_emp_code'])
    ? mysqli_real_escape_string($conn,$_GET['dns_emp_code'])
    : '';

/*
|--------------------------------------------------------------------------
| CUSTOMER VIEW PARAMETER
|--------------------------------------------------------------------------
|
| customer_view=YES → show customer details
| customer_view=NO  → hide customer details
|
*/

$customer_view = isset($_GET['customer_view'])
    ? strtoupper(mysqli_real_escape_string($conn,$_GET['customer_view']))
    : 'YES';

if (empty($dns_emp_code)) {

    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "dns_emp_code is required"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| GET EMPLOYEE DETAILS USING DNS EMP CODE
|--------------------------------------------------------------------------
*/

$employeeSql = "
    SELECT 
        emp_code,
        emp_name,
        dns_emp_code,
        branch_code,
        level,
        reporting_to
    FROM employee_master
    WHERE dns_emp_code = '$dns_emp_code'
    LIMIT 1
";

$employeeResult = mysqli_query($conn,$employeeSql);

if (!$employeeResult || mysqli_num_rows($employeeResult) == 0) {

    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "Employee not found"
    ]);

    exit;
}

$employeeData = mysqli_fetch_assoc($employeeResult);

$main_emp_code = $employeeData['emp_code'];

/*
|--------------------------------------------------------------------------
| GET COMPLETE EMPLOYEE HIERARCHY
|--------------------------------------------------------------------------
*/

$employeeTree = getEmployeeFlatTree($conn,$main_emp_code);

/*
|--------------------------------------------------------------------------
| FINAL RESPONSE ARRAY
|--------------------------------------------------------------------------
*/

$finalData = [];

foreach ($employeeTree as $employee) {

    $empCode = $employee['emp_code'];

    /*
    |--------------------------------------------------------------------------
    | GET MAPPED CUSTOMERS
    |--------------------------------------------------------------------------
    */

    $customers = [];

    if($customer_view=="YES")
    {

        $customerSql = "
            SELECT 
                r.customer_code,
                c.SAP_customer_code as customer_id,
                c.customer_name,
                c.branch_code,
                b.branch_name
            FROM customer_route_emp_relation r

            LEFT JOIN customer_master c
                ON r.customer_code = c.customer_code

            LEFT JOIN branch_master b
                ON c.branch_code = b.branch_code

            WHERE r.emp_code = '$empCode' and r.acedns='Y'
        ";

        $customerResult = mysqli_query($conn,$customerSql);

        while ($customerRow = mysqli_fetch_assoc($customerResult)) {

            $customers[] = [

                "customer_code" => $customerRow['customer_code'],
                "customer_id"   => $customerRow['customer_id'],
                "customer_name" => $customerRow['customer_name'],
                "branch_code"   => $customerRow['branch_code'],
                "branch_name"   => $customerRow['branch_name']
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUSH EMPLOYEE + CUSTOMER DATA
    |--------------------------------------------------------------------------
    */

    $employeeRow = [

        "emp_code"      => $employee['emp_code'],
        "emp_name"      => $employee['emp_name'],
        "dns_emp_code"  => $employee['dns_emp_code'],
        "branch_code"   => $employee['branch_code'],
        "level"         => $employee['level'],
        "reporting_to"  => $employee['reporting_to']

    ];

    if($customer_view=="YES"){
        $employeeRow["mapped_customers"] = $customers;
    }

    $finalData[] = $employeeRow;
}

/*
|--------------------------------------------------------------------------
| FINAL JSON RESPONSE
|--------------------------------------------------------------------------
*/

$jsonResponse = [
    "process_status"  => "YES",
    "process_message" => "Employee Relation Details Found",

    "customer_view" => $customer_view,

    "main_employee" => [
        "emp_code"      => $employeeData['emp_code'],
        "emp_name"      => $employeeData['emp_name'],
        "dns_emp_code"  => $employeeData['dns_emp_code'],
        "branch_code"   => $employeeData['branch_code'],
        "level"         => $employeeData['level']
    ],

    "total_employees" => count($finalData),

    "data" => $finalData
];
?>

<!DOCTYPE html>
<html>
<head>

    <title>Employee Relation Details</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f6f9;
            margin:20px;
        }

        .container{
            width:100%;
            max-width:1400px;
            margin:auto;
        }

        .title{
            font-size:24px;
            font-weight:bold;
            margin-bottom:20px;
            color:#333;
        }

        .search-box{
            margin-bottom:20px;
        }

        .search-box input{
            width:300px;
            padding:10px;
            border:1px solid #ccc;
            border-radius:5px;
            font-size:14px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:#fff;
        }

        table thead{
            background:#007bff;
            color:#fff;
        }

        table th,
        table td{
            padding:12px;
            border:1px solid #ddd;
            text-align:left;
            vertical-align:top;
        }

        table tr:nth-child(even){
            background:#f9f9f9;
        }

        .customer-box{
            background:#eef4ff;
            padding:8px;
            margin-bottom:5px;
            border-radius:4px;
        }

        .no-data{
            color:red;
            font-weight:bold;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="title">
        Employee Relation Details
    </div>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search Employee / Customer">
    </div>

    <table id="employeeTable">

        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Emp Name</th>
                <th>DNS Emp Code</th>
                <th>Branch</th>
                <th>Level</th>
                <th>Reporting To</th>

                <?php if($customer_view=="YES"){ ?>
                    <th>Mapped Customers</th>
                <?php } ?>

            </tr>
        </thead>

        <tbody>

        <?php foreach($jsonResponse['data'] as $row){ ?>

            <tr>

                <td><?php echo $row['emp_code']; ?></td>

                <td><?php echo $row['emp_name']; ?></td>

                <td><?php echo $row['dns_emp_code']; ?></td>

                <td><?php echo $row['branch_code']; ?></td>

                <td><?php echo $row['level']; ?></td>

                <td><?php echo $row['reporting_to']; ?></td>

                <?php if($customer_view=="YES"){ ?>

                <td>

                    <?php
                    if(count($row['mapped_customers']) > 0){

                        foreach($row['mapped_customers'] as $customer){
                    ?>

                        <div class="customer-box">

                            <b><?php echo $customer['customer_name']; ?></b><br>

                            Customer ID :
                            <?php echo $customer['customer_id']; ?><br>

                            Branch :
                            <?php echo $customer['branch_name']; ?>

                        </div>

                    <?php
                        }

                    }else{
                        echo '<span class="no-data">No Customers</span>';
                    }
                    ?>

                </td>

                <?php } ?>

            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<script>

document.getElementById("searchInput").addEventListener("keyup", function() {

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll("#employeeTable tbody tr");

    rows.forEach(function(row){

        let text = row.innerText.toLowerCase();

        row.style.display = text.includes(value) ? "" : "none";

    });

});

</script>

</body>
</html>

<?php

/*
|--------------------------------------------------------------------------
| FUNCTION : GET EMPLOYEE TREE
|--------------------------------------------------------------------------
*/

function getEmployeeFlatTree($conn,$managerCode, &$visited = [])
{
    $employees = [];

    $managerCodeEscaped = mysqli_real_escape_string($conn,$managerCode);

    /*
    |--------------------------------------------------------------------------
    | PREVENT INFINITE LOOP
    |--------------------------------------------------------------------------
    */

    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }

    $visited[] = $managerCodeEscaped;

    /*
    |--------------------------------------------------------------------------
    | GET CURRENT EMPLOYEE DETAILS
    |--------------------------------------------------------------------------
    */

    $selfSql = "
        SELECT 
            emp_code,
            emp_name,
            dns_emp_code,
            branch_code,
            level,
            reporting_to
        FROM employee_master
        WHERE emp_code = '$managerCodeEscaped'
        LIMIT 1
    ";

    $selfResult = mysqli_query($conn,$selfSql);

    if ($selfRow = mysqli_fetch_assoc($selfResult)) {

        $employees[] = [

            'emp_code'      => $selfRow['emp_code'],
            'emp_name'      => $selfRow['emp_name'],
            'dns_emp_code'  => $selfRow['dns_emp_code'],
            'branch_code'   => $selfRow['branch_code'],
            'level'         => $selfRow['level'],
            'reporting_to'  => $selfRow['reporting_to']
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GET CHILD EMPLOYEES
    |--------------------------------------------------------------------------
    */

    $childSql = "
        SELECT emp_code
        FROM employee_master
        WHERE reporting_to LIKE '%$managerCodeEscaped%'
    ";

    $childResult = mysqli_query($conn,$childSql);

    while ($childRow = mysqli_fetch_assoc($childResult)) {

        $childEmpCode = $childRow['emp_code'];

        $employees = array_merge(
            $employees,
            getEmployeeFlatTree($conn,$childEmpCode, $visited)
        );
    }

    return $employees;
}

/*
|--------------------------------------------------------------------------
| CLOSE MYSQL CONNECTION
|--------------------------------------------------------------------------
*/

mysqli_close($conn);

exit;

?>

