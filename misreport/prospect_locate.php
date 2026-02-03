
<!--<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBqSx3h3PfPlF28bgzeHO6a_m9ZwECzUs4&callback=initMap"></script>-->

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqSx3h3PfPlF28bgzeHO6a_m9ZwECzUs4&sensor=false&libraries=places&callback=initialize"></script>



<?php

require("adminUtils.php");
$get_latt = $_GET['get_latt'];
$get_long = $_GET['get_longi'];
$cust_name = $_GET['cust_name'];

$location = "$get_latt,$get_long";


$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='
                                         .$get_latt.','.$get_long.'&key=AIzaSyBqSx3h3PfPlF28bgzeHO6a_m9ZwECzUs4');
$output= json_decode($geocode);

$address = $output->results[0]->formatted_address;

$details = $cust_name." ".$address;

?>

<center>
<div id="map" style="width:600px;height:400px;border-style:outset; border-width:8px;" ></div>
</center>
<script>
function initialize() {
  var myLatlng = new google.maps.LatLng(<?php echo $location; ?>);
  var mapOptions = {
    zoom: 18,
    center: myLatlng
  };

  var map = new google.maps.Map(document.getElementById('map'), mapOptions);

  var contentString = '<?php echo $details; ?>';

  var infowindow = new google.maps.InfoWindow({
      content: contentString,
	  maxWidth:180
  });

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: 'Notification Details'
  });
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>