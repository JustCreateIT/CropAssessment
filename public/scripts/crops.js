$(document).ready(function(){ 
	var population_label = jQuery("label[for='crop_target_population']").html();
	var optimal_emergence_rate = ' 95% emergence';
    /** Set target_population when #crop_plant_spacing loses focus **/   
    $("#crop_plant_spacing").blur(function(){ 	
		if ($("#crop_plant_spacing").val() > 0 && $("#crop_bed_width").val() > 0 && $("#crop_bed_rows").val() > 0) {			
			$("#crop_target_population").val(calculateTargetPopulation());
			jQuery("label[for='crop_target_population']").html("");
			jQuery("label[for='crop_target_population']").html(population_label +' ['+$("#hectare_target_population").val()+'/ha]'+optimal_emergence_rate);
			//$("#paddock_target_population").val($("#paddock_target_population").val()+' ['+$("#hectare_target_population").val()+' t/ha]');
        }
    });		
});

/* return total plant population for paddock size */
function calculateTargetPopulation(){ 
	/* crop_bed_width (float) (in metres)
	 * crop_bed_rows (integer)
	 * crop_plant_spacing (in millimetres) */
	var plantsPerSqm = 0;
	var totalPlantPopulation = 0;
	plantsPerSqm = ((1/$("#crop_bed_width").val())*$("#crop_bed_rows").val())*(1000/$("#crop_plant_spacing").val());
	// Working on standard 95% emergence rate
    totalPlantPopulation = Math.round((plantsPerSqm*10000*$("#paddock_area").val()*0.95)/1000)*1000;	
	var hectarePopulation = Math.round((totalPlantPopulation/$("#paddock_area").val())/1000)*1000;
	$("#hectare_target_population").val(hectarePopulation);	
	//alert ($("#hectare_target_population").val());
	return totalPlantPopulation.toFixed();
}
