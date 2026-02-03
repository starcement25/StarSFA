<?php
session_start();
//include "connection.php";
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_PRABHUJI");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhJB9maJFpMdTZ_JXAbB7HBX4H8oDFURo&callback=initMap"
  type="text/javascript"></script>
<!--script src="http://maps.googleapis.com/maps/api/js"></script-->
<?php
$get_latt = $_REQUEST['latt'];
$get_long = $_REQUEST['longi'];
$survey_id = $_REQUEST['survey_id'];
$emp_code = $_REQUEST['emp_code'];
$survey_id='OE133020181116161333';

if($survey_id!=''){
	$sql_emp_name = "SELECT EM.emp_name,LO.latt,LO.longi FROM employee_master EM, location LO WHERE LO.trans_id = '".$survey_id."' 
					AND LO.emp_code = EM.emp_code";
}
else{
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
}

$res_emp_name = mysqli_query($link,$sql_emp_name);
$row_emp_name = mysqli_fetch_assoc($res_emp_name);
$emp_name = $row_emp_name['emp_name'];
echo $get_latt=$row_emp_name['latt'];
echo $get_long=$row_emp_name['longi'];
@$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='
                                         .$get_latt.','.$get_long.'&sensor=false');
@$output= json_decode($geocode);
@$address = $output->results[0]->formatted_address;

print_r($output);

$date = date('d-m-Y',strtotime(substr($survey_id,-14,8)));
$time = date('H:i:s',strtotime(substr($survey_id,-14)));
		
$v = $emp_name.": @".$time.", ".$address;

$info = "[\"".$v."\"]";
$t = "[,$get_latt,$get_long,]";
echo $info;
?>
<center>
<div id="map" style="width:600px;height:400px;border-style:outset; border-width:8px;" ></div>
<br />
<input type="button" value="Close" onclick="close_window();" />
</center>
<script>
var locations = [
      <?php echo $t; ?>
    ];
var info = [<?php echo $info; ?>];

function initialize() {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 12,
      center: new google.maps.LatLng(<?php echo $get_latt.",".$get_long; ?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({maxWidth:180});

    var marker, i;
	
	var markerBounds = new google.maps.LatLngBounds();

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

	var randomPoint = new google.maps.LatLng(locations[i][1], locations[i][2]);
	markerBounds.extend(randomPoint);

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(info[i][0]);
		  infowindow.open(map, marker);
        }
      })(marker, i));
    }
	
	map.fitBounds(markerBounds);
	
}
google.maps.event.addDomListener(window, 'load', initialize);


function close_window(){
	window.close();
}
</script>