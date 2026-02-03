<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	$current_date_array = explode("-",$current_date);
	$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	
	if($_SESSION['admin_login']=="admin")
	{
		//$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 ";
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.")";
	}
	
?><head>
     <style>
	.datatable{
	  width:80%;
	  table-layout: fixed;
	  }
	.tbl-header{
	  background-color: rgba(255,255,255,0.3);
	 }
	.tbl-content{
	  height:300px;
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
	  text-align: left;
	  vertical-align:middle;
	  font-weight: 300;
	  font-size: 12px;
	  color: #000000;
	  overflow-wrap: break-word;
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

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<body >
<center><br />
	<div>
    <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
    <table cellpadding="4">
          <tr class="TDHEAD">
          	<td colspan="2" align="center" style="font-weight:bold;">Search Criteria </td>
          </tr>
           <tr class="TDHEAD_SUB" >
            <td align="right" width="40%">Choose Date:</td>
            <td width="">
                    <?php $from_date=$_REQUEST['from_date'];?>
                  <input type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date" id="from_date"></input>&nbsp;
                    <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" 
                    width="20" height="18" ></a>
                </label>
                <script language="JavaScript" type="text/javascript">
                    <!-- // create calendar object(s) just after form tag closed
                     // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                     // note: you can have as many calendar objects as you need for your application
                    var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                    cal5.year_scroll = true;
                    cal5.time_comp = false;
                    //-->
                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!--input name="submit" type="button" value="Submit" onclick="return_result(from_date.value);" /-->
            </td>
       </tr> 
         <tr class="TDHEAD_SUB">
          	<td></td>
            <td>               
        		<input name="submit" type="button" value="Submit" id="submit" onClick="submitdata(from_date.value);">
             </td>    
           </tr>
        </table>
        </form>       
    </div>
    <br />
    <div id="display" ></div>
    <div style="width:60%;" align="right">
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
    </div>
</center>
</body>
<script>
	function GetXmlHttpObject()
	{
		var xmlHttp=null;
		try
		{
			// Firefox, Opera 8.0+, Safari
			xmlHttp=new XMLHttpRequest();
		}
	
	
		catch (e)
		{
			// Internet Explorer
			try
			{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		return xmlHttp;
	}

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
	a.download = 'Customer sauda limit' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Stock Audit', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Stock Audit</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function submitdata(from_date){
	xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
	if(document.getElementById("from_date").value.search(/\S/) == -1)
	{
		alert("Please choose date");
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	var url="customer_sauda_limit_update_report_data.php";
	xmlHttp.onreadystatechange=customersaudalimit;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send("from_date="+from_date);
	//GenericAjaxFunction('customer_wise_sauda_limit_data.php?emp_code='+emp_code+'&cust_code='+cust_name,'display',0);
}
function customersaudalimit()
 {
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("display").innerHTML =val; 
	 }
 }
</script>
<?php
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>