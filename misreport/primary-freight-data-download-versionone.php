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
<body >
<center><br /><div id="display">
<?php
$sqlprimaryfreight= "SELECT * FROM (SELECT FC.dns_prod_code,FC.branch_code,PM.prod_desc,
					DATE_FORMAT(SUBSTRING(FC.datetime,1,10),'%d-%m-%Y') As last_updated_date,FC.freight_cost,BM.branch_name,BM.dns_branch_code,
					FC.hire_cost,FC.datetime,BM.plant_code,BM.plant_name,FC.transport_mode,BM.branch_state,FC.truck_load FROM
					freight_cost FC,product_master PM,branch_master BM WHERE FC.dns_prod_code=PM.dns_prod_code AND 
					FC.branch_code=BM.branch_code AND PM.acedns='Y' AND FC.freight_cost >0 ORDER BY FC.datetime DESC) 
					AS SAT GROUP BY 1,2 ORDER BY 9 DESC,7 ASC";
$rsprimaryfreight = mysqli_query($link,$sqlprimaryfreight);
$total_primaryfreight = mysqli_num_rows($rsprimaryfreight);
$count = 1;
if($total_primaryfreight>0){
	?>
    <table width="94%" border="1" style="border-collapse:collapse;" cellpadding="4">
    <thead>
     <tr>
        <td colspan="14" class="TDHEAD" align="center">Primary Freight</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="5%">SI</td>
        <td width="10%">Date of Upload</td>
        <td width="8%">Material Code</td>
        <td width="11%">Material Description</td>
        <td width="10%%">Plant Name</td>
        <td width="8%">Depot Code</td>
         <td width="10%">Depot Name</td>
        <td width="10%">Primary Freight<br />(RS/CASE)</td>
        <td width="10%">Hire Cost<br />(RS/TON)</td>
        <td width="9%">Transport mode</td>
        <td width="9%">Load Capacity</td>
      </tr>
      </thead>
    <?php
	while($row_primaryfreight = mysqli_fetch_assoc($rsprimaryfreight)){
		$dns_prod_code = $row_primaryfreight['dns_prod_code'];
		$prod_desc = str_replace(',','',$row_primaryfreight['prod_desc']);
		$branch_name = $row_primaryfreight['branch_name'];
		$dns_branch_code = $row_primaryfreight['dns_branch_code'];
		$plant_code = $row_primaryfreight['plant_code'];
		$plant_name = $row_primaryfreight['plant_name'];
		$freight_cost = $row_primaryfreight['freight_cost'];
		$hire_cost = $row_primaryfreight['hire_cost'];
		$last_updated_date = $row_primaryfreight['last_updated_date'];
		$hire_cost = $row_primaryfreight['hire_cost'];
		$transport_mode = $row_primaryfreight['transport_mode'];
		$branch_state = $row_primaryfreight['branch_state'];
		$truck_load = $row_primaryfreight['truck_load'];
		
		echo "<tr>
				<td width=\"5%\">".$count."</td>
				<td width=\"10%\">".$last_updated_date."</td>
				<td width=\"8%\">".$dns_prod_code."</td>
				<td width=\"11%\">".$prod_desc."</td>
				<td width=\"10%\">".$plant_name."</td>
				<td width=\"8%\">".$dns_branch_code."</td>
				<td width=\"10%\">".$branch_name."</td>
				<td align=\"right\" width=\"10%\">".number_format($freight_cost,2)."</td>
				<td align=\"right\" width=\"10%\">".number_format($hire_cost,2)."</td>
				<td width=\"9%\">".$transport_mode."</td>
				<td width=\"9%\">".$truck_load."</td>
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
	a.download = 'Primar freight data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Primary Freight Data</title>');
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
mysqli_close($link);
?>
