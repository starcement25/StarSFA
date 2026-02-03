<?php
    $serverName = "SERVER"; //serverName\instanceName["serverName\\sqlexpress, 1542"; //serverName\instanceName, portNumber (default is 1433)]
    $connectionInfo = array( "Database"=>"base", "UID"=>"ex", "PWD"=>"******");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
;

        if( $conn ) {
             echo "Conexión establecida.<br />";
        }else{
            echo "Conexión no se pudo establecer.<br />";
            die( print_r( sqlsrv_errors(), true));
        }

    $sql = "SELECT name FROM [base].[dbo].[table]";
    $params = array();
    $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
    $stmt = sqlsrv_query( $conn, $sql , $params, $options );

    $row_count = sqlsrv_num_rows( $stmt );
  

        if ($row_count === false)
           echo "Error al obtener datos.";
        else
           echo "bien";
        //echo $row_count;

        while( $row = sqlsrv_fetch_array( $stmt) ) {
              print json_encode($row);
        }

    sqlsrv_close($conn);
?>