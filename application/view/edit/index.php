<div class="container">
    <h1>What Would You Like To Do?</h1>
    <div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
		<p>
			<form method="post" action="<?php echo Config::get('URL');?>edit/selection_action">
				<!-- Select farm of paddock to edit // deprecated
				<div class="float_left"><label for="farm_id">Select Farm</label>
					<select name="farm_id" id="farm_id">
					</select></div>
				-->
				<div><label for="paddock_id">Select Paddock</label>
					<select name="paddock_id" id="paddock_id">	
					</select>
				</div>
				
				<!-- Edit Farm Configuration landing page -->
				<input type="submit" name="edit" value="Edit Farm Information" <?php if($this->farm_info === false){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>>			
				<!-- Edit Paddock Configuration landing page -->
				<input type="submit" name="edit" value="Edit Paddock Information" <?php if($this->paddock_info === false){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>>
				<!-- Add farm users landing page -->
				<input type="submit" name="edit" value="Add Users To Your Farm" <?php if($this->farm_info === false){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>>
			</form>
			<input type="hidden" name="farm_details" id="farm_details" value='<?php echo $this->farm_details; ?>'>			
			<!-- Return to the home page -->
			<div class="app-button" style="margin:0; padding:0;">                
                <a href="<?php echo Config::get('URL'); ?>">Exit</a>
            </div>			
		</p>        
    </div>
</div>