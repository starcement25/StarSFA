<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){	
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = "";
	
	$create_control = "";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\"></span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection_pjp($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:70%; overflow-y: scroll; " align="center"></div><br />
    <!--<div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />-->
    
	<input type="hidden" id="report_name" />
    
    <script>
	function display_result(){
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
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
				
		var employee = document.getElementById("employee").value;
		
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		  GenericAjaxFunction('attendance_status_update_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	function PrintElem(elem)
	{
		var displaydivval = 'display';
		var displaydiv = document.getElementById(displaydivval).innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
	function Popup(data) 
	{
		var mywindow = window.open('', 'Arrendance Status Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Arrendance Status Report</title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
	}
	function exporttocsv(divid)
	{
		//alert(divid);
			//getting values of current time for generating the file name
			/*var dt = new Date();
			var day = dt.getDate();
			var month = dt.getMonth() + 1;
			var year = dt.getFullYear();
			var hour = dt.getHours();
			var mins = dt.getMinutes();
			var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;*/
			
			/*document.write('<div id=\'view\'>');
			document.write(view);
			document.write('<div>');*/
			//creating a temporary HTML link element (they support setting file names)*/
			/*var a = document.createElement('a');
			//getting data from our div that contains the HTML table
			var data_type = 'data:application/vnd.ms-excel';
			var table_div = document.getElementById('display');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = 'PJP Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();*/
		var employee = document.getElementById("employee").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		window.open('pjp_data_daywise_export.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'mywindow')	;
	}
	function exporttocsvpjp(divid)
	{
		/*var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;*/
		
		/*document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');*/
		//creating a temporary HTML link element (they support setting file names)*/
		/*var a = document.createElement('a');
		//getting data from our div that contains the HTML table
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'PJP Report' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();*/
		var employee = document.getElementById("employee").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		window.open('pjp_data_empwise_daywise_export.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'mywindow')	;
	}
	</script>
    <?php
if($_REQUEST['mode']=='att_status_update')
{
	$emp_date=$_REQUEST['emp_date'];
	//print_r($dono);
	for($i=0;$i<=count($emp_date);$i++)
	{
	  $emp_date_val=explode('_',$emp_date[$i]);
	  $emp_code=$emp_date_val[0];
	  $att_date=$emp_date_val[1];
	 
	  $status_val=$_REQUEST["status_".$emp_date[$i]];
	  $remarks_val=$_REQUEST["remarks_".$emp_date[$i]];
	  //echo $prod_val[$i];
	  //exit();
	  if($status_val!=''){
		$upd_sql="UPDATE location SET att_status ='$status_val',att_remarks ='$remarks_val' 
					WHERE emp_code = '" .$emp_code."' AND SUBSTRING(date,1,10)='".$att_date."' AND trans_id LIKE 'A%'";
		mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in att status update.");
	  }
	  
	}
		?>
         <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR">Status updated successfully</td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
        <?php
	//$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";
	//disphtml("main();");
  }// end main
}

?>