<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	
	

	if($mode =='add' || $mode =='edit')				disphtml("show_add_edit($_REQUEST[row_id]);");
	else    										   disphtml("main();");
ob_end_flush();

function main()
{
	if($_REQUEST['page']=='attendance')
	{
		$backurl='adminAttendanceTracker.php';
	}
	else if($_REQUEST['page']=='misreporthierarchy')
	{
		$backurl='adminMisReportEmphierarchy.php';
	}
	else
	{
		$backurl='adminMisReport.php';
	}
	$mode = $_REQUEST['mode'];
	if($mode=='MTD' || $mode=='monthly')
	{
		$date_condition =" AND YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())";
	}
	if($mode=='YTD')
	{
		$date_condition =" AND YEAR(date) = YEAR(CURDATE())";
	}
	if($mode=='custom'){
		$start_date = $_REQUEST['start_date'];
		$end_date = $_REQUEST['end_date'];
		$date_condition =" AND (SUBSTRING(date,1,10) BETWEEN '".$start_date."' AND '".$end_date."' ) ";
	}
	
	$sqlroutedetails="SELECT *,DATE_FORMAT(date,'%b %e ,%Y') AS date,DATE_FORMAT(date,'%T') AS time FROM location  
				WHERE emp_code='".$_REQUEST['emp_code']."' AND trans_id LIKE 'A%' ".$date_condition."";
	$rsroutedetails=mysql_query($sqlroutedetails) or die(mysql_error()." Error in select route details : ".$sqlroutedetails);	
	
	$trans_id=$rowtrans['trans_id'];
	$emp_code=$rowtrans['emp_code'];
	$time=$rowtrans['time'];
	
	$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$_REQUEST['emp_code']."'";
	$rsemp=mysql_query($sqlemp) or die(mysql_error()." Error in select employee : ".$sqlemp);
	$rowemp=mysql_fetch_array($rsemp);
	$emp_name=$rowemp['emp_name'];
	
	$routeDetails='[';
	while($rowroutedetails=mysql_fetch_array($rsroutedetails))
		{				
			$date=$rowroutedetails['date'];
			$time=$rowroutedetails['time'];
			$lat=$rowroutedetails['latt'];
			$lon=$rowroutedetails['longi'];
			
			$sql_checkout = "SELECT SUBSTRING(date,12) AS checkout_time FROM location WHERE emp_code = '".$_REQUEST['emp_code']."' AND trans_id LIKE 'CH%' AND date_time LIKE ".$date_condition;
			$res_checkout = mysql_query($sql_checkout);
			$row_checkout = mysql_fetch_array($res_checkout);
			$check_out_time = $row_checkout['checkout_time'];
			if($check_out_time == '')
				$check_out_time = '--';
			
			$checkout_details = 'Checkout:'.$check_out_time;
			
			$routeDetails.="['$emp_name', $lat, $lon,'$time $checkout_details','$date'],";
		}
	$routeDetails = rtrim($routeDetails,",");
	$routeDetails.=']';
?>
<script language="JavaScript">
//var geocoder = new GClientGeocoder();
var cnt;
var globalLat;
var globalLon;
var routes=<?=$routeDetails?>;
</script>
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Employee Attendance Details</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="99%"><a href="javascript:void(0);" style="color: #e40000" onclick="javascript:window.location='<?=$backurl?>?mode=<?php echo $_REQUEST['mode']?>&emp_code=<?php echo $_REQUEST['emp_code'];?>&page=<?php echo $_REQUEST['page'];?>&state=<?php echo $_REQUEST['state'];?>&emp_type=<?php echo $_REQUEST['emp_type'];?>&employee_lev_one=<?php echo $_REQUEST['employee_lev_one'];?>&modehierarchy=<?php echo $_REQUEST['modehierarchy'];?>'"> <img src="images/back.png" alt="back" /> </a></td>
					<td align="right" width="1%"></td>
				</tr>
			</table>
			<table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2">Attendance Details of <?=$emp_name?> </td>
				</tr>
              
                <tr>
                	<td colspan="2">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
                          <tr> 
                            <td  align="center">
                            <div id="map" style="width: 600px; height: 400px"></div>
                            </td>
                          </tr>
                          <tr> 
                            <td >&nbsp;</td>
                          </tr>
                        </table>
                    </td>
                 </tr>   
                
			</table>
			<br><br>
			</form>
		</td>
	</tr>
</table>
<?
}//End of main()

?>