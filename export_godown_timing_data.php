  <?php
  set_time_limit(1000);
ini_set('memory_limit', '-1');
//set_time_limit(0);
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
	$mail->SMTPDebug  = "1";                     // enables SMTP debug information (for testing)
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
	//$mail->Password   = "star@2023";

	$mail->Password   = "Krishna@6549";

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
	echo $mlsts = $mail->Send();
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
//$cur_day='02';
if($cur_day!='01')	
{
	$start_date=date('Y-m').'-01';
}
else
{	
	//$start_date=date('Y-m').'-01';
	$start_date=date('Y-m-d', strtotime(date('Y-m')." -1 month"));
}
//echo $start_date;
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
$filename='warehouse/godown_time_report_9th_Oct_2023_06_10_22_PM.xlsx';
/*$fp = fopen("/home/acedns/public_html/warehouse/$filename","wb");
fwrite($fp,$excelheader.$output);
fclose($fp);
	exit();*/
	require_once 'misreport/phpexcel/Classes/PHPExcel.php';
	include 'misreport/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();
 $i=0; while ($i < 1) {
	if($i==0)
	{
		$date=date('d/m/y');
		$xlsReader= new PHPExcel_Reader_Excel2007();
		$xlsTemplate = $xlsReader->load("$filename");
		$sheet1 = $xlsTemplate->getSheet(0);
		$objPHPExcel->addExternalSheet( $sheet1, 1 );
		$sheet1->setTitle('Godown Timing Report');
		$highestRow = $sheet1->getHighestRow(); 
		$highestColumn = $sheet1->getHighestColumn();
		
		//exit();
		
		$borderArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);
		$boldArray = array('font' => array('bold' => true,));
		$centerarray = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );
		for($cellcount=1;$cellcount<=$highestRow;$cellcount++)
		{
		$sheet1->getStyle('A'.$cellcount.":$highestColumn".$cellcount)->applyFromArray($borderArray);
		}
		$sheet1->getStyle('A1'.":$highestColumn".'1')->applyFromArray($boldArray);
		$sheet1->getStyle('A2'.":$highestColumn".'2')->applyFromArray($boldArray);
		
		$alphabet=array();
		for ($alphacnt = 'J'; $alphacnt !=$highestColumn; $alphacnt++) {
			//echo $alphacnt;
			array_push($alphabet,$alphacnt);
		}
		//print_r($alphabet);
		$start_letter = 0;
		$rowno = 1;
		$counttotal=$number_days;
		//exit();
		$merge=0;
		$sheet1->mergeCells('J1:K1')->getStyle('J1:K1')->applyFromArray($centerarray);
		$sheet1->mergeCells('L1:M1')->getStyle('L1:M1')->applyFromArray($centerarray);
		$sheet1->mergeCells('N1:O1')->getStyle('N1:O1')->applyFromArray($centerarray);
		$sheet1->mergeCells('P1:Q1')->getStyle('P1:Q1')->applyFromArray($centerarray);
		$sheet1->mergeCells('R1:S1')->getStyle('R1:S1')->applyFromArray($centerarray);
		$sheet1->mergeCells('T1:U1')->getStyle('T1:U1')->applyFromArray($centerarray);
		$sheet1->mergeCells('V1:W1')->getStyle('V1:W1')->applyFromArray($centerarray);
		$sheet1->mergeCells('X1:Y1')->getStyle('X1:Y1')->applyFromArray($centerarray);
		$sheet1->mergeCells('Z1:AA1')->getStyle('Z1:AA1')->applyFromArray($centerarray);
		/*$sheet1->mergeCells('AB1:AC1')->getStyle('AB1:AC1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AD1:AE1')->getStyle('AD1:AE1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AF1:AG1')->getStyle('AF1:AG1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AH1:AI1')->getStyle('AH1:AI1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AJ1:AK1')->getStyle('AJ1:AK1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AL1:AM1')->getStyle('AL1:AM1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AN1:AO1')->getStyle('AN1:AO1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AP1:AQ1')->getStyle('AP1:AQ1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AR1:AS1')->getStyle('AR1:AS1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AT1:AU1')->getStyle('AT1:AU1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AV1:AW1')->getStyle('AV1:AW1')->applyFromArray($centerarray);
		/*$sheet1->mergeCells('AX1:AY1')->getStyle('AX1:AY1')->applyFromArray($centerarray);
		$sheet1->mergeCells('AZ1:BA1')->getStyle('AZ1:BA1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BB1:BC1')->getStyle('BB1:BC1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BD1:BE1')->getStyle('BD1:BE1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BF1:BG1')->getStyle('BF1:BG1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BH1:BI1')->getStyle('BH1:BI1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BJ1:BK1')->getStyle('BJ1:BK1')->applyFromArray($centerarray);
		$sheet1->mergeCells('BL1:BM1')->getStyle('BL1:BM1')->applyFromArray($centerarray);*/
		
		$sheet1->getStyle('H3:H'.$highestRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$sheet1->getStyle('I3:I'.$highestRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		/*for ($mergecount = 0; $mergecount <= $counttotal; $mergecount++) {
			//echo $mergecount;
			$sheet1->mergeCells('J1:K1');
			if($mergecount == 0)
			{
				$mergestring="'".$alphabet[$mergecount].$rowno.':'.$alphabet[$mergecount+1].$rowno."'";
			//$sheet1->mergeCells($alphabet[$mergecount].$rowno.':'.$alphabet[$mergecount+1].$rowno);
				$sheet1->mergeCells($mergestring);
			}
			else
			{
				$numberval=$merge+1;
				$mergestring="'".$alphabet[$merge].$rowno.':'.$alphabet[$numberval].$rowno."'";
				$sheet1->mergeCells($mergestring);
			}
			//echo '<br />';
			$merge+=2;
		}*/

	}
	$i++;
 }
$objPHPExcel->removeSheetByIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
//header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// It will be called file.xls
//header('Content-Disposition: attachment; filename="Book12.xlsx"');
// Write file to the browser
//$objWriter->save('php://output');
$objWriter->save($filename);
	

$to_email = "rohitsingh@starcement.co.in,kashisharma@starcement.co.in,hirokbhuyan@starcement.co.in,manishprakash@starcement.co.in, basantsenapati@starcement.co.in,sunitadeuri@starcement.co.in,vivekkiran@starcement.co.in,logistics.guwahati@starcement.co.in,aakashpujari@starcement.co.in,manishranjan@starcement.co.in,pratipbhunia@starcement.co.in,anupshaw@starcement.co.in,dipankarc@coral.in";
//$to_email = "dipankarc@coral.in";
$subject = "STARSFA Godown Timing Report";
$bodyml = "STARSFA Godown Timing Report";
//$attachment_name="warehouse/".$filename;
$attachment_name=$filename;
$mres = send_the_mail($to_email,$subject,$bodyml,$attachment_name);

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