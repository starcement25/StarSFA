<?php
	ob_start();
	session_start();
	main();
	ob_end_flush();

function main()
{
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_MAGIK");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database");
	
	$live_tracking_latt_long = "live_tracking_latt_long";

		$sqllist="SELECT * FROM $live_tracking_latt_long ORDER BY update_time ASC";
		$rsllist=mysqli_query($link,$sqllist);
		$totallist = mysqli_num_rows($rsllist);
		if($totallist>0){
		while($rowlist=mysqli_fetch_assoc($rsllist)){
				$the_latitute = $rowlist["latt"];
				$the_longitude = $rowlist["long"];
				$update_time = $rowlist["update_time"];
		$loc_arr[] = array($the_latitute,$the_longitude,$update_time);
		  }
		}
		
		if(count($loc_arr)>0){
		$default_latlong= $loc_arr[0][0].",".$loc_arr[0][1];
		}
		//print_r($loc_arr);
?>
<html>
<head>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc"></script>
<style>
#map {
  height: 100%;
}
.body{
	height:500px;
}
</style>
</head>
<body onload="javascript:timedRefresh(100000);">
<table width="60%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong>Location Tracking</strong></td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#FFFFFF">
            <table width="90%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="right" class="ERR" width="99%"></td>
					<td align="right" width="1%"></td>
				</tr>
			</table>
			<table width="90%" align="center" border="0" class="border" cellpadding="5" cellspacing="1">
				<tr class="TDHEAD"> 
					<td colspan="2"></td>
				</tr>
              
                <tr>
                	<td colspan="2">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
                          <tr> 
                            <td  align="center">
                            <div id="map" style="width: 1000px; height: 800px"></div>
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
		</td>
	</tr>
</table>
<script type="text/javascript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
var locationsb = <?php echo json_encode($loc_arr);?>;
const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
let labelIndex = 0;
//var locationsb = [["infowindow",22,96,"test"]];
var map = new google.maps.Map(document.getElementById('map'), {
 zoom: 16,
 center: new google.maps.LatLng(<?php echo $default_latlong;?>),
 mapTypeId: google.maps.MapTypeId.ROADMAP
 });

 var infowindow = new google.maps.InfoWindow();
 // var image = '/marker/map.png';
 // var image2 = '/marker/Map1.png';

var marker1, n;
for (n = 0; n < locationsb.length; n++) {  
marker1 = new google.maps.Marker({
position: new google.maps.LatLng(locationsb[n][0], locationsb[n][1]),
offset: '0',
// icon: image2,
title: locationsb[n][2],
//label: labels[labelIndex++ % labels.length],
map: map       
});
google.maps.event.addListener(marker1, 'click', (function(marker1, n)
{
return function() {
infowindow.setContent(locationsb[n][2]);
infowindow.open(map, marker1);
}
})(marker1, n));
}

</script> 
</body>
</html>
<?php }?>