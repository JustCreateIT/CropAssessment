$(document).ready(function() { 
		
	$(window).load(function () {
		var ind = 0;		
		$( ".image-upload" ).each(function () {
			//$("#image-upload_"+ind).css('background-image', 'url(../images/upload_small.jpg)');	
			$("#image-upload_"+ind).css({
					"background-image": "url(../../images/upload_small.jpg)",
					"position": "relative",
					"background-repeat": "no-repeat",
					"background-size": "contain",
					"background-position": "center",
					"background-color": "#fff",
					"cursor": "pointer",
					"align-content": "center",
					"vertical-align": "middle",
					"padding": "0px",						
					"margin": "0px"
			});	
			ind++;
		});		
	});	
	
	
	//var plot_count = $( ".image-upload" ).length - 1;

	
	
	
	
	$(window).resize(function(){
		$('#loader_icon').css({
			position:'fixed',
			left: ($(window).width() - $('#loader_icon').outerWidth())/2,
			top: (($(window).height() - $('#loader_icon').outerHeight())/2)+100
		});
	});
	
	// To initially run the resize method
	$(window).resize();
	
	// when a user clicks on an upload image
	$(".sample_file_label").click(function() {
		var i = $(".sample_file_label").index( this );
		$("#sample_file_"+i).change(function() {
			var file_name = $("#sample_file_"+i)[0].files[0].name;	
			// set tooltip text
			$( "#tooltiptext_"+i ).text( file_name );
			var char_count = $( "#tooltiptext_"+i ).text().length;	
			var width_textbox = char_count*6;
			$( "#tooltiptext_"+i ).css("text-transform","lowercase");
			$( "#tooltiptext_"+i ).css("width",width_textbox);	
			//$("#image-upload_"+i).css('background-image', 'url(../images/tick_small.jpg)');
			$("#image-upload_"+i).css({
					"background-image": "url(../../images/tick_small.jpg)",
					"position": "relative",
					"background-repeat": "no-repeat",
					"background-size": "contain",
					"background-position": "center",
					"background-color": "#fff",
					"cursor": "pointer",
					"align-content": "center",
					"vertical-align": "middle",
					"padding": "0px",						
					"margin": "0px"
			});	
		});
	});	
	
	$('#imageEmail').submit(function(e) {			
		
		if($('#sample_file').val()) {
			e.preventDefault();
			$('#loader_icon').show();
			$('#btnSubmit').blur();
			$(this).ajaxSubmit({ 
				//target:   '', // no target required for initial post
				beforeSubmit: function() {
					$('#feedback_messages').empty(); // clear unwanted session feedback
				},
				/*
				uploadProgress: function (event, position, total, percentComplete){	
					
					$.get( "getFeedbackResponse", function( data ) {						
						$('#feedback_messages').append(data);
						$('#feedback_messages').show();
					});
				},
				*/
				success:function (){
					$('#loader_icon').hide();
					$.get( "getFeedbackResponse", function( data ) {						
						$('#feedback_messages').append(data);
						$('#feedback_messages').show();
					});	
				},
				resetForm: true		
			}); 
			return false; 
		}
	});

});