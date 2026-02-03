<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");
$mode = $_REQUEST['mode'];
if($mode == 'geofencingactivate')		    edit_record($_REQUEST['row_id']);
else {
disphtml("main();");
}

function main(){
	?>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script language="JavaScript" src="calendar3.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript" src="jquery.highlight.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <?php
	$hidden = "";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<table width=\"80%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" >
                    <tr> 
                        <td align=\"center\" class=\"ERR\">".stripslashes($GLOBALS['err_msg'])."</td>
                        <td align=\"right\" colspan=\"2\"></td>
                    </tr>
                </table><span style=\"font-weight:bold; font-size:14px;\">BRANCH SCHEMES PDF</span><br><br>";
	attribute_selection($hidden,$create_control='');
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 450px; max-width:1200px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <!--div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <form name="frm_opts" action="branchwise_schemes_PDF.php" method="post" >
        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="row_id" value="">
    </form>
    <script>
	function access_add_edit(ID)
	{
		//alert();
		document.frm_opts.mode.value='geofencingactivate';
		document.frm_opts.row_id.value=ID;
		document.frm_opts.submit();
	}
	function display_result(){
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('branch_schemes_PDF_data.php?zone='+zone+'&state='+state+'&branch='+branch,'display',0);
		document.getElementById("print_export").hidden = false;
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
	  function update_date_range(sl_no){
	    var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var start_date = document.getElementById("start_date_"+sl_no).value;
		var end_date = document.getElementById("end_date_"+sl_no).value;
		//alert(sl_no);
		//alert(end_date);
		var url = 'branch_schemes_PDF_data.php';
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction(''+url+'?zone='+zone+'&state='+state+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch+'&sl_no='+sl_no+'&opt_type=updateschemedate','display',0);
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}

	</script>
    <?php
}
?>