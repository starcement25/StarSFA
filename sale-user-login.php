<?php
$serverName = "172.20.31.183";
$connectionInfo = array( "Database"=>"KUCMIS", "UID"=>"erpdata", "PWD"=>"Erp@Data");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

/*if( $conn ) {
    echo "Connection established.<br />";
}else{
    echo "Connection could not be established.<br /><pre>";
    die( print_r( sqlsrv_errors(), true));
}*/
$phoneno=$_REQUEST['phoneno'];
if($phoneno !='')
{
	$sqllogin = "select phone_no FROM sales_master WHERE phone_no='".$phoneno."'";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$stmtlogin = sqlsrv_query( $conn, $sqllogin , $params, $options);
	$rowlogin= sqlsrv_fetch_array( $stmtlogin, SQLSRV_FETCH_NUMERIC);
	$phone_no=$rowlogin[0];
	
	if($phoneno ==$phone_no)
	{
		echo 'SUCCESS';
	}
	else echo 'FAILURE';
	sqlsrv_free_stmt( $stmtlogin);
}
else echo 'FAILURE';
sqlsrv_close($conn);
?>