<?php
ob_start();
session_start();
 require("adminUtils.php");
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
    <!--<script src="tableToExcel.js"></script>>
    <link rel="stylesheet" href="table.css" type="text/css"/>
    <style>
	.datatable1 {
		max-width:1100px;
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
	</style--> 
</head>
<body>
<center><br /><div id="display">
<?php
$sqlprocesscost= "SELECT * FROM (SELECT oil_type,plant_name,process_cost,DATE_FORMAT(SUBSTRING(datetime,1,10),'%d-%m-%Y') As last_updated_date,
					oil_category FROM
				process_cost ORDER BY datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 4 DESC,1 ASC";				
$rsprocesscost = mysql_query($sqlprocesscost);
$total_processcost = mysql_num_rows($rsprocesscost);
$count = 1;
if($total_processcost>0){
	?>
    <table width="60%" border="1" style="border-collapse:collapse;"  cellpadding="4">
    <thead>
     <tr>
        <td colspan="6" class="TDHEAD" align="center">Process Cost</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="8%">SI</td>
        <td width="20%">Date of Upload</td>
        <td width="24%">Oil Category</td>
        <td width="23%">Oil type</td>
        <td width="10%">Process Cost (Rs/MT)</td>
        <td width="15%">Plant name</td>
      </tr>
      </thead>
    <?php
	while($row_processcost = mysql_fetch_array($rsprocesscost)){
		$oil_type = $row_processcost['oil_type'];
		$oil_category = $row_processcost['oil_category'];
		$plant_name = $row_processcost['plant_name'];
		$process_cost = $row_processcost['process_cost'];
		$last_updated_date = $row_processcost['last_updated_date'];
		echo "<tr>
				<td width=\"8%\">".$count."</td>
				<td width=\"20%\">".$last_updated_date."</td>
				<td width=\"24%\">".$oil_category."</td>
				<td width=\"23%\">".$oil_type."</td>
				<td width=\"10%\" align=\"right\">".$process_cost."</td>
				<td width=\"15%\">".$plant_name."</td>
			  </tr>";
		$count++;
	}
?>
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
	a.download = 'Process cost data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Process Cost Data</title>');
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
