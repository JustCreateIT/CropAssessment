<div class="container">
    <h1>Define A New Crop</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<h2>Measurement Zone Information</h2>
        <p>
			<!-- define the names and percentage of total paddock area each zone occupies
			** Sum of all zones must = 100% -->
            <form id="input_form" name="input_form" value="create_zones" method="post" action="<?php echo Config::get('URL');?>setup/createZones_action">
                <?php if ($this->crop_zone_count) { ?>
					<p><input type="hidden" name="zone_count" id="zone_count" value="<?php echo $this->crop_zone_count ?>"></p>
					<?php
					for ($i = 0; $i < $this->crop_zone_count; $i++) { ?>
						<div class="float_left"><label>Zone <?php echo Statistics::getCharFromNumber($i+1); ?> name</label>
							<input type="text" name="zone_name_<?php echo $i+1; ?>" id="zone_name_<?php echo $i+1; ?>">
						</div>
						<div class="float_right"><label for="zone_paddock_percentage">Zone area</label>
							<input type="number" step=1 min=1 placeholder="% of total paddock area" name="zone_paddock_percentage_<?php echo $i+1; ?>" id="zone_paddock_percentage_<?php echo $i+1; ?>"required>
						</div>
					<?php } 
				} ?>				
				<div class="submit_float_left"> 
					<div class="app-button" style="margin:0; padding:0;">                
						<a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
					</div>
				</div>
				<div class="submit_float_left">
					<input type="submit" value="Save" id="submit_button" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc;"'; } ?>>
				</div>
				<div style="clear:both;"></div>
			</form>
        </p>		
    </div>
</div>