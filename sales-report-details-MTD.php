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


$sql = "SELECT (SELECT COP.VDESC FROM CSOPTFD COP,ICITEM IC WHERE IC.SEGMENT2=COP.value AND COP.OPTFIELD='CATEGORY'  AND IC.ITEMNO=ID.ITEM) AS category,
		(SELECT COP.VDESC FROM CSOPTFD COP WHERE COP.OPTFIELD='ITEMSEG' AND COP.value=(SELECT value FROM ICITEMO WHERE OPTFIELD='ITEMSEG' AND ITEMNO=ID.ITEM)) 
		AS segment,IH.INVDATE,IH.INVUNIQ,ID.QTYSHIPPED As tot_qty,ID.TBASE1 as tot_amount
		FROM OEINVD ID INNER JOIN 
		OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ WHERE SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' ORDER BY IH.INVDATE ASC,segment ASC,category ASC";

$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmt = sqlsrv_query( $conn, $sql , $params, $options);
if( $stmt === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count = sqlsrv_num_rows( $stmt );
//echo "Row Count: ".$row_count."<br>";
$zone_array=array();
$countrow=0;
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
    //echo $row[0].", ".$row[1]."<br />";
	//exit();
	if($row[0]=='') $row[0]='N/A';
	if($row[1]=='') $row[1]='N/A';
	$response[$countrow]= array(
					"category"=>$row[1],
					"product"=>$row[0],
					"invdate"=>$row[2],
					"INVUNIQ"=>$row[3],
					"qty"=>floor($row[4]),
					"amount"=>round($row[5],2),
				);
   $json_array['saleDetailsMTD'] = $response;
   
   $countrow++;
}
echo json_encode($json_array); 	
sqlsrv_free_stmt( $stmt);
sqlsrv_close($conn);
?>