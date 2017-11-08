var map;
var paddock;
var arr = [];
var vertices;
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
		zoom: 19,
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		// Center on Wellington, NZ
		center: new google.maps.LatLng(-41.2865,174.7762),
		mapTypeControl: true,
		mapTypeControlOptions: {
			position: google.maps.ControlPosition.TOP_LEFT,
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
			mapTypeIds: ['roadmap', 'terrain', 'satellite']
		}
 
	};
	
	// Drawing Manager control options
	var paddock = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.POLYGON,
		drawingControl: true,
		drawingControlOptions: {
			position: google.maps.ControlPosition.TOP_RIGHT,
			drawingModes: [google.maps.drawing.OverlayType.POLYGON]
		},
			polygonOptions: {
				editable: true,
				clickable: true,
				fillColor: '#3399CC',
				fillOpacity: 0.5 ,
				strokeColor: '#0066FF',
				strokeOpacity: 1
				
			}		  
	});
	
	// Create Map and bind Drawing Manager
	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);		
	paddock.setMap(map);
	
	// delete menu to remove vertices      
	DeleteMenu.prototype = new google.maps.OverlayView();

	
	// Define new PlacesService and use placeId to lookup information
	var service = new google.maps.places.PlacesService(map);

	service.getDetails({
		// Use placeId assigned to paddock to retrieve location information
		placeId: $('#paddock_google_place_id').val()				
	}, function(place, status) {
		if (status === google.maps.places.PlacesServiceStatus.OK) {
			
			var marker = new google.maps.Marker({
				map: map,
				position: place.geometry.location
			});
			map.setCenter(place.geometry.location);
			map.setZoom(19);
			
			// Fit the map to the viewport boundary of the paddock(s)
			/* Requires polygon paths information
			var paddockBounds = new google.maps.LatLngBounds(
				place.geometry.viewport.getSouthWest(), 
				place.geometry.viewport.getNorthEast()
			);

			map.fitBounds(paddockBounds);
			 */
		}
		
	});	
	
	// overlaycomplete listeners
	google.maps.event.addListener(paddock, "overlaycomplete", function(event) {
		var newShape = event.overlay;
		newShape.type = event.type;

	});

	google.maps.event.addListener(paddock, "overlaycomplete", function(event){
		overlayClickListener(event.overlay);
		
		shape = buildShape(event.overlay);	
		$('#paddock_google_latlong_paths').val(JSON.stringify(shape));

		// stop drawing mode when polygon complete
		paddock.setOptions({drawingMode: null});
	});	


	// overlay events handler
	function overlayClickListener(overlay) {
		
		google.maps.event.addListener(overlay, "mouseup", function(event){
			
			shape = buildShape(overlay);
			$('#paddock_google_latlong_paths').val(JSON.stringify(shape));

		});	

		var deleteMenu = new DeleteMenu();
		google.maps.event.addListener(overlay, 'rightclick', function(e) {				
			// Check if click was on a vertex control point
			if (e.vertex == undefined) {
				// right click on paddock will remove it 
				this.setMap(null);
				paddock.setOptions({drawingMode: google.maps.drawing.OverlayType.POLYGON});
				// clear any path information 
				$('#paddock_google_latlong_paths').val(null);				
				return;
			}			
			deleteMenu.open(map, overlay.getPath(), e.vertex);
		});	
		
		overlay.getPaths().forEach(function(path, index){

			google.maps.event.addListener(path, 'insert_at', function(){
				// New point
				//arr = addVertices(overlay);				
				//$('#paddock_google_latlong_paths').val(arr);
				//alert("new point added "+$('#paddock_google_latlong_paths').val());
				shape = buildShape(overlay);
				$('#paddock_google_latlong_paths').val(JSON.stringify(shape));				
			});

			google.maps.event.addListener(path, 'remove_at', function(){
				// Point was removed
				shape = buildShape(overlay);
				$('#paddock_google_latlong_paths').val(JSON.stringify(shape));				
				//alert("point removed "+$('#paddock_google_latlong_paths').val());
			});

			google.maps.event.addListener(path, 'set_at', function(){
				// Point was moved
				shape = buildShape(overlay);
				$('#paddock_google_latlong_paths').val(JSON.stringify(shape));
				//alert("point moved "+$('#paddock_google_latlong_paths').val());
			});
		});
	}
	
	/**
	* A menu that lets a user delete a selected vertex of a path drawn on a google map.
	* @constructor
	*/
	function DeleteMenu() {
		this.div_ = document.createElement('div');
		this.div_.className = 'delete-menu';
		this.div_.innerHTML = 'Delete Vertex';

		var menu = this;
		google.maps.event.addDomListener(this.div_, 'click', function() {
			menu.removeVertex();
		});
	}

	DeleteMenu.prototype.onAdd = function() {
		var deleteMenu = this;
		var map = this.getMap();
		this.getPanes().floatPane.appendChild(this.div_);

		// mousedown anywhere on the map except on the menu div will close the menu.
		this.divListener_ = google.maps.event.addDomListener(map.getDiv(), 'mousedown', function(e) {
			if (e.target != deleteMenu.div_) {
				deleteMenu.close();
			}
		}, true);
	};


	DeleteMenu.prototype.onRemove = function() {	
		google.maps.event.removeListener(this.divListener_);
		this.div_.parentNode.removeChild(this.div_);

		// clean up
		this.set('position');
		this.set('path');
		this.set('vertex');
	};

	DeleteMenu.prototype.close = function() {
		this.setMap(null);
	};

	DeleteMenu.prototype.draw = function() {
		var position = this.get('position');
		var projection = this.getProjection();

		if (!position || !projection) {
			return;
		}

		var point = projection.fromLatLngToDivPixel(position);
		this.div_.style.top = point.y + 'px';
		this.div_.style.left = point.x + 'px';
	};

	/**
	* Opens the menu at a vertex of a given path.
	*/
	DeleteMenu.prototype.open = function(map, path, vertex) {
		this.set('position', path.getAt(vertex));
		this.set('path', path);
		this.set('vertex', vertex);
		this.setMap(map);
		this.draw();
	};

	/**
	* Deletes the vertex from the path.
	*/
	DeleteMenu.prototype.removeVertex = function() {
		var path = this.get('path');
		var vertex = this.get('vertex');

		if (!path || vertex == undefined) {
			this.close();
			return;
		}

		path.removeAt(vertex);
		this.close();
	};
}

function buildShape(o){
	
	shape.length = 0;
	var tmp={type:google.maps.drawing.OverlayType['POLYGON'], id:null};
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