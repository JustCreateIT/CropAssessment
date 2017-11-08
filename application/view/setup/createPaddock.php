<div class="container">
    <h1>Setup A New Paddock</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<h2>Paddock Information</h2>
        <p>	
			<form id="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createPaddock_action">
			<p><label>Select Farm Where Paddock Is Located</label>
			<select name="farm_id">
				<?php foreach ($this->farms as $farm) { ?>
					<option value="<?= $farm->farm_id; ?>"><?= $farm->farm_name; ?></option>
				<?php } ?>	
			</select></p>		
			<p><label>Paddock name</label><input type="text" name="paddock_name" value="<?php $this->paddock_name; ?>" required /></p>			
			<p><label>Paddock Address</label><input type="text" name="paddock_address" value="<?php $this->paddock_address;?>" id="paddock_address" required /></p>
			<p><label>Paddock Area</label><input type="number" step=0.01 min=0 name="paddock_area" placeholder="in hectares" value="<?php $this->paddock_area; ?>" required /></p>
			<p><label>Latitude</label><input type="number" step=0.00000000000001 name="paddock_latitude" value="<?php $this->paddock_latitude; ?>" id="paddock_latitude"/></p>
			<p><label>Longitude</label><input type="number" step=0.00000000000001 name="paddock_longitude" value="<?php $this->paddock_longitude; ?>" id="paddock_longitude" /></p>			
			<p><label>Number of measurement zones</label><input type="number" step=1 min=1 name="paddock_zone_count" value="<?php $this->paddock_zone_count;?>" required /></p>
			<p><label>Number of sample plots per zone</label><input type="number" step=1 min=0 name="paddock_zone_sample_count" value="<?php $this->paddock_zone_sample_count;?>" required /></p>
			<input type="hidden" name="paddock_google_place_id" id="paddock_google_place_id" value="<?php $this->paddock_google_place_id;?>" />
			<input type="submit" value='Continue' autocomplete="off" />
			</form>
			<!-- Return to the setup page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
            </div>			
        </p>        
    </div>
</div>
