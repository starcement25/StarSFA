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
	function pdf_create($html, $filename, $stream=TRUE) 
	{
		require_once('dompdf/dompdf_config.inc.php');
		//$savein = '../upload/ASL/';
		$savein = 'starpdf/';
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		//$dompdf->setPaper('A4', 'portrait');

		$dompdf->render();
		$pdf = $dompdf->output();      // gets the PDF as a string
		file_put_contents($savein.str_replace("/","-",$filename), $pdf);    // save the pdf file on server
		//unset($html);
		//unset($dompdf); 
	}
		//previous date calc
		$curdateserver=gmdate('d-m-Y',strtotime('+330 minute'));
		$curdate=gmdate('Y-m-d',strtotime('+330 minute'));
		$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		$previous_day = date("d-m-Y",strtotime($previous_date));
		// Get the contents of the pdf into a variable for later
		//require_once('dompdf/dompdf_config.inc.php');
		/*ob_start();
		require_once($dir.'/tt_report.php');
		$pdf_html = ob_get_contents();
		ob_end_clean();*/
		//$pdf_html="<HTML><BODY>Hello how are you</BODY></HTML>";
		//start loop
	$sqlselemphighlevel="SELECT emp_code,emp_name FROM employee_master WHERE emp_code='E0163'";
	$rsselemphighlevel=mysqli_query($link,$sqlselemphighlevel);
	$rowselemphighlevel=mysqli_fetch_assoc($rsselemphighlevel);
	$emp_code=$rowselemphighlevel['emp_code'];
	$emp_name=$rowselemphighlevel['emp_name'];
	
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_val_rds=' AND (LO.emp_code IN('.$employee_hierarchy.'))';
		
		$pdf_html="<html><body>
				<table width=\"90%\" style=\"table-layout:fixed;\">
				  <tr>
				<td align=\"center\" ><b>SFA MANAGER ACTIVITY REPORT</b><br /><b>(DATE: $curdateserver)</b></td>
				  </tr>
				  <tr>
					<td ><b>MANAGER CODE: ".$emp_code."</b><br /><b>MANAGER NAME: ".$emp_name."</b><img src=\"http://salesmpower.acedns.in/logo/STAR.png\"/></td>
				  </tr>
				 </table>
				 <table border=\"1\" width=\"90%\" > 
					  <tr>
						<td  width=\"8%\">SL.NO.</td>
						<td width=\"40%\">EMPLOYEE NAME</td>
						<td width=\"12%\">DESIGNATION</td>
						<td width=\"12%\">HQ</td>
						<td width=\"14%\">TODAY CHECK-IN TIME</td>
						<td  width=\"14%\">TODAY COUNTER VISITS</td>
					  </tr>";
			   $sqllocation = "SELECT EM.emp_code,EM.emp_name,EM.designation,EM.HQ,DATE_FORMAT(LO.date,'%T') as att_time FROM employee_master EM INNER  JOIN location LO 
			    ON LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y-%m-%d')='".$curdate."'
				".$emp_val_rds." ORDER BY EM.emp_name ASC";
				
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
							 AND DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d')='".$curdate."'";
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
						<td align=\"right\"> $total_customer_visit</td>
					  </tr>";
					  
					 $cntac++; 
				}
				$pdf_html.="</table></body></html>";
				 //echo $pdf_html;
				$filename=$emp_code.'_activity.pdf';
			pdf_create($pdf_html, $filename, $stream=TRUE); 
			/*$dompdf = new DOMPDF(); // Create new instance of dompdf
			$dompdf->load_html($pdf_html); // Load the html
			//echo 'b';
			$dompdf->render(); // Parse the html, convert to PDF
			//echo 'c';
			$pdf_content = $dompdf->output(); // Put contents of pdf into variable for later*/
		}
?>

