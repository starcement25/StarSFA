<?php
ob_start();
session_start();
require("adminUtils.php");

if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){
$today = date('Y-m-d');
$condition = " SUBSTRING(PD.datetime,1,10)='".$today."' ";
function plant_sort($a, $b) {
    if($a==$b) return $a;
}
$count = 1;
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
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
<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Publish Rate Report', 'height=400,width=600');
	mywindow.document.write('<html><head>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv(divid)
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	/*document.write('<div id=\'view\'>');
	document.write(view);
	document.write('<div>');*/
	//creating a temporary HTML link element (they support setting file names)*/
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	//var table_html = encodeURIComponent(table_div.outerHTML.replace(/ /g, '%20'));
	//alert(table_html);return false;
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Publish Rate Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<body>
<center>
<br>
<div id="display" style="max-height: 440px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
<?php
$prod_code_array=array();
$sqlselduplicate="SELECT product_code FROM sauda_mrp where acedns='Y' GROUP BY product_code HAVING count(product_code) > 1 ";
$rsselduplicate=mysql_query($sqlselduplicate);
	while($rowselduplicate=mysql_fetch_array($rsselduplicate))
	{
		array_push($prod_code_array,$rowselduplicate['product_code']);
	}
/*$sqlpublishedproddetails="SELECT PM.prod_code,PM.prod_desc,PM.dns_prod_code,SM.sale_rate,SM.parent_child FROM product_master PM,sauda_mrp SM 
						WHERE PM.prod_code=SM.product_code AND release_date=(SELECT MAX(release_date) FROM  sauda_mrp) 
						ORDER BY PM.prod_code ASC";*/
$sqlpublishedproddetails="SELECT PM.prod_code,PM.prod_desc,PM.dns_prod_code,SM.sale_rate,SM.parent_child FROM product_master PM,sauda_mrp SM 
						WHERE PM.prod_code=SM.product_code AND SM.release_date!='0000-00-00 00:00:00' AND SM.acedns='Y' 
						ORDER BY PM.prod_code ASC,SM.parent_child ASC";						
$rspublishedproddetails=mysql_query($sqlpublishedproddetails);
$total_published_rows=mysql_num_rows($rspublishedproddetails);
if($rspublishedproddetails>0){
	?>
    <form name="frm_releaseprice" method="post" action=""/>
    <input type="hidden" name="mode" value="publishrate"/>
    <input type="hidden" name="prod_code" value=""/>
    <table border="1" style="border-collapse:collapse;" class="border" width="70%" cellpadding="4" align="center" id="display">
       <tr class="TDHEAD" align="center" id="head_main"><td colspan="6">Published Rate List</td></tr>
      <tr class="TDHEAD_SUB" align="center" id="head_main">
      	<td>SI</td>
        <td>Product Code</td>
        <td>Description</td>
        <td>Published Rate</td>
      </tr>
    <?php
	//print_r($prod_code_array);
	while($rowpublishedproddetails=mysql_fetch_array($rspublishedproddetails))
	{
		$prod_code=$rowpublishedproddetails['prod_code'];
		$prod_desc=$rowpublishedproddetails['prod_desc'];
		$dns_prod_code=$rowpublishedproddetails['dns_prod_code'];
		$sale_rate=$rowpublishedproddetails['sale_rate'];
		$parent_child=$rowpublishedproddetails['parent_child'];
		if(!in_array($prod_code,$prod_code_array))
		{
			if($parent_child=='child')
			{
				$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$prod_code."' AND acedns='Y'";
				$rsselindustrialrate=mysql_query($sqlselindustrialrate);
				$rowselindustrialrate=mysql_fetch_array($rsselindustrialrate);
				$cntselindustrialrate=mysql_num_rows($rsselindustrialrate);
				if($cntselindustrialrate >0)
				{
					$sale_rate=$rowselindustrialrate['sale_rate'];
					$sale_rate_TD="<td align=\"right\">".number_format($sale_rate,2)."</td>";
				}
				else $sale_rate_TD="<td align=\"right\">".number_format($sale_rate,2)."</td>";
			}
			else
			{
				$sale_rate_TD="<td align=\"right\">".number_format($sale_rate,2)."</td>";
			}
		}
		else
		{
			if($parent_child=='parent')
			{
				$sale_rate_TD="<td align=\"right\" style=\"background:red;\">".number_format($sale_rate,2)."</td>";
			}
			else
			{
				$sqlselindustrialrate="SELECT sale_rate FROM industrial_rate WHERE product_code='".$prod_code."' AND acedns='Y'";
				$rsselindustrialrate=mysql_query($sqlselindustrialrate);
				$rowselindustrialrate=mysql_fetch_array($rsselindustrialrate);
				$cntselindustrialrate=mysql_num_rows($rsselindustrialrate);
				if($cntselindustrialrate >0)
				{
				 $sale_rate=$rowselindustrialrate['sale_rate'];
				 $sale_rate_TD="<td align=\"right\">".number_format($sale_rate,2)."</td>";
				}
			}
		}
		
		echo "<tr id=\"tab".$count."\">
				<td>".$count."</td>
				<td>".$dns_prod_code."</td>
				<td>".$prod_desc."</td>"
				.$sale_rate_TD."
			  </tr>";
		$count++;
	}
	?>
    <br>
<?php  
}
else{
	echo "<tr><td align=\"center\"><strong><font color=\"red\">No records found</font></strong></td></tr>";
}
echo "</table></form>";  

mysql_close($link);
}?><div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>

</div>
</center></body>