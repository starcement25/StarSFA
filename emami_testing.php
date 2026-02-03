<?php
mysqli_connect("localhost","acedns_dnsprod","dnsprod1234");
mysqli_select_db("acedns_EMAMI");
$current_date = date('Y-m-d',strtotime('yesterday'));

/*-------------------------------------> Product Group Select <-------------------------------------*/
$sql_product_group_today = "SELECT DISTINCT PGM.product_group_code, PGM.product_group_name 
							FROM product_group_master PGM, sauda_transaction_log STL, product_master PM 
							WHERE PM.prod_code = STL.prod_code 
							AND PM.product_group_code = PGM.product_group_code 
							AND SUBSTRING(STL.download_time,1,10) = '".$current_date."' 
							ORDER BY PGM.product_group_name ASC";
$res_product_group_today = mysqli_query($link,$sql_product_group_today);
while($row_product_group_today = mysqli_fetch_assoc($res_product_group_today)){
	$product_group_code = $row_product_group_today['product_group_code'];
	$product_group_name = strtoupper($row_product_group_today['product_group_name']);
	/*-------------------------------> Total Quantity Select According To Prduct Group<----------------------------*/
	$sql_product_booked_quantity = "SELECT SUM(STL.convert_qty_two) as total_booked_ton 
									FROM sauda_transaction_log STL, product_master PM 
									WHERE STL.prod_code=PM.prod_code AND PM.product_group_code = '".$product_group_code."' 
									AND SUBSTRING(STL.download_time,1,10) = '".$current_date."'";
	$res_product_booked_quantity = mysqli_query($link,$sql_product_booked_quantity);
	$row_product_booked_quantity = mysqli_fetch_assoc($res_product_booked_quantity);
	$product_grwise_booked = number_format($row_product_booked_quantity['total_booked_ton'],3);
	
	$product_group_booked_array[$product_group_name] = $product_grwise_booked;
}
/*echo "<pre>";
print_r($product_group_booked_array);
echo "</pre>";*/

foreach($product_group_booked_array as $product_group_name=>$product_group_quantity){
	$data_pie .= "['".$product_group_name."',".$product_group_quantity."],";
}
$data_pie = rtrim($data_pie,",");
?>
<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
    
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1', {'packages':['corechart']});
      
    // Set a callback to run when the Google Visualization API is loaded.
    //google.setOnLoadCallback(show_graph_data);
      
    /*function drawChart() {
      var jsonData = $.ajax({
          url: "getData.php",
          dataType: "json",
          async: false
          }).responseText;
          
      // Create our data table out of JSON data loaded from server.
      var data = new google.visualization.DataTable(jsonData);

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      chart.draw(data, {width: 400, height: 240});
    }*/

    </script>
  </head>
<script>
function show_graph(val){
	$.post("getdata.php",
	{
		val: val
	},
	function(data, status){
		//alert("Data: " + data + "\nStatus: " + status);
		//alert(data);
		var data_pie = google.visualization.arrayToDataTable([
		  ['EmployeeName', 'Order'],data
		]);
		// Set chart options
	  var options = {'title':'Sauda Booked',
					 'width':640,
					 'height':480,
					 'backgroundColor':'transparent',
					 'fontName':'Times New Roman'
					 };
	
	  // Instantiate and draw our chart, passing in some options.
	  var chart_pie = new google.visualization.PieChart(document.getElementById('chart_div'));
	  
	  chart_pie.draw(data_pie, options);
	});
}
</script>
<body>
<!--Div that will hold the pie chart-->
<div id="chart_div"></div>
<div style="width:60%; font-weight:bold; color:#0066FF; border-color:#666666; padding:8px;">
        TODAY:<input type="radio" name="duration" value="today" id="today" onClick="show_graph(this.value);" />&nbsp;&nbsp;&nbsp;&nbsp;
        MTD:<input type="radio" name="duration" value="mtd" id="mtd" onClick="show_graph(this.value);" />
</body>
</html>