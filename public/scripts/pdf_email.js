$(document).ready(function(){ 
	
	$(window).resize(function(){			
		var top = Math.max($(window).height() / 2 - $("#loader_icon")[0].offsetHeight / 2, 0);
		var left = Math.max($(window).width() / 2 - $("#loader_icon")[0].offsetWidth / 2, 0);
		$("#loader_icon").css('top', top + "px");
		$("#loader_icon").css('left', left + "px");
		$("#loader_icon").css('position', 'fixed');
	});

	// To initially run the resize method
	$(window).resize();

	$('#pdf_email').submit(function(e) {			
		
		
			//e.preventDefault();
			$('#loader_icon').show();
			var top = Math.max($(window).height() / 2 - $("#loader_icon")[0].offsetHeight / 2, 0);
			var left = Math.max($(window).width() / 2 - $("#loader_icon")[0].offsetWidth / 2, 0);
			$("#loader_icon").css('top', top + "px");
			$("#loader_icon").css('left', left + "px");
			$("#loader_icon").css('position', 'fixed');
			$('#email').blur();
			$(this).ajaxSubmit({ 
				//target:   '', // no target required for initial post
				//beforeSubmit: function() {
				//	$('#feedback_messages').empty(); // clear unwanted session feedback
				//},
				/*
				uploadProgress: function (event, position, total, percentComplete){	
					
					$.get( "getFeedbackResponse", function( data ) {						
						$('#feedback_messages').append(data);
						$('#feedback_messages').show();
					});
				},
				*/
				success:function (){
					//alert ("ajax success");
					$('#loader_icon').hide();
					//$.get( "getFeedbackResponse", function( data ) {						
					//	$('#feedback_messages').append(data);
					//	$('#feedback_messages').show();
					//});	
				},
				//resetForm: true		
			}); 
			return false; 
			
		});
});	