<?php
error_reporting(0);
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
$plant_name = $_REQUEST['plant_name'];

$sql_product_details = "SELECT PM.dns_prod_code,PM.prod_desc,MR.sale_rate,MR.basic_rate,BM.branch_code,BM.branch_name,PM.conversion_factor,PM.conversion_factor_two
						 	FROM product_master PM, sauda_mrp MR,branch_master BM
						   WHERE  PM.prod_code = MR.product_code 
						   AND MR.branch_code=BM.branch_code AND BM.plant_name='".$plant_name."' AND PM.acedns='Y' AND 
						   PM.vertical_value='".$_SESSION['vertical_value']."' AND PM.black_list='N' AND PM.prod_desc NOT LIKE '%LUP%' AND BM.acedns='Y' ORDER BY BM.branch_name,PM.prod_desc ASC";
$res_product_details = mysql_query($sql_product_details);
$total_rows = mysql_num_rows($res_product_details);

if($total_rows >0){
?>
<!--script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.0.3/jquery.floatThead.js"></script>
<script language="javascript" type="text/javascript">
   $jquery(".sticky-header").floatThead({scrollingTop:50});
 </script-->  
	<div id="display">
    <table class="border" width="90%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
      <tr>
        <td colspan="11" class="TDHEAD" align="left">Product Sale Rate - <?php echo $plant_name; ?></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>SI</td>
        <td>Product Description</td>
        <td>Material Cost</td>
        <td>Freight</td>
        <td>Packing Cost</td>
        <td>Depot Cost</td>
        <td>Margin</td>
        <td>Honeycomb Cost</td>
        <td>Detention Cost</td>
        <td>Sale Rate</td>
      </tr>
<?php
	$count = 1;
	$res_product_details = mysql_query($sql_product_details);
	$depot_array=array();
	while($row_product_details = mysql_fetch_array($res_product_details))
	{
		$conversion_one=$row_product_details['conversion_factor'];
		$conversion_two=$row_product_details['conversion_factor_two'];

		$sqlpackingprodwise="SELECT packing_cost FROM packing_master WHERE 
		dns_prod_code='".$row_product_details['dns_prod_code']."' AND  plant_name='".$plant_name."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysql_query($sqlpackingprodwise);
		$rowpackingprodwise=mysql_fetch_array($rspackingprodwise);
		$packing_cost=$rowpackingprodwise['packing_cost'];
		if($packing_cost=='')  $packing_cost=0;
		
		/*$sqldepotcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		$freight=$rowdepotcostprodwise['freight'];

		if($depot_cost=='')  $depot_cost=0;
		if($freight=='')      $freight=0;*/
		$sqldepotcostprodwise="SELECT depot_cost FROM depot_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysql_query($sqldepotcostprodwise);
		$rowdepotcostprodwise=mysql_fetch_array($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		if($depot_cost=='')  $depot_cost=0;
		
		$sqlmargincostprodwise="SELECT margin_cost FROM margin_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsmargincostprodwise=mysql_query($sqlmargincostprodwise);
		$rowmargincostprodwise=mysql_fetch_array($rsmargincostprodwise);
		$margin_cost=$rowmargincostprodwise['margin_cost'];
		if($margin_cost=='')  $margin_cost=0;
		
		$sqlfreightcostprodwise="SELECT freight_cost FROM freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' 
							AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsfreightcostprodwise=mysql_query($sqlfreightcostprodwise);
		$rowfreightcostprodwise=mysql_fetch_array($rsfreightcostprodwise);
		$freight_cost=$rowfreightcostprodwise['freight_cost'];
		if($freight_cost=='')
		{
		$freight_cost=0;
		}
		
		$sqlhoneycombcostprodwise="SELECT honeycomb_cost FROM honeycomb_cost WHERE prod_code='".$row_product_details['dns_prod_code']."' 
									AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rshoneycombcostprodwise=mysql_query($sqlhoneycombcostprodwise);
		$rowhoneycombcostprodwise=mysql_fetch_array($rshoneycombcostprodwise);
		$honeycomb_cost=$rowhoneycombcostprodwise['honeycomb_cost'];
		if($honeycomb_cost=='')
		{
		 $honeycomb_cost=0;
		}
		
		$sqldetentioncostprodwise="SELECT detention_cost FROM detention_cost WHERE prod_code='".$row_product_details['dns_prod_code']."' 
									AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdetentioncostprodwise=mysql_query($sqldetentioncostprodwise);
		$rowdetentioncostprodwise=mysql_fetch_array($rsdetentioncostprodwise);
		$detention_cost=$rowdetentioncostprodwise['detention_cost'];
		if($detention_cost=='')
		{
		 $detention_cost=0;
		}

			$basic_rate=$row_product_details['basic_rate'];
			$material_cost=$basic_rate-$packing_cost-$margin_cost;
			$material_cost=round($material_cost,2);

		if(!in_array($row_product_details['branch_code'],$depot_array))
		{
			echo "<tr><td colspan='11' class='TDHEAD' align='center'>".$row_product_details['branch_name']."</td></tr>";
			array_push($depot_array,$row_product_details['branch_code']);

		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$row_product_details['prod_desc']."</td>
				<td align=\"right\">".number_format($material_cost,2)."</td>
				<td align=\"right\">".number_format($freight_cost,2)."</td>
				<td align=\"right\">".number_format($packing_cost,2)."</td>
				<td align=\"right\">".number_format($depot_cost,2)."</td>
				<td align=\"right\">".number_format($margin_cost,2)."</td>
				<td align=\"right\">".number_format($honeycomb_cost,2)."</td>
				<td align=\"right\">".number_format($detention_cost,2)."</td>
				<td align=\"right\">".number_format($row_product_details['sale_rate'],2)."</td>
		</tr>";
		$count++;
	}
	echo "</table>";
	echo "</div>";
	echo "<div style=\"width:60%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
    <input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"ExportToExcel('');\" ></div>";
}
else
{
	echo "<strong><font color=\"red\">No records</font></strong>";
}
?>
<script>
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Order Register', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Order Register</title>');
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
	//alert(divid);
        //getting values of current time for generating the file name
        var dt = new Date();
        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var mins = dt.getMinutes();
        var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		/*var displaydiv = document.getElementById("display").innerHTML;
		var customerdatadiv = document.getElementById("customerdata").innerHTML;
	    var productdiv = document.getElementById("product").innerHTML;
	    var customerwisediv = document.getElementById("customerwise").innerHTML;
		var view = displaydiv+'<br>'+customerdatadiv+'<br>'+productdiv+'<br>'+customerwisediv;
		document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');
        //creating a temporary HTML link element (they support setting file names)*/
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('display');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Order Register' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
function ExportToExcel(){
       var htmltable= document.getElementById('display');
       var html = htmltable.outerHTML;
       window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html)+';filename=exportData.xls');
    }
</script>
<?php
}
?>
