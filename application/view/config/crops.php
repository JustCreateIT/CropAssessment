<div class="container">
    <h1>Edit Crops</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>		
		<div class="table-edit">  
			<table>
			<thead>
			<tr>
				<td><label style="width:60px;">Paddock<label></td>
				<td><label style="width:45px;">Zones<label></td>
				<td><label style="width:55px;">Samples<label></td>
				<td><label style="width:65px;">Date Planted<label></td>
				<td><label style="width:55px;">Bedwidth (m)<label></td>
				<td><label style="width:40px;">Rows<label></td>
				<td><label style="width:50px;">Spacing (mm)<label></td>
				<td><label style="width:75px;">Population<label></td>
				<td colspan="2"><label style="width:75px;">Variety<label></td>		
			<tr>
			</thead>
			<?php foreach ($this->crops as $crop) { 
				 ?>
			<form action="<?= config::get("URL"); ?>config/configUpdateDeleteCrop" method="post">
				<tr>
					<td><input type="text" name="paddock_name" value="<?= $this->paddock->paddock_name; ?>" disabled></td>
					<td><input style="width:45px;" type="number" step=1 min=1 name="crop_zone_count" value="<?= $crop->crop_zone_count; ?>"></td>
					<td><input style="width:55px;" type="number" step=1 min=1 name="crop_zone_sample_count" value="<?= $crop->crop_zone_sample_count; ?>"></td>
					<td><input style="width:125px; padding:2.5px; font-family:arial;" type="date" name="crop_plant_date" value="<?= date($crop->crop_plant_date); ?>"></td>
					<td><input style="width:55px;" type="number" step=0.01 min=0 name="crop_bed_width" value="<?= $crop->crop_bed_width; ?>"></td>
					<td><input style="width:40px;" type="number" step=1 min=1 name="crop_bed_rows" value="<?= $crop->crop_bed_rows; ?>"></td>
					<td><input style="width:50px;" type="number" min=0 name="crop_plant_spacing" value="<?= $crop->crop_plant_spacing; ?>"></td>
					<td><input style="width:65px;" type="number" name="crop_target_population" value="<?= $crop->crop_target_population; ?>"></td>
					<td><select style="padding: 4px 0px 4px 2px;" name="variety_id" id="variety_id">
					<?php foreach ($this->variety_data as $variety) { 							
						if ($variety->variety_id == $crop->variety_id) { ?> 
						<option value="<?= $variety->variety_id ?>" selected><?= ucwords($variety->variety_name) ?></option>
					<?php } else { ?>
						<option value="<?= $variety->variety_id ?>"><?= ucwords($variety->variety_name)?></option>
					<?php } 									
					} ?>
					</select></td>
					<td><input type="submit" name="submit" value="Update" <?php if((Session::get("user_account_type") == 1)){ 
						echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td>
					<td><input type="submit" name="submit" value="Delete" <?php if((Session::get("user_account_type") == 1)){ 
						echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>/></td>
				</tr>
				<input type="hidden" name="paddock_id" value="<?= $this->paddock_id ?>" />
				<input type="hidden" name="crop_id" value="<?= $this->crop_id ?>" />
				<input type="hidden" name="return_page" value="config/crops">
			</form>
			<?php } ?>			
			</table>				
        </div>
		<div class="app-button" style="margin: 15px 0 0 0;">                
			<a href="<?php echo Config::get('URL'); ?>config">Exit</a>
		</div>
		<!-- End content section -->
    </div>
</div>