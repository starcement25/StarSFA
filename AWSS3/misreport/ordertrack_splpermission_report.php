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
function show_order_status(branch_code){
	if(document.getElementById("branch").value.search(/\S/) == -1){
		return false;
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('ordertrack_splpermission_details.php?branch_code='+branch_code,'display',0);
}

function send_to_pending(order_id){
	var branch_code = document.getElementById("branch").value;
	$.post("order_track_update.php",
    {
		reason_type: 'send_to_pending',
		order_no: order_id
    },
    function(data, status){
        alert(data);
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('ordertrack_splpermission_details.php?branch_code='+branch_code,'display',0);
		//document.getElementById("order_submit_id").disabled = false;
	});
}
</script>
</head>
<body>
<center>
<table cellpadding="8px">
  <tr style="background:#EEE8CD;">
  	<td align="left">Branch</td>
    <td align="right">
    <select id="branch" onChange="show_order_status(this.value);">
    	<option value="">Select</option>
    <?php
	$sql_branch = "SELECT branch_code, branch_name FROM branch_master";
	$res_branch = mysql_query($sql_branch);
	while($row_branch = mysql_fetch_array($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$branch_name = $row_branch['branch_name'];
		echo "<option value=\"".$branch_code."\">".$branch_name."</option>";
	}
	?>
    </select>
    </td>
  </tr>
</table>
<br>
<div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center">
</div>
<br><br>
<div id="display_details" style="max-height: 350px; width:35%; overflow-y: scroll;" align="center">
</div>
<br><br><br><br>
<a name="display_details"></a>
</center>
</body>
<?php
}
?>
