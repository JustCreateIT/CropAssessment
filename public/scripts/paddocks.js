$(document).ready(function(){  
	var geocoder;
    $("#paddock_address").blur(function(){ 		
		codeAddress();
    });	
});	

function initMap() {  
    geocoder = new google.maps.Geocoder();
}

function codeAddress() {
	var address = document.getElementById("paddock_address").value;
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			$("#paddock_latitude").val(results[0].geometry.location.lat().toFixed(14));
			$("#paddock_longitude").val(results[0].geometry.location.lng().toFixed(14));
			$("#paddock_google_place_id").val(results[0].place_id);
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}
