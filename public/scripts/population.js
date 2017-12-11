$(document).ready(function(){ 
	//iterate each paddock table row and set hectare population estimate
	$("#edit_crops > tbody > tr").each(function(){	
		hectarePopulation($(this));		
	});	
	
	$('input').on('blur', function(){ 
		$(this).closest('tr').find('.crop_target_population').val(calculateTargetPopulation($(this)));
	});
	$('input').on('focus', function(){ 
		$(this).closest('tr').find('.crop_target_population').val(calculateTargetPopulation($(this)));
	});
});

/* return total plant population for paddock size */
function calculateTargetPopulation($e){ 
	/* crop_bed_width (float) (in metres)
	 * crop_bed_rows (integer)
	 * crop_plant_spacing (in millimetres) */
	var current_population = $e.closest("tr").find('.crop_target_population').val();	
	var crop_plant_spacing = $e.closest("tr").find($(".crop_plant_spacing")).val();
	var crop_bed_width = $e.closest("tr").find($(".crop_bed_width")).val();
	var crop_bed_rows = $e.closest("tr").find($(".crop_bed_rows")).val();
	var paddock_area = $e.closest("tr").find($(".paddock_area")).val();	
	var hectare_population = $e.closest("tr").find($(".hectare_target_population"));
 
	if (crop_plant_spacing > 0 && crop_bed_width > 0 && crop_bed_rows > 0) {		
		var plants_per_sqm = 0;
		var totalPlantPopulation = 0;
		
		plants_per_sqm = ((1/crop_bed_width)*crop_bed_rows)*(1000/crop_plant_spacing);		
		// Working on standard 95% emergence rate
		totalPlantPopulation = Math.round((plants_per_sqm*10000*paddock_area*0.95)/1000)*1000;	
		//calculate new target hectare population
		hectarePopulation(hectare_population);
		return totalPlantPopulation.toFixed();	
	
	} else {
		//calculate new target hectare population
		return current_population;
	}
}

function hectarePopulation($e){
	
	var survey_population = $e.closest("tr").find($(".crop_target_population")).val();
	var paddock_area = $e.closest("tr").find($(".paddock_area")).val();
	var hectare_population = $e.closest("tr").find($(".hectare_target_population"));	
	hectare_population.val(Math.round((survey_population/paddock_area)/1000)*1000);

}
