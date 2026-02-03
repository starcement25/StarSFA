<?php
include "header.php";
//include "connection.php";
//include "check.php";
//session_start();
function pdf_create($html, $filename, $stream=TRUE) 
{
    require_once("../dompdf/dompdf_config.inc.php");
    //$savein = '../upload/ASL/';
	$savein = 'invoice/';
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
	$dompdf->set_paper('A4','landscape');

    $dompdf->render();
    /*$canvas = $dompdf->get_canvas();
    $font = Font_Metrics::get_font("arial", "normal","12px");
    //the same call as in my previous example
    $canvas->page_text(540, 773, "Page {PAGE_NUM} of {PAGE_COUNT}",
                   $font, 6, array(0,0,0));*/
				  // echo $html;
				  // exit();
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
	header('Content-Length: ' . filesize("$savein/Group Wise Report.pdf"));
	ob_clean();
	flush();
	readfile("$savein/Group Wise Report.pdf");
	exit();
    //unset($html);
    //unset($dompdf); 
}
?>
<!-- -->
<form name="group_details" id="group_details" method="POST" action="" onSubmit="return validate_date();">
<input type="hidden" name="mode" value="fetchdata" />
<input type="hidden" name="lenderarrowmode" value="" />
<input type="hidden" name="borrowerarrowmode" value="" />
<input type="hidden" name="modeval" value="" id="modeval" />


<div class="main-content">
  <section class="section">
  <div class="row">
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script language="javascript" type="text/javascript">

function validate_date()
{
	if(document.getElementById("lender_id").value=='')
	{
		alert("Please Select Lender");
			return false;
	}
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("From date/To date cannot be empty");
			return false;
		}
	/*if(document.getElementById("start_date").value> document.getElementById("end_date").value)
		{
			alert("From date cannot be greater than To date");
			return false;
		}*/
		return true;
}
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}
function Popup(data) 
{
	var curdate="<?php echo date('d/m/Y');?>";
	var mywindow = window.open('', 'Groupwise Lender Report', 'height=400,width=600');
	mywindow.document.write('<html><head>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write('<p align=right><b>'+curdate+'</b></p>');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');
	//mywindow.document.write('<p align=right><b>Powered By Forcepower</b></p></body></html>');
	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	document.getElementById('DET_TEXT').style.display='none';
	document.getElementById('DET_TEXT_HIDE').style.display='';
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Groupwise Lender Report' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
$(document).ready(function () {
		$('#start_date').datepicker({
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: "d/m/y"
});
$('#end_date').datepicker({
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: "d/m/y"
});		
    });
	function up_arrow_lender_submit()
	{
		document.group_details.lenderarrowmode.value='uparrow';
		document.group_details.borrowerarrowmode.value='';
		document.group_details.submit();
	}
	function down_arrow_lender_submit()
	{
		document.group_details.lenderarrowmode.value='downarrow';
		document.group_details.borrowerarrowmode.value='';
		document.group_details.submit();
	}
	function up_arrow_borrower_submit()
	{
		document.group_details.borrowerarrowmode.value='uparrow';
		document.group_details.lenderarrowmode.value='';
		document.group_details.submit();
	}
	function down_arrow_borrower_submit()
	{
		document.group_details.borrowerarrowmode.value='downarrow';
		document.group_details.lenderarrowmode.value='';
		document.group_details.submit();
	}
	function exporttopdf()
	{
		document.getElementById('modeval').value='pdfexport';
		document.getElementById('group_details').submit();
	}

    </script>
<!--div class="col-12">
  <div class="form-group" style="text-align:center;font-weight:bold;">Group Wise Lender Report
  </div></div-->
  <div class="col-4">
  <div class="form-group">
  <label for="usr">Group:</label>
  <?php /*$sqlquerylender="SELECT DISTINCT group_code,group_name FROM group_master WHERE group_code 
				IN(SELECT group_name FROM customer_master WHERE customer_code IN
				(SELECT DISTINCT lender_id FROM loan_transaction WHERE company_id = '".$_SESSION['company_id']."') UNION ALL (SELECT DISTINCT borrower_id FROM loan_transaction WHERE company_id = '".$_SESSION['company_id']."'))  AND
				company_id = '".$_SESSION['company_id']."'  ORDER BY group_name ASC"*/;?>
  <select name="group_code" id="group_code" style="height:2%; width:300px;" class="form-control">
    <option value="">SELECT</option>
        <?php 
        $sqlquerylender="SELECT DISTINCT group_code,group_name FROM group_master WHERE group_name 
				IN(SELECT group_name FROM customer_master WHERE customer_code IN
				(SELECT DISTINCT lender_id FROM loan_transaction WHERE company_id = '".$_SESSION['company_id']."' UNION  SELECT DISTINCT borrower_id FROM loan_transaction WHERE company_id = '".$_SESSION['company_id']."'))  AND
				company_id = '".$_SESSION['company_id']."'  ORDER BY group_name ASC";
        $resultquerylender = mysqli_query($link,$sqlquerylender);
        $countquerylender=mysqli_num_rows($resultquerylender);
        if($countquerylender>0){
        while($rowquerylender = mysqli_fetch_assoc($resultquerylender))
        {
            if( $_REQUEST['group_code']=="'".$rowquerylender['group_name']."'") { $selected='selected';}
            else{ $selected='';}
			$lenderstring .="'".$rowquerylender['group_name']."',";
           	$optionstring.="<option value=\"'".$rowquerylender['group_name']."'\" ".$selected.">".$rowquerylender['group_name']."</option>";
        }
		$lenderstring = rtrim($lenderstring,",");
		if( $_REQUEST['group_code']==$lenderstring) { $selectedall='selected';}
		else{ $selectedall='';}
		$optionall="<option value=\"".$lenderstring."\" ".$selectedall.">ALL</option>";
		echo $optionfinal=$optionall.$optionstring;
    }
    ?>	
   </select>
</div>
  </div>
<div class="col-4">
  <div class="form-group">
  <label for="usr">From Date:</label>
  <input type="text" class="form-control"  name='start_date' id='start_date' 
    value="<?php if($_REQUEST['start_date']!=''){ echo $_REQUEST['start_date']; }else{ echo date('d/m/y',strtotime($_SESSION['start_date']));}?>" style="width:200px;">
</div>
  </div>
  <div class="col-4">
  <div class="form-group">
  <label for="usr">To Date:</label>
  <input type="text" class="form-control"  name='end_date' id='end_date' 
    value="<?php if($_REQUEST['end_date']!=''){ echo $_REQUEST['end_date']; }else{ echo date('d/m/y');}?>" style="width:200px;">
</div>
  </div>
    <div class="col-4">
  <div class="form-group">
  <input type="submit" style="margin-top:8%;" class="btn btn-info" value="Show All" name="showall">
  <input type="submit" style="margin-top:8%;" class="btn btn-info" value="Show O/S" name="showos">
  </div>
  </div>
 <?php if($_REQUEST['mode']=='fetchdata'){
	 
	 		$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date;
			
	 	 	$start_date=str_replace('/','-',$_POST['start_date']);
			$start_date_array=explode("-",$start_date);
			if(strlen($start_date_array[1])==1) $start_date_array[1]='0'.$start_date_array[1];
			if(strlen($start_date_array[0])==1) $start_date_array[0]='0'.$start_date_array[0];
			$start_date_val='20'.$start_date_array[2].'-'.$start_date_array[1].'-'.$start_date_array[0];
			//$start_date_val=date('Y-m-d',strtotime($start_date));
			//$start_date_val=date('Y-m-d', strtotime("-1 days,$start_date_val "));
			
			//$end_date=str_replace(',','',$_POST['end_date']);
			$end_date=str_replace('/','-',$_POST['end_date']);
			$end_date_array=explode("-",$end_date);
			if(strlen($end_date_array[1])==1) $end_date_array[1]='0'.$end_date_array[1];
			if(strlen($end_date_array[0])==1) $end_date_array[0]='0'.$end_date_array[0];
			$end_date_val='20'.$end_date_array[2].'-'.$end_date_array[1].'-'.$end_date_array[0];

	 ?>
<?php
	if(strpos($_REQUEST['group_code'],',')===false){
		$querylender = "SELECT group_name FROM group_master WHERE  group_name=".$_REQUEST['group_code']." AND 
						company_id = '".$_SESSION['company_id']."'"; 
		$rslender=mysqli_query($link,$querylender);
		$rowlender=mysqli_fetch_assoc($rslender);
		$group_name=$rowlender['group_name'];
		$text_cond=strtoupper($group_name);
		$group_id_search=$_REQUEST['group_code'];
	}
	else
	{
	$group_id_search=$_REQUEST['group_code'];
	$text_cond="ALL";

	}
$count = 1;
			//echo "<div  style=\"position:fixed;\">";
			echo "<div class=\"table-responsive\" id=\"display\" ><div class=\"col-12\">";?>
			
  <?php echo"<div class=\"form-group\" style=\"text-align:center;font-weight:bold; display:''\" id=\"DET_TEXT\">DETAILS OF O/S. $text_cond
  </div></div><table  id=\"display_table\" class=\"form-group\"><tr style=\"display:none\" id=\"DET_TEXT_HIDE\"><td  style=\"text-align:center;font-weight:bold;\">DETAILS OF O/S. $text_cond
  </td></tr><tr><td class=\"form-group\" width='100%'><table class=\"table table-bordered table-striped\"  > <thead>";
			echo "<tr>
					<td width=\"10%\"><b>Date Of Deposit</b></td>";
					if($_REQUEST['lenderarrowmode']=='')
					{
						echo "<td width=\"23%\"><a href='javascript:void(0)' onclick='javascript:up_arrow_lender_submit();'><b> Lender</b> </a> 
     </td>";
					}
					else if($_REQUEST['lenderarrowmode']=='downarrow'){
					echo "<td width=\"23%\"><b><a href='javascript:void(0)' onclick='javascript:up_arrow_lender_submit();'><b> Lender</b> <img src='uparrow.png'></a> 
     </b></td>";
					}
					else if($_REQUEST['lenderarrowmode']=='uparrow'){
					echo "<td width=\"23%\"><b><a href='javascript:void(0)' onclick='javascript:down_arrow_lender_submit();'><b> Lender</b> <img src='downarrow.png'></a> 
      </b></td>";
					}
					if($_REQUEST['borrowerarrowmode']=='')
					{
						echo "<td width=\"23%\"> <a href='javascript:void(0)' onclick='javascript:up_arrow_borrower_submit();'><b>Borrower</b></a> 
     </td>";
					}
					else if($_REQUEST['borrowerarrowmode']=='downarrow'){
					echo "<td width=\"23%\"><a href='javascript:void(0)' onclick='javascript:up_arrow_borrower_submit();'><b>Borrower</b> <img src='uparrow.png'></a> 
     </td>";
					}
					else if($_REQUEST['borrowerarrowmode']=='uparrow'){
					echo "<td width=\"23%\"><a href='javascript:void(0)' onclick='javascript:down_arrow_borrower_submit();'><b>Borrower</b> <img src='downarrow.png'></a> 
     </td>";
					}
					echo"
					<td width=\"8%\" align=\"center\"><b>Amount<br />(Lacs)</b></td>
					<td width=\"8%\"><b>Int. Rate(%)</b></td>
					<td width=\"10%\" align=\"center\"><b>Period <br />Days</b></td>
					<td width=\"8%\"><b>Date of Maturity</b></td>
					<td width=\"10%\"><b>Input Remarks</b></td>
					";
				  echo "</tr></thead>";
				 //echo "</table>";
			//echo "</div>";
			echo "<br /><br />";
			$pdf_html_pdf ="<table width=\"100%\"><tr><td style=\"text-align:center;font-weight:bold;\">DETAILS OF O/S. $text_cond</td></tr></table><table width=\"100%\"><tr>
					<td width=\"10%\"><b>Date Of Deposit</b></td>
					<td width=\"23%\"><b>Lender</b></td>
					<td width=\"23%\"><b>Borrower</b></td>
					<td width=\"8%\"><b>Amount<br />(Lacs)</b></td>
					<td width=\"8%\"><b>Int. Rate(%)</b></td>
					<td width=\"10%\" align=\"center\"><b>Period <br />Days</b></td>
					<td width=\"8%\" align=\"center\"><b>Date of Maturity</b></td>
					<td width=\"10%\"><b>Input Remarks</b></td></tr>";

			if($_REQUEST['lenderarrowmode']=='' && $_REQUEST['borrowerarrowmode']==''){
				$sortstring=' ORDER BY maturity_date  ASC';
			}
			if($_REQUEST['lenderarrowmode']=='uparrow')
			{
				$sortstring=' ORDER BY (SElect customer_name FROM customer_master where customer_code=loan_transaction.lender_id)  DESC';
			}
			if($_REQUEST['lenderarrowmode']=='downarrow')
			{
				$sortstring=' ORDER BY (SElect customer_name FROM customer_master where customer_code=loan_transaction.lender_id)  ASC';
			}
			if($_REQUEST['borrowerarrowmode']=='uparrow')
			{
				$sortstring=' ORDER BY (SElect customer_name FROM customer_master where customer_code=loan_transaction.borrower_id)  DESC';
			}
			if($_REQUEST['borrowerarrowmode']=='downarrow')
			{
				$sortstring=' ORDER BY (SElect customer_name FROM customer_master where customer_code=loan_transaction.borrower_id)  ASC';
			}
			if(isset($_POST['showall']))
			{
				$buttonval='showall';
				$_SESSION['buttonval']='';
				$_SESSION['buttonval']='showall';
			}
			if(isset($_POST['showos']))
			{
				$buttonval='showos';
				$_SESSION['buttonval']='';
				$_SESSION['buttonval']='showos';
			}
			if($_SESSION['buttonval']=='showall')
			{
				$datecondition="SUBSTRING(disbursal_date,1,10) >='".$start_date_val."' AND SUBSTRING(disbursal_date,1,10) <='".$end_date_val."'";
			}
			if($_SESSION['buttonval']=='showos')
			{
				//$datecondition=" maturity_date >='".$contentsdatetime."'";
				$datecondition=" repayment_done='no' AND prepayment_done='no'";
				
			}
			$sql_loan_details = "SELECT * FROM loan_transaction WHERE $datecondition AND 
					(lender_id IN(SELECT customer_code FROM customer_master WHERE group_name IN(
					".$group_id_search.")) OR borrower_id IN(SELECT customer_code FROM customer_master WHERE group_name IN(
					".$group_id_search."))) AND 
					company_id = '".$_SESSION['company_id']."' AND deleted='no' $sortstring
					";
			$res_loan_details = mysqli_query($link,$sql_loan_details);
			$countloan=mysqli_num_rows($res_loan_details);
			//echo "<table border='1'>";
			if($countloan >0){
			while($row_loan_details=mysqli_fetch_assoc($res_loan_details))
			{
				$trans_id=$row_loan_details['trans_id'];
				$trans_date=date('j M, y',strtotime($row_loan_details['trans_date']));
				$trans_date_format=date('Y-m-d',strtotime($row_loan_details['trans_date']));
				$lender_id=$row_loan_details['lender_id'];
				$borrower_id=$row_loan_details['borrower_id'];
				$loan_amount=$row_loan_details['loan_amount'];
				$interest=$row_loan_details['interest'];
				$maturity_date=date('d/m/y',strtotime($row_loan_details['maturity_date']));
				$disbursal_date_format=date('d/m/y',strtotime($row_loan_details['disbursal_date']));
				$datediff = strtotime($row_loan_details['maturity_date']) - strtotime($row_loan_details['disbursal_date']);
				$input_remarks=$row_loan_details['input_remarks'];
				/*$years = floor($datediff / (365*60*60*24));  
				$period_months = floor(($datediff - $years * 365*60*60*24) 
                               / (30*60*60*24)); 
				$period_days=floor(($datediff - $years * 365*60*60*24 -  $period_months*30*60*60*24)/ (60*60*24));*/
				 $period_days=round($datediff / (60 * 60 * 24));

				$commission_amount=(($loan_amount*$commission)/100);

				$remarks=$row_loan_details['remarks'];
				$bill_no=$row_loan_details['bill_no'];
				if($bill_no==''){  
					$bill_date='N/A';
					$bill_no='N/A';
				}
				$total_amount=$loan_amount+$commission_amount+$GST_amount;
				if($input_remarks=='') $input_remarks_pdf=' ';
				
				$action = "<td class=\"action\"><a href=\"edit_loan.php?trans_id=$trans_id\" class=\"view\" id=\"view\">EDIT</a></td>";
				$sql_lender = "SELECT customer_name FROM customer_master where customer_code='".$lender_id."' AND company_id = '".$_SESSION['company_id']."'";
				$res_lender = mysqli_query($link,$sql_lender);
			//echo "<table border='1'>";
				$row_lender=mysqli_fetch_assoc($res_lender);
				$lender=$row_lender['customer_name'];
				$sql_borrower = "SELECT customer_name FROM customer_master where customer_code='".$borrower_id."' AND company_id = '".$_SESSION['company_id']."'";
				$res_borrower = mysqli_query($link,$sql_borrower);
			//echo "<table border='1'>";
				$row_borrower=mysqli_fetch_assoc($res_borrower);
				$borrower=$row_borrower['customer_name'];
				$total_amount_loan=$total_amount_loan+$loan_amount;
				echo "<tbody><tr class=\"odd\">
						<td style=\"font-size: 12px;height: 30px;\">$disbursal_date_format</td>
						<td style=\"font-size: 12px;height: 30px;\">$lender</td>
						<td style=\"font-size: 12px;height: 30px;\">$borrower</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".$loan_amount."</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".number_format($interest,2)."</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".$period_days."</td>
						<td style=\"font-size: 12px;height: 30px;\"><b>$maturity_date</b></td>
						<td style=\"font-size: 12px;height: 30px;\">".$input_remarks."</td>
						";
					  echo "</tr>";
				$pdf_html_pdf .="<tr>
						<td style=\"font-size: 12px;height: 30px;\">$disbursal_date_format</td>
						<td style=\"font-size: 12px;height: 30px;\">$lender</td>
						<td style=\"font-size: 12px;height: 30px;\">$borrower</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".$loan_amount."</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".number_format($interest,2)."</td>
						<td align=\"center\" style=\"font-size: 12px;height: 30px;\">".$period_days."</td>
						<td style=\"font-size: 12px;height: 30px;\"><b>$maturity_date</b></td>
						<td style=\"font-size: 12px;height: 30px;\">".$input_remarks_pdf."</td></tr>";	  
				$count++;
			}
				echo "<tr>
						<td colspan=\"3\" align=\"center\" style=\"font-size: 12px\"><b>TOTAL</b></td>
						<td align=\"center\" style=\"font-size: 14px\"><b>".$total_amount_loan."</b></td>
						<td align=\"right\" style=\"font-size: 11px\"></td>
						<td align=\"right\" style=\"font-size: 11px\"></td>
						<td></td><td></td>";
				echo "</tr></tbody>";
				
				$pdf_html_pdf .="<tr>
						<td colspan=\"3\" align=\"center\" style=\"font-size: 12px\"><b>TOTAL</b></td>
						<td align=\"center\" style=\"font-size: 14px\"><b>".$total_amount_loan."</b></td>
						<td align=\"right\" style=\"font-size: 11px\"></td>
						<td align=\"right\" style=\"font-size: 11px\"></td>
						<td></td><td></td></tr></table>";
			}
			if($countloan==0)
			{
				echo "<br /><br /><tr><td colspan=\"15\" align=\"center\"><b>No records found</b></td></tr>";
			}
			echo "</table></td></tr></table></div>
			  <br /><br />";
			  if($_REQUEST['modeval']=='pdfexport'){
 $filename='Group Wise Report.pdf';
 $pdf_html_pdf=$pdf_html_pdf;
	pdf_create($pdf_html_pdf, $filename, $stream=TRUE); 
 }
			  ?>
		 <input type="button" style="margin-left:2%; margin-top:2%;"class="btn btn-info" value="Print" name="print" id="print" onClick="PrintElem('#display');">
  <input type="button" style="margin-left:2%; margin-top:2%;" class="btn btn-info" value="Export" name="export" id="btnExport" onClick="exporttocsv();">
      <input type="button" style="margin-left:2%; margin-top:2%;" class="btn btn-info" value="Export PDF" name="export" id="btnPDF" onClick="exporttopdf();">

    <?php
 }
	?>
</div>
</section>
</div>
</form>

<?php
include "footer.php";
?>