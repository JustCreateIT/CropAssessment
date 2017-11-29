<div class="container">
	<!-- echo out the system feedback (error and success messages) -->
	<?php $this->renderFeedbackMessages(); ?>
	<form method="post" action="<?php echo Config::get('URL');?>dashboard/selection_action">
		<button type="submit" name="dashboard" value="setup"><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/settings.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Setup New Farms, Paddocks, Crops And Users</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="enter" <?php if (count(Session::get('user_crops')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/enter_data.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Collected Sample Data</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="view" <?php if (Session::get('user_reports') === false) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/reports.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">View Assessment Reports</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="config" <?php if (count(Session::get('user_farms')) == 0) { echo 'disabled'; } ?>><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/edit_data.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Edit Existing Farm Information</figcaption>
		</figure></button>					
	</form>	
</div>
