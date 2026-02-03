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
$sqlcountprospect="SELECT trans_id FROM prospective_customer_header WHERE customer_code = '$_GET[pcid]'";
$rscountprospect=mysql_query($sqlcountprospect);
$cntprospect=mysql_num_rows($rscountprospect);

$sql_prospect = "SELECT * FROM prospective_customer_header WHERE customer_code = '$_GET[pcid]' ORDER BY DATE_FORMAT(SUBSTRING(trans_id,-14,14),'%Y-%m-%d %H:%i:%s') ASC LIMIT 0,1";
$res_prospect = mysql_query($sql_prospect);
$row_prospect = mysql_fetch_array($res_prospect); 

$sql_tagged_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '$row_prospect[tagged_customer_code]'";
$res_tagged_customer_name = mysql_query($sql_tagged_customer_name);
$row_tagged_customer_name = mysql_fetch_array($res_tagged_customer_name);

$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '$row_prospect[area]'";
$res_route_name = mysql_query($sql_route_name);
$row_route_name = mysql_fetch_array($res_route_name);
$route_name=$row_route_name['route_name'];
if($route_name =='') $route_name=$row_prospect[area];

?>

<table border="1" width="100%" style="border-collapse:collapse;" class="border">
  <tr>
  	<td colspan="2" align="center" class="TDHEAD">Prospect Details</td>
  </tr>
  <tr>
  	<td><b>Prospect Name:</b></td>
    <td><?php echo $row_prospect['customer_name']; ?></td>
  </tr>
  <tr>
  	<td><b>Address:</b></td>
    <td><?php echo $row_prospect['address']; ?></td>
  </tr>
  <tr>
  	<td><b>Pin:</b></td>
    <td><?php echo $row_prospect['pin']; ?></td>
  </tr>
  <tr>
  	<td><b>Area:</b></td>
    <td><?php  echo $route_name; ?></td>
  </tr>
  <tr>
  	<td><b>Phone Number:</b></td>
    <td><?php echo $row_prospect['phone_no']; ?></td>
  </tr>
  <tr>
  	<td><b>Tagged Customer:</b></td>
    <td><?php echo $row_tagged_customer_name['customer_name']; ?></td>
  </tr>
  <tr>
  	<td><b>Customer Type:</b></td>
    <td><?php echo $row_prospect['cust_type']; ?></td>
  </tr>
	<tr>
  	<td><b>Created On:</b></td>
    <td><?php echo date('d-m-Y',strtotime(substr($_GET['pcid'],7,8))); ?></td>
  </tr>
	<?php //if($cntprospect > 1){?>
       <tr>
        <td align="center" colspan="2"> <a href="#" id="pros$count" onClick="GenericAjaxFunction('prospect_datewise_details.php?pcid=<?php echo $row_prospect[customer_code]?>&customer_name=<?php echo $row_prospect[customer_name];?>','prospect_info_datewise',0); changecolor(id);" style="color:red;">Details</a>
        </td>
      </tr>
    <?php //}?>
</table>

</center>
</body>
<?php
mysql_close($link);
?>
