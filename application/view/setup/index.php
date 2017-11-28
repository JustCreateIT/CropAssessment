<div class="container">
    <h1>Setup & Configuration</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>		
		<form method="post" action="<?php echo Config::get('URL');?>setup/selection_action">
			<?php /*  if(isset($this->farm_info) && !empty($this->farm_info)){ ?>
				<!-- Current farms available to this user -->
				<div><label>Current Farms</label>
					<select name="farm_id">
						<?php foreach ($this->farm_info as $farm_info) { ?>
							<option value="<?= $farm_info->farm_id; ?>"><?= $farm_info->farm_name; ?></option>
						<?php } ?>	
					</select>
				</div>
			<?php  } */?>
			<button type="submit" name="setup" value="farm"><figure class="item">
				<img src="<?php echo Config::get('URL'); ?>images/farm_setup.png"/>
				<figcaption class="responsive caption" data-min="10" data-max="18">Setup A New Farm</figcaption>
			</figure></button>
			<button type="submit" name="setup" value="paddock" <?php if (count(Session::get('user_farms')) == 0) { echo 'disabled'; } ?>><figure class="item">
				<img src="<?php echo Config::get('URL'); ?>images/paddock_setup.png"/>
				<figcaption class="responsive caption" data-min="10" data-max="18">Setup A New Paddock</figcaption>
			</figure></button>
			<button type="submit" name="setup" value="crop" <?php if (count(Session::get('user_paddocks')) == 0) { echo 'disabled'; } ?>><figure class="item">
				<img src="<?php echo Config::get('URL'); ?>images/crop_setup.png"/>
				<figcaption class="responsive caption" data-min="10" data-max="18">Define A New Crop</figcaption>
			</figure></button>
			<button type="submit" name="setup" value="user" <?php if (count(Session::get('user_farms')) == 0) { echo 'disabled'; } ?>><figure class="item">
				<img src="<?php echo Config::get('URL'); ?>images/add_user.png"/>
				<figcaption class="responsive caption" data-min="10" data-max="18">Add Users To Your Farm</figcaption>
			</figure></button>
		</form>		
    </div>
		<!-- Return to previous selection page -->
	<div class="app-button" style="margin:0; padding:15px 0 0 0;">                
		<a href="<?php echo Config::get('URL'); ?>dashboard">Back</a>
	</div>
</div>