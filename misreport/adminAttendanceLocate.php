<?php
ob_start();

	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	$GLOBALS['show']=30;
	if($_REQUEST['pageNo']=="")
	{
		$GLOBALS['start'] = 0;
		$_REQUEST['pageNo'] = 1;
	}
	else
	{
		$GLOBALS['start']=($_REQUEST['pageNo']-1) * $GLOBALS['show'];
	}
	$mode = $_REQUEST['mode'];
	

	if($mode =='add' || $mode =='edit')				 disphtml("show_add_edit($_REQUEST[row_id]);");
	else    											disphtml("main();");
ob_end_flush();

function main()
{
    require("include/dbcon.php");
	$sqltrans="SELECT *,DATE_FORMAT(date,'%b %e ,%y') AS date,DATE_FORMAT(date,'%T') AS time FROM location WHERE trans_id='".$_REQUEST['trans_id']."'";
	$rstrans=mysqli_query($link,$sqltrans) or die(mysqli_error()." Error in select transaction : ".$sqltrans);	
	$rowtrans=mysqli_fetch_assoc($rstrans);
	
	$trans_id=$rowtrans['trans_id'];
	$emp_code=$rowtrans['emp_code'];
	$time=$rowtrans['time'];
	$trans_id_parts=substr($trans_id,0,1);
	
	$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsemp=mysqli_query($link,$sqlemp) or die(mysqli_error()." Error in select employee : ".$sqlemp);
	$rowemp=mysqli_fetch_assoc($rsemp);
	$emp_name=$rowemp['emp_name'];
	
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
	
?>
<script language="JavaScript">
//var geocoder = new GClientGeocoder();
//var cnt;
//var globalLat;
//var globalLon;
var emp_name="<?=stripslashes($emp_name);?>";
var time="<?=$time?>";
var Lat="<?=$rowtrans['latt']?>";
var Lon="<?=$rowtrans['longi']?>";
var Place='';
var customer='';
var route='';
var trans_id_parts="<?=$trans_id_parts?>";
</script>



<!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqSx3h3PfPlF28bgzeHO6a_m9ZwECzUs4&region=IN&language=en&callback=initialize"></script>
-->

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
      var iwContent = '<div id="pop_window">' + '<div><b>Time</b> : '+time+'</div></div>';
      // put content to the infowindow
      infowindow.setContent(iwContent);
      // show infowindow in the google map and at the current marker location
      infowindow.open(map, marker);
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

</script>




<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> 
        <?php if($trans_id_parts=='A'){?>
        	Administrator >> Employee Attendance Details
         <?php }if($trans_id_parts=='C'){?>
         	Administrator >> Employee Checkout Details
         <?php }if($trans_id_parts=='S'){?>
         	Administrator >> Branding Verification Locate
         <?php }?>
        </strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
        	<form name = "frmAttendence" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="search_mode" value="">
			<input type="hidden" name="row_id" value="<?=$_REQUEST['row_id']?>">
			<input type="hidden" name="mode" >
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
            
			<?php 
			if($_REQUEST['page']!='activitydetails'){
			if($trans_id_parts!='S'){?>
				<tr> 
					<td align="right" class="ERR" width="99%"><a href="javascript:void(0);" style="color: #e40000" 
                    onclick="javascript:window.location='<?=$backurl?>?mode=<?php echo $_REQUEST['mode']?>&radio_search=<?php echo $_REQUEST['radio_search']?>&emp_code=<?php echo $_REQUEST['emp_code'];?>&page=<?php echo $_REQUEST['page'];?>&state=<?php echo $_REQUEST['state'];?>&emp_type=<?php echo $_REQUEST['emp_type'];?>&employee_lev_one=<?php echo $_REQUEST['employee_lev_one'];?>&modehierarchy=<?php echo $_REQUEST['modehierarchy'];?>&start_date=<?php echo $_REQUEST['start_date'];?>&end_date=<?php echo $_REQUEST['end_date'];?>'"> <img src="images/back.png" alt="back" /> </a></td>
					<td align="right" width="1%"></td>
				</tr>
                <?php 
			}
				}?>
			</table>
			<table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2">
                    <?php if($trans_id_parts=='A'){?>
                    Attendance Details of <?=$emp_name?> 
                    <?php }if($trans_id_parts=='C'){?>
                     Checkout Details of <?=$emp_name?>
                     <?php }if($trans_id_parts=='S'){?>
                        Branding Verification of <?=$emp_name?>
                     <?php }?> 
                    </td>
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
<?php
}//End of main()

?>