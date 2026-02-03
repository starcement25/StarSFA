<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$mode = $_REQUEST['mode'];
	
	disphtml("main();");
ob_end_flush();

function main()
{
    require("include/dbcon.php");
	$visit_map = $_REQUEST['visit_map'];
	$sqltrans="SELECT *,DATE_FORMAT(date,'%b %e ,%y') AS date,DATE_FORMAT(date,'%T') AS time FROM location WHERE trans_id='".$_REQUEST['trans_id']."'";
	$rstrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction : ".$sqltrans);	
	$rowtrans=mysqli_fetch_assoc($rstrans);
	
	$trans_id=$rowtrans['trans_id'];
	$emp_code=$rowtrans['emp_code'];
	$time=$rowtrans['time'];
	$operation_type=substr($trans_id,0,1);
	$operation_type_no=substr($trans_id,1,1);
	
	$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee : ".$sqlemp);
	$rowemp=mysqli_fetch_assoc($rsemp);
	$emp_name=$rowemp['emp_name'];
	
	$sqlcustomer="SELECT CM.customer_name,RM.route_name FROM customer_master CM,route_master RM WHERE 
					CM.route_code=RM.route_code AND CM.customer_code='".$_REQUEST['customer_code']."'";
	$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer : ".$sqlcustomer);	
	$rowcustomer=mysqli_fetch_assoc($rscustomer);
	$customer=$rowcustomer['customer_name'];
	$route=$rowcustomer['route_name'];
	
	
	if($_REQUEST['page']=='prospectdetails')
	{
		$sqlcustomer="SELECT CM.customer_name,RM.route_name,CM.area FROM prospective_customer_master CM,route_master RM WHERE 
					CM.area=RM.route_code AND CM.customer_code='".$_REQUEST['customer_code']."'";
		$rscustomer=mysqli_query($link,$sqlcustomer) or die(mysqli_error()." Error in select customer : ".$sqlcustomer);	
		$rowcustomer=mysqli_fetch_assoc($rscustomer);
		$customer=$rowcustomer['customer_name'];
		$route=$rowcustomer['route_name'];
		
		if($route=='') $route=$rowcustomer['area'];
	}
	
	//echo $_SERVER['PHP_SELF'];
	
	if($_REQUEST['page']=='activity')
	{
		$backurl='adminActivity.php';
	}
	else if($_REQUEST['page']=='misreporthierarchy')
	{
		$backurl='adminMisReportEmphierarchy.php';
	}
	else
	{
		$backurl='adminMisReport.php';
	}
?>


<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqSx3h3PfPlF28bgzeHO6a_m9ZwECzUs4&sensor=false&libraries=places&callback=initialize"></script>
	
<script type="text/javascript">
function initialize() 
{
	// put latitude and longitude data here
	
	var emp_name="<?=stripslashes($emp_name);?>";
var time="<?=$time?>";
var Lat="<?=$rowtrans['latt']?>";
var Lon="<?=$rowtrans['longi']?>";

   var latinfo = new google.maps.LatLng(Lat,Lon);
   var map = new google.maps.Map(document.getElementById('map'), {
      center: latinfo,
      zoom: 13
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latinfo,
      draggable: false,
      animation: google.maps.Animation.BOUNCE,
      anchorPoint: new google.maps.Point(0, -29)
   });
    var infowindow = new google.maps.InfoWindow();   
    google.maps.event.addListener(marker, 'click', function() 
    {
      var iwContent = '<div id="pop_window">' + '<div><b>EMP</b> : '+emp_name+'</div></div>';
      // put content to the infowindow
      infowindow.setContent(iwContent);
      // show infowindow in the google map and at the current marker location
      infowindow.open(map, marker);
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

</script>

<script language="JavaScript">
//var geocoder = new GClientGeocoder();
//var cnt;
//var globalLat;
//var globalLon;
var emp_name="<?=stripslashes($emp_name);?>";
var customer="<?=stripslashes($customer);?>";
var route="<?=stripslashes($route);?>";
var time="<?=$time?>";
var Lat="<?=$rowtrans['latt']?>";
var Lon="<?=$rowtrans['longi']?>";
var Place='';

</script>
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Employee Visit Details</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
            <?php 
			if($_REQUEST['page']!='activitydetails' && $_REQUEST['page']!='prospectdetails'){
			if($visit_map != 'true'){?>
				<tr> 
					<td align="right" class="ERR" width="95%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='<?=$backurl?>?from_date=<?php echo $_REQUEST['from_date']?>&to_date=<?php echo $_REQUEST['to_date']?>&emp_code=<?php echo $_REQUEST['emp_code']?>&customer_code=<?php echo $_REQUEST['customer_code']?>&mode=<?php echo $_REQUEST['mode']?>&page=<?php echo $_REQUEST['page']?>'"><img src="images/back.png" alt="back" /></a></td>
					<td align="right" width="5%"><a href="<?=$url;?>" title=" Refresh the page"><img border="0" src="images/icon_reload.gif"></a></td>
				</tr>
            <?php } else{ ?>
            <tr>
             	<td colspan="2"  align="right"><input type="button" value="Close" onclick="close_window();"</td>
             </tr>
            <?php } 
			}
			?>
			</table>
            <br /> <br /> <br />
            <table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2">Employee Visit Details</td>
				</tr>
				<tr> 
					<td  colspan="2" align="center"><strong>
                    <?php if($_REQUEST['customer_code']!=''){?>
                    Customer Name:  <font color="#FF0000"><?php echo stripslashes($rowcustomer['customer_name']);?></font>
                    <?php }?>
                    </strong></td>
				</tr>
                <tr> 
					<td  width="25%" align="left"><strong>Visit Date:  <font color="#FF0000"><?php echo $rowtrans['date'];?></font></strong></td>
                    <td  width="75%" align="right"><strong><?php if($_REQUEST['operation']==''){?>Visit Purpose: <?php }?>
                    <font color="#FF0000">
					<?php 
						if($operation_type=='O'){ echo 'Order';}
						if($operation_type=='P'){ echo 'Payment';}
						if($operation_type=='N'){ echo 'No Transaction';}
					?>
                    </font></strong></td>
                    
				</tr>
                <tr> 
					<td  colspan="2" align="center"><strong>Location:  </strong></td>
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
<script>
function close_window(){
	window.close();
}
 </script>   
<?
}//End of main()

?>