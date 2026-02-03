<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli("localhost", "root", "Passw0rd123#$", "acedns_STAR");

if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

try {
    
    $sql = "SELECT mtl_testing_format_id 
            FROM mtl_testing_format 
            WHERE download_time BETWEEN '2025-07-01 00:00:00' AND '2025-08-31 23:59:59'";
    $result = $mysqli->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $mysqli->error);
    }

    while ($row = $result->fetch_assoc()) {
        $mtl_id = $row['mtl_testing_format_id'];

       
        if (empty($mtl_id)) {
            continue;
        }

      
        $survey_id = "SUE" . substr($mtl_id, 2);

       
        $query = "
            SELECT row_id, value 
            FROM survey_output 
            WHERE survey_id = '".$mysqli->real_escape_string($survey_id)."' 
              AND row_id IN ('RA597', 'RA599', 'RA600')
        ";
// echo $query;die;
        $res2 = $mysqli->query($query);

        if (!$res2) {
            throw new Exception("Survey query failed for $survey_id : " . $mysqli->error);
        }

        $mtl_no   = null;
        $branch   = null;
        $district = null;

        while ($r = $res2->fetch_assoc()) {
            switch ($r['row_id']) {
                case 'RA597':
                    $mtl_no = $r['value'];
                    break;
                case 'RA599':
                    $branch = $r['value'];
                    break;
                case 'RA600':
                    $district = $r['value'];
                    break;
            }
        }

        
        if ($mtl_no !== null || $branch !== null || $district !== null) {
            $update = "
                UPDATE mtl_testing_format 
                SET 
                    mtl_no = IFNULL(?, mtl_no),
                    branch = IFNULL(?, branch),
                    district = IFNULL(?, district)
                WHERE mtl_testing_format_id = ?
                  AND download_time BETWEEN '2025-07-01 00:00:00' AND '2025-08-31 23:59:59'
            ";
            $stmt = $mysqli->prepare($update);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }

            $stmt->bind_param("ssss", $mtl_no, $branch, $district, $mtl_id);

            if (!$stmt->execute()) {
                throw new Exception("Update failed for $mtl_id : " . $stmt->error);
            }

            $stmt->close();
        }
    }

    echo "Update completed successfully for July & August 2025.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$mysqli->close();
