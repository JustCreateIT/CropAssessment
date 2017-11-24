$(document).ready(function(){ 
	
	/* Initial state - disable all then call updateViewState to refresh */
	disableViewState();
	updateViewState();
	
	/* If farm selection option changes need to update state */
	$( "[name=farm_id]" ).change( function() {		
		disableViewState();
		updateViewState();
	}); 

	/* If paddock selection option changes need to update state */
	$( "[name=paddock_id]" ).change( function() {		
		disableViewState();
		updateViewState();
	}); 
	
		/* If crop selection option changes need to update state */
	$( "[name=crop_id]" ).change( function() {		
		disableViewState();
		updateViewState();
	}); 
	
});

function disableViewState(){
	$( "[name=reports]" ).each( function( index, element ){
		$( this ).prop("disabled", true);			
	});	
}


/* update view state enabling options that have reports available */
function updateViewState(){
	
	var paddock_name = $( "[name=paddock_id]" ).find(":selected").text();
	var paddock_id = $( "[name=paddock_id]" ).find(":selected").val();
	var crop_id = $( "[name=crop_id]" ).find(":selected").val();	
	
	// always show survey map
	$( "#survey" ).prop("disabled", false);
	
	//if ( $('#'+paddock_id).val() != null ) {
	if ( $('#'+crop_id).val() != null ) {		
		
		//var selected = $('#'+paddock_id).val();
		var selected = $('#'+crop_id).val();		
		var arr = selected.split(',');
	
		jQuery.each(arr, function(index, item) {
			// do something with `item` (or `this` is also `item` if you like)
			switch ( parseInt( this ) ) {			
				case 1:	
					$( "#emergence" ).prop("disabled", false);							
					break;
				case 2:
					$( "#threeleaf" ).prop("disabled", false);									
					break;
				case 3:
					$( "#fiveleaf" ).prop("disabled", false);					
					break;
				case 4:
					$( "#bulbing" ).prop("disabled", false);							
					break;
				case 5:
					$( "#harvest" ).prop("disabled", false);								
					break;			
				default:
			}
		});	
	}
}