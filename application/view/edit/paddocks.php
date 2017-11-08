<div class="container">
    <h1>Edit Paddocks</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		
		<div class="table-edit">  
			<table>
			<thead>
			<tr>
				<td><label style="width:60px;">Paddock<label></td>
				<td><label style="width:60px;">Address<label></td>
				<td><label style="width:50px;">Area (h)<label></td>
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
			<?php foreach ($this->paddocks as $paddock) { 
				$paddock_area = $paddock->paddock_google_area > 0 ? $paddock->paddock_google_area : $paddock->paddock_area; ?>
			
			<form action="<?= config::get("URL"); ?>edit/editSavePaddock" method="post">
				<tr>
					<td><input type="text" name="paddock_name" value="<?= $paddock->paddock_name; ?>"></td>
					<td><input type="text" name="paddock_address" value="<?= $paddock->paddock_address; ?>"></td>
					<td><input style="width:50px;" type="number" step=0.01 min=0 name="paddock_area" value="<?= $paddock_area ?>"></td>
					<td><input style="width:45px;" type="number" step=1 min=1 name="paddock_zone_count" value="<?= $paddock->paddock_zone_count; ?>"></td>
					<td><input style="width:55px;" type="number" step=1 min=1 name="paddock_zone_sample_count" value="<?= $paddock->paddock_zone_sample_count; ?>"></td>
					<td><input style="width:115px; padding:2.5px; font-family:arial;" type="date" name="paddock_plant_date" value="<?= date($paddock->paddock_plant_date); ?>"></td>
					<td><input style="width:55px;" type="number" step=0.01 min=0 name="paddock_bed_width" value="<?= $paddock->paddock_bed_width; ?>"></td>
					<td><input style="width:40px;" type="number" step=1 min=1 name="paddock_bed_rows" value="<?= $paddock->paddock_bed_rows; ?>"></td>
					<td><input style="width:50px;" type="number" min=0 name="paddock_plant_spacing" value="<?= $paddock->paddock_plant_spacing; ?>"></td>
					<td><input style="width:65px;" type="number" name="paddock_target_population" value="<?= $paddock->paddock_target_population; ?>"></td>
					<td><input style="width:75px;" type="text" name="variety_name" value="<?= $paddock->variety_id; ?>"></td>
					<td><input type="submit" value="Update"/></td>
				</tr>
				<input type="hidden" name="paddock_id" value="<?= $paddock->paddock_id; ?>" />
				<input type="hidden" name="return_page" value="paddocks">
			</form>
			<?php } ?>
			</table>				
        </div>
    </div>
</div>