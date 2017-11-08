$(document).ready(function(){ 
	// Set farm to first instance 

	//$("#plants_counted").blur(function(){
	$('#plants_counted').change(function(){ 
		averageLeaves();
    });

	$('#leaves_counted').change(function(){
		averageLeaves();
    });  
		
});


function averageLeaves(){
	
	if ($('#plants_counted').val() > 0 && $('#leaves_counted').val() > 0){
		var mean_leaf_number = Math.round($('#leaves_counted').val()/$('#plants_counted').val() *1000)/1000; // 3 decimal places -> 2 decimal = *100/100
		$('#mean_leaf_number').val(mean_leaf_number);
		
	}
	return;
}
