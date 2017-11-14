<div class="container">
    <h1>Enter Harvest Data</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php 
			$this->renderFeedbackMessages(); 
			/* Set current page name */
			Session::set('page', basename(__FILE__, ".php"));
		?>
        <p>
			<form id="imageEmail"  method="post" action="<?php echo Config::get('URL');?>collection/enterCollectionData_action" enctype="multipart/form-data">
                <div class="container_center">
					<div><label for="farm_name">Farm</label>
						<input type="text" style="background-color: transparent;" id="farm_name"  name="farm_name" value="<?php echo $this->farm_name ?>" readonly />							
					</div>
					<div><label for="paddock_name">Paddock</label>
						<input type="text" style="background-color: transparent;"  id="farm_name" name="paddock_name" value="<?php echo $this->paddock_name ?>" readonly />							
					</div>
					<div><label for="crop_plant_date">Plant Date</label>
						<input type="text" style="background-color: transparent;"  name="crop_plant_date" id="crop_plant_date" value="<?php echo date_format(date_create($this->crop_plant_date), "Y-m-d") ?>" readonly/>
					</div>
				</div>
				<div class='float_left'><label>Sample Date</label><input type="date" name="sample_date" value="<?php echo date("Y-m-d"); ?>" required /></div>
				<div class='float_right'><label>Crop Zone</label>
				<select name="zone_id">
					<?php foreach ($this->zone_info as $zone_info) { ?>
						<option value="<?= $zone_info->zone_id; ?>"><?= $zone_info->zone_name; ?></option>
					<?php } ?>
				</select></div>
				<!-- max size 5 MB (as many people directly upload high res pictures from their phones) -->
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<div style="padding: 0; margin:0 0 10px 0;">
					<table class="data-table">
						<thead>
							<tr>
								<th>Plot#</th>
								<th>Bulb#</th>
								<th>Plot Weight (kg)</th>
								<th>Image</th>
								<th>Comments</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$index = 0;
						foreach ($this->sample_info as $sample_info) { ?>
							<tr>
								<td style="padding:0px; margin:0px; width:50px;"><input style="padding: 0 0 0 0; text-align: center;" type="text" name="sample_id[]" value="<?= $sample_info->sample_id; ?>" tabindex="-1" readonly></td>
								<td style="padding:0px; margin:0px; width:55px;"><input type="number" step=1 min=0 name="sample_count[]" required></td>
								<td style="padding:0px; margin:0px; width:55px;"><input type="number" step=0.01 min=1 name="sample_bulb_weight[]" required></td>
								<td style="padding:0px; margin:0px; height:37px; background-color: #fff;">
									<div class="image-upload" id="image-upload_<?= $index; ?>">
										<label class="sample_file_label" for="sample_file_<?= $index; ?>">
											<span id="tooltiptext_<?= $index; ?>" name="tooltiptext[<?= $index; ?>]" class="tooltiptext" style="width:75px;">Select image</span></label>
										<input type="file" name="sample_file[]" class="sample_file" id="sample_file_<?= $index; ?>">
									</div>								
								</td>
								<td class="col-last">
									<input type="text" name="sample_comment[]" class="data-table" placeholder="e.g. Disease, Quality">
								</td>
							</tr>
						<?php 
						$index++;
						} ?>
						</tbody>
					</table>
				</div>
				<input type="hidden" name="growth_stage_id" value="<?php echo $this->growth_stage_id ?>"/>
				<input type="hidden" name="farm_id" value="<?php echo $this->farm_id ?>"/>
				<input type="hidden" name="paddock_id" value="<?php echo $this->paddock_id ?>"/>
				<input type="hidden" name="crop_id" value="<?php echo $this->crop_id ?>"/>
				<div class="submit_float_left"> 
					<div class="app-button" style="margin:0; padding:0;">                
						<a href="<?php echo Config::get('URL'); ?>collection">Back</a>
					</div>
				</div>
				<div class="submit_float_left">
					<input type="submit" value="Upload Data" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc;"'; } ?>>
				</div>
				<div style="clear:both;"></div>	
			</form>
        </p>        
    </div>
</div>
<div id="loader_icon" style="display:none;"><img src="<?php echo Config::get('URL'); ?>images/ajax-loader.gif" /></div>