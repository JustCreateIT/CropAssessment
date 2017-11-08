<div class="container">
    <h1>Setup A New Farm</h1>
	<div class="input-box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
        <form id="input_form" method="post" action="<?php echo Config::get('URL');?>setup/createFarm_action">
			<p>
				<label>Farm name</label>
				<input type="text" name="farm_name" required />
			</p>
			<p>
				<label>First name</label>
				<input type="text" name="farm_contact_firstname" value="<?= $this->user_first_name; ?>" required />
			</p>
			<p>
				<label>Last name</label>
				<input type="text" name="farm_contact_lastname" value="<?= $this->user_last_name; ?>" required />
			</p>
			<p>
				<label>Email address</label>
				<input type="text" name="farm_email_address" value="<?= $this->user_email; ?>" required />
			</p>
			<p>
				<label>Phone number</label>
				<input type="text" name="farm_phone_number" value="<?= $this->user_phone_number; ?>" />
			</p>
			<div class="submit_float_left"> 
				<div class="app-button" style="margin:0; padding:0;">                
					<a href="<?php echo Config::get('URL'); ?>setup">Back</a>
				</div>
			</div>
			<div class="submit_float_left">
				<input type="submit" value="Save" <?php if((Session::get("user_account_type") == 1) || (Session::get("user_account_type") == 5)){ echo 'disabled style="color: #ccc; background-color: transparent; border: 2px solid #ccc;"'; } ?>>
			</div>
			<div style="clear:both;"></div>
		</form>
    </div>
</div>
