<?php
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

$to_emails = [
    "roe" => [
        "emails" => [
             "pankajamaria@starcement.co.in"
        ],
        "ccs" => [
            [
                "email" => "pintunath@starcement.co.in",
                "name" => "Pintu Nath"
            ],
             [
                "email" => "atanubanerjee@starcement.co.in",
                "name" => "Atanu Banerjee"
            ],
            [
                "email" => "giridharimukherjee@starcement.co.in",
                "name" => "Giridhari Mukherjee"
            ],
            [
                "email" => "nabarunchatterjee@starcement.co.in",
                "name" => "Nabarun Chatterjee"
            ],
            [
                "email" => "pradeep@starcement.co.in",
                "name" => "Pradeep Purohit"
            ],
            [
                "email" => "suvankardash@starcement.co.in",
                "name" => "Suvankar Dash"
            ],
            [
                "email" => "ritwickchatterjee@starcement.co.in",
                "name" => "Ritwick Chatterjee"
            ],
            [
                "email" => "rupeshmishra@starcement.co.in",
                "name" => "Rupesh Kumar Mishra"
            ],
            [
                "email" => "sanskarshukla@starcement.co.in",
                "name" => "Sanskar Shukla"
            ],
            [
                "email" => "priyankamondal@starcement.co.in",
                "name" => "Priyanka Mondal"
            ],
            [
                "email" => "pratipbhunia@starcement.co.in",
                "name" => "Pratip"
            ],
            [
                "email" => "samirdas@starcement.co.in",
                "name" => "Samir Das"
            ],
            [
                "email" => "pankajamaria@starcement.co.in",
                "name" => "Pankaj Amaria"
            ]
        ]
    ],
    "ne" => [
        "emails" => [
            "gauravdhanani@starcement.co.in"
        ],
        "ccs" => [
            [
                "email" => "ranjanmahanta@starcement.co.in",
                "name" => "Ranjan Jyoti Mahanta"
            ],
            [
                "email" => "atanubanerjee@starcement.co.in",
                "name" => "Atanu Banerjee"
            ],
            [
                "email" => "tarakghosh@starcement.co.in",
                "name" => "Tarak Nath Ghosh"
            ],
            [
                "email" => "brijeshsingh@starcement.co.in",
                "name" => "Brijesh Singh"
            ],
            [
                "email" => "nabarunchatterjee@starcement.co.in",
                "name" => "Nabarun Chatterjee"
            ],
            [
                "email" => "pradeep@starcement.co.in",
                "name" => "Pradeep Purohit"
            ],
            [
                "email" => "saurabhkumar@starcement.co.in",
                "name" => "Saurabh Kumar"
            ],
            [
                "email" => "basudevroy@starcement.co.in",
                "name" => "Basudev Roy"
            ],
            [
                "email" => "vikashkumar@starcement.co.in",
                "name" => "Vikash Kumar"
            ],
            [
                "email" => "ghalibayubi@starcement.co.in",
                "name" => "Ghalib Fahad Ayubi"
            ],
            [
                "email" => "rupeshmishra@starcement.co.in",
                "name" => "Rupesh Kumar Mishra"
            ],
            [
                "email" => "sanskarshukla@starcement.co.in",
                "name" => "Sanskar Shukla"
            ],
            [
                "email" => "priyankamondal@starcement.co.in",
                "name" => "Priyanka Mondal"
            ],
            [
                "email" => "pintunath@starcement.co.in",
                "name" => "Pintu Nath"
            ],
            [
                "email" => "subhasishbhowmik@starcement.co.in",
                "name" => "Subhasish Bhowmik"
            ],
            [
                "email" => "pratipbhunia@starcement.co.in",
                "name" => "Pratip"
            ],
            [
                "email" => "samirdas@starcement.co.in",
                "name" => "Samir Das"
            ],
            [
                "email" => "pankajamaria@starcement.co.in",
                "name" => "Pankaj Amaria"
            ]
        ]
    ]
];

// $to_emails = [
//     "roe" => [
//         "emails" => [
//             "souvik.pal@sbinfowaves.in"
//         ],
//         "ccs" => [
//             [
//                 "email" => "atanu.sahoo@sbinfowaves.com",
//                 "name" => "Atanu Sahoo"
//             ]
//         ]
//     ],
//     "ne" => [
//         "emails" => [
//             "souvik.pal@sbinfowaves.in"
//         ],
//         "ccs" => [
//             [
//                 "email" => "atanu.sahoo@sbinfowaves.com",
//                 "name" => "Atanu Sahoo"
//             ]
//         ]
//     ]
// ];

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

   
    $reportName = $reportForFileTitlePrefixeces[$reportsForIndex] . "_" . $formattedDate . ".xlsx";
    $savePath = __DIR__ . '/price_comparison_reports/' . $reportName;

    $writer = new Xlsx($spreadsheet);
    $writer->save($savePath);

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

    if ($weekday != 1 && !in_array($today, $skipNextDays)) {

        send_the_mail(array($to_emails[$reportFor]), $subject, $htmlBody, $attachmentPaths, $bodyEmbadedImages);
    }
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
