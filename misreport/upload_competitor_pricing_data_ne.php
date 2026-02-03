<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    ob_start();
    session_start();
    
    require("adminUtils.php");

    require("include/config.php");
    require("include/config-setup.php");
    require("include/dbcon.php");

    echo "<h2>Service is temporarily unavailable.</h2>";
    exit;

    // $companyPricingsQuery =  'SELECT id FROM roe_pricing_competitor ';
    // $companyPricings = mysqli_query($link,$companyPricingsQuery);
    // while($companyPricing = mysqli_fetch_assoc($companyPricings))
    // {
    //     echo "<br>".$companyPricing["id"];
    // }

    // Handle form submission
    $fixedCol = 4;
    $companyColDiff = 3;
    $competitorName = "";
    $COMPANY_STAR = "STAR";
    $COMPANY_DALMIA = "DALMIA";
    $COMPANY_ULTRATECH = "ULTRATECH";
    $PRODUCT_PPC = "PPC";
    $STAR_PPC = "STAR CEMENT PPC";
    $DALMIA_PPC = "DALMIA";
    $ULTRATECH_PPC = "ULTRATECH";
    $headerRowNumber = 1;
    $routes = "";
    $incoterm = "";
    $INCOTERM_TYPE_EX = "EX";
    $INCOTERM_TYPE_EX_VALUE = 1;
    $INCOTERM_TYPE_FOR = "FOR";
    $INCOTERM_TYPE_FOR_VALUE = 0;
    $STATUS_ACTIVE = 1;
    $is_compared_with = 0;

    $processdedData = array();
    $processdedDataCount = 0;
    $unprocessdedData = array();
    $unprocessdedDataCount = 0;

    function addProcessdedData($msg)
    {
        global $processdedData, $processdedDataCount;

        array_push($processdedData, $msg);
        $processdedDataCount++;
    }
    function addUnprocessdedData($msg)
    {
        global $unprocessdedData, $unprocessdedDataCount;

        array_push($unprocessdedData, $msg);
        $unprocessdedDataCount++;
    }
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['csv_file']['tmp_name'];
            
            if (($handle = fopen($tmpName, "r")) !== false) 
            {
                $rowCount = 0;
                $loopIndex = 0;
                while (($data = fgetcsv($handle, 0, ",")) !== false) 
                {
                    $rowCount++;
                    if($rowCount == $headerRowNumber)
                    {
                        continue;
                    }

                    if (count(array_filter($data)) === 0) {
                        continue;
                    }
                    $isBranchExistsQuery = "SELECT * FROM branch_master WHERE branch_name LIKE '".$data[1]."' LIMIT 1";
                    $isBranchExists = mysqli_query($link,$isBranchExistsQuery);
                    if(mysqli_num_rows($isBranchExists) == 0)
                    {
                        addUnprocessdedData("In row ".$rowCount." , Invalid branch.<br>");
                        continue;
                    }

                    
                    $routes = explode("&", $data[2]);
                    $routes = array_map('trim', $routes);
                    $loopIndex++;
                    foreach($routes as $routeNameVal)
                    {
                        $isRouteExistsQuery = "SELECT RM.route_code FROM route_master AS RM INNER JOIN branch_master AS BM ON RM.branch_code = BM.branch_code WHERE BM.branch_name LIKE '".$data[1]."' AND RM.route_name LIKE '".$routeNameVal."' LIMIT 1";
                        $isRouteExists = mysqli_query($link,$isRouteExistsQuery);
                        if(mysqli_num_rows($isRouteExists) == 0)
                        {
                            addUnprocessdedData("In row ".$rowCount." , Invalid route ".$routeNameVal.".<br>");
                            continue;
                        }
                        
                        for($compLoopIndex = $fixedCol; $compLoopIndex < count($data); $compLoopIndex += $companyColDiff) 
                        { 
                            $is_compared_with = 0;
                            if($data[$compLoopIndex + 2] == $INCOTERM_TYPE_EX)
                            {
                                $incoterm = $INCOTERM_TYPE_EX_VALUE;
                            }
                            elseif($data[$compLoopIndex + 2] == $INCOTERM_TYPE_FOR)
                            {
                                $incoterm = $INCOTERM_TYPE_FOR_VALUE;
                            }
                            else
                            {
                                // echo $data[$compLoopIndex + 2]." ";
                                addUnprocessdedData("In row ".$rowCount." Invalid Incoterm for company ".$data[$compLoopIndex].".<br>");
                                continue;
                            }

                            if($data[$compLoopIndex + 1] == $PRODUCT_PPC)
                            {
                                if($data[$compLoopIndex] == $COMPANY_STAR)
                                {
                                    $competitorName = $STAR_PPC;
                                }
                                elseif($data[$compLoopIndex] == $COMPANY_DALMIA)
                                {
                                    $competitorName = $DALMIA_PPC;
                                }
                                elseif($data[$compLoopIndex] == $COMPANY_ULTRATECH)
                                {
                                    $competitorName = $ULTRATECH_PPC;
                                }
                                else
                                {
                                    addUnprocessdedData("In row ".$rowCount." Invalid Company.<br>");
                                    continue;
                                }
                            }
                            else
                            {
                                addUnprocessdedData("In row ".$rowCount." Invalid Product for company ".$data[$compLoopIndex].".<br>");
                                continue;
                            }
                            if($compLoopIndex == $fixedCol)
                            {
                                $is_compared_with = 1;
                            }
                            $competitorGroupNameQuery = "SELECT RM.route_code, CGM.sl_no AS competitor_id FROM route_master AS RM INNER JOIN branch_master AS BM ON RM.branch_code = BM.branch_code INNER JOIN competitor_group_master AS CGM ON BM.branch_code = CGM.branch_code WHERE BM.branch_name LIKE '".$data[1]."' AND CGM.acedns = 'yes' AND CGM.competitor_name LIKE '".$competitorName."' AND RM.route_name LIKE '".$routeNameVal."' LIMIT 1"; 
                            $competitorGroupName = mysqli_query($link,$competitorGroupNameQuery);
                            if(mysqli_num_rows($competitorGroupName) == 0)
                            {
                                echo "<br><br>".$competitorGroupNameQuery;
                            }
                            while($competitorGroupNameVal = mysqli_fetch_assoc($competitorGroupName))
                            {
                                // $neDataQuery = "INSERT INTO ne_pricing_competitor (
                                //     pricing_group_number,
                                //     route_code,
                                //     zone_name,
                                //     display_branch_name,
                                //     company_display_name,
                                //     competitor_id,
                                //     product_display_name,
                                //     incoterm_type,
                                //     `status`,
                                //     created_at,
                                //     is_compared_with,
                                // ) VALUES(
                                //     ".$loopIndex.",
                                //     ".$competitorGroupNameVal['route_code'].",
                                //     ".$data[3].",
                                //     ".$data[0].",
                                //     ".$data[$compLoopIndex].",
                                //     ".$competitorGroupNameVal['competitor_id'].",
                                //     ".$data[$compLoopIndex + 1].",
                                //     ".$incoterm.",
                                //     ".$STATUS_ACTIVE.",
                                //     ".date("Y-m-d H:i:s").",
                                //     ".$is_compared_with.",
                                // )";
                                $neDataQuery = $link->prepare("
                                    INSERT INTO ne_pricing_competitor (
                                        pricing_group_number,
                                        route_code,
                                        zone_name,
                                        display_branch_name,
                                        company_display_name,
                                        competitor_id,
                                        product_display_name,
                                        incoterm_type,
                                        `status`,
                                        created_at,
                                        is_compared_with
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                ");
                                $created_at = date("Y-m-d H:i:s");
                                $neDataQuery->bind_param(
                                    "issssisiisi",
                                    $loopIndex,
                                    $competitorGroupNameVal['route_code'],
                                    $data[3],
                                    $data[0],
                                    $data[$compLoopIndex],
                                    $competitorGroupNameVal['competitor_id'],
                                    $data[$compLoopIndex + 1],
                                    $incoterm,
                                    $STATUS_ACTIVE,
                                    $created_at,
                                    $is_compared_with
                                );
                                if ($neDataQuery->execute()) {
                                    // echo "Insert successful.";
                                } else {
                                    addUnprocessdedData("In row ".$rowCount." Insert failed: ".$stmt->error.".<br>");
                                    continue;
                                }
                            }
                            // $processdedDataCount++;
                        }
                    }
                    
                }
                
                echo "<br>";
                echo "Data Uploaded successfully.";
                if(count($unprocessdedData) > 0)
                {
                    echo "Errors :- <br>".implode($unprocessdedData);
                }
                fclose($handle);
            } else {
                echo "Error opening uploaded file.";
            }
        } else {
            echo "File upload failed!";
        }
    }
?>

    <!-- HTML Upload Form -->
    <h2>Upload CSV File</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <br><br>
        <button type="submit">Upload & Preview</button>
    </form>

<?php
    mysqli_close($link);
?>