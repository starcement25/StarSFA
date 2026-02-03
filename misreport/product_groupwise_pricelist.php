<?php
error_reporting(0);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
$branch_code = $_REQUEST['branch_code'];
$product_group_code = $_REQUEST['product_group_code'];
$plant_name = $_REQUEST['plant_name'];

$sqllooserate="SELECT loose_rate_ton FROM pricing_detials WHERE product_group_code='".$product_group_code."' ORDER BY datetime DESC LIMIT 0,1";
$rslooserate=mysqli_query($link,$sqllooserate);
$rowlooserate=mysqli_fetch_assoc($rslooserate);
$loose_rate_ton=$rowlooserate['loose_rate_ton'];
$sql_product_details = "SELECT PM.dns_prod_code,PM.prod_desc,MR.sale_rate,BM.branch_code,BM.branch_name,PM.conversion_factor,PM.conversion_factor_two
						 	FROM product_master PM, sauda_mrp MR,branch_master BM
						   WHERE PM.product_group_code='".$product_group_code."' AND PM.prod_code = MR.product_code 
						   AND MR.branch_code=BM.branch_code AND BM.plant_name='".$plant_name."' AND PM.acedns='Y' AND PM.black_list='N' AND PM.prod_desc NOT LIKE '%LUP%' ORDER BY BM.branch_name,PM.prod_desc ASC";
$res_product_details = mysqli_query($link,$sql_product_details);
$total_rows = mysqli_num_rows($res_product_details);

$sqlproductgroupname="SELECT product_group_name FROM product_group_master WHERE product_group_code='".$product_group_code."'";
$rsproductgroupname=mysqli_query($link,$sqlproductgroupname);
$rowproductgroupname=mysqli_fetch_assoc($rsproductgroupname);
$product_group_name=$rowproductgroupname['product_group_name'];
if($total_rows >0){
?>
	<div id="display">
    <table class="border" width="90%" border="1" style="border-collapse:collapse;" cellpadding="5px" align="center">
      <tr>
        <td colspan="11" class="TDHEAD" align="center">Product Sale Rate - <?php echo $product_group_name; ?></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>SI</td>
        <td>Product Description</td>
        <td>Material Cost</td>
        <td>Freight</td>
        <td>Packing Cost</td>
        <td>Depot Cost</td>
        <td>Sale Rate</td>
      </tr>
<?php
	$count = 1;
	$res_product_details = mysqli_query($link,$sql_product_details);
	$depot_array=array();
	while($row_product_details = mysqli_fetch_assoc($res_product_details))
	{
		$conversion_one=$row_product_details['conversion_factor'];
		$conversion_two=$row_product_details['conversion_factor_two'];
		/*$sqlhire_cost="SELECT hire_cost FROM basic_freight WHERE branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rshire_cost=mysqli_query($link,$sqlhire_cost);
		$rowhire_cost=mysqli_fetch_assoc($rshire_cost);
		$hire_cost=$rowhire_cost['hire_cost'];
		if($hire_cost=='')  $hire_cost=0;*/
		$sqlpackingprodwise="SELECT packing_cost FROM packing_master WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rspackingprodwise=mysqli_query($link,$sqlpackingprodwise);
		$rowpackingprodwise=mysqli_fetch_assoc($rspackingprodwise);
		$packing_cost=$rowpackingprodwise['packing_cost'];
		if($packing_cost=='')  $packing_cost=0;
		$sqldepotcostprodwise="SELECT depot_cost,freight FROM depot_freight_cost WHERE dns_prod_code='".$row_product_details['dns_prod_code']."' AND branch_code='".$row_product_details['branch_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rsdepotcostprodwise=mysqli_query($link,$sqldepotcostprodwise);
		$rowdepotcostprodwise=mysqli_fetch_assoc($rsdepotcostprodwise);
		$depot_cost=$rowdepotcostprodwise['depot_cost'];
		$freight=$rowdepotcostprodwise['freight'];
		if($depot_cost=='')  $depot_cost=0;
		if($freight=='')      $freight=0;
		/*$sqltruckloadqty="SELECT qty_truck_load FROM load_distribution WHERE prod_code='".$row_product_details['dns_prod_code']."' ORDER BY datetime DESC LIMIT 0,1";
		$rstruckloadqty=mysqli_query($link,$sqltruckloadqty);
		$rowtruckloadqty=mysqli_fetch_assoc($rstruckloadqty);
		$qty_truck_load=$rowtruckloadqty['qty_truck_load'];
		if($qty_truck_load=='')  $qty_truck_load=0;*/
		
		$material_cost=round(($loose_rate_ton/$conversion_two),2);
		$material_cost=round(($material_cost*$conversion_one),2);
		/*if($qty_truck_load>0)
		{
			$freight=$hire_cost/$qty_truck_load;
		}
		else
		{
			$freight=0;
		}*/
		if(!in_array($row_product_details['branch_code'],$depot_array))
		{
			echo "<tr><td colspan='11' class='TDHEAD' align='center'>".$row_product_details['branch_name']."</td></tr>";
			array_push($depot_array,$row_product_details['branch_code']);

		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$row_product_details['prod_desc']."</td>
				<td align=\"right\">".number_format($material_cost,2)."</td>
				<td align=\"right\">".number_format($freight,2)."</td>
				<td align=\"right\">".number_format($packing_cost,2)."</td>
				<td align=\"right\">".number_format($depot_cost,2)."</td>
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
