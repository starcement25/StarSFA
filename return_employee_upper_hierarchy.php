<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

function return_employee_upper_hierarchy($emp_code) {
    $emphierarchy = array();
    $emphierarchystring = "";

    employee_upper_hierarchy_details($emp_code, $emphierarchy);

    $emphierarchy = array_unique($emphierarchy);

    foreach($emphierarchy as $hierarchyval) {
        $emphierarchystring .= $hierarchyval . ',';
    }

    $emphierarchystring = substr($emphierarchystring, 0, -1);

    return $emphierarchystring;
}

function employee_upper_hierarchy_details($emp_code, &$emphierarchy) {

    $link = mysqli_connect(SERVER, USER, PASSWORD, DB) or die("Database Connection Error.");

    $sqlemphierarchy = "
        SELECT reporting_to 
        FROM employee_master 
        WHERE emp_code = '".$emp_code."' 
        AND reporting_to <> ''
    ";

    $rsemphierarchy = mysqli_query($link, $sqlemphierarchy);
    $cntemphierarchy = mysqli_num_rows($rsemphierarchy);

    if($cntemphierarchy > 0) {

        while($rowemphierarchy = mysqli_fetch_assoc($rsemphierarchy)) {

            $reporting_to = $rowemphierarchy['reporting_to'];

            if(strpos($reporting_to, ',') !== false) {

                $reporting_to_Arr = explode(',', $reporting_to);

                for($cn = 0; $cn < count($reporting_to_Arr); $cn++) {

                    $manager = trim($reporting_to_Arr[$cn]);

                    if(!in_array("'".$manager."'", $emphierarchy)) {

                        $emphierarchy[] = "'".$manager."'";
                        employee_upper_hierarchy_details($manager, $emphierarchy);
                    }
                }

            } else {

                $manager = trim($reporting_to);

                if(!in_array("'".$manager."'", $emphierarchy)) {

                    $emphierarchy[] = "'".$manager."'";
                    employee_upper_hierarchy_details($manager, $emphierarchy);
                }
            }
        }

    } else {

        if(!in_array("'".$emp_code."'", $emphierarchy)) {
            $emphierarchy[] = "'".$emp_code."'";
        }
    }
}


/* ===========================
   GET emp_code USING dns_emp_code
   =========================== */

$dns_emp_code = $_GET['dns_emp_code'];

$link = mysqli_connect(SERVER, USER, PASSWORD, DB) or die("Database Connection Error.");

$sqlEmployee = "
    SELECT emp_code 
    FROM employee_master 
    WHERE dns_emp_code = '".$dns_emp_code."'
";

$rsEmployee = mysqli_query($link, $sqlEmployee);

if(mysqli_num_rows($rsEmployee) > 0) {

    $rowEmployee = mysqli_fetch_assoc($rsEmployee);

    $emp_code = $rowEmployee['emp_code'];

    // Get full upper hierarchy
    $hierarchyCodes = return_employee_upper_hierarchy($emp_code);

    // Fetch all employee details from hierarchy
    $sqlHierarchyDetails = "
        SELECT * 
        FROM employee_master 
        WHERE emp_code IN ($hierarchyCodes)
    ";

    $rsHierarchyDetails = mysqli_query($link, $sqlHierarchyDetails);

    echo "<pre>";

    while($row = mysqli_fetch_assoc($rsHierarchyDetails)) {
        print_r($row);
    }

    echo "</pre>";

} else {

    echo "No employee found.";
}
?>