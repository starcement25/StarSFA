<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '2G');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;
/* =========================================================
   EMPLOYEE HIERARCHY
========================================================= */

function return_employee_hierarchy($emp_code, $conn){

    $employees = [];

    $sql = "SELECT emp_code, reporting_to 
            FROM employee_master";

    $res = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($res)){

        $reporting_to = trim($row['reporting_to']);

        if($reporting_to == ''){
            continue;
        }

        $reportingArr = explode(',', $reporting_to);

        foreach($reportingArr as $manager){

            $manager = trim($manager);

            if($manager != ''){
                $employees[$manager][] = $row['emp_code'];
            }
        }
    }

    $hierarchy = [];
    $queue = [$emp_code];

    while(!empty($queue)){

        $current = array_shift($queue);

        if(isset($employees[$current])){

            foreach($employees[$current] as $child){

                if(!in_array($child, $hierarchy)){

                    $hierarchy[] = $child;
                    $queue[] = $child;
                }
            }
        }
    }

    $hierarchy[] = $emp_code;

    $hierarchy = array_unique($hierarchy);

    return "'" . implode("','", $hierarchy) . "'";
}

/* =========================================================
   EMPLOYEE FILTER
========================================================= */

//$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);


$SAP_customer_code =$_GET['customer_id']; //'1000002054';

echo "<h2>📊 Email Hierarchy Details for Customer Code: $SAP_customer_code</h2>";

$sqldealeremp = "SELECT emp_code 
                 FROM customer_route_emp_relation 
                 WHERE acedns='Y' 
                 AND customer_code='".$SAP_customer_code."'";

$rsdealeremp = mysqli_query($conn, $sqldealeremp);

if(mysqli_num_rows($rsdealeremp) == 0){
    echo "<p>No employee found for this customer.</p>";
}

$email_hierarchy = '';
$email_broker = '';

while($rowdealeremp = mysqli_fetch_array($rsdealeremp)) {

    $emp_code_db = $rowdealeremp['emp_code'];
    echo "<hr><b>🔹 Dealer-handling Employee:</b> $emp_code_db<br>";

    // Get the hierarchy chain
$employee_upper_hierarchy = return_employee_hierarchy($emp_code_db, $conn);

// remove single quotes and spaces
$employee_upper_hierarchy = str_replace(["'", " "], "", $employee_upper_hierarchy);

echo "<b>Hierarchy Emp Codes (cleaned):</b> $employee_upper_hierarchy<br>";

// convert to array
$emp_codes = explode(",", $employee_upper_hierarchy);

    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse; margin-top:10px;'>
            <tr style='background-color:#f2f2f2;'>
                <th>Level</th>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Email</th>
            </tr>";

    $level = 1;
    foreach ($emp_codes as $emp_code) {

        $sqlemailhierarchy = "SELECT emp_code, emp_name, designation, email 
                              FROM employee_master 
                              WHERE emp_code='$emp_code' 
                                AND UPPER(sale_access)='PRIMARY' 
                                AND acedns='Y'";
        $rsemailhierarchy = mysqli_query($conn, $sqlemailhierarchy);

        if(mysqli_num_rows($rsemailhierarchy) > 0){
            while($rowemailhierarchy = mysqli_fetch_array($rsemailhierarchy)){
                $empEmail = trim($rowemailhierarchy['email']);
                if($empEmail != ''){
                    $email_hierarchy .= $empEmail.',';
                }

                echo "<tr>
                        <td align='center'>$level</td>
                        <td>{$rowemailhierarchy['emp_code']}</td>
                        <td>{$rowemailhierarchy['emp_name']}</td>
                        <td>{$rowemailhierarchy['designation']}</td>
                        <td>{$rowemailhierarchy['email']}</td>
                      </tr>";
            }
        } else {
            echo "<tr>
                    <td align='center'>$level</td>
                    <td>$emp_code</td>
                    <td colspan='3'><i>No record found in employee_master</i></td>
                  </tr>";
        }

        $level++;
    }

    echo "</table>";
}

// 🔸 Broker email section
$sqlbroker = "SELECT broker_code 
              FROM customer_broker_relation 
              WHERE acedns='Y' 
              AND customer_code='".$SAP_customer_code."'";
$rsbroker = mysqli_query($conn, $sqlbroker);

if(mysqli_num_rows($rsbroker) > 0){
    echo "<h3>🧑‍💼 Broker Details:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>
            <tr style='background-color:#f2f2f2;'>
                <th>Broker Code</th>
                <th>Broker Name</th>
                <th>Email</th>
            </tr>";

    while($rowbroker = mysqli_fetch_array($rsbroker)){
        $sqlemailbroker = "SELECT broker_id, broker_name, mail_id 
                           FROM broker_master 
                           WHERE broker_id='".$rowbroker['broker_code']."' 
                             AND acedns='Y'";
        $rsemailbroker = mysqli_query($conn, $sqlemailbroker);
        while($rowemailbroker = mysql_fetch_array($rsemailbroker)){
            if($rowemailbroker['mail_id'] != ''){
                $email_broker .= $rowemailbroker['mail_id'].',';
            }

            echo "<tr>
                    <td>{$rowemailbroker['broker_id']}</td>
                    <td>{$rowemailbroker['broker_name']}</td>
                    <td>{$rowemailbroker['mail_id']}</td>
                  </tr>";
        }
    }

    echo "</table>";
} else {
    echo "<p><i>No broker linked for this customer.</i></p>";
}

// 🔸 Combine all emails
$final_email = substr($email_hierarchy,0,-1);
if ($email_broker != '') $final_email .= ',' . substr($email_broker,0,-1);
$final_email .= ',samirdas@starcement.co.in,antarabanerjee@starcement.co.in,pratipbhunia@starcement.co.in';

echo "<hr><h3>📧 Final Email Recipients:</h3>";
echo "<div style='word-wrap:break-word; max-width:900px;'>$final_email</div>";

?>
