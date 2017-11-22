<div class="container">
    <h1>View Reports</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<p>
			<form method="post" action="<?php echo Config::get('URL');?>reports/selection_action">
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
				<button type="submit" name="reports" value="View Emergence Assessment" id="emergence"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/reports_view_emergence.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Emergence Assessment</figcaption>
				</figure></button>
				<button type="submit" name="reports" value="View Three Leaf Assessment" id="threeleaf"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/reports_view_three_leaf.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Three Leaf Assessment</figcaption>
				</figure></button>
				<button type="submit" name="reports" value="View Five Leaf Assessment" id="fiveleaf"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/reports_view_five_leaf.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Five Leaf Assessment</figcaption>
				</figure></button>
				<button type="submit" name="reports" value="View Bulbing Assessment" id="bulbing"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/reports_view_bulbing.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Bulbing Assessment</figcaption>
				</figure></button>
				<button type="submit" name="reports" value="View Harvest Assessment" id="harvest" style="margin-bottom:10px"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/reports_view_harvest.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Harvest Assessment</figcaption>
				</figure></button>
				<button type="submit" name="reports" value="View Paddock Survey" id="survey" style="margin-bottom:10px"><figure class="item">
					<img src="<?php echo Config::get('URL'); ?>images/paddock_view.png"/>
					<figcaption class="responsive caption" data-min="10" data-max="18">View Survey Map</figcaption>
				</figure></button>						
			<?php foreach ($this->report_info as $report_info) { ?>
				<input type="hidden" name="<?= $report_info->paddock_name; ?>" id="<?= $report_info->paddock_id; ?>" value="<?= implode(',',$report_info->growth_stage_id); ?>">
			<?php } ?>
			</form>
			<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>
			<!-- Return to previous selection page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>dashboard">Back</a>
            </div>			
		</p> 
    </div>
</div>