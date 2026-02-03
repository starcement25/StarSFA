<?php
ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "Passw0rd123#$";
$database = "acedns_STAR";
//echo"<pre>";print_r('44');die;
// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customer_code and route_name from sfa_data_update
// $sql = "
//     SELECT sdu.customer_code, sdu.route_name, sdu.route_code
//     FROM sfa_data_update sdu
//     JOIN customer_master cm ON sdu.customer_code = cm.dns_customer_code
// ";
$sql = "SELECT rm.route_code,sdu.route_name,sdu.customer_code,sdu.AceDNS,sdu.route_code as sdu_route_code FROM sfa_data_update sdu LEFT JOIN route_master rm ON rm.route_name=sdu.route_name GROUP BY sdu.customer_code";
/*$sql="SELECT sdu.route_code,sdu.route_name,sdu.customer_code,sdu.AceDNS,sdu.route_code as sdu_route_code FROM sfa_data_update sdu LEFT JOIN route_master rm ON rm.dns_route_code=sdu.route_name WHERE sdu.customer_code IN (SELECT dns_customer_code FROM `customer_master` WHERE `route_code`='');";*/
//echo"<pre>";print_r($sql);die;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Prepare update statement
    //$updateStmt = $conn->prepare("UPDATE route_master SET route_name = ? WHERE route_code = ?");
    
    // Bind parameters
    //$updateStmt->bind_param("ss", $route_name, $route_code);

    // Loop through each row and update route_master
    $count=0;
    while ($row = $result->fetch_assoc()) {
        $route_name = trim($row['route_name']);
        $route_code_dns = trim($row['route_code']);
        $customer_code = trim($row['customer_code']);
        $AceDNS = trim($row['AceDNS']);
        $sdu_route_code = trim($row['sdu_route_code']);
        //$updateStmt->execute();
        if($route_code_dns!=''){
        $sql="UPDATE `customer_master` SET `route_code`='".$route_code_dns."',`acedns`='".$AceDNS."' WHERE `dns_customer_code`='".$customer_code."'";
        $res = $conn->query($sql);
        $sql2="UPDATE `route_master` SET `dns_route_code`='".$sdu_route_code."' WHERE `route_code`='$route_code_dns' and route_name='".$route_name."'";
        $conn->query($sql2);
        if($res){
            echo $customer_code."/Code- ".$route_code_dns."/ ".$route_name." <BR>";
             $count++;
        }
     }
    }

    echo "$count Route names updated successfully.";
    //$updateStmt->close();
} else {
    echo "No matching records found.";
}

$conn->close();
?>
