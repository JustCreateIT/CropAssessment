<div class="container">
	<!-- echo out the system feedback (error and success messages) -->
	<?php $this->renderFeedbackMessages(); ?>
	<form method="post" action="<?php echo Config::get('URL');?>dashboard/selection_action">
		<button type="submit" name="dashboard" value="setup"><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/settings.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Setup New Farms, Paddocks, Crops And Users</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="enter"><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/enter_data.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">Enter Collected Sample Data</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="view"><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/reports.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">View Assessment Reports</figcaption>
		</figure></button>
		<button type="submit" name="dashboard" value="config"><figure class="item">
			<img src="<?php echo Config::get('URL'); ?>images/edit_data.png"/>
			<figcaption class="responsive caption" data-min="10" data-max="18">View Or Update Existing Farm Information</figcaption>
		</figure></button>					
	</form>	
</div>
