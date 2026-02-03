<?php
//include "web_check.php";
session_start();
//require("functions.php");
//include "star_connection.php";
$serverName = "172.20.31.183";
	$connectionInfotwo = array( "Database"=>"KUCMIS", "UID"=>"erpdata", "PWD"=>"Erp@Data");
	/*$contwo = sqlsrv_connect( $serverName, $connectionInfotwo);
$the_sg_pcat_array = array();
$the_sg_wk_coll_array = array();
$curr_fy_array = array();
$sale_each_week_total_arr = array();
$collection_each_week_total_arr = array();
$week_val_array=array();
$the_page_name = "weekly_flash_report.php";
$the_next_page_name = "weekly_flash_zone_report_by_segment.php";
$back_qry_strng = "";
//$currYearMonth = date("Ym");
//$currYearMonth = "202001";
$financialYearMonth=(date("Y")-1).'04';
$sl_day_wise_default = date("Ym");
$sl_day_wise = @$_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : $sl_day_wise_default;
$weekNumbersArr = array(1,2,3,4);
$segmentWiseTWeekTotalArr = array();
$segmentWiseTWeekTotalArrColl = array();
$phone_no=$_GET['phone_no'];
if($_SESSION['salid_emp']=='')
{
	$sqlloginchk="SELECT salid_emp,salname FROM sales_master WHERE phone_no='".$phone_no."'";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$stmtloginchk = sqlsrv_query( $contwo, $sqlloginchk , $params, $options);
	$row_count_chk = sqlsrv_num_rows( $stmtloginchk );
	if($row_count_chk >0)
	{ 
		 $rowloginchk = sqlsrv_fetch_array( $stmtloginchk, SQLSRV_FETCH_NUMERIC);
		 $salid=$rowloginchk[0];
		 $_SESSION['phone_no']=$phone_no;
		 $_SESSION['salid_emp']= $rowloginchk[0];
		  $_SESSION['salname']= $rowloginchk[1];
	}
	$salid_emp=$_SESSION['salid_emp'];
}
else
{		 
$salid_emp=$_SESSION['salid_emp'];
}
if($salid_emp=='Director')
{
$sql = "select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from ( select ID.*,(DATEPART(wk,CONVERT(DATE, CAST(IH.INVDATE AS varchar(20)), 112))) WEEKVAL,(select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=IH.CUSTOMER AND OPTFIELD='ARSEG') segment FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ AND SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$sl_day_wise."' AND (IH.location LIKE '1%' or IH.location LIKE 'RUIYA%' or IH.location LIKE '3004%') UNION
select ID.*,(DATEPART(wk,CONVERT(DATE, CAST(IH.INVDATE AS varchar(20)), 112))) WEEKVAL,c.segment FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(IH.location AS varchar(20)),1,4) AND SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$sl_day_wise."' AND (IH.location LIKE '3%' and IH.location not LIKE '3004%') ) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";	

$sq2_collection = "select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.AMTRMIT) AS tot_amount from ( select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL,(select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=AR.IDCUST AND OPTFIELD='ARSEG') segment FROM ARTCR AR INNER JOIN ARCUS AC  ON AR.IDCUST=AC.IDCUST AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$sl_day_wise."'
AND (AC.IDGRP LIKE '1%' or AC.IDGRP LIKE 'RUIYA%' or AC.IDGRP LIKE '3004%') UNION
select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL,c.segment FROM ARTCR AR INNER JOIN ARCUS AC ON AR.IDCUST=AC.IDCUST INNER JOIN sales_hierarchy_new c ON c.SUBZONE=SUBSTRING(CAST(AC.IDGRP AS varchar(20)),1,4) AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$sl_day_wise."' AND (AC.IDGRP LIKE '3%' and AC.IDGRP not LIKE '3004%') ) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";
}
else
{
	$sqlsegment="SELECT segment FROM sales_master WHERE salid_emp='".$salid_emp."'";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$stmtsegment = sqlsrv_query( $contwo, $sqlsegment , $params, $options);
$rowsegment = sqlsrv_fetch_array( $stmtsegment, SQLSRV_FETCH_NUMERIC);
$segment_val=$rowsegment[0];
$parameter_one=$segment_val;
$parameter_two='';
$type='segment';
$employee_hierarchy=return_sales_hierarchy_all($parameter_one,$parameter_two,$type,$salid_emp);

	 if(strtolower($segment_val)!='branch')
	 {
		 $sql = "select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from ( select ID.*,(DATEPART(wk,CONVERT(DATE, CAST(IH.INVDATE AS varchar(20)), 112))) WEEKVAL,(select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=IH.CUSTOMER AND OPTFIELD='ARSEG') segment FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ AND SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$sl_day_wise."' AND (IH.location LIKE '1%' or IH.location LIKE 'RUIYA%' or IH.location LIKE '3004%') AND (select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=IH.CUSTOMER AND OPTFIELD='ARSEG')='".$segment_val."' INNER JOIN ARCUS ON ARCUS.IDCUST= IH.CUSTOMER INNER JOIN [KUCMIS].[dbo].sales_master SM  
			ON ARCUS.NAMECTAC=SM.salid AND SM.salid IN(".$employee_hierarchy.")) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";
		
		$sq2_collection = "select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.AMTRMIT) AS tot_amount from ( select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL,(select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=AR.IDCUST AND OPTFIELD='ARSEG') segment FROM ARTCR AR INNER JOIN ARCUS AC ON AR.IDCUST=AC.IDCUST AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$sl_day_wise."'
AND (AC.IDGRP LIKE '1%' or AC.IDGRP LIKE 'RUIYA%' or AC.IDGRP LIKE '3004%') AND (select ISNULL(VALUE,'NA') from ARCUSO where IDCUST=AR.IDCUST AND OPTFIELD='ARSEG')='".$the_segment_name."' INNER JOIN ARCUS ON ARCUS.IDCUST= AR.IDCUST INNER JOIN [KUCMIS].[dbo].sales_master SM  
			ON ARCUS.NAMECTAC=SM.salid AND SM.salid IN(".$employee_hierarchy.")) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";
	 }
	else
	{
		  $subzonedetails=str_replace("'","",$employee_hierarchy);
		  $subzonedetailsarray=explode(",",$subzonedetails);
		  $subzonestring='';
		  foreach($subzonedetailsarray as $subzoneval)
		  {
			  $subzonestring=$subzonestring."'".$subzoneval."'".",";
		  }
		  $subzonestring=substr($subzonestring,0,-1);
		  $sql="select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.QTYSHIPPED) AS tot_qty,SUM(SAT.TBASE1) AS tot_amount from (
select ID.*,(DATEPART(wk,CONVERT(DATE, CAST(IH.INVDATE AS varchar(20)), 112))) WEEKVAL,'Branch' As segment FROM OEINVD ID INNER JOIN OEINVH IH ON ID.INVUNIQ=IH.INVUNIQ AND SUBSTRING(CAST(IH.INVDATE AS varchar(20)),1,6)='".$sl_day_wise."' AND (IH.location LIKE '3%' and IH.location not LIKE '3004%') AND  SUBSTRING(CAST(IH.location AS varchar(20)),1,4) IN(".$subzonestring.")) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";	

		$sq2_collection = "select ISNULL(SAT.segment,'NA') segment,SAT.WEEKVAL,SUM(SAT.AMTRMIT) AS tot_amount from(select AR.*,(DATEPART(wk,CONVERT(DATE, CAST(AR.DATERMIT AS varchar(20)), 112))) WEEKVAL,'Branch' As segment FROM ARTCR AR INNER JOIN ARCUS AC ON AR.IDCUST=AC.IDCUST AND SUBSTRING(CAST(AR.DATERMIT AS varchar(20)),1,6)='".$sl_day_wise."' AND (AC.IDGRP LIKE '3%' and AC.IDGRP not LIKE '3004%') AND  SUBSTRING(CAST(AC.IDGRP AS varchar(20)),1,4) IN(".$subzonestring.")) SAT group by SAT.segment,SAT.WEEKVAL ORDER BY SAT.WEEKVAL ASC";
	}
}
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );

$stmt = sqlsrv_query( $conn, $sql , $params, $options);
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$row_count = sqlsrv_num_rows( $stmt );
if($row_count>0){
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
	$segment = trim($row[0]);
	$week_val = trim($row[1]);
	$tot_qty = intval(trim($row[2]));
	$tot_amount = $row[3];
	$the_sg_pcat_array[$segment][$week_val] = $tot_amount;
	if(!in_array($week_val,$week_val_array))
	{
		array_push($week_val_array,$week_val);
		$week_val_string.=$week_val.",";
	}
	}
	$week_val_string=substr($week_val_string,0,-1);
}

$stmt2 = sqlsrv_query( $conn, $sq2_collection , $params, $options);
if( $stmt2 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$row_count2 = sqlsrv_num_rows( $stmt2 );
if($row_count2>0){
	while( $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_NUMERIC) ) {
	$segment_coll = trim($row2[0]);
	$week_val_coll = trim($row2[1]);
	$tot_amount_coll = $row2[2];
	$the_sg_wk_coll_array[$segment_coll][$week_val_coll] = $tot_amount_coll;
	
	}
}

sqlsrv_free_stmt( $stmt);
sqlsrv_free_stmt( $stmt2);
sqlsrv_close($conn);
include "web_header.php";*/
?>
<style>

.table-scroll {
position:relative;
max-width:100%;
margin:auto;
overflow:hidden;
border:1px solid #e2e2e2;
}
.table-wrap {
width:100%;
overflow:auto;
}
.table-scroll table {
width:100%;
margin:auto;
border-collapse:separate;
border-spacing:0;
}
.table-scroll td {
padding:10px 10px;
border:1px solid #e2e2e2;
white-space:nowrap;
vertical-align:top;
}

.table-scroll th {
padding:10px 10px;
border:1px solid #e2e2e2;
background:#0065B2;
color:#ffffff;
white-space:nowrap;
vertical-align:top;
}
.table-scroll thead, .table-scroll tfoot {
background:#f9f9f9;
}
th:first-child, td:first-child
{
  position:sticky;
  left:0px;
}

td:first-child { background-color: #E90118;}
tbody .collection { background-color:#CCC;}


</style>
<script type="text/javascript">
jQuery(function(){
	jQuery("#sl_day_wise").change(function(){
		var sl_day_wise = jQuery(this).val();
		jQuery(".page-loader-wrapper").show();
		window.location = "<?php echo $the_page_name;?>?sl_day_wise="+sl_day_wise;
	});
});

</script>
<section class="content">
<div class="container-fluid">

<div class="row">
<!--div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
<div class="form-group">
  <label for="sel1">Date:</label>
  <label for="sel1"><?php //echo date("d.m.y");?></label>
</div>
</div-->
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<?php 
	$previous_month='04';
	$previous_month_date= (date('Y')-1).'-'.$previous_month.'-'.'01';
	$months = array();
	for ($x = $previous_month; $x < $previous_month + 12; $x++) {
		$year=substr($previous_month_date,0,4);
		$key=$year.date('m', mktime(0, 0, 0, $x, 1));
		$months[$key] = date('F', mktime(0, 0, 0, $x, 1)).'-'.$year;
		$previous_month_date = date("Y-m-d", strtotime("+1 month", strtotime($previous_month_date)));
	}
?>
<div class="form-group">
  <!--label for="usr">Month:</label>
  <label for="sel1"><?php //echo date("M'y");?></label-->
  <label for="sel1">Select Month:</label>
  <select class="form-control"  id="sl_day_wise">
    <?php
	foreach($months as $num => $monthvalue){
		if($sl_day_wise==$num ){ $selected="selected" ; } 
		else $selected="" ;
		
		echo "<option value=".$num." $selected>".$monthvalue."</option>";
	}
	?>
  </select>
</div>
</div>

</div>

</div>

<div class="row clearfix">
<div class="card">
<div id="table-scroll" class="table-scroll">
<div class="table-wrap">

<table class="main-table">
  <thead>
  
    <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr>
  </thead>
  <tbody>
<tr>
<td></td>
<td colspan="10" ><strong>Sales</strong></td>
<td colspan="8"  style="background-color:#CCC"><strong>Collection</strong></td>
</tr> 
 <tr>
      <th>Segmenttttttttttt</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> <tr>
      <th>Segment</th>
      <th>Original</th>
      <th>Outlook</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>% of Outlook<br> Target</th>
	  <th>Remaining To Be<br> Achieved</th>
      <th>Asking <br>Rate/ Day</th>
      <th>Target</th>
      <th>WTD1</th>
      <th>WTD2</th>
      <th>WTD3</th>
      <th>WTD4</th>
      <th>MTD</th>
      <th>To be Achieved</th>
      <th>Outlook</th>
    </tr> 
  <?php
  //exit();
 if($the_sg_pcat_array>0){
	foreach( $the_sg_pcat_array as $ki=>$the_sg_pcat_array_val) {
	$segment_val = $ki;
	?>
	<tr>
      <td  style="color:#fff">
	   <a href="<?php echo $the_next_page_name.'?the_segment_name='.urlencode($segment_val).'&sl_day_wise='.$sl_day_wise.'&week_val_string='.$week_val_string;?>" style="color:#fff"><?php echo $segment_val; ?></a>
	  </td>
      <td></td>
      <td></td>
      <?php 
	  $slweek=1;
	  foreach($week_val_array as $week_val_array_val){
		  if($slweek <=4)
		  {
		  ?>
      <td style="text-align:right;">
	  <?php if(array_key_exists($week_val_array_val,$the_sg_pcat_array[$segment_val])){ 
	  $firstWeek = $the_sg_pcat_array[$segment_val][$week_val_array_val];
	  if(array_key_exists("wtd1",$sale_each_week_total_arr)){
		  $sale_each_week_total_arr["wtd1"] = ($sale_each_week_total_arr["wtd1"] + $firstWeek);
	  }else{
		 $sale_each_week_total_arr["wtd1"] = $firstWeek; 
	  }
	  
	  if(array_key_exists($segment_val,$segmentWiseTWeekTotalArr)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArr[$segment_val])){
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] + $firstWeek);
		  }else{
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $firstWeek;
		  }
	  }else{
		$segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $firstWeek;  
	  }
	  ${'weekly_total_val'.$week_val_array_val}= ${'weekly_total_val'.$week_val_array_val}+$firstWeek;
	  echo number_format($firstWeek,2); } else{echo '0.00';}  ?>
	  </td>
      <?php $slweek++; }}?>
      <!--td>
	  <?php /*if(array_key_exists(2,$the_sg_pcat_array[$segment_val])){ $secondWeek = $the_sg_pcat_array[$segment_val][2];
	  if(array_key_exists("wtd2",$sale_each_week_total_arr)){
		  $sale_each_week_total_arr["wtd2"] = ($sale_each_week_total_arr["wtd2"] + $secondWeek);
	  }else{
		 $sale_each_week_total_arr["wtd2"] = $secondWeek; 
	  }
if(array_key_exists($segment_val,$segmentWiseTWeekTotalArr)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArr[$segment_val])){
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] + $secondWeek);
		  }else{
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $secondWeek;
		  }
	  }else{
		$segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $secondWeek;  
	  }

	  echo number_format($secondWeek,2); } ?>
	  </td>
      <td>
	  <?php if(array_key_exists(3,$the_sg_pcat_array[$segment_val])){ $thirdWeek = $the_sg_pcat_array[$segment_val][3];
	  
	  if(array_key_exists("wtd3",$sale_each_week_total_arr)){
		  $sale_each_week_total_arr["wtd3"] = ($sale_each_week_total_arr["wtd3"] + $thirdWeek);
	  }else{
		 $sale_each_week_total_arr["wtd3"] = $thirdWeek; 
	  }
	  
if(array_key_exists($segment_val,$segmentWiseTWeekTotalArr)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArr[$segment_val])){
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] + $thirdWeek);
		  }else{
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $thirdWeek;
		  }
	  }else{
		$segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $thirdWeek;  
	  }

	  echo number_format($thirdWeek,2); } ?>
	  </td>
      <td>
	  <?php if(array_key_exists(4,$the_sg_pcat_array[$segment_val])){ $fourthWeek = $the_sg_pcat_array[$segment_val][4];

	  if(array_key_exists("wtd4",$sale_each_week_total_arr)){
		  $sale_each_week_total_arr["wtd4"] = ($sale_each_week_total_arr["wtd4"] + $fourthWeek);
	  }else{
		 $sale_each_week_total_arr["wtd4"] = $fourthWeek; 
	  }
	  
if(array_key_exists($segment_val,$segmentWiseTWeekTotalArr)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArr[$segment_val])){
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] + $fourthWeek);
		  }else{
			  $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $fourthWeek;
		  }
	  }else{
		$segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"] = $fourthWeek;  
	  }

	  echo number_format($fourthWeek,2); } */?>
	  </td-->
      <td style="text-align:right;">
	  <?php
	  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArr[$segment_val])){
		  $the_segment_sale_mtd = $segmentWiseTWeekTotalArr[$segment_val]["segWeekTot"];
			if(array_key_exists("mtd",$sale_each_week_total_arr)){
			$sale_each_week_total_arr["mtd"] = ($sale_each_week_total_arr["mtd"] + $the_segment_sale_mtd);
			}else{
			$sale_each_week_total_arr["mtd"] = $the_segment_sale_mtd; 
			}
			  echo number_format($the_segment_sale_mtd,2);
		  }
	  ?>
	  </td>
      <td></td>
	  <td></td>
      <td></td>
      <td class="collection"></td>
      <?php 
	  $slweekcol=1;
	  foreach($week_val_array as $week_val_array_val){
		  if($slweekcol <=4)
		  {
		  ?>
      <td class="collection" style="text-align:right;">
	  <?php if(array_key_exists($week_val_array_val,$the_sg_wk_coll_array[$segment_val])){ $firstWeek_coll = $the_sg_wk_coll_array[$segment_val][$week_val_array_val];
	  
	  if(array_key_exists("wtd1",$collection_each_week_total_arr)){
		  $collection_each_week_total_arr["wtd1"] = ($collection_each_week_total_arr["wtd1"] + $firstWeek_coll);
	  }else{
		 $collection_each_week_total_arr["wtd1"] = $firstWeek_coll; 
	  }
	  
	  if(array_key_exists($segment_val,$segmentWiseTWeekTotalArrColl)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArrColl[$segment_val])){
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] + $firstWeek_coll);
		  }else{
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $firstWeek_coll;
		  }
	  }else{
		$segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $firstWeek_coll;  
	  }
		//$week_val_col_total_array[$week_val_array_val]=$week_val_col_total_array[$week_val_array_val]+$firstWeek_coll;
		${'weekly_col_total_val'.$week_val_array_val}= ${'weekly_col_total_val'.$week_val_array_val}+$firstWeek_coll;
	  echo number_format($firstWeek_coll,2); } else{echo '0.00';} ?>
	  </td>
      <?php $slweekcol++; }}?>
      <!--td>
	  
	  <?php /*if(array_key_exists(2,$the_sg_wk_coll_array[$segment_val])){ $secondWeek_coll = $the_sg_wk_coll_array[$segment_val][2];
	  
	  if(array_key_exists("wtd2",$collection_each_week_total_arr)){
		  $collection_each_week_total_arr["wtd2"] = ($collection_each_week_total_arr["wtd2"] + $secondWeek_coll);
	  }else{
		 $collection_each_week_total_arr["wtd2"] = $secondWeek_coll; 
	  }
	  
	  if(array_key_exists($segment_val,$segmentWiseTWeekTotalArrColl)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArrColl[$segment_val])){
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] + $secondWeek_coll);
		  }else{
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $secondWeek_coll;
		  }
	  }else{
		$segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $secondWeek_coll;  
	  }

	  echo number_format($secondWeek_coll,2); } ?>
	  
	  </td>
      <td>
	  
	  <?php if(array_key_exists(3,$the_sg_wk_coll_array[$segment_val])){ $thirdWeek_coll = $the_sg_wk_coll_array[$segment_val][3];
	  
	  if(array_key_exists("wtd3",$collection_each_week_total_arr)){
		  $collection_each_week_total_arr["wtd3"] = ($collection_each_week_total_arr["wtd3"] + $thirdWeek_coll);
	  }else{
		 $collection_each_week_total_arr["wtd3"] = $thirdWeek_coll; 
	  }
	  
	  if(array_key_exists($segment_val,$segmentWiseTWeekTotalArrColl)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArrColl[$segment_val])){
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] + $thirdWeek_coll);
		  }else{
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $thirdWeek_coll;
		  }
	  }else{
		$segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $thirdWeek_coll;  
	  }

	  echo number_format($thirdWeek_coll,2); } ?>
	  
	  </td>
      <td>
	  
	  <?php if(array_key_exists(4,$the_sg_wk_coll_array[$segment_val])){ $fourthWeek_coll = $the_sg_wk_coll_array[$segment_val][4];
	  
	  if(array_key_exists("wtd4",$collection_each_week_total_arr)){
		  $collection_each_week_total_arr["wtd4"] = ($collection_each_week_total_arr["wtd4"] + $fourthWeek_coll);
	  }else{
		 $collection_each_week_total_arr["wtd4"] = $fourthWeek_coll; 
	  }
	  
	  if(array_key_exists($segment_val,$segmentWiseTWeekTotalArrColl)){
		  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArrColl[$segment_val])){
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = ($segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] + $fourthWeek_coll);
		  }else{
			  $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $fourthWeek_coll;
		  }
	  }else{
		$segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"] = $fourthWeek_coll;  
	  }

	  echo number_format($fourthWeek_coll,2); } */?>
	  
	  </td-->
      <td class="collection" style="text-align:right;">
	  <?php
	  if(array_key_exists("segWeekTot",$segmentWiseTWeekTotalArrColl[$segment_val])){
		  $the_segment_collection_mtd = $segmentWiseTWeekTotalArrColl[$segment_val]["segWeekTot"];
			if(array_key_exists("mtd",$collection_each_week_total_arr)){
			$collection_each_week_total_arr["mtd"] = ($collection_each_week_total_arr["mtd"] + $the_segment_collection_mtd);
			}else{
			$collection_each_week_total_arr["mtd"] = $the_segment_collection_mtd; 
			}
			  echo number_format($the_segment_collection_mtd,2);
		  }
	  ?>
	  </td>
      <td class="collection"></td>
      <td class="collection"></td>
    </tr>
	<?php
	}
	?>
    </tbody>
     <tfoot>
	<tr>
<th>GRAND TOTAL</th>
<th></th>
<th></th>
<!--td>
<?php
/*if(array_key_exists("wtd1",$sale_each_week_total_arr)){
	echo number_format($sale_each_week_total_arr["wtd1"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd2",$sale_each_week_total_arr)){
	echo number_format($sale_each_week_total_arr["wtd2"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd3",$sale_each_week_total_arr)){
	echo number_format($sale_each_week_total_arr["wtd3"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd4",$sale_each_week_total_arr)){
	echo number_format($sale_each_week_total_arr["wtd4"],2);
}*/
?>
</td-->
<?php 
//print_r($week_val_total_array);
$slweektotal=1;
 foreach($week_val_array as $week_val_array_val){
	if($slweektotal <=4)
	  {
	?>
     <th style="text-align:right;">
    <?php		  
        echo number_format(${'weekly_total_val'.$week_val_array_val},2);
		$mtd_total=$mtd_total+${'weekly_total_val'.$week_val_array_val};
		?>
      </th>
        <?php
		}
		$slweektotal++;
	}?> 
<th style="text-align:right;">
<?php
/*if(array_key_exists("mtd",$sale_each_week_total_arr)){
	echo number_format($sale_each_week_total_arr["mtd"],2);
}*/
echo number_format($mtd_total,2);
?>
</th>
<th></th>
<th></th>
<th></th>
<th></th>
<!--td>
<?php
/*if(array_key_exists("wtd1",$collection_each_week_total_arr)){
	echo number_format($collection_each_week_total_arr["wtd1"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd2",$collection_each_week_total_arr)){
	echo number_format($collection_each_week_total_arr["wtd2"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd3",$collection_each_week_total_arr)){
	echo number_format($collection_each_week_total_arr["wtd3"],2);
}
?>
</td>
<td>
<?php
if(array_key_exists("wtd4",$collection_each_week_total_arr)){
	echo number_format($collection_each_week_total_arr["wtd4"],2);
}*/
?>
</td-->
<?php 
$slweektotalcol=1;
 foreach($week_val_array as $week_val_array_val){
		if($slweektotalcol <=4)
		  {
		?>
        <th style="text-align:right;">
        <?php	  
        echo number_format(${'weekly_col_total_val'.$week_val_array_val},2);
		$mtd_col_total=$mtd_col_total+${'weekly_col_total_val'.$week_val_array_val};
		?>
        </th>
        <?php
		  }
		  $slweektotalcol++; 
		}?> 
<th style="text-align:right;">
<?php

/*if(array_key_exists("mtd",$collection_each_week_total_arr)){
	echo number_format($collection_each_week_total_arr["mtd"],2);
}*/
echo number_format($mtd_col_total,2);
?>
</th>
<th></th>
<th></th>
</tr>
	 </tfoot>
	<?php
  }
  ?>
  
</table>

</div>
</div>
</div>
</div>


</div>
</section>
<script>
$('table').on('scroll', function() {
  $("table > *").width($("table").width() + $("table").scrollLeft());
});
</script>

                        
<?php
include "web_footer.php";
?>