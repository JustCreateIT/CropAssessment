$(document).ready(function(){ 
	
	setPopulation();
	$("#zone_id").change(function(){ 		
		setPopulation();
    });	
});

function setPopulation(){
	var obj = JSON.parse($('#population_info').val());
	var current_zone_id = $('#zone_id option:selected').val();
	$.each(obj, function(key,value){		
		if (value.zone_id == current_zone_id){
			var i = parseInt(value.zone_sample_plot_id)-1;
			$('#sample_count_'+i).val(value.sample_count);
		}
	});
}