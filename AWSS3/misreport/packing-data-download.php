<?php
ob_start();
session_start();
//require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

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

/*$sqllatestuploaddate="SELECT DATE_FORMAT(datetime,'%d-%m-%Y') AS packing_upload_date FROM `packing_master` ORDER BY datetime DESC LIMIT 0,1";
$rslatestuploaddate=mysql_query($sqllatestuploaddate);
$rowlatestuploaddate=mysql_fetch_array($rslatestuploaddate);
$packing_upload_date_latest=$rowlatestuploaddate['packing_upload_date'];*/

$sql_packing= "SELECT * FROM (SELECT PC.dns_prod_code, PC.prod_desc, PC.packing_cost, PC.no_pc_one_case,PC.plant_name,PC.packing_realization,
					PM.acedns,DATE_FORMAT(SUBSTRING(PC.datetime,1,10),'%d-%m-%Y') As last_updated_date,PM.product_group_code FROM
				packing_master PC,product_master PM WHERE PC.dns_prod_code=PM.dns_prod_code AND PM.acedns='Y' AND PC.plant_name!='' AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' ORDER BY PC.datetime DESC) AS SAT GROUP BY 1,5 ORDER BY 5,9,1";
/*$sql_packing= "SELECT PC.dns_prod_code, PC.prod_desc, PC.packing_cost, PC.no_pc_one_case,PC.plant_name,PC.packing_realization,PM.acedns FROM
				packing_master PC,product_master PM WHERE PC.dns_prod_code=PM.dns_prod_code AND 
				PM.vertical_value='".$_SESSION['vertical_value']."' 
				AND DATE_FORMAT(SUBSTRING(PC.datetime,1,10),'%d-%m-%Y')='".$packing_upload_date_latest."' GROUP BY PM.dns_prod_code,PC.plant_name 
				ORDER BY PC.plant_name ASC";*/				
$res_packing = mysql_query($sql_packing);
$total_packing = mysql_num_rows($res_packing);
$count = 1;
if($total_packing>0){
	?>
    <table width="90%" border="1" style="border-collapse:collapse;" class="BORDER" cellpadding="4">
     <tr>
        <td colspan="7" class="TDHEAD" align="center">Packing Master</td>
      </tr>
      <tr class="TDHEAD_SUB" align="center">
      	<td>SI</td>
        <td>Plant</td>
        <td>Code</td>
        <td>Description</td>
        <td>Sale</td>
        <td>Actual</td>
        <td>Last Update Date</td>
      </tr>
    <?php
	while($row_packing = mysql_fetch_array($res_packing)){
		$dns_prod_code = $row_packing['dns_prod_code'];
		$prod_desc = str_replace(',','',$row_packing['prod_desc']);
		$packing_cost = $row_packing['packing_cost'];
		$no_pc_one_case = $row_packing['no_pc_one_case'];
		$plant = $row_packing['plant_name'];
		$packing_realization = $row_packing['packing_realization'];
		$last_updated_date = $row_packing['last_updated_date'];
		/*$acedns = $row_packing['acedns'];
		if($acedns=='N'){
			$color="#f44242";
		}
		else $color="";
		echo "<tr>
				<td style=\"background:$color;\">".$count."</td>
				<td style=\"background:$color;\">".$plant."</td>
				<td style=\"background:$color;\">".$dns_prod_code."</td>
				<td style=\"background:$color;\">".$prod_desc."</td>
				<td align=\"right\">".$packing_cost."</td>
				<td align=\"right\">".$packing_realization."</td>
			  </tr>";*/
		echo "<tr>
				<td>".$count."</td>
				<td>".$plant."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".$packing_cost."</td>
				<td align=\"right\">".$packing_realization."</td>
				<td>".$last_updated_date."</td>
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
	a.download = 'Packing data' + postfix + '.xls';
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
	mywindow.document.write('<html><head><title>Packing Data</title>');
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
