<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	/*define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	mysql_connect(SERVER,USER,PASSWORD);
	mysql_select_db("acedns_RUPA");*/

	@$current_date = date('Y-m-d');
?>

<?php
if(!$_GET)
	disphtml("main();");
	
function main()
{
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
        Popup($(elem).html());
    }

function Popup(data) 
    {
        var mywindow = window.open('', 'Report Details', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Report Details</title>');
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

<body>
<center>
<div style="width:70%;" align="right">
<input name="print" type="button" value="Print" onClick="PrintElem('#display');">
<input name="export" type="button" value="Export" onClick="createexcel();"></div><br>

<div id="display" class="CSSTableGenerator" style="max-height: 500px; width:70%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader" hidden>
</div>

<br>
	
<!--<div id="product" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader1" hidden>
</div>

<br>

<div id="depotwise" style="max-height: 350px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader1" hidden>
</div>-->
	
<div style="width:60%; margin-left:10px;">
Select Vertical:<select id="select_vertical" onChange="reset_data();">
  <option value=" ">Select Vertical</option>
  <?php
  $sql_vertical = "SELECT distinct(vertical_value) FROM employee_master";
  $res_vertical = mysql_query($sql_vertical);
  while($row_vertical = mysql_fetch_array($res_vertical))
  {
	  echo "<option>".$row_vertical['vertical_value']."</option>";
  }
  ?>
</select>
<br><br>
Today:<input type="radio" name="duration" value="today" id="today" onClick="hide_date_div(); show_saudawise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" onClick="hide_date_div(); show_saudawise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" onClick="show_date_div();" />
</div>
	
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_saudawise();" />
</div>

<!--<div><a href="sauda_report_main.php" style="color:blue;">Back</a></div>-->
    
</center>
</body>

<script>
function show_saudawise()
{
	//document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	//document.getElementById("product").innerHTML = "";
	//document.getElementById("depotwise").innerHTML = "";
	
	var vertical = document.getElementById("select_vertical").value;
	if(document.getElementById("select_vertical").value.search(/\S/) == -1)
	{
		alert('Select Vertical');
	}
	
	else
	{
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
	{
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('rupa_verticalwise_data.php?type='+type+'&vertical='+vertical,'display',0);
	}
	else 
	{
		if(response1 != 0 && response2 != 0)
		{
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('rupa_verticalwise_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&vertical='+vertical,'display',0);
		}
	}
	
	}
}

function reset_data()
{
	document.getElementById("today").checked = false;
	document.getElementById("mtd").checked = false;
	document.getElementById("custom").checked = false;
	document.getElementById("date_div").hidden = true;
	document.getElementById("display").innerHTML = '';
}

function createexcel()
{
	var vertical = document.getElementById("select_vertical").value;
	if(document.getElementById("today").checked == true)
	{
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true)
	{
		var type = 'mtd';
	}
	else
	{
		var type = 'custom';
	}
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	window.open("rupa_verticalwise_data_excel.php?vertical="+vertical+"&type="+type+"&start_date="+start_date+"&end_date="+end_date,"mywindow");
	
	/*$.post("rupa_verticalwise_data_excel.php",
    	{
		vertical: vertical,
        type: type,
		start_date: start_date,
		end_date: end_date
    	});*/
		//function(data, status){
			//alert(data);
			//document.getElementById("testdiv").innerHTML = data;
			//document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			//GenericAjaxFunction('saudawise_product_edit.php?sauda_number='+sauda_number,'display',0);
		//});
}
</script>
<?php
}
?>
