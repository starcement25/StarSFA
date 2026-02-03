<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
?>
 <head>
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
<style>
.datatable1 {
	width:98%;
	table-layout:fixed;
	margin:auto;
}
.datatable1 th, td {
	padding:5px 10px;
}
.datatable1 thead, tfoot {
	background:#f9f9f9;
	display:table;
	width:100%;
	width:calc(100% - 19px);

}
.datatable1 tbody {
	height:500px;
	overflow:auto;
	overflow-x:hidden;
	display:block;
	width:100%;
}
.datatable1 tbody tr {
	display:table;
	width:100%;
	table-layout:fixed;
}

</style> 
</head>
<body >
<center><br /><div id="display">
<?php
$sqlbroker= "SELECT dns_broker_id,broker_name,brokerage_cost,acedns,DATE_FORMAT(SUBSTRING(download_time,1,10),'%d-%m-%Y') As last_updated_date,state_code FROM broker_master ORDER BY download_time ASC";
$rsbroker = mysql_query($sqlbroker);
$total_broker = mysql_num_rows($rsbroker);
$count = 1;
if($total_broker>0){
	?>
    <table width="90%" border="0" style="border-collapse:collapse;" class="datatable1" cellpadding="4">
    <thead>
     <tr>
        <td colspan="9" class="TDHEAD" align="center">Brokerage Report</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="7%">SI</td>
        <td width="13%">Date of Upload</td>
        <td width="10%">Broker Code</td>
        <td width="20%">Broker Name</td>
        <td width="10%">State code</td>
        <td width="20%">State Name</td>
        <td width="10%">Broker cost (Rs/unit)</td>
        <td width="10%">Active/Inactive</td>
      </tr>
     </thead>
     </table>
     <table width="90%" border="1" style="border-collapse:collapse;" class="datatable1" cellpadding="4"> 
     <tbody>
    <?php
	while($row_broker = mysql_fetch_array($rsbroker)){
		$dns_broker_id = $row_broker['dns_broker_id'];
		$broker_name = $row_broker['broker_name'];
		$brokerage_cost = $row_broker['brokerage_cost'];
		$acedns = $row_broker['acedns'];
		$state_code = $row_broker['state_code'];
		$last_updated_date = $row_broker['last_updated_date'];
		if($acedns=='N'){
			$color="#f44242";
		}
		else $color="";
		$sqlstatename="SELECT state FROM state_master WHERE dns_state_code='".$state_code."'";
		$rsstatename=mysql_query($sqlstatename);
		$rowstatename=mysql_fetch_array($rsstatename);
		$state_name=$rowstatename['state'];
		echo "<tr align=\"center\">
				<td style=\"background:$color;\" width=\"7%\">".$count."</td>
				<td style=\"background:$color;\" width=\"13%\">".$last_updated_date."</td>
				<td style=\"background:$color;\" width=\"10%\">".$dns_broker_id."</td>
				<td style=\"background:$color;\" width=\"20%\">".$broker_name."</td>
				<td style=\"background:$color;\" width=\"10%\">".$state_code."</td>
				<td style=\"background:$color;\" width=\"20%\">".$state_name."</td>
				<td align=\"right\" style=\"background:$color;\" width=\"10%\">".number_format($brokerage_cost,2)."</td>
				<td style=\"background:$color;\" width=\"10%\">".$acedns."</td>
			  </tr>";	  
		$count++;
	}
?>
</tbody>
</table>
</div><br />
  <!--div id="display" style="max-height: 400px; width:95%; overflow-y: scroll;" align="center"></div>
 <br /-->
   <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
    </center>
</body>
 <script>
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
	a.download = 'Brokerage report data' + postfix + '.xls';
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
	var mywindow = window.open('', 'Packing Data', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Brokerage Data</title>');
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
</script>
<?php	
}
else{
	echo "<strong><font color=\"red\">No records found</font></strong>";
}
}
mysql_close($link);
?>
