<div class="container">
    <h1>Enter Sample Data</h1>
    <!--<div class="box">-->
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<p>
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
				<input type="submit" name="collection" value="Enter Emergence Data">
				<input type="submit" name="collection" value="Enter Three Leaf Data">
				<input type="submit" name="collection" value="Enter Five Leaf Data">
				<input type="submit" name="collection" value="Enter Bulbing Data">
				<input type="submit" name="collection" value="Enter Harvest Data">
				<input type="hidden" name="page_" id="page_" value="collection">
			</form>
			<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>
			<!-- Return to previous selection page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>dashboard">Back</a>
            </div>
		</p> 
    </div>
</div>