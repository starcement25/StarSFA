<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$type = $_REQUEST['type'];
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
</script>

<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>');">
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>


<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>

<br><br>
	
<div id="product" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader1" hidden>
</div>

<br>

<div id="depotwise" style="max-height: 350px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader1" hidden>
</div>
	
<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" <?php if($type == 'today') echo "checked"; ?> onClick="hide_date_div(); show_saudawise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" <?php if($type == 'mtd') echo "checked"; ?> onClick="hide_date_div(); show_saudawise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" <?php if($type == 'custom') echo "checked"; ?> onClick="show_date_div();" />
</div>
	
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_saudawise();" />
</div>
<br />
<div id="printexport" style="width:60%;" align="right">
<input name="export" type="button" value="Export/Print" id="export" onClick="exporttocsv();">
</div>
<br>
<div><a href="#" style="color:blue;" onClick="show_prev_page();">Back</a></div>
    
</center>
</body>

<script>
function show_saudawise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	document.getElementById("product").innerHTML = "";
	document.getElementById("depotwise").innerHTML = "";
	
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
		GenericAjaxFunction('statewisedata.php?type='+type,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('statewisedata.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
		
}

function show_statewise_data(state,start_date,end_date,condition_value,product_group_code)
{
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('zonewise_product_details.php?state='+state+'&product_group_code='+product_group_code+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date+'&mode=state','product',0);
}

function exporttocsv()
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
	}
	
	window.open('exporttocsvstatezone.php?start_date='+start_date+'&end_date='+end_date+'&condition_value='+type,'mywindow');
}

function loadbodydata(type,start_date,end_date){
	<?php
	if($type == 'custom'){?>
		document.getElementById("date_div").hidden = false;<?php
	}
	?>
	GenericAjaxFunction('statewisedata.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}

function show_prev_page(){
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	}
	window.open('http://acedns.in/acednsproduct/misreport/sauda_report_main_selected.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

</script>
<?php
}
?>
