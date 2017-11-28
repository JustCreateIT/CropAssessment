$(document).ready(function(){ 
		$('input[rel="generate_password"]').each(function(){
			$(this).val(randString($(this)));
		});
		
		$("#generate_password").click(function(){
			var field = $(this).closest('div').find('input[rel="generate_password"]');
			field.val(randString(field));
			this.blur();
			this.hidefocus = true;
			this.style.outline = 'none';
		});
		
		$("#generate_password").keypress(function(){
			var field = $(this).closest('div').find('input[rel="generate_password"]');
			field.val(randString(field));
			this.blur();
			this.hidefocus = false;
			this.style.outline = null;
		});
});

function randString(id){
	var dataset = $(id).attr('data-character-set').split(',');
	var possible = '';
	
	if ($.inArray('a-z', dataset) >= 0){
		possible += 'abcdefghijklmnopqrstuvwxyz';
	}
	
	if ($.inArray('A-Z', dataset) >= 0){
		possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	
	if ($.inArray('0-9', dataset) >= 0){
		possible += '0123456789';
	}
	
	if ($.inArray('#', dataset) >= 0){
		possible += '!{}[]()%&$^<>?_@~|#*';
	}

	var text = '';
	for(var i=0;i<$(id).attr('data-size'); i++){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;	
}