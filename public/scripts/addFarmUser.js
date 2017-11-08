$(document).ready(function() { 
	
	$(window).resize(function(){
		$('#loader_icon').css({
			position:'absolute',
			left: ($(window).width() - $('#loader_icon').outerWidth())/2,
			top: (($(window).height() - $('#loader_icon').outerHeight())/2)+100
		});
	});
	
	// To initially run the resize method
	$(window).resize();
	
	$('#addFarmUser_action').submit(function(e) {
		e.preventDefault();
		$('#loader_icon').show();
		$('#btnSubmit').blur();
		$(this).ajaxSubmit({ 				
			beforeSubmit: function() {
				$('#feedback_messages').empty(); // clear unwanted session feedback
			},
			success:function (){
				$('#loader_icon').hide();
				// check for any session feedback messages and display as needed
				$.get( "getAddFarmUserFeedbackResponse", function( data ) {						
					$('#feedback_messages').append(data);
					$('#feedback_messages').show();
				});	
			},
			resetForm: true		
		}); 
		return false;		
	});
});