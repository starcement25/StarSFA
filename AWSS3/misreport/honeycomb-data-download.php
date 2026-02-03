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
/*$sqlhoneycomb= "SELECT * FROM (SELECT HC.prod_code,HC.branch_code, PM.prod_desc,PM.pack_type,DATE_FORMAT(SUBSTRING(HC.datetime,1,10),'%d-%m-%Y') As last_updated_date,BM.plant_name,HC.honeycomb_cost,BM.branch_name,HC.transport_mode,BM.dns_branch_code,BM.plant_code,HC.honeycomb_cost_ton,BM.branch_state FROM
				honeycomb_cost HC,product_master PM,branch_master BM WHERE HC.prod_code=PM.dns_prod_code AND 
				HC.branch_code=BM.branch_code AND PM.acedns='Y' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' ORDER BY HC.datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 4 DESC";*/
$sqlhoneycomb= "SELECT * FROM (SELECT HC.prod_code,HC.state_code,HC.plant_name, PM.prod_desc,PM.pack_type,DATE_FORMAT(SUBSTRING(HC.datetime,1,10),'%d-%m-%Y') As last_updated_date,HC.honeycomb_cost,HC.transport_mode,HC.honeycomb_cost_ton,SM.state FROM
				honeycomb_cost HC,product_master PM,state_master SM WHERE HC.prod_code=PM.dns_prod_code AND 
				HC.state_code=SM.dns_state_code AND PM.acedns='Y' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' ORDER BY HC.datetime DESC) AS SAT GROUP BY 1,2,3 ORDER BY 4 DESC";				
$rshoneycomb = mysql_query($sqlhoneycomb);
$total_honeycomb = mysql_num_rows($rshoneycomb);
$count = 1;
if($total_honeycomb>0){
	?>
    <table width="80%" border="0" style="border-collapse:collapse;" class="datatable1" cellpadding="4">
    <thead>
     <tr>
        <td colspan="13" class="TDHEAD" align="center">Honeycomb Cost</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="4%">SI</td>
        <td width="7%">Date of Upload</td>
        <td width="8%">Material Code</td>
        <td width="11%">Material Description</td>
        <td width="8%">Plant Code</td>
        <td width="10%">Plant Name</td>
        <!--td width="6%">Depot Code</td>
         <td width="10%">Depot Name</td-->
        <td width="10%">State Code</td>
        <td width="13%">State Name</td>
        <td width="9%">Honeycomb Cost (Rs/CASE)</td>
        <td width="10%">Honeycomb Cost (Rs/MT)</td>
        <td width="10%">Transport mode</td>
      </tr>
      </thead>
      </table>
      <table width="80%" border="1" style="border-collapse:collapse;" class="datatable1" cellpadding="4">
      <tbody>
    <?php
	while($row_honeycomb = mysql_fetch_array($rshoneycomb)){
		$dns_prod_code = $row_honeycomb['prod_code'];
		$prod_desc = str_replace(',','',$row_honeycomb['prod_desc']);
		$transport_mode = $row_honeycomb['transport_mode'];
		$product_group_name = $row_honeycomb['product_group_name'];
		$plant = $row_honeycomb['plant_name'];
		$plant_code=$row_honeycomb['plant_code'];
		$state=$row_honeycomb['branch_state'];
		$branch_name = $row_honeycomb['branch_name'];
		$dns_branch_code = $row_honeycomb['dns_branch_code'];
		$honeycomb_cost = $row_honeycomb['honeycomb_cost'];
		$honeycomb_cost_ton = $row_honeycomb['honeycomb_cost_ton'];
		 //$pack_type= $row_honeycomb['pack_type'];
		$last_updated_date = $row_honeycomb['last_updated_date'];
		$sqlplantcode="SELECT plant_code FROM branch_master WHERE plant_name='".addslashes($plant)."'";
		$rsplantcode=mysql_query($sqlplantcode);
		$rowplantcode=mysql_fetch_array($rsplantcode);
		$plant_code = $rowplantcode['plant_code'];
		$state_code=$row_honeycomb['state_code'];
		$state_name=$row_honeycomb['state'];
		echo "<tr>
				<td width=\"4%\">".$count."</td>
				<td width=\"7%\">".$last_updated_date."</td>
				<td width=\"8%\">".$dns_prod_code."</td>
				<td width=\"11%\">".$prod_desc."</td>
				<td width=\"8%\">".$plant_code."</td>
				<td width=\"10%\">".$plant."</td>
				<td width=\"10%\">".$state_code."</td>
				<td width=\"13%\">".$state_name."</td>
				<td align=\"right\" width=\"9%\">".number_format($honeycomb_cost,2)."</td>
				<td align=\"right\" width=\"10%\">".number_format($honeycomb_cost_ton,2)."</td>
				<td width=\"10%\">".$transport_mode."</td>
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
	a.download = 'Honeycomb data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Honeycomb Data</title>');
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
