<?php
$nick_name='STAR';
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_STAR");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
		$dir = dirname(__FILE__);
		
	function pdf_create($html, $filename, $messageid, $phoneno, $emp_name, $attach_file_name) 
	{
		$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
		$prevdate=date('d-m-Y', strtotime("-1 days,$curdate "));
		$curtime=gmdate('H:i:s',strtotime('+330 minute'));
		$prevdate_formated=date('Y-m-d', strtotime("-1 days,$curdate "));
		$prevdatetimeserver=$prevdate_formated.' '.$curtime;
		/*echo '{"messages":[{"from":"917595080005","to":"919474335413","messageId":"'.$messageid.'","content":{"templateName":"sfa_activity_report","templateData":{"body":{"placeholders":["'.$emp_name.'","'.$curdate.'"]},"header": {"type": "DOCUMENT","mediaUrl": "http://salesmpower.acedns.in/starpdf/'.$filename.'","filename": "'.$filename.'"}},"language":"en"},"callbackData":"Callback data"}]}';
		exit();*/
		require_once('tcpdf/tcpdf.php');
		//$savein = '../upload/ASL/';
		//define ('PDF_MARGIN_RIGHT', 4);
		$savein = 'starpdf/';
		$pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetFont('helvetica', '', 9);
		$pdf->AddPage('P',"A4");
		$pdf->writeHTML($html);
		//Use 'D' for download
		$tcpdf=$pdf->Output(__DIR__ .'/starpdf/'.$filename, 'F');
		//For Whats app
		if($tcpdf=='')
		{
			$encodedval=base64_encode('star_pushnotification:forcePower2021@#');
			$finalval='Basic '.$encodedval;
			//$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
			//$curdatetime=gmdate('d-m-Y',strtotime('+330 minute'));
			//$phoneno='9474335413';
			$phoneno='91'.$phoneno;
			
			$curl = curl_init();
			//7044497204
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://zwyv2.api.infobip.com/whatsapp/1/message/template',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{"messages":[{"from":"917595080005","to":"'.$phoneno.'","messageId":"'.$messageid.'","content":{"templateName":"sfa_activity_report","templateData":{"body":{"placeholders":["'.$emp_name.'","'.$prevdate.'"]},"header": {"type": "DOCUMENT","mediaUrl": "http://salesmpower.acedns.in/starpdf/'.$filename.'","filename": "'.$attach_file_name.'"}},"language":"en"},"callbackData":"Callback data"}]}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: $finalval",
					'Content-Type: application/json',
					'Accept: application/json'
				),
			));
			
			$response = curl_exec($curl);
			curl_close($curl);
			echo $response;
			//For log
			$pdf_URL='http://salesmpower.acedns.in/starpdf/'.$filename;
			$sqlinsert="INSERT INTO whats_app_log SET 	receive_emp_code='".substr($filename,0,5)."',
							receiver_phone='".$phoneno."',
							receive_date='".$prevdatetimeserver."',
							PDF_URL='".$pdf_URL."'";
			mysqli_query($link,$sqlinsert);				
		}
		//file_put_contents($savein.str_replace("/","-",$filename), $tcpdf);    // save the pdf file on server
	}
		//previous date calc
	$curdateserver=gmdate('d-m-Y',strtotime('+330 minute'));
	$curdatetimeserver=gmdate('dmYHis',strtotime('+330 minute'));
	$curtimeserver=gmdate('His',strtotime('+330 minute'));
	$curdate=gmdate('Y-m-d',strtotime('+330 minute'));
	$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	$previous_day = date("d-m-Y",strtotime($previous_date));
	$prevdatetimeserver=date("dmY",strtotime($previous_date)).$curtimeserver;
		
	//Unlink previos files
	$folder_path = "/home/acedns/public_html/starpdf"; 
	// List of name of files inside 
	// specified folder 
	$files = glob($folder_path.'/*');  
	   
	// Deleting all the files in the list 
	$dataprocessed=0; 
	foreach($files as $file) { 
	   // echo $file.'<br />';
		//$file_parts=explode("/",$file);
		$file_parts=explode("/",$file);
		//unlink($folder_path.'/'.$file_parts[5]);
	}
	//For DB query
	//$sqlselemphighlevel="SELECT emp_code,emp_name,phone_no FROM whats_app_phone_list WHERE emp_code='E1211' ORDER BY emp_name ASC";
	$sqlselemphighlevel="SELECT emp_code,emp_name,phone_no FROM whats_app_phone_list  ORDER BY emp_name ASC";
	$rsselemphighlevel=mysqli_query($link,$sqlselemphighlevel);
	while($rowselemphighlevel=mysqli_fetch_assoc($rsselemphighlevel)){
	$emp_code=$rowselemphighlevel['emp_code'];
	$emp_name=$rowselemphighlevel['emp_name'];
	$phone_no=$rowselemphighlevel['phone_no'];
	
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_val_rds=' AND (LO.emp_code IN('.$employee_hierarchy.'))';
		$pdf_html="<html><body>
				<table width=\"100%\" >
				  <tr>
					<td align=\"right\" width=\"65%\"><b>SFA MANAGER ACTIVITY REPORT</b><br /><b>(DATE: $previous_day)</b></td><td align=\"right\" width=\"35%\"><img src=\"http://salesmpower.acedns.in/logo/STAR.png\"/ height=\"50\" width=\"90\"></td>
				  </tr>
				  </table>
				  <table width=\"100%\" >
				  <tr>
					<td ><b>MANAGER CODE: ".$emp_code."</b><br /><b>MANAGER NAME: ".$emp_name."</b></td>
				  </tr>
				 </table>
				 <br />
				 <table border=\"1\" width=\"100%\" cellpadding=\"2\" cellspacing=\"2\"> 
					  <tr>
						<td  width=\"7%\"><b>SL.NO</b></td>
						<td width=\"32%\"><b>EMPLOYEE NAME</b></td>
						<td width=\"22%\"><b>DESIGNATION</b></td>
						<td width=\"19%\"><b>HQ</b></td>
						<td width=\"10%\"><b>TODAY CHECK-IN TIME</b></td>
						<td  width=\"10%\"><b>TODAY COUNTER VISITS</b></td>
					  </tr>";
		$sqllocation = "SELECT EM.emp_code,EM.emp_name,EM.designation,EM.HQ,DATE_FORMAT(LO.date,'%T') as att_time FROM employee_master EM INNER  JOIN location LO 
		ON LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')='".$previous_date."' 
		AND EM.sale_access='primary' ".$emp_val_rds." ORDER BY EM.emp_name ASC";
		$resultlocation = mysqli_query($link,$sqllocation);
		$count=mysqli_num_rows($resultlocation);
		if($count>0){
		$cntac=1;
		while($rowlocation = mysqli_fetch_assoc($resultlocation))
		{
		   $emp_code_lower=$rowlocation['emp_code'];
		   $emp_name_lower=$rowlocation['emp_name'];
		   $emp_designation_lower=$rowlocation['designation'];
		   $emp_HQ_lower=$rowlocation['HQ'];
		   ${att_time.$emp_code_lower}=$rowlocation['att_time'];

		   	$sqlcustomervisit="SELECT COUNT(DISTINCT customer_code) AS tot_customer_visit FROM customer_visit_details WHERE emp_code='".$emp_code_lower."' 
							 AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')='".$previous_date."'";
		   $rscustomervisit=mysqli_query($link,$sqlcustomervisit);
		   $rowcustomervisit=mysqli_fetch_assoc($rscustomervisit);
		   $total_customer_visit=$rowcustomervisit['tot_customer_visit'];
			
			$check_in_time = ((${att_time.$emp_code_lower}!='')?${att_time.$emp_code_lower}: 'ABSENT');
			 $pdf_html.="<tr>
				<td >$cntac</td>
				<td >$emp_name_lower</td>
				<td >$emp_designation_lower</td>
				<td >$emp_HQ_lower</td>
				<td align=\"right\" >$check_in_time</td>
				<td align=\"center\"> $total_customer_visit</td>
			  </tr>";
					  
			$cntac++; 
		  }
			$pdf_html.="</table></body></html>";
			 //echo $pdf_html;
			$filename=$emp_code.'_activity_'.$prevdatetimeserver.'.pdf';
			$attach_file_name=$emp_name.'_'.'market visit'.'_'.$previous_day;
			$messageid=$emp_code.'-'.$prevdatetimeserver;
			pdf_create($pdf_html, $filename, $messageid, $phone_no, $emp_name, $attach_file_name); 
		}
	}
?>