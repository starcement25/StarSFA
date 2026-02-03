<link href="http://www.acedns.in/acednsproduct/css/adminStyle.css" rel="stylesheet" type="text/css" />
<script src="http://maps.googleapis.com/maps/api/js"></script>
<?php
ob_start();
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","acedns_CDNS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
?>

<?php
if($_GET)
show_map();
else
main();
?>

<?php
function main()
{
$sql_notification = "SELECT NM.notification_id, EM.emp_name as sender, NM.type_of_notification, NM.message FROM employee_master EM, notification_master NM, notification_ack_relation NAR  WHERE NM.notification_id=NAR.notification_id AND NM.sender_id=EM.emp_code";
$res_notification = mysqli_query($link,$sql_notification);
//$row_notification = mysqli_fetch_assoc($res_notification);

if($res_notification == '')
{
	echo "No records";
	die;
}
else
{
	echo "<center>";
	echo "<table border=\"1\" width=\"700\" style=\"border-collapse:collapse;\" class=\"border\">
			<tr class=\"TDHEAD\">
				<td colspan=\"5\" align=\"center\">Pushnotification Details</td>
			</tr>
			<tr class=\"TDHEAD_SUB\">
				<td><b>Sender</b></td>
				<td><b>Message Type</b></td>
				<td><b>Contents</b></td>
				<td><b>Receiver</b></td>
				<td></td>
			</tr>";
	
	while($row_notification = mysqli_fetch_assoc($res_notification))
	{
		$notification_id = $row_notification['notification_id'];
		
		$sql_acknowledgement = "SELECT EM.emp_name, NAR.ack_id FROM employee_master EM, notification_ack_relation NAR WHERE NAR.receiver_id = EM.emp_code AND NAR.notification_id='".$notification_id."'";
		$res_acknowledgement = mysqli_query($link,$sql_acknowledgement);
		$row_acknowledgement = mysqli_fetch_assoc($res_acknowledgement);
		$ack_id = $row_acknowledgement['ack_id'];
		$emp_name = $row_acknowledgement['emp_name'];
		
		$sql_location = "SELECT date, LO.latt, LO.longi FROM location LO WHERE LO.trans_id='".$ack_id."'";
		$res_location = mysqli_query($link,$sql_location);
		$row_location = mysqli_fetch_assoc($res_location);
		$get_latt = $row_location['latt'];
		$get_longi = $row_location['longi'];
		$date = $row_location['date'];
		
		if($get_latt == '' || $get_longi == '')
			$locate = "--";
		else 
			$locate = "<a href=\"notification_report_details.php?get_latt=$get_latt&get_longi=$get_longi&emp_name=$emp_name\" style=\"color:red;\">Locate</a>";
		
		echo "<tr>
				<td>$row_notification[sender]</td>
				<td>$row_notification[type_of_notification]</td>
				<td>$row_notification[message]</td>
				<td>$row_acknowledgement[emp_name]</td>
				<td>$locate</td>
			  </tr>";
	}
	echo "</table>";
	echo "</center>";
}
}

function show_map()
{
	$get_latt = $_GET['get_latt'];
	$get_long = $_GET['get_longi'];
	$emp_name = $_GET['emp_name'];
	
	$location = "$get_latt,$get_long";
	
	
	@$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='
											 .$get_latt.','.$get_long.'&sensor=false');
	@$output= json_decode($geocode);
	
	@$address = $output->results[0]->formatted_address;
	
	$details = $emp_name." ".$address;
	
	echo "<center>";
	echo "<br><br><br><br><br>";
	echo "<div id=\"map\" style=\"width:600px;height:400px;border-style:outset; border-width:8px;\" ></div>";
	?>
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
    <?php
	echo "</center>";
}
?>



