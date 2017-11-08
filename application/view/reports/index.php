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
				<input type="submit" name="reports" value="View Emergence Assessment" id="emergence"/>
				<input type="submit" name="reports" value="View Three Leaf Assessment" id="threeleaf"/>
				<input type="submit" name="reports" value="View Five Leaf Assessment" id="fiveleaf"/>
				<input type="submit" name="reports" value="View Bulbing Assessment" id="bulbing"/>
				<input type="submit" name="reports" value="View Harvest Assessment" id="harvest"/>
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