<?php
ob_start();
session_start();
require("adminUtils.php");


require("include/config.php");
require("include/config-setup.php");
//require("include/dbcon.php");


require ("attribute_selection.php");
if($_SESSION['admin_login']=="") 
{ 	
    // header("product:index.php");
    // echo "hello";
    header("Location: index.php");
    exit;

}
disphtml("main();");

function main(){
    require("include/dbcon.php");
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = "";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">MARKET FEEDBACK - PRICING COMPARISON</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	// attribute_selection($hidden,"");
    echo "<div id=\"display_data\"><table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" >
            <tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Select Criteria</td></tr>
            <tr><td align=\"right\">Comparison For:</td>
            <td>
            <select name='report_for' id='report_for_id'>
            <option value=''>Select</option>
            <option value='roe'>ROE</option>
            <option value='ne'>NE</option>
            </select>
            </td></tr>
            <tr><td colspan='2' align='center'><div id='date_div'>
            Date:<input type='text' name='comparison_date' id='comparison_date_id' placeholder='dd/mm/yyyy' style='height:15px;' />
            </div></td></tr>
            <tr><td colspan='2' align='right'><input type='submit' name='submit' value='Submit' onclick='display_result();'></td></tr></table></div>";

	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1300px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden>
		<!-- <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">
		&nbsp; -->
		<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
        flatpickr("#comparison_date_id", {
    dateFormat: "Y-m-d",         // value sent to server
    altInput: true,
    altFormat: "d/m/Y",          // shown to user
    allowInput: false
  });
	function display_result(){
		var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
		if(document.getElementById("report_for_id").value.search(/\S/) == -1){
			alert('Please Select Report For');
			return false;
		}
		
		// var start_date = document.getElementById("start_date").value;
		// var end_date = document.getElementById("end_date").value;
		var comparisonDate = document.getElementById("comparison_date_id").value;
		var reportFor = document.getElementById("report_for_id").value;
		
		if(document.getElementById("comparison_date_id").value.search(/\S/) == -1){
			alert("Please provide date.");
			return false;
		}
		// if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
		// 	alert("Please provide start date/end date");
		// 	return false;
		// }
		
		// if(start_date>end_date){
		// 	alert("Start date cannot be greater than end date");
		// 	return false;
		// }
		
		
		//alert(employee);
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		/*if(nickname=='STAR')
			{
		GenericAjaxFunction('admin_mis_market_pricing_data_customize.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);
			}
			else
			{*/
			// if(nickname=='STAR'){
			// 	GenericAjaxFunction('admin_mis_market_pricing_data_customize_modified.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);
				
			// }
			// else
			// {
			// GenericAjaxFunction('admin_mis_market_pricing_data_customize.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);

			// }

            GenericAjaxFunction('roe_pricing.php?comparison_date='+comparisonDate+'&report_for='+reportFor,'display',0);

			//}
		document.getElementById("print_export").hidden = false;
	}
	
	// function PrintElem(elem)
	// {
	// 	var displaydiv = document.getElementById("display").innerHTML;
	// 	Popup(displaydiv);
	//    //Popup($(elem).html());
	// }

	// function Popup(data) 
	// {
	// 	var mywindow = window.open('', 'Market Feedback - Pricing Comparison', 'height=400,width=600');
	// 	mywindow.document.write('<html><head><title>Market Feedback Pricing Comparison</title>');
	// 	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	// 	// 🔽 Inject print CSS directly into the head
	// 	mywindow.document.write('<style>');
	// 	mywindow.document.write('@media print {');
	// 	mywindow.document.write('  body { margin: 0; }');
	// 	mywindow.document.write('  @page { margin: 0; size: landscape; }');
	// 	mywindow.document.write('  .container, .wrapper, .content { max-width: none !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }');
	// 	mywindow.document.write('  .no-print { display: none !important; }');
	// 	mywindow.document.write('}');
	// 	mywindow.document.write('</style>');

	// 	mywindow.document.write('</head><body >');
	// 	mywindow.document.write(data);
	// 	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
	// 	mywindow.document.close(); // necessary for IE >= 10
	// 	mywindow.focus(); // necessary for IE >= 10
	
	// 	mywindow.print();
	// 	mywindow.close();
	
	// 	return true;
	// }
	
	function exporttocsv(divid)
	{
		const REPORT_TYPE_NE = 'ne';
		const REPORT_TYPE_ROE = 'roe';
		let exportFileName = '';
		let comparisonDate = document.getElementById("comparison_date_id").value;
		let reportFor = document.getElementById("report_for_id").value;
		if(reportFor == REPORT_TYPE_NE)
		{
			exportFileName = "NE_Pricing_Comparison_"+formatDate(comparisonDate)+".xls";
		}
		else if(reportFor == REPORT_TYPE_ROE)
		{
			exportFileName = "ROE_Pricing_Comparison_"+formatDate(comparisonDate)+".xls";
		}
		else
		{
			exportFileName = "Pricing_Comparison_"+formatDate(comparisonDate)+".xls";
		}
		let table = document.getElementById("display").outerHTML;

		// Add UTF-8 BOM
		let utf8BOM = "\uFEFF";

		// Combine BOM with HTML content
		let blob = new Blob([utf8BOM + table], { type: 'application/vnd.ms-excel;charset=utf-8;' });

		let link = document.createElement("a");
		link.href = URL.createObjectURL(blob);
		link.download = exportFileName;
		link.click();


	}
	function formatDate(dateStr) {
		const date = new Date(dateStr);

		const day = date.getDate();
		const month = date.toLocaleString('default', { month: 'long' });
		const year = date.getFullYear();

		// Helper to add "st", "nd", "rd", "th"
		function getOrdinalSuffix(n) {
			if (n > 3 && n < 21) return 'th';
			switch (n % 10) {
				case 1: return 'st';
				case 2: return 'nd';
				case 3: return 'rd';
				default: return 'th';
			}
		}

		const formattedDay = day + getOrdinalSuffix(day);
		return `${formattedDay}_${month}_${year}`;
	}
	</script>
    <?php
}
?>