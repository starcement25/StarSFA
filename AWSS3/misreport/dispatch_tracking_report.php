<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
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

<script>
function show_normalized_data(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('dispatch_tracking_data.php','display',0);
}

function show_saudawise(){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var type = 'custom';
	
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}

	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1){
		alert("Start date/End date cannot be empty");
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';	
	GenericAjaxFunction('dispatch_tracking_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}

</script>
</head>
<body onLoad="show_normalized_data();">
<center>
<br>
<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll;" align="center">
</div>
<br>
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_saudawise();" />
</div>
</center>
</body>
<?php
}
?>