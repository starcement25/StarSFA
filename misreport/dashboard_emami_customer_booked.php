<?php
mysqli_connect("localhost","acedns_dnsprod","dnsprod1234");
mysqli_select_db("acedns_EMAMI");
$current_date = date('Y-m-d');

$prod_group_code = $_REQUEST['prod_group_code'];

if($_GET['type'] == 'today'){
	$condition = " SUBSTRING(STL.sauda_date,1,10) = '".$current_date."' ";
	$table_header = 'TODAY';
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = " YEAR(SUBSTRING(STL.sauda_date,1,10)) = '".$year."' AND  MONTH(SUBSTRING(STL.sauda_date,1,10)) = '".$month."' ";
	$table_header = 'MTD';
}
else if($_GET['type'] == 'custom'){
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$condition = " (SUBSTRING(STL.sauda_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
	$table_header = 'From '.date('d-m-Y',strtotime(''.$start_date.'')).' To '.date('d-m-Y',strtotime(''.$end_date.''));
}

$sql_qty_booked = "SELECT STL.customer_code, CM.customer_name, SUM(STL.convert_qty_two) as mt_booked, SUM(STL.qty) as case_booked FROM sauda_transaction_log STL, product_group_master PGM, product_master PM, customer_master CM WHERE".$condition." AND STL.customer_code=CM.customer_code AND STL.prod_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND PGM.product_group_code='".$prod_group_code."' GROUP BY STL.customer_code ORDER BY CM.customer_name ASC";
$res_qty_booked = mysqli_query($link,$sql_qty_booked);
while($row_qty_booked = mysqli_fetch_assoc($res_qty_booked)){
	$customer_code = $row_qty_booked['customer_code'];
	$customer_name = $row_qty_booked['customer_name'];
	$mt_booked = $row_qty_booked['mt_booked'];
	$case_booked = $row_qty_booked['case_booked'];
	
	$customer_name_code_array[$customer_code]= $customer_name;
	$mt_booked_array[$customer_code] = $mt_booked;
	$case_booked_array[$customer_code] = $case_booked;
}

/*foreach($sku_array as $sku_code_index=>$sku_qty){
	$data_pie .= "['".$sku_name_array[$sku_code_index]."',".str_replace(",","",$sku_qty)."],";
}
/*echo "<pre>";
print_r($sku_array);
echo "</pre>";*/

?>
<head>
	<title>Emami Dashboard</title>
    <!--<meta http-equiv="refresh" content="600" />-->
    <meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Rosario:400,700' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript">
   // Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
	</script>
	<script type="text/javascript"
		  src="https://www.google.com/jsapi?autoload={
			'modules':[{
			  'name':'visualization',
			  'version':'1',
			  'packages':['corechart']
			}]
		  }">
	</script>
</head>

<script type="text/javascript">
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChartPie);

// Callback that creates and populates a data table, 
// instantiates the pie chart, passes in the data and
// draws it.
function drawChartPie()
{
  // Create the data table.
  var data_pie = google.visualization.arrayToDataTable([
	  ['EmployeeName', 'Order'],
	  <?php echo $data_pie; ?>
	]);
	
  // Set chart options
  var options = {'title':'SAUDA BOOKED',
				 'width':640,
				 'height':480,
				 'backgroundColor':'transparent',
				 'fontName':'Times New Roman'
				 };

  // Instantiate and draw our chart, passing in some options.
  var chart_pie = new google.visualization.PieChart(document.getElementById('chart_div_pie'));
  
  chart_pie.draw(data_pie, options);
}

function show_date_div(){
	document.getElementById("date_div").hidden = false;
}

function hide_date_div(){
	document.getElementById("date_div").hidden = true;
}
function show_graph_data(value){
	//alert(value);
	if(document.getElementById("today").checked == true)
	{
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true)
	{
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true)
	{
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			var response1 = 0;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			var response2 = 0;
		}
	}
	
	if(type != 'custom')
		window.open('http://acedns.in/acednsproduct/misreport/dashboard_emami_skuwise.php?type='+type,'_self');
	else 
	{
		if(response1 != 0 && response2 != 0)
		window.open('http://acedns.in/acednsproduct/misreport/dashboard_emami_skuwise.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
	}
}
</script>

<body background="gray_back.jpg">
<center>
<br>
<div class="container-fluid" align="center">
<table width="90%" style="background:#EAEAEA;">
  <tr>
  	<td width="20%" align="center" valign="top">
    	<br>
    	<table cellpadding="6px">
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="dashboard_emami_skuwise.php" style="font-weight:bold; color:#104E8B; text-decoration:none;">SKU</a></td>
          </tr>
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="" style="font-weight:bold; color:#104E8B; text-decoration:none;">CUSTOMER WISE</a></td>
          </tr>
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="" style="font-weight:bold; color:#104E8B; text-decoration:none;">PRODUCT GROUP WISE</a></td>
          </tr>
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="" style="font-weight:bold; color:#104E8B; text-decoration:none;">BROKER WISE</a></td>
          </tr>
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="" style="font-weight:bold; color:#104E8B; text-decoration:none;">DEPOT WISE</a></td>
          </tr>
          <tr style="border-bottom:1px solid; border-bottom-color:#EAEAEA;">
          	<td style="padding:8px; background:#87CEFF;"><a href="dashboard_emami.php" style="font-weight:bold; color:#104E8B; text-decoration:none;">GRAPHICAL REPORT</a></td>
          </tr>
        </table>
    </td>
    <td  width="70%" align="center">
    <br>
    	<table width="95%">
          <tr>
          	<td align="center" style="font-weight:bold; color:#003399; padding:8px;" bgcolor="#B0C4DE"><?php echo $table_header; ?></td>
          </tr>
          <!--<tr>
          	<td><div id="chart_div_pie"></div></td>
          </tr>-->
          <tr>
          	<td align="center">
            <br />
            	<div style="max-height:600px; overflow-y:scroll;">
            	<table width="50%" style="border-collapse:collapse;">
                  <tr style="font-weight:bold; background:#CDC9C9;" align="center">
                  	<td>SI</td>
                  	<td>Customer Name</td>
                    <td colspan="2">Booked</td>
                  </tr>
                  <tr style="font-weight:bold; background:#CDC9C9;" align="center">
                  	<td></td>
                    <td></td>
                    <td>MT</td>
                    <td>Case</td>
                  </tr>
                  <?php
				  $count = 1;
				  foreach($customer_name_code_array as $index=>$val){
					  if($count%2 == 0)
					  	$color = '#FFFAFA';
					  else
					  	$color = '#EEE9E9';
					  echo "<tr style=\"background:$color;\">
					  			<td align=\"right\" style=\"padding:4px;\">".$count."</td>
					  			<td style=\"padding:4px; border-left:1px solid; border-left-color:#CDC9C9;\">".$val."</td>
								<td align=\"right\" style=\"padding:4px; border-left:1px solid; border-left-color:#CDC9C9;\">".number_format($mt_booked_array[$index],3)."</td>
								<td align=\"right\" style=\"padding:4px; border-left:1px solid; border-left-color:#CDC9C9;\">".number_format($case_booked_array[$index],3)."</td>
					  		</tr>";
					  $mt_booked_total += $mt_booked_array[$index];
					  $case_booked_total += $case_booked_array[$index];
					  $count++;
				  }
				  ?>
                  <tr style="background:#CDC9C9;">
                  	<td colspan="2" align="center" style="font-weight:bold;">Total</td>
                    <td align="right" style="font-weight:bold;"><?php echo $mt_booked_total; ?></td>
                    <td align="right" style="font-weight:bold;"><?php echo $case_booked_total; ?></td>
                  </tr>
                </table>
                </div>
            </td>
          </tr>
          <tr>
          	<td align="center"><a href="#" onClick="window.history.back();" style="font-weight:bold; color:blue;"><br />Back</a></td>
          </tr>
          <!--<tr>
          	<td align="center" bgcolor="#B0C4DE">
            <div style="width:60%; font-weight:bold; color:#0066FF; border-color:#666666; padding:8px;">
            TODAY:<input type="radio" name="duration" value="today" id="today" checked onClick="hide_date_div(); show_graph_data(this.value);" />&nbsp;&nbsp;&nbsp;&nbsp;
            MTD:<input type="radio" name="duration" value="mtd" id="mtd" onClick="hide_date_div(); show_graph_data(this.value);" />&nbsp;&nbsp;&nbsp;&nbsp;
            CUSTOM:<input type="radio" name="duration" value="custom" id="custom" onClick="show_date_div();" />
            </div>
            <br>
            </td>
          </tr>
          <tr>
          	<td align="center" bgcolor="#B0C4DE">
            <div id="date_div" style="font-weight:bold; padding:8px;" hidden >
            FROM:<input type="date" name="start_date" id="start_date" style="height:30px;" />
            TO:<input type="date" name="end_date" id="end_date" style="height:30px;" />
            <input type="submit" name="submit" value="Submit" onClick="show_graph_data(custom.value);" />
            </div>
            </td>
          </tr>-->
        </table>
        <br>
    </td>
  </tr>
</table>
<br />
</div>
</center>
</body>
<script>
<?php
if($_GET['type'] == 'today'){
	echo "document.getElementById(\"today\").checked = true;";
}
else if($_GET['type'] == 'mtd'){
	echo "document.getElementById(\"mtd\").checked = true;";
}
else if($_GET['type'] == 'custom'){
	echo "document.getElementById(\"custom\").checked = true;
		document.getElementById(\"date_div\").hidden = false;
		document.getElementById(\"start_date\").value = '$start_date';
		document.getElementById(\"end_date\").value = '$end_date';";
}
?>
</script>
