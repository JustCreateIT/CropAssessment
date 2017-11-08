var map;
var paddock;

function initMap() {
	
	// http://stackoverflow.com/questions/2177055/how-do-i-get-google-maps-to-show-a-whole-polygon
	google.maps.Polygon.prototype.getBounds = function() {
		var bounds = new google.maps.LatLngBounds();
		var paths = this.getPaths();
		var path;        
		for (var i = 0; i < paths.getLength(); i++) {
			path = paths.getAt(i);
			for (var ii = 0; ii < path.getLength(); ii++) {
				bounds.extend(path.getAt(ii));
			}
		}
		return bounds;
	}

	// Map Control options
	var mapOptions = {
		zoom: 17,
		//mapTypeId: google.maps.MapTypeId.SATELLITE,
		mapTypeId: google.maps.MapTypeId.HYBRID,
		streetViewControl: false,		
		// Default: center on Wellington, NZ
		center: new google.maps.LatLng(-41.2865,174.7762),
		mapTypeControl: true,
		fullscreenControl: true
	};
	
	// Create Map 
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		
	var json = JSON.parse($('#paddock_google_latlong_paths').val());
	
	paddock = parseJSON(json, map);
	var pCenter = polygonCenter(paddock);
	
	//var managementURL = 'http://smartfarm.co.nz/maps/microfarm.kmz';
		
	var baseURL = window.location.protocol + "//" + window.location.host + "/"
	var kmzPath = 'maps/';
	var kmzFile = $('#management_zone_map').val();
	var managementURL = baseURL+kmzPath+kmzFile;
	
	//alert(managementURL);
	paddock.setMap(map);
	map.setCenter(pCenter);
	
	//managementURL = 'http://smartfarm.co.nz/maps/microfarm.kmz';
	
	urlExists(managementURL, function(exists){
		//do more stuff based on the boolean value of exists
		if (exists === true) {
			paddock.setOptions({ strokeOpacity: 0.0, fillOpacity: 0.0 });
			map.fitBounds(paddock.getBounds());
			//paddock.setMap(null);
			var kmlLayer = new google.maps.KmlLayer({
				url: managementURL,
				suppressInfoWindows: true,				
				map: map								
			});		
		}
	});
	//paddock.setMap(map);	
	//map.setCenter(polygonCenter(paddock));
			
	
	geocoder = new google.maps.Geocoder();
	
	geocoder.geocode( { 'location': pCenter }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {

			map.fitBounds(paddock.getBounds());			
			//alert(map.getZoom());
			map.setZoom(map.getZoom());
			
			var infowindow = new google.maps.InfoWindow({
			   content: setInfoWindowContent()
			});
			
		   // variable to define the option of the marker
		   var marker = new google.maps.Marker({
			  position: pCenter, // variable with the coordinates Lat and Lng
			  map: map,
			  title: $("#farm_name").val()
		   });			
			
			// display infowindow when marker clicked
			google.maps.event.addListener(marker, 'click', function() {
			   infowindow.open(map,marker);
			});
			
			google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
				infowindow.open(map, marker);
			});
			
			// event to close the infoWindow with a click on the map
			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
			});
		
			// open infowindow by default
			infowindow.open(map, marker);
			
			// center map on marker when zoom changes
			google.maps.event.addListener(map, 'zoom_changed', function() {
				map.setCenter(marker.getPosition());
			});
			
			/*
			// center map on marker when viewport bounds change
			google.maps.event.addListener(map, 'bounds_changed', function() {
				map.setCenter(marker.getPosition());
			});
			*/
			/*
			map.addListener('resize', function() {
				map.setCenter(marker.getPosition());
			});
			*/
			/*
			map.addListener('center_changed', function() {
				// 200 milliseconds after the center of the map has changed, pan back to the
				// marker.
				window.setTimeout(function() {
					map.panTo(marker.getPosition());
				}, 200);
			});
			*/
			/*
			// Display information about the paddock	
			var areaInfoWindow = new google.maps.InfoWindow();
				areaInfoWindow.setContent(setInfoWindowContent());
				areaInfoWindow.setPosition(pCenter);
				areaInfoWindow.open(map);
			*/
		} else {
			//alert("Geocode was not successful for the following reason: " + status);
			map.setZoom(18);
		}
		
		// show kml management zone data if available
		//addManagementZones(map);
	});
	// To-do: automatically set map zoom to fit polygon bounds and viewport
	//map.setZoom(18);

}

function urlExists(url, callback){
  $.ajax({
    type: 'HEAD',
    url: url,
    success: function(){
      callback(true);
    },
    error: function() {
      callback(false);
    }
  });
}

function setInfoWindowContent(){
	
	   // variable to define the content of Info Window
   var content = '<div id="iw_container">' +
                  '<div class="iw_title">'+$("#farm_name").val()+'</div>' +
                  '<div class="iw_content">Paddock: '+$("#paddock_name").val()+'</div>' +
				  '<div class="iw_content">Plant Date: '+$("#paddock_plant_date").val()+'</div>' +
				  '<div class="iw_content">Area: '+calculateAreaFromPolygon()+' hectares</div>' +
				  '<div class="iw_content">Number of Zones: '+$("#paddock_zones").val()+'</div>' +
				  '<div class="iw_content">Samples per Zone: '+$("#paddock_zone_sample_count").val()+'</div>' +				  
                  '</div>';
	
	return content;
}

function parsePath(o){
	var shapes     = [],
          goo=google.maps,tmp;     
        
        switch(o.type){
           case 'CIRCLE':
              tmp=new goo.Circle({radius:Number(o.radius),center:this.pp_.apply(this,o.geometry)});
            break;
           case 'MARKER': 
              tmp=new goo.Marker({position:this.pp_.apply(this,o.geometry)});
            break;  
           case 'RECTANGLE': 
              tmp=new goo.Rectangle({bounds:this.bb_.apply(this,o.geometry)});
             break;   
           case 'POLYLINE': 
              tmp=new goo.Polyline({path:this.ll_(o.geometry)});
             break;   
           case 'POLYGON': 
              tmp=new goo.Polygon({paths:this.mm_(o.geometry)});              
             break;   
       }
      shapes.push(tmp);
      return shapes;
  }

function ll_(path){
	if(typeof path==='string'){
		return google.maps.geometry.encoding.decodePath(path);
	} else{
		var r=[];
		for(var i=0;i<path.length;++i){
			r.push(this.pp_.apply(this,path[i]));
		}
	return r;
	}
}

function mm_(paths){
	var r=[];
	for(var i=0;i<paths.length;++i){
		r.push(this.ll_.call(this,paths[i]));        
	}
	return r;
}

function p_(latLng){
	return([latLng.lat(),latLng.lng()]);
}

function pp_(lat,lng){
	return new google.maps.LatLng(lat,lng);
}


function b_(bounds){
	return([this.p_(bounds.getSouthWest()),
	this.p_(bounds.getNorthEast())]);
}
function bb_(sw,ne){
	return new google.maps.LatLngBounds(this.pp_.apply(this,sw),
									this.pp_.apply(this,ne));
}

function parseJSON( json , map ) {
    polygon = new google.maps.MVCArray();

    //Setup all the paths inside polygons
    json.forEach( function(rec) {
        rec.geometry.forEach( function( key ) {
			key.forEach( function ( value ) {
				polygon.push( pp_( value[0], value[1] ) );				
			})	
        });
    });	
	
	pathCoords = new google.maps.MVCArray();
	//Then set up your map:
	paddock = new google.maps.Polygon({
		path: pathCoords,		
		fillColor: '#3399CC',
		fillOpacity: 0.5 ,
		strokeColor: '#0066FF',
		strokeOpacity: 1,
		strokeWeight: 3
	});

	//You can then insert latLng objects:
	for (var i =0; i < polygon.getLength(); i++) {		
		var xy = polygon.getAt(i);
		paddock.getPath().insertAt( i, xy );		
	}
	
	return paddock;	
}

function polygonCenter(poly) {
    var lowx,
        highx,
        lowy,
        highy,
        lats = [],
        lngs = [],
        vertices = poly.getPath();

    for(var i=0; i<vertices.length; i++) {
      lngs.push(vertices.getAt(i).lng());
      lats.push(vertices.getAt(i).lat());
    }

    lats.sort();
    lngs.sort();
    lowx = lats[0];
    highx = lats[vertices.length - 1];
    lowy = lngs[0];
    highy = lngs[vertices.length - 1];
    center_x = lowx + ((highx-lowx) / 2);
    center_y = lowy + ((highy - lowy) / 2);
    return (new google.maps.LatLng(center_x, center_y));
  }
  
function calculateAreaFromPolygon() {
	if(paddock.getPath() && paddock.getPath().getArray().length > 2) {			
		var area = google.maps.geometry.spherical.computeArea(paddock.getPath());          
		var hectares = Math.round(area/10000 * 100) / 100;		  
//$('#area').val(Math.round(area/10000 * 100) / 100);
		return hectares.toPrecision(3);
	}
}

/*
function addManagementZones(map){

  var kmlLayer = new google.maps.KmlLayer({
    url: 'http://smartfarm.co.nz/maps/microfarm.kmz',
    suppressInfoWindows: true,
    map: map
  });
*/
	/*
  kmlLayer.addListener('click', function(kmlEvent) {
    var text = kmlEvent.featureData.description;
    showInContentWindow(text);
  });

  function showInContentWindow(text) {
    var sidediv = document.getElementById('content-window');
    sidediv.innerHTML = text;
  }
   
}
*/