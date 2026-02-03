<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$servername = "localhost";
$username = "acedns_dnsprod";
$password = "dnsprod1234#";
$dbname = "acedns_STAR";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful.<br>";
}


// Step 1: Get comma-separated list of lower_leaves
$sql = "
WITH RECURSIVE RecursiveCTE AS (
    SELECT emp_code, lower_leaves
    FROM employee_master
    WHERE emp_code = 'E0777'

    UNION ALL

    SELECT em.emp_code, em.lower_leaves
    FROM employee_master em
    INNER JOIN RecursiveCTE r
        ON em.emp_code = r.lower_leaves
)
SELECT GROUP_CONCAT(DISTINCT lower_leaves ORDER BY lower_leaves SEPARATOR ',') AS lower_leaves_list
FROM RecursiveCTE;
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the lower_leaves_list
    $row = $result->fetch_assoc();
    $lower_leaves_list = $row['lower_leaves_list'];
} else {
    die("Error fetching lower_leaves list: " . $conn->error);
}

// Step 2: Search in customer_route_emp_relation using the list
// Prepare the SQL query to search based on the comma-separated lower_leaves_list
$sql = "
SELECT crr.customer_code, crr.emp_code
FROM customer_route_emp_relation crr
WHERE crr.customer_code = 'C/0196445'
AND crr.emp_code IN (" . implode(',', '', explode(',', $lower_leaves_list)) .
")";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output results
    while ($row = $result->fetch_assoc()) {
        echo "Customer Code: " . $row["customer_code"] . " - Emp Code: " . $row["emp_code"] . "<br>";
    }
} else {
    echo "No records found.";
}

// Close connection
$conn->close();
?>
