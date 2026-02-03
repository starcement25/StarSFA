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
$sqlloaddistribution= "SELECT * FROM (SELECT LD.transport_mode,LD.truck_load,LD.qty_truck_load,
					DATE_FORMAT(SUBSTRING(LD.datetime,1,10),'%d-%m-%Y') As last_updated_date,LD.product_group_code,LD.prod_code,PM.prod_desc
						FROM
					load_distribution LD,product_master PM WHERE LD.prod_code=PM.dns_prod_code AND 
					LD.vertical_value='".$_SESSION['vertical_value']."' ORDER BY LD.datetime DESC) AS SAT GROUP BY 1,2,6 ORDER BY 4";
$rsloaddistribution = mysqli_query($link,$sqlloaddistribution);
$total_loaddistribution = mysqli_num_rows($rsloaddistribution);
$count = 1;
if($total_loaddistribution>0){
	?>
    <table width="80%" border="1" style="border-collapse:collapse;"  cellpadding="4">
    <thead>
     <tr>
        <td colspan="12" class="TDHEAD" align="center">Load Distribution</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="5%">SI</td>
        <td width="8%">Date of Upload</td>
        <!--td width="8%">Plant Name</td-->
        <td width="14%">Transport mode</td>
        <td width="9%">Load capacity</td>
         <!--td width="9%">Oil group</td>
        <td width="4%">Pack type</td-->
         <td width="8%">Material Code</td>
        <td width="13%">Material Description</td>
         <td width="10%">Truck load qty<br />(No. of Cases/Tins)</td>
      </tr>
       </thead>
    <?php
	while($row_load_distribution = mysqli_fetch_assoc($rsloaddistribution)){
		//$plant = $row_load_distribution['plant_name'];
		//$branch_name = $row_load_distribution['branch_name'];
		//$dns_branch_code = $row_load_distribution['dns_branch_code'];
		$transport_mode=$row_load_distribution['transport_mode'];
		$truck_load = $row_load_distribution['truck_load'];
		$qty_truck_load= floor($row_load_distribution['qty_truck_load']);
		//$pack_type= $row_load_distribution['pack_type'];
		//$product_group_code= $row_load_distribution['product_group_code'];
		$prod_code= $row_load_distribution['prod_code'];
		$prod_desc= $row_load_distribution['prod_desc'];
		$last_updated_date = $row_load_distribution['last_updated_date'];
		
		$sqloilgroup="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$product_group_code."'";
		$rsoilgroup=mysqli_query($link,$sqloilgroup);
		$rowoilgroup=mysqli_fetch_assoc($rsoilgroup);
		$product_group_name=$rowoilgroup['product_group_name'];

		echo "<tr>
				<td width=\"5%\">".$count."</td>
				<td width=\"8%\">".$last_updated_date."</td>
				<td width=\"14%\">".$transport_mode."</td>
				<td width=\"9%\">".$truck_load."</td>
				<td width=\"8%\">".$prod_code."</td>
				<td width=\"13%\">".$prod_desc."</td>
				<td align=\"right\" width=\"10%\">".number_format($qty_truck_load,2)."</td>
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
	a.download = 'Load Distribution Data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Load Distribution Data</title>');
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
