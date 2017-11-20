<div class="container">
    <h1>Paddock Survey Map</h1>
    <div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

		<div class="mapfit"><div id="map_canvas"></div><div>
		<input type="hidden" name="management_zone_map" id="management_zone_map" value="<?php echo $this->management_zone_map; ?>">
		<input type="hidden" name="paddock_google_latlong_paths" value='<?php echo $this->paddock_google_latlong_paths; ?>' id="paddock_google_latlong_paths" >
		<input type="hidden" name="farm_name" id="farm_name" value="<?php echo $this->farm_name; ?>">
		<input type="hidden" name="paddock_name" id="paddock_name" value="<?php echo $this->paddock_name; ?>">
		<input type="hidden" name="paddock_plant_date" id="paddock_plant_date" value="<?php echo $this->paddock_plant_date; ?>">
		<input type="hidden" name="paddock_zones" id="paddock_zones" value="<?php echo $this->paddock_zones; ?>">
		<input type="hidden" name="paddock_zone_sample_count" id="paddock_zone_sample_count" value="<?php echo $this->paddock_zone_sample_count; ?>">
		<!-- Return to data entry selection page -->
		<div class="app-button" style="margin: 15px 0 0 0;">                
			<a href="<?php echo Config::get('URL'); ?>reports">Back</a>
		</div>
		<!-- End content section -->
    </div>
</div>

