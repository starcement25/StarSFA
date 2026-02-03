<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$DO_no=$_REQUEST['DO_no'];
    $sqlquery="SELECT dns_DO_no FROM DO_transaction WHERE DO_no='".$DO_no."'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count > 0)
	{
	   $rowdnsno=mysqli_fetch_assoc($result);
	   echo $dns_DO_no=$rowdnsno['dns_DO_no'];
	}
	else
	{
		echo '0';
	}
		
	$url = APICALLLOGURL."/DO_dns_no_download.php?nick_name=$nick_name&DO_no=$DO_no";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=DO_no.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
