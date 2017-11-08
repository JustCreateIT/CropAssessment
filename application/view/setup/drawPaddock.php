<div class="container">
    <h1>Setup A New Paddock</h1>
		<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<h2>Draw Paddock Map</h2>
		<div id="map_canvas"></div>
		<form id="input_form" method="post" accept-charset="utf-8" action="<?php echo Config::get('URL');?>setup/drawPaddock_action">
			<input type="hidden" name="paddock_google_latlong_paths" value="<?php echo $this->paddock_google_latlong_paths; ?>" id="paddock_google_latlong_paths">
			<input type="hidden" name="paddock_google_area" value="<?php echo $this->paddock_google_area; ?>" id="paddock_google_area">
			<input type="hidden" name="paddock_google_place_id" value="<?php echo $this->paddock_google_place_id; ?>" id="paddock_google_place_id">
			<input type="submit" value='Continue' autocomplete="off" style="margin-top:15px;"/>
		</form>
		<!-- Return to the setup page -->
		<div class="app-button" style="margin:0; padding:0;">                
			<a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
		</div>	
		<!-- End content section -->
    </div>
</div>