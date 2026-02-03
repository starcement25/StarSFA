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
$currentmonth=$year.$month;

$sqlcategory = "select ISNULL(SAT.categoryval,'NA') categoryval,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from ( select ID.*, (SELECT COP.VDESC FROM CSOPTFD COP,ICITEM IC WHERE IC.SEGMENT2=COP.value AND COP.OPTFIELD='CATEGORY'  AND IC.ITEMNO=ID.ITEM) AS categoryval FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ and SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' AND (IH.location LIKE '1%' or IH.location LIKE 'RUIYA%' or IH.location LIKE '3004%') 
UNION 
select ID.*,(SELECT COP.VDESC FROM CSOPTFD COP,ICITEM IC WHERE IC.SEGMENT2=COP.value AND COP.OPTFIELD='CATEGORY'  AND IC.ITEMNO=ID.ITEM) AS categoryval FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(IH.location AS varchar(20)),1,4) and SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$currentmonth."' AND (IH.location LIKE '3%' and IH.location not LIKE '3004%') ) SAT group by SAT.categoryval";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmtcategory = sqlsrv_query( $conn, $sqlcategory , $params, $options);
if( $stmtcategory === false) {
   die( print_r( sqlsrv_errors(), true) );
}
$row_count_category = sqlsrv_num_rows( $stmtcategory );
//echo "Row Count: ".$row_count."<br>";
while( $row_category = sqlsrv_fetch_array( $stmtcategory, SQLSRV_FETCH_NUMERIC) ) {
    // echo $row[0].", ".$row[1]."<br />";
	$responsecategory[$row_category[0]]= array(
					"tot_qty"=>floor($row_category[1]),
					"tot_amount"=>round($row_category[2],2)
				);
   $json_array['categoryYTDsale'] = $responsecategory;
}

echo json_encode($json_array); 	
sqlsrv_free_stmt( $stmtcategory);
sqlsrv_close($conn);
?>