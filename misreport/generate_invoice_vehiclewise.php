<?php
//namespace dompdf;
//require_once 'dompdf/autoload.inc.php'; 
// Reference the Dompdf namespace 
//use Dompdf\Dompdf; 
ob_start();
session_start();
require("adminUtils.php");

if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function pdf_create($html, $filename, $stream=TRUE) 
{
    require_once("../dompdf/dompdf_config.inc.php");
    //$savein = '../upload/ASL/';
	$savein = 'invoice/';
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    /*$canvas = $dompdf->get_canvas();
    $font = Font_Metrics::get_font("arial", "normal","12px");
    //the same call as in my previous example
    $canvas->page_text(540, 773, "Page {PAGE_NUM} of {PAGE_COUNT}",
                   $font, 6, array(0,0,0));*/
    $pdf = $dompdf->output();      // gets the PDF as a string
    file_put_contents($savein.str_replace("/","-",$filename), $pdf);    // save the pdf file on server
	/*header("Content-type: application/pdf"); 
	header("Content-Disposition: attachment; filename=$filename");
	readfile("order_transaction/sale invoice.pdf");*/
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=$filename");
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize("$savein/sale invoice.pdf"));
	ob_clean();
	flush();
	readfile("$savein/sale invoice.pdf");
	exit();
    //unset($html);
    //unset($dompdf); 
}
function main(){
$plantnamearray = array();

$sqlinvoicedetails="SELECT CM.customer_name,RM.route_name,DT.vehicle_no,DT.invoice_no,DATE_FORMAT(DT.invoice_date,'%d-%m-%Y') AS invoice_date
				 FROM customer_master CM,route_master RM,DO_tracking DT WHERE 
				 CM.customer_code=DT.customer_code AND RM.route_code=DT.destination AND DT.invoice_date!='0000-00-00' 
				 ORDER BY CM.customer_name ASC";
$rsinvoicedetails=mysqli_query($link,$sqlinvoicedetails);
$total_rows=mysqli_num_rows($rsinvoicedetails);
if($total_rows>0){
	?>
    <form name="frm_invoicedownload" method="post" action=""/>
    <input type="hidden" name="mode" value="invoicedownload"/>
    <input type="hidden" name="invoice_no" value=""/>
    <table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center">
       <tr class="TDHEAD" align="center" id="head_main" ><td colspan="7">Download Invoice</td></tr>
      <tr class="TDHEAD_SUB" align="center" id="head_main">
      	<td>SI</td>
        <td>Customer Name</td>
        <td>Route Name</td>
        <td>Vehicle No</td>
        <td>Invoice No</td>
        <td>Invoice Date</td>
        <td>Download</td>
      </tr>
    <?php
	$count=1;
	while($rowinvoicedetails=mysqli_fetch_assoc($rsinvoicedetails))
	{
		$customer_name=$rowinvoicedetails['customer_name'];
		$route_name=$rowinvoicedetails['route_name'];
		$vehicle_no=$rowinvoicedetails['vehicle_no'];
		$invoice_no=$rowinvoicedetails['invoice_no'];
		$invoice_date=$rowinvoicedetails['invoice_date'];
		echo "<tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$customer_name."</td>
				<td>".$route_name."</td>
				<td>".$vehicle_no."</td>
				<td>".$invoice_no."</td>
				<td>".$invoice_date."</td>
				<td ><a href=\"javascript:download_invoice('".$invoice_no."');\" title=\" Download Invoice \"
                    style=\"color: #030 ;\">Download</a></td>
			  </tr>";
		$count++;
	}
	?>
 <script language="javascript" type="text/javascript">
function download_invoice(invoice_no)
{
	document.frm_invoicedownload.mode.value='invoicedownload';
	document.frm_invoicedownload.invoice_no.value=invoice_no;
	document.frm_invoicedownload.submit();
}
</script>
<?php  
}
else{
	echo "<tr><td align=\"center\"><strong><font color=\"red\">No records found</font></strong></td></tr>";
}
echo "</table></form>";

if($_REQUEST['mode']=='invoicedownload')
{
	$invoice_no=$_REQUEST['invoice_no'];
	/*$pdf_html="<table border=\"1\" width=\"95%\"><tr><td><font size=\"+4\">Invoice NO is:$invoice_no</font></td></tr></table>";*/
	$pdf_html="<table  width=\"95%\"><tr><td align=\"center\"><font size=\"+4\">Tax Invoice</font></td></tr></table><table  width=\"95%\" style=\"border: 1px solid #CCC;border-collapse: collapse;\"><tr><td width=\"50%\"><table ><tr style=\"border: none\"><td>Shipped To</td></tr><tr style=\"border: none\"><td><b>S.K ENTERPRISES(BHD)</b></td></tr><tr style=\"border: none\"><td>15c SUDHIPUR</td></tr><tr style=\"border: none\"><td >SHIVPUR VARANASI UP - 221003</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZC</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2460B</td></tr><tr style=\"border: none\"><td style=\"border-bottom: 1px solid;width: 100%;\">State Name : Uttar Pradesh, State Code : 09</td></tr><tr style=\"border: none\"><td>Billed To</td></tr><tr style=\"border: none\"><td><b>S.K ENTERPRISES(BHD)</b></td></tr><tr style=\"border: none\"><td>15c SUDHIPUR</td></tr><tr style=\"border: none\"><td>SHIVPUR VARANASI UP 221003</td></tr><tr style=\"border: none\"><td >GSTIN/UIN : 09AZKPS2460B1ZC</td></tr><tr style=\"border: none\"><td >PAN/IT No : AZKPS2460B</td></tr><tr style=\"border: none\"><td >State Name : Uttar Pradesh, State Code : 09</td></tr></table></td><td width=\"50%\"><table width=\"100%\" ><tr >
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
	$pdf_html.="<table  width=\"95%\" style=\"border: 1px solid #CCC;\"><tr><td width=\"5%\" style=\"border-right: 1px solid;\">Sl No.</td><td width=\"47%\" style=\"border-right: 1px solid;\">Goods</td><td width=\"8%\" style=\"border-right: 1px solid;\">HSN</td><td width=\"9%\" style=\"border-right: 1px solid;\">Qty Nos</td><td width=\"9%\" style=\"border-right: 1px solid;\">Weight MT</td><td width=\"9%\" style=\"border-right: 1px solid;\">Basic Rate</td><td width=\"13%\" style=\"border-right: 1px solid;\">Amount</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\">1</td><td width=\"50%\" style=\"border-right: 1px solid;\">DHRUV VANASPATI 15 KG BOX SUPER (P4)</td><td width=\"9%\" style=\"border-right: 1px solid;\">1516</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1105</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">16.575</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1163.00</td><td width=\"13%\" style=\"border-right: 1px solid;text-align: right;\">12,85,115.00</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\">2</td><td width=\"50%\" style=\"border-right: 1px solid;\">DHRUV VANASPATI 15 KG BOX SUPER (P5)</td><td width=\"9%\" style=\"border-right: 1px solid;\">1516</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1105</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">16.575</td><td width=\"9%\" style=\"border-right: 1px solid;text-align: right;\">1163.00</td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">2,65,175.00</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\"></td><td width=\"47%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">15,50,390.00</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\"></td><td width=\"47%\" style=\"border-right: 1px solid;text-align: right;\">OUTPUT IGST</td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;text-align: right;\">77,519.50</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;\"></td><td width=\"47%\" style=\"border-bottom: 1px solid;border-right: 1px solid;text-align: right;\">R/OFF</td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">0.50</td></tr><tr><td width=\"5%\" style=\"border-right: 1px solid;border-bottom: 1px solid;\"></td><td width=\"47%\" style=\"border-bottom: 1px solid;border-right: 1px solid;text-align: right;\">Total</td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;text-align: right;\">1,330 NOS</td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;text-align: right;\">19.950 MT</td><td width=\"9%\" style=\"border-bottom: 1px solid;border-right: 1px solid;\"></td><td width=\"13%\" style=\"border-right: 1px solid;border-bottom: 1px solid;text-align: right;\">Rs 16,27,910.00</td></tr><tr><td colspan=\"7\">INR Sixteen Lakh Twenty Seven Thousand Nine Hundred Ten Only</td></tr></table>";
	$filename='sale invoice.pdf';
	//use Dompdf\Dompdf; 
	//$pdf_html="<HTML><BODY>Hello how are you</BODY></HTML>";
	/*$dompdf = new Dompdf(); // Create new instance of dompdf
	$dompdf->loadHtml($pdf_html); // Load the html
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render(); // Parse the html, convert to PDF
	//$pdf_content = $dompdf->output(); // Put contents of pdf into variable for later
	
	//header("Content-type: application/pdf"); 
	//header("Content-Disposition: attachment; filename=sale_invoice.pdf");
	$dompdf->stream("sale invoice", array("Attachment" => 1));*/
	pdf_create($pdf_html, $filename, $stream=TRUE); 
}
mysqli_close($link);
}?>