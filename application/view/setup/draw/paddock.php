<div class="container">
    <h1>Draw Paddock Boundary</h1>
		<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>		
		<div id="map_canvas"></div>
		<form id="input_form" method="post" accept-charset="utf-8" action="<?php echo Config::get('URL');?>setup/drawPaddock_action">
			<input type="hidden" name="paddock_google_latlong_paths" value="<?php echo $this->paddock_google_latlong_paths; ?>" id="paddock_google_latlong_paths">
			<input type="hidden" name="paddock_google_area" value="<?php echo $this->paddock_google_area; ?>" id="paddock_google_area">
			<input type="hidden" name="paddock_google_place_id" value="<?php echo $this->paddock_google_place_id; ?>" id="paddock_google_place_id">
				<div class="submit_float_left"> 
					<div class="app-button" style="margin:0; padding:0;">                
						<a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
					</div>
				</div>
				<div class="submit_float_left">
					<input type="submit" value="Continue">
				</div>
				<div style="clear:both;"></div>				
				
            </form>
		<!-- End content section -->
    </div>
</div>