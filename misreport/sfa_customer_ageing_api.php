<?php
header("Content-Type: text/plain");

date_default_timezone_set('Asia/Kolkata');

 error_reporting(E_ALL);
 ini_set('display_errors', 1);

set_time_limit(1000);
ini_set('memory_limit','512M');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

function get_data_from_cserver($url_ck)

{

    $useragent = $_SERVER['HTTP_USER_AGENT'];
   //
    $username = "STARSAATHI"; //port using 44300

    $password = "Srikrishna@93933";  //port using 44300
    //$username = username;  //port using 44301

    //$password = password;  //port using 44301
    $ch_sheader = curl_init();

    curl_setopt($ch_sheader, CURLOPT_URL, $url_ck);

    curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");

    curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);

    $body_for_mcode = curl_exec($ch_sheader);

    if (curl_errno($ch_sheader)) {

        echo 'Curl error: ' . curl_error($ch_sheader);

    }

    $info = curl_getinfo($ch_sheader);

    // print_r($info);

    curl_close($ch_sheader);

    return $body_for_mcode;

}
function isJsonCk($str)

{

    $json = json_decode($str);

    return $json && $str != $json;

}
/* =========================================================
   VALIDATE INPUT
========================================================= */

if(!isset($_GET['emp_code']) || $_GET['emp_code'] == ''){

    echo "emp_code required";
    exit;
}

$emp_code = mysqli_real_escape_string($conn, $_GET['emp_code']);

/* =========================================================
   EMPLOYEE HIERARCHY
========================================================= */

function return_employee_hierarchy($emp_code, $conn){

    $employees = array();

    $sql = "SELECT emp_code, reporting_to FROM employee_master";

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

    $hierarchy = array();
    $queue = array($emp_code);

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
   GET EMPLOYEE HIERARCHY
========================================================= */

$employee_hierarchy = return_employee_hierarchy($emp_code, $conn);

/* =========================================================
   GET CUSTOMERS
========================================================= */

$sql_customer = "

SELECT DISTINCT
    c.customer_code,
    c.customer_name,
    c.SAP_customer_code as customer_id,
    c.zone

FROM customer_master c

INNER JOIN customer_route_emp_relation cre
    ON cre.customer_code = c.customer_code

WHERE cre.emp_code IN ($employee_hierarchy)
AND cre.acedns='Y'
AND c.SAP_customer_code!=''

ORDER BY c.customer_name ASC

";
//echo"<pre>";print_r($sql_customer);die;

$res_customer = mysqli_query($conn, $sql_customer);

if(!$res_customer){

    echo "SQL ERROR : " . mysqli_error($conn);
    exit;
}

/* =========================================================
   OUTPUT
========================================================= */

$count = 0;
$data  = '';

while($row_customer = mysqli_fetch_assoc($res_customer)){

    $customer_code = trim($row_customer['customer_code']);
    $customer_name = trim($row_customer['customer_name']);
    $customer_id   = trim($row_customer['customer_id']);
    $zone          = strtoupper(trim($row_customer['zone']));

    /* =====================================================
       REGION LOGIC
    ===================================================== */

    if($zone == 'NE1' || $zone == 'NE2'){

        $region = 'NE';

        $base_url = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/YCUSTAGENE_SRV/YCUSTAGENESet';

        $final = array(
            "Day3"  => 0,
            "Day10" => 0,
            "Day17" => 0,
            "Day25" => 0,
            "Day30" => 0,
            "Day45" => 0,
            "Day60" => 0,
            "Day90" => 0,
            "Abv90" => 0
        );

    }else{

        $region = 'ROE';

        $base_url = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/YCUSTAGEROE_SRV/YCUSTAGEROESet';

        $final = array(
            "Day3"  => 0,
            "Day7"  => 0,
            "Day12" => 0,
            "Day25" => 0,
            "Day30" => 0,
            "Day45" => 0,
            "Day60" => 0,
            "Day90" => 0,
            "Abv90" => 0
        );
    }

    /* =====================================================
       ONLY 1010 + 1017
    ===================================================== */

    $vkorg_array = array('1010');
    //$vkorg_array = array('1010','1017');

    foreach($vkorg_array as $vkorg){

        $url = $base_url .
        '?\$filter=Cocd%20eq%20%27'.$vkorg.
        '%27%20and%20Kunnr%20eq%20%27'.$customer_id.
        '%27&\$format=json';

        $response = get_data_from_cserver($url);

        if(!isJsonCk($response)){
            continue;
        }

        $json = json_decode($response, true);

        if(!isset($json['d']['results'][0])){
            continue;
        }

        $api_data = $json['d']['results'][0];

        foreach($final as $key => $value){

            if(isset($api_data[$key])){

                $final[$key] += (float)$api_data[$key];
            }
        }
    }

    /* =====================================================
       RESPONSE FORMAT
    ===================================================== */

    $count++;

    $line = '';

    $line .= $customer_code . "^";
    $line .= $customer_name . "^";
    $line .= $customer_id . "^";
    $line .= $region;

    foreach($final as $key => $value){

        $line .= "^".$key;
        $line .= "^".number_format($value, 3, '.', '');
    }

    $data .= $line . "\n";
}

/* =========================================================
   FINAL OUTPUT
========================================================= */

echo $count . "¥21\n";
echo $data;

mysqli_close($conn);
exit;

?>