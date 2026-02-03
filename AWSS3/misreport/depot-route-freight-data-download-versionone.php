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
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<body >
<center><br /><div id="display">
<?php
$sqlfreight=" SELECT * from (SELECT BM.dns_branch_code, RM.route_name, BRF.freight, BRF.acedns,BRF.transport_mode,BRF.capacity,BM.branch_name FROM branch_route_freight BRF, branch_master BM, route_master RM WHERE BM.branch_code = BRF.branch_code AND RM.route_code = BRF.route_code AND 
BRF.acedns = 'Y'  ORDER BY BRF.date DESC) alias GROUP BY 1,2,5,6 ORDER BY 2 DESC";
$resfreight = mysql_query($sqlfreight);
$total_freight = mysql_num_rows($resfreight);
$count = 1;
if($total_freight>0){
	?>
    <table width="60%" border="1"  cellpadding="4" align="center">
          <thead>
     <tr>
        <td colspan="12" class="TDHEAD" align="center">Depot Route Freight</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center" style="border-collapse:collapse">
          <td width="6%">SI</td>
          <td width="14%">Depot code</td>
           <td width="19%">Depot Name</td>
          <td width="20%">Route</td>
          <td width="10%">Freight</td>
          <td width="7%">acedns</td>
          <td width="14%">Transport mode</td>
          <td width="10%">Capacity</td>
      </tr>
      </thead>
      <tbody>
    <?php
	while($row_freight = mysql_fetch_array($resfreight)){
			$branch_code = $row_freight['dns_branch_code'];
			$route_name =preg_replace('/[\r\n]+/', '',$row_freight['route_name']);
			$date = date('d/m/Y',strtotime($row_freight['date']));
			$freight = $row_freight['freight'];
			$acedns = $row_freight['acedns'];
			$transport_mode = $row_freight['transport_mode'];
			$capacity = $row_freight['capacity'];
			$branch_name=$row_freight['branch_name'];

		echo "<tr>
				<td width='6%'>".$count."</td>
				<td width=\"9%\">".$branch_code."</td>
				<td width=\"11%\">".$branch_name."</td>
				<td width=\"12%\">".$route_name."</td>
				<td align=\"right\" width=\"9%\">".number_format($freight,2)."</td>
				<td width=\"7%\">".$acedns."</td>
				<td width=\"10%\">".$transport_mode."</td>
				<td width=\"8%\">".$capacity."</td>
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
	a.download = 'Depot route freight data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Depot Route Freight Data</title>');
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
