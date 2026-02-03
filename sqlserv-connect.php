<?php
$serverName = "172.20.31.183";
$connectionInfo = array( "Database"=>"BAPLDT", "UID"=>"erpdata", "PWD"=>"Erp@Data");
$connectionInfoone = array( "Database"=>"BAPLCO", "UID"=>"erpdata", "PWD"=>"Erp@Data");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
$connone=sqlsrv_connect( $serverName, $connectionInfoone);

if( $conn ) {
    echo "Connection established.<br />";
}else{
    echo "Connection could not be established.<br /><pre>";
    die( print_r( sqlsrv_errors(), true));
}
if( $connone ) {
    echo "Connection established.<br />";
}else{
    echo "Connection could not be established.<br /><pre>";
    die( print_r( sqlsrv_errors(), true));
}

$sql = "select * from [OEINVD] where ORDNUMBER='ORD000000000001'";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmt = sqlsrv_query( $conn, $sql , $params, $options);
if( $stmt === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count = sqlsrv_num_rows( $stmt );
echo "Row Count: ".$row_count."<br>";
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
     echo $row[0].", ".$row[1]."<br />";
}
sqlsrv_free_stmt( $stmt);
sqlsrv_close($conn);
echo 'previous year<br />';
echo'------------------';
$sql = "select TOP 50 INVDATE,INVFISCPER from [OEINVH] ORDER BY INVDATE ASC";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmt = sqlsrv_query( $connone, $sql , $params, $options);
if( $stmt === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count = sqlsrv_num_rows( $stmt );
echo "Row Count: ".$row_count."<br>";
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
     echo $row[0].", ".$row[1]."<br />";
}
sqlsrv_free_stmt( $stmt);
sqlsrv_close($connone);
?>