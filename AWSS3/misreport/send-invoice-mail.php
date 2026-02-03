<?php
$nick_name='NIMBUS';
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ASL");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
require_once('dompdf/dompdf_config.inc.php');
		// Load the SwiftMailer files
		require_once('swift/swift_required.php');
		/*$dir = dirname(__FILE__);
		//previous date calc
		$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
		$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		$previous_day = date("d-m-Y",strtotime($previous_date));
		//$survey_id=$_REQUEST['survey_id'];
		$survey_id='SUE015520200430155443';
		//$subject = "RUPA - Brand wise report as on ".$previous_day;
		$html_message="Auto generated mail for Quotation from ACEdns.<br><br>PFA<br><br>Powered by ACEdns";
		// Get the contents of the pdf into a variable for later
		require_once('dompdf/dompdf_config.inc.php');
		// Load the SwiftMailer files
		require_once('swift/swift_required.php');


		/*ob_start();
		require_once($dir.'/tt_report.php');
		$pdf_html = ob_get_contents();
		ob_end_clean();*/
		//$pdf_html="<HTML><BODY>Hello how are you</BODY></HTML>";
		//start loop
		/*$sqlsurveyval="SELECT value FROM survey_output WHERE survey_id='".$survey_id."' AND row_id='RA017'";
		$rssurveyval=mysql_query($sqlsurveyval);
		$rowsurveyval=mysql_fetch_array($rssurveyval);
		$survey_val=$rowsurveyval['value'];
		$survey_val_array=explode(";",$survey_val);
		$farmerid=$survey_val_array[1];
		
		$sqlfarmerval="SELECT * FROM farmer_master WHERE farmer_id='".$farmerid."'";
		$rsfarmerval=mysql_query($sqlfarmerval);
		$rowfarmerval=mysql_fetch_array($rsfarmerval);
		$quotation_generated=$rowfarmerval['quotation_generated'];
		if($quotation_generated=='YES')
		{
		$mi_id=$rowfarmerval['mi_id'];
		$farmer_name=$rowfarmerval['farmer_name'];
		$fittings_accessories=$rowfarmerval['fittings_accessories'];
		$mi_reference_no=$rowfarmerval['mi_reference_no'];
		$farmer_type=$rowfarmerval['farmer_type'];
		$mi_applied_for=$rowfarmerval['mi_applied_for'];
		$mi_applied_for_array=explode('#',$rowfarmerval['mi_applied_for']);
		$mi_type=$mi_applied_for_array[0];
		$mi_area=$rowfarmerval['mi_area'];
		$total_area=$rowfarmerval['total_area'];
		$type_of_crops=$rowfarmerval['type_of_crops'];
		$spacing =$rowfarmerval['spacing '];
		$additional_material =$rowfarmerval['additional_material'];
		
		$pdf_html="<HTML><BODY>
				<table width=\"90%\">
				  <tr>
					<td align=\"center\"><b>Nimbus Pipes Ltd - Quotation</b></td>
				  </tr>
				 </table>
				 <table border=\"1\" width=\"90%\"> 
					 <tr><td colspan='4'><b>Application Basic Detail</b></td></tr>
					  <tr>
						<td align=\"left\">MI ID</td>
						<td align=\"left\">$mi_id</td>
						<td align=\"left\">Farmer Name</td>
						<td align=\"left\">$farmer_name</td>
					  </tr>
					  <tr>
						<td align=\"left\">Fittings & Accessories</td>
						<td align=\"left\">$fittings_accessories</td>
						<td align=\"left\">Mi Reference No</td>
						<td align=\"left\">$mi_reference_no</td>
					  </tr>
					  <tr>
						<td align=\"left\">Farmer Type</td>
						<td align=\"left\">$farmer_type</td>
						<td align=\"left\">MI Type</td>
						<td align=\"left\">$mi_type</td>
					  </tr>
					   <tr>
						<td align=\"left\">Mi Area</td>
						<td align=\"right\">$mi_area</td>
						<td align=\"left\">Total Area</td>
						<td align=\"right\">$total_area</td>
					  </tr>
					  
					  </table>
					  <table width=\"90%\"> 
					  <tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>
					  </table>
					  <table border=\"1\" width=\"90%\">
					  <tr>
						<td>Crop Name</td>
						<td>MI Area</td>
						<td>Spacing</td>
						<td>Pro Rata Spacing</td>
					  </tr>
					  <tr>
						<td>$type_of_crops</td>
						<td align=\"right\">$mi_area</td>
						<td>$spacing</td>
						<td></td>
					  </tr>
				  </table>
				  <table width=\"90%\"> 
					  <tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>
				  </table>
				  <table border=\"1\" >
					  <tr>
						<td>S.No</td>
						<td>Component Name</td>
						<td>Unit</td>
						<td>Price/Unit(Rs.)</td>
						<td>Actual Qty(No's)</td>
						<td>Total Price(Rs)</td>
					  </tr>";
			    $sqlBOQ="SELECT prod_code,area,spacing,qty,amount FROM BOQ_master WHERE UPPER(mi_type)='".$mi_applied_for."' AND area='".$mi_area." ha'";
				$rsBOQ=mysql_query($sqlBOQ);
				$cntBOQ=1;
				while($rowBOQ=mysql_fetch_array($rsBOQ))
				{
					$prod_code=$rowBOQ['prod_code'];
					$sqlproddetails="SELECT prod_desc,UOM1 FROM product_master WHERE dns_prod_code='".$prod_code."'";
					$rsproddetails=mysql_query($sqlproddetails);
					$rowproddetails=mysql_fetch_array($rsproddetails);
					$prod_desc=$rowproddetails['prod_desc'];
					$UOM1=$rowproddetails['UOM1'];
					$area=$rowBOQ['area'];
					$spacing=$rowBOQ['spacing'];
					$qty=$rowBOQ['qty'];
					$amount=$rowBOQ['amount'];
					$rate=round($amount/$qty,2);
					$tot_qty=$tot_qty+$qty;
					$tot_amount=$tot_amount+$amount;
					 $pdf_html.="<tr>
						<td>$cntBOQ</td>
						<td>$prod_desc</td>
						<td>$UOM1</td>
						<td align=\"right\">$rate</td>
						<td align=\"right\">$qty</td>
						<td align=\"right\">$amount</td>
					  </tr>";
					  
					 $cntBOQ++; 
				}
				$pdf_html.="<tr>
						<td></td>
						<td>Total Material</td>
						<td></td>
						<td></td>
						<td align=\"right\">$tot_qty</td>
						<td align=\"right\">$tot_amount</td>
					  </tr></table>
					 <table width=\"90%\"> 
					  <tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					  </tr>
				  </table>";
				  if($additional_material !='') 
				  {
					 $additional_material_array=explode(';',$additional_material); 
					  $pdf_html.="<table border=\"1\" width=\"90%\"> 
						 <tr><td colspan='5'><b>Farmers Additional Requirement</b></td></tr>
						  <tr>
							<td>Component Name</td>
							<td>Unit</td>
							<td >Price/Unit(Rs.)</td>
							<td >Actual Qty(No's)</td>
							<td >Total Price(Rs)</td>
						  </tr>
					  ";
					  foreach($additional_material_array as $additional_material_val)
					  {
						  $additional_material_sub_array=explode("#",$additional_material_val);
						  $dns_prod_code_add_mat=$additional_material_sub_array[0];
						  $rate_add_mat=$additional_material_sub_array[1];
						  $qty_add_mat=$additional_material_sub_array[2];
						  $sqlproddetailsaddmat="SELECT prod_desc,uom FROM additional_material WHERE prod_code='".$dns_prod_code_add_mat."' 
						  						AND rate='".$rate_add_mat."' AND lower(mi_type)='".strtolower($mi_type)."'";
						  $rsproddetailsaddmat=mysql_query($sqlproddetailsaddmat);
						  $rowproddetailsaddmat=mysql_fetch_array($rsproddetailsaddmat);
						  $prd_desc_add_mat= $rowproddetailsaddmat['prod_desc'];
						  $UOM1_add_mat= $rowproddetailsaddmat['uom'];
						  $amount_add_mat=$qty_add_mat*$rate_add_mat;
						  $tot_qty_add_mat=$tot_qty_add_mat+$qty_add_mat;
						  $tot_amount_add_mat=$tot_amount_add_mat+$amount_add_mat;
						   $pdf_html.="<tr>
								<td>$prd_desc_add_mat</td>
								<td>$UOM1_add_mat</td>
								<td align=\"right\">$rate_add_mat</td>
								<td align=\"right\">$qty_add_mat</td>
								<td align=\"right\">$amount_add_mat</td>
							  </tr>";
					  }
					  $pdf_html.="<tr>
						<td>Total Material</td>
						<td></td>
						<td></td>
						<td align=\"right\">$tot_qty_add_mat</td>
						<td align=\"right\">$tot_amount_add_mat</td>
					  </tr></table>";
				  }
				  $pdf_html.="<table width=\"90%\"><tr><td  align=\"right\"><b>Powered by ACEdns</b></td></tr></table>";
					  $pdf_html.="</BODY></HTML>";*/
				 //echo $pdf_html;
			$pdf_html="<table  width=\"95%\"><tr><td align=\"center\"><font size=\"+4\">Tax Invoice</font></td></tr></table><table  width=\"95%\" style=\"border: 1px solid #CCC;border-collapse: collapse;\"><tr><td width=\"50%\"><table ><tr style=\"border: none\"><td>Shipped To</td></tr><tr style=\"border: none\"><td><b>AADHYA SALES CORPORATION (DELHI)</b></td></tr><tr style=\"border: none\"><td>GROUND FLOOR KH NO-156/237 O-06 POOTH KHURD DELHI-110039</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZB</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2468B</td></tr><tr style=\"border: none\"><td style=\"border-bottom: 1px solid;width: 100%;\">State Name : DELHI</td></tr><tr style=\"border: none\"><td>Billed To</td></tr><tr style=\"border: none\"><td><b>AADHYA SALES CORPORATION (DELHI)</b></td></tr><tr style=\"border: none\"><td>GROUND FLOOR KH NO-156/237 O-06 POOTH KHURD DELHI-110039</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZB</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2468B</td></tr><tr style=\"border: none\"><td >State Name : DELHI</td></tr></table></td><td width=\"50%\"><table width=\"100%\" ></table>";
			$dompdf = new DOMPDF(); // Create new instance of dompdf
			$mailer = new Swift_Mailer(new Swift_MailTransport()); // Create new instance of SwiftMailer
			//echo 'ak';	
			$dompdf->load_html($pdf_html); // Load the html
			//echo 'b';
			$dompdf->render(); // Parse the html, convert to PDF
			//echo 'c';
			$pdf_content = $dompdf->output(); // Put contents of pdf into variable for later
			//echo 'd';
			//print_r($pdf_content);
			//$val='kkd@forcepower.in';
			$val='dipankarc@coral.in';
			$valpart='KKD';
			$subject="Invoice generated for -AADHYA SALES CORPORATION (DELHI)";
			$message = Swift_Message::newInstance()
						   ->setSubject($subject) // Message subject
						   ->setTo(array($val=>$valpart)) // Array of people to send to
						   //->setBcc(array('dipankarc@coral.in' => 'DSC'))
						   ->setFrom(array('info@salesmpower.acedns.in' => 'salempower')) // From:
						   ->setBody($html_message, 'text/html') // Attach that HTML message from earlier
						   ->attach(Swift_Attachment::newInstance($pdf_content, "Tax invoice.pdf", 'application/pdf')); // Attach the generated PDF from earlier
			
			$mailer->send($message);
		//end loop
		echo "Mail sent successfully";
?>

