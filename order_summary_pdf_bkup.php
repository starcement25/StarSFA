<?php
$nick_name='STAR';
//require("include/config.php");
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ABDOS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
		$dir = dirname(__FILE__);
		
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
		$pdf->SetFont('helvetica', '', 9);
		$pdf->AddPage('P',"A4");
		$pdf->writeHTML($html);
		//Use 'D' for download
		$tcpdf=$pdf->Output(__DIR__ .'/orderpdf/'.$filename, 'D');
		//For Whats app
		//file_put_contents($savein.str_replace("/","-",$filename), $tcpdf);    // save the pdf file on server
	}
		//previous date calc
	$curdateserver=gmdate('Ymd',strtotime('+330 minute'));
	$curdateserverformated=gmdate('d-m-Y',strtotime('+330 minute'));
	$curdatetimeserver=gmdate('dmYHis',strtotime('+330 minute'));
	$curtimeserver=gmdate('His',strtotime('+330 minute'));
	$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
	$previous_date=date('Y-m-d', strtotime("-1 days,$curdateserver "));
	$previous_day = date("d-m-Y",strtotime($previous_date));
	$prevdatetimeserver=date("dmY",strtotime($previous_date)).$curtimeserver;
	$emp_code=$_REQUEST['emp_code'];
		
	//Unlink previos files
	/*$folder_path = "/home/acedns/public_html/orderpdf"; 
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
	}*/
	//For DB query
	$sql_emp_name = "SELECT dns_emp_code, emp_name, district,state FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_emp_name = mysqli_query($link,$sql_emp_name);
	$row_emp_name = mysqli_fetch_assoc($res_emp_name);
	$emp_name = $row_emp_name['emp_name'];
	$dns_emp_code = $row_emp_name['dns_emp_code'];
	$district = $row_emp_name['district'];
	$state = $row_emp_name['state'];
	
			$pdf_html="<html><body>
				
				 <table border=\"1\" width=\"100%\" cellpadding=\"2\" cellspacing=\"2\"> 
				 	  <tr>
							<td  width=\"7%\"><b>State</b></td>
							<td width=\"42%\" colspan=\"3\">$state</td>
							<td width=\"14%\"><b>Name</b></td>
							<td width=\"37%\" colspan=\"3\">$emp_name</td>
						</tr>
						<tr>
							<td  width=\"7%\"><b>District</b></td>
							<td width=\"42%\" colspan=\"3\">$district</td>
							<td width=\"14%\"><b>Date</b></td>
							<td width=\"37%\" colspan=\"3\">$curdate</td>
						</tr>			
					  <tr>
						<td  width=\"7%\"><b>Sl.No</b></td>
						<td width=\"14%\"><b>Area Name</b></td>
						<td width=\"14%\"><b>Route Name</b></td>
						<td width=\"14%\"><b>Counter Name</b></td>
						<td width=\"14%\"><b>Product Name</b></td>
						<td  width=\"10%\"><b>Product Qty In Pcs</b></td>
						<td  width=\"10%\"><b>Product Qty In CS</b></td>
						<td  width=\"17%\"><b>Remarks</b></td>
					  </tr>";

	$sqlordersummary = "SELECT DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d-%m-%Y') AS date_selected, SUBSTRING(POCM.order_no,-19,5) AS emp_code, 
							POCM.customer_code, POCM.product_code, POCM.visit_qty, POCM.d_instruction, POCM.rate, POCM.amount,POCM.order_no,
							CM.customer_name,RM.route_name,CM.rds_tag,CM.cust_type
							FROM prev_order_counting_master POCM,customer_master CM,route_master RM
							WHERE POCM.customer_code=CM.customer_code AND RM.route_code=CM.route_code AND SUBSTRING(POCM.order_no,-19,5)='".$emp_code."'
							AND (order_no LIKE 'O%' OR order_no LIKE 'NO%')  AND SUBSTRING(POCM.order_no,-14,8) = '".$curdateserver."'
							ORDER BY CM.customer_name ASC";
	$resordersummary = mysqli_query($link,$sqlordersummary);
	$totalordersummary=mysqli_num_rows($resordersummary);
	if($totalordersummary >0){
		$cntac=1;
	while($rowordersummary=mysqli_fetch_assoc($resordersummary)){
		$visit_qty = $rowordersummary['visit_qty'];
		$d_instruction = $rowordersummary['d_instruction'];
		$rate = $rowordersummary['rate'];
		$customer_name = $rowordersummary['customer_name'];
		$route_name = $rowordersummary['route_name'];
		$product_code = $rowordersummary['product_code'];
		$order_no = $rowordersummary['order_no'];
		$rds_tag = $rowordersummary['rds_tag'];
		$cust_type = $rowordersummary['cust_type'];
		
		$sql_rds_details = "SELECT route_code FROM customer_master WHERE customer_code = '".$rds_tag."'";
		$res_rds_details = mysqli_query($link,$sql_rds_details);
		$row_rds_details = mysqli_fetch_assoc($res_rds_details);
		$rds_route_code = $row_rds_details['route_code'];
		
		if($cust_type == 'D'){
			$area_name = $route_name;
		}
		else{
			$sql_area_name = "SELECT route_name FROM route_master WHERE route_code = '".$rds_route_code."'";
			$res_area_name = mysqli_query($link,$sql_area_name);
			$row_area_name = mysqli_fetch_assoc($res_area_name);
			$area_name = $row_area_name['route_name'];
		}
		
		if($product_code!='')
		{
		
		$sql_prod_desc = "SELECT product_group_code, prod_desc,conversion_factor FROM product_master WHERE prod_code = '".$product_code."'";
		$res_prod_desc = mysqli_query($link,$sql_prod_desc);
		$row_prod_desc = mysqli_fetch_assoc($res_prod_desc);
		$product_group_code = $row_prod_desc['product_group_code'];
		$prod_desc = $row_prod_desc['prod_desc'];
		$conversion_factor = $row_prod_desc['conversion_factor'];
		
		$sqlUOMorder="SELECT UOM,weightage,remarks FROM order_details WHERE order_no='".$order_no."' AND sku_code='".$product_code."'";
		$rsUOMorder=mysqli_query($link,$sqlUOMorder);
		$rowUOMorder=mysqli_fetch_assoc($rsUOMorder);
		$UOM_order=$rowUOMorder['UOM'];
		$weightage=$rowUOMorder['weightage'];
		$remarks=$rowUOMorder['remarks'];
		if(strtoupper($UOM_order)!='CASE')
		{
			$qtyconvertedcase=$visit_qty/$conversion_factor;
			$qtyconvertedpcs=$visit_qty;
		}
		if(strtoupper($UOM_order)=='CASE')
		{
			$qtyconvertedcase=$visit_qty;
			$qtyconvertedpcs=($visit_qty*$conversion_factor);
		}
		}
		else
		{
			$qtyconvertedcase='';
			$qtyconvertedpcs='';
		}
		

			 $pdf_html.="<tr>
				<td >$cntac</td>
				<td >$area_name</td>
				<td >$route_name</td>
				<td >$customer_name</td>
				<td >$prod_desc</td>
				<td align=\"center\" >$qtyconvertedpcs</td>
				<td align=\"center\"> $qtyconvertedcase</td>
				<td >$remarks</td>
			  </tr>";
			$cntac++; 
		  }
		 }
		 else
		 {
			 $pdf_html.="<tr><td>NO RECORDS</td></tr>";
		 }
		$pdf_html.="</table></body></html>";
			 //echo $pdf_html;
		$filename='Transaction_'.$emp_code.'_'.$curdateserverformated.'.pdf';
		//$attach_file_name=$emp_name.'_'.'less than 5 visits'.'_'.$previous_day;
		pdf_create($pdf_html, $filename); 
?>