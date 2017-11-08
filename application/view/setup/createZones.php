<div class="container">
    <h1>Setup A New Paddock</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<h2>Measurement Zone Information</h2>
        <p>
			<!-- define the names and percentage of total paddock area each zone occupies
			** Sum of all zones must = 100% -->
            <form id="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createZones_action">
                <?php if ($this->paddock_zone_count) { 
					?>
					<p><input type="hidden" name="paddock_zone_count" id="paddock_zone_count" value="<?php echo $this->paddock_zone_count ?>"></p>
					<?php
					for ($i = 0; $i < $this->paddock_zone_count; $i++) { ?>
						<div class="float_left"><label>Zone <?php echo Statistics::getCharFromNumber($i+1); ?> name</label>
							<input type="text" name="zone_name_<?php echo $i+1; ?>" id="zone_name_<?php echo $i+1; ?>">
						</div>
						<div class="float_right"><label for="zone_paddock_percentage">Zone area</label>
							<input type="number" step=1 min=1 placeholder="% of total paddock area" name="zone_paddock_percentage_<?php echo $i+1; ?>" id="zone_paddock_percentage_<?php echo $i+1; ?>"required>
						</div>
					<?php } 
				} ?>				
                <p><input type="submit" value="Save Paddock Details" id="submit_button" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>></p>
            </form>
			<!-- Return to the setup page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
            </div>			
        </p>		
    </div>
</div>