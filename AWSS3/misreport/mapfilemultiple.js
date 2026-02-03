// JavaScript Document
var geocoder;

var map;

var lastSearch;

var panel_open = false;

var slideUpdate = true;

var sideresults;

var browserName = navigator.appName;

var currType = 'street';

var panelTabs = { 0: 'panel_search', 1: 'panel_results', 2: 'panel_moreinfo' };



var last_markers = {};

var curr_markers = {};



var iconCluster = new GIcon();

iconCluster.image = "/map/images/cluster_purple2.png";

iconCluster.shadow = "/map/images/cluster_shadow2.png";

iconCluster.iconSize = new GSize(26, 25);

iconCluster.shadowSize = new GSize(35, 22);

iconCluster.iconAnchor = new GPoint(13, 25);

iconCluster.infoWindowAnchor = new GPoint(13, 1);

iconCluster.infoShadowAnchor = new GPoint(26, 13);



var garageIcon = new GIcon();

garageIcon.image = "/map/images/garage_pin.png";

garageIcon.shadow = "/map/images/single_shadow.png";

garageIcon.iconSize = new GSize(12, 20);

garageIcon.shadowSize = new GSize(22, 20);

garageIcon.iconAnchor = new GPoint(6, 20);

garageIcon.infoWindowAnchor = new GPoint(6, 1);

garageIcon.infoShadowAnchor = new GPoint(13, 13);



var streetIcon = new GIcon(garageIcon);

streetIcon.image = "/map/images/street_pin.png";



var iconSingle = garageIcon;



//create the ToolTip overlay object

function ToolTip(marker,html,width) {

    this.html_ = html;

    this.width_ = (width ? width + 'px' : 'auto');

    this.marker_ = marker;

}



ToolTip.prototype = new GOverlay();



ToolTip.prototype.initialize = function(map) {

    var div = document.createElement("div");

    div.style.display = 'none';

    map.getPane(G_MAP_FLOAT_PANE).appendChild(div);
    this.map_ = map;
    this.container_ = div;
}



ToolTip.prototype.remove = function() {
    this.container_.parentNode.removeChild(this.container_);
}



ToolTip.prototype.copy = function() {
    return new ToolTip(this.html_);

}



ToolTip.prototype.redraw = function(force) {

    if (!force) return;

    

    var pixelLocation = this.map_.fromLatLngToDivPixel(this.marker_.getPoint());

    this.container_.innerHTML = this.html_;

    

    this.container_.className = 'toolTip'

    this.container_.style.position = 'absolute';

    this.container_.style.left = parseInt(pixelLocation.x + 7) + "px";

    this.container_.style.top = parseInt(pixelLocation.y - 10) + "px";

    this.container_.style.width = this.width_;

    

    

    //one line to desired width

    this.container_.style.whiteSpace = 'nowrap';

    if(this.width_ != 'auto') this.container_.style.overflow = 'hidden';

    this.container_.style.display = 'block';

}



GMarker.prototype.ToolTipInstance = null;



GMarker.prototype.openToolTip = function(content) {

    //don't show the tool tip if there is acustom info window

    if(this.ToolTipInstance == null) {

        this.ToolTipInstance = new ToolTip(this,content)

        map.addOverlay(this.ToolTipInstance);

    }

}



GMarker.prototype.closeToolTip = function() {

    if(this.ToolTipInstance != null) {

        map.removeOverlay(this.ToolTipInstance);

        this.ToolTipInstance = null;

    }

}





function changeTab(newTab) {

    

    for(i in panelTabs) {

        

        if(panelTabs[i] != newTab) {

            $(panelTabs[i]).style.display = 'none';

            $(panelTabs[i] + "_tab").style.background = '#eee';

        }

        

    }

    

    $(newTab).style.display = '';

    $(newTab + "_tab").style.background = '#fff';

    

}



function panelSearchSubmit(formobj) {

	

	var completeAddress;

	completeAddress = '';

	

	if (formobj.streetaddress.value != '' && formobj.city.value != '' && formobj.state.value != '') {

		

		completeAddress += formobj.streetaddress.value + ','  + formobj.city.value + ',' + formobj.state.value;

		geocoder.getLocations(completeAddress, addLocation);

		

	} else if(formobj.zip.value != '') {

		

		 geocoder.getLocations(formobj.zip.value, addLocation);

		 

	} else if (formobj.city.value != '' && formobj.state.value != '') {

				

		completeAddress += formobj.city.value + ',' + formobj.state.value;

		geocoder.getLocations(completeAddress, addLocation);

		

	}

	

}



function toggleCat(type) {

    

    if(type == 'garage') {

        $('garage_tab_switch').className = 'typeActive';

        $('street_tab_switch').className = 'typeDeactive';

        $('both_tab_switch').className = 'typeDeactive';

        $('inputForm').parktype[1].checked = 'true';

        $('text_type_name').innerHTML = 'Garage';

    } else if (type == 'street') {

        $('garage_tab_switch').className = 'typeDeactive';

        $('both_tab_switch').className = 'typeDeactive';

        $('street_tab_switch').className = 'typeActive';

        $('inputForm').parktype[0].checked = 'true';

        $('text_type_name').innerHTML = 'Street';

    } else {

        $('garage_tab_switch').className = 'typeDeactive';

        $('street_tab_switch').className = 'typeDeactive';

        $('both_tab_switch').className = 'typeActive';

        $('inputForm').parktype[2].checked = 'true';

        $('text_type_name').innerHTML = 'All';

    }

    

    currType = type;

    map.closeInfoWindow();

    

    updateMarkers();

    changeTab('panel_results');

    $('panel_moreinfo_info').innerHTML = '';

    

}





function changeMarkerImage(id,imagepath) {

    

    var marker = curr_markers[id];

    var msFilter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + imagepath + '")';

    

    for (var i in marker)

    if (eval("typeof marker." + i) == "object")

    try {

        if (eval("typeof marker." + i + "[0].src") != "undefined")

        {

            

            if (browserName == "Microsoft Internet Explorer") {

                eval("marker." + i + "[0].style.filter = msFilter");

                } else {

                eval("marker." + i + "[0].src = imagepath");

            }

            

        }

        

    }

    

    catch (e) {}

    

    

}



function highlightMarker(id,obj) {
    var hoverImage = "http://labs.google.com/ridefinder/images/mm_20_yellow.png";
    //changeMarkerImage(id,hoverImage);
    curr_markers[id].openToolTip(obj.innerHTML);
}



function dehighlightMarker(id) {
    //changeMarkerImage(id,iconSingle.image);
    curr_markers[id].closeToolTip();
}



function highlightInput() {
    changeTab('panel_search');
}



function getRadioSelectedValue(object) {
    var i = object.length;
    for (var j=0; j<i; j++) {
        if (object[j].checked) {
            return object[j].value;
        }
    }
    return "";
}



function addRecommendations() {
    changeTab('panel_results');
    $('panel_results_info').innerHTML = '<div style="text-align: left; font-size: 0.70em; font-weight: bold; color: #0033FF;">Can\'t find that location. Did you mean one of these:</div>';
    for (var j=0; j<lastSearch.length; j++) {
        $('panel_results_info').innerHTML += '<p style="text-align:left; margin-left: 20px;"><a style="font-size: 0.7em;" href="javascript: recClick(' + j + ');">' + lastSearch[j].address + '</a></p>';
    }
}



function recClick(id) {
    map.setCenter(new GLatLng(lastSearch[id].Point.coordinates[1],lastSearch[id].Point.coordinates[0]),10);
}



function getPoints() {
    var address = $('inputForm').address.value;
    geocoder.getLocations(address, addLocation);
}



function updateMarkers() {

	var bounds = map.getBounds();
    var southWest = bounds.getSouthWest();
    var northEast = bounds.getNorthEast();
    var getVars = 'ne=' + northEast.toUrlValue() + '&sw=' + southWest.toUrlValue() + '&type=' + currType;
    //$('panel_results_info').innerHTML = '';
    map.closeInfoWindow();
    var request = GXmlHttp.create();
    //request.open('GET', 'functions/getPoints.php?'+getVars, true);
	request.open('GET', 'functions/getPoints.php?'+getVars, true);
	request.onreadystatechange = function() {
   	if (request.readyState == 4) {
   		
            var jscript = request.responseText;
            var points;    
            eval(jscript);
			//$('panel_results_info').innerHTML += '<p style="text-align:left; margin: 7px 0px;"><a style="font-size: 0.7em;" href="javascript: ajaxBalloon(\'' + i +'\');" onmouseover="highlightMarker(\'' + i +'\',this)" onmouseout="dehighlightMarker(\'' + i +'\')" onclick="dehighlightMarker(\'' + i +'\')">' + points[i].address + '</a></p>';
            for(i in last_markers) {
               alert(points[i]); 
				if(!points[i]) {
                    map.removeOverlay(curr_markers[i]);
                    delete curr_markers[i];
                }
            }
            
            var i = 'empty';
            /*for (i in points) {
                $('panel_results_info').innerHTML += '<p style="text-align:left; margin: 7px 0px;"><a style="font-size: 0.7em;" href="javascript: ajaxBalloon(\'' + i +'\');" onmouseover="highlightMarker(\'' + i +'\',this)" onmouseout="dehighlightMarker(\'' + i +'\')" onclick="dehighlightMarker(\'' + i +'\')">' + points[i].address + '</a></p>';
                if(!last_markers[i]) {
                    var point = new GLatLng(points[i].lat,points[i].lng);
                    curr_markers[i] = createMarker(point,points[i].type,i);
                    map.addOverlay(curr_markers[i]);
                }
            } */

            last_markers = points;
            if(i == 'empty') {
                $('panel_results_info').innerHTML = '<div style="text-align: left; font-size: 0.70em; font-weight: bold; color: #0033FF;">There are no parking locations in this area, sorry.</div>';
            }
        }
    }
    request.send(null);
}
function markerZoom(id) {
    //alert('dfdfdfdfdf');
    map.closeInfoWindow();
    slideUpdate = true;
    map.setCenter(curr_markers[id].getPoint(), map.getZoom() + 3);
}
/*function createMarker(point, id) {
    //create the marker with the appropriate icon
 	var marker = new GMarker(point,streetIcon);
    GEvent.addListener(marker, "click", function() {
        ajaxBalloon(id);
    });

    return marker;
}*/
function showMarker(point, place,emp_name,customer,route,time) 
{  
	var request = GXmlHttp.create();
	//request.open('GET', 'infoballon.php?ent_id=1', true);
	map.addOverlay(createMarker(emp_name,customer,route,time,point,place));

	/*request.onreadystatechange = function() {			
			if (request.readyState == 4) 
			{
				var jscript=request.responseText;
				var valArr = jscript.split("|");
				map.addOverlay(createMarker(valArr[1],valArr[0],point,place));
			}
		}
	request.send(null);*/
}

function createMarker(emp_name,customer,route,time,point,place)
 {
	//alert(point);
	//alert(name);
	//var final_photo='entrepreneur/smallthumbnail/smallthumb_'+photo;

	var marker = new GMarker(point);
	var html = '<table width="200" height="50">';
	//html += '<tr><td><img src='+final_photo+'></td></tr>';	
	html += '<tr><td valign="top"><strong>Mr. '+ emp_name+' visited Ms. '+customer+' at '+route+ ' @'+time+ 'hrs.</strong></td></tr>';		
	//html += '<tr><td style="text-align:center;"><strong>'+name+'</strong></td></tr>';
	html += '</table>';  
	GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(html); });	
	 return marker; 
}

 function createMarkerMultiple(point,html) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }

/*function ajaxBalloon(point) {

    

	var request = GXmlHttp.create();

    request.open('GET', 'infoBalloon.php', true);

    request.onreadystatechange = function() {

		//alert(request.responseText);

        if (request.readyState == 4) {

            var jscript = request.responseText;

            eval(jscript);

			var marker = new GMarker(point);

			

			var html = '<table width="300">';

			

				html += '<tr><td>ent name</td></tr>';

				

			html += '</table>';

			alert(html);

			marker.openInfoWindowHtml(html);

        }

    }

    

    request.send(null);

    

}*/



function addLocation(response) {

    if (!response || response.Status.code != 200) {

        

        changeTab('panel_results');

        $('panel_results_info').innerHTML = '<div style="text-align: left; font-size: 0.70em; font-weight: bold; color: #0033FF;">Could not find that address, sorry!</div>';

        

        } else {

        

        if(response.Placemark.length > 1) {

            

            lastSearch = new Array();

            lastSearch = response.Placemark;

            addRecommendations();

            

            } else {

            

            map.setCenter(new GLatLng(response.Placemark[0].Point.coordinates[1],response.Placemark[0].Point.coordinates[0]),10);

            

        }

        

    }

}

function forload(routes) {
    if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        geocoder = new GClientGeocoder();
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
		if(routes.length>5)
		{
			var zoom=11;
		}
		else
		{
			var zoom=12;
		}
		map.setCenter(new GLatLng(22.5, 88.3), zoom);
		// map.setCenter(new GLatLng(-33.89,151.27), 16);
		 //alert(routes);
		 //alert(routes.length);routes[i][3]
		 
		 for (var i = 0; i < routes.length; i++) {
			 //alert(routes[i][1]);
			// alert(routes[i][2]);
			var point = new GLatLng(routes[i][1],routes[i][2]);
			if(routes[i][6]=='')
			{
			var marker = createMarkerMultiple(point,'<table width="200" height="50"><tr><td valign="top"><strong>Mr. '+ routes[i][0]+' visited Ms. '+routes[i][3]+' at '+routes[i][4]+ ' @'+routes[i][5]+ 'hrs.</strong></td></tr></table>');
			}
			else
			{
			var marker = createMarkerMultiple(point,'<table width="200" height="50"><tr><td valign="top"><strong>Mr. '+ routes[i][0]+' visited Ms. '+routes[i][3]+' at '+routes[i][4]+ ' on ' + routes[i][6]+ ' @ '+routes[i][5]+ 'hrs.</strong></td></tr></table>');

			}
			map.addOverlay(marker);
			// var marker=map.addOverlay(new GMarker(point));
		 }
		//alert(point);
		/*alert(emp_name);
		alert(customer);
		alert(route);
		alert(time);*/
		//map.addOverlay(createMarker(point, place));
		//showMarker(point, place,emp_name,customer,route,time);
        //$('inputForm').parktype[0].checked = 'true';
        /*GEvent.addListener(map,'moveend',function() {
            if (slideUpdate) {
                updateMarkers();
                changeTab('panel_results');
                } else {
                slideUpdate = true;
            }
        });*/
        //updateMarkers();
    }
}