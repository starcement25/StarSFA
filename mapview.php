<?php
$servername = "localhost";
$username = "acedns_dnsprod";
$password = "dnsprod1234#";
$db_name = "acedns_hotelaccounts";

$conn = mysqli_connect($servername, $username, $password);
mysqli_select_db($db_name,$conn) or die("could not connect the database");
$submsg = "";
$default_latitute_longitude = "0.00,0.00";
$loc_arr = array();
$sqlmaplink = "select * from CETA_lat_long";
$resmaplink = mysqli_query($link,$sqlmaplink);
$total_maplink = mysqli_num_rows($resmaplink);
if($total_maplink>0){
while($rowmaplink=mysqli_fetch_assoc($resmaplink)){
			$address=$rowmaplink['address'];
			//$address_array=explode(',',$address);
			$the_latitute = $rowmaplink['latt'];
			$the_longitude = $rowmaplink['longi'];
			$loc_arr[] = array($the_latitute,$the_longitude,$address);
	}
	//print_r($loc_arr);
	if(count($loc_arr)>0){
	$default_latitute_longitude = $loc_arr[0][0].",".$loc_arr[0][1];
}
	
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAC5XJHC0k1ALyl5Bnelv3Nvuxpzr9nLdc&libraries=places"></script>
<style>
#map {
  height: 100%;
}
.body{
	height:500px;
}
</style>
    <section class="content">
        <div class="container-fluid">
<!-- Basic Examples -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="card">
<div class="header">
<h2>Location in Map</h2>
</div>
<div class="body">
<div id="map"></div>
</div>
</div>
</div>
</div>

        </div>
    </section>
<script type="text/javascript">
var locationsb = <?php echo json_encode($loc_arr);?>;
//var locationsb = [["infowindow",22,96,"test"]];
var map = new google.maps.Map(document.getElementById('map'), {
 zoom: 13,
 center: new google.maps.LatLng(<?php echo $default_latitute_longitude;?>),
 mapTypeId: google.maps.MapTypeId.ROADMAP
 });


 // var image = '/marker/map.png';
 // var image2 = '/marker/Map1.png';

var markerval, n;
for (n = 0; n < locationsb.length; n++) {  
var markerval='markerval_'+n;
markerval = new google.maps.Marker({
position: new google.maps.LatLng(locationsb[n][0], locationsb[n][1]),
offset: '0',
// icon: image2,
title: locationsb[n][2],
map: map       
});
markerval.setMap(map); 
 var infowindow = new google.maps.InfoWindow({
	disableAutoPan: true 
 });
infowindow.setContent(locationsb[n][2]);
infowindow.open(map, markerval); 
/*google.maps.event.addListener(marker1, 'click', (function(marker1, n)
{
return function() {
//infowindow.setContent(locationsb[n][2]+'<br>'+'Dealer');
infowindow.setContent(locationsb[n][2]);
infowindow.open(map, marker1);
}
})(marker1, n));*/

}

</script> 
<?php
}
else
{
	echo "Something Went Wrong";
}
?>
