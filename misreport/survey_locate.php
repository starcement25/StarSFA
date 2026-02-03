<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	//error_reporting(0);
	
if($_GET)
disphtml("main();");

function main()
{
?>
<script src="http://maps.googleapis.com/maps/api/js"></script>


<?php
$get_latt = $_GET['get_latt'];
$get_long = $_GET['get_longi'];
$emp_name = $_GET['emp_name'];
$locate_date = $_GET['locate_date'];
$locate_time = $_GET['locate_time'];

$location = "$get_latt,$get_long";


@$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='
                                         .$get_latt.','.$get_long.'&sensor=false');
@$output= json_decode($geocode);

@$address = $output->results[0]->formatted_address;

$details = $cust_name." ".$address." ".$locate_date." @".$locate_time;

?>

<center>
<div id="map" style="width:600px;height:400px;border-style:outset; border-width:8px;" ></div>

<br /><br />
<a href="survey_output_edit.php" style="color:blue;"><strong>Back</strong></a>
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

<?php } ?>