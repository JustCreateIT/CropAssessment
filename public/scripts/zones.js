$(document).ready(function(){  
   
	//alert('createZones');
	
	var zones = $("#zone_count").val();
	var secondLastZone = zones-1;
	
	$("[id^=zone_paddock_percentage_]").blur(function(){        
		if($(this).attr("id") == "zone_paddock_percentage_"+secondLastZone){
			/* sum the zone percentage sizes */
			sumZoneSizes(zones);
			$("#zone_name_"+zones).focus();
		}
    }); 
		
});

function sumZoneSizes(zones){
    var add = 0;
    $("[id^=zone_paddock_percentage_]").each(function(){
       add += Number($(this).val());
    });
    if(add < 100) {		
		$("#zone_paddock_percentage_"+zones).val(100-add);        
    }
	return;
}