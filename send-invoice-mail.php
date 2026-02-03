<?php
$nick_name='NIMBUS';
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ASL");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
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
		$rssurveyval=mysqli_query($link,$sqlsurveyval);
		$rowsurveyval=mysqli_fetch_assoc($rssurveyval);
		$survey_val=$rowsurveyval['value'];
		$survey_val_array=explode(";",$survey_val);
		$farmerid=$survey_val_array[1];
		
		$sqlfarmerval="SELECT * FROM farmer_master WHERE farmer_id='".$farmerid."'";
		$rsfarmerval=mysqli_query($link,$sqlfarmerval);
		$rowfarmerval=mysqli_fetch_assoc($rsfarmerval);
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
				$rsBOQ=mysqli_query($link,$sqlBOQ);
				$cntBOQ=1;
				while($rowBOQ=mysqli_fetch_assoc($rsBOQ))
				{
					$prod_code=$rowBOQ['prod_code'];
					$sqlproddetails="SELECT prod_desc,UOM1 FROM product_master WHERE dns_prod_code='".$prod_code."'";
					$rsproddetails=mysqli_query($link,$sqlproddetails);
					$rowproddetails=mysqli_fetch_assoc($rsproddetails);
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
						  $rsproddetailsaddmat=mysqli_query($link,$sqlproddetailsaddmat);
						  $rowproddetailsaddmat=mysqli_fetch_assoc($rsproddetailsaddmat);
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
$DO_no='';
$vehicle_no='';
//$sqlcustomerdet="SELECT CM.customer_name,CM.address,CM.pin,CM.TIN,CM.PAN,CM.state_code,CM.rds_tag FROM customer_master CM,DO_transaction DT WHERE CM.customer_code=DT.customer_code"
				 
			$pdf_html="<table  width=\"95%\"><tr><td align=\"center\"><font size=\"+4\">Tax Invoice</font></td></tr></table><table  width=\"95%\" style=\"border: 1px solid #CCC;border-collapse: collapse;\"><tr><td width=\"50%\"><table ><tr style=\"border: none\"><td>Shipped To</td></tr><tr style=\"border: none\"><td><b>AADHYA SALES CORPORATION (DELHI)</b></td></tr><tr style=\"border: none\"><td>GROUND FLOOR KH NO-156/237 O-06 POOTH KHURD DELHI-110039</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZB</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2468B</td></tr><tr style=\"border: none\"><td style=\"border-bottom: 1px solid;width: 100%;\">State Name : DELHI</td></tr><tr style=\"border: none\"><td>Billed To</td></tr><tr style=\"border: none\"><td><b>AADHYA SALES CORPORATION (DELHI)</b></td></tr><tr style=\"border: non e\"><td>GROUND FLOOR KH NO-156/237 O-06 POOTH KHURD DELHI-110039</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZB</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2468B</td></tr><tr style=\"border: none\"><td >State Name : DELHI</td></tr></table></td><td width=\"50%\"><table width=\"100%\" ><tr >
	<td width=\"50%\"><table width=\"100%\" style=\"border: 1px solid #CCC;border-collapse: collapse;\"><tr >
	<td style=\"border-bottom: 1px solid;\">Invoice NO.<br />2147</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Delivery Note<br />
B/BHD/1737 / BHD/DA-2848, B/BHD/1942 / BHD/DA-2849</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Supplier's Ref.<br />
B/BHD/1737 / DLI/SO-3012</td></tr><tr ><td style=\"border-bottom: 1px solid;\">P.O. No.<br />
&nbsp;</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Despatch Document No.<br />
6136</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Despatched through<br />
AGGARWAL TRANSPORT CO.-MISC</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Bill of Lading/LR-RR No.<br />
&nbsp;</td></tr></table></td><td width=\"50%\"><table width=\"100%\" style=\"border: 1px solid #CCC;border-collapse: collapse;\"><tr >
	<td style=\"border-bottom: 1px solid;\">Dated<br />17-Jul-2020</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Vehicle No.<br />
HR 74 A 5385<br />&nbsp;<br />&nbsp;</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Other Reference(s)<br />
&nbsp;<br />&nbsp;</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Dated<br />
17-Jul-2020, 17-Jul-2020</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Delivery Note Date<br />
17-Jul-2020, 17-Jul-2020</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Destination<br />
HARIDWAR<br />&nbsp;</td></tr><tr ><td style=\"border-bottom: 1px solid;\">Distance in K.M.<br />
500</td></tr></table></td></tr><tr ><td style=\"border-bottom: 1px solid;border-left: 1px solid;\" colspan=\"2\">Terms of Delivery<br />F.O.R.</td></tr></table></td></tr></table></td></tr></table>";
$pdf_html.="<table  width=\"95%\" style=\"border: 1px solid #CCC;\"><tr><td width=\"5%\" style=\"border-right: 1px solid;\">Sl No.</td><td width=\"47%\" style=\"border-right: 1px solid;\">Goods</td><td width=\"8%\" style=\"border-right: 1px solid;\">HSN</td><td width=\"9%\" style=\"border-right: 1px solid;\">Qty Nos</td><td width=\"9%\" style=\"border-right: 1px solid;\">Weight MT</td><td width=\"9%\" style=\"border-right: 1px solid;\">Basic Rate</td><td width=\"13%\" style=\"border-right: 1px solid;\">Amount</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\">1</td><td width=\"50%\" style=\"border-right: 1px solid;\">DHRUV VANASPATI 15 KG BOX SUPER (P4)</td><td width=\"9%\" style=\"border-right: 1px solid;\">1516</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1105</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">16.575</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1163.00</td><td width=\"13%\" style=\"border-right: 1px solid;text-align: right;\">12,85,115.00</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\">2</td><td width=\"50%\" style=\"border-right: 1px solid;\">DHRUV VANASPATI 15 KG BOX SUPER (P5)</td><td width=\"9%\" style=\"border-right: 1px solid;\">1516</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">228</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">16.575</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1163.00</td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">2,51,940.00</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\"></td><td width=\"47%\" style=\"border-right: 1px solid;text-align: right;\">OUTPUT IGST</td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;text-align: right;\">76,852.75</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\"></td><td width=\"47%\" style=\"border-bottom: 1px solid;border-right: 1px solid;text-align: right;\">R/OFF</td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">0.75</td></tr><tr><td colspan=\"7\">INR Sixteen Lakh Thirteen Thousand Nine Hundred Seven Only</td></tr></table>";
			$html_message="Auto generated mail for Quotation from ACEdns.<br><br>PFA<br><br>Powered by ACEdns";
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
			$val='kkd@forcepower.in';
			//$val='dipankarc@coral.in';
			//$val1='dipankarc@coral.in';
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

