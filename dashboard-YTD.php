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

$financialyearstart=$year.$month;
//For zone wise 
/*$sql = "SELECT SHN.zone,SUM(ID.QTYSHIPPED) tot_qty,SUM(ID.TBASE1) as tot_amount FROM OEINVD ID INNER JOIN 
OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ  INNER JOIN OEORDH OH ON 
IH.ORDNUMBER=OH.ORDNUMBER INNER JOIN OEORDH1 OH1 ON OH.ORDUNIQ=OH1.ORDUNIQ INNER JOIN sales_hierarchy_new SHN 
ON SHN.SALID=OH1.ENTEREDBY WHERE SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' GROUP BY SHN.zone";*/

$sql = "select ISNULL(SAT.zoneval,'NA') zoneval,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from ( select ID.*,(select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=IH.CUSTOMER AND OPTFIELD='ARZONE') zoneval FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ  AND (IH.location LIKE '1%' or IH.location LIKE 'RUIYA%' or IH.location LIKE '3004%') 
UNION 
select ID.*,c.zone zoneval FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(IH.location AS varchar(20)),1,4)  AND (IH.location LIKE '3%' and IH.location not LIKE '3004%') ) SAT group by SAT.zoneval
";

$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmt = sqlsrv_query( $conn, $sql , $params, $options);
if( $stmt === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count = sqlsrv_num_rows( $stmt );
//echo "Row Count: ".$row_count."<br>";
$zone_array=array();
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
    // echo $row[0].", ".$row[1]."<br />";
	$response[$row[0]]= array(
					"tot_qty"=>floor($row[1]),
					"tot_amount"=>round($row[2],2)
				);
   $json_array['zoneMTDsale'] = $response;
}
//For segment wise 
/*$sqlsegment = "SELECT COP.VDESC,SUM(ID.QTYSHIPPED) As tot_qty,SUM(ID.TBASE1) as tot_amount
FROM OEINVD ID INNER JOIN 
OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ  INNER JOIN ICITEMO IC ON 
IC.ITEMNO=ID.ITEM INNER JOIN CSOPTFD COP ON IC.value=COP.value AND COP.OPTFIELD='ITEMSEG' WHERE 
SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' GROUP BY COP.VDESC";*/

$sqlsegment = "select ISNULL(SAT.SEGMENT,'NA') SEGMENT,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from ( select ID.*, (select ISNULL(VDESC,'NA') from CSOPTFD where value in(select value from ICITEMO where OPTFIELD='ITEMSEG' and ITEMNO=ID.ITEM) and OPTFIELD='ITEMSEG' ) SEGMENT FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ AND (IH.location LIKE '1%' or IH.location LIKE 'RUIYA%' or IH.location LIKE '3004%') 
UNION 
select ID.*,(select ISNULL(VDESC,'NA') from CSOPTFD where value in(select value from ICITEMO where OPTFIELD='ITEMSEG' and ITEMNO=ID.ITEM) and OPTFIELD='ITEMSEG' ) SEGMENT FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(IH.location AS varchar(20)),1,4) AND (IH.location LIKE '3%' and IH.location not LIKE '3004%') ) SAT group by SAT.SEGMENT";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmtsegment = sqlsrv_query( $conn, $sqlsegment , $params, $options);
if( $stmt === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count_segment = sqlsrv_num_rows( $stmtsegment );
//echo "Row Count: ".$row_count."<br>";
while( $row_segment = sqlsrv_fetch_array( $stmtsegment, SQLSRV_FETCH_NUMERIC) ) {
    // echo $row[0].", ".$row[1]."<br />";
	$responsesegment[$row_segment[0]]= array(
					"tot_qty"=>floor($row_segment[1]),
					"tot_amount"=>round($row_segment[2],2)
				);
   $json_array['segmentMTDsale'] = $responsesegment;
}

//For outstanding 
$sqltotlimit="SELECT SUM(AMTCRLIMT) AS tot_limit FROM ARCUS";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmttotlimit = sqlsrv_query( $conn, $sqltotlimit , $params, $options);
$rowtotlimit = sqlsrv_fetch_array( $stmttotlimit, SQLSRV_FETCH_NUMERIC);
$tot_limit=$rowtotlimit[0];

$sqltotoutstanding="SELECT SUM([SOURCE AMT]) AS tot_outstanding FROM VWARLEDGER";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmttotoutstanding = sqlsrv_query( $conn, $sqltotoutstanding , $params, $options);
$rowtotoutstanding = sqlsrv_fetch_array( $stmttotoutstanding, SQLSRV_FETCH_NUMERIC);
$tot_outstanding=$rowtotoutstanding[0];

$tot_overdue=($tot_outstanding-$tot_limit);

$responseoutstanding['debtor']= array(
					"outstanding"=>round($tot_outstanding,2),
					"limit"=>round($tot_limit,2),
					"overdue"=>round($tot_overdue,2)
				);
$json_array['outstanding'] = $responseoutstanding;
echo json_encode($json_array); 	
sqlsrv_free_stmt( $stmt);
sqlsrv_free_stmt( $stmtsegment);
sqlsrv_close($conn);
?>