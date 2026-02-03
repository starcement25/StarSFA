<?php
$serverName = "172.20.31.183";
$connectionInfo = array( "Database"=>"BAPLDT", "UID"=>"erpdata", "PWD"=>"Erp@Data");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

/*if( $conn ) {
    echo "Connection established.<br />";
}else{
    echo "Connection could not be established.<br /><pre>";
    die( print_r( sqlsrv_errors(), true));
}*/


$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$currentmonth=$year.$month;
//For zone wise 
/*$sql = "SELECT SHN.zone,SUM(ID.QTYSHIPPED) tot_qty,SUM(ID.TBASE1) as tot_amount FROM OEINVD ID INNER JOIN 
OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ  INNER JOIN OEORDH OH ON 
IH.ORDNUMBER=OH.ORDNUMBER INNER JOIN OEORDH1 OH1 ON OH.ORDUNIQ=OH1.ORDUNIQ INNER JOIN sales_hierarchy_new SHN 
ON SHN.SALID=OH1.ENTEREDBY WHERE SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' GROUP BY SHN.zone";*/


$sql = "select TOP 4 SAT.WEEKVAL,SUM(SAT.AMTRMIT) AS tot_amount from ( select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL FROM ARTCR AR INNER JOIN ARCUS AC  ON AR.IDCUST=AC.IDCUST AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$currentmonth."' 
AND (AC.IDGRP LIKE '1%' or AC.IDGRP LIKE 'RUIYA%' or AC.IDGRP LIKE '3004%') UNION 
select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL FROM ARTCR AR INNER JOIN ARCUS AC ON AR.IDCUST=AC.IDCUST INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(AC.IDGRP AS varchar(20)),1,4) AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$currentmonth."' AND (AC.IDGRP LIKE '3%' and AC.IDGRP not LIKE '3004%') ) SAT group by SAT.WEEKVAL ORDER BY SAT.WEEKVAL DESC  
";

$params = array(); $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET ); 
$stmt = sqlsrv_query( $conn, $sql , $params, $options); if( $stmt === false) { 
die( print_r( sqlsrv_errors(), true) ); } $row_count = sqlsrv_num_rows( $stmt ); 
//echo "Row Count: ".$row_count."<br>"; $zone_array=array(); $countrow=0; while( 
$row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) { //echo $row[0].", 
".$row[1]."<br />"; //exit(); if($countrow==0) $row[0]='Week1'; if($countrow==1) 
$row[0]='Week2'; if($countrow==2) $row[0]='Week3'; if($countrow==3) 
$row[0]='Week4'; $response[$row[0]]= array( "tot_amount"=>round($row[1],2), ); 
$json_array['collectionDetailsWeek'] = $response;
   
   $countrow++;
}
echo json_encode($json_array); 	
sqlsrv_free_stmt( $stmt);
sqlsrv_close($conn);
?>