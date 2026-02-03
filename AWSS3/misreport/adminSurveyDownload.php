<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	ob_end_flush();
	$mode = $_REQUEST['mode'];

	if($mode =='excel_download')		excelDownload();
	else  disphtml("main();");
ob_end_flush();

function main()
{
?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>

<script language="javascript">
function display_result()
{
	if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
		alert("Please provide start date/end date");
		return false;
	}
	if(document.getElementById("start_date").value>document.getElementById("end_date").value){
		alert("Start date cannot be greater than end date");
		return false;
	}
	if(document.getElementById("select_customer").value.search(/\S/) == -1){
		alert('Please Select Customer');
		return false;
	}
	var start_date = document.getElementById("start_date").value
	var end_date = document.getElementById("end_date").value

	var customer = document.getElementById("select_customer").value;
	document.getElementById("display_details").innerHTML = '';
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('surveyDownloadData.php?customer='+customer+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	document.getElementById("print_export").hidden = false;
}
function select_datewise_customer(){
	var start_date = document.getElementById("start_date").value
	var end_date = document.getElementById("end_date").value
	document.getElementById("customer_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('selectSurveyCustomer.php?start_date='+start_date+'&end_date='+end_date,'customer_select_div',0);
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
		mywindow.document.write('<html><head><title>Survey Report</title>');
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
		//var get_report_name = document.getElementById("report_name").value
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
		a.download = 'Survey Report' + postfix + '.xls';
		a.click();
	}
</script>
<center>
<table width="80%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >>Survey Report</strong></td>
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
            <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>">
            <tr class="TDHEAD" > 
                <td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
            </tr>
            <tr><td colspan="2" align="center"><div id="date_div"  >
            From:<input type="date" name="start_date" id="start_date" style="height:15px;" />
            To:<input type="date" name="end_date" id="end_date" style="height:15px;" onChange="select_datewise_customer();"/>
            </div></td>
            	</tr>
                <!--tr>
                        <td colspan="4" align="center">Select Employee:<select name="search_emp_name">
                        <option value="">Select</option>
                        <option value="all">All</option>                                
                        <?php
                        /*$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
                        $res_select_emp = mysql_query($sql_select_emp);
                        while($row_select_emp = mysql_fetch_array($res_select_emp))
                        {
                            if($_POST['search_emp_name'] == $row_select_emp['emp_code'])
                                echo "<option value='$row_select_emp[emp_code]' selected>".$row_select_emp['emp_name']."</option>";
                            else
                                echo "<option value='$row_select_emp[emp_code]'>".$row_select_emp['emp_name']."</option>";
                        }*/
                        ?>
                        </select>
                        </td>
                    </tr-->
                   <tr>
                        <td  align="right" width="50%">Select Customer:</td><td width=""><div id="customer_select_div"></div></td>
                    </tr>
                    <tr>
                         <td align="center" width="" style="padding-left:10px;" colspan="4">
                            <input type="button" value="Submit" class="inplogin" onclick="javascript:display_result();">
                            <!--input name="btnShowAll" type="button" class="inplogin" value="Show All" onClick="javascript:show_all();"--> 
                        </td>
                    </tr>
                    </form>
                </table></td>
              </tr>
     </table> 
      <br />
     <div id="display" style="max-height: 450px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    	<div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    	<div style="width:80%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    	<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
	</div>
    </center>
<?php }//End of main()?>	