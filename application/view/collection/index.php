<div class="container">
    <h1>Enter Sample Data</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
			<form method="post" action="<?php echo Config::get('URL');?>collection/selection_action">
				<div class="container_center">
					<div>
						<label for="farm_id">Farm</label>
						<select name="farm_id" id="farm_id"></select>
					</div>
					<div>
						<label for="paddock_id">Paddock</label>
						<select name="paddock_id" id="paddock_id"></select>
					</div>
					<div>
						<label for="crop_id">Plant Date</label>
						<select name="crop_id" id="crop_id"></select>
					</div>
				</div>
		<button type="submit" name="collection" value="emergence" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/add_emergence.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Emergence Data</figcaption>
		</figure></button>
		<button type="submit" name="collection" value="threeleaf" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/add_threeleaf.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Three Leaf Data</figcaption>
		</figure></button>
		<button type="submit" name="collection" value="fiveleaf" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/add_fiveleaf.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Five Leaf Data</figcaption>
		</figure></button>
		<button type="submit" name="collection" value="bulbing" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/add_bulbing.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Bulbing Data</figcaption>
		</figure></button>
		<button type="submit" name="collection" value="harvest" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/add_harvest.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Harvest Data</figcaption>
		</figure></button>						
	</form>	
				<input type="hidden" name="page_" id="page_" value="collection">
			</form>
			<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>
			<!-- Return to previous selection page -->
			<div class="app-button" style="margin-top:15px; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>dashboard">Back</a>
            </div>
    </div>
</div>