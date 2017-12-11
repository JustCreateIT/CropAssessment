var map;
var marker;
var paddock;
var pCenter;
var shape = [];

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
	
	// get polygon data	
	var json = JSON.parse($('#paddock_google_latlong_paths').val());	
	polygon = new google.maps.MVCArray();
	
	console.log(json);
    //Setup all the paths inside polygons
    json.forEach( function(rec) {
		
        rec.geometry.forEach( function( key ) {
			key.forEach( function ( value ) {
				polygon.push( polygonPush( value[0], value[1] ) );				
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
		strokeWeight: 3,
		draggable: true,
		editable: true,
		clickable: true
	});

	//You can then insert latLng objects:
	for (var i =0; i < polygon.getLength(); i++) {		
		var xy = polygon.getAt(i);
		paddock.getPath().insertAt( i, xy );		
	}
	

	//find the center of the polygon
	centerMap();
	
	// infowindow to display paddock and crop information
	var infowindow = new google.maps.InfoWindow({
	   content: setInfoWindowContent()
	});	

	// open infowindow by default
	infowindow.open(map, marker);		

	// Infowindows event listeners
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
	
	// center map on marker when zoom changes
	google.maps.event.addListener(map, 'zoom_changed', function() {
		map.setCenter(marker.getPosition());
	});

	google.maps.event.addListener(paddock.getPath(), 'dragend', function(){
		//paddock.setMap(map);
		polygonChanged(infowindow);				
	});

	google.maps.event.addListener(paddock.getPath(), 'set_at', function(){
		polygonChanged(infowindow);		
	}); 
	google.maps.event.addListener(paddock.getPath(), 'insert_at', function(){
		polygonChanged(infowindow);
	});	
	
	google.maps.event.addListener(paddock.getPath(), 'remove_at', function(){		
		polygonChanged(infowindow);
	});		
							
	google.maps.event.addListener(paddock, 'click', function(){
		polygonChanged(infowindow);			
	});

	geocoder = new google.maps.Geocoder();
	
	geocoder.geocode( { 'location': pCenter }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setZoom(map.getZoom());
			map.fitBounds(paddock.getBounds());			
		} else {
			//alert("Geocode was not successful for the following reason: " + status);
			map.setZoom(18);
		}
	});
}

function centerMap(){
	pCenter = polygonCenter(paddock);	
	map.setCenter(pCenter);	
	// variable to define the option of the marker
	marker = new google.maps.Marker({
		position: pCenter, // variable with the coordinates Lat and Lng
		map: map,
		title: $("#farm_name").val()
	});			
	paddock.setMap(map);
}

function polygonChanged(infowindow){
	infowindow.close();
	infowindow.setContent(setInfoWindowContent());
	infowindow.open(map, marker);
	$('#paddock_google_area').val(calculateAreaFromPolygon);
	$('#paddock_google_latlong_paths').val(JSON.stringify(buildShape(paddock, $("#paddock_name").val())));
	//console.log($('#paddock_google_latlong_paths').val());
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

function polygonPush(lat,lng){
	return new google.maps.LatLng(lat,lng);
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
			return hectares.toPrecision(3);
		}	

}

function buildShape(o){
	
	shape.length = 0;
	var tmp= {
		type:google.maps.drawing.OverlayType['POLYGON'], 
		farm_id:$("#farm_id").val(),
		farm_name:$("#farm_name").val(),
		paddock_id:$("#paddock_id").val(),
		paddock_name:$("#paddock_name").val(),
		plant_date:$("#paddock_plant_date").val(),
		area:$("#paddock_google_area").val(),
		zones:$("#paddock_zones").val(),
		samples:$("#paddock_zone_sample_count").val()		
		};
	tmp.geometry=m_(o.getPaths(),false);
	shape.push(tmp);	
	return shape;
}	

function l_(path,e){
	path=(path.getArray)?path.getArray():path;
	if(e){
		return google.maps.geometry.encoding.encodePath(path);
	}else{
		var r=[];
		for(var i=0;i<path.length;++i){
			r.push(this.p_(path[i]));
		}
		return r;
	}
}

function m_(paths,e){
	var r=[];
	paths=(paths.getArray)?paths.getArray():paths;
	for(var i=0;i<paths.length;++i){
		r.push(this.l_(paths[i],e));
	}
	return r;
}

function p_(latLng){
	return([latLng.lat(),latLng.lng()]);
}

function parseJSON( json , map ) {
    polygon = new google.maps.MVCArray();

    //Setup all the paths inside polygons
    json.forEach( function(rec) {
        rec.geometry.forEach( function( key ) {
			key.forEach( function ( value ) {
				polygon.push( polygonPush( value[0], value[1] ) );				
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
		strokeWeight: 3,
		editable: true,
		clickable: true	  
	});

	//You can then insert latLng objects:
	for (var i =0; i < polygon.getLength(); i++) {		
		var xy = polygon.getAt(i);
		paddock.getPath().insertAt( i, xy );		
	}
	
	return paddock;	
}