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
</style> 
</head>
<body >
<center><br /><div id="display">
<?php
$sqldetention= "SELECT * FROM (SELECT DC.prod_code,DC.branch_code, PM.prod_desc,DATE_FORMAT(SUBSTRING(DC.datetime,1,10),'%d-%m-%Y') As last_updated_date,DC.detention_cost,BM.branch_name,BM.dns_branch_code,DC.detention_cost_ton FROM
				detention_cost DC,product_master PM,branch_master BM WHERE DC.prod_code=PM.dns_prod_code AND 
				DC.branch_code=BM.branch_code AND PM.acedns='Y' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' ORDER BY DC.datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 6";
$rsdetention = mysql_query($sqldetention);
$total_detention = mysql_num_rows($rsdetention);
$count = 1;
if($total_detention>0){
	?>
    <table width="90%" border="0" style="border-collapse:collapse;" class="datatable1" cellpadding="4">
    <thead>
     <tr>
        <td colspan="9" class="TDHEAD" align="center">Detention Cost</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="5%">SI</td>
        <td width="10%">Date of Upload</td>
        <td width="12%">Depot Code</td>
        <td width="16%">Depot Name</td>
        <td width="13%">Material Code</td>
        <td width="20%">Material Description</td>
        <td width="12%">Detention Charges (Rs/CASE)</td>
         <td width="12%">Detention Charges (Rs/MT)</td>
      </tr>
      </thead>
      </table>
      <table width="90%" border="1" style="border-collapse:collapse;" class="datatable1" cellpadding="4">
      <tbody>
    <?php
	while($row_detention = mysql_fetch_array($rsdetention)){
		$dns_prod_code = $row_detention['prod_code'];
		$prod_desc = str_replace(',','',$row_detention['prod_desc']);
		$dns_branch_code = $row_detention['dns_branch_code'];
		$branch_name = $row_detention['branch_name'];
		$detention_cost = $row_detention['detention_cost'];
		$detention_cost_ton = $row_detention['detention_cost_ton'];
		$last_updated_date = $row_detention['last_updated_date'];
		if($detention_cost >0){
		echo "<tr>
				<td width=\"5%\">".$count."</td>
				<td width=\"10%\">".$last_updated_date."</td>
				<td width=\"12%\">".$dns_branch_code."</td>
				<td width=\"16%\">".$branch_name."</td>
				<td width=\"13%\">".$dns_prod_code."</td>
				<td width=\"20%\">".$prod_desc."</td>
				<td align=\"right\" width=\"12%\">".number_format($detention_cost,2)."</td>
				<td align=\"right\" width=\"12%\">".number_format($detention_cost_ton,2)."</td>
			  </tr>";	  
		$count++;
		}
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
	a.download = 'Detention data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Detention Data</title>');
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
