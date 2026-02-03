<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<script type="text/javascript" src="ajax1.js"></script>
<script type="text/javascript">
function showhidediv(){
    var div = document.getElementById('customdate');
    div.style.display = 'block';
}
function show_cust_business_prospect(){
	var type = 'custom';
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;

	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		var response1 = 0;
	}

if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1){
		alert("Start date/End date cannot be empty");
		var response2 = 0;
	}
	if(response1 != 0 && response2 != 0){
		GenericAjaxFunction('business_prospect_customize_count.php?value=4&start_date='+start_date+'&end_date='+end_date,'prospect_count',0);
	}
}

function hidecustom(){
	document.getElementById('customdate').style.display = 'none';
}
</script>
<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

if(!$_GET)
	disphtml("main();");

function main()
{
$begin_year = date('Y-m-d', strtotime('first day of January this year'));

for($i=1; $i<=3; $i++)
{
	@$current_date = date('Y-m-d');

	if($i==1)
	{
		$condition = "substring(LO.date,1,10)="."'".$current_date."'";
	}
	else if($i==2)
	{
		$month = explode("-",$current_date);
		$year = $month[0];
		$month = $month[1];
		$condition = "(YEAR(LO.date) =". $year." AND MONTH(LO.date) =" .$month.")";
	}
	else if($i==3)
	{
		$begin_year = date('Y-m-d', strtotime('first day of april this year'));
		$month_last_date = date('Y-m-d', strtotime('last day of this month this year'));
		$condition = "(LO.date BETWEEN '".$begin_year." "."12:00:01' AND '".$month_last_date." "."23:59:59')";
	}

$sql_prospect = "SELECT count(PCH.customer_name) FROM employee_master EM, prospective_customer_header PCH, route_master RM, location LO WHERE LO.trans_id=PCH.trans_id AND PCH.area=RM.route_code AND EM.emp_code=substring(LO.trans_id,3,5) AND ".$condition." AND LO.trans_id LIKE 'DC%'";
$res_prospect = mysql_query($sql_prospect);
$row_prospect = mysql_fetch_array($res_prospect);

if($i == 1)
	$today = $row_prospect['count(PCH.customer_name)'];
else if($i == 2)
	$mtd = $row_prospect['count(PCH.customer_name)'];
else if($i == 3)
	$ytd = $row_prospect['count(PCH.customer_name)'];
}
?>

<script type="text/javascript" src="ajax1.js"></script>
<script>
function cleardiv()
{
	document.getElementById("prospect_detail").innerHTML = "";
	document.getElementById("prospect_info").innerHTML = "";
}
</script>
<body onLoad="GenericAjaxFunction('business_prospect_customize_count.php?value='+1,'prospect_count',0);">
<center>

<table>
<tr>
	<td colspan="4" align="center">
    Today<b>(<?php echo $today; ?>)</b>:<input name="duration" type="radio" value="today" checked onClick="GenericAjaxFunction('business_prospect_customize_count.php?value='+1,'prospect_count',0); cleardiv(); hidecustom();"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    MTD<b>(<?php echo $mtd; ?>)</b>:<input name="duration" type="radio" value="yesterday" onClick="GenericAjaxFunction('business_prospect_customize_count.php?value='+2,'prospect_count',0); cleardiv(); hidecustom();" />
&nbsp;&nbsp;&nbsp;&nbsp;
    YTD<b>(<?php echo $ytd; ?>)</b>:<input name="duration" type="radio" value="monthly" onClick="GenericAjaxFunction('business_prospect_customize_count.php?value='+3,'prospect_count',0); cleardiv(); hidecustom();" />
&nbsp;&nbsp;&nbsp;&nbsp;
    Custom:<input name="duration" type="radio" value="custom" onClick="showhidediv(); cleardiv();"/>
    </td>
</tr>
<tr id="customdate" style="display:none;">
	<td>
		From:<input type="date" name="start_date" id="start_date" />
    To:<input type="date" name="end_date" id="end_date"/>
		<input type="submit" name="sub" value="Search" onClick="show_cust_business_prospect();">
  </td>
</tr>
</table>

<br />

<div id="prospect_count" style="max-height: 200px; width:80%;"></div>

<br />
<br />

<div id="prospect_detail" style="max-height: 200px; width:80%; overflow-y: scroll;"></div>

<br />
<br />

<div id="prospect_info" style="max-height: 200px; width:80%; overflow-y: scroll;"></div>
<br />
<br />
<div id="prospect_info_datewise" style="max-height: 150px; width:80%; overflow-y: scroll;"></div>

</center>
<?php
}
?>
<script>
function clearprospectdiv()
{
	document.getElementById("prospect_info").innerHTML = "";
}

function changecolor(id)
{
	document.getElementById(id).style.color = "red";
}
</script>
