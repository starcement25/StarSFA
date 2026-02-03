<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$mode = $_REQUEST['mode'];

	if(!$_GET)
	{
	disphtml("main();");
	}
	else
	{
		csvexport();
	}
ob_end_flush();

function main()
{
  if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND emp_code IN('.$emp_hierarchy.')';
	}
	/*------------------------------> Select of month (financial year)<-------------------------------*/
	$current_month_year = date('M')."-".date('Y');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$current_year = date('Y');
		$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
	}
	else{
		$previous_year = date('Y');
		$current_year = date('Y', strtotime('+1 year'));
		$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
	}
?>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script language="javascript">
function check()
{
	if (document.frmSearch.emp_name.value==0) 
	{
		alert('Please select a employee.');
		document.frmSearch.emp_name.focus();
		return false;
	}
	if(document.frmSearch.from_date.value.search(/\S/)==0)
	{
		if(document.frmSearch.to_date.value.search(/\S/)==-1)
		{
			alert('Please input a value for To Date.');
			document.frmSearch.to_date.focus();
			return false;
		}
	}
	return true;
}
</script>
 <style>
.datatable{
  width:98%;
  table-layout: fixed;
  }
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:500px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
.datatable th{
  padding: 20px 15px;
  text-align: left;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
.datatable td{
  padding: 15px;
  text-align: left;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #000000;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}
/* demo styles */
/* for custom scrollbar for webkit browser*/
::-webkit-scrollbar {
    width: 6px;
} 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
} 
::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
}
</style>

</head>
<body>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >Stock Analysis</strong></td>
	</tr>
    
	<tr>
		<td valign="top" bgcolor="#FFFFFF" >
                <table width="80%" align="center" border="0" cellpadding="5" cellspacing="1" >
                    <tr> 
                        <td align="center" class="ERR"><? echo stripslashes($GLOBALS['err_msg']);?></td>
                        <td align="right" colspan="2"></td>
                    </tr>
                </table>
               
                <!--------------------------------Start Table for first time page loading---------------------------------!-->
                
                <table width="65%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
                	<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return check();">
					<input type="hidden" name="mode" value="visit_frequency_display">
                    <tr class="TDHEAD" > 
                        <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
                    </tr>
                    <tr class="TDHEAD_SUB">
                        <td align="right">Month:</td>
                        <td align="left">
                            <select name="month_select" id="month_select">
                            <option value="">Select</option>
                            <?php
                            foreach($months as $monthvalue){
                                echo "<option>".$monthvalue."</option>";
                                if($monthvalue == $current_month_year)
                                    break;
                            }
                            ?>
                            </select>
                        </td>
                      </tr>
                      <tr class="TDHEAD_SUB">
                        <td align="right">Employee:</td>
                        <td align="left">
                            <select name="employee" id="employee" onChange="javascript:emp_dealer(this.value);">
                                <option value="">Select</option>
                                <?php
                                $sql_empl = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM 
								WHERE acedns='Y' ".$emp_hierarchy_condition." ORDER BY EM.emp_name ASC";
                                $res_empl = mysqli_query($link,$sql_empl);
                                while($row_empl = mysqli_fetch_assoc($res_empl)){
                                    echo "<option value=\"".$row_empl['emp_code']."\">".$row_empl['emp_name']."</option>";
                                }
                                ?>
                             </select>
                        </td>
                        </tr>
                        <tr class="TDHEAD_SUB">
                            <td align="right">Dealer:</td>
                            <td align="left"><div id="dealer_div"></div></td>
                        </tr>
                    	 <tr class="TDHEAD_SUB">
                             <td align="center" width="" colspan="2">
                                <input type="button" value="Submit" class="inplogin" name="submit" onClick="display_result();">
                                <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                            </td>
                            </tr>
                		</table> 
                      </tr>
                      </form>
                     </table> 
                     <br />
                     <center>
       <div id="display"  align="center" ></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="center" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
</center>
	<script language="javascript" type="text/javascript">
	 function emp_dealer(emp_code){
		var employee = encodeURIComponent(emp_code);
		var month = document.getElementById("month_select").value;
		//alert(employee);
		document.getElementById("dealer_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_empdealer_data.php?employee='+employee+'&month='+month+'&type=stockanalysis','dealer_div',0);
		//GenericAjaxFunction('get_dealer_data.php','dealer_div',0);
		document.getElementById("display").innerHTML = '';
	}
	function display_result(){
		var month = document.getElementById("month_select").value;
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Provide month');
			return false;
		}
		var emp_code = document.getElementById("employee").value;
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Provide employee name');
			return false;
		}
		var dealer = document.getElementById("dealercontrol").value;
		if(document.getElementById("dealercontrol").value.search(/\S/) == -1){
			alert('Provide Dealer');
			return false;
		}
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('stockAnalysisReportDataValue.php?emp_code='+emp_code+'&month='+month+'&dealer='+dealer,'display',0);
		document.getElementById("print_export").hidden = false;
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
	function Popup(data) 
	{
		var mywindow = window.open('', 'Customer Visit Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Stock Analysis Report</title>');
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
	
	/*function exporttocsv(divid)
	{
		var get_report_name = document.getElementById("report_name").value
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var a = document.createElement('a');
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		a.download = 'Customer Visit Report' + postfix + '.xls';
		a.click();
	}*/
	function exporttocsv()
	{
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var a = document.createElement('a');
		//getting data from our div that contains the HTML table
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'Stock Analysis Report' + postfix + '.xls';
		//triggering the function
		a.click();
		//just in case, prevent default behaviour
		e.preventDefault();
	}
        </script>
        <br />
 </body>       
 <?php
}
?>