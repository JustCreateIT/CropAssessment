<div class="container">
    <h1>Setup A New Paddock</h1>
    <!--<div class="box">-->
	<!--<div class="login-box" style="width: 100%; display: block;">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<h2>Crop Details</h2>
        <p>
            <form id="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createCrop_action">
                <p><label>Variety</label>					
				<select name="paddock_variety_id">
					<?php foreach ($this->crop_variety as $variety) { ?>
						<option value="<?= $variety->variety_id; ?>" <?php if($variety->variety_id==$this->variety_id){ ?>selected<?php } ?>><?= $variety->variety_name; ?></option>
					<?php } ?>					
				</select></p>				
                <p><label>Planting date</label><input type="date" name="paddock_plant_date" value="<?php echo $this->paddock_plant_date; ?>" required /></p>
                <p><label>Bed width</label><input type="number" step=0.01 min=0 name="paddock_bed_width" id="paddock_bed_width" value="<?php echo $this->paddock_bed_width; ?>" placeholder="in metres" required /></p>
                <p><label>Rows/bed</label><input type="number" min=0 name="paddock_bed_rows" id="paddock_bed_rows" value="<?php echo $this->paddock_bed_rows; ?>" required /></p>
                <p><label>Plant spacing</label><input type="number" min=0 name="paddock_plant_spacing" id="paddock_plant_spacing" value="<?php echo $this->paddock_plant_spacing; ?>" placeholder="in millimetres" required  /></p>
				<p><label for="paddock_target_population">Total Paddock Target Population</label><input type="number" min=0 name="paddock_target_population" id="paddock_target_population" value="<?php echo $this->paddock_target_population; ?>" required /></p>
				<input type="hidden" name="paddock_area" id="paddock_area" value="<?php echo $this->paddock_area; ?>" />
				<input type="hidden" name="hectare_target_population" id="hectare_target_population" value="<?php echo $this->hectare_target_population; ?>" />
                <input type="submit" value="Continue">
            </form>
			<!-- Return to the setup page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>setup">Exit</a>
            </div>			
        </p>        
    </div>
</div>
