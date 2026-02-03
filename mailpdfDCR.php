<HTML>
<BODY>
<center>
<?php
		// Get the contents of the pdf into a variable for later
		ob_start();
		require_once("pdfDCRhtmldata.php");
		$pdf_html = ob_get_contents();
		ob_end_clean();
		
		require_once("dompdf/dompdf_config.inc.php");
		//$pdf_html="<HTML><BODY>Hello how are you</BODY></HTML>";
		$dompdf = new DOMPDF(); // Create new instance of dompdf
		$dompdf->load_html($pdf_html); // Load the html
		$dompdf->render(); // Parse the html, convert to PDF
		$pdf_content = $dompdf->output(); // Put contents of pdf into variable for later
		
		$html_message="AMPL EXECUTIVE WISE DAILY CALL REPORT";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		// Load the SwiftMailer files
		require_once("swift/swift_required.php");

		$mailer = new Swift_Mailer(new Swift_MailTransport()); // Create new instance of SwiftMailer

		$message = Swift_Message::newInstance()
				       ->setSubject('ACEDNS DAILY CALL REPORT SUMMARY of '.$dateprev.'') // Message subject
					   ->setTo(array('vaman@automotiveml.com' => 'VAMAN','rammohan.kaipa@automotiveml.com' => 'RAMMOHAN',
					   'rajuyk@automotiveml.com' => 'RAJUYK','mohammed.ismail.sr@gulfoil.co.in'  => 'MOHAMMED')) // Array of people to send to
					   ->setBcc(array('kuntald@coral.in' => 'KKD','dipankarc@coral.in' => 'DSC')) //Array of people to send bcc
					   ->setFrom(array('acedns@coral.in' => 'acedns')) // From:
					   ->setBody($html_message, 'text/html') // Attach that HTML message from earlier
					   ->attach(Swift_Attachment::newInstance($pdf_content, "DCR_AMPL_$dateprev.pdf", 'application/pdf')); // Attach the generated PDF from earlier
		
		$mailer->send($message);
		echo "Mail sent successfully";
?>



</center>
</BODY>
</HTML>

