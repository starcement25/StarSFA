<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<body>
<center>


<?php
if($_GET['value']==1)
{
	@$current_date = date('Y-m-d');
	$condition = "substring(LO.date,1,10)="."'".$current_date."'";
	$value = 1;

}
else if($_GET['value']==2)
{
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "(YEAR(LO.date) =". $year." AND MONTH(LO.date) =" .$month.")";
	$value = 3;
}
else if($_GET['value']==3)
{
	@$current_date = date('Y-m-d');
	$begin_year = date('Y-m-d', strtotime('first day of January this year'));
	$month_last_date = date('Y-m-d', strtotime('last day of this month this year'));
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$condition = "(LO.date BETWEEN '".$begin_year." "."12:00:01' AND '".$month_last_date." "."23:59:59')";
	$value = 3;
}
else if($_GET['value']==4){
	$start_date=$_GET['start_date'];
	$end_date=$_GET['end_date'];
	$condition = "(date(LO.date) BETWEEN '".$start_date."' AND '".$end_date."')";
}


$count = 1;
$sql_prospect = "SELECT EM.emp_name, PCH.customer_name, PCH.phone_no, PCH.customer_code, PCH.area,PCH.pin,LO.latt, LO.longi FROM employee_master EM, prospective_customer_header PCH, location LO WHERE LO.trans_id=PCH.trans_id AND  EM.emp_code=substring(LO.trans_id,3,5) AND ".$condition." AND LO.trans_id LIKE 'DC%' AND EM.emp_code='$_GET[emp_code]'";
$res_prospect = mysql_query($sql_prospect);
$row_prospect = mysql_fetch_array($res_prospect);
if($row_prospect=='')
{
	echo "No records found"."<br>";
	die;
}


echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">
		<tr>
			<td class=\"TDHEAD\" colspan=\"6\" align=\"center\">$row_prospect[emp_name]</td>
		</tr>
		<tr class=\"TDHEAD_SUB\">
			<td>SI</td>
			<td>Prospects Name</td>
			<td>Phone</td>
			<td>Route Name</td>
			<td>Pin</td>
			<td></td>
		</tr>";

$res_prospect = mysql_query($sql_prospect);
while($row_prospect = mysql_fetch_array($res_prospect))
{
	$route_code = $row_prospect['area'];
	$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
	$res_route_name = mysql_query($sql_route_name);
	$row_route_name = mysql_fetch_array($res_route_name);
	$route_name = $row_route_name['route_name'];
	$pin = $row_prospect['pin'];
	echo "<tr>
			<td>$count</td>
			<td><a href=\"#\" id=\"pros$count\" onClick=\"GenericAjaxFunction('prospect_details.php?pcid=$row_prospect[customer_code]&route_name=$row_prospect[route_name]','prospect_info',0); changecolor(id);\" style=\"color:blue;\">$row_prospect[customer_name]</a></td>
			<td>$row_prospect[phone_no]</td>
			<td>$route_name</td>
			<td>$pin</td>
			<td><a href=\"#\" id=\"locate$count\" style=\"color:blue;\"  onclick=\"window.open('prospect_locate.php?get_latt=$row_prospect[latt]&get_longi=$row_prospect[longi]&cust_name=$row_prospect[customer_name]','locate','width=600,height=400'); changecolor(id);\">Locate</a></td>
		  </tr>";
	$count++;
}
echo "</table>";
echo "</div>";
?>

</center>
<?php
mysql_close($link);
?>
