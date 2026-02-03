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
/*$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.branch_code, PM.prod_desc,PM.pack_size,
				DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As last_updated_date,PM.product_group_code,BM.plant_name,PGM.product_group_name,MC.margin_cost,BM.branch_name,BM.dns_branch_code,BM.plant_code,
				MC.margin_cost_ton,BM.branch_state FROM
				margin_cost MC,product_master PM,product_group_master PGM,branch_master BM WHERE MC.dns_prod_code=PM.dns_prod_code AND PM.product_group_code=PGM.product_group_code AND MC.branch_code=BM.branch_code AND PM.acedns='Y' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 10,6";*/
$sqlmargin= "SELECT * FROM (SELECT MC.dns_prod_code,MC.plant_name,PM.prod_desc,PM.pack_size,
				DATE_FORMAT(SUBSTRING(MC.datetime,1,10),'%d-%m-%Y') As last_updated_date,PM.product_group_code,PGM.product_group_name,MC.margin_cost,
				MC.margin_cost_ton,MC.state_code FROM
				margin_cost MC,product_master PM,product_group_master PGM 
				WHERE MC.dns_prod_code=PM.dns_prod_code AND PM.product_group_code=PGM.product_group_code 
				AND PM.acedns='Y'  ORDER BY MC.datetime DESC) AS SAT GROUP BY 1,2 ORDER BY 2,6";				
$rsmargin = mysql_query($sqlmargin);
$total_margin = mysql_num_rows($rsmargin);
$count = 1;
if($total_margin>0){
	?>
    <table width="78%" border="1" style="border-collapse:collapse;"  cellpadding="4">
    <thead>
     <tr>
        <td colspan="14" class="TDHEAD" align="center">Margin Cost</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td width="5%">SI</td>
        <td width="10%">Date of Upload</td>
        <td width="9%">Material Code</td>
        <td width="22%">Material Description</td>
        <td width="22%">Product Group</td>
        <td width="9%">Pack Size (BP/CP)</td>
        <!--td width="7%">Plant Code</td>
        <td width="9%">Plant Name</td>
        <!--td width="7%">Depot Code</td>
        <td width="10%">Depot Name</td>
        <td width="8%">State Code</td>
        <td width="18%">State Name</td-->
        <td width="12%">Margin Cost (Rs/Case)</td>
        <td width="11%">Margin Cost (Rs/MT)</td>
      </tr>
      </thead>
      
    <?php
	while($row_margin = mysql_fetch_array($rsmargin)){
		$dns_prod_code = $row_margin['dns_prod_code'];
		$prod_desc = str_replace(',','',$row_margin['prod_desc']);
		$pack_size = $row_margin['pack_size'];
		$product_group_name = $row_margin['product_group_name'];
		$plant = $row_margin['plant_name'];
		//$plant_code = $row_margin['plant_code'];
		//$branch_name = $row_margin['branch_name'];
		//$dns_branch_code = $row_margin['dns_branch_code'];
		//$branch_state = $row_margin['branch_state'];
		$margin_cost = $row_margin['margin_cost'];
		$margin_cost_ton = $row_margin['margin_cost_ton'];
		$last_updated_date = $row_margin['last_updated_date'];
		/*$sqlstatecode="SELECT dns_state_code FROM state_master WHERE state='".addslashes($branch_state)."'";
		$rsstatecode=mysql_query($sqlstatecode);
		$rowstatecode=mysql_fetch_array($rsstatecode);*/
		/*$sqlplantcode="SELECT plant_code FROM branch_master WHERE plant_name='".addslashes($plant)."'";
		$rsplantcode=mysql_query($sqlplantcode);
		$rowplantcode=mysql_fetch_array($rsplantcode);
		$plant_code = $rowplantcode['plant_code'];*/
		$state_code=$row_margin['state_code'];
		//$state_name=$row_margin['state'];
		echo "<tr>
				<td width=\"5%\">".$count."</td>
				<td width=\"10%\">".$last_updated_date."</td>
				<td width=\"9%\">".$dns_prod_code."</td>
				<td width=\"22%\">".$prod_desc."</td>
				<td width=\"22%\">".$product_group_name."</td>
				<td width=\"9%\">".$pack_size."</td>
				<td align=\"right\" width=\"12%\">".number_format($margin_cost,2)."</td>
				<td align=\"right\" width=\"11%\">".number_format($margin_cost_ton,2)."</td>
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
	a.download = 'Margin data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Margin Data</title>');
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
