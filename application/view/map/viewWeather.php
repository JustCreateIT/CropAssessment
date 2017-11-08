<div class="container">
    <h1>View Weather</h1>
    <div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>		
		<div id="map_canvas"></div>
		<input type="hidden" name="paddock_google_place_id" value="<?php echo $this->paddock_google_place_id; ?>" id="paddock_google_place_id">
		<!-- End content section -->
    </div>
</div>

