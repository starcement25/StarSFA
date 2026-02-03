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
$sql = "SELECT sfa_blank_rout_data.Customer_Name,sfa_blank_rout_data.DNS_Customercode,sfa_blank_rout_data.branch_code,sfa_blank_rout_data.Phone_no,sfa_blank_rout_data.route_code,sfa_blank_rout_data.route_name FROM sfa_blank_rout_data LEFT JOIN route_master rm ON sfa_blank_rout_data.route_code = rm.dns_route_code WHERE rm.route_code IS NULL;";
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
        $route_code = trim($row['route_code']);

        $Customer_Name = trim($row['Customer_Name']);
        $branch_code = trim($row['branch_code']);
        $DNS_Customercode = trim($row['DNS_Customercode']);
        
        $sql = "SELECT COUNT(*) FROM route_master WHERE route_code = '$route_code'";
        $result_r = $conn->query($sql);
        $row1 = $result_r->fetch_row();
        $count1 = $row1[0];
        if ($count1 > 0) {
            continue;
        }

        //$updateStmt->execute();
        $sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

        $rsmaxroutecode=$conn->query($sqlmaxroutecode);

        $rowmaxroutecode=$rsmaxroutecode->fetch_assoc();

        $new_route_code=$rowmaxroutecode['new_route_code'];
        $max_route_code='RT/'.($new_route_code+1);
            
        $sql2="INSERT `route_master` SET `route_code`='".$max_route_code."' , `branch_code`='".$branch_code ."', `dns_route_code`='".$route_code."' , route_name='".$route_name."',`emp_code`='".$DNS_Customercode."' ";
        //echo"<pre>";print_r($sql2);die;

        $res=$conn->query($sql2);
        if($res){
            echo $DNS_Customercode." C_Name".$Customer_Name."/RCode- ".$route_code."/ ".$route_name." <BR>";
             $count++;
        }
        
    }

    echo "$count Route names updated successfully.";
    //$updateStmt->close();
} else {
    echo "No matching records found.";
}

$conn->close();
?>
