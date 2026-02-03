function forload(lat,lon,place,customer,route,emp_name,time,trans_id_parts) {
	var locations = [[,lat,lon,]];
	if(customer=='' && route=='' && trans_id_parts=='A')
	{
		var info = [["Mr. "+ emp_name+" Gave Attendence @"+time+ "hrs."]];
	}
	else if(customer=='' && route=='' && trans_id_parts=='C')
	{
		var info = [["Mr. "+ emp_name+" Submit Day's Report @"+time+ "hrs."]];
	}
	else if(customer=='')
	{
		var info = [["Mr. "+ emp_name+" visited @"+time+ "hrs."]];
	}
	else
	{
		var info = [["Mr. "+ emp_name+"visited Ms. "+customer+" at "+route+ " @"+time+ "hrs."]];
	}
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 12,
      center: new google.maps.LatLng(lat+","+lon),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
	var bounds = new google.maps.LatLngBounds();
	map.fitBounds(bounds);
	var listener = google.maps.event.addListener(map, "idle", function() { 
	   map.setZoom(18); 
	  google.maps.event.removeListener(listener); 
	});

    var infowindow = new google.maps.InfoWindow({maxWidth:180});
    var marker, i;
	var markerBounds = new google.maps.LatLngBounds();
    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lon),
        map: map
      });

	var randomPoint = new google.maps.LatLng(lat, lon);
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