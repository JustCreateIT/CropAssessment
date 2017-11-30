$(document).ready(function(){ 
	
	setPopulation();
	$("#zone_id").change(function(){ 		
		setPopulation();
    });	
});

function setPopulation(){
	var obj = JSON.parse(window.atob($('#population_info').val()));
	var current_zone_id = $('#zone_id option:selected').val();
	setDefaults();
	$.each(obj, function(key,value){	
		var i = parseInt(value.zone_sample_plot_id)-1;
		if (value.zone_id == current_zone_id){							
			// populate as required 
			if(typeof value.sample_date !== 'undefined'){$('#sample_date').val(value.sample_date);};
			if(typeof value.sample_count !== 'undefined'){$('#sample_count_'+i).val(value.sample_count);};
			if(typeof value.sample_comment !== 'undefined'){$('#sample_comment_'+i).val(value.sample_comment);};
			if(typeof value.sample_ela_score !== 'undefined'){$('#sample_ela_score_'+i).val(value.sample_ela_score);};
			if(typeof value.mean_leaf_number !== 'undefined'){setLeafNumber(value.mean_leaf_number);};
			if(typeof value.sample_bulb_weight !== 'undefined'){$('#sample_bulb_weight_'+i).val(value.sample_bulb_weight);};
		}
	});
}

function setLeafNumber($leaf_number){
	if ($leaf_number !== null) {
		$('#plants_counted').val(100);
		var leaves = 100*$leaf_number;
		$('#leaves_counted').val(leaves.toPrecision(3));
		$('#mean_leaf_number').val($leaf_number);	
	}
}

function setDefaults(){
	$('#plants_counted').val("");
	$('#leaves_counted').val("");
	$('#mean_leaf_number').val("");
	$('[id^="sample_count_"]').val("");
	$('[id^="sample_comment_"]').val("");
	$('[id^="sample_ela_score_"]').val("");
	$('[id^="sample_bulb_weight_"]').val("");
}