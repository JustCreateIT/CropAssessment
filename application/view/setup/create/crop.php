
			
			
			<div class="container">
    <h1>Define A New Crop</h1>
    <!--<div class="box">-->
	<!--<div class="login-box" style="width: 100%; display: block;">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<p>
            <form id="input_form" name="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createCrop_action">
				<div class="float_left">
					<label for="farm_id">Farm</label>
					<select name="farm_id" id="farm_id"></select>
				</div>
				<div class="float_right">
					<label for="paddock_id">Paddock</label>
					<select name="paddock_id" id="paddock_id"></select>
				</div>
				
				<div class="float_left"><label>Variety</label>					
					<select name="crop_variety_id">
						<?php foreach ($this->crop_variety as $variety) { ?>
							<option value="<?= $variety->variety_id; ?>"><?= $variety->variety_name; ?></option>
						<?php } ?>					
					</select></div>	
				<div class="float_right"><label>Plant date</label>
					<input type="date" name="crop_plant_date" value="" required />
				</div>	
				<div style="clear:both;"></div>
				<div><label>Bed width</label><input type="number" step=0.01 min=0 name="crop_bed_width" id="crop_bed_width" value="" placeholder="in metres" required /></div>
                <div><label>Rows/bed</label><input type="number" min=0 name="crop_bed_rows" id="crop_bed_rows" value="" required /></div>
                <div><label>Plant spacing</label><input type="number" min=0 name="crop_plant_spacing" id="crop_plant_spacing" value="" placeholder="in millimetres" required  /></div>
				<div><label for="crop_target_population">Target Population</label><input type="number" min=0 name="crop_target_population" id="crop_target_population" value="" required /></div>
				<div><label>Number of measurement zones</label><input type="number" step=1 min=1 name="crop_zone_count" value="" required /></div>
				<div><label>Number of sample plots per zone</label><input type="number" step=1 min=0 name="crop_zone_sample_count" value="" required /></div>
				<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>
				<input type="hidden" name="paddock_area" id="paddock_area" value="">
				<input type="hidden" name="page_function" id="page_function" value="create_crop">
				<input type="hidden" name="hectare_target_population" id="hectare_target_population" value="" />
                
				<div class="submit_float_left"> 
					<div class="app-button" style="margin:0; padding:0;">                
						<a href="<?php echo Config::get('URL'); ?>setup">Back</a>
					</div>
				</div>
				<div class="submit_float_left">
					<input type="submit" value="Continue">
				</div>
				<div style="clear:both;"></div>
            </form>
        </p>        
    </div>
</div>
