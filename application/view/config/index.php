<div class="container">
    <h1>Update Existing Settings</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<p>
			<form method="post" action="<?php echo Config::get('URL');?>config/selection_action">
				<div class="container_center">
					<?php if($this->farm_info!=0){ ?>
					<div>
						<label for="farm_id">Farm</label>
						<select name="farm_id" id="farm_id"></select>
					</div>
					<?php } ?>
					<?php if($this->paddock_info!=0){ ?>
					<div>
						<label for="paddock_id">Paddock</label>
						<select name="paddock_id" id="paddock_id"></select>
					</div>
					<?php } ?>
					<?php if($this->crop_info!=0){ ?>
					<div>
						<label for="crop_id">Plant Date</label>
						<select name="crop_id" id="crop_id"></select>
					</div>
					<?php } ?>
				</div>
				<button type="submit" name="config" value="Edit Farm" <?php if($this->farm_info==0){ echo 'disabled'; } ?>><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/farm_edit.png"/>
					<figcaption class="caption">Edit Farm Information</figcaption>
				</figure></button>
				<button type="submit" name="config" value="Edit Paddock" <?php if($this->paddock_info==0){ echo 'disabled'; } ?>><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/paddock_edit.png"/>
					<figcaption class="caption">Edit Paddock Information</figcaption>
				</figure></button>
				<button type="submit" name="config" value="Edit Paddock Polygon" <?php if($this->crop_info==0){ echo 'disabled'; } ?>><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/paddock_view.png"/>
					<figcaption class="caption">Edit Paddock Map</figcaption>
				</figure></button>
				<button type="submit" name="config" value="Edit Crop" <?php if($this->crop_info==0){ echo 'disabled'; } ?>><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/crop_edit.png"/>
					<figcaption class="caption">Edit Crop Information</figcaption>
				</figure></button>
				<!--
				<button type="submit" name="config" value="View Weather"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/weather_view.png"/>
					<figcaption class="caption">View Weather Information</figcaption>
				</figure></button>
				-->
			</form>
			<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>
			<!-- Return to previous selection page -->
			<div class="app-button" style="margin:0; padding:15px 0 0 0;">                
                <a href="<?php echo Config::get('URL'); ?>dashboard">Back</a>
            </div>
		</p> 
    </div>
</div>