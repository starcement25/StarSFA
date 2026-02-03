<?php
ini_set('memory_limit', '1024M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");


ob_start();
session_start();

// === Configure logging ===
$logFile = "/var/log/pricing_comparison_cron.log";
function logMessage($message)
{
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}
logMessage("Cron Started");

define("SERVER", "localhost");
define("USER", "root");
define("PASSWORD", "Passw0rd123#$");
define("DB", "acedns_STAR");

// define("SERVER","103.233.25.204");
// define("USER","acedns_dnsprod");
// define("PASSWORD","dnsprod1234#");
// define("DB","acedns_STAR");

require("include/dbcon.php");
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// === Catch PHP runtime errors too ===
set_error_handler(function ($severity, $message, $file, $line) {
    logMessage("PHP Error: $message in $file on line $line");
});
set_exception_handler(function ($exception) {
    logMessage("Uncaught Exception: " . $exception->getMessage());
});
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        logMessage("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");
    }
});

function send_the_mail(array $to_emails, $subject, $bodyml, array $attachment_paths = [], array $bodyEmbadedImages = [])
{
    date_default_timezone_set("Asia/Kolkata");

    try {
        foreach ($to_emails as $emailValues) {
            $mail = new PHPMailer(true);
            // $mail->isSMTP();
            // $mail->SMTPDebug  = 2;
            // $mail->Debugoutput = 'html';
            // $mail->SMTPAuth   = true;
            // $mail->Host       = "smtp.gmail.com";
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            // $mail->Port       = 587;
            // $mail->Username   = 'test.sbinfowaves@gmail.com';
            // $mail->Password   = 'dzltchhdafyfnqhh'; 

            $mail->isSMTP();
            $mail->Host = "cloudmail2.up99plus.com";
            $mail->Port = 25;
            $mail->SMTPAuth = true;
            $mail->Username = "starcement@cloudmail.up99plus.com";
            $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
            $mail->SMTPSecure = false; // Explicit TLS encryption is not used
            $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
            $mail->setFrom('starcement@cloudmail.up99plus.com', 'SFA');

            // Debug settings
            $mail->SMTPDebug = 0; // Enable debug (0 = off, 1 = client msgs, 2 = client + server)
            // $mail->Debugoutput = function($str, $level) {
            //     // file_put_contents('/var/log/pricing_comparison_cron.log', date('Y-m-d H:i:s') . " [Level $level] $str\n", FILE_APPEND);
            //     logMessage("SMTP [Level $level]: $str");
            // };

            if (is_array($emailValues)) {
                if (isset($emailValues['emails']) && is_array($emailValues['emails'])) {
                    foreach ($emailValues['emails'] as $email) {
                        if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                            $mail->addAddress(trim($email));
                        }
                    }
                }
                if (isset($emailValues['ccs']) && is_array($emailValues['ccs'])) {
                    foreach ($emailValues['ccs'] as $cc) {
                        if (is_array($cc) && isset($cc['email']) && filter_var(trim($cc['email']), FILTER_VALIDATE_EMAIL)) {
                            if (isset($cc['name'])) {
                                $mail->addCC($cc['email'], $cc['name']);
                            }
                            $mail->addCC($cc['email']);
                        }
                    }
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $bodyml;
            $mail->AltBody = strip_tags($bodyml);

            if (is_array($attachment_paths)) {
                foreach ($attachment_paths as $attachment_path) {
                    if ($attachment_path && file_exists($attachment_path)) {
                        $mail->addAttachment($attachment_path);
                    }
                }
            }
            if (is_array($bodyEmbadedImages)) {
                foreach ($bodyEmbadedImages as $bodyEmbadedImage) {
                    if (is_array($bodyEmbadedImage) && isset($bodyEmbadedImage) && isset($bodyEmbadedImage['image_path']) && isset($bodyEmbadedImage['cid'])) {
                        $mail->addEmbeddedImage($bodyEmbadedImage['image_path'], $bodyEmbadedImage['cid']);
                    }
                }
            }

            $mail->send();
            logMessage("Email sent successfully for mail subject " . $subject);
        }
    } catch (Exception $e) {
        echo " PHPMailer Error: " . $mail->ErrorInfo . "<br>";
        logMessage("PHPMailer Error: " . $mail->ErrorInfo);
    } catch (\Throwable $e) {
        echo "PHP Throwable Error: " . $e->getMessage();
        logMessage("PHP Throwable Error: " . $e->getMessage());
    }
}

function dateFormat()
{
    $date = new DateTime();
    $date->modify('-1 day');

    // Get day with ordinal suffix (e.g., 5th)
    $day = $date->format('j'); // Day without leading zero
    $suffix = 'th';
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1:
                $suffix = 'st';
                break;
            case 2:
                $suffix = 'nd';
                break;
            case 3:
                $suffix = 'rd';
                break;
        }
    }

    $formattedDate = $day . $suffix . '_' . $date->format('F') . '_' . $date->format('Y');
    return $formattedDate;
}

function getOrdinalSuffix($day)
{
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1:
                return 'st';
            case 2:
                return 'nd';
            case 3:
                return 'rd';
        }
    }
    return 'th';
}

function columnNumberToLetter($num)
{
    $letters = '';
    while ($num > 0) {
        $num--;
        $letters = chr($num % 26 + 65) . $letters;
        $num = intval($num / 26);
    }
    return $letters;
}
// function generateMarketFeedbackDetailsSheet($spreadsheet, $link, $start_date, $end_date, $reportFor, $pricingMainWhereCondition, $competitor_string)
// {
// // print_r($competitor_string);die;
//     $spreadsheet->createSheet();
//     $sheetIndex = $spreadsheet->getSheetCount() - 1;
//     $spreadsheet->setActiveSheetIndex($sheetIndex);
//     $sheet = $spreadsheet->getActiveSheet();
//     $sheet->setTitle('Market Feedback Details');


//     $competitor_name_array = array();

//     if ($reportFor == "ne") {
//         $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns 
//                                FROM competitor_group_master 
//                                WHERE acedns='yes' 
//                                AND branch_code IS NOT NULL 
//                                AND branch_code!='' 
//                                ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC, competitor_name ASC";
//     } else {
//         $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns 
//                                FROM competitor_group_master 
//                                WHERE acedns='yes' 
//                                AND branch_code IS NOT NULL 
//                                AND branch_code!='' 
//                                ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC, competitor_name ASC";
//     }

//     $res_competitor_name = mysqli_query($link, $sql_competitor_name);
//     $countcompetitor = mysqli_num_rows($res_competitor_name);
//     $colspanheader = 10 + ($countcompetitor * 5);


//     $sheet->mergeCells('A1:' . columnNumberToLetter($colspanheader) . '1');
//     $sheet->setCellValue('A1', 'Market Feedback Details');
//     $styleArray = [
//         'font' => ['bold' => true, 'size' => 14],
//         'alignment' => [
//             'horizontal' => Alignment::HORIZONTAL_CENTER,
//             'vertical' => Alignment::VERTICAL_CENTER
//         ],
//         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
//         'fill' => [
//             'fillType' => Fill::FILL_SOLID,
//             'startColor' => ['argb' => 'FFFCE4D6']
//         ],
//     ];
//     $sheet->getStyle('A1:' . columnNumberToLetter($colspanheader) . '1')->applyFromArray($styleArray);

//     // Header Row 2 - Main Headers
//     $rowNum = 2;
//     $headers = [
//         'SI. No',
//         'Date of Visit',
//         'Emp Code',
//         'Emp Name',
//         'Branch',
//         'Cust Category',
//         'Cust Code',
//         'Cust Name',
//         'Route Name',
//         'Contact No'
//     ];

//     for ($i = 0; $i < count($headers); $i++) {
//         $sheet->setCellValue(columnNumberToLetter($i + 1) . $rowNum, $headers[$i]);
//     }

//     $colNum = 11;
//     while ($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)) {
//         $competitor_name = $row_competitor_name['competitor_name'];
//         array_push($competitor_name_array, $competitor_name);

//         $mergeFrom = $colNum;
//         $mergeTo = $colNum + 4;
//         $sheet->mergeCells(columnNumberToLetter($mergeFrom) . $rowNum . ':' . columnNumberToLetter($mergeTo) . $rowNum);
//         $sheet->setCellValue(columnNumberToLetter($mergeFrom) . $rowNum, $competitor_name);
//         $colNum = $mergeTo + 1;
//     }

//     $styleArray = [
//         'font' => ['bold' => true],
//         'alignment' => [
//             'horizontal' => Alignment::HORIZONTAL_CENTER,
//             'vertical' => Alignment::VERTICAL_CENTER
//         ],
//         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
//         'fill' => [
//             'fillType' => Fill::FILL_SOLID,
//             'startColor' => ['argb' => 'FFD9E1F2']
//         ],
//     ];
//     $sheet->getStyle('A2:' . columnNumberToLetter($colspanheader) . '2')->applyFromArray($styleArray);

//     // Header Row 3 - Sub Headers
//     $rowNum = 3;
//     $colNum = 11;
//     foreach ($competitor_name_array as $competitorheaderval) {
//         $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'Billing Ex');
//         $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'Billing For');
//         $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'WSP Ex');
//         $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'WSP For');
//         $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'RSP');
//     }
//     $sheet->getStyle('A3:' . columnNumberToLetter($colspanheader) . '3')->applyFromArray($styleArray);

//     // Query to get employee codes and branch codes from the pricing configuration
//     $employee_codes = array();
//     $branch_codes = array();

//     // if ($reportFor == "ne") {
//     //     // Extract employee codes from routes configured in pricing
//     //     $empQuery = "SELECT DISTINCT SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code
//     //                  FROM market_feedback MF
//     //                  JOIN customer_master CM ON MF.customer_code = CM.customer_code
//     //                  WHERE " . $pricingMainWhereCondition . "
//     //                  AND DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
//     //                      BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
//     // } else {
//     //     // For ROE, extract from branch codes
//     //     $empQuery = "SELECT DISTINCT SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code
//     //                  FROM market_feedback MF
//     //                  JOIN customer_master CM ON MF.customer_code = CM.customer_code
//     //                  JOIN branch_master BM ON CM.branch_code = BM.branch_code
//     //                  WHERE " . $pricingMainWhereCondition . "
//     //                  AND DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
//     //                      BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
//     // }
//     $empQuery = "SELECT DISTINCT  emp_code
//                      FROM employee_master 
//                      WHERE acedns='Y'
//                      ";
//     $empResult = mysqli_query($link, $empQuery);
//     while ($empRow = mysqli_fetch_assoc($empResult)) {
//         $employee_codes[] = "'" . $empRow['emp_code'] . "'";
//     }

//     $employee_arg = implode(',', $employee_codes);
//     //  echo $employee_arg;die;
//     if (empty($employee_arg)) {
//         // No employees found, add a row indicating no data
//         $rowNum = 4;
//         $sheet->mergeCells('A4:' . columnNumberToLetter($colspanheader) . '4');
//         $sheet->setCellValue('A4', 'No Records found');
//         $sheet->getStyle('A4:' . columnNumberToLetter($colspanheader) . '4')->applyFromArray([
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
//             'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
//         ]);
//         return;
//     }

//     // Main query for market feedback data
//     $sql_competitor_stock = "
//         SELECT
//             CM.customer_name,
//             CM.phone_no,
//             CM.cust_type,
//             CM.dns_customer_code,
//             RM.route_name,
//             EM.emp_name,
//             EM.dns_emp_code,
//             SUM(MF.PTD) AS PTD,
//             SUM(MF.PTR) AS PTR,
//             SUM(MF.PTC) AS PTC,
//             SUM(MF.PV) AS PV,
//             SUM(MF.billing_ex_for) AS billing_ex_for,
//             SUM(MF.wsp_ex_for) AS wsp_ex_for,
//             DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
//             BM.branch_name,
//             MF.competitor_name,
//             SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
//             MF.customer_code
//         FROM
//             market_feedback MF,
//             customer_master CM,
//             employee_master EM,
//             route_master RM,
//             branch_master BM
//         WHERE
//             MF.customer_code = CM.customer_code
//             AND CM.route_code = RM.route_code
//             AND SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
//             AND SUBSTRING(MF.market_feedback_id, 3, 5) IN (" . $employee_arg . ")
//             AND (DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "')
//             AND MF.competitor_name IN (" . $competitor_string . ")
//             AND CM.branch_code = BM.branch_code
//         GROUP BY
//             CM.customer_name,
//             CM.phone_no,
//             CM.cust_type,
//             CM.dns_customer_code,
//             RM.route_name,
//             EM.emp_name,
//             EM.dns_emp_code,
//             DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y'),
//             BM.branch_name,
//             MF.competitor_name,
//             SUBSTRING(MF.market_feedback_id, 3, 5),
//             MF.customer_code
//         ORDER BY
//             visit_date DESC,
//             FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
//             MF.competitor_name ASC
//     ";
// //  echo $sql_competitor_stock;die;
//     $res_competitor_stock = mysqli_query($link, $sql_competitor_stock);
//     $count_competitor_stock = mysqli_num_rows($res_competitor_stock);

//     if ($count_competitor_stock > 0) {
//         $customer_emp_date_array = array();

//         // First pass - organize data
//         while ($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)) {
//             $dns_emp_code = $row_competitor_stock['dns_emp_code'];
//             $visit_date = $row_competitor_stock['visit_date'];
//             $dns_customer_code = $row_competitor_stock['dns_customer_code'];

//             $key = $dns_emp_code . '#' . $dns_customer_code . '#' . $visit_date;

//             ${'emp_name' . $key} = $row_competitor_stock['emp_name'];
//             ${'customer_name' . $key} = $row_competitor_stock['customer_name'];
//             ${'phone_no' . $key} = $row_competitor_stock['phone_no'];
//             ${'cust_type' . $key} = $row_competitor_stock['cust_type'];
//             ${'route_name' . $key} = $row_competitor_stock['route_name'];
//             ${'branch_name' . $key} = $row_competitor_stock['branch_name'];

//             $comp_key = $key . $row_competitor_stock['competitor_name'];
//             ${'competitor_name_db' . $comp_key} = $row_competitor_stock['competitor_name'];
//             ${'PTD' . $comp_key} = $row_competitor_stock['PTD'];
//             ${'PTR' . $comp_key} = $row_competitor_stock['PTR'];
//             ${'PTC' . $comp_key} = $row_competitor_stock['PTC'];
//             ${'billing_ex_for' . $comp_key} = $row_competitor_stock['billing_ex_for'];
//             ${'wsp_ex_for' . $comp_key} = $row_competitor_stock['wsp_ex_for'];

//             if (!in_array($key, $customer_emp_date_array)) {
//                 array_push($customer_emp_date_array, $key);
//             }
//         }

//         // Second pass - write to Excel
//         $rowNum = 4;
//         $count = 1;

//         foreach ($customer_emp_date_array as $customer_emp_date_string) {
//             $parts = explode("#", $customer_emp_date_string);
//             $dns_emp_code_val = $parts[0];
//             $dns_customer_code_val = $parts[1];
//             $date_val = $parts[2];
//             $key = $customer_emp_date_string;

//             $sheet->setCellValue('A' . $rowNum, $count);
//             $sheet->setCellValue('B' . $rowNum, $date_val);
//             $sheet->setCellValue('C' . $rowNum, $dns_emp_code_val);
//             $sheet->setCellValue('D' . $rowNum, ${'emp_name' . $key});
//             $sheet->setCellValue('E' . $rowNum, ${'branch_name' . $key});
//             $sheet->setCellValue('F' . $rowNum, ${'cust_type' . $key});
//             $sheet->setCellValue('G' . $rowNum, $dns_customer_code_val);
//             $sheet->setCellValue('H' . $rowNum, ${'customer_name' . $key});
//             $sheet->setCellValue('I' . $rowNum, ${'route_name' . $key});
//             $sheet->setCellValue('J' . $rowNum, ${'phone_no' . $key});

//             $colNum = 11;
//             foreach ($competitor_name_array as $competitorval) {
//                 $comp_key = $key . $competitorval;

//                 if (
//                     isset(${'competitor_name_db' . $comp_key}) &&
//                     $competitorval == ${'competitor_name_db' . $comp_key}
//                 ) {
//                     $total_PTD_ex = ${'PTD' . $comp_key} == 0 ? '-' : ${'PTD' . $comp_key};
//                     $total_PTD_for = ${'billing_ex_for' . $comp_key} == 0 ? '-' : ${'billing_ex_for' . $comp_key};
//                     $total_PTR_ex = ${'PTR' . $comp_key} == 0 ? '-' : ${'PTR' . $comp_key};
//                     $total_PTR_for = ${'wsp_ex_for' . $comp_key} == 0 ? '-' : ${'wsp_ex_for' . $comp_key};
//                     $total_PTC = ${'PTC' . $comp_key} == 0 ? '-' : ${'PTC' . $comp_key};
//                 } else {
//                     $total_PTD_ex = '-';
//                     $total_PTD_for = '-';
//                     $total_PTR_ex = '-';
//                     $total_PTR_for = '-';
//                     $total_PTC = '-';
//                 }

//                 $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTD_ex);
//                 $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTD_for);
//                 $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTR_ex);
//                 $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTR_for);
//                 $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTC);
//             }

//             // Apply styling to data row
//             $styleArray = [
//                 'alignment' => [
//                     'horizontal' => Alignment::HORIZONTAL_CENTER,
//                     'vertical' => Alignment::VERTICAL_CENTER
//                 ],
//                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
//             ];
//             $sheet->getStyle('A' . $rowNum . ':' . columnNumberToLetter($colspanheader) . $rowNum)->applyFromArray($styleArray);

//             $rowNum++;
//             $count++;
//         }
//     } else {
//         $rowNum = 4;
//         $sheet->mergeCells('A4:' . columnNumberToLetter($colspanheader) . '4');
//         $sheet->setCellValue('A4', 'No Records found');
//         $sheet->getStyle('A4:' . columnNumberToLetter($colspanheader) . '4')->applyFromArray([
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
//             'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
//         ]);
//     }

//     // Auto-size columns
//     foreach (range('A', columnNumberToLetter($colspanheader)) as $col) {
//         $sheet->getColumnDimension($col)->setAutoSize(true);
//     }
// }
function generateMarketFeedbackDetailsSheet($spreadsheet, $link, $start_date, $end_date, $reportFor, $pricingMainWhereCondition, $competitor_string)
{
    $REPORT_TYPE_ROE = "roe";
    $REPORT_TYPE_NE = "ne";
    $STATUS_ACTIVE = 1;
    
    $spreadsheet->createSheet();
    $sheetIndex = $spreadsheet->getSheetCount() - 1;
    $spreadsheet->setActiveSheetIndex($sheetIndex);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Market Feedback Details');

    $competitor_name_array = array();

    // Query competitors based on report type
    if ($reportFor == "ne") {
        $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns 
                               FROM competitor_group_master 
                               WHERE acedns='yes' 
                               AND branch_code IS NOT NULL 
                               AND branch_code!='' 
                               ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC, competitor_name ASC";
    } else {
        $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns 
                               FROM competitor_group_master 
                               WHERE acedns='yes' 
                               AND branch_code IS NOT NULL 
                               AND branch_code!='' 
                               ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC, competitor_name ASC";
    }

    $res_competitor_name = mysqli_query($link, $sql_competitor_name);
    $countcompetitor = mysqli_num_rows($res_competitor_name);
    $colspanheader = 10 + ($countcompetitor * 5);

    // Header Row 1
    $sheet->mergeCells('A1:' . columnNumberToLetter($colspanheader) . '1');
    $sheet->setCellValue('A1', 'Market Feedback Details');
    $styleArray = [
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFCE4D6']
        ],
    ];
    $sheet->getStyle('A1:' . columnNumberToLetter($colspanheader) . '1')->applyFromArray($styleArray);

    // Header Row 2 - Main Headers
    $rowNum = 2;
    $headers = [
        'SI. No', 'Date of Visit', 'Emp Code', 'Emp Name', 'Branch',
        'Cust Category', 'Cust Code', 'Cust Name', 'Route Name', 'Contact No'
    ];

    for ($i = 0; $i < count($headers); $i++) {
        $sheet->setCellValue(columnNumberToLetter($i + 1) . $rowNum, $headers[$i]);
    }

    $colNum = 11;
    while ($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)) {
        $competitor_name = $row_competitor_name['competitor_name'];
        array_push($competitor_name_array, $competitor_name);

        $mergeFrom = $colNum;
        $mergeTo = $colNum + 4;
        $sheet->mergeCells(columnNumberToLetter($mergeFrom) . $rowNum . ':' . columnNumberToLetter($mergeTo) . $rowNum);
        $sheet->setCellValue(columnNumberToLetter($mergeFrom) . $rowNum, $competitor_name);
        $colNum = $mergeTo + 1;
    }

    $styleArray = [
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFD9E1F2']
        ],
    ];
    $sheet->getStyle('A2:' . columnNumberToLetter($colspanheader) . '2')->applyFromArray($styleArray);

    // Header Row 3 - Sub Headers
    $rowNum = 3;
    $colNum = 11;
    foreach ($competitor_name_array as $competitorheaderval) {
        $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'Billing Ex');
        $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'Billing For');
        $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'WSP Ex');
        $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'WSP For');
        $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, 'RSP');
    }
    $sheet->getStyle('A3:' . columnNumberToLetter($colspanheader) . '3')->applyFromArray($styleArray);

    // Get employee codes
    $employee_codes = array();
    $empQuery = "SELECT DISTINCT emp_code
                 FROM employee_master 
                 WHERE acedns='Y'";
    $empResult = mysqli_query($link, $empQuery);
    while ($empRow = mysqli_fetch_assoc($empResult)) {
        $employee_codes[] = "'" . $empRow['emp_code'] . "'";
    }

    $employee_arg = implode(',', $employee_codes);
    
    if (empty($employee_arg)) {
        $rowNum = 4;
        $sheet->mergeCells('A4:' . columnNumberToLetter($colspanheader) . '4');
        $sheet->setCellValue('A4', 'No Records found');
        $sheet->getStyle('A4:' . columnNumberToLetter($colspanheader) . '4')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        return;
    }
    
    // Build comprehensive filter using the SAME logic as Sheet 1
    $all_route_codes = array();
    $all_branch_codes = array();
    
    if ($reportFor == $REPORT_TYPE_NE) {
        // Get all pricing groups for NE
        $pricingGroupsQuery = 'SELECT NE_P_C.pricing_group_number
                        FROM ne_pricing_competitor AS NE_P_C
                        WHERE NE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY NE_P_C.pricing_group_number';
        $pricingGroups = mysqli_query($link, $pricingGroupsQuery);
        
        while ($pricingGroup = mysqli_fetch_assoc($pricingGroups)) {
            // For each pricing group, get route codes (same as Sheet 1 logic)
            $companyPricingsQuery = 'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", NE_P_C.route_code, "\'")) AS route_code
                    FROM ne_pricing_competitor AS NE_P_C 
                    WHERE NE_P_C.pricing_group_number = "' . $pricingGroup['pricing_group_number'] . '" 
                    AND NE_P_C.status = ' . $STATUS_ACTIVE;
            $companyPricings = mysqli_query($link, $companyPricingsQuery);
            $companyPricing = mysqli_fetch_assoc($companyPricings);
            
            if (!empty($companyPricing['route_code'])) {
                $all_route_codes[] = $companyPricing['route_code'];
            }
        }
        
        if (!empty($all_route_codes)) {
            // Combine all route codes and remove duplicates
            $combined_route_codes = implode(',', $all_route_codes);
            $filterCondition = "AND RM.route_code IN (" . $combined_route_codes . ")";
            logMessage("NE Filter applied with route codes");
        } else {
            $filterCondition = "";
            logMessage("WARNING: No route codes found for NE report");
        }
        
    } elseif ($reportFor == $REPORT_TYPE_ROE) {
        // Get all pricing groups for ROE
        $pricingGroupsQuery = 'SELECT ROE_P_C.pricing_group_number
                        FROM roe_pricing_competitor AS ROE_P_C
                        WHERE ROE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY ROE_P_C.pricing_group_number';
        $pricingGroups = mysqli_query($link, $pricingGroupsQuery);
        
        while ($pricingGroup = mysqli_fetch_assoc($pricingGroups)) {
            // For each pricing group, get branch codes (same as Sheet 1 logic)
            $companyPricingsQuery = 'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", ROE_P_C.branch_code, "\'")) AS branch_code
                    FROM roe_pricing_competitor AS ROE_P_C 
                    WHERE ROE_P_C.pricing_group_number = "' . $pricingGroup['pricing_group_number'] . '" 
                    AND ROE_P_C.status = ' . $STATUS_ACTIVE;
            $companyPricings = mysqli_query($link, $companyPricingsQuery);
            $companyPricing = mysqli_fetch_assoc($companyPricings);
            
            if (!empty($companyPricing['branch_code'])) {
                $all_branch_codes[] = $companyPricing['branch_code'];
            }
        }
        
        if (!empty($all_branch_codes)) {
            // Combine all branch codes and remove duplicates
            $combined_branch_codes = implode(',', $all_branch_codes);
            $filterCondition = "AND BM.branch_code IN (" . $combined_branch_codes . ")";
            logMessage("ROE Filter applied with branch codes");
        } else {
            $filterCondition = "";
            logMessage("WARNING: No branch codes found for ROE report");
        }
    }

    // Main query with proper filtering
    $sql_competitor_stock = "
        SELECT
            CM.customer_name,
            CM.phone_no,
            CM.cust_type,
            CM.dns_customer_code,
            RM.route_name,
            EM.emp_name,
            EM.dns_emp_code,
            SUM(MF.PTD) AS PTD,
            SUM(MF.PTR) AS PTR,
            SUM(MF.PTC) AS PTC,
            SUM(MF.PV) AS PV,
            SUM(MF.billing_ex_for) AS billing_ex_for,
            SUM(MF.wsp_ex_for) AS wsp_ex_for,
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
            BM.branch_name,
            MF.competitor_name,
            SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
            MF.customer_code
        FROM
            market_feedback MF
            INNER JOIN customer_master CM ON MF.customer_code = CM.customer_code
            INNER JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
            INNER JOIN route_master RM ON CM.route_code = RM.route_code
            INNER JOIN branch_master BM ON CM.branch_code = BM.branch_code
        WHERE
            SUBSTRING(MF.market_feedback_id, 3, 5) IN (" . $employee_arg . ")
            AND DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
                BETWEEN '" . $start_date . "' AND '" . $end_date . "'
            AND MF.competitor_name IN (" . $competitor_string . ")
            " . $filterCondition . "
        GROUP BY
            CM.customer_name,
            CM.phone_no,
            CM.cust_type,
            CM.dns_customer_code,
            RM.route_name,
            EM.emp_name,
            EM.dns_emp_code,
            visit_date,
            BM.branch_name,
            MF.competitor_name,
            emp_code,
            MF.customer_code
        ORDER BY
            visit_date DESC,
            FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
            MF.competitor_name ASC
    ";

    logMessage("Market Feedback Query for $reportFor executed");
    
    $res_competitor_stock = mysqli_query($link, $sql_competitor_stock);
    $count_competitor_stock = mysqli_num_rows($res_competitor_stock);

    if ($count_competitor_stock > 0) {
        $customer_emp_date_array = array();

        // First pass - organize data
        while ($row_competitor_stock = mysqli_fetch_assoc($res_competitor_stock)) {
            $dns_emp_code = $row_competitor_stock['dns_emp_code'];
            $visit_date = $row_competitor_stock['visit_date'];
            $dns_customer_code = $row_competitor_stock['dns_customer_code'];

            $key = $dns_emp_code . '#' . $dns_customer_code . '#' . $visit_date;

            ${'emp_name' . $key} = $row_competitor_stock['emp_name'];
            ${'customer_name' . $key} = $row_competitor_stock['customer_name'];
            ${'phone_no' . $key} = $row_competitor_stock['phone_no'];
            ${'cust_type' . $key} = $row_competitor_stock['cust_type'];
            ${'route_name' . $key} = $row_competitor_stock['route_name'];
            ${'branch_name' . $key} = $row_competitor_stock['branch_name'];

            $comp_key = $key . $row_competitor_stock['competitor_name'];
            ${'competitor_name_db' . $comp_key} = $row_competitor_stock['competitor_name'];
            ${'PTD' . $comp_key} = $row_competitor_stock['PTD'];
            ${'PTR' . $comp_key} = $row_competitor_stock['PTR'];
            ${'PTC' . $comp_key} = $row_competitor_stock['PTC'];
            ${'billing_ex_for' . $comp_key} = $row_competitor_stock['billing_ex_for'];
            ${'wsp_ex_for' . $comp_key} = $row_competitor_stock['wsp_ex_for'];

            if (!in_array($key, $customer_emp_date_array)) {
                array_push($customer_emp_date_array, $key);
            }
        }

        // Second pass - write to Excel
        $rowNum = 4;
        $count = 1;

        foreach ($customer_emp_date_array as $customer_emp_date_string) {
            $parts = explode("#", $customer_emp_date_string);
            $dns_emp_code_val = $parts[0];
            $dns_customer_code_val = $parts[1];
            $date_val = $parts[2];
            $key = $customer_emp_date_string;

            $sheet->setCellValue('A' . $rowNum, $count);
            $sheet->setCellValue('B' . $rowNum, $date_val);
            $sheet->setCellValue('C' . $rowNum, $dns_emp_code_val);
            $sheet->setCellValue('D' . $rowNum, ${'emp_name' . $key});
            $sheet->setCellValue('E' . $rowNum, ${'branch_name' . $key});
            $sheet->setCellValue('F' . $rowNum, ${'cust_type' . $key});
            $sheet->setCellValue('G' . $rowNum, $dns_customer_code_val);
            $sheet->setCellValue('H' . $rowNum, ${'customer_name' . $key});
            $sheet->setCellValue('I' . $rowNum, ${'route_name' . $key});
            $sheet->setCellValue('J' . $rowNum, ${'phone_no' . $key});

            $colNum = 11;
            foreach ($competitor_name_array as $competitorval) {
                $comp_key = $key . $competitorval;

                if (isset(${'competitor_name_db' . $comp_key}) &&
                    $competitorval == ${'competitor_name_db' . $comp_key}) {
                    $total_PTD_ex = ${'PTD' . $comp_key} == 0 ? '-' : ${'PTD' . $comp_key};
                    $total_PTD_for = ${'billing_ex_for' . $comp_key} == 0 ? '-' : ${'billing_ex_for' . $comp_key};
                    $total_PTR_ex = ${'PTR' . $comp_key} == 0 ? '-' : ${'PTR' . $comp_key};
                    $total_PTR_for = ${'wsp_ex_for' . $comp_key} == 0 ? '-' : ${'wsp_ex_for' . $comp_key};
                    $total_PTC = ${'PTC' . $comp_key} == 0 ? '-' : ${'PTC' . $comp_key};
                } else {
                    $total_PTD_ex = '-';
                    $total_PTD_for = '-';
                    $total_PTR_ex = '-';
                    $total_PTR_for = '-';
                    $total_PTC = '-';
                }

                $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTD_ex);
                $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTD_for);
                $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTR_ex);
                $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTR_for);
                $sheet->setCellValue(columnNumberToLetter($colNum++) . $rowNum, $total_PTC);
            }

            // Apply styling to data row
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $rowNum . ':' . columnNumberToLetter($colspanheader) . $rowNum)->applyFromArray($styleArray);

            $rowNum++;
            $count++;
        }
    } else {
        $rowNum = 4;
        $sheet->mergeCells('A4:' . columnNumberToLetter($colspanheader) . '4');
        $sheet->setCellValue('A4', 'No Records found');
        $sheet->getStyle('A4:' . columnNumberToLetter($colspanheader) . '4')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    // Auto-size columns
    foreach (range('A', columnNumberToLetter($colspanheader)) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}
$REPORT_TYPE_ROE = "roe";
$REPORT_TYPE_NE = "ne";
$reportForTitle = array("ROE Price", "NE Price");
$reportForEmailTitle = array("ROE", "NE");
$INCOTERM_TYPE_EX = 1;
$INCOTERM_TYPE_FOR = 0;
$STATUS_ACTIVE = 1;
$STATUS_INACTIVE = 0;
$IS_COMPARED_WITH_YES = 1;
$IS_COMPARED_WITH_NO = 0;
$date = new DateTime();
$date->modify('-1 day');
$yesterday = $date->format('Y-m-d');
$yesterday ='2025-12-16';
$formattedDate = dateFormat();
$start_date = $yesterday;
$end_date = $yesterday;
$dateForEmailTitle = new DateTime();
$dateForEmailTitle->modify('-1 day');
$formatForEmailTitle = $dateForEmailTitle->format('j');
$formatedDateForEmailTitle = $formatForEmailTitle . getOrdinalSuffix($formatForEmailTitle) . " " . $dateForEmailTitle->format("F'y");
$competitor_string = array();
$competitorsQuery = "";
$pricingMainWhereCondition = "";
$competitor_string_new = '';
$fixedColSpanHeader = 3;
$fixedPriceDiffColSpanHeader = 3;
$comparisonFieldsColSpanHeader = 5;
$variableColSpanHeader = 0;
$competitorNumber = 2;

$reportsForIndex = -1;
$reportsFor = array("roe", "ne");
$reportForFileTitlePrefixeces = array("ROE_Pricing_Comparison", "NE_Pricing_Comparison");

$colNum = 0;
$rowNum = 0;

// $to_emails = [
//     "roe" => [
//         "emails" => [
//              "gauravdhanani@starcement.co.in"
//         ],
//         "ccs" => [
//             [
//                 "email" => "pintunath@starcement.co.in",
//                 "name" => "Pintu Nath"
//             ],
//              [
//                 "email" => "atanubanerjee@starcement.co.in",
//                 "name" => "Atanu Banerjee"
//             ],
//             [
//                 "email" => "giridharimukherjee@starcement.co.in",
//                 "name" => "Giridhari Mukherjee"
//             ],
//             [
//                 "email" => "nabarunchatterjee@starcement.co.in",
//                 "name" => "Nabarun Chatterjee"
//             ],
//             [
//                 "email" => "pradeep@starcement.co.in",
//                 "name" => "Pradeep Purohit"
//             ],
//             [
//                 "email" => "suvankardash@starcement.co.in",
//                 "name" => "Suvankar Dash"
//             ],
//             [
//                 "email" => "ritwickchatterjee@starcement.co.in",
//                 "name" => "Ritwick Chatterjee"
//             ],
//             [
//                 "email" => "rupeshmishra@starcement.co.in",
//                 "name" => "Rupesh Kumar Mishra"
//             ],
//             [
//                 "email" => "sanskarshukla@starcement.co.in",
//                 "name" => "Sanskar Shukla"
//             ],
//             [
//                 "email" => "priyankamondal@starcement.co.in",
//                 "name" => "Priyanka Mondal"
//             ],
//             [
//                 "email" => "pratipbhunia@starcement.co.in",
//                 "name" => "Pratip"
//             ],
//             [
//                 "email" => "samirdas@starcement.co.in",
//                 "name" => "Samir Das"
//             ],
//             [
//                 "email" => "pankajamaria@starcement.co.in",
//                 "name" => "Pankaj Amaria"
//             ]
//         ]
//     ],
//     "ne" => [
//         "emails" => [
//             "gauravdhanani@starcement.co.in"
//         ],
//         "ccs" => [
//             [
//                 "email" => "ranjanmahanta@starcement.co.in",
//                 "name" => "Ranjan Jyoti Mahanta"
//             ],
//             [
//                 "email" => "atanubanerjee@starcement.co.in",
//                 "name" => "Atanu Banerjee"
//             ],
//             [
//                 "email" => "tarakghosh@starcement.co.in",
//                 "name" => "Tarak Nath Ghosh"
//             ],
//             [
//                 "email" => "brijeshsingh@starcement.co.in",
//                 "name" => "Brijesh Singh"
//             ],
//             [
//                 "email" => "nabarunchatterjee@starcement.co.in",
//                 "name" => "Nabarun Chatterjee"
//             ],
//             [
//                 "email" => "pradeep@starcement.co.in",
//                 "name" => "Pradeep Purohit"
//             ],
//             [
//                 "email" => "saurabhkumar@starcement.co.in",
//                 "name" => "Saurabh Kumar"
//             ],
//             [
//                 "email" => "basudevroy@starcement.co.in",
//                 "name" => "Basudev Roy"
//             ],
//             [
//                 "email" => "vikashkumar@starcement.co.in",
//                 "name" => "Vikash Kumar"
//             ],
//             [
//                 "email" => "ghalibayubi@starcement.co.in",
//                 "name" => "Ghalib Fahad Ayubi"
//             ],
//             [
//                 "email" => "rupeshmishra@starcement.co.in",
//                 "name" => "Rupesh Kumar Mishra"
//             ],
//             [
//                 "email" => "sanskarshukla@starcement.co.in",
//                 "name" => "Sanskar Shukla"
//             ],
//             [
//                 "email" => "priyankamondal@starcement.co.in",
//                 "name" => "Priyanka Mondal"
//             ],
//             [
//                 "email" => "pintunath@starcement.co.in",
//                 "name" => "Pintu Nath"
//             ],
//             [
//                 "email" => "subhasishbhowmik@starcement.co.in",
//                 "name" => "Subhasish Bhowmik"
//             ],
//             [
//                 "email" => "pratipbhunia@starcement.co.in",
//                 "name" => "Pratip"
//             ],
//             [
//                 "email" => "samirdas@starcement.co.in",
//                 "name" => "Samir Das"
//             ],
//             [
//                 "email" => "pankajamaria@starcement.co.in",
//                 "name" => "Pankaj Amaria"
//             ]
//         ]
//     ]
// ];

$to_emails = [
    "roe" => [
        "emails" => [
            "souvik.pal@sbinfowaves.com"
        ],
        "ccs" => [
            [
                "email" => "atanu.sahoo@sbinfowaves.com",
                "name" => "Atanu Sahoo"
            ]
        ]
    ],
    "ne" => [
        "emails" => [
            "souvik.pal@sbinfowaves.com"
        ],
        "ccs" => [
            [
                "email" => "atanu.sahoo@sbinfowaves.com",
                "name" => "Atanu Sahoo"
            ]
        ]
    ]
];

// Create new spreadsheet
// $spreadsheet = new Spreadsheet();

foreach ($reportsFor as $reportFor) {
    $reportsForIndex++;

    $comparedFrom = array();
    $comparedFromCount = 0;
    $comparedTo = array();
    $comparedtoCount = 0;

    $workSheetName = $reportForFileTitlePrefixeces[$reportsForIndex];
    // if($reportsForIndex > 0)
    // {
    //     $spreadsheet->createSheet();
    // }
    // $spreadsheet->setActiveSheetIndex($reportsForIndex)
    // ->setTitle($workSheetName);

    // Create new spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($workSheetName);

    if ($reportFor == $REPORT_TYPE_NE) {
        $competitorsQuery = 'SELECT  ANY_VALUE(NE_P_C.is_compared_with) AS is_compared_with,NE_P_C.company_display_name
                        FROM ne_pricing_competitor AS NE_P_C
                        WHERE NE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY NE_P_C.company_display_name';
        $competitors = mysqli_query($link, $competitorsQuery);
    } elseif ($reportFor == $REPORT_TYPE_ROE) {
        $competitorsQuery = 'SELECT ANY_VALUE(ROE_P_C.is_compared_with) AS is_compared_with,ROE_P_C.company_display_name
                        FROM roe_pricing_competitor AS ROE_P_C
                        WHERE ROE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY ROE_P_C.company_display_name';
        //print_r($competitorsQuery);die;
        $competitors = mysqli_query($link, $competitorsQuery);
    }
    // print_r( $competitorsQuery);die;
    while ($competitor = mysqli_fetch_assoc($competitors)) {
        if ($competitor["is_compared_with"] == $IS_COMPARED_WITH_YES) {
            array_push($comparedFrom, $competitor);
        } else {
            array_push($comparedTo, $competitor);
        }
    }
    $comparedFromCount = count($comparedFrom);
    $comparedToCount = count($comparedTo);
    $variableColSpanHeader = ($comparedFromCount * ($comparisonFieldsColSpanHeader + ($comparedToCount * ($comparisonFieldsColSpanHeader + $fixedPriceDiffColSpanHeader))));

    $sheet->mergeCells('A1:' . columnNumberToLetter($fixedColSpanHeader + $variableColSpanHeader) . '1');
    $sheet->setCellValue('A1', 'Market Feedback Comparisons');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
    ];
    $sheet->getStyle('A1:' . columnNumberToLetter($fixedColSpanHeader + $variableColSpanHeader) . '1')->applyFromArray($styleArray);

    $sheet->mergeCells('A2:' . columnNumberToLetter($fixedColSpanHeader) . '4');
    $sheet->setCellValue('A2', ($reportForTitle[$reportsForIndex] ?? ""));
    $styleArray = [
        'font' => [
            'bold' => true,
            'size' => 14
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
    ];
    $sheet->getStyle('A2:' . columnNumberToLetter($fixedColSpanHeader) . '4')->applyFromArray($styleArray);


    $colNum = $fixedColSpanHeader + 1;

    $sheet->mergeCells(columnNumberToLetter($colNum) . '2:' . columnNumberToLetter($fixedColSpanHeader + $variableColSpanHeader) . '2');
    $sheet->setCellValue(columnNumberToLetter($colNum) . '2', 'Date : ' . ($start_date == $end_date ? DateTime::createFromFormat('Y-m-d', $start_date)->format('d/m/Y') : DateTime::createFromFormat('Y-m-d', $start_date)->format('d/m/Y') . " - " . DateTime::createFromFormat('Y-m-d', $end_date)->format('d/m/Y')));
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFCE4D6']
        ],
    ];
    $sheet->getStyle(columnNumberToLetter($colNum) . '2:' . columnNumberToLetter($fixedColSpanHeader + $variableColSpanHeader) . '2')->applyFromArray($styleArray);

    // $colNum = $fixedColSpanHeader;
    $rowNum = 3;
    for ($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++) {
        $mergeFrom = $colNum;
        $colNum += ($comparisonFieldsColSpanHeader - 1);
        $mergeTo = $colNum;
        $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
        $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), $comparedFrom[$comparedFromIndex]["company_display_name"] . " PRICE");
        $styleArray = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFCE4D6']
            ],
        ];
        $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);
        for ($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++) {
            $mergeFrom = $colNum + 1;
            $colNum += $comparisonFieldsColSpanHeader;
            $mergeTo = $colNum;
            $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
            $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), $comparedTo[$comparedToIndex]["company_display_name"] . " PRICE");
            $styleArray = [
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFCE4D6']
                ],
            ];
            $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);

            $mergeFrom = $colNum + 1;
            $colNum += $fixedPriceDiffColSpanHeader;
            $mergeTo = $colNum;
            $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
            $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), "DIFFERENCE");
            $styleArray = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'ff0000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'ffff00']
                ],
            ];
            $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);
        }
    }

    $colNum = $fixedColSpanHeader + 1;
    $rowNum = 4;
    for ($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++) {
        $mergeFrom = $colNum;
        $colNum += ($comparisonFieldsColSpanHeader - 1);
        $mergeTo = $colNum;
        $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
        $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), "Max instances Price Reading (Rs./Bag)");
        $styleArray = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFCE4D6']
            ],
        ];
        $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);
        for ($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++) {
            $mergeFrom = $colNum + 1;
            $colNum += $comparisonFieldsColSpanHeader;
            $mergeTo = $colNum;
            $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
            $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), "Max instances Price Reading (Rs./Bag)");
            $styleArray = [
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFCE4D6']
                ],
            ];
            $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);

            $mergeFrom = $colNum + 1;
            $colNum += $fixedPriceDiffColSpanHeader;
            $mergeTo = $colNum;
            $sheet->mergeCells(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum);
            $sheet->setCellValue((columnNumberToLetter($mergeFrom) . '' . $rowNum), $comparedFrom[$comparedFromIndex]["company_display_name"] . " VS " . $comparedTo[$comparedToIndex]["company_display_name"] . " (Rs./Bag)");
            $styleArray = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '008000']
                ],
            ];
            $sheet->getStyle(columnNumberToLetter($mergeFrom) . '' . $rowNum . ':' . columnNumberToLetter($mergeTo) . '' . $rowNum)->applyFromArray($styleArray);
        }
    }

    $rowNum = 5;
    $colNum = 1;
    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), "Branch");
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'ffff00']
        ],
    ];
    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

    $colNum = 2;
    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), "Price-Destination");
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'ffff00']
        ],
    ];
    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

    $colNum = 3;
    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), "Zone");
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'ffff00']
        ],
    ];
    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

    $comparisonFieldHeaderValues = array("Product", "Incoterms", "BILLING", " WSP ", " RSP ");
    $comparisonFieldHeaderColors = array("ffff00", "ffff00", "d9e1f2", "d9e1f2", "d9e1f2");
    $comparisonFieldDiffHeaderValues = array("BILLING", " WSP ", " RSP ");
    $comparisonFieldDiffHeaderColors = array("d9e1f2", "d9e1f2", "d9e1f2");
    for ($comparedFromIndex = 0; $comparedFromIndex < $comparedFromCount; $comparedFromIndex++) {
        foreach ($comparisonFieldHeaderValues as $comparisonFieldHeaderValueIndex => $comparisonFieldHeaderValue) {
            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $comparisonFieldHeaderValues[$comparisonFieldHeaderValueIndex]);
            $styleArray = [
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $comparisonFieldHeaderColors[$comparisonFieldHeaderValueIndex]]
                ],
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
        }
        for ($comparedToIndex = 0; $comparedToIndex < $comparedToCount; $comparedToIndex++) {
            foreach ($comparisonFieldHeaderValues as $comparisonFieldHeaderValueIndex => $comparisonFieldHeaderValue) {
                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $comparisonFieldHeaderValues[$comparisonFieldHeaderValueIndex]);
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $comparisonFieldHeaderColors[$comparisonFieldHeaderValueIndex]]
                    ],
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
            }
            foreach ($comparisonFieldDiffHeaderValues as $comparisonFieldDiffHeaderValueIndex => $comparisonFieldDiffHeaderValue) {
                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $comparisonFieldDiffHeaderValues[$comparisonFieldDiffHeaderValueIndex]);
                $styleArray = [
                    'font' => [
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $comparisonFieldDiffHeaderColors[$comparisonFieldDiffHeaderValueIndex]]
                    ],
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
            }
        }
    }


    if ($reportFor == $REPORT_TYPE_NE) {

        $pricingCompetitorsQuery = 'SELECT NE_P_C.pricing_group_number, GROUP_CONCAT(DISTINCT NE_P_C.zone_name SEPARATOR " & ") AS zone_name, GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR " & ") AS route_name, GROUP_CONCAT(DISTINCT NE_P_C.display_branch_name SEPARATOR " & ") AS branch_name
                        FROM ne_pricing_competitor AS NE_P_C INNER JOIN route_master AS RM ON NE_P_C.route_code = RM.route_code INNER JOIN branch_master AS BM ON RM.branch_code = BM.branch_code
                        WHERE NE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY NE_P_C.pricing_group_number';
        $pricingCompetitors = mysqli_query($link, $pricingCompetitorsQuery);
    } elseif ($reportFor == $REPORT_TYPE_ROE) {
        $pricingCompetitorsQuery = 'SELECT ROE_P_C.pricing_group_number, GROUP_CONCAT(DISTINCT ROE_P_C.zone_name SEPARATOR " & ") AS zone_name,GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR " & ") AS branch_name, GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR " & ") AS route_name
                        FROM roe_pricing_competitor AS ROE_P_C INNER JOIN branch_master AS BM ON ROE_P_C.branch_code = BM.branch_code
                        WHERE ROE_P_C.status = ' . $STATUS_ACTIVE . ' 
                        GROUP BY ROE_P_C.pricing_group_number';
        $pricingCompetitors = mysqli_query($link, $pricingCompetitorsQuery);
    }
    while ($pricingCompetitor = mysqli_fetch_assoc($pricingCompetitors)) {
        $rowNum++;
        $colNum = 1;

        $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $pricingCompetitor["branch_name"]);
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ]
        ];
        $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

        $colNum++;
        $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $pricingCompetitor["route_name"]);
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ]
        ];
        $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

        $colNum++;
        $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $pricingCompetitor["zone_name"]);
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ],
            ]
        ];
        $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

        if ($reportFor == $REPORT_TYPE_NE) {
            $companyPricingsQuery =  'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", NE_P_C.route_code, "\'")) AS route_code,ANY_VALUE(NE_P_C.is_compared_with) AS is_compared_with,
    ANY_VALUE(NE_P_C.incoterm_type) AS incoterm_type, GROUP_CONCAT(DISTINCT CONCAT("\'", COMP_M.competitor_name, "\'")) AS competitor_name, GROUP_CONCAT(DISTINCT NE_P_C.product_display_name SEPARATOR " & ") AS product_display_name FROM ne_pricing_competitor AS NE_P_C INNER JOIN competitor_group_master AS COMP_M ON NE_P_C.competitor_id = COMP_M.sl_no WHERE NE_P_C.pricing_group_number = "' . $pricingCompetitor['pricing_group_number'] . '" AND NE_P_C.status = ' . $STATUS_ACTIVE . ' GROUP BY NE_P_C.company_display_name';
            // print_r($companyPricingsQuery );die;
        } elseif ($reportFor == $REPORT_TYPE_ROE) {
            $companyPricingsQuery =  'SELECT GROUP_CONCAT(DISTINCT CONCAT("\'", ROE_P_C.branch_code, "\'")) AS branch_code, ANY_VALUE(ROE_P_C.is_compared_with) AS is_compared_with, ANY_VALUE(ROE_P_C.incoterm_type) AS incoterm_type, GROUP_CONCAT(DISTINCT CONCAT("\'", COMP_M.competitor_name, "\'")) AS competitor_name, GROUP_CONCAT(DISTINCT ROE_P_C.product_display_name SEPARATOR " & ") AS product_display_name FROM roe_pricing_competitor AS ROE_P_C INNER JOIN competitor_group_master AS COMP_M ON ROE_P_C.competitor_id = COMP_M.sl_no WHERE ROE_P_C.pricing_group_number = "' . $pricingCompetitor['pricing_group_number'] . '" AND ROE_P_C.status = ' . $STATUS_ACTIVE . ' GROUP BY ROE_P_C.company_display_name';
            //  print_r($companyPricingsQuery );die;
        }
        $companyPricings = mysqli_query($link, $companyPricingsQuery);

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

        while ($companyPricing = mysqli_fetch_assoc($companyPricings)) {
            $competitor_string = $companyPricing['competitor_name'];
            if ($reportFor == $REPORT_TYPE_NE) {
                $pricingMainWhereCondition = "RM.route_code IN (" . $companyPricing['route_code'] . ")";
            } elseif ($reportFor == $REPORT_TYPE_ROE) {
                $pricingMainWhereCondition = "BM.branch_code IN (" . $companyPricing['branch_code'] . ")";
            }
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
                                    JOIN most_common_values mcv ON TRUE;
                                    ";
            //                           $marketFeedBacksQuery ="SELECT 
            //   RM.route_name,
            //   BM.branch_name,
            //   MF.competitor_name,
            //   SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
            //   DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS feedback_date,
            //   MF.customer_code,
            //   MF.PTD,
            //   MF.PTR,
            //   MF.PTC,
            //   MF.PV,
            //   MF.billing_ex_for,
            //   MF.wsp_ex_for
            // FROM market_feedback MF
            // JOIN customer_master CM ON MF.customer_code = CM.customer_code
            // JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
            // JOIN route_master RM ON CM.route_code = RM.route_code
            // JOIN branch_master BM ON CM.branch_code = BM.branch_code
            // WHERE DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d')
            //       BETWEEN '2025-12-10' AND '2025-12-10'
            //   AND MF.competitor_name = 'STAR CEMENT PPC(ADSTAR)'
            //   AND BM.branch_code IN ('B0001')
            // ORDER BY MF.market_feedback_id ASC
            // LIMIT 0, 25";             
            $marketFeedBacks = mysqli_query($link, $marketFeedBacksQuery);
            if (mysqli_num_rows($marketFeedBacks) > 0) {
                while ($marketFeedBack = mysqli_fetch_assoc($marketFeedBacks)) {

                    if ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR) {

                        if ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES) {
                            array_push($productComparedFrom, $companyPricing["product_display_name"]);
                            array_push($incotermComparedFrom, "FOR");
                            array_push($billingComparedFrom, $marketFeedBack["most_frequent_billing_ex_for"]);
                            array_push($wspComparedFrom, $marketFeedBack["most_frequent_wsp_ex_for"]);
                            array_push($rspComparedFrom, $marketFeedBack["most_frequent_ptc"]);
                        } elseif ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO) {
                            array_push($productComparedTo, $companyPricing["product_display_name"]);
                            array_push($incotermComparedTo, "FOR");
                            array_push($billingComparedTo, $marketFeedBack["most_frequent_billing_ex_for"]);
                            array_push($wspComparedTo, $marketFeedBack["most_frequent_wsp_ex_for"]);
                            array_push($rspComparedTo, $marketFeedBack["most_frequent_ptc"]);
                        }
                    } elseif ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX) {

                        if ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES) {
                            array_push($productComparedFrom, $companyPricing["product_display_name"]);
                            array_push($incotermComparedFrom, "EX");
                            array_push($billingComparedFrom, $marketFeedBack["most_frequent_ptd"]);
                            array_push($wspComparedFrom, $marketFeedBack["most_frequent_ptr"]);
                            array_push($rspComparedFrom, $marketFeedBack["most_frequent_ptc"]);
                        } elseif ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO) {
                            array_push($productComparedTo, $companyPricing["product_display_name"]);
                            array_push($incotermComparedTo, "EX");
                            array_push($billingComparedTo, $marketFeedBack["most_frequent_ptd"]);
                            array_push($wspComparedTo, $marketFeedBack["most_frequent_ptr"]);
                            array_push($rspComparedTo, $marketFeedBack["most_frequent_ptc"]);
                        }
                    }

                    break;
                }
            } else {

                if ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_YES) {
                    array_push($productComparedFrom, $companyPricing["product_display_name"]);
                    array_push($billingComparedFrom, 0);
                    array_push($wspComparedFrom, 0);
                    array_push($rspComparedFrom, 0);

                    if ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR) {
                        array_push($incotermComparedFrom, "FOR");
                    } elseif ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX) {
                        array_push($incotermComparedFrom, "EX");
                    }
                } elseif ($companyPricing["is_compared_with"] == $IS_COMPARED_WITH_NO) {
                    array_push($productComparedTo, $companyPricing["product_display_name"]);
                    array_push($billingComparedTo, 0);
                    array_push($wspComparedTo, 0);
                    array_push($rspComparedTo, 0);

                    if ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_FOR) {
                        array_push($incotermComparedTo, "FOR");
                    } elseif ($companyPricing["incoterm_type"] == $INCOTERM_TYPE_EX) {
                        array_push($incotermComparedTo, "EX");
                    }
                }
            }
        }
        $productComparedFromCount = count($productComparedFrom);
        $productComparedToCount = count($productComparedTo);

        for ($comparedFromIndex = 0; $comparedFromIndex < $productComparedFromCount; $comparedFromIndex++) {
            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $productComparedFrom[$comparedFromIndex]);
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ]
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $incotermComparedFrom[$comparedFromIndex]);
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ]
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($billingComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $billingComparedFrom[$comparedFromIndex]));
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ]
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($wspComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $wspComparedFrom[$comparedFromIndex]));
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ]
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

            $colNum++;
            $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($rspComparedFrom[$comparedFromIndex] == 0 ? ' - ' : $rspComparedFrom[$comparedFromIndex]));
            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ]
            ];
            $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

            for ($comparedToIndex = 0; $comparedToIndex < $productComparedToCount; $comparedToIndex++) {
                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $productComparedTo[$comparedToIndex]);
                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ]
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $incotermComparedTo[$comparedToIndex]);
                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ]
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($billingComparedTo[$comparedToIndex] == 0 ? ' - ' : $billingComparedTo[$comparedToIndex]));
                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ]
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($wspComparedTo[$comparedToIndex] == 0 ? ' - ' : $wspComparedTo[$comparedToIndex]));
                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ]
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

                $colNum++;
                $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), ($rspComparedTo[$comparedToIndex] == 0 ? ' - ' : $rspComparedTo[$comparedToIndex]));
                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ],
                    ]
                ];
                $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);

                $colNum++;
                if ($billingComparedFrom[$comparedFromIndex] == 0 || $billingComparedTo[$comparedToIndex] == 0) {
                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), " - ");
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                } else {
                    $priceDiff = $billingComparedFrom[$comparedFromIndex] - $billingComparedTo[$comparedToIndex];
                    if ($priceDiff > 0) {
                        $color = '68a490';
                        $icon = "▲";
                    } elseif ($priceDiff == 0) {
                        $color = 'a4802b';
                        $icon = "■";
                    } else {
                        $color = 'd65532';
                        $icon = "▼";
                    }


                    $richText = new RichText();


                    $iconText = $richText->createTextRun($icon . " ");
                    $iconText->getFont()->getColor()->setARGB($color);


                    $richText->createText($priceDiff);

                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $richText);
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                }

                $colNum++;
                if ($wspComparedFrom[$comparedFromIndex] == 0 || $wspComparedTo[$comparedToIndex] == 0) {
                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), " - ");
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                } else {
                    $priceDiff = $wspComparedFrom[$comparedFromIndex] - $wspComparedTo[$comparedToIndex];
                    if ($priceDiff > 0) {
                        $color = '68a490';
                        $icon = "▲";
                    } elseif ($priceDiff == 0) {
                        $color = 'a4802b';
                        $icon = "■";
                    } else {
                        $color = 'd65532';
                        $icon = "▼";
                    }

                    // Create RichText object
                    $richText = new RichText();

                    // Add icon with color
                    $iconText = $richText->createTextRun($icon . " ");
                    $iconText->getFont()->getColor()->setARGB($color);

                    // Add numeric part with default color
                    $richText->createText($priceDiff);

                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $richText);
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                }

                $colNum++;
                if ($rspComparedFrom[$comparedFromIndex] == 0 || $rspComparedTo[$comparedToIndex] == 0) {
                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), " - ");
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                } else {
                    $priceDiff = $rspComparedFrom[$comparedFromIndex] - $rspComparedTo[$comparedToIndex];
                    if ($priceDiff > 0) {
                        $color = '68a490';
                        $icon = "▲";
                    } elseif ($priceDiff == 0) {
                        $color = 'a4802b';
                        $icon = "■";
                    } else {
                        $color = 'd65532';
                        $icon = "▼";
                    }

                    // Create RichText object
                    $richText = new RichText();

                    // Add icon with color
                    $iconText = $richText->createTextRun($icon . " ");
                    $iconText->getFont()->getColor()->setARGB($color);

                    // Add numeric part with default color
                    $richText->createText($priceDiff);


                    $sheet->setCellValue((columnNumberToLetter($colNum) . $rowNum), $richText);
                    $styleArray = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN
                            ],
                        ]
                    ];
                    $sheet->getStyle(columnNumberToLetter($colNum) . $rowNum)->applyFromArray($styleArray);
                }
            }
        }
    }


    // $reportName = $reportForFileTitlePrefixeces[$reportsForIndex] . "_" . $formattedDate . ".xlsx";
    // $savePath = __DIR__ . '/price_comparison_reports/' . $reportName;
    //////////////////////////


    //  $rawDataSheet = $spreadsheet->createSheet();
    //     $rawDataSheet->setTitle('Raw Data');

    //     // Set up headers for raw data sheet
    //     $rawHeaders = [
    //         'Route Name',
    //         'Branch Name',
    //         'Competitor Name',
    //         'Employee Code',
    //         'Feedback Date',
    //         'Customer Code',
    //         'PTD',
    //         'PTR',
    //         'PTC',
    //         'PV',
    //         'Billing (Ex/For)',
    //         'WSP (Ex/For)'
    //     ];

    //     // Write headers
    //     $rawColNum = 1;
    //     foreach ($rawHeaders as $header) {
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum) . '1', $header);
    //         $styleArray = [
    //             'font' => [
    //                 'bold' => true,
    //                 'color' => ['argb' => 'FFFFFFFF']
    //             ],
    //             'alignment' => [
    //                 'horizontal' => Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => Alignment::VERTICAL_CENTER
    //             ],
    //             'borders' => [
    //                 'allBorders' => [
    //                     'borderStyle' => Border::BORDER_THIN
    //                 ],
    //             ],
    //             'fill' => [
    //                 'fillType' => Fill::FILL_SOLID,
    //                 'startColor' => ['argb' => 'FF4472C4']
    //             ],
    //         ];
    //         $rawDataSheet->getStyle(columnNumberToLetter($rawColNum) . '1')->applyFromArray($styleArray);
    //         $rawDataSheet->getColumnDimension(columnNumberToLetter($rawColNum))->setAutoSize(true);
    //         $rawColNum++;
    //     }

    //     // Fetch raw data from database
    //     if ($reportFor == $REPORT_TYPE_NE) {
    //         $rawDataQuery = "SELECT 
    //             RM.route_name,
    //             BM.branch_name,
    //             MF.competitor_name,
    //             SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
    //             DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS feedback_date,
    //             MF.customer_code,
    //             MF.PTD,
    //             MF.PTR,
    //             MF.PTC,
    //             MF.PV,
    //             MF.billing_ex_for,
    //             MF.wsp_ex_for
    //         FROM market_feedback MF
    //         JOIN customer_master CM ON MF.customer_code = CM.customer_code
    //         JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
    //         JOIN route_master RM ON CM.route_code = RM.route_code
    //         JOIN branch_master BM ON CM.branch_code = BM.branch_code
    //         WHERE DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
    //             BETWEEN '" . $start_date . "' AND '" . $end_date . "'
    //         ORDER BY RM.route_name, MF.competitor_name, feedback_date";
    //     } elseif ($reportFor == $REPORT_TYPE_ROE) {
    //         $rawDataQuery = "SELECT 
    //             BM.branch_name,
    //             BM.branch_name as route_name,
    //             MF.competitor_name,
    //             SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
    //             DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS feedback_date,
    //             MF.customer_code,
    //             MF.PTD,
    //             MF.PTR,
    //             MF.PTC,
    //             MF.PV,
    //             MF.billing_ex_for,
    //             MF.wsp_ex_for
    //         FROM market_feedback MF
    //         JOIN customer_master CM ON MF.customer_code = CM.customer_code
    //         JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
    //         JOIN branch_master BM ON CM.branch_code = BM.branch_code
    //         WHERE DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
    //             BETWEEN '" . $start_date . "' AND '" . $end_date . "'
    //         ORDER BY BM.branch_name, MF.competitor_name, feedback_date";
    //     }

    //     $rawDataResult = mysqli_query($link, $rawDataQuery);


    //     $rawRowNum = 2;
    //     while ($rawRow = mysqli_fetch_assoc($rawDataResult)) {
    //         $rawColNum = 1;

    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['route_name']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['branch_name']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['competitor_name']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['emp_code']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['feedback_date']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['customer_code']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['PTD']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['PTR']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['PTC']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['PV']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['billing_ex_for']);
    //         $rawDataSheet->setCellValue(columnNumberToLetter($rawColNum++) . $rawRowNum, $rawRow['wsp_ex_for']);


    //         $styleArray = [
    //             'borders' => [
    //                 'allBorders' => [
    //                     'borderStyle' => Border::BORDER_THIN
    //                 ],
    //             ],
    //         ];
    //         $rawDataSheet->getStyle('A' . $rawRowNum . ':' . columnNumberToLetter($rawColNum - 1) . $rawRowNum)->applyFromArray($styleArray);

    //         $rawRowNum++;
    //     }


    //     $rawDataSheet->setAutoFilter('A1:' . columnNumberToLetter(count($rawHeaders)) . '1');


    //     $rawDataSheet->freezePane('A2');


    //     $spreadsheet->setActiveSheetIndex(0);

    //     // ==================== END OF RAW DATA SHEET ====================



    // ... existing code for generating the first sheet ...

    // After all the existing sheet generation code, BEFORE saving the file, add:

    // Generate Market Feedback Details as second sheet


    $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns FROM competitor_group_master WHERE acedns='yes' AND branch_code IS NOT NULL AND branch_code!='' ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,competitor_name ASC";
    // echo $sql_competitor_name;die; 
    $res_competitor_name = mysqli_query($link, $sql_competitor_name);
    $countcompetitor = mysqli_num_rows($res_competitor_name);
    $colspanheader = 10 + ($countcompetitor * 4);

    while ($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)) {
        $competitor_name = $row_competitor_name['competitor_name'];
        $competitor_string_new .= "'" . $competitor_name . "',";
    }
    $competitor_string_new = rtrim($competitor_string_new, ",");
    // echo $competitor_string_new;die;
    generateMarketFeedbackDetailsSheet(
        $spreadsheet,
        $link,
        $start_date,
        $end_date,
        $reportFor,
        $pricingMainWhereCondition,
        $competitor_string_new
    );

    // Set back to first sheet before saving

    $reportName = $reportForFileTitlePrefixeces[$reportsForIndex] . "_" . $formattedDate . ".xlsx";
    $savePath = __DIR__ . '/price_comparison_reports/' . $reportName;



    ///////////////////////////////////

    $writer = new Xlsx($spreadsheet);
    $writer->save($savePath);

    // $spreadsheet->disconnectWorksheets();
    // unset($spreadsheet);
    // unset($writer);
    // gc_collect_cycles(); 


    $spreadsheet->setActiveSheetIndex(0);


    $htmlWriter = IOFactory::createWriter($spreadsheet, 'Html');
    $htmlReportName = $reportForFileTitlePrefixeces[$reportsForIndex] . "_" . $formattedDate . ".html";
    $htmlFilePath = __DIR__ . '/price_comparison_reports/' . $htmlReportName;
    $htmlWriter->save($htmlFilePath);



    $reportImageName = $reportForFileTitlePrefixeces[$reportsForIndex] . "_" . $formattedDate . ".png";
    $imagePath = __DIR__ . '/price_comparison_reports/' . $reportImageName;


    $command = "wkhtmltoimage --width 1200 --quality 90 $htmlFilePath $imagePath";

    exec($command, $output, $resultCode);
    if ($resultCode !== 0) {
        //die($output);
        die("Image generation failed. Command output:\n" . implode("\n", $output));
    }

    $cid = $reportImageName;
    $bodyEmbadedImages = [
        [
            "image_path" => $imagePath,
            "cid" => $cid
        ]
    ];
    $htmlBody = "
            Dear Sir,<br>
            Please find herewith the Sales Team recorded Price Report through SFA Portal for " . $formatedDateForEmailTitle . ".
            <img src='cid:$cid' alt='Report Snapshot' style='max-width:100%;'>
        ";
    $subject = "Sales Team Price Report for " . $formatedDateForEmailTitle . " : " . $reportForEmailTitle[$reportsForIndex];
    $attachmentPaths = [$savePath];

    /////////////////
    $blockedDates = [];
    $result = mysqli_query($link, "SELECT holiday FROM holidays");
    while ($row = mysqli_fetch_assoc($result)) {
        $blockedDates[] = $row['holiday'];
    }
    // print_r($blockedDates);die;
    $skipNextDays = array_map(function ($date) {
        return date('Y-m-d', strtotime($date . ' +1 day'));
    }, $blockedDates);
    $today   = date('Y-m-d');
    $weekday = date('N');

    // if ($weekday != 1 && !in_array($today, $skipNextDays)) {

    send_the_mail(array($to_emails[$reportFor]), $subject, $htmlBody, $attachmentPaths, $bodyEmbadedImages);
    // }
    ///////////////////////////////////////////
    // send_the_mail(array($to_emails[$reportFor]), $subject, $htmlBody, $attachmentPaths, $bodyEmbadedImages);

    if (file_exists($savePath)) {
        unlink($savePath);
    }
    if (file_exists($htmlFilePath)) {
        unlink($htmlFilePath);
    }
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}
mysqli_close($link);

logMessage("Cron Executed Succesfully");

echo "SUCCESS";
die;
