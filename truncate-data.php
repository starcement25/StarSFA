<?php
define("SERVER","216.237.114.58");
define("USER","coralweb");
define("PASSWORD","coral5071");
define("DB","CORAL");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database.");


$sqldeleteprodgroup="truncate product_group_master";
$rsdeleteprodgroup=mysqli_query($link,$sqldeleteprodgroup);

$sqldeleteprodsubgroup="truncate product_sub_group_master";
$rsdeleteprodsubgroup=mysqli_query($link,$sqldeleteprodsubgroup);

$sqlskutempdelete="truncate product_master_temp";
$rsskutempdelete=mysqli_query($link,$sqlskutempdelete);
$sqlskudelete="truncate product_master";
$rsskudelete=mysqli_query($link,$sqlskudelete);


$sqldeleteemp="truncate employee_master";
$rsdeleteemp=mysqli_query($link,$sqldeleteemp);
$sqldeletepassword="truncate changepassword";
$rsdeletepassword=mysqli_query($link,$sqldeletepassword);

$sql  = "insert into employee_master ";
$sql .= " SET emp_code='C0007'";
$sql .= " , emp_name='Common'";
mysqli_query($link,$sql);
						
$sqlcp  = "insert into changepassword ";
$sqlcp .= " SET emp_code='C0007'";
$sqlcp .= " , newpassword='1234'";
$sqlcp .= " , oldpassword='1234'"; 
$sqlcp .= " , status='true'";
$sqlcp .= " , is_licensed='1'"; 
mysqli_query($link,$sqlcp);

$sqldeleteroute="truncate route_master";
$rsdeletetroute=mysqli_query($link,$sqldeleteroute);

$sqlcusdelete="truncate customer_master";
$rscusdelete=mysqli_query($link,$sqlcusdelete);
$sqlcusdeletetemp="truncate customer_master_temp";
$rscusdeletetemp=mysqli_query($link,$sqlcusdeletetemp);


$sqldeleteout="truncate outstanding";
$rsdeleteout=mysqli_query($link,$sqldeleteout);

$sqldeletemrp="truncate mrp";
$rsdeletemrp=mysqli_query($link,$sqldeletemrp);

echo 'Truncate Successful.';
	
?>