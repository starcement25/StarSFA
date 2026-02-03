<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

?>
<script src="http://maps.googleapis.com/maps/api/js"></script>
<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<?php
if($_GET)
disphtml("show_map();");
else
disphtml("main();");

ob_end_flush();

?>

<?php
function main()
{
	$today = date('Y-m-d');
	$sql_notification = "SELECT NM.notification_id, NM.sender_id, NM.type_of_notification, NM.message FROM  notification_master NM, notification_ack_relation NAR  WHERE NM.notification_id=NAR.notification_id AND DATE_FORMAT(SUBSTRING(NM.notification_id,-14,8),'%Y-%m-%d')='".$today."' GROUP BY NM.notification_id";
	$res_notification = mysql_query($sql_notification);
	$total_rows = mysql_num_rows($res_notification);
	//$row_notification = mysql_fetch_array($res_notification);
	
	if($total_rows == 0)
	{
		echo "<center><font color='red'><strong>No records</strong></font></center>";
		die;
	}
	else
	{
		echo "<center>";
		echo "<table border=\"1\" width=\"700\" style=\"border-collapse:collapse;\" class=\"border\">
				<tr class=\"TDHEAD\">
					<td colspan=\"5\" align=\"center\">Push Notification Details</td>
				</tr>
				<tr class=\"TDHEAD_SUB\">
					<td><b>Sender</b></td>
					<td><b>Message Type</b></td>
					<td><b>Contents</b></td>
					<td><b>Receiver</b></td>
					<td></td>
				</tr>";
		
		while($row_notification = mysql_fetch_array($res_notification))
		{
			$sender_id = $row_notification['sender_id'];
			if($sender_id != 'ADMIN')
			{
				$sql_sender_name = "SELECT emp_name FROM employee_master WHERE emp_code = '$sender_id'";
				$res_sender_name = mysql_query($sql_sender_name);
				$row_sender_name = mysql_fetch_array($res_sender_name);
				$sender_name = $row_sender_name['emp_name'];
			}
			else
				$sender_name = 'ADMIN';
			
			$notification_id = $row_notification['notification_id'];
			
			$sql_acknowledgement = "SELECT EM.emp_code, EM.emp_name, NAR.ack_id FROM employee_master EM, notification_ack_relation NAR WHERE NAR.receiver_id = EM.emp_code AND NAR.notification_id='".$notification_id."'";
			$res_acknowledgement = mysql_query($sql_acknowledgement);
			while($row_acknowledgement = mysql_fetch_array($res_acknowledgement))
			{
				$emp_name = $row_acknowledgement['emp_name'];
				$ack_id = $row_acknowledgement['ack_id'];
				
				$sql_location = "SELECT date, LO.latt, LO.longi FROM location LO WHERE LO.trans_id='".$ack_id."'";
				$res_location = mysql_query($sql_location);
				$row_location = mysql_fetch_array($res_location);
				$get_latt = $row_location['latt'];
				$get_longi = $row_location['longi'];
				//$date = $row_location['date'];
			
				if($get_latt == '' && $get_longi == '')
					$locate = "--";
				else 
					$locate = "<a href=\"notification_report_details.php?get_latt=$get_latt&get_longi=$get_longi&emp_name=$emp_name\" style=\"color:red;\">Locate</a>";
				
				if($get_latt == '' && $get_longi == '')
					$locate = "--";
				else 
					$locate = "<a href=\"notification_report_details.php?get_latt=$get_latt&get_longi=$get_longi&emp_name=$emp_name\" style=\"color:red;\">Locate</a>";
					
				echo "<tr>
					<td>$sender_name</td>
					<td>$row_notification[type_of_notification]</td>
					<td>$row_notification[message]</td>
					<td>$row_acknowledgement[emp_name]</td>
					<td>$locate</td>
				  </tr>";
			}
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
	echo "<br><br>";
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



