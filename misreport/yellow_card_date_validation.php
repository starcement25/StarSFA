<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	/*------------------------------> Select of month (financial year)<-------------------------------*/
	if(date('m')=='01')
	{
		$previous_month='12';
	}
	else
	{
	$previous_month=date('m')-1;
	}
	//$previous_month='05';
	/*if(date('m')<4)
	{*/
	    $previous_month_date= (date('Y')-1).'-'.$previous_month.'-'.'01';
	/*}
	else
	{
		$previous_month_date= (date('Y')).'-'.$previous_month.'-'.'01';
	}*/
$months = array();
for ($x = $previous_month; $x < $previous_month + 14; $x++) {
	$year=substr($previous_month_date,0,4);
	$key=$year.'-'.date('m', mktime(0, 0, 0, $x, 1));
	$months[$key] = date('F', mktime(0, 0, 0, $x, 1)).'-'.$year;
	$previous_month_date = date("Y-m-d", strtotime("+1 month", strtotime($previous_month_date)));
}
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
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
</script>
<body>
<center>
<br>
<table width="40%" class="border" style="border-collapse:collapse;" border="1" cellpadding="4">
  <tr  class="TDHEAD">
  	<td colspan="2" align="center">Yellow Card Date Validation</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Month:</td>
    <td align="left">
    	<select name="month_select" id="month_select">
        <option value="">Select</option>
    	<?php
		//echo "<option value=".date('Y')."-03>March-".date('Y')."</option>";
		foreach($months as $num => $monthvalue){
			echo "<option value=".$num.">".$monthvalue."</option>";
		}
		?>
        </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB"><td  align="right">
Validation Date:</td><td align="left"><input type="date" name="validation_date" id="validation_date" style="height:15px;" />
</td></tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
</table>
<br />
<div id="display" style="max-height: 440px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
</div>

</center>
</body>
<script>
function remove_selection(){
	//document.getElementById("today").checked = false;
	//document.getElementById("mtd").checked = false;
	//document.getElementById("custom").checked = false;
	document.getElementById("date_div").hidden = true;
	document.getElementById("display").innerHTML = '';
}

function get_data(){
	var month_select = document.getElementById("month_select").value;
	var month_select_validate=month_select+'-'+'01';
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Please choose validation month');
		return false;
	}
	if(document.getElementById("validation_date").value.search(/\S/) == -1){
		alert('Please choose validation date');
		return false;
	}
	//alert(document.getElementById("validation_date").value);
	
	var dateval = encodeURIComponent(document.getElementById("validation_date").value);
	var datearray = dateval.split("-");
	var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
	if(newdate < month_select_validate)
	{
		alert('Please choose proper validation date');
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	GenericAjaxFunction('yellow_card_date_submission.php?month_select='+month_select+'&newdate='+newdate,'display',0);
}
</script>
<?php } ?>