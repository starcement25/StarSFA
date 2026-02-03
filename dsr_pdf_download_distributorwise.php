<?php
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
		$pdf->AddPage('L',"A4");
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
    $distributor_code=$_REQUEST['distributor_code'];
		
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
			
	$pdf_html_product= "<tr><td style=\"height: 40px;\" colspan=\"5\" width=\"25%\"><b>Product Name</b></td>";					
					  
	$sqlordersummary = "SELECT DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d-%m-%Y') AS date_selected, SUBSTRING(POCM.order_no,-19,5) AS emp_code, 
							POCM.customer_code, POCM.product_code, POCM.visit_qty, POCM.d_instruction, POCM.rate, POCM.amount,POCM.order_no,
							CM.customer_name,RM.route_name,CM.rds_tag,CM.cust_type,PM.prod_desc,PM.conversion_factor
							FROM prev_order_counting_master POCM,customer_master CM,route_master RM,product_master PM
							WHERE POCM.customer_code=CM.customer_code AND RM.route_code=CM.route_code AND POCM.product_code=PM.prod_code 
							AND SUBSTRING(POCM.order_no,-19,5)='".$emp_code."' AND POCM.order_no LIKE 'O%'  
							AND SUBSTRING(POCM.order_no,-14,8) = '".$curdateserver."' AND CM.rds_tag='".$distributor_code."'  ORDER BY CM.customer_name ASC";
	$restotalordersummary = mysqli_query($link,$sqlordersummary);
	$product_code_array=array();
	$customer_code_array=array();
	$total_product_array=array();
	$countordersummary=mysqli_num_rows($restotalordersummary);
	if($countordersummary)
	{
	$count=1;
	while($rowproducttotal=mysqli_fetch_assoc($restotalordersummary))
	{
		$product_code = $rowproducttotal['product_code'];
		if(!in_array($product_code,$total_product_array) )
		{
			array_push($total_product_array,$product_code);
		}
	}
	$product_td_width=(75/count($total_product_array));
	$product_sub_td_width=(75/(count($total_product_array)*2));
	$resordersummary = mysqli_query($link,$sqlordersummary);
	while($rowordersummaryproduct=mysqli_fetch_assoc($resordersummary))
	{
		$product_name=$rowordersummaryproduct['prod_desc'];
		$product_code = $rowordersummaryproduct['product_code'];
		if(!in_array($product_code,$product_code_array))
		{
			$pdf_html_product .= "<td   colspan=\"2\" width=\"$product_td_width%\"><b>$product_name</b></td>";
			$pdf_html_sub_product .= "<td width=\"$product_sub_td_width%\"><b>PCS</b></td><td width=\"$product_sub_td_width%\"ss><b>CS</b></td>";
			array_push($product_code_array,$product_code);
			//array_push($product_name_array,$product_name);
		}
		$conversion_factor = $rowordersummaryproduct['conversion_factor'];
		$visit_qty = $rowordersummaryproduct['visit_qty'];
		$d_instruction = $rowordersummaryproduct['d_instruction'];
		$rate = $rowordersummaryproduct['rate'];
		$customer_name = $rowordersummaryproduct['customer_name'];
		$customer_code = $rowordersummaryproduct['customer_code'];
		$route_name = $rowordersummaryproduct['route_name'];
		
		$order_no = $rowordersummaryproduct['order_no'];
		$rds_tag = $rowordersummaryproduct['rds_tag'];
		$cust_type = $rowordersummaryproduct['cust_type'];
		
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
		else if(strtoupper($UOM_order)=='CASE')
		{
			$qtyconvertedcase=$visit_qty;
			$qtyconvertedpcs=($visit_qty*$conversion_factor);
		}
		${'customer_name'.$customer_code}=$customer_name;
		${'route_name'.$customer_code}=$route_name;
		${'area_name'.$customer_code}=$area_name;
		${'prod_case_val'.$customer_code.$product_code}=${'prod_case_val'.$customer_code.$product_code}+$qtyconvertedcase;
		${'prod_pcs_val'.$customer_code.$product_code}=${'prod_pcs_val'.$customer_code.$product_code}+$qtyconvertedpcs;
		${'weightage'.$customer_code}=${'weightage'.$customer_code}+$weightage;
		//${'weightage'.$customer_code}='';
		$total_weightage=$total_weightage+$weightage;
		${'total_prod_case_val'.$product_code}=${'total_prod_case_val'.$product_code}+$qtyconvertedcase;
		${'total_prod_pcs_val'.$product_code}=${'total_prod_pcs_val'.$product_code}+$qtyconvertedpcs;
		//${'total_prod_case_val'.$product_code}='';
		//${'total_prod_pcs_val'.$product_code}='';
		
		if(!in_array($customer_code,$customer_code_array))
		{
			array_push($customer_code_array,$customer_code);
		}
	}
	$pdf_html="<html><body>
				 <table border=\"1\" width=\"100%\" cellpadding=\"3\" cellspacing=\"3\"> 
				 	  <tr>
							<td  width=\"7%\" style=\"height: 40px;\"><b>State</b></td>
							<td width=\"42%\" colspan=\"3\" style=\"height: 40px;\">$state</td>
							<td width=\"14%\" style=\"height: 40px;\"><b>Name</b></td>
							<td width=\"37%\" colspan=\"3\" style=\"height: 40px;\">$emp_name</td>
						</tr>
						<tr>
							<td  width=\"7%\" style=\"height: 40px;\"><b>District</b></td>
							<td width=\"42%\" colspan=\"3\" style=\"height: 40px;\">$district</td>
							<td width=\"14%\" style=\"height: 40px;\"><b>Date</b></td>
							<td width=\"37%\" colspan=\"3\" style=\"height: 40px;\">$curdate</td>
						</tr></table><table border=\"1\" width=\"100%\" cellpadding=\"3\" cellspacing=\"3\"> ".$pdf_html_product."</tr>
						<tr>
						<td width=\"3%\"><b>Sl.No</b></td>
						<td width=\"6%\"><b>Counter Name</b></td>
						<td width=\"6%\"><b>Route Name</b></td>
						<td width=\"6%\"><b>Area Name</b></td>
						<td width=\"4%\"><b>Weatage(Kg's)</b></td>".$pdf_html_sub_product."
					  </tr>";
		$cntac=1;
	foreach($customer_code_array as $customer_code_val){
		 $pdf_html.="<tr>
				<td>$cntac</td>
				<td>".${'customer_name'.$customer_code_val}."</td>
				<td>".${'route_name'.$customer_code_val}."</td>
				<td>".${'area_name'.$customer_code_val}."</td><td>".round(${'weightage'.$customer_code_val},2)."</td>";
		foreach($product_code_array as $product_code_val){
				if(${'prod_pcs_val'.$customer_code_val.$product_code_val}> 0) ${'prod_pcs_val'.$customer_code_val.$product_code_val}=round(${'prod_pcs_val'.$customer_code_val.$product_code_val},2);
				if(${'prod_case_val'.$customer_code_val.$product_code_val}> 0) ${'prod_case_val'.$customer_code_val.$product_code_val}=round(${'prod_case_val'.$customer_code_val.$product_code_val},2);

				 $pdf_html.="<td >".${'prod_pcs_val'.$customer_code_val.$product_code_val}."</td><td align=\"center\" >".${'prod_case_val'.$customer_code_val.$product_code_val}."</td>";
			}
			$pdf_html.="</tr>";
			$cntac++; 
		 }
		 foreach($product_code_array as $product_code_value){
			 if(${'total_prod_pcs_val'.$product_code_value}> 0) ${'total_prod_pcs_val'.$product_code_value}=round(${'total_prod_pcs_val'.$product_code_value},2);
			if(${'total_prod_case_val'.$product_code_value}> 0) ${'total_prod_case_val'.$product_code_value}=round(${'total_prod_case_val'.$product_code_value},2);
			$pdf_html_total.="<td >".${'total_prod_pcs_val'.$product_code_value}."</td><td align=\"center\" >".${'total_prod_case_val'.$product_code_value}."</td>";
		 }
		 $pdf_html.="<tr>
						<td colspan=\"4\" width=\"21%\" align=\"center\" style=\"color: red\"><b>Total</b></td>
						<td width=\"4%\"><b>".round($total_weightage,2)."</b></td>".$pdf_html_total."
					  </tr>";
		 //echo $pdf_html;
	}
	 else
	 {
		 $pdf_html.="<tr><td>NO RECORDS</td></tr>";
	 }
	$pdf_html.="</table></body></html>";
		 //echo $pdf_html;
	$filename='DSR_'.$emp_code.'_'.$curdateserverformated.'.pdf';
	//$attach_file_name=$emp_name.'_'.'less than 5 visits'.'_'.$previous_day;
	pdf_create($pdf_html, $filename); 
?>