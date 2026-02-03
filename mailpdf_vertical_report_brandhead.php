<?php
ini_set("display_errors", 0); 
ini_set('memory_limit', '2048M');
$nick_name='RUPA';
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_RUPA");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
/*define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_RUPA");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/functions.php");*/
?>
<HTML>
<BODY>
<center>
<?php
		$dir = dirname(__FILE__);
		//previous date calc
		$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
		$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		$previous_day = date("d-m-Y",strtotime($previous_date));
		$html_message="Auto generated mail of Brand Wise Report from ACEdns.<br><br>PFA<br><br>Powered by ACEdns";
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
		
		$final_logo=$nick_name.'.jpg';
		//if(file_exists("/home/acedns/public_html/acednsproduct/logo/$final_logo")) $final_logo=$nick_name.'.png';
		//else 																	   $final_logo='CSPL.png';

		$sqlvertical="SELECT vertical_name,headed_by_email,headed_by FROM vertical_mail_access WHERE headed_by!='E0030'";
		$rsvertical=mysqli_query($link,$sqlvertical);
		while($rowvertical=mysqli_fetch_assoc($rsvertical))
		{
			$dompdf = new DOMPDF(); // Create new instance of dompdf
			$mailer = new Swift_Mailer(new Swift_MailTransport()); // Create new instance of SwiftMailer

			$headed_by_email=$rowvertical['headed_by_email'];
			$headed_by=$rowvertical['headed_by'];
			$emp_hierarchy=return_employee_hierarchy($headed_by);
			$vertical_value_array=explode(',',$rowvertical['vertical_name']);
			$vertical_value = "'".implode("','", $vertical_value_array)."'";
			${pdf_html.$headed_by}="<html>
				<head>
					<style>
					body {font-family:Helvetica, Arial, sans-serif; font-size:10pt;}
					table {width:100%; border-collapse:collapse; border:1px solid #CCC; text-align:right;}
					td {padding:5px;}
					</style>
				</head>
				<body>
				<center>
				<table border=\"1\">
				  <tr>
				  	<td><img src=\"/home/acedns/public_html/acednsproduct/logo/$final_logo\" alt=\"\" /></td>
					<td colspan=\"6\" align=\"center\"><b>RUPA - ".$rowvertical['vertical_name']." Brand wise report as on ".$previous_day."</b></td>
				  </tr>
				  <tr>
					<td><b>SI</b></td>
					<td><b>State</b></td>
					<td><b>Calls made</b></td>
					<td><b>Productive</b></td>
					<td><b>Primary</b></td>
					<td><b>Secondary</b></td>
				  </tr>";

			 ${pdf_html_internal.$headed_by}='';
			 $subject = "RUPA - ".$rowvertical['vertical_name']." Brand wise report as on ".$previous_day;

			
			$sqlemp_vertical_value_details="SELECT EM.vertical_value,BM.branch_name,EM.emp_code,VBED.calls_made,VBED.productive,VBED.primary,VBED.secondary  
										FROM employee_master EM,branch_master BM,vertical_branch_employeewise_details VBED WHERE EM.branch_code=BM.branch_code 
										AND EM.emp_code=VBED.emp_code AND VBED.vertical_value IN(".$vertical_value.") AND VBED.emp_code IN(".$emp_hierarchy.") AND
										 VBED.operation_date='2015-08-20' GROUP BY VBED.vertical_value,VBED.branch_code ORDER BY EM.vertical_value,BM.branch_name ASC";
		    $resemp_vertical_value_details = mysqli_query($link,$sqlemp_vertical_value_details); 
			$countemp_vertical_value_details=mysqli_num_rows($resemp_vertical_value_details);
			if($countemp_vertical_value_details==0)
			{
				${pdf_html_internal.$headed_by}.="No Records Found";
			}
			else
			{
				$count = 1;
				$total_calls_masde = 0;
				$total_productive = 0;
				$total_primary = 0;
				$total_secondary = 0;
				$vertical_value_array=array();
				$previous_value_verticle='';
			while($rowemp_vertical_value_details = mysqli_fetch_assoc($resemp_vertical_value_details))
			{
				$vertical_value=$rowemp_vertical_value_details['vertical_value'];
				$emp_code=$rowemp_vertical_value_details['emp_code'];
				$emp_name=$rowemp_vertical_value_details['emp_name'];
				$branch_name=$rowemp_vertical_value_details['branch_name'];
				$calls_made=$rowemp_vertical_value_details['calls_made'];
				$productive=$rowemp_vertical_value_details['productive'];
				$primary=$rowemp_vertical_value_details['primary'];	
				$secondary=$rowemp_vertical_value_details['secondary'];				    
			
				/*$sqlemp_vertical_value_calls_made="SELECT COUNT(DISTINCT (customer_code)) AS calls_made FROM customer_master WHERE customer_code IN (SELECT PH.customer_code FROM 
									payment_header PH,employee_master EM WHERE (SUBSTRING(PH.receipt_id,2,5) IN('".$emp_code."') OR SUBSTRING(PH.receipt_id,3,5) IN('".$emp_code."')) 
									AND DATE_FORMAT(SUBSTRING(PH.receipt_id,-14,8),'%Y-%m-%d')='".$previous_date."' UNION ALL SELECT OH.customer_code 
									FROM order_header OH,employee_master EM WHERE (SUBSTRING(OH.order_no,2,5) IN('".$emp_code."') OR SUBSTRING(OH.order_no,3,5) IN('".$emp_code."')) 
									AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$previous_date."')";
				$resemp_vertical_value_calls_made = mysqli_query($link,$sqlemp_vertical_value_calls_made);
				$rowemp_vertical_value_calls_made=mysqli_fetch_assoc($resemp_vertical_value_calls_made);
				$calls_made=$rowemp_vertical_value_calls_made['calls_made'];
				
				
				$sqlemp_vertical_value_productive="SELECT COUNT(OH.order_no) AS productive FROM order_header OH WHERE SUBSTRING(OH.order_no,2,5) IN('".$emp_code."')
									AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$previous_date."'";
				$resemp_vertical_value_productive = mysqli_query($link,$sqlemp_vertical_value_productive);
				$rowemp_vertical_value_productive=mysqli_fetch_assoc($resemp_vertical_value_productive);
				$productive=$rowemp_vertical_value_productive['productive'];
				
				$sqlemp_vertical_value_primary="SELECT COUNT(OH.order_no) AS primary_value FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code AND 
												SUBSTRING(OH.order_no,2,5) IN('".$emp_code."') AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$previous_date."' AND CM.cust_type='D'";
				$resemp_vertical_value_primary = mysqli_query($link,$sqlemp_vertical_value_primary);
				$rowemp_vertical_value_primary=mysqli_fetch_assoc($resemp_vertical_value_primary);
				$primary=$rowemp_vertical_value_primary['primary_value'];
				
				$sqlemp_vertical_value_secondary="SELECT COUNT(OH.order_no) AS secondary_value FROM order_header OH,customer_master CM WHERE OH.customer_code=CM.customer_code AND 
												SUBSTRING(OH.order_no,2,5) IN('".$emp_code."') AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d')='".$previous_date."' AND CM.cust_type='R'";
				$resemp_vertical_value_secondary = mysqli_query($link,$sqlemp_vertical_value_secondary);
				$rowemp_vertical_value_secondary=mysqli_fetch_assoc($resemp_vertical_value_secondary);
				$secondary=$rowemp_vertical_value_secondary['secondary_value'];*/

					if($calls_made >0 || $productive >0 || $primary >0 || $secondary >0)
					{
						${pdf_html_internal.$headed_by}.="<tr>
								<td>$count</td>
								<td>$branch_name</td>
								<td style=\"text-align:right;\">$calls_made</td>
								<td style=\"text-align:right;\">$productive</td>
								<td style=\"text-align:right;\">$primary</td>
								<td style=\"text-align:right;\">$secondary</td>
							  </tr>";
						${total_calls_made.$vertical_value.$headed_by}=${total_calls_made.$vertical_value.$headed_by}+$calls_made;
						${total_productive.$vertical_value.$headed_by}=${total_productive.$vertical_value.$headed_by}+$productive;
						${total_primary.$vertical_value.$headed_by}=${total_primary.$vertical_value.$headed_by}+$primary;
						${total_secondary.$vertical_value.$headed_by}=${total_secondary.$vertical_value.$headed_by}+$secondary;
						
						$count++;
					}
				}
				${pdf_html_internal.$headed_by}.="<tr>
								<td colspan=\"2\"><b>Grand Total</b></td>
								<td style=\"text-align:right;\">".${total_calls_made.$vertical_value.$headed_by}."</td>
								<td style=\"text-align:right;\">".${total_calls_made.$vertical_value.$headed_by}."</td>
								<td style=\"text-align:right;\">".${total_primary.$vertical_value.$headed_by}."</td>
								<td style=\"text-align:right;\">".${total_secondary.$vertical_value.$headed_by}."</td>
							  </tr>";
			}
			${pdf_html.$headed_by}.=${pdf_html_internal.$headed_by};
			${pdf_html.$headed_by}.="<tr><td colspan=\"7\" align=\"right\"><b>Powered by ACEdns</b></td></tr></table></body></html>";
			$dompdf->load_html(${pdf_html.$headed_by}); // Load the html
			$dompdf->render(); // Parse the html, convert to PDF
			$pdf_content = $dompdf->output(); // Put contents of pdf into variable for later
			$val=$headed_by_email;
			$valpart=strtoupper(substr($val,0,3));
			$val='kkd@forcepower.in';
			$valpart='KKD';
			$message = Swift_Message::newInstance()
						   ->setSubject($subject) // Message subject
						   ->setTo(array($val=>$valpart)) // Array of people to send to
						   ->setBcc(array('dipankarc@coral.in' => 'DSC'))
						   ->setFrom(array('acedns@acedns.in' => 'acedns')) // From:
						   ->setBody($html_message, 'text/html') // Attach that HTML message from earlier
						   ->attach(Swift_Attachment::newInstance($pdf_content, 'Brand_wise_report.pdf', 'application/pdf')); // Attach the generated PDF from earlier
			
			$mailer->send($message);
		}
		//end loop
		echo "Mail sent successfully";
?>
</center>
</BODY>
</HTML>
<td><img src="" alt="" /></td>

