<?php
echo $_REQUEST['type'];
//echo "a".$_SESSION['type'];
if($_REQUEST['type'] == '')
	$_SESSION['type'] = 'today';
if($_REQUEST['type'] != ''){
	$_SESSION['type'] = $_REQUEST['type'];
	$_SESSION['start_date'] = $_REQUEST['start_date'];
	$_SESSION['end_date'] = $_REQUEST['end_date'];
}
echo "b".$_SESSION['type'];
mysql_close($link);
?>