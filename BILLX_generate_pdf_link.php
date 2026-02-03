<?php
header("Access-Control-Allow-Origin: *");
$param=$_REQUEST['param'];
$dir = dirname(__FILE__);
	//{"customer_data":[{"customerName":"Dutta Drug","date":"01-10-2022","invoiceNumber":"0710586234","netSales":"1781.00","district":"Naihati","transporter":"N.T.C","totalBoxes":"1","boxNumber":"03"},{"customerName":"Bose Drug","date":"06-10-2022","invoiceNumber":"0710586244","netSales":"1791.00","district":"Kolkata","transporter":"N.P.C","totalBoxes":"2","boxNumber":"07"}]}	
$inputJSON = file_get_contents('php://input');
//$inputJSON='{"customer_data":[{"customerName":"Dutta Drug","date":"01-10-2022","invoiceNumber":"0710586234","netSales":"1781.00","district":"Naihati","transporter":"N.T.C","totalBoxes":"1","boxNumber":"03"},{"customerName":"Bose Drug","date":"06-10-2022","invoiceNumber":"0710586244","netSales":"1791.00","district":"Kolkata","transporter":"N.P.C","totalBoxes":"2","boxNumber":"07"}]}';
$input= json_decode($inputJSON, true);
//$customer_data=$input['customer_data'];

//echo $input['customer_data']['customerName'];
/*foreach ($input['customer_data'] as $inputval)
{
	echo $customerName=$inputval['customerName'];
}*/
	function pdf_create($html, $filename) 
	{
		$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
		$prevdate=date('d-m-Y', strtotime("-1 days,$curdate "));
		$curtime=gmdate('H:i:s',strtotime('+330 minute'));
		$prevdate_formated=date('Y-m-d', strtotime("-1 days,$curdate "));
		$prevdatetimeserver=$prevdate_formated;
		/*echo '{"messages":[{"from":"917595080005","to":"919474335413","messageId":"'.$messageid.'","content":{"templateName":"sfa_activity_report","templateData":{"body":{"placeholders":["'.$emp_name.'","'.$curdate.'"]},"header": {"type": "DOCUMENT","mediaUrl": "http://salesmpower.acedns.in/starpdf/'.$filename.'","filename": "'.$filename.'"}},"language":"en"},"callbackData":"Callback data"}]}';
		exit();*/
		require_once('tcpdf/tcpdf.php');
		//$savein = '../upload/ASL/';
		//define ('PDF_MARGIN_RIGHT', 4);
		//$savein = 'pdf/';
		$pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetFont('helvetica', '', 10);
		$pdf->AddPage('L',"A7");
		$pdf->writeHTML($html);
		//Use 'D' for download
		$tcpdf=$pdf->Output(__DIR__ .'/billxpdf/'.$filename, 'D');
		//For Whats app
		//file_put_contents($savein.str_replace("/","-",$filename), $tcpdf);    // save the pdf file on server
	}
	$curdateserver=gmdate('Ymd',strtotime('+330 minute'));
	$curdateserverformated=gmdate('d-m-Y',strtotime('+330 minute'));
	$curdatetimeserver=gmdate('dmYHis',strtotime('+330 minute'));
	$curtimeserver=gmdate('His',strtotime('+330 minute'));
	$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
	$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	$previous_day = date("d-m-Y",strtotime($previous_date));
	$pdf_html="<html><body>";
	foreach($input['customer_data'] as $customer_data_val){
			$customerName = $customer_data_val["customerName"] ? trim($customer_data_val["customerName"]) : "";
			$date = $customer_data_val["date"] ? trim($customer_data_val["date"]) : "";
			$invoiceNumber = $customer_data_val["invoiceNumber"] ? trim($customer_data_val["invoiceNumber"]) : "";
			$netSales = $customer_data_val["netSales"] ? trim($customer_data_val["netSales"]) : "";
			$district = $customer_data_val["district"] ? trim($customer_data_val["district"]) : "";
			$transporter = $customer_data_val["transporter"] ? trim($customer_data_val["transporter"]) : "";
			$totalBoxes = $customer_data_val["totalBoxes"] ? trim($customer_data_val["totalBoxes"]) : "";
			$boxNumber = $customer_data_val["boxNumber"] ? trim($customer_data_val["boxNumber"]) : "";

	$pdf_html.="<table width=\"100%\" >
				  <tr>
					<td ><b>Customer:</b></td> <td align='right'><img src=\"http://salesmpower.acedns.in/logo/BILLX.png\" ></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>".$customerName."</b></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>Date: ".$date."</b></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>Invoice No: ".$invoiceNumber."</b></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>Net Sales: ".$netSales."</b></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>District: ".$district."</b></td>
				  </tr>
				  <tr>
					<td colspan='2'><b>Transporter: ".$transporter."</b></td>
				  </tr>
				   <tr>
					<td colspan='2'><b>Boxes: $totalBoxes /$boxNumber</b></td>
				  </tr>
				  <tr>
					<td colspan='2' style=\"height: 20px\"></td>
				  </tr></table>";
	}
	
	$pdf_html.="</body></html>";
		 //echo $pdf_html;
	$filename='BILLEX'.'_'.$curdateserverformated.'.pdf';
	//$attach_file_name=$emp_name.'_'.'less than 5 visits'.'_'.$previous_day;
	//pdf_create($pdf_html, $filename); 
	echo "https://backup-ace.s3.ap-south-1.amazonaws.com/root/billexpdf_31_10_22_16_59_04.pdf";
?>