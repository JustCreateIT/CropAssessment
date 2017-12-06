$(document).ready(function(){ 
	// Set farm to first instance
	var initial_farm = JSON.parse($("#farm_details").val());
	addSelectOptions( initial_farm[0].farm.farm_id );	
	
	
    $("#farm_id").change(function(){ 
		// update on change (when multiple farms available)
		var f_id = $( "#farm_id option:selected" ).val();
		addSelectOptions(f_id);
		$(function() {
			// make me selected
			$("#farm_id").val(f_id);
		});
		
	});	

    $("#paddock_id").change(function(){ 
		// update on change (when multiple paddocks available)
		var f_id = $( "#farm_id option:selected" ).val();
		var p_id = $( "#paddock_id option:selected" ).val();
		addSelectOptions(f_id , p_id);
		$(function() {
			// make me selected
			$("#farm_id").val(f_id);
			$("#paddock_id").val(p_id);
		});		
	});	
	
	$("#crop_id").change(function(){ 
		// update on change (when multiple paddocks available)
		var f_id = $( "#farm_id option:selected" ).val();
		var p_id = $( "#paddock_id option:selected" ).val();
		var c_id = $( "#crop_id option:selected" ).val();
		addSelectOptions(f_id , p_id);
		$(function() {
			// make me selected
			$("#farm_id").val(f_id);
			$("#paddock_id").val(p_id);
			$("#crop_id").val(c_id);
		});		
	});	
});

function removeChildren(){
	$("#paddock_id").children().remove().end();
	$("#farm_id").children().remove().end();
	$("#crop_id").children().remove().end();
}

/* add options to farm, paddock and crop select boxes */
function addSelectOptions(f_id, p_id){ 
	
	// Remove all select options then add new paddock,crops specific to selected farm_id 
	removeChildren();
	// selectable options
	var farm_options= $("#farm_id");
	var paddock_options = $("#paddock_id");
	var crop_options = $("#crop_id");
	// farm data json
	var farms = JSON.parse($("#farm_details").val());

	$.each(farms, function (key, value){		
		farm_options.append($("<option />").val(value.farm.farm_id).text(value.farm.farm_name));
		if (value.farm.farm_id == f_id) { // selected farm
			var paddocks = value.farm.paddocks;
			// check to see if paddock select option changed
			if (p_id == null) { // default/initial state - no change
				// set to the first paddock associated with this farm
				p_id = paddocks[0].paddock_id; 				
			}
			$.each(paddocks, function (key, value){					
				if ( (value.paddock_id == p_id ) ) { // currently selected paddock
					if ( ($.isEmptyObject(value.crops) == false) || $("#page_function").val() == "create_crop" ) {
						// add associated crop plant date
						var crops = value.crops;
						$.each(crops, function (key, value){					
							crop_options.append($("<option />").val(value.crop_id).text(value.crop_plant_date)); 
						});
						// add associated paddock areas
						$("#paddock_area").val(value.paddock_area);	
					}
				}
				// add farms associated paddock selection values
				paddock_options.append($("<option />").val(value.paddock_id).text(value.paddock_name));			
			});		
		}
	});
}	
