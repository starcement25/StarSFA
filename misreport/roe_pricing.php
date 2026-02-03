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

    function isValidYMDDate($dateStr) 
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateStr);
        return $date && $date->format('Y-m-d') === $dateStr;
    }

    function priceDiffValWithIcon($priceDiff)
    {
        if($priceDiff > 0)
        {
            return '<td> <span style="color:#68a490;">&#9650;&nbsp;&nbsp;</span>'.$priceDiff.'</td>';
        }
        elseif($priceDiff == 0)
        {
            return '<td> <span style="color:#a4802b; height:2px;width: 10px;">&#9644;&nbsp;&nbsp;</span> '.$priceDiff.'</td>';      
        }
        else
        {
            return '<td><span style="color:#d65532;">&#9660;&nbsp;&nbsp;</span> '.$priceDiff.'</td>';
        }
    }
    // echo "<h2>Service is temporary unavailable, please try again later.</h2>";
    // exit;
    // $reportType = array("roe", "ne");
    $REPORT_TYPE_ROE = "roe";
    $REPORT_TYPE_NE = "ne";
    $reportForTitle = "";
    $INCOTERM_TYPE_EX = 1;
    $INCOTERM_TYPE_FOR = 0;
    $STATUS_ACTIVE = 1;
    $STATUS_INACTIVE = 0;
    $IS_COMPARED_WITH_YES = 1;
    $IS_COMPARED_WITH_NO = 0;
    $inputDate = $_REQUEST['comparison_date'] ?? null;
    $start_date = $inputDate;
    $end_date = $inputDate;
    $competitor_string = array();
    $comparedFrom = array();
    $comparedFromCount = 0;
    $comparedTo = array();
    $comparedtoCount = 0;
    $competitorsQuery = "";
    $pricingMainWhereCondition = "";
    
    $fixedColSpanHeader = 3;
    $fixedPriceDiffColSpanHeader = 3;
    $comparisonFieldsColSpanHeader = 5;
    $variableColSpanHeader = 0;
    $competitorNumber = 2;

    if (!$inputDate || !isValidYMDDate($inputDate)) {
        echo "Invalid date or format";
        exit;
    }

    if(!($_REQUEST['report_for'] ?? null))
    {
        echo "Report For is required.";
        exit;
    }
    elseif(!in_array($_REQUEST['report_for'], [$REPORT_TYPE_NE, $REPORT_TYPE_ROE]))
    {
        echo "Invalid Input Comparison For.";
        exit;
    }

    if($_REQUEST['report_for'] == $REPORT_TYPE_NE)
    {  
        $reportForTitle = "NE Price";
        // $competitorsQuery = 'SELECT NE_P_C.is_compared_with,NE_P_C.route_code, NE_P_C.zone_name, RM.route_name
        //              FROM ne_pricing_competitor AS NE_P_C INNER JOIN route_master AS RM ON NE_P_C.route_code = RM.route_code
        //              WHERE NE_P_C.status = '.$STATUS_ACTIVE.' 
        //              GROUP BY NE_P_C.route_code, NE_P_C.pricing_group_number';
        $competitorsQuery = 'SELECT ANY_VALUE(NE_P_C.is_compared_with) AS is_compared_with,NE_P_C.company_display_name
                     FROM ne_pricing_competitor AS NE_P_C
                     WHERE NE_P_C.status = '.$STATUS_ACTIVE.' 
                     GROUP BY NE_P_C.company_display_name';
        $competitors = mysqli_query($link,$competitorsQuery);
    } 
    elseif($_REQUEST['report_for'] == $REPORT_TYPE_ROE)
    {  
        $reportForTitle = "ROE Price"; 
        // $competitorsQuery = 'SELECT ROE_P_C.is_compared_with,ROE_P_C.branch_code, ROE_P_C.zone_name, BM.branch_name
        //              FROM roe_pricing_competitor AS ROE_P_C INNER JOIN branch_master AS BM ON ROE_P_C.branch_code = BM.branch_code
        //              WHERE ROE_P_C.status = '.$STATUS_ACTIVE.' 
        //              GROUP BY ROE_P_C.branch_code, ROE_P_C.pricing_group_number';
        $competitorsQuery = 'SELECT ANY_VALUE(ROE_P_C.is_compared_with) AS is_compared_with,ROE_P_C.company_display_name
                     FROM roe_pricing_competitor AS ROE_P_C
                     WHERE ROE_P_C.status = '.$STATUS_ACTIVE.' 
                     GROUP BY ROE_P_C.company_display_name';
        $competitors = mysqli_query($link,$competitorsQuery);
    } 
    while($competitor = mysqli_fetch_assoc($competitors))
    {
        if($competitor["is_compared_with"] == $IS_COMPARED_WITH_YES)
        {
            array_push($comparedFrom, $competitor);
        }
        else
        {
            array_push($comparedTo, $competitor);
        }
    }
    $comparedFromCount = count($comparedFrom);
    $comparedToCount = count($comparedTo);
    $variableColSpanHeader = ($comparedFromCount * ($comparisonFieldsColSpanHeader + ($comparedToCount * ($comparisonFieldsColSpanHeader + $fixedPriceDiffColSpanHeader))));
    ?>
    <table border="1" style="border-collapse:collapse;" class="border" width="150%">
        <tr class="TDHEAD"><td colspan="<?php echo ($fixedColSpanHeader + $variableColSpanHeader);?>" align="center">Market Feedback Comparisons</td></tr>
        <tr class="TDHEAD_SUB">
            <td style="background:#fff;" rowspan = "3" colspan="<?php echo $fixedColSpanHeader;?>"> <span style="border-bottom:2px solid #000; font-size:18px;padding-bottom:10px;padding-left:15px;padding-right:15px;font-weight:bold;">
                <?php 
                    echo $reportForTitle;
                ?>
            </span></td>
            <td  style="background:#fce4d6;" colspan="<?php echo $variableColSpanHeader;?>" align="center">Date : <?php echo $start_date == $end_date ? DateTime::createFromFormat('Y-m-d', $start_date)->format('d/m/Y') : DateTime::createFromFormat('Y-m-d', $start_date)->format('d/m/Y')." - ".DateTime::createFromFormat('Y-m-d', $end_date)->format('d/m/Y') ?> </td>
        </tr>
        <tr class="TDHEAD_SUB">
            <?php
                for($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++)
                {
                    ?>
                        <td style="background:#fce4d6;" colspan="<?php echo $comparisonFieldsColSpanHeader;?>" align="center"><?php echo $comparedFrom[$comparedFromIndex]["company_display_name"] ?> PRICE</td>
                    <?php
                    for($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++)
                    {
                        ?>
                        <td style="background:#fce4d6;" colspan="<?php echo $comparisonFieldsColSpanHeader;?>" align="center"><?php echo $comparedTo[$comparedToIndex]["company_display_name"] ?> PRICE</td>
                        <td style="background:#ffff00; color:#ff0000;" colspan="<?php echo $fixedPriceDiffColSpanHeader;?>" align="center">DIFFERENCE</td>
                        <?php
                    }
            ?>
            <?php
                }
            ?>
        </tr>
    
        <tr class="TDHEAD_SUB">

            <?php
                for($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++)
                {
                    ?>
                        <td style="background:#d9e1f2;" colspan="<?php echo $comparisonFieldsColSpanHeader;?>" align="center">Max instances Price Reading (Rs./Bag)</td>
                    <?php
                    for($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++)
                    {
                        ?>
                        <td style="background:#d9e1f2;" colspan="<?php echo $comparisonFieldsColSpanHeader;?>" align="center">Max instances Price Reading (Rs./Bag)</td>
                        <td style="background:#008000; color:#fff;" colspan="<?php echo $fixedPriceDiffColSpanHeader;?>" align="center"><?php echo $comparedFrom[$comparedFromIndex]["company_display_name"]." VS ".$comparedTo[$comparedToIndex]["company_display_name"]." (Rs./Bag)"; ?></td>
                        <?php
                    }
            ?>
            <?php
                }
            ?>
        </tr>
        
        <tr class="TDHEAD_SUB">
            <td style="background:#ffff00">Branch</td>
            <td style="background:#ffff00">Price-Destination</td>
            <td style="background:#ffff00">Zone</td>
            <?php
                for($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++)
                {
                    ?>
                        <td style="background:#ffff00">Product</td>
                        <td style="background:#ffff00">Incoterms</td>
                        <td style="background:#d9e1f2">BILLING</td>
                        <td style="background:#d9e1f2">WSP</td>
                        <td  style="background:#d9e1f2">RSP</td>
                    <?php
                    for($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++)
                    {
                        ?>
                        <td style="background:#ffff00">Product</td>
                        <td style="background:#ffff00">Incoterms</td>
                        <td style="background:#d9e1f2">BILLING</td>
                        <td style="background:#d9e1f2">WSP</td>
                        <td  style="background:#d9e1f2">RSP</td>

                        <td  style="background:#d9e1f2">BILLING</td>
                        <td  style="background:#d9e1f2">WSP</td>
                        <td  style="background:#d9e1f2">RSP</td>
                        <?php
                    }
                }
            ?>
        </tr>
    <?php
    
    if($_REQUEST['report_for'] == $REPORT_TYPE_NE)
    { 
        
        $pricingCompetitorsQuery = 'SELECT NE_P_C.pricing_group_number, GROUP_CONCAT(DISTINCT NE_P_C.zone_name SEPARATOR " & ") AS zone_name, GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR " & ") AS route_name, GROUP_CONCAT(DISTINCT NE_P_C.display_branch_name SEPARATOR " & ") AS branch_name
                     FROM ne_pricing_competitor AS NE_P_C INNER JOIN route_master AS RM ON NE_P_C.route_code = RM.route_code INNER JOIN branch_master AS BM ON RM.branch_code = BM.branch_code
                     WHERE NE_P_C.status = '.$STATUS_ACTIVE.' 
                     GROUP BY NE_P_C.pricing_group_number';
        $pricingCompetitors = mysqli_query($link,$pricingCompetitorsQuery);
    } 
    elseif($_REQUEST['report_for'] == $REPORT_TYPE_ROE)
    {  
        $pricingCompetitorsQuery = 'SELECT ROE_P_C.pricing_group_number, GROUP_CONCAT(DISTINCT ROE_P_C.zone_name SEPARATOR " & ") AS zone_name,GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR " & ") AS branch_name
                     FROM roe_pricing_competitor AS ROE_P_C INNER JOIN branch_master AS BM ON ROE_P_C.branch_code = BM.branch_code
                     WHERE ROE_P_C.status = '.$STATUS_ACTIVE.' 
                     GROUP BY ROE_P_C.pricing_group_number';
        $pricingCompetitors = mysqli_query($link,$pricingCompetitorsQuery);
    } 
    while($pricingCompetitor = mysqli_fetch_assoc($pricingCompetitors))
    {
        echo '<tr>';
        if($_REQUEST['report_for'] == $REPORT_TYPE_NE)
        { 
            echo '<td>'.$pricingCompetitor["branch_name"].'</td>';
            echo '<td>'.$pricingCompetitor["route_name"].'</td>';
            echo '<td>'.$pricingCompetitor["zone_name"].'</td>';
            $companyPricingsQuery =  'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", NE_P_C.route_code, "\'")) AS route_code, ANY_VALUE(NE_P_C.is_compared_with) AS is_compared_with,
    ANY_VALUE(NE_P_C.incoterm_type) AS incoterm_type, GROUP_CONCAT(DISTINCT CONCAT("\'", COMP_M.competitor_name, "\'")) AS competitor_name, GROUP_CONCAT(DISTINCT NE_P_C.product_display_name SEPARATOR " & ") AS product_display_name FROM ne_pricing_competitor AS NE_P_C INNER JOIN competitor_group_master AS COMP_M ON NE_P_C.competitor_id = COMP_M.sl_no WHERE NE_P_C.pricing_group_number = "'.$pricingCompetitor['pricing_group_number'].'" AND NE_P_C.status = '.$STATUS_ACTIVE.' GROUP BY NE_P_C.company_display_name';
        }
        elseif($_REQUEST['report_for'] == $REPORT_TYPE_ROE)
        {
            echo '<td>'.$pricingCompetitor["branch_name"].'</td>';
            echo '<td>'.$pricingCompetitor["branch_name"].'</td>';
            echo '<td>'.$pricingCompetitor["zone_name"].'</td>';
            $companyPricingsQuery =  'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", ROE_P_C.branch_code, "\'")) AS branch_code, ANY_VALUE(ROE_P_C.is_compared_with) AS is_compared_with, ANY_VALUE(ROE_P_C.incoterm_type) AS incoterm_type, GROUP_CONCAT(DISTINCT CONCAT("\'", COMP_M.competitor_name, "\'")) AS competitor_name, GROUP_CONCAT(DISTINCT ROE_P_C.product_display_name SEPARATOR " & ") AS product_display_name FROM roe_pricing_competitor AS ROE_P_C INNER JOIN competitor_group_master AS COMP_M ON ROE_P_C.competitor_id = COMP_M.sl_no WHERE ROE_P_C.pricing_group_number = "'.$pricingCompetitor['pricing_group_number'].'" AND ROE_P_C.status = '.$STATUS_ACTIVE.' GROUP BY ROE_P_C.company_display_name';
        } 
        $companyPricings = mysqli_query($link,$companyPricingsQuery);
      
        $productComparedFrom = array();
        $productComparedFromCount = 0;
        $incotermComparedFrom = array();
        $billingComparedFrom = array();
        $wspComparedFrom = array();
        $rspComparedFrom = array();

        $productComparedTo = array();
        $productComparedToCount = 0;
        $incotermComparedTo = array();
        $billingComparedTo = array();
        $wspComparedTo = array();
        $rspComparedTo = array();

        while($companyPricing = mysqli_fetch_assoc($companyPricings))
        {
            
            $competitor_string = $companyPricing['competitor_name'];
            if($_REQUEST['report_for'] == $REPORT_TYPE_NE)
            {
                $pricingMainWhereCondition = "RM.route_code IN (".$companyPricing['route_code'].")";
            }
            elseif($_REQUEST['report_for'] == $REPORT_TYPE_ROE)
            {
                $pricingMainWhereCondition = "BM.branch_code IN (".$companyPricing['branch_code'].")";
            }
            // echo($competitor_string);
            // $marketFeedBacksQuery = "SELECT RM.route_name,BM.branch_name,MF.competitor_name,
            // 				SUM(MF.PTD) PTD,SUM(MF.PTR) PTR,SUM(MF.PTC) PTC,SUM(MF.PV) PV ,SUM(MF.billing_ex_for) billing_ex_for,SUM(MF.wsp_ex_for) wsp_ex_for
            // 				FROM market_feedback MF,employee_master EM,route_master RM,branch_master BM 
            // 				WHERE MF.route_code=RM.route_code AND RM.branch_code=BM.branch_code AND
            // 				SUBSTRING(MF.market_feedback_id,3,5)=EM.emp_code AND 
            // 				(DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') AND  
            // 				MF.competitor_name IN (".$competitor_string .") AND BM.branch_code = '".$roePricing["branch_code"]."'
            // 				GROUP BY 
            //                 RM.route_name,
            //                 BM.branch_name,
            //                 MF.competitor_name,
            //                 SUBSTRING(MF.market_feedback_id,3,5),
            //                 DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y'),
            //                 MF.customer_code";

            //----previous joining and conditon query by jayanta for bkp-------
            // FROM market_feedback MF
            //                             JOIN route_master RM ON MF.route_code = RM.route_code
            //                             JOIN branch_master BM ON RM.branch_code = BM.branch_code
            //                             JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
            //                             WHERE 
            //                                 DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
            //                                 AND MF.competitor_name IN ($competitor_string)
            //                                 AND ".$pricingMainWhereCondition."
            //                             GROUP BY 
            //                                 RM.route_name,
            //                                 BM.branch_name,
            //                                 MF.competitor_name,
            //                                 SUBSTRING(MF.market_feedback_id, 3, 5),
            //                                 DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y'),
            //                                 MF.customer_code
            //----previous joining and conditon query by jayanta for bkp-------

            $marketFeedBacksQuery = "WITH aggregated_data AS (
                                        SELECT 
                                            RM.route_name,
                                            BM.branch_name,
                                            MF.competitor_name,
                                            SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
                                            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS feedback_date,
                                            MF.customer_code,
                                            
                                            SUM(MF.PTD) AS PTD,
                                            SUM(MF.PTR) AS PTR,
                                            SUM(MF.PTC) AS PTC,
                                            SUM(MF.PV) AS PV,
                                            SUM(MF.billing_ex_for) AS billing_ex_for,
                                            SUM(MF.wsp_ex_for) AS wsp_ex_for

                                        FROM market_feedback MF
                                        JOIN customer_master CM 
                                            ON MF.customer_code = CM.customer_code
                                        JOIN employee_master EM 
                                            ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
                                        JOIN route_master RM 
                                            ON CM.route_code = RM.route_code
                                        JOIN branch_master BM 
                                            ON CM.branch_code = BM.branch_code

                                        WHERE 
                                            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
                                                BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                                            AND MF.competitor_name IN (" . $competitor_string . ")
                                            AND " . $pricingMainWhereCondition . "

                                        GROUP BY 
                                            RM.route_name,
                                            BM.branch_name,
                                            MF.competitor_name,
                                            SUBSTRING(MF.market_feedback_id, 3, 5),
                                            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y'),
                                            MF.customer_code
                                    ),

                                    ptd_freq AS (
                                        SELECT PTD, COUNT(*) AS cnt,
                                            RANK() OVER (ORDER BY COUNT(*) DESC, PTD DESC) AS rnk
                                        FROM aggregated_data
                                        GROUP BY PTD HAVING PTD > 0
                                    ),

                                    ptr_freq AS (
                                        SELECT PTR, COUNT(*) AS cnt,
                                            RANK() OVER (ORDER BY COUNT(*) DESC, PTR DESC) AS rnk
                                        FROM aggregated_data
                                        GROUP BY PTR HAVING PTR > 0
                                    ),

                                    ptc_freq AS (
                                        SELECT PTC, COUNT(*) AS cnt,
                                            RANK() OVER (ORDER BY COUNT(*) DESC, PTC DESC) AS rnk
                                        FROM aggregated_data
                                        GROUP BY PTC HAVING PTC > 0
                                    ),

                                    billing_freq AS (
                                        SELECT billing_ex_for, COUNT(*) AS cnt,
                                            RANK() OVER (ORDER BY COUNT(*) DESC, billing_ex_for DESC) AS rnk
                                        FROM aggregated_data
                                        GROUP BY billing_ex_for HAVING billing_ex_for > 0
                                    ),

                                    wsp_freq AS (
                                        SELECT wsp_ex_for, COUNT(*) AS cnt,
                                            RANK() OVER (ORDER BY COUNT(*) DESC, wsp_ex_for DESC) AS rnk
                                        FROM aggregated_data
                                        GROUP BY wsp_ex_for HAVING wsp_ex_for > 0
                                    ),

                                    most_common_values AS (
                                        SELECT 
                                            (SELECT PTD FROM ptd_freq WHERE rnk = 1 LIMIT 1) AS most_frequent_ptd,
                                            (SELECT PTR FROM ptr_freq WHERE rnk = 1 LIMIT 1) AS most_frequent_ptr,
                                            (SELECT PTC FROM ptc_freq WHERE rnk = 1 LIMIT 1) AS most_frequent_ptc,
                                            (SELECT billing_ex_for FROM billing_freq WHERE rnk = 1 LIMIT 1) AS most_frequent_billing_ex_for,
                                            (SELECT wsp_ex_for FROM wsp_freq WHERE rnk = 1 LIMIT 1) AS most_frequent_wsp_ex_for
                                    )

                                    SELECT 
                                        ad.*,
                                        mcv.most_frequent_ptd,
                                        mcv.most_frequent_ptr,
                                        mcv.most_frequent_ptc,
                                        mcv.most_frequent_billing_ex_for,
                                        mcv.most_frequent_wsp_ex_for
                                    FROM aggregated_data ad
                                    JOIN most_common_values mcv ON TRUE;";
            
            //  echo $marketFeedBacksQuery."<br><br>";
            $marketFeedBacks = mysqli_query($link,$marketFeedBacksQuery);
            // echo mysqli_num_rows($marketFeedBacks)."<br><br>";
            // if (!$marketFeedBacks) {
            //     echo "Query error: " . mysqli_error($link);
            //     break;
            // }
            
            if(mysqli_num_rows($marketFeedBacks) > 0)
            {
                while($marketFeedBack = mysqli_fetch_assoc($marketFeedBacks))
                {

                    if($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR)
                    {
                        // echo $marketFeedBack["billing_ex_for"].'&emsp;'.$marketFeedBack["wsp_ex_for"].'&emsp;'.$marketFeedBack["PTC"].'&emsp;'.'<br>';
                        
                        // echo '<td>'.$roePricing["product_display_name"].'</td>';
                        
                        // echo '<td>FOR</td>';
                        
                        // echo '<td>'.($marketFeedBack["most_frequent_billing_ex_for"] == 0 ? ' - ' : $marketFeedBack["most_frequent_billing_ex_for"]).'</td>';
                        // echo '<td>'.($marketFeedBack["most_frequent_wsp_ex_for"] == 0 ? ' - ' : $marketFeedBack["most_frequent_wsp_ex_for"]).'</td>';
                        // echo '<td>'.($marketFeedBack["most_frequent_ptc"] == 0 ? ' - ' : $marketFeedBack["most_frequent_ptc"]).'</td>';

                        if($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES)
                        {
                            array_push($productComparedFrom, $companyPricing["product_display_name"]);
                            array_push($incotermComparedFrom, "FOR");
                            array_push($billingComparedFrom, $marketFeedBack["most_frequent_billing_ex_for"]);
                            array_push($wspComparedFrom, $marketFeedBack["most_frequent_wsp_ex_for"]);
                            array_push($rspComparedFrom, $marketFeedBack["most_frequent_ptc"]);
                        }
                        elseif($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO)
                        {
                            array_push($productComparedTo, $companyPricing["product_display_name"]);
                            array_push($incotermComparedTo, "FOR");
                            array_push($billingComparedTo, $marketFeedBack["most_frequent_billing_ex_for"]);
                            array_push($wspComparedTo, $marketFeedBack["most_frequent_wsp_ex_for"]);
                            array_push($rspComparedTo, $marketFeedBack["most_frequent_ptc"]);
                        }
                    }
                    elseif($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX)
                    {
                        // echo $marketFeedBack["most_frequent_ptd"].'&emsp;'.$marketFeedBack["most_frequent_ptr"].'&emsp;'.$marketFeedBack["most_frequent_ptc"].'&emsp;'.'<br>';
                        
                        // echo '<td>'.$roePricing["product_display_name"].'</td>';
                        // echo '<td>EX</td>';
                        // echo '<td>'.($marketFeedBack["most_frequent_ptd"] == 0 ? ' - ' : $marketFeedBack["most_frequent_ptd"]).'</td>';
                        // echo '<td>'.($marketFeedBack["most_frequent_ptr"] == 0 ? ' - ' : $marketFeedBack["most_frequent_ptr"]).'</td>';
                        // echo '<td>'.($marketFeedBack["most_frequent_ptc"] == 0 ? ' - ' : $marketFeedBack["most_frequent_ptc"]).'</td>';

                        if($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES)
                        {
                            array_push($productComparedFrom, $companyPricing["product_display_name"]);
                            array_push($incotermComparedFrom, "EX");
                            array_push($billingComparedFrom, $marketFeedBack["most_frequent_ptd"]);
                            array_push($wspComparedFrom, $marketFeedBack["most_frequent_ptr"]);
                            array_push($rspComparedFrom, $marketFeedBack["most_frequent_ptc"]);
                        }
                        elseif($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO)
                        {
                            array_push($productComparedTo, $companyPricing["product_display_name"]);
                            array_push($incotermComparedTo, "EX");
                            array_push($billingComparedTo, $marketFeedBack["most_frequent_ptd"]);
                            array_push($wspComparedTo, $marketFeedBack["most_frequent_ptr"]);
                            array_push($rspComparedTo, $marketFeedBack["most_frequent_ptc"]);
                        }
                    }
                    
                    break;
                }
            }
            else
            {
                
                if($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES)
                {
                    array_push($productComparedFrom, $companyPricing["product_display_name"]);
                    array_push($billingComparedFrom, 0);
                    array_push($wspComparedFrom, 0);
                    array_push($rspComparedFrom, 0);   

                    if($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR)
                    {
                        array_push($incotermComparedFrom, "FOR");
                    }
                    elseif($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX)
                    {
                        array_push($incotermComparedFrom, "EX");
                    }
                }
                elseif($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO)
                {
                    array_push($productComparedTo, $companyPricing["product_display_name"]);
                    array_push($billingComparedTo, 0);
                    array_push($wspComparedTo, 0);
                    array_push($rspComparedTo, 0);

                    if($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR)
                    {
                        array_push($incotermComparedTo, "FOR");
                    }
                    elseif($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX)
                    {
                        array_push($incotermComparedTo, "EX");
                    }
                }
                // for($loopIndex = 0; $loopIndex < $comparisonFieldsColSpanHeader; $loopIndex++)
                // {
                //     echo '<td align="center"> - </td>';
                // }
            }
        }
        $productComparedFromCount = count($productComparedFrom);
        $productComparedToCount = count($productComparedTo);
        // for($comparedFromIndex = 0; $comparedFromIndex < count($billingComparedFrom); $comparedFromIndex++)
        // {
        //     for($comparedToIndex = 0; $comparedToIndex < count($billingComparedTo); $comparedToIndex++)
        //     {
        //         if($billingComparedFrom[$comparedFromIndex] == 0 || $billingComparedTo[$comparedToIndex] == 0)
        //         {
        //             echo '<td> - </td>';
        //         }
        //         else
        //         {
        //             // echo '<td><span style="color:red;">&#9660;&nbsp;&nbsp;</span>'.($billingComparedFrom[$comparedFromIndex] - $billingComparedTo[$comparedToIndex]).'</td>';
        //             echo '<td>'.($billingComparedFrom[$comparedFromIndex] - $billingComparedTo[$comparedToIndex]).'</td>';
        //         }
        //         if($wspComparedFrom[$comparedFromIndex] == 0 || $wspComparedTo[$comparedToIndex] == 0)
        //         {
        //             echo '<td> - </td>';
        //         }
        //         else
        //         {
        //             if(($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]) > 0)
        //             {
        //                 echo '<td> <span style="color:#68a490;">&#9650;&nbsp;&nbsp;</span>'.($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]).'</td>';
        //             }
        //             elseif(($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]) == 0)
        //             {
                                               
        //                 echo '<td> <span style="color:#a4802b; height:2px;width: 10px;">&#9644;&nbsp;&nbsp;</span> '.($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]).'</td>';                       

        //             }
        //             else
        //             {
        //                 echo '<td><span style="color:#d65532;">&#9660;&nbsp;&nbsp;</span> '.($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]).'</td>';
        //             }
        //         }
        //         if($rspComparedFrom[$comparedFromIndex] == 0 || $rspComparedTo[$comparedToIndex] == 0)
        //         {
        //             echo '<td> - </td>';
        //         }
        //         else
        //         {
        //             echo '<td>'.($rspComparedFrom[$comparedFromIndex] - $rspComparedTo[$comparedToIndex]).'</td>';
        //         }
        //     }
        // }
        for($comparedFromIndex = 0; $comparedFromIndex < $productComparedFromCount; $comparedFromIndex++)
        {
            echo '<td>'.$productComparedFrom[$comparedFromIndex].'</td>';
            echo '<td>'.$incotermComparedFrom[$comparedFromIndex].'</td>';
            echo '<td>'.($billingComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $billingComparedFrom[$comparedFromIndex]).'</td>';
            echo '<td>'.($wspComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $wspComparedFrom[$comparedFromIndex]).'</td>';
            echo '<td>'.($rspComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $rspComparedFrom[$comparedFromIndex]).'</td>';
            for($comparedToIndex = 0; $comparedToIndex < $productComparedToCount; $comparedToIndex++)
            {
                echo '<td>'.$productComparedTo[$comparedToIndex].'</td>';
                echo '<td>'.$incotermComparedTo[$comparedToIndex].'</td>';
                echo '<td>'.($billingComparedTo[$comparedToIndex] == 0 ? ' - ' : $billingComparedTo[$comparedToIndex]).'</td>';
                echo '<td>'.($wspComparedTo[$comparedToIndex] == 0 ? ' - ' : $wspComparedTo[$comparedToIndex]).'</td>';
                echo '<td>'.($rspComparedTo[$comparedToIndex] == 0 ? ' - ' : $rspComparedTo[$comparedToIndex]).'</td>';
                
                if($billingComparedFrom[$comparedFromIndex] == 0 || $billingComparedTo[$comparedToIndex] == 0)
                {
                    echo '<td> - </td>';
                }
                else
                {
                    echo priceDiffValWithIcon($billingComparedFrom[$comparedFromIndex] - $billingComparedTo[$comparedToIndex]);
                }
                if($wspComparedFrom[$comparedFromIndex] == 0 || $wspComparedTo[$comparedToIndex] == 0)
                {
                    echo '<td> - </td>';
                }
                else
                {
                    echo priceDiffValWithIcon($wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex]);
                }
                if($rspComparedFrom[$comparedFromIndex] == 0 || $rspComparedTo[$comparedToIndex] == 0)
                {
                    echo '<td> - </td>';
                }
                else
                {
                    echo priceDiffValWithIcon($rspComparedFrom[$comparedFromIndex] - $rspComparedTo[$comparedToIndex]);
                }
            }
        }
        echo '</tr>';
    }
    echo '</table>';
    mysqli_close($link);
?>