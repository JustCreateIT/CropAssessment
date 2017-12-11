<div class="container">
    <h1>Edit Crops</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>		
		<div class="table-edit">		 
			<table id="edit_crops">
			<thead>
			<tr>
				<td><label style="width:70px;">Paddock Name<label></td>
				<td><label style="width:50px;">Survey Area (ha)<label></td>
				<td><label style="width:50px;">Number Of Zones<label></td>
				<td><label style="width:50px;">Samples Per Zone<label></td>
				<td><label style="width:75px;">Date Planted<label></td>
				<td><label style="width:50px;">Bed Width (m)<label></td>
				<td><label style="width:40px;">Rows Per Bed<label></td>
				<td><label style="width:50px;">Plant Spacing (mm)<label></td>
				<td><label style="width:75px;">Estimated Survey Population<label></td>
				<td><label style="width:75px;">Plants Per Hectare (95%)<label></td>
				<td colspan="2"><label style="width:75px;">Variety<label></td>		
			<tr>
			</thead>
			<tbody>
			<?php foreach ($this->crops as $crop) { ?>
				<form action="<?= config::get("URL"); ?>config/configUpdateDeleteCrop" method="post">
				<tr>
					<td><input style="width:150px;" type="text" name="paddock_name" value="<?= $this->paddock->paddock_name; ?>" disabled readonly></td>
					<td><input style="width:50px;" type="text" name="paddock_area" class="paddock_area" value="<?php if($this->paddock->paddock_google_area > 0){ echo $this->paddock->paddock_google_area; } else { echo $this->paddock->paddock_area; } ?>" disabled readonly></td>					
					<td><input style="width:50px;" type="number" name="crop_zone_count" value="<?= $crop->crop_zone_count; ?>" disabled readonly></td>
					<td><input style="width:50px;" type="number" Sname="crop_zone_sample_count" value="<?= $crop->crop_zone_sample_count; ?>" disabled readonly></td>
					<td><input style="width:125px; padding:2.5px; font-family:arial;" type="date" name="crop_plant_date" value="<?= date($crop->crop_plant_date); ?>"></td>
					<td><input style="width:50px;" type="number" step=0.01 min=0 name="crop_bed_width" class="crop_bed_width" value="<?= $crop->crop_bed_width; ?>"></td>
					<td><input style="width:40px;" type="number" step=1 min=1 name="crop_bed_rows" class="crop_bed_rows" value="<?= $crop->crop_bed_rows; ?>"></td>
					<td><input style="width:50px;" type="number" min=0 name="crop_plant_spacing" class="crop_plant_spacing" value="<?= $crop->crop_plant_spacing; ?>"></td>
					<td><input style="width:75px;" type="number" name="crop_target_population" class="crop_target_population" value="<?= $crop->crop_target_population; ?>" readonly></td>
					<td><input style="width:75px;" type="number" name="hectare_target_population" class="hectare_target_population" readonly></td>					
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
						echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>></td>
					<td><input type="submit" name="submit" value="Delete" <?php if((Session::get("user_account_type") == 1)){ 
						echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc; cursor: default;"'; } ?>></td>
				</tr>
				<input type="hidden" name="crop_id" value="<?= $crop->crop_id; ?>">
				<input type="hidden" name="return_page" value="<?= $crop->crop_id; ?>">
				</form>	
			<?php } ?>	
			</tbody>
			</table>
					
        </div>
		<div class="app-button" style="margin: 15px 0 0 0;">                
			<a href="<?php echo Config::get('URL'); ?>config">Exit</a>
		</div>
		<!-- End content section -->
    </div>
</div>