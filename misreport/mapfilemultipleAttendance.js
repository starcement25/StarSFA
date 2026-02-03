function forload(routes) {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
	var bounds = new google.maps.LatLngBounds();
	map.fitBounds(bounds);
	var listener = google.maps.event.addListener(map, "idle", function() { 
	   map.setZoom(9); 
	  google.maps.event.removeListener(listener); 
	});

    var infowindow = new google.maps.InfoWindow({maxWidth:180});
    var marker, i;
	var markerBounds = new google.maps.LatLngBounds();
    for (i = 0; i < routes.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(routes[i][1],routes[i][2]),
        map: map
      });

	var randomPoint = new google.maps.LatLng(routes[i][1],routes[i][2]);
	markerBounds.extend(randomPoint);
	if(routes[i][0]!=''){
		var info = "Mr. "+ routes[i][0]+" Gave Attendence on "+routes[i][4]+" @ "+routes[i][3]+ " hrs.";
	}
	else
	{
		var info = ""+ routes[i][3] +"";
	}
      /*google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(info_$i);
		  infowindow.open(map, marker);
        }
      })(marker, i));*/
	  google.maps.event.addListener(marker,'click', (function(marker,info,infowindow){ 
   		 return function() {
        infowindow.setContent(info);
        infowindow.open(map,marker);
    	};
	})(marker,info,infowindow));  
    }
	map.fitBounds(markerBounds);
}