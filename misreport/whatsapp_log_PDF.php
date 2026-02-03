<?php
ob_start();
session_start();
require("adminUtils.php");
//require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");
$mode = $_REQUEST['mode'];
disphtml("main();");

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
    <body >
    <?php
	$hidden = "";
	echo "<center>";
	echo "<br>";
	$curdate=gmdate('d-m-Y',strtotime('+330 minute'));
	$prevdate_formated=date('Y-m-d', strtotime("-3 days,$curdate "));
	$prevdate_formated_one=date('Y-m-d', strtotime("-1 days,$curdate "));
	?>
    <span style="font-weight:bold; font-size:14px;">Whats App Notification Report</span><br><br>
        <table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px">
      <tr class="TDHEAD_SUB">
      	<td align="right">Employee</td>
        <td align="left">
        <select id="employee" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
		$sql_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM,whats_app_phone_list WP WHERE EM.emp_code=WP.emp_code AND EM.acedns = 'Y' ORDER BY EM.emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
		}
		?>
        </select>
        </td>
        </tr>
        <tr class="TDHEAD_SUB">
        <td colspan="2" align="center">
        <div id="date_div" >
    From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
    To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
    
    </div>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      <td colspan="2" align="right">
      <input type="submit" name="submit" value="Submit" onClick="display_result();" />
      </td>
      </tr>
    </table>
    <br /> <br />
    <div id="display" style="max-height: 450px; max-width:1200px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <!--div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
	<input type="hidden" id="report_name" />
    <?php
	echo "</center></body>";
	?>
    <form name="frm_opts" action="whatsapp_log_PDF.php" method="post" >
        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="row_id" value="">
    </form>
    <script>
	function display_result(){
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		else
			var employee = document.getElementById("employee").value;
		
					
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}
		 var date1 = new Date(start_date);
		 var date2 = new Date(end_date);
  		 var Difference_In_Time = date2.getTime() - date1.getTime();
		// To calculate the no. of days between two dates
		 var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
		 //alert(Difference_In_Days);
		//alert(Difference_In_Days);
		var prevdate="<?php echo $prevdate_formated;?>";
		var prevdate_one="<?php echo $prevdate_formated_one;?>";
		//alert(start_date);
		//alert(prevdate);
		//alert(prevdate_one);
		if((Difference_In_Days+1) >3 || start_date < prevdate || start_date > prevdate_one)
		{
			alert("You can check only last three days report.");
			return false;
		}
		
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('whatsapp_log_PDF_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
	</script>
    <?php
}
?>