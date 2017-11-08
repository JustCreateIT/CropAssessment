
var map;
var geoJSON;
var request;
var gettingData = false;
var anchor;
var marker;
// https://home.openweathermap.org/api_keys
var openWeatherMapKey = "1114727b4c9e35f18431f3ee3bda2e2f";

function initMap() {

    var mapOptions = {
		zoom: 6,
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
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);	

	var service = new google.maps.places.PlacesService(map);

	service.getDetails({
		//placeId: 'ChIJTxJOziBNaG0RKX0FsNaXNlY'
		placeId: $('#paddock_google_place_id').val()				
	}, function(place, status) {
		if (status === google.maps.places.PlacesServiceStatus.OK) {
			map.setCenter(place.geometry.location);
			map.setZoom(13);
		}
	});
	// define infowindow
	var infowindow = new google.maps.InfoWindow;
    // Add interaction listeners to make weather requests
    google.maps.event.addListener(map, 'dragend', checkIfDataRequested);
	google.maps.event.addListener(map, 'zoom_changed', checkIfDataRequested);	
	google.maps.event.addDomListener(window, 'load', checkIfDataRequested);
	/* InfoWindow used to display weather data for a selected city */
    // Sets up and populates the info window with details
	map.data.addListener('click', function(event) { 	
		infowindow.setOptions({
			content: setInfoWindowContent(event),
			position:{
				lat: event.latLng.lat(),
				lng: event.latLng.lng()
			},
			pixelOffset: {
				width: 0,
				height: -15
			}
		});
	
		infowindow.open(map);
	});	
		
	// event to close the infoWindow with a click on the map
	google.maps.event.addListener(map, 'click', function() {
		infowindow.close();
	});
}
  
function setInfoWindowContent(event){

	
	// variable to define the content of Info Window
	var content = '<div id="iw_container">' +
                  '<div class="iw_title">'+event.feature.getProperty("city")+'</div>' +
				  '<div class="iw_content">Weather: '+event.feature.getProperty("weather")+'</div>' +
                  '<div class="iw_content">Temperature: '+Math.round(event.feature.getProperty("temperature")) + '&deg;C</div>' +
				  '<div class="iw_content_no_change">Pressure: '+ Math.round(event.feature.getProperty("pressure"))+' hPa</div>' +
				  '<div class="iw_content">Humidity: '+event.feature.getProperty("humidity")+'%</div>' +
				  '<div class="iw_content_no_change">Wind Speed: '+event.feature.getProperty("windSpeed")+' m/s</div>' +
				  '<div class="iw_content_no_change">Wind Direction: '+degToCompass(Math.round(event.feature.getProperty("windDegrees")))+ ' ['+Math.round(event.feature.getProperty("windDegrees"))+'&deg;]</div>' +				  				  
                  '</div>';
	
	return content;
} 

var degToCompass = function(windDegrees) {
	
	var compassPoints = ["N","NNE","NE","ENE","E","ESE", "SE", "SSE","S","SSW","SW","WSW","W","WNW","NW","NNW"];
    var x = parseInt((windDegrees/22.5)+0.5);
	var y = compassPoints.length; 
	var i = x % y;

    
    return compassPoints[i];
};

var checkIfDataRequested = function() {
	// Stop extra requests being sent
	while (gettingData === true) {
		request.abort();
		gettingData = false;
	}
	getCoords();
};

// Get the coordinates from the Map bounds
var getCoords = function() {
	var bounds = map.getBounds();
	var NE = bounds.getNorthEast();
	var SW = bounds.getSouthWest();
	getWeather(NE.lat(), NE.lng(), SW.lat(), SW.lng());
};



// Make the weather request
var getWeather = function(northLat, eastLng, southLat, westLng) {
	gettingData = true;
	var apicalltype = "http://api.openweathermap.org/data/2.5/box/city?bbox=";
	//var apicalltype = "http://api.openweathermap.org/data/2.5/forecast/city?bbox=";
	var requestString = apicalltype
						+ westLng + "," + northLat + "," //left top
						+ eastLng + "," + southLat + "," //right bottom
						+ map.getZoom()
						+ "&cluster=yes&format=json"
						+ "&APPID=" + openWeatherMapKey;						
					
	request = new XMLHttpRequest();
	request.onload = proccessResults;
	request.open("get", requestString, true);
	request.send();
};

// Take the JSON results and process them
var proccessResults = function() {
	//console.log(this);
	var results = JSON.parse(this.responseText);	
	if (results.list.length > 0) {		
		resetData();		
		for (var i = 0; i < results.list.length; i++) {							
			geoJSON.features.push(jsonToGeoJson(results.list[i]));		  
		}
		drawIcons(geoJSON);
	}
};

// For each result that comes back, convert the data to geoJSON
var jsonToGeoJson = function (weatherItem) {
	var feature = {
		type: "Feature",
		properties: {
			city: weatherItem.name,
			weather: weatherItem.weather[0].main,
			temperature: weatherItem.main.temp, // Kelvin
			min: weatherItem.main.temp_min,
			max: weatherItem.main.temp_max,
			humidity: weatherItem.main.humidity, 
			pressure: weatherItem.main.pressure, //hPa
			windSpeed: weatherItem.wind.speed, // m/s
			windDegrees: weatherItem.wind.deg,
			windGust: weatherItem.wind.gust,
			icon: "http://openweathermap.org/img/w/"
				  + weatherItem.weather[0].icon  + ".png",
			coordinates: [weatherItem.coord.lon, weatherItem.coord.lat]
		},
		geometry: {
			type: "Point",
			coordinates: [weatherItem.coord.lon, weatherItem.coord.lat]
		}
	};
	// Set the custom marker icon
	marker = map.data.setStyle(function(feature) {
		return {
			icon: {
				url: feature.getProperty('icon'),
				anchor: new google.maps.Point(25, 25)
			}
		};
	});
	// returns object
	return feature;
};

  // Add the markers to the map
  var drawIcons = function (weather) {
     map.data.addGeoJson(geoJSON);
     // Set the flag to finished
     gettingData = false;
  };

  // Clear data layer and geoJSON
  var resetData = function () {	 
    geoJSON = {
      type: "FeatureCollection",
      features: []
    };
    map.data.forEach(function(feature) {
      map.data.remove(feature);
    });
  };
  
  

  
 