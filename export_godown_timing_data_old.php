  <?php
ini_set('memory_limit', '999M');
set_time_limit(0);
ob_start();
 define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_STAR");
	//define("DB","acedns_ARCHITA");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
ob_end_flush();	
function send_the_mail($to_email,$subject,$bodyml,$attachment_name){
	error_reporting(E_STRICT);
	set_time_limit(0);
	date_default_timezone_set("Asia/Kolkata");
	require_once('class.phpmailer.php');
	require_once('class.smtp.php');
	$sts = "FALSE";
	$to_email_arr = array();
	$to_email = $to_email ? trim($to_email) : "";
	$subject = $subject ? trim($subject) : "";
	$bodyml = $bodyml ? trim($bodyml) : "";
	if($to_email!="" && $subject!="" && $bodyml!=""){
	$to_email_arr = explode(",",$to_email);
	if(count($to_email_arr)>0){
	$mail             = new PHPMailer();
	$bodyml             = $bodyml;
	//$bodyml             = eregi_replace("[\]",'',$bodyml);
	$mail->IsSMTP(); // telling the class to use SMTP
	//$mail->Host       = "mail.starcement.co.in"; // SMTP server (For gmail "mail.coral.in")
	$mail->Host       	= "mail.google.com"; 
	$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "";                 // sets the prefix to the servier
	//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
	//$mail->Host       = "96.45.76.75";       
	$mail->Host       = "ssl://smtp.gmail.com"; 		// sets GMAIL as the SMTP server (For gmail "mail.coral.in")
	//$mail->Port       = 587;                   
	$mail->Port       = 465;					// set the SMTP port for the GMAIL server (For gmail 465 )
	//$mail->Username   = "dev@starsaathi.com";  // GMAIL username
	//$mail->Username   = "starsaathi-starcement";
	$mail->Username   = "starsfa@starcement.co.in";
	//$mail->Password   = "google3d33#";            // GMAIL password
	//$mail->Password   = "BVhf@_745hw";
	$mail->Password   = "Star@2023";
	$mail->addAttachment("$attachment_name");
	//$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
	$mail->SetFrom('starsfa@starcement.co.in', 'Starsfa');
	$mail->Subject    = $subject;
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	$mail->MsgHTML($bodyml);
	foreach($to_email_arr as $to_email_arr_val){
		if(trim($to_email_arr_val)!=""){
		if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
			$mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
		}
		}
	}
	$mlsts = $mail->Send();
	if(!$mlsts) {
	  $sts = "FALSE";
	} else {
	 $sts = "TRUE";
	}
	}
	}
	return $sts;
}
	


function GodowntimeCsvfile(){
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$warehouse_master = "warehouse_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$curr_date_format=date('Y-m-d');
$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));

$cur_month=date('m');
$cur_day=date('d');
//if($cur_month=='02' && $cur_day!='01')
	//$cur_day='01';
if($cur_day!='01')	
{
	$start_date=date('Y-m').'-01';
}
else
{	
	//$start_date=date('Y-m').'-01';
	$start_date=date('Y-m-d', strtotime(date('Y-m')." -1 month"));
}
	//exit();
$end_date=$dateprevious;
	$datediff = strtotime($end_date) - strtotime($start_date);
	$number_days = round($datediff / (60 * 60 * 24));
	$excel_header_date = date('Y-m-d',strtotime("-1 days, $start_date"));
	//$excel_header_date= date("Y-m-d", $excel_header_date);
	
	$excelheader="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	$excelheader.="". "\t";
	for($count_number_days=0;$count_number_days<=$number_days;$count_number_days++)
	{
		//$excelheader.=date('d-m-Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		$excel_header_date = strtotime("+1 days, $excel_header_date");
		$excel_header_date=date("Y-m-d", $excel_header_date);
		$excelheader.= $excel_header_date. "\t".$excel_header_date. "\t";
	}
	$excelheader.="". "\t\n";
	
	//exit();


$the_file_name = "godown_time_report_".$curr_date.".xls";
$output = "";
$qry = "select * from $warehouse_master WHERE is_active='Y' ORDER BY warehouse asc ";
$sql = mysqli_query($link,$qry);



$output .= "sl no"."\t"."zone"."\t"."incharge"."\t"."coordinator"."\t"."warehouse"."\t"."diversion"."\t"."Format"."\t"."standard open time"."\t"."standard close time"."\t";

for($count_number_days=0;$count_number_days<=$number_days;$count_number_days++)
	{
		//$excelheader.=date('d-m-Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		$output .="open time"."\t"."close time"."\t";
	}

$output .="remarks"."\t"."\n";
// Get Records from the table
$count=1;
while ($row1 = mysqli_fetch_assoc($sql)) {
$warehouse_code = $row1["warehouse_code"];
$emp_code = $row1["emp_code"];
	
$warehouse = $row1["warehouse"];
$diversion = $row1["diversion"];
$coordinator = $row1["coordinator"];
$incharge = $row1["incharge"];
$zone = $row1["zone"];
$godown_open_time = $row1["godown_open_time"];
$godown_close_time = $row1["godown_close_time"];
$hours_format = $row1["hours_format"];
$remarks = '';


$output .= $count."\t".$zone."\t".$incharge."\t".$coordinator."\t".$warehouse."\t".$diversion."\t".$hours_format."\t".$godown_open_time."\t".$godown_close_time."\t";
		$excel_header_value_date =  date('Y-m-d',strtotime("-1 days, $start_date"));

for($count_number_days=0;$count_number_days<=$number_days;$count_number_days++)
	{
		$excel_header_date = strtotime("+1 days, $excel_header_date");
		$excel_header_date=date("Y-m-d", $excel_header_date);
	$excel_header_value_date = strtotime("+1 days, $excel_header_value_date");
		$excel_header_value_date=date("Y-m-d", $excel_header_value_date);

		
		$sqllatestopentime="SELECT CheckIN as open_time FROM t_att_checkout_info 
								where 	Entry_Date='$excel_header_value_date' AND Emp_Id IN(".$emp_code.")  ORDER BY CheckIN ASC LIMIT 0,1";
		$rslatestopentime = mysqli_query($link,$sqllatestopentime);
		$rowlatestopentime=mysqli_fetch_assoc($rslatestopentime);
		$latest_open_time=$rowlatestopentime['open_time'];
		
		$sqllatestclosetime="SELECT CheckOUT as close_time FROM t_att_checkout_info where Entry_Date='$excel_header_value_date' AND Emp_Id IN(".$emp_code.") ORDER BY CheckOUT DESC LIMIT 0,1";
		$rslatestclosetime = mysqli_query($link,$sqllatestclosetime);
		$rowlatestclosetime=mysqli_fetch_assoc($rslatestclosetime);
		$latest_close_time=$rowlatestclosetime['close_time'];

		//$excelheader.=date('d-m-Y',strtotime($count_number_days.'-'.$month.'-'.$year)). "\t";
		$output.= $latest_open_time. "\t".$latest_close_time. "\t";
	}
$output .=""."\t"."\n";
$count++;
}

// Download the file

$filename = $the_file_name;
	//$filename='godown_time_report_13th_Feb_2023_11_02_41_AM.xls';
$fp = fopen("/home/acedns/public_html/warehouse/$filename","wb");
fwrite($fp,$excelheader.$output);
fclose($fp);
	//require_once 'misreport/phpexcel/Classes/PHPExcel.php';
	//include 'misreport/phpexcel/Classes/PHPExcel/Writer/Excel5.php';
		//include 'misreport/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
	set_include_path('misreport/phpexcel/Classes/');
	require_once 'PHPExcel.php';
	include 'PHPExcel/IOFactory.php';

//$objPHPExcel = new PHPExcel();
 /*$i=0; while ($i < 1) {
	if($i==0)
	{
		$date=date('d/m/y');
		$xlsReader= new PHPExcel_Reader_Excel2007();
		$xlsTemplate = $xlsReader->load("warehouse/$filename");
		$sheet1 = $xlsTemplate->getSheet(0);
		$objPHPExcel->addExternalSheet( $sheet1, 1 );
		$sheet1->setTitle('Godown Timing Report');
		
		$borderArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);
		$boldArray = array('font' => array('bold' => true,));
		for($cellcount=2;$cellcount<=40;$cellcount++)
		{
		$sheet1->getStyle('A'.$cellcount.':V'.$cellcount)->applyFromArray($borderArray);
		$sheet1->getStyle('A'.$cellcount.':V'.$cellcount)->applyFromArray($boldArray);
		}
	}
	$i++;
 }*/
	$inputFileName = "warehouse/$filename";
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	//$fileType = 'Excel5';
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);

//  Get worksheet dimensions
//$sheet1 = $objPHPExcel->getSheet(0); 
//$highestRow = $sheet1->getHighestRow(); 
//$highestColumn = $sheet1->getHighestColumn();
	/*$objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('A1', 'Hello')
   ->setCellValue('B1', 'World!');*/
	$borderArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);
	$boldArray = array('font' => array('bold' => true,));
	//$objPHPExcel->getSheet(0)->getStyle('A1:V1')->applyFromArray($boldArray);

	/*$borderArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);
		$boldArray = array('font' => array('bold' => true,));
		for($cellcount=2;$cellcount<=40;$cellcount++)
		{
		$sheet1->getStyle('A'.$cellcount.':V'.$cellcount)->applyFromArray($borderArray);
		$sheet1->getStyle('A'.$cellcount.':V'.$cellcount)->applyFromArray($boldArray);
		}*/
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($inputFileName);
//$objPHPExcel->removeSheetByIndex(0);
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
//header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// It will be called file.xls
//header('Content-Disposition: attachment; filename="Book12.xlsx"');
// Write file to the browser
//$objWriter->save('php://output');
//$objWriter->save(str_replace(__FILE__,'warehouse/Book12.xlsx',__FILE__));

$to_email = "dipankarc@coral.in,abhishekd@coral.in,pratipbhunia@starcement.co.in,manishranjan@starcement.co.in";
$subject = "STARSFA Godown Timing Report";
$bodyml = "STARSFA Godown Timing Report";
$attachment_name="warehouse/".$filename;
//$mres = send_the_mail($to_email,$subject,$bodyml,$attachment_name);

/*header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;*/
}
GodowntimeCsvfile();
mysqli_close($conn);
?>