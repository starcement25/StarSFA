<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ABDOS");
$link=mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB,$link);
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
	
	//For order
	$sqlselorder="SELECT customer_code,order_no FROM order_header_old";
	$rsselorder=mysqli_query($link,$sqlselorder);
	while($rowselorder=mysqli_fetch_assoc($rsselorder))
	{
		$customer_code_old=$rowselorder['customer_code'];
		$order_no_old=$rowselorder['order_no'];
		$sqlorder  = "UPDATE order_header SET customer_code='".$customer_code_old."' WHERE order_no='".$order_no_old."'";
		mysqli_query($link,$sqlorder);
	}
	
	//For stock
	$sqlselstock="SELECT customer_code,transaction_id FROM stock_audit_old";
	$rsselstock=mysqli_query($link,$sqlselstock);
	while($rowselstock=mysqli_fetch_assoc($rsselstock))
	{
		$customer_code_old_stk=$rowselstock['customer_code'];
		$transaction_id_old=$rowselstock['transaction_id'];
		$sqlaudit  = "UPDATE stock_audit SET customer_code='".$customer_code_old_stk."' WHERE transaction_id='".$transaction_id_old."'";
		mysqli_query($link,$sqlaudit);
	}
	echo 'success';
?>